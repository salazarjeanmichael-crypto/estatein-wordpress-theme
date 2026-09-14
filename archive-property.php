<?php
/**
 * Property archive and taxonomy listings.
 *
 * Shares the search bar, card grid and pagination with the Properties page so
 * /property/ and /location/<term>/ look and behave identically.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$heading = post_type_archive_title( '', false );
$intro   = __( 'Browse every property currently listed with Estatein.', 'estatein' );

if ( is_tax() ) {
	$term    = get_queried_object();
	$heading = single_term_title( '', false );
	$intro   = ( $term instanceof WP_Term && $term->description )
		? $term->description
		: sprintf(
			/* translators: %s: taxonomy term name */
			__( 'Properties in %s.', 'estatein' ),
			$heading
		);
}
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<h1><?php echo esc_html( $heading ); ?></h1>
		<p><?php echo esc_html( $intro ); ?></p>
	</div>
</section>

<section class="section" aria-label="<?php esc_attr_e( 'Search properties', 'estatein' ); ?>">
	<div class="container">
		<?php
		get_template_part( 'template-parts/components/property-search', null, array(
			'action' => get_post_type_archive_link( 'property' ),
		) );
		?>
	</div>
</section>

<section class="section">
	<div class="container">

		<?php if ( have_posts() ) : ?>

			<p class="results-count">
				<?php
				printf(
					/* translators: %s: number of matching properties */
					esc_html( _n( '%s property found', '%s properties found', (int) $GLOBALS['wp_query']->found_posts, 'estatein' ) ),
					'<b>' . esc_html( number_format_i18n( $GLOBALS['wp_query']->found_posts ) ) . '</b>'
				);
				?>
			</p>

			<div class="property-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/components/property-card' );
				endwhile;
				?>
			</div>

			<?php estatein_pagination( $GLOBALS['wp_query'] ); ?>

		<?php else : ?>

			<div class="empty-state">
				<h2><?php esc_html_e( 'No properties here yet', 'estatein' ); ?></h2>
				<p><?php esc_html_e( 'Nothing is listed under this category at the moment. Try browsing the full portfolio.', 'estatein' ); ?></p>
				<a class="btn btn--primary" href="<?php echo esc_url( estatein_page_url( 'properties' ) ); ?>">
					<?php esc_html_e( 'Browse all properties', 'estatein' ); ?>
				</a>
			</div>

		<?php endif; ?>

	</div>
</section>

<?php
get_template_part( 'template-parts/components/cta' );

get_footer();
