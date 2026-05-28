<?php
/**
 * Enqueue scripts and styles.
 *
 * @package Corestarter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue front-end styles and scripts.
 */
function corestarter_scripts() {
	// Main stylesheet.
	wp_enqueue_style(
		'corestarter-style',
		CORESTARTER_URI . '/assets/css/main.css',
		array(),
		CORESTARTER_VERSION
	);

	// Theme stylesheet (style.css — required by WordPress).
	wp_enqueue_style(
		'corestarter-theme-style',
		get_stylesheet_uri(),
		array( 'corestarter-style' ),
		CORESTARTER_VERSION
	);

	// Navigation script (deferred for non-blocking load).
	wp_enqueue_script(
		'corestarter-navigation',
		CORESTARTER_URI . '/assets/js/navigation.js',
		array(),
		CORESTARTER_VERSION,
		array( 'strategy' => 'defer' )
	);

	// Main theme script (deferred for non-blocking load).
	wp_enqueue_script(
		'corestarter-main',
		CORESTARTER_URI . '/assets/js/main.js',
		array(),
		CORESTARTER_VERSION,
		array( 'strategy' => 'defer' )
	);

	// Pass theme settings to JS.
	wp_localize_script(
		'corestarter-main',
		'corestarterSettings',
		array(
			'stickyHeader' => corestarter_get_option( 'sticky_header' ),
		)
	);

	// Register Google Maps API (enqueued only when [corestarter_map] shortcode is used).
	$maps_key = corestarter_get_option( 'google_maps_key' );
	if ( ! empty( $maps_key ) ) {
		wp_register_script(
			'corestarter-google-maps',
			'https://maps.googleapis.com/maps/api/js?key=' . rawurlencode( $maps_key ) . '&callback=Function.prototype',
			array(),
			null, // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			true
		);
	}

	// Threaded comments reply script.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'corestarter_scripts' );

/**
 * Enqueue Google Fonts based on typography settings.
 */
function corestarter_enqueue_google_fonts() {
	$typography       = corestarter_get_option( 'typography' );
	$google_fonts_list = corestarter_get_google_fonts();
	$families         = array();

	if ( ! is_array( $typography ) ) {
		return;
	}

	foreach ( $typography as $settings ) {
		if ( empty( $settings['family'] ) || ! in_array( $settings['family'], $google_fonts_list, true ) ) {
			continue;
		}

		$weight = ! empty( $settings['weight'] ) ? $settings['weight'] : '400';
		$key    = $settings['family'];

		if ( ! isset( $families[ $key ] ) ) {
			$families[ $key ] = array();
		}

		if ( ! in_array( $weight, $families[ $key ], true ) ) {
			$families[ $key ][] = $weight;
		}
	}

	if ( empty( $families ) ) {
		return;
	}

	// Build Google Fonts API v2 URL.
	$params = array();
	foreach ( $families as $family => $weights ) {
		sort( $weights );
		$params[] = 'family=' . rawurlencode( $family ) . ':wght@' . implode( ';', $weights );
	}
	$url = 'https://fonts.googleapis.com/css2?' . implode( '&', $params ) . '&display=swap';

	wp_enqueue_style( 'corestarter-google-fonts', $url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
}
add_action( 'wp_enqueue_scripts', 'corestarter_enqueue_google_fonts' );

/**
 * Output resource hints and preload critical assets.
 */
function corestarter_resource_hints() {
	// Preload main stylesheet.
	echo '<link rel="preload" href="' . esc_url( CORESTARTER_URI . '/assets/css/main.css' ) . '" as="style">' . "\n";

	// Preconnect + preload Google Fonts — only when Google Fonts are in use.
	$typography   = corestarter_get_option( 'typography' );
	$google_fonts = corestarter_get_google_fonts();

	if ( is_array( $typography ) ) {
		$used_google_families = array();

		foreach ( $typography as $settings ) {
			if ( ! empty( $settings['family'] ) && in_array( $settings['family'], $google_fonts, true ) ) {
				$used_google_families[] = $settings['family'];
			}
		}

		if ( ! empty( $used_google_families ) ) {
			// Preconnect to Google Fonts origins.
			echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
			echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

			// DNS-prefetch as a fallback for older browsers.
			echo '<link rel="dns-prefetch" href="https://fonts.googleapis.com">' . "\n";
			echo '<link rel="dns-prefetch" href="https://fonts.gstatic.com">' . "\n";
		}
	}
}
add_action( 'wp_head', 'corestarter_resource_hints', 1 );
