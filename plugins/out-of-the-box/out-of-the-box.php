<?php

/*
  Plugin Name: Out-of-the-Box
  Plugin URI: http://www.florisdeleeuw.nl/wordpress-demo/
  Description: Integrates your Dropbox in WordPress
  Version: 1.5
  Author: F. de Leeuw
  Author URI:
  Text Domain: outofthebox
 */

/* * ***********SYSTEM SETTINGS****************** */
define('OUTOFTHEBOX_VERSION', '1.5');
define('OUTOFTHEBOX_ROOTPATH', plugins_url('', __FILE__));
define('OUTOFTHEBOX_ROOTDIR', __DIR__);
define('OUTOFTHEBOX_CACHEDIR', __DIR__ . '/cache/');
define('OUTOFTHEBOX_CACHEURL', OUTOFTHEBOX_ROOTPATH . '/cache/');

if (!class_exists('OutoftheBox')) {

  class OutoftheBox {

    /**
     * Construct the plugin object
     */
    public function __construct() {

      add_action('init', array(&$this, 'OutoftheBox_Init'));

      if (is_admin() && !defined('DOING_AJAX')) {
        require_once(sprintf("%s/admin_page.php", dirname(__FILE__)));
        $OutoftheBox_settings = new OutoftheBox_settings($this);
      }

      add_action('wp_head', array(&$this, 'OutoftheBox_LoadIEstyles'));

      add_action('wp_enqueue_scripts', array(&$this, 'OutoftheBox_LoadScripts'));
      add_action('wp_enqueue_scripts', array(&$this, 'OutoftheBox_LoadStyles'));

      /* Shortcodes */
      add_shortcode('outofthebox', array(&$this, 'OutoftheBox_CreateTemplate'));

      /* Add user folder if needed */
      $this->advancedsettings = get_option('out_of_the_box_advancedsettings');
      $advancedsettings = $this->advancedsettings;

      if (isset($advancedsettings['userfolder_oncreation']) && $advancedsettings['userfolder_oncreation'] === 'Yes') {
        add_action('user_register', array(&$this, 'OutoftheBox_UpdateUserfolder'));
      }
      if (isset($advancedsettings['userfolder_update']) && $advancedsettings['userfolder_update'] === 'Yes') {
        add_action('profile_update', array(&$this, 'OutoftheBox_UpdateUserfolder'), 100, 2);
      }
      if (isset($advancedsettings['userfolder_remove']) && $advancedsettings['userfolder_remove'] === 'Yes') {
        add_action('delete_user', array(&$this, 'OutoftheBox_DeleteUserfolder'));
      }

      add_action('wp_head', array(&$this, 'OutoftheBox_CustomCss'), 100);

      /* Ajax calls */
      add_action('wp_ajax_nopriv_outofthebox-getfilelist', array(&$this, 'OutoftheBox_StartProcess'));
      add_action('wp_ajax_outofthebox-getfilelist', array(&$this, 'OutoftheBox_StartProcess'));

      add_action('wp_ajax_nopriv_outofthebox-search', array(&$this, 'OutoftheBox_StartProcess'));
      add_action('wp_ajax_outofthebox-search', array(&$this, 'OutoftheBox_StartProcess'));

      add_action('wp_ajax_nopriv_outofthebox-getgallery', array(&$this, 'OutoftheBox_StartProcess'));
      add_action('wp_ajax_outofthebox-getgallery', array(&$this, 'OutoftheBox_StartProcess'));

      add_action('wp_ajax_nopriv_outofthebox-uploadfile', array(&$this, 'OutoftheBox_StartProcess'));
      add_action('wp_ajax_outofthebox-uploadfile', array(&$this, 'OutoftheBox_StartProcess'));

      add_action('wp_ajax_nopriv_outofthebox-deleteentry', array(&$this, 'OutoftheBox_StartProcess'));
      add_action('wp_ajax_outofthebox-deleteentry', array(&$this, 'OutoftheBox_StartProcess'));

      add_action('wp_ajax_nopriv_outofthebox-renameentry', array(&$this, 'OutoftheBox_StartProcess'));
      add_action('wp_ajax_outofthebox-renameentry', array(&$this, 'OutoftheBox_StartProcess'));

      add_action('wp_ajax_nopriv_outofthebox-addfolder', array(&$this, 'OutoftheBox_StartProcess'));
      add_action('wp_ajax_outofthebox-addfolder', array(&$this, 'OutoftheBox_StartProcess'));

      add_action('wp_ajax_nopriv_outofthebox-getplaylist', array(&$this, 'OutoftheBox_StartProcess'));
      add_action('wp_ajax_outofthebox-getplaylist', array(&$this, 'OutoftheBox_StartProcess'));

      add_action('wp_ajax_nopriv_outofthebox-createzip', array(&$this, 'OutoftheBox_StartProcess'));
      add_action('wp_ajax_outofthebox-createzip', array(&$this, 'OutoftheBox_StartProcess'));

      add_action('wp_ajax_nopriv_outofthebox-thumbnail', array(&$this, 'OutoftheBox_GenerateThumbnail'));
      add_action('wp_ajax_outofthebox-thumbnail', array(&$this, 'OutoftheBox_GenerateThumbnail'));

      add_action('wp_ajax_nopriv_outofthebox-createlink', array(&$this, 'OutoftheBox_StartProcess'));
      add_action('wp_ajax_outofthebox-createlink', array(&$this, 'OutoftheBox_StartProcess'));

      add_action('wp_ajax_nopriv_outofthebox-embedded', array(&$this, 'OutoftheBox_StartProcess'));
      add_action('wp_ajax_outofthebox-embedded', array(&$this, 'OutoftheBox_StartProcess'));

      add_action('wp_ajax_outofthebox-revoke', array(&$this, 'OutoftheBox_StartProcess'));

      add_action('wp_ajax_outofthebox-getpopup', array(&$this, 'OutoftheBox_GetPopup'));

      /* add settings link on plugin page */
      add_filter('plugin_row_meta', array(&$this, 'OutoftheBox_AddSettingsLink'), 10, 2);

      if ((isset($_GET['action'])) && ($_GET['action'] === 'outofthebox-download')) {
        $this->OutoftheBox_StartProcess();
      }

      /* Updater */
      $settings = get_option('out_of_the_box_settings');
      if (isset($settings['purcasecode'])) {
        require_once('wp-updates-plugin.php');
        new WPUpdatesPluginUpdater_338('http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__), $settings['purcasecode']);
      }
    }

    public function OutoftheBox_Init() {
      /* Localize */
      $i18n_dir = dirname(plugin_basename(__FILE__)) . '/languages/';
      load_plugin_textdomain('outofthebox', false, $i18n_dir);
    }

    public function OutoftheBox_AddSettingsLink($links, $file) {
      $plugin = plugin_basename(__FILE__);

      /* create link */
      if ($file == $plugin && !is_network_admin()) {
        return array_merge(
                $links, array(sprintf('<a href="options-general.php?page=%s">%s</a>', 'OutoftheBox_settings', __('Settings', 'outofthebox')))
        );
      }

      return $links;
    }

    public function OutoftheBox_LoadScripts() {
      wp_register_script('jQuery.jplayer', plugins_url('includes/jQuery.jPlayer/jquery.jplayer.min.js', __FILE__), array('jquery'));
      wp_register_script('jQuery.jplayer.playlist', plugins_url('includes/jQuery.jPlayer/add-on/jplayer.playlist.min.js', __FILE__), array('jquery'));

      wp_register_script('Colorbox', plugins_url('includes/colorbox/jquery.colorbox-min.js', __FILE__), array('jquery'), filemtime(plugin_dir_path(__FILE__) . 'includes/colorbox/jquery.colorbox-min.js'));
      wp_register_script('collagePlus', plugins_url('includes/collagePlus/jquery.collagePlus.min.js', __FILE__), array('jquery'), filemtime(plugin_dir_path(__FILE__) . 'includes/collagePlus/jquery.collagePlus.min.js'));
      wp_register_script('removeWhitespace', plugins_url('includes/collagePlus/extras/jquery.removeWhitespace.min.js', __FILE__), array('jquery'), filemtime(plugin_dir_path(__FILE__) . 'includes/collagePlus/extras/jquery.removeWhitespace.min.js'));
      wp_register_script('imagesloaded', plugins_url('includes/jquery-qTip/imagesloaded.pkgd.min.js', __FILE__), null, false, true);
      wp_register_script('qtip', plugins_url('includes/jquery-qTip/jquery.qtip.min.js', __FILE__), array('jquery', 'imagesloaded'), false, true);
      wp_register_script('unveil', plugins_url('includes/jquery-unveil/jquery.unveil.min.js', __FILE__), array('jquery'), false, true);

      /* load in footer */
      wp_register_script('jQuery.iframe-transport', plugins_url('includes/jquery-file-upload/js/jquery.iframe-transport.js', __FILE__), array('jquery'), false, true);
      wp_register_script('jQuery.fileupload', plugins_url('includes/jquery-file-upload/js/jquery.fileupload.js', __FILE__), array('jquery'), false, true);
      wp_register_script('jQuery.fileupload-process', plugins_url('includes/jquery-file-upload/js/jquery.fileupload-process.js', __FILE__), array('jquery'), false, true);
      wp_register_script('jQuery.fileupload-validate', plugins_url('includes/jquery-file-upload/js/jquery.fileupload-validate.js', __FILE__), array('jquery'), false, true);
      wp_register_script('jQuery.fileupload-ui', plugins_url('includes/jquery-file-upload/js/jquery.fileupload-ui.js', __FILE__), array('jquery'), false, true);
      wp_register_script('jQuery.fileupload-jquery-ui', plugins_url('includes/jquery-file-upload/js/jquery.fileupload-jquery-ui.js', __FILE__), array('jquery'), false, true);

      wp_register_script('OutoftheBox', plugins_url('includes/OutoftheBox.js', __FILE__), array('jquery'), filemtime(plugin_dir_path(__FILE__) . 'includes/OutoftheBox.js'), true);

      wp_enqueue_script('json2');
      wp_enqueue_script('jquery');
      wp_enqueue_script('jquery-ui-core');
      wp_enqueue_script('jquery-ui-droppable');
      wp_enqueue_script('jquery-ui-mouse');
      wp_enqueue_script('jquery-ui-widget');
      wp_enqueue_script('jquery-ui-button');
      wp_enqueue_script('jquery-ui-position');
      wp_enqueue_script('jquery-ui-progressbar');
      wp_enqueue_script('jquery-ui-autocomplete');

      wp_enqueue_script('jquery-ui-dialog');
      wp_enqueue_script('jquery-effects-fade');

      wp_enqueue_script('unveil');
      wp_enqueue_script('collagePlus');
      wp_enqueue_script('removeWhitespace');
      wp_enqueue_script('Colorbox');
      wp_enqueue_script('imagesloaded');
      wp_enqueue_script('qtip');

      wp_enqueue_script('jQuery.jplayer');
      wp_enqueue_script('jQuery.jplayer.playlist');

      wp_enqueue_script('jQuery.iframe-transport');
      wp_enqueue_script('jQuery.fileupload');
      wp_enqueue_script('jQuery.fileupload-process');
      wp_enqueue_script('jQuery.fileupload-validate');
      wp_enqueue_script('jQuery.fileupload-ui');
      wp_enqueue_script('jQuery.fileupload-jquery-ui');
      wp_enqueue_script('OutoftheBox');

      $post_max_size_bytes = min(OutoftheBox_return_bytes(ini_get('post_max_size')), OutoftheBox_return_bytes(ini_get('upload_max_filesize')));

      $localize = array(
          'plugin_url' => plugins_url('', __FILE__),
          'ajax_url' => admin_url('admin-ajax.php'),
          'js_url' => plugins_url('includes/jQuery.jPlayer/Jplayer.swf', __FILE__),
          'checkpdf_url' => plugins_url('includes/PluginDetect/empty.pdf', __FILE__),
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
    }

    public function OutoftheBox_LoadStyles() {
      /* First looks in theme/template directories for the stylesheet, falling back to plugin directory */
      $cssfile = 'outofthebox.css';
      if (file_exists(get_stylesheet_directory() . '/' . $cssfile)) {
        $stylesheet = get_stylesheet_directory_uri() . '/' . $cssfile;
      } elseif (file_exists(get_template_directory() . '/' . $cssfile)) {
        $stylesheet = get_template_directory_uri() . '/' . $cssfile;
      } else {
        $stylesheet = plugins_url('css/' . $cssfile, __FILE__);
      }

      wp_register_style('Fileupload-jquery-ui', plugins_url('includes/jquery-file-upload/css', __FILE__) . '/jquery.fileupload-ui.css');
      wp_enqueue_style('Fileupload-jquery-ui');

      wp_enqueue_style('qtip', plugins_url('includes/jquery-qTip/jquery.qtip.min.css', __FILE__), null, false, false);
      wp_enqueue_style('qtip');

      wp_register_style('Colorbox', plugins_url('includes/colorbox/colorbox.css', __FILE__), false, (filemtime(__DIR__ . "/includes/colorbox/colorbox.css")));
      wp_enqueue_style('Colorbox');

      wp_register_style('OutoftheBox-css', plugins_url('css/skin/simple.mediabox/simple.mediabox.css', __FILE__), false, (filemtime(__DIR__ . "/css/skin/simple.mediabox/simple.mediabox.css")));
      wp_enqueue_style('OutoftheBox-css');

      wp_register_style('OutoftheBox-dialogs', plugins_url('css', __FILE__) . '/jquery-ui-1.10.3.custom.css');
      wp_enqueue_style('OutoftheBox-dialogs');

      wp_register_style('Awesome-Font-css', plugins_url('includes/font-awesome/css/font-awesome.min.css', __FILE__), false, (filemtime(__DIR__ . "/includes/font-awesome/css/font-awesome.min.css")));
      wp_enqueue_style('Awesome-Font-css');

      wp_register_style('OutoftheBox', $stylesheet, false, filemtime(__FILE__));
      wp_enqueue_style('OutoftheBox');
    }

    public function OutoftheBox_LoadIEstyles() {

      echo "<!--[if IE]>\n";
      echo "<link rel='stylesheet' type='text/css' href='" . plugins_url('css/skin/simple.mediabox/simple.mediabox-ie.css', __FILE__) . "' />\n";
      echo "<![endif]-->\n";
    }

    public function OutoftheBox_StartProcess() {
      if (isset($_REQUEST['action'])) {
        switch ($_REQUEST['action']) {
          case 'outofthebox-getfilelist':
            require_once 'includes/OutoftheBox_Filebrowser.php';
            $processor = new OutoftheBox_Filebrowser;
            $processor->startProcess();
            break;
          case 'outofthebox-download':
          case 'outofthebox-createzip':
          case 'outofthebox-createlink':
          case 'outofthebox-embedded':
          case 'outofthebox-revoke':
            require_once(ABSPATH . 'wp-includes/pluggable.php');
            require_once 'includes/OutoftheBox_Dropbox.php';
            $processor = new OutoftheBox_Dropbox;
            $processor->startProcess();
            break;

          case 'outofthebox-getgallery':
            require_once 'includes/OutoftheBox_Gallery.php';
            $processor = new OutoftheBox_Gallery;
            $processor->startProcess();
            break;

          case 'outofthebox-uploadfile':
          case 'outofthebox-deleteentry':
          case 'outofthebox-renameentry':
          case 'outofthebox-addfolder':
            require_once 'includes/OutoftheBox_Dropbox.php';
            $processor = new OutoftheBox_Dropbox;
            $processor->startProcess();
            break;



          case 'outofthebox-getplaylist':
            require_once 'includes/OutoftheBox_Mediaplayer.php';
            $processor = new OutoftheBox_Mediaplayer;
            $processor->startProcess();
            break;
        }
      }
    }

    public function OutoftheBox_CustomCss() {
      if (!empty($this->advancedsettings['custom_css'])) {
        echo '<!-- Custom OutoftheBox CSS Styles -->' . "\n";
        echo '<style type="text/css" media="screen">' . "\n";
        echo $this->advancedsettings['custom_css'] . "\n";
        echo '</style>' . "\n";
      }
    }

    public function OutoftheBox_CreateTemplate($atts = array()) {
      require_once 'includes/OutoftheBox_Dropbox.php';
      $processor = new OutoftheBox_Dropbox;
      return $processor->createFromShortcode($atts);
    }

    public function OutoftheBox_GenerateThumbnail() {
      require_once 'includes/OutoftheBox_Dropbox.php';
      $processor = new OutoftheBox_Dropbox;
      return $processor->createThumb();
    }

    public function OutoftheBox_GetPopup() {
      include OUTOFTHEBOX_ROOTDIR . '/templates/tinymce_popup.php';
      die();
    }

    public function OutoftheBox_UpdateUserfolder($user_id, $old_user_data = false) {
      $outoftheboxlists = get_option('out_of_the_box_lists', array());
      $updatelists = array();

      foreach ($outoftheboxlists as $list) {

        if (isset($list['user_upload_folders']) && $list['user_upload_folders'] === '1') {
          $updatelists[] = $list['root'];
        }
      }


      if (count($updatelists) > 0) {
        require_once 'includes/OutoftheBox_Dropbox.php';
        $processor = new OutoftheBox_Dropbox;

        foreach ($updatelists as $rootfolder) {

          $oldfoldername = false;
          if ($old_user_data !== false) {
            $oldfoldername = strtr($processor->advancedsetting['userfolder_name'], array(
                "%user_login%" => $old_user_data->user_login,
                "%user_email%" => $old_user_data->user_email,
                "%display_name%" => $old_user_data->display_name,
                "%ID%" => $old_user_data->ID
            ));
          }

          $new_user = get_user_by('id', $user_id);

          $userfoldername = strtr($processor->advancedsetting['userfolder_name'], array(
              "%user_login%" => $new_user->user_login,
              "%user_email%" => $new_user->user_email,
              "%display_name%" => $new_user->display_name,
              "%ID%" => $new_user->ID
          ));


          if ($oldfoldername === false || ($oldfoldername !== $userfoldername)) {
            $processor->userChangeFolder($rootfolder, $userfoldername, $oldfoldername, false);
          }
        }
      }
    }

    public function OutoftheBox_DeleteUserfolder($user_id) {
      $outoftheboxlists = get_option('out_of_the_box_lists', array());
      $updatelists = array();

      foreach ($outoftheboxlists as $list) {

        if (isset($list['user_upload_folders']) && $list['user_upload_folders'] === '1') {
          $updatelists[] = $list['root'];
        }
      }


      if (count($updatelists) > 0) {
        require_once 'includes/OutoftheBox_Dropbox.php';
        $processor = new OutoftheBox_Dropbox;

        foreach ($updatelists as $rootfolder) {

          $deleted_user = get_user_by('id', $user_id);

          $userfoldername = strtr($processor->advancedsetting['userfolder_name'], array(
              "%user_login%" => $deleted_user->user_login,
              "%user_email%" => $deleted_user->user_email,
              "%display_name%" => $deleted_user->display_name,
              "%ID%" => $deleted_user->ID
          ));

          $processor->userChangeFolder($rootfolder, $userfoldername, false, true);
        }
      }
    }

  }

}

if (class_exists('OutoftheBox')) {
  /* Installation and uninstallation hooks */
  register_activation_hook(__FILE__, 'OutoftheBox_Network_Activate');
  register_deactivation_hook(__FILE__, 'OutoftheBox_Network_Deactivate');

  $OutoftheBox = new OutoftheBox();
}

/* Activation & Deactivation */

/**
 * Activate the plugin on network
 */
function OutoftheBox_Network_Activate($network_wide) {
  if (is_multisite() && $network_wide) { // See if being activated on the entire network or one blog
    global $wpdb;

    // Get this so we can switch back to it later
    $current_blog = $wpdb->blogid;
    // For storing the list of activated blogs
    $activated = array();

    // Get all blogs in the network and activate plugin on each one
    $sql = "SELECT blog_id FROM %d";
    $blog_ids = $wpdb->get_col($wpdb->prepare($sql, $wpdb->blogs));
    foreach ($blog_ids as $blog_id) {
      switch_to_blog($blog_id);
      OutoftheBox_Activate(); // The normal activation function
      $activated[] = $blog_id;
    }

    // Switch back to the current blog
    switch_to_blog($current_blog);

    // Store the array for a later function
    update_site_option('out_of_the_box_activated', $activated);
  } else { // Running on a single blog
    OutoftheBox_Activate(); // The normal activation function
  }
}

/**
 * Activate the plugin
 */
function OutoftheBox_Activate() {
  add_option('out_of_the_box_settings', array(
      'purcasecode' => '',
      'dropbox_app_key' => '',
      'dropbox_app_secret' => '',
      'dropbox_app_token' => '',
      'dropbox-auth-csrf-token' => '')
  );
  add_option('out_of_the_box_advancedsettings', array(
      'custom_css' => '',
      'shortlinks' => 'Dropbox',
      'bitly_login' => '', 'bitly_apikey' => '',
      'thumbnails' => 'Out-of-the-Box',
      'userfolder_name' => '%user_login% (%user_email%)',
      'userfolder_oncreation' => 'Yes',
      'userfolder_onfirstvisit' => 'No',
      'userfolder_update' => 'Yes',
      'userfolder_remove' => 'Yes',
      'download_template' => '',
      'upload_template' => '')
  );

  $advancedoptions = get_option('out_of_the_box_advancedsettings');
  if (empty($advancedoptions['download_template'])) {
    $advancedoptions['download_template'] = 'Hi!

%visitor% has downloaded a file from your site: %filename% (%filesize%)';
  }
  if (empty($advancedoptions['upload_template'])) {
    $advancedoptions['upload_template'] = 'Hi!

%visitor% has uploaded the following file(s) to your Dropbox:

%filelist%';
  }
  update_option('out_of_the_box_advancedsettings', $advancedoptions);

  update_option('out_of_the_box_lists', array());
}

/**
 * Deactivate the plugin on network
 */
function OutoftheBox_Network_Deactivate($network_wide) {
  if (is_multisite() && $network_wide) { // See if being activated on the entire network or one blog
    global $wpdb;

    // Get this so we can switch back to it later
    $current_blog = $wpdb->blogid;

    // If the option does not exist, plugin was not set to be network active
    if (get_site_option('out_of_the_box_activated') === false) {
      return false;
    }

    // Get all blogs in the network
    $activated = get_site_option('out_of_the_box_activated');

    $sql = "SELECT blog_id FROM %d";
    $blog_ids = $wpdb->get_col($wpdb->prepare($sql, $wpdb->blogs));
    foreach ($blog_ids as $blog_id) {
      if (!in_array($blog_id, $activated)) { // Plugin is not activated on that blog
        switch_to_blog($blog_id);
        OutoftheBox_Deactivate();
      }
    }

    // Switch back to the current blog
    switch_to_blog($current_blog);

    // Store the array for a later function
    update_site_option('out_of_the_box_activated', $activated);
  } else { // Running on a single blog
    OutoftheBox_Deactivate();
  }
}

/**
 * Deactivate the plugin
 */
function OutoftheBox_Deactivate() {
  update_option('out_of_the_box_lists', array());

  $wp_upload_dir = wp_upload_dir();
  $uploaddir = $wp_upload_dir['basedir'] . '/outofthebox';
  $uploadfiles = @scandir($uploaddir);

  if ($uploadfiles !== FALSE) {
    foreach ($uploadfiles as $uploadfile) {
      @unlink($uploaddir . '/' . $uploadfile);
    }
    @rmdir($uploaddir);
  }
}

/**
 * Deactivate the plugin on network
 */
function OutoftheBox_Network_Uninstall($network_wide) {
  if (is_multisite() && $network_wide) { // See if being activated on the entire network or one blog
    global $wpdb;

    // Get this so we can switch back to it later
    $current_blog = $wpdb->blogid;

    // If the option does not exist, plugin was not set to be network active
    if (get_site_option('out_of_the_box_activated') === false) {
      return false;
    }

    // Get all blogs in the network
    $activated = get_site_option('out_of_the_box_activated');

    $sql = "SELECT blog_id FROM %d";
    $blog_ids = $wpdb->get_col($wpdb->prepare($sql, $wpdb->blogs));
    foreach ($blog_ids as $blog_id) {
      if (!in_array($blog_id, $activated)) { // Plugin is not activated on that blog
        switch_to_blog($blog_id);
        OutoftheBox_Uninstall();
      }
    }

    // Switch back to the current blog
    switch_to_blog($current_blog);

    // Store the array for a later function
    update_site_option('out_of_the_box_activated', $activated);
  } else { // Running on a single blog
    OutoftheBox_Uninstall();
  }
}

/**
 * Deactivate the plugin
 */
function OutoftheBox_Uninstall() {
  //delete_option('out_of_the_box_settings');
  //delete_option('out_of_the_box_advancedsettings');
  delete_option('out_of_the_box_lists');
  delete_option('out_of_the_box_activated');

  $cachefiles = @scandir(OUTOFTHEBOX_CACHEDIR);

  if ($cachefiles !== FALSE) {
    $cachefiles = array_diff($cachefiles, array('..', '.', '.htaccess'));
    foreach ($cachefiles as $cachefile) {
      @unlink(OUTOFTHEBOX_CACHEDIR . '/' . $cachefile);
    }
  }
}

//Helpers

function OutoftheBox_return_bytes($size_str) {
  switch (substr($size_str, -1)) {
    case 'M': case 'm': return (int) $size_str * 1048576;
    case 'K': case 'k': return (int) $size_str * 1024;
    case 'G': case 'g': return (int) $size_str * 1073741824;
    default: return $size_str;
  }
}

function OutoftheBox_bytesToSize1024($bytes, $precision = 2) {
// human readable format -- powers of 1024
  $unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
  return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision) . ' ' . $unit[$i];
}

function OutoftheBox_mbPathinfo($filepath) {
  preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $filepath, $m);
  if (isset($m[1]))
    $ret['dirname'] = $m[1];
  if (isset($m[2]))
    $ret['basename'] = $m[2];
  if (isset($m[5]))
    $ret['extension'] = $m[5];
  if (isset($m[3]))
    $ret['filename'] = $m[3];

  if (substr($filepath, -1) === '.') {
    $ret['basename'] .= '.';
    unset($ret['extension']);
  }

  return $ret;
}