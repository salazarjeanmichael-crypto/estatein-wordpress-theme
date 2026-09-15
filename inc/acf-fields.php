<?php
/**
 * Advanced Custom Fields integration.
 *
 * Groups are declared in PHP, not the database, so they are versioned with the
 * theme and deploy with it instead of needing a separate JSON import.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether ACF is available to register field groups.
 *
 * @return bool
 */
function estatein_acf_active() {
	return function_exists( 'acf_add_local_field_group' );
}

/**
 * Build one field definition.
 *
 * Field names deliberately match the native meta keys in meta.php, so
 * estatein_meta() reads the same name whichever storage is in play.
 *
 * @param string $name  Field name.
 * @param string $label Visible label.
 * @param string $type  ACF field type.
 * @param array  $extra Any additional ACF settings.
 * @return array
 */
function estatein_acf_field( $name, $label, $type = 'text', array $extra = array() ) {
	return array_merge( array(
		'key'   => 'field_estatein_' . $name,
		'label' => $label,
		'name'  => $name,
		'type'  => $type,
	), $extra );
}

/**
 * Settings shared by the pricing textareas.
 *
 * Repeaters are ACF PRO, so each pricing card is one textarea of piped rows;
 * spelling the syntax out in the instructions keeps that usable for a client.
 *
 * @param string $extra Optional sentence appended to the instructions.
 * @return array
 */
function estatein_acf_rows_args( $extra = '' ) {
	return array(
		'instructions' => trim( __( 'Each row becomes a line in this pricing card.', 'estatein' ) . ' ' . $extra ),
		'rows'         => 6,
		'new_lines'    => '',
	);
}

/**
 * Register the theme's field groups.
 */
function estatein_acf_fields() {
	if ( ! estatein_acf_active() ) {
		return;
	}

	/* --- Property ------------------------------------------------------ */
	acf_add_local_field_group( array(
		'key'                   => 'group_estatein_property',
		'title'                 => __( 'Property Details', 'estatein' ),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'hide_on_screen'        => array( 'custom_fields' ),
		'location'              => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'property',
				),
			),
		),
		'fields' => array(

			estatein_acf_field( 'price', __( 'Price (USD)', 'estatein' ), 'number', array(
				'instructions' => __( 'Digits only, e.g. 550000. Formatting is applied on output.', 'estatein' ),
				'min'          => 0,
				'wrapper'      => array( 'width' => '33' ),
			) ),
			estatein_acf_field( 'bedrooms', __( 'Bedrooms', 'estatein' ), 'number', array(
				'instructions' => __( 'Shown in the facts row under the description.', 'estatein' ),
				'min'     => 0,
				'wrapper' => array( 'width' => '33' ),
			) ),
			estatein_acf_field( 'bathrooms', __( 'Bathrooms', 'estatein' ), 'number', array(
				'min'     => 0,
				'wrapper' => array( 'width' => '34' ),
			) ),
			estatein_acf_field( 'area', __( 'Area', 'estatein' ), 'text', array(
				'instructions' => __( 'Free text, e.g. 2,500 Square Feet.', 'estatein' ),
				'wrapper'      => array( 'width' => '33' ),
			) ),
			estatein_acf_field( 'style', __( 'Style', 'estatein' ), 'text', array(
				'instructions' => __( 'Shown on the third card tag, e.g. Villa.', 'estatein' ),
				'wrapper'      => array( 'width' => '33' ),
			) ),
			estatein_acf_field( 'year', __( 'Build year', 'estatein' ), 'number', array(
				'instructions' => __( 'Four digits, e.g. 2019.', 'estatein' ),
				'min'          => 1800,
				'wrapper'      => array( 'width' => '34' ),
			) ),
			estatein_acf_field( 'address', __( 'Address', 'estatein' ), 'text', array(
				'instructions' => __( 'Appears beside the title and is carried into the inquiry form.', 'estatein' ),
			) ),

			estatein_acf_field( 'features', __( 'Key features and amenities', 'estatein' ), 'textarea', array(
				'instructions' => __( 'Each one becomes a row in the Key Features and Amenities panel.', 'estatein' ),
				'rows'         => 8,
				'new_lines'    => '',
			) ),

			estatein_acf_field( 'pricing_intro', __( 'Comprehensive Pricing Details', 'estatein' ), 'message', array(
				'message' => __( 'Each box below becomes one card in the pricing section on the listing. Leave a box empty to drop that card. The listing price is added to Total Initial Costs on its own.', 'estatein' ),
			) ),

			estatein_acf_field( 'fees', __( 'Additional Fees', 'estatein' ), 'textarea', estatein_acf_rows_args() ),
			estatein_acf_field( 'monthly', __( 'Monthly Costs', 'estatein' ), 'textarea', estatein_acf_rows_args() ),
			estatein_acf_field( 'initial', __( 'Total Initial Costs', 'estatein' ), 'textarea', estatein_acf_rows_args(
				__( 'The listing price is added as the first row automatically, so it never has to be kept in step by hand.', 'estatein' )
			) ),
			estatein_acf_field( 'expenses', __( 'Monthly Expenses', 'estatein' ), 'textarea', estatein_acf_rows_args() ),
		),
	) );

	/* --- Home page ----------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'            => 'group_estatein_home',
		'title'          => __( 'Home Page Content', 'estatein' ),
		'position'       => 'normal',
		'menu_order'     => 0,
		'hide_on_screen' => array( 'custom_fields' ),
		'location'       => array(
			array(
				array(
					'param'    => 'page_type',
					'operator' => '==',
					'value'    => 'front_page',
				),
			),
		),
		'fields' => array(
			estatein_acf_field( 'hero_heading', __( 'Hero heading', 'estatein' ), 'text', array(
				'instructions' => __( 'The h1 at the top of the home page.', 'estatein' ),
			) ),
			estatein_acf_field( 'hero_text', __( 'Hero text', 'estatein' ), 'textarea', array( 'rows' => 3, 'new_lines' => '' ) ),
			estatein_acf_field( 'hero_image', __( 'Hero photo', 'estatein' ), 'image', array(
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'instructions'  => __( 'Roughly 3:4. Falls back to the photo shipped with the theme.', 'estatein' ),
			) ),
			estatein_acf_field( 'hero_stats', __( 'Hero statistics', 'estatein' ), 'textarea', array(
				'instructions' => __( 'The counters under the hero buttons.', 'estatein' ),
				'rows'         => 4,
				'new_lines'    => '',
			) ),
			estatein_acf_field( 'home_features', __( 'Feature tiles', 'estatein' ), 'textarea', array(
				'instructions' => __( 'The four linked tiles in the strip below the hero.', 'estatein' ),
				'rows'         => 5,
				'new_lines'    => '',
			) ),
		),
	) );

	/* --- Pages --------------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'            => 'group_estatein_page',
		'title'          => __( 'Page Heading', 'estatein' ),
		'position'       => 'normal',
		'menu_order'     => 0,
		'hide_on_screen' => array( 'custom_fields' ),
		// Everything except the front page, which has its own group above.
		'location'       => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'page',
				),
				array(
					'param'    => 'page_type',
					'operator' => '!=',
					'value'    => 'front_page',
				),
			),
		),
		'fields' => array(
			estatein_acf_field( 'page_heading', __( 'Heading', 'estatein' ), 'text', array(
				'instructions' => __( 'Replaces the heading at the top of the page. Leave empty to use the page title.', 'estatein' ),
			) ),
			estatein_acf_field( 'page_intro', __( 'Intro text', 'estatein' ), 'textarea', array(
				'instructions' => __( 'The paragraph under the heading.', 'estatein' ),
				'rows'         => 3,
				'new_lines'    => '',
			) ),
		),
	) );

	/* --- About page ---------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'            => 'group_estatein_about',
		'title'          => __( 'About Page Content', 'estatein' ),
		'position'       => 'normal',
		'hide_on_screen' => array( 'custom_fields' ),
		'location'       => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'page-about.php',
				),
			),
		),
		'fields' => array(
			estatein_acf_field( 'about_stats', __( 'Statistics', 'estatein' ), 'textarea', array(
				'instructions' => __( 'The counters beside the journey photo.', 'estatein' ),
				'rows'         => 4,
				'new_lines'    => '',
			) ),
		),
	) );

	/* --- Testimonial --------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'            => 'group_estatein_testimonial',
		'title'          => __( 'Testimonial Details', 'estatein' ),
		'position'       => 'normal',
		'hide_on_screen' => array( 'custom_fields' ),
		'location'       => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'testimonial',
				),
			),
		),
		'fields' => array(
			estatein_acf_field( 'rating', __( 'Rating', 'estatein' ), 'number', array(
				'instructions' => __( 'Whole number from 1 to 5.', 'estatein' ),
				'min'          => 1,
				'max'          => 5,
				'default_value' => 5,
				'wrapper'      => array( 'width' => '33' ),
			) ),
			estatein_acf_field( 'author', __( 'Client name', 'estatein' ), 'text', array(
				'wrapper' => array( 'width' => '33' ),
			) ),
			estatein_acf_field( 'location', __( 'Location', 'estatein' ), 'text', array(
				'instructions' => __( 'e.g. USA, California', 'estatein' ),
				'wrapper'      => array( 'width' => '34' ),
			) ),
		),
	) );

	/* --- Team ---------------------------------------------------------- */
	acf_add_local_field_group( array(
		'key'            => 'group_estatein_team',
		'title'          => __( 'Team Member Details', 'estatein' ),
		'position'       => 'normal',
		'hide_on_screen' => array( 'custom_fields' ),
		'location'       => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'team',
				),
			),
		),
		'fields' => array(
			estatein_acf_field( 'role', __( 'Job title', 'estatein' ), 'text', array(
				'instructions' => __( 'e.g. Chief Real Estate Officer', 'estatein' ),
				'wrapper'      => array( 'width' => '34' ),
			) ),
			estatein_acf_field( 'twitter', __( 'Twitter URL', 'estatein' ), 'url', array(
				'wrapper' => array( 'width' => '33' ),
			) ),
			estatein_acf_field( 'email', __( 'Email', 'estatein' ), 'email', array(
				'wrapper' => array( 'width' => '33' ),
			) ),
		),
	) );
}
add_action( 'acf/init', 'estatein_acf_fields' );

/**
 * Register the Site Settings options page.
 *
 * Options pages are an ACF PRO feature, so on the free plugin this does
 * nothing and estatein_field( ..., 'option' ) keeps returning its defaults.
 */
function estatein_acf_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page( array(
		'page_title' => __( 'Site Settings', 'estatein' ),
		'menu_title' => __( 'Site Settings', 'estatein' ),
		'menu_slug'  => 'estatein-settings',
		'capability' => 'manage_options',
		'icon_url'   => 'dashicons-admin-settings',
		'position'   => 59,
		'redirect'   => false,
	) );

	acf_add_local_field_group( array(
		'key'      => 'group_estatein_settings',
		'title'    => __( 'Site Settings', 'estatein' ),
		'location' => array(
			array(
				array(
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'estatein-settings',
				),
			),
		),
		'fields' => array(
			estatein_acf_field( 'banner_text', __( 'Banner text', 'estatein' ), 'text', array(
				'instructions' => __( 'Leave empty to hide the announcement bar.', 'estatein' ),
			) ),
			estatein_acf_field( 'banner_link_label', __( 'Banner link label', 'estatein' ) ),
			estatein_acf_field( 'banner_link_url', __( 'Banner link URL', 'estatein' ), 'url' ),
			estatein_acf_field( 'contact_email', __( 'Enquiry inbox', 'estatein' ), 'email', array(
				'instructions' => __( 'Where contact form submissions go. Defaults to the admin email.', 'estatein' ),
			) ),
			estatein_acf_field( 'contact_phone', __( 'Phone', 'estatein' ) ),
			estatein_acf_field( 'contact_address', __( 'Address', 'estatein' ), 'textarea', array( 'rows' => 3 ) ),
			estatein_acf_field( 'social_facebook', __( 'Facebook URL', 'estatein' ), 'url' ),
			estatein_acf_field( 'social_linkedin', __( 'LinkedIn URL', 'estatein' ), 'url' ),
			estatein_acf_field( 'social_twitter', __( 'Twitter URL', 'estatein' ), 'url' ),
			estatein_acf_field( 'social_instagram', __( 'Instagram URL', 'estatein' ), 'url' ),
			estatein_acf_field( 'social_youtube', __( 'YouTube URL', 'estatein' ), 'url' ),
		),
	) );
}
add_action( 'acf/init', 'estatein_acf_options_page' );

/**
 * Mirror saved ACF values back onto the native _estatein_ meta keys.
 *
 * The property filters and the admin price column query those keys directly,
 * so without this an edit made in ACF would leave filtering on a stale value.
 *
 * @param int|string $post_id Post ID, or an ACF string ID such as "options".
 */
function estatein_acf_sync_to_meta( $post_id ) {
	if ( ! is_numeric( $post_id ) ) {
		return;
	}

	$post_id   = (int) $post_id;
	$post_type = get_post_type( $post_id );
	$all       = estatein_meta_fields();

	if ( ! isset( $all[ $post_type ] ) ) {
		return;
	}

	foreach ( array_keys( $all[ $post_type ] ) as $key ) {
		$value = get_field( $key, $post_id );

		if ( null === $value || '' === $value || false === $value ) {
			delete_post_meta( $post_id, '_estatein_' . $key );
			continue;
		}

		update_post_meta( $post_id, '_estatein_' . $key, is_scalar( $value ) ? $value : wp_json_encode( $value ) );
	}
}
add_action( 'acf/save_post', 'estatein_acf_sync_to_meta', 20 );

/**
 * Point the client at the right screens on their first look at the dashboard.
 */
function estatein_admin_notice() {
	$screen = get_current_screen();

	if ( ! $screen || 'dashboard' !== $screen->id || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="notice notice-info">
		<p>
			<strong><?php esc_html_e( 'Estatein theme', 'estatein' ); ?></strong> &mdash;
			<?php esc_html_e( 'Listings live under Properties, quotes under Testimonials, staff under Team, and the question block under FAQs. Menus are in Appearance > Menus.', 'estatein' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'estatein_admin_notice' );
