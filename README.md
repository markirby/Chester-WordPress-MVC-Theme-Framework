# Chester WordPress MVC Theme Framework Documentation

Chester is a lightweight WordPress MVC theming framework for people who want to build their own custom themes with an MVC approach.

To learn how to use it, the easiest thing to do is follow the tutorial and download the boilerplate example over at http://markirby.github.com/Boilerplate-Chester-WordPress-Theme/

## MVC Concepts

### Models built from standard WordPress calls

We believe in using standard WordPress template calls which return data that can then be pulled into an array for passing to Mustache templates. This keeps the HTML clean and free of PHP calls.

We provide a data helper file, Chester/wp_core_data_helpers.php to provide access to standard data easily gathered from WordPress. At present this is as far as the concept of Models is implemented. 

Here is an example of how we pull site data into an array for use in a header template.

	public static function getBlogInfoData() {
    return array(
      'title' => self::getBlogTitle(),
      'template_directory' => get_bloginfo('template_directory'),
      'charset' => get_bloginfo('charset'),
      'pingback_url' => get_bloginfo('pingback_url'),
    );
  }


### Views based on Mustache templates

You create views using [Mustache](http://mustache.github.com/).

Here is an example of a header template that displays the above data.

	<!DOCTYPE html>
	  <head>
	    <title>{{{title}}}</title>

	    <link rel="shortcut icon" href="{{template_directory}}/favicon.ico" />

	    <meta charset="{{charset}}" />
      <link rel="stylesheet" href="{{template_directory}}/css/global.css">
	    <link rel="pingback" href="{{pingback_url}}" />


### Controllers to pull everything together

A controller talks to the data helpers, loads the mustache template and can then be called from your WordPress template files.

Here's a sample function from a controller that loads the header data into the header template.

	public function header() {
	  echo $this->render('header', ChesterWPCoreDataHelpers::getBlogInfoData());
	}
	
## Install

Install Chester into lib/Chester

Add the following line to functions.php:

	require_once(dirname(__FILE__).'/lib/chester/require.php');
	
Create a default structure to store your controllers and templates in.

	mkdir mvc
	mkdir mvc/controllers
	mkdir mvc/templates

## Creating basic controllers and views

### Creating a controller

Controllers should extend ChesterBaseController. This then provides access to the templating functions. 

	class PageController extends ChesterBaseController {
		
		public function showPage() {
			...
		}
		
	}

You could group functions in a single controller, or create separate controllers for each template type. We favour the later.

Place controllers inside mvc/controllers.

### Calling a controller from a WordPress template page

[Create a template for WordPress](http://codex.wordpress.org/Template_Hierarchy), for example page.php which is used when pages are loaded.

Require the controller, init it and call the relevant function.

	require_once(dirname(__FILE__).'/mvc/controllers/page_controller.php');

	$siteController = new PageController();
	$siteController->showPage();

### Creating mustache templates

Create your mustache template within mvc/templates.

[The Mustache manual](http://mustache.github.com/mustache.5.html) will be your guide.

Here is an example template showing a post:

	<h1><a href="{{permalink}}">{{{title}}}</a></h1>
	<p>{{time}}</p>
	{{{content}}}

### Loading a template from within a controller

To load the above template, you can use the built in function render from within your controller.
	
	echo $this->render('template_name', array(
    'permalink' => get_permalink(),
    'title' => get_the_title(),
    'time' => get_the_time($dateFormat),
    'content' => self::getTheFilteredContentFromLoop(),
	));

### Loading templates with automatically included Header and footer feature

Create the following templates:

* header.mustache
* header_close.mustache
* site_title.mustache
* site_title_on_home.mustache
* footer.mustache 

Examples of each can be found in https://github.com/markirby/Boilerplate-Chester-WordPress-Theme

Once these are created, you can call the function renderPage from within your controller and get the template surrounded by header, footer and site title files, with wp_head() and wp_footer() called.

	echo $this->renderPage('template_name', array(
	  'permalink' => get_permalink(),
	  'title' => get_the_title(),
	  'time' => get_the_time($dateFormat),
	  'content' => self::getTheFilteredContentFromLoop(),
	));
	
## ChesterBaseController - base_controller.php

All controllers should extend this to inherit the following:

### render($templateName, $templateVars)

* $templateName - string - name of the template inside the root/mvc/templates folder, without the mustache extension
* $templateVars - array - array of variables to output in the template

Returns the template.

E.g (called from within a sub-controller):

	echo $this->render('template_name', array(
	  'permalink' => get_permalink(),
	  'title' => get_the_title(),
	  'time' => get_the_time($dateFormat),
	  'content' => self::getTheFilteredContentFromLoop(),
	));


### renderPage($templateName, $templateVars)

* $templateName - string - name of the template inside the root/mvc/templates folder, without the mustache extension
* $templateVars - array - array of variables to output in the template

Returns the following:

* root/mvc/templates/header.mustache 
* wp_head()
* root/mvc/templates/header_close.mustache 
* root/mvc/templates/site_title.mustache or, if home page root/mvc/templates/site_title_on_home.mustache
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

## ChesterWPCoreDataHelpers - wp_core_data_helpers.php

### getBlogInfoData()

Returns an array containing:

* all the variables listed in http://codex.wordpress.org/Function_Reference/bloginfo
* blog_title - if the helper was called from the homepage this will be the same as 'name', which is the sites name, but if the helper was called from any other page it will return the format "page/post title - site name"

This content is automatically available to your templates/header.mustache file.

### getWordpressPostsFromLoop($dateFormat = false)

* $dateFormat - string - how you want the date to be shown, as seen in http://codex.wordpress.org/Function_Reference/get_the_time

Runs the WordPress loop and returns an array of arrays, one array per post.

Each post contains the following:

* permalink - posts permalink (http://codex.wordpress.org/Function_Reference/get_permalink)
* title - posts title (http://codex.wordpress.org/Function_Reference/get_the_title)
* time - time of the post as set by $dataFormat, or defaults to "Nov 1st, 2012" (http://codex.wordpress.org/Function_Reference/get_the_time)
* content - the filtered html content of the post, the same as calling http://codex.wordpress.org/Function_Reference/the_content
* excerpt - the excerpt of the post (http://codex.wordpress.org/Function_Reference/get_the_excerpt)
* author - the author of the post (http://codex.wordpress.org/Function_Reference/get_the_author)
* author_link - link to the authors website (http://codex.wordpress.org/Function_Reference/get_the_author_link)
* the_tags - an array of tag objects which have been converted to associative arrays ready to use in mustache - for a list of available fields see (http://codex.wordpress.org/Function_Reference/get_the_tags)
* has_tags - true if tags were found, else false
* the_category - an array of tag objects which have been converted to associative arrays ready to use in mustache - for a list of available fields see (http://codex.wordpress.org/Function_Reference/get_the_category)
* has_categories - true if categories found, else false

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