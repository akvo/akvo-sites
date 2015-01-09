<?php

global $wpdb; 

//akvo_debug_dump($wpdb->last_result);
    
    ?>
<?php get_header(); ?>

<div id="container">
	<div id="container2">

		<div id="left-div">

			<div class="post-wrapper">
			<?php esc_html_e('Dit is een beveiligde pagina. Log in om verder te gaan','Quadro');?> 
                <?php
                $args = array(
        'form_id' => 'loginform-custom',
        'label_username' => __( 'Gebruikersnaam' ),
        'label_password' => __( 'Wachtwoord' ),
        'label_remember' => __( 'Onthou mij op deze website' ),
        'label_log_in' => __( 'Inloggen' ),
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
