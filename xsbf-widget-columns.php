<?php

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
			'FB Columns Widget',
			array( 'description' => __( 'Colored section with columns of icons and/or text' ) 
			)
		);
	 }
	
	/**
	 * Note that protected variable declarations at the very end due to their length.
	 * e.g. $colors, $alignments, $styles, $icons.
	 */

	/** 
	 * Main function to display the widget on the front-end
	 */
	function widget($args, $instance) {
	
		// Get widget area settings, such as before/after widget, before/after title
	    extract( $args );

		// Get this specific widget instance's parameters
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$subtitle = $instance['subtitle'];
		$bgcolor = htmlspecialchars($instance['bgcolor']);
		$alignment = htmlspecialchars($instance['alignment']);
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		$icon_color = htmlspecialchars($instance['icon_color']);
		$btn_text = htmlspecialchars($instance['btn_text']);
		$btn_style = htmlspecialchars($instance['btn_style']);
		$btn_url = htmlspecialchars($instance['btn_url']);

		// Loop through this for each column
		for ($col = 1; $col <= $this->max_columns; $col++) {
			$col_title[$col] = strip_tags($instance['col_title_' . $col]);

			// Allow HTML if user is authorized. Note wp_filter_post_kses() expects slashed.
			if ( current_user_can('unfiltered_html') ) {
				$col_subtitle[$col] = $instance['col_subtitle_' . $col];
				$col_text[$col] = $instance['col_text_' . $col];
			} else { 
				$col_subtitle[$col] = stripslashes( wp_filter_post_kses( addslashes($instance['col_subtitle_' . $col]) ) ); 		
				$col_text[$col] = stripslashes( wp_filter_post_kses( addslashes($instance['col_text_' . $col]) ) );
			} //endif current_user_can()
			$col_icon[$col] = stripslashes($instance['col_icon_' . $col]);
			$col_url[$col] = stripslashes($instance['col_url_' . $col]);
		} //endfor		

		// Output the widget
		echo $before_widget;
		
		// If Page Top or Page Bottom Widget area, then kill off container and
		// add it later after the colored section div
		// TO-DO: For shortcode, check whether full-width page!
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
			.( ( $alignment AND $alignment != 'left' ) ? 'align' . $alignment . ' ' : '' )
			.( $padding_needed ? 'padding' : '' )
			.'">';
		if ( $container_needed ) echo '<div class="container">';

		if ( $title ) echo $before_title . $title . $after_title;
		if ( $subtitle ) echo '<h3>' . $subtitle . '</h3>'; 
		if ( $text ) echo '<p>' . $text . '</p>'; 

		if ( $btn_url AND $btn_text ) echo '<p><a href="' . $btn_url . '" class="btn btn-lg btn-' . $btn_style .'">' . $btn_text . '</a></p>'; 

		// First count which columns are not empty, so we can adjust the grid
		$num_columns = 0; $col_title_or_text = false;
		for ($col = 1; $col <= $this->max_columns; $col++) {
			if ( $col_title[$col] OR $col_subtitle[$col] OR $col_text[$col] OR $col_icon[$col] ) $num_columns++;
			if ( $col_title[$col] OR $col_subtitle[$col] OR $col_text[$col] ) $col_title_or_text = true;
		} //endfor
		//$num_columns = 4; // TEST
		
		// Set the Bootstrap grid column class based on number of columns. For 4 
		// columns where we have a title or text, make it 2 column on iPad.
		if ( $num_columns = 4 AND $col_title_or_text ) $col_style = 'col-sm-6 col-lg-3 centered';
		else $col_style = 'col-sm-' . ( 12 / $num_columns ) . ' centered';

		// Loop through all columns and apply Bootstrap grid to them
		for ($col = 1; $col <= $this->max_columns; $col++) {
			if ( $col == 1 ) echo '<div class="row">';
			
			if ( $col_title[$col] OR $col_subtitle[$col] OR $col_text[$col] OR $col_icon[$col] ) {
				echo '<div class="' . $col_style . '">';

				if ( $col_title[$col] ) echo '<h2>' . $col_title[$col] . '</h2>';
				if ( $col_url[$col] AND $col_icon[$col] ) {
					echo '<a href="' . $col_url[$col] 
						. '" class="fa icon-xlg fa-' . $col_icon[$col]
						.( $icon_color ? ' color-' . $icon_color : '' )
						. '"></a>';
				} elseif ( $col_icon[$col] ) {
					echo '<i class="fa icon-xlg fa-' . $col_icon[$col] 
									.( $icon_color ? ' color-' . $icon_color : '' )
									. '"></i>';			
				} //endif $col_url
				if ( $col_subtitle[$col] ) echo '<h3>' . $col_subtitle[$col] . '</h3>';
				if ( $col_text[$col] ) echo '<p>' . $col_text[$col] . '</p>';
			
				echo '</div><!-- col -->';

			} //endif $col_title, etc.
			
			if ( $col == $this->max_columns ) echo '</div><!-- row -->';
		} //endfor		
		
		if ( $container_needed ) echo '</div><!-- container -->';
		echo '</div><!-- section -->';

		echo $after_widget;

	} //endfunction

	/** 
	 * This function updates the widget settings to the database
	 */
	function update( $new_instance, $old_instance ) {
	
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
		$instance['btn_text'] = stripslashes($new_instance['btn_text']);
		$instance['btn_style'] = stripslashes($new_instance['btn_style']);
		$instance['btn_url'] = stripslashes($new_instance['btn_url']);
		
		// Loop through this for each column
		for ($col = 1; $col <= $this->max_columns; $col++) {
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
			$instance['col_url_' . $col] = stripslashes($new_instance['col_url_' . $col]);
		} //endfor		
				
		return $instance;
	}

	/** 
	 * This function displays the widget settings form
	 */
	function form( $instance ) {
	
		$title = strip_tags($instance['title']);
		$subtitle = esc_attr($instance['subtitle']);
		$text = esc_textarea($instance['text']);
		$bgcolor = stripslashes($instance['bgcolor']);
		$alignment = stripslashes($instance['alignment']);
		$icon_color = stripslashes($instance['icon_color']);
		$btn_text = stripslashes($instance['btn_text']);
		$btn_style = stripslashes($instance['btn_style']);
		$btn_url = stripslashes($instance['btn_url']);

		// Loop through this for each column
		for ($col = 1; $col <= $this->max_columns; $col++) {
			$col_title[$col] = strip_tags($instance['col_title_' . $col]);
			$col_subtitle[$col] = esc_attr($instance['col_subtitle_' . $col]);
			$col_text[$col] = esc_attr($instance['col_text_' . $col]);
			$col_icon[$col] = stripslashes($instance['col_icon_' . $col]);
			$col_url[$col] = stripslashes($instance['col_url_' . $col]);
		} //endfor
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

		<p><label for="<?php echo $this->get_field_id('icon_color'); ?>"><?php _e('Icon Color:'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('icon_color'); ?>" name="<?php echo $this->get_field_name('icon_color'); ?>">
			<?php 
			foreach ( $this->colors as $color ) :
			?>
				<option value="<?php echo esc_attr($color) ?>" <?php selected($color, $icon_color) ?>><?php echo $color; ?></option>
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
		<br /><small><?php _e('(Alignment applies to title, subtitle, and text)'); ?></small></p>

		<p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Body Text:'); ?></label>
		<textarea class="widefat" rows="4" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
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
		// Loop through this section for each column
		for ($col = 1; $col <= $this->max_columns; $col++) : ?>
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

			<p><label for="<?php echo $this->get_field_id('col_url_' . $col); ?>"><?php _e('Link (URL)'); //echo ' ' . $col . ':'; ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('col_url_' . $col); ?>" name="<?php echo $this->get_field_name('col_url_' . $col); ?>" type="text" value="<?php echo $col_url[$col]; ?>" /></p>

			<p><label for="<?php echo $this->get_field_id('col_subtitle_' . $col); ?>"><?php _e('Subtitle'); //echo ' ' . $col . ':'; ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('col_subtitle_' . $col); ?>" name="<?php echo $this->get_field_name('col_subtitle_' . $col); ?>" type="text" value="<?php echo $col_subtitle[$col]; ?>" /></p>

			<p><label for="<?php echo $this->get_field_id('col_text_' . $col); ?>"><?php _e('Body Text'); //echo ' ' . $col . ':'; ?></label>
			<textarea class="widefat" rows="4" cols="20" id="<?php echo $this->get_field_id('col_text_' . $col); ?>" name="<?php echo $this->get_field_name('col_text_' . $col); ?>"><?php echo $col_text[$col]; ?></textarea></p>

		<?php endfor; ?>
	<?php
	} // endfunction
	
	/* 
	 * Declare some protected variables for use in our class functions
	 */

	// Set the maximum number of columns to support. Start with 4 but could be
	// up to 6 and still fit the Bootstrap grid.
	protected $max_columns = 4;

	// Our color palette (colors renamed from Flat UI palette)
	protected $colors = array ( '', 'white', 'offwhite', 'lightgray', 'gray', 'darkgray', 'lightgreen', 'darkgreen', 'brightgreen', 'darkbrightgreen', 'yellow', 'lightorange', 'orange', 'darkorange', 'blue', 'darkblue', 'purple', 'darkpurple', 'midnightblue', 'darkmidnightblue', 'red', 'brightred', 'darkred', 'almostblack', 'notquiteblack', 'black' );

	// WordPress core theme (_S)
	protected $alignments = array( '', 'left', 'center', 'right' );
	
	// Bootstrap v3.3.2 (http://getbootstrap.com) plus our btn-hollow and btn-transparent	
	protected $styles = array( 'primary', 'success', 'info', 'warning', 'danger', 'hollow', 'default', 'link' ); 

	// Font Awesome 4.3.0 by @davegandy - http://fontawesome.io - @fontawesome
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

	/**
	 * Create the [flat_bootstrap_section] shortcode
	 */
	public function xs_columns_widget_shortcode( $atts ) {
	
		//ini_set ( 'display_errors', 1 ); //TEST

		// Create a widget instance
		$xs_columns_widget = new XS_Columns_Widget();

		$defaults = array (
			'title' => '',
			'subtitle' => '',
			'bgcolor' => '',
			'alignment' => 'center',
			'text' => '',
			'icon_color' => '',
			'btn_text' => '',
			'btn_style' => 'primary',
			'btn_url' => ''
		);

		// Loop through this for each column
		for ($col = 1; $col <= $xs_columns_widget->max_columns; $col++) {
			$defaults['col_title_' . $col] = ''; 
			$defaults['col_subtitle_' . $col] = '';
			$defaults['col_text_' . $col] = '';
			$defaults['col_icon_' . $col] = '';
			$defaults['col_url_' . $col] = '';
		} //endfor

		$args = shortcode_atts( $defaults, $atts, 'flat_bootstrap_columns' );
		//print_r( $args ); //TEST
		
		// To help users with "debugging" use of this shortcode, default in some
		// text if they didn't fill out the parameters correctly.
		if ( !$args['title'] AND !$args['subtitle'] AND !$args['text'] ) $args['text'] = __( 'You need to add some parameters, such as &lbrack;flat_bootstrap_columns title="MY TITLE" subtitle="My Subtitle" text="This is just an example"&rbrack;' );
	
		// Since the widget uses "echo", collect up all that to return at the end
		ob_start();

		// The first parameter to the widget is the default widget area fields
		// such as before_widget, before_title, etc. We need to mirror the 
		// settings from the actual Page Top widget area. The second parameter 
		// is the actual shortcode arguments to use to build the widget content.
		$widget_area_defaults = array(
			'before_widget' => '<div class="widget widget_xs_section_widget clearfix">',
			'before_title' 	=> '<h2 class="widget-title">',
			'after_title' 	=> '</h2>',
			'after_widget' 	=> '</div>',
		);
		$xs_columns_widget->widget( $widget_area_defaults, $args );

		// Take the echo'd output, flush the buffer, and return the output
		$return = ob_get_contents();	
		ob_end_clean();
		
		//ini_set ( 'display_errors', 0 ); //TEST

		return $return;

	} //endfunction
	
} //endclass
