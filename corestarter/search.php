<?php
/**
 * The template for displaying search results.
 *
 * @package Corestarter
 * @since   1.0.0
 */

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">
					<?php
					printf(
						/* translators: %s: search query. */
						esc_html__( 'Search Results for: %s', 'corestarter' ),
						'<span>' . get_search_query() . '</span>'
					);
					?>
				</h1>
			</header>

			<div class="posts-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content/content', 'search' );
				endwhile;
				?>
			</div>

			<?php corestarter_pagination(); ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content/content', 'none' ); ?>

		<?php endif; ?>

	</main>
</div>

<?php
get_sidebar();
get_footer();
