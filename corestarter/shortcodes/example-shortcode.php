<?php
/**
 * Example Custom Shortcode
 *
 * This file demonstrates how to add custom shortcodes to the Corestarter theme.
 * Place .php shortcode files in your child theme's shortcodes/ directory and they
 * will be loaded automatically alongside the built-in shortcodes.
 *
 * USAGE IN CHILD THEME:
 *   1. Copy this file to: corestarter-child/shortcodes/my-shortcode.php
 *   2. Rename the function and shortcode tag to avoid conflicts.
 *   3. The shortcode will be available site-wide automatically.
 *
 * @package Corestarter
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * [corestarter_badge] — Outputs a styled inline badge/label.
 *
 * Usage: [corestarter_badge text="New" color="primary"]
 *        [corestarter_badge text="Sale" color="#dc2626"]
 *
 * Parameters:
 *   text  — Badge label text (default: 'Badge')
 *   color — 'primary', 'secondary', or any hex color (default: 'primary')
 *
 * @param array $atts Shortcode attributes.
 * @return string Badge HTML.
 */
function corestarter_badge_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'text'  => esc_html__( 'Badge', 'corestarter' ),
			'color' => 'primary',
		),
		$atts,
		'corestarter_badge'
	);

	$text = esc_html( $atts['text'] );

	// Resolve named colors to CSS custom properties, or use the literal value.
	$color_map = array(
		'primary'   => 'var(--cs-primary)',
		'secondary' => 'var(--cs-secondary)',
	);

	$bg_color = isset( $color_map[ $atts['color'] ] )
		? $color_map[ $atts['color'] ]
		: sanitize_hex_color( $atts['color'] );

	if ( ! $bg_color ) {
		$bg_color = 'var(--cs-primary)';
	}

	return sprintf(
		'<span class="cs-badge" style="background:%s; color:#fff; padding:0.2em 0.6em; border-radius:4px; font-size:0.75em; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; display:inline-block;">%s</span>',
		esc_attr( $bg_color ),
		$text
	);
}
add_shortcode( 'corestarter_badge', 'corestarter_badge_shortcode' );
