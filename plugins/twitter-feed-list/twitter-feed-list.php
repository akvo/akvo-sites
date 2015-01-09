<?php
/*
Plugin Name: Twitter feed list
Plugin URI: http://wordpress.org/plugins/twitter-feed-list/
Description: Twitter API 1.1 compliant wordpress plugin that provides a widget to display a twitter feed based on your search input.
Version: 1.0.1
Author: Eveline Sparreboom
*/


require('ApiTwitter.class.php');
require('twitter-feed-list-settings.php');

function tfl_checkSettings(){
    $config['key'] = get_option('tdf_consumer_key');
    $config['secret'] = get_option('tdf_consumer_secret');
    $config['token'] = get_option('tdf_access_token');
    $config['token_secret'] = get_option('tdf_access_token_secret');
    $error=false;
    foreach($config AS $val){
        if(!$val || $val==''){
            $error = true;
            break;
        }
    }
    return $error;
}
function tfl_queryTweets($count = 20, $query = false, $options = false) {
  $config['key'] = get_option('tdf_consumer_key');
  $config['secret'] = get_option('tdf_consumer_secret');
  $config['token'] = get_option('tdf_access_token');
  $config['token_secret'] = get_option('tdf_access_token_secret');
  $config['cache_expire'] = 3600;
  $config['directory'] = plugin_dir_path(__FILE__);
  
  $obj = new ApiTwitter($config);
  $res = $obj->queryTweets($count, $query, $options);
  update_option('tdf_last_error',$obj->st_last_error);
  return $res;
  
}

function tfl_filterTweetContent($tweet){
    //make html links for urls, users and hashtags
    $tweet = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $tweet);
    $tweet = preg_replace('/(^|\s)@([a-z0-9_]+)/i','$1<a href="http://www.twitter.com/$2">@$2</a>',$tweet);
    $tweet = preg_replace('/(^|\s)#([a-z0-9_]+)/i','$1<a href="http://www.twitter.com/search?q=%23$2&src=hash">#$2</a>',$tweet);
                            
    return $tweet;
}
function register_oauth_twitter_widget(){
    register_widget( 'oAuth_Twitter_Widget' );
}
add_action( 'widgets_init', 'register_oauth_twitter_widget' );
function tfl_check_widget() {
    if( is_active_widget( '', '', 'oauth_twitter_widget' ) ) { // check if search widget is used
        wp_register_style( 'twitter-feed-style', plugins_url('style.css', __FILE__) );
        wp_enqueue_style( 'twitter-feed-style' );
    }
}

add_action( 'init', 'tfl_check_widget' );


add_filter('plugin_action_links', 'tfl_plugin_action_links', 10, 2);

function tfl_plugin_action_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        // The "page" query string value must be equal to the slug
        // of the Settings admin page we defined earlier, which in
        // this case equals "myplugin-settings".
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=tdf_settings">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}
/**
 * Twitter widget
 */
class oAuth_Twitter_Widget extends WP_Widget {

	function __construct() {
		// Instantiate the parent object
        
		parent::__construct( false, 'Twitter widget' );
	}
    
	function widget( $args, $instance ) {
		// Widget output
        extract($instance);
        $search_query = '';
        //$str     = "Line 1\nLine 2\rLine 3\r\nLine 4\n";
        $order   = array("\r\n", "\n", "\r");
        $replace = ' OR ';

        // Processes \r\n's first so they aren't converted twice.
        $newstr = str_replace($order, $replace, $query);
        $newstr = str_replace('@', 'from:', $newstr);
        if($query != '')$search_query=$newstr;
        echo $args['before_widget'];
        ?>
				
            <div id="twitter-widget" class="widget twitter">
                <div class="widget_tag"></div>
                <div class="tweets"> 
				<div class="tweets_header"><?php echo $args['before_title'].$title.$args['after_title'];?></h2></div> 
				<div> 
                <?php $tweets = tfl_queryTweets($display_limit,$search_query); 
                    if(count($tweets)>0){
                       // var_dump($tweets);
                        foreach($tweets AS $tweet){
                            //make html links for urls, users and hashtags
                            $tweet['text'] = tfl_filterTweetContent($tweet['text']);
                            ?>
                    <div class="tweet">
                        <?php if (is_array($tweet['user'])){?>
                        <img src="<?php echo $tweet['user']['profile_image_url'];?>" align="left" /><a href="https://twitter.com/<?php echo $tweet['user']['screen_name'];?>"><?php echo $tweet['user']['screen_name'];?></a>
                        <?php } ?>
                        <?php echo $tweet['text'];?>
                        <br style="clear:both;" />
                    </div>
                            
                            <?php
                        }
                    }else{
                        echo 'No tweets found for '.strtolower($search_query);
                    }
                    ?>
                </div> 

            </div>
            </div>
        <?php
        echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
        $instance = array();
		$instance['query'] = strip_tags( $new_instance['query'] );
		$instance['display_limit'] = strip_tags( $new_instance['display_limit'] );
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	function form( $instance ) {
		// Output admin widget options form
        $title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : 'Recent tweets';
        $query = ( isset( $instance[ 'query' ] ) ) ? $instance[ 'query' ] : '';
        $limit = ( isset( $instance[ 'display_limit' ] ) ) ? $instance[ 'display_limit' ] : 5;
        if(tfl_checkSettings()){
		?>
        <p><b>Warning! You have not authorized twitter yet, so tweets can not be received. Please visit the <a href="<?php echo get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=tdf_settings';?>">settings</a> page to do this. </b></p>
        <?php
        }
        ?>
		<p>
            
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'title:' ); ?></label> 
        <input  id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo $title; ?>">
		<br /><br /><label for="<?php echo $this->get_field_id( 'query' ); ?>">Enter @usernames or #hashtags to follow. One per line.</label> 
        <br /><textarea  id="<?php echo esc_attr($this->get_field_id('query')); ?>" name="<?php echo esc_attr($this->get_field_name('query')); ?>" type="text" rows="10" ><?php echo $query; ?></textarea>
		<br /><label for="<?php echo $this->get_field_id( 'display_limit' ); ?>"><?php _e( 'limit:' ); ?></label> 
        <input  id="<?php echo esc_attr($this->get_field_id('display_limit')); ?>" name="<?php echo esc_attr($this->get_field_name('display_limit')); ?>" type="text" value="<?php echo $limit; ?>">
		
		</p>
		<?php 
	}
}
?>