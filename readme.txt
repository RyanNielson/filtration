=== Filtration ===
Contributors: RyanNielson
Tags: filtered, filtration, filter, keywords, profanity, swearing, post, admin, comments, page, title, content
Requires at least: 2.7
Tested up to: 3.3.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: trunk

Remove any unwanted words from your page/post titles, content, and comments.

== Description ==

Filtration allows you to filter out any keywords you wish, and replace them with any specified text. This allows you to remove profanity, or any other content automatically from titles, content and comments. This replacement text can be applied to each character in a filtered word, or to the filtered word as a whole.

You can choose which WordPress elements to filter, including:

* Post/Page Titles
* Post/Page Content
* Comments

When chosing Filtration keywords, they can be strict or non-strict keywords. Non-strict keywords are removed ONLY when they stand alone in the content, with boundry characters on either site. Strict keywords are removed anywhere in the string.

i.e. If 'cake' is specified as a non-strict keyword, it will be filtered in the string 'the cake was great', but it will be left alone in the string 'pancakes are fantastic'. On the other hand, if 'cake' is strict, it will be filtered in both cases above and replaced with the filter character. 

Keywords are replaced with the Filter Character (specified in Filtration Options).

== Installation ==

This section describes how to install Filtration.

1. Download and extract the Plugin zip file.
2. Copy the Filration folder to your Plugin folder (wp-content/plugins/)
3. Activate the plugin via the 'Plugins' menu.
4. After activation, visit the settings page by clicking Filtration under the Settings menu.
5. Enter your keywords, selected the type of content to filter, and add a filter character.

== Changelog ==

= 1.2 =
* Added the ability to have the replacement character replace all characters in a filtered word, or the entire word.

= 1.1 =
* Reorganized code to prevent bad interactions with user code.
* Input now sanitized.
* Text validation now checks input to see if it matches required format.
* A few speed improvements and bug fixes.

= 1.0 =
* Added filtration of comments, titles and content.
