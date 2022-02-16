<?php

// Meta-Box Generator
// How to use: $meta_value = get_post_meta( $post_id, $field_id, true );
// Example: get_post_meta( get_the_ID(), "my_metabox_field", true );

class VideoMetaDataMetabox {

    private $screens = array('tiny_video_item');

    private $fields = array(
        array(
            'label' => 'Video Source',
            'id' => 'tiny_video_type',
            'type' => 'select',
            'options' => array(
                'yt' => 'Youtube',
            ),
        ),
        array(
            'label' => 'Youtube Url',
            'id' => 'tiny_video_source',
            'type' => 'text',
        ),
        array(
            'label' => 'Youtube Thumbnail',
            'id' => 'tiny_video_thumb',
            'type' => 'url',
        )
    );

    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_fields'));
        add_action('admin_footer', array($this, 'add_media_fields'));
    }

    public function add_meta_boxes() {
        foreach ($this->screens as $s) {
            add_meta_box(
                'VideoMetaData',
                __('Video Meta Data', 'tiny-video-gallery'),
                array($this, 'meta_box_callback'),
                $s,
                'advanced',
                'high'
            );
        }
    }

    public function meta_box_callback($post) {
        wp_nonce_field('VideoMetaData_data', 'VideoMetaData_nonce');
        $this->field_generator($post);
    }

    public function field_generator($post) {
        $output = '';
        foreach ($this->fields as $field) {
            $label = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
            $meta_value = get_post_meta($post->ID, $field['id'], true);
            if (empty($meta_value)) {
                if (isset($field['default'])) {
                    $meta_value = $field['default'];
                }
            }
            switch ($field['type']) {
                case 'select':
                    $input = sprintf(
                        '<select id="%s" name="%s">',
                        $field['id'],
                        $field['id']
                    );
                    foreach ($field['options'] as $key => $value) {
                        $field_value = !is_numeric($key) ? $key : $value;
                        $input .= sprintf(
                            '<option %s value="%s">%s</option>',
                            $meta_value === $field_value ? 'selected' : '',
                            $field_value,
                            $value
                        );
                    }
                    $input .= '</select>';
                    break;

                case 'media':
                    $meta_url = '';
                    if ($meta_value) {
                        if ($field['returnvalue'] == 'url') {
                            $meta_url = $meta_value;
                        } else {
                            $meta_url = wp_get_attachment_url($meta_value);
                        }
                    }
                    $input = sprintf(
                        '<input id="%s" name="%s" type="text" value="%s" class="large-text" data-return="%s"><div id="preview%s" style="display:none;background-color:#fafafa;margin-right:12px;border:1px solid #eee;width: 150px;height:150px;background-image:url(%s);background-size:cover;background-repeat:no-repeat;background-position:center;"></div><input style="width: 15%%;margin-right:5px;" class="button new-media" id="%s_button" name="%s_button" type="button" value="Select" /><input style="width: 15%%;" class="button remove-media" id="%s_buttonremove" name="%s_buttonremove" type="button" value="Delete" />',
                        $field['id'],
                        $field['id'],
                        $meta_value,
                        $field['returnvalue'],
                        $field['id'],
                        $meta_url,
                        $field['id'],
                        $field['id'],
                        $field['id'],
                        $field['id']
                    );
                    break;

                default:
                    $input = sprintf(
                        '<input %s id="%s" name="%s" type="%s" value="%s">',
                        $field['type'] !== 'color' ? 'style="width: 100%"' : '',
                        $field['id'],
                        $field['id'],
                        $field['type'],
                        $meta_value
                    );
            }
            $output .= $this->format_rows($label, $input);
        }
        echo '<table class="widefat striped"><tbody>' . wp_kses($output, $this->expanded_alowed_tags()) . '</tbody></table>';
    }

    public function format_rows($label, $input) {
        return '<tr><td style="width: 20%">' . $label . '</td><td>' . $input . '</td></tr>';
    }


    public function add_media_fields() {
?>
        <script>
            jQuery(document).ready(function($) {
                if (typeof wp.media !== 'undefined') {
                    var _custom_media = true,
                        _orig_send_attachment = wp.media.editor.send.attachment;
                    $('.new-media').click(function(e) {
                        var send_attachment_bkp = wp.media.editor.send.attachment;
                        var button = $(this);
                        var id = button.attr('id').replace('_button', '');
                        _custom_media = true;
                        wp.media.editor.send.attachment = function(props, attachment) {
                            if (_custom_media) {
                                if ($('input#' + id).data('return') == 'url') {
                                    $('input#' + id).val(attachment.url);
                                } else {
                                    $('input#' + id).val(attachment.id);
                                }
                                $('div#preview' + id).css('background-image', 'url(' + attachment.url + ')');
                            } else {
                                return _orig_send_attachment.apply(this, [props, attachment]);
                            };
                        }
                        wp.media.editor.open(button);
                        return false;
                    });
                    $('.add_media').on('click', function() {
                        _custom_media = false;
                    });
                    $('.remove-media').on('click', function() {
                        var parent = $(this).parent();
                        parent.find('input[type="text"]').val('');
                        parent.find('div').css('background-image', 'url()');
                    });
                }
            });
        </script>
<?php
    }


    public function save_fields($post_id) {
        if (!isset($_POST['VideoMetaData_nonce'])) {
            return $post_id;
        }
        $nonce = $_POST['VideoMetaData_nonce'];
        if (!wp_verify_nonce($nonce, 'VideoMetaData_data')) {
            return $post_id;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        $vid_id = '';
        foreach ($this->fields as $field) {
            if ($field['id'] == 'tiny_video_source') {
                $vid_id = $this->convertYoutube($_POST[$field['id']]);
                $_POST[$field['id']] = esc_url_raw("https://www.youtube.com/embed/" . $vid_id);
            }
            if (isset($_POST[$field['id']])) {
                switch ($field['type']) {
                    case 'email':
                        $_POST[$field['id']] = sanitize_email($_POST[$field['id']]);
                        break;
                    case 'text':
                        $_POST[$field['id']] = esc_url_raw($_POST[$field['id']]);
                        break;
                }

                if ($field['id'] == 'tiny_video_thumb' && empty($_POST[$field['id']])) {

                    $thumb = esc_url_raw("https://img.youtube.com/vi/" . $vid_id . "/maxresdefault.jpg");
                    if ($this->check_url_exists($thumb)) {
                        $_POST[$field['id']] = $thumb;
                    } else {
                        $_POST[$field['id']] = esc_url_raw("https://img.youtube.com/vi/" . $vid_id . "/0.jpg");
                    }
                }
                update_post_meta($post_id, $field['id'], $_POST[$field['id']]);
            } else if ($field['type'] === 'checkbox') {
                update_post_meta($post_id, $field['id'], '0');
            }
        }
    }


    private function convertYoutube($string) {
        preg_match('/(.*?)(^|\/|v=)([a-z0-9_-]{11})(.*)?/im', $string, $match);
        return $match[3];
    }


    private function check_url_exists($url) {
        $headers = @get_headers($url);
        if ($headers || strpos($headers[0], '404')) {
            return false;
        }
        return true;
    }


    public function expanded_alowed_tags() {
        $my_allowed = wp_kses_allowed_html('post');
        // iframe
        $my_allowed['iframe'] = array(
            'src'             => array(),
            'height'          => array(),
            'width'           => array(),
            'frameborder'     => array(),
            'allowfullscreen' => array(),
        );
        // form fields - input
        $my_allowed['input'] = array(
            'class' => array(),
            'id'    => array(),
            'name'  => array(),
            'value' => array(),
            'type'  => array(),
            'style'  => array(),
        );
        // select
        $my_allowed['select'] = array(
            'class'  => array(),
            'id'     => array(),
            'name'   => array(),
            'value'  => array(),
            'type'   => array(),
        );
        // select options
        $my_allowed['option'] = array(
            'selected' => array(),
        );
        // style
        $my_allowed['style'] = array(
            'types' => array(),
        );

        return $my_allowed;
    }
}

if (class_exists('VideoMetaDataMetabox')) {
    new VideoMetaDataMetabox;
};
