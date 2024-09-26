=== Convert Blocks to JSON ===
Contributors: badasswp
Tags: convert, blocks, json, gutenberg, editor.
Requires at least: 4.0
Tested up to: 6.6.2
Stable tag: 1.0.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Convert your WP blocks to JSON.

== Installation ==

1. Go to 'Plugins > Add New' on your WordPress admin dashboard.
2. Search for 'Convert Blocks to JSON' plugin from the official WordPress plugin repository.
3. Click 'Install Now' and then 'Activate'.
4. Proceed to your Block Editor and locate the top right corner.
5. You should now see the 'Convert Blocks to JSON' icon available for use.

== Description ==

This plugin offers a powerful solution for exporting and importing WordPress blocks in JSON format, making it easier to manage and reuse block structures across different projects. It is particularly beneficial for developers and site owners who are adopting a Headless CMS approach on the front-end that is powered by tools like React, Vue & so on.

With the ability to seamlessly transfer block data between environments, you can maintain consistency, streamline content updates, and enhance the efficiency of your development workflow.

== Changelog ==

= 1.0.2 =
* Refactor: Use `is_user_permissible` for permissions callback.
* Updated Unit Tests coverage.
* Tested up to WP 6.6.2.

= 1.0.1 =
* Added Import functionality.
* Custom Hooks - `cbtj_rest_import`.
* New custom REST API endpoint - `cbtj/v1/import`.
* Updated translation files to cater for Import modal window text translations.
* Added more Unit Tests coverage.
* Tested up to WP 6.6.2.

= 1.0.0 =
* Convert & Export Blocks to JSON.
* Custom Hooks - `cbtj_rest_response`.
* Provided support for Arabic, Chinese, Hebrew, Hindi, Russian, German, Italian, Croatian, Spanish & French languages.
* Unit Tests coverage.
* Tested up to WP 6.6.1.

== Contribute ==

If you'd like to contribute to the development of this plugin, you can find it on [GitHub](https://github.com/badasswp/convert-blocks-to-json).

To build, clone repo and run `yarn install && yarn build`
