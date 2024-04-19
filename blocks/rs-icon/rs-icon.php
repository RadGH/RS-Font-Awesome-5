<?php

/**
 * @global   array $block The block settings and attributes.
 * @global   string $content The block inner HTML (empty).
 * @global   bool $is_preview True during backend preview render.
 * @global   int $post_id The post ID the block is rendering content against.
 *           This is either the post ID currently being displayed inside a query loop,
 *           or the post ID of the post hosting this block.
 * @global   array $context The context provided to the block by the post or it's parent block.
 */

// Todo list:
// Choose icon version
// Choose icon style
// Choose icon type
// Color
// Scaling (Font size or pixels)

$icon_key = get_field( 'icon', $block['id'] ); // star, user, etc.
$style = get_field( 'style', $block['id'] ); // regular, solid, light, duotone, brands
$title = get_field( 'title', $block['id'] ); // title attribute

$color = get_field( 'color', $block['id'] ); // Color slug from theme.json
$color_2 = get_field( 'color_2', $block['id'] ); // Second color, only used for duotone icons

// Get block attributes to add to the element
$atts = WP_Block_Supports::get_instance()->apply_block_supports();

// Remove class: "wp-block-rs-font-awesome-5-rs-icon"
if ( isset($atts['class']) ) {
	$atts['class'] = str_replace( 'wp-block-rs-font-awesome-5-rs-icon', '', $atts['class'] );
}

// If not yet specified, use the "flag-alt" as default
if ( ! $icon_key && $is_preview ) {
	$icon_key = 'flag-alt';
}

echo RS_Font_Awesome_5_Icons::get_icon_html( $icon_key, $style, $title, $color, $color_2, $atts );