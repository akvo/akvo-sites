<?php

global $wpdb; 

//akvo_debug_dump($wpdb->last_result);
    
    ?>
<?php get_header(); ?>

<div id="container">
	<div id="container2">

		<div id="left-div">

			<div class="post-wrapper">
			<?php esc_html_e('This is a secured page. Please Log in to continue','Quadro');?> 
                <?php
                $args = array(
        'form_id' => 'loginform-custom',
        'label_username' => __( 'Username' ),
        'label_password' => __( 'Password' ),
        'label_remember' => __( 'Remember me' ),
        'label_log_in' => __( 'Login' ),
        'remember' => true
    );
    wp_login_form( $args );
    ?>
			</div>
		   
		</div>
        <?php get_sidebar(); ?>
	</div>
	
</div>
<?php get_footer(); ?>
</body>
</html>
