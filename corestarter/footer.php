<?php
/**
 * The footer template.
 *
 * @package Corestarter
 * @since   1.0.0
 */

$footer_text = corestarter_get_option( 'footer_text' );
?>
		</div><!-- .container -->
	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">

		<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) ) : ?>
			<div class="footer-widgets">
				<div class="container">
					<div class="footer-widgets-grid">
						<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
							<div class="footer-widget-area">
								<?php dynamic_sidebar( 'footer-1' ); ?>
							</div>
						<?php endif; ?>

						<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
							<div class="footer-widget-area">
								<?php dynamic_sidebar( 'footer-2' ); ?>
							</div>
						<?php endif; ?>

						<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
							<div class="footer-widget-area">
								<?php dynamic_sidebar( 'footer-3' ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="footer-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Footer Menu', 'corestarter' ); ?>">
				<div class="container">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'menu_class'     => 'footer-menu',
							'container'      => false,
							'depth'          => 1,
						)
					);
					?>
				</div>
			</nav>
		<?php endif; ?>

		<?php
		$social_loc = corestarter_get_option( 'social_location' );
		if ( 'footer' === $social_loc || 'both' === $social_loc ) :
			?>
			<div class="footer-social">
				<div class="container">
					<?php get_template_part( 'template-parts/social-icons' ); ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="site-info">
			<div class="container">
				<?php if ( $footer_text ) : ?>
					<p class="footer-custom-text"><?php echo wp_kses_post( $footer_text ); ?></p>
				<?php endif; ?>

				<p class="footer-copyright">
					&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php bloginfo( 'name' ); ?>
					</a>.
					<?php esc_html_e( 'All rights reserved.', 'corestarter' ); ?>
				</p>

				<p class="theme-credit">
					<?php
					printf(
						/* translators: %s: Theme author link. */
						esc_html__( 'Theme by %s', 'corestarter' ),
						'<a href="https://github.com/darylric" rel="nofollow noopener" target="_blank">Daryl Ric Lanaban</a>'
					);
					?>
				</p>
			</div>
		</div>
	</footer>

	<?php if ( corestarter_get_option( 'back_to_top' ) ) : ?>
		<!-- Back to Top Button -->
		<button id="back-to-top" class="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'corestarter' ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="18 15 12 9 6 15"></polyline></svg>
		</button>
	<?php endif; ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
