<?php
/**
 * Site header: announcement banner, brand, primary navigation.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$banner_text  = estatein_field( 'banner_text', __( 'Discover Your Dream Property with Estatein', 'estatein' ), 'option' );
$banner_label = estatein_field( 'banner_link_label', __( 'Learn More', 'estatein' ), 'option' );
$banner_url   = estatein_field( 'banner_link_url', estatein_page_url( 'properties' ), 'option' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'no-js' ); ?>>
<?php wp_body_open(); ?>
<script>document.body.classList.remove('no-js');</script>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'estatein' ); ?></a>

<?php if ( $banner_text ) : ?>
	<div class="banner" id="announcement">
		<p class="banner__text">
			<?php /* Figma sets this as the sparkle emoji in the copy, not an icon. */ ?>
			<span><?php echo '&#x2728;' . esc_html( $banner_text ); ?></span>
			<?php if ( $banner_url && $banner_label ) : ?>
				<a class="banner__link" href="<?php echo esc_url( $banner_url ); ?>"><?php echo esc_html( $banner_label ); ?></a>
			<?php endif; ?>
		</p>
		<button class="banner__close" type="button" data-dismiss="#announcement">
			<span class="screen-reader-text"><?php esc_html_e( 'Dismiss announcement', 'estatein' ); ?></span>
			<?php estatein_icon( 'banner-close', 24 ); ?>
		</button>
	</div>
<?php endif; ?>

<header class="site-header">
	<div class="container site-header__inner">

		<?php estatein_brand(); ?>

		<button class="nav-toggle" type="button"
		        aria-expanded="false"
		        aria-controls="site-nav">
			<span class="screen-reader-text"><?php esc_html_e( 'Toggle navigation', 'estatein' ); ?></span>
			<span class="nav-toggle__bar" aria-hidden="true"></span>
		</button>

		<div class="site-header__nav" id="site-nav">
			<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'estatein' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => 'estatein_nav_fallback',
				) );
				?>
			</nav>
			<div class="site-header__cta">
				<a class="btn" href="<?php echo esc_url( estatein_page_url( 'contact-us' ) ); ?>">
					<?php esc_html_e( 'Contact Us', 'estatein' ); ?>
				</a>
			</div>
		</div>

	</div>
</header>

<main id="main">
