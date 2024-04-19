<?php

class RS_Font_Awesome_5_Settings {
	
	/**
	 * Initialized when the plugin is loaded
	 *
	 * @return void
	 */
	public static function init() {
		
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_local_fonts' ) );
		
		add_action( 'wp_print_scripts', array( __CLASS__, 'enqueue_local_fonts' ) );
	
	}
	
	/**
	 * Check if font awesome is enabled in the settings
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return get_field( 'fa_enabled', 'rs_icons' );
	}
	
	/**
	 * Checks if a style is enabled in the settings.
	 * Available styles: regular, solid, brands, light, duotone
	 *
	 * @param $style
	 *
	 * @return false
	 */
	public static function is_style_enabled( $style ) {
		if ( self::is_enabled() ) {
			$settings = get_field( 'fa_settings', 'rs_icons' );
			$styles = $settings['fa_5_styles'] ?: array();
			return in_array( $style, $styles );
		}
		return false;
	}
	
	
	/**
	 * Check if we should include support for font awesome 4 syntax
	 *
	 * @return bool
	 */
	public static function is_version_4_supported( $version ) {
		if ( self::is_enabled() ) {
			$settings = get_field( 'fa_settings', 'rs_icons' );
			return !empty( $settings['support_version_4'] );
		}
		return false;
	}
	
	/**
	 * Returns an array of styles supported by Font Awesome 5
	 *
	 * @return array[ $style ] {
	 *     @type string $style The style of Font Awesome to include
	 *     @type string $css   The CSS file to include. (If all styles are enabled, uses all.css instead)
	 *     @type array $font   The font properties to use when including a font file as a web font
	 * }
	 */
	public static function get_styles() {
		return array(
			'regular' => array(
				'style' => 'regular',
				'css' => 'regular.css',
				'font' => array(
					'font-family' => 'Font Awesome 5 Pro',
					'font-weight' => 400,
				),
			),
			'solid' => array(
				'style' => 'solid',
				'css' => 'solid.css',
				'font' => array(
					'font-family' => 'Font Awesome 5 Pro',
					'font-weight' => 900,
				),
			),
			'brands' => array(
				'style' => 'brands',
				'css' => 'brands.css',
				'font' => array(
					'font-family' => 'Font Awesome 5 Brands',
					'font-weight' => 400,
				),
			),
			'light' => array(
				'style' => 'light',
				'css' => 'light.css',
				'font' => array(
					'font-family' => 'Font Awesome 5 Pro',
					'font-weight' => 300,
				),
			),
			'duotone' => array(
				'style' => 'duotone',
				'css' => 'duotone.css',
				'font' => array(
					'font-family' => 'Font Awesome 5 Duotone',
					'font-weight' => 900,
				),
			),
		);
	}
	
	/**
	 * Enqueue the Font Awesome CSS uploaded through the settings screen
	 *
	 * @return void
	 */
	public static function enqueue_local_fonts() {
		if ( ! self::is_enabled() ) return;
		
		$settings = get_field( 'fa_settings', 'rs_icons' );
		if ( ! $settings ) return;
		
		$kits = array();
		$fonts = array();
		
		$styles = self::get_styles();
		
		// Include the CSS for this version of font awesome
		self::include_font_awesome_css( $styles );
		
		// Get variations supported for this version
		// Example: $styles_supported = ["regular", "solid", "brands"]
		$styles_supported = $settings['fa_5_styles'] ?? array();
		
		foreach( $styles as $style => $style_options ) {
			
			// Check if this style is supported
			if ( ! self::is_style_enabled( $style ) ) continue;
			
			$include_method = $settings['fa_5_'.$style]['include_method'] ?? false;
			$font_kit = $settings['fa_5_'.$style]['font_kit'] ?? false;
			$woff2_file = $settings['fa_5_'.$style]['woff2_file'] ?? false;
			
			if ( $include_method === 'kit' ) {
				// Include a font kit like a script
				$kits[ $style ] = $font_kit;
			}else if ( $include_method === 'font' ) {
				$fonts[ $style ] = $woff2_file;
			}
		}
		
		if ( $kits ) self::output_kits( $kits );
		
		if ( $fonts ) self::output_fonts( $fonts );
		
		self::output_icon_colors();
	}
	
	/**
	 * Include the Font Awesome CSS for a specific version and style
	 *
	 * @param string[] $styles
	 *
	 * @return void
	 */
	private static function include_font_awesome_css( $styles ) {
		$style_config = self::get_styles();
		$v = '5.15.4';
		
		$settings = get_field( 'fa_settings', 'rs_icons' );
		$include_all_styles = $settings['include_all_styles'] ?? false;
		
		if ( count($styles) == count($style_config) || $include_all_styles ) {
			
			// Include all styles
			$url = RS_Font_Awesome_5_URL . 'assets/font-awesome/all.css';
			$url = self::maybe_minify($url);
			wp_enqueue_style( 'font-awesome-5', $url, array(), $v );
			
		}else{
			
			// Include core styles
			$url = RS_Font_Awesome_5_URL . 'assets/font-awesome/fontawesome.css';
			$url = self::maybe_minify($url);
			wp_enqueue_style( 'font-awesome-5-core', $url, array(), $v );
			
			// Include individual styles per selected style
			foreach( $styles as $style ) {
				if ( ! isset($style_config[ $style ]) ) continue;
				$css = $style_config[ $style ]['css'];
				$url = RS_Font_Awesome_5_URL . 'assets/font-awesome/' . $css;
				$url = self::maybe_minify($url);
				wp_enqueue_style( 'font-awesome-5-' . $style, $url, array(), $v );
			}
			
		}
		
		// Include font awesome 4 support shim?
		// This is needed even if using "all.css"
		if ( self::is_version_4_supported( 4 ) ) {
			$url = RS_Font_Awesome_5_URL . 'assets/font-awesome/v4-shims.css';
			$url = self::maybe_minify($url);
			wp_enqueue_style( 'font-awesome-4-shim', $url, array(), '4.7.0' );
		}
	}
	
	/**
	 * If minified files are enabled, replaces ".css" with ".min.css" for the given url or path
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	private static function maybe_minify( $file ) {
		$settings = get_field( 'fa_settings', 'rs_icons' );
		
		// If minify is enabled, and not already minified, replace ".css" with ".min.css"
		if ( $settings && $settings['minify'] ) {
			if ( ! str_contains($file, '.min.css') ) {
				$file = str_replace('.css', '.min.css', $file);
			}
		}
		
		return $file;
	}
	
	/**
	 * Enqueue a Font Awesome kit (script tags)
	 *
	 * @param array[] $kits Key is the style, value is the kit URL to include
	 *
	 * @return void
	 */
	private static function output_kits( $kits ) {
		foreach( $kits as $style => $kit ) {
			
			echo "\n";
			echo '<!-- RS  Font Awesome 5 - ' . esc_html($style) . ' -->';
			echo "\n";
			echo $kit;
			echo "\n";
		}
	}
	
	/**
	 * Enqueue a Font Awesome kit (script tags)
	 *
	 * @param array[] $fonts Key is the style, value is the WOFF2 file to include
	 *
	 * @return void
	 */
	private static function output_fonts( $fonts ) {
		foreach( $fonts as $style => $woff2_file ) {
			if ( is_array($woff2_file) ) {
				$font_url = $woff2_file['url'];
			}else if ( is_numeric($woff2_file) ) {
				$font_url = wp_get_attachment_url( $woff2_file );
			}
			
			$font_settings = array_merge(array(
				'font-family' => 'Undefined',
				'font-weight' => 400,
				'font-style' => 'normal',
				'font-display' => 'block',
				'src' => sprintf("url(%s) format('woff2')", esc_html($font_url) )
			), self::get_font_details( $style ));
			
			echo "\n";
			echo '<style id="rs-font-awesome-5--' . esc_attr($style) .'">';
			echo "\n";
			echo "@font-face {";
			echo "\n";
			foreach( $font_settings as $k => $v ) {
				if ( $k == 'font-family' ) $v = "'" . $v . "'";
				echo '    ' . $k . ': ' . $v . ";\n";
			}
			echo "\n";
			echo "}";
			echo "\n";
			echo '</style>';
		}
	}
	
	/**
	 * Get the font family and weight for a specific style of Font Awesome
	 *
	 * @param string $style The style to check. Supports 'solid', 'regular', 'light', 'duotone', 'brands'.
	 *
	 * @return array {
	 *      @type string $font-family The font family name
	 *      @type int $font-weight The font weight
	 * }
	 */
	private static function get_font_details( $style ) {
		$styles = self::get_styles();
		
		if ( isset($styles[ $style ]) ) {
			return $styles[ $style ]['font'];
		}else{
			return array(
				'font-family' => 'Font Awesome Unknown',
				'font-weight' => 400,
			);
		}
	}
	
	
	/**
	 * Outputs color classes based on theme.json color palette
	 *
	 * @return void
	 */
	public static function output_icon_colors() {
		$colors = self::get_theme_colors();
		
		// Primary colors:
		// fa5-has-color fa5-color-{SLUG}
		
		// Secondary colors:
		// fa5-has-secondary-color fa5-secondary-color-{SLUG}
		
		echo "\n";
		echo '<style id="rs-font-awesome-5--colors">';
		echo "\n";
		foreach( $colors as $color ) {
			$name = $color['name'];
			$slug = $color['slug'];
			$color = $color['color'];
			?>
			/* Color: <?php echo esc_html($name); ?> */
			.fa5-primary-color-<?php echo esc_attr($slug); ?> {
				color: <?php echo esc_attr($color); ?>;
				--fa-primary-color: <?php echo esc_attr($color); ?>;
			}
			
			.fa5-secondary-color-<?php echo esc_attr($slug); ?> {
				--fa-secondary-color: <?php echo esc_attr($color); ?>;
			}
			<?php
		}
		echo "\n";
		echo '</style>';
	}
	
	/**
	 * Get the color palette from the theme
	 *
	 * @return array
	 */
	public static function get_theme_colors() {
		// Get the color palette from theme.json
		if ( function_exists('wp_get_global_settings') ) {
			$settings = wp_get_global_settings();
			$colors = $settings['color']['palette']['theme'];
		}else{
			$colors = array();
		}
		
		// Allow plugins to modify color options
		$colors = apply_filters( 'rs_fa5/colors', $colors );
		
		return $colors;
	}
	
	/**
	 * Get the color data based on the color's slug from theme.json
	 * @param string $slug
	 * @return array|false
	 */
	public static function get_theme_color( $slug ) {
		foreach( self::get_theme_colors() as $c ) {
			if ( $c['slug'] === $slug ) return $c;
		}
		
		return false;
	}
	
}

RS_Font_Awesome_5_Settings::init();