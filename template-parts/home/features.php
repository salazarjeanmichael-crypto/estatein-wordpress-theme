<?php
/**
 * Homepage feature strip: four value propositions below the hero.
 *
 * In Figma this is one bordered panel holding four cards, with a 10px ring
 * around the panel itself.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$features = estatein_rows_field( 'home_features', array(
	array(
		'icon'  => 'feature-home',
		'title' => __( 'Find Your Dream Home', 'estatein' ),
		'url'   => estatein_page_url( 'properties' ),
	),
	array(
		'icon'  => 'feature-value',
		'title' => __( 'Unlock Property Value', 'estatein' ),
		'url'   => estatein_page_url( 'services' ) . '#valuation-mastery',
	),
	array(
		'icon'  => 'feature-management',
		'title' => __( 'Effortless Property Management', 'estatein' ),
		'url'   => estatein_page_url( 'services' ) . '#property-management',
	),
	array(
		'icon'  => 'feature-invest',
		'title' => __( 'Smart Investments, Informed Decisions', 'estatein' ),
		'url'   => estatein_page_url( 'services' ) . '#strategic-marketing',
	),
), array( 'title', 'url', 'icon' ) );

if ( ! $features ) {
	return;
}
?>
<section class="features-section" id="features" aria-label="<?php esc_attr_e( 'What Estatein offers', 'estatein' ); ?>">
	<div class="features-wrap">
		<ul class="features">
			<?php foreach ( $features as $feature ) : ?>
				<li class="feature">
					<a class="feature__link" href="<?php echo esc_url( $feature['url'] ); ?>">
						<span class="feature__icon">
							<span class="feature__icon-inner">
								<?php estatein_icon( $feature['icon'], 34 ); ?>
							</span>
						</span>
						<span class="feature__title"><?php echo esc_html( $feature['title'] ); ?></span>
						<span class="feature__arrow">
							<?php estatein_icon( 'arrow-diagonal', 34 ); ?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
