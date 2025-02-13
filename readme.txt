=== Convert Blocks to JSON ===
Contributors: badasswp
Tags: convert, blocks, json, gutenberg, editor.
Requires at least: 4.0
Tested up to: 6.7.1
Stable tag: 1.0.6
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Convert your WP blocks to JSON. Import & Export blocks across multiple WordPress websites easily & quickly. Generate JSON for Headless CMS websites.

== Installation ==

1. Go to 'Plugins > Add New' on your WordPress admin dashboard.
2. Search for 'Convert Blocks to JSON' plugin from the official WordPress plugin repository.
3. Click 'Install Now' and then 'Activate'.
4. Proceed to your Block Editor and locate the top right corner.
5. You should now see the 'Convert Blocks to JSON' icon available for use.

== Description ==

This plugin offers a powerful solution for <strong>importing</strong> and <strong>exporting</strong> WordPress <strong>blocks</strong> in JSON format, making it easy to move posts across different WP websites. It is also beneficial for WP engineers who are adopting a <strong>Headless CMS</strong> approach and would like a way to be able to fetch data from the front-end using tools like <strong>React</strong>, </strong>Vue</strong> & so on.

It's <strong>simple</strong>, yet very <strong>powerful</strong>!

= ✨ Getting Started =

Create a new Post or open an existing Post. Locate the <strong>Convert Blocks to JSON</strong> icon at the <strong>top right</strong> corner of the Block Editor and click on it. From here you can do the following actions:

1. <strong>View JSON</strong> - Generate JSON data of the post you are working on.
2. <strong>Import JSON</strong> - Import JSON data from a JSON file to the post you are working on.
3. <strong>Export JSON</strong> - Export JSON data of the post to a JSON file.

You can get a taste of how this works, by using the [demo](https://tastewp.com/create/NMS/8.0/6.7.0/convert-blocks-to-json/twentytwentythree?ni=true&origin=wp) link.

= ⚡ Why Convert Blocks to JSON =

1. This plugin is useful for users who want to be able to <strong>import</strong> and <strong>export</strong> articles or posts between different WordPress sites easily.

2. This plugin is beneficial for WP engineers who are adopting a <strong>Headless CMS</strong> approach and would like a way to be able to fetch data from the front-end using tools like <strong>React</strong>, </strong>Vue</strong> & so on.

= 🔌🎨 Plug and Play or Customize =

The <strong>Convert Blocks to JSON</strong> plugin is built to work right out of the box. Simply install, activate and start using in your Block Editor.

Want to add your personal touch? All of our documentation can be found [here](https://github.com/badasswp/convert-blocks-to-json). You can override the plugin's behaviour with custom logic of your own using [hooks](https://github.com/badasswp/convert-blocks-to-json?tab=readme-ov-file#hooks).

== Screenshots ==

1. Convert Blocks to JSON icon - Convert your blocks to JSON and vice versa.
2. Convert Blocks to JSON options - View, Import Blocks & Export Blocks.
3. Convert Blocks to JSON sidebar - See Convert Blocks to JSON on sidebar.

== Changelog ==

= 1.0.7 =
* Ensure `REST` response for blocks' imports & exports.
* Update Hook names `cbtj_rest_response` to `cbtj_rest_export`.
* Update function names.
* Update README docs.
* Tested up to WP 6.7.2.

= 1.0.6 =
* Fix breaking/faulty dependency.
* Fix linting issues.
* Tested up to WP 6.7.1.

= 1.0.5 =
* Fix CI/CD build process.
* Update README text content.
* Bump up plugin version.
* Tested up to WP 6.7.0.

= 1.0.4 =
* Provide graceful fallback for block arrays.
* Filter out empty|null blocks.
* Ignore `file_get_contents` warning.
* Update Permalink structure if empty, flush rules.
* Tested up to WP 6.6.2.

= 1.0.3 =
* Replace `mt_rand` with `string` version for asset enqueuing.
* Fix Bugs and Linting issues.
* Updated README notes with screenshots.
* Tested up to WP 6.6.2.

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
* Custom Hooks - `cbtj_rest_export`.
* Provided support for Arabic, Chinese, Hebrew, Hindi, Russian, German, Italian, Croatian, Spanish & French languages.
* Unit Tests coverage.
* Tested up to WP 6.6.1.

== Contribute ==

If you'd like to contribute to the development of this plugin, you can find it on [GitHub](https://github.com/badasswp/convert-blocks-to-json).

To build, clone repo and run `yarn install && yarn build`
