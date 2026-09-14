<?php
/**
 * Template Name: Services
 *
 * Three service blocks from the Figma design. Each block is data plus one
 * shared card partial, so adding a service is a matter of editing the array
 * rather than copying markup.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$properties_url = estatein_page_url( 'properties' );
$contact_url    = estatein_page_url( 'contact-us' ) . '#contact-form';

/**
 * The four value propositions, shared with the homepage feature strip.
 */
$features = array(
	array( 'icon' => 'feature-home',       'title' => __( 'Find Your Dream Home', 'estatein' ),                 'url' => $properties_url ),
	array( 'icon' => 'feature-value',      'title' => __( 'Unlock Property Value', 'estatein' ),                'url' => '#unlock-property-value' ),
	array( 'icon' => 'feature-management', 'title' => __( 'Effortless Property Management', 'estatein' ),       'url' => '#property-management' ),
	array( 'icon' => 'feature-invest',     'title' => __( 'Smart Investments, Informed Decisions', 'estatein' ), 'url' => '#smart-investments' ),
);

$selling = array(
	array( 'id' => 'valuation-mastery',    'icon' => 'svc-valuation',   'title' => __( 'Valuation Mastery', 'estatein' ),    'text' => __( 'Discover the true worth of your property with our expert valuation services.', 'estatein' ) ),
	array( 'id' => 'strategic-marketing',  'icon' => 'svc-marketing',   'title' => __( 'Strategic Marketing', 'estatein' ),  'text' => __( 'Selling a property requires more than just a listing; it demands a strategic marketing approach.', 'estatein' ) ),
	array( 'id' => 'negotiation-wizardry', 'icon' => 'svc-negotiation', 'title' => __( 'Negotiation Wizardry', 'estatein' ), 'text' => __( 'Negotiating the best deal is an art, and our negotiation experts are masters of it.', 'estatein' ) ),
	array( 'id' => 'closing-success',      'icon' => 'svc-closing',     'title' => __( 'Closing Success', 'estatein' ),      'text' => __( 'A successful sale is not complete until the closing. We guide you through the intricate closing process.', 'estatein' ) ),
);

$management = array(
	array( 'icon' => 'svc-tenant',      'title' => __( 'Tenant Harmony', 'estatein' ),          'text' => __( 'Our Tenant Management services ensure that your tenants have a smooth experience, reducing vacancies.', 'estatein' ) ),
	array( 'icon' => 'svc-maintenance', 'title' => __( 'Maintenance Ease', 'estatein' ),        'text' => __( 'Say goodbye to property maintenance headaches. We handle all aspects of property upkeep.', 'estatein' ) ),
	array( 'icon' => 'svc-finance',     'title' => __( 'Financial Peace of Mind', 'estatein' ), 'text' => __( 'Managing property finances can be complex. Our financial experts take care of rent collection.', 'estatein' ) ),
	array( 'icon' => 'svc-legal',       'title' => __( 'Legal Guardian', 'estatein' ),          'text' => __( 'Stay compliant with property laws and regulations effortlessly.', 'estatein' ) ),
);

$investment = array(
	array( 'icon' => 'svc-market',    'title' => __( 'Market Insight', 'estatein' ),          'text' => __( 'Stay ahead of market trends with our expert Market Analysis. We provide in-depth insights into real estate market conditions.', 'estatein' ) ),
	array( 'icon' => 'svc-roi',       'title' => __( 'ROI Assessment', 'estatein' ),          'text' => __( 'Make investment decisions with confidence. Our ROI Assessment services evaluate the potential returns on your investments.', 'estatein' ) ),
	array( 'icon' => 'svc-strategy',  'title' => __( 'Customized Strategies', 'estatein' ),   'text' => __( 'Every investor is unique, and so are their goals. We develop Customized Investment Strategies tailored to your specific needs.', 'estatein' ) ),
	array( 'icon' => 'svc-diversify', 'title' => __( 'Diversification Mastery', 'estatein' ), 'text' => __( 'Diversify your real estate portfolio effectively. Our experts guide you in spreading your investments across property types and locations.', 'estatein' ) ),
);

/**
 * Render one service block: three cards, then a fourth beside a promo panel.
 *
 * @param array  $cards Four service card definitions.
 * @param array  $promo Promo panel: title, text, label, url.
 */
$render_block = function ( array $cards, array $promo ) {
	$lead = array_slice( $cards, 0, 3 );
	$last = isset( $cards[3] ) ? $cards[3] : null;
	?>
	<div class="service-rows">
		<div class="service-grid service-grid--three">
			<?php
			foreach ( $lead as $card ) {
				get_template_part( 'template-parts/components/service-card', null, $card );
			}
			?>
		</div>

		<div class="service-split">
			<?php
			if ( $last ) {
				get_template_part( 'template-parts/components/service-card', null, $last );
			}
			?>
			<aside class="promo">
				<div class="promo__head">
					<h3><?php echo esc_html( $promo['title'] ); ?></h3>
					<a class="btn" href="<?php echo esc_url( $promo['url'] ); ?>">
						<?php echo esc_html( $promo['label'] ); ?>
					</a>
				</div>
				<p><?php echo esc_html( $promo['text'] ); ?></p>
			</aside>
		</div>
	</div>
	<?php
};
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<h1><?php esc_html_e( 'Elevate Your Real Estate Experience', 'estatein' ); ?></h1>
		<p><?php esc_html_e( 'Welcome to Estatein, where your real estate aspirations meet expert guidance. Explore our comprehensive range of services, each designed to cater to your unique needs and dreams.', 'estatein' ); ?></p>
	</div>
</section>

<section class="features-section" aria-label="<?php esc_attr_e( 'Service categories', 'estatein' ); ?>">
	<div class="container">
		<ul class="features">
			<?php foreach ( $features as $feature ) : ?>
				<li class="feature">
					<a class="feature__link" href="<?php echo esc_url( $feature['url'] ); ?>">
						<span class="feature__icon">
							<span class="feature__icon-inner"><?php estatein_icon( $feature['icon'], 34 ); ?></span>
						</span>
						<span class="feature__title"><?php echo esc_html( $feature['title'] ); ?></span>
						<span class="feature__arrow"><?php estatein_icon( 'arrow-diagonal', 34 ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section" id="unlock-property-value" aria-labelledby="selling-title">
	<div class="container">
		<?php
		estatein_section_head( array(
			'title' => __( 'Unlock Property Value', 'estatein' ),
			'text'  => __( 'Selling your property should be a rewarding experience, and at Estatein, we make sure it is. Our Property Selling Service is designed to maximize the value of your property, ensuring you get the best deal possible.', 'estatein' ),
		) );

		$render_block( $selling, array(
			'title' => __( 'Unlock the Value of Your Property Today', 'estatein' ),
			'text'  => __( 'Ready to unlock the true value of your property? Explore our Property Selling Service categories and let us help you achieve the best deal possible for your valuable asset.', 'estatein' ),
			'label' => __( 'Learn More', 'estatein' ),
			'url'   => $contact_url,
		) );
		?>
	</div>
</section>

<section class="section" id="property-management" aria-labelledby="management-title">
	<div class="container">
		<?php
		estatein_section_head( array(
			'title' => __( 'Effortless Property Management', 'estatein' ),
			'text'  => __( 'Owning a property should be a pleasure, not a hassle. Estatein Property Management Service takes the stress out of property ownership, offering comprehensive solutions tailored to your needs.', 'estatein' ),
		) );

		$render_block( $management, array(
			'title' => __( 'Experience Effortless Property Management', 'estatein' ),
			'text'  => __( 'Ready to experience hassle-free property management? Explore our Property Management Service categories and let us handle the complexities while you enjoy the benefits of property ownership.', 'estatein' ),
			'label' => __( 'Learn More', 'estatein' ),
			'url'   => $contact_url,
		) );
		?>
	</div>
</section>

<section class="section" id="smart-investments" aria-labelledby="investment-title">
	<div class="container invest-layout">

		<div class="invest-layout__intro">
			<?php
			estatein_section_head( array(
				'title' => __( 'Smart Investments, Informed Decisions', 'estatein' ),
				'text'  => __( 'Building a real estate portfolio requires a strategic approach. Estatein Investment Advisory Service empowers you to make smart investments and informed decisions.', 'estatein' ),
			) );
			?>

			<aside class="promo promo--stacked">
				<h3><?php esc_html_e( 'Unlock Your Investment Potential', 'estatein' ); ?></h3>
				<p><?php esc_html_e( 'Explore our Investment Advisory categories and let us handle the complexities while you enjoy the benefits of a growing portfolio.', 'estatein' ); ?></p>
				<a class="btn btn--block" href="<?php echo esc_url( $contact_url ); ?>">
					<?php esc_html_e( 'Learn More', 'estatein' ); ?>
				</a>
			</aside>
		</div>

		<div class="invest-layout__cards">
			<?php
			foreach ( $investment as $card ) {
				get_template_part( 'template-parts/components/service-card', null, $card );
			}
			?>
		</div>

	</div>
</section>

<?php
get_template_part( 'template-parts/components/cta' );

get_footer();
