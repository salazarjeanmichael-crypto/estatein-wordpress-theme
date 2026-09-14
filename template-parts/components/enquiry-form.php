<?php
/**
 * Property enquiry form ("Let's Make it Happen").
 *
 * Posts to the shared handler in inc/forms.php: a failure re-renders with
 * values intact and fields marked, a success redirects so refresh is safe.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$counts = array_combine( range( 1, 5 ), array( '1+', '2+', '3+', '4+', '5+' ) );

$fields = array(
	array( 'name' => 'first_name', 'label' => __( 'First Name', 'estatein' ), 'placeholder' => __( 'Enter First Name', 'estatein' ), 'required' => true, 'autocomplete' => 'given-name' ),
	array( 'name' => 'last_name',  'label' => __( 'Last Name', 'estatein' ),  'placeholder' => __( 'Enter Last Name', 'estatein' ), 'autocomplete' => 'family-name' ),
	array( 'name' => 'email',      'label' => __( 'Email', 'estatein' ),      'type' => 'email', 'placeholder' => __( 'Enter your Email', 'estatein' ), 'required' => true, 'autocomplete' => 'email' ),
	array( 'name' => 'phone',      'label' => __( 'Phone', 'estatein' ),      'type' => 'tel',   'placeholder' => __( 'Enter Phone Number', 'estatein' ), 'autocomplete' => 'tel' ),

	array( 'name' => 'pref_location', 'label' => __( 'Preferred Location', 'estatein' ), 'type' => 'select', 'placeholder' => __( 'Select Location', 'estatein' ),      'options' => estatein_term_options( 'property_location' ) ),
	array( 'name' => 'pref_type',     'label' => __( 'Property Type', 'estatein' ),      'type' => 'select', 'placeholder' => __( 'Select Property Type', 'estatein' ), 'options' => estatein_term_options( 'property_type' ) ),
	array( 'name' => 'bathrooms',     'label' => __( 'No. of Bathrooms', 'estatein' ),   'type' => 'select', 'placeholder' => __( 'Select no. of Bathrooms', 'estatein' ), 'options' => $counts ),
	array( 'name' => 'bedrooms',      'label' => __( 'No. of Bedrooms', 'estatein' ),    'type' => 'select', 'placeholder' => __( 'Select no. of Bedrooms', 'estatein' ),  'options' => $counts ),

	array(
		'name'        => 'budget',
		'label'       => __( 'Budget', 'estatein' ),
		'type'        => 'select',
		'placeholder' => __( 'Select Budget', 'estatein' ),
		'full'        => true,
		'options'     => array(
			'under-500k' => __( 'Under $500,000', 'estatein' ),
			'500k-750k'  => __( '$500,000 - $750,000', 'estatein' ),
			'750k-1m'    => __( '$750,000 - $1M', 'estatein' ),
			'1m-plus'    => __( '$1M and above', 'estatein' ),
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

		<div class="form-grid">
			<?php
			foreach ( $fields as $field ) {
				estatein_form_field( $field );
			}
			?>
		</div>

		<?php estatein_form_footer(); ?>
	</form>
</div>
