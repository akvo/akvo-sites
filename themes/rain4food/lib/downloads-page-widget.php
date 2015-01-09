<?php

class DownloadsPageWidget extends WP_Widget {
    private $textDomain = 'rain4food';
	function __construct() {
		// Instantiate the parent object
		parent::__construct(
	 		'r4f_downloadspage_widget', // Base ID
			'Rain4food downloads page Widget', // Name
			array( 'description' => __( 'display downloads page content'), ) // Args
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
                <?php echo $before_title.  get_the_title($instance['link_target']).$after_title;?>
                <?php echo get_post_field('post_content',$instance['link_target']);?>
                
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
			$link_target = ( isset( $instance[ 'link_target' ] ) ) ? $instance[ 'link_target' ] : '/';
		?>
        
		<p>
            <label for="<?php echo $this->get_field_id('link_target'); ?>"><?php _e('Select downloads page:'); ?></label>
            <?php wp_dropdown_pages(array('id'=>$this->get_field_id( 'link_target' ),'name'=>$this->get_field_name( 'link_target' ),'selected'=>$link_target)); ?>
        </p>
		
        
		<?php 
	}
}


?>