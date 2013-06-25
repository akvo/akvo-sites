=== The Dropbox Plugin ===
Contributors: player1
Tags: dropbox
Requires at least: 2.7.1
Tested up to: 3.2.1
Stable tag: trunk

Dropbox in wordpress. This is the duct tape and chewing gum release. It works again, that's what matters.

== Description ==

The plugin allows browsing downloading and uploading of files to dropbox. Many more features have been added.<br/>

== Installation ==

To access your dropbox you will need to set up a dropbox app in order to get a consumer key and a secret key.<br/>

1) Go to https://www.dropbox.com/developers/apps<br/>
2) Click "Create an App"<br/>
3) Add a name and description<br/>
4) Copy and paste the two codes in thee corresponding text boxes of the plugin options screen<br/>

To display the box use the shortcode: [dropbox]<br/>

Add these parameters:<br/>
Name (default value) - description<br/>
home() - root folder, case sensitive, ex: /Photos<br/>
separator(~) - folder separator used in displayed box<br/>
hometext(home) - text for link to specified root<br/>
dirsfirst(true) - display directories before files<br/>
orderby(date) - parameter to order files and folders, possible values: date, size, type (do not include parameter to sort by name)<br/>
orderdir(1) - 1 for asending -1 for descending<br/>
showdirs(true) - show directories in box<br/>
dateformat(l jS \of F Y h:i:s A) - format for date<br/>
allowdownload(true) - allow download of files, can be true/false<br/>
allownavigate(true) - alow folder navigation, can be true/false<br/>
columns(INDSD) - order of columns I:icon, N:name, D:date, S:size, a column may appear more than one time or not at all<br/>
asctxt(&#x25b2;) - text used for link for ascending ordering<br/>
desctxt(&#x25bc;) - text used for link for ascending ordering<br/>
ordering(true) - allow user to order the files/folders<br/>
allowupload(false) - allow user to upload files to current folder<br/>

! Do NOT place more than one box with browsing/downloading/sorting enabled on the same page. You can, but it might act odd.<br/>

In order to add icons place files in the 'images' folder(located in the plugin folder). File names must be '[extension of file].png'. For example 'txt.png' will be used as the icon for all *.txt files, 'folder.png' will be used for folders, and 'default.png' will be used for everything without a match.<br/>


Questions?: http://software.o-o.ro<br/>

== Troubleshooting ==
Nothing yet, please let me know though.<br/>
http://software.o-o.ro

== Changelog ==
= 104 =
Fixed subdirectory as root crash, sorting, and improper date display

= 101 =
Fixed bug where download from root was broken.

= 100 =
New (almost) everything, now uses the dropbox library from github.com/tijsverkoyen/Dropbox

= 010 =
Added option to show file size
Added option to show date and time of last modification

= 009 =
Fixed to work with the new dropbox url.
Fixed a problem with the permalinks
Added shortcodes

= 008 =
Switched to the new way of storing options. Might be compatible with wordpressMU. Need volunteer to find out.

= 007 =
Security update restricting acces to plugin settings. Please update if you have a multi-user blog.

= 006 =
Fixed obvious mistake in password code.

= 005 =
Files with a space in the name now mostly work.

= 004 =
Fixed url character bug.

= 003 =
Updated Dropbox connection class.

= 002 =
Updated Dropbox connection class.

= 001 =
Release.
