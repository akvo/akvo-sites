<?php

require_once 'OutoftheBox_Dropbox.php';

class OutoftheBox_Filebrowser extends OutoftheBox_Dropbox {

  private $_search = false;

  public function getFilesList() {

    $this->_folder = $this->getFolder();

    if (($this->_folder !== false)) {
      $this->filesarray = $this->createFilesArray();

      $this->renderFilelist();
    }
  }

  public function searchFiles() {
    $this->_search = true;
    $input = mb_strtolower($_REQUEST['query'], 'UTF-8');
    $this->_folder = array();
    $this->_folder['contents'] = $this->searchByName($input);

    if (($this->_folder !== false)) {
      $this->filesarray = $this->createFilesArray();

      $this->renderFilelist();
    }
  }

  public function renderFilelist() {

    /* Create HTML Filelist */
    $filelist_html = "";

    if (count($this->filesarray) > 0) {
      $hasfilesorfolders = false;

      foreach ($this->filesarray as $item) {
        /* Render folder div */
        if ($item['is_dir']) {
          $filelist_html .= $this->renderDir($item);


          if ($item['parentfolder'] === false) {
            $hasfilesorfolders = true;
          }
        }
      }

      $filelist_html .= $this->renderNewFolder();


      foreach ($this->filesarray as $item) {
        /* Render files div */
        if (!$item['is_dir']) {
          $filelist_html .= $this->renderFile($item);
          $hasfilesorfolders = true;
        }
      }

      if ($hasfilesorfolders === false) {
        if ($this->options['show_files'] === '1') {
          $filelist_html .= $this->renderNoResults();
        }
      }
    } else {
      if ($this->options['show_files'] === '1' || $this->_search === true) {
        $filelist_html .= $this->renderNoResults();
      }
    }

    /* Create HTML Filelist title */
    $spacer = ' &raquo; ';

    $breadcrumbelements = array_filter(explode('/', $this->_requestedPath));

    $location = '';
    foreach ($breadcrumbelements as &$element) {
      $location .= '/' . $element;
      $class = 'folder';
      if (basename($this->_requestedPath) == $element) {
        $class .= ' current_folder';
      }
      $element = "<a href='javascript:void(0)' class='" . $class . "' data-url='" . urlencode($location) . "'>" . $element . "</a>";
    }

    if (($this->options['show_root'] === '1') && ($this->_rootFolder != '/')) {
      $startelement = "<a href='javascript:void(0)' class='folder' data-url='" . urlencode('/') . "'>" . ltrim($this->_rootFolder, '/') . "</a>";
      array_unshift($breadcrumbelements, $startelement);
    } else {
      if ($this->_userFolder !== false) {
        $startelement = "<a href='javascript:void(0)' class='folder' data-url='" . urlencode('/') . "'>" . $this->_userFolder . "</a>";
      } else {
        $startelement = "<a href='javascript:void(0)' class='folder' data-url='" . urlencode('/') . "'>" . $this->options['root_text'] . "</a>";
      }

      array_unshift($breadcrumbelements, $startelement);
    }

    $filepath = implode($spacer, $breadcrumbelements);

    $raw_path = '';
    if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing') == 'true') {
      $raw_path = $this->_requestedCompletePath;
    }


    if ($this->_search === true) {
      $expires = 0;
    } else {
      $expires = time() + 60 * 5;
    }

    echo json_encode(array(
        'lastpath' => urlencode($this->_lastPath),
        'rawpath' => $raw_path,
        'breadcrumb' => $filepath,
        'html' => $filelist_html,
        'expires' => $expires));

    die();
  }

  public function renderNoResults() {

    $html = '<div class="entry folder">
<div class="entry_icon">
<img src="' . OUTOFTHEBOX_ROOTPATH . '/css/clouds/cloud_status_16.png" ></div>
<div class="entry_name"><a class="entry_link">' . __('No files or folders found', 'outofthebox') . '</a></div></div>
';

    return $html;
  }

  public function renderDir($item) {
    $return = '';
    $return .= "<div class='entry folder' data-url='" . urlencode($item['path']) . "' data-name='" . $item['basename'] . "'>\n";
    $return .= "<div class='entry_icon " . $item['icon'] . "'></div>\n";

    if ($item['parentfolder'] === false) {
      $return .= "<div class='entry_name'><a class='entry_link'>" . $item['basename'] . "</a></div>";
    } else {
      $return .= "<div class='entry_name'><a class='entry_link'>" . $item['name'] . "</a></div>";
    }

    if (($this->options['can_download_zip'] === '1') && ($this->checkUserRole($this->options['download_role'])) && ($item['parentfolder'] === false)) {
      $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='" . urlencode($item['basename']) . "'/></div>";
    }

    if (($this->options['mcepopup'] === 'links') && ($item['parentfolder'] === false)) {
      $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='" . urlencode($item['basename']) . "'/></div>";
    }

    if ($item['parentfolder'] === false) {
      $return .= "<div class='entry_edit'>";
      $return .= $this->renderEditItem($item);
      $return .= "</div>";
    }

    $return .= "</div>\n";
    return $return;
  }

  public function renderFile($item) {
    $return = '';
    $return .= "<div class='entry file' data-url='" . urlencode($item['path']) . "' data-name='" . $item['name'] . "'>\n";
    $return .= "<div class='entry_icon " . $item['icon'] . "'></div>";

    $link = $this->renderFileNameLink($item);
    $return .= "<div class='entry_name'><a " . $link['url'] . " " . $link['target'] . " class='" . $link['class'] . "'>" . $link['filename'] . "</a>";

    if ($this->_search === true) {
      $return .= "<div class='entry_foundpath'>" . $item['path'] . "</div>";
    }

    $return .= "</div>";

    if (($this->options['can_download_zip'] === '1') && ($this->checkUserRole($this->options['download_role']))) {
      $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='" . urlencode($item['basename']) . "'/></div>";
    }

    if (in_array($this->options['mcepopup'], array('links', 'embedded'))) {
      $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='" . urlencode($item['basename']) . "'/></div>";
    }

    $return .= "<div class='entry_edit'>";
    $return .= $this->renderEditItem($item);
    $return .= "</div>";

    if ($this->options['show_filesize'] === '1') {
      $return .= "<div class='entry_size'>" . $item['size'] . "</div>";
    }

    if ($this->options['show_filedate'] === '1') {
      $edited = date_i18n(get_option('date_format') . ' H:s', strtotime($item['edited']));
      $return .= "<div class='entry_lastedit'>" . $edited . "</div>";
    }

    $return .= "</div>\n";

    return $return;
  }

  public function renderFileNameLink($item) {
    $class = '';
    $url = '';
    $target = '';

    /* Check if user is allowed to download file */
    if (($this->options['mcepopup'] === '0') && ($this->checkUserRole($this->options['download_role']))) {
      if ($this->options['forcedownload'] === '1') {
        $url = "?action=outofthebox-download&OutoftheBoxpath=" . urlencode($item['path']) . "&lastpath=" . urlencode($this->_lastPath) . "&listtoken=" . $this->listtoken;
        $class = 'entry_link';
      } else {

        $forcedownload = '0';
        if (in_array($item['extension'], array('pdf', 'txt'))) {
          $class = 'entry_link colorbox-inline-group';
        } elseif (in_array($item['extension'], array('jpg', 'jpeg', 'gif', 'png'))) {
          $class = 'entry_link colorbox-group';
          $forcedownload = '1';
        }
        $url = "?action=outofthebox-download&OutoftheBoxpath=" . urlencode($item['path']) . "&lastpath=" . urlencode($this->_lastPath) . "&listtoken=" . $this->listtoken . "&dl=" . $forcedownload;

        if ($this->mobile) {
          $class = '';
        }
      }
    }

    $filename = $item['name'];
    $filename .= (($this->options['show_ext'] === '1' && !empty($item['extension'])) ? '.' . $item['extension'] : '');

    if (!empty($url)) {
      $url = "href='" . $url . "'";
    };
    if (!empty($target)) {
      $url = "target='" . $target . "'";
    };

    return array('filename' => $filename, 'class' => $class, 'url' => $url, 'target' => $target);
  }

  public function renderEditItem($item) {
    $html = '';

    if ($item['is_dir']) {
      $usercanrename = ($this->checkUserRole($this->options['renamefolders_role']));
      $usercandelete = ($this->checkUserRole($this->options['deletefolders_role']));
    } else {
      $usercanrename = ($this->checkUserRole($this->options['renamefiles_role']));
      $usercandelete = ($this->checkUserRole($this->options['deletefiles_role']));
    }

    /* View */
    if ($this->options['forcedownload'] !== '1' && (!$item['is_dir'])) {
      if (in_array($item['extension'], array('pdf', 'txt', 'jpg', 'jpeg', 'gif', 'png'))) {
        $html .= "<li><a class='entry_action_view' title='" . __('Preview', 'outofthebox') . "'><i class='fa fa-desktop fa-lg'></i>&nbsp;" . __('Preview', 'outofthebox') . "</a></li>";
      }
    }

    /* Download */
    if (!$item['is_dir']) {
      $html .= "<li><a href='?action=outofthebox-download&OutoftheBoxpath=" . urlencode($item['path']) . "&lastpath=" . urlencode($this->_lastPath) . "&listtoken=" . $this->listtoken . "&dl=1' class='entry_action_download' title='" . __('Download file', 'outofthebox') . "'><i class='fa fa-cloud-download fa-lg'></i>&nbsp;" . __('Download file', 'outofthebox') . "</a></li>";
    }

    /* Shortlink */
    if (!$item['is_dir']) {
      if (($this->options['show_sharelink'] === '1') && ($this->checkUserRole($this->options['download_role']))) {
        $html .= "<li><a class='entry_action_shortlink' title='" . __('Sharing link', 'outofthebox') . "'><i class='fa fa-group fa-lg'></i>&nbsp;" . __('Sharing link', 'outofthebox') . "</a></li>";
      }
    }

    /* Rename */
    if (($this->options['rename'] === '1') && ($usercanrename)) {
      $html .= "<li><a class='entry_action_rename' title='" . __('Rename', 'outofthebox') . "'><i class='fa fa-tag fa-lg'></i>&nbsp;" . __('Rename', 'outofthebox') . "</a></li>";
    }

    /* Delete */
    if (($this->options['delete'] === '1') && ($usercandelete)) {
      $html .= "<li><a class='entry_action_delete' title='" . __('Delete', 'outofthebox') . "'><i class='fa fa-times-circle fa-lg'></i>&nbsp;" . __('Delete', 'outofthebox') . "</a></li>";
    }

    if ($html !== '') {
      return "<a class='entry_edit_menu'><i class='fa fa-toggle-down'></i></a><div id='menu-" . $item['id'] . "' class='oftb-dropdown-menu'><ul data-path='" . urlencode($item['path']) . "' data-name='" . $item['basename'] . "'>" . $html . "</ul></div>\n";
    }

    return $html;
  }

  public function renderNewFolder() {
    $html = '';
    if (($this->_search === false) && ($this->options['addfolder'] === '1')) {
      $user_can_add_folder = $this->checkUserRole($this->options['addfolder_role']);

      if ($user_can_add_folder) {
        $html .= "<div class='entry folder newfolder'>";
        $html .= "<div class='entry_icon'><span class='ui-icon ui-icon-plusthick'></span></div>";
        $html .= "<div class='entry_name'>" . __('Add folder', 'outofthebox') . "</div>";
        $html .= "<div class='entry_description'>" . __('Add a new folder in this directory', 'outofthebox') . "</div>";
        $html .= "</div>";
      }
    }
    return $html;
  }

  public function createFilesArray() {
    $filesarray = array();

// Add 'back to Previous folder' if needed

    if (($this->_search === false) && ($this->_folder['path'] !== $this->_rootFolder)) {
      $foldername = basename($this->_folder['path']);
      $location = str_replace('\\', '/', (dirname($this->_requestedPath)));
      array_push($filesarray, array(
          'id' => md5($location),
          'name' => ' <strong>' . __('Previous folder', 'outofthebox') . '</strong>',
          'basename' => $foldername,
          'path' => $location,
          'icon' => 'folder_gray',
          'is_dir' => true,
          'parentfolder' => true
      ));
    }

//Add folders and files to filelist
    if (count($this->_folder['contents']) > 0) {

      foreach ($this->_folder['contents'] as $child) {

//Skip entry if its a file, and we dont want to show files
        if (($child['is_dir'] === false) && ($this->options['show_files'] === '0')) {
          continue;
        }

        $path = $child['path'];

        $path_parts = OutoftheBox_mbPathinfo($path);

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

        if ($this->_search === true) {
          if ($this->_rootFolder !== '/') {
            $pathreg = str_replace('/', '\/', $this->_rootFolder);
            $location = preg_replace('/' . $pathreg . '/', '', $path_parts['dirname'], 1);
          } else {
            $location = $path_parts['dirname'];
          }
          $location = $location . '/' . $path_parts['basename'];
        } else {
          $location = ($this->_lastPath == '/') ? '/' . $path_parts['basename'] : $this->_lastPath . '/' . $path_parts['basename'];
        }

        $extension = (isset($path_parts['extension'])) ? $path_parts['extension'] : '';

        array_push($filesarray, array(
            'id' => md5($location),
            'name' => $path_parts['filename'],
            'basename' => $path_parts['basename'],
            'extension' => strtolower($extension),
            'path' => $location,
            'icon' => $child['icon'],
            'is_dir' => $child['is_dir'],
            'size' => $child['size'],
            'edited' => (isset($child['client_mtime']) && (strtotime($child['client_mtime']) > strtotime($child['modified']))) ? $child['client_mtime'] : $child['modified'],
            'parentfolder' => false
        ));
      }
    }

    return $filesarray;
  }

}