<?php

class RS_Font_Awesome_5_Setup {
	
	/**
	 * Initialized when the plugin is loaded
	 *
	 * @return void
	 */
	public static function init() {
		
		// Register (but do not enqueue) CSS and JS files
		add_action( 'init', array( __CLASS__, 'register_all_assets' ) );
		
		// Enqueue assets on the dashboard.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		
		// Enqueue assets on the front-end.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_public_assets' ) );
		
		// Enqueue scripts for the block editor
		add_action( 'enqueue_block_assets', array( __CLASS__, 'enqueue_block_assets' ) );
		
		// Add an ACF options page under Settings > RS Utility Blocks
		add_action( 'acf/init', array( __CLASS__, 'add_acf_options_page' ) );
		
		// Include ACF fields
		add_action( 'acf/init', array( __CLASS__, 'add_acf_fields' ) );
		
		// Allow WordPress to upload WOFF2 and SVG files
		add_filter( 'upload_mimes', array( __CLASS__, 'allow_woff2_svg_uploads_mimes' ) );
		
		// Allow WOFF2 and SVG files to be uploaded
		add_filter( 'wp_check_filetype_and_ext', array( __CLASS__, 'allow_woff_svg_uploads_ext' ), 10, 4 );
		
	}
	
	/**
	 * Enqueue public scripts (theme/front-end)
	 *
	 * @return void
	 */
	public static function register_all_assets() {
		
		// Global CSS
		wp_register_style( 'rs-font-awesome-5', RS_Font_Awesome_5_URL . 'assets/rs-font-awesome-5.css', array(), RS_Font_Awesome_5_VERSION );
		
		// Global JS
		wp_register_script( 'rs-font-awesome-5-public', RS_Font_Awesome_5_URL . 'assets/public.js', array(), RS_Font_Awesome_5_VERSION, true );
		
		// Admin CSS
		wp_register_style( 'rs-font-awesome-5-admin', RS_Font_Awesome_5_URL . 'assets/admin.css', array(), RS_Font_Awesome_5_VERSION );
		wp_register_script( 'rs-font-awesome-5-admin', RS_Font_Awesome_5_URL . 'assets/admin.js', array(), RS_Font_Awesome_5_VERSION );
		
	}
	
	/**
	 * Enqueue assets on the wordpress dashboard (backend).
	 *
	 * @return void
	 */
	public static function enqueue_admin_assets() {
		
		wp_enqueue_style( 'rs-font-awesome-5' );
		
		wp_enqueue_style( 'rs-font-awesome-5-admin' );
		wp_enqueue_script( 'rs-font-awesome-5-admin' );
		
	}
	
	/**
	 * Enqueue assets on the front-end.
	 *
	 * @return void
	 */
	public static function enqueue_public_assets() {
		
		wp_enqueue_style( 'rs-font-awesome-5' );
		
		wp_enqueue_script( 'rs-font-awesome-5-public' );
		
	}
	
	/**
	 * Enqueue block editor assets, wherever blocks are used
	 *
	 * @return void
	 */
	public static function enqueue_block_assets() {
		
		wp_enqueue_style( 'rs-font-awesome-5' );
		
		wp_enqueue_script( 'rs-font-awesome-5-block-editor' );
		
	}
	
	/**
	 * Add an ACF options page under Settings > RS Utility Blocks
	 */
	public static function add_acf_options_page() {
		if ( ! function_exists('acf_add_options_sub_page') ) return;
		
		acf_add_options_sub_page( array(
			'parent_slug' => 'options-general.php',
			'page_title' => 'RS Font Awesome 5 – Settings',
			'menu_title' => 'RS Font Awesome 5',
			'menu_slug' => 'rs-font-awesome-5',
			'post_id' => 'rs_icons', // $s = get_field( 's', 'rs_icons' );
			'capability' => 'manage_options',
			'redirect' => false
		) );
	}
	
	/**
	 * Add an ACF options page under Settings > RS Utility Blocks
	 */
	public static function add_acf_fields() {
		
		// require_once( RS_Font_Awesome_5_PATH . '/includes/acf-fields.php' );
		
	}
	
	/**
	 * Allow WordPress to upload WOFF2 and SVG files
	 *
	 * @param array $mimes The existing mime types
	 *
	 * @return array
	 */
	public static function allow_woff2_svg_uploads_mimes( $mimes ) {
		$mimes['woff2'] = 'font/woff2';
		$mimes['svg'] = 'image/svg+xml';
		
		return $mimes;
	}
	
	/**
	 * Allow WOFF2 and SVG files to be uploaded
	 *
	 * @param array $data
	 * @param string $file
	 * @param string $filename
	 * @param array $mimes
	 *
	 * @return array
	 */
	public static function allow_woff_svg_uploads_ext( $data, $file, $filename, $mimes ) {
		$filetype = wp_check_filetype( $filename, $mimes );
		
		if ( $filetype['ext'] == 'woff2' && $filetype['type'] == 'font/woff2' ) {
			$data['ext'] = 'woff2';
			$data['type'] = 'font/woff2';
		}
		
		if ( $filetype['ext'] == 'svg' && $filetype['type'] == 'image/svg+xml' ) {
			$data['ext'] = 'svg';
			$data['type'] = 'image/svg+xml';
		}
		
		return $data;
	}
	
	
	
}

RS_Font_Awesome_5_Setup::init();