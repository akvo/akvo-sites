<?php


class AkvoTestimonials{
    private $textDomain;
    public function __construct() {
        global $akvo_testimonials_plugin_options;
        global $akvo_testimonials_text_domain;
        $this->textDomain = $akvo_testimonials_text_domain;
    }
    
    /**
    * Register a testimonials post type.
    *
    * @link http://codex.wordpress.org/Function_Reference/register_post_type
    */
    function register_post_type() {
        $labels = array(
            'name'               => _x( 'Testimonials', 'post type general name', $this->textDomain ),
            'singular_name'      => _x( 'Testimonial', 'post type singular name', $this->textDomain ),
            'menu_name'          => _x( 'Testimonials', 'admin menu', $this->textDomain ),
            'name_admin_bar'     => _x( 'Testimonial', 'add new on admin bar', $this->textDomain ),
            'add_new'            => _x( 'Add New', 'book', $this->textDomain ),
            'add_new_item'       => __( 'Add New Testimonial', $this->textDomain ),
            'new_item'           => __( 'New Testimonial', $this->textDomain ),
            'edit_item'          => __( 'Edit Testimonial', $this->textDomain ),
            'view_item'          => __( 'View Testimonial', $this->textDomain ),
            'all_items'          => __( 'All Testimonials', $this->textDomain ),
            'search_items'       => __( 'Search Testimonials', $this->textDomain ),
            'parent_item_colon'  => __( 'Parent Testimonials:', $this->textDomain ),
            'not_found'          => __( 'No testimonials found.', $this->textDomain ),
            'not_found_in_trash' => __( 'No testimonials found in Trash.', $this->textDomain )
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'testimonial' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' )
        );

        register_post_type( 'testimonial', $args );
    }
    
    // create taxonomies, type for the post type "testimonial"
    function create_testimonial_taxonomies() {
        // Add new taxonomy, make it hierarchical (like categories)
        $labels = array(
            'name'              => _x( 'Testimonial types', 'taxonomy general name' ),
            'singular_name'     => _x( 'Testimonial type', 'taxonomy singular name' ),
            'search_items'      => __( 'Search Testimonial types' ),
            'all_items'         => __( 'All Testimonial types' ),
            'parent_item'       => __( 'Parent Testimonial type' ),
            'parent_item_colon' => __( 'Parent Testimonial type:' ),
            'edit_item'         => __( 'Edit Testimonial type' ),
            'update_item'       => __( 'Update Testimonial type' ),
            'add_new_item'      => __( 'Add New Testimonial type' ),
            'new_item_name'     => __( 'New Testimonial type Name' ),
            'menu_name'         => __( 'Testimonial type' ),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'testimonial-type' ),
        );

        register_taxonomy( 'testimonial-type', array( 'testimonial' ), $args );

        
    }
    
    // add admin meta fields using advanced-custom-fields-code
    function set_meta_fields(){
        if(function_exists("register_field_group"))
        {
            register_field_group(array (
                'id' => 'acf_testimonials',
                'title' => 'Testimonials',
                'fields' => array (
                    array (
                        'key' => 'field_53d6212cd27e1',
                        'label' => 'Image',
                        'name' => 'image',
                        'type' => 'image',
                        'instructions' => 'Slider image',
                        'required' => 1,
                        'save_format' => 'object',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                    ),
                    array (
                        'key' => 'field_53d62673d27e2',
                        'label' => 'Quote',
                        'name' => 'quote',
                        'type' => 'textarea',
                        'instructions' => 'Testimonial quote',
                        'default_value' => '',
                        'placeholder' => '',
                        'maxlength' => '',
                        'rows' => '',
                        'formatting' => 'br',
                    ),
                    array (
                        'key' => 'field_53d626a0d27e3',
                        'label' => 'Function',
                        'name' => 'function',
                        'type' => 'text',
                        'instructions' => 'Professional function of this person',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array (
                        'key' => 'field_53d626c7d27e4',
                        'label' => 'Location',
                        'name' => 'location',
                        'type' => 'text',
                        'instructions' => 'Location of this person',
                        'default_value' => '',
                        'placeholder' => 'Amsterdam, Netherlands',
                        'prepend' => '',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array (
                        'key' => 'field_53d626ecd27e5',
                        'label' => 'Company URL',
                        'name' => 'company_url',
                        'type' => 'text',
                        'instructions' => 'the URL to the company website, without http:// or https://',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => 'http(s)://',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                    array (
                        'key' => 'field_53d6285bd27e9',
                        'label' => 'Show detail overlay?',
                        'name' => 'show_detail_overlay',
                        'type' => 'true_false',
                        'message' => '',
                        'default_value' => 0,
                    ),
                    array (
                        'key' => 'field_53d62824d27e8',
                        'label' => 'Detail overlay',
                        'name' => '',
                        'type' => 'tab',
                        'conditional_logic' => array (
                            'status' => 1,
                            'rules' => array (
                                array (
                                    'field' => 'field_53d6285bd27e9',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                            'allorany' => 'all',
                        ),
                    ),
                    array (
                        'key' => 'field_53d62770d27e6',
                        'label' => 'Upload video',
                        'name' => 'video',
                        'type' => 'file',
                        'instructions' => 'Video file (h264 encoded)',
                        'save_format' => 'object',
                        'library' => 'all',
                    ),
                    array (
                        'key' => 'field_54057b0c97859',
                        'label' => 'Or insert youtube video url',
                        'name' => 'youtube_embed_url',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => 'eg. https://www.youtube.com/watch?v=OgZsPepO9Z0',
                        'formatting' => 'none',
                        'maxlength' => '',
                    ),
                    array (
                        'key' => 'field_53d627e8d27e7',
                        'label' => 'Overlay content',
                        'name' => 'overlay_content',
                        'type' => 'wysiwyg',
                        'instructions' => 'Content in the detail overlay',
                        'default_value' => '',
                        'toolbar' => 'full',
                        'media_upload' => 'yes',
                    ),
                    array (
                        'key' => 'field_53d628a2d27ea',
                        'label' => 'Twitter profile name',
                        'name' => 'twitter_profile_name',
                        'type' => 'text',
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => 'https://twitter.com/',
                        'append' => '',
                        'formatting' => 'html',
                        'maxlength' => '',
                    ),
                ),
                'location' => array (
                    array (
                        array (
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'testimonial',
                            'order_no' => 0,
                            'group_no' => 0,
                        ),
                    ),
                ),
                'options' => array (
                    'position' => 'acf_after_title',
                    'layout' => 'no_box',
                    'hide_on_screen' => array (
                        0 => 'permalink',
                        1 => 'the_content',
                        2 => 'excerpt',
                        3 => 'custom_fields',
                        4 => 'discussion',
                        5 => 'comments',
                        6 => 'revisions',
                        7 => 'slug',
                        8 => 'author',
                        9 => 'format',
                        10 => 'featured_image',
                        11 => 'categories',
                        12 => 'tags',
                        13 => 'send-trackbacks',
                    ),
                ),
                'menu_order' => 0,
            ));
        }

    }
}
