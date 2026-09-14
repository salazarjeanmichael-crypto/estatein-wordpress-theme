<?php
/**
 * Optional Advanced Custom Fields integration.
 *
 * ACF is never required; helpers.php falls back when it is absent. Groups are
 * declared in PHP, not the database, so they are versioned with the theme.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add the Site Settings options page.
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
}
add_action( 'acf/init', 'estatein_acf_options_page' );

/**
 * Register the Site Settings field group.
 */
function estatein_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

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
			array(
				'key'   => 'field_estatein_banner_tab',
				'label' => __( 'Announcement Banner', 'estatein' ),
				'type'  => 'tab',
			),
			array(
				'key'           => 'field_estatein_banner_text',
				'label'         => __( 'Banner text', 'estatein' ),
				'name'          => 'banner_text',
				'type'          => 'text',
				'instructions'  => __( 'Leave empty to hide the banner.', 'estatein' ),
				'default_value' => __( 'Discover Your Dream Property with Estatein', 'estatein' ),
			),
			array(
				'key'           => 'field_estatein_banner_link_label',
				'label'         => __( 'Banner link label', 'estatein' ),
				'name'          => 'banner_link_label',
				'type'          => 'text',
				'default_value' => __( 'Learn More', 'estatein' ),
			),
			array(
				'key'   => 'field_estatein_banner_link_url',
				'label' => __( 'Banner link URL', 'estatein' ),
				'name'  => 'banner_link_url',
				'type'  => 'url',
			),

			array(
				'key'   => 'field_estatein_contact_tab',
				'label' => __( 'Contact', 'estatein' ),
				'type'  => 'tab',
			),
			array(
				'key'          => 'field_estatein_contact_email',
				'label'        => __( 'Enquiry inbox', 'estatein' ),
				'name'         => 'contact_email',
				'type'         => 'email',
				'instructions' => __( 'Where contact form submissions are sent. Defaults to the admin email.', 'estatein' ),
			),
			array(
				'key'   => 'field_estatein_contact_phone',
				'label' => __( 'Phone', 'estatein' ),
				'name'  => 'contact_phone',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_estatein_contact_address',
				'label' => __( 'Address', 'estatein' ),
				'name'  => 'contact_address',
				'type'  => 'textarea',
				'rows'  => 3,
			),

			array(
				'key'   => 'field_estatein_social_tab',
				'label' => __( 'Social', 'estatein' ),
				'type'  => 'tab',
			),
			array(
				'key'   => 'field_estatein_social_facebook',
				'label' => __( 'Facebook URL', 'estatein' ),
				'name'  => 'social_facebook',
				'type'  => 'url',
			),
			array(
				'key'   => 'field_estatein_social_linkedin',
				'label' => __( 'LinkedIn URL', 'estatein' ),
				'name'  => 'social_linkedin',
				'type'  => 'url',
			),
			array(
				'key'   => 'field_estatein_social_twitter',
				'label' => __( 'Twitter URL', 'estatein' ),
				'name'  => 'social_twitter',
				'type'  => 'url',
			),
			array(
				'key'   => 'field_estatein_social_youtube',
				'label' => __( 'YouTube URL', 'estatein' ),
				'name'  => 'social_youtube',
				'type'  => 'url',
			),
		),
	) );
}
add_action( 'acf/init', 'estatein_acf_fields' );

/**
 * Tell the client where to manage content, on first look at the dashboard.
 */
function estatein_admin_notice() {
	$screen = get_current_screen();

	if ( ! $screen || 'dashboard' !== $screen->id || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( get_user_meta( get_current_user_id(), 'estatein_notice_dismissed', true ) ) {
		return;
	}
	?>
	<div class="notice notice-info">
		<p>
			<strong><?php esc_html_e( 'Estatein theme', 'estatein' ); ?></strong> &mdash;
			<?php esc_html_e( 'Manage listings under Properties, client quotes under Testimonials, and the questions block under FAQs. Menus live in Appearance > Menus.', 'estatein' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'estatein_admin_notice' );
