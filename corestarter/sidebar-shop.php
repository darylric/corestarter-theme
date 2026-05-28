<?php
/**
 * The shop sidebar template.
 *
 * Displays the shop-sidebar widget area on WooCommerce archive pages.
 *
 * @package Corestarter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'shop-sidebar' ) ) {
	return;
}
?>

<aside id="shop-sidebar" class="widget-area shop-widget-area" role="complementary" aria-label="<?php esc_attr_e( 'Shop Filters', 'corestarter' ); ?>">
	<div class="shop-sidebar-header">
		<h2 class="shop-sidebar-title"><?php esc_html_e( 'Filter Products', 'corestarter' ); ?></h2>
		<button class="shop-sidebar-close" aria-label="<?php esc_attr_e( 'Close filters', 'corestarter' ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
		</button>
	</div>
	<?php dynamic_sidebar( 'shop-sidebar' ); ?>
</aside>
