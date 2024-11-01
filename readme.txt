=== Simple SEO Pack ===
Contributors: vtardia
Donate link: http://www.vtardia.com/simple-seo-pack/
Tags: seo, metadata, keywords, meta tags
Requires at least: 2.5
Tested up to: 4.5
Stable tag: 1.1.3.8

Simple SEO is a quick way to add HTML meta tags to your site and pages using WP integrated custom fields feature.

== Description ==

With Simple SEO you can set your custom content for the "keywords" and "description" meta tags in each post or page using the standard WordPress metabox interface.

You can also set default values for each meta tag which will be displayed in all your pages, although this is not a SEO best practice.

= Available languages =

 * English
 * Italian
 * Romanian by Web Geek Science ([Web Hosting Geeks](http://webhostinggeeks.com/))
 * Serbo-Croatian by Borisa Djuraskovic ([Webhostinghub](http://www.webhostinghub.com/))
 * Ukrainian by Michael Yunat ([http://getvoip.com]("http://getvoip.com/blog))
 * Indonesian by Jordan Silaen ([http://chameleonjohn.com]("http://chameleonjohn.com))

== Installation ==

1. Download Simple SEO Pack plugin
2. Extract the zipped archive
3. Upload the directory `simple-seo-pack` to the `wp-content/plugins`
   directory of your Wordpress installation
4. Activate the plugin from your WordPress admin 'Plugins' page.
5. Configure plugin options in Settings -> Simple SEO menu
6. Customize you posts and pages keywords and description using the "Simple
   SEO Tags" metabox.

== Frequently Asked Questions ==

= Does qTranslate support work on values set through the options page? =

Yes, but you have to insert the qTranslate tags manually (eg. `<!--:it-->Contenuto italiano<!--:en-->English content<!--:-->`).

= How can I convert metadata inserted using the previous version of this plugin? =

You can update your database manually, see the upgrade notice below.

== Screenshots ==

1. Simple SEO metabox displayed on a single page/post edit form
2. Simple SEO options page where you can set global keywords and description


== Changelog ==

= 1.1.3.8 =

 * Added Indonesian localization provided by Jordan Silaen ([http://chameleonjohn.com]("http://chameleonjohn.com))

= 1.1.3.7 =

 * Added Ukrainian localization provided by Michael Yunat ([http://getvoip.com]("http://getvoip.com/blog))

= 1.1.3.6 =

 * Marked static methods as "static"
 * Support for qTranslate is deprecated, actually it's commented for this release, will be removed on the next one

= 1.1.3.5 =

 * Added Serbo-Croatian localization provided by Borisa Djuraskovic ([Webhostinghub](http://www.webhostinghub.com/))

= 1.1.3.4 =

 * Fixed a couple of Javascript bugs in qTranslate integration

= 1.1.3.3 =

 * Fixed a bug that caused metatag display (with random data) on archive pages

= 1.1.3.2 =

 * Fixed a bug that caused a PHP error on 404 pages
 * Added Romanian localization provided by [Web Geek Science](http://webhostinggeeks.com/)

= 1.1.3.1 =

 * Fixed a bug that caused a PHP alert while loading the JS for qTranslate compatibility

= 1.1.3 =

 * Displays custom meta-box for filling meta tags.
 * Enable/disable default meta tags for each post/page (even if it's not
   recommended for a right SEO optimization).
 * Localization POT file and Italian translation compiled.
 * Support for multi-language content using the qTranslate plugin.

= 1.0.1 =

 * Initial version. Uses hand-typed custom fields.

== Upgrade Notice ==

= 1.1.3 =

If you are upgrading from version 1.0.1 which used hand-typed custom fields,  you may want to update your database in order to keep your previous data.

== Manual Database Update ==

In order to update your database you can run the following queries using your favorite MySQL admin tool, changing your table prefix if needed (a backup is strongly recomended).

    UPDATE wp_postmeta SET meta_key = '_sseo_meta_keywords' WHERE meta_key = 'meta_keywords';

    UPDATE wp_postmeta SET meta_key = '_sseo_meta_description' WHERE meta_key = 'meta_description';
