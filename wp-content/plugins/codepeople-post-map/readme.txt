=== CP Google Maps ===
Contributors: codepeople
Donate link: http://wordpress.dwbooster.com/content-tools/codepeople-post-map
Tags:google maps,shortcode,map,maps,categories,post map,point,marker,list,location,address,images,geocoder,google maps,google
Requires at least: 3.0.5
Tested up to: 3.5
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

CP Google Maps allows to associate geolocation information to your posts and to integrate your blog with Google Maps in an easy and natural way.

== Description ==

Google Map features:

	► Insert a Google map in the best position within your blog
	► Deal with large volumes of dots or markers on the Google Maps
	► Uses Google Maps to discover additional entries related to the post
	► The location can be defined by physical address and point coordinates
	► Map markers customization
	► Allows to embed Google Maps in multiple languages
	► Allows several Google Maps controls and configuration options

**CP Google Maps** allows to insert a Google Maps in a post or in any of the WordPress templates that display multiple posts.

The Google Maps inserted in a single post displays a marker at the position indicated by the geolocation information pertaining to the post, but also shows markers of the last posts published in related categories. The number of markers to display on the Google Maps can be set in the plugin's settings.

The Google Maps inserted into a template displaying multiple posts will contain as many markers as posts making up the page with the associated geolocation info. When the mouse is hovered over the marker, the post to which it belongs gets highlighted.  

**Google Maps** has a wide range of settings to make your maps more versatile and adaptable.

**More about the Main Features:**

*   The plugin is capable of dealing with **large volumes of dots or markers**.
*   Another way for users to discover **additional entries related** to the post.
*   The **location information** can be defined by physical address and point coordinates.
*   Allows to **insert the Google maps** in the best position within your blog or simply **associate the geolocation information** to the post but without displaying the Google maps.
*   Markers **customization**.
*   Allows to display or hide the bubbles with markers information.
*   Allows to display a bubble opened by default.
*   Based on **Google Maps Javascript API Version 3**.
*   Allows to embed Google maps in **multiple languages**.
*   Displays **markers** belonging to posts of the same categories.
*   **Several customization options** are available: initial zoom, width, height, margins, alignment, map type, map language, the way the map is displayed in a single post (either fully deployed or icon to display the Google maps), enable or disable map controls, the number of points plotted on a Google map, as well as the class that will be assigned to the post when the mouse hovers over the marker associated with the post.

**Premium Features:**

*   Allows to load all points that belong to a specific category.
*   Allows to load the points associated to all posts.
*   The location information and description may be used in posts search.
*   Allows to associate multiple Google maps points to each post/page.
*   Allows to draw routes through points in the same post.
*   Allows to associate the Google maps with any public post_type in WordPress.
*   In non singular webpages, Google Maps display a map for each post.

Note 1: To display all points that belong to a specific category, it is required to insert the following shortcode [codepeople-post-map cat=3]. The number 3 represent the category ID, replace this number by the corresponding category's ID. To insert the code directly in a template, the snippet of code is:

            <?php echo do_shortcode('[codepeople-post-map cat=3]'); ?>
            
Note 2: To display all points in the website, use -1 as the category's ID: [codepeople-post-map cat=-1] or  <?php echo do_shortcode('[codepeople-post-map cat=-1]'); ?> for template.

If you prefer configure your map directly from the shortcode, then you must enter an attribute for each map feature to specify. For example:
            
            [codepeople-post-map width="500" height="500"]

The complete list of allowed attributes is:
            
            width, height, zoom, type, mousewheel, zoompancontrol, typecontrol
            
If you want more information about this plugin or another one don't doubt to visit my website:

[http://wordpress.dwbooster.com](http://wordpress.dwbooster.com "CodePeople WordPress Repository")

== Installation ==

**To install CP Google Maps, follow these steps:**

1.	Download and unzip the plugin
2.	Upload the entire codepeople-post-map/ directory to the /wp-content/plugins/ directory
3.	Activate the plugin through the Plugins menu in WordPress

== Interface ==

**Google Maps** offers several setting options and is highly flexible. Options can be set up in the Settings page (and will become the **default setup** for all maps added to posts in the future), or may be **specific to each post** to be associated with the Google maps (in this case the values are entered in the editing screen of the post in question.)

The settings are divided into two main groups, those belonging to the Google maps and those belonging to the geolocation point.

**Google Maps configuration options:**

*   Map zoom: initial map zoom.
*   Map width: Width of the map.
*   Map height: Height of the map.
*   Map margin: Margin of the map.
*   Map type: Select one of the possible types of maps to load (roadmap, satellite, terrain, hybrid).
*   Map language: a large number of languages is available to be used on maps, select the one that matches your blog's language.
*   Show info bubbles: display or hide the bubbles with the information associated to the points.
*   Display a bubble by default: display  a bubble opened by default.
*   Display map in post / page: When the Google maps are inserted in a post you can select whether to display the Google maps or display an icon, which displays the map, when pressed (if the Google maps are inserted into a template that allows multiple posts, this option does not apply)
*   Options: This setting allows you to select which map controls should be available.
*   Enter the number of points on the post / page map: When the Google maps are inserted into a post, points that belong to the same categories will be shown on the same Google map. This option allows you to set the number of points to be shown. When the Google maps are inserted into a template that allows multiple posts this option does not apply.
*   Highlight post when mouse hovers over related point on map:  When the Google maps are inserted into a template that allows multiple posts,  hovering the mouse over one of the points will highlight the associated post through assignment of a class in the next setup option. 
*   Highlight class: Name of the class to be assigned to a post to highlight when the mouse is hovered over the point associated with that post on the Google map.

**Configuration options related to the location points**

*   Location name: Name of the place you are indicating on the Google maps, alternatively, the name of the post can be used.
*   Location description: Description of the place you are showing on the Google maps. If left blank, the post summary will be used.
*   Select an image from media library: Select an image to associate with the localization point.
*   Address: Physical address of the geolocation point.
*   Latitude: Latitude of the geolocation point.
*   Longitude: Longitude of the geolocation point.
*   Verify: This button allows you to check the accuracy of the geolocation point address by updating the latitude and longitude where necessary.
*   Select the marker by clicking on the images: Select the bookmark icon to show on the Google maps.
*   Insert the map tag: Inserts a shortcode in the content of the post where the Google map is displayed with the features selected in the setup. You can attach geolocation information to a post but choose not to show the Google maps in the content of the post. In case you do want to display a map in the post content, use this button.

== Frequently Asked Questions ==

= Q: How many maps I can insert into a post? =

A: Only one, because only one point location can be associated with a single post.

= Q: How to insert Google maps into a template? =

A: Load the template in which you want to place the map in the text editor of your choice and place the following code in the position where you want to display the Google maps:
<?php echo do_shortcode ('[codepeople-post-map]'); ?>

= Q: Is possible to load all points in a category? =

A: To display all points that belong to a specific category, it is required to insert the following shortcode [codepeople-post-map cat=3]. The number 3 represent the category ID, replace this number by the corresponding category's ID. To insert the code directly in a template, the snippet of code is: 
<?php echo do_shortcode ('[codepeople-post-map cat=3]'); ?>

= Q: Is possible to load all points in the website? =

A: To display all points in the website use -1 as the category ID: [codepeople-post-map cat=-1] or <?php echo do_shortcode ('[codepeople-post-map cat=-1]'); ?> for template.

= Q: If I link geolocation information to a post but do not insert a Google map in it, will the geolocation information be available? =

A: If you have inserted a Google map into a template where multiple posts are displayed, then the geolocation information associated with posts is displayed on the map.

= Q: How can I disable the information window of point opened by default? =

A: Go to the settings of map (the settings page of plugin for settings of all maps, or the settings of a particular map), and uncheck the option "Display a bubble by default"

= Q: How can I disable all information windows of points? =

A: Go to the settings of map (the settings page of plugin for settings of all maps, or the settings of a particular map), and uncheck the option "Show info bubbles"

== Screenshots ==

1. Global maps settings
2. Point insertion's form 
3. Maps in action