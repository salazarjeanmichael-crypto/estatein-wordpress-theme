<?php
/**
 * Default page template.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class(); ?>>

		<section class="page-hero">
			<div class="container page-hero__inner">
				<h1><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</section>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="container">
				<?php the_post_thumbnail( 'estatein-wide', array( 'class' => 'page-featured' ) ); ?>
			</div>
		<?php endif; ?>

		<section class="section">
			<div class="container entry">
				<?php
				the_content();

				wp_link_pages( array(
					'before' => '<nav class="pagination">',
					'after'  => '</nav>',
				) );
				?>
			</div>
		</section>

	</article>
	<?php
endwhile;

get_template_part( 'template-parts/components/cta' );

get_footer();
