jQuery(document).ready(function($) {
  'use strict';

  /* Fix for not scrolling popup*/
  if (/Android|webOS|iPhone|iPod|iPad|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    var parent = $(tinyMCEPopup.getWin().document);

    if (parent.find('#safari_fix').length === 0) {
      parent.find('.mceWrapper iframe').wrap(function() {
        var $this = $(this);
        return $('<div id="safari_fix"/>').css({
          'width': "100%",
          'height': "100%",
          'overflow': 'auto',
          '-webkit-overflow-scrolling': 'touch'
        });
      });
    }
  }

  $('.option').hide();
  $('.option.forfilebrowser').show();

  $('input:checkbox:not(.simple)').radiobutton({
    className: 'jquery-switch',
    checkedClass: 'jquery-switch-on'
  });

  $('input:radio').radiobutton();

  $("input[name=mode]:radio").change(function() {
    $('.mode-image').attr("src", OutoftheBox_vars.plugin_url + "/css/images/mode-" + $(this).val() + ".png");

    $('.option').hide();

    switch ($(this).val()) {
      case 'files':
        $('.option.forfilebrowser').show();
        $('#OutoftheBox_upload_ext, #OutoftheBox_ext').val('');
        break;

      case 'gallery':
        $('.option.forgallery').show();
        $('#OutoftheBox_upload_ext, #OutoftheBox_ext').val('gif|jpg|jpeg|png|bmp');
        break;

      case 'audio':
        $('.option.foraudio').show();
        break;

      case 'video':
        $('.option.forvideo').show();
        break;
    }
  });

  $("#OutoftheBox_breadcrumb, #OutoftheBox_upload, #OutoftheBox_rename, #OutoftheBox_delete, #OutoftheBox_addfolder, #OutoftheBox_user_folders").change(function() {
    var toggleelement = '.' + $(this).attr('data-div-toggle');
    if ($(this).is(":checked")) {
      $(toggleelement).show();
    } else {
      $(toggleelement).hide();
    }
  });

  $(".OutoftheBox .insert_links").click(createDirectLinks);
  $('.OutoftheBox .insert_embedded').click(insertEmbedded);
  $('.OutoftheBox .insert_shortcode').click(insertOutoftheBoxShortCode);

  function insertOutoftheBoxShortCode() {

    var dir = $('.current-folder-raw').text(),
            show_files = $('#OutoftheBox_showfiles').prop("checked"),
            ext = $('#OutoftheBox_ext').val(),
            show_filesize = $('#OutoftheBox_filesize').prop("checked"),
            show_filedate = $('#OutoftheBox_filedate').prop("checked"),
            show_ext = $('#OutoftheBox_showext').prop("checked"),
            show_columnnames = $('#OutoftheBox_showcolumnnames').prop("checked"),
            candownloadzip = $('#OutoftheBox_candownloadzip').prop("checked"),
            showsharelink = $('#OutoftheBox_showsharelink').prop("checked"),
            show_breadcrumb = $('#OutoftheBox_breadcrumb').prop("checked"),
            breadcrumb_roottext = $('#OutoftheBox_roottext').val(),
            show_root = $('#OutoftheBox_rootname').prop("checked"),
            search = $('#OutoftheBox_search').prop("checked"),
            force_download = $('#OutoftheBox_forcedownload').prop("checked"),
            include = $('#OutoftheBox_include').val(),
            exclude = $('#OutoftheBox_exclude').val(),
            sort_field = $("input[name=sort_field]:radio:checked").val(),
            sort_order = $("input[name=sort_order]:radio:checked").val(),
            crop = $('#OutoftheBox_crop').prop("checked"),
            maximages = $('#OutoftheBox_maximage').val(),
            partial_lastrow = $('#OutoftheBox_allowPartialLastRow').prop("checked"),
            target_height = $('#OutoftheBox_targetHeight').val(),
            shuffle = $('#OutoftheBox_shuffle').prop("checked"),
            max_width = $('#OutoftheBox_max_width').val(),
            upload = $('#OutoftheBox_upload').prop("checked"),
            overwrite = $('#OutoftheBox_overwrite').prop("checked"),
            upload_ext = $('#OutoftheBox_upload_ext').val(),
            maxfilesize = $('#OutoftheBox_maxfilesize').val(),
            rename = $('#OutoftheBox_rename').prop("checked"),
            can_delete = $('#OutoftheBox_delete').prop("checked"),
            can_addfolder = $('#OutoftheBox_addfolder').prop("checked"),
            notification_download = $('#OutoftheBox_notificationdownload').prop("checked"),
            notification_upload = $('#OutoftheBox_notificationupload').prop("checked"),
            notification_emailaddress = $('#OutoftheBox_notification_email').val(),
            user_folders = $('#OutoftheBox_user_folders').prop("checked"),
            view_role = readCheckBoxes("input[name='OutoftheBox_view_role[]']"),
            download_role = readCheckBoxes("input[name='OutoftheBox_download_role[]']"),
            upload_role = readCheckBoxes("input[name='OutoftheBox_upload_role[]']"),
            renamefiles_role = readCheckBoxes("input[name='OutoftheBox_renamefiles_role[]']"),
            renamefolders_role = readCheckBoxes("input[name='OutoftheBox_renamefolders_role[]']"),
            deletefiles_role = readCheckBoxes("input[name='OutoftheBox_deletefiles_role[]']"),
            deletefolders_role = readCheckBoxes("input[name='OutoftheBox_deletefolders_role[]']"),
            addfolder_role = readCheckBoxes("input[name='OutoftheBox_addfolder_role[]']"),
            view_user_folders_role = readCheckBoxes("input[name='OutoftheBox_view_user_folders_role[]']"),
            mediaextensions = readCheckBoxes("input[name='OutoftheBox_mediaextensions[]']");


    var data = '';

    if (OutoftheBox_vars.shortcodeRaw === '1') {
      data += '[raw]';
    }

    data += '[outofthebox ';

    if (dir !== '/' && dir !== '') {
      data += 'dir="' + dir + '" ';
    }

    if (max_width !== '') {
      if (max_width.indexOf("px") !== -1 || max_width.indexOf("%") !== -1) {
        data += 'maxwidth="' + max_width + '" ';
      } else {
        data += 'maxwidth="' + parseInt(max_width) + '" ';
      }
    }

    data += 'mode="' + $("input[name=mode]:radio:checked").val() + '" ';

    if (ext !== '') {
      data += 'ext="' + ext + '" ';
    }

    if (include !== '') {
      data += 'include="' + include + '" ';
    }
    if (exclude !== '') {
      data += 'exclude="' + exclude + '" ';
    }

    if (view_role !== 'administrator|editor|author|contributor|subscriber|pending|guest') {
      data += 'viewrole="' + view_role + '" ';
    }

    if (sort_field !== 'name') {
      data += 'sortfield="' + sort_field + '" ';
    }

    if (sort_order !== 'asc') {
      data += 'sortorder="' + sort_order + '" ';
    }

    var mode = $("input[name=mode]:radio:checked").val();
    switch (mode) {
      case 'audio':
      case 'video':
        if (mediaextensions === '') {
          $("#OutoftheBox_mediaextension_div p").first().css("color", "red");
          $('html, body').animate({
            scrollTop: $("#OutoftheBox_mediaextension_div").offset().top
          }, 1000);
          return false;
        }

        data += 'mediaextensions="' + mediaextensions + '" ';
        break;

      case 'files':
      case 'gallery':
        if (mode === 'gallery') {
          if (maximages !== '') {
            data += 'maximages="' + maximages + '" ';
          }

          if (crop === true) {
            data += 'crop="1" ';
          }

          if (partial_lastrow === false) {
            data += 'partiallastrow="0" ';
          }

          if (shuffle === true) {
            data += 'shuffle="1" ';
          }

          if (target_height !== '') {
            data += 'targetheight="' + target_height + '" ';
          }
        }

        if (mode === 'files') {
          if (show_files === false) {
            data += 'showfiles="0" ';
          }

          if (show_filesize === false) {
            data += 'filesize="0" ';
          }

          if (show_filedate === false) {
            data += 'filedate="0" ';
          }

          if (show_ext === false) {
            data += 'showext="0" ';
          }

          if (force_download === true) {
            data += 'forcedownload="1" ';
          }

          if (show_columnnames === false) {
            data += 'showcolumnnames="0" ';
          }

          if (download_role !== 'administrator|editor|author|contributor|subscriber|pending|guest') {
            data += 'downloadrole="' + download_role + '" ';
          }
        }

        if (candownloadzip === true) {
          data += 'candownloadzip="1" ';
        }

        if (showsharelink === true) {
          data += 'showsharelink="1" ';
        }

        if (search === false) {
          data += 'search="0" ';
        }

        if (show_breadcrumb === true) {
          if (show_root === true) {
            data += 'showroot="1" ';
          }
          if (breadcrumb_roottext !== '') {
            data += 'roottext="' + breadcrumb_roottext + '" ';
          }
        } else {
          data += 'showbreadcrumb="0" ';
        }

        if (notification_download === true || notification_upload === true) {
          if (notification_emailaddress !== '%admin_email%') {
            data += 'notificationemail="' + notification_emailaddress + '" ';
          }
        }

        if (notification_download === true) {
          data += 'notificationdownload="1" ';
        }

        if (upload === true) {
          data += 'upload="1" ';

          if (upload_role !== 'administrator|editor|author|contributor|subscriber') {
            data += 'uploadrole="' + upload_role + '" ';
          }
          if (maxfilesize !== '') {
            data += 'maxfilesize="' + maxfilesize + '" ';
          }

          if (overwrite === true) {
            data += 'overwrite="1" ';
          }
          if (upload_ext !== '') {
            data += 'uploadext="' + upload_ext + '" ';
          }

          if (notification_upload === true) {
            data += 'notificationupload="1" ';
          }
        }

        if (rename === true) {
          data += 'rename="1" ';

          if (renamefiles_role !== 'administrator|editor') {
            data += 'renamefilesrole="' + renamefiles_role + '" ';
          }
          if (renamefolders_role !== 'administrator|editor') {
            data += 'renamefoldersrole="' + renamefolders_role + '" ';
          }
        }

        if (can_delete === true) {
          data += 'delete="1" ';

          if (deletefiles_role !== 'administrator|editor') {
            data += 'deletefilesrole="' + deletefiles_role + '" ';
          }
          if (deletefolders_role !== 'administrator|editor') {
            data += 'deletefoldersrole="' + deletefolders_role + '" ';
          }
        }

        if (can_addfolder === true) {
          data += 'addfolder="1" ';

          if (addfolder_role !== 'administrator|editor') {
            data += 'addfolderrole="' + addfolder_role + '" ';
          }
        }

        if (user_folders === true) {
          data += 'userfolders="1" ';

          if (view_user_folders_role !== 'administrator') {
            data += 'viewuserfoldersrole="' + view_user_folders_role + '" ';
          }
        }



        break;
    }

    data += ']';

    if (OutoftheBox_vars.shortcodeRaw === '1') {
      data += '[/raw]';
    }

    tinyMCEPopup.execCommand('mceInsertContent', false, data);
    // Refocus in window
    if (tinyMCEPopup.isWindow)
      window.focus();
    tinyMCEPopup.editor.focus();
    tinyMCEPopup.close();
  }

  function createDirectLinks() {
    var listtoken = $(".OutoftheBox.files").attr('data-token'),
            lastpath = $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-path'),
            entries = readArrCheckBoxes(".OutoftheBox[data-token='" + listtoken + "'] input[name='selected-files[]']");

    if (entries.length === 0) {
      if (tinyMCEPopup.isWindow)
        window.focus();
      tinyMCEPopup.editor.focus();
      tinyMCEPopup.close();
    }

    $.ajax({
      type: "POST",
      url: OutoftheBox_vars.ajax_url,
      data: {
        action: 'outofthebox-createlink',
        listtoken: listtoken,
        lastpath: lastpath,
        entries: entries,
        _ajax_nonce: OutoftheBox_vars.createlink_nonce
      },
      beforeSend: function() {
        $(".OutoftheBox .loading").height($(".OutoftheBox .ajax-filelist").height());
        $(".OutoftheBox .loading").fadeTo(400, 0.8);
        $(".OutoftheBox .insert_links").attr('disabled', 'disabled');
      },
      complete: function() {
        $(".OutoftheBox .loading").fadeOut(400);
        $(".OutoftheBox .insert_links").removeAttr('disabled');
      },
      success: function(response) {
        if (response !== null) {
          if (response.links !== null && response.links.length > 0) {

            var data = '<table>';

            $.each(response.links, function(key, linkresult) {
              data += '<tr><td><a href="' + linkresult.link.replace('?dl=1', '') + '">' + linkresult.name + '</a></td><td>&nbsp;</td><td>' + linkresult.size + '</td></tr>';
            });

            data += '</table>';

            tinyMCEPopup.execCommand('mceInsertContent', false, data);
            // Refocus in window
            if (tinyMCEPopup.isWindow)
              window.focus();
            tinyMCEPopup.editor.focus();
            tinyMCEPopup.close();
          } else {
          }
        }
      },
      dataType: 'json'
    });
    return false;
  }

  function insertEmbedded() {
    var listtoken = $(".OutoftheBox.files").attr('data-token'),
            lastpath = $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-path'),
            entries = readArrCheckBoxes(".OutoftheBox[data-token='" + listtoken + "'] input[name='selected-files[]']");

    if (entries.length === 0) {
      if (tinyMCEPopup.isWindow)
        window.focus();
      tinyMCEPopup.editor.focus();
      tinyMCEPopup.close();
    }

    $.ajax({
      type: "POST",
      url: OutoftheBox_vars.ajax_url,
      data: {
        action: 'outofthebox-embedded',
        listtoken: listtoken,
        lastpath: lastpath,
        entries: entries,
        _ajax_nonce: OutoftheBox_vars.createlink_nonce
      },
      beforeSend: function() {
        $(".OutoftheBox .loading").height($(".OutoftheBox .ajax-filelist").height());
        $(".OutoftheBox .loading").fadeTo(400, 0.8);
        $(".OutoftheBox .insert_links").attr('disabled', 'disabled');
      },
      complete: function() {
        $(".OutoftheBox .loading").fadeOut(400);
        $(".OutoftheBox .insert_links").removeAttr('disabled');
      },
      success: function(response) {
        if (response !== null) {
          if (response.links !== null && response.links.length > 0) {

            var data = '';

            $.each(response.links, function(key, linkresult) {
              data += '<iframe src="' + linkresult.embeddedlink + '" height="480" style="width:100%;" frameborder="0" scrolling="no" class="oftb-embedded"></iframe>';
            });

            tinyMCEPopup.execCommand('mceInsertContent', false, data);
            // Refocus in window
            if (tinyMCEPopup.isWindow)
              window.focus();
            tinyMCEPopup.editor.focus();
            tinyMCEPopup.close();
          } else {
          }
        }
      },
      dataType: 'json'
    });
    return false;
  }


  function readCheckBoxes(element) {
    var values = $(element + ":checked").map(function() {
      return this.value;
    }).get();

    return values.join('|');
  }
});