<?php

class BAPWidget extends WP_Widget {
    private $textDomain = 'rain4food';
	function __construct() {
		// Instantiate the parent object
		parent::__construct(
	 		'r4f_bap_widget', // Base ID
			'Rain4food Become a part of it Widget', // Name
			array( 'description' => __( 'Become a part of it widget'), ) // Args
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
        wp_enqueue_script('upload_media_widget', get_stylesheet_directory_uri() . '/lib/become-a-part-of-it-widget/upload-media.js');
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
            <div class="cDivTitleImage">
                <img class="img-responsive" src="<?php echo $instance['title_image'];?>" />
                <br style="clear:both;" />
            </div>
            <div class="cDivContent">
                <p><?php echo $instance['content'];?></p>
                <?php if(is_numeric($instance['link_target'])){ ?>
                <a class='btn btn-default cAreadMore' href="<?php echo get_permalink($instance['link_target']);?>"><?php _e('Read More',$this->textDomain);?></a>
                <?php } ?>
            </div>
            
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
		$instance['title_image'] = strip_tags( $new_instance['title_image'] );
		$instance['content'] = strip_tags( $new_instance['content'],'<b><strong><br><a>' );
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
			$titleImage = ( isset( $instance[ 'title_image' ] ) ) ? $instance[ 'title_image' ] : '';
			$content = ( isset( $instance[ 'content' ] ) ) ? $instance[ 'content' ] : '';
            $link_target = ( isset( $instance[ 'link_target' ] ) ) ? $instance[ 'link_target' ] : '/';
		?>
        <p>
            <label for="<?php echo $this->get_field_name( 'title_image' ); ?>"><?php _e( 'Title image:' ); ?></label>
            <input name="<?php echo $this->get_field_name( 'title_image' ); ?>" id="<?php echo $this->get_field_id( 'title_image' ); ?>" class="widefat" type="hidden" size="36"  value="<?php echo esc_url( $titleImage ); ?>" />
            <br />
            <img src="<?php echo esc_url( $titleImage ); ?>" id="image_upload_example_<?php echo $this->get_field_id( 'title_image' ); ?>" width="50%" class="upload_image_button" data-input="<?php echo $this->get_field_id( 'title_image' ); ?>" />
            <br />
            <input class="upload_image_button button button-primary" type="button" value="Upload title image" data-input="<?php echo $this->get_field_id( 'title_image' ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Content:' ); ?></label> 
            <textarea class="widefat" name="<?php echo $this->get_field_name( 'content' ); ?>"><?php echo $content;?></textarea>
		</p>
        <p>
            <label for="<?php echo $this->get_field_id('link_target'); ?>"><?php _e('Link Target:'); ?></label>
            <?php wp_dropdown_pages(array('id'=>$this->get_field_id( 'link_target' ),'name'=>$this->get_field_name( 'link_target' ),'selected'=>$link_target,'show_option_none'=>'none')); ?>
        </p>
		<?php 
	}
}


?>