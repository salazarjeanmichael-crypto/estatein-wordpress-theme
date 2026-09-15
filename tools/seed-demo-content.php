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
require_once __DIR__ . '/lib-seed.php';

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

		// estatein_meta() reads ACF first, so seeding only the native key would
		// leave the old ACF value winning on the front end.
		if ( function_exists( 'update_field' ) ) {
			update_field( $key, $value, $post_id );
		}
	}

	if ( ! empty( $item['image'] ) ) {
		$attachment_id = estatein_seed_image( $item['image'] );

		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	// The gallery reads attachments, so the extras are parented to the post.
	$gallery = array();

	foreach ( ( isset( $item['gallery'] ) ? $item['gallery'] : array() ) as $file ) {
		$gid = estatein_seed_image( $file, $item['slug'] );

		if ( $gid ) {
			wp_update_post( array( 'ID' => $gid, 'post_parent' => $post_id ) );
			$gallery[] = $gid;
		}
	}

	// Detach anything a previous run left behind, or re-running the seeder
	// after editing a gallery would keep growing it.
	foreach ( get_posts( array(
		'post_type'      => 'attachment',
		'post_parent'    => $post_id,
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) ) as $stale ) {
		if ( ! in_array( $stale, $gallery, true ) ) {
			wp_update_post( array( 'ID' => $stale, 'post_parent' => 0 ) );
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
		'gallery' => array( 'gallery-1.jpg', 'gallery-2.jpg', 'gallery-3.jpg', 'gallery-4.jpg', 'gallery-5.jpg', 'gallery-6.jpg', 'gallery-7.jpg', 'gallery-8.jpg' ),
		'meta'    => array( 'price' => '550000', 'bedrooms' => '4', 'bathrooms' => '3', 'style' => 'Villa', 'area' => '2,500 Square Feet', 'address' => 'Malibu, California', 'year' => '2021', 'features' => "Expansive oceanfront terrace for outdoor entertaining
Gourmet kitchen with top-of-the-line appliances
Private beach access for morning strolls and sunset views
Master suite with a spa-inspired bathroom and ocean-facing balcony
Private garage and ample storage space" ),
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
		'gallery' => array( 'gallery-2.jpg', 'gallery-3.jpg', 'gallery-4.jpg', 'gallery-5.jpg', 'gallery-6.jpg', 'gallery-7.jpg', 'gallery-8.jpg', 'gallery-9.jpg' ),
		'meta'    => array( 'price' => '550000', 'bedrooms' => '2', 'bathrooms' => '2', 'style' => 'Villa', 'area' => '1,200 Square Feet', 'address' => 'Downtown, Chicago', 'year' => '2019', 'features' => "Floor-to-ceiling glazing across every room
Residents gym, roof terrace and 24-hour concierge
Secure underground parking for one car
Fully fitted kitchen with integrated appliances
Two underground lines within five minutes" ),
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
		'gallery' => array( 'gallery-1.jpg', 'gallery-3.jpg', 'gallery-4.jpg', 'gallery-5.jpg', 'gallery-6.jpg', 'gallery-7.jpg', 'gallery-8.jpg', 'gallery-9.jpg' ),
		'meta'    => array( 'price' => '550000', 'bedrooms' => '3', 'bathrooms' => '3', 'style' => 'Villa', 'area' => '1,850 Square Feet', 'address' => 'Aspen, Colorado', 'year' => '2016', 'features' => "Private garden to the rear, fully enclosed
Original brickwork and exposed beams throughout
Gated development with its own security barrier
Recently rebuilt kitchen and bathrooms
Visitor parking and a shared green" ),
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
		'gallery' => array( 'gallery-1.jpg', 'gallery-2.jpg', 'gallery-3.jpg', 'gallery-4.jpg', 'gallery-6.jpg', 'gallery-7.jpg', 'gallery-8.jpg', 'gallery-9.jpg' ),
		'meta'    => array( 'price' => '625000', 'bedrooms' => '3', 'bathrooms' => '2', 'style' => 'House', 'area' => '1,980 Square Feet', 'address' => 'Portland, Oregon', 'year' => '2012', 'features' => "Level lawn and a covered patio off the kitchen
Off-street parking for two cars
Solar panels fitted in 2021
Walk-in wardrobe to the principal bedroom
Home office with its own entrance" ),
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
		'gallery' => array( 'gallery-2.jpg', 'gallery-3.jpg', 'gallery-4.jpg', 'gallery-5.jpg', 'gallery-6.jpg', 'gallery-7.jpg', 'gallery-8.jpg', 'gallery-9.jpg' ),
		'meta'    => array( 'price' => '1250000', 'bedrooms' => '4', 'bathrooms' => '4', 'style' => 'Penthouse', 'area' => '3,400 Square Feet', 'address' => 'Manhattan, New York', 'year' => '2022', 'features' => "Wraparound terrace on three sides
Private lift straight into the apartment
Chef's kitchen with a butler's pantry
Climate-controlled wine store
Two secure parking bays and a storage cage" ),
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
		'gallery' => array( 'gallery-1.jpg', 'gallery-2.jpg', 'gallery-3.jpg', 'gallery-5.jpg', 'gallery-6.jpg', 'gallery-7.jpg', 'gallery-8.jpg', 'gallery-9.jpg' ),
		'meta'    => array( 'price' => '780000', 'bedrooms' => '2', 'bathrooms' => '2', 'style' => 'Cabin', 'area' => '1,450 Square Feet', 'address' => 'Lake Tahoe, Nevada', 'year' => '2018', 'features' => "Deep water frontage with a private jetty
Wood burner and underfloor heating throughout
Floor-to-ceiling glazing to the lake
Detached studio suitable for guests
Fully furnished and ready to occupy" ),
		'terms'   => array( 'property_type' => array( 'House' ), 'property_location' => array( 'Nevada' ) ),
	),
);


/* -------------------------------------------------------------------------
 * Testimonials
 * ---------------------------------------------------------------------- */

$testimonials = array(
	array(
		'type'    => 'testimonial',
		'image'   => 'avatar-1.png',
		'slug'    => 'exceptional-service',
		'title'   => 'Exceptional Service!',
		'content' => 'Our experience with Estatein was outstanding. Their team dedication and professionalism made finding our dream home a breeze. Highly recommended!',
		'order'   => 1,
		'meta'    => array( 'rating' => '5', 'author' => 'Wade Warren', 'location' => 'USA, California' ),
	),
	array(
		'type'    => 'testimonial',
		'image'   => 'avatar-2.png',
		'slug'    => 'efficient-and-reliable',
		'title'   => 'Efficient and Reliable',
		'content' => 'Estatein provided us with top-notch service. They helped us sell our property quickly and at a great price. We could not be happier with the results.',
		'order'   => 2,
		'meta'    => array( 'rating' => '5', 'author' => 'Emelie Thomson', 'location' => 'USA, Florida' ),
	),
	array(
		'type'    => 'testimonial',
		'image'   => 'avatar-3.png',
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
 * Journal posts
 * ---------------------------------------------------------------------- */

/*
 * The design has no blog, but WordPress ships one, so these replace the
 * default Hello World rather than leaving boilerplate in the sitemap.
 */
$posts = array(
	array(
		'type'    => 'post',
		'slug'    => 'what-to-check-before-you-view-a-property',
		'title'   => 'What to Check Before You View a Property',
		'image'   => 'gallery-2.jpg',
		'excerpt' => 'A viewing is short and easy to get wrong. These are the checks worth making before you arrive, and the questions worth asking while you are there.',
		'content' => "A viewing rarely lasts more than twenty minutes, and most of that goes on first impressions. Knowing what to look at beforehand is the difference between a useful visit and a pleasant one.

<h2>Before you go</h2>

Read the listing twice. Note the build year, the floor area and what is included in the price, then check what comparable homes nearby have sold for. If the price sits well above or below that range, ask why.

Check the commute at the time you would actually travel, not at midday. Look at the street on a map and see what backs onto the garden.

<h2>While you are there</h2>

Open things. Windows, cupboards, the boiler cupboard, the loft hatch. Run a tap and watch the pressure. Look at the ceilings in the corners of upstairs rooms, where a roof problem shows first.

Ask how long the property has been on the market and whether any offers have fallen through. The answer tells you more about the price than the listing does.

<h2>Afterwards</h2>

Write your notes the same day, while the rooms are still distinct in your memory. If you are seeing several homes in a week, they blur together faster than you expect.",
		'terms'   => array( 'category' => array( 'Buying' ) ),
	),
	array(
		'type'    => 'post',
		'slug'    => 'how-property-valuations-are-actually-calculated',
		'title'   => 'How Property Valuations Are Actually Calculated',
		'image'   => 'gallery-6.jpg',
		'excerpt' => 'Valuation looks like an opinion and is mostly arithmetic. Here is what a surveyor is really doing when they put a number on a home.',
		'content' => "Ask three people what a house is worth and you will get three answers. Ask a surveyor and you will get one, with workings behind it.

<h2>Comparable sales</h2>

The starting point is what similar homes nearby actually sold for, not what they were listed at. Similar means comparable size, condition, age and street. A valuer will usually take three to five of these and adjust each one up or down.

<h2>Adjustments</h2>

An extra bedroom, a converted loft, off-street parking and a south-facing garden all carry a figure. So does the work a buyer would have to do: a kitchen at the end of its life comes off the total, not off the asking price.

<h2>Why the figure can move</h2>

Valuations are a snapshot. Interest rates, a new school catchment or a planning decision two streets away can shift the comparable set within months. That is why a valuation carries a date, and why a year-old one is of limited use.",
		'terms'   => array( 'category' => array( 'Guides' ) ),
	),
	array(
		'type'    => 'post',
		'slug'    => 'the-costs-that-come-after-the-asking-price',
		'title'   => 'The Costs That Come After the Asking Price',
		'image'   => 'gallery-8.jpg',
		'excerpt' => 'Transfer tax, legal fees, inspection, insurance. A rundown of what a purchase costs on top of the listing price, and which of them you can influence.',
		'content' => "The asking price is the number everyone budgets for. It is rarely the number that leaves your account.

<h2>One-off costs</h2>

Property transfer tax is the largest, calculated from the sale price and set locally. Legal fees cover the title transfer and the searches. An inspection is optional and almost always worth it: a report that costs a few hundred can take thousands off a negotiation.

<h2>Recurring costs</h2>

Property taxes, buildings insurance and, in a managed development, an association fee for shared maintenance and security. These are the ones that shape what the home costs to hold rather than to buy.

<h2>What you can influence</h2>

Legal fees and inspection costs are quotable, so get more than one. Insurance is worth re-quoting annually. Transfer tax is fixed by the sale price, which makes the negotiation the only lever on it, and the most valuable one.",
		'terms'   => array( 'category' => array( 'Buying' ) ),
	),
);

/* -------------------------------------------------------------------------
 * Pricing
 * ---------------------------------------------------------------------- */

/**
 * Build the four pricing cards for one listing.
 *
 * Derived from the price rather than typed out per property, so the demo
 * figures stay internally consistent with whatever a listing is worth.
 *
 * @param int $price Listing price.
 * @return array Meta keyed by field name.
 */
function estatein_seed_pricing( $price ) {
	$money = function ( $amount ) {
		return '$' . number_format( $amount, 0, '.', ',' );
	};

	$transfer   = round( $price * 0.02 );
	$legal      = 3000;
	$inspection = 500;
	$insurance  = 1200;
	$down       = round( $price * 0.2 );
	$taxes      = round( $price * 0.001 );
	$hoa        = 300;

	return array(
		'fees' => implode( "
", array(
			'Property Transfer Tax | ' . $money( $transfer ) . ' | Based on the sale price and local regulations',
			'Legal Fees | ' . $money( $legal ) . ' | Approximate cost for legal services, including title transfer',
			'Home Inspection | ' . $money( $inspection ) . ' | Recommended for due diligence',
			'Property Insurance | ' . $money( $insurance ) . ' | Annual cost for comprehensive property insurance',
			'Mortgage Fees | Varies | If applicable, consult with your lender for specific details',
		) ),
		'monthly' => implode( "
", array(
			'Property Taxes | ' . $money( $taxes ) . ' | Approximate monthly property tax based on the sale price and local rates',
			"Homeowners' Association Fee | " . $money( $hoa ) . ' | Monthly fee for common area maintenance and security',
		) ),
		'initial' => implode( "
", array(
			'Additional Fees | ' . $money( $transfer + $legal + $inspection + $insurance ) . ' | Property transfer tax, legal fees, inspection, insurance',
			'Down Payment | ' . $money( $down ) . ' | 20%',
			'Mortgage Amount | ' . $money( $price - $down ) . ' | If applicable',
		) ),
		'expenses' => implode( "
", array(
			'Property Taxes | ' . $money( $taxes ) . ' |',
			"Homeowners' Association Fee | " . $money( $hoa ) . ' |',
			'Mortgage Payment | Varies based on terms and interest rate | If applicable',
			'Property Insurance | $100 | Approximate monthly cost',
		) ),
	);
}

// Anything set on the listing itself wins, so a hand-written figure survives.
foreach ( $properties as $index => $property ) {
	$properties[ $index ]['meta'] = array_merge(
		estatein_seed_pricing( (int) $property['meta']['price'] ),
		$property['meta']
	);
}


/* -------------------------------------------------------------------------
 * Run
 * ---------------------------------------------------------------------- */

echo "Seeding Estatein demo content\n\n";

foreach ( array_merge( $properties, $posts, $testimonials, $faqs, $team ) as $item ) {
	estatein_seed_post( $item );
}

// Trashed rather than deleted: WordPress boilerplate that would otherwise sit
// in the blog archive and the sitemap, but it stays recoverable.
foreach ( array( 'hello-world' => 'post', 'sample-page' => 'page' ) as $slug => $type ) {
	$stale = get_page_by_path( $slug, OBJECT, $type );

	if ( $stale && 'trash' !== $stale->post_status ) {
		wp_trash_post( $stale->ID );
		echo "  trashed     {$slug}\n";
	}
}

echo "\nDone.\n";
