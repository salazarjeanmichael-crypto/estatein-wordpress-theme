<?php
/**
 * Template Name: About Us
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$stats = estatein_field( 'about_stats', array(
	array( 'value' => '200+', 'label' => __( 'Happy Customers', 'estatein' ) ),
	array( 'value' => '10k+', 'label' => __( 'Properties For Clients', 'estatein' ) ),
	array( 'value' => '16+',  'label' => __( 'Years of Experience', 'estatein' ) ),
) );

$values = array(
	array( 'icon' => 'value-trust',      'title' => __( 'Trust', 'estatein' ),          'text' => __( 'Trust is the cornerstone of every successful real estate transaction.', 'estatein' ) ),
	array( 'icon' => 'value-excellence', 'title' => __( 'Excellence', 'estatein' ),     'text' => __( 'We set the bar high for ourselves. From the properties we list to the services we provide.', 'estatein' ) ),
	array( 'icon' => 'value-client',     'title' => __( 'Client-Centric', 'estatein' ), 'text' => __( 'Your dreams and needs are at the center of our universe. We listen, we understand.', 'estatein' ) ),
	array( 'icon' => 'value-trust',      'title' => __( 'Our Commitment', 'estatein' ), 'text' => __( 'We are dedicated to providing you with the highest level of service, professionalism and support.', 'estatein' ) ),
);

$achievements = array(
	array( 'title' => __( '3+ Years of Excellence', 'estatein' ),  'text' => __( 'With over 3 years in the industry, we have amassed a wealth of knowledge and experience, becoming a go-to resource for all things real estate.', 'estatein' ) ),
	array( 'title' => __( 'Happy Clients', 'estatein' ),           'text' => __( 'Our greatest achievement is the satisfaction of our clients. Their success stories fuel our passion for what we do.', 'estatein' ) ),
	array( 'title' => __( 'Industry Recognition', 'estatein' ),    'text' => __( 'We have earned the respect of our peers and industry leaders, with accolades and awards that reflect our commitment to excellence.', 'estatein' ) ),
);

$steps = array(
	array( 'title' => __( 'Discover a World of Possibilities', 'estatein' ), 'text' => __( 'Your journey begins with exploring our carefully curated property listings. Use our intuitive search tools to filter properties based on your preferences, including location, type, size and budget.', 'estatein' ) ),
	array( 'title' => __( 'Narrowing Down Your Choices', 'estatein' ),       'text' => __( 'Once you have found properties that catch your eye, save them to your account or make a shortlist. This allows you to compare and revisit your favourites as you make your decision.', 'estatein' ) ),
	array( 'title' => __( 'Personalized Guidance', 'estatein' ),             'text' => __( 'Have questions about a property or need more information? Our dedicated team of real estate experts is just a call or message away.', 'estatein' ) ),
	array( 'title' => __( 'See It for Yourself', 'estatein' ),               'text' => __( 'Arrange viewings of the properties you are interested in. We coordinate with the property owners and accompany you to ensure you get a firsthand look at your potential new home.', 'estatein' ) ),
	array( 'title' => __( 'Making Informed Decisions', 'estatein' ),         'text' => __( 'Before making an offer, our team will assist you with due diligence, including property inspections, legal checks and market analysis.', 'estatein' ) ),
	array( 'title' => __( 'Getting the Best Deal', 'estatein' ),             'text' => __( 'We will help you negotiate the best terms and prepare your offer. Our goal is to secure the property at the right price and on favourable terms.', 'estatein' ) ),
);

$clients = array(
	array(
		'since'    => '2019',
		'name'     => __( 'ABC Corporation', 'estatein' ),
		'domain'   => __( 'Commercial Real Estate', 'estatein' ),
		'category' => __( 'Luxury Home Development', 'estatein' ),
		'quote'    => __( 'Estatein expertise in finding the perfect office space for our expanding operations was invaluable. They truly understand our business needs.', 'estatein' ),
	),
	array(
		'since'    => '2018',
		'name'     => __( 'GreenTech Enterprises', 'estatein' ),
		'domain'   => __( 'Commercial Real Estate', 'estatein' ),
		'category' => __( 'Retail Space', 'estatein' ),
		'quote'    => __( 'Estatein ability to identify prime retail locations helped us expand our brand presence. They are a trusted partner in our growth.', 'estatein' ),
	),
);

$team = new WP_Query( array(
	'post_type'      => 'team',
	'posts_per_page' => 8,
	'post_status'    => 'publish',
	'orderby'        => 'menu_order date',
	'order'          => 'ASC',
) );
?>

<section class="section" id="our-story" aria-labelledby="journey-title">
	<div class="container journey">
		<div class="journey__text">
			<?php
			// First heading on the page, so it carries the h1 rather than the
			// default h2 — the design has no separate page title above it.
			estatein_section_head( array(
				'tag'   => 'h1',
				'title' => __( 'Our Journey', 'estatein' ),
				'text'  => __( 'Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary. Over the years, we have expanded our reach, forged valuable partnerships, and gained the trust of countless clients.', 'estatein' ),
			) );
			?>
			<ul class="hero__stats">
				<?php foreach ( $stats as $stat ) : ?>
					<li class="stat">
						<span class="stat__value"><?php echo esc_html( $stat['value'] ); ?></span>
						<span class="stat__label"><?php echo esc_html( $stat['label'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="journey__media">
			<img src="<?php echo esc_url( ESTATEIN_URI . '/assets/img/journey.jpg' ); ?>"
			     width="755" height="546"
			     alt="<?php esc_attr_e( 'A model house held in an open hand', 'estatein' ); ?>"
			     loading="lazy" decoding="async">
		</div>
	</div>
</section>

<section class="section" id="our-values" aria-labelledby="values-title">
	<div class="container values">
		<div class="values__text">
			<?php
			estatein_section_head( array(
				'title' => __( 'Our Values', 'estatein' ),
				'text'  => __( 'Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary.', 'estatein' ),
			) );
			?>
		</div>

		<ul class="values__panel">
			<?php foreach ( $values as $value ) : ?>
				<li class="value">
					<div class="value__head">
						<span class="value__icon"><?php estatein_icon( $value['icon'], 34 ); ?></span>
						<h3><?php echo esc_html( $value['title'] ); ?></h3>
					</div>
					<p><?php echo esc_html( $value['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section" id="our-works" aria-labelledby="achievements-title">
	<div class="container">
		<?php
		estatein_section_head( array(
			'title' => __( 'Our Achievements', 'estatein' ),
			'text'  => __( 'Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary.', 'estatein' ),
		) );
		?>
		<div class="achievements">
			<?php foreach ( $achievements as $item ) : ?>
				<article class="value-card">
					<h3><?php echo esc_html( $item['title'] ); ?></h3>
					<p><?php echo esc_html( $item['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section" id="how-it-works" aria-labelledby="steps-title">
	<div class="container">
		<?php
		estatein_section_head( array(
			'title' => __( 'Navigating the Estatein Experience', 'estatein' ),
			'text'  => __( 'At Estatein, we have designed a straightforward process to help you find and purchase your dream property with ease. Here is a step-by-step guide to how it all works.', 'estatein' ),
		) );
		?>
		<ol class="step-grid">
			<?php foreach ( $steps as $index => $step ) : ?>
				<li class="step">
					<p class="step__number">
						<?php
						printf(
							/* translators: %s: zero-padded step number */
							esc_html__( 'Step %s', 'estatein' ),
							esc_html( str_pad( $index + 1, 2, '0', STR_PAD_LEFT ) )
						);
						?>
					</p>
					<div class="step__body">
						<h3><?php echo esc_html( $step['title'] ); ?></h3>
						<p><?php echo esc_html( $step['text'] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<?php if ( $team->have_posts() ) : ?>
	<section class="section" id="our-team" aria-labelledby="team-title">
		<div class="container">
			<?php
			estatein_section_head( array(
				'title' => __( 'Meet the Estatein Team', 'estatein' ),
				'text'  => __( 'At Estatein, our success is driven by the dedication and expertise of our team. Get to know the people behind our mission to make your real estate dreams a reality.', 'estatein' ),
			) );
			?>
			<ul class="team-grid">
				<?php
				while ( $team->have_posts() ) :
					$team->the_post();
					$role    = estatein_meta( 'role' );
					$twitter = estatein_meta( 'twitter' );
					$email   = estatein_meta( 'email' );
					?>
					<li class="team-card">
						<div class="team-card__media">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'estatein-card', array( 'alt' => get_the_title(), 'loading' => 'lazy' ) );
							}
							?>
							<?php if ( $twitter ) : ?>
								<a class="team-card__social" href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener noreferrer">
									<span class="screen-reader-text">
										<?php
										printf(
											/* translators: %s: team member name */
											esc_html__( '%s on Twitter', 'estatein' ),
											esc_html( get_the_title() )
										);
										?>
									</span>
									<?php estatein_icon( 'team-twitter', 24 ); ?>
								</a>
							<?php endif; ?>
						</div>

						<div class="team-card__body">
							<div class="team-card__text">
								<h3 class="team-card__name"><?php the_title(); ?></h3>
								<?php if ( $role ) : ?>
									<p class="team-card__role"><?php echo esc_html( $role ); ?></p>
								<?php endif; ?>
							</div>

							<a class="team-card__hello" href="<?php echo esc_url( $email ? 'mailto:' . $email : estatein_page_url( 'contact-us' ) . '#contact-form' ); ?>">
								<span><?php esc_html_e( 'Say Hello', 'estatein' ); ?> &#128075;</span>
								<span class="team-card__send"><?php estatein_icon( 'team-send', 24 ); ?></span>
							</a>
						</div>
					</li>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<section class="section" id="our-clients" aria-labelledby="clients-title">
	<div class="container">
		<?php
		estatein_section_head( array(
			'title' => __( 'Our Valued Clients', 'estatein' ),
			'text'  => __( 'At Estatein, we have had the privilege of working with a diverse range of clients across various industries. Here are some of the clients we have had the pleasure of serving.', 'estatein' ),
		) );
		?>
		<div class="client-grid">
			<?php foreach ( $clients as $client ) : ?>
				<article class="client">
					<div class="client__head">
						<div>
							<p class="client__since">
								<?php
								printf(
									/* translators: %s: year the relationship started */
									esc_html__( 'Since %s', 'estatein' ),
									esc_html( $client['since'] )
								);
								?>
							</p>
							<h3><?php echo esc_html( $client['name'] ); ?></h3>
						</div>
						<a class="btn btn--surface" href="<?php echo esc_url( estatein_page_url( 'contact-us' ) ); ?>">
							<?php esc_html_e( 'Visit Website', 'estatein' ); ?>
						</a>
					</div>

					<dl class="client__meta">
						<div>
							<dt><?php estatein_icon( 'client-domain', 24 ); ?><span><?php esc_html_e( 'Domain', 'estatein' ); ?></span></dt>
							<dd><?php echo esc_html( $client['domain'] ); ?></dd>
						</div>
						<div>
							<dt><?php estatein_icon( 'client-category', 24 ); ?><span><?php esc_html_e( 'Category', 'estatein' ); ?></span></dt>
							<dd><?php echo esc_html( $client['category'] ); ?></dd>
						</div>
					</dl>

					<blockquote class="client__quote">
						<p class="client__quote-label"><?php esc_html_e( 'What They Said', 'estatein' ); ?> &#129303;</p>
						<p><?php echo esc_html( $client['quote'] ); ?></p>
					</blockquote>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
get_template_part( 'template-parts/components/cta' );

get_footer();
