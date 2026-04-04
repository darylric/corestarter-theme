<?php
/**
 * WooCommerce Compatibility.
 *
 * @package Corestarter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Declare WooCommerce support.
 */
function corestarter_woocommerce_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 300,
			'single_image_width'    => 600,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 3,
				'min_columns'     => 1,
				'max_columns'     => 4,
			),
		)
	);

	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'corestarter_woocommerce_setup' );

/**
 * Enqueue WooCommerce-specific styles only on shop pages.
 */
function corestarter_woocommerce_scripts() {
	if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
		wp_enqueue_style(
			'corestarter-woocommerce',
			CORESTARTER_URI . '/assets/css/woocommerce.css',
			array( 'corestarter-style' ),
			CORESTARTER_VERSION
		);
	}
}
add_action( 'wp_enqueue_scripts', 'corestarter_woocommerce_scripts' );

/**
 * Disable WooCommerce default styles selectively.
 *
 * We keep the general and smallscreen styles, but override the layout.
 *
 * @param array $enqueue_styles WooCommerce styles.
 * @return array Modified styles.
 */
function corestarter_woocommerce_dequeue_styles( $enqueue_styles ) {
	unset( $enqueue_styles['woocommerce-layout'] );
	return $enqueue_styles;
}
add_filter( 'woocommerce_enqueue_styles', 'corestarter_woocommerce_dequeue_styles' );

/**
 * Replace WooCommerce wrapper markup to match theme structure.
 */
function corestarter_woocommerce_wrapper_before() {
	echo '<div id="primary" class="content-area">';
	echo '<main id="main" class="site-main">';
}

function corestarter_woocommerce_wrapper_after() {
	echo '</main>';
	echo '</div>';
}

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', 'corestarter_woocommerce_wrapper_before' );
add_action( 'woocommerce_after_main_content', 'corestarter_woocommerce_wrapper_after' );

/**
 * Remove default WooCommerce sidebar.
 * The theme uses its own sidebar via get_sidebar().
 */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Modify products per page.
 *
 * @return int Number of products per page.
 */
function corestarter_woocommerce_products_per_page() {
	return 12;
}
add_filter( 'loop_shop_per_page', 'corestarter_woocommerce_products_per_page' );

/**
 * Add WooCommerce body classes.
 *
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function corestarter_woocommerce_body_classes( $classes ) {
	$classes[] = 'woocommerce-active';

	if ( is_shop() || is_product_category() || is_product_tag() ) {
		$classes[] = 'woocommerce-archive';
	}

	return $classes;
}
add_filter( 'body_class', 'corestarter_woocommerce_body_classes' );

/**
 * Output the header cart link markup (used in fragments too).
 */
function corestarter_header_cart_link() {
	?>
	<a class="header-cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View cart', 'corestarter' ); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
		<span class="cart-count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
	</a>
	<?php
}

/**
 * Add cart with mini-cart dropdown to header.
 */
function corestarter_woocommerce_header_cart() {
	if ( ! function_exists( 'wc_get_cart_url' ) ) {
		return;
	}
	?>
	<div class="header-cart-wrap">
		<?php corestarter_header_cart_link(); ?>
		<div class="header-cart-dropdown">
			<?php the_widget( 'WC_Widget_Cart', array( 'title' => '' ) ); ?>
		</div>
	</div>
	<?php
}

/**
 * Update cart link and mini-cart dropdown via AJAX fragments.
 *
 * @param array $fragments Cart fragments.
 * @return array Updated fragments.
 */
function corestarter_woocommerce_cart_link_fragment( $fragments ) {
	ob_start();
	corestarter_header_cart_link();
	$fragments['a.header-cart-link'] = ob_get_clean();

	ob_start();
	the_widget( 'WC_Widget_Cart', array( 'title' => '' ) );
	$fragments['div.header-cart-dropdown'] = '<div class="header-cart-dropdown">' . ob_get_clean() . '</div>';

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'corestarter_woocommerce_cart_link_fragment' );

/**
 * Customize WooCommerce breadcrumb markup.
 *
 * @param array $defaults Default breadcrumb args.
 * @return array Modified breadcrumb args.
 */
function corestarter_woocommerce_breadcrumb_defaults( $defaults ) {
	$defaults['delimiter']   = ' <span class="breadcrumb-sep">/</span> ';
	$defaults['wrap_before'] = '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'corestarter' ) . '">';
	$defaults['wrap_after']  = '</nav>';
	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'corestarter_woocommerce_breadcrumb_defaults' );

/**
 * Ensure product images have lazy loading and async decoding attributes.
 *
 * WordPress 5.5+ handles most images, but WooCommerce loop images
 * can miss these attributes. This filter ensures coverage.
 *
 * @param array $attr Image attributes.
 * @return array Modified attributes.
 */
function corestarter_wc_lazy_load_images( $attr ) {
	if ( ! isset( $attr['loading'] ) ) {
		$attr['loading'] = 'lazy';
	}
	if ( ! isset( $attr['decoding'] ) ) {
		$attr['decoding'] = 'async';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'corestarter_wc_lazy_load_images' );
