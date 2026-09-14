<?php
/**
 * Search results.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<h1>
			<?php
			printf(
				/* translators: %s: search term */
				esc_html__( 'Results for &#8220;%s&#8221;', 'estatein' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
		<p>
			<?php
			printf(
				/* translators: %s: number of results */
				esc_html( _n( '%s match found.', '%s matches found.', (int) $GLOBALS['wp_query']->found_posts, 'estatein' ) ),
				esc_html( number_format_i18n( $GLOBALS['wp_query']->found_posts ) )
			);
			?>
		</p>
		<?php get_search_form(); ?>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<ul class="result-list">
				<?php while ( have_posts() ) : the_post(); ?>
					<li class="result">
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( estatein_trim( get_the_excerpt(), 28 ) ); ?></p>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php estatein_pagination( $GLOBALS['wp_query'] ); ?>
		<?php else : ?>
			<div class="empty-state">
				<h2><?php esc_html_e( 'Nothing matched that search', 'estatein' ); ?></h2>
				<p><?php esc_html_e( 'Try a different word, or browse the full property portfolio.', 'estatein' ); ?></p>
				<a class="btn btn--primary" href="<?php echo esc_url( estatein_page_url( 'properties' ) ); ?>">
					<?php esc_html_e( 'Browse properties', 'estatein' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
