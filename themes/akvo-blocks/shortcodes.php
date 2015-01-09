<?php

//////////////////////////////////////////////////////////
// Button
// example. [btn size='sm' type='info' link='http://akvoblocks.com' icon="glyphicon-heart"] Go To the akvoblocks.com Website [/btn]
//////////////////////////////////////////////////////////

    function btn_func($atts, $content = NULL) {
        extract( shortcode_atts( array(
            'size' => '',
            'type' => '',
            'link' => '#',
            'icon' => ''
        ), $atts));

        if ($size != '') {
            $size = ' btn-' . $size;
        } else {
            $size = ' btn-default';
        }

        if ($icon != '') {
            $icon = '<span class="glyphicon '. $icon .'"></span>';
        }

        if ($type != '') {
            $type = ' btn-' . $type;
        } else {
            $type = 'btn-default';
        }

        $btn = "<a href='". $link ."' class='btn" . $size . "" . $type. "'>" . $icon . $content . "</a>";
        return $btn;
    }

    add_shortcode('btn', 'btn_func');

//////////////////////////////////////////////////////////
// Alert Box
// example. [alert type='info' dismiss='yes' size='3' title='Alert Block Title'] Go To the akvoblocks.com Website [/alert]
//////////////////////////////////////////////////////////

function alert_func($atts, $content = NULL) {
    extract( shortcode_atts( array(
        'type' => '',
        'dismiss' => '',
        'size' => '',
        'title' => ''
    ), $atts));

    if ($dismiss == 'yes') {
        $dismiss = "data-dismiss='alert'";
        $dismisslink ="<a class='close'>&times;</a>";
    } else {
        $dismiss = '';
        $dismisslink = '';
    }

    if ($title != '') {
        $title = "<h4 class='alert-heading'>".$title."</h4>";
        $block = " alert-block ";
    } else {
        $title = "";
        $block = "";
    }

    if ($type != '') {
        $type = " alert-" . $type ." ";
    }

    if ($size != '') {
        $size = " col-md-" . $size;
    }


    $alert = "<div class='alert". $block ."". $type ."". $size ."'". $dismiss .">";
    $alert .= $dismisslink . $title;
    $alert .= "<p>". $content . "</p>";
    $alert .= "</div>";
    return $alert;
}

add_shortcode('alert', 'alert_func');
//////////////////////////////////////////////////////////
// Panel Box
// example. [alert type='info' dismiss='yes' size='3' title='Alert Block Title'] Go To the akvoblocks.com Website [/alert]
//////////////////////////////////////////////////////////

function box_func($atts, $content = NULL) {
    extract( shortcode_atts( array(
        'type' => 'default',
        'size' => '',
        'title' => '',
        'footer' => ''
    ), $atts));

    

    if ($title != '') {
        $sTitle = $title;
        $title = "<div class='panel-heading'>";
        $title .= "<h3 class='panel-title'>".$sTitle."</h3>";
        $title .= "</div>";
    } else {
        $title = "";
    }
    if ($footer != '') {
        $sFooter = $footer;
        $footer = "<div class='panel-footer'>".$sFooter."</div>";
    } else {
        $footer = "";
    }

    if ($type != '') {
        $type = " panel-" . $type ." ";
    }

    if ($size != '') {
        $size = " col-md-" . $size;
    }


    $panel = "<div class='panel". $type ."". $size ."'>";
    $panel .= $title;
    $panel .= "<div class='panel-body'>". $content . "</div>";
    $panel .= $footer;
    $panel .= "</div>";
    return $panel;
}

add_shortcode('box', 'box_func');

function yt_video_func($atts, $content = NULL) {
    extract( shortcode_atts( array(
        'channel' => '',
        'link' => '',
        'width' => '100%'
    ), $atts));

    if($channel!==''){
        $latestvideo = AkvoSiteConfig::getLatestVideo($channel);
        $link = 'https://www.youtube.com/watch?v='.$latestvideo['id'];
    }
    $embed = '';
    if($link!==''){
        parse_str(parse_url($link,PHP_URL_QUERY), $aQuery);
        $float = ($width!=='100%') ? 'float:left;' : '';
        $embed .= '<div class="cDivFlexibleContainer" style="width:'.$width.';'.$float.'">';
        $embed .= '<iframe width="560" height="315" src="//www.youtube.com/embed/'.$aQuery['v'].'" frameborder="0" allowfullscreen></iframe>';
        $embed .= '</div>';
    
    }
    
    return $embed;
}

add_shortcode('yt_video', 'yt_video_func');
add_filter('widget_text', 'do_shortcode');