<?php
/**
 * The template for displaying WooCommerce pages.
 *
 * @package Corestarter
 * @since   1.0.0
 */

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">
		<?php woocommerce_content(); ?>
	</main>
</div>

<?php
get_sidebar();
get_footer();
