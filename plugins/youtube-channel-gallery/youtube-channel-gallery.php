<?php
	/*
	Plugin Name: Youtube Channel Gallery
	Plugin URI: http://www.poselab.com/
	Description: Show a youtube video and a gallery of thumbnails for a youtube channel.
	Author: Javier Gómez Pose
	Author URI: http://www.poselab.com/
	Version: 1.7.5.1
	License: GPL2
		
		Copyright 2013 Javier Gómez Pose  (email : javierpose@gmail.com)

		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License, version 2, as 
		published by the Free Software Foundation.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/



	/**
	 * widget class.
	 */
	class YoutubeChannelGallery_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {

			//localization
			load_plugin_textdomain('youtube-channel-gallery', false, dirname(plugin_basename( __FILE__ ) ) . '/languages/' ); 
			add_shortcode('Youtube_Channel_Gallery', array($this, 'YoutubeChannelGallery_Shortcode'));   
		
			//load admin scripts
			add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts_and_styles'));
			
			parent::__construct(
				'youtubechannelgallery_widget', // Base ID
				 __( 'Youtube Channel Gallery', 'youtube-channel-gallery' ), // Name
				array( 'description' => __( 'Show a youtube video and a gallery of thumbnails for a youtube channel', 'youtube-channel-gallery' ), ) // Args
			);
		}

		/**
		 * Front-end display of widget.
		 */
		public function widget( $args, $instance ) {

			// Load JavaScript and stylesheets  
			$this->register_scripts_and_styles();

			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $before_widget;
				if ( ! empty( $title ) ){
					echo $before_title . $title . $after_title;
				}

				echo $this->ytchag_rss_markup($instance);

			echo $after_widget;
		}

		/**
		 * Sanitize widget form values as they are saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );

			// Feed options
			$instance['ytchag_feed'] = strip_tags( $new_instance['ytchag_feed'] );
			$instance['ytchag_user'] = strip_tags( $new_instance['ytchag_user'] );
			$instance['ytchag_feed_order'] = strip_tags( $new_instance['ytchag_feed_order'] );

			// Player options
			$instance['ytchag_video_width'] = strip_tags( $new_instance['ytchag_video_width'] );
			$instance['ytchag_ratio'] = strip_tags( $new_instance['ytchag_ratio'] );
			$instance['ytchag_theme'] = strip_tags( $new_instance['ytchag_theme'] );
			$instance['ytchag_color'] = strip_tags( $new_instance['ytchag_color'] );
			$instance['ytchag_quality'] = strip_tags( $new_instance['ytchag_quality'] );
			$instance['ytchag_autoplay'] = strip_tags( $new_instance['ytchag_autoplay'] );
			$instance['ytchag_rel'] = strip_tags( $new_instance['ytchag_rel'] );
			$instance['ytchag_showinfo'] = strip_tags( $new_instance['ytchag_showinfo'] );	
			
			// Thumbnail options
			$instance['ytchag_maxitems'] = strip_tags( $new_instance['ytchag_maxitems'] );
			$instance['ytchag_thumb_width'] = strip_tags( $new_instance['ytchag_thumb_width'] );
			$instance['ytchag_thumb_ratio'] = strip_tags( $new_instance['ytchag_thumb_ratio'] );
			$instance['ytchag_thumb_columns'] = strip_tags( $new_instance['ytchag_thumb_columns'] );
			$instance['ytchag_title'] = strip_tags( $new_instance['ytchag_title'] );
			$instance['ytchag_description'] = strip_tags( $new_instance['ytchag_description'] );
			$instance['ytchag_thumbnail_alignment'] = strip_tags( $new_instance['ytchag_thumbnail_alignment'] );
			$instance['ytchag_description_words_number'] = strip_tags( $new_instance['ytchag_description_words_number'] );

			// Link options
			$instance['ytchag_link'] = $new_instance['ytchag_link'];
			$instance['ytchag_link_tx'] = strip_tags( $new_instance['ytchag_link_tx'] );
			$instance['ytchag_link_window'] = strip_tags( $new_instance['ytchag_link_window'] );

			return $instance;
		}

		/**
		 * Back-end widget form.
		 */
		public function form( $instance ) {
			$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';

			// Feed options
			$ytchag_feed = isset( $instance['ytchag_feed'] ) ? esc_attr( $instance['ytchag_feed'] ) : '';
			$ytchag_user = isset( $instance['ytchag_user'] ) ? esc_attr( $instance['ytchag_user'] ) : ''; //left ytchag_user variable name for backward compatibility
			$ytchag_feed_order = isset( $instance['ytchag_feed_order'] ) ? esc_attr( $instance['ytchag_feed_order'] ) : '';


			// Player options
			$ytchag_video_width = isset( $instance['ytchag_video_width'] ) ? esc_attr( $instance['ytchag_video_width'] ) : ''; 
			$ytchag_ratio = isset( $instance['ytchag_ratio'] ) ? esc_attr( $instance['ytchag_ratio'] ) : ''; 
			$ytchag_theme = isset( $instance['ytchag_theme'] ) ? esc_attr( $instance['ytchag_theme'] ) : ''; 
			$ytchag_color = isset( $instance['ytchag_color'] ) ? esc_attr( $instance['ytchag_color'] ) : ''; 
			$ytchag_quality = isset( $instance['ytchag_quality'] ) ? esc_attr( $instance['ytchag_quality'] ) : ''; 
			$ytchag_autoplay = isset( $instance['ytchag_autoplay'] ) ? esc_attr( $instance['ytchag_autoplay'] ) : ''; 
			$ytchag_rel = isset( $instance['ytchag_rel'] ) ? esc_attr( $instance['ytchag_rel'] ) : ''; 
			$ytchag_showinfo = isset( $instance['ytchag_showinfo'] ) ? esc_attr( $instance['ytchag_showinfo'] ) : ''; 

			// Thumbnail options
			$ytchag_maxitems = isset( $instance['ytchag_maxitems'] ) ? esc_attr( $instance['ytchag_maxitems'] ) : ''; 
			$ytchag_thumb_width = isset( $instance['ytchag_thumb_width'] ) ? esc_attr( $instance['ytchag_thumb_width'] ) : ''; 
			$ytchag_thumb_ratio = isset( $instance['ytchag_thumb_ratio'] ) ? esc_attr( $instance['ytchag_thumb_ratio'] ) : ''; 
			$ytchag_thumb_columns = isset( $instance['ytchag_thumb_columns'] ) ? esc_attr( $instance['ytchag_thumb_columns'] ) : ''; 
			$ytchag_title = isset( $instance['ytchag_title'] ) ? esc_attr( $instance['ytchag_title'] ) : ''; 
			$ytchag_description = isset( $instance['ytchag_description'] ) ? esc_attr( $instance['ytchag_description'] ) : ''; 
			$ytchag_thumbnail_alignment = isset( $instance['ytchag_thumbnail_alignment'] ) ? esc_attr( $instance['ytchag_thumbnail_alignment'] ) : ''; 
			$ytchag_description_words_number = isset( $instance['ytchag_description_words_number'] ) ? esc_attr( $instance['ytchag_description_words_number'] ) : ''; 

			// Link options
			$ytchag_link = isset( $instance['ytchag_link'] ) ? esc_attr( $instance['ytchag_link'] ) : 0;
			$ytchag_link_tx = isset( $instance['ytchag_link_tx'] ) ? esc_attr( $instance['ytchag_link_tx'] ) : '';
			$ytchag_link_window = isset( $instance['ytchag_link_window'] ) ? esc_attr( $instance['ytchag_link_window'] ) : 0; 

			?>

			<div class="ytchg">
				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'youtube-channel-gallery' ); ?></label> 
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</p>

				<script type="text/javascript">
					jQuery(document).ready(function($) {

						//Update widget form after drag-and-drop (WP save bug)
						//http://wordpress.stackexchange.com/a/37707/16964
						$('#widgets-right').ajaxComplete(function(event, XMLHttpRequest, ajaxOptions){

							// determine which ajax request is this (we're after "save-widget")
							var request = {}, pairs = ajaxOptions.data.split('&'), i, split, widget;

							for(i in pairs){
								split = pairs[i].split('=');
								request[decodeURIComponent(split[0])] = decodeURIComponent(split[1]);
							}

							// only proceed if this was a widget-save request
							if(request.action && (request.action === 'save-widget')){

							// locate the widget block
							widget = $('input.widget-id[value="' + request['widget-id'] + '"]').parents('.widget');

							// trigger manual save, if this was the save request 
							// and if we didn't get the form html response (the wp bug)
							if(!XMLHttpRequest.responseText)
								wpWidgets.save(widget, 0, 1, 0);

							// we got an response, this could be either our request above,
							// or a correct widget-save call, so fire an event on which we can hook our js
							else
								$(document).trigger('saved_widget', widget);

							}

						});

						//tabs
						//---------------
						$('#tabs-<?php echo $this->id; ?> > div:not(:first)').hide();
						//$('#tabs-<?php echo $this->id; ?>-1').show();
						$('#tabs-<?php echo $this->id; ?> ul li:first').addClass('active');

						$('#tabs-<?php echo $this->id; ?> ul li a').click(function(){
							//not work on the current tab
							if(!$(this).parent().hasClass('active')){
								$('#tabs-<?php echo $this->id; ?> ul li').removeClass('active');
								$(this).parent().addClass('active');
								var currentTab = $(this).attr('href');
								//slideUp and slideDown to give it animation
								$('#tabs-<?php echo $this->id; ?> > div').slideUp('fast');
								$(currentTab).slideDown('fast');
							}
							return false;							
						});


						//checkboxes with associated content
						//---------------
						show_title_description ();


						$('#tabs-<?php echo $this->id; ?>-3 .ytchg-tit-desc a').click(function(){
							if(!$(this).parent().parent().hasClass('active')){
								slide_title_description ( 'slideDown' );								
							} else{
								slide_title_description ( 'slideUp' );	
							}
							return false;
						});


						function slide_title_description ( action ){
							if(action === 'slideDown'){								
								$('#tabs-<?php echo $this->id; ?>-3 .ytchg-title-and-description').slideDown('fast');
								$('#tabs-<?php echo $this->id; ?>-3 fieldset.ytchg-field-tit-desc').addClass('ytchg-fieldborder active');
							} else if(action === 'slideUp'){
								$('#tabs-<?php echo $this->id; ?>-3 .ytchg-title-and-description').slideUp('fast');
								$('#tabs-<?php echo $this->id; ?>-3 fieldset.ytchg-field-tit-desc').removeClass('ytchg-fieldborder active');
							}
						}

						function show_title_description (){
							if( $('#tabs-<?php echo $this->id; ?>-3 .ytchg-tit').is(':checked') || $('#tabs-<?php echo $this->id; ?>-3 .ytchg-desc').is(':checked')){
								$('#tabs-<?php echo $this->id; ?>-3 .ytchg-title-and-description').show();
								$('#tabs-<?php echo $this->id; ?>-3 fieldset.ytchg-field-tit-desc').addClass('ytchg-fieldborder active');
							} else{
								$('#tabs-<?php echo $this->id; ?>-3 .ytchg-title-and-description').hide();

							}
						}


						//Feed label title
						//---------------
						var feedSelect = '#<?php echo $this->get_field_id( 'ytchag_feed' ); ?>';
						var userLabel = 'label[for="<?php echo $this->get_field_id( 'ytchag_user' ); ?>"]';
						var feedOrder = '.<?php echo $this->get_field_id( 'ytchag_feed_order' ); ?>';

						changeFeedType ();
						$(feedSelect).change(function () {
							changeFeedType ();
						});

						function changeFeedType (){
							if($(feedSelect + ' option:selected').val() === 'user'){
								$(userLabel).text('<?php _e( 'YouTube user id:', 'youtube-channel-gallery' ); ?>');	
								$(feedOrder).slideUp('fast');
							}
							/*if($(feedSelect + ' option:selected').val() === 'userfav'){
								$(userLabel).text('<?php _e( 'YouTube user id:', 'youtube-channel-gallery' ); ?>');								
							}*/
							if($(feedSelect + ' option:selected').val() === 'playlist'){
								$(userLabel).text('<?php _e( 'YouTube playlist id:', 'youtube-channel-gallery' ); ?>');
								$(feedOrder).slideDown('fast');								
							}
						}
					});
				</script>


				<?php //http://wordpress.stackexchange.com/questions/5515/update-widget-form-after-drag-and-drop-wp-save-bug?>

				<div id="tabs-<?php echo $this->id; ?>" class="ytchgtabs">
					<ul class="ytchgtabs-tabs">
						<li><a href="#tabs-<?php echo $this->id; ?>-1"><?php _e( 'Feed', 'youtube-channel-gallery' ); ?></a></li>
						<li><a href="#tabs-<?php echo $this->id; ?>-2"><?php _e( 'Player', 'youtube-channel-gallery' ); ?></a></li>
						<li><a href="#tabs-<?php echo $this->id; ?>-3"><?php _e( 'Thumbnails', 'youtube-channel-gallery' ); ?></a></li>
						<li><a href="#tabs-<?php echo $this->id; ?>-4"><?php _e( 'Link', 'youtube-channel-gallery' ); ?></a></li>
					</ul>


					<?php 
					/*
					Feed Tab
					--------------------
					*/
					?>
					<div id="tabs-<?php echo $this->id; ?>-1" class="ytchgtabs-content">

						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_feed' ); ?>"><?php _e( 'Video feed type:', 'youtube-channel-gallery' ); ?></label>
							<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_feed' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_feed' ); ?>">
								<option value="user"<?php selected( $instance['ytchag_feed'], 'user' ); ?>><?php _e( 'Uploaded by a user', 'youtube-channel-gallery' ); ?></option>
								<?php /*<option value="favorites"<?php selected( $instance['ytchag_feed'], 'favorites' ); ?>><?php _e( 'User\'s favorites', 'youtube-channel-gallery' ); ?></option>*/?>
								<option value="playlist"<?php selected( $instance['ytchag_feed'], 'playlist' ); ?>><?php _e( 'Playlist', 'youtube-channel-gallery' ); ?></option>
							</select>
						</p>

						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_user' ); ?>"><?php _e( 'YouTube user id:', 'youtube-channel-gallery' ); ?></label>
							<input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_user' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_user' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_user ); ?>" />
						</p>

						<p class="<?php echo $this->get_field_id( 'ytchag_feed_order' ); ?>">
							<label for="<?php echo $this->get_field_id( 'ytchag_feed_order' ); ?>"><?php _e( 'Playlist order:', 'youtube-channel-gallery' ); ?></label>
							<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_feed_order' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_feed_order' ); ?>">
								<option value="asc"<?php selected( $instance['ytchag_feed_order'], 'asc' ); ?>><?php _e( 'Ascending Order', 'youtube-channel-gallery' ); ?></option>
								<option value="desc"<?php selected( $instance['ytchag_feed_order'], 'desc' ); ?>><?php _e( 'Descending Order', 'youtube-channel-gallery' ); ?></option>
							</select>
						</p>

					</div>


					<?php 
					/*
					Player Tab
					--------------------
					*/
					?>
					<div id="tabs-<?php echo $this->id; ?>-2" class="ytchgtabs-content">

						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_video_width' ); ?>"><?php _e( 'Video width:', 'youtube-channel-gallery' ); ?></label>
							<input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_video_width' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_video_width' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_video_width ); ?>" />
						</p>

						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_ratio' ); ?>"><?php _e( 'Aspect ratio:', 'youtube-channel-gallery' ); ?></label>
							<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_ratio' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_ratio' ); ?>">
								<option value="4x3"<?php selected( $instance['ytchag_ratio'], '4x3' ); ?>><?php _e( 'Standard (4x3)', 'youtube-channel-gallery' ); ?></option>
								<option value="16x9"<?php selected( $instance['ytchag_ratio'], '16x9' ); ?>><?php _e( 'Widescreen (16x9)', 'youtube-channel-gallery' ); ?></option>
							</select>
						</p>

						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_theme' ); ?>"><?php _e( 'Theme:', 'youtube-channel-gallery' ); ?></label>
							<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_theme' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_theme' ); ?>">
								<option value="dark"<?php selected( $instance['ytchag_theme'], 'dark' ); ?>><?php _e( 'Dark', 'youtube-channel-gallery' ); ?></option>
								<option value="light"<?php selected( $instance['ytchag_theme'], 'light' ); ?>><?php _e( 'Light', 'youtube-channel-gallery' ); ?></option>
							</select>
						</p>

						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_color' ); ?>"><?php _e( 'Progress bar color:', 'youtube-channel-gallery' ); ?></label>
							<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_color' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_color' ); ?>">
								<option value="red"<?php selected( $instance['ytchag_color'], 'red' ); ?>><?php _e( 'Red', 'youtube-channel-gallery' ); ?></option>
								<option value="white"<?php selected( $instance['ytchag_color'], 'white' ); ?>><?php _e( 'White', 'youtube-channel-gallery' ); ?></option>
							</select>
						</p>

						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_quality' ); ?>"><?php _e( 'Video quality:', 'youtube-channel-gallery' ); ?></label>
							<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_quality' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_quality' ); ?>">
								<option value="default"<?php selected( $instance['ytchag_quality'], 'default' ); ?>><?php _e( 'default', 'youtube-channel-gallery' ); ?></option>
								<option value="highres"<?php selected( $instance['ytchag_quality'], 'highres' ); ?>><?php _e( 'highres', 'youtube-channel-gallery' ); ?></option>
								<option value="hd1080"<?php selected( $instance['ytchag_quality'], 'hd1080' ); ?>><?php _e( 'hd1080', 'youtube-channel-gallery' ); ?></option>
								<option value="hd720"<?php selected( $instance['ytchag_quality'], 'hd720' ); ?>><?php _e( 'hd720', 'youtube-channel-gallery' ); ?></option>
								<option value="large"<?php selected( $instance['ytchag_quality'], 'large' ); ?>><?php _e( 'large', 'youtube-channel-gallery' ); ?></option>
								<option value="medium"<?php selected( $instance['ytchag_quality'], 'medium' ); ?>><?php _e( 'medium', 'youtube-channel-gallery' ); ?></option>
								<option value="small"<?php selected( $instance['ytchag_quality'], 'small' ); ?>><?php _e( 'small', 'youtube-channel-gallery' ); ?></option>
							</select>
						</p>
														 
							<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['ytchag_autoplay'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_autoplay' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_autoplay' ); ?>" />
							<label for="<?php echo $this->get_field_id( 'ytchag_autoplay' ); ?>"><?php _e('Autoplay', 'youtube-channel-gallery'); ?></label>

						<br>

							<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['ytchag_rel'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_rel' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_rel' ); ?>" />
							<label for="<?php echo $this->get_field_id( 'ytchag_rel' ); ?>"><?php _e('Show related videos', 'youtube-channel-gallery'); ?></label> 

						<br>

							<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['ytchag_showinfo'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_showinfo' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_showinfo' ); ?>" />
							<label for="<?php echo $this->get_field_id( 'ytchag_showinfo' ); ?>"><?php _e('Show info (title, uploader)', 'youtube-channel-gallery'); ?></label> 

					</div>


					<?php 
					/*
					Thumbnails Tab
					--------------------
					*/
					?>
					<div id="tabs-<?php echo $this->id; ?>-3">
						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_maxitems' ); ?>"><?php _e( 'Number of videos to show:', 'youtube-channel-gallery' ); ?></label>
							<input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_maxitems' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_maxitems' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_maxitems ); ?>" />
						</p>    
					
						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_thumb_width' ); ?>"><?php _e( 'Thumbnail width:', 'youtube-channel-gallery' ); ?></label>
							<input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_thumb_width' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_width' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_thumb_width ); ?>" />
						</p>

						<p>

							<label for="<?php echo $this->get_field_id( 'ytchag_thumb_ratio' ); ?>"><?php _e( 'Aspect ratio:', 'youtube-channel-gallery' ); ?></label>
							<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_thumb_ratio' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_ratio' ); ?>">
								<option value="4x3"<?php selected( $instance['ytchag_thumb_ratio'], '4x3' ); ?>><?php _e( 'Standard (4x3)', 'youtube-channel-gallery' ); ?></option>
								<option value="16x9"<?php selected( $instance['ytchag_thumb_ratio'], '16x9' ); ?>><?php _e( 'Widescreen (16x9)', 'youtube-channel-gallery' ); ?></option>
							</select>
						</p>
					
						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_thumb_columns' ); ?>"><?php _e( 'Thumbnail columns:', 'youtube-channel-gallery' ); ?></label>
							<input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_thumb_columns' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumb_columns' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_thumb_columns ); ?>" />
						</p>

						<p>
							<fieldset class="ytchg-field-tit-desc">
								<legend class="ytchg-tit-desc">
									<a href="#"><?php _e('Show title or description', 'youtube-channel-gallery'); ?></a>
								</legend>

								<div class="ytchg-title-and-description">

									<p>
										<input class="checkbox ytchg-tit" type="checkbox" <?php checked( (bool) $instance['ytchag_title'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_title' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_title' ); ?>" />
										<label for="<?php echo $this->get_field_id( 'ytchag_title' ); ?>"><?php _e('Show title', 'youtube-channel-gallery'); ?></label>
									</p>
									
									<p>
										<input class="checkbox ytchg-desc" type="checkbox" <?php checked( (bool) $instance['ytchag_description'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_description' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_description' ); ?>" />
										<label for="<?php echo $this->get_field_id( 'ytchag_description' ); ?>"><?php _e('Show description', 'youtube-channel-gallery'); ?></label>
									</p>

									<p>
										<label for="<?php echo $this->get_field_id( 'ytchag_thumbnail_alignment' ); ?>"><?php _e( 'Thumbnail alignment:', 'youtube-channel-gallery' ); ?></label>
										<select class="widefat" id="<?php echo $this->get_field_id( 'ytchag_thumbnail_alignment' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_thumbnail_alignment' ); ?>">
											<option value="left"<?php selected( $instance['ytchag_thumbnail_alignment'], 'left' ); ?>><?php _e( 'Left', 'youtube-channel-gallery' ); ?></option>
											<option value="right"<?php selected( $instance['ytchag_thumbnail_alignment'], 'right' ); ?>><?php _e( 'Right', 'youtube-channel-gallery' ); ?></option>
											<option value="top"<?php selected( $instance['ytchag_thumbnail_alignment'], 'top' ); ?>><?php _e( 'Top', 'youtube-channel-gallery' ); ?></option>
											<option value="bottom"<?php selected( $instance['ytchag_thumbnail_alignment'], 'bottom' ); ?>><?php _e( 'Bottom', 'youtube-channel-gallery' ); ?></option>
										</select>
									</p>

									<p>
										<label for="<?php echo $this->get_field_id( 'ytchag_description_words_number' ); ?>"><?php _e( 'Description words number:', 'youtube-channel-gallery' ); ?></label>
										<input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_description_words_number' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_description_words_number' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_description_words_number ); ?>" />
									</p> 
								</div>
							</fieldset> 
						</p>



					</div>


					<?php 
					/*
					Link Tab
					--------------------
					*/
					?>
					<div id="tabs-<?php echo $this->id; ?>-4">

						<p>
							<label for="<?php echo $this->get_field_id( 'ytchag_link_tx' ); ?>"><?php _e( 'Link text:', 'youtube-channel-gallery' ); ?></label>
							<input class="widefat" id="<?php echo $this->get_field_id( 'ytchag_link_tx' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_link_tx' ); ?>" type="text" value="<?php echo esc_attr( $ytchag_link_tx ); ?>" />
						</p>

						<p>
							<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['ytchag_link'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_link' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_link' ); ?>" />
							<label for="<?php echo $this->get_field_id( 'ytchag_link' ); ?>"><?php _e('Show link to channel', 'youtube-channel-gallery'); ?></label>
						
						</br>
						
							<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['ytchag_link_window'], true ); ?> id="<?php echo $this->get_field_id( 'ytchag_link_window' ); ?>" name="<?php echo $this->get_field_name( 'ytchag_link_window' ); ?>" />
							<label for="<?php echo $this->get_field_id( 'ytchag_link_window' ); ?>"><?php _e('Open in a new window or tab', 'youtube-channel-gallery'); ?></label>
						</p>

					</div>
				</div>



			</div>

			<?php 
		}


		/*--------------------------------------------------*/ 
		/* Private Functions 
		/*--------------------------------------------------*/
		private function ytchag_rss_markup($instance){

			//$instance variables
			//--------------------------------

			// Feed options
			$ytchag_feed = apply_filters('ytchag_feed', $instance['ytchag_feed']);
			$ytchag_user = apply_filters('ytchag_user', $instance['ytchag_user']);
			$ytchag_feed_order = apply_filters('ytchag_feed_order', $instance['ytchag_feed_order']);

			// Player options
			$ytchag_video_width = apply_filters('ytchag_video_width', $instance['ytchag_video_width']);
			$ytchag_ratio = apply_filters('ytchag_ratio', $instance['ytchag_ratio']);
			$ytchag_theme = apply_filters('ytchag_theme', $instance['ytchag_theme']);
			$ytchag_color = apply_filters('ytchag_color', $instance['ytchag_color']);
			$ytchag_quality = apply_filters('ytchag_quality', $instance['ytchag_quality']);
			$ytchag_autoplay = apply_filters('ytchag_autoplay', $instance['ytchag_autoplay']);
			$ytchag_rel = apply_filters('ytchag_rel', $instance['ytchag_rel']);
			$ytchag_showinfo = apply_filters('ytchag_showinfo', $instance['ytchag_showinfo']);

			// Thumbnail options
			$ytchag_maxitems = apply_filters('ytchag_maxitems', $instance['ytchag_maxitems']);
			$ytchag_thumb_width = apply_filters('ytchag_thumb_width', $instance['ytchag_thumb_width']);
			$ytchag_thumb_ratio = apply_filters('ytchag_thumb_ratio', $instance['ytchag_thumb_ratio']);
			$ytchag_thumb_columns = apply_filters('ytchag_thumb_columns', $instance['ytchag_thumb_columns']);
			$ytchag_title = apply_filters('ytchag_title', $instance['ytchag_title']);
			$ytchag_description = apply_filters('ytchag_description', $instance['ytchag_description']);
			$ytchag_thumbnail_alignment = apply_filters('ytchag_thumbnail_alignment', $instance['ytchag_thumbnail_alignment']);
			$ytchag_description_words_number = apply_filters('ytchag_description_words_number', $instance['ytchag_description_words_number']);

			// Link options
			$ytchag_link = apply_filters('ytchag_link', $instance['ytchag_link']);
			$ytchag_link_tx = apply_filters('ytchag_link_tx', $instance['ytchag_link_tx']);
			$ytchag_link_window = apply_filters('ytchag_link_window', $instance['ytchag_link_window']);
			//--------------------------------
			//end $instance variables


			//defaults
			//--------------------------------

			// Feed options
			$ytchag_feed = ( $ytchag_feed ) ? $ytchag_feed : 'user'; //default user
			$ytchag_feed_order = ( $ytchag_feed_order ) ? $ytchag_feed_order : 'asc'; //default ascending

			// Player options
			$ytchag_video_width = ( $ytchag_video_width ) ? $ytchag_video_width : 250;
			$ytchag_theme = ( $ytchag_theme ) ? '&theme='. $ytchag_theme : ''; //default dark
			$ytchag_color = ( $ytchag_color ) ? '&color='. $ytchag_color : ''; //default red
			$ytchag_quality = ( $ytchag_quality ) ? $ytchag_quality : 'default'; //default default
			$ytchag_autoplay = ( $ytchag_autoplay ) ? '&autoplay='. $ytchag_autoplay : ''; //default 0
			$ytchag_rel = ( $ytchag_rel ) ? '&rel='. $ytchag_rel : '&rel=0'; //default 1
			$ytchag_showinfo = ( $ytchag_showinfo ) ? '&showinfo='. $ytchag_showinfo : '&showinfo=0'; //default 1

			// Thumbnail options
			$ytchag_maxitems = ( $ytchag_maxitems ) ? $ytchag_maxitems : 9;
			$ytchag_thumb_width = ( $ytchag_thumb_width ) ? $ytchag_thumb_width : 85;
			$ytchag_thumb_columns = (( $ytchag_thumb_columns ) || ( $ytchag_thumb_columns != 0 )) ? $ytchag_thumb_columns : 0;

				//title and desc
				$ytchag_title = ( $ytchag_title ) ? $ytchag_title : 0;
				$ytchag_description = ( $ytchag_description ) ? $ytchag_description : 0;
				$ytchag_thumbnail_alignment = ( $ytchag_thumbnail_alignment ) ? $ytchag_thumbnail_alignment : 'left';
				$ytchag_description_words_number = ( $ytchag_description_words_number ) ? $ytchag_description_words_number : 10;

			// Link options
			$ytchag_link = ( $ytchag_link ) ? $ytchag_link : 0;
			$ytchag_link_tx = ( $ytchag_link_tx ) ? $ytchag_link_tx : __('Show more videos»', 'youtube-channel-gallery');
			$ytchag_link_window = ( $ytchag_link_window ) ? 'target="_blank"' : 0;
			//--------------------------------
			//end defaults


			//heights of video and thumbnail
			//--------------------------------
			//video height
			if ($ytchag_ratio == '16x9') {
				$ytchag_video_heigh = round( ($ytchag_video_width * 9) / 16);
			} else {
				$ytchag_video_heigh = round( ($ytchag_video_width * 3) / 4);
			}

			//thumbnail height
			if ($ytchag_thumb_ratio == '16x9') {
				$ytchag_thumb_height = round( ($ytchag_thumb_width * 9) / 16);
			} else {
				$ytchag_thumb_height = round( ($ytchag_thumb_width * 3) / 4);
			}
			

			// only if user name inserted 
			if( $ytchag_user ) { 
				

				// YouTube feed types 
				//--------------------------------
				$youtube_feed_url = 'http://gdata.youtube.com/feeds/api';
				// links
				if($ytchag_feed == 'user'){
					$ytchag_rss_url 	= $youtube_feed_url . '/users/' . $ytchag_user . '/uploads';
					$ytchag_link_url 	= 'http://www.youtube.com/user/' . $ytchag_user;
					$errorMesagge = __('You must insert a valid YouTube user id.', 'youtube-channel-gallery');
				}
				if($ytchag_feed == 'favorites'){
					$ytchag_rss_url 	= $youtube_feed_url . '/users/' . $ytchag_user . '/favorites';
					$ytchag_link_url 	= 'http://www.youtube.com/user/' . $ytchag_user . '/favorites';
					$errorMesagge = __('You must insert a valid YouTube user id.', 'youtube-channel-gallery');
				}
				if($ytchag_feed == 'playlist'){
					$ytchag_rss_url 	= $youtube_feed_url . '/playlists/' . $ytchag_user . '?v=2';//&prettyprint=true
					//print_r($ytchag_rss_url . '<br>');
					$ytchag_link_url 	= 'http://www.youtube.com/playlist?list=' . $ytchag_user;
					$errorMesagge = __('You must insert a valid playlist id.', 'youtube-channel-gallery');
				}
				
				//RSS Feed				
				include_once(ABSPATH . WPINC . '/feed.php');
				
				$rss = fetch_feed($ytchag_rss_url);
				//to get the appropriate order of items
				$rss->set_stupidly_fast(true);

				// check if no correct user name
				if (!is_wp_error( $rss ) ) {

					//playlist descending order
					//get totalResultsData from playlist rss to order correctly videos
					if($ytchag_feed == 'playlist' && $ytchag_feed_order == 'desc'){
						//openSearch:totalResults
						$totalResults = $rss->get_feed_tags('http://a9.com/-/spec/opensearch/1.1/', 'totalResults');
						$totalResultsData = $totalResults[0]['data'];
						//print_r('totalResultsData: ' . $totalResultsData . '<br>');

						//get rss playlist again with the last videos. YouTube does not load in the first request, even if the orderby parameter is set.

						//Youtube feed limit is 1000
						if($totalResultsData >= 1000){
							$startindex = 1000 - $ytchag_maxitems + 1;
						} elseif ($ytchag_maxitems >= $totalResultsData) {
							$startindex = 1;
						} else {
							$startindex = $totalResultsData - $ytchag_maxitems + 1;
						}

						//print_r('startindex: ' . $startindex . '<br>');
						$ytchag_rss_url = $ytchag_rss_url . '&start-index=' . $startindex . '&max-results=' . $ytchag_maxitems . '&orderby=reversedPosition';
						//print_r($ytchag_rss_url . '<br>');
						$rss = fetch_feed($ytchag_rss_url);

						//to get the appropriate order of items
						$rss->set_stupidly_fast(true);
						//print_r($ytchag_rss_url . '<br>');
					}

					$items = $rss->get_items(0, $ytchag_maxitems);

					if (!empty($items)) {
						$i = 0;
						$column = 0;
						STATIC $plugincount = 0;
						foreach ( $items as $item ) {
							$url = $item->get_permalink();
							$youtubeid = $this->youtubeid($url);
							$title = $item->get_title();
							$description = $item->get_description();

							//default url thumbnail
							if ($enclosure = $item->get_enclosure()){
								$thumb = $enclosure->get_thumbnail();
							}
							
							//to appropriate thumbnail

							//media:thumbnail tag
							$media_group = $item->get_item_tags('http://search.yahoo.com/mrss/', 'group');
							$media_content = $media_group[0]['child']['http://search.yahoo.com/mrss/']['thumbnail'];

							/*
							// to check order of playlist items
							$episode = $item->get_item_tags('http://gdata.youtube.com/schemas/2007', 'episode'); //yt
							$episodecontent = $episode[0]['attribs']['']['number'];
							if(!$episode){
								$episode = $item->get_item_tags('http://gdata.youtube.com/schemas/2007', 'position'); //yt
								$episodecontent = $episode[0]['data'];
							}
							//print_r($episodecontent . '-');
							*/

							//Check the thumbnail width
							$thumbW = array();
							foreach ($media_content as $index => $media_contentw) {
								$thumbW[$index] = $media_content[$index]['attribs']['']['width'];
							}
							//appropriate thumbnail width
							$thumbcorrectW = $this->closest($thumbW, $ytchag_thumb_width);

							//index in array of thumbnail width
    							$thumbcorrectWIndex = array_search($thumbcorrectW, $thumbW);

    						//appropriate url thumbnail
    						$thumb = $media_content[$thumbcorrectWIndex]['attribs']['']['url'];


							//rows and columns control

							$column++;
							$columnlastfirst = $tableclass = $columnnumber = '';
							if($ytchag_thumb_columns !=0 && $column == 1){
								$columnlastfirst = ' ytccell-first';
								STATIC $rowcount = 0;
								$rowcount++;					
								$row_oddeven = ($rowcount%2==1)?' ytc-r-odd':' ytc-r-even';
								$tableclass = ' ytc-table';			
								$columnnumber = ' ytc-columns'. $ytchag_thumb_columns;

							}
							if($ytchag_thumb_columns !=0 && $column%$ytchag_thumb_columns == 0){
								$columnlastfirst = ' ytccell-last';
							}// end columns control


							//check if title or description
							if($ytchag_title || $ytchag_description){
								$title_and_description_alignment_class = ' ytc-td-' . $ytchag_thumbnail_alignment;
							} else{
								$title_and_description_alignment_class = '';
							}


							//The content
							//--------------------------------

							//Show me the player: iframe player
							if($i == 0) {
								//count the plugin occurrences on page
								$plugincount++;

								$content = '<iframe id="ytcplayer' . $plugincount . '" class="ytcplayer" allowfullscreen width="' . $ytchag_video_width . '" height="' . $ytchag_video_heigh . '" src="http://www.youtube.com/embed/' . $youtubeid . '?version=3' . $ytchag_theme . $ytchag_color .  $ytchag_autoplay . $ytchag_rel . $ytchag_showinfo .'&enablejsapi=1" frameborder="0"></iframe>';
								$content.= '<ul class="ytchagallery ytccf' . $tableclass . $title_and_description_alignment_class . $columnnumber . '">';

							} // if player end
							$i++;



							//title and description content

							if($ytchag_title || $ytchag_description){
								$title_and_description_content= '<div class="ytctitledesc-cont">';

								if($ytchag_title){
									$title_and_description_content.= '<h5 class="ytctitle"><a class="ytclink" href="http://youtu.be/' . $youtubeid . '" data-playerid="ytcplayer' . $plugincount . '" data-quality="' . $ytchag_quality . '" alt="' . $title . '" title="' . $title . '">' . $title . '</a></h5>';
								}

								if($ytchag_description){
									$description = wp_trim_words( $description, $num_words = $ytchag_description_words_number, $more = '&hellip;' );
									$title_and_description_content.= '<div class="ytctdescription">' . $description . '</div>';
								}

								$title_and_description_content.= '</div>';
							} else{
								$title_and_description_content = '';
							}
							//end title and description content


		//----
							if($ytchag_thumb_columns !=0 && $column == 1){
								$content.=  "\n\n" .'<div class="ytccf ytc-row ytc-r-' . $rowcount . $row_oddeven . ' ">' . "\n\n";
							}

									//$content.= '$column: ' + $column;
									$content.=  "\n\n" . '	<li class="ytccell-' . $column . $columnlastfirst . '">';

										$content.= '<div class="ytcliinner">';

											if($ytchag_thumbnail_alignment == 'bottom'){
												$content.= $title_and_description_content;

											}

											$content.= '<div class="ytcthumb-cont">';
											$content.= '<a class="ytcthumb ytclink" href="http://youtu.be/' . $youtubeid . '" data-playerid="ytcplayer' . $plugincount . '" data-quality="' . $ytchag_quality . '" alt="' . $title . '" title="' . $title . '" style="background-image: url(' . $thumb . ');">';
											$content.= '<div class="ytcplay" style="width: ' . $ytchag_thumb_width . 'px; height: ' . $ytchag_thumb_height . 'px"></div>';
											$content.= '</a>';
											$content.= '</div>';

											if($ytchag_thumbnail_alignment != 'bottom'){
												$content.= $title_and_description_content;
											}

										$content.= '</div>';

									$content.= '</li>' . "\n\n";

		//----
							if($ytchag_thumb_columns !=0 && $column%$ytchag_thumb_columns == 0 ){
											$column = 0;
											$columnlastfirst = ' ytccell-last';	
											$content.= '</div>' . "\n\n\n";						
							}
							
						} //foreach end

						//if last row 
						if($ytchag_thumb_columns !=0 && $columnlastfirst != ' ytccell-last'){
								$content.= '</div>' . "\n\n\n";
						}

						$content.= '</ul>';

							//link to youtube.com gallery
						if( $ytchag_link) {
							$content.= '<a href="' . $ytchag_link_url . '" class="ytcmore" ' .$ytchag_link_window. ' >' . $ytchag_link_tx . '</a>';
						}
					}
				} else {
					$content= '<p class="empty">' .  $errorMesagge . '</p>';
				} // end check user name

			// user name not inserted 
			} else {
					$content= '<p class="empty">' . __('There is no video to show.', 'youtube-channel-gallery') . '</p>';				
			}
			
			return $content;

		}//ytchag_rss_markup

		//parse youtube url to extract id
		private function youtubeid($url) {
			$url_string = parse_url($url, PHP_URL_QUERY);
			parse_str($url_string, $args);
			return isset($args['v']) ? $args['v'] : false;
		}//youtubeid


		private function closest($array, $number) {
			sort($array);
			foreach ($array as $a) {
				if ($a >= $number) return $a;
			}
			return end($array); // or return NULL;

		}

		// load css or js
		private function register_scripts_and_styles() {
				wp_enqueue_script('jquery');
				wp_enqueue_script('youtube_player_api', 'http://www.youtube.com/player_api', false, false, true);
				wp_enqueue_script('youtube-channel-gallery', plugins_url('/scripts.js', __FILE__), false, false, true);
				wp_enqueue_style('youtube-channel-gallery', plugins_url('/styles.css', __FILE__), false, false, 'all');
		}//register_scripts_and_styles


		public function register_admin_scripts_and_styles($hook) {
			if( 'widgets.php' != $hook )
				return;
			wp_enqueue_style('youtube-channel-gallery', plugins_url('/admin-styles.css', __FILE__));
		}

		/*--------------------------------------------------*/ 
		/* Shortcode 
		/*--------------------------------------------------*/

		public function YoutubeChannelGallery_Shortcode($atts) {

			// Load JavaScript and stylesheets  
			$this->register_scripts_and_styles();

			extract( shortcode_atts( array(
				'user' => '',

				// Feed options
				'feed' => '',
				'feedorder' => '',

				// Player options
				'videowidth' => '',
				'ratio' => '',
				'theme' => '',
				'color' => '',
				'quality' => '',
				'autoplay' => '',
				'rel' => '',
				'showinfo' => '',

				// Thumbnail options
				'maxitems' => '',
				'thumbwidth' => '',
				'thumbratio' => '',
				'thumbcolumns' => '',
				'title' => '',
				'description' => '',
				'thumbnail_alignment' => '',
				'descriptionwordsnumber' => '',

				// Link options
				'link' => '',
				'link_tx' => '',
				'link_window' => ''

			), $atts ) );

			// Feed options
			$instance['ytchag_feed'] = $feed;
			$instance['ytchag_user'] = $user;
			$instance['ytchag_feed_order'] = $feedorder;

			// Player options
			$instance['ytchag_video_width'] = $videowidth;
			$instance['ytchag_ratio'] = $ratio;
			$instance['ytchag_theme'] = $theme;
			$instance['ytchag_color'] = $color;
			$instance['ytchag_quality'] = $quality;
			$instance['ytchag_autoplay'] = $autoplay;
			$instance['ytchag_rel'] = $rel;
			$instance['ytchag_showinfo'] = $showinfo;
				
			// Thumbnail options
			$instance['ytchag_maxitems'] = $maxitems;
			$instance['ytchag_thumb_width'] = $thumbwidth;
			$instance['ytchag_thumb_ratio'] = $thumbratio;
			$instance['ytchag_thumb_columns'] = $thumbcolumns;
			$instance['ytchag_title'] = $title;
			$instance['ytchag_description'] = $description;
			$instance['ytchag_thumbnail_alignment'] = $thumbnail_alignment;
			$instance['ytchag_description_words_number'] = $descriptionwordsnumber;

			// Link options
			$instance['ytchag_link'] = $link;
			$instance['ytchag_link_tx'] = $link_tx;
			$instance['ytchag_link_window'] = $link_window;


			return '<div class="ytcshort">'. $this->ytchag_rss_markup($instance) . '</div>';

		} // YoutubeChannelGallery_Shortcode


	} // class YoutubeChannelGallery_Widget

	// register YoutubeChannelGallery_Widget widget
	add_action( 'widgets_init', create_function( '', 'register_widget( "YoutubeChannelGallery_Widget" );' ) );

?>