<?php
/**
 * Template part for displaying social icons.
 *
 * Used in both header and footer based on the social_location option.
 *
 * @package Corestarter
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

$social_icons = corestarter_get_option( 'social_icons' );

if ( empty( $social_icons ) || ! is_array( $social_icons ) ) {
	return;
}
?>
<div class="social-icons">
	<?php foreach ( $social_icons as $item ) :
		if ( empty( $item['link'] ) ) {
			continue;
		}
		$icon_url = ! empty( $item['icon_url'] ) ? $item['icon_url'] : '';
		$new_tab  = ! empty( $item['new_tab'] );
		?>
		<a href="<?php echo esc_url( $item['link'] ); ?>"
		   class="social-icon-link"
		   <?php echo $new_tab ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
			<?php if ( $icon_url ) : ?>
				<img src="<?php echo esc_url( $icon_url ); ?>" alt="" class="social-icon-img" width="24" height="24" loading="lazy">
			<?php endif; ?>
		</a>
	<?php endforeach; ?>
</div>
