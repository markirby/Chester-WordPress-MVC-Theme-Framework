# Chester WordPress MVC Theme Framework Documentation

Chester is a lightweight WordPress MVC theming framework for people who want to build their own custom themes with an MVC approach

## MVC Concepts

### Models built from standard WordPress calls

We believe in using standard WordPress template calls which return data that can then be pulled into an array for passing to Mustache templates. This keeps the HTML clean and free of PHP calls.

We provide a data helper file, Chester/wp_core_data_helpers.php to provide access to standard data easily gathered from WordPress. At present this is as far as the concept of Models is implemented. 

Here is an example of how we pull site data into an array for use in a header template.

	public static function getHeaderData() {
    return array(
      'title' => self::getTitle(),
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
	  echo $this->render('header', ChesterWPCoreDataHelpers::getHeaderData());
	}