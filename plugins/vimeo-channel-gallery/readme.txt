=== Vimeo Channel Gallery ===
Contributors: javitxu123
Donate link: http://poselab.com/
Tags: widget, gallery, Vimeo, channel, user, video
Requires at least: 2.8
Tested up to: 3.5
Stable tag: 1.5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show a Vimeo video and a gallery of thumbnails of a Vimeo user, channel, group or album.

== Description ==

Show a Vimeo video and a gallery of thumbnails of a Vimeo user, channel, group or album.

= Features: =
* Display latest thumbnail videos from a Vimeo user, channel, group or album.
* When you click on one of the thumbnails the video plays at the top.
* This plugin uses the Vimeo IFrame player API that allows Vimeo to serve an HTML5 player rather than a Flash player for mobile devices that do not support Flash.
* You can choose to use this plugin as a widget or as a shortcode.
* You can use multiple instances of the plugin on the same page.

= Widget fields: =
* Title: Widget Title.
* Vimeo user name: the username of the user's Vimeo videos you want to show.
* Type of gallery: the type of gallery to create, user, channel, group or album.
* Show link to channel: option to display a link to the Vimeo user channel.
* Number of videos to show: It must be a number indicating the number of thumbnails to be displayed.
* Video width: indicates the width of the video player.
* Thumbnail size: indicates the width of the thumbnails. The height is automatically generated.
* Thumbnail columns: assign a numeric class to each thumbnail based on the number of columns to apply styles to each column.
* color: select the Vimeo player color.

= Shortcode syntax: =
If you want to use it as Shortcode:

`[Vimeo_Channel_Gallery user="originalvideos" color="fff" thumbwidth="100" type="channel" videowidth="630"]`

The attributes used in the shortcode are the same as the fields available in the widget, except the title field.

* user: Vimeo user, channel, group or album id (required).
* type: Vimeo gallery type. Values: user, channel, group or album. (required).
* link: Show link to channel. Values: 0 or 1. (optional).
* maxitems: Number of videos to show (optional).
* videowidth: Video width (optional).
* thumbwidth: Thumbnail size (optional).
* thumbcolumns: Thumbnail columns (optional).
* color: Color (optional).


= Demo: =
You can see a demo of the plugin at the following URL:

[Vimeo Channel Gallery Demo](http://poselab.com/Vimeo-channel-gallery)

= Languages: =
* Spanish (es_ES) - [PoseLab](http://poselab.com/)

If you have created your own language pack, or have an update of an existing one, you can [send me](mailto:javierpose@gmail.com) your gettext PO and MO so that I can bundle it into the Vimeo Channel Gallery.


== Installation ==

1. Upload the *.zip copy of this plugin into your WordPress through your 'Plugin' admin page.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Place the widget in your desired sidebar through the "widgets" admin page.

== Frequently Asked Questions ==

= Where is the “widgets” admin page? =

The “widgets” admin page is found in the administrator part (wp-admin) of your WordPress site. Go to Appearance > Widgets.

= How do I find the Vimeo user, channel, group or album id? =

* If your user url is https://vimeo.com/evanprosofsky, your channel id is evanprosofsky.
* If your channel url is https://vimeo.com/channels/staffpicks, your channel id is staffpicks.
* If your group url is https://vimeo.com/groups/maxoncinema4d, your group id is maxoncinema4d.
* If your album url is https://vimeo.com/album/1946876, your album id is 1946876.


== Screenshots ==

1. Vimeo Channel Gallery admin area.
2. Vimeo Channel Gallery.

== Changelog ==

= 1.5.2 =
* Fixed url to videos of user.

= 1.5.2 =
* Added gallery types. Now, you can create galleries by user, channel, group or album.
* Used Vimeo Simple API instead of RSS.
* Used Vimeo JavaScript API to control the player.
* Changes in css.
* Improved thumbnail sizes.
* Replaced SimplePie by SimpleXML and Wordpress HTTP API.
* Improved video player size.
* Added scroll to player only if not in view. 

= 1.4.9 =
* Improve control of color. Now you can use shorthand or regular hexadecimal colors.
* Thumbnail play button gets the color assigned.
* Fixed bug with shortcode position.
* Added target _blank to link to Vimeo user page. 

= 1.4.7 =
* Initial Release. Fork of YouTube Channel Gallery.