<?php
/**
 * Homepage: What Our Clients Say.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$testimonials = new WP_Query( array(
	'post_type'           => 'testimonial',
	'posts_per_page'      => 6,
	'post_status'         => 'publish',
	'orderby'             => 'menu_order date',
	'order'               => 'ASC',
	'ignore_sticky_posts' => true,
) );

if ( ! $testimonials->have_posts() ) {
	return;
}
?>
<section class="section" id="testimonials" aria-labelledby="testimonials-title">
	<div class="container">

		<?php
		estatein_section_head( array(
			'title'      => __( 'What Our Clients Say', 'estatein' ),
			'text'       => __( 'Read the success stories and heartfelt testimonials from our valued clients. Discover why they chose Estatein for their real estate needs.', 'estatein' ),
			'link_url'   => estatein_page_url( 'about-us' ) . '#our-clients',
			'link_label' => __( 'View All Testimonials', 'estatein' ),
		) );
		?>

		<div class="carousel" data-carousel>
			<div class="carousel__viewport">
				<div class="carousel__track" data-carousel-track>
					<?php
					while ( $testimonials->have_posts() ) :
						$testimonials->the_post();
						get_template_part( 'template-parts/components/testimonial-card' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>

			<?php
			get_template_part( 'template-parts/components/carousel-nav', null, array(
				'total' => (int) $testimonials->found_posts,
				'label' => __( 'testimonials', 'estatein' ),
			) );
			?>
		</div>

	</div>
</section>
