=== Error Log Monitor ===
Contributors: whiteshadow
Tags: dashboard widget, administration, error reporting, admin, maintenance, php
Requires at least: 3.4
Tested up to: 3.4.2
Stable tag: 1.1

Adds a Dashboard widget that displays the latest messages from your PHP error log. It can also send logged errors to email.

== Description ==

This plugin adds a Dashboard widget that displays the latest messages from your PHP error log. It can also send you email notifications about newly logged errors.

**Features**

* Automatically detects error log location.
* Explains how to configure PHP error logging if it's not enabled yet.
* The number of displayed log entries is configurable.
* Sends you email notifications about logged errors (optional).
* Configurable email address and frequency.
* You can easily clear the log file.
* The dashboard widget is only visible to administrators.
* Optimized to work well even with very large log files.

**Usage**

Once you've installed the plugin, go to the Dashboard and enable the "PHP Error Log" widget through the "Screen Options" panel. The widget should automatically display the last 20 lines from your PHP error log. If you see an error message like "Error logging is disabled" instead, follow the displayed instructions to configure error logging.

Email notifications are disabled by default. To enable them, click the "Configure" link in the top-right corner of the widget and enter your email address in the "Periodically email logged errors to:" box. If desired, you can also change email frequency by selecting the minimum time interval between emails from the "How often to send email" drop-down.

== Installation ==

Follow these steps to install the plugin on your site:

1. Download the .zip file to your computer.
2. Go to *Plugins -> Add New* and select the "Upload" option.
3. Upload the .zip file.
4. Activate the plugin through the *Plugins -> Installed Plugins" page.
5. Go to the Dashboard and enable the "PHP Error Log" widget through the "Screen Options" panel.
6. (Optional) Click the "Configure" link in the top-right of the widget to configure the plugin.

== Screenshots ==

1. The "PHP Error Log" widget added by the plugin. 
2. Dashboard widget configuration screen.

== Changelog ==

= 1.1 = 
* Fixed plugin homepage URL.
* Fix: If no email address is specified, simply skip emailing the log instead of throwing an error.
* Tested with WordPress 3.4.2.

= 1.0 =
* Initial release.
