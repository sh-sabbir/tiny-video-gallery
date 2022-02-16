<?php

// Create Video Gallery element for Visual Composer
add_action('vc_before_init', 'codeianvideogaller_integrateWithVC');
function codeianvideogaller_integrateWithVC() {
    vc_map(array(
        'name' => __('Tiny Video Gallery', 'tiny-video-gallery'),
        'base' => 'tiny_video_gallery',
        'show_settings_on_create' => true,
        'category' => __('Content', 'tiny-video-gallery'),
        "controls"    => "full",
        "icon" => TPVG_ASSETS . "static/vc_icon.png",
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => __('Number of Column', 'tiny-video-gallery'),
                'param_name' => 'col',
                'value' => array(
                    __('1 Column',  "tiny-video-gallery") => '1',
                    __('2 Column',  "tiny-video-gallery") => '2',
                    __('3 Column',  "tiny-video-gallery") => '3',
                    __('4 Column',  "tiny-video-gallery") => '4',
                ),
                'std'         => '3',
                "description" => __("Enter number of column.", "tiny-video-gallery")
            ),
        )
    ));
}
