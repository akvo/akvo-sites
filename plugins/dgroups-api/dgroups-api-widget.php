<?php

class DgroupsApiWidget extends WP_Widget {
    private $textDomain = 'dgroups_api_plugin';
	function __construct() {
		// Instantiate the parent object
		parent::__construct(
	 		'dgroups_api_widget', // Base ID
			'Dgroups Api Widget', // Name
			array( 'description' => __( 'Widget with current network status'), ) // Args
		);
        
//        add_action('admin_enqueue_scripts', array($this, 'upload_scripts'));
//        add_action('admin_enqueue_styles', array($this, 'upload_styles'));
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
        $show = unserialize($instance['show']);
        $aData = DgroupsApi::getData();
		echo $before_widget;
        ?>
        <div class="cDivWrapper">
            <div class="cDivContent">
                <?php echo $before_title.$instance['title'].$after_title;?>
                <p><?php echo $instance['content'];?></p>
            </div>
            <?php foreach ($show AS $datakey){ ?>
            <div class="cDivDataArea <?php echo strtolower($datakey); ?>">
                <div class="cDivDataName"><h3><?php _e($datakey,$this->textDomain); ?></h3></div>
                <div class="cDivDataImage">
                    <img class="img-responsive" src="<?php echo get_stylesheet_directory_uri();?>/images/dgroups-data-<?php echo strtolower($datakey); ?>.png" />
                </div>
                <div class="cDivDataValue"><?php echo $aData[$datakey]; ?></div>
            </div>
            
            <?php } ?>
            <br style="clear:both;" />
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
		$instance['show'] = serialize( $new_instance['show'] );

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
			$show = ( isset( $instance[ 'show' ] ) ) ? unserialize($instance[ 'show' ]) : array();
            $aDataKeys = array(
                'Members',
                'Contributions',
                'Contributors',
                'Countries',
                'FirstContribution',
                'LastContribution',
            );
		?>
        
		<p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title;?>" />
		</p>
		<p>
            <label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Content:' ); ?></label> 
            <textarea rows="10" class="widefat" name="<?php echo $this->get_field_name( 'content' ); ?>"><?php echo $content;?></textarea>
		</p>
		<p>
            <label for="<?php echo $this->get_field_id( 'show' ); ?>"><?php _e( 'Show:' ); ?></label><br />
            <?php foreach($aDataKeys AS $datakey){ ?>
                <?php $checked = (in_array($datakey,$show)) ? 'checked' : '';?>
                <input type="checkbox" id="<?php echo $this->get_field_id( 'show' ); ?>-<?php echo $datakey;?>" name="<?php echo $this->get_field_name( 'show' ); ?>[]" value="<?php echo $datakey;?>" <?php echo $checked;?> />
                <label for="<?php echo $this->get_field_id( 'show' ); ?>-<?php echo $datakey;?>"><?php echo $datakey; ?></label>
                <br />
            <?php } ?>
		</p>
        
		
        
		<?php 
	}
}


?>