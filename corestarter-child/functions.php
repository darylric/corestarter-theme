<?php
/**
 * Corestarter Child Theme Functions
 *
 * @package Corestarter_Child
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue parent and child theme styles.
 *
 * The parent theme's styles are loaded as a dependency so the child
 * theme stylesheet is always loaded after.
 */
function corestarter_child_enqueue_styles() {
	$parent_version = defined( 'CORESTARTER_VERSION' ) ? CORESTARTER_VERSION : '1.0.0';

	wp_enqueue_style(
		'corestarter-parent-style',
		get_template_directory_uri() . '/assets/css/main.css',
		array(),
		$parent_version
	);

	wp_enqueue_style(
		'corestarter-child-style',
		get_stylesheet_uri(),
		array( 'corestarter-parent-style' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'corestarter_child_enqueue_styles' );

/*
 * =====================================================================
 * HOW TO OVERRIDE PARENT THEME FILES
 * =====================================================================
 *
 * This child theme supports overriding files from the parent theme.
 * Simply create the same file in the same relative path within this
 * child theme folder, and WordPress (or the parent's override logic)
 * will load your version instead.
 *
 * AUTO-DETECTED BY WORDPRESS (just drop the file here):
 *   - header.php, footer.php, sidebar.php, single.php, page.php, etc.
 *   - template-parts/content/content.php (any get_template_part() call)
 *   - template-parts/social-icons.php
 *   - templates/template-full-width.php (page templates)
 *
 * AUTO-DETECTED BY PARENT THEME (via corestarter_require_inc()):
 *   - inc/enqueue.php
 *   - inc/template-tags.php
 *   - inc/template-functions.php
 *   - inc/shortcodes.php
 *   - inc/woocommerce.php
 *
 * NOT OVERRIDABLE (always loaded from parent for consistency):
 *   - inc/theme-options-defaults.php
 *   - inc/theme-options.php
 *
 * SHORTCODES:
 *   - Place .php files in the shortcodes/ folder of this child theme.
 *   - They will be loaded alongside (not replacing) the parent's
 *     shortcodes, unless you override inc/shortcodes.php entirely.
 *
 * EXAMPLE: To override the enqueue file, copy:
 *   corestarter/inc/enqueue.php → corestarter-child/inc/enqueue.php
 *   Then edit the child theme copy.
 *
 * =====================================================================
 */

/*
 * Add your custom functions below this line.
 * -----------------------------------------------
 */
