<?php
/**
 * The header template.
 *
 * @package Corestarter
 * @since   1.0.0
 */

$sticky_class = corestarter_get_option( 'sticky_header' ) ? ' sticky-header' : '';
$logo_url     = corestarter_get_option( 'logo_url' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#main">
		<?php esc_html_e( 'Skip to content', 'corestarter' ); ?>
	</a>

	<header id="masthead" class="site-header<?php echo esc_attr( $sticky_class ); ?>" role="banner">
		<div class="container header-inner">

			<div class="site-branding">
				<?php if ( $logo_url ) : ?>
					<div class="site-logo">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
						</a>
					</div>
				<?php endif; ?>

				<div class="site-branding-text">
					<?php if ( is_front_page() && is_home() ) : ?>
						<h1 class="site-title">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<?php bloginfo( 'name' ); ?>
							</a>
						</h1>
					<?php else : ?>
						<p class="site-title">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<?php bloginfo( 'name' ); ?>
							</a>
						</p>
					<?php endif; ?>

					<?php
					$description = get_bloginfo( 'description', 'display' );
					if ( $description ) :
						?>
						<p class="site-description"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'corestarter' ); ?>">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle Menu', 'corestarter' ); ?>">
					<span class="hamburger-line"></span>
					<span class="hamburger-line"></span>
					<span class="hamburger-line"></span>
				</button>

				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'container'      => false,
						'fallback_cb'    => 'corestarter_fallback_menu',
					)
				);
				?>
			</nav>

			<?php
			$social_loc = corestarter_get_option( 'social_location' );
			if ( 'header' === $social_loc || 'both' === $social_loc ) {
				get_template_part( 'template-parts/social-icons' );
			}
			?>

		</div>
	</header>

	<div id="content" class="site-content">
		<div class="container">
