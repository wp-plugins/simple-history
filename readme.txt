=== Plugin Name ===
Contributors: eskapism
Donate link: http://eskapism.se/sida/donate/
Tags: history, log, changes, changelog, audit, trail, pages, attachments, users, cms, dashboard, admin
Requires at least: 2.9.2
Tested up to: 3.0
Stable tag: trunk

View changes made by users within WordPress. It’s a history/change log/audit/recent changes-plugin.

== Description ==

Simple History is a plugin that shows recent changes made within WordPress.

Users of the system can see what articles have been created, modified or deleted,
and what attachments have been uploaded, modified or deleted? And so on. You get the idea.

It’s a pretty good plugin to have on websites where several people are 
involved: _”Has someone done anything today? Ah, Sarah uploaded 
the new press release and created an article for it. Good. Now I know.”_

It fits perfectly on your dashboard – or on a separate page. It's all configurable.

#### See it in action
See the plugin in action with this short screencast:
[youtube http://www.youtube.com/watch?v=4cu4kooJBzs]

![Screenshot of Simple History](http://eskapism.se/wordpress/wp-content/uploads/2010/07/simple-history-dashboard-screenshot.png "Screenshot of Simple History")

== Installation ==

1. Upload the folder "simple-history" to "/wp-content/plugins/"
1. Activate the plugin through the "Plugins" menu in WordPress
1. Done!

Now Simple History will be visible both on the dashboard and in the menu under pages.

== Feedback ==
Like the plugin? Dislike it? Got bugs or feature request?
Great! Contact me at par.thernstrom@gmail.com or at twitter.com/eskapism and hopefully 
I can do something about it.

== Screenshots ==

1. Simple History as it looks on your (well, mine anyway..) dashboard.

== Changelog ==

= 0.3 =
- page is now added under dashboard (was previously under tools). just feel better.
- mouse over on date now display detailed date a bit faster
- layout fixes to make it cooler, better, faster, stronger
- multiple events of same type, performed on the same object, by the same user, are now grouped together. This way 30 edits on the same page does not end up with 30 rows in Simple History. Much better overview!
- the name of deleted items now show up, instead of "Unknown name" or similar
- added support for plugins (who activated/deactivated what plugin)
- support for third party history items. Use like this:
simple_history_add("action=repaired&object_type=starship&object_name=USS Enterprise");
this would result in somehting like this:
Starship "USS Enterprise" repaired
by admin (John Doe), just now
- capability edit_pages needed to show history. Is this an appropriate capability do you think?

= 0.2 =
* Compatible with 2.9.2

= 0.1 =
* First public version. It works!
