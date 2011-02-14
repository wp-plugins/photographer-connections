=== Photographer Connections ===
Contributors: Marty Thornley	
Tags: contact, integration, ShootQ, Pictage, SmugMug, Flickr, Picasa, photography
Requires at least: 2.9.0
Tested up to: 3.0.5
Stable tag: trunk

This plugin connects different photography related API's to WordPress. For Example: ShootQ integration allows Contact Form 7 forms to send info to ShootQ.

== Description ==

This plugin connects different photography related API's to WordPress. It adds certain template tags and short codes but for the most part is meant as a means of communication with the photography sites. Other plugins can then use that information.

For Example: ShootQ integration allows contact forms to send info to ShootQ, using Contact Form 7.

**Short Codes**

1. [shootq_form]
1. [pictage_events]
1. [smugmug_albums]

== Installation ==

Install and activate the plugin. 

= General =

* Look in the main admin menu for "Photographer Connections".
* Activate each module you want to use.
* Currently there is access to Pictage, SmugMug and ShootQ.

= Pictage =

* Activate Pictage at "Photographer Connections".
* Visit "Photographer Connections->Pictage".
* Add your Pictage account information.

Pictage allows you to list all your active events.

Just add the shortcode [pictage_events] to any post or page.

= SmugMug =

* Activate SmugMug at "Photographer Connections".
* Visit "Photographer Connections->SmugMug".
* Add your SmugMug account information.

To display all SmugMug albums, use [smugmug_albums].
To display one album, use [smugmug_albums album="My Album Name"]

Look on the SmugMug settings page after you save your account info and it will include shortcodes for all available albums.

= ShootQ =

* Activate ShootQ at "Photographer Connections".
* Visit "Photographer Connections->ShootQ".
* Add your ShootQ account information.

To embed the contact form provided by ShootQ, add the shortcode [shootq_form] to any post or page.

To have contact form information sent to ShootQ, you need to install and use Contact Form 7:
Install [Contact Form 7](http://wordpress.org/extend/plugins/contact-form-7/) and set up a form.

The plugin should also work with the contact forms in ProPhotoBlogs Version 3, but has not been fully tested.

= ShootQ with Contact Form 7 =

In order for your Contact Form 7 forms to send info to ShootQ, they must use fields with these names.

**General Info:**

* "name" or "first-name" and "last-name"
* "phonenumber"
* "email" 
* "type" (this is for the type of event - Portrait, Wedding, etc.)
* "referred_by" 
* "referrer_id" 
* "remarks" (a textarea to allow comments)
* "subject" (will be combined into the shootq remarks area)
* "message" (will be combined into the shootq remarks area)

**For Wedding Events:**

* "ceremony_location"
* "ceremony_start_time"
* "ceremony_end_time"
* "reception_location"
* "reception_start_time"
* "reception_end_time"
* "groomsmen_count"
* "bridesmaids_count"
* "guests_count"

**For Portrait Events:**

* "classifier" (should be a drop down list of types of shoots that you offer - kids, family, senior, etc.)
* "group_size"

== Frequently Asked Questions ==

**Do SmugMug images have to link to the image on SmugMug?**

For now, yes. But they work with the Shadowbox JS plugin, which will make them popup into a nice overlay window so that the user never leaves your site.

**How do I customize the SmugMug gallery?**

All this plugin does is get and display the thumbnail version of the images. You can use your theme's style.css to make them look nicer.

**Does it work with Multisite?**

Yes. This was built for [PhotographyBlogSites.com](http://photographyblogsites.com), which is multisite. The plugin even has a Super Admin level menu that allows Super Admins to enable or disable any of the modules site wide.

== Changelog ==

= 1.0 =
* fixed readme, corrected version number, description.

= 0.1 =
* Initial upload