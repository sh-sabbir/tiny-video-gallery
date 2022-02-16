<?php

// Register Custom Post Type Tiny Video Gallery
function create_tinyvideogalleryitem_cpt() {

    $labels = array(
        'name' => _x('Tiny Gallery', 'Post Type General Name', 'tiny-video-gallery'),
        'singular_name' => _x('Tiny Video Gallery', 'Post Type Singular Name', 'tiny-video-gallery'),
        'menu_name' => _x('Tiny Gallery', 'Admin Menu text', 'tiny-video-gallery'),
        'name_admin_bar' => _x('Tiny Video Gallery', 'Add New on Toolbar', 'tiny-video-gallery'),
        'archives' => __('Tiny Video Gallery Archives', 'tiny-video-gallery'),
        'attributes' => __('Tiny Video Gallery Attributes', 'tiny-video-gallery'),
        'parent_item_colon' => __('Parent Tiny Video Gallery:', 'tiny-video-gallery'),
        'all_items' => __('All Videos', 'tiny-video-gallery'),
        'add_new_item' => __('Add New Video', 'tiny-video-gallery'),
        'add_new' => __('Add New', 'tiny-video-gallery'),
        'new_item' => __('New Video', 'tiny-video-gallery'),
        'edit_item' => __('Edit Video', 'tiny-video-gallery'),
        'update_item' => __('Update Video', 'tiny-video-gallery'),
        'view_item' => __('View Video', 'tiny-video-gallery'),
        'view_items' => __('View Videos', 'tiny-video-gallery'),
        'search_items' => __('Search Videos', 'tiny-video-gallery'),
        'not_found' => __('Not found', 'tiny-video-gallery'),
        'not_found_in_trash' => __('Not found in Trash', 'tiny-video-gallery'),
        'featured_image' => __('Featured Image', 'tiny-video-gallery'),
        'set_featured_image' => __('Set featured image', 'tiny-video-gallery'),
        'remove_featured_image' => __('Remove featured image', 'tiny-video-gallery'),
        'use_featured_image' => __('Use as featured image', 'tiny-video-gallery'),
        'insert_into_item' => __('Insert into Video', 'tiny-video-gallery'),
        'uploaded_to_this_item' => __('Uploaded to this Video', 'tiny-video-gallery'),
        'items_list' => __('Video list', 'tiny-video-gallery'),
        'items_list_navigation' => __('Video list navigation', 'tiny-video-gallery'),
        'filter_items_list' => __('Filter Video list', 'tiny-video-gallery'),
    );
    $args = array(
        'label' => __('Tiny Video Gallery', 'tiny-video-gallery'),
        'description' => __('', 'tiny-video-gallery'),
        'labels' => $labels,
        'menu_icon' => 'dashicons-format-video',
        'supports' => array('title', 'thumbnail'),
        'taxonomies' => array('tiny_video_category'),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 10,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => false,
        'can_export' => true,
        'has_archive' => false,
        'hierarchical' => false,
        'exclude_from_search' => false,
        'show_in_rest' => true,
        'publicly_queryable' => true,
        'capability_type' => 'post',
    );
    register_post_type('tiny_video_item', $args);
}
add_action('init', 'create_tinyvideogalleryitem_cpt', 0);
