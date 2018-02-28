<?php

//ini_set ( 'display_errors', 1 ); //TEST

/** 
 * Tell WordPress to instantiate this widget
 */
add_action( 'widgets_init', create_function('', 'return register_widget("XS_Columns_Widget");') );

/**
 * Tell WordPress to allow the [flat_bootstrap_columns] shortcode
 */
add_shortcode( 'flat_bootstrap_columns', array( 'XS_Columns_Widget', 'xs_columns_widget_shortcode' ) );

/**
 * Our class to handle the widget
 */
class XS_Columns_Widget extends WP_Widget {
	
	/** 
	 * This function gets called automatically when a new widget instance is created
	 */
	public function __construct() {
		parent::__construct(
	 		'XS_Columns_Widget',
			'Flat Columns',
			array( 'description' => __( 'Colored section with columns of icons and/or text' ) 
			)
		);
		//global $xsbf_plugin_options;		
	 }

	/** 
	 * Main function to display the widget on the front-end
	 */
	function widget($args, $instance) {
	
		// Extract some of our theme options to discreet variables
		global $xsbf_plugin_options;
		
	    $widget_classes = $xsbf_plugin_options['widget_classes'];
	    $color_prefix = $xsbf_plugin_options['color_prefix'];
	    //$alignment_prefix = $xsbf_plugin_options['alignment_prefix'];
	    $max_columns = $xsbf_plugin_options['max_columns'];

		// Extract some of our local variables
	    $fa_prefix = $this->fa_prefix;
	    echo "<p>fa_prefix='{$fa_prefix}'</p>"; //TEST
	    $icon_prefix = $this->icon_prefix;
	    //$icon_large = $this->icon_large;

		// Extract $args into individual variables. Eg $args['name'] to $name.
	    extract( $args );

		// Get this specific widget instance's parameters
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$subtitle = $instance['subtitle'];
		$bgcolor = htmlspecialchars($instance['bgcolor']);
		$alignment = htmlspecialchars($instance['alignment']);
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		$text = do_shortcode( $text ); //allow shortcodes
		$icon_color = htmlspecialchars($instance['icon_color']);
		$icon_size = htmlspecialchars($instance['icon_size']);
		//$btn_text = htmlspecialchars($instance['btn_text']);
		//$btn_style = htmlspecialchars($instance['btn_style']);
		//$btn_url = htmlspecialchars($instance['btn_url']);

		// Loop through this for each column and build individual fields from the array
		$num_columns = 0; $icons_only = true;
		for ($col = 1; $col <= $max_columns; $col++) {

			$col_title[$col] = strip_tags($instance['col_title_' . $col]);

			// Allow HTML if user is authorized. Note wp_filter_post_kses() expects slashed.
			$col_text[$col] = do_shortcode( $col_text[$col] ); //allow shortcodes
			//if ( current_user_can('unfiltered_html') ) {
				$col_subtitle[$col] = $instance['col_subtitle_' . $col];
				$col_text[$col] = $instance['col_text_' . $col];
			/*} else { 
				$col_subtitle[$col] = stripslashes( wp_filter_post_kses( addslashes($instance['col_subtitle_' . $col]) ) ); 		
				$col_text[$col] = stripslashes( wp_filter_post_kses( addslashes($instance['col_text_' . $col]) ) );
			}*/ //endif current_user_can()

			$col_icon[$col] = stripslashes($instance['col_icon_' . $col]);
			$col_btn_text[$col] = stripslashes($instance['col_btn_text_' . $col]);
			$col_btn_style[$col] = stripslashes($instance['col_btn_style_' . $col]);
			$col_url[$col] = stripslashes($instance['col_url_' . $col]);
			
			if ( $col_title[$col] OR $col_subtitle[$col] OR $col_text[$col] ) $icons_only = false;
			//if ( $col_text[$col] ) $icons_only = false;
			if ( $col_title[$col] OR $col_subtitle[$col] OR $col_text[$col] OR $col_icon[$col] ) $num_columns++;
			
		} //endfor	
		
		//echo 'icons_only=' . $icons_only . '<br />'; //TEST	

		// Start the widget HTML

/*
		// Add colors and alignment to widget section
		$classes = $widget_classes
			. ( $bgcolor ? ' ' . $color_prefix . $bgcolor : '' )
			.( ( $alignment AND $alignment != 'left' ) ? ' ' . $alignment_prefix . $alignment : '' );

		// Apply filter to allow themes and plugins to override widget classes. To add a
		// filter, use: add_filter ( 'xsbf_widget_classes', my_widget_classes, 10, 3 );
		// The 10 is the priority and the 3 says to accept all 4 arguments.
		$classes = apply_filters ( 'xsbf_widget_classes', $classes, 'widget_xs_columns_widget', $args, $instance );

		$before_widget = str_ireplace( 'widget_xs_columns_widget', 'widget_xs_columns_widget ' . $classes, $before_widget ); 
*/

		echo $before_widget;

		// Display the widget title and other fields		
		if ( $title ) echo $before_title . $title . $after_title;
		if ( $subtitle ) echo '<h3>' . $subtitle . '</h3>'; 
		if ( $text ) echo '<p>' . $text . '</p>'; 
		//if ( $btn_url AND $btn_text ) echo '<p><a href="' . $btn_url . '" class="btn btn-lg btn-' . $btn_style .'">' . $btn_text . '</a></p>'; 

		// Determine what column style to use if any based on the widget area and number
		// of columns in this widget instance itself.
/*
		// If in the Sidebar, collapse to a single column. Assuming this for all themes 
		// is reasonable.
		if ( strtolower ( $name ) == 'sidebar' ) {
			$col_style = 'col-xs-12 centered'; 
		// If in the footer, assume theme already handles columns and don't add any column
		// logic at all. Handle variations of FooterX as well. This should be a reasonable 
		// assumption for most themes.
		} elseif ( strpos( strtolower ( $name ), 'footer' ) !== false ) {
			$col_style = '';
		// For columns with text, make it 2 rows of 2 columns on a portrait tablet display
		} elseif ( $num_columns == 4 AND !$icons_only ) {
			$col_style = 'col-sm-6 col-lg-3 centered';
		// For columns with icons only, display them in columns even on smartphones
		} elseif ( $icons_only ) {
			//$col_style = 'col-xs-6 centered';
			$col_style = 'col-xs-' . ( 12 / $num_columns ) . ' centered';
		// Otherwise, just make it the number of columns specified
		} else {
			$col_style = 'col-sm-' . ( 12 / $num_columns ) . ' centered';
		}
*/
		// If in the Sidebar, collapse to a single column.
		if ( strtolower ( $name ) == 'sidebar' ) {
			$col_style = 'col-xs-12 centered'; 
		// In the footer, columns are already handled so don't do anything to them
		//} elseif ( strpos( strtolower ( $name ), 'footer' ) !== false ) {
		} elseif ( strtolower ( $name ) == 'footer' ) {
			$col_style = '';
		// For columns with text, make it 2 rows of 2 columns on a portrait tablet display
		} elseif ( $num_columns == 4 AND !$icons_only ) {
			$col_style = 'col-sm-6 col-lg-3 centered';
		// For columns with icons only, display them in columns even on smartphones
		} elseif ( $icons_only ) {
			//$col_style = 'col-xs-6 centered';
			$col_style = 'col-xs-' . ( 12 / $num_columns ) . ' centered';
		// Otherwise, just make it the number of columns specified
		} else {
			$col_style = 'col-sm-' . ( 12 / $num_columns ) . ' centered';
		}

		// Allow our theme and others to override the column logic. To override, 
		// use: add_filter ( 'xsbf_narrow_widget_area', my_narrow_widget_area, 10, 3 );
		// Note that to disable columns altogether (as in a Footer that already has 
		// columns, set $col_style to blank.
		$col_style = apply_filters ( 'xsbf_widget_column_style', $col_style, 'widget_xs_columns_widget', $args );

		// Loop through all columns and apply Bootstrap grid to them
		for ($col = 1; $col <= $xsbf_plugin_options['max_columns']; $col++) {
			if ( $col_style AND $col == 1 ) echo '<div class="row">';
			
			if ( $col_title[$col] OR $col_subtitle[$col] OR $col_text[$col] OR $col_icon[$col] ) {
				if ( ! $icons_only ) echo '<div' 
					.( $col_style ? ' class="' . $col_style . '"' : '' )
					.'>';

				if ( $col_title[$col] ) echo '<h2>' . $col_title[$col] . '</h2>';
				if ( $col_url[$col] AND $col_icon[$col] ) {
					echo '<a href="' . $col_url[$col] . '">'
						//.'<i class="fa fa-4x fa-' . $col_icon[$col]
						//.'<i class="' . $icon_large . ' ' . $icon_prefix . $col_icon[$col]
						//.'<i class="fa fa-fw ' 
						.'<i class="' . $fa_prefix 
						.( $icon_size ? $icon_prefix . $icon_size : '' )
						.' ' . $icon_prefix . $col_icon[$col]
						.( $icon_color ? ' ' . $color_prefix . $xsbf_plugin_options['colors'][$icon_color] : '' )
						.'">&nbsp;</i>'
						.'</a>'; 
				} elseif ( $col_icon[$col] ) {
					//echo '<i class="fa fa-4x fa-' . $col_icon[$col]
					//echo '<i class="' . $icon_large . ' ' . $icon_prefix . $col_icon[$col]
					//echo '<i class="fa fa-fw ' 
					echo '<i class="' . $fa_prefix 
						.($icon_size ? $icon_prefix . $icon_size : '' ) 
						.' ' . $icon_prefix . $col_icon[$col]
						.( $icon_color ? ' ' . $color_prefix . $xsbf_plugin_options['colors'][$icon_color] : '' )
						. '">&nbsp;</i>';			
				} //endif $col_url
				if ( $col_subtitle[$col] ) echo '<h3>' . $col_subtitle[$col] . '</h3>';
				if ( $col_text[$col] ) echo '<p>' . $col_text[$col] . '</p>';

				//if ( $col_btn_text[$col] ) echo '<p>' . $col_btn_text[$col] . '</p>';
				if ( $col_url[$col] AND $col_btn_text[$col] ) {
					echo '<a href="' . $col_url[$col] . '" class="btn btn-lg ' . $xsbf_plugin_options['buttons'][$col_btn_style[$col]] .'">' . $col_btn_text[$col] . '</a>';
				}
			
				if ( ! $icons_only ) echo '</div><!-- col -->';
				
				if ( $col_style AND !$icons_only AND $num_columns == 4 AND $col == 2 ) echo '<div class="clearfix hidden-lg"></div>';

			} //endif $col_title, etc.
			
			if ( $col_style AND $col == $xsbf_plugin_options['max_columns'] ) echo '</div><!-- row -->';
		} //endfor		
		
		echo $after_widget;

	} //endfunction

	/** 
	 * This function updates the widget settings to the database
	 */
	function update( $new_instance, $old_instance ) {
	
		global $xsbf_plugin_options;

		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		
		// Allow HTML if user is authorized. Note wp_filter_post_kses() expects slashed.
		if ( current_user_can('unfiltered_html') ) {
			$instance['subtitle'] =  $new_instance['subtitle'];
			$instance['text'] =  $new_instance['text'];
		} else { 
			$instance['subtitle'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['subtitle']) ) ); 		
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) );
		} //endif current_user_can()
		$instance['bgcolor'] = stripslashes($new_instance['bgcolor']);
		$instance['alignment'] = stripslashes($new_instance['alignment']);
		$instance['icon_color'] = stripslashes($new_instance['icon_color']);
		$instance['icon_size'] = stripslashes($new_instance['icon_size']);
		//$instance['btn_text'] = stripslashes($new_instance['btn_text']);
		//$instance['btn_style'] = stripslashes($new_instance['btn_style']);
		//$instance['btn_url'] = stripslashes($new_instance['btn_url']);
		
		// Loop through this for each column
		//for ($col = 1; $col <= $this->max_columns; $col++) {
		for ($col = 1; $col <= $xsbf_plugin_options['max_columns']; $col++) {
			$instance['col_title_' . $col] = strip_tags($new_instance['col_title_' . $col]);

			// Allow HTML if user is authorized. Note wp_filter_post_kses() expects slashed.
			if ( current_user_can('unfiltered_html') ) {
				$instance['col_subtitle_' . $col] = $new_instance['col_subtitle_' . $col];
				$instance['col_text_' . $col] = $new_instance['col_text_' . $col];
			} else { // wp_filter_post_kses() expects slashed
				$instance['col_subtitle_' . $col] = stripslashes( wp_filter_post_kses( addslashes($new_instance['col_subtitle_' . $col]) ) ); 		
				$instance['col_text_' . $col] = stripslashes( wp_filter_post_kses( addslashes($new_instance['col_text_' . $col]) ) );
			} //endif current_user_can()
			$instance['col_icon_' . $col] = stripslashes($new_instance['col_icon_' . $col]);
			$instance['col_btn_text_' . $col] = stripslashes($new_instance['col_btn_text_' . $col]);
			$instance['col_btn_style_' . $col] = stripslashes($new_instance['col_btn_style_' . $col]);
			$instance['col_url_' . $col] = stripslashes($new_instance['col_url_' . $col]);
		} //endfor		
				
		return $instance;
	}

	/** 
	 * This function displays the widget settings form
	 */
	function form( $instance ) {

		global $xsbf_plugin_options;
			
		$title = strip_tags($instance['title']);
		$subtitle = esc_attr($instance['subtitle']);
		$text = esc_textarea($instance['text']);
		$bgcolor = stripslashes($instance['bgcolor']);
		$alignment = stripslashes($instance['alignment']);
		$icon_color = stripslashes($instance['icon_color']);
		$icon_size = stripslashes($instance['icon_size']);
		//$btn_text = stripslashes($instance['btn_text']);
		//$btn_style = stripslashes($instance['btn_style']);
		//$btn_url = stripslashes($instance['btn_url']);

		// Loop through this for each column
		for ($col = 1; $col <= $xsbf_plugin_options['max_columns']; $col++) {
			$col_title[$col] = strip_tags($instance['col_title_' . $col]);
			$col_subtitle[$col] = esc_attr($instance['col_subtitle_' . $col]);
			$col_text[$col] = esc_attr($instance['col_text_' . $col]);
			$col_icon[$col] = stripslashes($instance['col_icon_' . $col]);
			$col_btn_text[$col] = stripslashes($instance['col_btn_text_' . $col]);
			$col_btn_style[$col] = stripslashes($instance['col_btn_style_' . $col]);
			$col_url[$col] = stripslashes($instance['col_url_' . $col]);
		} //endfor
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
			//foreach ( $this->colors as $color ) :
			foreach ( $xsbf_plugin_options['colors'] as $color ) :
			?>
				<option value="<?php echo esc_attr($color) ?>" <?php selected($color, $bgcolor) ?>><?php echo $color; ?></option>
			<?php				
			endforeach;
			?>
        </select>
		<br /><small><?php _e('(Choose from our color palette)'); ?></small></p>
-->

<!--
		<p><label for="<?php echo $this->get_field_id('icon_color'); ?>"><?php _e('Icon Color:'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('icon_color'); ?>" name="<?php echo $this->get_field_name('icon_color'); ?>">
			<?php 
			//foreach ( $this->colors as $color ) :
			foreach ( $xsbf_plugin_options['colors'] as $color_option => $color_css ) :
			?>
				<option value="<?php echo esc_attr($color_option) ?>" <?php selected($color_option, $icon_color) ?>><?php echo $color_option; ?></option>
			<?php				
			endforeach;
			?>
        </select>
-->
		<!-- <br /><small><?php _e('(Choose from our color palette)'); ?></small></p> -->

		<p><label for="<?php echo $this->get_field_id('icon_size'); ?>"><?php _e('Icon Size:'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('icon_size'); ?>" name="<?php echo $this->get_field_name('icon_size'); ?>">
			<?php 
			//foreach ( $this->colors as $color ) :
			//foreach ( $xsbf_plugin_options['sizes'] as $size ) :
			foreach ( $xsbf_plugin_options['sizes'] as $size_option => $size_css ) :
			?>
				<option value="<?php echo esc_attr($size_option) ?>" <?php selected($size_option, $icon_size) ?>><?php echo $size_option; ?></option>
			<?php				
			endforeach;
			?>
        </select>
		<!-- <br /><small><?php _e('(Choose a font size in ems)'); ?></small></p> -->

<!--
		<p><label for="<?php echo $this->get_field_id('alignment'); ?>"><?php _e('Alignment:'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('alignment'); ?>" name="<?php echo $this->get_field_name('alignment'); ?>">
			<?php 
			//foreach ( $this->alignments as $alignment_option ) :
			foreach ( $xsbf_plugin_options['alignments'] as $alignment_option ) :
			?>
				<option value="<?php echo esc_attr($alignment_option) ?>" <?php selected($alignment_option, $alignment) ?>><?php echo $alignment_option; ?></option>
			<?php				
			endforeach;
			?>
        </select>
		<br /><small><?php _e('(Alignment applies to title, subtitle, and text)'); ?></small></p>
-->

		<p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Body Text:'); ?></label>
		<textarea class="widefat" rows="4" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
		<br /><small><?php _e('(Body text allows basic HTML tags)'); ?></small></p>

<!--
		<p><label for="<?php echo $this->get_field_id('btn_text'); ?>"><?php _e('Button Text (Label):'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('btn_text'); ?>" name="<?php echo $this->get_field_name('btn_text'); ?>" type="text" value="<?php echo $btn_text; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('btn_style'); ?>"><?php _e('Button Style:'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('btn_style'); ?>" name="<?php echo $this->get_field_name('btn_style'); ?>">
			<?php 
			//foreach ( $this->styles as $style ) :
			foreach ( $xsbf_plugin_options['buttons'] as $style ) :
			?>
				<option value="<?php echo esc_attr($style) ?>" <?php selected($style, $btn_style) ?>><?php echo $style; ?></option>
			<?php				
			endforeach;
			?>
        </select>
		<br /><small><?php _e('(Choose from our button styles)'); ?></small></p>
-->
<!--
		<p><label for="<?php echo $this->get_field_id('btn_url'); ?>"><?php _e('Button Link (URL):'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('btn_url'); ?>" name="<?php echo $this->get_field_name('btn_url'); ?>" type="text" value="<?php echo $btn_url; ?>" /></p>
-->
		<?php
		// Loop through this section for each column
		//for ($col = 1; $col <= $this->max_columns; $col++) :
		for ($col = 1; $col <= $xsbf_plugin_options['max_columns']; $col++) : ?>
			<hr>
			<p><b>Column <?php echo $col; ?></b></p>

			<p><label for="<?php echo $this->get_field_id('col_title_' . $col); ?>"><?php _e('Title'); //echo ' ' . $col . ':'; ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('col_title_' . $col); ?>" name="<?php echo $this->get_field_name('col_title_' . $col); ?>" type="text" value="<?php echo $col_title[$col]; ?>" /></p>

			<p><label for="<?php echo $this->get_field_id('col_icon_' . $col); ?>"><?php _e('Icon:'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('col_icon_' . $col); ?>" name="<?php echo $this->get_field_name('col_icon_' . $col); ?>">
				<?php 
				foreach ( $this->icons as $icon ) :
				?>
					<option value="<?php echo esc_attr($icon) ?>" <?php selected($icon, $col_icon[$col]) ?>><?php echo $icon; ?></option>
				<?php				
				endforeach;
				?>
			</select></p>

			<p><label for="<?php echo $this->get_field_id('col_btn_text_' . $col); ?>"><?php _e('Button Text:'); //echo ' ' . $col . ':'; ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('col_btn_text_' . $col); ?>" name="<?php echo $this->get_field_name('col_btn_text_' . $col); ?>" type="text" value="<?php echo $col_btn_text[$col]; ?>" /></p>

			<p><label for="<?php echo $this->get_field_id('col_btn_style_' . $col); ?>"><?php _e('Button Style:'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('col_btn_style_' . $col); ?>" name="<?php echo $this->get_field_name('col_btn_style_' . $col); ?>">
				<?php 
			foreach ( $xsbf_plugin_options['buttons'] as $button_option => $button_css ) :
				?>
					<option value="<?php echo esc_attr($button_option) ?>" <?php selected($button_option, $col_btn_style[$col]) ?>><?php echo $button_option; ?></option>
				<?php				
				endforeach;
				?>
			</select></p>
			<!-- <br /><small><?php _e('(Choose from our button styles)'); ?></small></p> -->


			<p><label for="<?php echo $this->get_field_id('col_url_' . $col); ?>"><?php _e('Link (URL)'); //echo ' ' . $col . ':'; ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('col_url_' . $col); ?>" name="<?php echo $this->get_field_name('col_url_' . $col); ?>" type="text" value="<?php echo $col_url[$col]; ?>" /></p>

			<p><label for="<?php echo $this->get_field_id('col_subtitle_' . $col); ?>"><?php _e('Subtitle'); //echo ' ' . $col . ':'; ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('col_subtitle_' . $col); ?>" name="<?php echo $this->get_field_name('col_subtitle_' . $col); ?>" type="text" value="<?php echo $col_subtitle[$col]; ?>" /></p>

			<p><label for="<?php echo $this->get_field_id('col_text_' . $col); ?>"><?php _e('Body Text'); //echo ' ' . $col . ':'; ?></label>
			<textarea class="widefat" rows="4" cols="20" id="<?php echo $this->get_field_id('col_text_' . $col); ?>" name="<?php echo $this->get_field_name('col_text_' . $col); ?>"><?php echo $col_text[$col]; ?></textarea></p>

		<?php endfor; ?>
	<?php
	} // endfunction
	
	/**
	 * Create the [flat_bootstrap_section] shortcode
	 */
	public function xs_columns_widget_shortcode( $atts, $text = '' ) {
	
		//ini_set ( 'display_errors', 1 ); //TEST
		
		global $xsbf_plugin_options;

		$defaults = array (
			'title' => '',
			'subtitle' => '',
			'bgcolor' => '',
			'alignment' => 'center',
			//'text' => '',
			'text' => $text,
			'icon_color' => '',
			'icon_size' => '',
			//'btn_text' => '',
			//'btn_style' => 'primary',
			//'btn_url' => ''
		);

		// Loop through this for each column
		for ($col = 1; $col <= $xsbf_plugin_options['max_columns']; $col++) {
			$defaults['col_title_' . $col] = ''; 
			$defaults['col_subtitle_' . $col] = '';
			$defaults['col_text_' . $col] = '';
			$defaults['col_icon_' . $col] = '';
			$defaults['col_btn_text_' . $col] = '';
			$defaults['col_btn_style_' . $col] = '';
			$defaults['col_url_' . $col] = '';
		} //endfor

		$args = shortcode_atts( $defaults, $atts, 'flat_bootstrap_columns' );
		//print_r( $args ); //TEST
		
		// To help users with "debugging" use of this shortcode, display a message
		// if they didn't fill out the parameters correctly.
		if ( !$args['title'] AND !$args['subtitle'] AND !$args['text'] ) $args['text'] = __( 'You need to add some parameters and/or text, such as &lbrack;flat_bootstrap_columns title="MY TITLE" subtitle="My Subtitle" col_icon_1="facebook" col_icon_2="twitter" col_icon_3="google-plus"&rbrack;This is just an example.&lbrack;/flat_bootstrap_columns&rbrack;' );
	
		// Since the widget uses "echo", collect up all that to return at the end
		ob_start();

		// Allow themes or plugins to alter this. Note we also pass the args so that the
		// filter has all the paremeters to work with if needed. To add a filter, use:
		// add_filter ( 'xsbf_widget_area_defaults', my_widget_area_defaults, 10, 3 );
		// The 10 is the priority and the 3 says to accept all 3 arguments.
		$widget_area_defaults = array(
			'before_widget' => '<div class="widget widget_xs_columns_widget"><div class="container-fluid">',
			'before_title' 	=> '<h2 class="widget-title">',
			'after_title' 	=> '</h2>',
			'after_widget' 	=> '</div></div>'
		);
		$widget_area_defaults = apply_filters ( 'xsbf_widget_area_defaults', $widget_area_defaults, 'widget_xs_columns_widget', $args );

		// The first parameter to the widget is the default widget area fields
		// such as before_widget, before_title, etc. The second parameter is the
		// actual shortcode arguments to use to build the widget content.
		$xs_columns_widget = new XS_Columns_Widget();
		$xs_columns_widget->widget( $widget_area_defaults, $args );

		// Take the echo'd output, flush the buffer, and return the output
		$return = ob_get_contents();	
		ob_end_clean();
		
		//ini_set ( 'display_errors', 0 ); //TEST

		return $return;

	} //endfunction

	/* 
	 * Declare the $icons here at the end simply because it is a huge list
	 */

	// Font Awesome 4.3.0 by @davegandy - http://fontawesome.io - @fontawesome
	//protected $fa_prefix = 'fa fa-fw '; 
	protected $fa_prefix = 'fa '; 
	protected $icon_prefix = 'fa-'; 
	//protected $icon_large = 'fa-4x'; 
	protected $icons = array( 
		'', 
		'adjust', 
		'adn', 
		'align-center', 
		'align-justify', 
		'align-left', 
		'align-right', 
		'ambulance', 
		'anchor', 
		'android', 
		'angellist', 
		'angle-double-down', 
		'angle-double-left', 
		'angle-double-right', 
		'angle-double-up', 
		'angle-down', 
		'angle-left', 
		'angle-right', 
		'angle-up', 
		'apple', 
		'archive', 
		'area-chart', 
		'arrow-circle-down', 
		'arrow-circle-left', 
		'arrow-circle-o-down', 
		'arrow-circle-o-left', 
		'arrow-circle-o-right', 
		'arrow-circle-o-up', 
		'arrow-circle-right', 
		'arrow-circle-up', 
		'arrow-down', 
		'arrow-left', 
		'arrow-right', 
		'arrow-up', 
		'arrows', 
		'arrows-alt', 
		'arrows-h', 
		'arrows-v', 
		'asterisk', 
		'at', 
		'automobile', 
		'backward', 
		'ban', 
		'bank', 
		'bar-chart', 
		'bar-chart-o', 
		'barcode', 
		'bars', 
		'bed', 
		'beer', 
		'behance', 
		'behance-square', 
		'bell', 
		'bell-o', 
		'bell-slash', 
		'bell-slash-o', 
		'bicycle', 
		'binoculars', 
		'birthday-cake', 
		'bitbucket', 
		'bitbucket-square', 
		'bitcoin', 
		'bold', 
		'bolt', 
		'bomb', 
		'book', 
		'bookmark', 
		'bookmark-o', 
		'briefcase', 
		'btc', 
		'bug', 
		'building', 
		'building-o', 
		'bullhorn', 
		'bullseye', 
		'bus', 
		'buysellads', 
		'cab', 
		'calculator', 
		'calendar', 
		'calendar-o', 
		'camera', 
		'camera-retro', 
		'car', 
		'caret-down', 
		'caret-left', 
		'caret-right', 
		'caret-square-o-down', 
		'caret-square-o-left', 
		'caret-square-o-right', 
		'caret-square-o-up', 
		'caret-up', 
		'cart-arrow-down', 
		'cart-plus', 
		'cc', 
		'cc-amex', 
		'cc-discover', 
		'cc-mastercard', 
		'cc-paypal', 
		'cc-stripe', 
		'cc-visa', 
		'certificate', 
		'chain', 
		'chain-broken', 
		'check', 
		'check-circle', 
		'check-circle-o', 
		'check-square', 
		'check-square-o', 
		'chevron-circle-down', 
		'chevron-circle-left', 
		'chevron-circle-right', 
		'chevron-circle-up', 
		'chevron-down', 
		'chevron-left', 
		'chevron-right', 
		'chevron-up', 
		'child', 
		'circle', 
		'circle-o', 
		'circle-o-notch', 
		'circle-thin', 
		'clipboard', 
		'clock-o', 
		'close', 
		'cloud', 
		'cloud-download', 
		'cloud-upload', 
		'cny', 
		'code', 
		'code-fork', 
		'codepen', 
		'coffee', 
		'cog', 
		'cogs', 
		'columns', 
		'comment', 
		'comment-o', 
		'comments', 
		'comments-o', 
		'compass', 
		'compress', 
		'connectdevelop', 
		'copy', 
		'copyright', 
		'credit-card', 
		'crop', 
		'crosshairs', 
		'css', 
		'cube', 
		'cubes', 
		'cut', 
		'cutlery', 
		'dashboard', 
		'dashcube', 
		'database', 
		'dedent', 
		'delicious', 
		'desktop', 
		'deviantart', 
		'diamond', 
		'digg', 
		'dollar', 
		'dot-circle-o', 
		'download', 
		'dribbble', 
		'dropbox', 
		'drupal', 
		'edit', 
		'eject', 
		'ellipsis-h', 
		'ellipsis-v', 
		'empire', 
		'envelope', 
		'envelope-o', 
		'envelope-square', 
		'eraser', 
		'eur', 
		'euro', 
		'exchange', 
		'exclamation', 
		'exclamation-circle', 
		'exclamation-triangle', 
		'expand', 
		'external-link', 
		'external-link-square', 
		'eye', 
		'eye-slash', 
		'eyedropper', 
		'facebook', 
		'facebook-', 
		'facebook-official', 
		'facebook-square', 
		'fast-backward', 
		'fast-forward', 
		'fax', 
		'female', 
		'fighter-jet', 
		'file', 
		'file-archive-o', 
		'file-audio-o', 
		'file-code-o', 
		'file-excel-o', 
		'file-image-o', 
		'file-movie-o', 
		'file-o', 
		'file-pdf-o', 
		'file-photo-o', 
		'file-picture-o', 
		'file-powerpoint-o', 
		'file-sound-o', 
		'file-text', 
		'file-text-o', 
		'file-video-o', 
		'file-word-o', 
		'file-zip-o', 
		'files-o', 
		'film', 
		'filter', 
		'fire', 
		'fire-extinguisher', 
		'flag', 
		'flag-checkered', 
		'flag-o', 
		'flash', 
		'flask', 
		'flickr', 
		'floppy-o', 
		'folder', 
		'folder-o', 
		'folder-open', 
		'folder-open-o', 
		'font', 
		'forumbee', 
		'forward', 
		'foursquare', 
		'frown-o', 
		'futbol-o', 
		'gamepad', 
		'gavel', 
		'gbp', 
		'ge', 
		'gear', 
		'gears', 
		'genderless', 
		'gift', 
		'git', 
		'git-square', 
		'github', 
		'github-alt', 
		'github-square', 
		'gittip', 
		'glass', 
		'globe', 
		'google', 
		'google-plus', 
		'google-plus-square', 
		'google-wallet', 
		'graduation-cap', 
		'gratipay', 
		'group', 
		'h-square', 
		'hacker-news', 
		'hand-o-down', 
		'hand-o-left', 
		'hand-o-right', 
		'hand-o-up', 
		'hdd-o', 
		'header', 
		'headphones', 
		'heart', 
		'heart-o', 
		'heartbeat', 
		'history', 
		'home', 
		'hospital-o', 
		'hotel', 
		'html', 
		'ils', 
		'image', 
		'inbox', 
		'indent', 
		'info', 
		'info-circle', 
		'inr', 
		'instagram', 
		'institution', 
		'ioxhost', 
		'italic', 
		'joomla', 
		'jpy', 
		'jsfiddle', 
		'key', 
		'keyboard-o', 
		'krw', 
		'language', 
		'laptop', 
		'lastfm', 
		'lastfm-square', 
		'lea', 
		'leanpub', 
		'legal', 
		'lemon-o', 
		'level-down', 
		'level-up', 
		'life-bouy', 
		'life-buoy', 
		'life-ring', 
		'life-saver', 
		'lightbulb-o', 
		'line-chart', 
		'link', 
		'linkedin', 
		'linkedin-square', 
		'linux', 
		'list', 
		'list-alt', 
		'list-ol', 
		'list-ul', 
		'location-arrow', 
		'lock', 
		'long-arrow-down', 
		'long-arrow-left', 
		'long-arrow-right', 
		'long-arrow-up', 
		'magic', 
		'magnet', 
		'mail-forward', 
		'mail-reply', 
		'mail-reply-all', 
		'male', 
		'map-marker', 
		'mars', 
		'mars-double', 
		'mars-stroke', 
		'mars-stroke-h', 
		'mars-stroke-v', 
		'maxcdn', 
		'meanpath', 
		'medium', 
		'medkit', 
		'meh-o', 
		'mercury', 
		'microphone', 
		'microphone-slash', 
		'minus', 
		'minus-circle', 
		'minus-square', 
		'minus-square-o', 
		'mobile', 
		'mobile-phone', 
		'money', 
		'moon-o', 
		'mortar-board', 
		'motorcycle', 
		'music', 
		'navicon', 
		'neuter', 
		'newspaper-o', 
		'openid', 
		'outdent', 
		'pagelines', 
		'paint-brush', 
		'paper-plane', 
		'paper-plane-o', 
		'paperclip', 
		'paragraph', 
		'paste', 
		'pause', 
		'paw', 
		'paypal', 
		'pencil', 
		'pencil-square', 
		'pencil-square-o', 
		'phone', 
		'phone-square', 
		'photo', 
		'picture-o', 
		'pie-chart', 
		'pied-piper', 
		'pied-piper-alt', 
		'pinterest', 
		'pinterest-p', 
		'pinterest-square', 
		'plane', 
		'play', 
		'play-circle', 
		'play-circle-o', 
		'plug', 
		'plus', 
		'plus-circle', 
		'plus-square', 
		'plus-square-o', 
		'power-of', 
		'print', 
		'puzzle-piece', 
		'qq', 
		'qrcode', 
		'question', 
		'question-circle', 
		'quote-left', 
		'quote-right', 
		'ra', 
		'random', 
		'rebel', 
		'recycle', 
		'reddit', 
		'reddit-square', 
		'refresh', 
		'remove', 
		'renren', 
		'reorder', 
		'repeat', 
		'reply', 
		'reply-all', 
		'retweet', 
		'rmb', 
		'road', 
		'rocket', 
		'rotate-left', 
		'rotate-right', 
		'rouble', 
		'rss', 
		'rss-square', 
		'rub', 
		'ruble', 
		'rupee', 
		'save', 
		'scissors', 
		'search', 
		'search-minus', 
		'search-plus', 
		'sellsy', 
		'send', 
		'send-o', 
		'server', 
		'share', 
		'share-alt', 
		'share-alt-square', 
		'share-square', 
		'share-square-o', 
		'shekel', 
		'sheqel', 
		'shield', 
		'ship', 
		'shirtsinbulk', 
		'shopping-cart', 
		'sign-in', 
		'sign-out', 
		'signal', 
		'simplybuilt', 
		'sitemap', 
		'skyatlas', 
		'skype', 
		'slack', 
		'sliders', 
		'slideshare', 
		'smile-o', 
		'soccer-ball-o', 
		'sort', 
		'sort-alpha-asc', 
		'sort-alpha-desc', 
		'sort-amount-asc', 
		'sort-amount-desc', 
		'sort-asc', 
		'sort-desc', 
		'sort-down', 
		'sort-numeric-asc', 
		'sort-numeric-desc', 
		'sort-up', 
		'soundcloud', 
		'space-shuttle', 
		'spinner', 
		'spoon', 
		'spotify', 
		'square', 
		'square-o', 
		'stack-exchange', 
		'stack-overflow', 
		'star', 
		'star-hal', 
		'star-half-empty', 
		'star-half-full', 
		'star-half-o', 
		'star-o', 
		'steam', 
		'steam-square', 
		'step-backward', 
		'step-forward', 
		'stethoscope', 
		'stop', 
		'street-view', 
		'strikethrough', 
		'stumbleupon', 
		'stumbleupon-circle', 
		'subscript', 
		'subway', 
		'suitcase', 
		'sun-o', 
		'superscript', 
		'support', 
		'table', 
		'tablet', 
		'tachometer', 
		'tag', 
		'tags', 
		'tasks', 
		'taxi', 
		'tencent-weibo', 
		'terminal', 
		'text-height', 
		'text-width', 
		'th', 
		'th-large', 
		'th-list', 
		'thumb-tack', 
		'thumbs-down', 
		'thumbs-o-down', 
		'thumbs-o-up', 
		'thumbs-up', 
		'ticket', 
		'times', 
		'times-circle', 
		'times-circle-o', 
		'tint', 
		'toggle-down', 
		'toggle-left', 
		'toggle-of', 
		'toggle-on', 
		'toggle-right', 
		'toggle-up', 
		'train', 
		'transgender', 
		'transgender-alt', 
		'trash', 
		'trash-o', 
		'tree', 
		'trello', 
		'trophy', 
		'truck', 
		'try', 
		'tty', 
		'tumblr', 
		'tumblr-square', 
		'turkish-lira', 
		'twitch', 
		'twitter', 
		'twitter-square', 
		'umbrella', 
		'underline', 
		'undo', 
		'university', 
		'unlink', 
		'unlock', 
		'unlock-alt', 
		'unsorted', 
		'upload', 
		'usd', 
		'user', 
		'user-md', 
		'user-plus', 
		'user-secret', 
		'user-times', 
		'users', 
		'venus', 
		'venus-double', 
		'venus-mars', 
		'viacoin', 
		'video-camera', 
		'vimeo-square', 
		'vine', 
		'vk', 
		'volume-down', 
		'volume-of', 
		'volume-up', 
		'warning', 
		'wechat', 
		'weibo', 
		'weixin', 
		'whatsapp', 
		'wheelchair', 
		'wifi', 
		'windows', 
		'won', 
		'wordpress', 
		'wrench', 
		'xing', 
		'xing-square', 
		'yahoo', 
		'yelp', 
		'yen', 
		'youtube', 
		'youtube-play', 
		'youtube-square' 
		); 
	
} //endclass

//ini_set ( 'display_errors', 0 ); //TEST
