<?php
/**
 * Testimonial card.
 *
 * Expects to run inside the loop, or receive $args['post_id'].
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();

if ( ! $post_id ) {
	return;
}

$rating   = estatein_meta( 'rating', $post_id, 5 );
$author   = estatein_meta( 'author', $post_id );
$location = estatein_meta( 'location', $post_id );
$quote    = get_post_field( 'post_content', $post_id );
?>
<article class="testimonial">

	<?php estatein_rating( $rating ); ?>

	<div class="testimonial__body">
		<h3 class="testimonial__title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
		<blockquote class="testimonial__quote">
			<p><?php echo esc_html( wp_strip_all_tags( $quote ) ); ?></p>
		</blockquote>
	</div>

	<?php if ( $author ) : ?>
		<footer class="testimonial__author">
			<?php
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, 'thumbnail', array(
					'alt'     => '',
					'loading' => 'lazy',
				) );
			} else {
				echo get_avatar( '', 60, '', '', array( 'class' => 'testimonial__avatar' ) );
			}
			?>
			<p>
				<span class="testimonial__name"><?php echo esc_html( $author ); ?></span>
				<?php if ( $location ) : ?>
					<span class="testimonial__loc"><?php echo esc_html( $location ); ?></span>
				<?php endif; ?>
			</p>
		</footer>
	<?php endif; ?>

</article>
