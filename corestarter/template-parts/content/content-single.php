<?php
/**
 * Template part for displaying single post content.
 *
 * @package Corestarter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta">
				<?php
				corestarter_posted_on();
				corestarter_posted_by();
				?>
			</div>
		<?php endif; ?>
	</header>

	<?php corestarter_post_thumbnail( 'large' ); ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'corestarter' ),
				'after'  => '</div>',
			)
		);
		?>
	</div>

	<footer class="entry-footer">
		<?php corestarter_entry_footer(); ?>
	</footer>

</article>
