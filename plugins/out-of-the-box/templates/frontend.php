<div class="list-container" style="width:<?php echo $this->options['maxwidth']; ?>;max-width:<?php echo $this->options['maxwidth']; ?>">
  <?php if ($this->options['show_breadcrumb'] === '1' || $this->options['search'] === '1') { ?>
    <div class="nav-header">
      <?php if ($this->options['show_breadcrumb'] === '1') { ?>
        <a class="nav-home" title="<?php _e('Back to our first folder', 'outofthebox'); ?>">
          <i class="fa fa-home pull-left"></i>
        </a>
        <a class="nav-refresh" title="<?php _e('Refresh', 'outofthebox'); ?>">
          <i class="fa fa-refresh pull-right"></i>
        </a>
        <?php
      }
      if (($this->options['can_download_zip'] === '1') && ($this->checkUserRole($this->options['download_role']))) {
        ?>
        <a class="download-zip" title="<?php _e('Download multiple files at once', 'outofthebox'); ?>">
          <i class="fa fa-download pull-right"></i>
        </a>
        <div class="download-zip-menu" data-token="<?php echo $this->listtoken; ?>">
          <ul>
            <li><a class="all-files-to-zip"><i class='fa fa-cloud-download fa-lg'></i><?php _e('Download all files', 'outofthebox'); ?> (.zip)</a></li>
            <li><a class="selected-files-to-zip"><i class='fa fa-cloud-download fa-lg'></i><?php _e('Download selected files', 'outofthebox'); ?> (.zip)</a></li>
            <li><a class="no-files-zip-menu"><i class='fa fa-exclamation-circle fa-lg'></i><?php _e('No files found or selected', 'outofthebox'); ?> (.zip)</a></li>
          </ul>
        </div>
        <?php
      }
      ?>
      <?php if ($this->options['search'] === '1') { ?>
        <a class="nav-search">
          <i class="fa fa-search pull-right"></i>
        </a>

        <div class="search-div">
          <div class="search-remove"><i class="fa fa-times-circle fa-lg"></i></div>
          <input name="q" type="text" size="40" placeholder="<?php echo __('Search filenames', 'outofthebox'); ?>" class="search-input" />
        </div>
      <?php }; ?>
      <?php if ($this->options['show_breadcrumb'] === '1') { ?>
        <div class="nav-title"><?php _e('Loading...', 'outofthebox'); ?></div>
      <?php }; ?>
    </div>
  <?php } ?>
  <?php if ($this->options['show_columnnames'] === '1') { ?>
    <div class='column_names'>
      <div class='entry_icon'></div>
      <div class='entry_name sortable <?php echo ($this->options['sort_field'] === 'name') ? $this->options['sort_order'] : ''; ?>' data-sortname="name"><a class='entry_sort'><?php _e('Name', 'outofthebox'); ?></a><span class="sort_icon">&nbsp;</span></div>
      <?php if (($this->options['can_download_zip'] === '1') && ($this->checkUserRole($this->options['download_role']))) { ?>
        <div class='entry_checkallbox'><input type='checkbox' name='select-all-files' class='select-all-files'/></div>
        <?php
      };
      ?>
      <div class='entry_edit'>&nbsp;</div>
      <?php
      if ($this->options['show_filesize'] === '1') {
        ?>
        <div class='entry_size sortable <?php echo ($this->options['sort_field'] === 'size') ? $this->options['sort_order'] : ''; ?>' data-sortname="size"><span class="sort_icon">&nbsp;</span><a class='entry_sort'><?php _e('Size', 'outofthebox'); ?></a></div>
        <?php
      };

      if ($this->options['show_filedate'] === '1') {
        ?>
        <div class='entry_lastedit sortable <?php echo ($this->options['sort_field'] === 'modified') ? $this->options['sort_order'] : ''; ?>' data-sortname="modified"><a class='entry_sort'><?php _e('Date modified', 'outofthebox'); ?></a><span class="sort_icon">&nbsp;</span></div>
        <?php
      };
      ?>

    </div>
  <?php }; ?>
  <div class="loading initialize">&nbsp;</div>
  <div class="ajax-filelist">&nbsp;</div>
</div>