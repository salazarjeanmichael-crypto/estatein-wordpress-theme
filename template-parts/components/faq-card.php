<?php
/**
 * FAQ card.
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

$answer = wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );
$id     = 'faq-' . $post_id;
?>
<article class="faq-card">

	<div class="faq-card__body">
		<h3 class="faq-card__q" id="<?php echo esc_attr( $id ); ?>">
			<?php echo esc_html( get_the_title( $post_id ) ); ?>
		</h3>
		<div class="faq-card__a" id="<?php echo esc_attr( $id ); ?>-answer">
			<p data-faq-teaser><?php echo esc_html( estatein_trim( $answer, 16 ) ); ?></p>
			<p data-faq-full hidden><?php echo esc_html( $answer ); ?></p>
		</div>
	</div>

	<button class="btn btn--surface" type="button"
	        data-faq-toggle
	        aria-expanded="false"
	        aria-controls="<?php echo esc_attr( $id ); ?>-answer">
		<span data-faq-label><?php esc_html_e( 'Read More', 'estatein' ); ?></span>
		<span class="screen-reader-text">
			<?php
			printf(
				/* translators: %s: question text */
				esc_html__( 'about: %s', 'estatein' ),
				esc_html( get_the_title( $post_id ) )
			);
			?>
		</span>
	</button>

</article>
