<?php
/**
 * Property inquiry form, shown on a single listing.
 *
 * Falls back to the general contact form if the inquiry form is missing, so
 * the section never renders as an empty box.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="enquiry" id="contact-form">
	<?php
	if ( estatein_render_cf7( 'estatein_form_inquiry' ) ) {
		echo '</div>';
		return;
	}

	get_template_part( 'template-parts/components/contact-form' );
	?>
</div>
