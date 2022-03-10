<?php

// Create Shortcode tiny_video_gallery
// Use the shortcode: [tiny_video_gallery col=""]
function create_tiny_video_gallery_shortcode($atts) {
    // Attributes
    $atts = shortcode_atts(
        array(
            'col' => 3,
        ),
        $atts,
        'tiny_video_gallery'
    );
    // Attributes in var
    $col_count = $atts['col'];

    // Output Code
    $output = tpvg_render($col_count);

    return $output;
}
add_shortcode('tiny_video_gallery', 'create_tiny_video_gallery_shortcode');


function tpvg_render($col_count) {
    $id = wp_unique_id();
    ob_start(); ?>
    <div id="tiny_video_gallery-<?php echo esc_attr($id); ?>">
        <div class="tpvg_filter-wrap" style="--tpvg-accent:black;">
            <div class="tpvg_filter-buttons">
                <ul id="tpvg_filter-btns">
                    <?php
                    $terms = get_terms(array(
                        'taxonomy' => 'tiny_video_category',
                        'hide_empty' => true,
                    ));

                    if (!empty($terms)) { ?>
                        <li class="active" data-target="all">ALL</li>
                    <?php
                        foreach ($terms as $term) {
                            echo '<li data-target="' . esc_attr($term->slug) . '">' . esc_attr($term->name) . '</li>';
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="tpvg_gallery-wrap">
            <div class="tpvg_gallery-items" style="--tpvg-cols:<?php echo esc_attr($col_count); ?>">

                <?php
                $q_query_args = array(
                    'post_type' => array('tiny_video_item'),
                    'order' => 'ASC',
                    'orderby' => 'date',
                    'nopaging' => true,
                );

                // The Query
                $query = new WP_Query($q_query_args);

                // The Loop
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();

                        $thumbnail = get_post_meta(get_the_ID(), "tiny_video_thumb", true);;
                        $vid_url = get_post_meta(get_the_ID(), "tiny_video_source", true);;
                        $title = get_the_title();

                        $cats = wp_get_post_terms(get_the_ID(), array('tiny_video_category'));
                        $cats = wp_list_pluck($cats, 'slug');
                        $cats = implode(",", $cats);
                ?>

                        <div id="gallery-item-<?php echo esc_attr(get_the_ID()); ?>" class="tpvg_gallery-item" data-id="<?php echo esc_attr($cats); ?>" data-video="<?php echo esc_url($vid_url); ?>">
                            <div class="inner">
                                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($title); ?>">
                                <div class="tpvg-caption"><?php echo esc_attr($title); ?></div>
                            </div>
                        </div>
                <?php
                    }
                }

                // Reset Original Post Data
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </div>
<?php
    return ob_get_clean();
}
