<?php
/**
 * Empty cart page.
 *
 * This template overrides the WooCommerce default empty cart display
 * with a friendlier layout using theme styles.
 *
 * @package Corestarter
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked wc_empty_cart_message - 10
 */
do_action( 'woocommerce_cart_is_empty' );

if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
	<div class="cart-empty-state">
		<div class="cart-empty-icon">
			<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
		</div>
		<p class="return-to-shop">
			<a class="button cs-button cs-button--primary" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php
					/**
					 * Filter the "Return to Shop" button text.
					 *
					 * @param string $text Button text.
					 */
					echo esc_html( apply_filters( 'woocommerce_return_to_shop_text', __( 'Return to Shop', 'corestarter' ) ) );
				?>
			</a>
		</p>
	</div>
<?php endif; ?>
