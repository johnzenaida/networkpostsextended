=== Network Posts Extended ===
Contributors: johnzenausa,superfrontender
Tags: network global posts, network posts, global posts, multisite posts, shared posts, network posts extended
Donate link: https://wp-plugins.johncardell.com/
Requires at least: 4.0
Tested up to: 4.7.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The plugin is designed to share posts, pages, and custom post types from across entire network on any given page for any subdomain and the main blog. You may list them in single or double column mode. Add custom css styling to your text, images, whole container and title.

== Description ==
The plugin is designed to share posts, pages, and custom post types from across entire network on any given page for any subdomain and the main blog. You may list them in single or double column mode. Add custom css styling to your text, images, whole container and title.

You can specify categories and tags. All posts will be shown in ascending and descending order by date or post ID. You can specify how old (in days) the collected posts may be. Also you can specify how many posts should be displayed from each blog. You can set thumbnails image size and style or disable them.

You may also include or exclude posts, pages, categories, and blogs by using the appropriate arguments. They're all listed at the end of this file.

== Installation ==
You may install the plugin using one of the three following methods:<br />
1. Unzip file and using an ftp program upload it to the wp-content/plugins/ directory then activate in plugins page.<br />
2. Using the search field in the admin plugins area type in 'network posts extended' (without quotes) then install from there.<br />
3. Upload zip file through the standard plugins menu.<br />

Note: For multisite installations only. Do not Network Activate. Activate on each site separately. May also remove read more link at the end of each excerpt by post/page title.

== Frequently Asked Questions ==
Q) Should I network activate the plugin?<br />
A) No. Activate individually on each domain. Now works across entire network.

Q) May I only include an x amount of posts that I choose?<br />
A) Yes, use include_post= and put in your posts in comma separated format surrounded by double quotes.
Example include_post="5,78,896".

Q) My title is too long and looks ugly, anyway I can shorten it?<br />
A) You may shorten it using the argument title_length="10" will rounded it off to the last complete word before it reaches 10 characters.

Q) I would like to just show an X amount of random posts on the home page. Is it possible?<br />
A) Use the following arguments: random="true" and list="10" will show ten different posts randomly whenever the page is loaded.

Q) May I order my posts in specific order by date or title?<br />
A) Yes you may give specific ordering of your posts or pages via alphabetical order (by title), by date or page or post ID specific order.

Q) Does this plugin list pages from woocommerce?<br />
A) Yes it now does as of version 0.1.4. You may list via page/post id or via taxonomy="custom woocommerce category". Woocommerce default directory/taxonomy is product show you would just use the argument taxonomy="Product" which is the title of the directory. (Not case sensitive)<br /><br />

Note: Also works with Tips and Tricks eStore plugin.<br /><br />

Q) Will this plugin also include the prices from the products I create with the Woocommerce and eStore plugins?<br />
A) Yes it will including the following argument: include_price="woocommerce" or include_price="estore". If for some reason you have both plugins installed you would use include_price="estore|woocommerce" if you want to list them both.

Q) When I use the following argument wrap_start="&lt;div style="color:blue;"&gt;" and wrap_end="&lt;/div&gt;" the text doesn't change color.<br />
A) That's because since double quotes are used after the = sign single quotes must be used in the html. For example you would have to have wrap_start="&lt;div style='color:blue;'&gt;". Notice the single quotes in the html?

Q) Does this plugin work with custom post types. That is post_type="custom-post-type".<br>
A) Yes it now works with custom post types.



== Screenshots ==
1. Single Column Default
2. Single Column with Blue Header and Thumbnail Dimmensions size="240,160"
3. Double Column with Red Header
4. Example of custom image sizes

== Changelog ==
= 2.4 =
Fixed bug where it stopped working with posty_type="page" and now works with custom post types. So if you make a post type called listing you may now add, post_type="listing".
= 2.2 =
Fixed incompatibility problem between browsers. Now tested and works with Firefox, Chrome and Microsoft Edge. Should work with Safari and Opra browsers also.
= 2.0 =
Added the ability to resize images and use them as the default thumbnail.
= 1.5 =
This plugin now will show prices for commerce sites using the Woocommerce or WP eStore plugins.

= 1.4 =
The plugin can now list post and pages from any subomain/blog including woocommerce and eStore. No longer syntax error created.

= 1.3 =
Removed below custom class and added checkbox to remove read more link on excerpts.

Added text box to remove read more link individually by inserting the title of post/page.

Moved the location of the plugin from the tools menu to inside the settings menu for less confusion.

It is now named "Network Posts Ext" with the quotes of course.

= 1.2 =
Added custom class to plugins net_posts_extended.css file

.netsposts-read-more-link

This way you can remove the read more links from the excerpts by using the following attribute:

a.netsposts-read-more-link { visibility: hidden; }

= 1.1 =
Added ability to list posts in specific order by date, title or page (pertains to post_type=page only).

Arguments now work with paginate=false random=true

Fixed call to function error


= 1.0 =
Added two more arguments.

manual_excerpt_length=

post_height=


= 0.9 =
Added the function to be able to use your own custom classes in tools area.

Plus added the following arguments:

column_width (default (px): 200)

title_color (default: black)

text_color - color of text. Examples text_color="red" or text_color="#ff0000". Both will have the texts color turn red.

meta_info - Default true


= 0.8 =
wrap_title_start,wrap_title_end - wrap_image_start,wrap_image_end - wrap_text_start,wrap_text_end.

meta_width - Same as title length except in percentage to shorten long meta data.

Added the ability to show posts or pages randomly using the following argument: random="true"

The list= argument works with pagination= true or false (default: false)

= 0.10 =
Added the ability to have custom thumbnail sizes created on image upload.

You will be able to have image sizes that are also rectangular for better matching on listing your pages. For example an image with a width of 300px and height of 200px.

== Upgrade Notice ==
Please upgrade to get the latest features and security updates.

== List of Arguments ==

Network Posts Extended Shortcodes and Arguments<br /><br />
[netsposts include_blog="1,2,5" days="30" taxonomy="news" titles_only="false" show_author="true" thumbnail="true" size="90,90" image_class="alignleft" auto_excerpt="true" excerpt_length="150" paginate="true" list="5"]<br />
/*Including and/or Excluding pages, posts, categories, and blogs*/<br />
include_post &#150; list of posts/pages that you want to include (example: include_post="5" or include_post="5,8,153" for multiple posts.<br /><br />
exclude_post &#150; list of posts/pages that you want to exclude (example: exclude_post="5" &#150; exclude_post="5,8,153"<br /><br />
include_blog &#150; list of blogs, with the posts which will be displayed (default all blogs)<br /><br />
exclude_blog &#150; list of excluded blogs (default none) (works only if include_blogs argument is not present)<br /><br />
taxonomy &#150; list of categories to include in list. Default is all categories. Example: taxonomy="Books" or taxonomy="Digital Books,Product,News" for multiple categories. Use title of category for the taxonomy name.<br /><br />
Miscellaneous show/hide arguments<br />
days &#150; how old in days the post can be (default 0' &#150; no limit) Example: days="10" will only show the posts/pages which have been created within the last ten days.<br /><br />
titles_only &#150; if true shows titles only (default false) Example: titles_only="true" will only show the titles. Not the image or excerpt.<br /><br />
show_author &#150; if true shows a posts author (default false) Example: show_author="true"<br /><br />
Thumbnails<br />
thumbnail &#150; if true shows thumbnails (default false) Example: thumbnail="true"<br /><br />
size &#150; size of thumbnail (width, height) (default thumbnail) Example: size="50,50" will show a thumbnail which has a size of 50px high and 50px wide.<br /><br />
image_class &#150; CSS class for image (default post&#150;thumbnail) Example: image_class="custom-image-class"<br /><br />
Excerpts<br />
auto_excerpt &#150; if true an excerpt will be taken from post content, if false a post excerpt (shows the short description in the excerpt box. Note you will need to use a plugin to show this box when creating pages instead of posts) will be used (default false).<br /><br />
excerpt_length &#150; the length of excerpt (in characters) (auto_excerpt should be true)(default 400') Example: excerpt_length="500" will show the first 500 characters.<br /><br />
manual_excerp_length &#150; You can set the length of the manual excerpt. For example if someone has 500 words in the manual excerpt field it may be trimmed down to 400 like so: manual_excerpt_length="400" (defaul 9999)<br /><br />
Listing Designs<br />
post_type &#150; type of posts (default post) Example: post_type="page" will show pages not posts in the list. To show posts either don't include this argument (since posts are default) or use post_type="post". Now works with custom post types. So you may add post_type="mycustomposttype".<br /><br />
full_text &#150; full text instead of excerpt (default false) Example: full_text="true" will show entire text in content field in list.<br /><br />
Showing Date<br />
date_format &#150; format of the post date (default n/j/Y). Example this will show January 2 1963 for example. If you would like to show the date first just use: date_format="j/n/Y".<br /><br />
Custom HTML<br />
wrap_start, wrap_end &#150; you can wrap the posts for example: (wrap_start="&lt;div style='font&#150;weight:bold;vertical&#150;align:middle;' class='myclass'&gt;" wrap_end="&lt;/div&gt;")
wrap_title_start,wrap_title_end &#150; wrap_image_start,wrap_image_end &#150; wrap_text_start,wrap_text_end. Use the same way as wrap_start,wrap_end above. But will only wrap given argument.<br /><br />
page_title_style &#150; style for the page title (default: none) Example: page_title_style="italic" will make the title italic. For bold you may use: page_title_style="bold" for italic and bold use: page_title_style="italic,bold"<br /><br />
Miscellaneous List Arguments &#150; Pagination Links and Order Post/Page by Properties
end_size &#150; how many numbers on either the start and the end list edges (used for pagination) Example: end_size="3" will show the first and last three pages as links in numerical form.<br /><br />
mid_size &#150; how many numbers to either side of current page, but not including current page (used for pagination)<br /><br />
order_by &#150; Sort in ascending (default value) and descending order via the following arguments &#150; Ascending: order_post_by='alphabetical_order' order_post_by='date_order' order_post_by='page_order' and descending: order_post_by='alphabetical_order desc' order_post_by='date_order desc' order_post_by='page_order desc' (note: descending must be surrounded by single or double quotes because of the empty space after page_order<br /><br />
Pagination &#150; When list is to have multiple pages
paginate &#150; if true the result will be paginated (default false) Example: paginate="true" will break the list in to multiple pages by the list argument.<br /><br />
list &#150; how many posts per page (default 10) Example: list="20" will show the last 20 posts or pages. If paginate="true" is used above then will break the list in to pages showing 20 posts or pages on each page.<br /><br />
prev_next &#150; Whether to include the previous and next links in the list or not (used for pagination. Default: true)<br /><br />
prev &#150; the previous page link text. Works only if prev_next argument is set to true. (Default: &#xab; Previous)<br /><br />
next &#150; The next page text. Works only if prev_next argument is set to true. Example: next="New Posts" will replace the default &#150; Next &#150; with &#150; New Posts. (Default: Next &#xbb;)<br /><br />
random &#150; Set to true to show posts randomly. To show an x amount of posts randomly make sure the list argument is set to the amount you want. (Default: set to false)<br />
Custom Arguments<br />
Titles<br />
title &#150; custom title (default: none) Example: title="Joe's Favorite Bicycles"<br /><br />
title_color &#150; Color of the title text. Example: title_color="red" or title="color:#ff0000" both will give you a color of red. (Default black)<br /><br />
title_length &#150; Cuts off the title at X amount of characters so won't make long wrap around which looks ugly. The length is in characters including spaces and symbols (Default 999)<br /><br />
include_link_title &#150; This will now make all titles clickable (default false). If you want the titles to also link to the post or page set this argument to true. Example: include_link_title="true"<br /><br />
exclude_link_title &#150; This will exclude certain posts/pages from the title being clickable. For example if you don't want the title to link to posts 8,45,47 you would use: exclude_link_title_posts="8,45,47"
Custom Column Designs<br />
column &#150; number of columns (default: 1)<br />
column_width &#150; Width of column in pixels. Example column_width="250". (Default: 200)<br />
post_height &#150; Sets the default height for all posts. Recommended for 2 column mode. For example if manual_excerpt_length="400" or excerpt_length="400" and you want posts with less of an excerpt to have same dimensions use this feature. post_height="300" will give a standard height of 300 pixels. So if post has less characters of text will still keep square shape so titles line up nicely.<br /><br />
meta_info &#150; Example: meta_info="false" (Default 'true')<br /><br />
meta_length &#150; Example: meta_length="75%" (Default 100%)<br /><br />
menu_name &#150; name of the menu (should be created in Appearance > Menu)(default: The one created in Appearance > Menu)<br /><br />
menu_class &#150; CSS class for the menu. Example menu_class="menu-class". Separate multiple classes with commas.<br /><br />
container_class &#150; the CSS class that is applied to the menu container<br /><br />
<strong>Using Custom Image Sizes:</strong><br />
Under settings > Network Posts Ext menu you will see a box to add a custom image size. You may name it anything you like and use the default alias name or your own. Wants created you must include the following in your shortcode:<br />thumbnail="true" size="Name of Alias". For example if what is listed in the alias box is 600x400 then it would be size="600x400" so your thumbnails will have a size of 600px wide and 400px high. You may change the diplayed size of these images using custom css.<br />For example you may create a class called img-size-300x200 and change the displayed image size thus: .img-size-300x200 &#x7b; height:300px;width:200px; &#x7d;. Then add this to the shortcode: image_class="img-size-300x200".<br />
For a complete tutorial please visit: <a href='https://wp-plugins.johncardell.com/' target='ejejcsingle'>https://wp-plugins.johncardell.com/</a>