<?php

// Register Taxonomy Category
function create_tinyvideogallerycategory_tax() {

	$labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name', 'tiny-video-gallery' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name', 'tiny-video-gallery' ),
		'search_items'      => __( 'Search Categories', 'tiny-video-gallery' ),
		'all_items'         => __( 'All Categories', 'tiny-video-gallery' ),
		'parent_item'       => __( 'Parent Category', 'tiny-video-gallery' ),
		'parent_item_colon' => __( 'Parent Category:', 'tiny-video-gallery' ),
		'edit_item'         => __( 'Edit Category', 'tiny-video-gallery' ),
		'update_item'       => __( 'Update Category', 'tiny-video-gallery' ),
		'add_new_item'      => __( 'Add New Category', 'tiny-video-gallery' ),
		'new_item_name'     => __( 'New Category Name', 'tiny-video-gallery' ),
		'menu_name'         => __( 'Category', 'tiny-video-gallery' ),
	);
	$args = array(
		'labels' => $labels,
		'description' => __( '', 'tiny-video-gallery' ),
		'hierarchical' => false,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => false,
		'show_tagcloud' => true,
		'show_in_quick_edit' => true,
		'show_admin_column' => true,
		'show_in_rest' => true,
	);
	register_taxonomy( 'tiny_video_category', array('tiny_video_item'), $args );

}
add_action( 'init', 'create_tinyvideogallerycategory_tax' );
