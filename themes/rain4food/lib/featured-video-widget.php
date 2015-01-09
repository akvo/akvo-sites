<?php

class FeaturedVideoWidget extends WP_Widget {
    private $textDomain = 'rain4food';
	function __construct() {
		// Instantiate the parent object
		parent::__construct(
	 		'r4f_featured_video_widget', // Base ID
			'Rain4food featured video Widget', // Name
			array( 'description' => __( 'Featured video widget'), ) // Args
		);
        
	}
    
    
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
        extract( $args );
		echo $before_widget;
        ?>
        <div class="cDivWrapper">
            
            <div class="cDivContent">
                <?php echo $before_title.$instance['title'].$after_title;?>
                <p><?php echo $instance['content'];?></p>
                
            </div>
            <div class="cDivVideos">
                <?php for($i=1;$i<=2;$i++){ 
                    if($instance['video'.$i.'_url']===''){
                        continue;
                    }
                    $aParams = array();
                    parse_str(parse_url($instance['video'.$i.'_url'],PHP_URL_QUERY),$aParams);
                    $videoID = $aParams['v'];
                    ?>
                <div class='cDivVideo col-sm-6'>
                    <div class='cDivFlexibleContainer'>
                       <iframe width="560" height="315" src="//www.youtube.com/embed/<?php echo $videoID; ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <div class='cDivDescription'>
                        <?php echo $instance['video'.$i.'_description'];?>
                    </div>
                </div>
                <?php } ?>
            </div>
            <a class='btn btn-default cAreadMore' href="<?php echo get_permalink($instance['link_target']);?>"><?php _e('Read More',$this->textDomain);?></a>
        </div>
        <?php
        echo $after_widget;
    
        
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['content'] = strip_tags( $new_instance['content'] );
		$instance['video1_url'] = strip_tags( $new_instance['video1_url'] );
		$instance['video2_url'] = strip_tags( $new_instance['video2_url'] );
		$instance['video1_description'] = strip_tags( $new_instance['video1_description'] );
		$instance['video2_description'] = strip_tags( $new_instance['video2_description'] );
		$instance['link_target'] = strip_tags( $new_instance['link_target'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
			$title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : '';
			$content = ( isset( $instance[ 'content' ] ) ) ? $instance[ 'content' ] : '';
			$urlvideo1 = ( isset( $instance[ 'video1_url' ] ) ) ? $instance[ 'video1_url' ] : '';
			$urlvideo2 = ( isset( $instance[ 'video2_url' ] ) ) ? $instance[ 'video2_url' ] : '';
			$descriptionvideo1 = ( isset( $instance[ 'video1_description' ] ) ) ? $instance[ 'video1_description' ] : '';
			$descriptionvideo2 = ( isset( $instance[ 'video2_description' ] ) ) ? $instance[ 'video2_description' ] : '';
			$link_target = ( isset( $instance[ 'link_target' ] ) ) ? $instance[ 'link_target' ] : '/';
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title;?>" />
		</p>
		<p>
            <label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Content:' ); ?></label> 
            <textarea class="widefat" name="<?php echo $this->get_field_name( 'content' ); ?>"><?php echo $content;?></textarea>
		</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'video1_url' ); ?>"><?php _e( 'Youtube url video 1:' ); ?></label> 
            <input class="widefat" name="<?php echo $this->get_field_name( 'video1_url' ); ?>" value="<?php echo $urlvideo1;?>" />
		</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'video1_description' ); ?>"><?php _e( 'Description video 1:' ); ?></label> 
            <input class="widefat" name="<?php echo $this->get_field_name( 'video1_description' ); ?>" value="<?php echo $descriptionvideo1;?>" />
		</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'video2_url' ); ?>"><?php _e( 'Youtube url video 2:' ); ?></label> 
            <input class="widefat" name="<?php echo $this->get_field_name( 'video2_url' ); ?>" value="<?php echo $urlvideo2;?>" />
		</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'video2_description' ); ?>"><?php _e( 'Description video 2:' ); ?></label> 
            <input class="widefat" name="<?php echo $this->get_field_name( 'video2_description' ); ?>" value="<?php echo $descriptionvideo2;?>" />
		</p>
		<p>
            <label for="<?php echo $this->get_field_id('link_target'); ?>"><?php _e('Link Target:'); ?></label>
            <?php wp_dropdown_pages(array('id'=>$this->get_field_id( 'link_target' ),'name'=>$this->get_field_name( 'link_target' ),'selected'=>$link_target)); ?>
        </p>
		
        
		<?php 
	}
}


?>