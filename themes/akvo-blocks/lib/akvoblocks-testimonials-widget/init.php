<?php

require_once 'akvo-testimonials-class.php';

/**
 * init Testimonials
 */
function akvo_testimonials_init(){
    $akvoTestimonials = new AkvoTestimonials();
    $akvoTestimonials->register_post_type();
    $akvoTestimonials->create_testimonial_taxonomies();
    $akvoTestimonials->set_meta_fields();
}
add_action( 'init', 'akvo_testimonials_init', 0 );



/**
 * load widgets
 */
require_once('widget.php');
function akvo_testimonials_register_widgets() {
	register_widget( 'AkvoTestimonialsWidget' );
}

add_action( 'widgets_init', 'akvo_testimonials_register_widgets' );

