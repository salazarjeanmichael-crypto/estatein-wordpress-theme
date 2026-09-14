<?php
/**
 * General contact form ("Let's Connect").
 *
 * Same handler and validation contract as the property enquiry form, with the
 * shorter field set from the Contact page design.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$fields = array(
	array( 'name' => 'first_name', 'label' => __( 'First Name', 'estatein' ), 'placeholder' => __( 'Enter First Name', 'estatein' ), 'required' => true, 'autocomplete' => 'given-name' ),
	array( 'name' => 'last_name',  'label' => __( 'Last Name', 'estatein' ),  'placeholder' => __( 'Enter Last Name', 'estatein' ), 'autocomplete' => 'family-name' ),
	array( 'name' => 'email',      'label' => __( 'Email', 'estatein' ),      'type' => 'email', 'placeholder' => __( 'Enter your Email', 'estatein' ), 'required' => true, 'autocomplete' => 'email' ),
	array( 'name' => 'phone',      'label' => __( 'Phone', 'estatein' ),      'type' => 'tel',   'placeholder' => __( 'Enter Phone Number', 'estatein' ), 'autocomplete' => 'tel' ),

	array(
		'name'        => 'inquiry',
		'label'       => __( 'Inquiry Type', 'estatein' ),
		'type'        => 'select',
		'placeholder' => __( 'Select Inquiry Type', 'estatein' ),
		'options'     => array(
			'buying'     => __( 'Buying a property', 'estatein' ),
			'selling'    => __( 'Selling a property', 'estatein' ),
			'renting'    => __( 'Renting', 'estatein' ),
			'investment' => __( 'Investment advice', 'estatein' ),
			'management' => __( 'Property management', 'estatein' ),
			'other'      => __( 'Something else', 'estatein' ),
		),
	),

	array(
		'name'        => 'source',
		'label'       => __( 'How Did You Hear About Us?', 'estatein' ),
		'type'        => 'select',
		'placeholder' => __( 'Select', 'estatein' ),
		'options'     => array(
			'search'   => __( 'Search engine', 'estatein' ),
			'social'   => __( 'Social media', 'estatein' ),
			'referral' => __( 'Friend or colleague', 'estatein' ),
			'advert'   => __( 'Advertisement', 'estatein' ),
			'other'    => __( 'Other', 'estatein' ),
		),
	),

	array( 'name' => 'message', 'label' => __( 'Message', 'estatein' ), 'type' => 'textarea', 'placeholder' => __( 'Enter your Message here..', 'estatein' ), 'required' => true, 'full' => true ),
);
?>
<div class="enquiry" id="contact-form">

	<?php estatein_form_notice(); ?>

	<form method="post" action="<?php echo esc_url( get_permalink() ); ?>#contact-form" novalidate>
		<?php
		wp_nonce_field( 'estatein_contact', 'estatein_contact_nonce' );
		estatein_form_honeypot();

		// Carry the return URL so success does not depend on a Referer header.
		printf(
			'<input type="hidden" name="estatein_return" value="%s">',
			esc_url( get_permalink() )
		);
		?>

		<div class="form-grid form-grid--thirds">
			<?php
			foreach ( $fields as $field ) {
				estatein_form_field( $field );
			}
			?>
		</div>

		<?php estatein_form_footer(); ?>
	</form>
</div>
