<?php
/**
 * Corestarter Theme Functions
 *
 * @package Corestarter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Define theme constants.
 */
define( 'CORESTARTER_VERSION', '1.1.0' );
define( 'CORESTARTER_DIR', get_template_directory() );
define( 'CORESTARTER_URI', get_template_directory_uri() );

/**
 * Theme setup.
 *
 * Sets up theme defaults and registers support for various WordPress features.
 */
function corestarter_setup() {
	global $content_width;

	// Set content width (matches the default container width of 1440px).
	if ( ! isset( $content_width ) ) {
		$content_width = 1440;
	}

	// Make theme available for translation.
	load_theme_textdomain( 'corestarter', CORESTARTER_DIR . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Register navigation menus.
	register_nav_menus(
		array(
			'primary'   => esc_html__( 'Primary Menu', 'corestarter' ),
			'footer'    => esc_html__( 'Footer Menu', 'corestarter' ),
		)
	);

	// Switch default core markup to HTML5.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Add support for custom background.
	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'ffffff',
		)
	);

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	// Add support for wide and full alignment.
	add_theme_support( 'align-wide' );

	// Add support for editor styles.
	add_theme_support( 'editor-styles' );

	// Register editor stylesheet so block editor matches front end.
	add_editor_style( 'editor-style.css' );
}
add_action( 'after_setup_theme', 'corestarter_setup' );

/**
 * Register custom image sizes for the theme's layout.
 *
 * Ensures WordPress generates correctly-cropped images instead of
 * relying on CSS scaling, which improves LCP and reduces bandwidth.
 */
function corestarter_add_image_sizes() {
	// Card thumbnail — used in archive/blog post cards (16:9).
	add_image_size( 'corestarter-card', 780, 439, true );

	// Wide content image — single post featured image, no sidebar.
	add_image_size( 'corestarter-wide', 1200, 675, true );

	// Sidebar-constrained — single post featured image with sidebar.
	add_image_size( 'corestarter-medium', 780, 500, true );

	// Square — used for author avatars and profile thumbnails.
	add_image_size( 'corestarter-square', 300, 300, true );
}
add_action( 'after_setup_theme', 'corestarter_add_image_sizes' );

/**
 * Register widget areas.
 */
function corestarter_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'corestarter' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here to appear in the sidebar.', 'corestarter' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Shop Sidebar', 'corestarter' ),
			'id'            => 'shop-sidebar',
			'description'   => esc_html__( 'Add filter widgets here to appear on the WooCommerce shop and product category pages.', 'corestarter' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Widget Area 1', 'corestarter' ),
			'id'            => 'footer-1',
			'description'   => esc_html__( 'First footer widget area.', 'corestarter' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Widget Area 2', 'corestarter' ),
			'id'            => 'footer-2',
			'description'   => esc_html__( 'Second footer widget area.', 'corestarter' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Widget Area 3', 'corestarter' ),
			'id'            => 'footer-3',
			'description'   => esc_html__( 'Third footer widget area.', 'corestarter' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'corestarter_widgets_init' );

/**
 * Load an inc/ file with child theme override support.
 *
 * Checks if the file exists in the child theme's inc/ folder first.
 * If found, loads the child version instead of the parent version.
 * This allows the child theme to override any inc/ file by placing
 * a file with the same name in its own inc/ directory.
 *
 * @param string $file Filename relative to inc/ (e.g. 'enqueue.php').
 */
function corestarter_require_inc( $file ) {
	$child_file = get_stylesheet_directory() . '/inc/' . $file;

	if ( get_stylesheet_directory() !== get_template_directory() && file_exists( $child_file ) ) {
		require $child_file;
	} else {
		require CORESTARTER_DIR . '/inc/' . $file;
	}
}

/**
 * Include required files.
 *
 * Each file can be overridden by placing a copy in the child theme's inc/ folder.
 * Note: theme-options-defaults.php and theme-options.php are always loaded from the
 * parent to ensure the options system stays consistent. Override at your own risk.
 */
require CORESTARTER_DIR . '/inc/theme-options-defaults.php';
require CORESTARTER_DIR . '/inc/theme-options.php';
corestarter_require_inc( 'enqueue.php' );
corestarter_require_inc( 'template-tags.php' );
corestarter_require_inc( 'template-functions.php' );
corestarter_require_inc( 'shortcodes.php' );

// WooCommerce compatibility.
if ( class_exists( 'WooCommerce' ) ) {
	corestarter_require_inc( 'woocommerce.php' );
}
