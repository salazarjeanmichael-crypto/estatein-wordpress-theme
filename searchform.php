<?php
/**
 * Search form.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_search_id = 'search-' . wp_rand( 1000, 9999 );
?>
<form class="property-search__bar" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $estatein_search_id ); ?>">
		<?php esc_html_e( 'Search this site', 'estatein' ); ?>
	</label>
	<input type="search" id="<?php echo esc_attr( $estatein_search_id ); ?>" name="s"
	       value="<?php echo esc_attr( get_search_query() ); ?>"
	       placeholder="<?php esc_attr_e( 'Search', 'estatein' ); ?>">
	<button class="btn btn--primary" type="submit"><?php esc_html_e( 'Search', 'estatein' ); ?></button>
</form>
