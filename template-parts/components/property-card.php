<?php
/**
 * Property card.
 *
 * Used by the homepage carousel, the properties archive and related listings,
 * so the card markup exists in exactly one place.
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

$permalink = get_permalink( $post_id );
$bedrooms  = estatein_meta( 'bedrooms', $post_id );
$bathrooms = estatein_meta( 'bathrooms', $post_id );
$style     = estatein_meta( 'style', $post_id );
$price     = estatein_meta( 'price', $post_id );
$excerpt   = get_the_excerpt( $post_id );

// Tags mirror the Figma card: bedrooms, bathrooms, then the property style.
$tags = array();

if ( $bedrooms ) {
	$tags[] = array(
		'icon' => 'bed',
		/* translators: %s: number of bedrooms */
		'text' => sprintf( _n( '%s-Bedroom', '%s-Bedroom', (int) $bedrooms, 'estatein' ), number_format_i18n( $bedrooms ) ),
	);
}

if ( $bathrooms ) {
	$tags[] = array(
		'icon' => 'bath',
		/* translators: %s: number of bathrooms */
		'text' => sprintf( _n( '%s-Bathroom', '%s-Bathroom', (int) $bathrooms, 'estatein' ), number_format_i18n( $bathrooms ) ),
	);
}

if ( $style ) {
	$tags[] = array( 'icon' => 'villa', 'text' => $style );
}
?>
<article <?php post_class( 'property-card', $post_id ); ?>>

	<div class="property-card__media">
		<a href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
			<?php
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, 'estatein-card', array(
					'alt'     => the_title_attribute( array( 'echo' => false, 'post' => $post_id ) ),
					'loading' => 'lazy',
				) );
			} else {
				printf(
					'<img src="%s" width="640" height="400" alt="" loading="lazy" decoding="async">',
					esc_url( ESTATEIN_URI . '/assets/img/property-placeholder.jpg' )
				);
			}
			?>
		</a>
	</div>

	<div class="property-card__body">

		<header class="property-card__head">
			<h3 class="property-card__title">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
			</h3>

			<?php if ( $excerpt ) : ?>
				<p class="property-card__excerpt">
					<?php echo esc_html( estatein_trim( $excerpt, 16 ) ); ?>
					<a href="<?php echo esc_url( $permalink ); ?>">
						<?php esc_html_e( 'Read More', 'estatein' ); ?>
						<span class="screen-reader-text">
							<?php
							printf(
								/* translators: %s: property name */
								esc_html__( 'about %s', 'estatein' ),
								esc_html( get_the_title( $post_id ) )
							);
							?>
						</span>
					</a>
				</p>
			<?php endif; ?>
		</header>

		<?php if ( $tags ) : ?>
			<ul class="property-tags">
				<?php foreach ( $tags as $tag ) : ?>
					<li class="property-tag">
						<?php estatein_icon( $tag['icon'], 24 ); ?>
						<span><?php echo esc_html( $tag['text'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="property-card__foot">
			<?php if ( $price ) : ?>
				<dl class="property-card__price">
					<dt><?php esc_html_e( 'Price', 'estatein' ); ?></dt>
					<dd><?php echo esc_html( estatein_price( $price ) ); ?></dd>
				</dl>
			<?php endif; ?>

			<a class="btn btn--primary" href="<?php echo esc_url( $permalink ); ?>">
				<?php esc_html_e( 'View Property Details', 'estatein' ); ?>
				<span class="screen-reader-text">
					<?php
					printf(
						/* translators: %s: property name */
						esc_html__( 'for %s', 'estatein' ),
						esc_html( get_the_title( $post_id ) )
					);
					?>
				</span>
			</a>
		</div>

	</div>
</article>
