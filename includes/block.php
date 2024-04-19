<?php

class RS_Icon_Block {
	
	/**
	 * Initialized when the plugin is loaded
	 *
	 * @return void
	 */
	public static function init() {
		
		// Register custom block types
		add_action( 'init', array( __CLASS__, 'register_block' ) );
		
		// Include the icon search library on the dashboard
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		
		// Populate the Icon dropdown with icons based on the icon metadata file
		add_filter('acf/load_field/key=field_6621640b2ed0c', array( __CLASS__, 'populate_icon_field' ) );
		
		// Populate the Color and Color 2 dropdowns with a choice of colors from the theme
		add_filter('acf/load_field/key=field_662298fdb3f48', array( __CLASS__, 'populate_color_field' ) );
		add_filter('acf/load_field/key=field_66229911b3f49', array( __CLASS__, 'populate_color_field' ) );
		
	}
	
	/**
	 * Register custom block types
	 */
	public static function register_block() {
		
		$icon_svg = file_get_contents( RS_Font_Awesome_5_PATH . '/assets/icons/rs-fa5.min.svg' );
		
		register_block_type( RS_Font_Awesome_5_PATH . '/blocks/rs-icon/block.json', array( 'icon' => $icon_svg ) );
		
	}
	
	/**
	 * Include the necessary assets for the icon search
	 *
	 * @return void
	 */
	public static function enqueue_assets() {
		
		wp_enqueue_script( 'rs-font-awesome-5-search', RS_Font_Awesome_5_URL . 'assets/icon-search.js', array(), null );
		
		wp_enqueue_style( 'rs-font-awesome-5-search', RS_Font_Awesome_5_URL . 'assets/icon-search.css' );
		
	}
	
	/**
	 * Populate the Icon dropdown with icons based on the icon metadata file
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public static function populate_icon_field( $field ) {
		// Do not populate on the field group screen or when exporting
		if ( acf_is_screen('acf-field-group') ) return $field;
		if ( acf_is_screen('acf_page_acf-tools') ) return $field;
		
		// Do not populate the front-end
		if ( ! is_admin() ) return $field;
		
		// Get the icon data
		$icon_data = RS_Font_Awesome_5_Icons::get_data();
		
		$field['choices'] = array();
		
		// Loop through the icon data and add each icon to the field choices
		foreach( $icon_data as $icon ) {
			$key = $icon['key'];
			$search = $icon['search'];
			$styles = $icon['styles'];
			// $unicode = $icon['unicode'];
			
			$icon_html = RS_Font_Awesome_5_Icons::get_icon_html( $key, $styles[0] );
			
			$icon_name = ucwords( str_replace( '-', ' ', $key ) );
			
			$field['choices'][ $key ] =
				'<div class="rs-fa5-icon">' .
				'<div class="icon-tag">' .
				$icon_html .
				'</div>' .
				'<div class="icon-name">' .
				$icon_name .
				'</div>' .
				'<div class="icon-tags screen-reader-text">' .
				implode(', ', $search ) .
				'</div>' .
				'</div>';
		}
		
		return $field;
	}
	
	/**
	 * Populate the Color and Color 2 dropdowns with a choice of colors from the theme
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	public static function populate_color_field( $field ) {
		// Do not populate on the field group screen or when exporting
		if ( acf_is_screen('acf-field-group') ) return $field;
		if ( acf_is_screen('acf_page_acf-tools') ) return $field;
		
		// Do not populate the front-end
		if ( ! is_admin() ) return $field;
		
		$colors = RS_Font_Awesome_5_Settings::get_theme_colors();
		
		// Add ACF choices for each color
		$field['choices'] = array();
		
		foreach( $colors as $c ) {
			$color = $c['color'];
			$name = $c['name'];
			$slug = $c['slug'];
			
			$field['choices'][ $slug ] =
				'<div class="rs-fa5-color">' .
				'<div class="color-tag" style="background: '. esc_attr($color) .';"></div>' .
				'<div class="color-name">' . $name . ' <span class="color-value">('.esc_html($color).')</span></div>' .
				'<div class="color-tags screen-reader-text">' . $slug . ' ' . $color . '</div>' .
				'</div>';
		}
		
		return $field;
	}
	
}

RS_Icon_Block::init();