hatch-wordpress-mvc-framework
=============================

A lightweight wordpress MVC theming framework for people who want to build their own custom themes with an MVC approach

The idea:

You can use it, or not. So must be able to load all the usual files, and these work in the normal way.

When choosing to use the system, you should be able to simply require your chosen controller, and then init it and call relevant function. 

e.g. 

require_once(dirname(__FILE__).'/controllers/site_controller.php');

$siteController = new site_controller();
$siteController->showPage();

Then, in site_controller everything to do with mustache should already be loaded.

You can then go self::renderTemplate()

so ..

class SiteController extends BaseController {
	
	showPage();
	
}

no need to require BaseController, as its required when the theme loads, due to a wordpress action