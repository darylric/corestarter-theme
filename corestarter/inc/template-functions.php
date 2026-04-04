<?php
/**
 * Template functions that enhance the theme.
 *
 * @package Corestarter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add custom body classes.
 *
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function corestarter_body_classes( $classes ) {
	// Add class for singular pages.
	if ( is_singular() ) {
		$classes[] = 'singular';
	}

	// Add sidebar position class.
	$sidebar_position = corestarter_get_option( 'sidebar_position' );
	$classes[]        = 'sidebar-' . sanitize_html_class( $sidebar_position );

	// Add class when sticky header is enabled.
	if ( corestarter_get_option( 'sticky_header' ) ) {
		$classes[] = 'has-sticky-header';
	}

	// Add class if no sidebar widgets.
	if ( ! is_active_sidebar( 'sidebar-1' ) || 'none' === $sidebar_position ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'corestarter_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function corestarter_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'corestarter_pingback_header' );

/**
 * Modify the excerpt length.
 *
 * @param int $length Default excerpt length.
 * @return int Modified excerpt length.
 */
function corestarter_excerpt_length( $length ) {
	if ( is_admin() ) {
		return $length;
	}
	return 30;
}
add_filter( 'excerpt_length', 'corestarter_excerpt_length' );

/**
 * Modify the excerpt "read more" text.
 *
 * @param string $more Default more text.
 * @return string Modified more text.
 */
function corestarter_excerpt_more( $more ) {
	if ( is_admin() ) {
		return $more;
	}
	return '&hellip;';
}
add_filter( 'excerpt_more', 'corestarter_excerpt_more' );

/**
 * Output dynamic CSS from theme options.
 *
 * Generates CSS variables, typography rules, visibility rules,
 * and appends any custom CSS from the options panel.
 */
function corestarter_dynamic_css() {
	$opts     = get_option( 'corestarter_options', array() );
	$defaults = corestarter_get_defaults();
	$opts     = wp_parse_args( $opts, $defaults );

	// CSS custom properties.
	$css  = ':root {';
	$css .= '--cs-primary: ' . esc_attr( $opts['primary_color'] ) . ';';
	$css .= '--cs-secondary: ' . esc_attr( $opts['secondary_color'] ) . ';';
	$css .= '--cs-header-bg: ' . esc_attr( $opts['header_bg_color'] ) . ';';
	$css .= '--cs-footer-bg: ' . esc_attr( $opts['footer_bg_color'] ) . ';';

	if ( 'full' === $opts['container_type'] ) {
		$css .= '--cs-container-width: 100%;';
	} else {
		$css .= '--cs-container-width: ' . absint( $opts['container_width'] ) . 'px;';
	}
	$css .= '}';

	// Hide site title / description.
	if ( ! empty( $opts['hide_site_title'] ) ) {
		$css .= '.site-title { display: none !important; }';
	}
	if ( ! empty( $opts['hide_site_description'] ) ) {
		$css .= '.site-description { display: none !important; }';
	}

	// Typography rules.
	$system_fonts = corestarter_get_system_fonts();
	$typography   = isset( $opts['typography'] ) ? $opts['typography'] : array();

	foreach ( $typography as $element => $settings ) {
		$rules = '';

		if ( ! empty( $settings['family'] ) ) {
			if ( isset( $system_fonts[ $settings['family'] ] ) ) {
				$rules .= 'font-family: ' . $system_fonts[ $settings['family'] ] . ';';
			} else {
				$rules .= 'font-family: "' . esc_attr( $settings['family'] ) . '", sans-serif;';
			}
		}

		if ( ! empty( $settings['size'] ) ) {
			$size             = absint( $settings['size'] );
			$heading_elements = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
			if ( in_array( $element, $heading_elements, true ) ) {
				$rules .= 'font-size: calc(' . $size . 'px * var(--cs-type-scale, 1));';
			} else {
				$rules .= 'font-size: calc(' . $size . 'px * var(--cs-body-type-scale, 1));';
			}
		}

		if ( ! empty( $settings['weight'] ) ) {
			$rules .= 'font-weight: ' . absint( $settings['weight'] ) . ';';
		}

		if ( $rules ) {
			$css .= esc_attr( $element ) . ' { ' . $rules . ' }';
		}
	}

	// Responsive type scales.
	$scales   = isset( $opts['responsive_scales'] ) ? $opts['responsive_scales'] : array();
	$s_def    = corestarter_get_defaults()['responsive_scales'];
	$th       = isset( $scales['tablet_heading'] ) ? floatval( $scales['tablet_heading'] ) : floatval( $s_def['tablet_heading'] );
	$tb       = isset( $scales['tablet_body'] ) ? floatval( $scales['tablet_body'] ) : floatval( $s_def['tablet_body'] );
	$mh       = isset( $scales['mobile_heading'] ) ? floatval( $scales['mobile_heading'] ) : floatval( $s_def['mobile_heading'] );
	$mb       = isset( $scales['mobile_body'] ) ? floatval( $scales['mobile_body'] ) : floatval( $s_def['mobile_body'] );

	// Interpolate intermediate breakpoints.
	$bp1200_h = round( ( 1.0 + $th ) / 2, 4 );  // Midpoint between 1.0 and tablet.
	$bp768_h  = round( ( $th + $mh ) / 2, 4 );   // Midpoint between tablet and mobile.
	$bp768_b  = round( ( $tb + $mb ) / 2, 4 );
	$bp480_h  = round( $mh, 4 );
	$bp480_b  = round( $mb, 4 );

	$css .= '@media (max-width: 1200px) { :root { --cs-type-scale: ' . $bp1200_h . '; } }';
	$css .= '@media (max-width: 992px) { :root { --cs-type-scale: ' . $th . '; --cs-body-type-scale: ' . $tb . '; } }';
	$css .= '@media (max-width: 768px) { :root { --cs-type-scale: ' . $bp768_h . '; --cs-body-type-scale: ' . $bp768_b . '; } }';
	$css .= '@media (max-width: 480px) { :root { --cs-type-scale: ' . $bp480_h . '; --cs-body-type-scale: ' . $bp480_b . '; } }';

	// Custom CSS from theme options.
	$custom_css = isset( $opts['custom_css'] ) ? $opts['custom_css'] : '';
	if ( $custom_css ) {
		$css .= wp_strip_all_tags( $custom_css );
	}

	wp_add_inline_style( 'corestarter-style', $css );
}
add_action( 'wp_enqueue_scripts', 'corestarter_dynamic_css', 20 );

/**
 * Output custom JavaScript from theme options.
 */
function corestarter_custom_js() {
	$custom_js = corestarter_get_option( 'custom_js' );
	if ( $custom_js ) {
		// Custom JS is admin-controlled; output as-is.
		echo '<script id="corestarter-custom-js">' . $custom_js . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_footer', 'corestarter_custom_js', 100 );

/**
 * Add SEO-friendly structured data to header.
 */
function corestarter_schema_markup() {
	if ( is_singular( 'post' ) ) {
		echo '<script type="application/ld+json">';
		echo wp_json_encode(
			array(
				'@context'      => 'https://schema.org',
				'@type'         => 'Article',
				'headline'      => get_the_title(),
				'datePublished' => get_the_date( 'c' ),
				'dateModified'  => get_the_modified_date( 'c' ),
				'author'        => array(
					'@type' => 'Person',
					'name'  => get_the_author(),
				),
			),
			JSON_UNESCAPED_SLASHES
		);
		echo '</script>';
	}
}
add_action( 'wp_head', 'corestarter_schema_markup' );

/**
 * Output Google Analytics 4 tracking script.
 */
function corestarter_output_ga4() {
	if ( is_admin() ) {
		return;
	}

	$ga4_id = corestarter_get_option( 'ga4_id' );
	if ( empty( $ga4_id ) ) {
		return;
	}
	?>
	<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga4_id ); ?>"></script>
	<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo esc_js( $ga4_id ); ?>');</script>
	<?php
}
add_action( 'wp_head', 'corestarter_output_ga4', 1 );

/**
 * Output Google Tag Manager head snippet.
 */
function corestarter_output_gtm_head() {
	if ( is_admin() ) {
		return;
	}

	$gtm_id = corestarter_get_option( 'gtm_id' );
	if ( empty( $gtm_id ) ) {
		return;
	}
	?>
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( $gtm_id ); ?>');</script>
	<?php
}
add_action( 'wp_head', 'corestarter_output_gtm_head', 2 );

/**
 * Output Google Tag Manager noscript fallback after <body>.
 */
function corestarter_output_gtm_body() {
	$gtm_id = corestarter_get_option( 'gtm_id' );
	if ( empty( $gtm_id ) ) {
		return;
	}
	?>
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $gtm_id ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<?php
}
add_action( 'wp_body_open', 'corestarter_output_gtm_body', 1 );

/**
 * Output custom head scripts from theme options.
 */
function corestarter_output_head_scripts() {
	if ( is_admin() ) {
		return;
	}

	$scripts = corestarter_get_option( 'head_scripts' );
	if ( ! empty( $scripts ) ) {
		echo $scripts . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_head', 'corestarter_output_head_scripts', 99 );

/**
 * Output custom body scripts from theme options.
 */
function corestarter_output_body_scripts() {
	$scripts = corestarter_get_option( 'body_scripts' );
	if ( ! empty( $scripts ) ) {
		echo $scripts . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'wp_body_open', 'corestarter_output_body_scripts', 10 );
