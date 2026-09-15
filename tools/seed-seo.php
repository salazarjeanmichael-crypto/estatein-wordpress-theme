<?php
/**
 * Fill in the Rank Math fields for the demo content.
 *
 * Only writes where a field is empty, so anything typed in the editor wins on
 * a re-run; pass --force to overwrite.
 * Usage: php wp-content/themes/estatein/tools/seed-seo.php [--force]
 *
 * @package Estatein
 */

if ( 'cli' !== php_sapi_name() ) {
	exit( 'This script runs from the command line only.' );
}

$root = dirname( __DIR__, 4 );
require_once $root . '/wp-load.php';

if ( ! class_exists( 'RankMath' ) ) {
	exit( "Rank Math is not active.\n" );
}

/**
 * Write one Rank Math field unless the editor has already set it.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key without the rank_math_ prefix.
 * @param string $value   Value to store.
 * @return bool Whether anything was written.
 */
function estatein_seo_set( $post_id, $key, $value ) {
	$meta  = 'rank_math_' . $key;
	$force = in_array( '--force', (array) $GLOBALS['argv'], true );

	if ( '' === trim( (string) $value ) || ( ! $force && get_post_meta( $post_id, $meta, true ) ) ) {
		return false;
	}

	update_post_meta( $post_id, $meta, $value );

	return true;
}

/**
 * Trim to a length search engines will actually show, without cutting a word.
 *
 * @param string $text  Source text.
 * @param int    $limit Character budget.
 * @return string
 */
function estatein_seo_clip( $text, $limit = 155 ) {
	$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $text ) ) );

	if ( strlen( $text ) <= $limit ) {
		return $text;
	}

	$window   = substr( $text, 0, $limit );
	$sentence = strrpos( $window, '. ' );

	if ( false !== $sentence && $sentence > $limit / 2 ) {
		return substr( $window, 0, $sentence + 1 );
	}

	return rtrim( substr( $window, 0, (int) strrpos( $window, ' ' ) ), " ,.;:" ) . '.';
}

$name = get_bloginfo( 'name' );
$done = 0;

/* -------------------------------------------------------------------------
 * Pages
 * ---------------------------------------------------------------------- */

$pages = array(
	'home' => array(
		'keyword' => 'dream property, real estate',
		'title'   => 'Estatein - Find Your Dream Property',
	),
	'about-us' => array(
		'keyword' => 'about estatein, real estate agency',
		'title'   => 'About Estatein - Our Journey and Values',
	),
	'properties' => array(
		'keyword' => 'dream property, properties for sale',
		'title'   => 'Properties for Sale - Estatein',
	),
	'services' => array(
		'keyword' => 'real estate services, property management',
		'title'   => 'Real Estate Services - Estatein',
	),
	'contact-us' => array(
		'keyword' => 'contact estatein, real estate enquiry',
		'title'   => 'Contact Estatein - Get in Touch',
	),
);

foreach ( $pages as $slug => $seo ) {
	$page = get_page_by_path( $slug );

	if ( ! $page ) {
		echo "  ! missing page {$slug}\n";
		continue;
	}

	// The same copy the page shows, so the description matches the content
	// the crawler finds rather than being written twice.
	$intro = get_post_meta( $page->ID, 'home' === $slug ? 'hero_text' : 'page_intro', true );

	$done += (int) estatein_seo_set( $page->ID, 'focus_keyword', $seo['keyword'] );
	$done += (int) estatein_seo_set( $page->ID, 'title', $seo['title'] );
	$done += (int) estatein_seo_set( $page->ID, 'description', estatein_seo_clip( $intro ) );

	echo '  page      ' . str_pad( $slug, 14 ) . $seo['title'] . "\n";
}

/* -------------------------------------------------------------------------
 * Properties
 * ---------------------------------------------------------------------- */

$properties = get_posts( array(
	'post_type'      => 'property',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
) );

foreach ( $properties as $property ) {
	$id        = $property->ID;
	$style     = estatein_meta( 'style', $id );
	$address   = estatein_meta( 'address', $id );
	$bedrooms  = estatein_meta( 'bedrooms', $id );
	$bathrooms = estatein_meta( 'bathrooms', $id );
	$area      = estatein_meta( 'area', $id );
	$price     = estatein_meta( 'price', $id );
	$parts     = array_filter( array_map( 'trim', explode( ',', $address ) ) );
	$city      = $parts ? end( $parts ) : '';

	// The listing name is the strongest keyword: it is in the title, the slug
	// and the copy, which is what the analysis looks for.
	$keywords = array( strtolower( $property->post_title ) );

	if ( $style && $city ) {
		$keywords[] = strtolower( $style . ' in ' . $city );
	}

	$title = $property->post_title;

	if ( $bedrooms && $style && $city ) {
		$title = sprintf( '%s - %d Bed %s in %s', $property->post_title, $bedrooms, $style, $city );
	}

	$facts = array_filter( array(
		$bedrooms ? $bedrooms . ' bed' : '',
		$bathrooms ? $bathrooms . ' bath' : '',
		$area,
	) );

	$description = trim( $property->post_excerpt ? $property->post_excerpt : $property->post_content );
	$description = estatein_seo_clip( $description, 110 );

	if ( $facts ) {
		$description .= ' ' . implode( ', ', $facts ) . '.';
	}

	if ( $price ) {
		$description .= ' ' . estatein_price( $price ) . '.';
	}

	$done += (int) estatein_seo_set( $id, 'focus_keyword', implode( ', ', $keywords ) );
	$done += (int) estatein_seo_set( $id, 'title', estatein_seo_clip( $title . ' | ' . $name, 60 ) );
	$done += (int) estatein_seo_set( $id, 'description', estatein_seo_clip( $description ) );

	echo '  property  ' . str_pad( $property->post_name, 26 ) . $title . "\n";
}

/* -------------------------------------------------------------------------
 * Journal posts
 * ---------------------------------------------------------------------- */

$keywords = array(
	'what-to-check-before-you-view-a-property'        => 'property viewing checklist, viewing a property',
	'how-property-valuations-are-actually-calculated' => 'property valuation, how valuations are calculated',
	'the-costs-that-come-after-the-asking-price'      => 'cost of buying a property, property transfer tax',
);

foreach ( get_posts( array( 'post_type' => 'post', 'posts_per_page' => -1, 'post_status' => 'publish' ) ) as $post ) {
	// A written post already has a headline and a standfirst worth using, so
	// only the keyword has to be supplied.
	$keyword = isset( $keywords[ $post->post_name ] )
		? $keywords[ $post->post_name ]
		: strtolower( $post->post_title );

	$done += (int) estatein_seo_set( $post->ID, 'focus_keyword', $keyword );
	$done += (int) estatein_seo_set( $post->ID, 'title', estatein_seo_clip( $post->post_title . ' | ' . $name, 60 ) );
	$done += (int) estatein_seo_set( $post->ID, 'description', estatein_seo_clip( $post->post_excerpt ? $post->post_excerpt : $post->post_content ) );

	echo '  post      ' . str_pad( $post->post_name, 50 ) . $post->post_title . "\n";
}

echo "\n{$done} fields written.\n";
echo "Scores stay N/A until each post is opened and saved: Rank Math works them\n";
echo "out in the editor, not on the server.\n";
