<?php

class MembersWidget extends WP_Widget {

	function MembersWidget() {
		// Instantiate the parent object
		parent::__construct(
	 		'members_widget', // Base ID
			'Members Widget', // Name
			array( 'description' => __( 'Display all member logos'), ) // Args
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
		$title = apply_filters( 'widget_title', $instance['title'] );
		$cat = $instance['cat'];

		echo $before_widget;
        ?>
    <div class="memberwidget">
    <?php
		if ( ! empty( $title ) ){
			echo $before_title . $title . $after_title;
        }
		query_posts(array('cat'=> $cat,'posts_per_page'=>-1,'nopaging'=>true));
        if ( have_posts() ) {
            while ( have_posts() ) {
                the_post();
                
                $thumb = get_field('logo');
                $sLink = get_field('url'); ?>
        <div style="float:left;">
                <?php if($thumb <> '') { ?>
                    <div>
                        <a href="<?php echo $sLink; ?>" target="_blank" rel="bookmark" title="<?php the_title(); ?>">
							<img src="<?php echo $thumb; ?>" />
                        </a>
                    </div>
                <?php }; ?>
<!--                    <div>
                        <a href="<?php echo $sLink; ?>" target="_blank" rel="bookmark" title="<?php the_title(); ?>">
							<?php the_title(); ?>
						</a>
                    </div>-->
                 </div>   
                <?php  
            }
        }?>
    
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
		$instance['cat'] = strip_tags( $new_instance['cat'] );

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
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		if ( isset( $instance[ 'cat' ] ) ) {
			$cat = $instance[ 'cat' ];
		}
		else {
			$cat = null;
		}
        
        
        $categories = get_categories();
        
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        <label for="<?php echo $this->get_field_id( 'cat' ); ?>"><?php _e( 'Category:' ); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id( 'cat' ); ?>" name="<?php echo $this->get_field_name( 'cat' ); ?>">
            <?php
            foreach($categories AS $category){
                $selected = ($cat==$category->cat_ID) ? 'selected' : '' ;
                echo '<option value="'.$category->cat_ID.'" '.$selected.'>'.$category->cat_name.'</option>';
            }
            ?>
        </select>
		</p>
		<?php 
	}
}
?>
