# Chester WordPress MVC Theme Framework Documentation

Chester is a lightweight WordPress MVC theming framework for people who want to build their own custom themes with an MVC approach.

To learn how to use it, the easiest thing to do is follow the tutorial and download the boilerplate example over at http://markirby.github.com/Boilerplate-Chester-WordPress-Theme/

## MVC Concepts

### Models built from standard WordPress calls

We believe in using standard WordPress template calls which return data that can then be pulled into an array for passing to Mustache templates. This keeps the HTML clean and free of PHP calls.

We provide a data helper file, Chester/wp_core_data_helpers.php to provide access to standard data easily gathered from WordPress. At present this is as far as the concept of Models is implemented. 

Here is an example of how we pull site data into an array for use in a header template.

```php
public static function getBlogInfoData() {
    return array(
        'title' => self::getBlogTitle(),
        'template_directory' => get_bloginfo('template_directory'),
        'charset' => get_bloginfo('charset'),
        'pingback_url' => get_bloginfo('pingback_url'),
    );
}
```


### Views based on Mustache templates

You create views using [Mustache](http://mustache.github.com/).

Here is an example of a header template that displays the above data.

```html
<!DOCTYPE html>
  <head>
    <title>{{{title}}}</title>
    
    <link rel="shortcut icon" href="{{template_directory}}/favicon.ico" />
	
    <meta charset="{{charset}}" />
    
    <link rel="stylesheet" href="{{template_directory}}/css/global.css">
    <link rel="pingback" href="{{pingback_url}}" />
```


### Controllers to pull everything together

A controller talks to the data helpers, loads the mustache template and can then be called from your WordPress template files.

Here's a sample function from a controller that loads the header data into the header template.

```php
public function header() {
    echo $this->render('header', ChesterWPCoreDataHelpers::getBlogInfoData());
}
```
	
## Install

Install Chester into lib/Chester

Add the following line to functions.php:

```php
require_once(dirname(__FILE__).'/lib/chester/require.php');
```
	
Create a default structure to store your controllers and templates in.

```shell
mkdir mvc
mkdir mvc/controllers
mkdir mvc/templates
```

## Creating basic controllers and views

### Creating a controller

Controllers should extend ChesterBaseController. This then provides access to the templating functions. 

```php
class PageController extends ChesterBaseController {

    public function showPage() {
        ...
    }

}
```

You could group functions in a single controller, or create separate controllers for each template type. We favour the later.

Place controllers inside mvc/controllers.

### Calling a controller from a WordPress template page

[Create a template for WordPress](http://codex.wordpress.org/Template_Hierarchy), for example page.php which is used when pages are loaded.

Require the controller, init it and call the relevant function.

```php
require_once(dirname(__FILE__).'/mvc/controllers/page_controller.php');

$siteController = new PageController();
$siteController->showPage();
```    

### Creating mustache templates

Create your mustache template within mvc/templates.

[The Mustache manual](http://mustache.github.com/mustache.5.html) will be your guide.

Here is an example template showing a post:

```html
<h1><a href="{{permalink}}">{{{title}}}</a></h1>
<p>{{time}}</p>
{{{content}}}
```

### Loading a template from within a controller

To load the above template, you can use the built in function render from within your controller.

```php	
echo $this->render('template_name', array(
    'permalink' => get_permalink(),
    'title' => get_the_title(),
    'time' => get_the_time($dateFormat),
    'content' => self::getTheFilteredContentFromLoop(),
));
```

### Loading templates with automatically included Header and footer feature

Create the following templates:

* header.mustache
* header_close.mustache
* site_title.mustache
* site_title_on_home.mustache
* footer.mustache 

Examples of each can be found in https://github.com/markirby/Boilerplate-Chester-WordPress-Theme

`header_close` can include the tag `{{{siteTitleHTML}}}`, which will output the content of `site_title` on a regular page, and `site_title_on_home` on the homepage, if you wish. Otherwise, leave them both blank.

Once these are created, you can call the function `renderPage` from within your controller and get the template surrounded by header, footer and site title files, with `wp_head()` and `wp_footer()` called.

```php
echo $this->renderPage('template_name', array(
    'permalink' => get_permalink(),
    'title' => get_the_title(),
    'time' => get_the_time($dateFormat),
    'content' => self::getTheFilteredContentFromLoop(),
));
```
	
## ChesterBaseController - base_controller.php

All controllers should extend this to inherit the following:

### render($templateName, $templateVars)

* `$templateName` - string - name of the template inside the root/mvc/templates folder, without the mustache extension
* `$templateVars` - array - array of variables to output in the template

Returns the template.

E.g (called from within a sub-controller):

```php
echo $this->render('template_name', array(
    'permalink' => get_permalink(),
    'title' => get_the_title(),
    'time' => get_the_time($dateFormat),
    'content' => self::getTheFilteredContentFromLoop(),
));
```


### renderPage($templateName, $templateVars)

* `$templateName` - string - name of the template inside the root/mvc/templates folder, without the mustache extension
* `$templateVars` - array - array of variables to output in the template

Returns the following:

* root/mvc/templates/header.mustache 
* wp_head()
* root/mvc/templates/header_close.mustache 
* The template passed in
* wp_footer()
* root/mvc/templates/footer.mustache

E.g (called from within a sub-controller):

	echo $this->render('template_name', array(
	  'permalink' => get_permalink(),
	  'title' => get_the_title(),
	  'time' => get_the_time($dateFormat),
	  'content' => self::getTheFilteredContentFromLoop(),
	));

You can override this in a subcontroller if you want, for example, to include navigation on every page with a custom array.

### renderSiteTitle() 

Renders the root/mvc/templates/site_title.mustache if not on home page
Renders the root/mvc/templates/site_title_home.mustache if on home page

This is good for SEO, allowing you to render the title in an h1 on the homepage and a p elsewhere.

## ChesterWPCoreDataHelpers - wp_core_data_helpers.php

### getBlogInfoData()

Returns an array containing:

* all the variables listed in http://codex.wordpress.org/Function_Reference/bloginfo
* blog_title - if the helper was called from the homepage this will be the same as 'name', which is the sites name, but if the helper was called from any other page it will return the format "page/post title - site name"

This content is automatically available to your templates/header.mustache file.

### getWordpressPostsFromLoop($dateFormat = false, $customFields = array(), $fetchAllPosts = false)

* $dateFormat - string - how you want the date to be shown, as seen in http://codex.wordpress.org/Function_Reference/get_the_time
* $customFields - array - array of custom fields you have associated with the post/posts via the ChesterAdminController. E.g. array('map', 'location', 'website')
* $fetchAllPosts - string - set to true to fetch all posts on the page, in menu order. You can change the menu order using a plugin such as [advanced post types ordering](http://www.nsp-code.com/premium-plugins/wordpress-plugins/advanced-post-types-order/)

Runs the WordPress loop and returns an array of arrays, one array per post.

Each post contains the following:

* permalink - posts permalink (http://codex.wordpress.org/Function_Reference/get_permalink)
* title - posts title (http://codex.wordpress.org/Function_Reference/get_the_title)
* time - time of the post as set by $dataFormat, or defaults to "Nov 1st, 2012" (http://codex.wordpress.org/Function_Reference/get_the_time)
* content - the filtered html content of the post, the same as calling http://codex.wordpress.org/Function_Reference/the_content
* excerpt - the excerpt of the post (http://codex.wordpress.org/Function_Reference/get_the_excerpt)
* author - the author of the post (http://codex.wordpress.org/Function_Reference/get_the_author)
* author_link - link to the authors website (http://codex.wordpress.org/Function_Reference/get_the_author_link)
* the_tags - an array of tag objects which have been converted to associative arrays ready to use in mustache - for a list of available fields see (http://codex.wordpress.org/Function_Reference/get_the_tags). Also includes 'tag_link', which is a link to the tag view.
* has_tags - true if tags were found, else false
* the_categories - an array of tag objects which have been converted to associative arrays ready to use in mustache - for a list of available fields see (http://codex.wordpress.org/Function_Reference/get_the_category).  Also includes 'category_link', which is a link to the tag view.
* has_categories - true if categories found, else false

Then it returns any custom fields, with the same name as you passed in.

Finally it returns featured images (if you set them), as follows:

* featured_image_url_thumbnail
* featured_image_url_medium
* featured_image_url_large
* featured_image_url_full

e.g. (called from within a controller)

	$posts = ChesterWPCoreDataHelpers::getWordpressPostsFromLoop();
	echo $this->renderPage('post_previews', array(
	  'posts' => $posts,
	  'next_posts_link' => get_next_posts_link(),
	  'previous_posts_link' => get_previous_posts_link()
	));
	
or
	
	$posts = ChesterWPCoreDataHelpers::getWordpressPostsFromLoop();
	echo $posts[0]['permalink'];


### getPosts($dateFormat = false, $postType = 'post', $numberPostsToFetch = -1, $customFields = array(), $oddOrEven = false)

To get posts outside of the loop to use elsewhere (for example on the home page), use the getPosts function.

* $dateFormat - string - how you want the date to be shown, as seen in http://codex.wordpress.org/Function_Reference/get_the_time
* $postType - string - post type to fetch
* $numberPostsToFetch - int/string - number of posts you wish to fetch
* $customFields - array - either an array of custom fields as strings (if you aren't using our admin features), or better - the fields array from your field block (see admin_controller.php below). If you pass the fields array, paragraph tags will be added to your textarea's automatically whenever there is a line break
* $oddOrEven - string - ODD to retrieve only odd posts, EVEN to retrieve only even posts, useful for displaying 2 rows of posts with left to right priority

## ChesterPatternPrimerController - pattern_primer/pattern_primer_controller.php

The pattern primer controller allows you to create a pattern primer page to show all your different templates in one place. [Read more about the concept](http://adactio.com/journal/5028/), and [view an example](http://patternprimer.adactio.com/). Ours is based on this example.

The boilerplate will take you through setting up a pattern primer. Here are the API docs:

### Loading the controller

	$patternPrimerController = new ChesterPatternPrimerController();
	
### showPatternPrimer($patternSets = array(), $patternsHTML = ""

Calling this will automatically include a set of basic content, h1 - h8, blockquote, link, text etc.

* $foldersToConvert - array - an array of folder names within mvc/templates. Each mustache template within these folders will be rendered as is, without any variables being passed in, unless the folder is named 'grids'. In case of folder named 'grids', variables named {{content_block_1}}, {{content_block_2}} up to 8 will be replaced with HTML to produce a grey block in the pattern primer.
* $patternsHTML - string - remaining HTML to include under the initial folders. Use renderCustomPatternGroup to provide this content.

Returns HTML to be echoed.

### renderCustomPatternGroup($patternsHTML, $patternTitle)

This generates a block of patterns for use in showPatternPrimer().

* $patternsHTML - string - HTML to render within the pattern group, use renderPattern() to generate it.
* $patternTitle - string - title to display above patterns

Returns HTML to be used in $patternsHTML field of showPatternPrimer().

### renderPattern($templateName, $templateVars = false)

Generates a single pattern.

* $templateName - string - name of the template inside mvc/templates. You can use subfolders, e.g. 'modules/template'.
* $templateVars - array - array of variables to display for template tags

Returns HTML to be used in $patternsHTML field of renderCustomPatternGroup()

### Example

	$patternPrimerController = new ChesterPatternPrimerController();

	$post = $patternPrimerController->renderPattern('post', array(
	  'post' => array(
	    'permalink' => 'http://brightonculture.co.uk',
	    'title' => 'Post title',
	    'time' => '12th Nov 2012',
	    'content' => '<p>Sample content</p>',
	  )
	));

	$postPreview = $patternPrimerController->renderPattern('post_previews', array(
	  'posts' => array(
	    'permalink' => 'http://brightonculture.co.uk',
	    'title' => 'Post preview title',
	    'time' => '12th Nov 2012',
	    'content' => '<p>Sample content</p>',
	  )
	));

	$patternGroup = $patternPrimerController->renderCustomPatternGroup($post . $postPreview, 'modules/');

	$patternPrimerController->showPatternPrimer(array('typography', 'grids'), $patternGroup);

## ChesterAdminController - admin_controller.php

The admin controller allows you to instantly create custom post types, with a selection of custom fields available courtesy of [wpalchemy](http://github.com/farinspace/wpalchemy/), which is included in Chester, but not maintained by us.

To use, create an instance of ChesterAdminController, passing in settings, as shown below:

	$galleryLocationBlock = array(
	  'name' => 'location',
		'blockTitle' => 'Gallery Location',
		'fields' => array(
			array(
				'name' => 'location',
				'labelTitle' => 'Location',
				'fieldType' => 'textField',
			),
			array(
				'name' => 'map',
				'labelTitle' => 'Link to a map',
				'fieldType' => 'textField',
			)
		)
	);

	$galleryInfoBlock = array(
	  'name' => 'other',
	  'blockTitle' => 'Other details',
	  'fields' => array(
	    array(
	      'name' => 'website',
	      'labelTitle' => 'Website address',
	      'fieldType' => 'textField'
	    )
	  )
	);

	$galleryCustomPostType = array(
		'name' => 'gallery',
		'displayName' => 'Gallery',
		'pluralDisplayName' => 'Galleries',
		'enablePostThumbnailSupport' => true,
		'fieldBlocks' => array($galleryLocationBlock, $galleryInfoBlock)
	);

	$adminSettings = array(
		'customPostTypes' => array($galleryCustomPostType)
	);

	$adminController = new ChesterAdminController($adminSettings);

This example shows you can create a number of blocks, with fields in each, for a custom post type of gallery.

The settings are as follows:

General

* customPostTypes - array - array of custom posts, described below
* thumbnailsInStandardPosts - boolean - set to true to enabled featured image in all standard posts (non custom posts)

Custom Post

* name - name for the system to call the custom post, this should be unique to your theme
* displayName - the name to use for displaying the post type in the back end
* pluralDisplayName - the name to use when describing the post type in plural
* enablePostThumbnailSupport - set to true to include a featured image box in the admin section for the post type
* fieldBlocks - an array of field blocks, described below
* supports - set to customize what of the usual posts appear, the default is array('title', 'editor', 'thumbnail', 'tags') so if you don't want any of those you can remove them. This is especially useful if you want to remove the main editor!

Field Block

* name - name of the field block, should be unique to this custom post
* blockTitle - label to appear at the top of the field block
* fields - array of fields, described below

Field

* name - name of the field, unique to this custom post
* labelTitle - title of the field, appears above it in the admin section
* fieldType - type of field to use, textField, textarea (comes with basic wordpress toolbar, you need to put html in here, name must not contain underscores or hyphens), imageUploader (to add custom images)

You then need to create a file for each block named [customPostType]_[blockName].php, inside mvc/admin_templates.

For above, you create the files:

* mvc/admin_templates/gallery_location.php
* mvc/admin_templates/gallery_other.php

Each of these needs to contain the following line of code:

	<?php ChesterWPAlchemyHelpers::showFields($mb); ?>

This is annoying, but a limitation of the WPAlchemy library we use which expects you to stick HTML into a php file. Instead we are bumping the code back into our system which will generate the HTML for you. In future I will address this.
