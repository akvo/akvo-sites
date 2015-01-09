<?php

class AkvoblocksFeaturedWidget extends WP_Widget {
    private $textDomain = 'akvoblocksbootstrap3';
	function __construct() {
		// Instantiate the parent object
		parent::__construct(
	 		'akvoblocks_featured_widget', // Base ID
			'Akvo Blocks Featured Widget', // Name
			array( 'description' => __( 'Widget with image background and short text'), ) // Args
		);
        
        add_action('admin_enqueue_scripts', array($this, 'upload_scripts'));
        add_action('admin_enqueue_styles', array($this, 'upload_styles'));
	}
    
    /**
     * Upload the Javascripts for the media uploader
     */
    public function upload_scripts()
    {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('upload_media_widget', get_stylesheet_directory_uri() . '/lib/akvoblocks-featured-widget/upload-media.js');
    }
    
    /**
     * Add the styles for the upload media box
     */
    public function upload_styles()
    {
        wp_enqueue_style('thickbox');
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
            <div class="cDivBackgroundImage">
                <img src="<?php echo $instance['background_image'];?>" />
            </div>
            <div class="cDivContent col-lg-4 col-md-5 col-sm-8">
                <div class="cDivTitle">
                    <?php echo $before_title.$instance['title'].$after_title;?>
                </div>
                <div class="cDivText">
                    <p><?php echo $instance['content'];?></p>
                    <?php if($instance['link_target']!==''){?>
                    <a class="btn btn-default" href="<?php echo get_permalink($instance['link_target']);?>"><?php _e('Read More',$this->textDomain);?></a>
                    <?php } ?>
                </div>
            </div>
            <?php if($instance['download_btn']==='1'){ ?>
                <div class="cDivDownloadBtn col-md-3 col-sm-3">
                    <h4><?php echo $instance['download_title'];?></h4>
                    <a href="<?php echo $instance['download_file'];?>">
                            <img class="img-responsive" src="<?php echo $instance['download_image'];?>" />
                    </a>
                </div>
            <?php } ?>
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
		$instance['background_image'] = strip_tags( $new_instance['background_image'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['content'] = strip_tags( $new_instance['content'],'<p><b><strong><br><a>');
		$instance['link_target'] = strip_tags( $new_instance['link_target'] );
		$instance['download_btn'] = strip_tags( $new_instance['download_btn'] );
		$instance['download_title'] = strip_tags( $new_instance['download_title'] );
		$instance['download_image'] = strip_tags( $new_instance['download_image'] );
		$instance['download_file'] = strip_tags( $new_instance['download_file'] );

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
			$bgImage = ( isset( $instance[ 'background_image' ] ) ) ? $instance[ 'background_image' ] : '';
			$title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : '';
			$content = ( isset( $instance[ 'content' ] ) ) ? $instance[ 'content' ] : '';
			$link_target = ( isset( $instance[ 'link_target' ] ) ) ? $instance[ 'link_target' ] : '/';
			$downloadBtn = ( isset( $instance[ 'download_btn' ] ) ) ? $instance[ 'download_btn' ] : '0';
			$downloadTitle = ( isset( $instance[ 'download_title' ] ) ) ? $instance[ 'download_title' ] : '';
			$downloadImage = ( isset( $instance[ 'download_image' ] ) ) ? $instance[ 'download_image' ] : '';
			$downloadFile = ( isset( $instance[ 'download_file' ] ) ) ? $instance[ 'download_file' ] : '';
		?>
        <p>
            <label for="<?php echo $this->get_field_name( 'background_image' ); ?>"><?php _e( 'Background image:' ); ?></label>
            <input name="<?php echo $this->get_field_name( 'background_image' ); ?>" id="<?php echo $this->get_field_id( 'background_image' ); ?>" class="widefat" type="hidden" size="36"  value="<?php echo esc_url( $bgImage ); ?>" />
            <br />
            <img src="<?php echo esc_url( $bgImage ); ?>" id="image_upload_example_<?php echo $this->get_field_id( 'background_image' ); ?>" width="50%" class="upload_image_button" data-input="<?php echo $this->get_field_id( 'background_image' ); ?>" />
            <br />
            <input class="upload_image_button button button-primary" type="button" value="Upload background image" data-input="<?php echo $this->get_field_id( 'background_image' ); ?>" />
        </p>
		<p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title;?>" />
		</p>
		<p>
            <label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Content:' ); ?></label> 
            <textarea rows="10" class="widefat" name="<?php echo $this->get_field_name( 'content' ); ?>"><?php echo $content;?></textarea>
		</p>
        <p>
            <label for="<?php echo $this->get_field_id('link_target'); ?>"><?php _e('Link Target:'); ?></label>
            <?php wp_dropdown_pages(array('show_option_none'=>'none','id'=>$this->get_field_id( 'link_target' ),'name'=>$this->get_field_name( 'link_target' ),'selected'=>$link_target)); ?>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id( 'download_btn' ); ?>"><?php _e( 'Show download button:' ); ?></label> 
            <input type="radio" class="widefat cRadioDownloadBtn" id="<?php echo $this->get_field_id( 'download_btn' ); ?>" name="<?php echo $this->get_field_name( 'download_btn' ); ?>" value="1" <?php if($downloadBtn=='1')echo 'checked'; ?> /> yes
            <input type="radio" class="widefat cRadioDownloadBtn" id="<?php echo $this->get_field_id( 'download_btn' ); ?>" name="<?php echo $this->get_field_name( 'download_btn' ); ?>" value="0" <?php if($downloadBtn=='0')echo 'checked'; ?> /> no
		</p>
        <div class="cDivDownloadInfo-<?php echo $this->get_field_id( 'download_btn' ); ?>">
            <p>
                <label for="<?php echo $this->get_field_id( 'download_title' ); ?>"><?php _e( 'Download title:' ); ?></label> 
                <input class="widefat" name="<?php echo $this->get_field_name( 'download_title' ); ?>" value="<?php echo $downloadTitle;?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_name( 'download_image' ); ?>"><?php _e( 'Download image:' ); ?></label>
                <input name="<?php echo $this->get_field_name( 'download_image' ); ?>" id="<?php echo $this->get_field_id( 'download_image' ); ?>" class="widefat" type="hidden" size="36"  value="<?php echo esc_url( $downloadImage ); ?>" />
                <br />
                <img src="<?php echo esc_url( $downloadImage ); ?>" id="image_upload_example_<?php echo $this->get_field_id( 'download_image' ); ?>" width="50%" class="upload_image_button" data-input="<?php echo $this->get_field_id( 'download_image' ); ?>" />
                <br />
                <input class="upload_image_button button button-primary" type="button" value="Upload image" data-input="<?php echo $this->get_field_id( 'download_image' ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_name( 'download_file' ); ?>"><?php _e( 'Download file:' ); ?></label>
                <input name="<?php echo $this->get_field_name( 'download_file' ); ?>" id="<?php echo $this->get_field_id( 'download_file' ); ?>" class="widefat" type="text" size="36"  value="<?php echo esc_url( $downloadFile ); ?>" />
                <input class="upload_image_button button button-primary" type="button" value="Upload File" data-input="<?php echo $this->get_field_id( 'download_file' ); ?>" />
            </p>
        </div>
		
        
		<?php 
	}
}


?>