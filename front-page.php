<?php
/**
 * Homepage template.
 *
 * Each section is its own template part, so a section can be reordered,
 * reused on another page, or removed without touching the others.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/home/hero' );
get_template_part( 'template-parts/home/features' );
get_template_part( 'template-parts/home/properties' );
get_template_part( 'template-parts/home/testimonials' );
get_template_part( 'template-parts/home/faq' );
get_template_part( 'template-parts/components/cta' );

get_footer();
