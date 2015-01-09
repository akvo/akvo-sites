=== Twitter Feed List ===
Contributors: eefsparreboom
Donate link: http://www.kominski.net/
Tags: twitter, oauth, feed, tweets
Requires at least: 3.4
Tested up to: 3.5
Stable tag: 1.0.1
Version: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Twitter API 1.1 compliant wordpress plugin that provides a widget to display a twitter feed based on your search input.

== Description ==

A simple Twitter API 1.1 compliant wordpress plugin that provides a widget to display a twitter feed based on your search input.
 
== Installation ==

Install the plugin using the plugin manager, or upload the files to your wp-content/plugins directory.

Navigate to Settings > Twitter Auth.

Here you'll find instructions and settings fields to authenticate with Twitter.  

Navigate to Appearance > widgets

Move the "Twitter widget" to your sidebar. Follow the instructions in the widget form.

You can specify a number of tweets to return (up to 20).  For example, to display the five latest tweets from a user and tweets including a certain hashtag you fill out the form using these settings:

In the textarea, fill out one user or hashtag per line, e.g:

`@user_name
#hashtag`

For limit, fill out "5"

== Screenshots ==

1. Log in on  http://dev.twitter.com and create a new application. When creating an application for this plugin, you don't need to set a callback location and you only need read access.

You will need to generate an oAuth token once you've created the application. The button for that is on the bottom of the application overview page.

2. Configure your widget settings

== Changelog ==

= 1.0 =
* First version

= 1.0.1 =
* Added some usability improvements