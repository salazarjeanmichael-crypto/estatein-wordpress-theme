<?php
/**
 * 404 - page not found.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<p class="post-meta"><?php esc_html_e( 'Error 404', 'estatein' ); ?></p>
		<h1><?php esc_html_e( 'We could not find that page', 'estatein' ); ?></h1>
		<p><?php esc_html_e( 'The link may be out of date, or the property may have been sold and taken down. Here are a few ways back.', 'estatein' ); ?></p>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="hero__actions">
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Back to home', 'estatein' ); ?>
			</a>
			<a class="btn btn--surface" href="<?php echo esc_url( estatein_page_url( 'properties' ) ); ?>">
				<?php esc_html_e( 'Browse properties', 'estatein' ); ?>
			</a>
			<a class="btn btn--surface" href="<?php echo esc_url( estatein_page_url( 'contact-us' ) ); ?>">
				<?php esc_html_e( 'Contact us', 'estatein' ); ?>
			</a>
		</div>
	</div>
</section>

<?php
get_footer();
