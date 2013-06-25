<?php

class NewsWidget extends WP_Widget {

	function NewsWidget() {
		// Instantiate the parent object
		parent::__construct(
	 		'news_widget', // Base ID
			'News Widget', // Name
			array( 'description' => __( 'Display latest news post'), ) // Args
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

		echo $before_widget;
        ?>
    
    <?php
//		if ( ! empty( $title ) ){
//			echo $before_title . $title . $after_title;
//        }
		query_posts(array('post_type'=> 'news','posts_per_page'=>1));
        if ( have_posts() ) {
            while ( have_posts() ) {
                the_post();
                
                $classtext = 'no-border';
                $titletext = get_the_title();
                
                $thumbnail = get_thumbnail(271,167,$classtext,$titletext,$titletext,true,'Featured');
                $thumb = $thumbnail["thumb"];
                if($thumb==''){
                    $thumb = catch_that_image();
                }

                $thumb = '/wp-content/plugins/akvo-site-config/classes/thumb.php?src='.$thumb.'&w=271&h=167&zc=1&q=100';
                //$thumb = get_field('logo');
                $sLink = get_field('url'); ?>
                <div class="newswidget">
                <div class="cDivNewsPostImageTag"></div>
                <div class="cDivNewsImage">
                <?php if($thumb <> '') { ?>
                    <div class="cDivBlogPostImageWrapper">
                                            <div class="cDivBlogPostImage">
                                            
                        <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>">
							<img src="<?php echo $thumb; ?>" />
                        </a>
                    </div>
                                        </div>
                <?php }; ?>

                 </div>
        <div class="cDivNewsText">
            <h2><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>
            <div class="cDivBlogPostDate">
                <?php echo date('M d, Y',  strtotime(get_the_date())); ?>
            </div>
            <?php the_excerpt(); ?>
            <div class="readmore">
				<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), the_title()) ?>"><?php esc_html_e('Read More','Quadro'); ?></a>
			</div>
        </div>
                <br style="clear:both" />
    </div>
                <?php  
            }
        }?>
        
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
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
}
?>
