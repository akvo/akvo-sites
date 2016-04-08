<?php

require_once 'OutoftheBox_Processor.php';

// Load Dropbox SDK
// Check if Dropbox autoloader is already defined
if (!function_exists('Dropbox\autoload')) {
  try {
    require_once "dropbox-sdk/Dropbox/autoload.php";
  } catch (Exception $ex) {
    return new WP_Error('broke', __('Something went wrong... See settings page', 'outofthebox'));
  }
}

use \Dropbox as dbx;

class OutoftheBox_Dropbox extends OutoftheBox_Processor {

  /** @var string */
  private $accessToken = null;
  private $appKey = 'm3n3zyvyr59cdjb';
  private $appSecret = 'eu73x5upk7ehes4';
  private $client = null;

  /** @var string */
  public $redirectUri;

  /** @var array */
  private $appInfo;
  private $cache;

  /*
   * Try to load prestored token
   *
   * @return boolean|WP_Error
   */

  public function loadToken() {
    if (empty($this->settings['dropbox_app_token'])) {
      return new WP_Error('broke', '<strong>' . __("Out-of-the-Box needs your help!", 'outofthebox') . '</strong> ' . __('Authorize the plugin. If you would like to use your own Dropbox App, enter your App key &amp; secret and re-authorize', 'outofthebox') . '.');
    } else {
      $this->accessToken = $this->settings['dropbox_app_token'];
    }

    return true;
  }

  /*
   * Revoke token
   *
   * @return boolean|WP_Error
   */

  public function revokeToken() {
    $this->client->disableAccessToken();
    $this->accessToken = '';
    $this->settings['dropbox_app_token'] = '';
    update_option('out_of_the_box_settings', $this->settings);
    return true;
  }

  /*
   * Read Dropbox app key and secret
   */

  function setAppConfig() {
    if ((!empty($this->settings['dropbox_app_key'])) && (!empty($this->settings['dropbox_app_secret']))) {
      $this->appKey = $this->settings['dropbox_app_key'];
      $this->appSecret = $this->settings['dropbox_app_secret'];
    }

    $_appinfo = array('key' => $this->appKey, 'secret' => $this->appSecret);
    try {
      $appInfo = dbx\AppInfo::loadFromJson($_appinfo);
    } catch (dbx\AppInfoLoadException $ex) {
      return new WP_Error('broke', __("Your Dropbox App key or token seems to be invalid: ", 'outofthebox') . $ex->getMessage());
    }

    $clientIdentifier = "Out-of-the-Box(Wordpress)/" . OUTOFTHEBOX_VERSION;
    $userLocale = 'en';

    $this->appInfo = array('appInfo' => $appInfo, 'clientIdentifier' => $clientIdentifier, 'userLocale' => $userLocale);
    return $this->appInfo;
  }

  /*
   * Start Dropbox API Client with token
   *
   * @return WP_Error|dbx\Client
   */

  public function startClient() {
    if ($this->accessToken === false)
      die();

    try {
      $accessToken = $this->accessToken;
      $dbxClient = new dbx\Client($accessToken, $this->appInfo['clientIdentifier'], $this->appInfo['userLocale'], $this->appInfo['appInfo']->getHost());
    } catch (Exception $e) {
      return new WP_Error('broke', __("Error in getClient: ", 'outofthebox') . $e->getMessage());
    }

    $this->client = $dbxClient;

    return $this->client;
  }

  /*
   * Get AccountInfo
   *
   * @return mixed|WP_Error
   */

  function getAccountInfo() {
    if ($this->client === null)
      return false;

    try {
      $accountInfo = $this->client->getAccountInfo();
    } catch (Exception $ex) {
      return new WP_Error('broke', $ex->getMessage());
    }

    return $accountInfo;
  }

  /*
   * Gets a $authorizeUrl
   *
   * @return string|WP_Error
   * The URL to redirect the user to.
   */

  public function startWebAuth() {
    try {
      $authorizeUrl = $this->getWebAuth()->start();
    } catch (Exception $ex) {
      return new WP_Error('broke', __("/dropbox-auth-start: could not start authorization: ", 'outofthebox') . $ex->getMessage());
    }
    update_option('out_of_the_box_settings', $this->settings);
    return $authorizeUrl;
  }

  /*
   * Creates token after the user has visited the authorize URL, approved the app,
   * and was redirected to your redirect URI.
   *
   * @return WP_Error|true
   */

  public function createToken() {
    if (!empty($_GET['code'])) {
      try {
        list($accessToken, $userId, $urlState) = $this->getWebAuth()->finish($_GET);
        $this->accessToken = $accessToken;
        $this->settings['dropbox_app_token'] = $accessToken;
        $this->settings['dropbox_csrf-token'] = '';
      } catch (dbx\WebAuthException_BadRequest $ex) {
        $this->settings['dropbox_csrf-token'] = '';
        update_option('out_of_the_box_settings', $this->settings);
        return new WP_Error('broke', __("/dropbox-auth-finish: bad request: ", 'outofthebox') . $ex->getMessage());
      } catch (dbx\WebAuthException_BadState $ex) {
// Auth session expired.  Restart the auth process.
        header('Location: ' . $this->redirectUri);
        die();
      } catch (dbx\WebAuthException_Csrf $ex) {
        $this->settings['dropbox_csrf-token'] = '';
        update_option('out_of_the_box_settings', $this->settings);
        return new WP_Error('broke', __("/dropbox-auth-finish: CSRF mismatch: ", 'outofthebox') . $ex->getMessage());
      } catch (dbx\WebAuthException_NotApproved $ex) {
        $this->settings['dropbox_csrf-token'] = '';
        update_option('out_of_the_box_settings', $this->settings);
        return new WP_Error('broke', __("/dropbox-auth-finish: not approved: ", 'outofthebox') . $ex->getMessage());
      } catch (dbx\WebAuthException_Provider $ex) {
        $this->settings['dropbox_csrf-token'] = '';
        update_option('out_of_the_box_settings', $this->settings);
        return new WP_Error('broke', __("/dropbox-auth-finish: error redirect from Dropbox: ", 'outofthebox') . $ex->getMessage());
      } catch (dbx\Exception $ex) {
        $this->settings['dropbox_csrf-token'] = '';
        update_option('out_of_the_box_settings', $this->settings);
        return new WP_Error('broke', __("/dropbox-auth-finish: error communicating with Dropbox API: ", 'outofthebox') . $ex->getMessage());
      }

      update_option('out_of_the_box_settings', $this->settings);

      return true;
    }
  }

  /*
   * Get folders and files or download a file
   *
   * @return false|array
   * array['path'] = current path
   * array['filelist'] = files and folders in current path
   */

  public function getFolder() {
    if ($this->client === null)
      return false;

//Get requested path
    if (isset($_REQUEST['OutoftheBoxpath']) && $_REQUEST['OutoftheBoxpath'] != '') {

//Check if requested path is allowed
      if (!$this->isEntryAuthorized($this->_requestedPath)) {
//Gotcha, this path isn't allowed, return to root or die() if downloadfile
        $this->setLastPath('');
        unset($_REQUEST['OutoftheBoxpath']);
        return $this->getFolder();
      }

      $this->setLastPath($this->_requestedPath);
    }

//Check if $requested_path is al valid path
    $pathError = dbx\Path::findError($this->_requestedCompletePath);
    if ($pathError !== null) {
      return false;
    } else {

//Get folder meta data
      $entry = $this->client->getMetadataWithChildren($this->_requestedCompletePath);

//No valid folder, return to root
      if ($entry === null) {
        if ($this->_requestedPath === '') {
          return false; // root directory doesn't exist
        }
        $this->setLastPath('');
        unset($_REQUEST['OutoftheBoxpath']);
        return $this->getFolder();
      } else {
        $this->setLastPath($this->_requestedPath);
      }

      /* Sort contents */
      if (count($entry['contents']) > 0) {
        $entry['contents'] = $this->sortFilelist($entry['contents']);
      }

      return $entry;
    }

    return false;
  }

  public function getFolderImages($path) {
    $entry = $this->client->getMetadataWithChildren($path);

    if (($entry === null) || (!isset($entry['contents']))) {
      return array();
    } else {
      return $entry['contents'];
    }
  }

  public function searchByName($query) {
    try {
      $result = $this->client->searchFileNames($this->_requestedCompletePath, $query);
    } catch (Exception $ex) {
      return array();
    }

    if (count($result) > 0) {
      return $result;
    } else {
      return array();
    }
  }

  /*
   * Uploads file to server
   * After upload send file to Dropbox
   * and delete files from tempdir
   */

  function uploadFile() {
    /* UploadHandler options */
    require('jquery-file-upload/server/UploadHandler.php');
    $accept_file_types = '/.(' . $this->options['upload_ext'] . ')$/i';
    $max_file_size = $this->options['maxfilesize'];

    $uploadir = wp_upload_dir();

    $options = array(
        'upload_dir' => $uploadir['path'] . '/',
        'upload_url' => $uploadir['url'] . '/',
        'access_control_allow_methods' => array('POST', 'PUT'),
        'accept_file_types' => $accept_file_types,
        'inline_file_types' => '/\.____$/i',
        'orient_image' => false,
        'image_versions' => array(),
        'max_file_size' => $max_file_size
    );

    if ($this->options['demo'] === '1') {
      $options['accept_file_types'] = '/\.____$/i';
    }

    $error_messages = array(
        1 => __('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'outofthebox'),
        2 => __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'outofthebox'),
        3 => __('The uploaded file was only partially uploaded', 'outofthebox'),
        4 => __('No file was uploaded', 'outofthebox'),
        6 => __('Missing a temporary folder', 'outofthebox'),
        7 => __('Failed to write file to disk', 'outofthebox'),
        8 => __('A PHP extension stopped the file upload', 'outofthebox'),
        'post_max_size' => __('The uploaded file exceeds the post_max_size directive in php.ini', 'outofthebox'),
        'max_file_size' => __('File is too big', 'outofthebox'),
        'min_file_size' => __('File is too small', 'outofthebox'),
        'accept_file_types' => __('Filetype not allowed', 'outofthebox'),
        'max_number_of_files' => __('Maximum number of files exceeded', 'outofthebox'),
        'max_width' => __('Image exceeds maximum width', 'outofthebox'),
        'min_width' => __('Image requires a minimum width', 'outofthebox'),
        'max_height' => __('Image exceeds maximum height', 'outofthebox'),
        'min_height' => __('Image requires a minimum height', 'outofthebox')
    );

    $this->upload_handler = new UploadHandler($options, false, $error_messages);
    $response = @$this->upload_handler->post(false);

    /* Upload files to Dropbox */
    foreach ($response['files'] as &$file) {
      if (!isset($file->error)) {

        /* Check if $dropboxPath is al valid path */
        $pathError = dbx\Path::findError($this->_requestedCompletePath);
        if ($pathError !== null) {
          $file->error = __('Invalid <dropbox-path>', 'outofthebox');
        } else {

          /* Check if file already exists */
          $file_exists = $this->client->getMetadata($this->_requestedCompletePath . '/' . $file->name);
          if ($file_exists !== null) {
            $file_rev = $file_exists['rev'];
          }

          /* Add or update file? */
          if ($this->options['overwrite'] === '1' && isset($file_rev)) {
            $writemode = dbx\WriteMode::update($file_rev);
          } else {
            $writemode = dbx\WriteMode::add();
          }

          /* Write file */
          $filePath = $file->tmp_path;
          $fp = fopen($filePath, "rb");
          try {
            $metadata = $this->client->uploadFileChunked($this->_requestedCompletePath . '/' . $file->name, $writemode, $fp, $file->size);
            //$metadata = $this->client->uploadFile($this->_requestedCompletePath . '/' . $file->name, $writemode, $fp, $file->size);
          } catch (Exception $ex) {
            $file->error = __('Not uploaded to Dropbox ', 'outofthebox') . $ex->getMessage();
          }
          fclose($fp);
        }
      } else {
        if ($this->options['debug'] === '1') {
          $file->error = __('Uploading failed', 'outofthebox') . ': ' . $file->error;
        } else {
          $file->error = __('Uploading failed', 'outofthebox');
        }
      }
    }

    /* Send email if needed */
    if ($this->options['notificationupload'] === '1') {
      $this->sendNotificationEmail('upload', $response['files']);
    }

    /* Create response */
    $this->upload_handler->generate_response($response);
    die();
  }

  /*
   * Delete entry from Dropbox
   */

  function deleteEntry() {

    if ($this->options['demo'] === '1') {
      return new WP_Error('broke', __('Failed to delete entry', 'outofthebox'));
    }

//Check if requested path is allowed
    if (!$this->isEntryAuthorized($this->_requestedCompletePath)) {
//Gotcha, this path isn't allowed
      return new WP_Error('broke', __('Failed to delete entry', 'outofthebox'));
    }

    if (!$this->isExtensionAuthorized($this->_requestedCompletePath, $this->options['ext'], '.')) {
//Gotcha, this extension isn't allowed
      return new WP_Error('broke', __('Failed to delete entry', 'outofthebox'));
    }

//Check if $dropboxPath is a valid path
    $pathError = dbx\Path::findError($this->_requestedCompletePath);
    if ($pathError !== null) {
      if ($this->options['debug'] === '1') {
        return new WP_Error('broke', __('Invalid <dropbox-path>', 'outofthebox') . ' ' . $pathError);
      } else {
        return new WP_Error('broke', __('Failed to delete entry', 'outofthebox'));
      }
    } else {
      try {
        $pathdata = $this->client->getMetadata($this->_requestedCompletePath);

        if ($pathdata['is_dir']) {
          if (!$this->checkUserRole($this->options['deletefolders_role'])) {
            return new WP_Error('broke', __('Failed to delete entry', 'outofthebox'));
          }
        } else {
          if (!$this->checkUserRole($this->options['deletefiles_role'])) {
            return new WP_Error('broke', __('Failed to delete entry', 'outofthebox'));
          }
        }

        $meta_entry = $this->client->delete($this->_requestedCompletePath);
      } catch (Exception $ex) {
        if ($this->options['debug'] === '1') {
          return new WP_Error('broke', $ex->getMessage());
        } else {
          return new WP_Error('broke', __('Failed to delete entry', 'outofthebox'));
        }
      }
    }

    return $meta_entry;
  }

  /*
   * Rename entry from Dropbox
   */

  function renameEntry($new_filename = null) {

    if ($new_filename === null && $this->options['debug'] === '1') {
      return new WP_Error('broke', __('No new name set', 'outofthebox'));
    }

//Check if requested path is allowed
    if (!$this->isEntryAuthorized($this->_requestedCompletePath)) {
//Gotcha, this path isn't allowed
      return new WP_Error('broke', __('Failed to rename entry', 'outofthebox'));
    }

//Check if extensions is allowed
    if (!$this->isExtensionAuthorized($this->_requestedCompletePath, $this->options['ext'], '.')) {
//Gotcha, this extension isn't allowed
      return new WP_Error('broke', __('Failed to rename entry', 'outofthebox'));
    }

//Check if $dropboxPath is a valid path
    $pathError = dbx\Path::findError($this->_requestedCompletePath);
    if ($pathError !== null) {
      if ($this->options['debug'] === '1') {
        return new WP_Error('broke', __('Invalid <dropbox-path>', 'outofthebox') . ' ' . $pathError);
      } else {
        return new WP_Error('broke', __('Failed to rename entry', 'outofthebox'));
      }
    } else {

      $pathdata = $this->client->getMetadata($this->_requestedCompletePath);

      if ($pathdata['is_dir']) {
        if (!$this->checkUserRole($this->options['renamefolders_role'])) {
          return new WP_Error('broke', __('Failed to rename entry', 'outofthebox'));
        }
      } else {
        if (!$this->checkUserRole($this->options['renamefiles_role'])) {
          return new WP_Error('broke', __('Failed to rename entry', 'outofthebox'));
        }
      }

      //Set new entry path
      $pathinfo = OutoftheBox_mbPathinfo($this->_requestedCompletePath);
      $extension = (isset($pathinfo['extension']) && !$pathdata['is_dir']) ? '.' . $pathinfo['extension'] : '';
      $newdropboxPath = dirname($this->_requestedCompletePath) . '/' . $new_filename . strtolower($extension);


      try {
        $meta_entry = $this->client->move($this->_requestedCompletePath, $newdropboxPath);
      } catch (Exception $ex) {
        if ($this->options['debug'] === '1') {
          return new WP_Error('broke', $ex->getMessage());
        } else {
          return new WP_Error('broke', __('Failed to rename entry', 'outofthebox'));
        }
      }
    }

    return $meta_entry;
  }

  /*
   * Add directory to Dropbox
   */

  function addFolder($new_folder = null) {

    if ($this->options['demo'] === '1') {
      return new WP_Error('broke', __('Failed to add folder', 'outofthebox'));
    }

    if ($new_folder === null && $this->options['debug'] === '1') {
      return new WP_Error('broke', __('No new foldername set', 'outofthebox'));
    }

//Check if requested path is allowed
    if (!$this->isEntryAuthorized($this->_requestedCompletePath)) {
//Gotcha, this path isn't allowed
      return new WP_Error('broke', __('Failed to add folder', 'outofthebox'));
    }

//Set new entry path
    $newdropboxPath = $this->_requestedCompletePath . '/' . $new_folder;

//Check if $dropboxPath is a valid path
    $pathError = dbx\Path::findError($this->_requestedCompletePath);
    if ($pathError !== null) {
      if ($this->options['debug'] === '1') {
        return new WP_Error('broke', __('Invalid <dropbox-path>', 'outofthebox') . ' ' . $pathError);
      } else {
        return new WP_Error('broke', __('Failed to add folder', 'outofthebox'));
      }
    } else {
      try {
        $meta_entry = $this->client->createFolder($newdropboxPath);
      } catch (Exception $ex) {
        if ($this->options['debug'] === '1') {
          return new WP_Error('broke', $ex->getMessage());
        } else {
          return new WP_Error('broke', __('Failed to add folder', 'outofthebox'));
        }
      }
    }

    if ($meta_entry === null) {
      return new WP_Error('broke', __('Folder', 'outofthebox') . " '$new_folder' " . __('already exists', 'outofthebox'));
    } else {

      $this->setLastPath($this->_requestedPath . '/' . $new_folder);
    }

    return $meta_entry;
  }

  function addUserFolder() {

    $pathError = dbx\Path::findError($this->_requestedCompletePath);
    if ($pathError !== null) {
      if ($this->options['debug'] === '1') {
        return new WP_Error('broke', __('Invalid <dropbox-path>', 'outofthebox') . ' ' . $pathError);
      } else {
        return new WP_Error('broke', __('Failed to add user folder', 'outofthebox'));
      }
    } else {
      $pathdata = $this->client->getMetadata($this->_requestedCompletePath);

      if ($pathdata === null) {
        try {
          $this->client->createFolder($this->_requestedCompletePath);
        } catch (Exception $ex) {
          if ($this->options['debug'] === '1') {
            return new WP_Error('broke', $ex->getMessage());
          } else {
            return new WP_Error('broke', __('Failed to add folder', 'outofthebox'));
          }
        }
      } else {
        return false;
      }
    }

    return true;
  }

  function updateUserFolder($rootfolder, $userfoldername, $olduserfoldername = false, $delete = false) {
    $pathError = dbx\Path::findError($rootfolder);
    if ($pathError === null) {
      if ($olduserfoldername === false && $delete === false) {
        $pathdata = $this->client->getMetadata($rootfolder);
      } elseif ($delete === true) {
        $pathdata = $this->client->getMetadata($rootfolder . '/' . $userfoldername);
      } else {
        $pathdata = $this->client->getMetadata($rootfolder . '/' . $olduserfoldername);
      }

      if ($pathdata !== null) {
        try {
          if ($olduserfoldername === false && $delete === false) {
            $this->client->createFolder($rootfolder . '/' . $userfoldername);
          } elseif ($delete === true) {
            $this->client->delete($rootfolder . '/' . $userfoldername);
          } else {
            $this->client->move($rootfolder . '/' . $olduserfoldername, $rootfolder . '/' . $userfoldername);
          }
        } catch (Exception $ex) {

        }
      } else {
        return false;
      }
    } else {
      return false;
    }

    return true;
  }

  /*
   * OAuth 2 "authorization code" flow
   */

  function getWebAuth() {
    if (isset($this->settings['dropbox_csrf-token'])) {
      $this->settings['dropbox_csrf-token'] = '';
    }
    $csrfTokenStore = new dbx\ArrayEntryStore($this->settings, 'dropbox-auth-csrf-token');
    return new dbx\WebAuth($this->appInfo['appInfo'], $this->appInfo['clientIdentifier'], $this->redirectUri, $csrfTokenStore);
  }

  function setRedirectUri($url) {
    $this->redirectUri = $url;
    return $this->redirectUri;
  }

  /*
   * Set Filelist token
   */

  public function setListToken($listtoken) {
    $this->listtoken = $listtoken;
    return $this->listtoken;
  }

  /*
   * Download file
   */

  function downloadFile() {

    if (isset($_REQUEST['media'])) {
      $this->redirecttoMediaFile();
      die();
    }

    //only allowed files
    $path_parts = OutoftheBox_mbPathinfo($this->_requestedCompletePath);
    if ((isset($path_parts['extension']) && !in_array(strtolower($path_parts['extension']), $this->options['ext'])) && $this->options['ext'][0] != '*') {
      die();
    }

    //skip excluded folders and files
    if ($this->options['exclude'][0] != '*') {
      if ((isset($path_parts['basename']) && in_array($path_parts['basename'], $this->options['exclude'])) ||
              (isset($path_parts['dirname']) && in_array(str_replace('/', '', $path_parts['dirname']), $this->options['exclude']))) {
        die();
      }
    }

    /* only allow included folders and files */
    if ($this->options['include'][0] != '*') {
      if (!in_array($path_parts['basename'], $this->options['include'])) {
        die();
      }
    }

    //Get folder meta data
    $entry = $this->client->getMetadata($this->_requestedCompletePath);

    //No valid folder, return to root
    if ($entry !== null) {

      //Get Link
      $link = $this->_mediaFromCache($entry['path'], 'files');
      $link = $link['cache'];

      /* Send email if needed */
      if ($this->options['notificationdownload'] === '1') {
        $this->sendNotificationEmail('download', $entry);
      }

      if ($this->options['forcedownload'] === '1' || ((isset($_REQUEST['dl'])) && ($_REQUEST['dl'] == 1))) {
        header('Location: ' . $link['url'] . '?dl=1');
      } else {
        header('Location: ' . $link['url']);
      }
    }

    die();
  }

  public function redirecttoMediaFile() {
    if (isset($_REQUEST['media']) && isset($_REQUEST['token'])) {
      $cachename = $_REQUEST['media'];
      $token = $_REQUEST['token'];
      $this->_readCache();

      if ((isset($this->cache['media'][$cachename])) && (($this->cache['media'][$cachename]['expires']) > time()) && (isset($this->cache['media'][$cachename]['tokens']))) {

        if (isset($this->cache['media'][$cachename]['tokens'][$token]) && ($this->cache['media'][$cachename]['tokens'][$token] > time())) {
          header('Location: ' . $this->cache['media'][$cachename]['url'] . '?dl=1');
        } elseif ($this->cache['media'][$cachename]['tokens'][$token] <= time()) {
          $this->_lockCache();
          unset($this->cache['media'][$cachename]['tokens'][$token]);
          $this->_setCache();
        }
      } elseif ((($this->cache['media'][$cachename]['expires']) <= time()) || (isset($this->cache['media'][$cachename]['tokens']))) {
        $this->_lockCache();
        unset($this->cache['media'][$cachename]);
        $this->_setCache();
      }
    }

    die();
  }

  /*
   * Create zipfile
   */

  public function createZip() {
//Check if requested path is allowed
    if (!$this->isEntryAuthorized($this->_requestedCompletePath)) {
//Gotcha, this path isn't allowed
      return new WP_Error('broke', __("Requested directory isn't allowed", 'outofthebox'));
    }

    $wp_upload_dir = wp_upload_dir();
    $uploaddir = $wp_upload_dir['basedir'] . '/outofthebox/';
    /* Create upload folder if needed */
    if (!is_readable($uploaddir)) {
      @mkdir($uploaddir);
    }
    if (!is_writable($uploaddir)) {
      die('Temp dir isn\'t writable: ' . $uploaddir);
    }

    //Get all files and folders to be zipped
    //From a zip index file, or create one
    if (!isset($_REQUEST['zipid'])) {
      $_ID = uniqid();
    } else {
      $_ID = $_REQUEST['zipid'];
    }
    $index_filename = '_zipindex_' . $_ID;
    $zip_filename = '_zip_' . basename($this->_requestedCompletePath) . '_' . $_ID . '.zip';

    $json_options = 0;
    if (defined('JSON_PRETTY_PRINT')) {
      $json_options |= JSON_PRETTY_PRINT;  // Supported in PHP 5.4+
    }

    if (!is_readable($uploaddir . $index_filename)) {

      if (isset($_REQUEST['files'])) {
        $dirlisting = array('folders' => array(), 'files' => array(), 'bytes' => 0, 'bytes_total' => 0);

        foreach ($_REQUEST['files'] as $file) {
          $entry = str_replace('//', '/', $this->_requestedCompletePath . '/' . urldecode($file));
          $data = $this->_getRecursiveFiles($entry, '', true);
          $dirlisting['files'] = array_merge($dirlisting['files'], $data['files']);
          $dirlisting['folders'] = array_merge($dirlisting['folders'], $data['folders']);
          $dirlisting['bytes_total'] += $data['bytes_total'];
        }
      } else {
        $dirlisting = $this->_getRecursiveFiles($this->_requestedCompletePath);
      }
      $data = json_encode($dirlisting, $json_options);

      @file_put_contents($uploaddir . $index_filename, $data);
    } else {
      $cache_file = @file_get_contents($uploaddir . $index_filename);
      $dirlisting = json_decode($cache_file, true);
    }

    $maxbytes = $dirlisting['bytes'] + 524288; // 0.5 Mb
    $filename = $uploaddir . $zip_filename;

    if (count($dirlisting['folders']) > 0 || count($dirlisting['files']) > 0) {

      //Create zip file
      $zip = new ZipArchive();

      if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
        //Cannot create or open zip file
        die();
      }

// Add folders
      if (count($dirlisting['folders']) > 0) {

        foreach ($dirlisting['folders'] as $key => $folder) {
          $zip->addEmptyDir(iconv("UTF-8", "CP852", $folder));
          unset($dirlisting['folders'][$key]);
        }
      }

      //Add files
      if (count($dirlisting['files']) > 0) {

        foreach ($dirlisting['files'] as $key => $file) {
          if ($dirlisting['bytes'] > $maxbytes) {
            break;
          }

          $tmpfile = $uploaddir . '/_zip_' . md5(basename($file['path']));
          $handle = @fopen($tmpfile, "wb");

          //get file
          $metadata = $this->client->getFile($this->_requestedCompletePath . '/' . $file['path'], $handle);

          if ($metadata !== null) {

            //add file
            $zip->addFile($tmpfile, iconv("UTF-8", "CP852", $file['path']));

            //Close and remove tmp file
          }
          $dirlisting['bytes'] += $file['bytes'];
          unset($dirlisting['files'][$key]);
          fclose($handle);
        }
      }

      //Close zip
      $result = $zip->close();

      //Update index file or remove if needed;
      if (count($dirlisting['folders']) > 0 || count($dirlisting['files']) > 0) {
        $data = json_encode($dirlisting, $json_options);
        @file_put_contents($uploaddir . $index_filename, $data);
        $finished = false;
      } else {
        @unlink($uploaddir . $index_filename);
        $finished = true;
      }

      //Remove all old tmp or zip files
      foreach (glob($uploaddir . "_zip_*") as $filename) {

        //if file is 10 minutes (600 seconds) old then delete it
        if (filemtime($filename) < (time() - 600)) {
          @unlink($filename);
        }
      }

      //Send result
      $firstelement = reset($dirlisting['files']);
      $nextfile = ($firstelement === false) ? false : basename($firstelement['path']);

      if ($result) {
        $resultdata = array(
            'listtoken' => $this->listtoken,
            'lastpath' => urlencode($this->_requestedPath),
            'zipid' => $_ID,
            'next_file' => $nextfile,
            'bytes_total' => $dirlisting['bytes_total'],
            'bytes_total_str' => OutoftheBox_bytesToSize1024($dirlisting['bytes_total']),
            'bytes_current' => $dirlisting['bytes'],
            'bytes_current_str' => OutoftheBox_bytesToSize1024($dirlisting['bytes']),
            'zip_location' => $wp_upload_dir['baseurl'] . '/outofthebox/' . $zip_filename,
            'finished' => $finished,
            'error' => false
        );
      } else {
        $resultdata = array(
            'listtoken' => $this->listtoken,
            'lastpath' => urlencode($this->_requestedPath),
            'zipid' => $_ID,
            'next_file' => $nextfile,
            'bytes_total' => $dirlisting['bytes_total'],
            'bytes_total_str' => OutoftheBox_bytesToSize1024($dirlisting['bytes_total']),
            'bytes_current' => $dirlisting['bytes'],
            'bytes_current_str' => OutoftheBox_bytesToSize1024($dirlisting['bytes']),
            'zip_location' => $wp_upload_dir['baseurl'] . '/outofthebox/' . $zip_filename,
            'finished' => $finished,
            'error' => true,
            'error_msg' => __("Couldn't create zip file", 'outofthebox')
        );
      }

      echo json_encode($resultdata, $json_options);

      die();
    } else {
      die('No files or folders selected');
    }
  }

  private function _getRecursiveFiles($path, $currentpath = '', $selection = false, &$dirlisting = array('folders' => array(), 'files' => array(), 'bytes' => 0, 'bytes_total' => 0)) {
    //Get folder meta data
    $entry = $this->client->getMetadataWithChildren($path);

    if ($entry !== null) {

      //First add Current Folder/File
      if ($selection) {
        $path_parts = OutoftheBox_mbPathinfo($entry['path']);

        //Only add allowed files to array
        if ((isset($path_parts['extension']) && !in_array(strtolower($path_parts['extension']), $this->options['ext'])) && $this->options['ext'][0] != '*') {
          continue;
        }

        //skip excluded folders and files
        if ($this->options['exclude'][0] != '*') {
          $subs = array_filter(explode('/', $path));

          foreach ($subs as $sub) {
            if (in_array($sub, $this->options['exclude'])) {
              continue 2;
            }
          }
        }

        /* only allow included folders and files */
        if ($this->options['include'][0] != '*') {
          $subs = array_filter(explode('/', $path));
          $found = false;

          foreach ($subs as $sub) {
            if (in_array($sub, $this->options['include'])) {
              $found = true;
              continue;
            }
          }
          if (!$found) {
            continue;
          }
        }

        $location = $path_parts['basename'];

        if ($entry['is_dir'] === false) {
          $dirlisting['files'][] = array('path' => $location, 'bytes' => $entry['bytes']);
          $dirlisting['bytes_total'] += $entry['bytes'];
        } else {
          $dirlisting['folders'][] = $location;
          $currentpath = $location;
        }
      }

      //If Folder add all children
      if (isset($entry['contents']) && count($entry['contents']) > 0) {

        foreach ($entry['contents'] as $child) {

          $path_parts = OutoftheBox_mbPathinfo($child['path']);

          //Only add allowed files to array
          if ((isset($path_parts['extension']) && !in_array(strtolower($path_parts['extension']), $this->options['ext'])) && $this->options['ext'][0] != '*') {
            continue;
          }

          //skip excluded folders and files
          if ($this->options['exclude'][0] != '*') {
            $subs = array_filter(explode('/', $path));

            foreach ($subs as $sub) {
              if (in_array($sub, $this->options['exclude'])) {
                continue 2;
              }
            }
          }

          /* only allow included folders and files */
          if ($this->options['include'][0] != '*') {
            $subs = array_filter(explode('/', $path));
            $found = false;

            foreach ($subs as $sub) {
              if (in_array($sub, $this->options['include'])) {
                $found = true;
                continue;
              }
            }
            if (!$found) {
              continue;
            }
          }

          $location = ($currentpath === '') ? $path_parts['basename'] : $currentpath . '/' . $path_parts['basename'];

          if ($child['is_dir'] === false) {
            $dirlisting['files'][] = array('path' => $location, 'bytes' => $child['bytes']);
            $dirlisting['bytes_total'] += $child['bytes'];
          } else {
            $dirlisting['folders'][] = $location;
            $this->_getRecursiveFiles($child['path'], $location, false, $dirlisting);
          }
        }
      }
    }

    return $dirlisting;
  }

  public function createLink($requested_entry = false) {
    $link = false;
    $error = false;

    if ($requested_entry === false) {
      $requested_entry = $this->_requestedCompletePath;
    }

    if ($this->isEntryAuthorized($this->_requestedPath)) {

      $pathError = dbx\Path::findError($requested_entry);
      if ($pathError === null) {
        $entry = $this->client->getMetadata($requested_entry);

        if ($entry !== false) {
          $this->_readCache();
          $cachename = sha1($entry['path']);

          if (!isset($this->cache['files'])) {
            $this->cache['files'] = array();
          }

          if (!empty($this->cache['files'][$cachename]['url'])) {
            $link = $this->cache['files'][$cachename];
          } else {
            $url = $this->client->createShareableLink($requested_entry) . '?dl=1';
            $this->_lockCache();
            $this->cache['files'][$cachename] = array('url' => $url, 'shortened' => 0);
            $link = $this->cache['files'][$cachename];
            $this->_setCache();
          }
        }
      }
    }

    if (!$link) {
      $error = __("Can't create link", 'outofthebox');
    }

    //If Short url is requested
    if ($link['shortened'] === 0 && $this->advancedsetting['shortlinks'] === 'Bitly') {
      require_once 'bitly/bitly.php';

      try {
        $this->bitly = new Bitly($this->advancedsetting['bitly_login'], $this->advancedsetting['bitly_apikey']);

        $response = $this->bitly->shorten($link['url']);

        if ($response) {
          $url = $response['url'];
          $this->_lockCache();
          $this->cache['files'][$cachename] = array('url' => $url, 'shortened' => 1);
          $link = $this->cache['files'][$cachename];
          $this->_setCache();
        }
      } catch (Exception $ex) {

      }
    }

    $embedlink = 'https://docs.google.com/viewer?embedded=true&url=' . urlencode($link['url']);

    $path_parts = OutoftheBox_mbPathinfo($entry['path']);
    $resultdata = array(
        'name' => $path_parts['filename'],
        'link' => $link['url'],
        'embeddedlink' => $embedlink,
        'size' => $entry['size'],
        'error' => $error
    );

    return $resultdata;
  }

  public function createLinks() {
    $links = array('links' => array());

    foreach ($_REQUEST['entries'] as $entry) {
      $links['links'][] = $this->createLink($this->_requestedPath . '/' . urldecode($entry));
    }

    return $links;
  }

  /*
   * Check if $entry is allowed
   */

  public function isEntryAuthorized($entry) {
    if ($entry === $this->_rootFolder) {
      return true;
    }

    if ($this->options['exclude'][0] != '*') {
      foreach ($this->options['exclude'] as $excludedentry) {
        if (stripos($entry, '/' . $excludedentry) !== false) {
          return false;
        }
      }
    }

    if ($this->options['include'][0] != '*') {
      foreach ($this->options['include'] as $includedentry) {
        if (stripos($entry, '/' . $includedentry) === false) {
          return false;
        }
      }
    }
    return true;
  }

  /*
   * Check if $extensions array has $entry
   */

  public function isExtensionAuthorized($entry, $extensions, $prefix = '.') {
    if ($extensions[0] != '*') {

      $pathinfo = OutoftheBox_mbPathinfo($entry);
      if (!isset($pathinfo['extension'])) {
        return true;
      }

      foreach ($extensions as $allowedextensions) {
        if (stripos($entry, $prefix . $allowedextensions) !== false) {
          return true;
        }
      }
    } else {
      return true;
    }
    return false;
  }

  protected function _imageFromCache($entry, $folderthumb = false) {
    $h = ($folderthumb === false) ? $this->options['targetheight'] * 1.5 : $this->options['targetheight'] * 1.1;
    $c = ($folderthumb === false) ? ($this->options['crop'] === '1') : true;
    $c = ($c === true) ? '1' : '0';
    $f = 'jpeg';

    $cachename = sha1($entry['path'] . $entry['modified']);

//Save image_s_urls in session
//Since Dropbox api is quite slow generating temp urls
    $this->_readCache();

    if (!isset($this->cache['images'])) {
      $this->cache['images'] = array();
    }

    if (!isset($this->cache['images'][$cachename])) {
      return $this->loadImageThumb($entry, $cachename, $h, $c, $f);
    }

    if (!isset($this->cache['images'][$cachename][$h . '_' . $c])) {
      return $this->loadImageThumb($entry, $cachename, $h, $c, $f);
    }

    if (($this->cache['images'][$cachename]['modified'] !== strtotime($entry['modified']))) {
      unset($this->cache['images'][$cachename]);
      return $this->loadImageThumb($entry, $cachename, $h, $c, $f);
    }

    if ((!file_exists(OUTOFTHEBOX_CACHEDIR . $this->cache['images'][$cachename][$h . '_' . $c]['thumb']))) {
      unset($this->cache['images'][$cachename][$h . '_' . $c]);
      return $this->loadImageThumb($entry, $cachename, $h, $c, $f);
    }

    return array('image' => $this->cache['images'][$cachename], 'cache' => $this->cache['images'][$cachename][$h . '_' . $c]);
  }

  public function loadImageThumb($entry, $cachename, $h, $c, $f) {

    if ((isset($this->cache['images'][$cachename][$h . '_' . $c]) && ($this->cache['images'][$cachename][$h . '_' . $c]['height'] === $h))) {
      return $this->cache['images'][$cachename][$h . '_' . $c];
    } else {
      $this->_lockCache();
      $this->cache['images'][$cachename]['modified'] = strtotime($entry['modified']);
      if (!isset($this->cache['images'][$cachename]['entry'])) {
        $this->cache['images'][$cachename]['entry'] = $entry['path'];
      }
      if (!isset($this->cache['images'][$cachename]['url'])) {
        $this->cache['images'][$cachename]['url'] = '';
      }

      $this->cache['images'][$cachename][$h . '_' . $c] = array(
          'load_thumb' => '',
          'create_thumb_url' => admin_url('admin-ajax.php') . '?action=outofthebox-thumbnail&src=' . $cachename . '&i=' . $h . '_' . $c,
          'thumb' => '',
          'width' => $h,
          'height' => $h,
          'crop' => $c);

      $this->_setCache();

      return array('image' => $this->cache['images'][$cachename], 'cache' => $this->cache['images'][$cachename][$h . '_' . $c]);
    }
  }

  public function createThumb() {
    $authorized = false;

    //Creating images can take a while
    set_time_limit(15);

    if ((!empty($_REQUEST['src'])) && (!empty($_REQUEST['i']))) {
      $cachename = $_REQUEST['src'];
      $index = $_REQUEST['i'];

      $this->_readCache();

      require_once('phpThumb/phpthumb.class.php');

      if (empty($this->cache['images'])) {
        die();
      }

      if (!empty($this->cache['images'][$cachename][$index]['thumb'])) {
        if (file_exists(OUTOFTHEBOX_CACHEDIR . $this->cache['images'][$cachename][$index]['thumb'])) {
          header('Location: ' . OUTOFTHEBOX_CACHEURL . $this->cache['images'][$cachename][$index]['thumb']);
          die();
        }
      }

      //Create direct URL if needed
      try {
        $filecachename = sha1($this->cache['images'][$cachename]['entry']);
        if (empty($this->cache['files'][$filecachename]['url'])) {
          if ($this->_isAuthorized()) {
            $authorized = true;
            $link = $this->client->createShareableLink($this->cache['images'][$cachename]['entry']);
            if ($link) {
              $this->_lockCache();
              $this->cache['images'][$cachename]['url'] = $link;
              $this->cache['files'][$filecachename] = array('url' => $link, 'shortened' => 0);
              $this->_setCache();
            } else {
              return false;
            }
          } else {
            return false;
          }
        }

        $imagedata = false;
        if ($this->advancedsetting['thumbnails'] === 'Out-of-the-Box' && (ini_get('allow_url_fopen'))) {
          $imagedata = file_get_contents($this->cache['images'][$cachename]['url'] . '?dl=1');
        }

        if ($this->advancedsetting['thumbnails'] === 'Dropbox' || $imagedata === false) {

          if (!$authorized) {
            if ($this->_isAuthorized()) {

            } else {
              return false;
            }
          }

          $sizes = array('l' => array('width' => '640', 'height' => '480'), 'm' => array('width' => '128', 'height' => '128'), 's' => array('width' => '64', 'height' => '64'), 'sx' => array('width' => '32', 'height' => '32'));
          $thumbsize = null;
          foreach ($sizes as $size => $dimension) {
            $thumbnail = $this->client->getThumbnail($this->cache['images'][$cachename]['entry'], 'jpeg', $size);

            if ($thumbnail !== null) {
              $imagedata = $thumbnail[1];
              $thumbsize = $dimension;
              break;
            }
          }
        }

        if ($imagedata === false) {
          die(__('Cannot get image'));
        }

        // create phpThumb object
        $phpThumb = new phpThumb();
        $phpThumb->setParameter('config_cache_directory', OUTOFTHEBOX_CACHEDIR);
        $phpThumb->setSourceData($imagedata);
        $phpThumb->setParameter('config_output_format', 'jpeg');

        //First create best thumbnail for gallery
        $phpThumb->setParameter('q', 92);
        if ($this->cache['images'][$cachename][$index]['crop'] === '1') {
          $phpThumb->setParameter('zc', 1);
          $phpThumb->setParameter('w', $this->cache['images'][$cachename][$index]['height']);
        }
        $phpThumb->setParameter('h', $this->cache['images'][$cachename][$index]['height']);

        if ($phpThumb->GenerateThumbnail() && ($this->advancedsetting['thumbnails'] === 'Out-of-the-Box')) {
          $phpThumb->CalculateThumbnailDimensions();
          $phpThumb->SetCacheFilename();
          $phpThumb->RenderToFile($phpThumb->cache_filename);

          $_cachefile_location = $phpThumb->cache_filename;
          $_locations = (explode(DIRECTORY_SEPARATOR, $_cachefile_location));
          $this->_lockCache();
          $this->cache['images'][$cachename][$index]['thumb'] = end($_locations);
          $this->cache['images'][$cachename][$index]['width'] = $phpThumb->thumbnail_width;
          $this->cache['images'][$cachename][$index]['height'] = $phpThumb->thumbnail_height;

          $this->_setCache();
        } else {

          if ($imagedata === null) {
            return false;
          }

          $file = file_put_contents(OUTOFTHEBOX_CACHEDIR . $cachename . '.jpg', $imagedata);

          //Try to get the correct size
          $thumbnail = wp_get_image_editor(OUTOFTHEBOX_CACHEDIR . $cachename . '.jpg');
          $thumb_file = $cachename . '.jpg';

          if (!is_wp_error($thumbnail)) {
            $thumbsize = $thumbnail->get_size();

            if (($thumbsize['width'] !== $thumbsize['height']) && $c === true) {
              $thumbnail->resize($thumbsize['height'], $thumbsize['height'], true);
              $thumbnail->save(OUTOFTHEBOX_CACHEDIR . $cachename . '.jpg', 'image/jpeg');
              $thumbnail->save(OUTOFTHEBOX_CACHEDIR . $cachename . '-thumb.jpg', 'image/jpeg');
              $thumb_file = $cachename . '-thumb.jpg';
            } else {
              $thumbnail->resize(NULL, $thumbsize['height'], false);
              $thumbnail->save(OUTOFTHEBOX_CACHEDIR . $cachename . '-thumb.jpg', 'image/jpeg');
              $thumb_file = $cachename . '-thumb.jpg';
            }
          }

          $this->_lockCache();
          $this->cache['images'][$cachename][$index]['crop'] = '1';
          $this->cache['images'][$cachename][$index]['thumb'] = $cachename . '.jpg';
          $this->cache['images'][$cachename][$index]['load_thumb'] = $thumb_file;
          $this->cache['images'][$cachename][$index]['width'] = $thumbsize['width'];
          $this->cache['images'][$cachename][$index]['height'] = $thumbsize['height'];
          $this->_setCache();

          header('Location: ' . OUTOFTHEBOX_CACHEURL . $cachename . '.jpg');
          die();
        }

        $phpThumb->resetObject();
        //Create cropped thumbnail for first run
        $phpThumb = new phpThumb();
        $phpThumb->setParameter('config_cache_directory', OUTOFTHEBOX_CACHEDIR);
        $phpThumb->setSourceData($imagedata);
        $phpThumb->setParameter('config_output_format', 'jpeg');

        $phpThumb->setParameter('q', 92);
        $phpThumb->setParameter('zc', 1);
        $phpThumb->setParameter('h', $this->cache['images'][$cachename][$index]['height']);
        $phpThumb->setParameter('w', $this->cache['images'][$cachename][$index]['height']);


        if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
          $phpThumb->SetCacheFilename();
          $phpThumb->RenderToFile($phpThumb->cache_filename);

          $_cachefile_location = $phpThumb->cache_filename;
          $_locations = (explode(DIRECTORY_SEPARATOR, $_cachefile_location));
          $this->_lockCache();
          $this->cache['images'][$cachename][$index]['load_thumb'] = end($_locations);
          $this->_setCache();

          header('Location: ' . OUTOFTHEBOX_CACHEURL . end($_locations));
          die();
        } else {
          die();
        }
      } catch (Exception $e) {
        die();
      }
    } else {
      die();
    }
  }

  protected function _mediaFromCache($entry, $type = 'media') {
    $this->_lockCache();
    if (!isset($this->cache[$type])) {
      $this->cache[$type] = array();
    }

    $cachename = sha1($entry);

    if ((!isset($this->cache[$type][$cachename])) || (($this->cache[$type][$cachename]['expires']) < time()) || (!isset($this->cache[$type][$cachename]['tokens']))) {
      list($link, $expires) = $this->client->createTemporaryDirectLink($entry);

      if ($link !== null) {

        $expires->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $expiresstr = $expires->getTimestamp();

        $this->cache[$type][$cachename] = array(
            'url' => $link,
            'expires' => $expiresstr,
            'tokens' => array()
        );
      }
    }

    if ($type === 'media') {
      //Remove old media tokens
      foreach ($this->cache[$type][$cachename]['tokens'] as $token => $expires) {
        if ($expires < time()) {
          unset($this->cache[$type][$cachename]['tokens'][$token]);
        }
      }

      //Create new media token for 15 minutes
      $this->cache[$type][$cachename]['tokens'][uniqid(rand())] = time() + 15 * 60;
    }

    $this->_setCache();
    return array('cachename' => $cachename, 'cache' => $this->cache[$type][$cachename]);
  }

  protected function _setCache() {
    $json_options = 0;
    if (defined('JSON_PRETTY_PRINT')) {
      $json_options |= JSON_PRETTY_PRINT;  // Supported in PHP 5.4+
    }

    $data = json_encode($this->cache, $json_options);

    if (!is_writable(OUTOFTHEBOX_CACHEDIR)) {
      $this->removeLock();
      return false;
//return new WP_Error('broke', __("Cache directory isn't writeable" . ". " . OUTOFTHEBOX_CACHEDIR, 'outofthebox'));
    }

    $result = @file_put_contents(OUTOFTHEBOX_CACHEDIR . 'index', $data);

    if ($result === false) {
      $this->removeLock();
      return false;
//return new WP_Error('broke', __("Cache index file isn't writeable" . ". " . OUTOFTHEBOX_CACHEDIR . 'index', 'outofthebox'));
    }

    $this->removeLock();
    return true;
  }

  protected function _lockCache() {
    if ($this->isLocked()) {
      // some other process is writing to this file too, wait until it's done to prevent hiccups
      $this->waitForLock();
    }
    $this->_readCache();
    $this->createLock();
  }

  protected function _readCache() {
    $cache = array();
    if (!is_readable(OUTOFTHEBOX_CACHEDIR . 'index')) {
      @file_put_contents(OUTOFTHEBOX_CACHEDIR . 'index', json_encode(array()));

      if (!is_readable(OUTOFTHEBOX_CACHEDIR . 'index')) {
//new WP_Error('broke', __("Couldn't create cache index file" . ". " . OUTOFTHEBOX_CACHEDIR . 'index', 'outofthebox'));
      }
    } else {

      $cache_file = @file_get_contents(OUTOFTHEBOX_CACHEDIR . 'index');

      if ($cache_file === false) {
//return new WP_Error('broke', __("Couldn't read cache index file" . ". " . OUTOFTHEBOX_CACHEDIR . 'index', 'outofthebox'));
      } else {

        $cache = json_decode($cache_file, true);

        if ($cache === null) {
//Reset index file
          file_put_contents(OUTOFTHEBOX_CACHEDIR . 'index', json_encode(array()));
          $cache = array();
        }
      }
    }
    $this->cache = $cache;

    $this->_cleanCache();

    return $this->cache;
  }

  /*
   * Clean Cache
   */

  private function _cleanCache() {
    /*
      $cachefiles = array_diff(scandir(OUTOFTHEBOX_CACHEDIR), array('..', '.', '.htaccess', 'index', 'index.lock'));

      foreach ($cachefiles as $cachefile) {

      $splitcachename = explode("-", $cachefile);
      $itemname = $splitcachename[0];
      $itemsize = $splitcachename[1];

      if ((!isset($this->cache['images'][$itemname])) || (!isset($this->cache['images'][$itemname][$itemsize]))) {
      unlink(OUTOFTHEBOX_CACHEDIR . '/' . $cachefile);
      }
      } */
  }

  private function isLocked() {
    // our lock file convention is simple: /the/file/path.lock
    return file_exists(OUTOFTHEBOX_CACHEDIR . 'index' . '.lock');
  }

  private function createLock() {
    @touch(OUTOFTHEBOX_CACHEDIR . 'index' . '.lock');
  }

  private function removeLock() {
    // suppress all warnings, if some other process removed it that's ok too
    @unlink(OUTOFTHEBOX_CACHEDIR . 'index' . '.lock');
    $this->_readCache();
  }

  private function waitForLock() {
    // 20 x 50000ms = 10 seconds
    $tries = 20;
    $cnt = 0;
    do {
      // make sure PHP picks up on file changes. This is an expensive action but really can't be avoided
      clearstatcache();
      // 250 ms is a long time to sleep, but it does stop the server from burning all resources on polling locks..
      usleep(50000);
      $cnt++;
    } while ($cnt <= $tries && $this->isLocked());
    if ($this->isLocked()) {
      usleep(5000000);
      // 5 seconds passed, assume the owning process died off and remove it
      $this->removeLock();
    }
  }

}