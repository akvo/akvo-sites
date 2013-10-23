=== Plugin Name ===
Contributors: yourlocalwebmaster
Donate link: http://yourlocalwebmaster.com/donate
Tags: newsletter signup, email capture, email form, subscribe form, form, email subscribe form, monthly newsletter subscription form, email handling, subscriber handling, lead generation, squeeze page, subscribe
Requires at least: 2.0.2
Tested up to: 3.5
Stable tag: 3.4.2 trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add a newsletter signup form to your site and start collecting email addresses to use with MailChimp, Constant contact or other third-party bulk-mailer program! Complete with email management admin panel.


== Description ==

Easily add a newsletter subscription form to your site and start collecting email addresses to use with MailChimp, Constant contact or other third-party bulk-mailer program! Complete with email management admin panel. Web development is affordable @ <a href="http://www.yourlocalwebmaster.com">Your Local Webmaster.com</a>

== Installation ==

1. To install the Simple Newsletter Signup, simply download it and upload it in the Plugins menu.
Or, Install it via the "search" feature on the plugin page. "Simple Newsletter Signup"

2. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= Why aren't my height and width attributes working? =

Perhaps you are not aware, but shortcode attributes are case sensitive. Please remember to use all lowercase letters when adding attributes.

= Are there any other available attributes? =

I am constantly updating my plugins. Looks for more attributes very soon!

= Help! I don't know CSS! Are there any custom form templates available? =

I will be releasing some very soon.

== Screenshots ==

Coming Soon!

== Changelog ==

Coming Soon!

== Upgrade Notice ==

Coming Soon!

== Usage Notes ==

**Shortcodes**
You may use these shortcodes in your pages, posts and HTML widgets!

[simple_newsletter]
This will display a basic email subscribe form.

**Available Options**

[simple_newsletter name="true"]
Set to true to create a "Name" field if you wish to capture the subscribers name as well as email address.

[simple_newsletter title="My Form Title"]
Gives your form a title. Default displays no title.

[simple_newsletter button="Go!"]
Changes the text on the button. Default displays "Subscribe"

[simple_newsletter terms="http://www.someURL.com/terms.html"]
Do you want the subscriber to agree to terms and conditions first? Then enter the URL.

[simple_newsletter terms_text="I agree to terms & conditions"]
Enter the link text for the terms and conditions link.

[simple_newsletter thanks="Thank you for subscribing!"]
This is the thank you text that displays after successful submit.

**Example of use:**

[simple_newsletter name="true" terms_text="I agree to the terms and conditions" terms="http://www.google.com" button="GoYo!" thanks="Thank you very much for joining our mailing list!"]

**Hardcoded into a Theme**

*Paste this into your theme where you want the form displayed.*

`<?php
$snsf_args = array(
"name"=>1,
"title"=>"My New Title",
"button"=>"My New Button",
"terms" => "http://www.google.com",
"terms_text" => "this is my terms text",
"thanks" => "Thank you sooo much!"
);
echo do_newsletter($snsf_args);
?>`
