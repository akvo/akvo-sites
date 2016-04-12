<div id="jp_container_<?php echo $this->listtoken; ?>" class="jp-video" style="width:<?php echo $this->options['maxwidth']; ?>;max-width:<?php echo $this->options['maxwidth']; ?>">

  <!--container in which our video will be played-->
  <div id="jquery_jplayer_<?php echo $this->listtoken; ?>" class="jp-jplayer"></div>

  <!--main containers for our controls-->
  <div class="jp-gui">
    <div class="jp-interface">
      <div class="jp-controls-holder">

        <!--play and pause buttons-->
        <a href="javascript:;" class="jp-play" tabindex="1">play</a>
        <a href="javascript:;" class="jp-pause" tabindex="1">pause</a>

        <div class="jp-current-time"></div>

        <!--progress bar-->
        <div class="jp-progress">
          <div class="jp-seek-bar">
            <div class="jp-play-bar"><span></span></div>
          </div>
        </div>

        <div class="jp-duration"></div>

        <div class="jp-options">
          <!--mute / unmute toggle-->
          <a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a>
          <a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a>

          <!--volume bar-->
          <div class="jp-volume-bar">
            <div class="jp-volume-bar-value"><span class="handle"></span></div>
          </div>

          <!--full screen toggle-->
          <a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a>
          <a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a>

        </div>
      </div><!--end jp-controls-holder-->
    </div><!--end jp-interface-->
  </div><!--end jp-gui-->

  <div class="jp-playlist-loading">&nbsp;</div>

  <div class="jp-playlist">
    <ul>
      <!-- The method Playlist.displayPlaylist() uses this unordered list -->
      <li></li>
    </ul>
  </div>

  <!--unsupported message-->
  <div class="jp-no-solution">
    <span><?php _e('Update Required', 'outofthebox'); ?></span>
    <?php _e('To play the media you will need to either update your browser to a recent version or update your', 'outofthebox'); ?> <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
  </div>
</div>