<?php
/**
 * Theme Options Defaults and Helper Functions.
 *
 * Provides the single source of truth for all default option values,
 * a cached helper for retrieving options, and font lists.
 *
 * @package Corestarter
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get the default values for all theme options.
 *
 * @return array Complete defaults array.
 */
function corestarter_get_defaults() {
	$typography_defaults = array(
		'family' => '',
		'size'   => '',
		'weight' => '',
	);

	return array(
		// General.
		'logo_id'              => 0,
		'logo_url'             => '',
		'hide_site_title'      => false,
		'hide_site_description' => false,

		// Header.
		'sticky_header'        => true,
		'header_bg_color'      => '#ffffff',

		// Layout.
		'container_type'       => 'fixed',
		'container_width'      => 1200,
		'sidebar_position'     => 'right',

		// Typography.
		'typography'           => array(
			'h1'   => $typography_defaults,
			'h2'   => $typography_defaults,
			'h3'   => $typography_defaults,
			'h4'   => $typography_defaults,
			'h5'   => $typography_defaults,
			'h6'   => $typography_defaults,
			'p'    => $typography_defaults,
			'a'    => $typography_defaults,
			'span' => $typography_defaults,
		),

		// Colors.
		'primary_color'        => '#2563eb',
		'secondary_color'      => '#1e40af',
		'footer_bg_color'      => '#1f2937',

		// Social.
		'social_icons'         => array(),
		'social_location'      => 'header',

		// Footer.
		'footer_text'          => '',
		'back_to_top'          => true,

		// Custom Code.
		'custom_css'           => '',
		'custom_js'            => '',

		// Tracking & Integrations.
		'ga4_id'               => '',
		'gtm_id'               => '',
		'google_maps_key'      => '',
		'head_scripts'         => '',
		'body_scripts'         => '',
	);
}

/**
 * Retrieve a single theme option with caching.
 *
 * @param string      $key    Top-level option key.
 * @param string|null $subkey Optional sub-key for nested arrays (e.g., typography).
 * @return mixed Option value or default.
 */
function corestarter_get_option( $key, $subkey = null ) {
	static $cache = null;

	if ( null === $cache ) {
		$defaults = corestarter_get_defaults();
		$stored   = get_option( 'corestarter_options', array() );
		$cache    = wp_parse_args( $stored, $defaults );

		// Deep merge typography.
		if ( isset( $stored['typography'] ) && is_array( $stored['typography'] ) ) {
			foreach ( $defaults['typography'] as $el => $el_defaults ) {
				$cache['typography'][ $el ] = wp_parse_args(
					isset( $stored['typography'][ $el ] ) ? $stored['typography'][ $el ] : array(),
					$el_defaults
				);
			}
		}
	}

	if ( ! array_key_exists( $key, $cache ) ) {
		return null;
	}

	if ( null !== $subkey && is_array( $cache[ $key ] ) ) {
		return isset( $cache[ $key ][ $subkey ] ) ? $cache[ $key ][ $subkey ] : null;
	}

	return $cache[ $key ];
}

/**
 * Get curated list of Google Fonts.
 *
 * @return array Font family names.
 */
function corestarter_get_google_fonts() {
	return array(
		'Roboto',
		'Open Sans',
		'Lato',
		'Montserrat',
		'Poppins',
		'Raleway',
		'Nunito',
		'Inter',
		'Oswald',
		'Merriweather',
		'Playfair Display',
		'Source Sans 3',
		'PT Sans',
		'Roboto Slab',
		'Noto Sans',
		'Ubuntu',
		'Rubik',
		'Work Sans',
		'Fira Sans',
		'Barlow',
		'Mulish',
		'Quicksand',
		'Josefin Sans',
		'DM Sans',
		'Libre Baskerville',
	);
}

/**
 * Get system font stacks.
 *
 * @return array Keyed by display name => CSS value.
 */
function corestarter_get_system_fonts() {
	return array(
		'System Default' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
		'Georgia'        => 'Georgia, "Times New Roman", Times, serif',
		'Helvetica'      => 'Helvetica, Arial, sans-serif',
		'Courier'        => '"Courier New", Courier, monospace',
		'Garamond'       => 'Garamond, "Times New Roman", Times, serif',
		'Trebuchet MS'   => '"Trebuchet MS", Helvetica, sans-serif',
	);
}
