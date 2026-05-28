<?php
/**
 * The template for displaying WooCommerce pages.
 *
 * @package Corestarter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">
		<?php woocommerce_content(); ?>
	</main>
</div>

<?php
if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
	get_sidebar( 'shop' );
} else {
	get_sidebar();
}
get_footer();
