<?php
/**
 * Closing call to action, shown above the footer on every page.
 *
 * Text is overridable per page via $args so the same component can close the
 * homepage, the properties archive and the service pages.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$title = isset( $args['title'] ) ? $args['title'] : __( 'Start Your Real Estate Journey Today', 'estatein' );
$text  = isset( $args['text'] )  ? $args['text']  : __( 'Your dream property is just a click away. Whether you are looking for a new home, a strategic investment, or expert real estate advice, Estatein is here to assist you every step of the way. Take the first step towards your real estate goals and explore our available properties or get in touch with our team for personalized assistance.', 'estatein' );
$label = isset( $args['label'] ) ? $args['label'] : __( 'Explore Properties', 'estatein' );
$url   = isset( $args['url'] )   ? $args['url']   : estatein_page_url( 'properties' );
?>
<section class="cta" aria-labelledby="cta-title">
	<div class="container cta__inner">
		<div class="cta__text">
			<h2 id="cta-title"><?php echo esc_html( $title ); ?></h2>
			<p><?php echo esc_html( $text ); ?></p>
		</div>
		<a class="btn btn--primary" href="<?php echo esc_url( $url ); ?>">
			<?php echo esc_html( $label ); ?>
		</a>
	</div>
</section>
