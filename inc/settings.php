<?php
/**
 * Site Settings screen.
 *
 * ACF options pages are a PRO feature, so the site-wide values the header,
 * footer and CTA read live in one option the theme owns and edits natively.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions for the settings screen, grouped by section.
 *
 * Keys match the selectors the templates pass to estatein_field(), so a value
 * saved here and a value saved in ACF are read the same way.
 *
 * @return array
 */
function estatein_settings_fields() {
	return array(
		'announcement' => array(
			'label'  => __( 'Announcement bar', 'estatein' ),
			'fields' => array(
				'banner_text'       => array( 'label' => __( 'Text', 'estatein' ), 'hint' => __( 'Leave empty to hide the bar site-wide.', 'estatein' ) ),
				'banner_link_label' => array( 'label' => __( 'Link label', 'estatein' ) ),
				'banner_link_url'   => array( 'label' => __( 'Link URL', 'estatein' ), 'type' => 'url' ),
			),
		),
		'contact' => array(
			'label'  => __( 'Contact details', 'estatein' ),
			'fields' => array(
				'contact_email'   => array( 'label' => __( 'Enquiry inbox', 'estatein' ), 'type' => 'email', 'hint' => __( 'Where form submissions are sent. Defaults to the admin email.', 'estatein' ) ),
				'contact_phone'   => array( 'label' => __( 'Phone', 'estatein' ) ),
				'contact_address' => array( 'label' => __( 'Address', 'estatein' ), 'type' => 'textarea' ),
			),
		),
		'social' => array(
			'label'  => __( 'Social links', 'estatein' ),
			'fields' => array(
				'social_facebook'  => array( 'label' => __( 'Facebook', 'estatein' ), 'type' => 'url' ),
				'social_linkedin'  => array( 'label' => __( 'LinkedIn', 'estatein' ), 'type' => 'url' ),
				'social_twitter'   => array( 'label' => __( 'Twitter', 'estatein' ), 'type' => 'url' ),
				'social_instagram' => array( 'label' => __( 'Instagram', 'estatein' ), 'type' => 'url' ),
				'social_youtube'   => array( 'label' => __( 'YouTube', 'estatein' ), 'type' => 'url' ),
			),
		),
		'cta' => array(
			'label'  => __( 'Closing call to action', 'estatein' ),
			'fields' => array(
				'cta_title' => array( 'label' => __( 'Heading', 'estatein' ), 'hint' => __( 'Shown above the footer on every page.', 'estatein' ) ),
				'cta_text'  => array( 'label' => __( 'Text', 'estatein' ), 'type' => 'textarea' ),
				'cta_label' => array( 'label' => __( 'Button label', 'estatein' ) ),
			),
		),
	);
}

/**
 * Saved settings, read once per request.
 *
 * @return array
 */
function estatein_settings() {
	static $settings = null;

	if ( null === $settings ) {
		$settings = (array) get_option( 'estatein_settings', array() );
	}

	return $settings;
}

/**
 * Register the option and its sections.
 */
function estatein_settings_register() {
	register_setting( 'estatein_settings', 'estatein_settings', array(
		'type'              => 'array',
		'sanitize_callback' => 'estatein_settings_sanitize',
		'default'           => array(),
	) );

	foreach ( estatein_settings_fields() as $section => $group ) {
		add_settings_section( 'estatein_' . $section, $group['label'], '__return_false', 'estatein-settings' );

		foreach ( $group['fields'] as $key => $field ) {
			add_settings_field(
				$key,
				$field['label'],
				'estatein_settings_render_field',
				'estatein-settings',
				'estatein_' . $section,
				array( 'key' => $key, 'field' => $field, 'label_for' => 'estatein-' . $key )
			);
		}
	}
}
add_action( 'admin_init', 'estatein_settings_register' );

/**
 * Render one input.
 *
 * @param array $args Field arguments passed by add_settings_field().
 */
function estatein_settings_render_field( $args ) {
	$key      = $args['key'];
	$field    = $args['field'];
	$type     = isset( $field['type'] ) ? $field['type'] : 'text';
	$settings = estatein_settings();
	$value    = isset( $settings[ $key ] ) ? $settings[ $key ] : '';

	if ( 'textarea' === $type ) {
		printf(
			'<textarea id="estatein-%1$s" name="estatein_settings[%1$s]" rows="3" class="large-text">%2$s</textarea>',
			esc_attr( $key ),
			esc_textarea( $value )
		);
	} else {
		printf(
			'<input type="%1$s" id="estatein-%2$s" name="estatein_settings[%2$s]" value="%3$s" class="regular-text">',
			esc_attr( $type ),
			esc_attr( $key ),
			esc_attr( $value )
		);
	}

	if ( ! empty( $field['hint'] ) ) {
		printf( '<p class="description">%s</p>', esc_html( $field['hint'] ) );
	}
}

/**
 * Sanitise on save, by the type each field declares.
 *
 * @param array $input Submitted values.
 * @return array
 */
function estatein_settings_sanitize( $input ) {
	$clean = array();

	foreach ( estatein_settings_fields() as $group ) {
		foreach ( $group['fields'] as $key => $field ) {
			$raw  = isset( $input[ $key ] ) ? $input[ $key ] : '';
			$type = isset( $field['type'] ) ? $field['type'] : 'text';

			if ( 'url' === $type ) {
				$clean[ $key ] = esc_url_raw( $raw );
			} elseif ( 'email' === $type ) {
				$clean[ $key ] = sanitize_email( $raw );
			} elseif ( 'textarea' === $type ) {
				$clean[ $key ] = sanitize_textarea_field( $raw );
			} else {
				$clean[ $key ] = sanitize_text_field( $raw );
			}
		}
	}

	return $clean;
}

/**
 * Add the menu entry.
 *
 * Top level rather than under Settings: the client edits this far more often
 * than anything in the core Settings menu.
 */
function estatein_settings_menu() {
	// ACF PRO registers the same slug in acf-fields.php; on PRO its screen wins
	// so the client is not shown two versions of the same settings.
	if ( function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	add_menu_page(
		__( 'Site Settings', 'estatein' ),
		__( 'Site Settings', 'estatein' ),
		'manage_options',
		'estatein-settings',
		'estatein_settings_page',
		'dashicons-admin-settings',
		59
	);
}
add_action( 'admin_menu', 'estatein_settings_menu' );

/**
 * Render the settings screen.
 */
function estatein_settings_page() {
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p>
			<?php esc_html_e( 'These values appear across the whole site. Page content is edited on the pages themselves, and listings under Properties.', 'estatein' ); ?>
		</p>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'estatein_settings' );
			do_settings_sections( 'estatein-settings' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}
