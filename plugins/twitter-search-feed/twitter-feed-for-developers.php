<?php
/*
Plugin Name: Twitter search feed
Plugin URI: http://kominski.net/dev/plugins/twitter-search-feed.zip
Description: Twitter API 1.1 compliant plugin that provides a widget displaying a search query feed.
Version: 1.0
Author: Eveline Sparreboom
*/


require('ApiTwitter.class.php');
require('twitter-feed-for-developers-settings.php');


function queryTweets($count = 20, $query = false, $options = false) {
  $config['key'] = get_option('tdf_consumer_key');
  $config['secret'] = get_option('tdf_consumer_secret');
  $config['token'] = get_option('tdf_access_token');
  $config['token_secret'] = get_option('tdf_access_token_secret');
  $config['screenname'] = get_option('tdf_user_timeline');
  $config['cache_expire'] = intval(get_option('tdf_cache_expire'));
  if ($config['cache_expire'] < 1) $config['cache_expire'] = 3600;
  $config['directory'] = plugin_dir_path(__FILE__);
  
  $obj = new ApiTwitter($config);
  $res = $obj->queryTweets($count, $query, $options);
  update_option('tdf_last_error',$obj->st_last_error);
  return $res;
  
}

function filterTweetContent($tweet){
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
        ?>
				
            <div id="twitter-widget" class="widget twitter">
                <div class="tweets"> 
				<div class="tweets_header"><h2>Recent tweets</h2></div> 
				<div> 
                <?php $tweets = queryTweets($display_limit,$search_query); 
                    if(count($tweets)>0){
                       // var_dump($tweets);
                        foreach($tweets AS $tweet){
                            //make html links for urls, users and hashtags
                            $tweet['text'] = filterTweetContent($tweet['text']);
                            ?>
                    <div class="tweet">
                        <img style="margin:0px 5px 5px 0px" src="<?php echo $tweet['user']['profile_image_url'];?>" align="left" /><a href="https://twitter.com/<?php echo $tweet['user']['screen_name'];?>"><?php echo $tweet['user']['screen_name'];?></a>
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
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
        $instance = array();
		$instance['query'] = strip_tags( $new_instance['query'] );
		$instance['display_limit'] = strip_tags( $new_instance['display_limit'] );

		return $instance;
	}

	function form( $instance ) {
		// Output admin widget options form
        $query = ( isset( $instance[ 'query' ] ) ) ? $instance[ 'query' ] : '';
        $limit = ( isset( $instance[ 'display_limit' ] ) ) ? $instance[ 'display_limit' ] : 5;
        
		?>
		<p>
            
		<br /><label for="<?php echo $this->get_field_id( 'query' ); ?>">Enter @usernames or #hashtags to follow. One per line.</label> 
        <br /><textarea  id="<?php echo esc_attr($this->get_field_id('query')); ?>" name="<?php echo esc_attr($this->get_field_name('query')); ?>" type="text" ><?php echo $query; ?></textarea>
		<br /><label for="<?php echo $this->get_field_id( 'display_limit' ); ?>"><?php _e( 'limit:' ); ?></label> 
        <br /><input  id="<?php echo esc_attr($this->get_field_id('display_limit')); ?>" name="<?php echo esc_attr($this->get_field_name('display_limit')); ?>" type="text" value="<?php echo $limit; ?>">
		
		</p>
		<?php 
	}
}