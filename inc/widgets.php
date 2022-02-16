<?php

// Include the base widget
require(TPVG_DIR_PATH . 'inc/widgets/generic.php');

// Check if Visual Composer is installed and active
if (defined('WPB_VC_VERSION')) {
    require(TPVG_DIR_PATH . 'inc/widgets/vc.php');
}

// Check if Elementor installed and activated
if (did_action('elementor/loaded')) {
    require(TPVG_DIR_PATH . 'inc/widgets/elementor.php');

    // Register Widget
    add_action('elementor/widgets/widgets_registered', 'register_widgets');

    // Register Widget Styles
    add_action('elementor/frontend/after_enqueue_styles', 'widget_styles');

    // Register Widget Scripts
    add_action('elementor/frontend/after_enqueue_scripts', 'widget_scripts');
}

function register_widgets() {
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new TinyVideoGallery\Widgets\Tiny_Video_Gallery());
}

function widget_styles() {
    wp_enqueue_style('tiny-video-gallery-css', TPVG_ASSETS.'css/tiny-video-gallery.css');
}

function widget_scripts() {
    wp_enqueue_script('tiny-video-gallery-js', TPVG_ASSETS.'js/tiny-video-gallery.js', array('jquery'));
}
