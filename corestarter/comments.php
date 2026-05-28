<?php
/**
 * The template for displaying comments.
 *
 * @package Corestarter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>

		<h2 class="comments-title">
			<?php
			$comment_count = get_comments_number();
			printf(
				/* translators: 1: comment count, 2: post title. */
				esc_html( _nx( '%1$s comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', $comment_count, 'comments title', 'corestarter' ) ),
				number_format_i18n( $comment_count ),
				'<span>' . wp_kses_post( get_the_title() ) . '</span>'
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 50,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation();

	endif;

	comment_form();
	?>

</div>
