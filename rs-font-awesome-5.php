<?php
/*
Plugin Name: RS Font Awesome 5
Description: Enables your theme to use icons from Font Awesome 5. Allows you to include Font Awesome scripts by official kit, or direct font files, or by deferring to another plugin. Icons can be inserted using the Font Awesome 5 Icon block or the [fa5] shortcode.
Version: 1.0.1
Author: Radley Sustaire
Author URI: https://radleysustaire.com
GitHub Plugin URI: https://github.com/RadGH/RS-Font-Awesome-5
*/

define( 'RS_Font_Awesome_5_PATH', __DIR__ );
define( 'RS_Font_Awesome_5_URL', plugin_dir_url(__FILE__) );
define( 'RS_Font_Awesome_5_VERSION', '1.0.1' );

class RS_Font_Awesome_5 {
	
	/**
	 * Checks that required plugins are loaded before continuing
	 *
	 * @return void
	 */
	public static function load_plugin() {
		// 1. Check for required plugins
		$missing_plugins = array();
		
		if ( ! class_exists('ACF') ) {
			$missing_plugins[] = 'Advanced Custom Fields Pro';
		}
		
		// Show error on the dashboard if any plugins are missing
		if ( $missing_plugins ) {
			self::add_admin_notice( '<strong>RS Font Awesome 5:</strong> The following plugins are required: '. implode(', ', $missing_plugins) . '.', 'error' );
			return;
		}
		
		// 2. Add settings link to the plugins page
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(__CLASS__, 'add_settings_link') );
		
		// 3. Load plugin files
		require_once( RS_Font_Awesome_5_PATH . '/includes/setup.php' );
		require_once( RS_Font_Awesome_5_PATH . '/includes/settings.php' );
		require_once( RS_Font_Awesome_5_PATH . '/includes/font-awesome-icons.php' );
		require_once( RS_Font_Awesome_5_PATH . '/includes/block.php' );
		
		// 4. Load shortcodes
		require_once( RS_Font_Awesome_5_PATH . '/shortcodes/fa5.php' );
		require_once( RS_Font_Awesome_5_PATH . '/shortcodes/fa5_icon_list.php' );
		
		// 5. Load ACF fields
		require_once( RS_Font_Awesome_5_PATH . '/assets/acf-fields/fields.php' );
		
	}
	
	/**
	 * Adds an admin notice to the dashboard's "admin_notices" hook.
	 *
	 * @param string $message The message to display
	 * @param string $type    The type of notice: info, error, warning, or success. Default is "info"
	 * @param bool $format    Whether to format the message with wpautop()
	 *
	 * @return void
	 */
	public static function add_admin_notice( $message, $type = 'info', $format = true ) {
		add_action( 'admin_notices', function() use ( $message, $type, $format ) {
			?>
			<div class="notice notice-<?php echo $type; ?> rs-font-awesome-5-notice">
				<?php echo $format ? wpautop($message) : $message; ?>
			</div>
			<?php
		});
	}
	
	/**
	 * Adds a settings link to the plugins page
	 *
	 * @param array $links The existing links
	 *
	 * @return array
	 */
	public static function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=rs-font-awesome-5">Settings</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
	
}

// Initialize the plugin
add_action( 'plugins_loaded', array('RS_Font_Awesome_5', 'load_plugin') );
