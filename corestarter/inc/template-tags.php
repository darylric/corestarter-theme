<?php
/**
 * Custom template tags for the theme.
 *
 * @package Corestarter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Display post meta information (date, author, categories).
 */
function corestarter_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf(
		$time_string,
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( DATE_W3C ) ),
		esc_html( get_the_modified_date() )
	);

	printf(
		'<span class="posted-on"><a href="%1$s" rel="bookmark">%2$s</a></span>',
		esc_url( get_permalink() ),
		$time_string // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
}

/**
 * Display the post author.
 */
function corestarter_posted_by() {
	printf(
		'<span class="byline"><span class="author vcard"><a class="url fn n" href="%1$s">%2$s</a></span></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_html( get_the_author() )
	);
}

/**
 * Display categories and tags for a post.
 */
function corestarter_entry_footer() {
	if ( 'post' === get_post_type() ) {
		$categories_list = get_the_category_list( esc_html__( ', ', 'corestarter' ) );
		if ( $categories_list ) {
			printf( '<span class="cat-links">%s</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'corestarter' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">%s</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link(
			esc_html__( 'Leave a Comment', 'corestarter' ),
			esc_html__( '1 Comment', 'corestarter' ),
			esc_html__( '% Comments', 'corestarter' )
		);
		echo '</span>';
	}

	edit_post_link(
		esc_html__( 'Edit', 'corestarter' ),
		'<span class="edit-link">',
		'</span>'
	);
}

/**
 * Display post thumbnail with fallback.
 *
 * @param string $size Image size.
 */
function corestarter_post_thumbnail( $size = 'post-thumbnail' ) {
	if ( post_password_required() || is_attachment() ) {
		return;
	}

	if ( has_post_thumbnail() ) : ?>
		<div class="post-thumbnail">
			<?php if ( is_singular() ) : ?>
				<?php the_post_thumbnail( $size ); ?>
			<?php else : ?>
				<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
					<?php the_post_thumbnail( $size, array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif;
}

/**
 * Display pagination for archive pages.
 */
function corestarter_pagination() {
	the_posts_pagination(
		array(
			'mid_size'  => 2,
			'prev_text' => '<span aria-hidden="true">&laquo;</span> <span class="screen-reader-text">' . esc_html__( 'Previous', 'corestarter' ) . '</span>',
			'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'corestarter' ) . '</span> <span aria-hidden="true">&raquo;</span>',
		)
	);
}

/**
 * Fallback menu when no menu is assigned.
 */
function corestarter_fallback_menu() {
	echo '<ul id="primary-menu" class="menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'corestarter' ) . '</a></li>';
	wp_list_pages(
		array(
			'title_li' => '',
			'depth'    => 1,
		)
	);
	echo '</ul>';
}
