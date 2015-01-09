<?php

/**
 * Calls the class on the post edit screen.
 */
function call_Metaboxes() {
    new AkvoBlocks_Sidebars_Meta();
}

if ( is_admin() ) {
    add_action( 'load-post.php', 'call_Metaboxes' );
    add_action( 'load-post-new.php', 'call_Metaboxes' );
}

/**
 * Dynamic sidebars metaboxes
 */
class AkvoBlocks_Sidebars_Meta{
    public function __construct(){
        add_action( 'add_meta_boxes', array($this,'add_meta_box') );
        add_action( 'save_post', array($this,'save'));
    }
    /**
	 * Adds the meta box container.
	 */
	function add_meta_box() {

        $screens = array( 'post', 'page' );
        $priorities = array('top','right');
        foreach ( $screens as $screen ) {
            foreach ( $priorities as $position ) {

                add_meta_box(
                    'akvoblocks_meta_'.$position,
                    __( 'Add dynamic sidebar at the '.$position, 'akvoblocksbootstrap3' ),
                    array($this,'render_metabox_content'),
                    $screen,
                    'normal',
                    'high',
                    array('position'=>$position)
                );

            }
        }

    }

    /**
     * Prints the box content.
     * 
     * @param WP_Post $post The object for the current post/page.
     */
    function render_metabox_content( $post, $info ) {
        $id = $info['id'];
        $position = $info['args']['position'];
        // Add an nonce field so we can check for it later.
        wp_nonce_field( $id, $id.'_nonce' );

        /*
         * retrieve an existing value
         * from the database and use the value for the form.
         */
        $value = self::getValue($position,$post->ID);
        $aSideBars = get_option('otw_sidebars');
        echo '<label for="'.$id.'_field">';
        _e( 'Select a sidebar, or add a new sidebar <a href="/wp-admin/admin.php?page=otw-wpl">here</a>', 'myplugin_textdomain' );
        echo '</label><br /><br /> ';
        echo '<select id="'.$id.'_field" name="'.$id.'_field">';
        echo '<option value="none">none</option>';
        foreach($aSideBars AS $iSidebarID => $aSidebar){
            $selected = ($iSidebarID===$value) ? 'selected' : '';
            echo '<option value="'.$iSidebarID.'" '.$selected.'>'.$aSidebar['title'].'</option>';
        }
        echo '</select>';
    }

    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id The ID of the post being saved.
     */
    function save( $post_id ) {
        $positions = array('top','right');
        foreach($positions AS $position){
            $box_id = 'akvoblocks_meta_'.$position;

            /*
             * We need to verify this came from our screen and with proper authorization,
             * because the save_post action can be triggered at other times.
             */

            // Check if our nonce is set.
            if ( ! isset( $_POST[$box_id.'_nonce'] ) ) {
                continue;
            }

            // Verify that the nonce is valid.
            if ( ! wp_verify_nonce( $_POST[$box_id.'_nonce'], $box_id ) ) {
                continue;
            }

            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                continue;
            }

            // Check the user's permissions.
            if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

                if ( ! current_user_can( 'edit_page', $post_id ) ) {
                    continue;
                }

            } else {

                if ( ! current_user_can( 'edit_post', $post_id ) ) {
                    continue;
                }
            }

            /* OK, it's safe for us to save the data now. */

            // Make sure that it is set.
            if ( ! isset( $_POST[$box_id.'_field'] ) ) {
                continue;
            }

            // Sanitize user input.
            $my_data = sanitize_text_field( $_POST[$box_id.'_field'] );

            // Update the meta field in the database.
            update_post_meta( $post_id, $box_id, $my_data );
        }
        return;
    }
    /**
     * Get sidebar for post/page
     * @global type $post
     * @param string $position required 'top','right'
     * @param int $iPostID optional
     */
    public static function getValue($position,$iPostID = null){
        $box_id = 'akvoblocks_meta_'.$position;
        if(!$iPostID){
            global $post;
            $iPostID = $post->ID;
        }
        $sidebar = get_post_meta( $iPostID, $box_id, true );
        return ($sidebar!=='none') ? $sidebar : false;
    }
}


