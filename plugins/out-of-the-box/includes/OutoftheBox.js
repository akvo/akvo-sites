var _active = false,
        _refreshtimer,
        _updatetimer,
        _resizeTimer = null,
        _thumbTimer = null,
        readArrCheckBoxes,
        _DBcache = {},
        mobile = false;

jQuery(document).ready(function($) {
  'use strict';

  if (/Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    mobile = true;
  }

  refreshLists();
  //Refresh lists every 5 minutes
  _refreshtimer = setInterval(refreshLists, 1000 * 60 * 10);

  //Remove no JS message
  $(".OutoftheBox.jsdisabled").removeClass('jsdisabled');

  //Add return to home event to nav-home
  $('.OutoftheBox .nav-home').click(function() {
    var listtoken = $(this).closest(".OutoftheBox").attr('data-token'),
            orgpath = $(this).closest(".OutoftheBox").attr('data-org-path'),
            data = {
      listtoken: listtoken
    };
    $(".OutoftheBox[data-qtip-id='" + listtoken + "'] .search-input").val('');
    $(this).closest(".OutoftheBox").attr('data-path', orgpath);
    getFileList(data);
  });

  //Add refresh event to nav-refresh
  $('.OutoftheBox .nav-refresh').click(function() {
    var listtoken = $(this).closest(".OutoftheBox").attr('data-token'),
            data = {
      listtoken: listtoken
    };
    $(".OutoftheBox[data-qtip-id='" + listtoken + "'] .search-input").val('');
    getFileList(data, 'hardrefresh');
  });

  // Searchbox
  $('.OutoftheBox .nav-search').each(function() {
    var listtoken = $(this).closest(".OutoftheBox").attr('data-token');

    $(this).qtip({
      prerender: true,
      id: listtoken,
      content: {
        text: $(this).next('.search-div'),
        button: $(this).next('.search-div').find('.search-remove')
      },
      position: {
        my: 'top right',
        at: 'bottom center',
        target: $(this).find('i'),
        viewport: $(window),
        adjust: {
          scroll: false
        }
      },
      style: {
        classes: 'OutoftheBox qtip-light'
      },
      show: {
        effect: function() {
          $(this).fadeTo(90, 1, function() {
            $('input', this).focus();
          });
        }
      },
      hide: {
        fixed: true,
        delay: 1500
      }
    });
  });

  $('.OutoftheBox .search-input').each(function() {
    $(this).on("keyup", function(event) {
      var listtoken = $(this).closest(".OutoftheBox").attr('data-qtip-id');
      clearTimeout(_updatetimer);
      var data = {
        listtoken: listtoken
      };
      _updatetimer = setTimeout(function() {
        getFileList(data);
      }, 1000);
      if ($(this).val().length > 0) {
        $(".OutoftheBox[data-token='" + listtoken + "'] .loading").addClass('search');
        $(".OutoftheBox[data-token='" + listtoken + "'] .nav-search").addClass('inuse');
      } else {
        $(".OutoftheBox[data-token='" + listtoken + "'] .nav-search").removeClass('inuse');
      }
    });
  });
  $('.OutoftheBox .search-remove').click(function() {
    if ($(this).parent().find('.search-input').val() !== '') {
      $(this).parent().find('.search-input').val('');
      $(this).parent().find('.search-input').trigger('keyup');
    }
  });

  //Sortable column Names
  $(".OutoftheBox .sortable").click(function() {

    var listtoken = $(this).closest(".OutoftheBox").attr('data-token');

    var newclass = 'asc';
    if ($(this).hasClass('asc')) {
      newclass = 'desc';
    }

    $(".OutoftheBox[data-token='" + listtoken + "'] .sortable").removeClass('asc').removeClass('desc');
    $(this).addClass(newclass);
    var sortstr = $(this).attr('data-sortname') + ':' + newclass;
    $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-sort', sortstr);

    var data = {
      listtoken: listtoken
    };

    clearTimeout(_updatetimer);
    _updatetimer = setTimeout(function() {
      getFileList(data);
    }, 300);
  });


  //To ZIP

  $('.select-all-files').click(function() {
    $(this).closest(".OutoftheBox").find(".selected-files:checkbox").prop("checked", $(this).prop("checked"));
    if ($(this).prop("checked") === true) {
      $(this).closest(".OutoftheBox").find(".selected-files:checkbox").show();
    } else {
      $(this).closest(".OutoftheBox").find(".selected-files:checkbox").hide();
    }
  });

  /* Zip button */
  $('.OutoftheBox .download-zip').each(function() {
    var listtoken = $(this).closest(".OutoftheBox").attr('data-token');

    $(this).qtip({
      prerender: true,
      id: listtoken,
      content: {
        text: $(this).next('.download-zip-menu')
      },
      position: {
        my: 'top right',
        at: 'bottom center',
        target: $(this).find('i'),
        viewport: $(window),
        adjust: {
          scroll: false
        }
      },
      style: {
        classes: 'OutoftheBox qtip-light'
      },
      show: {
        solo: true
      },
      hide: {
        event: 'click mouseleave unfocus',
        fixed: true,
        delay: 200
      },
      events: {
        show: function(event, api) {
          var selectedboxes = readArrCheckBoxes(".OutoftheBox[data-token='" + listtoken + "'] input[name='selected-files[]']");
          if ($(".OutoftheBox[data-qtip-id='" + listtoken + "'] .search-input").val() !== '') {
            api.elements.content.find(".all-files-to-zip").parent().hide();
          } else {
            api.elements.content.find(".all-files-to-zip").parent().show();
          }

          if (selectedboxes.length === 0) {
            api.elements.content.find(".selected-files-to-zip").parent().hide();
          } else {
            api.elements.content.find(".selected-files-to-zip").parent().show();
          }

          if (selectedboxes.length === 0 && $(".OutoftheBox[data-qtip-id='" + listtoken + "'] .search-input").val() !== '') {
            api.elements.content.find(".no-files-zip-menu").parent().show();
          } else {
            api.elements.content.find(".no-files-zip-menu").parent().hide();
          }
        }
      }
    });
  });

  $(".OutoftheBox .all-files-to-zip, .OutoftheBox .selected-files-to-zip").click(function(event) {
    var listtoken = $(this).closest(".download-zip-menu").attr('data-token'),
            lastpath = $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-path');
    $(".OutoftheBox[data-token='" + listtoken + "'] .loading").height($(".OutoftheBox[data-token='" + listtoken + "'] .ajax-filelist").height());
    $(".OutoftheBox[data-token='" + listtoken + "'] .loading").fadeTo(400, 1);
    $(".OutoftheBox[data-token='" + listtoken + "'] .loading").append("<div class='zip-status'><span class='zip-next-file'>" + OutoftheBox_vars.str_zip_createzip + "</span><span class='zip-bytes'></span></div>");
    var data = {
      action: 'outofthebox-createzip',
      listtoken: listtoken,
      lastpath: lastpath,
      _ajax_nonce: OutoftheBox_vars.createzip_nonce
    };
    if ($(event.target).hasClass('selected-files-to-zip')) {
      data.files = readArrCheckBoxes(".OutoftheBox[data-token='" + listtoken + "'] input[name='selected-files[]']");
    }

    createZipFile(data);
    return false;
  });

  function createZipFile(data) {
    var message = $(".OutoftheBox[data-token='" + data.listtoken + "'] .loading");

    $.ajax({type: "POST",
      url: OutoftheBox_vars.ajax_url,
      data: data,
      success: function(response) {
        if (typeof response !== 'undefined') {
          $(message).find('.zip-bytes').text(response.bytes_current_str + '/' + response.bytes_total_str);
          if (typeof response.finished !== 'undefined' && response.finished !== true) {

            $(message).find('.zip-next-file').text(response.next_file);
            var data = {
              action: 'outofthebox-createzip',
              listtoken: response.listtoken,
              lastpath: response.lastpath,
              zipid: response.zipid,
              _ajax_nonce: OutoftheBox_vars.createzip_nonce
            };

            createZipFile(data);

          } else if (response.finished === true) {
            $(message).find('.zip-next-file').text(OutoftheBox_vars.str_success);
            var zipfile = response.zip_location;
            downloadURL(zipfile);

            $(message).delay(1000).fadeOut(400);
            $(message).find(".zip-status").remove();
          } else {
            $(message).delay(1000).fadeOut(400);
            $(message).find(".zip-status").remove();
          }
        } else {
          $(message).find('.zip-next-file').text('Error');
          $(".OutoftheBox[data-token='" + response.listtoken + "'] .loading").delay(1000).fadeOut(400);
          $(".OutoftheBox[data-token='" + response.listtoken + "'] .loading .zip-status").remove();
        }
      },
      dataType: 'json'
    });
  }

  function isCached(identifyer, listtoken) {
    if (typeof _DBcache[listtoken] === 'undefined') {
      _DBcache[listtoken] = {};
    }

    if (typeof _DBcache[listtoken][identifyer] === 'undefined' || $.isEmptyObject(_DBcache[listtoken][identifyer])) {
      return false;
    } else {

      var unixtime = Math.round((new Date()).getTime() / 1000);
      if (_DBcache[listtoken][identifyer].expires < unixtime) {
        _DBcache[listtoken][identifyer] = {};
        return false;
      }
      return _DBcache[listtoken][identifyer];
    }
  }

  function updateDiv(response, identifyer, listtoken) {
    $(".OutoftheBox[data-token='" + listtoken + "'] .loading").fadeTo(400, 1);

    if (typeof _DBcache[listtoken] === 'undefined') {
      _DBcache[listtoken] = {};
    }

    _DBcache[listtoken][identifyer] = response;

    $(".OutoftheBox[data-token='" + listtoken + "'] .ajax-filelist").html(response.html);
    $(".OutoftheBox[data-token='" + listtoken + "'] .nav-title").html(response.breadcrumb);
    $(".current-folder-raw").text(response.rawpath);
    if (response.lastpath !== null) {
      $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-path', response.lastpath);
    }

    $(".OutoftheBox[data-token='" + listtoken + "'] .loading").fadeOut(400);

    updateActions(listtoken);
  }

  function getFileList(data, hardrefresh) {
    if (_refreshtimer) {
      clearInterval(_refreshtimer);
    }

    _refreshtimer = setInterval(refreshLists, 1000 * 60 * 10);

    var listtoken = data.listtoken,
            list = $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-list'),
            lastpath = $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-path'),
            sort = $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-sort'),
            query = $(".OutoftheBox[data-qtip-id='" + listtoken + "'] .search-input").val(),
            ajax_action = 'outofthebox-getfilelist',
            deeplink = $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-deeplink'),
            nonce = OutoftheBox_vars.refresh_nonce;

    if (list === 'gallery') {
      ajax_action = 'outofthebox-getgallery';
      nonce = OutoftheBox_vars.gallery_nonce;
    }

    if (typeof query !== 'undefined' && query.length > 2) {
      data.query = query;
    }

    if (typeof hardrefresh !== 'undefined') {
      _DBcache = [];
    }

    data.deeplink = deeplink;
    data.sort = sort;
    data.action = ajax_action;
    data._ajax_nonce = nonce;

    /* Identifyer for cache */
    var str = JSON.stringify(data);
    var identifyer = str.hashCode();
    var request = false;

    request = isCached(identifyer, listtoken);

    if (request !== false) {
      return updateDiv(request, identifyer, listtoken);
    }

    /* Don't add in the identifyer */
    data.lastpath = lastpath;

    $.ajax({
      type: "POST",
      url: OutoftheBox_vars.ajax_url,
      data: data,
      beforeSend: function() {
        $(".OutoftheBox[data-token='" + listtoken + "'] .no_results").remove();
        $(".OutoftheBox[data-token='" + listtoken + "'] .loading").removeClass('initialize upload error');
        $(".OutoftheBox[data-token='" + listtoken + "'] .loading").height($(".OutoftheBox[data-token='" + listtoken + "'] .ajax-filelist").height());
        $(".OutoftheBox[data-token='" + listtoken + "'] .loading").fadeTo(400, 1);
      },
      complete: function() {
        $(".OutoftheBox[data-token='" + listtoken + "'] .loading").removeClass('search');
      },
      success: function(response) {
        if (response !== null && response !== 0) {
          updateDiv(response, identifyer, listtoken);
        } else {
          $(".OutoftheBox[data-token='" + listtoken + "'] .nav-title").html(OutoftheBox_vars.str_no_filelist);
          $(".OutoftheBox[data-token='" + listtoken + "'] .loading").addClass('error');
          updateActions(listtoken);
        }
      },
      error: function() {
        $(".OutoftheBox[data-token='" + listtoken + "'] .nav-title").html(OutoftheBox_vars.str_no_filelist);
        $(".OutoftheBox[data-token='" + listtoken + "'] .loading").addClass('error');
        updateActions(listtoken);
      },
      dataType: 'json'
    });
  }

  function changeEntry(data) {
    var listtoken = data.listtoken,
            lastpath = $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-path');
    data.lastpath = lastpath;
    $.ajax({type: "POST",
      url: OutoftheBox_vars.ajax_url,
      data: data,
      beforeSend: function() {
        $(".OutoftheBox[data-token='" + listtoken + "'] .loading").height($(".OutoftheBox[data-token='" + listtoken + "'] .ajax-filelist").height());
        $(".OutoftheBox[data-token='" + listtoken + "'] .loading").fadeTo(400, 0.8);
      },
      complete: function() {
        var data = {
          listtoken: listtoken
        };
        getFileList(data, 'hardrefresh');
      }, success: function(response) {
        if (typeof response !== 'undefined') {
          if (typeof response.result !== 'undefined' && response.result !== '1') {

            var dialog_html = $("<div class='dialog' title='" + OutoftheBox_vars.str_error_title + "'><p>" + response.msg + "</em></p></div>");
            var l18nButtons = {};
            l18nButtons[OutoftheBox_vars.str_close_title] = function() {
              $(this).dialog("close");
            };
            dialog_html.dialog({
              dialogClass: 'OutoftheBox',
              resizable: false, height: 200,
              width: 400,
              modal: true,
              buttons: l18nButtons
            });
          } else {
            if (response.lastpath !== null) {
              $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-path', response.lastpath);
            }
          }
        }
      },
      dataType: 'json'
    });
  }

  function refreshLists() {
    var selector = $('.OutoftheBox.files, .OutoftheBox.gridgallery');
    if (_active) {
      var selector = $('.OutoftheBox.files');
    }

    //Create file lists
    selector.each(function() {

      var data = {
        OutoftheBoxpath: $(this).attr('data-path'),
        listtoken: $(this).attr('data-token')
      };
      getFileList(data);
    });
    _active = true;
  }

  window.updateCollage = function updateCollage(listtoken) {
    var selector = $(".OutoftheBox.gridgallery[data-token='" + listtoken + "']");

    //Set Image container explicit
    var padding = parseInt($(selector).find(".image-collage").css('padding-left')) + parseInt($(selector).find(".image-collage").css('padding-right'));
    var containerwidth = $(selector).width() - padding - 1;
    $(selector).find(".image-collage").outerWidth(containerwidth);

    var targetheight = $(selector).attr('data-targetheight');
    var allowpartiallastrow = ($(selector).attr('data-lastrow') === 'true');

    $(selector).find('.image-collage').removeWhitespace().collagePlus({
      'targetHeight': targetheight,
      'allowPartialLastRow': allowpartiallastrow,
      'fadeSpeed': "slow"
    });

    $(selector).find(".image-container.hidden").fadeOut(0);
    $(selector).find(".image-collage").fadeTo(1500, 1);

    $(selector).find(".image-container").each(function() {
      $(this).find(".folder-thumb").width($(this).width()).height($(this).height());
    });

    $(selector).find('.image-folder-img').delay(1000).animate({opacity: 0}, 1500);
    if (_thumbTimer) {
      clearInterval(_thumbTimer);
    }

    updateImageFolders();
    _thumbTimer = setInterval(updateImageFolders, 15000);

  };

  function updateImageFolders() {
    $(".OutoftheBox.gridgallery .image-folder").each(function() {
      $(this).find('.folder-thumb').fadeIn(1500);

      var delay = Math.floor(Math.random() * 3000) + 1500;

      $(this).find(".thumb3").delay(delay).fadeOut(1500);
      $(this).find(".thumb2").delay(delay + 1500).delay(delay).fadeOut(1500);
      $(this).find(".thumb3").delay(2 * (delay + 1500)).delay(delay).fadeIn(1500);
    });
  }
  function updateActions(listtoken) {

    $(".OutoftheBox[data-token='" + listtoken + "'] .entry").unbind('hover');
    $(".OutoftheBox[data-token='" + listtoken + "'] .entry").hover(
            function() {
              $(this).find('.entry_edit_menu').show();
              $(this).find('.selected-files:checkbox').show();
            },
            function() {
              if (!$(this).hasClass('hasfocus')) {
                $(this).find('.entry_edit_menu').hide();
              }
              if ($(this).find('.selected-files:checkbox').prop("checked") === false) {
                $(this).find('.selected-files:checkbox').hide();
              }
            }
    );

    /* Edit menu popup */
    $(".OutoftheBox[data-token='" + listtoken + "'] .entry .entry_edit_menu").each(function() {
      $(this).click(function(e) {
        e.stopPropagation();
      });

      $(this).qtip({
        content: {
          text: $(this).next('.oftb-dropdown-menu')
        },
        position: {
          my: 'top center',
          at: 'bottom center',
          target: $(this).find('i'),
          scroll: false,
          viewport: $(".OutoftheBox[data-token='" + listtoken + "']")
        },
        show: {
          event: 'click',
          solo: true
        },
        hide: {
          event: 'click mouseleave unfocus',
          delay: 500,
          fixed: true
        },
        events: {
          show: function(event, api) {
            api.elements.target.show();
            api.elements.target.closest('.entry').addClass('hasfocus');
          },
          hide: function(event, api) {
            api.elements.target.hide();
            api.elements.target.closest('.entry').removeClass('hasfocus');
          }
        },
        style: {
          classes: 'OutoftheBox qtip-light'
        }
      });
    });


    $(".OutoftheBox[data-token='" + listtoken + "'] .nextimages").click(function() {
      $(".OutoftheBox[data-qtip-id='" + listtoken + "'] .search-input").val('');

      var loadimages = $(this).attr('data-loadimages'),
              images = $(".OutoftheBox[data-token='" + listtoken + "'] .image-container:hidden:lt(" + loadimages + ")"),
              lastimage = $(".OutoftheBox[data-token='" + listtoken + "'] .image-container:visible").last();

      if (images.length > 0) {
        images.each(function() {
          $(this).fadeIn(500);
          $(this).removeClass('hidden');
          $(this).find('img').removeClass('hidden');
        });

        $(".OutoftheBox img.preloading").not('.hidden').unveil(200, null, function() {
          $(this).load(function() {
            $(this).removeClass('preloading');
          });
        });

        $('html, body').animate({
          scrollTop: lastimage.offset().top
        }, 2000);
      }

      if ($(".OutoftheBox[data-token='" + listtoken + "'] .image-container:hidden").length === 0) {
        $(this).fadeOut(500, function() {
          $(this).remove();
        });
      }
    });

    $(".OutoftheBox[data-token='" + listtoken + "'] .folder, .OutoftheBox[data-token='" + listtoken + "'] .image-folder").unbind('click');
    $(".OutoftheBox[data-token='" + listtoken + "'] .folder, .OutoftheBox[data-token='" + listtoken + "'] .image-folder").click(function() {
      $(".OutoftheBox[data-qtip-id='" + listtoken + "'] .search-input").val('');

      var data = {
        OutoftheBoxpath: $(this).attr('data-url'),
        listtoken: listtoken
      };
      getFileList(data);

    });

    /* Use timeout to load images in viewport correctly */
    setTimeout(function() {

      $(".OutoftheBox img.preloading").not('.hidden').unveil(200, $(window), function() {
        $(this).load(function() {
          $(this).removeClass('preloading');
        });
      });

      $(".OutoftheBox img.preloading").not('.hidden').unveil(200, $(".OutoftheBox .ajax-filelist"), function() {
        $(this).load(function() {
          $(this).removeClass('preloading');
        });
      });

      setTimeout(function() {
        $(".OutoftheBox .image-collage").fadeTo(0, 0);
        updateCollage(listtoken);
      }, 200);
    }, 500);

    $(".OutoftheBox[data-token='" + listtoken + "'] .image-container .image-rollover").css("opacity", "0");
    $(".OutoftheBox[data-token='" + listtoken + "'] .image-container").hover(
            function() {
              $(this).find('.image-rollover, .image-folder-img').stop().animate({opacity: 1}, 400);
            },
            function() {
              $(this).find('.image-rollover, .image-folder-img').stop().animate({opacity: 0}, 400);
            });

    $(".OutoftheBox[data-token='" + listtoken + "'] .colorbox-group").colorbox({
      rel: 'colorbox-group',
      maxWidth: '90%',
      minWidth: '200px',
      maxHeight: '90%',
      className: 'OutoftheBox',
      fixed: true,
      photo: true,
      slideshow: true,
      slideshowAuto: false,
      current: "{current}/{total}",
      previous: OutoftheBox_vars.str_previous_title,
      next: OutoftheBox_vars.str_next_title,
      close: OutoftheBox_vars.str_close_title,
      xhrError: OutoftheBox_vars.str_xhrError_title,
      imgError: OutoftheBox_vars.str_imgError_title,
      slideshowStart: OutoftheBox_vars.str_startslideshow,
      slideshowStop: OutoftheBox_vars.str_stopslideshow,
      retinaImage: true
    });

    //if deeplinking, activate that colorbox item
    if ($(".OutoftheBox .deeplink").length > 0) {
      $(".OutoftheBox[data-token='" + listtoken + "']  .deeplink a").click();
    }

    $(".OutoftheBox[data-token='" + listtoken + "'] .colorbox-inline-group").colorbox({
      rel: 'colorbox-inline-group',
      width: '90%',
      height: '90%',
      className: 'OutoftheBox',
      fixed: true,
      current: "{current}/{total}",
      iframe: true,
      previous: OutoftheBox_vars.str_previous_title,
      next: OutoftheBox_vars.str_next_title,
      close: OutoftheBox_vars.str_close_title,
      xhrError: OutoftheBox_vars.str_xhrError_title,
      imgError: OutoftheBox_vars.str_imgError_title,
      slideshowStart: OutoftheBox_vars.str_startslideshow,
      slideshowStop: OutoftheBox_vars.str_stopslideshow
    });

    $(".OutoftheBox[data-token='" + listtoken + "'] .entry_checkbox").unbind('click');
    $(".OutoftheBox[data-token='" + listtoken + "'] .entry_checkbox").click(function(e) {
      e.stopPropagation();
      return true;
    });

    $(".OutoftheBox[data-token='" + listtoken + "'] .entry_action_view").unbind('click');
    $(".OutoftheBox[data-token='" + listtoken + "'] .entry_action_view").click(function() {
      $('.qtip.OutoftheBox').qtip('hide');
      var datapath = $(this).closest("ul").attr('data-path');
      var link = $(".OutoftheBox[data-token='" + listtoken + "'] .entry[data-url='" + datapath + "']").find(".entry_link").trigger("click");
    });

    $(".OutoftheBox[data-token='" + listtoken + "'] .entry_action_shortlink").unbind('click');
    $(".OutoftheBox[data-token='" + listtoken + "'] .entry_action_shortlink").click(function() {
      $('.qtip.OutoftheBox').qtip('hide');

      var datapath = $(this).closest("ul").attr('data-path');
      var dataurl = $(".OutoftheBox[data-token='" + listtoken + "'] .entry[data-url='" + datapath + "']").attr('data-url');
      var dialog_html = $("<div class='dialog' title='" + OutoftheBox_vars.str_share_link + "'><input type='text' class='shared-link-url' value='" + OutoftheBox_vars.str_create_shared_link + "' style='width: 98%;'/></div>");

      var l18nButtons = {};
      l18nButtons[OutoftheBox_vars.str_close_title] = function() {
        $(this).dialog("destroy");
      };

      dialog_html.dialog({
        dialogClass: 'OutoftheBox',
        resizable: false,
        height: 150,
        width: 400,
        modal: true,
        buttons: l18nButtons,
        open: function(event, ui) {

          $.ajax({
            type: "POST", url: OutoftheBox_vars.ajax_url,
            data: {
              action: 'outofthebox-createlink',
              listtoken: listtoken,
              OutoftheBoxpath: dataurl,
              _ajax_nonce: OutoftheBox_vars.createlink_nonce
            },
            beforeSend: function() {
              $(".OutoftheBox[data-token='" + listtoken + "'] .loading").height($(".OutoftheBox[data-token='" + listtoken + "'] .ajax-filelist").height());
              $(".OutoftheBox[data-token='" + listtoken + "'] .loading").fadeTo(400, 0.8);
            },
            complete: function() {
              $(".OutoftheBox[data-token='" + listtoken + "'] .loading").fadeOut(400);
            },
            success: function(response) {
              if (response !== null) {
                if (response.link !== null) {
                  $(dialog_html).find('.shared-link-url').val(response.link);
                } else {
                  $(dialog_html).find('.shared-link-url').val(response.error);
                }
              }
            },
            dataType: 'json'
          });
        }
      });
      return false;
    });

    $(".OutoftheBox[data-token='" + listtoken + "'] .entry_action_delete").unbind('click');
    $(".OutoftheBox[data-token='" + listtoken + "'] .entry_action_delete").click(function() {
      $('.qtip.OutoftheBox').qtip('hide');

      var datapath = $(this).closest("ul").attr('data-path');
      var dataname = $(".OutoftheBox[data-token='" + listtoken + "'] .entry[data-url='" + datapath + "']").attr('data-name');
      var dataurl = $(".OutoftheBox[data-token='" + listtoken + "'] .entry[data-url='" + datapath + "']").attr('data-url');
      var dialog_html = $("<div class='dialog' title='" + OutoftheBox_vars.str_delete_title + "'><p>" + OutoftheBox_vars.str_delete + ' <em>' + dataname + "</em></p></div>");
      var l18nButtons = {};
      l18nButtons[OutoftheBox_vars.str_delete_title] = function() {
        var data = {
          action: 'outofthebox-deleteentry',
          OutoftheBoxpath: dataurl,
          listtoken: listtoken,
          _ajax_nonce: OutoftheBox_vars.delete_nonce
        };
        changeEntry(data);
        $(this).dialog("destroy");
      };
      l18nButtons[OutoftheBox_vars.str_cancel_title] = function() {
        $(this).dialog("destroy");
      };
      dialog_html.dialog({
        dialogClass: 'OutoftheBox',
        resizable: false,
        height: 200,
        width: 400,
        modal: true,
        buttons: l18nButtons
      });
      return false;
    });

    $(".OutoftheBox[data-token='" + listtoken + "'] .entry_action_rename").unbind('click');
    $(".OutoftheBox[data-token='" + listtoken + "'] .entry_action_rename").click(function() {
      $('.qtip.OutoftheBox').qtip('hide');

      var datapath = $(this).closest("ul").attr('data-path');
      var dataname = $(".OutoftheBox[data-token='" + listtoken + "'] .entry[data-url='" + datapath + "']").attr('data-name');
      var dataurl = $(".OutoftheBox[data-token='" + listtoken + "'] .entry[data-url='" + datapath + "']").attr('data-url');
      var dialog_html = $("<div class='dialog' title='" + OutoftheBox_vars.str_rename_title + "'><p>" + OutoftheBox_vars.str_rename +
              '<input type="text" name="newname" id="newname" value="' + dataname + '" class="text ui-widget-content ui-corner-all" style=" width: 98%; "/></p></div>');
      var l18nButtons = {};
      l18nButtons[OutoftheBox_vars.str_rename_title] = function() {
        var data = {
          action: 'outofthebox-renameentry',
          OutoftheBoxpath: dataurl,
          newname: encodeURIComponent($('#newname').val()),
          listtoken: listtoken,
          _ajax_nonce: OutoftheBox_vars.rename_nonce
        };
        changeEntry(data);
        $(this).dialog("destroy");
      };
      l18nButtons[OutoftheBox_vars.str_cancel_title] = function() {
        $(this).dialog("destroy");
      };
      dialog_html.dialog({
        dialogClass: 'OutoftheBox',
        resizable: false,
        height: 200,
        width: 400,
        modal: true,
        buttons: l18nButtons});
      return false;
    });
    $(".OutoftheBox[data-token='" + listtoken + "'] .newfolder").unbind('click');
    $(".OutoftheBox[data-token='" + listtoken + "'] .newfolder").click(function() {
      $('.qtip.OutoftheBox').qtip('hide');
      var dialog_html = $("<div class='dialog' title='" + OutoftheBox_vars.str_addfolder_title + "'><p>" +
              '<input type="text" name="newfolder" id="newfolder" value="' + OutoftheBox_vars.str_addfolder + '" class="text ui-widget-content ui-corner-all" style=" width: 90%; "/></p></div>');
      var l18nButtons = {};
      l18nButtons[OutoftheBox_vars.str_addfolder_title] = function() {
        var data = {
          action: 'outofthebox-addfolder',
          newfolder: encodeURIComponent($('#newfolder').val()),
          listtoken: listtoken,
          _ajax_nonce: OutoftheBox_vars.addfolder_nonce
        };
        changeEntry(data);
        $(this).dialog("destroy");
      };
      l18nButtons[OutoftheBox_vars.str_cancel_title] = function() {
        $(this).dialog("destroy");
      };
      dialog_html.dialog({
        dialogClass: 'OutoftheBox',
        resizable: false,
        height: 200,
        width: 400,
        modal: true,
        buttons: l18nButtons
      });
      return false;
    });
  }

  // Initialize the jQuery File Upload widget:
  $('.OutoftheBox .fileuploadform').each(function() {
    $(this).fileupload({
      url: OutoftheBox_vars.ajax_url,
      type: 'POST',
      maxFileSize: OutoftheBox_vars.post_max_size,
      acceptFileTypes: new RegExp($(this).find('input[name="acceptfiletypes"]').val(), "i"),
      dropZone: $(this).closest('.OutoftheBox').find('.ajax-filelist'),
      messages: {maxNumberOfFiles: OutoftheBox_vars.maxNumberOfFiles,
        acceptFileTypes: OutoftheBox_vars.acceptFileTypes,
        maxFileSize: OutoftheBox_vars.maxFileSize,
        minFileSize: OutoftheBox_vars.minFileSize
      },
      limitConcurrentUploads: 3,
      disableImageLoad: true,
      disableImageResize: true,
      disableImagePreview: true,
      disableAudioPreview: true,
      disableVideoPreview: true,
      uploadTemplateId: null,
      downloadTemplateId: null,
      uploadTemplate: function(o) {
        var rows = $();
        $.each(o.files, function(index, file) {
          var row = $('<div class="template-upload" data-file="' + file.name + '"><span class="ui-icon"></span><div class="upload-name"></div><div class="upload-status">' + (file.error ? '<span class="error">' + OutoftheBox_vars.str_error + '</span>' : '<span class="queue">' + OutoftheBox_vars.str_inqueue + '</span>') +
                  '</div>' +
                  (file.error ? '<div class="upload-error"></div>' : '<div class="upload-buttons">' +
                          '<button class="start small">' +
                          '<span>' + OutoftheBox_vars.str_start_title + '</span>' +
                          '</button>' +
                          '<button class="cancel small">' +
                          '<span>' + OutoftheBox_vars.str_cancel_title + '</span>' +
                          '</button></div>' + '<div class="upload-progress"><div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div></div>') +
                  '</div>');
          row.find('.upload-name').html(file.name + "<span class='file-size'>" + o.formatFileSize(file.size) + "</span>");
          if (file.error) {
            row.find('.upload-error').text(file.error);
            row.find('.ui-icon').addClass('ui-icon-circle-close');
          } else {
            row.find('.ui-icon').addClass('ui-icon-circle-arrow-n');
          }
          rows = rows.add(row);
        });
        return rows;
      }, downloadTemplate: function(o) {
        var rows = $();
        $.each(o.files, function(index, file) {
          var row = $('<div class="template-download"><span class="ui-icon"></span><div class="upload-name"></div><div class="upload-status">' + (file.error ? '<span class="error">' + OutoftheBox_vars.str_error + '</span>' : '<span class="succes">' + OutoftheBox_vars.str_success + '</span>') +
                  '</div>' +
                  (file.error ?
                          '<div class="upload-error"></div>' : '') +
                  '</div>');
          row.find('.upload-name').html(file.name + "<span class='file-size'>" + o.formatFileSize(file.size) + "</span>");
          if (file.error) {
            row.find('.upload-error').text(file.error);
            row.find('.ui-icon').addClass('ui-icon-circle-close');
          } else {
            row.find('.ui-icon').addClass('ui-icon-circle-check');
          }
          rows = rows.add(row);
        });
        return rows;
      }
    }).on('fileuploadsubmit', function(e, data) {
      var datatoken = $(this).attr('data-token');
      $(".OutoftheBox[data-token='" + datatoken + "'] .loading").addClass('upload');
      $(".OutoftheBox[data-token='" + datatoken + "'] .loading").height($(".OutoftheBox[data-token='" + datatoken + "'] .ajax-filelist").height());
      $(".OutoftheBox[data-token='" + datatoken + "'] .loading").fadeTo(400, 1);

      $.each(data.files, function(index, file) {
        $(".OutoftheBox[data-token='" + datatoken + "'] div[data-file='" + file.name + "'] .queue").text(OutoftheBox_vars.str_uploading);
      });
      data.formData = {
        action: 'outofthebox-uploadfile',
        lastpath: $(".OutoftheBox[data-token='" + datatoken + "']").attr('data-path'),
        listtoken: datatoken,
        _ajax_nonce: OutoftheBox_vars.upload_nonce
      };
    }).on('fileuploadalways', function() {
      var datatoken = $(this).attr('data-token');
      $(".OutoftheBox[data-token='" + datatoken + "'] .loading").fadeOut(400);

      $('.OutoftheBox .fileuploadform[data-token="' + datatoken + '"] .template-download').delay(5000).animate({"opacity": "0"}, "slow", function() {
        $(this).remove();
      });
    }).on('fileuploaddone', function() {

      var formData = {
        listtoken: $(this).attr('data-token')
      };
      getFileList(formData);
    }).on('fileuploadfail', function() {

    });
  });
  /* drag and drop functionality*/
  $(document).bind('dragover', function(e) {
    var dropZone = $('.OutoftheBox .fileuploadform').closest('.OutoftheBox').find('.ajax-filelist'),
            timeout = window.dropZoneTimeout;
    if (!timeout) {
      dropZone.addClass('in');
    } else {
      clearTimeout(timeout);
    }
    var found = false, node = e.target;
    do {
      if ($(node).is(dropZone)) {
        found = true;
        break;
      }
      node = node.parentNode;
    } while (node !== null);
    if (found) {
      $(node).addClass('hover');
    } else {
      dropZone.removeClass('hover');
    }
    window.dropZoneTimeout = setTimeout(function() {
      window.dropZoneTimeout = null;
      dropZone.removeClass('in hover');
    }, 100);
  });

// Create Audio Boxes
  $('.OutoftheBox.media.audio').each(function() {
    var listtoken = $(this).attr('data-token'), extensions = $(this).attr('data-extensions'),
            jPlayerSelector = '#' + $(this).find('.jp-jplayer').attr('id'),
            cssSelector = '#' + $(this).find('.jp-video').attr('id');
    var playlist = new jPlayerPlaylist({
      jPlayer: jPlayerSelector,
      cssSelectorAncestor: cssSelector
    }, [], {
      swfPath: OutoftheBox_vars.js_url,
      backgroundColor: '#EEEEEE',
      supplied: extensions,
      solution: "html,flash",
      keyEnabled: true,
      ready: function() {
        $(".jp-title").show();

        var data = {
          action: 'outofthebox-getplaylist',
          lastpath: $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-path'),
          sort: $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-sort'),
          listtoken: listtoken,
          _ajax_nonce: OutoftheBox_vars.getplaylist_nonce
        };
        $.ajax({
          type: "POST",
          url: OutoftheBox_vars.ajax_url,
          data: data,
          success: function(result) {
            if (result !== '-1') {
              playlist.setPlaylist(result);
              $(".OutoftheBox[data-token='" + listtoken + "'] .jp-playlist-loading").hide();
            }
          },
          dataType: 'json'
        });

      },
      loadstart: function(e) {
        $(".OutoftheBox[data-token='" + listtoken + "'] .jp-title").html($(".OutoftheBox[data-token='" + listtoken + "'] .jp-playlist-item.jp-playlist-current").html()).show();
      }
    });
  });

  // Create Video Boxes
  $('.OutoftheBox.media.video').each(function() {
    var listtoken = $(this).attr('data-token'), extensions = $(this).attr('data-extensions'),
            jPlayerSelector = '#' + $(this).find('.jp-jplayer').attr('id'),
            cssSelector = '#' + $(this).find('.jp-video').attr('id');
    var playlist = new jPlayerPlaylist({
      jPlayer: jPlayerSelector,
      cssSelectorAncestor: cssSelector
    }, [], {
      swfPath: OutoftheBox_vars.js_url,
      backgroundColor: '#EEEEEE',
      supplied: extensions,
      solution: "html,flash",
      keyEnabled: true,
      audioFullScreen: true,
      errorAlerts: false,
      warningAlerts: false,
      size: {
        width: "100%",
        height: "100%"
      },
      ready: function() {
        var data = {
          action: 'outofthebox-getplaylist',
          lastpath: $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-path'),
          sort: $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-sort'),
          listtoken: listtoken,
          _ajax_nonce: OutoftheBox_vars.getplaylist_nonce
        };
        $.ajax({
          type: "POST",
          url: OutoftheBox_vars.ajax_url,
          data: data,
          success: function(result) {
            if (result !== '-1') {
              playlist.setPlaylist(result);
              $(".OutoftheBox[data-token='" + listtoken + "'] .jp-playlist-loading").hide();
            }
          },
          dataType: 'json'
        });
        $(".OutoftheBox[data-token='" + listtoken + "'] .jp-jplayer").height($(".OutoftheBox[data-token='" + listtoken + "'] .jp-jplayer").width() / 1.6);
        $(".OutoftheBox[data-token='" + listtoken + "'] object").width('100%');
        $(".OutoftheBox[data-token='" + listtoken + "'] img").height($(".OutoftheBox[data-token='" + listtoken + "'] .jp-jplayer").height());
        $(".OutoftheBox[data-token='" + listtoken + "'] object").height($(".OutoftheBox[data-token='" + listtoken + "'] .jp-jplayer").height());
      },
      loadstart: function(e) {
        $(".OutoftheBox[data-token='" + listtoken + "'] .jp-title").html($(".OutoftheBox[data-token='" + listtoken + "'] .jp-playlist-item.jp-playlist-current").html()).show();
      },
      progress: function(e) {
        if (e.jPlayer.status.videoHeight !== 0 && e.jPlayer.status.videoWidth !== 0) {
          var ratio = e.jPlayer.status.videoWidth / e.jPlayer.status.videoHeight;
          var videoselector = $(".OutoftheBox[data-token='" + listtoken + "'] object");
          if (e.jPlayer.html.active === true) {
            videoselector = $(".OutoftheBox[data-token='" + listtoken + "'] video");
            videoselector.bind('contextmenu', function() {
              return false;
            });
          }
          if (videoselector.height() !== videoselector.width() / ratio) {
            videoselector.height(videoselector.width() / ratio);
            $(".OutoftheBox[data-token='" + listtoken + "'] .jp-jplayer").height(videoselector.width() / ratio);
          }
        }
      },
      waiting: function(e) {
        var videoselector = $(".OutoftheBox[data-token='" + listtoken + "'] object");
        if (e.jPlayer.html.active === true) {
          videoselector = $(".OutoftheBox[data-token='" + listtoken + "'] video");
          videoselector.bind('contextmenu', function() {
            return false;
          });
        }
        $(".OutoftheBox[data-token='" + listtoken + "'] img").height($(".OutoftheBox[data-token='" + listtoken + "'] .jp-jplayer").height());
      },
      resize: function(e) {
        if (e.jPlayer.options.fullScreen === false) {
          $(".OutoftheBox[data-token='" + listtoken + "'] img").height($(".OutoftheBox[data-token='" + listtoken + "'] .jp-jplayer").height());
        }
      }
    });
  });

  // Resize handlers
  $(window).resize(function() {
    $('.OutoftheBox.media.video .jp-jplayer').each(function() {
      var status = ($(this).data().jPlayer.status);
      if (status.videoHeight !== 0 && status.videoWidth !== 0) {
        var ratio = status.videoWidth / status.videoHeight;
        var jpvideo = $(this);
        if ($(this).find('object').length > 0) {
          var jpobject = $(this).find('object');
        } else {
          var jpobject = $(this).find('video');
        }

        if (jpvideo.height() !== jpvideo.width() / ratio) {
          jpobject.height(jpobject.width() / ratio);
          jpvideo.height(jpobject.width() / ratio);
        }
      }

    });

    // set a timer to re-apply the plugin
    if (_resizeTimer) {
      clearTimeout(_resizeTimer);
    }

    _resizeTimer = setTimeout(function() {
      $(".OutoftheBox .image-collage").fadeTo(100, 0);
      $(".OutoftheBox .image-collage").each(function() {
        var listtoken = $(this).closest('.OutoftheBox').attr('data-token');
        updateCollage(listtoken);
      });
    }, 200);
  });

  var downloadURL = function downloadURL(url) {
    var hiddenIFrameID = 'hiddenDownloader',
            iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
      iframe = document.createElement('iframe');
      iframe.id = hiddenIFrameID;
      iframe.style.display = 'none';
      document.body.appendChild(iframe);
    }
    iframe.src = url;
  };

  readArrCheckBoxes = function(element) {
    var values = $(element + ":checked").map(function() {
      return this.value;
    }).get();

    return values;
  };

  /* Safari bug fix for embedded iframes*/
  if (/iPhone|iPod|iPad/.test(navigator.userAgent)) {
    $('iframe.oftb-embedded').wrap(function() {
      var $this = $(this);
      return $('<div id="safari_fix"/>').css({
        'width': $this.outerWidth() + "px",
        'height': $this.outerHeight() + "px",
        'overflow': 'auto',
        '-webkit-overflow-scrolling': 'touch'
      });
    });
  }
});

String.prototype.hashCode = function() {
  var hash = 0, i, char;
  if (this.length === 0)
    return hash;
  for (i = 0, l = this.length; i < l; i++) {
    char = this.charCodeAt(i);
    hash = ((hash << 5) - hash) + char;
    hash |= 0; // Convert to 32bit integer
  }
  return hash;
};