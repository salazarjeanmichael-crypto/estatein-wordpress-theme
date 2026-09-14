<?php
/**
 * Copy native meta box values into ACF storage.
 *
 * ACF keys values by field name while the native boxes prefix with _estatein_,
 * so without this an ACF install shows empty inputs over existing content.
 *
 * @package Estatein
 */

if ( 'cli' !== php_sapi_name() ) {
	exit( 'This script runs from the command line only.' );
}

// Four levels up: tools -> estatein -> themes -> wp-content -> root. From
// __DIR__, not getcwd(), so the script runs from any working directory.
require_once dirname( __DIR__, 4 ) . '/wp-load.php';

if ( ! function_exists( 'update_field' ) ) {
	exit( "ACF is not active - nothing to migrate.\n" );
}

$moved = 0;
$empty = 0;

foreach ( estatein_meta_fields() as $post_type => $fields ) {
	$posts = get_posts( array(
		'post_type'      => $post_type,
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'fields'         => 'ids',
	) );

	printf( "%s: %d posts\n", $post_type, count( $posts ) );

	foreach ( $posts as $post_id ) {
		foreach ( array_keys( $fields ) as $key ) {
			$native = get_post_meta( $post_id, '_estatein_' . $key, true );

			if ( '' === $native ) {
				continue;
			}

			// Never overwrite a value an editor has already set in ACF.
			$current = get_field( $key, $post_id );

			if ( '' !== $current && null !== $current && false !== $current ) {
				$empty++;
				continue;
			}

			update_field( $key, $native, $post_id );
			$moved++;
		}
	}
}

printf( "\nmigrated %d values, skipped %d already set in ACF\n", $moved, $empty );
printf( "Native _estatein_* meta is left in place as a fallback.\n" );
