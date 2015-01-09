<?php

?>
<?php // theloop
if ( have_posts() ) : 
    while ( have_posts() ) : 
    the_post(); 
?>
<?php 
    $video = get_field('video');
    $videoYT = get_field('youtube_embed_url');
    $content = get_field('overlay_content');
    $twitter = get_field('twitter_profile_name');
    ?>
    <h2 class="page-header"><?php the_title() ;?></h2>
    <?php if($video){ ?>
        <div class="cDivVideo">
            <div class='cDivFlexibleContainer'>
            <video id="example_video_1" class="video-js vjs-default-skin"
                controls preload="auto" width="560" height="315">
               <source src="<?php echo $video['url'];?>" type='video/mp4' />
               <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
              </video>
            </div>
            <?php //echo $video['url'];?>
        </div>
    <br style="clear:both;" />
    <?php } ?>
    <?php if($videoYT){ ?>
        <div class="cDivVideo">
            <div class='cDivFlexibleContainer'>
            <?php
            $aParams = array();
            parse_str(parse_url($videoYT,PHP_URL_QUERY),$aParams);
            $videoID = $aParams['v'];
                    ?>
                
                       <iframe width="560" height="315" src="//www.youtube.com/embed/<?php echo $videoID; ?>" frameborder="0" allowfullscreen></iframe>
                  
            </div>
            <?php //echo $video['url'];?>
        </div>
    <br style="clear:both;" />
    <?php } ?>
    <?php if($content){ ?>
        <div class="cDivContent">
            <?php echo $content;?>
        </div>
    <?php } ?>
    <?php if($twitter){ ?>
        <div class="cDivTwitter">
            <a href="https://twitter.com/<?php echo $twitter;?>" target="_blank"><?php _e('Follow this person on twitter');?></a>
        </div>
    <?php } ?>
    
    

<?php endwhile; ?>
<?php else: ?>

    <?php get_404_template(); ?>

<?php endif; ?>