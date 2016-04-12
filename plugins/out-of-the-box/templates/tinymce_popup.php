<?php
if (!current_user_can('edit_pages')) {
  die();
}

$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'default';

if (!function_exists('shortcode_exists')) {

  function shortcode_exists($shortcode = false) {
    global $shortcode_tags;

    if (!$shortcode)
      return false;

    if (array_key_exists($shortcode, $shortcode_tags))
      return true;

    return false;
  }

}

function wp_roles_checkbox($name, $selected) {
  global $wp_roles;
  if (!isset($wp_roles)) {
    $wp_roles = new WP_Roles();
  }

  $roles = $wp_roles->get_names();

  $checked = ' checked="checked" ';
  foreach ($roles as $role_value => $role_name) {
    echo '<input class="simple" type="checkbox" name="' . $name . '[]" value="' . $role_value . '" ' . $checked . '>' . $role_name . '&nbsp&nbsp';

    if ($role_value === $selected) {
      $checked = '';
    }
  }
  echo '<input class="simple" type="checkbox" name="' . $name . '[]" value="guest" ' . $checked . '>' . __('Guest', 'outofthebox');
}

wp_register_script('Colorbox', OUTOFTHEBOX_ROOTPATH . '/includes/colorbox/jquery.colorbox-min.js', array('jquery'), filemtime(OUTOFTHEBOX_ROOTDIR . '/includes/colorbox/jquery.colorbox-min.js'));
wp_register_script('collagePlus', OUTOFTHEBOX_ROOTPATH . '/includes/collagePlus/jquery.collagePlus.min.js', array('jquery'), filemtime(OUTOFTHEBOX_ROOTDIR . '/includes/collagePlus/jquery.collagePlus.min.js'));
wp_register_script('removeWhitespace', OUTOFTHEBOX_ROOTPATH . '/includes/collagePlus/extras/jquery.removeWhitespace.min.js', array('jquery'), filemtime(OUTOFTHEBOX_ROOTDIR . '/includes/collagePlus/extras/jquery.removeWhitespace.min.js'));
wp_register_script('Radiobuttons', OUTOFTHEBOX_ROOTPATH . '/includes/jquery-radiobutton/jquery-radiobutton-2.0.js', array('jquery'), filemtime(OUTOFTHEBOX_ROOTDIR . '/includes/jquery-radiobutton/jquery-radiobutton-2.0.js'));
wp_register_script('imagesloaded', OUTOFTHEBOX_ROOTPATH . '/includes/jquery-qTip/imagesloaded.pkgd.min.js', null, false, true);
wp_register_script('qtip', OUTOFTHEBOX_ROOTPATH . '/includes/jquery-qTip/jquery.qtip.min.js', array('jquery', 'imagesloaded'), false, true);
wp_register_script('unveil', OUTOFTHEBOX_ROOTPATH . '/includes/jquery-unveil/jquery.unveil.min.js', array('jquery'), false, true);

wp_register_script('OutoftheBox.tinymce', OUTOFTHEBOX_ROOTPATH . '/includes/OutoftheBox_tinymce_popup.js', array('jquery'), filemtime(OUTOFTHEBOX_ROOTDIR . '/includes/OutoftheBox_tinymce_popup.js'));
wp_register_script('OutoftheBox', OUTOFTHEBOX_ROOTPATH . '/includes/OutoftheBox.js', array('jquery'), filemtime(OUTOFTHEBOX_ROOTDIR . '/includes/OutoftheBox.js'), true);

function OutoftheBox_remove_all_scripts() {
  global $wp_scripts;
  $wp_scripts->queue = array();

  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_script('jquery-ui-widget');
  wp_enqueue_script('jquery-ui-position');
  wp_enqueue_script('jquery-ui-button');
  wp_enqueue_script('jquery');

  wp_enqueue_script('collagePlus');
  wp_enqueue_script('removeWhitespace');
  wp_enqueue_script('imagesloaded');
  wp_enqueue_script('qtip');
  wp_enqueue_script('unveil');

  wp_enqueue_script('Radiobuttons');
  wp_enqueue_script('Colorbox');
  wp_enqueue_script('OutoftheBox.tinymce');
  wp_enqueue_script('OutoftheBox');
}

add_action('wp_print_scripts', 'OutoftheBox_remove_all_scripts', 100);

$post_max_size_bytes = min(OutoftheBox_return_bytes(ini_get('post_max_size')), OutoftheBox_return_bytes(ini_get('upload_max_filesize')));

$localize = array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'plugin_url' => OUTOFTHEBOX_ROOTPATH,
    'js_url' => OUTOFTHEBOX_ROOTPATH . '/includes/jQuery.jPlayer',
    'post_max_size' => $post_max_size_bytes,
    'refresh_nonce' => wp_create_nonce("outofthebox-refresh-folders"),
    'gallery_nonce' => wp_create_nonce("outofthebox-get-gallery"),
    'upload_nonce' => wp_create_nonce("outofthebox-upload-file"),
    'delete_nonce' => wp_create_nonce("outofthebox-delete-entry"),
    'rename_nonce' => wp_create_nonce("outofthebox-rename-entry"),
    'addfolder_nonce' => wp_create_nonce("outofthebox-add-folder"),
    'getplaylist_nonce' => wp_create_nonce("outofthebox-getplaylist"),
    'createzip_nonce' => wp_create_nonce("outofthebox-createzip"),
    'createlink_nonce' => wp_create_nonce("outofthebox-createlink"),
    'str_success' => __('Success', 'outofthebox'),
    'str_error' => __('Error', 'outofthebox'),
    'str_inqueue' => __('In queue', 'outofthebox'),
    'str_uploading' => __('Uploading', 'outofthebox'),
    'str_error_title' => __('Error', 'outofthebox'),
    'str_close_title' => __('Close', 'outofthebox'),
    'str_start_title' => __('Start', 'outofthebox'),
    'str_cancel_title' => __('Cancel', 'outofthebox'),
    'str_delete_title' => __('Delete', 'outofthebox'),
    'str_zip_title' => __('Create zip file', 'outofthebox'),
    'str_delete' => __('Do you really want to delete:', 'outofthebox'),
    'str_rename_title' => __('Rename', 'outofthebox'),
    'str_rename' => __('Rename to:', 'outofthebox'),
    'str_no_filelist' => __("Can't receive filelist", 'outofthebox'),
    'str_addfolder_title' => __('Add folder', 'outofthebox'),
    'str_addfolder' => __('New folder', 'outofthebox'),
    'str_zip_nofiles' => __('No files found or selected', 'outofthebox'),
    'str_zip_createzip' => __('Creating zip file', 'outofthebox'),
    'str_share_link' => __('Share file', 'outofthebox'),
    'str_create_shared_link' => __('Creating shared link...', 'outofthebox'),
    'str_previous_title' => __('Previous', 'outofthebox'),
    'str_next_title' => __('Next', 'outofthebox'),
    'str_xhrError_title' => __('This content failed to load', 'outofthebox'),
    'str_imgError_title' => __('This image failed to load', 'outofthebox'),
    'str_startslideshow' => __('Start slideshow', 'outofthebox'),
    'str_stopslideshow' => __('Stop slideshow', 'outofthebox'),
    'maxNumberOfFiles' => __('Maximum number of files exceeded', 'outofthebox'),
    'acceptFileTypes' => __('File type not allowed', 'outofthebox'),
    'maxFileSize' => __('File is too large', 'outofthebox'),
    'minFileSize' => __('File is too small', 'outofthebox')
);

wp_localize_script('OutoftheBox', 'OutoftheBox_vars', $localize);
?>
<html>
  <head>
    <title><?php
      if ($type === 'default') {
        _e('Create Shortcode', 'outofthebox');
      } else if ($type === 'links') {
        _e('Insert direct links to files or folders', 'outofthebox');
      } else if ($type === 'embedded') {
        _e('Embed files', 'outofthebox');
      }
      ?>
    </title>
    <script type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
    <script type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
    <script type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
    <base target="_self" />
    <?php wp_print_scripts(); ?>
    <link rel='stylesheet' id='OutoftheBox-jquery-css'  href='<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/jquery-ui-1.10.3.custom.css?ver=<?php echo (filemtime(OUTOFTHEBOX_ROOTDIR . "/css/jquery-ui-1.10.3.custom.css")); ?>' type='text/css' media='all' />
    <link rel='stylesheet' id='OutoftheBox-css'  href='<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/outofthebox.css?ver=<?php echo (filemtime(OUTOFTHEBOX_ROOTDIR . "/css/outofthebox.css")); ?>' type='text/css' media='all' />
    <link rel='stylesheet' id='OutoftheBox-tinymce-css'  href='<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/outofthebox_tinymce.css?ver=<?php echo (filemtime(OUTOFTHEBOX_ROOTDIR . "/css/outofthebox_tinymce.css")); ?>' type='text/css' media='all' />
    <link rel='stylesheet' id='Awesome-Font-css'  href='<?php echo OUTOFTHEBOX_ROOTPATH; ?>/includes/font-awesome/css/font-awesome.min.css?ver=<?php echo (filemtime(OUTOFTHEBOX_ROOTDIR . "/includes/font-awesome/css/font-awesome.min.css")); ?>' type='text/css' media='all' />
    <link rel='stylesheet' id='qTip'  href='<?php echo OUTOFTHEBOX_ROOTPATH; ?>/includes/jquery-qTip/jquery.qtip.min.css?ver=<?php echo (filemtime(OUTOFTHEBOX_ROOTDIR . "/includes/jquery-qTip/jquery.qtip.min.css")); ?>' type='text/css' media='all' />
  </head>
  <body class="<?php echo $type; ?>">
    <form id="OutoftheBox_addshortce_form" action="#" class="OutoftheBox">

      <?php if ($type === 'default') { ?>
        <h2><?php _e('Out-of-the-Box should behave as', 'outofthebox'); ?></h2>
        <img class="mode-image" src="<?php echo OUTOFTHEBOX_ROOTPATH; ?>/css/images/mode-files.png" height="100" width="150"/>
        <div class="radiobuttons-container">
          <div class="radiobutton">
            <input type="radio" id="files" name="mode" checked="checked" value="files" class="mode"/><label for="files"><?php _e('Filelist', 'outofthebox'); ?></label>
          </div>
          <div class="radiobutton">
            <input type="radio" id="gallery" name="mode" value="gallery" class="mode"/><label for="gallery"><?php _e('Photogallery', 'outofthebox'); ?></label>
          </div>
          <div class="radiobutton">
            <input type="radio" id="audio" name="mode" value="audio" class="mode"/><label for="audio"><?php _e('Audio player', 'outofthebox'); ?></label>
          </div>
          <div class="radiobutton">
            <input type="radio" id="video" name="mode" value="video" class="mode"/><label for="video"><?php _e('Video player', 'outofthebox'); ?></label>
          </div>
        </div>
        <br/>

        <h2><?php _e('Select root folder', 'outofthebox'); ?></h2>
        <?php
        $mcepopup = 'shortcode';
      } else if ($type === 'links') {
        ?>
        <h2><?php _e('Insert direct links to files or folders', 'outofthebox'); ?></h2>
        <?php
        $mcepopup = 'links';
      } else if ($type === 'embedded') {
        ?>
        <h2><?php _e('Embed files', 'outofthebox'); ?></h2>
        <p><?php _e('Out-of-the-Box uses Google Doc Viewer to embed your files.', 'outofthebox'); ?>&nbsp;
          <?php _e('A list of supported file types can be found', 'outofthebox'); ?>&nbsp;<a href="http://support.google.com/docs/?hl=en&p=docs_viewer" target="_blank"><?php _e('here', 'outofthebox'); ?></a></p>
        <?php
        $mcepopup = 'embedded';
      }
      ?>
      <div class="tinymce">
        <?php
        $atts = array(
            'mode' => 'files',
            'showfiles' => '1',
            'upload' => '0',
            'delete' => '0',
            'rename' => '0',
            'addfolder' => '0',
            'showcolumnnames' => '0',
            'viewrole' => 'administrator|editor|author|contributor',
            'candownloadzip' => '0',
            'showsharelink' => '0',
            'mcepopup' => $mcepopup,
            '_random' => time()
        );

        echo $this->OutoftheBox_CreateTemplate($atts);
        ?></div>
      <?php if ($type === 'default') { ?>
        <p><strong><?php _e('Selected root folder', 'outofthebox'); ?>: </strong><span class="current-folder-raw"></span></p>

        <h2><?php _e('Layout', 'outofthebox'); ?></h2>
        <div class="section">
          <div class="checkbox option forfilebrowser forgallery">
            <input type="checkbox" name="OutoftheBox_breadcrumb" id="OutoftheBox_breadcrumb" checked="checked" data-div-toggle="breadcrumb-options"/>
            <label for="OutoftheBox_breadcrumb"><?php _e('Show folders breadcrumb', 'outofthebox'); ?></label>
          </div>

          <div class="toggle breadcrumb-options option forfilebrowser forgallery">
            <div class="smallinput">
              <div><strong><?php _e('Root breadcrumb title', 'outofthebox'); ?>.</strong><br/><?php echo __('Leave empty for default value', 'outofthebox'); ?> (Start).</div>
              <input type="text" name="OutoftheBox_roottext" id="OutoftheBox_roottext" placeholder="Start"/>
            </div>

            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_rootname" id="OutoftheBox_rootname"/>
              <label for="OutoftheBox_rootname"><?php _e('Show parents of root folder in breadcrumb', 'outofthebox'); ?></label>
            </div>
          </div>

          <div class="checkbox option forfilebrowser forgallery">
            <input type="checkbox" name="OutoftheBox_search" id="OutoftheBox_search" checked="checked"/>
            <label for="OutoftheBox_search"><?php _e('Show searchbox', 'outofthebox'); ?></label>
          </div>

          <div class="checkbox option forfilebrowser">
            <input type="checkbox" name="OutoftheBox_showcolumnnames" id="OutoftheBox_showcolumnnames" checked="checked" />
            <label for="OutoftheBox_showcolumnnames"><?php _e('Show columnnames', 'outofthebox'); ?></label>
          </div>

          <div class="checkbox option forfilebrowser forgallery">
            <input type="checkbox" name="OutoftheBox_candownloadzip" id="OutoftheBox_candownloadzip"/>
            <label for="OutoftheBox_candownloadzip"><?php _e("Show download button (zip-archive)", 'outofthebox'); ?></label>
          </div>

          <div class="checkbox option forfilebrowser forgallery">
            <input type="checkbox" name="OutoftheBox_showsharelink" id="OutoftheBox_showsharelink"/>
            <label for="OutoftheBox_showsharelink"><?php _e("Show share button", 'outofthebox'); ?></label>
          </div>

          <div class="size-options option forfilebrowser forgallery foraudio forvideo">
            <div class="smallinput"><div><strong><?php _e("Set max width for the Out-of-the-Box container", "outofthebox"); ?>. </strong><br/>
                <?php _e("You can use pixels or percentages, eg '360px', '480px', '70%'", "outofthebox"); ?>. <?php echo __('Leave empty for default value', 'outofthebox'); ?> (100%).
              </div>
              <input type="text" name="OutoftheBox_max_width" id="OutoftheBox_max_width" placeholder="100%"/>
            </div>
          </div>
        </div>

        <div class="option foraudio forvideo" id="OutoftheBox_mediaextension_div">
          <h2><?php _e('Media files', 'outofthebox'); ?></h2>
          <div class="section">
            <p><strong><?php _e('Select which sort of media files you will provide', 'outofthebox'); ?>.</strong></p>
            <div class="option foraudio">
              <p><u><?php _e('Do always supply a mp3 file to support all browsers', 'outofthebox'); ?></u></p>
              <div class="checkbox">
                <input class="simple" type="checkbox" name="OutoftheBox_mediaextensions[]" value='mp3'/>mp3&nbsp&nbsp
                <input class="simple" type="checkbox" name="OutoftheBox_mediaextensions[]" value='mp4'/>mp4&nbsp&nbsp
                <input class="simple" type="checkbox" name="OutoftheBox_mediaextensions[]" value='m4a'/>m4a&nbsp&nbsp
                <input class="simple" type="checkbox" name="OutoftheBox_mediaextensions[]" value='ogg'/>ogg&nbsp&nbsp
                <input class="simple" type="checkbox" name="OutoftheBox_mediaextensions[]" value='oga'/>oga&nbsp&nbsp
              </div>
            </div>
            <div class="option forvideo">
              <p><u><?php _e('Do always supply a m4v/mp4 file to support all browsers', 'outofthebox'); ?></u></p>
              <div class="checkbox">
                <input class="simple" type="checkbox" name="OutoftheBox_mediaextensions[]" value='mp4'/>mp4&nbsp&nbsp
                <input class="simple" type="checkbox" name="OutoftheBox_mediaextensions[]" value='m4v'/>m4v&nbsp&nbsp
                <input class="simple" type="checkbox" name="OutoftheBox_mediaextensions[]" value='ogg'/>ogg&nbsp&nbsp
                <input class="simple" type="checkbox" name="OutoftheBox_mediaextensions[]" value='ogv'/>ogv&nbsp&nbsp
                <input class="simple" type="checkbox" name="OutoftheBox_mediaextensions[]" value='webmv'/>webmv&nbsp&nbsp
              </div>
            </div>
            <p><?php _e('The mediaplayer will decided, based on the provided formats, if the user will have a HTML5 player or a Flash Player', 'outofthebox'); ?>. <?php _e('You may provide the same file with different extensions to increase cross-browser support', 'outofthebox'); ?></p>
          </div>
        </div>

        <h2><?php _e('Sorting', 'outofthebox'); ?></h2>
        <div class="section">
          <div class="sorting option forfilebrowser forgallery foraudio forvideo">
            <div class="radiobuttons-container sort_fields">
              <strong><?php _e('Sort files and folders by:', 'outofthebox'); ?></strong>
              <div class="radiobutton">
                <input type="radio" id="name" name="sort_field" checked="checked" value="name" class="mode"/><label for="name"><?php _e('Name', 'outofthebox'); ?></label>
              </div>
              <div class="radiobutton">
                <input type="radio" id="size" name="sort_field" value="size" class="mode"/><label for="size"><?php _e('Size', 'outofthebox'); ?></label>
              </div>
              <div class="radiobutton">
                <input type="radio" id="modified" name="sort_field" value="modified" class="mode"/><label for="modified"><?php _e('Date modified', 'outofthebox'); ?></label>
              </div>
            </div>

            <div class="radiobuttons-container">
              <strong><?php _e('Sort order:', 'outofthebox'); ?></strong>
              <div class="radiobutton">
                <input type="radio" id="asc" name="sort_order" checked="checked" value="asc" class="mode"/><label for="files"><?php _e('Ascending', 'outofthebox'); ?></label>
              </div>
              <div class="radiobutton">
                <input type="radio" id="desc" name="sort_order" value="desc" class="mode"/><label for="gallery"><?php _e('Descending', 'outofthebox'); ?></label>
              </div>
            </div>
          </div>
        </div>

        <div class="option forfilebrowser">
          <h2><?php _e('Filelist options', 'outofthebox'); ?></h2>
          <div class="section">

            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_showfiles" id="OutoftheBox_showfiles" checked="checked"/>
              <label for="OutoftheBox_showfiles"><?php _e('Show files in folder', 'outofthebox'); ?></label>
            </div>
            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_filesize" id="OutoftheBox_filesize" checked="checked" />
              <label for="OutoftheBox_filesize"><?php _e('Show filesize', 'outofthebox'); ?></label>
            </div>
            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_filedate" id="OutoftheBox_filedate" checked="checked"/>
              <label for="OutoftheBox_filedate"><?php _e('Show date last modified', 'outofthebox'); ?></label>
            </div>
            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_showext" id="OutoftheBox_showext" checked="checked"/>
              <label for="OutoftheBox_showext"><?php _e('Show file extensions', 'outofthebox'); ?></label>
            </div>
            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_forcedownload" id="OutoftheBox_forcedownload"/>
              <label for="OutoftheBox_forcedownload"><?php _e("Force a 'Save as' Dialog on downloading file", 'outofthebox'); ?></label>
            </div>
          </div>
        </div>

        <div class="option forgallery">
          <h2><?php _e('Gallery options', 'outofthebox'); ?></h2>
          <div class="section">
            <div class="smallinput">
              <div><strong><?php _e("Number of images to be loaded each time", 'outofthebox'); ?>.</strong><br/><?php _e("Set to 0 to load all images at once", 'outofthebox'); ?>. <?php echo __('Leave empty for default value', 'outofthebox'); ?> (25).</div>
              <input type="text" name="OutoftheBox_maximage" id="OutoftheBox_maximage" placeholder="25"/>
            </div>

            <div class="smallinput">
              <div><strong><?php _e("Row height", 'outofthebox'); ?>.</strong><br/><?php _e("The ideal height you want your grid rows to be", 'outofthebox'); ?>. <?php _e("It won't set it exactly to this as plugin adjusts the row height to get the correct width", 'outofthebox'); ?>. <?php echo __('Leave empty for default value', 'outofthebox'); ?> (150).</div>
              <input type="text" name="OutoftheBox_targetHeight" id="OutoftheBox_targetHeight" placeholder="150"/>
            </div>

            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_allowPartialLastRow" id="OutoftheBox_allowPartialLastRow" checked="checked"/>
              <label for="OutoftheBox_allowPartialLastRow"><?php _e('Don\'t stretch last row to fit grid width', 'outofthebox'); ?></label>
            </div>

            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_crop" id="OutoftheBox_crop"/>
              <label for="OutoftheBox_crop"><?php _e('Crop images for a squared grid', 'outofthebox'); ?></label>
            </div>

            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_shuffle" id="OutoftheBox_shuffle"/>
              <label for="OutoftheBox_shuffle"><?php _e('Shuffle images', 'outofthebox'); ?></label>
            </div>
          </div>
        </div>

        <h2><?php _e('Exclusions', 'outofthebox'); ?></h2>
        <div class="section">
          <div class="largeinput">
            <div><strong><?php _e('Limit files by extension', 'outofthebox'); ?></strong><br/>
              <?php echo __('Add extensions separated with | e.g. (jpg|png|gif)', 'outofthebox') . '. ' . __('Leave empty to show all files', 'outofthebox', 'outofthebox'); ?></div>
            <input type="text" name="OutoftheBox_ext" id="OutoftheBox_ext"/>
          </div>

          <div class="largeinput">
            <div><strong><?php _e('Include files or folders (only show these items)', 'outofthebox'); ?></strong></br>
              <?php echo __('Add files or folders separated with | e.g. (file1.jpg|long folder name)', 'outofthebox'); ?></div>
            <input type="text" name="OutoftheBox_include" id="OutoftheBox_include"/>
          </div>

          <div class="largeinput">
            <div><strong><?php _e('Exclude files or folders', 'outofthebox'); ?></strong></br>
              <?php echo __('Add files or folders separated with | e.g. (file1.jpg|long folder name)', 'outofthebox'); ?></div>
            <input type="text" name="OutoftheBox_exclude" id="OutoftheBox_exclude"/>
          </div>
        </div>

        <div class="forfilebrowser forgallery">
          <h2><?php _e('Upload form options', 'outofthebox'); ?></h2>
          <div class="section">
            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_upload" id="OutoftheBox_upload" data-div-toggle="upload-options"/>
              <label for="OutoftheBox_upload"><?php _e('Include upload form', 'outofthebox'); ?></label>
            </div>

            <div class="toggle upload-options" style="display:none;">
              <div class="checkbox">
                <input type="checkbox" name="OutoftheBox_overwrite" id="OutoftheBox_overwrite"/>
                <label for="OutoftheBox_overwrite"><?php _e('Overwrite existing files', 'outofthebox'); ?></label>
              </div>

              <div class="largeinput">
                <div><strong><?php _e('Limit upload by extension', 'outofthebox'); ?>. </strong><br/>
                  <?php echo __('Add extensions separated with | e.g. (jpg|png|gif)', 'outofthebox') . ' ' . __('Leave empty for no restricion', 'outofthebox', 'outofthebox'); ?>
                </div>
                <input type="text" name="OutoftheBox_upload_ext" id="OutoftheBox_upload_ext" />
              </div>

              <div class="smallinput">
                <?php $max_size_bytes = min(OutoftheBox_return_bytes(ini_get('post_max_size')), OutoftheBox_return_bytes(ini_get('upload_max_filesize'))); ?>
                <div><strong><?php _e('Max filesize for uploading in bytes', 'outofthebox'); ?>. </strong><br/>
                  <?php echo __('Leave empty for server maximum ', 'outofthebox'); ?> (<?php echo $max_size_bytes; ?> bytes). <a href="http://www.google.nl/#q=1mb+in+bytes" target="_blank"><?php echo __('How to calculate?', 'outofthebox'); ?></a>
                </div>
                <input type="text" name="OutoftheBox_maxfilesize" id="OutoftheBox_maxfilesize"/>

              </div>
            </div>
          </div>
        </div>

        <div class="option forfilebrowser forgallery">
          <h2><?php _e('Email notifications', 'outofthebox'); ?></h2>
          <div class="section">
            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_notificationdownload" id="OutoftheBox_notificationdownload"/>
              <label for="OutoftheBox_notificationdownload"><?php _e('Would you like to receive a notification email when someone downloads a file?', 'outofthebox'); ?></label>
            </div>

            <div class="upload-options" style="display:none;">
              <div class="checkbox">
                <input type="checkbox" name="OutoftheBox_notificationupload" id="OutoftheBox_notificationupload"/>
                <label for="OutoftheBox_notificationupload"><?php _e('Would you like to receive a notification email when someone uploads a file?', 'outofthebox'); ?></label>
              </div>
            </div>

            <div class="largeinput">
              <div><strong><?php _e('On which email address would you like to receive the notification?', 'outofthebox'); ?> </strong><br/>
                <?php echo __('Default value is:', 'outofthebox') . ' %admin_email%'; ?>
              </div>
              <input type="text" name="OutoftheBox_notification_email" id="OutoftheBox_notification_email" value="%admin_email%" />
            </div>

          </div>
        </div>

        <div class="sorting option forfilebrowser forgallery">
          <h2><?php _e('Edit files &amp; folders', 'outofthebox'); ?></h2>
          <div class="section">
            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_rename" id="OutoftheBox_rename"  data-div-toggle="rename-options"/>
              <label for="OutoftheBox_rename"><?php _e('Files &amp; folders can be renamed', 'outofthebox'); ?></label>
            </div>

            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_delete" id="OutoftheBox_delete"  data-div-toggle="delete-options"/>
              <label for="OutoftheBox_delete"><?php _e('Files &amp; folders can be deleted', 'outofthebox'); ?></label>
            </div>

            <div class="checkbox">
              <input type="checkbox" name="OutoftheBox_addfolder" id="OutoftheBox_addfolder" data-div-toggle="addfolder-options"/>
              <label for="OutoftheBox_addfolder"><?php _e('Can add new folders', 'outofthebox'); ?></label>
            </div>
          </div>
        </div>

        <div class="usermanagment">
          <h2><?php _e('User management', 'outofthebox'); ?></h2>
          <div class="section">
            <p><?php echo __('Out-of-the-Box uses Wordpress Roles to determine how an user can use the plugin', 'outofthebox'); ?>.</p>

            <div><strong><?php _e('View Out-of-the-Box', 'outofthebox'); ?></strong><br/>
              <p><?php wp_roles_checkbox('OutoftheBox_view_role', 'guest'); ?></p>
            </div>

            <div class="option forfilebrowser">
              <div><strong><?php _e('Download files', 'outofthebox'); ?></strong><br/>
                <p><?php wp_roles_checkbox('OutoftheBox_download_role', 'guest'); ?></p>
              </div>
            </div>

            <div class="forfilebrowser forgallery">
              <div class="upload-options" style="display:none;">
                <div><strong><?php _e('Upload files', 'outofthebox'); ?></strong><br/>
                  <p><?php wp_roles_checkbox('OutoftheBox_upload_role', 'subscriber'); ?></p>
                </div>
              </div>


              <div class="rename-options" style="display:none;">
                <div><strong><?php _e('Rename files', 'outofthebox'); ?></strong><br/>
                  <p><?php wp_roles_checkbox('OutoftheBox_renamefiles_role', 'editor'); ?></p>
                </div>
              </div>

              <div class="rename-options" style="display:none;">
                <div><strong><?php _e('Rename folders', 'outofthebox'); ?></strong><br/>
                  <p><?php wp_roles_checkbox('OutoftheBox_renamefolders_role', 'editor'); ?></p>
                </div>
              </div>

              <div class="delete-options" style="display:none;">
                <div><strong><?php _e('Delete files', 'outofthebox'); ?></strong><br/>
                  <p><?php wp_roles_checkbox('OutoftheBox_deletefiles_role', 'editor'); ?></p>
                </div>
              </div>

              <div class="delete-options" style="display:none;">
                <div><strong><?php _e('Delete folders', 'outofthebox'); ?></strong><br/>
                  <p><?php wp_roles_checkbox('OutoftheBox_deletefolders_role', 'editor'); ?></p>
                </div>
              </div>

              <div class="addfolder-options" style="display:none;">
                <div><strong><?php _e('Add folders', 'outofthebox'); ?></strong><br/>
                  <p><?php wp_roles_checkbox('OutoftheBox_addfolder_role', 'editor'); ?></p>
                </div>
              </div>


              <div class="upload-options" style="display:none;">
                <br/>
                <div class="checkbox">
                  <input type="checkbox" name="OutoftheBox_user_folders" id="OutoftheBox_user_folders" data-div-toggle="userfolder-options"/>
                  <label for="OutoftheBox_user_folders"><?php _e('Users can only use their own folder within the selected rootfolder', 'outofthebox'); ?></label>
                </div>

                <div class="toggle userfolder-options" style="display:none;">
                  <div><strong><?php _e('Note', 'outofthebox'); ?>:</strong> <?php echo __('By default guests (not logged in users) will also get their own folder', 'outofthebox'); ?>. <?php echo __("Remove 'Guest' from View Roles to disable guests to use the plugin", 'outofthebox'); ?>.</div>
                  <br/>
                  <div><strong><?php _e('See and edit all user folders', 'outofthebox'); ?></strong><br/>
                    <p><?php wp_roles_checkbox('OutoftheBox_view_user_folders_role', 'administrator'); ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php } ?>
      <div class="footer">
        <div style="float: left">
          <input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'outofthebox'); ?>" onclick="tinyMCEPopup.close();" />
        </div>
        <div style="float: right">
          <?php if ($type === 'default') { ?>
            <input type="submit" id="insert"  class="insert_shortcode" name="insert" value="<?php _e("Insert", 'outofthebox'); ?>" />
          <?php } elseif ($type === 'links') { ?>
            <input type="submit" id="insert" class="insert_links" name="insert" value="<?php _e("Insert links", 'outofthebox'); ?>" />
          <?php } elseif ($type === 'embedded') { ?>
            <input type="submit" id="insert" class="insert_embedded" name="insert" value="<?php _e("Embed", 'outofthebox'); ?>" />
          <?php } ?>
        </div>
      </div>
    </form>
  </body>
</html>