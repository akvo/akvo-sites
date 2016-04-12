<?php

class AkvoTestimonialsWidget extends WP_Widget {
    private $textDomain = 'akvoblocksbootstrap3';
	function __construct() {
		// Instantiate the parent object
		parent::__construct(
	 		'akvoblocks_testimonials_widget', // Base ID
			'Akvo Blocks Testimonials Widget', // Name
			array( 'description' => __( 'Widget with slider through testimonials'), ) // Args
		);
        
        add_action('wp_enqueue_scripts', array($this, 'load_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'load_styles'));
	}
    
    /**
     * Upload the Javascripts for the media uploader
     */
    public function load_scripts()
    {
        wp_enqueue_script('bxslider', get_template_directory_uri() . '/lib/akvoblocks-testimonials-widget/jquery.bxslider.min.js',array('jquery'));
        wp_enqueue_script('videojs', '//vjs.zencdn.net/4.7/video.js',array('jquery'));
    }
    
    /**
     * Add the styles for the upload media box
     */
    public function load_styles()
    {
        wp_enqueue_style('bxslider', get_template_directory_uri() . '/lib/akvoblocks-testimonials-widget/jquery.bxslider.css');
        wp_enqueue_style('videojs', '//vjs.zencdn.net/4.7/video-js.css');
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
        $sliderTimeout = $instance['timeout']*1000;
        $img_w = $instance['imgwidth'];
        $img_h = $instance['imgheight'];
        $args = array(
            'post_type' => 'testimonial',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'testimonial-type',
                    'field' => 'slug',
                    'terms' => $instance['category']
                )
            )
        );
        
		echo $before_widget;
        ?>
        <div class="cDivWrapper" id="sliderWrapper-<?php echo $widget_id;?>">
            <?php if($instance['title']!=='')echo $before_title.$instance['title'].$after_title;?>
            
        <div class="cDivSlider">
            <?php
            $query = new WP_Query( $args );
        // The Loop
        while ( $query->have_posts() ) {
            $query->the_post();
            $oSliderImage = get_field('image');
            $video = get_field('video');
            $videoYT = get_field('youtube_embed_url');
            $name = get_the_title();
            $quote = get_field('quote');
            $function = get_field('function');
            $location = get_field('location');
            $url = get_field('company_url');
            $show_overlay = get_field('show_detail_overlay');
            $sModalClass = (!$show_overlay) ? '' : 'hasModal';
            $overlayAttributes = (!$show_overlay) ? '' : 'data-testimonial="'.get_permalink().'" data-toggle="modal" data-target=".testimonials-modal-'.$widget_id.'"';
            ?>
            <div class="cDivSlide <?php echo $sModalClass; ?>" <?php echo $overlayAttributes;?>>
                <?php if($oSliderImage){ 
                    $imgurl = get_template_directory_uri().'/lib/thumb.php?src='.$oSliderImage['url'];
                    $imgurl.= '&w='.$img_w;
                    $imgurl.= '&h='.$img_h;
                    $imgurl.= '&zc=1&q=100';
                    ?>
                    <div class="cDivSlideImage" <?php echo $overlayAttr;?>>
                        <img src="<?php echo $imgurl;?>" class="img-responsive" />
                            <br style="clear:both;" />
                        <?php if($video || $videoYT) { ?>
                            <div class="cDivPlayOverlay"></div>
                        <?php } ?>
                    </div>
                <?php } ?>
                    <div class="cDivSlideContent" <?php echo $overlayAttr;?>>
                        <?php if($quote){ ?>
                            <div class="cDivQuote">
                                <?php echo $quote;?>
                            </div>
                        <?php } ?>
                        <div class="cDivInfo clearfix">
                            <?php if($name){ ?>
                                <span class="cDivName">
                                    <?php echo $name;?>
                                </span>
                            <?php } ?>
                            <?php if($function){ ?>
                                <span class="cDivFunction">
                                    <?php echo $function;?>
                                </span>
                            <?php } ?>
                            <?php if($location){ ?>
                                <span class="cDivLocation">
                                    <?php echo $location;?>
                                </span>
                            <?php } ?>
                            <?php if($url){ ?>
                                <span class="cDivCompanyUrl">
                                    <a href="http://<?php echo $url; ?>"><?php echo $url;?></a>
                                </span>
                            <?php } ?>
                        </div>
                    </div>
                    <br style="clear:both;" />
            </div>
            <?php
        }

        // Restore original Post Data 
        
        wp_reset_postdata();
            ?>
            
        </div>
        </div>
        <div class="modal fade testimonials-modal-<?php echo $widget_id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">&nbsp;</h4>
                  </div>
                  <div class="modal-body">
                    ...
                  </div>
              </div>
            </div>
          </div>
        <script type="text/javascript">
            +function ($) {
                $('#sliderWrapper-<?php echo $widget_id;?> .cDivSlider').bxSlider({
                 auto:true,
				 pause: 8000,
                 adaptiveHeight:true,
                 pause: <?php echo $sliderTimeout; ?>
                });
                
                $('.testimonials-modal-<?php echo $widget_id; ?>').on('hidden.bs.modal', function (e) {
                    $('.testimonials-modal-<?php echo $widget_id; ?> .modal-body').html('...');
                });
                $('.testimonials-modal-<?php echo $widget_id; ?>').on('shown.bs.modal', function (e) {
                    var el = $(e.relatedTarget);
                    var url = el.data('testimonial');
                    $.get(url,{},function(response){
                        $('.testimonials-modal-<?php echo $widget_id; ?> .modal-body').html(response);
                    });
                });
                
            }(jQuery);
        </script>
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
		$instance['category'] = strip_tags( $new_instance['category'] );
		$instance['timeout'] = strip_tags( $new_instance['timeout'] );
		$instance['imgwidth'] = strip_tags( $new_instance['imgwidth'] );
		$instance['imgheight'] = strip_tags( $new_instance['imgheight'] );
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
			$category = ( isset( $instance[ 'category' ] ) ) ? $instance[ 'category' ] : '';
			$imgwidth = ( isset( $instance[ 'imgwidth' ] ) ) ? $instance[ 'imgwidth' ] : '400';
			$imgheight = ( isset( $instance[ 'imgheight' ] ) ) ? $instance[ 'imgheight' ] : '300';
			$timeout = ( isset( $instance[ 'timeout' ] ) ) ? $instance[ 'timeout' ] : '5';
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'timeout' ); ?>"><?php _e( 'Slider timeout:' ); ?></label> 
            <input class="widefat" name="<?php echo $this->get_field_name( 'timeout' ); ?>" value="<?php echo $timeout;?>" />
		</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title;?>" />
		</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'imgwidth' ); ?>"><?php _e( 'Image width:' ); ?></label> 
            <input class="widefat" name="<?php echo $this->get_field_name( 'imgwidth' ); ?>" value="<?php echo $imgwidth;?>" />
		</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'imgheight' ); ?>"><?php _e( 'Image height:' ); ?></label> 
            <input class="widefat" name="<?php echo $this->get_field_name( 'imgheight' ); ?>" value="<?php echo $imgheight;?>" />
		</p>
		<p>
            <label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category:' ); ?></label> 
            <select class="widefat" name="<?php echo $this->get_field_name( 'category' ); ?>">
            <?php 
                $aCategories = get_terms('testimonial-type', array('hide_empty'=>false));
                if($aCategories){
                    foreach($aCategories AS $oCategory){
                        $selected = ($category == $oCategory->slug) ? 'selected' : '';
                        ?>
                        <option value="<?php echo $oCategory->slug;?>" <?php echo $selected;?>><?php echo $oCategory->name;?></option>
                        <?php
                    }
                }
            ?>
            </select>
		</p>
        
		
        
		<?php 
	}
}


?>