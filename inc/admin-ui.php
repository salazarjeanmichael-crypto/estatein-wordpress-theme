<?php
/**
 * Admin editing aids.
 *
 * Repeaters are ACF PRO, so the list fields store piped lines; this turns those
 * textareas into a row editor so an editor never types a separator by hand.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icon choices for a field column, read from the theme's icon directory.
 *
 * @param string $prefix Only icons whose file name starts with this.
 * @return array value => label
 */
function estatein_icon_choices( $prefix = '' ) {
	$files   = glob( ESTATEIN_DIR . '/assets/img/icons/' . $prefix . '*.svg' );
	$choices = array();

	foreach ( (array) $files as $file ) {
		$name             = basename( $file, '.svg' );
		$choices[ $name ] = ucwords( str_replace( array( '-', '_' ), ' ', $name ) );
	}

	return $choices;
}

/**
 * Which fields are lists, and what the columns on each line mean.
 *
 * One definition drives both the editor UI and the field instructions, so the
 * two cannot describe different formats.
 *
 * @return array
 */
function estatein_row_fields() {
	return array(
		'hero_stats' => array(
			'add'     => __( 'Add statistic', 'estatein' ),
			'columns' => array(
				array( 'label' => __( 'Value', 'estatein' ), 'width' => 30, 'placeholder' => '200+' ),
				array( 'label' => __( 'Label', 'estatein' ), 'width' => 70, 'placeholder' => __( 'Happy Customers', 'estatein' ) ),
			),
		),
		'about_stats' => array(
			'add'     => __( 'Add statistic', 'estatein' ),
			'columns' => array(
				array( 'label' => __( 'Value', 'estatein' ), 'width' => 30, 'placeholder' => '200+' ),
				array( 'label' => __( 'Label', 'estatein' ), 'width' => 70, 'placeholder' => __( 'Happy Customers', 'estatein' ) ),
			),
		),
		'home_features' => array(
			'add'     => __( 'Add tile', 'estatein' ),
			'columns' => array(
				array( 'label' => __( 'Title', 'estatein' ), 'width' => 45, 'placeholder' => __( 'Find Your Dream Home', 'estatein' ) ),
				array( 'label' => __( 'Link', 'estatein' ), 'width' => 31, 'placeholder' => '/properties/' ),
				array( 'label' => __( 'Icon', 'estatein' ), 'width' => 24, 'options' => estatein_icon_choices( 'feature-' ) ),
			),
		),
		'features' => array(
			'add'     => __( 'Add feature', 'estatein' ),
			'columns' => array(
				array( 'label' => __( 'Feature', 'estatein' ), 'width' => 100, 'placeholder' => __( 'Private beach access', 'estatein' ) ),
			),
		),
	);
}

/**
 * The four pricing cards share one column layout.
 *
 * @return array
 */
function estatein_row_fields_all() {
	$fields  = estatein_row_fields();
	$pricing = array(
		'add'     => __( 'Add row', 'estatein' ),
		'columns' => array(
			array( 'label' => __( 'Label', 'estatein' ), 'width' => 34, 'placeholder' => __( 'Property Transfer Tax', 'estatein' ) ),
			array( 'label' => __( 'Amount', 'estatein' ), 'width' => 22, 'placeholder' => '$25,000' ),
			array( 'label' => __( 'Note', 'estatein' ), 'width' => 44, 'placeholder' => __( 'Optional, shown as a pill', 'estatein' ) ),
		),
	);

	foreach ( array( 'fees', 'monthly', 'initial', 'expenses' ) as $name ) {
		$fields[ $name ] = $pricing;
	}

	return $fields;
}

/**
 * Load the row editor on the screens that have one of these fields.
 *
 * @param string $hook Current admin page.
 */
function estatein_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$css = '/assets/css/admin.css';
	$js  = '/assets/js/admin-rows.js';

	wp_enqueue_style( 'estatein-admin', ESTATEIN_URI . $css, array(), estatein_asset_version( $css ) );
	wp_enqueue_script( 'estatein-admin-rows', ESTATEIN_URI . $js, array(), estatein_asset_version( $js ), true );

	wp_localize_script( 'estatein-admin-rows', 'estateinRows', array(
		'fields' => estatein_row_fields_all(),
		'i18n'   => array(
			'remove' => __( 'Remove row', 'estatein' ),
			'up'     => __( 'Move up', 'estatein' ),
			'down'   => __( 'Move down', 'estatein' ),
			'empty'  => __( 'Nothing yet.', 'estatein' ),
		),
	) );
}
add_action( 'admin_enqueue_scripts', 'estatein_admin_assets' );
