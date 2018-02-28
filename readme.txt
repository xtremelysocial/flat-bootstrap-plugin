=== Flat Bootstrap ===
Contributors: timnicholson
Tags: colors, widgets, flat
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JGJUJVK99KHRE
Requires at least: 4.3
Tested up to: 4.9.4
Stable tag: 1.0
License: GPLv3
License URI: http://www.opensource.org/licenses/GPL-3.0

Add awesome colored backgrounds to your widgets to make your widget areas look great.

== Description ==
This plugin is created by XtremelySocial.com for use with our Bootstrap-base themes, Flat Bootstrap, Pratt, Spot, and Link. The available colors are an adaptation of the flat-ui kit color palette. Padding is automatically added based on which widget area the widget is added to.

== Installation ==
1. Download the plugin
2. Unzip the folder into the `/wp-content/plugins/` directory
3. Activate the plugin through the \'Plugins\' menu in WordPress
4. Read the \"Frequently Asked Questions\" section and check out our website for information and examples on how best to use this plugin.

== Frequently Asked Questions ==

= What padding gets automatically added? =

The Sidebar and Footer widget areas get narrow padding added to all sides (24px). The other widget areas such as Home, Page Top, Page Bottom, get larger padding added to all sides (48px). 

= How do I use this plugin with other themes and widgets? =

Unfortunately, that just isn't possible. Every theme uses it's own unique structure and CSS classes so there is no way to handle the infinite possibilities there. Sure, we could just add our own colors and padding to other themes, but there is no way that would look good. Worse, it would often break the layout of the site for non-responsive themes.

_Changing Font Color_

When you choose a background color, the plugin will override the font color to be offwhite for dark colored backgrounds, but leave it at the theme default for lighter backgrounds. 
Usually you will want to adjust a single type of widget, such as a text widget or calendar widget, or newsletter sign-up form. In that case, use "Widget-Text", "Calendar-Wrap", etc.

Here is an example for changing the font color:
.textwidget {
	color: #f2f2f2;
}
<<< PUT ANSWER HERE >>>

_Changing Link Colors_

Similarly, it also will override the link and hover colors. However, depending on your theme's link color, you may want to further adjust it for some background colors. Here is how you would change the link colors and add an underline on link hover to all text widgets.

.textwidget a {
	color: #fff;
}
.textwidget a:hover,
.textwidget a:active {
	color: #000;
	text-decoration: underline;
}

<<< PUT ANSWER HERE >>>

_Changing Margin Between Widgets_

Some themes may not bother to put margin between widgets because they don't allow changing widget backgrounds and just let the typography spacing handle it. However, when you add a colored background to a widget area, you may want to have margin in between. Here is an example how to apply a little bottom-margin to every widget. Of course, you can use more specific widget types as discussed above.

.widget {
	margin-bottom: 11px;
}

== Screenshots ==
1. Sample Colored Sidebar Widgets
2. Sample Colored Full-Width Widgets
3. Widget Options in Admin

== Changelog ==

= v1.0 Feb 28, 2018 =
Initial public release

== Upgrade Notice ==
N/A - This is the initial release of the plugin