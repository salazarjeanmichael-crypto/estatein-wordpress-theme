<?php
/**
 * Homepage: Frequently Asked Questions.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$faqs = new WP_Query( array(
	'post_type'           => 'faq',
	'posts_per_page'      => 6,
	'post_status'         => 'publish',
	'orderby'             => 'menu_order date',
	'order'               => 'ASC',
	'ignore_sticky_posts' => true,
) );

if ( ! $faqs->have_posts() ) {
	return;
}
?>
<section class="section" id="faq" aria-labelledby="faq-title">
	<div class="container">

		<?php
		estatein_section_head( array(
			'title'      => __( 'Frequently Asked Questions', 'estatein' ),
			'text'       => __( 'Find answers to common questions about Estatein services, property listings, and the real estate process. We are here to provide clarity and assist you every step of the way.', 'estatein' ),
			'link_url'   => estatein_page_url( 'contact-us' ),
			'link_label' => __( 'View All FAQ&#8217;s', 'estatein' ),
		) );
		?>

		<div class="carousel" data-carousel>
			<div class="carousel__viewport">
				<div class="carousel__track" data-carousel-track>
					<?php
					while ( $faqs->have_posts() ) :
						$faqs->the_post();
						get_template_part( 'template-parts/components/faq-card' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>

			<?php
			get_template_part( 'template-parts/components/carousel-nav', null, array(
				'total' => (int) $faqs->found_posts,
				'label' => __( 'questions', 'estatein' ),
			) );
			?>
		</div>

	</div>
</section>
