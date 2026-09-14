<?php
/**
 * Property search and filtering.
 *
 * Filters submit with GET so a filtered view is shareable and the back button
 * behaves. Every value is sanitised before it reaches WP_Query.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read the active filters from the query string.
 *
 * @return array
 */
function estatein_active_filters() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only, public filtering.
	return array(
		'keyword'  => isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
		'location' => isset( $_GET['location'] ) ? sanitize_title( wp_unslash( $_GET['location'] ) ) : '',
		'type'     => isset( $_GET['type'] ) ? sanitize_title( wp_unslash( $_GET['type'] ) ) : '',
		'price'    => isset( $_GET['price'] ) ? sanitize_text_field( wp_unslash( $_GET['price'] ) ) : '',
		'beds'     => isset( $_GET['beds'] ) ? absint( $_GET['beds'] ) : 0,
	);
	// phpcs:enable
}

/**
 * Price brackets offered in the filter bar.
 *
 * Keys are the URL value; each entry is [min, max]. A null max means no ceiling.
 *
 * @return array
 */
function estatein_price_ranges() {
	return array(
		'0-500000'       => array( 'label' => __( 'Under $500,000', 'estatein' ),      'min' => 0,       'max' => 500000 ),
		'500000-750000'  => array( 'label' => __( '$500,000 - $750,000', 'estatein' ), 'min' => 500000,  'max' => 750000 ),
		'750000-1000000' => array( 'label' => __( '$750,000 - $1M', 'estatein' ),      'min' => 750000,  'max' => 1000000 ),
		'1000000-'       => array( 'label' => __( '$1M and above', 'estatein' ),       'min' => 1000000, 'max' => null ),
	);
}

/**
 * Build the WP_Query for the properties listing, honouring active filters.
 *
 * @param int $per_page Results per page.
 * @return WP_Query
 */
function estatein_property_query( $per_page = 6 ) {
	$filters = estatein_active_filters();
	$paged   = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

	$args = array(
		'post_type'      => 'property',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
		'paged'          => $paged,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	);

	if ( $filters['keyword'] ) {
		$args['s'] = $filters['keyword'];
	}

	$tax_query = array();

	if ( $filters['location'] ) {
		$tax_query[] = array(
			'taxonomy' => 'property_location',
			'field'    => 'slug',
			'terms'    => $filters['location'],
		);
	}

	if ( $filters['type'] ) {
		$tax_query[] = array(
			'taxonomy' => 'property_type',
			'field'    => 'slug',
			'terms'    => $filters['type'],
		);
	}

	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}

	if ( $tax_query ) {
		$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}

	$meta_query = array();

	$ranges = estatein_price_ranges();

	if ( $filters['price'] && isset( $ranges[ $filters['price'] ] ) ) {
		$range = $ranges[ $filters['price'] ];

		$meta_query[] = array(
			'key'     => '_estatein_price',
			'value'   => null === $range['max'] ? $range['min'] : array( $range['min'], $range['max'] ),
			'type'    => 'NUMERIC',
			'compare' => null === $range['max'] ? '>=' : 'BETWEEN',
		);
	}

	if ( $filters['beds'] ) {
		$meta_query[] = array(
			'key'     => '_estatein_bedrooms',
			'value'   => $filters['beds'],
			'type'    => 'NUMERIC',
			'compare' => '>=',
		);
	}

	if ( count( $meta_query ) > 1 ) {
		$meta_query['relation'] = 'AND';
	}

	if ( $meta_query ) {
		$args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
	}

	return new WP_Query( $args );
}

/**
 * Whether any filter is currently applied.
 *
 * @return bool
 */
function estatein_has_active_filters() {
	foreach ( estatein_active_filters() as $value ) {
		if ( $value ) {
			return true;
		}
	}

	return false;
}

/**
 * Render pagination for a custom query.
 *
 * @param WP_Query $query The query to paginate.
 */
function estatein_pagination( $query ) {
	if ( ! $query instanceof WP_Query || $query->max_num_pages < 2 ) {
		return;
	}

	$links = paginate_links( array(
		'total'     => $query->max_num_pages,
		'current'   => max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) ),
		'mid_size'  => 1,
		'prev_text' => '&larr;',
		'next_text' => '&rarr;',
		'type'      => 'array',
	) );

	if ( ! $links ) {
		return;
	}

	echo '<nav class="pagination" aria-label="' . esc_attr__( 'Properties pagination', 'estatein' ) . '">';
	foreach ( $links as $link ) {
		echo wp_kses_post( $link );
	}
	echo '</nav>';
}
