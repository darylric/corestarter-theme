<?php
/**
 * Single Product Content Template
 *
 * Custom Corestarter layout:
 *   - Left  : sticky product gallery with vertical thumbnail strip
 *   - Right : title, rating, price, short description, add-to-cart, meta
 *   - Below : description / reviews / attributes tabs, upsells, related
 *
 * @package Corestarter
 * @since   1.1.0
 *
 * This file overrides woocommerce/content-single-product.php.
 */

defined( 'ABSPATH' ) || exit;

global $product;
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'cs-product-single', $product ); ?>>

	<?php
	/**
	 * Hook: woocommerce_before_single_product.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 */
	do_action( 'woocommerce_before_single_product' );

	if ( post_password_required() ) {
		echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	?>

	<div class="woocommerce-notices-wrapper"></div>

	<!-- ════════════════════════════════════════════════════════
	     Two-column: gallery (left) + summary (right)
	     ════════════════════════════════════════════════════════ -->
	<div class="cs-product-layout">

		<!-- Left: gallery + sale flash (flash moved here via PHP hook in woocommerce.php) -->
		<div class="cs-product-gallery-col">
			<?php
			/**
			 * Hook: woocommerce_before_single_product_summary.
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10  (removed; re-hooked to summary)
			 * @hooked woocommerce_show_product_images     - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );
			?>
		</div><!-- .cs-product-gallery-col -->

		<!-- Right: all summary content -->
		<div class="cs-product-summary-col">
			<div class="summary entry-summary">
				<?php
				/**
				 * Hook: woocommerce_single_product_summary.
				 *
				 * @hooked woocommerce_show_product_sale_flash         - 1  (added by our theme)
				 * @hooked woocommerce_template_single_title           - 5
				 * @hooked woocommerce_template_single_rating          - 10
				 * @hooked woocommerce_template_single_price           - 10
				 * @hooked woocommerce_template_single_excerpt         - 20
				 * @hooked woocommerce_template_single_add_to_cart     - 30
				 * @hooked woocommerce_template_single_meta            - 40
				 * @hooked woocommerce_template_single_sharing         - 50
				 * @hooked WC_Structured_Data::generate_product_data() - 60
				 */
				do_action( 'woocommerce_single_product_summary' );
				?>
			</div><!-- .summary.entry-summary -->
		</div><!-- .cs-product-summary-col -->

	</div><!-- .cs-product-layout -->

	<!-- ════════════════════════════════════════════════════════
	     Full-width: tabs + upsells + related products
	     ════════════════════════════════════════════════════════ -->
	<div class="cs-product-lower">
		<?php
		/**
		 * Hook: woocommerce_after_single_product_summary.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display           - 15
		 * @hooked woocommerce_output_related_products  - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
		?>
	</div><!-- .cs-product-lower -->

	<?php do_action( 'woocommerce_after_single_product' ); ?>

</div><!-- #product-<?php the_ID(); ?> -->
