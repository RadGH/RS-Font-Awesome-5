<?php

/**
 * Displays a table of icons.
 *
 * @param $atts
 * @param $content
 * @param $shortcode_name
 *
 * @return false|string
 */
function shortcode_fa5_icon_list( $atts, $content = '', $shortcode_name = 'fa5_icon_list' ) {
	$atts = shortcode_atts(array(
		'style' => null,  // solid, regular, light, thin, duotone, brands
	), $atts, $shortcode_name);
	
	$style = $atts['style'] ?: false;
	
	$icon_data = RS_Font_Awesome_5_Icons::get_data();
	
	// If style is provided, filter the results by style
	if ( $style ) {
		$icon_data = array_filter($icon_data, function($icon) use ($style) {
			return in_array( $style, $icon['styles'] );
		});
	}
	
	ob_start();
	?>
	<div class="rs-font-awesome-5-display-list">
		<?php
		foreach ( $icon_data as $icon ) {
			$icon_html = RS_Font_Awesome_5_Icons::get_icon_html( $icon['key'], $style );
			$icon_shortcode = RS_Font_Awesome_5_Icons::get_icon_shortcode( $icon['key'], $style );
			$data = RS_Font_Awesome_5_Icons::get_single_icon_data( $icon['key'] );
			
			$search_terms = $data['search'];
			// Remove the icon name from the search terms
			$search_terms = array_diff( $search_terms, array($icon['key']) );
			
			?>
			<div class="rs-icon">
				
				<div class="rs-icon--icon"><?php echo $icon_html; ?></div>
				
				<?php
				// Icon name and (hidden) search terms
				if ( $search_terms ) {
					?>
					<details class="rs-icon--name">
						<summary><?php echo esc_html($icon['key']); ?></summary>
						<div class="rs-icon--search">
							<div class="rs-icon--search-label">Keywords:</div>
							<div class="rs-icon--search-items">
								<span><?php echo implode('</span> <span>', $search_terms ); ?></span>
							</div>
						</div>
					</details>
					<?php
				}else{
					?>
					<div class="rs-icon--name"><?php echo esc_html($icon['key']); ?></div>
					<?php
				}
				?>
				
				<div class="rs-icon--actions">
					<div class="rs-icon--copy-label">Copy:</div>
					<button type="button" class="rs-icon--copy-link" data-copy="<?php echo esc_html($icon['key']); ?>">Name</button>
					<button type="button" class="rs-icon--copy-link" data-copy="<?php echo esc_html($icon_shortcode); ?>">Shortcode</button>
					<button type="button" class="rs-icon--copy-link" data-copy="<?php echo esc_html($icon_html); ?>">HTML</button>
				</div>
				
			</div>
			<?php
		}
		?>
	</div>
	<?php
	$html = ob_get_clean();
	
	return $html;
}
add_shortcode( 'fa5_icon_list', 'shortcode_fa5_icon_list' );