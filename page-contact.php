<?php
/**
 * Template Name: Contact
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$email   = estatein_field( 'contact_email', 'info@estatein.com', 'option' );
$phone   = estatein_field( 'contact_phone', '+1 (123) 456-7890', 'option' );
$address = estatein_field( 'contact_address', '123 Estatein Plaza, City Center, Metropolis', 'option' );

// Reuses the homepage feature-strip classes: the design treats these as the
// same object, so sharing styles stops the two pages drifting apart.
$methods = array(
	array(
		'icon'  => 'contact-mail',
		'label' => $email,
		'url'   => 'mailto:' . $email,
	),
	array(
		'icon'  => 'contact-phone',
		'label' => $phone,
		'url'   => 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ),
	),
	array(
		'icon'  => 'contact-pin',
		'label' => __( 'Main Headquarters', 'estatein' ),
		'url'   => '#our-offices',
	),
);

$offices = array(
	array(
		'kind'    => 'regional',
		'label'   => __( 'Main Headquarters', 'estatein' ),
		'address' => __( '123 Estatein Plaza, City Center, Metropolis', 'estatein' ),
		'text'    => __( 'Our main headquarters serve as the heart of Estatein. Located in the bustling city center, this is where our core team of experts operates, driving the excellence and innovation that define us.', 'estatein' ),
		'email'   => 'info@estatein.com',
		'phone'   => '+1 (123) 456-7890',
		'city'    => __( 'Metropolis', 'estatein' ),
	),
	array(
		'kind'    => 'regional',
		'label'   => __( 'Regional Offices', 'estatein' ),
		'address' => __( '456 Urban Avenue, Downtown District, Metropolis', 'estatein' ),
		'text'    => __( 'Estatein presence extends to multiple regions, each with its own dynamic real estate landscape. Discover our regional offices, staffed by local experts who understand the nuances of their respective markets.', 'estatein' ),
		'email'   => 'info@estatein.com',
		'phone'   => '+1 (123) 628-7890',
		'city'    => __( 'Metropolis', 'estatein' ),
	),
	array(
		'kind'    => 'international',
		'label'   => __( 'International Offices', 'estatein' ),
		'address' => __( '789 Global Street, Harbour Quarter, Port City', 'estatein' ),
		'text'    => __( 'Our international desk supports buyers and sellers across borders, coordinating viewings, paperwork and finance in the time zone that suits you.', 'estatein' ),
		'email'   => 'global@estatein.com',
		'phone'   => '+1 (123) 998-2210',
		'city'    => __( 'Port City', 'estatein' ),
	),
);
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<h1><?php esc_html_e( 'Get in Touch with Estatein', 'estatein' ); ?></h1>
		<p><?php esc_html_e( 'Welcome to Estatein Contact Us page. We are here to assist you with any inquiries, requests, or feedback you may have. Whether you are looking to buy or sell a property, explore investment opportunities, or simply want to connect, we are just a message away.', 'estatein' ); ?></p>
	</div>
</section>

<section class="features-section" aria-label="<?php esc_attr_e( 'Ways to reach us', 'estatein' ); ?>">
	<div class="features-wrap">
		<ul class="features">
			<?php foreach ( $methods as $method ) : ?>
				<li class="feature">
					<a class="feature__link" href="<?php echo esc_url( $method['url'] ); ?>">
						<span class="feature__icon">
							<span class="feature__icon-inner">
								<?php estatein_icon( $method['icon'], 34 ); ?>
							</span>
						</span>
						<span class="feature__title"><?php echo esc_html( $method['label'] ); ?></span>
						<span class="feature__arrow"><?php estatein_icon( 'arrow-diagonal', 34 ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>

			<?php
			/*
			 * Not an anchor like the three above: this card holds three
			 * separate destinations, and a link inside a link is invalid
			 * markup that browsers recover from unpredictably.
			 */
			?>
			<li class="feature">
				<div class="feature__link">
					<span class="feature__icon">
						<span class="feature__icon-inner">
							<?php estatein_icon( 'contact-share', 34 ); ?>
						</span>
					</span>
					<span class="feature__title feature__socials">
						<?php
						$social_links = array(
							__( 'Instagram', 'estatein' ) => estatein_field( 'social_instagram', 'https://instagram.com/', 'option' ),
							__( 'LinkedIn', 'estatein' )  => estatein_field( 'social_linkedin', 'https://linkedin.com/', 'option' ),
							__( 'Facebook', 'estatein' )  => estatein_field( 'social_facebook', 'https://facebook.com/', 'option' ),
						);

						foreach ( $social_links as $label => $url ) {
							printf(
								'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
								esc_url( $url ),
								esc_html( $label )
							);
						}
						?>
					</span>
				</div>
			</li>
		</ul>
	</div>
</section>

<section class="section" aria-labelledby="connect-title">
	<div class="container">
		<?php
		estatein_section_head( array(
			'title' => __( 'Let&#8217;s Connect', 'estatein' ),
			'text'  => __( 'We are excited to connect with you and learn more about your real estate goals. Use the form below to get in touch with Estatein.', 'estatein' ),
		) );
		?>
		<?php get_template_part( 'template-parts/components/contact-form' ); ?>
	</div>
</section>

<section class="section" id="our-offices" aria-labelledby="offices-title">
	<div class="container">
		<?php
		estatein_section_head( array(
			'title' => __( 'Discover Our Office Locations', 'estatein' ),
			'text'  => __( 'Estatein is here to serve you across multiple locations. Whether you are looking to meet our team, discuss real estate opportunities, or simply drop by for a chat, we have offices conveniently located to serve your needs.', 'estatein' ),
		) );
		?>

		<div class="tabs" data-filter-group>
			<button class="tab is-active" type="button" data-filter="all" aria-pressed="true"><?php esc_html_e( 'All', 'estatein' ); ?></button>
			<button class="tab" type="button" data-filter="regional" aria-pressed="false"><?php esc_html_e( 'Regional', 'estatein' ); ?></button>
			<button class="tab" type="button" data-filter="international" aria-pressed="false"><?php esc_html_e( 'International', 'estatein' ); ?></button>
		</div>

		<div class="office-grid" data-filter-items>
			<?php foreach ( $offices as $office ) : ?>
				<article class="office" data-kind="<?php echo esc_attr( $office['kind'] ); ?>">
					<p class="office__label"><?php echo esc_html( $office['label'] ); ?></p>
					<h3 class="office__address"><?php echo esc_html( $office['address'] ); ?></h3>
					<p class="office__text"><?php echo esc_html( $office['text'] ); ?></p>

					<ul class="property-tags">
						<li class="property-tag"><?php estatein_icon( 'contact-mail', 20 ); ?><span><?php echo esc_html( $office['email'] ); ?></span></li>
						<li class="property-tag"><?php estatein_icon( 'contact-phone', 20 ); ?><span><?php echo esc_html( $office['phone'] ); ?></span></li>
						<li class="property-tag"><?php estatein_icon( 'contact-pin', 20 ); ?><span><?php echo esc_html( $office['city'] ); ?></span></li>
					</ul>

					<a class="btn btn--primary btn--block"
					   href="<?php echo esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $office['address'] ) ); ?>"
					   target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'Get Direction', 'estatein' ); ?>
					</a>
				</article>
			<?php endforeach; ?>
		</div>

		<p class="filter-empty" data-filter-empty hidden>
			<?php esc_html_e( 'No offices in that category yet.', 'estatein' ); ?>
		</p>
	</div>
</section>

<?php
get_template_part( 'template-parts/components/cta' );

get_footer();
