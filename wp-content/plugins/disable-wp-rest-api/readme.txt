=== Disable WP REST API ===

Plugin Name: Disable WP REST API
Plugin URI: https://perishablepress.com/disable-wp-rest-api/
Description: Disables the WP REST API for visitors not logged into WordPress.
Tags: rest, rest-api, api, json, disable
Author: Jeff Starr
Author URI: https://plugin-planet.com/
Donate link: https://monzillamedia.com/donate.html
Contributors: specialk
Requires at least: 4.7
Tested up to: 7.1
Stable tag: 2.6.9
Version:    2.6.9
Requires PHP: 5.6.20
Text Domain: disable-wp-rest-api
Domain Path: /languages
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Disables the WP REST API for visitors not logged into WordPress.



== Description ==

__Does one thing:__ Completely disables the WordPress REST API for visitors who are not logged into WordPress. No configuration required.

__Important:__ This plugin completely disables the WP REST API for visitors who are NOT logged in to WordPress. So not recommended if your site needs the WP REST API for any non-logged users.

👉 The fast, simple way to prevent abuse of your site's REST/JSON API
👉 Protects your site's REST data from all non-logged users and bots
👉 Uses only 4KB of code, so super lightweight, fast, and effective

🛠️ Pro version available! [Check out REST Pro Tools &raquo;](https://plugin-planet.com/rest-pro-tools/)



== Features ==

* Disable REST/JSON for visitors (not logged in)
* Disables REST header in HTTP response for all users
* Disables REST links in HTML head for all users
* 100% plug-and-play, set-it-and-forget solution


**How does it work?**

This plugin completely disables the WP REST API _unless_ the user is logged into WordPress. 

* For logged-in (authenticated) users, WP REST API works normally
* For logged-out (unauthenticated) users, WP REST API is disabled

What happens if logged-out visitor makes a JSON/REST request? They will get only a simple message:

	rest_login_required: REST API restricted to authenticated users.

This message may customized via the filter hook, `disable_wp_rest_api_error`. Check out [this post](https://wordpress.org/support/topic/not-entirely-for-non-techies/#post-12014965) for an example of how to do it.



== Pro Version ==

🛠️ Check out the Pro version, [REST Pro Tools](https://plugin-planet.com/rest-pro-tools/), loaded with many awesome features:

* One-click disable all routes
* One-click disable all /users routes
* Disable any specific user routes based on role
* Whitelist any user IDs
* Whitelist any IP addresses
* Customize the REST error message
* Customize the REST response code
* Always require or force SSL/TLS
* Disable all JSONP shenanigans
* One-click disable any REST API headers
* Add any post meta (custom field) to REST API
* Add any user meta (custom field) to REST API
* Add routes for site profile and author profile
* Add routes for featured images and post categories
* Add routes for post taxonomies and terms
* At-a-glance check status of REST API

The free version does only one thing: disables REST API for unauthenticated users. The PRO version can do that and much more! Take full control of the REST API with [REST Pro Tools&nbsp;&raquo;](https://plugin-planet.com/rest-pro-tools/)



== Privacy ==

This plugin does not collect or store any user data. It does not set any cookies, and it does not connect to any third-party locations. Thus, this plugin does not affect user privacy in any way. If anything it _improves_ user privacy, as it protects potentially sensitive information from being displayed/accessed via REST API.

Disable WP REST API is developed and maintained by [Jeff Starr](https://x.com/perishable), 15-year [WordPress developer](https://plugin-planet.com/) and [book author](https://books.perishablepress.com/).



== Installation ==

**How to Install**

1. Upload the plugin to your blog and activate
2. Done! No further configuration is required.

[More info on installing WP plugins](https://wordpress.org/documentation/article/manage-plugins/#installing-plugins-1)


**Testing**

To test that the plugin is working, log out of WordPress and then request `https://example.com/wp-json/` in a browser. See FAQs for more infos.


**Pro Version**

Need more control of the WP REST API? [Check out the Pro version&nbsp;&raquo;](https://plugin-planet.com/rest-pro-tools/)


**Like the plugin?**

If you like Disable WP REST API, please take a moment to [give a 5-star rating](https://wordpress.org/support/plugin/disable-wp-rest-api/reviews/?rate=5#new-post). It helps to keep development and support going strong. Thank you!


**Uninstalling**

To uninstall/remove the plugin, visit the Plugins screen, deactivate and delete the plugin. This plugin makes no changes to the WP database.



== Upgrade Notice ==

Visit the WordPress Plugins screen, locate the plugin, and click "Update" :)



== Frequently Asked Questions ==

**Why would anyone want to disable the REST API?**

Technically this plugin disables REST API only for visitors who are __not__ logged into WordPress. With that in mind, here are some good reasons why someone would want to disable REST API for non-logged users:

* The REST API may not be needed for non-logged users
* Disabling the REST API conserves server resources
* Disabling the REST API minimizes potential attack vectors
* Disabling the REST API prevents content scraping and plagiarism

I'm sure there are [other valid reasons](https://digwp.com/2018/08/secure-wp-rest-api/), but you get the idea :)


**What is the default access-denied message?**

When the user is logged in to WordPress, the normal REST API data will be displayed. When the user is *not* logged in, this is the default message:

`{"code":"rest_login_required","message":"REST API restricted to authenticated users.","data":{"status":401}}`


**How do I test that REST is disabled?**

Testing is easy:

1. Log out of WordPress
2. Using a browser, request `https://example.com/wp-json/`

If you see the following message, REST is disabled:

"rest_login_required: REST API restricted to authenticated users."

Then if you log back in and make a new request for `https://example.com/wp-json/`, you will see that REST is working normally.


**Does it disable REST functionality added by other plugins?**

Yes. If the user is NOT logged in, this plugin disables ALL endpoints that are registered with the WP REST API. Otherwise, if the user IS logged in, then this plugin does not block anything.


**Does this work with Gutenberg/Block Editor?**

Yes. It works the same regardless of which editor (Classic or Block) you are using.


**How to customize the error message?**

By default the plugin displays a message for unauthenticated users: "REST API restricted to authenticated users." To customize that message to whatever you want, add the following code via functions.php or simple [custom plugin](https://digwp.com/2022/02/custom-code-wordpress/):

`function disable_wp_rest_api_error_custom($message) {
	
	return 'Customize your message here.'; // change this to whatever you want
	
}
add_filter('disable_wp_rest_api_error', 'disable_wp_rest_api_error_custom');`


**How to allow access for Contact Form 7?**

As explained in this [thread](https://wordpress.org/support/topic/contact-forrm-7-bypass-solution/), the plugin Contact Form 7 requires REST API access in order for the contact form to work. To allow for this, follow [this guide](https://perishablepress.com/contact-form-7-disable-wp-rest-api/).


**Got a question?**

Send any questions or feedback via my [contact form](https://plugin-planet.com/support/#contact)



== Support development of this plugin ==

I develop and maintain this free plugin with love for the WordPress community. To show support, you can [make a donation](https://monzillamedia.com/donate.html) or purchase one of my books: 

* [The Tao of WordPress](https://wp-tao.com/)
* [Digging into WordPress](https://digwp.com/)
* [.htaccess made easy](https://htaccessbook.com/)
* [WordPress Themes In Depth](https://wp-tao.com/wordpress-themes-book/)
* [Wizard's SQL Recipes for WordPress](https://books.perishablepress.com/downloads/wizards-collection-sql-recipes-wordpress/)

And/or purchase one of my premium WordPress plugins:

* [BBQ Pro](https://plugin-planet.com/bbq-pro/) - Blazing fast WordPress firewall
* [Blackhole Pro](https://plugin-planet.com/blackhole-pro/) - Automatically block bad bots
* [Banhammer Pro](https://plugin-planet.com/banhammer-pro/) - Monitor traffic and ban the bad guys
* [GA Google Analytics Pro](https://plugin-planet.com/ga-google-analytics-pro/) - Connect WordPress to Google Analytics
* [Head Meta Pro](https://plugin-planet.com/head-meta-pro/) - Ultimate Meta Tags for WordPress
* [REST Pro Tools](https://plugin-planet.com/rest-pro-tools/) - Awesome tools for managing the WP REST API
* [Simple Ajax Chat Pro](https://plugin-planet.com/simple-ajax-chat-pro/) - Unlimited chat rooms
* [USP Pro](https://plugin-planet.com/usp-pro/) - Unlimited front-end forms

Links, tweets and likes also appreciated. Thank you! :)



== Changelog ==

If you like Disable WP REST API, please take a moment to [give a 5-star rating](https://wordpress.org/support/plugin/disable-wp-rest-api/reviews/?rate=5#new-post). It helps to keep development and support going strong. Thank you!

> 🛠️ Pro version now available! Get granular control over the REST API with [REST Pro Tools&nbsp;&raquo;](https://plugin-planet.com/rest-pro-tools/)


= 2.6.9 =

* Tests on WordPress 7.1

= 2.6.8 =

* Improves readme.txt documentation
* Adds blurb about new pro version
* Tests on WordPress 7.0


Full changelog @ [https://plugin-planet.com/wp/changelog/disable-wp-rest-api.txt](https://plugin-planet.com/wp/changelog/disable-wp-rest-api.txt)
