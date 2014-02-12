=== Custom Banners ===
Contributors: ghuger, richardgabriel
Tags: banners, ads, rotating banners, custom banners, custom ads, custom rotating banners, random banners, random rotating banners
Requires at least: 3.8
Tested up to: 3.8.1
Stable tag: 1.2.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Custom Banners provides a simple interface to upload several banners and show a random or specific one to each visitor, using a shortcode.

== Description ==
Custom Banners is a WordPress plugin that allows you to easily manage several banners (ads) and display them on the front end.

- Create Banners Once, and Reuse Them Throughout Your Website -
Use custom banners to create resuable banners that your whole team can use! Setup the banners once, and you'll be able to re-use them throughout the website. Best of all, if you need to make an update you can just make it once.

- Update Your Banners Without Touching Your Code -
Simply place a shortcode into the page where you'd like your banner to appear, and then you'll never have to edit that page again!

Instead, you'll be able to manage that banner right from the WordPress dashboard, uploading new banners and taking down old ones as you like. All without having to edit your pages.

- Easily Add Captions and Call-To-Action Buttons To Your Custom Banners -
Custom Banners lets you optionally specify a caption and a call-to-action text and URL for each banner, making your banners an effective way to drive visitors to your most important pages.

Use the captions to announce a new special, and use the call-to-action button to let your customers claim it right away.

- With the Pro version, easily add Fading or Sliding banners throughout your site!

Example:
"Get Two Free Personal Training Sessions With A New Yearly Membership [Sign up Now]"

- Rotate Between Several Banners with Banner Groups -
Custom Banners also gives you many options for rotating banners within a position. So you can specify several banners which belong to a Banner Group, and then the software will automatically rotate through the banners in the Banner Group. 

- Automatically Publish New Banners At A Specified Time -
Do you have a banner that's announcing a new special, but you want to hide it until the right time? No problem - with the Publish Time feature of Custom Banners you can setup your banner now, but not show it until the right time.

== Installation ==

= Add the Plugin to your Website =

1. Download and Unzip http://downloads.wordpress.org/plugin/custom-banners.zip
2. Upload the contents of '/custom-banners/' to the '/wp-content/plugins/' directory
3. Activate Custom Banners through the 'Plugins' menu in WordPress

This section describes how to use the plugin on your website.

= Adding a New Banner =

Adding a New Banner is easy! There are 3 ways to start adding a new banner

*How to Add a New Banner*

1. Click on "+ New" -> Banner, from the Admin Bar or
2. Click on "Add New Banner" from the Menu Bar in the WordPress Admin or
3. Click on "Add New Banner" from the top of the list of Banners, if you're viewing them all.

= New Banner Content =

You have a few things to pay attention to:

- *Banner Title:* this is for internal reference.
- *Banner Body:* this is the content of your Banner. This will be output in the Call to Action bar.
- *Target URL:* where a user should be sent when they click on the banner or the call to action button.
- *Call To Action Text:* the "Call To Action" (text) of the button. Leave this field blank to hide the call to action button.
- *CSS Class:* any extra CSS classes that you would like applied to this banner.
- *Featured Image:* this image is shown as the banner.

= Editing a Banner =

*This is as easy as adding a New Banner!*

1. Click on "Banners" in the Admin Menu.
2. Hover over the Banner you want to Edit and click "Edit".
3. Change the fields to the desired content and click "Update".

= Deleting a Banner =

*This is as easy as adding a New Banner!*

1. Click on "Banners" in the Admin Menu.
2. Hover over the Banner you want to Delete and click "Delete". You can also change the Status of a Banner, if you want to keep it on file.

= Outputting Banners =

- To output a Random Banner, place the shortcode [banner] in the desired area of the Page or Post Content.
- To output a specific Banner, place the shortcode [banner id=123] in the desired area of the Page or Post Content.
- To output a Random Banner from a Specific Group, place the shortcode [banner group='test'] in the desired area of the Page or Post Content.
- To control the postion of the Caption, use the attribute caption_position="left".  Acceptable values are left, right, top, bottom.  For example, [banner caption_positon="left"].

= Outputting Fading or Sliding Banners =
* NOTE: This feature requires the Pro version of Custom Banners: http://goldplugins.com/our-plugins/custom-banners/
* LIVE Examples are available here: http://goldplugins.com/documentation/custom-banners-documentation/custom-banners-pro-examples/
- To output Random Banners from a Specific Group, place the shortcode [banner group='test' count='3' transition='scrollHorz' timer='2000'] in the desired area of the Page or Post Content.  Change the value of count from 3 to however many slides you want to use.  For transition, use either 'scrollHorz' or 'fadeIn'.  For timer, use 1000 times the number of seconds you want between transitions (ie, for 4 seconds input 4000.)
- To output Random Banners, place the shortcode [banner count='3' transition='scrollHorz' timer='2000'] in the desired area of the Page or Post Content.  Change the value of count from 3 to however many slides you want to use.  For transition, use either 'scrollHorz' or 'fadeIn'.  For timer, use 1000 times the number of seconds you want between transitions (ie, for 4 seconds input 4000.)
- Supported transitions are "scrollVert","scrollHorz","fadeIn","fadeOut","flipHorz","flipVert", and "tileSlide".

== Frequently Asked Questions ==

= What should I do if I don't want a call to action button? =

Simply leave the call to action text field blank

= How do I link the entire Banner to the Target URL and not just the Call To Action Button? =

On the Options screen, check the box next to "Link Entire Banner".


== Screenshots ==

1. This is the Add New Banner Page.
2. This is the List of Banners - from here you can Edit or Delete a Banner.
3. This is the List of Banner Groups - from here you can Create, Edit, or Delete a Banner Group.

== Changelog ==

= 1.2.1 =
* Adds ability to link the entire banner to the target URL, not just the call to action button.

= 1.2 =
* Adds support for Vertical Scrolling, Shuffling, Flipping, and Tiling.
* Enables Featured Image functionality for all sites.

= 1.1.1 =
* Bugfix

= 1.1 =
* Adds Support for JS Transitions.

= 1.0 =
* Released!

== Upgrade Notice ==

* 1.2.1: Update available!