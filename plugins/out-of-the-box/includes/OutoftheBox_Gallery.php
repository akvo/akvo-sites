<?php

require_once 'OutoftheBox_Dropbox.php';

class OutoftheBox_Gallery extends OutoftheBox_Dropbox {

  private $_search = false;

  public function getImagesList() {
    $this->_folder = $this->getFolder();

    if (($this->_folder !== false)) {
      /* Create Gallery array */
      $this->imagesarray = $this->createImageArray();

      $this->renderImagesList();
    }
  }

  public function searchImageFiles() {
    $this->_search = true;
    $input = mb_strtolower($_REQUEST['query'], 'UTF-8');
    $this->_folder = array();
    $this->_folder['contents'] = $this->searchByName($input);

    if (($this->_folder !== false)) {
      /* Create Gallery array */
      $this->imagesarray = $this->createImageArray();

      $this->renderImagesList();
    }
  }

  public function renderImagesList() {

    /* Create HTML Filelist */
    $imageslist_html = "";

    if (count($this->imagesarray) > 0) {
      $imageslist_html = "<div class='images image-collage'>";
      foreach ($this->imagesarray as $item) {
        /* Render folder div */
        if ($item['is_dir']) {
          $imageslist_html .= $this->renderDir($item);
        }
      }

      $imageslist_html .= $this->renderNewFolder();


      $i = 0;
      foreach ($this->imagesarray as $item) {

        /* Render file div */
        if (!$item['is_dir']) {
          $hidden = (($this->options['maximages'] !== '0') && ($i >= $this->options['maximages']));
          $imageslist_html .= $this->renderFile($item, $hidden);
          $i++;
        }
      }

      $imageslist_html .= "</div>";
    } else {
      if ($this->_search === true) {
        $imageslist_html .= '<div class="no_results">' . __('No files or folders found', 'outofthebox') . '</div>';
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
      $startelement = "<a href='javascript:void(0)' class='folder' data-url='" . urlencode('/') . "'>" . $this->options['root_text'] . "</a>";
      array_unshift($breadcrumbelements, $startelement);
    }

    $filepath = implode($spacer, $breadcrumbelements);

    if ($this->_search === true) {
      $expires = 0;
    } else {
      $expires = time() + 60 * 5;
    }

    $nextimages = '';
    if (($this->options['maximages'] !== '0') && (count($this->imagesarray) > $this->options['maximages'])) {
      $nextimages = '<div class="nextimages" data-loadimages="' . $this->options['maximages'] . '"><a><i class="fa fa-play-circle"></i>&nbsp;' . __('Load more...', 'outofthebox') . '</a></div>';
    }

    echo json_encode(array(
        'lastpath' => urlencode($this->_lastPath),
        'breadcrumb' => $filepath,
        'html' => $imageslist_html . $nextimages,
        'expires' => $expires));

    die();
  }

  public function renderDir($item) {
    $return = "";
    if ($item['parentfolder'] === true) {
      $return .= "<div class='image-container image-folder' data-url='" . urlencode($item['path']) . "' data-name='" . $item['basename'] . "'>";
    } else {
      $return .= "<div class='image-container image-folder entry ' data-url='" . urlencode($item['path']) . "' data-name='" . $item['basename'] . "'>";

      $return .= "<div class='entry_edit'>";
      $return .= $this->renderEditItem($item);

      if (($this->options['can_download_zip'] === '1') && ($this->checkUserRole($this->options['download_role']))) {
        $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='" . urlencode($item['path']) . "'/></div>";
      }
      $return .= "</div>";
    }
    $return .= "<a title='" . $item['name'] . "'>";
    $return .= "<img class='preloading image-folder-img' src='" . OUTOFTHEBOX_ROOTPATH . "/css/images/transparant.png' data-src='" . plugins_url('css/images/folder.png', dirname(__FILE__)) . "' width='" . $item['width'] . "' height='" . $item['height'] . "' style='width:" . $item['width'] . "px;height:" . $item['height'] . "px;'/>";

    if (count($item['folderimages']) > 0) {
      $size = $this->options['targetheight'];
      $number = 1;

      foreach (array_reverse($item['folderimages']) as $folderimage) {
        $img = (!empty($folderimage['thumb'])) ? OUTOFTHEBOX_CACHEURL . $folderimage['thumb'] : $folderimage['create_thumb_url'];
        $return .= "<div class='folder-thumb thumb$number' style='width:" . $size . "px;height:" . $size . "px;background-image: url(" . $img . ")'></div>";
        $number++;
      }
    }

    $return .= "<div class='folder'>" . $item['name'] . "</div></a>";

    $return .= "</div>\n";

    return $return;
  }

  public function renderFile($item, $hidden = false) {


    $hiddenclass = ($hidden) ? 'hidden' : '';
    $class = $hiddenclass;

    if ((!empty($_REQUEST['deeplink'])) && (md5($item['url']) === $_REQUEST['deeplink'])) {
      $class .= ' deeplink';
    }

    $return = "<div class='image-container $class entry' data-url='" . urlencode($item['path']) . "' data-name='" . $item['name'] . "'>";

    $return .= "<div class='entry_edit'>";
    $return .= $this->renderEditItem($item);

    if (($this->options['can_download_zip'] === '1') && ($this->checkUserRole($this->options['download_role']))) {
      $return .= "<div class='entry_checkbox'><input type='checkbox' name='selected-files[]' class='selected-files' value='" . urlencode($item['path']) . "'/></div>";
    }
    $return .= "</div>";


    $return .= "<a href='" . $item['url'] . "' title='" . $item['name'] . "' class='colorbox-group'><span class='image-rollover'></span>";
    $return .= "<img class='preloading $hiddenclass' src='" . OUTOFTHEBOX_ROOTPATH . "/css/images/transparant.png' data-src='" . $item['thumb'] . "' width='" . $item['width'] . "' height='" . $item['height'] . "' style='width:" . $item['width'] . "px;height:" . $item['height'] . "px;'/>";
    $return .= "</a>";

    $return .= "</div>\n";
    return $return;
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
        $height = $this->options['targetheight'];
        $html .= "<div class='image-container image-folder image-add-folder grey newfolder'>";
        $html .= "<a title='" . __('Add folder', 'outofthebox') . "'><div class='folder'>" . __('Add folder', 'outofthebox') . "</div>";
        $html .= "<img class='preloading' data-original='" . plugins_url('css/images/addfolder.png', dirname(__FILE__)) . "' width='$height' height='$height' style='width:" . $height . "px;height:" . $height . "px;'/>";
        $html .= "</a>";
        $html .= "</div>\n";
      }
    }
    return $html;
  }

  public function createImageArray() {
    $imagearray = array();

    //Add folders and files to filelist


    if (count($this->_folder['contents']) > 0) {

      foreach ($this->_folder['contents'] as $child) {

        $path = $child['path'];
        $path_parts = OutoftheBox_mbPathinfo($path);

        //Only add allowed files to array
        if ((isset($path_parts['extension']) && !in_array(strtolower($path_parts['extension']), $this->options['ext'])) && $this->options['ext'] [0] != '*') {
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

        if ($child['is_dir'] === true) {
          //Read folder for possible images
          $foldercontents = $this->getFolderImages($path);
          $folderimages = array();

          $foldercontents = $this->removeElementWithValue($foldercontents, 'is_dir', true);

          if (count($foldercontents) > 0) {
            //Process only the first three
            $foldercontents = array_slice($foldercontents, 0, 3);

            foreach ($foldercontents as $folderimage) {
              $image = $this->_imageFromCache($folderimage, 'folderthumb');
              $folderimages[] = $image['cache'];
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

          array_push($imagearray, array(
              'id' => md5($location),
              'name' => $path_parts['filename'],
              'basename' => $path_parts['basename'],
              'path' => $location,
              'is_dir' => true,
              'thumb' => plugins_url('css/images/folder.png', dirname(__FILE__)),
              'width' => $this->options['targetheight'],
              'height' => $this->options['targetheight'],
              'folderimages' => $folderimages,
              'parentfolder' => false
          ));
          continue;
        }


        //add files with thumbnails
        if ($child['thumb_exists'] === true) {

          $image = $this->_imageFromCache($child);
          $location = ($this->_lastPath == '/') ? '/' . $path_parts['basename'] : $this->_lastPath . '/' . $path_parts['basename'];

          array_push($imagearray, array(
              'id' => md5($location),
              'name' => $path_parts['filename'],
              'basename' => $path_parts['basename'],
              'path' => $location,
              'is_dir' => false,
              'url' => (empty($image['image']['url'])) ? $image['cache']['create_thumb_url'] : $image['image']['url'] . '?dl=1',
              'thumb' => (!empty($image['cache']['thumb'])) ? OUTOFTHEBOX_CACHEURL . $image['cache']['thumb'] : $image['cache']['create_thumb_url'],
              'width' => $image['cache']['width'],
              'height' => $image['cache']['height']
          ));
        }
      }
    }

    return $imagearray;
  }

}