<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!class_exists("AkvoSiteConfig")) {

	/**
	 *
	 */
	class AkvoSiteConfig {
        
        public function __construct() {
            
        }
        
        
        public static function registerNewsType(){
            register_post_type( 'news',
                        array( 
                        'labels' => array(
                            'name'=> __('News'),
                            'singular_name' => __('News item')
                            ), 
                        'public' => true, 
                        'show_ui' => true,
                        'show_in_nav_menus' => false,
                        'menu_position' => 25,
                            'exclude_from_search' => false,
                        'rewrite' => array(
                            'slug' => 'news',
                            'with_front' => FALSE,
                        ),
                        'supports' => array(
                                'title',
                                'excerpt',
                                'editor',
                                'custom-fields',
                                'page-attributes',
                                'thumbnail')
                            ) 
                        );
        }
        
        public static function getLatestVideo($sUserName){
            $xml = simplexml_load_file(sprintf('http://gdata.youtube.com/feeds/base/users/%s/uploads?alt=rss&v=2&orderby=published', $sUserName));
            $avideo = array();
            if ( ! empty($xml->channel->item[0]->link) )
            {
                
                parse_str(parse_url($xml->channel->item[0]->link, PHP_URL_QUERY), $url_query);
              
              if ( ! empty($url_query['v']) )
                $id = $url_query['v'];
                $img = 'http://img.youtube.com/vi/'.$id.'/0.jpg';
                $feedURL = 'http://gdata.youtube.com/feeds/api/videos/' . $id;
                $entry = AkvoYoutube::parseVideoEntry(simplexml_load_file($feedURL));
                $avideo['image'] = '/wp-content/plugins/akvo-site-config/classes/thumb.php?src='.$img.'&w=271&h=167&zc=1&q=100';
                $avideo['title'] = (string)$entry->title;
                $avideo['post_content'] = (string)$entry->description;
                $avideo['post_type'] = 'video';
                $avideo['link'] = (string)$xml->channel->item[0]->link;
                $avideo['date'] = (string)$xml->channel->item[0]->pubDate;
                
                return $avideo;
            }
        }
		
		/**
         * run this every hour to get the embedded video screenshots from posts
         */
        public static function getPostsFeaturedVideo(){
			//wp_mail('eveline@kominski.net', 'getting video images', 'hoi');
            
            $today = getdate();
            $args = array(
                'post_type'=>'any',
                'posts_per_page'=>-1,
                'year'=>$today['year'],
                'nopaging'=>true,
                'monthnum'=>$today['mon'],
                'day'=>$today['mday']
                
            );
            $query = new WP_Query($args);
            foreach($query->posts AS $post){
                if($post->post_content!=''){
                    $document = new DOMDocument();
                    $document->loadHTML($post->post_content);
					$aItems = array();
                    $aItems[] = $document->getElementsByTagName('iframe');
					$aItems[] = $document->getElementsByTagName('embed');
					foreach($aItems AS $lst){
						for ($i=0; $i<$lst->length; $i++) {
							$iframe= $lst->item($i);
							$src= $iframe->attributes->getNamedItem('src')->value;
							$url_query=parse_url($src);
							if(strpos($url_query['host'], 'youtube.com')!==false){
								preg_match("@/v\/(?P<vid>.{11})|\/embed\/(?P<id>.{11})@", $url_query['path'], $matches);
								$id = ($matches['vid']!='') ? $matches['vid'] : $matches['id'];
								$image = $return = 'http://img.youtube.com/vi/'.$id.'/0.jpg';
							}elseif(strpos($url_query['host'], 'vimeo.com')!==false){
								preg_match("@\/video/(?P<id>[0-9]*)@", $url_query['path'], $matches);
								$id = $matches['id'];
								$oVideo = json_decode(file_get_contents('http://vimeo.com/api/v2/video/'.$id.'.json'));
								if(is_array($oVideo) && isset($oVideo[0]->thumbnail_large)){
									$image = $oVideo[0]->thumbnail_large;
								}
							}else{
								//no video found in post, so skip post
								continue;
							}
							if($image!=''){
								delete_post_meta($post->ID, 'enclosure');
								add_post_meta($post->ID, 'enclosure', $image,true);
							}
						}
					}
                }
            }
            
        }
        
    }
}
?>
