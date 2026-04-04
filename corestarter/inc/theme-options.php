<?php
/**
 * Theme Options Admin Page.
 *
 * Registers the admin menu page, settings, sanitization,
 * and renders the tabbed options interface.
 *
 * @package Corestarter
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the top-level admin menu page.
 */
function corestarter_add_options_page() {
	add_menu_page(
		esc_html__( 'Corestarter Options', 'corestarter' ),
		esc_html__( 'Corestarter', 'corestarter' ),
		'manage_options',
		'corestarter-options',
		'corestarter_options_page_html',
		'dashicons-admin-customizer',
		61
	);
}
add_action( 'admin_menu', 'corestarter_add_options_page' );

/**
 * Register the setting and sanitize callback.
 */
function corestarter_register_settings() {
	register_setting(
		'corestarter_options_group',
		'corestarter_options',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'corestarter_sanitize_options',
			'default'           => corestarter_get_defaults(),
		)
	);
}
add_action( 'admin_init', 'corestarter_register_settings' );

/**
 * Enqueue admin assets only on our options page.
 *
 * @param string $hook The current admin page hook suffix.
 */
function corestarter_admin_enqueue( $hook ) {
	if ( 'toplevel_page_corestarter-options' !== $hook ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );

	wp_enqueue_style(
		'corestarter-admin-options',
		CORESTARTER_URI . '/assets/css/admin-options.css',
		array( 'wp-color-picker' ),
		CORESTARTER_VERSION
	);

	wp_enqueue_script(
		'corestarter-admin-options',
		CORESTARTER_URI . '/assets/js/admin-options.js',
		array( 'jquery', 'wp-color-picker' ),
		CORESTARTER_VERSION,
		true
	);

	wp_localize_script(
		'corestarter-admin-options',
		'corestarterAdmin',
		array(
			'mediaTitle'  => esc_html__( 'Choose Image', 'corestarter' ),
			'mediaButton' => esc_html__( 'Use This Image', 'corestarter' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'corestarter_admin_enqueue' );

/**
 * Allow SVG, WEBP, and AVIF uploads.
 *
 * @param array $mimes Existing MIME types.
 * @return array Modified MIME types.
 */
function corestarter_allow_modern_uploads( $mimes ) {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	$mimes['webp'] = 'image/webp';
	$mimes['avif'] = 'image/avif';
	return $mimes;
}
add_filter( 'upload_mimes', 'corestarter_allow_modern_uploads' );

/**
 * Fix WordPress not recognizing SVG file type on upload.
 *
 * @param array  $data     File data.
 * @param string $file     Full path to the file.
 * @param string $filename The name of the file.
 * @param array  $mimes    Allowed MIME types.
 * @return array Modified file data.
 */
function corestarter_fix_svg_mime_type( $data, $file, $filename, $mimes ) {
	$ext = isset( $data['ext'] ) ? $data['ext'] : '';
	if ( '' === $ext ) {
		$filetype = wp_check_filetype( $filename, $mimes );
		$ext      = $filetype['ext'];
	}
	if ( 'svg' === $ext || 'svgz' === $ext ) {
		$data['type'] = 'image/svg+xml';
		$data['ext']  = $ext;
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'corestarter_fix_svg_mime_type', 10, 4 );

/**
 * One-time migration from Customizer theme_mods to the new options array.
 */
function corestarter_maybe_migrate_customizer() {
	if ( get_option( 'corestarter_migrated' ) ) {
		return;
	}

	$map = array(
		'corestarter_sticky_header'         => 'sticky_header',
		'corestarter_hide_site_title'       => 'hide_site_title',
		'corestarter_hide_site_description' => 'hide_site_description',
		'corestarter_primary_color'         => 'primary_color',
		'corestarter_secondary_color'       => 'secondary_color',
		'corestarter_header_bg_color'       => 'header_bg_color',
		'corestarter_footer_bg_color'       => 'footer_bg_color',
		'corestarter_footer_text'           => 'footer_text',
		'corestarter_back_to_top'           => 'back_to_top',
		'corestarter_sidebar_position'      => 'sidebar_position',
		'corestarter_container_width'       => 'container_width',
	);

	$migrated = array();
	foreach ( $map as $old_key => $new_key ) {
		$value = get_theme_mod( $old_key );
		if ( false !== $value ) {
			$migrated[ $new_key ] = $value;
		}
	}

	// Migrate the custom logo.
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$migrated['logo_id']  = $custom_logo_id;
		$migrated['logo_url'] = wp_get_attachment_url( $custom_logo_id );
	}

	if ( ! empty( $migrated ) ) {
		$existing = get_option( 'corestarter_options', array() );
		update_option( 'corestarter_options', array_merge( $existing, $migrated ) );
	}

	update_option( 'corestarter_migrated', true );
}
add_action( 'admin_init', 'corestarter_maybe_migrate_customizer' );

/**
 * Sanitize all theme options on save.
 *
 * @param array $input Raw form input.
 * @return array Sanitized options.
 */
function corestarter_sanitize_options( $input ) {
	$defaults      = corestarter_get_defaults();
	$clean         = array();
	$google_fonts  = corestarter_get_google_fonts();
	$system_fonts  = array_keys( corestarter_get_system_fonts() );
	$valid_fonts   = array_merge( $google_fonts, $system_fonts );
	$valid_weights = array( '100', '200', '300', '400', '500', '600', '700', '800', '900' );

	// General.
	$clean['logo_id']              = isset( $input['logo_id'] ) ? absint( $input['logo_id'] ) : 0;
	$clean['logo_url']             = $clean['logo_id'] ? wp_get_attachment_url( $clean['logo_id'] ) : '';
	$clean['hide_site_title']      = ! empty( $input['hide_site_title'] );
	$clean['hide_site_description'] = ! empty( $input['hide_site_description'] );

	// Header.
	$clean['sticky_header']   = ! empty( $input['sticky_header'] );
	$clean['header_bg_color'] = isset( $input['header_bg_color'] ) ? sanitize_hex_color( $input['header_bg_color'] ) : $defaults['header_bg_color'];
	if ( ! $clean['header_bg_color'] ) {
		$clean['header_bg_color'] = $defaults['header_bg_color'];
	}

	// Layout.
	$clean['container_type']  = isset( $input['container_type'] ) && in_array( $input['container_type'], array( 'fixed', 'full' ), true ) ? $input['container_type'] : 'fixed';
	$clean['container_width'] = isset( $input['container_width'] ) ? absint( $input['container_width'] ) : 1200;
	$clean['container_width'] = max( 960, min( 1600, $clean['container_width'] ) );
	$clean['sidebar_position'] = isset( $input['sidebar_position'] ) && in_array( $input['sidebar_position'], array( 'right', 'left', 'none' ), true ) ? $input['sidebar_position'] : 'right';

	// Typography.
	$clean['typography'] = array();
	$elements            = array_keys( $defaults['typography'] );
	foreach ( $elements as $el ) {
		$clean['typography'][ $el ] = array(
			'family' => '',
			'size'   => '',
			'weight' => '',
		);

		if ( isset( $input['typography'][ $el ] ) ) {
			$typo = $input['typography'][ $el ];

			if ( ! empty( $typo['family'] ) && in_array( $typo['family'], $valid_fonts, true ) ) {
				$clean['typography'][ $el ]['family'] = $typo['family'];
			}

			if ( ! empty( $typo['size'] ) ) {
				$size = absint( $typo['size'] );
				if ( $size >= 8 && $size <= 120 ) {
					$clean['typography'][ $el ]['size'] = $size;
				}
			}

			if ( ! empty( $typo['weight'] ) && in_array( (string) $typo['weight'], $valid_weights, true ) ) {
				$clean['typography'][ $el ]['weight'] = (string) $typo['weight'];
			}
		}
	}

	// Colors.
	$color_keys = array( 'primary_color', 'secondary_color', 'footer_bg_color' );
	foreach ( $color_keys as $ck ) {
		$clean[ $ck ] = isset( $input[ $ck ] ) ? sanitize_hex_color( $input[ $ck ] ) : $defaults[ $ck ];
		if ( ! $clean[ $ck ] ) {
			$clean[ $ck ] = $defaults[ $ck ];
		}
	}

	// Social.
	$clean['social_icons'] = array();
	if ( isset( $input['social_icons'] ) && is_array( $input['social_icons'] ) ) {
		foreach ( $input['social_icons'] as $item ) {
			$icon_id = isset( $item['icon_id'] ) ? absint( $item['icon_id'] ) : 0;
			$link    = isset( $item['link'] ) ? esc_url_raw( $item['link'] ) : '';

			// Skip completely empty rows.
			if ( ! $icon_id && ! $link ) {
				continue;
			}

			$clean['social_icons'][] = array(
				'icon_id'  => $icon_id,
				'icon_url' => $icon_id ? wp_get_attachment_url( $icon_id ) : '',
				'link'     => $link,
				'new_tab'  => ! empty( $item['new_tab'] ),
			);
		}
	}
	$clean['social_location'] = isset( $input['social_location'] ) && in_array( $input['social_location'], array( 'header', 'footer', 'both' ), true ) ? $input['social_location'] : 'header';

	// Footer.
	$clean['footer_text'] = isset( $input['footer_text'] ) ? wp_kses_post( $input['footer_text'] ) : '';
	$clean['back_to_top'] = ! empty( $input['back_to_top'] );

	// Custom Code.
	$clean['custom_css'] = isset( $input['custom_css'] ) ? wp_strip_all_tags( $input['custom_css'] ) : '';
	$clean['custom_js']  = isset( $input['custom_js'] ) ? $input['custom_js'] : '';

	// Tracking & Integrations.
	$clean['ga4_id']          = isset( $input['ga4_id'] ) ? sanitize_text_field( $input['ga4_id'] ) : '';
	$clean['gtm_id']          = isset( $input['gtm_id'] ) ? sanitize_text_field( $input['gtm_id'] ) : '';
	$clean['google_maps_key'] = isset( $input['google_maps_key'] ) ? sanitize_text_field( $input['google_maps_key'] ) : '';
	$clean['head_scripts']    = isset( $input['head_scripts'] ) ? $input['head_scripts'] : '';
	$clean['body_scripts']    = isset( $input['body_scripts'] ) ? $input['body_scripts'] : '';

	return $clean;
}

/**
 * Render the Theme Options admin page.
 */
function corestarter_options_page_html() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$opts         = get_option( 'corestarter_options', array() );
	$defaults     = corestarter_get_defaults();
	$opts         = wp_parse_args( $opts, $defaults );
	$google_fonts = corestarter_get_google_fonts();
	$system_fonts = corestarter_get_system_fonts();
	$typo_elements = array(
		'h1'   => 'Heading 1 (H1)',
		'h2'   => 'Heading 2 (H2)',
		'h3'   => 'Heading 3 (H3)',
		'h4'   => 'Heading 4 (H4)',
		'h5'   => 'Heading 5 (H5)',
		'h6'   => 'Heading 6 (H6)',
		'p'    => 'Paragraph (P)',
		'a'    => 'Links (A)',
		'span' => 'Span',
	);
	$weight_options = array( '100', '200', '300', '400', '500', '600', '700', '800', '900' );
	$tabs = array(
		'general'     => esc_html__( 'General', 'corestarter' ),
		'header'      => esc_html__( 'Header', 'corestarter' ),
		'layout'      => esc_html__( 'Layout', 'corestarter' ),
		'typography'  => esc_html__( 'Typography', 'corestarter' ),
		'colors'      => esc_html__( 'Colors', 'corestarter' ),
		'social'      => esc_html__( 'Social', 'corestarter' ),
		'footer'      => esc_html__( 'Footer', 'corestarter' ),
		'tracking'    => esc_html__( 'Tracking & Integrations', 'corestarter' ),
		'shortcodes'  => esc_html__( 'Shortcodes', 'corestarter' ),
		'custom-code' => esc_html__( 'Custom Code', 'corestarter' ),
	);
	?>
	<div class="wrap cs-options-wrap">
		<h1><?php esc_html_e( 'Corestarter Theme Options', 'corestarter' ); ?></h1>

		<?php settings_errors( 'corestarter_options' ); ?>

		<nav class="nav-tab-wrapper cs-nav-tabs">
			<?php foreach ( $tabs as $slug => $label ) : ?>
				<a href="#tab-<?php echo esc_attr( $slug ); ?>"
				   class="nav-tab<?php echo 'general' === $slug ? ' nav-tab-active' : ''; ?>"
				   data-tab="tab-<?php echo esc_attr( $slug ); ?>">
					<?php echo esc_html( $label ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<form method="post" action="options.php" class="cs-options-form">
			<?php settings_fields( 'corestarter_options_group' ); ?>

			<!-- ============================================================
			     General Tab
			     ============================================================ -->
			<div id="tab-general" class="cs-tab-content active">
				<h2><?php esc_html_e( 'General Settings', 'corestarter' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'Site Logo', 'corestarter' ); ?></th>
						<td>
							<div class="cs-media-field">
								<input type="hidden" name="corestarter_options[logo_id]"
									   id="cs-logo-id"
									   value="<?php echo esc_attr( $opts['logo_id'] ); ?>">
								<div class="cs-media-preview" id="cs-logo-preview">
									<?php if ( ! empty( $opts['logo_url'] ) ) : ?>
										<img src="<?php echo esc_url( $opts['logo_url'] ); ?>" alt="">
									<?php endif; ?>
								</div>
								<button type="button" class="button cs-upload-btn" data-target="cs-logo"><?php esc_html_e( 'Upload Logo', 'corestarter' ); ?></button>
								<button type="button" class="button cs-remove-btn" data-target="cs-logo"
										style="<?php echo empty( $opts['logo_url'] ) ? 'display:none;' : ''; ?>">
									<?php esc_html_e( 'Remove', 'corestarter' ); ?>
								</button>
								<p class="description"><?php esc_html_e( 'Accepts SVG, PNG, JPG, WEBP, and other modern image formats.', 'corestarter' ); ?></p>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Site Title & Description', 'corestarter' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="corestarter_options[hide_site_title]"
									   value="1" <?php checked( $opts['hide_site_title'] ); ?>>
								<?php esc_html_e( 'Hide Site Title', 'corestarter' ); ?>
							</label>
							<br>
							<label>
								<input type="checkbox" name="corestarter_options[hide_site_description]"
									   value="1" <?php checked( $opts['hide_site_description'] ); ?>>
								<?php esc_html_e( 'Hide Site Description', 'corestarter' ); ?>
							</label>
						</td>
					</tr>
				</table>
			</div>

			<!-- ============================================================
			     Header Tab
			     ============================================================ -->
			<div id="tab-header" class="cs-tab-content">
				<h2><?php esc_html_e( 'Header Settings', 'corestarter' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'Sticky Header', 'corestarter' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="corestarter_options[sticky_header]"
									   value="1" <?php checked( $opts['sticky_header'] ); ?>>
								<?php esc_html_e( 'Enable sticky header on scroll', 'corestarter' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Header Background', 'corestarter' ); ?></th>
						<td>
							<input type="text" name="corestarter_options[header_bg_color]"
								   value="<?php echo esc_attr( $opts['header_bg_color'] ); ?>"
								   class="cs-color-picker" data-default-color="<?php echo esc_attr( $defaults['header_bg_color'] ); ?>">
						</td>
					</tr>
				</table>
			</div>

			<!-- ============================================================
			     Layout Tab
			     ============================================================ -->
			<div id="tab-layout" class="cs-tab-content">
				<h2><?php esc_html_e( 'Layout Settings', 'corestarter' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'Container Type', 'corestarter' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input type="radio" name="corestarter_options[container_type]"
										   value="fixed" <?php checked( $opts['container_type'], 'fixed' ); ?>>
									<?php esc_html_e( 'Fixed Width', 'corestarter' ); ?>
								</label>
								<br>
								<label>
									<input type="radio" name="corestarter_options[container_type]"
										   value="full" <?php checked( $opts['container_type'], 'full' ); ?>>
									<?php esc_html_e( 'Full Width', 'corestarter' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr class="cs-container-width-row" style="<?php echo 'full' === $opts['container_type'] ? 'display:none;' : ''; ?>">
						<th scope="row"><?php esc_html_e( 'Container Width (px)', 'corestarter' ); ?></th>
						<td>
							<input type="number" name="corestarter_options[container_width]"
								   value="<?php echo esc_attr( $opts['container_width'] ); ?>"
								   min="960" max="1600" step="10" class="small-text">
							<p class="description"><?php esc_html_e( 'Value between 960 and 1600 pixels.', 'corestarter' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Sidebar Position', 'corestarter' ); ?></th>
						<td>
							<select name="corestarter_options[sidebar_position]">
								<option value="right" <?php selected( $opts['sidebar_position'], 'right' ); ?>><?php esc_html_e( 'Right', 'corestarter' ); ?></option>
								<option value="left" <?php selected( $opts['sidebar_position'], 'left' ); ?>><?php esc_html_e( 'Left', 'corestarter' ); ?></option>
								<option value="none" <?php selected( $opts['sidebar_position'], 'none' ); ?>><?php esc_html_e( 'No Sidebar', 'corestarter' ); ?></option>
							</select>
						</td>
					</tr>
				</table>
			</div>

			<!-- ============================================================
			     Typography Tab
			     ============================================================ -->
			<div id="tab-typography" class="cs-tab-content">
				<h2><?php esc_html_e( 'Typography Settings', 'corestarter' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Leave fields empty to use the theme defaults. Google Fonts are loaded automatically when selected.', 'corestarter' ); ?></p>
				<table class="form-table cs-typography-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Element', 'corestarter' ); ?></th>
							<th><?php esc_html_e( 'Font Family', 'corestarter' ); ?></th>
							<th><?php esc_html_e( 'Size (px)', 'corestarter' ); ?></th>
							<th><?php esc_html_e( 'Weight', 'corestarter' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $typo_elements as $el => $label ) :
							$typo = isset( $opts['typography'][ $el ] ) ? $opts['typography'][ $el ] : $defaults['typography'][ $el ];
							?>
							<tr>
								<td><strong><?php echo esc_html( $label ); ?></strong></td>
								<td>
									<select name="corestarter_options[typography][<?php echo esc_attr( $el ); ?>][family]" class="cs-font-select">
										<option value=""><?php esc_html_e( '— Inherit / Default —', 'corestarter' ); ?></option>
										<optgroup label="<?php esc_attr_e( 'System Fonts', 'corestarter' ); ?>">
											<?php foreach ( $system_fonts as $name => $stack ) : ?>
												<option value="<?php echo esc_attr( $name ); ?>" <?php selected( $typo['family'], $name ); ?>>
													<?php echo esc_html( $name ); ?>
												</option>
											<?php endforeach; ?>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Google Fonts', 'corestarter' ); ?>">
											<?php foreach ( $google_fonts as $font ) : ?>
												<option value="<?php echo esc_attr( $font ); ?>" <?php selected( $typo['family'], $font ); ?>>
													<?php echo esc_html( $font ); ?>
												</option>
											<?php endforeach; ?>
										</optgroup>
									</select>
								</td>
								<td>
									<input type="number"
										   name="corestarter_options[typography][<?php echo esc_attr( $el ); ?>][size]"
										   value="<?php echo esc_attr( $typo['size'] ); ?>"
										   min="8" max="120" step="1" class="small-text"
										   placeholder="—">
								</td>
								<td>
									<select name="corestarter_options[typography][<?php echo esc_attr( $el ); ?>][weight]">
										<option value=""><?php esc_html_e( '— Default —', 'corestarter' ); ?></option>
										<?php foreach ( $weight_options as $w ) : ?>
											<option value="<?php echo esc_attr( $w ); ?>" <?php selected( $typo['weight'], $w ); ?>>
												<?php echo esc_html( $w ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<!-- ============================================================
			     Colors Tab
			     ============================================================ -->
			<div id="tab-colors" class="cs-tab-content">
				<h2><?php esc_html_e( 'Color Settings', 'corestarter' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'Primary Color', 'corestarter' ); ?></th>
						<td>
							<input type="text" name="corestarter_options[primary_color]"
								   value="<?php echo esc_attr( $opts['primary_color'] ); ?>"
								   class="cs-color-picker" data-default-color="<?php echo esc_attr( $defaults['primary_color'] ); ?>">
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Secondary Color', 'corestarter' ); ?></th>
						<td>
							<input type="text" name="corestarter_options[secondary_color]"
								   value="<?php echo esc_attr( $opts['secondary_color'] ); ?>"
								   class="cs-color-picker" data-default-color="<?php echo esc_attr( $defaults['secondary_color'] ); ?>">
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Header Background', 'corestarter' ); ?></th>
						<td>
							<input type="text" name="corestarter_options[header_bg_color]"
								   value="<?php echo esc_attr( $opts['header_bg_color'] ); ?>"
								   class="cs-color-picker" data-default-color="<?php echo esc_attr( $defaults['header_bg_color'] ); ?>">
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Footer Background', 'corestarter' ); ?></th>
						<td>
							<input type="text" name="corestarter_options[footer_bg_color]"
								   value="<?php echo esc_attr( $opts['footer_bg_color'] ); ?>"
								   class="cs-color-picker" data-default-color="<?php echo esc_attr( $defaults['footer_bg_color'] ); ?>">
						</td>
					</tr>
				</table>
			</div>

			<!-- ============================================================
			     Social Tab
			     ============================================================ -->
			<div id="tab-social" class="cs-tab-content">
				<h2><?php esc_html_e( 'Social Icons', 'corestarter' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'Display Location', 'corestarter' ); ?></th>
						<td>
							<select name="corestarter_options[social_location]">
								<option value="header" <?php selected( $opts['social_location'], 'header' ); ?>><?php esc_html_e( 'Header', 'corestarter' ); ?></option>
								<option value="footer" <?php selected( $opts['social_location'], 'footer' ); ?>><?php esc_html_e( 'Footer', 'corestarter' ); ?></option>
								<option value="both" <?php selected( $opts['social_location'], 'both' ); ?>><?php esc_html_e( 'Both', 'corestarter' ); ?></option>
							</select>
						</td>
					</tr>
				</table>

				<div id="cs-social-repeater">
					<?php
					$social_icons = isset( $opts['social_icons'] ) ? $opts['social_icons'] : array();
					if ( ! empty( $social_icons ) ) :
						foreach ( $social_icons as $i => $item ) :
							?>
							<div class="cs-social-row" data-index="<?php echo esc_attr( $i ); ?>">
								<div class="cs-social-row-inner">
									<div class="cs-social-icon-field">
										<input type="hidden"
											   name="corestarter_options[social_icons][<?php echo esc_attr( $i ); ?>][icon_id]"
											   class="cs-social-icon-id"
											   value="<?php echo esc_attr( $item['icon_id'] ); ?>">
										<div class="cs-media-preview cs-social-icon-preview">
											<?php if ( ! empty( $item['icon_url'] ) ) : ?>
												<img src="<?php echo esc_url( $item['icon_url'] ); ?>" alt="">
											<?php endif; ?>
										</div>
										<button type="button" class="button cs-social-upload-btn"><?php esc_html_e( 'Upload Icon', 'corestarter' ); ?></button>
										<button type="button" class="button cs-social-remove-icon-btn"
												style="<?php echo empty( $item['icon_url'] ) ? 'display:none;' : ''; ?>">
											<?php esc_html_e( 'Remove', 'corestarter' ); ?>
										</button>
									</div>
									<div class="cs-social-link-field">
										<label><?php esc_html_e( 'URL', 'corestarter' ); ?></label>
										<input type="url"
											   name="corestarter_options[social_icons][<?php echo esc_attr( $i ); ?>][link]"
											   class="regular-text cs-social-link"
											   value="<?php echo esc_url( $item['link'] ); ?>"
											   placeholder="https://">
									</div>
									<div class="cs-social-newtab-field">
										<label>
											<input type="checkbox"
												   name="corestarter_options[social_icons][<?php echo esc_attr( $i ); ?>][new_tab]"
												   class="cs-social-newtab"
												   value="1" <?php checked( ! empty( $item['new_tab'] ) ); ?>>
											<?php esc_html_e( 'Open in new tab', 'corestarter' ); ?>
										</label>
									</div>
									<div class="cs-social-actions">
										<button type="button" class="button cs-social-remove-row">
											<span class="dashicons dashicons-trash"></span>
										</button>
									</div>
								</div>
							</div>
						<?php
						endforeach;
					endif;
					?>
				</div>

				<!-- Hidden template row for JS cloning -->
				<script type="text/html" id="tmpl-cs-social-row">
					<div class="cs-social-row" data-index="__INDEX__">
						<div class="cs-social-row-inner">
							<div class="cs-social-icon-field">
								<input type="hidden"
									   name="corestarter_options[social_icons][__INDEX__][icon_id]"
									   class="cs-social-icon-id"
									   value="">
								<div class="cs-media-preview cs-social-icon-preview"></div>
								<button type="button" class="button cs-social-upload-btn"><?php esc_html_e( 'Upload Icon', 'corestarter' ); ?></button>
								<button type="button" class="button cs-social-remove-icon-btn" style="display:none;">
									<?php esc_html_e( 'Remove', 'corestarter' ); ?>
								</button>
							</div>
							<div class="cs-social-link-field">
								<label><?php esc_html_e( 'URL', 'corestarter' ); ?></label>
								<input type="url"
									   name="corestarter_options[social_icons][__INDEX__][link]"
									   class="regular-text cs-social-link"
									   value=""
									   placeholder="https://">
							</div>
							<div class="cs-social-newtab-field">
								<label>
									<input type="checkbox"
										   name="corestarter_options[social_icons][__INDEX__][new_tab]"
										   class="cs-social-newtab"
										   value="1" checked>
									<?php esc_html_e( 'Open in new tab', 'corestarter' ); ?>
								</label>
							</div>
							<div class="cs-social-actions">
								<button type="button" class="button cs-social-remove-row">
									<span class="dashicons dashicons-trash"></span>
								</button>
							</div>
						</div>
					</div>
				</script>

				<p>
					<button type="button" class="button button-secondary" id="cs-add-social">
						<span class="dashicons dashicons-plus-alt2"></span>
						<?php esc_html_e( 'Add Social Icon', 'corestarter' ); ?>
					</button>
				</p>
				<p class="description"><?php esc_html_e( 'Upload custom icons (SVG, PNG, JPG, WEBP) and enter the link for each social profile.', 'corestarter' ); ?></p>
			</div>

			<!-- ============================================================
			     Footer Tab
			     ============================================================ -->
			<div id="tab-footer" class="cs-tab-content">
				<h2><?php esc_html_e( 'Footer Settings', 'corestarter' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'Custom Footer Text', 'corestarter' ); ?></th>
						<td>
							<textarea name="corestarter_options[footer_text]"
									  rows="4" class="large-text"><?php echo esc_textarea( $opts['footer_text'] ); ?></textarea>
							<p class="description"><?php esc_html_e( 'HTML is allowed. Displays above the copyright line.', 'corestarter' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Footer Background', 'corestarter' ); ?></th>
						<td>
							<input type="text" name="corestarter_options[footer_bg_color]"
								   value="<?php echo esc_attr( $opts['footer_bg_color'] ); ?>"
								   class="cs-color-picker" data-default-color="<?php echo esc_attr( $defaults['footer_bg_color'] ); ?>">
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Back to Top', 'corestarter' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="corestarter_options[back_to_top]"
									   value="1" <?php checked( $opts['back_to_top'] ); ?>>
								<?php esc_html_e( 'Show "Back to Top" button', 'corestarter' ); ?>
							</label>
						</td>
					</tr>
				</table>
			</div>

			<!-- ============================================================
			     Tracking & Integrations Tab
			     ============================================================ -->
			<div id="tab-tracking" class="cs-tab-content">
				<h2><?php esc_html_e( 'Tracking & Integrations', 'corestarter' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'GA4 Measurement ID', 'corestarter' ); ?></th>
						<td>
							<input type="text" name="corestarter_options[ga4_id]"
								   value="<?php echo esc_attr( $opts['ga4_id'] ); ?>"
								   class="regular-text" placeholder="G-XXXXXXXXXX">
							<p class="description"><?php esc_html_e( 'Enter your Google Analytics 4 Measurement ID. The tracking script is injected automatically.', 'corestarter' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'GTM Container ID', 'corestarter' ); ?></th>
						<td>
							<input type="text" name="corestarter_options[gtm_id]"
								   value="<?php echo esc_attr( $opts['gtm_id'] ); ?>"
								   class="regular-text" placeholder="GTM-XXXXXXX">
							<p class="description"><?php esc_html_e( 'Enter your Google Tag Manager Container ID. Both the head snippet and body noscript fallback are injected automatically.', 'corestarter' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Google Maps API Key', 'corestarter' ); ?></th>
						<td>
							<input type="text" name="corestarter_options[google_maps_key]"
								   value="<?php echo esc_attr( $opts['google_maps_key'] ); ?>"
								   class="regular-text" placeholder="AIza...">
							<p class="description">
								<?php
								printf(
									/* translators: %s: Google Cloud Console URL. */
									esc_html__( 'Required for the [corestarter_map] shortcode. Get your key from the %s.', 'corestarter' ),
									'<a href="https://console.cloud.google.com/apis/credentials" target="_blank" rel="noopener noreferrer">Google Cloud Console</a>'
								);
								?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Head Scripts', 'corestarter' ); ?></th>
						<td>
							<textarea name="corestarter_options[head_scripts]"
									  rows="8" class="large-text cs-code-editor"
									  placeholder="<!-- Paste scripts/meta tags here -->"><?php echo esc_textarea( $opts['head_scripts'] ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Output in <head>. Use for verification meta tags, Facebook Pixel, etc. Include full <script> or <meta> tags.', 'corestarter' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Body Scripts', 'corestarter' ); ?></th>
						<td>
							<textarea name="corestarter_options[body_scripts]"
									  rows="8" class="large-text cs-code-editor"
									  placeholder="<!-- Paste scripts here -->"><?php echo esc_textarea( $opts['body_scripts'] ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Output right after the opening <body> tag. Use for GTM noscript fallbacks, chat widgets, etc.', 'corestarter' ); ?></p>
						</td>
					</tr>
				</table>
			</div>

			<!-- ============================================================
			     Shortcodes Reference Tab
			     ============================================================ -->
			<div id="tab-shortcodes" class="cs-tab-content">
				<h2><?php esc_html_e( 'Available Shortcodes', 'corestarter' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Copy and paste these shortcodes into any post, page, or widget. All parameters shown with their default values.', 'corestarter' ); ?></p>

				<table class="widefat striped cs-shortcode-table" style="margin-top:16px;">
					<thead>
						<tr>
							<th style="width:180px;"><?php esc_html_e( 'Shortcode', 'corestarter' ); ?></th>
							<th><?php esc_html_e( 'Usage & Parameters', 'corestarter' ); ?></th>
							<th style="width:280px;"><?php esc_html_e( 'Description', 'corestarter' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><code>[corestarter_year]</code></td>
							<td><code>[corestarter_year]</code></td>
							<td><?php esc_html_e( 'Outputs the current year. Useful in footer copyright text.', 'corestarter' ); ?></td>
						</tr>
						<tr>
							<td><code>[corestarter_button]</code></td>
							<td>
								<code>[corestarter_button url="#" text="Click Here" style="primary" target="_self"]</code>
								<br><br>
								<strong><?php esc_html_e( 'Parameters:', 'corestarter' ); ?></strong><br>
								<code>url</code> &mdash; <?php esc_html_e( 'Link URL (default: #)', 'corestarter' ); ?><br>
								<code>text</code> &mdash; <?php esc_html_e( 'Button text (default: Click Here)', 'corestarter' ); ?><br>
								<code>style</code> &mdash; <code>primary</code> | <code>outline</code> <?php esc_html_e( '(default: primary)', 'corestarter' ); ?><br>
								<code>target</code> &mdash; <code>_self</code> | <code>_blank</code> <?php esc_html_e( '(default: _self)', 'corestarter' ); ?>
							</td>
							<td><?php esc_html_e( 'Renders a styled button/link with customizable appearance.', 'corestarter' ); ?></td>
						</tr>
						<tr>
							<td><code>[corestarter_map]</code></td>
							<td>
								<strong><?php esc_html_e( 'By address:', 'corestarter' ); ?></strong><br>
								<code>[corestarter_map address="New York, NY" zoom="14" height="400px"]</code>
								<br><br>
								<strong><?php esc_html_e( 'By coordinates:', 'corestarter' ); ?></strong><br>
								<code>[corestarter_map lat="14.5995" lng="120.9842" zoom="16" height="350px"]</code>
								<br><br>
								<strong><?php esc_html_e( 'Parameters:', 'corestarter' ); ?></strong><br>
								<code>address</code> &mdash; <?php esc_html_e( 'Street address to geocode', 'corestarter' ); ?><br>
								<code>lat</code> / <code>lng</code> &mdash; <?php esc_html_e( 'Coordinates (override address if both set)', 'corestarter' ); ?><br>
								<code>zoom</code> &mdash; <?php esc_html_e( 'Zoom level 1-20 (default: 14)', 'corestarter' ); ?><br>
								<code>height</code> &mdash; <?php esc_html_e( 'Map height (default: 400px)', 'corestarter' ); ?>
							</td>
							<td>
								<?php esc_html_e( 'Embeds a Google Map. Requires a Google Maps API key set in the Tracking & Integrations tab. The Maps script is only loaded on pages that use this shortcode.', 'corestarter' ); ?>
							</td>
						</tr>
					</tbody>
				</table>

				<div style="margin-top:20px; padding:12px 16px; background:#f0f6fc; border-left:4px solid var(--wp-admin-theme-color, #2271b1); border-radius:2px;">
					<p style="margin:0;">
						<strong><?php esc_html_e( 'Adding Custom Shortcodes', 'corestarter' ); ?></strong><br>
						<?php esc_html_e( 'Place .php files in the shortcodes/ folder of your child theme. They are loaded automatically alongside the built-in shortcodes.', 'corestarter' ); ?>
					</p>
				</div>
			</div>

			<!-- ============================================================
			     Custom Code Tab
			     ============================================================ -->
			<div id="tab-custom-code" class="cs-tab-content">
				<h2><?php esc_html_e( 'Custom Code', 'corestarter' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'Custom CSS', 'corestarter' ); ?></th>
						<td>
							<textarea name="corestarter_options[custom_css]"
									  rows="12" class="large-text cs-code-editor"
									  placeholder="/* Your custom CSS */"><?php echo esc_textarea( $opts['custom_css'] ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Added to the <head> section. Do not include <style> tags.', 'corestarter' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Custom JavaScript', 'corestarter' ); ?></th>
						<td>
							<textarea name="corestarter_options[custom_js]"
									  rows="12" class="large-text cs-code-editor"
									  placeholder="// Your custom JavaScript"><?php echo esc_textarea( $opts['custom_js'] ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Added before the closing </body> tag. Do not include <script> tags.', 'corestarter' ); ?></p>
						</td>
					</tr>
				</table>
			</div>

			<?php submit_button( esc_html__( 'Save Settings', 'corestarter' ) ); ?>

		</form>
	</div>
	<?php
}
