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
                    @$document->loadHTML($post->post_content);
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
		
		/**
		 * This function fetch three latest videos from specified YouTube channel.
		 * @param type $sType - whether to use Channel ID or Username to fetch data. Values should be 'id' or 'forUsername'.
		 * @param type $sValue - value for the type. Ex: 'id' => UC9bKtqBXpsl4E449bR1XADA, 'forUsername' => Footballforwater
		 * @return type array
		 * @author Rumeshkumar <rumeshin@gmail.com>
		 */
		public function getLatestYouTubeVideo($sType, $sValue) {
			
			$sKey = 'xxx'; // Use correct key to get videos
						
			$sAPIUploadPlaylist = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&' . $sType . '=' . $sValue . '&key=' .$sKey;

			//get ID of "uploads" playlist
			$aUploadPlaylistResult = wp_remote_get($sAPIUploadPlaylist);

			$aUploadPlaylistResultBody = json_decode($aUploadPlaylistResult['body'], true);

			$aUploadPlaylistItem = $aUploadPlaylistResultBody['items'][0];

			$sUpoadsPlaylist = $aUploadPlaylistItem['contentDetails']['relatedPlaylists']['uploads'];			
			
			// get all the videos under uploads playlist								
			$sAPIVideos = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId='. $sUpoadsPlaylist . '&maxResults=3&key=' . $sKey;
			
            $aVideosResult = wp_remote_get($sAPIVideos);
			
			$aVideoBody = json_decode($aVideosResult['body'], true);
						
			$aVideoItems = $aVideoBody['items'];
			
			$aVideo = array();
			if ($aVideoBody['pageInfo']['totalResults'] > 0) {
				$i = 0;
				foreach($aVideoItems as $aVideoItem) {
					$sImg = $aVideoItem['snippet']['thumbnails']['medium']['url'];
					
					$aVideo[$i]['id'] = $aVideoItem['id'];
					$aVideo[$i]['image'] = '/wp-content/plugins/akvo-site-config/classes/thumb.php?src='.$sImg.'&w=271&h=167&zc=1&q=100';					
					$aVideo[$i]['title'] = $aVideoItem['snippet']['title'];
					$aVideo[$i]['post_content'] = $aVideoItem['snippet']['description'];
					$aVideo[$i]['post_type'] = 'video';
					$aVideo[$i]['link'] = (string)$aVideoItem['snippet']['resourceId']['videoId'];
					$aVideo[$i]['date'] = (string)$aVideoItem['snippet']['publishedAt'];
					
					$i++;
				}			
			}						
			
			return $aVideo;
		}				
	}
}
?>
