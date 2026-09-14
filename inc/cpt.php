<?php
/**
 * Custom post types and taxonomies.
 *
 * Properties, testimonials and FAQs are first-class content types rather than
 * pages, so the client gets a dedicated admin screen for each and the front end
 * can query, filter and paginate them with a standard WP_Query.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the theme's post types.
 */
function estatein_register_post_types() {

	/* --- Properties ---------------------------------------------------- */
	register_post_type( 'property', array(
		'labels' => array(
			'name'               => __( 'Properties', 'estatein' ),
			'singular_name'      => __( 'Property', 'estatein' ),
			'add_new'            => __( 'Add New', 'estatein' ),
			'add_new_item'       => __( 'Add New Property', 'estatein' ),
			'edit_item'          => __( 'Edit Property', 'estatein' ),
			'new_item'           => __( 'New Property', 'estatein' ),
			'view_item'          => __( 'View Property', 'estatein' ),
			'search_items'       => __( 'Search Properties', 'estatein' ),
			'not_found'          => __( 'No properties found', 'estatein' ),
			'all_items'          => __( 'All Properties', 'estatein' ),
			'menu_name'          => __( 'Properties', 'estatein' ),
			'featured_image'     => __( 'Property Photo', 'estatein' ),
			'set_featured_image' => __( 'Set property photo', 'estatein' ),
		),
		'public'        => true,
		'has_archive'   => true,
		'rewrite'       => array( 'slug' => 'property', 'with_front' => false ),
		'menu_icon'     => 'dashicons-building',
		'menu_position' => 20,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
		'show_in_rest'  => true,
		'hierarchical'  => false,
	) );

	/* --- Testimonials -------------------------------------------------- */
	register_post_type( 'testimonial', array(
		'labels' => array(
			'name'          => __( 'Testimonials', 'estatein' ),
			'singular_name' => __( 'Testimonial', 'estatein' ),
			'add_new_item'  => __( 'Add New Testimonial', 'estatein' ),
			'edit_item'     => __( 'Edit Testimonial', 'estatein' ),
			'all_items'     => __( 'All Testimonials', 'estatein' ),
			'menu_name'     => __( 'Testimonials', 'estatein' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'menu_icon'     => 'dashicons-format-quote',
		'menu_position' => 21,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'show_in_rest'  => true,
	) );

	/* --- Team ---------------------------------------------------------- */
	register_post_type( 'team', array(
		'labels' => array(
			'name'           => __( 'Team', 'estatein' ),
			'singular_name'  => __( 'Team Member', 'estatein' ),
			'add_new_item'   => __( 'Add New Team Member', 'estatein' ),
			'edit_item'      => __( 'Edit Team Member', 'estatein' ),
			'all_items'      => __( 'All Team Members', 'estatein' ),
			'menu_name'      => __( 'Team', 'estatein' ),
			'featured_image' => __( 'Portrait', 'estatein' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'menu_icon'     => 'dashicons-groups',
		'menu_position' => 23,
		'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
		'show_in_rest'  => true,
	) );

	/* --- FAQs ---------------------------------------------------------- */
	register_post_type( 'faq', array(
		'labels' => array(
			'name'          => __( 'FAQs', 'estatein' ),
			'singular_name' => __( 'FAQ', 'estatein' ),
			'add_new_item'  => __( 'Add New FAQ', 'estatein' ),
			'edit_item'     => __( 'Edit FAQ', 'estatein' ),
			'all_items'     => __( 'All FAQs', 'estatein' ),
			'menu_name'     => __( 'FAQs', 'estatein' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'menu_icon'     => 'dashicons-editor-help',
		'menu_position' => 22,
		'supports'      => array( 'title', 'editor', 'page-attributes' ),
		'show_in_rest'  => true,
	) );
}
add_action( 'init', 'estatein_register_post_types' );

/**
 * Register property taxonomies.
 */
function estatein_register_taxonomies() {
	register_taxonomy( 'property_type', 'property', array(
		'labels' => array(
			'name'          => __( 'Property Types', 'estatein' ),
			'singular_name' => __( 'Property Type', 'estatein' ),
			'all_items'     => __( 'All Types', 'estatein' ),
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'property-type' ),
	) );

	register_taxonomy( 'property_location', 'property', array(
		'labels' => array(
			'name'          => __( 'Locations', 'estatein' ),
			'singular_name' => __( 'Location', 'estatein' ),
			'all_items'     => __( 'All Locations', 'estatein' ),
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'location' ),
	) );
}
add_action( 'init', 'estatein_register_taxonomies' );

/**
 * Flush rewrite rules once after the post types exist.
 *
 * Flushing on every load is expensive; never flushing means /property/ 404s
 * until someone re-saves permalinks. A one-shot option is the middle ground.
 */
function estatein_maybe_flush_rewrites() {
	if ( get_option( 'estatein_rewrites_flushed' ) === ESTATEIN_VERSION ) {
		return;
	}

	estatein_register_post_types();
	estatein_register_taxonomies();
	flush_rewrite_rules();

	update_option( 'estatein_rewrites_flushed', ESTATEIN_VERSION );
}
add_action( 'after_switch_theme', 'estatein_maybe_flush_rewrites' );
add_action( 'admin_init', 'estatein_maybe_flush_rewrites' );

/**
 * Tune the main query on property archives.
 *
 * Six per page fills two rows of the three-column grid.
 *
 * @param WP_Query $query Main query.
 */
function estatein_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( 'property' )
		|| $query->is_tax( array( 'property_type', 'property_location' ) ) ) {
		$query->set( 'posts_per_page', 6 );
	}
}
add_action( 'pre_get_posts', 'estatein_archive_query' );
