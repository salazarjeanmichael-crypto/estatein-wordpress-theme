<?php
/**
 * Single property.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$price     = estatein_meta( 'price' );
	$address   = estatein_meta( 'address' );
	$bedrooms  = estatein_meta( 'bedrooms' );
	$bathrooms = estatein_meta( 'bathrooms' );
	$area      = estatein_meta( 'area' );
	$style     = estatein_meta( 'style' );

	$facts = array_filter( array(
		__( 'Bedrooms', 'estatein' )   => $bedrooms,
		__( 'Bathrooms', 'estatein' )  => $bathrooms,
		__( 'Floor area', 'estatein' ) => $area,
		__( 'Type', 'estatein' )       => $style,
	) );
	?>

	<article <?php post_class( 'property-single' ); ?>>

		<section class="property-hero">
			<div class="container">

				<div class="property-hero__head">
					<div class="property-hero__title">
						<h1><?php the_title(); ?></h1>
						<?php if ( $address ) : ?>
							<p><?php echo esc_html( $address ); ?></p>
						<?php endif; ?>
					</div>
					<a class="btn btn--surface" href="<?php echo esc_url( estatein_page_url( 'properties' ) ); ?>">
						<?php esc_html_e( 'Back to all properties', 'estatein' ); ?>
					</a>
				</div>

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="property-hero__gallery">
						<?php
						the_post_thumbnail( 'estatein-wide', array(
							'fetchpriority' => 'high',
							'decoding'      => 'async',
						) );
						?>
					</div>
				<?php endif; ?>

			</div>
		</section>

		<section class="section">
			<div class="container property-layout">

				<div>
					<?php if ( $facts ) : ?>
						<div class="property-facts">
							<?php foreach ( $facts as $label => $value ) : ?>
								<dl class="property-fact">
									<dt><?php echo esc_html( $label ); ?></dt>
									<dd><?php echo esc_html( $value ); ?></dd>
								</dl>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<div class="entry">
						<?php the_content(); ?>
					</div>
				</div>

				<aside class="property-aside" aria-label="<?php esc_attr_e( 'Property summary', 'estatein' ); ?>">
					<?php if ( $price ) : ?>
						<p>
							<span class="property-aside__label"><?php esc_html_e( 'Price', 'estatein' ); ?></span>
							<span class="property-aside__price"><?php echo esc_html( estatein_price( $price ) ); ?></span>
						</p>
					<?php endif; ?>

					<a class="btn btn--primary btn--block" href="<?php echo esc_url( estatein_page_url( 'contact-us' ) ); ?>#contact-form">
						<?php esc_html_e( 'Enquire About This Property', 'estatein' ); ?>
					</a>

					<a class="btn btn--block" href="<?php echo esc_url( estatein_page_url( 'properties' ) ); ?>">
						<?php esc_html_e( 'Browse Similar Homes', 'estatein' ); ?>
					</a>
				</aside>

			</div>
		</section>

		<?php
		// Matched on type, not location: someone viewing a villa is shopping by
		// kind of home. Untyped listings fall through to recent, not empty.
		$types = wp_get_post_terms( get_the_ID(), 'property_type', array( 'fields' => 'ids' ) );

		$related = new WP_Query( array(
			'post_type'      => 'property',
			'post__not_in'   => array( get_the_ID() ),
			'posts_per_page' => 3,
			'post_status'    => 'publish',
			'tax_query'      => ( $types && ! is_wp_error( $types ) ) ? array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'property_type',
					'field'    => 'term_id',
					'terms'    => $types,
				),
			) : array(),
		) );

		if ( $related->have_posts() ) :
			?>
			<section class="section" aria-labelledby="related-title">
				<div class="container">
					<?php
					estatein_section_head( array(
						'title'      => __( 'You May Also Like', 'estatein' ),
						'text'       => __( 'Other listings in the same category, picked from our current portfolio.', 'estatein' ),
						'link_url'   => estatein_page_url( 'properties' ),
						'link_label' => __( 'View All Properties', 'estatein' ),
					) );
					?>
					<div class="property-grid">
						<?php
						while ( $related->have_posts() ) :
							$related->the_post();
							get_template_part( 'template-parts/components/property-card' );
						endwhile;
						?>
					</div>
				</div>
			</section>
			<?php
		endif;

		wp_reset_postdata();
		?>

	</article>

	<?php
endwhile;

get_template_part( 'template-parts/components/cta' );

get_footer();
