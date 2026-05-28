<?php
/**
 * Template part for displaying posts in archive/index views.
 *
 * @package Corestarter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>

	<?php corestarter_post_thumbnail( 'medium_large' ); ?>

	<div class="post-card-content">
		<header class="entry-header">
			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

			<?php if ( 'post' === get_post_type() ) : ?>
				<div class="entry-meta">
					<?php
					corestarter_posted_on();
					corestarter_posted_by();
					?>
				</div>
			<?php endif; ?>
		</header>

		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div>

		<footer class="entry-footer">
			<?php corestarter_entry_footer(); ?>
		</footer>
	</div>

</article>
