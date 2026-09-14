<?php
/**
 * Declarative form field rendering.
 *
 * The theme has two forms (property enquiry and general contact) that share a
 * look, validation display and accessibility contract but differ in fields.
 * Rendering each field through one function keeps that contract in a single
 * place: a label is always associated, an invalid field always gets
 * aria-invalid plus an aria-describedby pointing at its message, and a failed
 * submission always repopulates.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render one form field.
 *
 * @param array $field {
 *     @type string $name        Field name, also the id.
 *     @type string $label       Visible label.
 *     @type string $type        text|email|tel|textarea|select|checkbox. Default text.
 *     @type string $placeholder Placeholder text.
 *     @type bool   $required    Whether the field is required.
 *     @type bool   $full        Span the full grid width.
 *     @type array  $options     value => label pairs, for select.
 *     @type string $autocomplete Autocomplete token.
 * }
 */
function estatein_form_field( array $field ) {
	$field = wp_parse_args( $field, array(
		'name'         => '',
		'label'        => '',
		'type'         => 'text',
		'placeholder'  => '',
		'required'     => false,
		'full'         => false,
		'options'      => array(),
		'autocomplete' => '',
		'rows'         => 5,
	) );

	if ( ! $field['name'] ) {
		return;
	}

	$name    = $field['name'];
	$error   = estatein_form_error( $name );
	$value   = estatein_form_value( $name );
	$classes = 'form-field' . ( $field['full'] ? ' form-field--full' : '' );

	// Shared attributes for every control type.
	$attrs = ' id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '"';

	if ( $field['required'] ) {
		$attrs .= ' required';
	}

	if ( $field['autocomplete'] ) {
		$attrs .= ' autocomplete="' . esc_attr( $field['autocomplete'] ) . '"';
	}

	if ( $error ) {
		$attrs .= ' aria-invalid="true" aria-describedby="' . esc_attr( $name ) . '-error"';
	}

	echo '<p class="' . esc_attr( $classes ) . '">';

	printf(
		'<label for="%s">%s</label>',
		esc_attr( $name ),
		esc_html( $field['label'] )
	);

	switch ( $field['type'] ) {

		case 'textarea':
			printf(
				'<textarea%s rows="%d" placeholder="%s">%s</textarea>',
				$attrs, // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts above.
				absint( $field['rows'] ),
				esc_attr( $field['placeholder'] ),
				esc_textarea( $value )
			);
			break;

		case 'select':
			printf( '<select%s>', $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput

			if ( $field['placeholder'] ) {
				printf( '<option value="">%s</option>', esc_html( $field['placeholder'] ) );
			}

			foreach ( $field['options'] as $option_value => $option_label ) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $option_value ),
					selected( $value, $option_value, false ),
					esc_html( $option_label )
				);
			}

			echo '</select>';
			break;

		default:
			printf(
				'<input type="%s"%s value="%s" placeholder="%s">',
				esc_attr( $field['type'] ),
				$attrs, // phpcs:ignore WordPress.Security.EscapeOutput
				esc_attr( $value ),
				esc_attr( $field['placeholder'] )
			);
	}

	if ( $error ) {
		printf(
			'<span class="form-field__error" id="%s-error">%s</span>',
			esc_attr( $name ),
			esc_html( $error )
		);
	}

	echo '</p>';
}

/**
 * Render the consent checkbox and submit button shared by both forms.
 */
function estatein_form_footer() {
	$error = estatein_form_error( 'consent' );
	?>
	<div class="enquiry__foot">
		<p class="form-consent">
			<input type="checkbox" id="consent" name="consent" value="1" required
				<?php echo $error ? ' aria-invalid="true" aria-describedby="consent-error"' : ''; ?>>
			<label for="consent">
				<?php
				printf(
					/* translators: 1: terms of use link, 2: privacy policy link */
					esc_html__( 'I agree with %1$s and %2$s', 'estatein' ),
					'<a href="' . esc_url( home_url( '/terms-conditions/' ) ) . '">' . esc_html__( 'Terms of Use', 'estatein' ) . '</a>',
					'<a href="' . esc_url( home_url( '/privacy-policy/' ) ) . '">' . esc_html__( 'Privacy Policy', 'estatein' ) . '</a>'
				);

				if ( $error ) {
					printf(
						'<span class="form-field__error" id="consent-error">%s</span>',
						esc_html( $error )
					);
				}
				?>
			</label>
		</p>

		<button class="btn btn--primary" type="submit" name="estatein_contact_submit" value="1">
			<?php esc_html_e( 'Send Your Message', 'estatein' ); ?>
		</button>
	</div>
	<?php
}

/**
 * Render the hidden honeypot field.
 *
 * Bots fill every input they find; people never see this one.
 */
function estatein_form_honeypot() {
	?>
	<p class="screen-reader-text" aria-hidden="true">
		<label for="estatein-website"><?php esc_html_e( 'Leave this field empty', 'estatein' ); ?></label>
		<input type="text" id="estatein-website" name="estatein_website" tabindex="-1" autocomplete="off">
	</p>
	<?php
}

/**
 * Render the success or error notice above a form.
 */
function estatein_form_notice() {
	$state = estatein_form_state();
	$sent  = isset( $_GET['sent'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( $sent ) {
		printf(
			'<p class="form-note form-note--ok" role="status">%s</p>',
			esc_html__( 'Thank you — your message is on its way. We will be in touch shortly.', 'estatein' )
		);
		return;
	}

	if ( ! empty( $state['errors'] ) ) {
		printf(
			'<p class="form-note form-note--err" role="alert">%s</p>',
			esc_html__( 'Please check the highlighted fields and try again.', 'estatein' )
		);
	}
}
