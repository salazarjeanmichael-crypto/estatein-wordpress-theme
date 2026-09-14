<?php
/**
 * Custom fields for properties and testimonials.
 *
 * These are plain WordPress meta boxes rather than an ACF dependency. The
 * theme therefore works on a bare WordPress install; if the client later adds
 * ACF, estatein_field() will happily read from it instead (see helpers.php).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions, keyed by post type.
 *
 * Keeping this as data rather than markup means the render and save routines
 * stay generic: adding a field is a one-line change here.
 *
 * @return array
 */
function estatein_meta_fields() {
	return array(
		'property' => array(
			'price'     => array( 'label' => __( 'Price (USD)', 'estatein' ),  'type' => 'number', 'hint' => __( 'Digits only, e.g. 550000', 'estatein' ) ),
			'bedrooms'  => array( 'label' => __( 'Bedrooms', 'estatein' ),     'type' => 'number' ),
			'bathrooms' => array( 'label' => __( 'Bathrooms', 'estatein' ),    'type' => 'number' ),
			'style'     => array( 'label' => __( 'Style', 'estatein' ),        'type' => 'text',   'hint' => __( 'Shown on the third tag, e.g. Villa', 'estatein' ) ),
			'area'      => array( 'label' => __( 'Floor area', 'estatein' ),   'type' => 'text',   'hint' => __( 'e.g. 2,500 sq ft', 'estatein' ) ),
			'address'   => array( 'label' => __( 'Address', 'estatein' ),      'type' => 'text' ),
		),
		'testimonial' => array(
			'rating'   => array( 'label' => __( 'Rating (1-5)', 'estatein' ), 'type' => 'number' ),
			'author'   => array( 'label' => __( 'Client name', 'estatein' ),  'type' => 'text' ),
			'location' => array( 'label' => __( 'Location', 'estatein' ),     'type' => 'text',  'hint' => __( 'e.g. USA, California', 'estatein' ) ),
		),
		'team' => array(
			'role'    => array( 'label' => __( 'Job title', 'estatein' ), 'type' => 'text', 'hint' => __( 'e.g. Chief Real Estate Officer', 'estatein' ) ),
			'twitter' => array( 'label' => __( 'Twitter URL', 'estatein' ), 'type' => 'text' ),
			'email'   => array( 'label' => __( 'Email', 'estatein' ), 'type' => 'text' ),
		),
	);
}

/**
 * Register the meta boxes.
 */
function estatein_add_meta_boxes() {
	foreach ( estatein_meta_fields() as $post_type => $fields ) {
		add_meta_box(
			'estatein-details',
			__( 'Details', 'estatein' ),
			'estatein_render_meta_box',
			$post_type,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'estatein_add_meta_boxes' );

/**
 * Render the details meta box.
 *
 * @param WP_Post $post Current post.
 */
function estatein_render_meta_box( $post ) {
	$all    = estatein_meta_fields();
	$fields = isset( $all[ $post->post_type ] ) ? $all[ $post->post_type ] : array();

	wp_nonce_field( 'estatein_save_meta', 'estatein_meta_nonce' );

	echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px">';

	foreach ( $fields as $key => $field ) {
		$value = get_post_meta( $post->ID, '_estatein_' . $key, true );
		$id    = 'estatein-' . $key;

		echo '<p style="margin:0">';
		printf(
			'<label for="%1$s" style="display:block;font-weight:600;margin-bottom:4px">%2$s</label>',
			esc_attr( $id ),
			esc_html( $field['label'] )
		);
		printf(
			'<input type="%1$s" id="%2$s" name="estatein_meta[%3$s]" value="%4$s" class="widefat">',
			esc_attr( $field['type'] ),
			esc_attr( $id ),
			esc_attr( $key ),
			esc_attr( $value )
		);

		if ( ! empty( $field['hint'] ) ) {
			printf( '<span class="description">%s</span>', esc_html( $field['hint'] ) );
		}

		echo '</p>';
	}

	echo '</div>';
}

/**
 * Save the details meta box.
 *
 * @param int $post_id Post ID.
 */
function estatein_save_meta( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['estatein_meta_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['estatein_meta_nonce'] ) ), 'estatein_save_meta' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$all       = estatein_meta_fields();
	$post_type = get_post_type( $post_id );

	if ( ! isset( $all[ $post_type ] ) ) {
		return;
	}

	$submitted = isset( $_POST['estatein_meta'] ) ? (array) wp_unslash( $_POST['estatein_meta'] ) : array();

	foreach ( $all[ $post_type ] as $key => $field ) {
		$raw = isset( $submitted[ $key ] ) ? $submitted[ $key ] : '';

		if ( 'number' === $field['type'] ) {
			$value = ( '' === $raw ) ? '' : (string) abs( (float) $raw );
		} else {
			$value = sanitize_text_field( $raw );
		}

		if ( '' === $value ) {
			delete_post_meta( $post_id, '_estatein_' . $key );
		} else {
			update_post_meta( $post_id, '_estatein_' . $key, $value );
		}
	}
}
add_action( 'save_post', 'estatein_save_meta' );

/**
 * Read one of the theme's meta values, preferring ACF when it is installed.
 *
 * @param string $key      Field key without the _estatein_ prefix.
 * @param int    $post_id  Post ID, defaults to the current post.
 * @param mixed  $fallback Value when nothing is stored.
 * @return mixed
 */
function estatein_meta( $key, $post_id = 0, $fallback = '' ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$acf     = estatein_field( $key, null, $post_id );

	if ( null !== $acf && '' !== $acf ) {
		return $acf;
	}

	$value = get_post_meta( $post_id, '_estatein_' . $key, true );

	return ( '' === $value ) ? $fallback : $value;
}

/**
 * Show the property price as a sortable admin column.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function estatein_property_columns( $columns ) {
	$new = array();

	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;

		if ( 'title' === $key ) {
			$new['estatein_price'] = __( 'Price', 'estatein' );
		}
	}

	return $new;
}
add_filter( 'manage_property_posts_columns', 'estatein_property_columns' );

/**
 * Render the price column.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function estatein_property_column_content( $column, $post_id ) {
	if ( 'estatein_price' === $column ) {
		$price = estatein_meta( 'price', $post_id );
		echo $price ? esc_html( estatein_price( $price ) ) : '&mdash;';
	}
}
add_action( 'manage_property_posts_custom_column', 'estatein_property_column_content', 10, 2 );
