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
		$price = get_post_meta( get_the_ID(), '_estatein_price', true );

		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'RealEstateListing',
			'name'        => get_the_title(),
			'url'         => get_permalink(),
			'description' => estatein_meta_description(),
		);

		if ( has_post_thumbnail() ) {
			$schema['image'] = get_the_post_thumbnail_url( get_the_ID(), 'estatein-wide' );
		}

		if ( $price ) {
			$schema['offers'] = array(
				'@type'         => 'Offer',
				'price'         => (string) $price,
				'priceCurrency' => 'USD',
				'availability'  => 'https://schema.org/InStock',
			);
		}
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
