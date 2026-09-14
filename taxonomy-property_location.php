<?php
/**
 * property_location archive.
 *
 * Delegates to archive-property.php so taxonomy listings share the search bar,
 * grid and pagination with the main properties archive.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_template_part( 'archive-property' );
