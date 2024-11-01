=== TRT Post Views ===
Contributors: tamim34
Donate link: https://www.buymeacoffee.com/wptamim
Tags: post views, views counter, analytics, post statistics, page views
Requires at least: 4.5
Tested up to: 6.6
Stable tag: 1.2
Requires PHP: 7.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin for tracking and displaying post by views on your website.

== Description ==

TRT Post Views is a lightweight and efficient plugin that allows you to track and display the number of views for each post on your WordPress site. Easily monitor post popularity and engage your audience.

With TRT Post Views, you can enable post views tracking effortlessly through the WordPress admin settings. Customize where post views are displayed, whether at the beginning or end of posts, and choose to filter views based on IP addresses.

Display post views directly on your posts or pages using the `[trt_pvc_post_views]` shortcode. Additionally, utilize the `[trt_pvc_custom_query]` shortcode to showcase a post grid or archive sorted by views, providing an elegant and modern design for your content.

== Frequently Asked Questions ==

= How do I enable post views tracking? =

Post views tracking is enabled by default. You can customize settings in the WordPress admin under "TRT Post Views Settings."

= Can I display post views on my posts? =

Yes, you can use the `[trt_pvc_post_views]` shortcode to display post views on any post or page.

== Screenshots ==

1. Post views settings in the WordPress admin.
2. Example of post views displayed on a single post.
3. Display post grid with shortcode.

== Changelog ==

= 1.2 =
* Compatible with WordPress version 6.6

= 1.19 =
* Implemented pagination feature for displaying posts in a grid or archive format, allowing users to navigate through multiple pages of content seamlessly.
* Enhanced user experience by adding previous and next buttons to the pagination, providing intuitive navigation options.
* Improved plugin usability by integrating AJAX functionality for dynamic loading of content, reducing page reloads and enhancing performance.

= 1.18 =
* Ensured all variables, options, and output data are properly sanitized, escaped, and validated to enhance security.
* Updated direct file access prevention by adding the check for 'ABSPATH' at the beginning of PHP files.
* Fixed display of post views and post titles to ensure they are escaped when echoed to prevent potential XSS vulnerabilities.

= 1.17 =
* Added plugin usage instructions to the settings page for better user guidance.

= 1.14 =
* Added options to display total views at the beginning or end of posts.
* Fixed checkbox issue on the settings page.

= 1.13 =
* Improved performance and compatibility with the latest WordPress version.

== Upgrade Notice ==

= 1.1 =
Upgrade for improved performance and compatibility.

== Features ==

* Post Views Tracking
* Customizable Display
* IP Address Filtering
* Shortcode Support
* Grid and Archive Display
* Post grid pagination
* Column and posts per page adjustment features
