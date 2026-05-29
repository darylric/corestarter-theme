<?php
/**
 * WooCommerce Compatibility.
 *
 * @package Corestarter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Utilities\FeaturesUtil;

/**
 * Declare compatibility with WooCommerce features (HPOS, cart/checkout blocks).
 */
function corestarter_declare_woocommerce_compatibility() {
	if ( class_exists( FeaturesUtil::class ) ) {
		FeaturesUtil::declare_compatibility( 'custom_order_tables', CORESTARTER_DIR . '/functions.php', true );
		FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', CORESTARTER_DIR . '/functions.php', true );
	}
}
add_action( 'before_woocommerce_init', 'corestarter_declare_woocommerce_compatibility' );

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
 * Tell WooCommerce the cart widget is always active.
 *
 * This ensures WooCommerce loads and localizes the cart-fragments
 * script on every page, so the header mini-cart stays up to date.
 */
add_filter( 'woocommerce_widget_cart_is_hidden', '__return_false' );

/**
 * Enqueue WooCommerce-specific styles only on shop pages.
 */
function corestarter_woocommerce_scripts() {
	// Header icon styles — always loaded (icons visible site-wide).
	wp_enqueue_style(
		'corestarter-woocommerce-header',
		CORESTARTER_URI . '/assets/css/woocommerce-header.css',
		array( 'corestarter-style' ),
		CORESTARTER_VERSION
	);

	// Shop page styles — only on WooCommerce pages.
	if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
		wp_enqueue_style(
			'corestarter-woocommerce',
			CORESTARTER_URI . '/assets/css/woocommerce.css',
			array( 'corestarter-style' ),
			CORESTARTER_VERSION
		);
	}

	// Single product layout — only on individual product pages.
	if ( is_product() ) {
		wp_enqueue_style(
			'corestarter-woocommerce-single',
			CORESTARTER_URI . '/assets/css/woocommerce-single.css',
			array( 'corestarter-woocommerce' ),
			CORESTARTER_VERSION
		);
	}


	// Shop filter drawer script — only on shop archive pages.
	if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
		wp_enqueue_script(
			'corestarter-shop-filters',
			CORESTARTER_URI . '/assets/js/shop-filters.js',
			array(),
			CORESTARTER_VERSION,
			array( 'strategy' => 'defer' )
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
	unset( $enqueue_styles['woocommerce-smallscreen'] );
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
 * Modify products per page using the theme option.
 *
 * @return int Number of products per page.
 */
function corestarter_woocommerce_products_per_page() {
	$per_page = absint( corestarter_get_option( 'products_per_page' ) );
	return $per_page > 0 ? $per_page : 12;
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
		<span class="cart-count" aria-live="polite" aria-atomic="true"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
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
			<div class="widget_shopping_cart_content">
				<?php woocommerce_mini_cart(); ?>
			</div>
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
	woocommerce_mini_cart();
	$fragments['div.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . ob_get_clean() . '</div>';

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
	$defaults['wrap_before'] = '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'corestarter' ) . '" itemscope itemtype="https://schema.org/BreadcrumbList">';
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

/**
 * Output the header My Account icon link.
 */
function corestarter_header_account_link() {
	if ( ! function_exists( 'wc_get_page_permalink' ) ) {
		return;
	}
	$account_url = wc_get_page_permalink( 'myaccount' );
	?>
	<a class="header-account-link" href="<?php echo esc_url( $account_url ); ?>" title="<?php esc_attr_e( 'My Account', 'corestarter' ); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
		<span class="screen-reader-text"><?php esc_html_e( 'My Account', 'corestarter' ); ?></span>
	</a>
	<?php
}

/**
 * Remove WooCommerce pages (Cart, Checkout, My Account) from the primary nav menu.
 * These are replaced by header icons.
 *
 * @param array  $sorted_menu_items Sorted menu items.
 * @param object $args              Menu arguments.
 * @return array Filtered menu items.
 */
function corestarter_remove_wc_menu_items( $sorted_menu_items, $args ) {
	if ( 'primary' !== $args->theme_location ) {
		return $sorted_menu_items;
	}

	$wc_page_ids = array_filter( array(
		wc_get_page_id( 'cart' ),
		wc_get_page_id( 'checkout' ),
		wc_get_page_id( 'myaccount' ),
	) );

	if ( empty( $wc_page_ids ) ) {
		return $sorted_menu_items;
	}

	foreach ( $sorted_menu_items as $key => $item ) {
		if ( 'page' === $item->object && in_array( (int) $item->object_id, $wc_page_ids, true ) ) {
			unset( $sorted_menu_items[ $key ] );
		}
	}

	return $sorted_menu_items;
}
add_filter( 'wp_nav_menu_objects', 'corestarter_remove_wc_menu_items', 10, 2 );

/**
 * Force left sidebar layout on WooCommerce shop archive pages.
 *
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function corestarter_woocommerce_force_shop_sidebar( $classes ) {
	if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
		$classes = array_diff( $classes, array( 'sidebar-right', 'sidebar-none', 'no-sidebar' ) );
		$classes[] = 'sidebar-left';
		$classes[] = 'woocommerce-has-shop-sidebar';
	}
	return $classes;
}
add_filter( 'body_class', 'corestarter_woocommerce_force_shop_sidebar', 20 );

/**
 * Wrap the Sort By and result count in a toolbar div.
 */
function corestarter_shop_toolbar_open() {
	echo '<div class="shop-toolbar">';
}
add_action( 'woocommerce_before_shop_loop', 'corestarter_shop_toolbar_open', 15 );

function corestarter_shop_toolbar_close() {
	echo '</div>';
}
add_action( 'woocommerce_before_shop_loop', 'corestarter_shop_toolbar_close', 35 );

/**
 * Output the mobile filter toggle button and overlay.
 */
function corestarter_shop_filter_toggle() {
	if ( ! ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) ) {
		return;
	}
	?>
	<button class="shop-filter-toggle" aria-controls="shop-sidebar" aria-expanded="false">
		<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
		<?php esc_html_e( 'Filter Products', 'corestarter' ); ?>
	</button>
	<div class="shop-sidebar-overlay" aria-hidden="true"></div>
	<?php
}
add_action( 'woocommerce_before_shop_loop', 'corestarter_shop_filter_toggle', 12 );

/**
 * Use h3 for product titles in the shop loop to maintain heading hierarchy.
 * h1 = page title, h2 = widget/section titles, h3 = product titles.
 */
function corestarter_shop_loop_product_title() {
	echo '<h3 class="woocommerce-loop-product__title">' . esc_html( get_the_title() ) . '</h3>';
}
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'corestarter_shop_loop_product_title', 10 );

/**
 * Single product page tweaks — runs after the query is set up.
 *
 * 1. Force no sidebar so the two-column gallery/summary layout has full width.
 * 2. Move the sale flash badge from the gallery column into the summary column
 *    (renders above the product title, styled as a pill in woocommerce-single.css).
 */
add_action( 'wp', function () {
	if ( ! is_product() ) {
		return;
	}

	// ── 1. No sidebar on single product pages ────────────────────────────────
	add_filter( 'body_class', function ( $classes ) {
		$classes = array_diff( $classes, array( 'sidebar-right', 'sidebar-left' ) );
		$classes[] = 'sidebar-none';
		$classes[] = 'no-sidebar';
		return $classes;
	}, 20 );

	// ── 2. Move sale badge into the summary column ───────────────────────────
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_show_product_sale_flash', 1 );
} );
