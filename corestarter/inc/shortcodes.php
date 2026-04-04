<?php
/**
 * Theme shortcodes.
 *
 * @package Corestarter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load shortcode files from the shortcodes directory.
 */
function corestarter_load_shortcodes() {
	$shortcode_dir = CORESTARTER_DIR . '/shortcodes/';

	if ( ! is_dir( $shortcode_dir ) ) {
		return;
	}

	$files = glob( $shortcode_dir . '*.php' );

	if ( $files ) {
		foreach ( $files as $file ) {
			require_once $file;
		}
	}
}
corestarter_load_shortcodes();

/**
 * [corestarter_year] — Outputs the current year.
 *
 * Usage: [corestarter_year]
 *
 * @return string Current year.
 */
function corestarter_year_shortcode() {
	return esc_html( date_i18n( 'Y' ) );
}
add_shortcode( 'corestarter_year', 'corestarter_year_shortcode' );

/**
 * [corestarter_button] — Outputs a styled button.
 *
 * Usage: [corestarter_button url="#" text="Click Me" style="primary"]
 *
 * @param array $atts Shortcode attributes.
 * @return string Button HTML.
 */
function corestarter_button_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'url'    => '#',
			'text'   => esc_html__( 'Click Here', 'corestarter' ),
			'style'  => 'primary',
			'target' => '_self',
		),
		$atts,
		'corestarter_button'
	);

	return sprintf(
		'<a href="%s" class="cs-button cs-button--%s" target="%s">%s</a>',
		esc_url( $atts['url'] ),
		sanitize_html_class( $atts['style'] ),
		esc_attr( $atts['target'] ),
		esc_html( $atts['text'] )
	);
}
add_shortcode( 'corestarter_button', 'corestarter_button_shortcode' );

/**
 * [corestarter_map] — Outputs a Google Map embed.
 *
 * Usage: [corestarter_map address="New York" zoom="14" height="400px"]
 *        [corestarter_map lat="14.5995" lng="120.9842" zoom="16" height="350px"]
 *
 * @param array $atts Shortcode attributes.
 * @return string Map HTML.
 */
function corestarter_map_shortcode( $atts ) {
	static $map_count = 0;
	$map_count++;

	$atts = shortcode_atts(
		array(
			'address' => '',
			'lat'     => '',
			'lng'     => '',
			'zoom'    => '14',
			'height'  => '400px',
			'id'      => '',
		),
		$atts,
		'corestarter_map'
	);

	$api_key = corestarter_get_option( 'google_maps_key' );

	if ( empty( $api_key ) ) {
		return '<div class="cs-map-notice">' . esc_html__( 'Google Maps API key is not configured. Please add it in Theme Options &rarr; Tracking &amp; Integrations.', 'corestarter' ) . '</div>';
	}

	$map_id = ! empty( $atts['id'] ) ? sanitize_html_class( $atts['id'] ) : 'cs-map-' . $map_count;
	$zoom   = absint( $atts['zoom'] );
	$height = esc_attr( $atts['height'] );

	// Enqueue Maps API only when shortcode is used.
	wp_enqueue_script( 'corestarter-google-maps' );

	$output = '<div class="cs-map" id="' . esc_attr( $map_id ) . '" style="height:' . $height . '"></div>';

	// Build init script.
	if ( ! empty( $atts['lat'] ) && ! empty( $atts['lng'] ) ) {
		$lat = floatval( $atts['lat'] );
		$lng = floatval( $atts['lng'] );
		$output .= '<script>
document.addEventListener("DOMContentLoaded",function(){
	var m=new google.maps.Map(document.getElementById("' . esc_js( $map_id ) . '"),{zoom:' . $zoom . ',center:{lat:' . $lat . ',lng:' . $lng . '}});
	new google.maps.Marker({position:{lat:' . $lat . ',lng:' . $lng . '},map:m});
});
</script>';
	} elseif ( ! empty( $atts['address'] ) ) {
		$address = esc_js( $atts['address'] );
		$output .= '<script>
document.addEventListener("DOMContentLoaded",function(){
	var el=document.getElementById("' . esc_js( $map_id ) . '");
	var m=new google.maps.Map(el,{zoom:' . $zoom . ',center:{lat:0,lng:0}});
	var g=new google.maps.Geocoder();
	g.geocode({address:"' . $address . '"},function(r,s){
		if(s==="OK"){m.setCenter(r[0].geometry.location);new google.maps.Marker({position:r[0].geometry.location,map:m});}
	});
});
</script>';
	} else {
		return '<div class="cs-map-notice">' . esc_html__( 'Please provide an address or lat/lng coordinates for the map.', 'corestarter' ) . '</div>';
	}

	return $output;
}
add_shortcode( 'corestarter_map', 'corestarter_map_shortcode' );
