<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package Corestarter
 * @since   1.0.0
 */

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'Page Not Found', 'corestarter' ); ?></h1>
			</header>

			<div class="page-content">
				<p><?php esc_html_e( 'It looks like nothing was found at this location. Try searching for what you need.', 'corestarter' ); ?></p>

				<?php get_search_form(); ?>
			</div>
		</section>

	</main>
</div>

<?php
get_sidebar();
get_footer();
