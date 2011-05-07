=== Photographer Connections ===
Contributors: Marty Thornley	
Tags: contact form 7, ShootQ, Pictage, SmugMug, photography, Album Exposure
Requires at least: 3.0
Tested up to: 3.1
Stable tag: trunk

This plugin connects different photography related API's to WordPress. For Example: ShootQ integration allows Contact Form 7 forms to send info to ShootQ.

== Description ==

This plugin connects different photography related API's to WordPress. It adds certain template tags and short codes but for the most part is meant as a means of communication with the photography sites. Other plugins can then use that information.

For Example: ShootQ integration allows contact forms to send info to ShootQ, using Contact Form 7.

**Short Codes**

1. [shootq_form]
1. [pictage_events]
1. [smugmug_albums]
1. [album_exposure]

== Installation ==

Install and activate the plugin. 

You will need accounts with each site to use them:
* [Pictage](http://pictage.com)
* [ShootQ](http://shootq.com)
* [SmugMug](http://smugmug.com)
* [Album Exposure](http://albumexposure.com)

= General =

* Look in the main admin menu for "Photographer Connections".
* Activate each module you want to use.
* Currently there is access to Pictage, SmugMug and ShootQ.

= Album Exposure =

* Activate Pictage at "Photographer Connections".
* Visit "Photographer Connections->Album Exposure".
* Add your Album Exposure user name.

Add short code [album_exposure] to any page to embed the Album Exposure app inside your site.
Select the 'Album Exposure' page template to make it go full screen.
If the 'Album Exposure' template was not added to your theme, you can download a sample from 'Photographer Connections->Album Exposure'

[http://albumexposure.com](http://albumexposure.com)

= Pictage =

* Activate Pictage at "Photographer Connections".
* Visit "Photographer Connections->Pictage".
* Add your Pictage account information.

Pictage allows you to list all your active events.

Just add the shortcode [pictage_events] to any post or page.

[http://pictage.com](http://pictage.com)

= SmugMug =

* Activate SmugMug at "Photographer Connections".
* Visit "Photographer Connections->SmugMug".
* Add your SmugMug account information.

To display all SmugMug albums, use [smugmug_albums].
To display one album, use [smugmug_albums album="My Album Name"]

Look on the SmugMug settings page after you save your account info and it will include shortcodes for all available albums.

[http://smugmug.com](http://smugmug.com)

= ShootQ =

* Activate ShootQ at "Photographer Connections".
* Visit "Photographer Connections->ShootQ".
* Add your ShootQ account information.

To embed the contact form provided by ShootQ, add the shortcode [shootq_form] to any post or page.

To have contact form information sent to ShootQ, you need to install and use Contact Form 7:
Install [Contact Form 7](http://wordpress.org/extend/plugins/contact-form-7/) and set up a form.

The plugin should also work with the contact forms in ProPhotoBlogs Version 3, but has not been fully tested.

[http://shootq.com](http://shootq.com)

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

**What is my Pictage Studio ID?**

Log in at Pictage and go to My Account > Manage Studio. It's usually your first and 
last initials, followed by 3 numbers. I.E. AA123

**Some of my Pictage events are not listed, why?**

Be sure the event is set to be "searchable" from "Edit Info & Settings".

**What if I don't want a Pictage event listed?**

Be sure the event is not set to be "searchable" from "Edit Info & Settings".

**Do SmugMug images have to link to the image on SmugMug?**

For now, yes. But they work with the Shadowbox JS plugin, which will make them popup into a nice overlay window so that the user never leaves your site.

**How do I customize the SmugMug gallery?**

All this plugin does is get and display the thumbnail version of the images. You can use your theme's style.css to make them look nicer.

**Does it work with Multisite?**

Yes. This was built for [PhotographyBlogSites.com](http://photographyblogsites.com), which is multisite. The plugin even has a Super Admin level menu that allows Super Admins to enable or disable any of the modules site wide.

== Changelog ==

= 1.3.1 =

fixed glitch in smugmug album display in shortcodes

= 1.3 =

Fixed SmugMug Error reporting, removing uncaught exception bugs
Improved SmugMug gallery displays
Added some default styling to SmugMug Galleries, option to include or not.

= 1.2 =

fixed some bugs

= 1.1 =

* Ready for 3.1...
* Made admin menus ready for new network dashboard
* fixed a couple typos in sample module. Doesn't effect plugin operation.

**New Module**
Album Exposure - [visit albumexposure.com](http://albumexposure.com)

= 1.0 =

* fixed readme, corrected version number, description.

= 0.1 =

* Initial upload