<div class="fileupload-container" style="width:<?php echo $this->options['maxwidth']; ?>;max-width:<?php echo $this->options['maxwidth']; ?>">
  <div>
    <form id="fileupload-<?php echo $this->listtoken; ?>" class="fileuploadform" method="POST" enctype="multipart/form-data" data-token='<?php echo $this->listtoken; ?>'>
      <input type="hidden" name="acceptfiletypes" value="<?php echo $acceptfiletypes; ?>">
      <div class="fileupload-buttonbar">
        <span class="fileinput-button">
          <span><?php _e('Add files', 'outofthebox'); ?></span>
          <input type="file" name="files[]" multiple="multiple" class='fileupload-browse-button'>
        </span>
        <button type="submit" class="start">
          <span><?php _e('Start upload', 'outofthebox'); ?></span>
        </button>
        <button type="reset" class="cancel">
          <span><?php _e('Cancel upload', 'outofthebox'); ?></span>
        </button>
        <span class="filesize"><?php _e('Max. ', 'outofthebox'); ?> <span><?php echo OutoftheBox_bytesToSize1024($this->options['maxfilesize']) ?></span></span>
      </div>
      <div class='fileupload-list'>
        <div role="presentation">
          <div class="files">&nbsp;</div>

        </div>
      </div>
    </form>
  </div>
</div>