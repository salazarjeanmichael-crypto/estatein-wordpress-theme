<?php
/**
 * Baseline SEO and social metadata.
 *
 * Deliberately lightweight. If the client later installs Yoast or Rank Math,
 * this file steps aside rather than emitting competing tags.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether a dedicated SEO plugin is handling metadata.
 *
 * @return bool
 */
function estatein_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' );
}

/**
 * Build a description for the current view.
 *
 * @return string
 */
function estatein_meta_description() {
	// These pages are built from fields rather than the editor, so there is no
	// post_content for either this or an SEO plugin to summarise.
	if ( is_front_page() ) {
		$text = estatein_field( 'hero_text', '' );

		if ( $text ) {
			return estatein_trim( $text, 30 );
		}
	}

	if ( is_page() ) {
		$text = estatein_field( 'page_intro', '' );

		if ( $text ) {
			return estatein_trim( $text, 30 );
		}
	}

	if ( is_singular() ) {
		$post = get_queried_object();

		if ( $post instanceof WP_Post ) {
			$text = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
			$text = estatein_trim( $text, 30 );

			if ( $text ) {
				return $text;
			}
		}
	}

	if ( is_post_type_archive( 'property' ) ) {
		return __( 'Browse every property listed with Estatein — villas, apartments and townhouses, with full details and pricing.', 'estatein' );
	}

	if ( is_tax() || is_category() || is_tag() ) {
		$term = get_queried_object();

		if ( $term instanceof WP_Term && $term->description ) {
			return estatein_trim( $term->description, 30 );
		}
	}

	return get_bloginfo( 'description' );
}

/**
 * Output description, canonical and Open Graph tags.
 */
function estatein_meta_tags() {
	if ( estatein_seo_plugin_active() ) {
		return;
	}

	$description = estatein_meta_description();
	$title       = wp_get_document_title();
	$url         = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );

	if ( $description ) {
		printf( "\n<meta name=\"description\" content=\"%s\">", esc_attr( $description ) );
	}

	// WordPress core already emits rel=canonical on singular views via
	// rel_canonical(). Only fill the gap on archives, search and the home page,
	// so the page never carries two competing canonicals.
	if ( ! is_singular() ) {
		printf( "\n<link rel=\"canonical\" href=\"%s\">", esc_url( $url ) );
	}

	printf( "\n<meta property=\"og:site_name\" content=\"%s\">", esc_attr( get_bloginfo( 'name' ) ) );
	printf( "\n<meta property=\"og:type\" content=\"%s\">", is_singular() ? 'article' : 'website' );
	printf( "\n<meta property=\"og:title\" content=\"%s\">", esc_attr( $title ) );
	printf( "\n<meta property=\"og:url\" content=\"%s\">", esc_url( $url ) );

	if ( $description ) {
		printf( "\n<meta property=\"og:description\" content=\"%s\">", esc_attr( $description ) );
	}

	$image = '';

	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( get_the_ID(), 'estatein-wide' );
	}

	if ( $image ) {
		printf( "\n<meta property=\"og:image\" content=\"%s\">", esc_url( $image ) );
		printf( "\n<meta name=\"twitter:card\" content=\"summary_large_image\">" );
	} else {
		printf( "\n<meta name=\"twitter:card\" content=\"summary\">" );
	}

	echo "\n";
}
add_action( 'wp_head', 'estatein_meta_tags', 2 );

/**
 * Structured data for the property being viewed.
 *
 * Kept separate from the tag output because Rank Math wants the same node
 * added to its own graph rather than printed as a second script.
 *
 * @return array
 */
function estatein_listing_schema() {
	$id     = get_the_ID();
	$schema = array(
		'@type'       => 'RealEstateListing',
		'name'        => get_the_title(),
		'url'         => get_permalink(),
		'description' => estatein_meta_description(),
	);

	if ( has_post_thumbnail( $id ) ) {
		$schema['image'] = get_the_post_thumbnail_url( $id, 'estatein-wide' );
	}

	$address = estatein_meta( 'address', $id );

	if ( $address ) {
		$schema['address'] = array(
			'@type'         => 'PostalAddress',
			'streetAddress' => $address,
		);
	}

	$bedrooms  = estatein_meta( 'bedrooms', $id );
	$bathrooms = estatein_meta( 'bathrooms', $id );
	$area      = estatein_meta( 'area', $id );

	if ( $bedrooms ) {
		$schema['numberOfRooms'] = (int) $bedrooms;
	}

	if ( $bathrooms ) {
		$schema['numberOfBathroomsTotal'] = (int) $bathrooms;
	}

	if ( $area ) {
		$schema['floorSize'] = array( '@type' => 'QuantitativeValue', 'name' => $area );
	}

	$price = estatein_meta( 'price', $id );

	if ( $price ) {
		$schema['offers'] = array(
			'@type'         => 'Offer',
			'price'         => (string) $price,
			'priceCurrency' => 'USD',
			'availability'  => 'https://schema.org/InStock',
		);
	}

	return $schema;
}

/**
 * Emit schema.org JSON-LD.
 *
 * A RealEstateListing on a single property, RealEstateAgent elsewhere. Search
 * engines use this to build rich results for listings.
 */
function estatein_schema() {
	if ( estatein_seo_plugin_active() ) {
		return;
	}

	if ( is_singular( 'property' ) ) {
		$schema = array( '@context' => 'https://schema.org' ) + estatein_listing_schema();
	} else {
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'RealEstateAgent',
			'name'        => get_bloginfo( 'name' ),
			'url'         => home_url( '/' ),
			'description' => get_bloginfo( 'description' ),
		);

		$phone = estatein_field( 'contact_phone', '', 'option' );

		if ( $phone ) {
			$schema['telephone'] = $phone;
		}
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
add_action( 'wp_head', 'estatein_schema', 3 );

/**
 * Hand Rank Math the two things it cannot work out for itself.
 *
 * The filters no-op when the plugin is absent, so they are registered
 * unconditionally rather than behind another version check.
 */

/**
 * Description fallback, for pages whose copy lives in fields.
 *
 * @param string $description Description Rank Math worked out.
 * @return string
 */
function estatein_rank_math_description( $description ) {
	return $description ? $description : estatein_meta_description();
}
add_filter( 'rank_math/frontend/description', 'estatein_rank_math_description' );

/**
 * Add the listing node to Rank Math's graph on a single property.
 *
 * Price, bedrooms and floor area are custom fields, so no plugin can discover
 * them; this is the one piece of schema the theme has to supply.
 *
 * @param array $data Nodes Rank Math will output.
 * @return array
 */
function estatein_rank_math_schema( $data ) {
	if ( is_singular( 'property' ) ) {
		$data['estateinListing'] = estatein_listing_schema();
	}

	return $data;
}
add_filter( 'rank_math/json_ld', 'estatein_rank_math_schema', 20 );
