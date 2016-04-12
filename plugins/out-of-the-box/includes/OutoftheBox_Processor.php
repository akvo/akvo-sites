<?php

class OutoftheBox_Processor {

  public $options = array();
  protected $lists = array();
  protected $listtoken = '';
  protected $_requestedFile;
  protected $_requestedDir;
  protected $_requestedPath;
  protected $_requestedCompletePath;
  protected $_lastPath = '/';
  protected $_userFolder = false;
  public $mobile = false;

  /**
   * Construct the plugin object
   */
  public function __construct() {
    $this->settings = get_option('out_of_the_box_settings', array('purcasecode' => '', 'dropbox_app_key' => '', 'dropbox_app_secret' => '', 'dropbox_app_token' => '', 'dropbox-auth-csrf-token' => ''));
    $this->advancedsetting = get_option('out_of_the_box_advancedsettings', array('shortlinks' => 'Dropbox', 'bitly_login' => '', 'bitly_apikey' => '', 'thumbnails' => 'Out-of-the-Box', 'userfolder_name' => '%user_login% (%user_email%)', 'userfolder_oncreation' => 'Yes', 'userfolder_onfirstvisit' => 'No', 'userfolder_update' => 'Yes', 'userfolder_remove' => 'Yes'));
    $this->lists = get_option('out_of_the_box_lists', array());

    if (isset($_REQUEST['mobile']) && ($_REQUEST['mobile'] === 'true')) {
      $this->mobile = true;
    }
  }

  public function startProcess() {
    if (isset($_REQUEST['action'])) {

      $authorized = $this->_IsAuthorized();

      if (($authorized === true) && ($_REQUEST['action'] === 'outofthebox-revoke')) {
        $this->revokeToken();
        die();
      }

      if ((!isset($_REQUEST['listtoken']))) {
        die();
      }

      $this->listtoken = $_REQUEST['listtoken'];
      if (!isset($this->lists[$this->listtoken])) {
        die();
      }

      $this->options = $this->lists[$this->listtoken];

//Set rootFolder
      if (($this->options['user_upload_folders'] === '1') && !$this->checkUserRole($this->options['view_user_folders_role'])) {
        $this->_rootFolder = $this->options['root'] . $this->createUserFolder();
      } else {
        $this->_rootFolder = $this->options['root'];
      }

      if (!$this->checkUserRole($this->options['view_role'])) {
        die();
      }

      if (isset($_REQUEST['lastpath'])) {
        $this->_lastPath = urldecode($_REQUEST['lastpath']);
      }

      if (isset($_REQUEST['OutoftheBoxpath']) && $_REQUEST['OutoftheBoxpath'] != '') {
        $this->_setRequestedPath(urldecode($_REQUEST['OutoftheBoxpath']));
      } else {
        $this->_setRequestedPath();
      }

      switch ($_REQUEST['action']) {
        case 'outofthebox-getfilelist':
          if (is_wp_error($authorized)) {
// No valid token is set
            echo json_encode(array('lastpath' => $this->_lastPath, 'path' => '', 'folder' => '', 'html' => ''));
            die();
          }

          if (isset($_REQUEST['query']) && $this->options['search'] === '1') { // Search files
            $filelist = $this->searchFiles();
          } else {
            $filelist = $this->getFilesList(); // Read folder
          }

          break;

        case 'outofthebox-download':
        case 'outofthebox-createzip':
        case 'outofthebox-createlink':
        case 'outofthebox-embedded':
          if (!$this->checkUserRole($this->options['download_role'])) {
            die();
          }

          if (is_wp_error($authorized)) {
            die();
          }

          if ($_REQUEST['action'] === 'outofthebox-download') {
            $file = $this->downloadFile();
          } elseif ($_REQUEST['action'] === 'outofthebox-createzip') {
            $file = $this->createZip();
          } else {
            if (isset($_REQUEST['entries'])) {
              $links = $this->createLinks();
              echo json_encode($links);
            } else {
              $link = $this->createLink();
              echo json_encode($link);
            }

            die();
          }

          break;
        case 'outofthebox-getgallery':
          if (is_wp_error($authorized)) {
// No valid token is set
            echo json_encode(array('lastpath' => $this->_lastPath, 'folder' => '', 'html' => ''));
            die();
          }

          if (isset($_REQUEST['query']) && $this->options['search'] === '1') { // Search files
            $imagelist = $this->searchImageFiles();
          } else {
            $imagelist = $this->getImagesList(); // Read folder
          }

          break;

        case 'outofthebox-uploadfile':
          $user_can_upload = false;
          if ($this->options['upload'] === '1') {
            if ($this->checkUserRole($this->options['upload_role'])) {
              $user_can_upload = true;
            }
          }

          if (is_wp_error($authorized) || $user_can_upload === false) {
            die();
          }

          $upload = $this->uploadFile();
          die();
          break;

        case 'outofthebox-deleteentry':
//Check if user is allowed to delete entry
          $user_can_delete = false;
          if ($this->options['delete'] === '1') {
            if ($this->checkUserRole($this->options['deletefiles_role']) || $this->checkUserRole($this->options['deletefolders_role'])) {
              $user_can_delete = true;
            }
          }

          if (is_wp_error($authorized) || $user_can_delete === false) {
            echo json_encode(array('result' => '-1', 'msg' => __('Failed to delete entry', 'outofthebox')));
            die();
          }

          $file = $this->deleteEntry();

          if (is_wp_error($file)) {
            echo json_encode(array('result' => '-1', 'msg' => $file->get_error_message()));
          } else {
            echo json_encode(array('result' => '1', 'msg' => __('Entry was deleted', 'outofthebox')));
          }
          die();
          break;

        case 'outofthebox-renameentry':
//Check if user is allowed to rename entry
          $user_can_rename = false;
          if ($this->options['rename'] === '1') {
            if ($this->checkUserRole($this->options['renamefiles_role']) || $this->checkUserRole($this->options['renamefolders_role'])) {
              $user_can_rename = true;
            }
          }

          if (is_wp_error($authorized) || $user_can_rename === false) {
            echo json_encode(array('result' => '-1', 'msg' => __('Failed to rename entry', 'outofthebox')));
            die();
          }

//Strip unsafe characters
          $newname = urldecode($_REQUEST['newname']);
          $special_chars = array("?", "/", "\\", "<", ">", ":", "\"", "*");
          $newname = str_replace($special_chars, '', $newname);

          $file = $this->renameEntry($newname);

          if (is_wp_error($file)) {
            echo json_encode(array('result' => '-1', 'msg' => $file->get_error_message()));
          } else {
            echo json_encode(array('result' => '1', 'msg' => __('Entry was renamed', 'outofthebox')));
          }

          die();
          break;

        case 'outofthebox-addfolder':

//Check if user is allowed to add folder
          $user_can_addfolder = false;
          if ($this->options['addfolder'] === '1') {
            if ($this->checkUserRole($this->options['addfolder_role'])) {
              $user_can_addfolder = true;
            }
          }

          if (is_wp_error($authorized) || $user_can_addfolder === false) {
            echo json_encode(array('result' => '-1', 'msg' => __('Failed to add folder', 'outofthebox')));
            die();
          }

//Strip unsafe characters
          $newfolder = urldecode($_REQUEST['newfolder']);
          $special_chars = array("?", "/", "\\", "<", ">", ":", "\"", "*");
          $newfolder = str_replace($special_chars, '', $newfolder);

          $file = $this->addFolder($newfolder);

          if (is_wp_error($file)) {
            echo json_encode(array('result' => '-1', 'msg' => $file->get_error_message()));
          } else {
            echo json_encode(array('result' => '1', 'msg' => __('Folder', 'outofthebox') . ' ' . $newfolder . ' ' . __('was added', 'outofthebox'), 'lastpath' => $this->_lastPath,));
          }
          die();
          break;

        case 'outofthebox-getplaylist':
          if (is_wp_error($authorized)) {
            die();
          }

          $playlist = $this->getMediaList();

          break;

        default:
          die();
      }
    } else {
      die();
    }
  }

  public function createFromShortcode($atts) {

//Create a unique identifier
    $atts = (is_string($atts)) ? array() : $atts;
    $this->listtoken = md5(OUTOFTHEBOX_VERSION . serialize($atts));

    $max_size_bytes = min(OutoftheBox_return_bytes(ini_get('post_max_size')), OutoftheBox_return_bytes(ini_get('upload_max_filesize')));

//Read shortcode
    extract(shortcode_atts(array(
        'dir' => '/',
        'mode' => 'files',
        'userfolders' => '0',
        'viewuserfoldersrole' => 'administrator',
        'ext' => '*',
        'showfiles' => '1',
        'filesize' => '1',
        'filedate' => '1',
        'showcolumnnames' => '1',
        'showext' => '1',
        'showroot' => '0',
        'sortfield' => 'name',
        'sortorder' => 'asc',
        'showbreadcrumb' => '1',
        'candownloadzip' => '0',
        'showsharelink' => '0',
        'roottext' => __('Start', 'outofthebox'),
        'search' => '1',
        'include' => '*',
        'exclude' => '*',
        'maxwidth' => '100%',
        'viewrole' => 'administrator|editor|author|contributor|subscriber|pending|guest',
        'downloadrole' => 'administrator|editor|author|contributor|subscriber|pending|guest',
        'forcedownload' => '0',
        'maximages' => '25',
        'crop' => '0',
        'quality' => '90',
        'targetheight' => '150',
        'partiallastrow' => '1',
        'shuffle' => '0',
        'mediaextensions' => '',
        'notificationupload' => '0',
        'notificationdownload' => '0',
        'notificationemail' => '%admin_email%',
        'upload' => '0',
        'overwrite' => '0',
        'uploadext' => '.',
        'uploadrole' => 'administrator|editor|author|contributor|subscriber',
        'maxfilesize' => $max_size_bytes,
        'delete' => '0',
        'deletefilesrole' => 'administrator|editor',
        'deletefoldersrole' => 'administrator|editor',
        'rename' => '0',
        'renamefilesrole' => 'administrator|editor',
        'renamefoldersrole' => 'administrator|editor',
        'addfolder' => '0',
        'addfolderrole' => 'administrator|editor',
        'mcepopup' => '0',
        'debug' => '0',
        'demo' => '0'
                    ), $atts));

    if (!isset($this->lists[$this->listtoken])) {

      $authorized = $this->_isAuthorized();

      if (is_wp_error($authorized)) {
        if ($debug === '1') {
          return "<div id='message' class='error'><p>" . $autorized->get_error_message() . "</p></div>";
        }
        return "";
      }

      $this->lists[$this->listtoken] = array();

//Set Session Data
      switch ($mode) {
        case 'audio':
        case 'video':
          $mediaextensions = explode('|', $mediaextensions);
          break;
        case 'gallery':
          $ext = ($ext == '*') ? 'gif|jpg|jpeg|png|bmp' : $ext;
          $uploadext = ($uploadext == '.') ? 'gif|jpg|jpeg|png|bmp' : $uploadext;
        default:
          $mediaextensions = '';
          break;
      }

      //Force $candownloadzip = 0 if we can't use ZipArchive
      if (!class_exists('ZipArchive')) {
        $candownloadzip = '0';
      }

      $dir = rtrim($dir, "/");
      $dir = ($dir == '') ? '/' : $dir;
      if (substr($dir, 0, 1) !== '/') {
        $dir = '/' . $dir;
      }

      // Explode roles
      $viewrole = explode('|', $viewrole);
      $downloadrole = explode('|', $downloadrole);
      $uploadrole = explode('|', $uploadrole);
      $deletefilesrole = explode('|', $deletefilesrole);
      $deletefoldersrole = explode('|', $deletefoldersrole);
      $renamefilesrole = explode('|', $renamefilesrole);
      $renamefoldersrole = explode('|', $renamefoldersrole);
      $addfolderrole = explode('|', $addfolderrole);
      $viewuserfoldersrole = explode('|', $viewuserfoldersrole);

      $this->options = array(
          'root' => htmlspecialchars_decode($dir),
          'mode' => $mode,
          'user_upload_folders' => $userfolders,
          'view_user_folders_role' => $viewuserfoldersrole,
          'media_extensions' => $mediaextensions,
          'ext' => explode('|', strtolower($ext)),
          'show_files' => $showfiles,
          'show_filesize' => $filesize,
          'show_filedate' => $filedate,
          'show_columnnames' => $showcolumnnames,
          'show_ext' => $showext,
          'show_root' => $showroot,
          'sort_field' => $sortfield,
          'sort_order' => $sortorder,
          'show_breadcrumb' => $showbreadcrumb,
          'can_download_zip' => $candownloadzip,
          'show_sharelink' => $showsharelink,
          'root_text' => $roottext,
          'search' => $search,
          'include' => explode('|', htmlspecialchars_decode($include)),
          'exclude' => explode('|', htmlspecialchars_decode($exclude)),
          'maxwidth' => $maxwidth,
          'view_role' => $viewrole,
          'download_role' => $downloadrole,
          'forcedownload' => $forcedownload,
          'notificationupload' => $notificationupload,
          'notificationdownload' => $notificationdownload,
          'notificationemail' => $notificationemail,
          'upload' => $upload,
          'overwrite' => $overwrite,
          'upload_ext' => strtolower($uploadext),
          'upload_role' => $uploadrole,
          'maxfilesize' => $maxfilesize,
          'delete' => $delete,
          'deletefiles_role' => $deletefilesrole,
          'deletefolders_role' => $deletefoldersrole,
          'rename' => $rename,
          'renamefiles_role' => $renamefilesrole,
          'renamefolders_role' => $renamefoldersrole,
          'addfolder' => $addfolder,
          'addfolder_role' => $addfolderrole,
          'maximages' => $maximages,
          'crop' => $crop,
          'quality' => $quality,
          'targetheight' => $targetheight,
          'partiallastrow' => $partiallastrow,
          'shuffle' => $shuffle,
          'mcepopup' => $mcepopup,
          'debug' => $debug,
          'demo' => $demo,
          'expire' => strtotime('+1 weeks'),
          'listtoken' => $this->listtoken);

      $this->updateLists();

      //Create userfolders if needed

      if (($this->options['user_upload_folders'] === '1')) {
        if ($this->advancedsetting['userfolder_onfirstvisit'] === 'Yes') {

          $allusers = array();
          $roles = array_diff($this->options['upload_role'], $this->options['view_user_folders_role']);

          foreach ($roles as $role) {
            $users_query = new WP_User_Query(array(
                'fields' => 'all_with_meta',
                'role' => $role,
                'orderby' => 'display_name'
            ));
            $results = $users_query->get_results();
            if ($results) {
              $allusers = array_merge($allusers, $results);
            }
          }

          foreach ($allusers as $user) {
            $requestedCompletePath = $this->options['root'] . '/' . $this->createUserFolder($user);
            $this->_requestedCompletePath = str_replace('//', '/', $requestedCompletePath);
            //Creating folders can take a while, max 20 second per user
            set_time_limit(20);
            $this->addUserFolder();
          }
        }
      }
    } else {
      $this->options = $this->lists[$this->listtoken];
    }

    ob_start();
    $this->renderTemplate();

    return ob_get_clean();
  }

  public function renderTemplate() {

// Render the  template
    if ($this->checkUserRole($this->options['view_role'])) {

      echo "<div id='OutoftheBox'>";
      echo "<noscript><div class='OutoftheBox-nojsmessage'>" . __('To view the Dropbox folders, you need to have JavaScript enabled in your browser', 'outofthebox') . ".<br/>";
      echo "<a href='http://www.enable-javascript.com/' target='_blank'>" . __('To do so, please follow these instructions', 'outofthebox') . "</a>.</div></noscript>";

      switch ($this->options['mode']) {
        case 'files':

          echo "<div id='OutoftheBox-$this->listtoken' class='OutoftheBox files oftb-list jsdisabled' data-list='files' data-token='$this->listtoken' data-path='" . urlencode($this->_lastPath) . "' data-org-path='" . urlencode($this->_lastPath) . "' data-sort='" . $this->options['sort_field'] . ":" . $this->options['sort_order'] . "' data-deeplink='" . ((!empty($_REQUEST['file'])) ? $_REQUEST['file'] : '') . "' data-layout='list'>";
          include(sprintf("%s/templates/frontend.php", OUTOFTHEBOX_ROOTDIR));
          $this->renderUploadform();
          echo "</div>";
          break;

        case 'gallery':
          echo "<div id='OutoftheBox-$this->listtoken' class='OutoftheBox gridgallery jsdisabled' data-list='gallery' data-token='$this->listtoken' data-path='" . urlencode($this->_lastPath) . "' data-org-path='" . urlencode($this->_lastPath) . "' data-sort='" . $this->options['sort_field'] . ":" . $this->options['sort_order'] . "'  data-targetheight='" . $this->options['targetheight'] . "' data-lastrow='" . (($this->options['partiallastrow'] === '1') ? 'true' : 'false') . "' data-deeplink='" . ((!empty($_REQUEST['image'])) ? $_REQUEST['image'] : '') . "'>";
          include(sprintf("%s/templates/gallery.php", OUTOFTHEBOX_ROOTDIR));
          $this->renderUploadform();
          echo "</div>";
          break;

        case 'video':
        case 'audio':
          $mp4key = array_search('mp4', $this->options['media_extensions']);
          if ($mp4key !== false) {
            unset($this->options['media_extensions'][$mp4key]);
            if ($this->options['mode'] === 'video') {
              if (!in_array('m4v', $this->options['media_extensions'])) {
                $this->options['media_extensions'][] = 'm4v';
              }
            } else {
              if (!in_array('m4a', $this->options['media_extensions'])) {
                $this->options['media_extensions'][] = 'm4a';
              }
            }
          }

          $oggkey = array_search('ogg', $this->options['media_extensions']);
          if ($oggkey !== false) {
            unset($this->options['media_extensions'][$oggkey]);
            if ($this->options['mode'] === 'video') {
              if (!in_array('ogv', $this->options['media_extensions'])) {
                $this->options['media_extensions'][] = 'ogv';
              }
            } else {
              if (!in_array('oga', $this->options['media_extensions'])) {
                $this->options['media_extensions'][] = 'oga';
              }
            }
          }

          $extensions = join(',', $this->options['media_extensions']);
          if ($extensions !== '') {
            echo "<div id='OutoftheBox-$this->listtoken' class='OutoftheBox media " . $this->options['mode'] . " jsdisabled' data-list='media' data-token='$this->listtoken' data-extensions='" . $extensions . "' data-path='$this->_lastPath' data-sort='" . $this->options['sort_field'] . ":" . $this->options['sort_order'] . "' data-deeplink=''>";
            include(sprintf("%s/templates/player.php", OUTOFTHEBOX_ROOTDIR));
            echo "</div>";
          } else {
            echo '<strong>Out-of-the-Box:</strong>' . __('Please update your mediaplayer shortcode', 'outofthebox');
          }

          break;
      }
      echo "</div>";
    }
  }

  public function renderUploadform() {
    $user_can_upload = false;
    if ($this->checkUserRole($this->options['upload_role'])) {
      $user_can_upload = true;
    }

    if ($this->options['upload'] === '1' && $user_can_upload) {
      $post_max_size_bytes = min(OutoftheBox_return_bytes(ini_get('post_max_size')), OutoftheBox_return_bytes(ini_get('upload_max_filesize')));
      $post_max_size_str = OutoftheBox_bytesToSize1024($post_max_size_bytes);

      $acceptfiletypes = '.(' . $this->options['upload_ext'] . ')$';

      include(sprintf("%s/templates/uploadform.php", OUTOFTHEBOX_ROOTDIR));
    }
  }

  private function _setRequestedPath($path = '') {

    if ($path === '') {
      if ($this->_lastPath !== '') {
        $path = $this->_lastPath;
      } else {
        $path = '/';
      }
    }

    $special_chars = array("?", "\\", "=", "<", ">", ":", "\"", "*", "|");
    $path = str_replace($special_chars, '', $path);

    $path = rtrim($path, "/");
    if (($path !== '') && (substr($path, 0, 1) !== '/')) {
      $path = '/' . $path;
    }

    $path = str_replace(array('\\', '//'), '/', $path);

    $path_parts = OutoftheBox_mbPathinfo($path);

    $this->_requestedDir = '';
    $this->_requestedFile = '';

    if (isset($path_parts['extension'])) {
//it's a file
      $this->_requestedFile = $path_parts['basename'];
      $this->_requestedDir = str_replace('\\', '/', $path_parts['dirname']);
      $requestedDir = ($this->_requestedDir === '/') ? '/' : $this->_requestedDir . '/';
      $this->_requestedPath = $requestedDir . $this->_requestedFile;
    } else {
//it's a dir
      $this->_requestedDir = str_replace('\\', '/', $path);
      $this->_requestedFile = '';
      $this->_requestedPath = $this->_requestedDir;
    }

    $requestedCompletePath = $this->_rootFolder . $this->_requestedPath;
    $this->_requestedCompletePath = str_replace('//', '/', $requestedCompletePath);

//Create user folder if need and doesn't exists
    if (($this->options['user_upload_folders'] === '1') && !$this->checkUserRole($this->options['view_user_folders_role'])) {
      $this->addUserFolder();
    }
  }

  protected function setLastPath($path) {
    $this->_lastPath = $path;
    if ($this->_lastPath === '') {
      $this->_lastPath = '/';
    }
    $this->_setRequestedPath();
    return $this->_lastPath;
  }

  protected function updateLists() {
    $this->lists[$this->listtoken] = $this->options;
    $this->_cleanLists();
    update_option('out_of_the_box_lists', $this->lists);
  }

  protected function sortFilelist($foldercontents) {
    if (count($foldercontents) > 0) {
// Sort Filelist, folders first
      $sort = array();


      if ($this->options['shuffle']) {
        shuffle($foldercontents);
        return $foldercontents;
      }


      $sort_field = 'path';
      $sort_order = SORT_ASC;

      if (isset($_REQUEST['sort'])) {
        $sort_options = explode(':', $_REQUEST['sort']);

        if (count($sort_options) === 2) {

          switch ($sort_options[0]) {
            case 'name':
              $sort_field = 'path';
              break;
            case 'size':
              $sort_field = 'bytes';
              break;
            case 'modified':
              $sort_field = 'modified';
              break;
          }

          switch ($sort_options[1]) {
            case 'asc':
              $sort_order = SORT_ASC;
              break;
            case 'desc':
              $sort_order = SORT_DESC;
              break;
          }
        }
      }

      foreach ($foldercontents as $k => $v) {
        $sort['is_dir'][$k] = $v['is_dir'];

        if ($sort_field === 'modified') {
          if (isset($v['client_mtime']) && (strtotime($v['client_mtime']) > strtotime($v['modified']))) {
            $sort['sort'][$k] = strtotime($v['client_mtime']);
          } else {
            $sort['sort'][$k] = strtotime($v['modified']);
          }
        } else {
          $sort['sort'][$k] = strtolower($v[$sort_field]);
        }
      }

// Sort by dir desc and then by name asc
      array_multisort($sort['is_dir'], SORT_DESC, $sort['sort'], $sort_order, $foldercontents);
    }
    return $foldercontents;
  }

  protected function createUserFolder($user = false) {
// Create unique user path
// Needed if $userfolders is set
    $userfoldername = '';

    if (is_user_logged_in() && $user === false) {
      $current_user = wp_get_current_user();

      $userfoldersname = strtr($this->advancedsetting['userfolder_name'], array(
          "%user_login%" => $current_user->user_login,
          "%user_email%" => $current_user->user_email,
          "%display_name%" => $current_user->display_name,
          "%ID%" => $current_user->ID,
      ));

      $userfoldername = '/' . $userfoldersname;
      $this->_userFolder = $userfoldersname;
    } elseif ($user !== false) {

      $userfoldersname = strtr($this->advancedsetting['userfolder_name'], array(
          "%user_login%" => $user->user_login,
          "%user_email%" => $user->user_email,
          "%display_name%" => $user->display_name,
          "%ID%" => $user->ID,
      ));

      $userfoldername = '/' . $userfoldersname;
      $this->_userFolder = $userfoldersname;
    } else {
      $userfolder = uniqid();
      if (!isset($_COOKIE['OftB-ID'])) {
        $expire = time() + 60 * 60 * 24 * 7;
        setcookie('OftB-ID', $userfolder, $expire, '/');
      } else {
        $userfolder = $_COOKIE['OftB-ID'];
      }

      $userhash = md5($userfolder);
      $userfoldername = '/' . __('Guests', 'outofthebox') . '/' . $userhash;
      $this->_userFolder = __('Guest', 'outofthebox');
    }

    return $userfoldername;
  }

  public function userChangeFolder($rootfolder, $userfoldername, $oldfoldername, $delete = false) {
    if ($this->_isAuthorized(true) === true) {
      if ($userfoldername !== '' && $oldfoldername !== '') {
        $this->updateUserFolder($rootfolder, $userfoldername, $oldfoldername, $delete);
      }
    }
  }

  protected function sendNotificationEmail($emailtype = false, $entry = array()) {

    if ($emailtype === false) {
      return;
    }

    /* Get emailaddress */
    $email = strtr(trim($this->options['notificationemail']), array(
        "%admin_email%" => get_option('admin_email')
    ));

    /* Vistor name */
    $visitor = __('A guest', 'outofthebox');
    if (is_user_logged_in()) {
      $current_user = wp_get_current_user();
      $visitor = $current_user->display_name;
    }

    /* Subject */
    $subject = get_option('blogname');

    /* Create Message */
    switch ($emailtype) {
      case 'download':
        $path_parts = OutoftheBox_mbPathinfo($entry['path']);

        $subject .= ' | ' . __('File downloaded', 'outofthebox') . ': ' . $path_parts['basename'];
        $message = strtr(trim($this->advancedsetting['download_template']), array(
            "%visitor%" => $visitor,
            "%filename%" => $path_parts['filename'],
            "%filesize%" => OutoftheBox_bytesToSize1024($entry['size'])
        ));
        break;

      case'upload':
        $subject .= ' | ' . __('New file(s) in your Dropbox', 'outofthebox');

        $filelist = '';
        foreach ($entry as $file) {
          $succeeded = (isset($file->error)) ? " | " . $file->error : '';
          $filelist = $file->name . " (" . OutoftheBox_bytesToSize1024($file->size) . ")" . $succeeded . "\n";
        }

        $message = strtr(trim($this->advancedsetting['upload_template']), array(
            "%visitor%" => $visitor,
            "%filelist%" => $filelist
        ));
        break;
    }

    /* Send mail */
    try {
      wp_mail($email, $subject, $message);
    } catch (Exception $ex) {

    }
  }

  private function _cleanLists() {
    $now = time();
    foreach ($this->lists as $token => $list) {

      if (!isset($list['expire']) || ($list['expire']) < $now) {
        unset($this->lists[$token]);
      }
    }
  }

  protected function _isAuthorized($hook = false) {
    if (isset($_REQUEST['action']) && ($hook === false)) {
      switch ($_REQUEST['action']) {
        case 'outofthebox-uploadfile':
          check_ajax_referer('outofthebox-upload-file');
          break;
        case 'outofthebox-getfilelist':
          check_ajax_referer('outofthebox-refresh-folders');
          break;
        case 'outofthebox-getgallery':
          check_ajax_referer('outofthebox-get-gallery');
          break;
        case 'outofthebox-deleteentry':
          check_ajax_referer('outofthebox-delete-entry');
          break;
        case 'outofthebox-renameentry':
          check_ajax_referer('outofthebox-rename-entry');
          break;
        case 'outofthebox-addfolder':
          check_ajax_referer('outofthebox-add-folder');
          break;
        case 'outofthebox-getplaylist':
          check_ajax_referer('outofthebox-getplaylist');
          break;
        case 'outofthebox-createzip':
          check_ajax_referer('outofthebox-createzip');
          break;
        case 'outofthebox-createlink':
        case 'outofthebox-embedded':
          check_ajax_referer('outofthebox-createlink');
          break;
        case 'outofthebox-download':
        case 'outofthebox-getpopup':
        case 'outofthebox-thumbnail':
        case 'outofthebox-revoke':
          break;
        default:
          die();
      }
    }

    $hasToken = $this->loadToken();

    if (is_wp_error($hasToken)) {
      return $hasToken;
    }

    if (is_wp_error($appInfo = $this->setAppConfig())) {
      return $appInfo;
    }

    $client = $this->startClient();
    $accountInfo = $this->getAccountInfo();

    if ($accountInfo === false) {
      return new WP_Error('broke', __('No valid Dropbox token found... Please Authorize!', 'outofthebox'));
    } else if (is_wp_error($accountInfo)) {
      return $accountInfo;
    }

    return true;
  }

  /**
   * Checks if a particular user has a role.
   * Returns true if a match was found.
   *
   * @param array $roles Roles array.
   * @return bool
   */
  public function checkUserRole($roles_to_check = array()) {

    if (in_array('none', $roles_to_check)) {
      return false;
    }

    if (in_array('guest', $roles_to_check)) {
      return true;
    }

    if (is_super_admin()) {
      return true;
    }

    if (!is_user_logged_in()){
      return false;
    }

    $user = wp_get_current_user();

    if (empty($user) ||  (!($user instanceof WP_User))){
      return false;
    }

    foreach ($user->roles as $role) {
      if (in_array($role, $roles_to_check)) {
        return true;
      }
    }

    return false;
  }

  public function removeElementWithValue($array, $key, $value) {
    foreach ($array as $subKey => $subArray) {
      if ($subArray[$key] == $value) {
        unset($array[$subKey]);
      }
    }

    return $array;
  }

}