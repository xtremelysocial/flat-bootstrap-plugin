<?php

/** 
 * Tell WordPress to instantiate this widget
 */
add_action( 'widgets_init', create_function('', 'return register_widget("XS_Section_Widget");') );

/**
 * Tell WordPress to allow the [flat_bootstrap_section] shortcode
 */
add_shortcode( 'flat_bootstrap_section', array( 'XS_Section_Widget', 'xs_section_widget_shortcode' ) );

/**
 * Our class to handle the widget
 */
class XS_Section_Widget extends WP_Widget {

	/** 
	 * This function gets called automatically when a new widget instance is created
	 */
	public function __construct() {
		parent::__construct(
	 		'XS_Section_Widget',
			'FB Colored Section',
			array( 'description' => __( 'Colored section with optional call to action button' ) 
			)
		);
	 }
		
	/**
	 * Declare some protected variables. These can be overriden if a new class
	 * is created but don't want them to be public outside the class to avoid
	 * conflict with other plugins.
	 */
	 
	// Our color palette (colors renamed from Flat UI palette)
	protected $colors = array ( '', 'white', 'offwhite', 'lightgray', 'gray', 'darkgray', 'lightgreen', 'darkgreen', 'brightgreen', 'darkbrightgreen', 'yellow', 'lightorange', 'orange', 'darkorange', 'blue', 'darkblue', 'purple', 'darkpurple', 'midnightblue', 'darkmidnightblue', 'red', 'brightred', 'darkred', 'almostblack', 'notquiteblack', 'black' );

	// WordPress core theme (_S)
	protected $alignments = array( '', 'left', 'center', 'right' );
	
	// Bootstrap v3.3.2 (http://getbootstrap.com) plus our btn-hollow and btn-transparent	
	protected $styles = array( 'primary', 'success', 'info', 'warning', 'danger', 'hollow', 'transparent', 'default', 'link' ); 

	/** 
	 * Main function to display the widget on the front-end
	 */
	public function widget($args, $instance) {
	
		if ( !$instance ) {
			echo 'Trying to build the section contents...<br />'; //TEST
			echo 'args=' . print_r ( $args ) . '<br />'; //TEST
		}
	    
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

		// Output the widget
		echo $before_widget;
		
		// If Page Top or Page Bottom Widget area, then kill off container and add it
		// later after the colored section div
		$container_needed = $padding_needed = false;
		if ( $name == 'Page Top' OR $name == 'Page Bottom' ) {
			echo '</div><!-- container -->';		
			$container_needed = true;

		// Otherwise, if a background color is selected, then need to add padding
		} elseif ( $name AND $bgcolor ) {
			$padding_needed = true;

		// Otherwise, this is being called from a shortcode and we need a container
		} else {
			$container_needed = true;
		} //endif $name

		// Display the widget title and other fields		
		echo '<div class="section ' 
			.( $bgcolor ? 'bg-' . $bgcolor . ' ' : '' )
			.( $alignment ? 'align' . $alignment . ' ' : '' )
			.( $padding_needed ? 'padding' : '' )
			.'">';
		if ( $container_needed ) echo '<div class="container">';
		if ( $title ) echo $before_title . $title . $after_title;
		if ( $subtitle ) echo '<h3>' . $subtitle . '</h3>'; 
		if ( $text ) echo '<p>' . $text . '</p>'; 
		if ( $btn_url AND $btn_text ) echo '<p><a href="' . $btn_url . '" class="btn btn-lg btn-' . $btn_style .'">' . $btn_text . '</a></p>'; 
		if ( $container_needed ) echo '</div><!-- container -->';
		echo '</div><!-- section -->';

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
		
		return $instance;
	}

	/** 
	 * This function displays the widget settings form
	 */
	public function form( $instance ) {
	
		// Parse incoming variables
		$title = strip_tags($instance['title']);
		$subtitle = esc_attr($instance['subtitle']);
		$text = esc_textarea($instance['text']);
		$bgcolor = stripslashes($instance['bgcolor']);
		$alignment = stripslashes($instance['alignment']);
		$btn_text = stripslashes($instance['btn_text']);
		$btn_style = stripslashes($instance['btn_style']);
		$btn_url = stripslashes($instance['btn_url']);
				
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e('Subtitle:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo $subtitle; ?>" />
		<br /><small><?php _e('(Subtitle allows basic HTML tags)'); ?></small></p>

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

		<p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Body Text:'); ?></label>
		<!-- <input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo $text; ?>" /></p> -->
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
		<br /><small><?php _e('(Body text allows basic HTML tags)'); ?></small></p>

		<p><label for="<?php echo $this->get_field_id('btn_text'); ?>"><?php _e('Button Text (Label):'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('btn_text'); ?>" name="<?php echo $this->get_field_name('btn_text'); ?>" type="text" value="<?php echo $btn_text; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('btn_style'); ?>"><?php _e('Button Style:'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('btn_style'); ?>" name="<?php echo $this->get_field_name('btn_style'); ?>">
			<?php 
			foreach ( $this->styles as $style ) :
			?>
				<option value="<?php echo esc_attr($style) ?>" <?php selected($style, $btn_style) ?>><?php echo $style; ?></option>
			<?php				
			endforeach;
			?>
        </select>
		<br /><small><?php _e('(Choose from our button styles)'); ?></small></p>

		<p><label for="<?php echo $this->get_field_id('btn_url'); ?>"><?php _e('Button Link (URL):'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('btn_url'); ?>" name="<?php echo $this->get_field_name('btn_url'); ?>" type="text" value="<?php echo $btn_url; ?>" /></p>

	<?php
	} // endfunction
	
	/**
	 * Create the [flat_bootstrap_section] shortcode
	 */
	public function xs_section_widget_shortcode( $atts ) {

		$defaults = array (
			'title' => '',
			'subtitle' => '',
			'bgcolor' => '',
			'alignment' => 'center',
			'text' => '',
			'btn_text' => '',
			'btn_style' => 'primary',
			'btn_url' => ''
		);
		$args = shortcode_atts( $defaults, $atts, 'flat_bootstrap_section' );
		
		// To help users with "debugging" use of this shortcode, default in some
		// text if they didn't fill out the parameters correctly.
		if ( !$args['title'] AND !$args['subtitle'] AND !$args['text'] ) $args['text'] = __( 'You need to add some parameters, such as &lbrack;flat_bootstrap_section title="MY TITLE" subtitle="My Subtitle" text="This is just an example"&rbrack;' );
	
		// Since the widget uses "echo", collect up all that to return at the end
		ob_start();

		// The first parameter to the widget is the default widget area fields
		// such as before_widget, before_title, etc. We need to mirror the 
		// settings from the actual Page Top widget area. The second parameter 
		// is the actual shortcode arguments to use to build the widget content.
		$widget_area_defaults = array(
			'before_widget' => '<div class="widget widget_xs_column_widget clearfix">',
			'before_title' 	=> '<h2 class="widget-title">',
			'after_title' 	=> '</h2>',
			'after_widget' 	=> '</div>',
		);
		$xs_section_widget = new XS_Section_Widget();
		$xs_section_widget->widget( $widget_area_defaults, $args );

		// Take the echo'd output, flush the buffer, and return the output
		$return = ob_get_contents();	
		ob_end_clean();
		return $return;

	} //endfunction
	
} //endclass
