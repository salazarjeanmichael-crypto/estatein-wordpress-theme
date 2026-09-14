<?php
/**
 * Homepage hero.
 *
 * Two columns on desktop (copy | photo) collapsing to a single column below
 * 1100px, matching the Laptop and Mobile frames in the Figma file.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$heading = estatein_field( 'hero_heading', __( 'Discover Your Dream Property with Estatein', 'estatein' ) );
$text    = estatein_field( 'hero_text', __( 'Your journey to finding the perfect property begins here. Explore our listings to find the home that matches your dreams.', 'estatein' ) );
$image   = estatein_field( 'hero_image', '' );

$stats = estatein_field( 'hero_stats', array(
	array( 'value' => '200+', 'label' => __( 'Happy Customers', 'estatein' ) ),
	array( 'value' => '10k+', 'label' => __( 'Properties For Clients', 'estatein' ) ),
	array( 'value' => '16+',  'label' => __( 'Years of Experience', 'estatein' ) ),
) );

// The trailing space is load-bearing: characters wrap a full 360 degrees, so
// without it the last letter collides with the first.
$badge_text = __( 'Discover Your Dream Property ', 'estatein' );
$badge_len  = max( 1, mb_strlen( $badge_text ) );
?>
<section class="hero" aria-labelledby="hero-title">
	<div class="hero__grid">

		<div class="hero__copy">

			<div class="hero__text">
				<h1 id="hero-title"><?php echo esc_html( $heading ); ?></h1>
				<p><?php echo esc_html( $text ); ?></p>

				<?php /* Decorative rotating badge from the Figma design. */ ?>
				<div class="hero__badge" aria-hidden="true">
					<span class="hero__badge-ring">
						<?php
						for ( $i = 0; $i < $badge_len; $i++ ) {
							printf(
								'<span style="--a:%sdeg">%s</span>',
								esc_attr( round( ( 360 / $badge_len ) * $i, 2 ) ),
								esc_html( mb_substr( $badge_text, $i, 1 ) )
							);
						}
						?>
					</span>
					<span class="hero__badge-inner">
						<?php estatein_icon( 'arrow-up-right', 34 ); ?>
					</span>
				</div>
			</div>

			<div class="hero__actions">
				<a class="btn btn--ghost" href="<?php echo esc_url( estatein_page_url( 'about-us' ) ); ?>">
					<?php esc_html_e( 'Learn More', 'estatein' ); ?>
				</a>
				<a class="btn btn--primary" href="<?php echo esc_url( estatein_page_url( 'properties' ) ); ?>">
					<?php esc_html_e( 'Browse Properties', 'estatein' ); ?>
				</a>
			</div>

			<?php if ( $stats ) : ?>
				<ul class="hero__stats">
					<?php foreach ( $stats as $stat ) : ?>
						<li class="stat">
							<span class="stat__value"><?php echo esc_html( $stat['value'] ); ?></span>
							<span class="stat__label"><?php echo esc_html( $stat['label'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</div>

		<div class="hero__media">
			<?php if ( $image && is_array( $image ) ) : ?>
				<img src="<?php echo esc_url( $image['url'] ); ?>"
				     alt="<?php echo esc_attr( $image['alt'] ); ?>"
				     width="<?php echo esc_attr( $image['width'] ); ?>"
				     height="<?php echo esc_attr( $image['height'] ); ?>"
				     fetchpriority="high" decoding="async">
			<?php else : ?>
				<?php /* fetchpriority high: this is the largest contentful paint element. */ ?>
				<img src="<?php echo esc_url( ESTATEIN_URI . '/assets/img/hero.jpg' ); ?>"
				     alt="<?php esc_attr_e( 'Glass-fronted apartment tower against a clear sky', 'estatein' ); ?>"
				     width="960" height="814"
				     fetchpriority="high" decoding="async">
			<?php endif; ?>
		</div>

	</div>
</section>
