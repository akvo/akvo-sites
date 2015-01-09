<?php

class DgroupsLoginWidget extends WP_Widget {
    private $textDomain = 'dgroups_api_plugin';
	function __construct() {
		// Instantiate the parent object
		parent::__construct(
	 		'dgroups_login_widget', // Base ID
			'Dgroups Login Widget', // Name
			array( 'description' => __( 'Dgroups login form widget'), ) // Args
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
        global $dgroups_api_plugin_options;
        extract( $args );
        
		echo $before_widget;
        ?>
        <div claSS="cDivLoginForm">
            <form method="post" action="https://dgroups.org/rwsn/<?php echo $dgroups_api_plugin_options['path'];?>/login" class="form">
                <h3><?php _e('Already a member?',$this->textDomain); ?></h3>
                    
                            <div class="form-group">
                                <input type="text" id="email" name="email" value="" class="form-control" placeholder="<?php _e('Email',$this->textDomain); ?>">
                            </div>
                            <div class="form-group">
                                <input type="password" id="password" name="password" value="" class="form-control" placeholder="<?php _e('Password',$this->textDomain); ?>">
                            </div>
                            <a target="_blank" href="https://dgroups.org/rwsn/<?php echo $dgroups_api_plugin_options['path'];?>/new_password" class="forgotten"><?php _e('Set new password',$this->textDomain); ?></a>
                            <div class="checkbox">
                                <label>
                                <input type="checkbox" id="remember" name="rememberme" value="true">
                                <input type="hidden" id="rememberH" name="rememberme" value="false"> 
                                <?php _e('Remember me',$this->textDomain); ?>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-default" name="login"><?php _e('Login',$this->textDomain); ?></button>
                    
                    <input type="hidden" name="ReturnUrl" value="/rwsn/<?php echo $dgroups_api_plugin_options['path'];?>">
                
            </form>
        </div>
        <div class="cDivRegister">
            <h3><?php _e('Not a member yet?',$this->textDomain); ?></h3>
            <a target="_blank" href="https://dgroups.org/rwsn/<?php echo $dgroups_api_plugin_options['path'];?>/join">
                <?php _e('Join now! It will take only 43 seconds of your time.',$this->textDomain); ?>
            </a>       
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
		//$instance = array();
		

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
			
		?>
        
		
        
		
        
		<?php 
	}
}


?>