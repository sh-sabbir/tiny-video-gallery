<?php

class TVG_Cron {
    private static $instance = null;

    private $status = [];
    private $vidItems = [];

    public function __construct() {
        // session_start();
        add_action('manage_posts_extra_tablenav', [$this, 'admin_order_list_top_bar_button'], 20, 1);
        add_action('restrict_manage_posts', [$this, 'display_admin_tiny_video_item_language_filter'], 20, 1);

        add_action('wp_ajax_tvg_yt_sync', function () {
            // echo 'hola';
            $this->vidItems = [];
            delete_transient('tvga_stat');
            $this->yt_sync_callback(null, true);
        });

        add_action('wp_ajax_tvg_yt_insert', function () {

            $id = $_REQUEST['id'];
            $title = $_REQUEST['title'];

            $this->handleDataInsert($id, $title);

            die();
        });

        add_action('wp_ajax_tvg_yt_clear_log', function () {
            delete_transient('tvga_stat');
            die();
        });

        add_action('wp_ajax_tvg_yt_sync_stat', function () {
            wp_send_json_success(get_transient('tvga_stat'));
        });

        // Register a Custom Cron Interval
        add_filter('cron_schedules', [$this, 'cron_add_weekly']);

        add_action('current_screen', function () {
            $current_screen = get_current_screen();
            if (!$current_screen || !strstr($current_screen->post_type, 'tiny_video_item')) {
                return;
            }
            add_action('in_admin_footer', function () {
                $this->render_modal_skeleton();
            });
        });

        add_action('tvg_yt_sync_event', [$this, 'yt_sync_callback']);
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new TVG_Cron();
        }
        return self::$instance;
    }

    // Display an action button in admin order list header

    function admin_order_list_top_bar_button($which) {
        global $pagenow, $typenow;
        if ('tiny_video_item' === $typenow && 'edit.php' === $pagenow && 'top' === $which) {
?>
            <div class="alignleft actions custom">
                <button type="submit" name="import_yt_vids" id="tvg_yt_sync" class="button" value="yes"><?php echo __('Import Videos From Youtube', 'woocommerce'); ?></button>
            </div>
        <?php
        }
    }

    function display_admin_tiny_video_item_language_filter() {
        global $pagenow, $typenow;

        if (
            'tiny_video_item' === $typenow && 'edit.php' === $pagenow &&
            isset($_GET['import_yt_vids']) && $_GET['import_yt_vids'] === 'yes'
        ) {
            $this->yt_sync_callback(null, true);
            do_action('tvg_yt_sync_event');
        }
    }

    function cron_add_weekly($schedules) {
        $schedules['four_hourly'] = array(
            'interval' => 4 * HOUR_IN_SECONDS,
            'display' => __('Every 4 hours')
        );
        return $schedules;
    }

    function yt_sync_callback($nextPageToken = null, $override = false) {
        if (!defined("SAVEQUERIES")) {
            define('SAVEQUERIES', false);
        }
        wp_suspend_cache_addition(true);

        $this->status[] = "Gettings Tiny Video Settings";
        set_transient('tvga_stat', $this->status);
        $tvg_settings = get_option('tvg_setting');
        $lastFetchAmount = get_option('tvg_last_amount', 0);
        $hasAutoSync = isset($tvg_settings['enable_youtube_auto_sync']) ? true : false;

        // error_log(print_r($tvg_settings, true));
        $this->status[] = "Checking Youtube API Key";
        set_transient('tvga_stat', $this->status);
        $ytApiKey = $tvg_settings['youtube_api_key'];
        $ytPaginate = get_option('tvg_yt_per_page', 50);
        $ytContentType = $tvg_settings['data_source'];
        $ytPlaylist = $tvg_settings['source_id']; //get_option('tvg_yt_playlist', 'PLMBMWX-ldP-DaHgW0-Mvu_DcGv4l4_dtv');
        $ytChannel = $tvg_settings['source_id'];

        $apiBase = 'https://youtube.googleapis.com/youtube/v3/';

        $this->status[] = "Preparing request url...";
        set_transient('tvga_stat', $this->status);

        if (wp_doing_cron() && !$hasAutoSync) {
            return;
        }

        if ($ytApiKey) {
            // error_log("Hit");
            $data = array(
                'part' => 'snippet',
                'maxResults' => $ytPaginate,
                'key' => $ytApiKey,
            );

            if ($ytContentType == 'channel') {
                $data['channelId'] = $ytChannel;
                $data['order'] = 'date';
                $apiBase = $apiBase . 'search';
            } else {
                $data['playlistId'] = $ytPlaylist;
                $apiBase = $apiBase . 'playlistItems';
            }

            if ($nextPageToken) {
                $data['pageToken'] = $nextPageToken;
            }

            $this->status[] = "Doing API request...";
            set_transient('tvga_stat', $this->status);
            $query = http_build_query($data, "");
            $requestURI = $apiBase . '?' . $query;
            $response = wp_remote_get($requestURI);

            if (is_array($response) && !is_wp_error($response)) {
                $headers = $response['headers']; // array of http header lines
                $body    = $response['body']; // use the content

                $this->status[] = "Found Api Data...";
                set_transient('tvga_stat', $this->status);
                $this->status[] = "Processing API Data...";
                set_transient('tvga_stat', $this->status);
                if (wp_doing_ajax()) {
                    $this->handleAjaxCall($body);
                } else {
                    $this->handleApiData($body, $lastFetchAmount);
                }
            } else {
                $this->status[] = "API request Error...";
                set_transient('tvga_stat', $this->status);
            }
        } else {
            $this->status[] = "Error...Invalid...";
            set_transient('tvga_stat', $this->status);
            // echo 'hola2';
        }
    }

    function handleAjaxCall($data) {
        if ($data) {
            $json = json_decode($data);

            if (isset($json->error)) {
                wp_send_json_error($json->error);
            }

            $totalResults = $json->pageInfo->totalResults;
            $this->status[] = "Total " . $totalResults . " items found...";
            set_transient('tvga_stat', $this->status);
            $nextPage = isset($json->nextPageToken) ? $json->nextPageToken : null;
            $items = $json->items;

            $this->status[] = "Filtering data and inserting...";
            set_transient('tvga_stat', $this->status);

            $progress = 0;
            foreach ($items as $vid) {
                $vid_id = '';
                $vid_title = '';

                if (isset($vid->kind) && $vid->kind == 'youtube#searchResult') {
                    if (isset($vid->id->kind) && $vid->id->kind == "youtube#video") {
                        $progress++;
                        $vid_id = $vid->id->videoId;
                        $vid_title = $vid->snippet->title;
                        $this->status[] = "Inserting video " . $progress . "...";
                        set_transient('tvga_stat', $this->status);

                        $this->vidItems[] = array(
                            'id' => $vid_id,
                            'title' => $vid_title
                        );

                        // $this->handleDataInsert($vid_id, $vid_title);
                    }
                } else {
                    $progress++;
                    $vid_id = $vid->snippet->title;
                    $vid_title = $vid->snippet->title;
                    $this->status[] = "Inserting video " . $progress . "...";
                    set_transient('tvga_stat', $this->status);

                    // $this->vidItems[$vid_id] = $vid_title;

                    $this->vidItems[] = array(
                        'id' => $vid_id,
                        'title' => $vid_title
                    );

                    // $this->handleDataInsert($vid_id, $vid_title);
                }
            }

            if ($nextPage) {
                $this->yt_sync_callback($nextPage);
            } else {
                $resp = [
                    'status' => 'success',
                    'data' => $this->vidItems
                ];
                wp_send_json_success($resp);
            }
        }
    }
    function handleApiData($data, $oldCount) {
        // var_dump($data);
        if ($data) {
            $json = json_decode($data);

            if (isset($json->error)) {
                wp_send_json_error($json->error);
            }
            // error_log(print_r($json, true));

            $totalResults = $json->pageInfo->totalResults;
            $this->status[] = "Total " . $totalResults . " items found...";
            set_transient('tvga_stat', $this->status);
            $nextPage = isset($json->nextPageToken) ? $json->nextPageToken : null;
            $items = $json->items;
            $items = array_reverse($items);

            if ($oldCount == $totalResults) {
                return;
            }

            $this->status[] = "Filtering data and inserting...";
            set_transient('tvga_stat', $this->status);

            $progress = 0;
            foreach ($items as $vid) {
                $vid_id = '';
                $vid_title = '';

                if (isset($vid->kind) && $vid->kind == 'youtube#searchResult') {
                    if (isset($vid->id->kind) && $vid->id->kind == "youtube#video") {
                        $progress++;
                        $vid_id = $vid->id->videoId;
                        $vid_title = $vid->snippet->title;
                        $this->status[] = "Inserting video " . $progress . "...";
                        set_transient('tvga_stat', $this->status);
                        $this->handleDataInsert($vid_id, $vid_title);
                    }
                } else {
                    $progress++;
                    $vid_id = $vid->snippet->resourceId->videoId;
                    $vid_title = $vid->snippet->title;
                    $this->status[] = "Inserting video " . $progress . "...";
                    set_transient('tvga_stat', $this->status);
                    $this->handleDataInsert($vid_id, $vid_title);
                }
                sleep(2);
            }

            if ($nextPage) {
                $this->yt_sync_callback($nextPage);
            }
        }
    }


    function handleDataInsert($id, $title) {

        $this->status[] = "Preparing data...";
        set_transient('tvga_stat', $this->status);
        $oldVids = get_option('tvg_old_vids', []);
        if (!in_array($id, $oldVids)) {
            $thumbMax = esc_url_raw("https://img.youtube.com/vi/" . $id . "/maxresdefault.jpg");
            if ($this->check_yt_thumb_exists($thumbMax)) {
                $thumb = $thumbMax;
            } else {
                $thumb = esc_url_raw("https://img.youtube.com/vi/" . $id . "/0.jpg");
            }

            $post_arr = array(
                'post_type'    => 'tiny_video_item',
                'post_title'   => $title,
                'post_status'  => 'publish',
                'meta_input'   => array(
                    'tiny_video_source' => $id,
                    'tiny_video_thumb' => $thumb,
                ),
            );

            $this->status[] = "Creating video item...";
            set_transient('tvga_stat', $this->status);
            $newVid = wp_insert_post($post_arr);

            if ($newVid) {
                $this->status[] = "Done! Video item created...";
                set_transient('tvga_stat', $this->status);
                array_push($oldVids, $id);
                //$oldVids[] = $id;
                update_option('tvg_old_vids', $oldVids);

                return $newVid;
            }
        } else {
            $this->status[] = "Video already exist in database..aborting...";
            set_transient('tvga_stat', $this->status);
        }
    }


    function check_yt_thumb_exists($url) {
        $response = wp_remote_head($url);
        return 200 === wp_remote_retrieve_response_code($response);
    }

    private function render_modal_skeleton() {
        ?>
        <div id="tvg_yt_stat">
            <div class="tvg_yt_stat_overlay">
                <div class="tvg_yt_stat_container">
                    <div class="loading">
                        <div class="lds-dual-ring"></div>
                    </div>
                    <div class="heading">Tiny Gallery Syncing Youtube Videos</div>
                    <div class="data" id="tvg_yt_stat_summary">Please wait...</div>
                </div>
            </div>
        </div>
<?php
    }
}

TVG_Cron::getInstance();
