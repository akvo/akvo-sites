<?php

class AkvoblocksSearchWidget extends WP_Widget {
    private $textDomain = 'akvoblocksbootstrap3';
	function __construct() {
		// Instantiate the parent object
		parent::__construct(
	 		'akvoblocks_search_widget', // Base ID
			'Akvo Blocks Search Widget', // Name
			array( 'description' => __( 'Search field widget'), ) // Args
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
            <div class="row">
            <form action="<?php bloginfo('siteurl'); ?>" id="searchform" method="get">
                <fieldset>
                    <div class="col-xs-9">
                    <input type="search" id="s" name="s" class="" placeholder="<?php _e('Search',$this->textDomain);?>" required />
                    </div>
                    <div class="col-xs-3">
                    <input type="submit" id="searchsubmit" class="btn" value="<?php _e('Go!',$this->textDomain);?>" />
                    </div>
                </fieldset>
           </form>
            
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
//		$instance = array();
//		$instance['title'] = strip_tags( $new_instance['title'] );
//		$instance['content'] = strip_tags( $new_instance['content'] );
//		$instance['link_target'] = strip_tags( $new_instance['link_target'] );

		return $new_instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
        return;
			$title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : '';
			$content = ( isset( $instance[ 'content' ] ) ) ? $instance[ 'content' ] : '';
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
            <label for="<?php echo $this->get_field_id('link_target'); ?>"><?php _e('Link Target:'); ?></label>
            <?php wp_dropdown_pages(array('id'=>$this->get_field_id( 'link_target' ),'name'=>$this->get_field_name( 'link_target' ),'selected'=>$link_target)); ?>
        </p>
		
        
		<?php 
	}
}


?>