<?php
/**
 * Single blog post.
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
				<p class="post-meta">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
						<?php echo esc_html( get_the_date() ); ?>
					</time>
					<?php
					$categories = get_the_category_list( ', ' );

					if ( $categories ) {
						echo ' &middot; ' . wp_kses_post( $categories );
					}
					?>
				</p>
				<h1><?php the_title(); ?></h1>
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

		<?php if ( comments_open() || get_comments_number() ) : ?>
			<section class="section">
				<div class="container entry">
					<?php comments_template(); ?>
				</div>
			</section>
		<?php endif; ?>

	</article>
	<?php
endwhile;

get_template_part( 'template-parts/components/cta' );

get_footer();
