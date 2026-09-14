<?php
/**
 * Homepage: Featured Properties.
 *
 * A standard WP_Query, so the section reflects what the client published.
 * Without JavaScript the carousel track wraps into a plain, readable grid.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$properties = new WP_Query( array(
	'post_type'           => 'property',
	'posts_per_page'      => 6,
	'post_status'         => 'publish',
	'orderby'             => 'menu_order date',
	'order'               => 'ASC',
	'ignore_sticky_posts' => true,
) );

if ( ! $properties->have_posts() ) {
	return;
}

$total = (int) $properties->found_posts;
?>
<section class="section" id="properties" aria-labelledby="featured-properties-title">
	<div class="container">

		<?php
		estatein_section_head( array(
			'title'      => __( 'Featured Properties', 'estatein' ),
			'text'       => __( 'Explore our handpicked selection of featured properties. Each listing offers a glimpse into exceptional homes and investments available through Estatein. Click "View Details" for more information.', 'estatein' ),
			'link_url'   => get_post_type_archive_link( 'property' ),
			'link_label' => __( 'View All Properties', 'estatein' ),
		) );
		?>

		<div class="carousel" data-carousel>
			<div class="carousel__viewport">
				<div class="carousel__track" data-carousel-track>
					<?php
					while ( $properties->have_posts() ) :
						$properties->the_post();
						get_template_part( 'template-parts/components/property-card' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>

			<?php
			get_template_part( 'template-parts/components/carousel-nav', null, array(
				'total' => $total,
				'label' => __( 'properties', 'estatein' ),
			) );
			?>
		</div>

	</div>
</section>
