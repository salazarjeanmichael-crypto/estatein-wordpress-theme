<?php
/**
 * Estatein theme setup.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

define( 'ESTATEIN_VERSION', '1.0.0' );
define( 'ESTATEIN_DIR', get_template_directory() );
define( 'ESTATEIN_URI', get_template_directory_uri() );

require_once ESTATEIN_DIR . '/inc/helpers.php';
require_once ESTATEIN_DIR . '/inc/cpt.php';
require_once ESTATEIN_DIR . '/inc/meta.php';
require_once ESTATEIN_DIR . '/inc/query.php';
require_once ESTATEIN_DIR . '/inc/settings.php';
require_once ESTATEIN_DIR . '/inc/admin-ui.php';
require_once ESTATEIN_DIR . '/inc/acf-fields.php';
require_once ESTATEIN_DIR . '/inc/forms.php';
require_once ESTATEIN_DIR . '/inc/form-fields.php';
require_once ESTATEIN_DIR . '/inc/seo.php';


/* -------------------------------------------------------------------------
 * Theme setup
 * ---------------------------------------------------------------------- */

/**
 * Register theme supports and navigation menus.
 */
function estatein_setup() {
	load_theme_textdomain( 'estatein', ESTATEIN_DIR . '/languages' );

	add_theme_support( 'title-tag' );          // Lets WordPress manage <title>, which SEO needs.
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo', array(
		'height'      => 48,
		'width'       => 160,
		'flex-width'  => true,
		'flex-height' => true,
	) );

	// Card image ratio taken from the Figma property card (512 x 318).
	add_image_size( 'estatein-card', 640, 400, true );
	add_image_size( 'estatein-wide', 1280, 720, true );

	register_nav_menus( array(
		'primary'           => __( 'Primary Menu', 'estatein' ),
		'footer-home'       => __( 'Footer: Home', 'estatein' ),
		'footer-about'      => __( 'Footer: About Us', 'estatein' ),
		'footer-properties' => __( 'Footer: Properties', 'estatein' ),
		'footer-services'   => __( 'Footer: Services', 'estatein' ),
		'footer-contact'    => __( 'Footer: Contact Us', 'estatein' ),
	) );
}
add_action( 'after_setup_theme', 'estatein_setup' );


/* -------------------------------------------------------------------------
 * Assets
 * ---------------------------------------------------------------------- */

/**
 * Enqueue front-end styles and scripts.
 *
 * Versions use filemtime() so a deploy busts the cache without a manual bump.
 */
function estatein_assets() {
	// Urbanist is the typeface specified in the Figma type guide.
	wp_enqueue_style(
		'estatein-fonts',
		'https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'estatein-style', get_stylesheet_uri(), array(), ESTATEIN_VERSION );

	$css = estatein_asset_path( '/assets/css/main', 'css' );
	$js  = estatein_asset_path( '/assets/js/main', 'js' );

	wp_enqueue_style( 'estatein-main', ESTATEIN_URI . $css, array( 'estatein-style' ), estatein_asset_version( $css ) );
	wp_enqueue_script( 'estatein-main', ESTATEIN_URI . $js, array(), estatein_asset_version( $js ), true );
}
add_action( 'wp_enqueue_scripts', 'estatein_assets' );

/**
 * Pick the minified asset, falling back to the source.
 *
 * SCRIPT_DEBUG serves the readable file so the theme stays debuggable, and the
 * fallback means a missing .min never takes the site down with it.
 *
 * @param string $base Path without extension, relative to the theme.
 * @param string $ext  File extension.
 * @return string
 */
function estatein_asset_path( $base, $ext ) {
	$min = $base . '.min.' . $ext;

	if ( ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) && file_exists( ESTATEIN_DIR . $min ) ) {
		return $min;
	}

	return $base . '.' . $ext;
}

/**
 * File modification time as a cache-busting version string.
 *
 * @param string $relative Path relative to the theme root.
 * @return string
 */
function estatein_asset_version( $relative ) {
	$path = ESTATEIN_DIR . $relative;

	return file_exists( $path ) ? (string) filemtime( $path ) : ESTATEIN_VERSION;
}

/**
 * Mark the document as scripted before anything paints.
 *
 * The reveal animations hide their targets in CSS, so that rule has to be
 * live on the first paint or the content flashes in and back out.
 */
function estatein_html_class_script() {
	?>
	<script>
	(function (h) {
		h.className += ' js';

		if (!('IntersectionObserver' in window) ||
			(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
			h.className += ' no-reveal';
			return;
		}

		// If main.js never loads, the CSS above would leave the page blank.
		// Unhide everything unless that file has claimed the work by then.
		setTimeout(function () {
			if (!h.getAttribute('data-reveal-ready')) {
				h.className += ' no-reveal';
			}
		}, 2500);
	})(document.documentElement);
	</script>
	<?php
}
add_action( 'wp_head', 'estatein_html_class_script', 1 );

/**
 * Preconnect to the Google Fonts file origin so the font request starts early.
 *
 * @param array  $hints    Current hints.
 * @param string $relation Hint type.
 * @return array
 */
function estatein_resource_hints( $hints, $relation ) {
	if ( 'preconnect' === $relation ) {
		$hints[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}

	return $hints;
}
add_filter( 'wp_resource_hints', 'estatein_resource_hints', 10, 2 );

/**
 * Load the main script without blocking rendering.
 *
 * @param string $tag    Script tag.
 * @param string $handle Script handle.
 * @return string
 */
function estatein_defer_scripts( $tag, $handle ) {
	if ( 'estatein-main' === $handle ) {
		return str_replace( ' src=', ' defer src=', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'estatein_defer_scripts', 10, 2 );


/* -------------------------------------------------------------------------
 * Performance
 * ---------------------------------------------------------------------- */

/**
 * Drop front-end weight WordPress ships by default but this theme never uses.
 */
function estatein_trim_head() {
	// Emoji detection script and its inline styles: ~15KB for no benefit here.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );

	// The block library CSS is unused: this is a classic, hand-written theme.
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );

	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}
add_action( 'wp_enqueue_scripts', 'estatein_trim_head', 100 );

/**
 * Let the browser decode images off the main thread.
 *
 * WordPress already adds loading="lazy"; pairing it with decoding="async"
 * keeps a long property grid from janking the scroll.
 *
 * @param array $attr Attachment image attributes.
 * @return array
 */
function estatein_image_attributes( $attr ) {
	if ( empty( $attr['decoding'] ) ) {
		$attr['decoding'] = 'async';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'estatein_image_attributes' );


/* -------------------------------------------------------------------------
 * Content tweaks
 * ---------------------------------------------------------------------- */

/**
 * Shorter excerpts, sized for the property card in the design.
 *
 * @return int
 */
function estatein_excerpt_length() {
	return 18;
}
add_filter( 'excerpt_length', 'estatein_excerpt_length' );

/**
 * Replace the default excerpt ellipsis.
 *
 * @return string
 */
function estatein_excerpt_more() {
	return '...';
}
add_filter( 'excerpt_more', 'estatein_excerpt_more' );

/**
 * Add helpful classes to <body>.
 *
 * @param array $classes Existing classes.
 * @return array
 */
function estatein_body_class( $classes ) {
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'estatein_body_class' );


/* -------------------------------------------------------------------------
 * Widgets
 * ---------------------------------------------------------------------- */

/**
 * Register the blog sidebar.
 */
function estatein_widgets() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'estatein' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Shown beside blog posts and archives.', 'estatein' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'estatein_widgets' );
