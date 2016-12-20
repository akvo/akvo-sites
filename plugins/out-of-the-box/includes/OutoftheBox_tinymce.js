(function() {
  tinymce.create('tinymce.plugins.outofthebox', {
    init: function(ed, url) {
      ed.addCommand('mceOutoftheBox', function() {
        ed.windowManager.open({
          file: ajaxurl + '?action=outofthebox-getpopup',
          width: 800,
          height: 600,
          inline: 1
        }, {
          plugin_url: url
        });
      });
      ed.addCommand('mceOutoftheBox_links', function() {
        ed.windowManager.open({
          file: ajaxurl + '?action=outofthebox-getpopup&type=links',
          width: 800,
          height: 400,
          inline: 1
        }, {
          plugin_url: url
        });
      });
      ed.addCommand('mceOutoftheBox_embedded', function() {
        ed.windowManager.open({
          file: ajaxurl + '?action=outofthebox-getpopup&type=embedded',
          width: 800,
          height: 400,
          inline: 1
        }, {
          plugin_url: url
        });
      });
      ed.addButton('outofthebox', {
        title: 'Out-of-the-Box shortcode',
        image: url + '/../css/images/dropbox_logo_blue.png',
        cmd: 'mceOutoftheBox'
      });
      ed.addButton('outofthebox_links', {
        title: 'Out-of-the-Box links',
        image: url + '/../css/images/dropbox_logo_blue_link.png',
        cmd: 'mceOutoftheBox_links'
      });
      ed.addButton('outofthebox_embedded', {
        title: 'Embed files from Dropbox',
        image: url + '/../css/images/dropbox_logo_blue_embedded.png',
        cmd: 'mceOutoftheBox_embedded'
      });
    },
    createControl: function(n, cm) {
      return null;
    }
  });

  tinymce.PluginManager.add('outofthebox', tinymce.plugins.outofthebox);

})();