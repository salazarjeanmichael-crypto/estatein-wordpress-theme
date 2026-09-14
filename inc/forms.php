<?php
/**
 * Form handling: contact enquiries and newsletter sign-ups.
 *
 * Both handlers run on template_redirect, before any output, so a successful
 * submission can redirect (Post/Redirect/Get) and a refresh will not resubmit.
 *
 * Security model for every submission:
 *   1. nonce check    - the request came from our form
 *   2. honeypot check - a hidden field only a bot fills in
 *   3. sanitize       - every value is cast through a WordPress sanitiser
 *   4. validate       - required fields and email format
 *   5. escape on out  - handled in the template
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Validation state for the current request, read by the contact template.
 *
 * @var array
 */
$GLOBALS['estatein_form'] = array(
	'errors' => array(),
	'values' => array(),
);

/**
 * Return the current form state.
 *
 * @return array
 */
function estatein_form_state() {
	return $GLOBALS['estatein_form'];
}

/**
 * Error message for one field, if any.
 *
 * @param string $field Field name.
 * @return string
 */
function estatein_form_error( $field ) {
	$state = estatein_form_state();

	return isset( $state['errors'][ $field ] ) ? $state['errors'][ $field ] : '';
}

/**
 * Previously submitted value for one field, so a failed submit is not retyped.
 *
 * @param string $field Field name.
 * @return string
 */
function estatein_form_value( $field ) {
	$state = estatein_form_state();

	return isset( $state['values'][ $field ] ) ? $state['values'][ $field ] : '';
}

/**
 * Handle a contact form submission.
 */
function estatein_handle_contact() {
	if ( empty( $_POST['estatein_contact_submit'] ) ) {
		return;
	}

	if ( ! isset( $_POST['estatein_contact_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['estatein_contact_nonce'] ) ), 'estatein_contact' ) ) {
		$GLOBALS['estatein_form']['errors']['_form'] = __( 'Your session expired. Please try again.', 'estatein' );
		return;
	}

	// Honeypot: a real person never sees or fills this field. Bots get the same
	// success response as everyone else, so they learn nothing from the reply.
	if ( ! empty( $_POST['estatein_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'sent', '1', estatein_form_return_url() ) );
		exit;
	}

	// Every text field goes through the same sanitiser; only the message keeps
	// its line breaks, and only consent is a boolean.
	$text_fields = array(
		'first_name', 'last_name', 'phone', 'inquiry', 'source',
		'pref_location', 'pref_type', 'bathrooms', 'bedrooms', 'budget',
	);

	$values = array(
		'email'   => sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ),
		'message' => sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) ),
		'consent' => ! empty( $_POST['consent'] ),
	);

	foreach ( $text_fields as $field ) {
		$values[ $field ] = sanitize_text_field( wp_unslash( $_POST[ $field ] ?? '' ) );
	}

	$errors = array();

	if ( '' === $values['first_name'] ) {
		$errors['first_name'] = __( 'Please enter your first name.', 'estatein' );
	}

	if ( '' === $values['email'] ) {
		$errors['email'] = __( 'Please enter your email address.', 'estatein' );
	} elseif ( ! is_email( $values['email'] ) ) {
		$errors['email'] = __( 'That email address does not look right.', 'estatein' );
	}

	if ( '' === $values['message'] ) {
		$errors['message'] = __( 'Please tell us how we can help.', 'estatein' );
	}

	if ( ! $values['consent'] ) {
		$errors['consent'] = __( 'Please accept the terms to continue.', 'estatein' );
	}

	if ( $errors ) {
		$GLOBALS['estatein_form']['errors'] = $errors;
		$GLOBALS['estatein_form']['values'] = $values;
		return;
	}

	$to      = estatein_field( 'contact_email', get_option( 'admin_email' ), 'option' );
	$subject = sprintf(
		/* translators: 1: site name, 2: sender name */
		__( '[%1$s] New enquiry from %2$s', 'estatein' ),
		get_bloginfo( 'name' ),
		trim( $values['first_name'] . ' ' . $values['last_name'] )
	);

	$summary = array(
		__( 'Name', 'estatein' )               => trim( $values['first_name'] . ' ' . $values['last_name'] ),
		__( 'Email', 'estatein' )              => $values['email'],
		__( 'Phone', 'estatein' )              => $values['phone'],
		__( 'Inquiry type', 'estatein' )       => $values['inquiry'],
		__( 'Heard about us via', 'estatein' ) => $values['source'],
		__( 'Preferred location', 'estatein' ) => $values['pref_location'],
		__( 'Property type', 'estatein' )      => $values['pref_type'],
		__( 'Bedrooms', 'estatein' )           => $values['bedrooms'],
		__( 'Bathrooms', 'estatein' )          => $values['bathrooms'],
		__( 'Budget', 'estatein' )             => $values['budget'],
	);

	$lines = array();

	// Skip empty optional fields rather than mailing a wall of blank labels.
	foreach ( $summary as $label => $value ) {
		if ( '' !== $value ) {
			$lines[] = $label . ': ' . $value;
		}
	}

	$lines[] = '';
	$lines[] = $values['message'];

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $values['first_name'] . ' <' . $values['email'] . '>',
	);

	wp_mail( $to, $subject, implode( "\n", $lines ), $headers );

	/**
	 * Fires after a contact enquiry is accepted.
	 *
	 * Lets a CRM integration hook in without touching the theme.
	 *
	 * @param array $values Sanitised submission.
	 */
	do_action( 'estatein_contact_submitted', $values );

	wp_safe_redirect( add_query_arg( 'sent', '1', estatein_form_return_url() ) . '#contact-form' );
	exit;
}
add_action( 'template_redirect', 'estatein_handle_contact' );

/**
 * Where to send the browser after a successful submission.
 *
 * Prefers the URL the form carried in a hidden field, because Referer is
 * optional and is stripped by some privacy tools and proxies. The value is run
 * through wp_validate_redirect() so it can only ever point at this site.
 *
 * @return string
 */
function estatein_form_return_url() {
	$posted = isset( $_POST['estatein_return'] ) // phpcs:ignore WordPress.Security.NonceVerification -- nonce already verified by the caller.
		? esc_url_raw( wp_unslash( $_POST['estatein_return'] ) )
		: '';

	$fallback = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$target   = $posted ? $posted : $fallback;

	// Drop any stale flags so the notice does not stick across submissions.
	$target = remove_query_arg( array( 'sent', 'subscribed' ), $target );

	return wp_validate_redirect( $target, home_url( '/' ) );
}

/**
 * Handle a newsletter sign-up from the footer.
 */
function estatein_handle_subscribe() {
	if ( empty( $_POST['estatein_subscribe_email'] ) ) {
		return;
	}

	if ( ! isset( $_POST['estatein_subscribe_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['estatein_subscribe_nonce'] ) ), 'estatein_subscribe' ) ) {
		return;
	}

	$email = sanitize_email( wp_unslash( $_POST['estatein_subscribe_email'] ) );

	if ( ! is_email( $email ) ) {
		return;
	}

	/**
	 * Fires on a valid newsletter sign-up.
	 *
	 * Mailchimp, Brevo or similar can hook here.
	 *
	 * @param string $email Subscriber address.
	 */
	do_action( 'estatein_subscribed', $email );

	wp_mail(
		estatein_field( 'contact_email', get_option( 'admin_email' ), 'option' ),
		sprintf(
			/* translators: %s: site name */
			__( '[%s] New newsletter subscriber', 'estatein' ),
			get_bloginfo( 'name' )
		),
		$email
	);

	wp_safe_redirect( add_query_arg( 'subscribed', '1', estatein_form_return_url() ) );
	exit;
}
add_action( 'template_redirect', 'estatein_handle_subscribe' );
