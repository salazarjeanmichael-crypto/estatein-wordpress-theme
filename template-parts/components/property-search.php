<?php
/**
 * Property search and filter bar.
 *
 * Submits with GET so a filtered view is shareable and the back button works.
 * Every control is a labelled form element, so it works without a mouse.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$filters   = estatein_active_filters();
$locations = get_terms( array( 'taxonomy' => 'property_location', 'hide_empty' => true ) );
$types     = get_terms( array( 'taxonomy' => 'property_type', 'hide_empty' => true ) );
$ranges    = estatein_price_ranges();
$action    = isset( $args['action'] ) ? $args['action'] : estatein_page_url( 'properties' );

/**
 * Each dropdown is one <select> dressed with the design's icon, rule and
 * chevron. Wrapping it in a <label> keeps the whole box clickable without
 * needing JavaScript to proxy the click.
 */
$controls = array(
	array(
		'name'    => 'location',
		'icon'    => 'filter-location',
		'label'   => __( 'Location', 'estatein' ),
		'options' => ( is_wp_error( $locations ) ? array() : wp_list_pluck( $locations, 'name', 'slug' ) ),
		'value'   => $filters['location'],
	),
	array(
		'name'    => 'type',
		'icon'    => 'filter-type',
		'label'   => __( 'Property Type', 'estatein' ),
		'options' => ( is_wp_error( $types ) ? array() : wp_list_pluck( $types, 'name', 'slug' ) ),
		'value'   => $filters['type'],
	),
	array(
		'name'    => 'price',
		'icon'    => 'filter-price',
		'label'   => __( 'Pricing Range', 'estatein' ),
		'options' => wp_list_pluck( $ranges, 'label' ),
		'value'   => $filters['price'],
	),
	array(
		'name'    => 'beds',
		'icon'    => 'filter-size',
		'label'   => __( 'Property Size', 'estatein' ),
		'options' => array( 1 => '1+ bedrooms', 2 => '2+ bedrooms', 3 => '3+ bedrooms', 4 => '4+ bedrooms', 5 => '5+ bedrooms' ),
		'value'   => $filters['beds'],
	),
	array(
		'name'    => 'built',
		'icon'    => 'filter-year',
		'label'   => __( 'Build Year', 'estatein' ),
		'options' => array( 2020 => '2020 or newer', 2015 => '2015 or newer', 2010 => '2010 or newer', 2000 => '2000 or newer' ),
		'value'   => $filters['built'],
	),
);
?>
<form class="property-search" method="get" action="<?php echo esc_url( $action ); ?>" role="search">

	<div class="property-search__bar-wrap">
		<div class="property-search__bar">
			<label class="screen-reader-text" for="property-q">
				<?php esc_html_e( 'Search for a property', 'estatein' ); ?>
			</label>
			<input type="search" id="property-q" name="q"
			       value="<?php echo esc_attr( $filters['keyword'] ); ?>"
			       placeholder="<?php esc_attr_e( 'Search For A Property', 'estatein' ); ?>">
			<button class="btn btn--primary" type="submit">
				<?php estatein_icon( 'search', 24 ); ?>
				<?php esc_html_e( 'Find Property', 'estatein' ); ?>
			</button>
		</div>
	</div>

	<div class="property-search__filters">
		<?php foreach ( $controls as $control ) : ?>
			<label class="filter">
				<span class="screen-reader-text"><?php echo esc_html( $control['label'] ); ?></span>
				<span class="filter__icon"><?php estatein_icon( $control['icon'], 24 ); ?></span>
				<span class="filter__rule" aria-hidden="true"></span>

				<select name="<?php echo esc_attr( $control['name'] ); ?>">
					<option value=""><?php echo esc_html( $control['label'] ); ?></option>
					<?php foreach ( $control['options'] as $key => $text ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( (string) $control['value'], (string) $key ); ?>>
							<?php echo esc_html( $text ); ?>
						</option>
					<?php endforeach; ?>
				</select>

				<span class="filter__chevron" aria-hidden="true"><?php estatein_icon( 'chevron-down', 24 ); ?></span>
			</label>
		<?php endforeach; ?>

		<?php if ( estatein_has_active_filters() ) : ?>
			<a class="btn btn--ghost filter__reset" href="<?php echo esc_url( $action ); ?>">
				<?php esc_html_e( 'Clear filters', 'estatein' ); ?>
			</a>
		<?php endif; ?>
	</div>
</form>
