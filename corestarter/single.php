<?php
/**
 * The template for displaying single posts.
 *
 * @package Corestarter
 * @since   1.0.0
 */

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content/content', 'single' );

			// Post navigation.
			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'corestarter' ) . '</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'corestarter' ) . '</span> <span class="nav-title">%title</span>',
				)
			);

			// Comments template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
		?>

	</main>
</div>

<?php
get_sidebar();
get_footer();
