<?php

class AboutboxWidget extends WP_Widget {

	function AboutboxWidget() {
		// Instantiate the parent object
		parent::__construct(
	 		'aboutbox_widget', // Base ID
			'About box Widget', // Name
			array( 'description' => __( 'Akvo widget for home about box'), ) // Args
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
		$type = apply_filters( 'widget_type', $instance['type'] );
		
		//echo $before_widget;
        ?>
    <div id="featured">
    <?php
		if ( ! empty( $title ) ){
			echo $before_title . $title . $after_title;
        }
        query_posts("pagename=home");
        if ( have_posts() ) {
            while ( have_posts() ) {
                the_post();
                $sURL = get_field('url');
                $sLink = ($sURL) ? $sURL : get_permalink() ; 
                switch($type){
                    case "map":
                        $width = (isset($instance['width'])) ? apply_filters( 'widget_type', $instance['width'] ) : 470;
                        $height = (isset($instance['height'])) ? apply_filters( 'widget_type', $instance['height'] ) : 240;

                        ?>
                <div id="iDivMap"  style="width: <?php echo $width;?>px; height: <?php echo $height;?>px; float: left;"></div>
                        <?php
                        if (function_exists('showMap')) {
                            showMap('');
                        }
                    break;
                    case "photo":
                        
                        $width = (isset($instance['width'])) ? apply_filters( 'widget_type', $instance['width'] ) : 480;
                        $height = (isset($instance['height'])) ? apply_filters( 'widget_type', $instance['height'] ) : 212;

                        $classtext = 'no-border';
                        $titletext = get_the_title();
                        $thumbnail = get_thumbnail($width, $height, $classtext, $titletext, $titletext, false, 'Featured');
                        $thumb = $thumbnail["thumb"];
                        ?>

                        <?php if ($thumb <> '') { ?>
                            <div class="thumbnail-div-featured">
                                <a href="<?php echo $sLink; ?>">
                                    <?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
                                </a>
                            </div>  <!-- end .thumbnail-div-featured -->
                        <?php } 
                    break;
                    case "slider":
                        $width = (isset($instance['width'])) ? apply_filters( 'widget_type', $instance['width'] ) : 468;
                        $height = (isset($instance['height'])) ? apply_filters( 'widget_type', $instance['height'] ) : 255;

                        $classtext = 'no-border';
                        $titletext = get_the_title();
                        $aImages = catch_post_images();
                        ?>
                            <div class="thumbnail-div-slider">
                            <ul class="bxslider">
                                <?php foreach($aImages AS $sImage){
                                    $thumb = plugins_url('akvo-site-config/classes/thumb.php').'?src='.$sImage.'&w='.$width.'&h='.$height.'&zc=1&q=100';

                                    echo '<li><img src="'.$thumb.'" /></li>';
                                }?>
                              
                            </ul>
                            </div>
                            <script type="text/javascript">
                                jQuery.getScript('<?php echo plugins_url('akvo-site-config/bxslider.js');?>', function(){
                                   jQuery('.bxslider').bxSlider({auto:true});
                                });
                            </script> 
                            
                        <?php
                    break;
                }
            }
        }
                
                
    ?>
                <div class="featured-content">
                    <h1 class="titles-featured">
                        <a href="<?php echo $sLink; ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), the_title()) ?>">
                            <?php the_title(); ?>
                        </a>
                    </h1>
                    <?php the_excerpt(); ?>
                    <div style="clear: both;"></div>

                    <div class="readmore">
                        <a href="<?php echo $sLink; ?>" rel="bookmark" title="<?php printf(esc_attr__('Permanent Link to %s','Quadro'), the_title()) ?>"><?php esc_html_e('Read More','Quadro'); ?></a>
                    </div>
                </div> <!-- end #featured-content -->

		<div style="clear: both;"></div>
    </div>
    <?php
        
		//echo $after_widget;
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
		$instance['type'] = strip_tags( $new_instance['type'] );
		$instance['width'] = strip_tags( $new_instance['width'] );
		$instance['height'] = strip_tags( $new_instance['height'] );

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
			$type = ( isset( $instance[ 'type' ] ) ) ? $instance[ 'type' ] :  __( 'map', 'text_domain' );;
			$width = ( isset( $instance[ 'width' ] ) ) ? $instance[ 'width' ] : 470;
			$height = ( isset( $instance[ 'height' ] ) ) ? $instance[ 'height' ] : 240;
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Type:' ); ?></label> 
        <select name="<?php echo $this->get_field_name( 'type' ); ?>">
            <option value="map" <?php if($type=='map')echo "selected";?>>map</option>
            <option value="photo" <?php if($type=='photo')echo "selected";?>>photo</option>
            <option value="slider" <?php if($type=='slider')echo "selected";?>>slider</option>
        </select>
        <br />
		<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:' ); ?></label> 
        <br />
		<input name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $width;?>" />
		<br />
		<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:' ); ?></label> 
        <br />
		<input name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $height;?>" />
		</p>
		<?php 
	}
}
?>
