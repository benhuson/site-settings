=== Site Settings  ===
Contributors: husobj
Tags: admin, settings
Requires at least: 3.9
Tested up to: 4.5.3
Stable tag: 0.2
License: GPLv3
License URI: http://www.opensource.org/licenses/gpl-license.php

Manage custom site settings and global content fields (extendable by plugins/themes).

== Description ==

Use the Site Settings plugin to create admin fields for storing short text snippets for use in your theme templates. For example, an email or phone number that is displayed via your header.php template.

It can also be used for storing data which you may need to reference such as the ID of a page or a post category which you use to feature posts on your home page.

Storing these settings in the admin is preferable to hardcoding them into your templates so they can be updated easily.

[Read the documentation](https://github.com/benhuson/site-settings/wiki) to find out how to add admin fields and sections.

== Installation ==

1. Download the archive file and uncompress it.
2. Put the "site-settings" folder in "wp-content/plugins"
3. Enable in WordPress by visiting the "Plugins" menu and activating it.
4. [Read the documentation](https://github.com/benhuson/site-settings/wiki) to find out how to add admin fields and sections.

== Frequently Asked Questions ==

None at the moment.

== Screenshots ==

1. Initial site settings admin screen.
2. Site settings admin screen with section and fields.

== Changelog ==

= 0.2 =

* Add support for post type select menus, checkboxes and radio buttons.
* Fix custom select menus selected states.
* Fix `sprintf` mis-spelling causing fatal error in select menus.
* Textdomain must be a string, not constant, to work with parsers.

= 0.1 =

* Initial development.

== Upgrade Notice ==

= 0.2 =
Add support for post type select menus, checkboxes and radio buttons.

= 0.1 =
First Release
