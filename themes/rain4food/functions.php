<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

////////////////////////////////////////////////////////////////////
// Theme Information
////////////////////////////////////////////////////////////////////

    $themename = "rain4food";
    $developer_uri = "http://akvoblocks.com";
    $shortname = "dm";
    $version = '1.29';
    load_theme_textdomain( 'rain4food', get_template_directory() . '/languages' );
////////////////////////////////////////////////////////////////////
// Register rain4food widgets
////////////////////////////////////////////////////////////////////
require_once('lib/featured-video-widget.php');
require_once('lib/downloads-page-widget.php');
require_once('lib/become-a-part-of-it-widget/widget.php');


function r4f_register_widgets() {
	register_widget( 'FeaturedVideoWidget' );
	register_widget( 'DownloadsPageWidget' );
	register_widget( 'BAPWidget' );
}

add_action( 'widgets_init', 'r4f_register_widgets' );

function ifad_iframe($atts=null,$content){
    $addclass=(is_array($atts) && (array_search('wide',$atts)!==false) || array_key_exists('wide', $atts)) ? 'no_sidebar' : '';
    return '<div class="ifad_iframe '.$addclass.'">'.do_shortcode($content).'<br style="clear:both;"></div>';
}
add_shortcode( 'ifad_iframe', 'ifad_iframe' );

function ifad_sharedocs($atts=null,$content){
    //$addclass=(is_array($atts) && (array_search('wide',$atts)!==false) || array_key_exists('wide', $atts)) ? 'no_sidebar' : '';
    return '</div> <!-- end .post-wrapper -->
                <div class="post-wrapper no_sidebar">
                    <div id="shared_docs_container">
                        <h2>Library</h2>
                        <div class="filter">
                            <select id="contenttypes" rel="contenttype"></select>
                            <select id="themes" rel="theme"></select>
                            <select id="regions" rel="region"></select>
                            <select id="languages" rel="language"></select>
                            <input id="filesearch" type="text" name="filesearch" />
                            <button id="filterDocs" value="filter">filter</button>
                        </div>
                        <div id="api_results">
                            
                        </div>
                    </div>
                ';
}
add_shortcode( 'ifad_sharedocs', 'ifad_sharedocs' );
function ifad_waterchannel($atts=null,$content){
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,"http://www.thewaterchannel.tv/categories/773-water-harvesting");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
                "cache_time=1&ssl_verify_peer=1&auto_detect=0&auto_detect_user=0&main_lang=en&debug_mode=0&limitstart=0&display=list");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $sString = curl_exec ($ch);
    curl_close ($ch);

    $doc = new DOMDocument();
    $doc->loadHTML($sString);
    $xpath = new DOMXpath($doc);
    $divs = $xpath->query("//*/div[@class='media-item']");
    
    $gallery = '<div class="row">';
    $i=0;
    foreach ($divs as $div) {
        
        $children = $div->childNodes;
        $links = $div->getElementsByTagName('a');
        foreach($links AS $link){
        
            $sLink = $link->getAttribute('href');
            $pattern = '@media-gallery\/([0-9]{2,4})-@i';
            preg_match($pattern, $sLink,$matches);
            $iId = $matches[1];
            $images = $link->getElementsByTagName('img');
            foreach($images AS $img){
                $image = $img->getAttribute('src');
                $aTitle = explode('::', $img->getAttribute('title'),2);
                
            }
            if($aTitle[0]===''){
                    continue;
                }
            $image = (substr($image,0,1)==='/') ? 'http://www.thewaterchannel.tv/'.$image : $image;
            $sFirstMov = ($i===0) ? 'http://www.thewaterchannel.tv/media-gallery?task=get.embed&amp;id='.$iId.'&amp;width=560&amp;height=415' : $sFirstMov;
            if($i % 4 === 0){
                $gallery.='</div><br style="clear:both;" /><div class="row">';

            }
            $gallery.='<div class="cDivVideo col-sm-3">
                            <a onclick="javascript:document.getElementById(\'iFrameWaterChannel\').src=\'http://www.thewaterchannel.tv/media-gallery?task=get.embed&amp;id='.$iId.'&amp;width=560&amp;height=415\';" href="#" class="cAplayMovie" style="display:block;" data-mov="">
                                <img class="img-responsive" src="'.  get_template_directory_uri().'/lib/thumb.php?w=250&h=180&zc=1&src='.$image.'" />
                                    <br />
                                    '.$aTitle[0].'
                           </a>
                    </div>';
            $i++;
        }
    }
    $gallery.='</div>';
    $return = '<div class="row">
        <div class="cDivVideo col-sm-12">
            <div class="cDivFlexibleContainer">
                <iframe id="iFrameWaterChannel" width="560" height="415" src="'.$sFirstMov.'" frameborder="0" scrolling="no"></iframe>
            </div>
        </div>
        </div>'.$gallery;
    
    
    return $return;
    
}
add_shortcode( 'ifad_waterchannel', 'ifad_waterchannel' );

for($i=1;$i<=3;$i++){
    register_sidebar(
        array(
            'name' => 'Ambassadors page area '.$i,
            'id' => 'ambassadors-area-'.$i,
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h3>',
            'after_title' => '</h3>',
        )
    );
}
register_sidebar(
    array(
        'name' => 'Ambassadors page downloads area ',
        'id' => 'ambassadors-downloads-area',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    )
);