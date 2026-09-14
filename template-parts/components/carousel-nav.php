<?php
/**
 * Carousel counter and previous/next controls.
 *
 * Shared by the three homepage carousels so the markup and the accessible
 * labelling stay identical across them.
 *
 * @param int    $args['total'] Total number of items.
 * @param string $args['label'] Noun used in the button labels, e.g. "properties".
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$total = isset( $args['total'] ) ? (int) $args['total'] : 0;
$label = isset( $args['label'] ) ? $args['label'] : __( 'items', 'estatein' );
?>
<div class="carousel__foot">
	<p class="carousel__count">
		<b data-carousel-current>01</b>
		<?php
		printf(
			/* translators: %s: zero-padded total number of items */
			esc_html__( 'of %s', 'estatein' ),
			esc_html( str_pad( $total, 2, '0', STR_PAD_LEFT ) )
		);
		?>
	</p>

	<div class="carousel__nav">
		<button class="carousel__btn" type="button" data-carousel-prev disabled>
			<span class="screen-reader-text">
				<?php
				printf(
					/* translators: %s: item type, e.g. properties */
					esc_html__( 'Previous %s', 'estatein' ),
					esc_html( $label )
				);
				?>
			</span>
			<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
				<path d="M19 12H5m6-6l-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</button>

		<button class="carousel__btn" type="button" data-carousel-next>
			<span class="screen-reader-text">
				<?php
				printf(
					/* translators: %s: item type, e.g. properties */
					esc_html__( 'More %s', 'estatein' ),
					esc_html( $label )
				);
				?>
			</span>
			<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
				<path d="M5 12h14m-6-6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</button>
	</div>
</div>
