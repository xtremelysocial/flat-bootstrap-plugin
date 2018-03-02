<?php
/**
Plugin Name: Flat Bootstrap Widgets
Plugin URI:  http://xtremelysocial.com/wordpress/flat-bootstrap-widgets/
Description: Add awesome colored backgrounds to your widgets with various padding options to make your widget areas look great.
Author: 	   Tim Nicholson
Author URI:  http://xtremelysocial.com
Version: 	   1.0
License: 	   GNU General Public License
License URI: http://www.opensource.org/licenses/GPL-3.0

Flat Bootstrap Plugin, Copyright (C) 2018 XtremelySocial
Flat Bootstrap Plugin is licensed under the GPL.
See readme.txt file for license information on components used in this plugin.
*/

/**
 * SET PLUGIN OPTIONS
 *
 * These can be overrided in your own plugin or theme's function.php file by setting
 * values in $xsbf_plugin_options. Your values will be merged with the defaults.
 * 
*/
$xsbf_plugin_defaults = array(

	// Full file names (including .php) of modules within this plugin to include
	'modules' => array (
		'xsbf-widget-css.php',
		//'xsbf-widget-section.php', // now use standard text box instead
		'xsbf-widget-columns.php',
		'xsbf-widget-call-to-action.php'
	),

	// Main class(es) to always add to the outer widget <div> (or <aside>). This is just
	// here in case someone wants to use it. 
	'widget_classes' 					=> '',

	// Class(es) to always add to the heading (h1, h2, etc.)  This is just here in case
	// someone wants to use it. 
	'heading_classes' 					=> '',

	// Our flat color palette (colors renamed from Flat UI palette and additional ones
	// added)
	'bg_prefix' 						=> 'bg-',
	'color_prefix' 					=> 'color-',
	'colors' => array ( 
		'' 							=> '',
		'White' 						=> 'white',
		'Offwhite' 					=> 'offwhite',
		'Light Gray' 					=> 'lightgray',
		'Gray' 						=> 'gray',
		'Dark Gray' 					=> 'darkgray',
		'Light Green' 					=> 'lightgreen',
		'Dark Green' 					=> 'darkgreen',
		'Bright Green' 				=> 'brightgreen',
		'Dark Bright Green' 			=> 'darkbrightgreen',
		'Yellow' 						=> 'yellow',
		'Light Orange' 				=> 'lightorange',
		'Orange' 						=> 'orange',
		'Dark Orange' 					=> 'darkorange',
		'Blue' 						=> 'blue',
		'Dark Blue' 					=> 'darkblue',
		'Midnight Blue' 				=> 'midnightblue',
		'Dark Midnight Blue' 			=> 'darkmidnightblue',
		'Purple' 						=> 'purple',
		'Dark Purple' 					=> 'darkpurple',
		'Red' 						=> 'red',
		'Bright Red' 					=> 'brightred',
		'Dark Red' 					=> 'darkred',
		'Almost Black' 				=> 'almostblack',
		'Not Quite Black' 				=> 'notquiteblack',
		'Black' 						=> 'black'
	),

	// This maps a widget area name to the corresponding padding from the theme
	'paddings_map' => array (
		'Page Top'					=> 'section',
		'Page Bottom'					=> 'section',
		'Home Page'					=> 'section',
		'Footer'						=> 'padding',
		'Sidebar'						=> 'padding'
	),

	// Bootstrap text alignments (text-left, text-center, etc.)
	//'alignment_prefix' => 'text-',
	'alignments' => array ( 
		'' 							=> '',
		'Left' 						=> 'text-left',
		'Center' 						=> 'text-center',
		'Justified' 					=> 'text-justified',
		'Right' 						=> 'text-right'
	),

	// Font-awsome sizes. These are in "em's". 
	//'size_prefix' => 'fa-',
	'sizes' => array ( 
		'5x'							=> '5x',
		'4x'							=> '4x',
		'3x'							=> '3x', 
		'2x'							=> '2x', 
		'1x'							=> '1x', 
		'Fixed Width'					=> 'fw' 
	),

	// Bootstrap buttons plus our inverse, hollow, and transparent ones we added
	//'button_prefix' => 'btn-',
	'buttons' => array ( 
		'Default' 					=> 'btn-default',
		'Primary'						=> 'btn-primary',
		'Success' 					=> 'btn-success',
		'Info' 						=> 'btn-info',
		'Warning' 					=> 'btn-waring',
		'Danger' 						=> 'btn-danger',
		'Inverse' 					=> 'btn-inverse',
		'Outline' 					=> 'btn-hollow',
		'Transparent'					=> 'btn-transparent',
		'Link' 						=> 'btn-link'
	),

	// Number of columns for the columns widget. Should be 2, 4, or 6.
	'max_columns' => 4

);

/**
 * MERGE PLUGIN DEFAULTS WITH ANY OVERRIDES
 *
 * These can be overrided in your own plugin or theme's function.php file by setting
 * values in $xsbf_plugin_options. Your values will be merged with the defaults.
 */
if ( ! isset($xsbf_plugin_options) ) $xsbf_plugin_options = null;
$xsbf_plugin_options = wp_parse_args( $xsbf_plugin_options, $xsbf_plugin_defaults );

/*
 * LOAD EACH OF OUR MODULES AS DEFINED BY THE PLUGIN OPTIONS
 */
foreach ( $xsbf_plugin_options['modules'] as $module ) {
	@include_once $module; // @ suppresses errors if file doesn't exist
}
