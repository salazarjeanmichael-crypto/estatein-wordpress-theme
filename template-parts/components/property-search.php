<?php
/**
 * Property search and filter bar.
 *
 * Submits with GET so a filtered view is shareable and the back button works.
 * Every control is a real form element with a label, so the bar is usable with
 * a keyboard and a screen reader.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$filters   = estatein_active_filters();
$locations = get_terms( array( 'taxonomy' => 'property_location', 'hide_empty' => true ) );
$types     = get_terms( array( 'taxonomy' => 'property_type', 'hide_empty' => true ) );
$ranges    = estatein_price_ranges();
$action    = isset( $args['action'] ) ? $args['action'] : estatein_page_url( 'properties' );
?>
<form class="property-search" method="get" action="<?php echo esc_url( $action ); ?>" role="search">

	<div class="property-search__bar">
		<label class="screen-reader-text" for="property-q">
			<?php esc_html_e( 'Search for a property', 'estatein' ); ?>
		</label>
		<input type="search" id="property-q" name="q"
		       value="<?php echo esc_attr( $filters['keyword'] ); ?>"
		       placeholder="<?php esc_attr_e( 'Search For A Property', 'estatein' ); ?>">
		<button class="btn btn--primary" type="submit">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
				<circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
				<path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
			</svg>
			<?php esc_html_e( 'Find Property', 'estatein' ); ?>
		</button>
	</div>

	<div class="property-search__filters">

		<p class="filter">
			<label class="screen-reader-text" for="filter-location"><?php esc_html_e( 'Location', 'estatein' ); ?></label>
			<select id="filter-location" name="location">
				<option value=""><?php esc_html_e( 'Location', 'estatein' ); ?></option>
				<?php if ( ! is_wp_error( $locations ) ) : ?>
					<?php foreach ( $locations as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $filters['location'], $term->slug ); ?>>
							<?php echo esc_html( $term->name ); ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</p>

		<p class="filter">
			<label class="screen-reader-text" for="filter-type"><?php esc_html_e( 'Property Type', 'estatein' ); ?></label>
			<select id="filter-type" name="type">
				<option value=""><?php esc_html_e( 'Property Type', 'estatein' ); ?></option>
				<?php if ( ! is_wp_error( $types ) ) : ?>
					<?php foreach ( $types as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $filters['type'], $term->slug ); ?>>
							<?php echo esc_html( $term->name ); ?>
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</p>

		<p class="filter">
			<label class="screen-reader-text" for="filter-price"><?php esc_html_e( 'Pricing Range', 'estatein' ); ?></label>
			<select id="filter-price" name="price">
				<option value=""><?php esc_html_e( 'Pricing Range', 'estatein' ); ?></option>
				<?php foreach ( $ranges as $key => $range ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $filters['price'], $key ); ?>>
						<?php echo esc_html( $range['label'] ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="filter">
			<label class="screen-reader-text" for="filter-beds"><?php esc_html_e( 'Bedrooms', 'estatein' ); ?></label>
			<select id="filter-beds" name="beds">
				<option value=""><?php esc_html_e( 'Bedrooms', 'estatein' ); ?></option>
				<?php foreach ( array( 1, 2, 3, 4, 5 ) as $n ) : ?>
					<option value="<?php echo esc_attr( $n ); ?>" <?php selected( $filters['beds'], $n ); ?>>
						<?php
						printf(
							/* translators: %d: minimum number of bedrooms */
							esc_html__( '%d+ bedrooms', 'estatein' ),
							(int) $n
						);
						?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>

		<?php if ( estatein_has_active_filters() ) : ?>
			<a class="btn btn--ghost filter__reset" href="<?php echo esc_url( $action ); ?>">
				<?php esc_html_e( 'Clear filters', 'estatein' ); ?>
			</a>
		<?php endif; ?>

	</div>
</form>
