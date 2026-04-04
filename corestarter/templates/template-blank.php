<?php
/**
 * Template Name: Blank Canvas
 * Template Post Type: page
 *
 * A blank template with no header, footer, or sidebar.
 * Useful for landing pages and custom layouts.
 *
 * @package Corestarter
 * @since   1.0.0
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'blank-canvas' ); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site blank-canvas-page">
	<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>

	</main>
</div>

<?php wp_footer(); ?>

</body>
</html>
