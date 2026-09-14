<?php
/**
 * Site footer: brand, newsletter, link columns, legal bar and socials.
 *
 * Each column is a real menu location so the client can edit it; the fallback
 * arrays reproduce the design, so the footer is never empty on a fresh install.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$footer_columns = array(
	'footer-home' => array(
		'label' => __( 'Home', 'estatein' ),
		'links' => array(
			__( 'Hero Section', 'estatein' )  => home_url( '/#main' ),
			__( 'Features', 'estatein' )      => home_url( '/#features' ),
			__( 'Properties', 'estatein' )    => home_url( '/#properties' ),
			__( 'Testimonials', 'estatein' )  => home_url( '/#testimonials' ),
			__( 'FAQ&#8217;s', 'estatein' )   => home_url( '/#faq' ),
		),
	),
	'footer-about' => array(
		'label' => __( 'About Us', 'estatein' ),
		'links' => array(
			__( 'Our Story', 'estatein' )    => estatein_page_url( 'about-us' ) . '#our-story',
			__( 'Our Works', 'estatein' )    => estatein_page_url( 'about-us' ) . '#our-works',
			__( 'How It Works', 'estatein' ) => estatein_page_url( 'about-us' ) . '#how-it-works',
			__( 'Our Team', 'estatein' )     => estatein_page_url( 'about-us' ) . '#our-team',
			__( 'Our Clients', 'estatein' )  => estatein_page_url( 'about-us' ) . '#our-clients',
		),
	),
	'footer-properties' => array(
		'label' => __( 'Properties', 'estatein' ),
		'links' => array(
			__( 'Portfolio', 'estatein' )  => estatein_page_url( 'properties' ),
			__( 'Categories', 'estatein' ) => estatein_page_url( 'properties' ) . '#categories',
		),
	),
	'footer-services' => array(
		'label' => __( 'Services', 'estatein' ),
		'links' => array(
			__( 'Valuation Mastery', 'estatein' )   => estatein_page_url( 'services' ) . '#valuation-mastery',
			__( 'Strategic Marketing', 'estatein' ) => estatein_page_url( 'services' ) . '#strategic-marketing',
			__( 'Negotiation Wizardry', 'estatein' ) => estatein_page_url( 'services' ) . '#negotiation-wizardry',
			__( 'Closing Success', 'estatein' )     => estatein_page_url( 'services' ) . '#closing-success',
			__( 'Property Management', 'estatein' ) => estatein_page_url( 'services' ) . '#property-management',
		),
	),
	'footer-contact' => array(
		'label' => __( 'Contact Us', 'estatein' ),
		'links' => array(
			__( 'Contact Form', 'estatein' ) => estatein_page_url( 'contact-us' ) . '#contact-form',
			__( 'Our Offices', 'estatein' )  => estatein_page_url( 'contact-us' ) . '#our-offices',
		),
	),
);

$socials = array(
	'facebook' => array( 'label' => 'Facebook', 'url' => estatein_field( 'social_facebook', 'https://facebook.com/', 'option' ) ),
	'linkedin' => array( 'label' => 'LinkedIn', 'url' => estatein_field( 'social_linkedin', 'https://linkedin.com/', 'option' ) ),
	'twitter'  => array( 'label' => 'Twitter',  'url' => estatein_field( 'social_twitter',  'https://twitter.com/',  'option' ) ),
	'youtube'  => array( 'label' => 'YouTube',  'url' => estatein_field( 'social_youtube',  'https://youtube.com/',  'option' ) ),
);
?>
</main><!-- #main -->

<footer class="site-footer">
	<div class="container site-footer__top">

		<div class="site-footer__brand">
			<?php estatein_brand(); ?>

			<?php
			// CF7 owns the newsletter when installed; the markup below is the
			// fallback so the footer still collects addresses without it.
			if ( ! estatein_render_cf7( 'estatein_form_newsletter' ) ) :
				?>
			<form class="footer-subscribe" method="post" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php estatein_icon( 'mail', 24, '', 'footer-subscribe__icon' ); ?>
				<label class="screen-reader-text" for="subscribe-email">
					<?php esc_html_e( 'Email address', 'estatein' ); ?>
				</label>
				<input type="email" id="subscribe-email" name="estatein_subscribe_email"
				       placeholder="<?php esc_attr_e( 'Enter Your Email', 'estatein' ); ?>"
				       autocomplete="email" required>
				<?php wp_nonce_field( 'estatein_subscribe', 'estatein_subscribe_nonce' ); ?>
				<input type="hidden" name="estatein_return" value="<?php echo esc_url( home_url( add_query_arg( array() ) ) ); ?>">
				<button type="submit">
					<span class="screen-reader-text"><?php esc_html_e( 'Subscribe', 'estatein' ); ?></span>
					<?php estatein_icon( 'send', 30 ); ?>
				</button>
			</form>

			<?php if ( ! empty( $_GET['subscribed'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
				<p class="form-note form-note--ok" role="status">
					<?php esc_html_e( 'Thanks — you are on the list.', 'estatein' ); ?>
				</p>
			<?php endif; ?>
			<?php endif; ?>
		</div>

		<div class="footer-cols">
			<?php foreach ( $footer_columns as $location => $column ) : ?>
				<nav class="footer-col" aria-labelledby="fcol-<?php echo esc_attr( $location ); ?>">
					<h2 id="fcol-<?php echo esc_attr( $location ); ?>"><?php echo esc_html( $column['label'] ); ?></h2>
					<?php
					if ( has_nav_menu( $location ) ) {
						wp_nav_menu( array(
							'theme_location' => $location,
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => false,
						) );
					} else {
						echo '<ul>';
						foreach ( $column['links'] as $label => $url ) {
							printf(
								'<li><a href="%s">%s</a></li>',
								esc_url( $url ),
								esc_html( $label )
							);
						}
						echo '</ul>';
					}
					?>
				</nav>
			<?php endforeach; ?>
		</div>

	</div>

	<div class="site-footer__bottom-wrap">
		<div class="container site-footer__bottom">
			<p class="site-footer__legal">
				<span>
					<?php
					printf(
						/* translators: 1: year, 2: site name */
						esc_html__( '@%1$s %2$s. All Rights Reserved.', 'estatein' ),
						esc_html( wp_date( 'Y' ) ),
						esc_html( get_bloginfo( 'name' ) )
					);
					?>
				</span>
				<a href="<?php echo esc_url( home_url( '/terms-conditions/' ) ); ?>">
					<?php esc_html_e( 'Terms &amp; Conditions', 'estatein' ); ?>
				</a>
			</p>

			<ul class="social">
				<?php foreach ( $socials as $key => $social ) : ?>
					<?php if ( ! $social['url'] ) { continue; } ?>
					<li>
						<a href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer">
							<span class="screen-reader-text">
								<?php
								printf(
									/* translators: %s: social network name */
									esc_html__( 'Estatein on %s', 'estatein' ),
									esc_html( $social['label'] )
								);
								?>
							</span>
							<?php estatein_icon( $key, 24 ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
