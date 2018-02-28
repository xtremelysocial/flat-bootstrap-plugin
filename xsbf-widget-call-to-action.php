<?php

//ini_set ( 'display_errors', 1 ); //TEST

/** 
 * Tell WordPress to instantiate this widget
 */
add_action( 'widgets_init', create_function('', 'return register_widget("XS_CTA_Widget");') );

/**
 * Tell WordPress to allow the [flat_bootstrap_cta] shortcode
 */
add_shortcode( 'flat_bootstrap_cta', array( 'XS_CTA_Widget', 'xs_cta_widget_shortcode' ) );

/**
 * Our class to handle the widget
 */
class XS_CTA_Widget extends WP_Widget {

	/** 
	 * This function gets called automatically when a new widget instance is created
	 */
	public function __construct() {
		parent::__construct(
	 		'XS_CTA_Widget',
			'Flat Call-to-Action',
			array( 'description' => __( 'Colored section with optional call to action button(s)' ) 
			)
		);
	 }
		
	/**
	 * Declare some protected variables. These can be overriden if a new class
	 * is created but don't want them to be public outside the class to avoid
	 * conflict with other plugins.
	 */
	 
	// Our color palette (colors renamed from Flat UI palette)
	//protected $colors = array ( '', 'white', 'offwhite', 'lightgray', 'gray', 'darkgray', 'lightgreen', 'darkgreen', 'brightgreen', 'darkbrightgreen', 'yellow', 'lightorange', 'orange', 'darkorange', 'blue', 'darkblue', 'purple', 'darkpurple', 'midnightblue', 'darkmidnightblue', 'red', 'brightred', 'darkred', 'almostblack', 'notquiteblack', 'black' );

	// WordPress core theme (_S)
	//protected $alignments = array( '', 'left', 'center', 'right' );
	
	// Bootstrap v3.3.2 (http://getbootstrap.com) plus our btn-hollow and btn-transparent	
	//protected $styles = array( 'primary', 'success', 'info', 'warning', 'danger', 'hollow', 'transparent', 'default', 'link' ); 
	//protected $styles = $xsbf_widgetcss_options['buttons'];

	// Theme-specific widget areas that are full-width and don't need extra padding
	// when a colored background is used. Any widget area not listed here will get the
	// padding, which should work for most themes with just a standard sidebar and footer
	//protected $widgets_fullwidth = array ( 'Page Top', 'Page Bottom', 'Home Page' );

	/** 
	 * Main function to display the widget on the front-end
	 */
	public function widget($args, $instance) {

		// Get our plugin's options
		global $xsbf_plugin_options;
	
		// Get widget area settings, such as before/after widget, before/after title
	    extract( $args );

		// Get this specific widget instance's parameters
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$subtitle = $instance['subtitle'];
		$bgcolor = htmlspecialchars($instance['bgcolor']);
		$alignment = htmlspecialchars($instance['alignment']);
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		$btn_text = htmlspecialchars($instance['btn_text']);
		$btn_style = htmlspecialchars($instance['btn_style']);
		$btn_url = htmlspecialchars($instance['btn_url']);
		$btn_text_2 = htmlspecialchars($instance['btn_text_2']);
		$btn_style_2 = htmlspecialchars($instance['btn_style_2']);
		$btn_url_2 = htmlspecialchars($instance['btn_url_2']);

		// Output the widget
/*
		// Add our colors and alignment to widget section
		//echo 'before_widget=' . htmlspecialchars( $before_widget ); //TEST
		$classes = ( in_array ( $name, $this->widgets_fullwidth ) ? 'section' : 'padding' )
			. ( $bgcolor ? ' bg-' . $bgcolor : '' )
			.( ( $alignment AND $alignment != 'left' ) ? ' align' . $alignment : '' );			
		//$before_widget = str_ireplace( 'class="', 'class="' . $classes . ' ', $before_widget ); 
		//preg_replace( '|class=/"(.*)/"|', '|class=/"{$1} ' . $classes . '/"|', $before_widget ); 
		//echo '<div class="' . $classes . '">';
		$before_widget = str_ireplace( 'widget_xs_cta_widget', 'widget_xs_cta_widget ' . $classes, $before_widget ); 
*/
		echo $before_widget;
/*		
		// If Page Top or Page Bottom Widget area, then kill off container and add it
		// later after the colored section div
		$container_needed = $padding_needed = false;
		//if ( $name == 'Page Top' OR $name == 'Page Bottom' OR ($name == 'Home Page' AND function_exists('xsbf_is_fullwidth') AND !xsbf_is_fullwidth() ) ) {
		if ( $name == 'Page Top' OR $name == 'Page Bottom' OR $name == 'Home Page' ) {
			//echo 'full-width widget area. kill the div and add a container...<br />'; //TEST
			///echo '</div><!-- container -->';		
			///$container_needed = true;

		// Otherwise, this is being called from a shortcode and we need a container
		// if the page template is full-width
		} elseif ( function_exists('xsbf_is_fullwidth') AND xsbf_is_fullwidth() ) {
			//echo 'xsbf_is_fullwidth=' . xsbf_is_fullwidth() . '...<br />'; //TEST
			//echo 'IS a full width page. Adding a container...<br />'; //TEST
			///$container_needed = true;

		// Otherwise, if a background color is selected, then need to add padding
		} elseif ( $name AND $bgcolor ) {
			$padding_needed = true;

		} //endif $name
		//echo 'container_needed=' . $container_needed . '<br />'; //TEST
*/
		// Display the widget title and other fields		
		/*echo '<div class="section ' 
			.( $bgcolor ? 'bg-' . $bgcolor . ' ' : '' )
			.( $alignment ? 'align' . $alignment . ' ' : '' )
			.( $padding_needed ? 'padding' : '' )
			.'">';
		if ( $container_needed ) echo '<div class="container">';*/
		if ( $title ) echo $before_title . $title . $after_title;
		if ( $subtitle ) echo '<h3>' . $subtitle . '</h3>'; 
		if ( $text ) echo '<p>' . $text . '</p>'; 
		if ( ( $btn_url AND $btn_text ) OR ( $btn_url_2 AND $btn_text_2 ) ) echo '<p>';
		//if ( $btn_url AND $btn_text ) echo '<a href="' . $btn_url . '" class="btn btn-lg btn-' . $btn_style .'">' . $btn_text . '</a>';
		//if ( $btn_url_2 AND $btn_text_2 ) echo '&nbsp;<a href="' . $btn_url_2 . '" class="btn btn-lg btn-' . $btn_style_2 .'">' . $btn_text_2 . '</a>'; 
		//echo "<p>btn_style='{$btn_style}'</p>"; //TEST 
		if ( $btn_url AND $btn_text ) {
			echo '<a href="' . $btn_url . '" class="btn btn-lg ' . $xsbf_plugin_options['buttons'][$btn_style] .'">' . $btn_text . '</a>';
			if ( $btn_url_2 AND $btn_text_2 ) echo '&nbsp;';
		}
		if ( $btn_url_2 AND $btn_text_2 ) echo '<a href="' . $btn_url_2 . '" class="btn btn-lg ' . $xsbf_plugin_options['buttons'][$btn_style_2] .'">' . $btn_text_2 . '</a>';
		if ( ( $btn_url AND $btn_text ) OR ( $btn_url_2 AND $btn_text_2 ) ) echo '</p>';
		/*if ( $container_needed ) echo '</div><!-- container -->';
		echo '</div><!-- section -->';*/

		echo $after_widget;

	} //endfunction

	/** 
	 * This function updates the widget settings to the database
	 */
	public function update( $new_instance, $old_instance ) {
	
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

		// Allow HTML if user is authorized. Note wp_filter_post_kses() expects slashed.
		if ( current_user_can('unfiltered_html') ) {
			$instance['subtitle'] =  $new_instance['subtitle'];
			$instance['text'] =  $new_instance['text'];
		} else { // wp_filter_post_kses() expects slashed
			$instance['subtitle'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['subtitle']) ) ); 		
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) );
		} //endif current_user_can()

		$instance['bgcolor'] = stripslashes($new_instance['bgcolor']);
		$instance['alignment'] = stripslashes($new_instance['alignment']);
		$instance['btn_text'] = stripslashes($new_instance['btn_text']);
		$instance['btn_style'] = stripslashes($new_instance['btn_style']);
		$instance['btn_url'] = stripslashes($new_instance['btn_url']);
		$instance['btn_text_2'] = stripslashes($new_instance['btn_text_2']);
		$instance['btn_style_2'] = stripslashes($new_instance['btn_style_2']);
		$instance['btn_url_2'] = stripslashes($new_instance['btn_url_2']);
		
		return $instance;
	}

	/** 
	 * This function displays the widget settings form
	 */
	public function form( $instance ) {

		// Get our plugin's options
		global $xsbf_plugin_options;
		//$styles = $xsbf_plugin_options['buttons'];
	
		// Parse incoming variables
		$title = strip_tags($instance['title']);
		$subtitle = esc_attr($instance['subtitle']);
		$text = esc_textarea($instance['text']);
		//$bgcolor = stripslashes($instance['bgcolor']);
		//$alignment = stripslashes($instance['alignment']);
		$btn_text = stripslashes($instance['btn_text']);
		$btn_style = stripslashes($instance['btn_style']);
		$btn_url = stripslashes($instance['btn_url']);
		$btn_text_2 = stripslashes($instance['btn_text_2']);
		$btn_style_2 = stripslashes($instance['btn_style_2']);
		$btn_url_2 = stripslashes($instance['btn_url_2']);
				
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e('Subtitle:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo $subtitle; ?>" />
		<br /><small><?php _e('(Subtitle allows basic HTML tags)'); ?></small></p>

<!--
		<p><label for="<?php echo $this->get_field_id('bgcolor'); ?>"><?php _e('Background Color:'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('bgcolor'); ?>" name="<?php echo $this->get_field_name('bgcolor'); ?>">
			<?php 
			foreach ( $this->colors as $color ) :
			?>
				<option value="<?php echo esc_attr($color) ?>" <?php selected($color, $bgcolor) ?>><?php echo $color; ?></option>
			<?php				
			endforeach;
			?>
        </select>
		<br /><small><?php _e('(Choose from our color palette)'); ?></small></p>

		<p><label for="<?php echo $this->get_field_id('alignment'); ?>"><?php _e('Alignment:'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('alignment'); ?>" name="<?php echo $this->get_field_name('alignment'); ?>">
			<?php 
			foreach ( $this->alignments as $alignment_option ) :
			?>
				<option value="<?php echo esc_attr($alignment_option) ?>" <?php selected($alignment_option, $alignment) ?>><?php echo $alignment_option; ?></option>
			<?php				
			endforeach;
			?>
        </select>
		<br /><small><?php _e('(Choose from left, center, or right alignment)'); ?></small></p>
-->

		<p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Body Text:'); ?></label>
		<!-- <input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo $text; ?>" /></p> -->
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
		<br /><small><?php _e('(Body text allows basic HTML tags)'); ?></small></p>

		<p><label for="<?php echo $this->get_field_id('btn_text'); ?>"><?php _e('Button Text (Label):'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('btn_text'); ?>" name="<?php echo $this->get_field_name('btn_text'); ?>" type="text" value="<?php echo $btn_text; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('btn_style'); ?>"><?php _e('Button Style:'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('btn_style'); ?>" name="<?php echo $this->get_field_name('btn_style'); ?>">
			<?php 
			//foreach ( $this->styles as $style ) :
			//foreach ( $styles as $style ) :
			foreach ( $xsbf_plugin_options['buttons'] as $button_option => $button_css ) :
			?>
				<option value="<?php echo esc_attr($button_option) ?>" <?php selected($button_option, $btn_style) ?>><?php echo $button_option; ?></option>
			<?php				
			endforeach;
			?>
        </select>
		<br /><small><?php _e('(Choose from our button styles)'); ?></small></p>

		<p><label for="<?php echo $this->get_field_id('btn_url'); ?>"><?php _e('Button Link (URL):'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('btn_url'); ?>" name="<?php echo $this->get_field_name('btn_url'); ?>" type="text" value="<?php echo $btn_url; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('btn_text_2'); ?>"><?php _e('Button 2 Text (Label):'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('btn_text_2'); ?>" name="<?php echo $this->get_field_name('btn_text_2'); ?>" type="text" value="<?php echo $btn_text_2; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('btn_style_2'); ?>"><?php _e('Button 2 Style:'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('btn_style_2'); ?>" name="<?php echo $this->get_field_name('btn_style_2'); ?>">
			<?php 
			//foreach ( $this->styles as $style ) :
			//foreach ( $styles as $style ) :
			foreach ( $xsbf_plugin_options['buttons'] as $button_option => $button_css ) :
			?>
<!--
				<option value="<?php echo esc_attr($style) ?>" <?php selected($style, $btn_style_2) ?>><?php echo $style; ?></option>
-->
				<option value="<?php echo esc_attr($button_option) ?>" <?php selected($button_option, $btn_style_2) ?>><?php echo $button_option; ?></option>
			<?php				
			endforeach;
			?>
        </select>
		<br /><small><?php _e('(Choose from our button styles)'); ?></small></p>

		<p><label for="<?php echo $this->get_field_id('btn_url'); ?>"><?php _e('Button 2 Link (URL):'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('btn_url_2'); ?>" name="<?php echo $this->get_field_name('btn_url_2'); ?>" type="text" value="<?php echo $btn_url_2; ?>" /></p>

	<?php
	} // endfunction
	
	/**
	 * Create the [flat_bootstrap_cta] shortcode
	 */
	public function xs_cta_widget_shortcode( $atts ) {

		$defaults = array (
			'title' => '',
			'subtitle' => '',
			'bgcolor' => '',
			'alignment' => 'center',
			'text' => '',
			'btn_text' => '',
			'btn_style' => 'primary',
			'btn_url' => '',
			'btn_text_2' => '',
			'btn_style_2' => 'primary',
			'btn_url_2' => ''
		);
		$args = shortcode_atts( $defaults, $atts, 'flat_bootstrap_cta' );
		
		// To help users with "debugging" use of this shortcode, default in some
		// text if they didn't fill out the parameters correctly.
		if ( !$args['title'] AND !$args['subtitle'] AND !$args['text'] ) $args['text'] = __( 'You need to add some parameters, such as &lbrack;flat_bootstrap_cta title="MY TITLE" subtitle="My Subtitle" text="This is just an example"&rbrack;' );
	
		// Since the widget uses "echo", collect up all that to return at the end
		ob_start();

		// The first parameter to the widget is the default widget area fields
		// such as before_widget, before_title, etc. We need to mirror the 
		// settings from the actual Page Top widget area. The second parameter 
		// is the actual shortcode arguments to use to build the widget content.
		/*$widget_area_defaults = array(
			'before_widget' => '<div class="widget widget_xs_cta_widget clearfix">',
			'before_title' 	=> '<h2 class="widget-title">',
			'after_title' 	=> '</h2>',
			'after_widget' 	=> '</div>',
		);*/
		$widget_area_defaults = array(
			'before_widget' => '<div class="widget widget_xs_cta_widget"><div class="container">',
			'before_title' 	=> '<h2 class="widget-title">',
			'after_title' 	=> '</h2>',
			'after_widget' 	=> '</div><!-- container --></div>',
		);
		$xs_cta_widget = new XS_CTA_Widget();
		$xs_cta_widget->widget( $widget_area_defaults, $args );

		// Take the echo'd output, flush the buffer, and return the output
		$return = ob_get_contents();	
		ob_end_clean();
		return $return;

	} //endfunction
	
} //endclass

//ini_set ( 'display_errors', 0 ); //TEST
