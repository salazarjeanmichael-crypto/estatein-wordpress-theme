<?php
/**
 * Service card: ringed icon and title on one row, description beneath.
 *
 * @param string $args['icon']  Icon file name, without extension.
 * @param string $args['title'] Card title.
 * @param string $args['text']  Card description.
 * @param string $args['id']    Optional anchor id.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$icon  = isset( $args['icon'] ) ? $args['icon'] : '';
$title = isset( $args['title'] ) ? $args['title'] : '';
$text  = isset( $args['text'] ) ? $args['text'] : '';
$id    = isset( $args['id'] ) ? $args['id'] : '';

if ( ! $title ) {
	return;
}
?>
<article class="service-card"<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>>
	<div class="service-card__head">
		<span class="service-card__icon">
			<span class="service-card__icon-inner">
				<?php estatein_icon( $icon, 34 ); ?>
			</span>
		</span>
		<h3><?php echo esc_html( $title ); ?></h3>
	</div>
	<?php if ( $text ) : ?>
		<p><?php echo esc_html( $text ); ?></p>
	<?php endif; ?>
</article>
