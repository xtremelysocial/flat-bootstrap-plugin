<?php
/**
 * Plugin: Flat Bootstrap Widgets
 *
 * Main class and code for our CSS widget and shortcode [flat_bootstrap_section]
 * shortcode.
 *
 * @package flat-bootstrap-plugin
 */

/*
 * Load our widget CSS options
 */
global $xsbf_widgetcss_options;


//wp_cache_delete ( 'widget_css' ); //TEST
if ( ( !$xsbf_widgetcss_options = get_option( 'widget_css' ) ) || !is_array ( $xsbf_widgetcss_options ) ) 
	$xsbf_widgetcss_options = array();

//echo '<pre>'; var_dump( $xsbf_widgetcss_options ); echo '</pre>'; //TEST

/* If in Admin, then hook into the widget controls */
if ( is_admin() ) {
	add_action( 'sidebar_admin_setup', 'xsbf_widget_css_expand_control' );
	add_filter( 'widget_update_callback', 'xsbf_widget_css_widget_update_callback', 10, 3 ); 
}

/* Always hook into the sidebar parameters to add our fields to the widget */
add_filter( 'dynamic_sidebar_params', 'xsbf_filter_widget' );

/**
 * Registered Callback function for the admin sidebar setup
 *
 * Register the widget controls and when updated, concatenate all the CSS classes and 
 * save them to the options table.
 */
function xsbf_widget_css_expand_control() {

	global $wp_registered_widgets, $wp_registered_widget_controls, $xsbf_plugin_options, $xsbf_widgetcss_options;

	/* If updated, concatenate all the CSS classes and save them to the options table */
	if( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] ) ) {	
		foreach( (array) $_POST['widget-id'] as $widget_number => $widget_id ) {

			//$xsbf_widgetcss_options[$widget_id] = null;
			$xsbf_widgetcss_options[$widget_id] = '';

			/* Save color if field set */
			$bg_prefix = $xsbf_plugin_options['bg_prefix'];
			if ( isset( $_POST[$widget_id.'-widget_bgcolor'] ) ) {
				$bgcolor = $_POST[$widget_id.'-widget_bgcolor'];
				if ( $bgcolor ) {
					$xsbf_widgetcss_options[$widget_id] .= $bg_prefix . $xsbf_plugin_options['colors'][$bgcolor] . ' ';
					//if ( $_POST[$widget_id.'-widget_bgcolor'] != ' ' ) {
						//$xsbf_widgetcss_options[$widget_id] .= ' ';
					//}
				} //endif $bgcolor
			} //endif isset()

			/* Save alignment if field set */
			if ( isset( $_POST[$widget_id.'-widget_alignment'] ) ) {
				$alignment = $_POST[$widget_id.'-widget_alignment'];
				if ( $alignment ) {
					$xsbf_widgetcss_options[$widget_id] .= $xsbf_plugin_options['alignments'][$alignment] . ' ';
					//if ( $_POST[$widget_id.'-widget_alignment'] != '' ) {
						//$xsbf_widgetcss_options[$widget_id] .= ' ';
					//}
				} //endif $alignment
			} //endif isset()
			
			/* Save additional CSS if field set */
			if ( isset( $_POST[$widget_id.'-widget_css'] ) ) {
				$xsbf_widgetcss_options[$widget_id] .= $_POST[$widget_id.'-widget_css'];
			} //endif

			/* Remove any extra spaces at the end */
			$xsbf_widgetcss_options[$widget_id] = trim ( $xsbf_widgetcss_options[$widget_id] );

		} //endforeach
	} //endif post
	//wp_cache_delete ( 'widget_css' ); //TEST
	update_option( 'widget_css', $xsbf_widgetcss_options );

	/* Register the widget controls */	
	foreach ( $wp_registered_widgets as $id => $widget ) {
		if ( !$wp_registered_widget_controls[$id] ) {
			wp_register_widget_control($id,$widget['name'], 'xsbf_widget_css_empty_control');
		}
		
		$wp_registered_widget_controls[$id]['xsbf_callback_widget_css_redirect'] = $wp_registered_widget_controls[$id]['callback'];
		$wp_registered_widget_controls[$id]['callback'] = 'xsbf_widget_css_extra_control';
		array_push( $wp_registered_widget_controls[$id]['params'], $id );	
	} //endforeach
	
} //endfunction

/**
 * Callback function for the agumented widget control.
 *
 * Renders the text boxes on the widget for the color, alignment, and additional CSS
 * class(es)
 */
function xsbf_widget_css_extra_control() {
				
	global $wp_registered_widget_controls, $xsbf_plugin_options, $xsbf_widgetcss_options;

	/* Handle widget parameters and callback */
	$params = func_get_args();
	$id = array_pop( $params );
	$callback = $wp_registered_widget_controls[$id]['xsbf_callback_widget_css_redirect'];

	if( is_callable( $callback ) ) {
		call_user_func_array( $callback, $params );		
	}

	/* Strip any harmful stuff from the CSS options field */
	 $stripped_options = !empty( $xsbf_widgetcss_options[$id] ) ? htmlspecialchars( stripslashes( strtolower( $xsbf_widgetcss_options[$id] ) ), ENT_QUOTES ) : '';
	 
	//$bgcolor_value = null;
	//$css_value = null;
	//$alignment_value = null;
	$bgcolor_value = '';
	$css_value = '';
	$alignment_value = '';

	/* Get the widget number */
	if( isset( $params[0]['number']) ) {
		$number = $params[0]['number'];
	}

	// TO-DO: Find out why the values need to be cleared in this case. I think it's when 
	// a widget is deleted.
	if( isset( $number ) && $number == -1 ) { 
		$number="%i%";
	}

	//echo "<p>number={$number}</p>"; //TEST

	/* Set the field ID's */	
	$id_disp = $id;	
	if( isset( $number ) ) {
		$id_disp = $wp_registered_widget_controls[$id]['id_base'].'-'.$number;
	}
	$id_bgcolor = $id_disp . "-widget_bgcolor";
	$id_alignment = $id_disp . "-widget_alignment";	
	$id_css = $id_disp . "-widget_css";

	/* Output the extra widget option fields */
	echo "<hr>";
	//echo '<pre>'; var_dump ( $wp_registered_widget_controls[$id] ); echo '</pre>'; //TEST
	//echo '<h3>params</h3><pre>'; var_dump ( $params ); echo '</pre>'; //TEST
	//echo '<pre>'; var_dump ( $wp_registered_widget_controls ); echo '</pre>'; //TEST
	//echo "<p><b>" . __( 'Widget Options', 'langdirective' ) . "</b></p>"; 
	
	/* 
	 * TO-DO: Add checkbox to display the title or not. That way we can put in a title
	 * for viewing in Admin, but hide it in case we want to use a different Hx tag or not
	 * have a title at all displayed.
	 */
	
	/* Background color field */
	$bg_prefix = $xsbf_plugin_options['bg_prefix'];
	echo "<p><label for='{$id_bgcolor}'>" . __('Background Color:', 'langdirective') . "</label>"
		."<select id='{$id_bgcolor}' name='{$id_bgcolor}' class='widefat'>";
	foreach ( $xsbf_plugin_options['colors'] as $bgcolor_option => $bgcolor_css) {

		// TO-DO: Find out why the $number value matters
		////if( ! isset( $number ) OR $number != -1 ) { 
			if ( $bgcolor_option != '' ) {			
				$stripped_options = trim ( str_replace ( $bg_prefix . $bgcolor_css . ' ', '', $stripped_options . ' ', $count ) );
				if ( $count > 0 ) $bgcolor_value = $bgcolor_option;
			} //endif $bgcolor_option
		////} //endif
		echo "<option value='{$bgcolor_option}'" . ( $bgcolor_option != '' ? selected( $bgcolor_option,  $bgcolor_value, false) : '' ) . ">{$bgcolor_option}</option>";
	} //endforeach
	echo "</select>";
		//."<br /><small>" . __('(Choose from our various background colors', 'langdirective') . "</small></p>";

	/* Alignment field */
	echo "<p><label for='{$id_alignment}'>" . __('Alignment:', 'langdirective') . "</label>"
		."<select id='{$id_alignment}' name='{$id_alignment}' class='widefat'>";
	foreach ( $xsbf_plugin_options['alignments'] as $alignment_option => $alignment_css) {

		// TO-DO: Find out why the $number value matters
		////if( ! isset( $number ) OR $number != -1 ) { 
			if ( $alignment_option != '' ) {
				$stripped_options = trim ( str_replace ( $alignment_css . ' ', '', $stripped_options . ' ', $count ) );
				if ( $count > 0 ) $alignment_value = $alignment_option;
			} //endif $alignment_option
		////} //endif
		echo "<option value='{$alignment_option}'" . ( $alignment_option != '' ? selected( $alignment_option,  $alignment_value, false) : '' ) . ">{$alignment_option}</option>";
	} //endforeach
	echo "</select>";
		//."<br /><small>" . __('(Choose from center, left, or right alignment)', 'langdirective') . "</small></p>";

	/* Additional CSS Classes */
	// TO-DO: Find out why the $number value matters
	////if( ! isset( $number ) OR $number != -1 ) { 
		$css_value = $stripped_options;
	////} //endif 
	echo "<p><label for='{$id_css}'>" . __('Additional CSS classes (Separate with spaces):', 'langdirective') . " <input type='text' name='{$id_css}' id='{$id_css}' class='widefat' value='{$css_value}' /></label>";
		//."<br /><small>" . __('(Separate multiple classes with a space)', 'langdirective') . "</small></p>";

	echo "<hr>";
}

/**
 * Callback Function for widget update.
 *
 * Saves the bgcolor, alignment and additional CSS class value that has been specified for
 * the widget.
 */
function xsbf_widget_css_widget_update_callback( $instance, $new_instance, $this_widget )
{	
	global $xsbf_plugin_options, $xsbf_widgetcss_options;

	/* Set the widget id */
	$widget_id = $this_widget->id;

	/* Clear the field to hold all the css values */
	//$xsbf_widgetcss_options[$widget_id] = null;
	$xsbf_widgetcss_options[$widget_id] = '';
	
	/* Save background color */
	$bg_prefix = $xsbf_plugin_options['bg_prefix'];	
	if ( isset( $_POST[$widget_id.'-widget_bgcolor'] ) ) {
		$bgcolor = $_POST[$widget_id.'-widget_bgcolor'];
		$xsbf_widgetcss_options[$widget_id] .= $bg_prefix . $xsbf_plugin_options['colors'][$bgcolor] . ' ';
	}

	/* Save alignment */
	if ( isset( $_POST[$widget_id.'-widget_alignment'] ) ) {
		$alignment = $_POST[$widget_id.'-widget_alignment'];
		$xsbf_widgetcss_options[$widget_id] .= $xsbf_plugin_options['alignments'][$alignment] . ' ';
	}
	
	/* Save additional CSS classes */
	if ( isset( $_POST[$widget_id.'-widget_css'] ) ) {
		$xsbf_widgetcss_options[$widget_id] .= $_POST[$widget_id.'-widget_css'];
	}

	$xsbf_widgetcss_options[$widget_id] = trim ( $xsbf_widgetcss_options[$widget_id] );
	//echo 'Saving widget options...<pre>'; print_r ( $xsbf_widgetcss_options[$widget_id] ); echo '</pre>';//TEST
	//wp_cache_delete ( 'widget_css' ); //TEST
	update_option( 'widget_css', $xsbf_widgetcss_options );
	
	return $instance;
}

/**
 * Callback function for the dynamic_sidebar_params
 *
 * Dynamically applies the specified CSS class to the widget when it is rendered at the 
 * front end. Since we store all the CSS is a single options record, this handles bgcolor,
 * alignment, and any additional CSS classes. It also automatically adds padding when
 * a bgcolor is specified.
 */
function xsbf_filter_widget( $params ) {

	global $xsbf_widgetcss_options, $xsbf_plugin_options;
	//echo '<pre>'; var_dump ( $params[0] ); echo '</pre>';//TEST
	//echo 'before_widget=' . htmlspecialchars ( $xsbf_widgetcss_options[$params[0]['widget_id']] ) .'!<br />'; //TEST

	if( isset( $xsbf_widgetcss_options[$params[0]['widget_id']] ) && trim($xsbf_widgetcss_options[$params[0]['widget_id']]) !='' ) {

		//echo '<p>In xsbf_filter_widget() and there are extra widget parameters</p>'; //TEST 
		
		/* Add default widget_css, if specified */
		$xsbf_widgetcss_options[$params[0]['widget_id']] .= ' ' .  $xsbf_plugin_options['widget_classes'];

		/* If a background color was specified, then add the appropriate padding too 
		   based on what widget area we are in. */
		$bg_prefix = $xsbf_plugin_options['bg_prefix'];
		foreach ( $xsbf_plugin_options['colors'] as $bgcolor_option => $bgcolor_css ) {
			//echo "<p>Looking for '{$bg_prefix}{$bgcolor_css}' in '{$xsbf_widgetcss_options[$params[0]['widget_id']]}'...</p>"; //TEST
			if( $bgcolor_css != '' AND stripos ( $xsbf_widgetcss_options[$params[0]['widget_id']], $bg_prefix . $bgcolor_css ) !== false ) {
			//echo "<p>Found '{$bg_prefix}{$bgcolor_css}' in '{$xsbf_widgetcss_options[$params[0]['widget_id']]}'!</p>"; //TEST

				if ( !is_admin() ) {			
					//echo "<p>We shouldn't be displaying in admin here!</p>"; //TEST 					
					$widget_area = $params[0]['name'];
					$xsbf_widgetcss_options[$params[0]['widget_id']] .= ' ' .  $xsbf_plugin_options['paddings_map'][$widget_area];
				}
				
				break;
		  	} //endif
		} //endforeach
		//$xsbf_widgetcss_options[$params[0]['widget_id']] .= ' padding'; //TEST

		/* If no div tag, add one so we can include our CSS classes */
		if( trim( $params[0]['before_widget']) == '' ) {
			$params[0]['before_widget'] = '<div class="'.$xsbf_widgetcss_options[$params[0]['widget_id']].'">';
			$params[0]['after_widget'] = '</div>';

		/* Otherwise, add our CSS classes to the existing ones */
		} else {
			$xml = simplexml_load_string($params[0]['before_widget']."#splt#".$params[0]['after_widget']);

			if( isset( $xml['class'] ) ) {
				$xml['class'] = $xml['class'] . ' ' . $xsbf_widgetcss_options[$params[0]['widget_id']];
			} else {
				$xml['class'] = $xsbf_widgetcss_options[$params[0]['widget_id']];
			} //endif $xml
				
			$processedtags = explode( '#splt#', $xml->asXML() );
			
			$params[0]['before_widget'] = $processedtags[0];
			$params[0]['after_widget'] = $processedtags[1];
		}
	}
	//echo 'before_widget=' . htmlspecialchars ( $params[0]['before_widget'] ) .'!<br />'; //TEST
	return $params;
} //endfunction xsbf_filter_widget