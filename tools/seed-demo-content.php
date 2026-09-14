<?php
/**
 * Seed the demo content shown in the Figma design.
 *
 * CLI only. Safe to re-run: items match on slug, so a second pass updates
 * rather than duplicates. Usage: php tools/seed-demo-content.php
 *
 * @package Estatein
 */

if ( 'cli' !== php_sapi_name() ) {
	exit( 'This script runs from the command line only.' );
}

// Four levels up: tools -> estatein -> themes -> wp-content -> root. From
// __DIR__, not getcwd(), so the script runs from any working directory.
$root = dirname( __DIR__, 4 );

require_once $root . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

/**
 * Import a theme image into the media library once, and return its ID.
 *
 * @param string $filename File inside assets/img.
 * @return int Attachment ID, or 0 on failure.
 */
function estatein_seed_image( $filename ) {
	$existing = get_posts( array(
		'post_type'      => 'attachment',
		'posts_per_page' => 1,
		'post_status'    => 'inherit',
		'meta_key'       => '_estatein_seed_source',
		'meta_value'     => $filename,
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

	$upload = wp_upload_bits( $filename, null, file_get_contents( $source ) );

	if ( ! empty( $upload['error'] ) ) {
		echo "  ! upload failed for {$filename}: {$upload['error']}\n";
		return 0;
	}

	$attachment_id = wp_insert_attachment( array(
		'post_mime_type' => 'image/jpeg',
		'post_title'     => pathinfo( $filename, PATHINFO_FILENAME ),
		'post_status'    => 'inherit',
	), $upload['file'] );

	wp_update_attachment_metadata(
		$attachment_id,
		wp_generate_attachment_metadata( $attachment_id, $upload['file'] )
	);

	update_post_meta( $attachment_id, '_estatein_seed_source', $filename );

	return (int) $attachment_id;
}

/**
 * Create or update a demo post.
 *
 * @param array $item Post definition.
 * @return int Post ID.
 */
function estatein_seed_post( array $item ) {
	$existing = get_page_by_path( $item['slug'], OBJECT, $item['type'] );

	$postarr = array(
		'post_type'    => $item['type'],
		'post_title'   => $item['title'],
		'post_name'    => $item['slug'],
		'post_content' => isset( $item['content'] ) ? $item['content'] : '',
		'post_excerpt' => isset( $item['excerpt'] ) ? $item['excerpt'] : '',
		'post_status'  => 'publish',
		'menu_order'   => isset( $item['order'] ) ? $item['order'] : 0,
	);

	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
		$post_id       = wp_update_post( $postarr );
	} else {
		$post_id = wp_insert_post( $postarr );
	}

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		echo "  ! failed: {$item['slug']}\n";
		return 0;
	}

	foreach ( ( isset( $item['meta'] ) ? $item['meta'] : array() ) as $key => $value ) {
		update_post_meta( $post_id, '_estatein_' . $key, $value );
	}

	if ( ! empty( $item['image'] ) ) {
		$attachment_id = estatein_seed_image( $item['image'] );

		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	foreach ( ( isset( $item['terms'] ) ? $item['terms'] : array() ) as $taxonomy => $terms ) {
		wp_set_object_terms( $post_id, $terms, $taxonomy );
	}

	echo "  " . str_pad( $item['type'], 12 ) . $item['slug'] . " => {$post_id}\n";

	return (int) $post_id;
}


/* -------------------------------------------------------------------------
 * Properties
 * ---------------------------------------------------------------------- */

$properties = array(
	array(
		'type'    => 'property',
		'slug'    => 'seaside-serenity-villa',
		'order'   => 1,
		'title'   => 'Seaside Serenity Villa',
		'excerpt' => 'A stunning 4-bedroom, 3-bathroom villa in a peaceful suburban neighborhood, moments from the water.',
		'content' => "Wake up to the sound of the sea. This four-bedroom villa sits on a quiet coastal street, with floor-to-ceiling glazing across the living space and a private terrace that catches the afternoon sun.\n\nThe kitchen is fully fitted, the primary suite has a walk-in wardrobe and an ensuite, and there is off-street parking for two cars. The beach is a six-minute walk.",
		'image'   => 'property-1.jpg',
		'meta'    => array( 'price' => '550000', 'bedrooms' => '4', 'bathrooms' => '3', 'style' => 'Villa', 'area' => '2,500 sq ft', 'address' => 'Malibu, California' ),
		'terms'   => array( 'property_type' => array( 'Villa' ), 'property_location' => array( 'California' ) ),
	),
	array(
		'type'    => 'property',
		'slug'    => 'metropolitan-haven',
		'order'   => 2,
		'title'   => 'Metropolitan Haven',
		'excerpt' => 'A chic and fully-furnished 2-bedroom apartment with panoramic city views.',
		'content' => "A two-bedroom apartment on the twenty-second floor, sold fully furnished. Floor-to-ceiling windows run the length of the living room and both bedrooms, and the building has a residents' gym, roof terrace and 24-hour concierge.\n\nTransport links are on the doorstep, with two underground lines and the business district within fifteen minutes.",
		'image'   => 'property-2.jpg',
		'meta'    => array( 'price' => '550000', 'bedrooms' => '2', 'bathrooms' => '2', 'style' => 'Apartment', 'area' => '1,200 sq ft', 'address' => 'Downtown, Chicago' ),
		'terms'   => array( 'property_type' => array( 'Apartment' ), 'property_location' => array( 'Illinois' ) ),
	),
	array(
		'type'    => 'property',
		'slug'    => 'rustic-retreat-cottage',
		'order'   => 3,
		'title'   => 'Rustic Retreat Cottage',
		'excerpt' => 'An elegant 3-bedroom, 2.5-bathroom townhouse in a gated community.',
		'content' => "A three-bedroom townhouse in a gated development, arranged over three floors with a private garden to the rear. Original brickwork and exposed beams sit alongside a recently rebuilt kitchen and bathrooms.\n\nThe development has its own security gate, visitor parking and a shared green.",
		'image'   => 'property-3.jpg',
		'meta'    => array( 'price' => '550000', 'bedrooms' => '3', 'bathrooms' => '3', 'style' => 'Townhouse', 'area' => '1,850 sq ft', 'address' => 'Aspen, Colorado' ),
		'terms'   => array( 'property_type' => array( 'Townhouse' ), 'property_location' => array( 'Colorado' ) ),
	),
	array(
		'type'    => 'property',
		'slug'    => 'garden-court-residence',
		'order'   => 4,
		'title'   => 'Garden Court Residence',
		'excerpt' => 'A bright 3-bedroom family home with a mature garden and a converted loft.',
		'content' => "A three-bedroom family home on a quiet residential road. The loft has been converted into a fourth room currently used as a study, and the garden is mature and fully enclosed.\n\nTwo primary schools and a park are within walking distance.",
		'image'   => 'property-1.jpg',
		'meta'    => array( 'price' => '625000', 'bedrooms' => '3', 'bathrooms' => '2', 'style' => 'House', 'area' => '1,980 sq ft', 'address' => 'Portland, Oregon' ),
		'terms'   => array( 'property_type' => array( 'House' ), 'property_location' => array( 'Oregon' ) ),
	),
	array(
		'type'    => 'property',
		'slug'    => 'skyline-penthouse',
		'order'   => 5,
		'title'   => 'Skyline Penthouse',
		'excerpt' => 'A 4-bedroom penthouse with a wraparound terrace and private lift access.',
		'content' => "A penthouse occupying the full top floor, with a wraparound terrace on three sides and a private lift opening directly into the hallway.\n\nFour bedrooms, three of them ensuite, and a separate staff or guest room off the kitchen.",
		'image'   => 'property-2.jpg',
		'meta'    => array( 'price' => '1250000', 'bedrooms' => '4', 'bathrooms' => '4', 'style' => 'Penthouse', 'area' => '3,400 sq ft', 'address' => 'Manhattan, New York' ),
		'terms'   => array( 'property_type' => array( 'Apartment' ), 'property_location' => array( 'New York' ) ),
	),
	array(
		'type'    => 'property',
		'slug'    => 'lakeside-modern-cabin',
		'order'   => 6,
		'title'   => 'Lakeside Modern Cabin',
		'excerpt' => 'A 2-bedroom architect-designed cabin with direct lake frontage.',
		'content' => "An architect-designed cabin with thirty metres of private lake frontage and a timber deck that runs the width of the building.\n\nTwo bedrooms, a double-height living space with a wood burner, and a boathouse included in the sale.",
		'image'   => 'property-3.jpg',
		'meta'    => array( 'price' => '780000', 'bedrooms' => '2', 'bathrooms' => '2', 'style' => 'Cabin', 'area' => '1,450 sq ft', 'address' => 'Lake Tahoe, Nevada' ),
		'terms'   => array( 'property_type' => array( 'House' ), 'property_location' => array( 'Nevada' ) ),
	),
);


/* -------------------------------------------------------------------------
 * Testimonials
 * ---------------------------------------------------------------------- */

$testimonials = array(
	array(
		'type'    => 'testimonial',
		'slug'    => 'exceptional-service',
		'title'   => 'Exceptional Service!',
		'content' => 'Our experience with Estatein was outstanding. Their team dedication and professionalism made finding our dream home a breeze. Highly recommended!',
		'order'   => 1,
		'meta'    => array( 'rating' => '5', 'author' => 'Wade Warren', 'location' => 'USA, California' ),
	),
	array(
		'type'    => 'testimonial',
		'slug'    => 'efficient-and-reliable',
		'title'   => 'Efficient and Reliable',
		'content' => 'Estatein provided us with top-notch service. They helped us sell our property quickly and at a great price. We could not be happier with the results.',
		'order'   => 2,
		'meta'    => array( 'rating' => '5', 'author' => 'Emelie Thomson', 'location' => 'USA, Florida' ),
	),
	array(
		'type'    => 'testimonial',
		'slug'    => 'trusted-advisors',
		'title'   => 'Trusted Advisors',
		'content' => 'The Estatein team guided us through the entire buying process. Their knowledge and commitment to our needs were impressive. Thank you for your support!',
		'order'   => 3,
		'meta'    => array( 'rating' => '5', 'author' => 'John Mans', 'location' => 'USA, Nevada' ),
	),
	array(
		'type'    => 'testimonial',
		'slug'    => 'straightforward-from-start-to-finish',
		'title'   => 'Straightforward From Start to Finish',
		'content' => 'We were first-time buyers and had a lot of questions. Every one of them was answered clearly and without pressure. The process never felt rushed.',
		'order'   => 4,
		'meta'    => array( 'rating' => '5', 'author' => 'Priya Raman', 'location' => 'USA, Texas' ),
	),
);


/* -------------------------------------------------------------------------
 * FAQs
 * ---------------------------------------------------------------------- */

$faqs = array(
	array(
		'type'    => 'faq',
		'slug'    => 'how-do-i-search-for-properties',
		'title'   => 'How do I search for properties on Estatein?',
		'content' => 'Learn how to use our user-friendly search tools to find properties that match your criteria. Filter by location, property type, number of bedrooms and price, then save any listing to revisit later.',
		'order'   => 1,
	),
	array(
		'type'    => 'faq',
		'slug'    => 'what-documents-do-i-need-to-sell',
		'title'   => 'What documents do I need to sell my property through Estatein?',
		'content' => 'Find out about the necessary documentation for listing your property with us. In most cases we need proof of ownership, photo identification, any existing survey or floor plan, and details of outstanding charges on the property.',
		'order'   => 2,
	),
	array(
		'type'    => 'faq',
		'slug'    => 'how-can-i-contact-an-agent',
		'title'   => 'How can I contact an Estatein agent?',
		'content' => 'Discover the different ways you can get in touch with our experienced agents. You can call the office directly, send an enquiry through the contact form, or request a call back at a time that suits you.',
		'order'   => 3,
	),
	array(
		'type'    => 'faq',
		'slug'    => 'how-long-does-a-sale-take',
		'title'   => 'How long does a typical sale take to complete?',
		'content' => 'Most sales complete within eight to twelve weeks of an offer being accepted, though this varies with the chain and the type of finance involved. Your agent will give you a realistic estimate for your specific situation.',
		'order'   => 4,
	),
);


/* -------------------------------------------------------------------------
 * Team
 * ---------------------------------------------------------------------- */

$team = array(
	array(
		'type'  => 'team',
		'slug'  => 'max-mitchell',
		'order' => 1,
		'title' => 'Max Mitchell',
		'image' => 'team-1.jpg',
		'meta'  => array( 'role' => 'Founder', 'twitter' => 'https://twitter.com/', 'email' => 'max@estatein.com' ),
	),
	array(
		'type'  => 'team',
		'slug'  => 'sarah-johnson',
		'order' => 2,
		'title' => 'Sarah Johnson',
		'image' => 'team-2.jpg',
		'meta'  => array( 'role' => 'Chief Real Estate Officer', 'twitter' => 'https://twitter.com/', 'email' => 'sarah@estatein.com' ),
	),
	array(
		'type'  => 'team',
		'slug'  => 'david-brown',
		'order' => 3,
		'title' => 'David Brown',
		'image' => 'team-3.jpg',
		'meta'  => array( 'role' => 'Head of Property Management', 'twitter' => 'https://twitter.com/', 'email' => 'david@estatein.com' ),
	),
	array(
		'type'  => 'team',
		'slug'  => 'michael-turner',
		'order' => 4,
		'title' => 'Michael Turner',
		'image' => 'team-4.jpg',
		'meta'  => array( 'role' => 'Legal Counsel', 'twitter' => 'https://twitter.com/', 'email' => 'michael@estatein.com' ),
	),
);


/* -------------------------------------------------------------------------
 * Run
 * ---------------------------------------------------------------------- */

echo "Seeding Estatein demo content\n\n";

foreach ( array_merge( $properties, $testimonials, $faqs, $team ) as $item ) {
	estatein_seed_post( $item );
}

echo "\nDone.\n";
