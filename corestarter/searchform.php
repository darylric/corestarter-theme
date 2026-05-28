<?php
/**
 * Custom search form template.
 *
 * @package Corestarter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'corestarter' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search&hellip;', 'corestarter' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	</label>
	<button type="submit" class="search-submit">
		<span class="screen-reader-text"><?php esc_html_e( 'Search', 'corestarter' ); ?></span>
		<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
	</button>
</form>
