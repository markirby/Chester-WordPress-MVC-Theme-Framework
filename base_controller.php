<?php

/**
 * @author   	Mark Kirby
 * @copyright	Copyright (c) 2012, Mark Kirby, http://mark-kirby.co.uk/
 * @license  	http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package  	Chester
 * @version  	0.1
 * @link     	https://github.com/markirby/Chester-WordPress-MVC-Theme-Framework
 * @link     	http://thisishatch.co.uk/
 */
 
class ChesterBaseController {
  
  protected $template = "";
  
  public function __construct() {
    $templatesFolderLocation = self::getTemplatesFolderLocation();
    
    Mustache_Autoloader::register();
    
    $this->template = new Mustache_Engine(array(
        'loader' => new Mustache_Loader_FilesystemLoader($templatesFolderLocation),
        'partials_loader' => new Mustache_Loader_FilesystemLoader($templatesFolderLocation)
    ));
    
  }

  public function render($templateName, $templateVars = false) {
    return $this->template->render($templateName, $templateVars);
  }
  
  public function renderPage($templateName, $templateVars = false) {
    echo $this->render('header', ChesterWPCoreDataHelpers::getBlogInfoData());
    wp_head();
    echo $this->render('header_close', array(
      'siteTitleHTML' => self::renderSiteTitle()
    ));
    echo $this->render($templateName, $templateVars);
    wp_footer();
    echo $this->render('footer');
  }

  public static function getTemplatesFolderLocation() {
    return str_replace('//','/',dirname(__FILE__).'/') . '../../mvc/templates'; 
  }
    
  public function renderSiteTitle() {
    if (is_home()) {
      return $this->render('site_title_on_home', array('blog_name' => get_bloginfo('name')));
    } else {
      return $this->render('site_title', array('blog_name' => get_bloginfo('name')));
    }
  }
  
  
  
}

?>