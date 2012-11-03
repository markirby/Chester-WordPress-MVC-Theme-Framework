<?php

require_once(dirname(__FILE__).'/lib/mustache/Autoloader.php');
require_once(dirname(__FILE__).'/lib/mustache/Compiler.php');
require_once(dirname(__FILE__).'/lib/mustache/Context.php');
require_once(dirname(__FILE__).'/lib/mustache/Engine.php');
require_once(dirname(__FILE__).'/lib/mustache/HelperCollection.php');
require_once(dirname(__FILE__).'/lib/mustache/Loader.php');
require_once(dirname(__FILE__).'/lib/mustache/Loader/MutableLoader.php');
require_once(dirname(__FILE__).'/lib/mustache/Loader/ArrayLoader.php');
require_once(dirname(__FILE__).'/lib/mustache/Loader/FilesystemLoader.php');
require_once(dirname(__FILE__).'/lib/mustache/Loader/StringLoader.php');
require_once(dirname(__FILE__).'/lib/mustache/Parser.php');
require_once(dirname(__FILE__).'/lib/mustache/Template.php');
require_once(dirname(__FILE__).'/lib/mustache/Tokenizer.php');

class hwpMVCBaseController {
  
  private $template = "";
      
  public function __construct() {
    Mustache_Autoloader::register();
    $templatesFolderLocation = str_replace('//','/',dirname(__FILE__).'/') .'../../mvc/templates';
    $this->template = new Mustache_Engine(array(
        'loader' => new Mustache_Loader_FilesystemLoader($templatesFolderLocation),
        'partials_loader' => new Mustache_Loader_FilesystemLoader($templatesFolderLocation)
    ));
    
  }

  public function render($templateName, $templateVars = false) {
    return $this->template->render($templateName, $templateVars);
  }
  
  public function renderPage($templateName, $templateVars = false) {
    echo $this->render('header', hwpMVCWPHelpers::getHeaderData());
    wp_head();
    echo $this->render('header_close');
    echo $this->getSiteTitle();
    echo $this->render($templateName, $templateVars);
    wp_footer();
    echo $this->render('footer');
  }
  
  private function getSiteTitle() {
    if (is_home()) {
      return $this->render('site_title_on_home', array('title' => get_bloginfo('name')));
    } else {
      return $this->render('site_title', array('title' => get_bloginfo('name')));
    }
  }
}

?>