<?php
/**
Plugin Name: Flat Bootstrap
Plugin URI: http://xtremelysocial.com/wordpress/flat-plugin/
Description: This plugin adds enhancements to our various Flat Bootstrap themes as well as any Bootstrap-based theme. Features include: "Quicktags" to easily add Bootstrap HTML for various components, such as buttons, carousels, etc; XYZ...
Author: Tim Nicholson
Version: 1.0
Author URI: http://xtremelysocial.com
**/

/**
 * SET PLUGIN OPTIONS HERE
 *
 * These can be overrided in your own plugin or theme's function.php file by setting
 * values in $xsbf_plugin_options. Your values will be merged with the defaults.
 * 
 * Parameters:
 * css - Full path to the CSS file to style the plugin features. Only loads if one of our
 * 		Flat Bootstrap themes's is not being used.
 * 
 * quicktags - Add quicktags to the WordPress editor.
 * 
 * widget-section - Load our colored section / call-to-action widget
 * 
 * widget-columns - Load our columns / icon widget
 */
$xsbf_plugin_defaults = array(
	//////'css'				=> 'css/theme-flat.css',
	//////'css'				=> null,
	/////'quicktags'			=> false,
	'widget-section'	=> true,
	'widget-columns'	=> true
);
if ( isset( $xsbf_plugin_options ) ) {
	$xsbf_plugin_options = wp_parse_args( $xsbf_plugin_options, $xsbf_plugin_defaults );
} else {
	$xsbf_plugin_options = $xsbf_plugin_defaults;
}

/*
 * Load our plugin's stylesheet unless its already loaded by one of our themes
 */
/*
if ( $xsbf_plugin_options['css'] AND ! function_exists ( 'xsbf_scripts' ) ) {

	// Add our plugin's stylesheet to the front-end. The '20' ensures this runs
	// after the theme's setup routines.
	add_action( 'wp_enqueue_scripts', 'xsbf_plugin_styles', 20 );
	function xsbf_plugin_styles() {
		//if ( ! wp_style_is( 'theme-flat', 'enqueued' ) ) {
			wp_register_style( 'theme-flat', plugins_url( 'flat-bootstrap/css/theme-flat.css' ), $deps = null, '20150114', 'all' );
			wp_enqueue_style( 'theme-flat');
		//}
	}
	
	// Add our plugin's stylesheet to the backend (WordPress editor)
	add_action( 'after_setup_theme', 'xsbf_plugin_setup_theme', 20 );
	function xsbf_plugin_setup_theme() {
		add_editor_style( plugins_url( 'flat-bootstrap/css/theme-flat.css' ) );
	}
	
}
*/

/*
 * Load our quicktags for the WordPress editor
 */
/*
if ( $xsbf_plugin_options['quicktags'] ) {
	include_once( 'quicktags.php' );
}
*/

/*
 * Load our colored section widget and [flat_bootstrap_section] shortcode
 */
if ( $xsbf_plugin_options['widget-section'] ) {
	include_once( 'xsbf-widget-section.php' );
}

/*
 * Load our icon columns widget and [flat_bootstrap_columns] shortcode
 */
if ( $xsbf_plugin_options['widget-columns'] ) {
	include_once( 'xsbf-widget-columns.php' );
}
