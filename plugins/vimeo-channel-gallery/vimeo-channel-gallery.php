<?php
/*
Plugin Name: Vimeo Channel Gallery
Plugin URI: http://www.poselab.com/
Description: Show a Vimeo video and a gallery of thumbnails for a Vimeo channel.
Author: Javier Gómez Pose
Author URI: http://www.poselab.com/
Version: 1.5.3
License: GPL2
	
	Copyright 2012 Javier Gómez Pose  (email : javierpose@gmail.com)

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
class VimeoChannelGallery_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		//localization
		load_plugin_textdomain('vimeo-channel-gallery', false, dirname(plugin_basename( __FILE__ ) ) . '/languages/' ); 
		add_shortcode('Vimeo_Channel_Gallery', array($this, 'VimeoChannelGallery_Shortcode'));  
		
		parent::__construct(
			'VimeoChannelGallery_widget', // Base ID
			 __( 'Vimeo Channel Gallery', 'vimeo-channel-gallery' ), // Name
			array( 'description' => __( 'Show a vimeo video and a gallery of thumbnails for a vimeo channel', 'vimeo-channel-gallery' ), ) // Args
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

			echo $this->vmg_rss_markup($instance);

		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['vmg_user'] = strip_tags( $new_instance['vmg_user'] );
		$instance['vmg_type'] = strip_tags( $new_instance['vmg_type'] );
		
		$instance['vmg_link'] = $new_instance['vmg_link'];
		$instance['vmg_maxitems'] = strip_tags( $new_instance['vmg_maxitems'] );
		$instance['vmg_video_width'] = strip_tags( $new_instance['vmg_video_width'] );
		$instance['vmg_thumb_width'] = strip_tags( $new_instance['vmg_thumb_width'] );
		$instance['vmg_thumb_columns'] = strip_tags( $new_instance['vmg_thumb_columns'] );
		$instance['vmg_color'] = strip_tags( $new_instance['vmg_color'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $instance ) {
		$title      = esc_attr($instance['title']);
		$vmg_user = strip_tags($instance['vmg_user']);
		$vmg_type = strip_tags($instance['vmg_type']);
		$vmg_link = esc_attr($instance['vmg_link']);
		$vmg_maxitems = strip_tags($instance['vmg_maxitems']);
		$vmg_video_width = strip_tags($instance['vmg_video_width']);
		$vmg_thumb_width = strip_tags($instance['vmg_thumb_width']);
		$vmg_thumb_columns = strip_tags($instance['vmg_thumb_columns']);
		$vmg_color = strip_tags($instance['vmg_color']);
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'vimeo-channel-gallery' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'vmg_user' ); ?>"><?php _e( 'Vimeo id:', 'vimeo-channel-gallery' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'vmg_user' ); ?>" name="<?php echo $this->get_field_name( 'vmg_user' ); ?>" type="text" value="<?php echo esc_attr( $vmg_user ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'vmg_type' ); ?>"><?php _e( 'Type of gallery:', 'vimeo-channel-gallery' ); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'vmg_type' ); ?>" name="<?php echo $this->get_field_name( 'vmg_type' ); ?>">
					<option value="user"<?php selected( $instance['vmg_type'], 'user' ); ?>><?php _e( 'User', 'vimeo-channel-gallery' ); ?></option>
					<option value="channel"<?php selected( $instance['vmg_type'], 'channel' ); ?>><?php _e( 'Channel', 'vimeo-channel-gallery' ); ?></option>
					<option value="album"<?php selected( $instance['vmg_type'], 'album' ); ?>><?php _e( 'Album', 'vimeo-channel-gallery' ); ?></option>
					<option value="group"<?php selected( $instance['vmg_type'], 'group' ); ?>><?php _e( 'Group', 'vimeo-channel-gallery' ); ?></option>
				</select>
			</p>
		
			<p>
				<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['vmg_link'], true ); ?> id="<?php echo $this->get_field_id( 'vmg_link' ); ?>" name="<?php echo $this->get_field_name( 'vmg_link' ); ?>" />
				<label for="<?php echo $this->get_field_id( 'vmg_link' ); ?>"><?php _e('Show link to channel:', 'vimeo-channel-gallery'); ?></label><br />
			</p>    
		
			<p>
				<label for="vmg_maxitems"><?php _e( 'Number of videos to show:', 'vimeo-channel-gallery' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'vmg_maxitems' ); ?>" name="<?php echo $this->get_field_name( 'vmg_maxitems' ); ?>" type="text" value="<?php echo esc_attr( $vmg_maxitems ); ?>" />
			</p>    
		
			<p>
				<label for="vmg_video_width"><?php _e( 'Video width:', 'vimeo-channel-gallery' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'vmg_video_width' ); ?>" name="<?php echo $this->get_field_name( 'vmg_video_width' ); ?>" type="text" value="<?php echo esc_attr( $vmg_video_width ); ?>" />
			</p>
		
			<p>
				<label for="vmg_thumb_width"><?php _e( 'Thumbnail width:', 'vimeo-channel-gallery' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'vmg_thumb_width' ); ?>" name="<?php echo $this->get_field_name( 'vmg_thumb_width' ); ?>" type="text" value="<?php echo esc_attr( $vmg_thumb_width ); ?>" />
			</p>
		
			<p>
				<label for="vmg_thumb_columns"><?php _e( 'Thumbnail columns:', 'vimeo-channel-gallery' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'vmg_thumb_columns' ); ?>" name="<?php echo $this->get_field_name( 'vmg_thumb_columns' ); ?>" type="text" value="<?php echo esc_attr( $vmg_thumb_columns ); ?>" />
			</p>
		
			<p>
				<label for="vmg_color"><?php _e( 'Player color:', 'vimeo-channel-gallery' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'vmg_color' ); ?>" name="<?php echo $this->get_field_name( 'vmg_color' ); ?>" type="text" value="<?php echo esc_attr( $vmg_color ); ?>" />
			</p>

		<?php 
	}


	/*--------------------------------------------------*/ 
	/* Private Functions 
	/*--------------------------------------------------*/
	private function vmg_rss_markup($instance){

		//$instance variables
		$vmg_user = apply_filters('vmg_user', $instance['vmg_user']);
		$vmg_type = apply_filters('vmg_type', $instance['vmg_type']);
		$vmg_link = apply_filters('vmg_link', $instance['vmg_link']);
		$vmg_maxitems = apply_filters('vmg_maxitems', $instance['vmg_maxitems']);
		$vmg_video_width = apply_filters('vmg_video_width', $instance['vmg_video_width']);
		$vmg_thumb_width = apply_filters('vmg_thumb_width', $instance['vmg_thumb_width']);
		$vmg_thumb_columns = apply_filters('vmg_thumb_columns', $instance['vmg_thumb_columns']);
		$vmg_color = apply_filters('vmg_color', $instance['vmg_color']);

		//defaults
		$vmg_type = ( $vmg_type ) ? $vmg_type : 'user';
		$vmg_video_width = ( $vmg_video_width ) ? $vmg_video_width : 250;
		$vmg_thumb_width = ( $vmg_thumb_width ) ? $vmg_thumb_width : 85;
		$vmg_thumb_columns = ( $vmg_thumb_columns ) ? $vmg_thumb_columns : 0;
		$vmg_color = ( $vmg_color ) ? $vmg_color : '00adef';

		//get colors
		$vmg_color = $this->get_regular_color($vmg_color);
		$vmg_triangle_class = $this->get_triangle_class($vmg_color);

		//heights of video and thumbnail
		$vmg_video_heigh = round($vmg_video_width/(1280/720));
		$vmg_thumb_height = round($vmg_thumb_width*75/100); 

		if( $vmg_user ) { // only if user name inserted 
			
			// xml links to vídeos to user, group, channel or album with Simple API
			// https://developer.vimeo.com/apis/simple
			//--------------------------
			$vmg_feed_url = 'http://vimeo.com/api/v2/';
			if($vmg_type == 'user'){
				$vmg_type_url = '';
			} else {
				$vmg_type_url = $vmg_type . '/';
			}
			
			libxml_use_internal_errors(true);
			$vmg_videos_url = $vmg_feed_url . $vmg_type_url . $vmg_user . '/videos.xml';
			$vmg_info_url = $vmg_feed_url . $vmg_type_url . $vmg_user . '/info.xml';
			//print_r($vmg_info_url);
			//--------------------------

			//HTTP API and simplexml
			$videos_result = wp_remote_get($vmg_videos_url);
			$vmg_videos = simplexml_load_string($videos_result['body']);

			$info_result = wp_remote_get($vmg_info_url);
			$vmg_info = simplexml_load_string($info_result['body']);

			if($vmg_videos ===  FALSE) {
				$content= '<div class="vmcerror">' . sprintf( __( 'Something went wrong. Check if the id %1$d belongs to a %2$d and if it exist.', 'vimeo-channel-gallery' ), $vmg_user, $vmg_type) . '</div>';
			} 
			else {
				$i = 0;
				$column = 0;

				// url to user, group, channel or album
				if($vmg_type == 'user'){
					$vmg_link_url = $vmg_info->$vmg_type->videos_url;	
				} else {
					$vmg_link_url = $vmg_info->$vmg_type->url;				
				}
				print_r($url);

				//loop through thumbnails
				foreach ($vmg_videos->video as $video){

					// get video id and title
					$video_id =  $video->id;
					$video_title = $video->title;
					$video_url = $video->url;

					//get thumbnails and set the appropriate
					if($vmg_thumb_width <= '100' ){
						$thumb = $video->thumbnail_small;//100x75
					} elseif ($vmg_thumb_width <= '200') {
						$thumb = $video->thumbnail_medium;//200x150
					} else {
						$thumb = $video->thumbnail_large;//640x360
						$vmg_thumb_height = round(($vmg_thumb_width*360)/640);
					} 

					//Show me the player: iframe player
					if($i == 0) {
						//count the plugin occurrences on page
						STATIC $plugincount = 0;
						$plugincount++;
						$player_id = 'vmcplayer' . $plugincount . 'video' . ($i +1);
																									
						$content= '<div class="vmcplayerdiv">';
						$content.= '<iframe id="vmcplayer' . $player_id . '" class="vmcplayer" data-color="' . $vmg_color . '" style="width:' . $vmg_video_width . 'px;height:' . $vmg_video_heigh . 'px" src="http://player.vimeo.com/video/' . $video_id . '?api=1&player_id=' . $player_id . '&color=' . $vmg_color . '"  frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
						$content.= '</div>';
						$content.= '<ul class="vmgallery">';
	
					} // if player end
					$i++;

					$column++;
					// list of thumbnail videos						
					
					$content.= '<li class="vmccell-' . $column . '">';
					$content.= '	<a class="vmcthumb" href="' . $video_url . '" data-playerid="vmcplayer' . $plugincount . 'video' . $i . '" alt="' . $video_title . '" title="' . $video_title . '" style="background-image:url(' . $thumb . ');width:' . $vmg_thumb_width . 'px;height:' . $vmg_thumb_height . 'px">';
					$content.= '		<div class="vmcplay" style="background: #' . $vmg_color . ';"><div class="vmtriangle ' . $vmg_triangle_class . '"></div></div>';
					$content.= '	</a>';
					$content.= '</li>';

				
					if($vmg_thumb_columns !=0 && $column%$vmg_thumb_columns === 0){
						$column = 0;
					}
					if ($vmg_maxitems == $i) {
						break;
					}
					
				} //foreach end
				$content.= '</ul>';

					//link to vimeo.com gallery
				if( $vmg_link) {
					$content.= '<a href="' . $vmg_link_url . '" class="more" target="_blank">' .  __('Show more videos»', 'vimeo-channel-gallery') . '</a>';
				}
			}
		} 

		return $content;

	}//vmg_rss_markup


	// load css or js
	private function register_scripts_and_styles() {
			wp_enqueue_script('vimeo-channel-gallery', plugins_url('/scripts.js', __FILE__), false, false, true);
			wp_enqueue_style('vimeo-channel-gallery', plugins_url('/styles.css', __FILE__), false, false, 'all');
	}//register_scripts_and_styles



	//regular color
	private function get_regular_color($vmg_color) {
		//delete # to pass to player
		$vmg_color = str_replace("#", "", $vmg_color);

		//shorthand color to regular
		if (strlen($vmg_color) == 3) {
			$vmg_color = $vmg_color[0].$vmg_color[0].$vmg_color[1].$vmg_color[1].$vmg_color[2].$vmg_color[2];
		}
		return $vmg_color;
	}
		
	//triangle color
	private function get_triangle_class($vmg_color) {
		if(strtolower($vmg_color) =='ffffff'){
			$vmg_triangle_class = 'vmtrgray';
		} else{
			$vmg_triangle_class = 'vmtrwhite';			
		}
		return $vmg_triangle_class;
}

	/*--------------------------------------------------*/ 
	/* Shortcode 
	/*--------------------------------------------------*/

	public function VimeoChannelGallery_Shortcode($atts) {

		// Load JavaScript and stylesheets  
		$this->register_scripts_and_styles();

		extract( shortcode_atts( array(
			'user' => '',
			'type' => '',
			'link' => '0',
			'maxitems' => '9',
			'videowidth' => '280',
			'thumbwidth' => '85',
			'thumbcolumns' => '0',
			'color' => '00adef',
		), $atts ) );

		$instance['vmg_user'] = $user;
		$instance['vmg_type'] = $type;
		
		$instance['vmg_link'] = $link;
		$instance['vmg_maxitems'] = $maxitems;
		$instance['vmg_video_width'] = $videowidth;
		$instance['vmg_thumb_width'] = $thumbwidth;
		$instance['vmg_thumb_columns'] = $thumbcolumns;
		$instance['vmg_color'] = $color;

		return $this->vmg_rss_markup($instance);

	} // VimeoChannelGallery_Shortcode


} // class VimeoChannelGallery_Widget

// register VimeoChannelGallery_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "VimeoChannelGallery_Widget" );' ) );

?>