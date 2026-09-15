<?php
/**
 * Single property.
 *
 * Everything here comes from the post: gallery from its attachments, facts and
 * pricing from its fields, so nothing on this page is hard-coded per listing.
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

	$gallery  = estatein_gallery_ids( get_the_ID() );
	$features = estatein_parse_rows( estatein_meta( 'features' ), 1 );
	$fees     = estatein_parse_rows( estatein_meta( 'fees' ) );
	$monthly  = estatein_parse_rows( estatein_meta( 'monthly' ) );
	$initial  = estatein_parse_rows( estatein_meta( 'initial' ) );
	$expenses = estatein_parse_rows( estatein_meta( 'expenses' ) );

	// The price lives in its own field, so the initial-costs card borrows it
	// rather than asking an editor to keep two copies in step.
	if ( $initial && $price ) {
		array_unshift( $initial, array( __( 'Listing Price', 'estatein' ), estatein_price( $price ), '' ) );
	}

	$pricing = array(
		array( 'title' => __( 'Additional Fees', 'estatein' ), 'rows' => $fees ),
		array( 'title' => __( 'Monthly Costs', 'estatein' ), 'rows' => $monthly ),
		array( 'title' => __( 'Total Initial Costs', 'estatein' ), 'rows' => $initial ),
		array( 'title' => __( 'Monthly Expenses', 'estatein' ), 'rows' => $expenses ),
	);

	$pricing = array_filter( $pricing, function ( $group ) {
		return (bool) $group['rows'];
	} );

	$facts = array();

	if ( $bedrooms ) {
		$facts[] = array( 'icon' => 'bed', 'label' => __( 'Bedrooms', 'estatein' ), 'value' => str_pad( $bedrooms, 2, '0', STR_PAD_LEFT ) );
	}
	if ( $bathrooms ) {
		$facts[] = array( 'icon' => 'bath', 'label' => __( 'Bathrooms', 'estatein' ), 'value' => str_pad( $bathrooms, 2, '0', STR_PAD_LEFT ) );
	}
	if ( $area ) {
		$facts[] = array( 'icon' => 'area', 'label' => __( 'Area', 'estatein' ), 'value' => $area );
	}
	?>

	<article <?php post_class( 'property-single' ); ?>>

		<section class="section">
			<div class="container">

				<header class="detail-head">
					<div class="detail-head__title">
						<h1><?php the_title(); ?></h1>
						<?php if ( $address ) : ?>
							<p class="detail-head__where">
								<?php estatein_icon( 'contact-pin', 24 ); ?>
								<span><?php echo esc_html( $address ); ?></span>
							</p>
						<?php endif; ?>
					</div>

					<?php if ( $price ) : ?>
						<dl class="detail-head__price">
							<dt><?php esc_html_e( 'Price', 'estatein' ); ?></dt>
							<dd><?php echo esc_html( estatein_price( $price ) ); ?></dd>
						</dl>
					<?php endif; ?>
				</header>

				<?php if ( $gallery ) : ?>
					<?php
					/*
					 * The stage shows two photos at a time and steps one at a time,
					 * as in the design; without JavaScript every photo is simply in view.
					 */
					?>
					<div class="gallery" data-gallery>
						<ul class="gallery__thumbs">
							<?php foreach ( $gallery as $i => $id ) : ?>
								<li>
									<button class="gallery__thumb<?php echo 0 === $i ? ' is-active' : ''; ?>"
									        type="button"
									        data-gallery-thumb="<?php echo esc_attr( $i ); ?>"
									        aria-label="<?php
											printf(
												/* translators: %d: image number */
												esc_attr__( 'Show image %d', 'estatein' ),
												(int) $i + 1
											);
										?>">
										<?php echo wp_get_attachment_image( $id, 'medium', false, array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
									</button>
								</li>
							<?php endforeach; ?>
						</ul>

						<div class="gallery__stage" data-gallery-viewport>
							<div class="gallery__track" data-gallery-track>
							<?php foreach ( $gallery as $i => $id ) : ?>
								<figure class="gallery__slide" data-gallery-slide="<?php echo esc_attr( $i ); ?>">
									<?php
									echo wp_get_attachment_image( $id, 'estatein-wide', false, array(
										'alt'           => the_title_attribute( array( 'echo' => false ) ),
										'fetchpriority' => 0 === $i ? 'high' : 'low',
									) );
									?>
								</figure>
							<?php endforeach; ?>
							</div>
						</div>

						<?php if ( count( $gallery ) > 1 ) : ?>
							<div class="gallery__nav">
								<button class="carousel__btn" type="button" data-gallery-prev>
									<span class="screen-reader-text"><?php esc_html_e( 'Previous image', 'estatein' ); ?></span>
									<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
										<path d="M19 12H5m6-6l-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</button>
								<ol class="gallery__dots">
									<?php foreach ( $gallery as $i => $id ) : ?>
										<li class="gallery__dot<?php echo 0 === $i ? ' is-active' : ''; ?>" data-gallery-dot="<?php echo esc_attr( $i ); ?>"></li>
									<?php endforeach; ?>
								</ol>
								<button class="carousel__btn" type="button" data-gallery-next>
									<span class="screen-reader-text"><?php esc_html_e( 'Next image', 'estatein' ); ?></span>
									<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
										<path d="M5 12h14m-6-6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</button>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="detail-split">

					<div class="detail-panel">
						<div class="detail-panel__text">
							<h2><?php esc_html_e( 'Description', 'estatein' ); ?></h2>
							<div class="entry"><?php the_content(); ?></div>
						</div>

						<?php if ( $facts ) : ?>
							<dl class="detail-facts">
								<?php foreach ( $facts as $fact ) : ?>
									<div>
										<dt>
											<?php estatein_icon( $fact['icon'], 24 ); ?>
											<span><?php echo esc_html( $fact['label'] ); ?></span>
										</dt>
										<dd><?php echo esc_html( $fact['value'] ); ?></dd>
									</div>
								<?php endforeach; ?>
							</dl>
						<?php endif; ?>
					</div>

					<?php if ( $features ) : ?>
						<div class="detail-panel">
							<h2><?php esc_html_e( 'Key Features and Amenities', 'estatein' ); ?></h2>
							<ul class="feature-list">
								<?php foreach ( $features as $feature ) : ?>
									<li>
										<?php estatein_icon( 'feature-bolt', 24 ); ?>
										<span><?php echo esc_html( $feature[0] ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>

				</div>

			</div>
		</section>

		<section class="section" id="enquire" aria-labelledby="enquire-title">
			<div class="container detail-inquire">
				<div class="detail-inquire__intro">
					<?php
					estatein_section_head( array(
						/* translators: %s: property name */
						'title' => sprintf( __( 'Inquire About %s', 'estatein' ), get_the_title() ),
						'text'  => __( 'Interested in this property? Fill out the form below and our real estate experts will get back to you with more details, including scheduling a viewing and answering any questions you may have.', 'estatein' ),
					) );
					?>
				</div>
				<div class="detail-inquire__form">
					<?php
					get_template_part( 'template-parts/components/property-inquiry-form', null, array(
						'property' => trim( get_the_title() . ( $address ? ', ' . $address : '' ) ),
					) );
					?>
				</div>
			</div>
		</section>

		<?php if ( $pricing ) : ?>
			<section class="section" aria-labelledby="pricing-title">
				<div class="container">
					<?php
					estatein_section_head( array(
						'title' => __( 'Comprehensive Pricing Details', 'estatein' ),
						/* translators: %s: property name */
						'text'  => sprintf( __( 'At Estatein, transparency is key. We want you to have a clear understanding of all costs associated with your property investment. Below, we break down the pricing for %s to help you make an informed decision.', 'estatein' ), get_the_title() ),
					) );
					?>

					<p class="pricing-note">
						<b><?php esc_html_e( 'Note', 'estatein' ); ?></b>
						<span><?php esc_html_e( 'The figures provided below are estimates and may vary depending on the property, location, and individual circumstances.', 'estatein' ); ?></span>
					</p>

					<div class="pricing-cards">
						<?php foreach ( $pricing as $group ) : ?>
							<section class="pricing-card">
								<header class="pricing-card__head">
									<h3><?php echo esc_html( $group['title'] ); ?></h3>
									<a class="btn btn--surface" href="<?php echo esc_url( estatein_page_url( 'contact-us' ) ); ?>">
										<?php esc_html_e( 'Learn More', 'estatein' ); ?>
									</a>
								</header>

								<dl class="pricing-rows">
									<?php foreach ( $group['rows'] as $row ) : ?>
										<div>
											<dt><?php echo esc_html( $row[0] ); ?></dt>
											<dd>
												<b><?php echo esc_html( $row[1] ); ?></b>
												<?php if ( $row[2] ) : ?>
													<span class="pricing-rows__note"><?php echo esc_html( $row[2] ); ?></span>
												<?php endif; ?>
											</dd>
										</div>
									<?php endforeach; ?>
								</dl>
							</section>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/home/faq' ); ?>

		<?php
		// Related: same property type, excluding the current listing.
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
