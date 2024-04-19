<?php

/**
 * Displays a single icon
 *
 * @param $atts
 * @param $content
 * @param $shortcode_name
 *
 * @return string
 */
function shortcode_fa5( $atts, $content = '', $shortcode_name = 'rs_icon' ) {
	$atts = shortcode_atts(array(
		'0' => null,       // [fa5 star] = star; star, user, etc.
		'icon' => null,    // [fa5 icon="star"] = star; star, user, etc.
		
		'style' => null,   // [fa5 star style="solid"] = regular; regular, solid, light, duotone, brands
		'title' => null,   // [fa5 user title="My Account"] = Title to show on hover. If empty, will be treated as decorative (aria-hidden=true)
		
		'color' => null,   // Color slug from theme.json
		'color_2' => null, // Second color, only used for duotone icons
		
		'type' => null,    // Alias of icon
		'name' => null,    // Alias of icon
		'color2' => null,  // Alias of color_2
	), $atts, $shortcode_name);
	
	$icon = $atts['icon'] ?: false;
	$style = $atts['style'] ?: false;
	$title = $atts['title'] ?: false;
	
	$color = $atts['color'] ?: false;
	$color_2 = $atts['color_2'] ?: false;
	
	// Aliases
	if ( ! $color_2 && $atts['color2'] ) $color_2 = $atts['color2'];
	
	// If icon is missing, check for [0] which is the first parameter without a key
	// These are valid uses of the shortcode:
	// [fa5 star]
	// [fa5 icon="star"]
	// [fa5 name="star"]
	if ( ! $icon && isset($atts['0']) ) $icon = $atts[0];
	if ( ! $icon && isset($atts['name']) ) $icon = $atts['name'];
	if ( ! $icon && isset($atts['type']) ) $icon = $atts['type'];
	
	// Show warning if invalid icon was selected
	if ( ! $icon ) {
		if ( current_user_can('edit_pages' ) ) {
			return '(fa5 shortcode error: No icon selected)';
		}else{
			return '';
		}
	}
	
	return RS_Font_Awesome_5_Icons::get_icon_html( $icon, $style, $title, $color, $color_2 );
}
add_shortcode( 'fa5', 'shortcode_fa5' );

// Add generic [fa] shortcode as a replacement for other plugins
if ( ! shortcode_exists('fa') ) {
	add_shortcode( 'fa', 'shortcode_fa5' );
}

// Add generic [icon] shortcode as a replacement for other plugins
if ( ! shortcode_exists('icon') ) {
	add_shortcode( 'icon', 'shortcode_fa5' );
}