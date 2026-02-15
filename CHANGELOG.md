# Changelog

## 1.3.0
* Feat: Add language translations for Japanese, Indonesia, Turkish, Polish, Dutch, Danish, Brazil, Portuguese.
* Feat: Add Image import functionality across websites.
* Feat: Add custom hook - `cbtj.innerBlocks`.
* Feat: Add Notification modal during import.
* Fix: Import issues with `core/list` and `core/list-item` blocks.
* Fix: Import issues with `core/pargraph` block.
* Fix: Import issues with `core/heading` block.
* Fix: Import issues with `core/details` block.
* Fix: Import issues with `core/cover` block.
* Fix: Incorrectly quoted translation bits.
* Refactor: Replace `get_400_response` with `get_error_response`.
* Test: Add e2e tests for plugin codebase.
* Chore: Add pull request template to repo.
* Docs: Update README docs.
* Tested up to WP 6.9.

## 1.2.1
* Specify `wordpress-plugin` as Composer package type.
* Tested up to WP 6.9.

## 1.2.0
* Fix: Resolve `Image` block import issues.
* Fix: Missing post title during import.
* Feat: Add custom hooks `cbtj_import_block`, `cbtj_export_block`, `cbtj_blocks`.
* Test: Add PHP unit tests to improve code.
* Docs: Update README docs.
* Tested up to WP 6.8.

## 1.1.0
* Feat: Add REST namespace filter `cbtj_rest_namespace`.
* Refactor: Use classes for PHP codebase.
* Docs: Improve README docs.
* Tested up to WP 6.7.2.

## 1.0.9
* Bump up plugin version.
* Tested up to WP 6.8
* Update README docs.

## 1.0.8
* Enforce WP style linting across plugin.
* Add local dev env setup for WP.
* Update README docs.
* Tested up to WP 6.7.2.

## 1.0.7
* Ensure `REST` response for blocks' imports & exports.
* Update Hook names `cbtj_rest_response` to `cbtj_rest_export`.
* Update function names.
* Update README docs.
* Tested up to WP 6.7.2.

## 1.0.6
* Fix breaking/faulty dependency.
* Fix linting issues.
* Tested up to WP 6.7.1.

## 1.0.5
* Fix CI/CD build process.
* Update README text content.
* Bump up plugin version.
* Tested up to WP 6.7.0.

## 1.0.4
* Provide graceful fallback for block arrays.
* Filter out empty|null blocks.
* Ignore `file_get_contents` warning.
* Update Permalink structure if empty, flush rules.
* Tested up to WP 6.6.2.

## 1.0.3
* Replace `mt_rand` with `string` version for asset enqueuing.
* Fix Bugs and Linting issues.
* Updated README notes with screenshots.
* Tested up to WP 6.6.2.

## 1.0.2
* Refactor: Use `is_user_permissible` for permissions callback.
* Updated Unit Tests coverage.
* Tested up to WP 6.6.2.

## 1.0.1
* Added Import functionality.
* Custom Hooks - `cbtj_rest_import`.
* New custom REST API endpoint - `cbtj/v1/import`.
* Updated translation files to cater for Import modal window text translations.
* Added more Unit Tests coverage.
* Tested up to WP 6.6.2.

## 1.0.0 (Initial Release)
* Convert & Export Blocks to JSON.
* Custom Hooks - `cbtj_rest_response`.
* Provided support for Arabic, Chinese, Hebrew, Hindi, Russian, German, Italian, Croatian, Spanish & French languages.
* Unit Tests coverage.
* Tested up to WP 6.6.1.
