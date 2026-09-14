<?php
/**
 * Blog archive: categories, tags, dates and the posts index.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$estatein_description = get_the_archive_description();
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<h1><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
		<?php if ( $estatein_description ) : ?>
			<p><?php echo esc_html( wp_strip_all_tags( $estatein_description ) ); ?></p>
		<?php endif; ?>
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
				<h2><?php esc_html_e( 'Nothing published here yet', 'estatein' ); ?></h2>
				<p><?php esc_html_e( 'Check back soon.', 'estatein' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
