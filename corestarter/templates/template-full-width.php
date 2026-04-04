<?php
/**
 * Template Name: Full Width
 * Template Post Type: page, post
 *
 * A full-width page template with no sidebar.
 *
 * @package Corestarter
 * @since   1.0.0
 */

get_header();
?>

<div id="primary" class="content-area full-width">
	<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content/content', 'page' );

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
		?>

	</main>
</div>

<?php
get_footer();
