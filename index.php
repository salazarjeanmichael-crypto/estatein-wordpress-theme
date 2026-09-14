<?php
/**
 * Fallback template — required by WordPress for any theme.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="container section">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'card' ); ?> style="margin-bottom:var(--sp-4)">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
	<?php else : ?>
		<h1><?php esc_html_e( 'Nothing found', 'estatein' ); ?></h1>
		<p><?php esc_html_e( 'No content matched your request.', 'estatein' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
