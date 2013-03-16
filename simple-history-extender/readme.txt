=== Simple History Extender ===
Contributors: offereins
Tags: simple history, history
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extends the Simple History plugin with a class for plugins to easily support custom events.

== Description ==

Packaged with support for Widgets, bbPress, and Gravity Forms events.

Plugins that want to easily support Simple History should create a class extending SH_Extend and fill the required methods with their events and log messages.

== Installation ==

1. Upload the `simple-history-extender` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Developers: Extend the SH_Extend class with your custom events class
4. Activate the event modules through the 'Simple History' settings page in WordPress

== Changelog ==

= 1.3 =
* In development. Not released yet.
