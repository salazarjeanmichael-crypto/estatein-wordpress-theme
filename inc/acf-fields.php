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
				'min'     => 0,
				'wrapper' => array( 'width' => '33' ),
			) ),
			estatein_acf_field( 'bathrooms', __( 'Bathrooms', 'estatein' ), 'number', array(
				'min'     => 0,
				'wrapper' => array( 'width' => '34' ),
			) ),
			estatein_acf_field( 'style', __( 'Style', 'estatein' ), 'text', array(
				'instructions' => __( 'Shown on the third card tag, e.g. Villa.', 'estatein' ),
				'wrapper'      => array( 'width' => '33' ),
			) ),
			estatein_acf_field( 'area', __( 'Floor area', 'estatein' ), 'text', array(
				'instructions' => __( 'e.g. 2,500 sq ft', 'estatein' ),
				'wrapper'      => array( 'width' => '33' ),
			) ),
			estatein_acf_field( 'address', __( 'Address', 'estatein' ), 'text', array(
				'wrapper' => array( 'width' => '34' ),
			) ),
			estatein_acf_field( 'year', __( 'Build year', 'estatein' ), 'number', array(
				'instructions' => __( 'Four digits, e.g. 2019.', 'estatein' ),
				'min'          => 1800,
				'wrapper'      => array( 'width' => '33' ),
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
