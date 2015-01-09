<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class AkvoFlickrSlideshow{
    // [akvoflickr user="" width="" height="" api_key=""]
    public static function displayslideshow($atts){
        extract( shortcode_atts( array(
            'width' => 583,
            'height' => 450,
            "user" => "60455798@N03",
            'api_key' => '6a3eb90aa9326a66aed37c198b5e519b'
        ), $atts ) );
        $sBaseURI = 'https://www.flickr.com/services/rest/?nojsoncallback=1&format=json';
        $aArgs = array(
            'method' => 'flickr.people.getPublicPhotos',
            'user_id' => $user,
            'api_key' => $api_key
        );
        $args_string = '';
        foreach($aArgs as $key=>$value) { $args_string .= '&'.$key.'='.$value; }
        rtrim($args_string, '&');
        $sApiResponse = file_get_contents($sBaseURI.$args_string);
        $aFotos = json_decode($sApiResponse,true);
		?>
<!-- load Galleria -->
<script src="/wp-content/themes/Quadro/js/galleria/galleria-1.2.9.min.js"></script>

<!-- load flickr plugin -->
<script src="/wp-content/themes/Quadro/js/galleria/galleria.flickr.min.js"></script>
<div id="iDivAkvoFlickr" style="width:<?php echo $width;?>px;height:<?php echo $height;?>px;">
    <?php //var_dump($aFotos);?>
    
    <?php foreach($aFotos['photos'] AS $aPage){
        if(is_array($aPage)){
            foreach($aPage AS $aPhoto){

            echo '<a href="http://farm'.$aPhoto['farm'].'.staticflickr.com/'.$aPhoto['server'].'/'.$aPhoto['id'].'_'.$aPhoto['secret'].'_b.jpg" >';
            echo '<img data-title="'.$aPhoto['title'].'" src="http://farm'.$aPhoto['farm'].'.staticflickr.com/'.$aPhoto['server'].'/'.$aPhoto['id'].'_'.$aPhoto['secret'].'_q.jpg" />';
            echo '</a>';
            }
        }
    }?>
</div>

<style>
	
</style>
<script>
    Galleria.loadTheme('/wp-content/themes/Quadro/js/galleria/themes/classic/galleria.classic.min.js');
    Galleria.ready(function(options) {
        this.setOptions('fullscreenDoubleTap',true);
    this.attachKeyboard({
        left: this.prev, // applies the native prev() function
        right: this.next,
        27:this.exitFullscreen()
    });
		this.lazyLoadChunks( 10 );
    
		// creates a new element with the id 'mystuff':
this.addElement('togglefulscr');

// appends the element to the container
this.appendChild('stage','togglefulscr');
		jQuery('.galleria-togglefulscr').click(function(e){
			Galleria.log('toggle');
        jQuery('#iDivAkvoFlickr').data('galleria').toggleFullscreen();
    });
});
    Galleria.configure({
    thumbnails: "lazy"
});
	Galleria.run('#iDivAkvoFlickr');
	
</script>
        <?php
	
    }
}
?>
