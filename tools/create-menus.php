<?php
/**
 * Create the navigation menus and assign them to the theme's locations.
 *
 * Until a menu is assigned, footer.php falls back to hardcoded links, which
 * the client cannot edit. This puts the same links under Appearance > Menus.
 *
 * @package Estatein
 */

if ( 'cli' !== php_sapi_name() ) {
	exit( 'This script runs from the command line only.' );
}

require_once dirname( __DIR__, 4 ) . '/wp-load.php';

/**
 * Create or rebuild one menu and bind it to a theme location.
 *
 * Items are cleared and re-added rather than diffed, so re-running never
 * leaves duplicates behind.
 *
 * @param string $name     Menu name.
 * @param string $location Theme location slug.
 * @param array  $items    Label => URL pairs. A page slug is resolved to its permalink.
 */
function estatein_build_menu( $name, $location, array $items ) {
	$menu = wp_get_nav_menu_object( $name );

	if ( $menu ) {
		$menu_id = (int) $menu->term_id;

		foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $item ) {
			wp_delete_post( $item->ID, true );
		}
	} else {
		$menu_id = wp_create_nav_menu( $name );
	}

	if ( is_wp_error( $menu_id ) ) {
		printf( "  ! %s: %s\n", $name, $menu_id->get_error_message() );
		return;
	}

	foreach ( $items as $label => $target ) {
		$page = get_page_by_path( $target );

		if ( $page ) {
			// A real page reference, so the item tracks slug changes and picks
			// up the current-page class the header styling depends on.
			wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-title'     => $label,
				'menu-item-object-id' => $page->ID,
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			) );
			continue;
		}

		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'  => $label,
			'menu-item-url'    => $target,
			'menu-item-type'   => 'custom',
			'menu-item-status' => 'publish',
		) );
	}

	$locations              = get_theme_mod( 'nav_menu_locations', array() );
	$locations[ $location ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );

	printf( "  %-22s -> %-18s %d items\n", $location, $name, count( $items ) );
}

$home     = home_url( '/' );
$about    = get_permalink( get_page_by_path( 'about-us' ) );
$props    = get_permalink( get_page_by_path( 'properties' ) );
$services = get_permalink( get_page_by_path( 'services' ) );
$contact  = get_permalink( get_page_by_path( 'contact-us' ) );

echo "Building menus\n";

estatein_build_menu( 'Primary', 'primary', array(
	'Home'       => 'home',
	'About Us'   => 'about-us',
	'Properties' => 'properties',
	'Services'   => 'services',
) );

estatein_build_menu( 'Footer - Home', 'footer-home', array(
	'Hero Section' => $home . '#main',
	'Features'     => $home . '#features',
	'Properties'   => $home . '#properties',
	'Testimonials' => $home . '#testimonials',
	'FAQ\'s'       => $home . '#faq',
) );

estatein_build_menu( 'Footer - About Us', 'footer-about', array(
	'Our Story'    => $about . '#our-story',
	'Our Works'    => $about . '#our-works',
	'How It Works' => $about . '#how-it-works',
	'Our Team'     => $about . '#our-team',
	'Our Clients'  => $about . '#our-clients',
) );

estatein_build_menu( 'Footer - Properties', 'footer-properties', array(
	'Portfolio'  => $props,
	'Categories' => $props . '#categories',
) );

estatein_build_menu( 'Footer - Services', 'footer-services', array(
	'Valuation Mastery'   => $services . '#valuation-mastery',
	'Strategic Marketing' => $services . '#strategic-marketing',
	'Negotiation Wizardry' => $services . '#negotiation-wizardry',
	'Closing Success'     => $services . '#closing-success',
	'Property Management' => $services . '#property-management',
) );

estatein_build_menu( 'Footer - Contact Us', 'footer-contact', array(
	'Contact Form' => $contact . '#contact-form',
	'Our Offices'  => $contact . '#our-offices',
) );

echo "\nDone. Edit these under Appearance > Menus.\n";
