<?php

class RS_Font_Awesome_5_Icons {
	
	// Configuration - Override in child classes
	protected static $icon_data_path = '/assets/metadata/font-awesome-5.json';
	protected static $shortcode_name = 'fa5';
	protected static $default_style = 'regular';
	
	// Internal variables
	protected static $icon_data = null;
	
	/**
	 * Get the default style
	 *
	 * @return string
	 */
	public static function get_default_style() {
		return self::$default_style;
	}
	
	/**
	 * Get the icon HTML for a single icon
	 *
	 * @param string        $key     The icon key to display
	 * @param string|false  $style   The style of the icon
	 * @param string|false  $title   The title to show on hover. If false, will be treated as decorative (aria-hidden=false)
	 * @param string|false  $color   The color slug from theme.json
	 * @param string|false  $color_2 The second color, only used for duotone icons
	 * @param array         $atts    Additional attributes to add to the icon element
	 *
	 * @return string
	 */
	public static function get_icon_html( $key, $style = false, $title = false, $color = false, $color_2 = false, $atts = array() ) {
		if ( ! is_array($atts) ) $atts = array();
		
		// Attribute: class
		if ( ! isset($atts['class']) ) $atts['class'] = array();
		else if ( ! is_array($atts['class']) ) $atts['class'] = explode(' ', $atts['class'] );
		
		$icon_classes = self::get_icon_classes( $key, $style, $color, $color_2 );
		
		$atts['class'] = array_merge( $atts['class'], $icon_classes );
		$atts['class'] = implode( ' ', $atts['class'] );
		$atts['class'] = trim($atts['class']);
		
		// Attribute: title or aria-hidden
		if ( $title ) {
			$atts['title'] = $title;
		}else{
			$atts['aria-hidden'] = 'true';
		}
		
		// Create the HTML element
		$html = '<i';
		
		// Add attributes
		foreach( $atts as $name => $value ) {
			$html .= ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
		}
		
		$html .= '></i>';
		
		return $html;
	}
	
	/**
	 * Get the classes used for an icon element
	 *
	 * @param string $key               The icon key to display
	 * @param string|false $style       The style of the icon
	 * @param string|false $color       The color slug from theme.json
	 * @param string|false $color_2     The second color, only used for duotone icons
	 * @param array $additional_classes Classes to add to the icon
	 *
	 * @return array
	 */
	public static function get_icon_classes( $key, $style, $color, $color_2, $additional_classes = array() ) {
		if ( ! $style ) $style = self::$default_style;
		
		// Use the first style if the icon doesn't support the requested style
		$data = self::get_single_icon_data( $key );
		if ( $data && ! in_array( $style, $data['styles'] ) ) {
			$style = $data['styles'][0];
		}
		
		switch( $style ) {
			case 'solid':
				$prefix = 'fas';
				break;
			case 'brands':
				$prefix = 'fab';
				break;
			case 'light':
				$prefix = 'fal';
				break;
			case 'duotone':
				$prefix = 'fad';
				break;
			case 'regular':
			default:
				$prefix = 'far';
				break;
		}
		
		$classes = array( 'rs-fa', $prefix, 'fa-' . $key );
		$classes = array_merge( $classes, $additional_classes );
		$classes = array_map( 'sanitize_html_class', $classes );
		
		// Add colors as classes
		if ( $color ) {
			$classes[] = 'fa5-primary-color-' . esc_attr(sanitize_html_class( $color ));
		}
		
		if ( $color_2 ) {
			$classes[] = 'fa5-secondary-color-' . esc_attr(sanitize_html_class( $color_2 ));
		}
		
		return $classes;
	}
	
	/**
	 * Get the shortcode to embed a single icon
	 *
	 * @param string $key
	 * @param string $style
	 * @param string[] $options
	 *
	 * @return string
	 */
	public static function get_icon_shortcode( $key, $style ) {
		
		$shortcode = '['. self::$shortcode_name . ' icon="' . $key . '"';
		if ( $style && $style !== self::$default_style ) $shortcode .= ' style="' . $style . '"';
		$shortcode .= ']';
		
		return $shortcode;
	}
	
	/**
	 * Get an array of icon data
	 *
	 * @return array[] {
	 *     @type string   $key The key identifying the icon
	 *     @type string[] $search An array of search terms for the icon
	 *     @type string[] $styles An array of styles supported by this icon
	 *     @type string   $unicode The unicode character for the icon
	 * }
	 */
	public static function get_data() {
		if ( self::$icon_data === null ) {
			self::$icon_data = self::get_cached_icon_data();
		}
		
		if ( self::$icon_data === null ) {
			self::$icon_data = self::load_icon_data_from_json();
		}
		
		return self::$icon_data;
	}
	
	/**
	 * Set the icon data for the icon search
	 *
	 * @return array[]|null
	 */
	public static function get_cached_icon_data() {
		return wp_cache_get( 'rs-font-awesome-5-icon-data' ) ?: null;
	}
	
	/**
	 * Set the icon data for the icon search
	 *
	 * @param array[] $data
	 */
	public static function store_icon_data_in_cache( $data ) {
		wp_cache_set( 'rs-font-awesome-5-icon-data', $data );
	}
	
	/**
	 * Load icon metadata file from a json file
	 *
	 * @see self::get_data()
	 *
	 * @return array[]
	 */
	protected static function load_icon_data_from_json() {
		$json_path = RS_Font_Awesome_5_PATH . self::$icon_data_path;
		if ( ! file_exists(  $json_path ) ) return array();
		
		$json = file_get_contents( $json_path );
		
		$data = json_decode( $json, true );
		
		self::store_icon_data_in_cache( $data );
		
		return $data ?: array();
	}
	
	/**
	 * Get the data for a single icon
	 *
	 * @param $key
	 *
	 * @return array|false
	 */
	public static function get_single_icon_data( $key ) {
		$data = self::get_data();
		
		return isset($data[$key]) ? $data[$key] : false;
	}
	
}