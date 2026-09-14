<?php
/**
 * Template helpers.
 *
 * Templates say what to render; these know how. Content reads are ACF-aware
 * with hard-coded fallbacks, so the theme is correct with or without ACF.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return an ACF field value, or a fallback when ACF is absent or the field is empty.
 *
 * A theme that fatals without a plugin is a theme that cannot be handed to a
 * client, so every ACF read in this theme goes through here.
 *
 * @param string $selector Field name.
 * @param mixed  $fallback Value to use when ACF is unavailable or the field is empty.
 * @param mixed  $post_id  Optional post ID, or 'option' for the options page.
 * @return mixed
 */
function estatein_field( $selector, $fallback = '', $post_id = false ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $fallback;
	}

	$value = get_field( $selector, $post_id );

	return ( '' === $value || null === $value || false === $value || array() === $value )
		? $fallback
		: $value;
}

/**
 * Permalink for one of the theme's known pages, by slug.
 *
 * Falls back to an anchor on the home page so links never break if an editor
 * deletes or renames a page.
 *
 * @param string $slug Page slug.
 * @return string
 */
function estatein_page_url( $slug ) {
	static $cache = array();

	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}

	$page = get_page_by_path( $slug );
	$cache[ $slug ] = $page ? get_permalink( $page ) : home_url( '/' );

	return $cache[ $slug ];
}

/**
 * Echo an SVG icon from the theme's icon directory.
 *
 * Icons are exported straight from the Figma file, so the glyphs match the
 * design exactly rather than being re-drawn by hand.
 *
 * @param string $name  File name without extension.
 * @param int    $size  Rendered square size in px.
 * @param string $alt   Alt text. Empty string marks the icon decorative.
 * @param string $class Optional extra class.
 */
function estatein_icon( $name, $size = 24, $alt = '', $class = '' ) {
	$file = '/assets/img/icons/' . $name . '.svg';

	if ( ! file_exists( ESTATEIN_DIR . $file ) ) {
		return;
	}

	printf(
		'<img src="%1$s" width="%2$d" height="%2$d" alt="%3$s"%4$s%5$s decoding="async">',
		esc_url( ESTATEIN_URI . $file ),
		absint( $size ),
		esc_attr( $alt ),
		$class ? ' class="' . esc_attr( $class ) . '"' : '',
		'' === $alt ? ' aria-hidden="true"' : ''
	);
}

/**
 * Echo the Estatein wordmark, linked home.
 *
 * The mark is two SVGs (symbol + wordmark) exported from Figma, matching the
 * 160x48 logo lockup in the design.
 *
 * @param string $class Optional extra class on the anchor.
 */
function estatein_brand( $class = '' ) {
	$custom = get_custom_logo();

	// Respect a logo uploaded in the Customizer, if the client sets one.
	if ( $custom && has_custom_logo() ) {
		echo '<div class="site-brand ' . esc_attr( $class ) . '">' . $custom . '</div>';
		return;
	}
	?>
	<a class="site-brand <?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<img class="site-brand__symbol"
		     src="<?php echo esc_url( ESTATEIN_URI . '/assets/img/logo-symbol.svg' ); ?>"
		     width="48" height="48" alt="" aria-hidden="true" decoding="async">
		<img class="site-brand__text"
		     src="<?php echo esc_url( ESTATEIN_URI . '/assets/img/logo-text.svg' ); ?>"
		     width="102" height="21"
		     alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" decoding="async">
	</a>
	<?php
}

/**
 * Menu fallback: render the theme's pages when no menu is assigned.
 *
 * Without this a fresh install shows no navigation at all, which makes the
 * theme look broken before the client has configured anything.
 */
function estatein_nav_fallback() {
	$pages = array(
		'home'       => __( 'Home', 'estatein' ),
		'about-us'   => __( 'About Us', 'estatein' ),
		'properties' => __( 'Properties', 'estatein' ),
		'services'   => __( 'Services', 'estatein' ),
	);

	echo '<ul>';
	foreach ( $pages as $slug => $label ) {
		$url     = 'home' === $slug ? home_url( '/' ) : estatein_page_url( $slug );
		$current = ( 'home' === $slug && is_front_page() ) || ( 'home' !== $slug && is_page( $slug ) );

		printf(
			'<li class="%1$s"><a href="%2$s"%3$s>%4$s</a></li>',
			$current ? 'current-menu-item' : '',
			esc_url( $url ),
			$current ? ' aria-current="page"' : '',
			esc_html( $label )
		);
	}
	echo '</ul>';
}

/**
 * Echo the three-star flourish that sits above each section heading in Figma.
 */
function estatein_stars() {
	echo '<span class="section-head__stars" aria-hidden="true">';
	estatein_icon( 'star-lg', 30 );
	estatein_icon( 'star-md', 18 );
	estatein_icon( 'star-sm', 8 );
	echo '</span>';
}

/**
 * Render a section heading: flourish, title, blurb and an optional action.
 *
 * @param array $args {
 *     @type string $title      Heading text.
 *     @type string $text       Supporting paragraph.
 *     @type string $link_url   Optional action URL.
 *     @type string $link_label Optional action label.
 *     @type string $tag        Heading level, default h2.
 *     @type bool   $stars      Whether to show the flourish, default true.
 * }
 */
function estatein_section_head( $args = array() ) {
	$args = wp_parse_args( $args, array(
		'title'      => '',
		'text'       => '',
		'link_url'   => '',
		'link_label' => '',
		'tag'        => 'h2',
		'stars'      => true,
	) );

	$tag = in_array( $args['tag'], array( 'h1', 'h2', 'h3' ), true ) ? $args['tag'] : 'h2';
	?>
	<div class="section-head">
		<div class="section-head__text">
			<?php
			if ( $args['stars'] ) {
				estatein_stars();
			}
			if ( $args['title'] ) {
				printf( '<%1$s>%2$s</%1$s>', $tag, esc_html( $args['title'] ) );
			}
			if ( $args['text'] ) {
				printf( '<p>%s</p>', esc_html( $args['text'] ) );
			}
			?>
		</div>
		<?php if ( $args['link_url'] && $args['link_label'] ) : ?>
			<a class="btn btn--surface" href="<?php echo esc_url( $args['link_url'] ); ?>">
				<?php echo esc_html( $args['link_label'] ); ?>
			</a>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Echo a row of rating stars with an accessible text equivalent.
 *
 * @param int $rating Number of filled stars, 0-5.
 */
function estatein_rating( $rating = 5 ) {
	$rating = max( 0, min( 5, (int) $rating ) );
	?>
	<div class="testimonial__stars">
		<span class="screen-reader-text">
			<?php
			printf(
				/* translators: %d: star rating out of five */
				esc_html__( 'Rated %d out of 5', 'estatein' ),
				$rating
			);
			?>
		</span>
		<?php for ( $i = 0; $i < $rating; $i++ ) : ?>
			<svg viewBox="0 0 24 24" fill="#FFC107" aria-hidden="true" focusable="false">
				<path d="M12 2.5l2.9 5.88 6.49.95-4.7 4.58 1.11 6.46L12 17.32l-5.8 3.05 1.1-6.46-4.69-4.58 6.49-.95L12 2.5z"/>
			</svg>
		<?php endfor; ?>
	</div>
	<?php
}

/**
 * Format a price for display.
 *
 * Stored as a plain number so it stays sortable and filterable; formatting is
 * a presentation concern and belongs here rather than in the database.
 *
 * @param mixed  $value  Raw price.
 * @param string $prefix Currency symbol.
 * @return string
 */
function estatein_price( $value, $prefix = '$' ) {
	if ( '' === $value || null === $value ) {
		return '';
	}

	if ( is_numeric( $value ) ) {
		return $prefix . number_format( (float) $value, 0, '.', ',' );
	}

	return (string) $value;
}

/**
 * Truncate text to a word count without breaking mid-word.
 *
 * @param string $text  Source text.
 * @param int    $words Word limit.
 * @return string
 */
function estatein_trim( $text, $words = 14 ) {
	return wp_trim_words( wp_strip_all_tags( $text ), $words, '...' );
}

/**
 * Build a value => label list from a taxonomy, for a <select> control.
 *
 * @param string $taxonomy Taxonomy name.
 * @return array
 */
function estatein_term_options( $taxonomy ) {
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );

	if ( is_wp_error( $terms ) ) {
		return array();
	}

	$options = array();

	foreach ( $terms as $term ) {
		$options[ $term->name ] = $term->name;
	}

	return $options;
}
