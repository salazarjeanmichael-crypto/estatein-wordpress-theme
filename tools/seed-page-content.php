<?php
/**
 * Fill the Site Settings screen and the page fields with the design copy.
 *
 * The templates already fall back to this text, but writing it into the fields
 * means an editor opening a page sees what is on screen and can change it.
 *
 * Usage: php wp-content/themes/estatein/tools/seed-page-content.php
 *
 * @package Estatein
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 'CLI only.' );
}

$root = dirname( __DIR__, 4 );
require_once $root . '/wp-load.php';
require_once __DIR__ . '/lib-seed.php';

/**
 * Write a value to both ACF and native meta, so a later plugin change is safe.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Field name.
 * @param mixed  $value   Value to store.
 */
function estatein_seed_field( $post_id, $key, $value ) {
	update_post_meta( $post_id, $key, $value );

	if ( function_exists( 'update_field' ) ) {
		update_field( $key, $value, $post_id );
	}
}

/* -------------------------------------------------------------------------
 * Site settings
 * ---------------------------------------------------------------------- */

$settings = array(
	'banner_text'       => 'Discover Your Dream Property with Estatein',
	'banner_link_label' => 'Learn More',
	'banner_link_url'   => get_permalink( get_page_by_path( 'properties' ) ),
	'contact_email'     => 'info@estatein.com',
	'contact_phone'     => '+1 (123) 456-7890',
	'contact_address'   => 'Main Headquarters, 123 Estatein Plaza, City Center, Metropolis',
	'social_facebook'   => 'https://facebook.com/',
	'social_linkedin'   => 'https://linkedin.com/',
	'social_twitter'    => 'https://twitter.com/',
	'social_instagram'  => 'https://instagram.com/',
	'social_youtube'    => 'https://youtube.com/',
	'cta_title'         => 'Start Your Real Estate Journey Today',
	'cta_text'          => 'Your dream property is just a click away. Whether you are looking for a new home, a strategic investment, or expert real estate advice, Estatein is here to assist you every step of the way. Take the first step towards your real estate goals and explore our available properties or get in touch with our team for personalized assistance.',
	'cta_label'         => 'Explore Properties',
);

update_option( 'estatein_settings', $settings );
echo "Site Settings: " . count( $settings ) . " values\n";

/* -------------------------------------------------------------------------
 * Pages
 * ---------------------------------------------------------------------- */

$pages = array(
	'home' => array(
		'hero_heading'  => 'Discover Your Dream Property with Estatein',
		'hero_text'     => 'Your journey to finding the perfect property begins here. Explore our listings to find the home that matches your dreams.',
		'hero_stats'    => "200+ | Happy Customers\n10k+ | Properties For Clients\n16+ | Years of Experience",
		'home_features' => implode( "\n", array(
			'Find Your Dream Home | /estatein/properties/ | feature-home',
			'Unlock Property Value | /estatein/services/#valuation-mastery | feature-value',
			'Effortless Property Management | /estatein/services/#property-management | feature-management',
			'Smart Investments, Informed Decisions | /estatein/services/#strategic-marketing | feature-invest',
		) ),
	),
	'about-us' => array(
		'page_heading' => 'Our Journey',
		'page_intro'   => 'Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary. Over the years, we have expanded our reach, forged valuable partnerships, and gained the trust of countless clients.',
		'about_stats'  => "200+ | Happy Customers\n10k+ | Properties For Clients\n16+ | Years of Experience",
	),
	'properties' => array(
		'page_heading' => 'Find Your Dream Property',
		'page_intro'   => 'Welcome to Estatein, where your dream property awaits in every corner of our beautiful world. Explore our curated selection of properties, each offering a unique story and a chance to redefine your life.',
	),
	'services' => array(
		'page_heading' => 'Elevate Your Real Estate Experience',
		'page_intro'   => 'Welcome to Estatein, where your real estate aspirations meet expert guidance. Explore our comprehensive range of services, each designed to cater to your unique needs and dreams.',
	),
	'contact-us' => array(
		'page_heading' => 'Get in Touch with Estatein',
		'page_intro'   => 'Welcome to Estatein Contact Us page. We are here to assist you with any inquiries, requests, or feedback you may have. Whether you are looking to buy or sell a property, explore investment opportunities, or simply want to connect, we are just a message away.',
	),
);

foreach ( $pages as $slug => $fields ) {
	$page = get_page_by_path( $slug );

	if ( ! $page ) {
		echo "  ! missing page {$slug}\n";
		continue;
	}

	foreach ( $fields as $key => $value ) {
		estatein_seed_field( $page->ID, $key, $value );
	}

	echo "  page  " . str_pad( $slug, 12 ) . count( $fields ) . " fields\n";
}

/* -------------------------------------------------------------------------
 * Hero photo
 * ---------------------------------------------------------------------- */

$home = get_page_by_path( 'home' );
$hero = estatein_seed_image( 'hero.jpg' );

if ( $home && $hero ) {
	estatein_seed_field( $home->ID, 'hero_image', $hero );
	echo "  hero photo attached\n";
}

echo "\nDone.\n";
