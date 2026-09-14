<?php
/**
 * Template Name: Properties
 *
 * Property listing with keyword search, taxonomy and price filters, paginated
 * results, and the enquiry form from the design.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$query = estatein_property_query( 6 );
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<h1><?php echo esc_html( estatein_field( 'page_heading', __( 'Find Your Dream Property', 'estatein' ) ) ); ?></h1>
		<p>
			<?php
			echo esc_html( estatein_field(
				'page_intro',
				__( 'Welcome to Estatein, where your dream property awaits in every corner of our beautiful world. Explore our curated selection of properties, each offering a unique story and a chance to redefine your life.', 'estatein' )
			) );
			?>
		</p>
	</div>
</section>

<section class="section" aria-label="<?php esc_attr_e( 'Search properties', 'estatein' ); ?>">
	<div class="container">
		<?php get_template_part( 'template-parts/components/property-search' ); ?>
	</div>
</section>

<section class="section" id="categories" aria-labelledby="results-title">
	<div class="container">

		<?php
		estatein_section_head( array(
			'title' => __( 'Discover a World of Possibilities', 'estatein' ),
			'text'  => __( 'Our portfolio of properties is as diverse as your dreams. Explore the following categories to find the perfect property that resonates with your vision of home.', 'estatein' ),
		) );
		?>

		<?php if ( $query->have_posts() ) : ?>

			<p class="results-count">
				<?php
				printf(
					/* translators: %s: number of matching properties */
					esc_html( _n( '%s property found', '%s properties found', (int) $query->found_posts, 'estatein' ) ),
					'<b>' . esc_html( number_format_i18n( $query->found_posts ) ) . '</b>'
				);
				?>
			</p>

			<div class="property-grid">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					get_template_part( 'template-parts/components/property-card' );
				endwhile;
				?>
			</div>

			<?php estatein_pagination( $query ); ?>

		<?php else : ?>

			<div class="empty-state">
				<h3><?php esc_html_e( 'No properties matched those filters', 'estatein' ); ?></h3>
				<p><?php esc_html_e( 'Try widening your search — fewer filters usually means more homes to look at.', 'estatein' ); ?></p>
				<a class="btn btn--primary" href="<?php echo esc_url( estatein_page_url( 'properties' ) ); ?>">
					<?php esc_html_e( 'Clear all filters', 'estatein' ); ?>
				</a>
			</div>

		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

	</div>
</section>

<?php
// Anything the client types into the page editor renders here, between the
// listing and the enquiry form.
$page_content = get_post_field( 'post_content', get_queried_object_id() );

if ( trim( $page_content ) ) :
	?>
	<section class="section">
		<div class="container entry">
			<?php echo wp_kses_post( apply_filters( 'the_content', $page_content ) ); ?>
		</div>
	</section>
	<?php
endif;
?>

<section class="section" aria-labelledby="enquiry-title">
	<div class="container">
		<?php
		estatein_section_head( array(
			'title' => __( 'Let&#8217;s Make it Happen', 'estatein' ),
			'text'  => __( 'Ready to take the first step toward your dream property? Fill out the form below and our team will work to find your perfect match.', 'estatein' ),
		) );
		?>
		<?php get_template_part( 'template-parts/components/enquiry-form' ); ?>
	</div>
</section>

<?php
get_template_part( 'template-parts/components/cta' );

get_footer();
