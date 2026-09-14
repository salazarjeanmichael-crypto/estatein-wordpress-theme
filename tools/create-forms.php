<?php
/**
 * Create the Contact Form 7 forms the templates look for.
 *
 * The markup is written out here rather than built in the CF7 UI so the forms
 * carry the theme's own classes and deploy with the code, not by hand.
 *
 * @package Estatein
 */

if ( 'cli' !== php_sapi_name() ) {
	exit( 'This script runs from the command line only.' );
}

require_once dirname( __DIR__, 4 ) . '/wp-load.php';

if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
	exit( "Contact Form 7 is not active - nothing to create.\n" );
}

$admin_email = get_option( 'admin_email' );
$site        = get_bloginfo( 'name' );

/**
 * Create or update one CF7 form, keyed by title.
 *
 * @param string $title      Form title, also the lookup key.
 * @param string $markup     Form body.
 * @param array  $mail       CF7 mail template.
 * @param string $option_key Option storing the resulting form ID.
 */
function estatein_make_form( $title, $markup, array $mail, $option_key ) {
	$existing = get_posts( array(
		'post_type'      => 'wpcf7_contact_form',
		'title'          => $title,
		'posts_per_page' => 1,
		'post_status'    => 'any',
		'fields'         => 'ids',
	) );

	$form = $existing
		? WPCF7_ContactForm::get_instance( $existing[0] )
		: WPCF7_ContactForm::get_template( array( 'title' => $title ) );

	$form->set_title( $title );
	$form->set_properties( array(
		'form'     => $markup,
		'mail'     => $mail,
		'messages' => array(
			'mail_sent_ok'    => __( 'Thank you - your message is on its way. We will be in touch shortly.', 'estatein' ),
			'mail_sent_ng'    => __( 'Something went wrong sending your message. Please try again.', 'estatein' ),
			'validation_error' => __( 'Please check the highlighted fields and try again.', 'estatein' ),
			'accept_terms'    => __( 'Please accept the terms to continue.', 'estatein' ),
			'invalid_required' => __( 'This field is required.', 'estatein' ),
			'invalid_email'   => __( 'That email address does not look right.', 'estatein' ),
		),
	) );

	$id = $form->save();

	update_option( $option_key, $id );
	printf( "  %-22s => %d (%s)\n", $option_key, $id, $existing ? 'updated' : 'created' );
}


/* -------------------------------------------------------------------------
 * 1. General contact - "Let's Connect"
 * ---------------------------------------------------------------------- */

$contact = '
<div class="form-grid form-grid--thirds">
	<p class="form-field"><label>First Name [text* first_name placeholder "Enter First Name"]</label></p>
	<p class="form-field"><label>Last Name [text last_name placeholder "Enter Last Name"]</label></p>
	<p class="form-field"><label>Email [email* email placeholder "Enter your Email"]</label></p>
	<p class="form-field"><label>Phone [tel phone placeholder "Enter Phone Number"]</label></p>
	<p class="form-field"><label>Inquiry Type [select inquiry first_as_label "Select Inquiry Type" "Buying a property" "Selling a property" "Renting" "Investment advice" "Property management" "Something else"]</label></p>
	<p class="form-field"><label>How Did You Hear About Us? [select source first_as_label "Select" "Search engine" "Social media" "Friend or colleague" "Advertisement" "Other"]</label></p>
	<p class="form-field form-field--full"><label>Message [textarea* message rows:5 placeholder "Enter your Message here.."]</label></p>
</div>
<div class="enquiry__foot">
	<p class="form-consent">[acceptance consent] I agree with the Terms of Use and Privacy Policy [/acceptance]</p>
	[submit class:btn class:btn--primary "Send Your Message"]
</div>';

estatein_make_form(
	'Estatein Contact',
	$contact,
	array(
		'subject'            => sprintf( '[%s] New enquiry from [first_name] [last_name]', $site ),
		'sender'             => sprintf( '%s <%s>', $site, $admin_email ),
		'recipient'          => $admin_email,
		'body'               => "Name: [first_name] [last_name]\nEmail: [email]\nPhone: [phone]\nInquiry: [inquiry]\nHeard via: [source]\n\n[message]\n",
		'additional_headers' => 'Reply-To: [email]',
		'attachments'        => '',
		'use_html'           => 0,
		'exclude_blank'      => 1,
	),
	'estatein_form_contact'
);


/* -------------------------------------------------------------------------
 * 2. Property enquiry - "Let's Make it Happen"
 * ---------------------------------------------------------------------- */

$locations = implode( ' ', array_map( function ( $name ) {
	return '"' . $name . '"';
}, array_keys( estatein_term_options( 'property_location' ) ) ) );

$types = implode( ' ', array_map( function ( $name ) {
	return '"' . $name . '"';
}, array_keys( estatein_term_options( 'property_type' ) ) ) );

$enquiry = '
<div class="form-grid">
	<p class="form-field"><label>First Name [text* first_name placeholder "Enter First Name"]</label></p>
	<p class="form-field"><label>Last Name [text last_name placeholder "Enter Last Name"]</label></p>
	<p class="form-field"><label>Email [email* email placeholder "Enter your Email"]</label></p>
	<p class="form-field"><label>Phone [tel phone placeholder "Enter Phone Number"]</label></p>
	<p class="form-field"><label>Preferred Location [select pref_location first_as_label "Select Location" ' . $locations . ']</label></p>
	<p class="form-field"><label>Property Type [select pref_type first_as_label "Select Property Type" ' . $types . ']</label></p>
	<p class="form-field"><label>No. of Bathrooms [select bathrooms first_as_label "Select no. of Bathrooms" "1+" "2+" "3+" "4+" "5+"]</label></p>
	<p class="form-field"><label>No. of Bedrooms [select bedrooms first_as_label "Select no. of Bedrooms" "1+" "2+" "3+" "4+" "5+"]</label></p>
	<p class="form-field form-field--full"><label>Budget [select budget first_as_label "Select Budget" "Under $500,000" "$500,000 - $750,000" "$750,000 - $1M" "$1M and above"]</label></p>
	<p class="form-field form-field--full"><label>Message [textarea* message rows:5 placeholder "Enter your Message here.."]</label></p>
</div>
<div class="enquiry__foot">
	<p class="form-consent">[acceptance consent] I agree with the Terms of Use and Privacy Policy [/acceptance]</p>
	[submit class:btn class:btn--primary "Send Your Message"]
</div>';

estatein_make_form(
	'Estatein Property Enquiry',
	$enquiry,
	array(
		'subject'            => sprintf( '[%s] Property enquiry from [first_name] [last_name]', $site ),
		'sender'             => sprintf( '%s <%s>', $site, $admin_email ),
		'recipient'          => $admin_email,
		'body'               => "Name: [first_name] [last_name]\nEmail: [email]\nPhone: [phone]\n\nPreferred location: [pref_location]\nProperty type: [pref_type]\nBedrooms: [bedrooms]\nBathrooms: [bathrooms]\nBudget: [budget]\n\n[message]\n",
		'additional_headers' => 'Reply-To: [email]',
		'attachments'        => '',
		'use_html'           => 0,
		'exclude_blank'      => 1,
	),
	'estatein_form_enquiry'
);


/* -------------------------------------------------------------------------
 * 3. Footer newsletter
 * ---------------------------------------------------------------------- */

$newsletter = '<div class="footer-subscribe">[email* estatein_subscribe_email placeholder "Enter Your Email"][submit class:footer-subscribe__send "Subscribe"]</div>';

estatein_make_form(
	'Estatein Newsletter',
	$newsletter,
	array(
		'subject'            => sprintf( '[%s] New newsletter subscriber', $site ),
		'sender'             => sprintf( '%s <%s>', $site, $admin_email ),
		'recipient'          => $admin_email,
		'body'               => "New subscriber: [estatein_subscribe_email]\n",
		'additional_headers' => 'Reply-To: [estatein_subscribe_email]',
		'attachments'        => '',
		'use_html'           => 0,
		'exclude_blank'      => 1,
	),
	'estatein_form_newsletter'
);

echo "\nDone.\n";
