<?php
/**
 * Shared helper for the seeding scripts.
 *
 * Both seeders import the same theme images, so the routine lives here rather
 * than being copied into each of them.
 *
 * @package Estatein
 */

if ( 'cli' !== php_sapi_name() ) {
	exit( 'This script runs from the command line only.' );
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

/**
 * Import a theme image into the media library once, and return its ID.
 *
 * Gallery images pass a scope so each listing gets its own attachment: the
 * gallery is read from post_parent, and a shared one can only belong to one.
 *
 * @param string $filename File inside assets/img.
 * @param string $scope    Optional owner key, so the same file can import twice.
 * @return int Attachment ID, or 0 on failure.
 */
function estatein_seed_image( $filename, $scope = '' ) {
	$marker = $scope ? $scope . '/' . $filename : $filename;
	$existing = get_posts( array(
		'post_type'      => 'attachment',
		'posts_per_page' => 1,
		'post_status'    => 'inherit',
		'meta_key'       => '_estatein_seed_source',
		'meta_value'     => $marker,
		'fields'         => 'ids',
	) );

	if ( $existing ) {
		return (int) $existing[0];
	}

	$source = get_template_directory() . '/assets/img/' . $filename;

	if ( ! file_exists( $source ) ) {
		echo "  ! missing image {$filename}\n";
		return 0;
	}

	$upload = wp_upload_bits( $scope ? $scope . '-' . $filename : $filename, null, file_get_contents( $source ) );

	if ( ! empty( $upload['error'] ) ) {
		echo "  ! upload failed for {$filename}: {$upload['error']}\n";
		return 0;
	}

	$attachment_id = wp_insert_attachment( array(
		'post_mime_type' => 'png' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ? 'image/png' : 'image/jpeg',
		'post_title'     => pathinfo( $filename, PATHINFO_FILENAME ),
		'post_status'    => 'inherit',
	), $upload['file'] );

	wp_update_attachment_metadata(
		$attachment_id,
		wp_generate_attachment_metadata( $attachment_id, $upload['file'] )
	);

	update_post_meta( $attachment_id, '_estatein_seed_source', $marker );

	return (int) $attachment_id;
}
