<?php

/**
 * Plugin Name
 *
 * @package           TinyVideoGallery
 * @author            Sabbir Hasan
 * @copyright         2021 Sabbir Hasan
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Tiny Video Gallery
 * Description:       A tiny video gallery plugin with category and filtering support. Native support for Elementor, Gutenberg, WP Bakery, Visual Composer and other pagebuilder with shortcode.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.0
 * Author:            Sabbir Hasan
 * Author URI:        https://iamsabbir.dev
 * Text Domain:       tiny-video-gallery
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://example.com/my-plugin/
 * Domain Path:       /languages
 */

defined('ABSPATH') || die();

//Define Plugin Constants
define('TPVG__FILE__', __FILE__);
define('TPVG_DIR_PATH', plugin_dir_path(TPVG__FILE__));
define('TPVG_DIR_URL', plugin_dir_url(TPVG__FILE__));
define('TPVG_ASSETS', trailingslashit(TPVG_DIR_URL . 'assets'));


if (!class_exists('TinyVideoGallery')) {
    class TinyVideoGallery {

        /**
         * Constructor
         */
        public function __construct() {
            $this->load_dependency();
            $this->setup_actions();

            add_action('wp_enqueue_scripts', [$this, 'load_scripts']);
        }

        /**
         * Loading Deependency
         */
        public function load_dependency() {
            // Register Custom Post Type
            require(TPVG_DIR_PATH . 'inc/cpt.php');

            // Register Custom Taxonomy
            require(TPVG_DIR_PATH . 'inc/taxonomy.php');

            // Register Custom Post Type
            require(TPVG_DIR_PATH . 'inc/metabox.php');

            // Register Widgets
            require(TPVG_DIR_PATH . 'inc/widgets.php');
        }


        /**
         * Load Scripts
         */
        public function load_scripts() {
            wp_enqueue_style('tiny-video-yt-css', TPVG_ASSETS . 'css/youtube-overlay.css');
            wp_enqueue_script('tiny-video-yt-js', TPVG_ASSETS . 'js/youtube-overlay.js', array('jquery'));

            wp_enqueue_style('tiny-video-gallery-css', TPVG_ASSETS . 'css/tiny-video-gallery.css');
            wp_enqueue_script('tiny-video-gallery-js', TPVG_ASSETS . 'js/tiny-video-gallery.js', array('jquery'));
        }

        /**
         * Setting up Hooks
         */
        public function setup_actions() {
            //Main plugin hooks
            register_activation_hook(TPVG__FILE__, array('TinyVideoGallery', 'activate'));
            register_deactivation_hook(TPVG__FILE__, array('TinyVideoGallery', 'deactivate'));
        }

        /**
         * Activate callback
         */
        public static function activate() {
            //Activation code in here
            //TODO: Handle Custom Meta Here
        }

        /**
         * Deactivate callback
         */
        public static function deactivate() {
            //Deactivation code in here
            //TODO: Handle Custom Meta Here
        }
    }


    // instantiate the plugin class
    $tiny_video_galery = new TinyVideoGallery();
}
