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
 
class ChesterAdminController {
  
  private $settings;
  
  public function __construct($settings = false) {
    $this->settings = $settings;
    add_action('init', array($this, 'registerPostTypes'));
    add_action('after_setup_theme', array($this, 'addThemeSupport'));
  }
  
  
  # actions

  public function registerPostTypes() {
      
    $customPostTypes = $this->getCustomPostTypesFromSettings();
    
    foreach ($customPostTypes as $customPost) {
      if (!isset($customPost['name'])) {
        continue;
      } else {
        $name = $customPost['name'];
      }
      
      if (!isset($customPost['displayName'])) {
        $displayName = $name;
      } else {
        $displayName = $customPost['displayName'];
      }
      
      if (!isset($customPost['pluralDisplayName'])) {
        $pluralDisplayName = $name . "s";
      } else {
        $pluralDisplayName = $customPost['pluralDisplayName'];
      }
    }
    
    $this->registerPostType($name, $displayName, $pluralDisplayName, $supports);
    
    
  }
  
  public function addThemeSupport() {
    add_theme_support('post-thumbnails', $this->getCustomPostsThatRequirePostThumbnailsSupportFromSettings());
  }
  
  # level 2
  
  private function registerPostType($customPostTypeName, $displayName, $pluralDisplayName, $supports = NULL) {
    
    if ($supports == NULL) {
      $supports = array('title', 'editor', 'thumbnail', 'tags');
    }
    
    $labels = array(
      'name' => $pluralDisplayName,
      'singular_name' => $displayName,
      'add_new' => 'Add '.$displayName,
      'add_new_item' => 'Add '.$displayName,
      'edit_item' => 'Edit '.$displayName,
      'new_item' => 'New '.$displayName,
      'view_item' => 'View '.$displayName,
      'search_items' => 'Search '.$pluralDisplayName,
    );
    
    $args = array(
      'labels' => $labels,
      'public' => true,
      'menu_position' => 5,
      'supports' => $supports,
      'has_archive' => true,
      'rewrite' => array(
        'slug' => $displayName
    ));
    
    register_post_type($customPostTypeName, $args );  
    
  }
  
  private function getCustomPostsThatRequirePostThumbnailsSupportFromSettings() {

    $customPostTypes = $this->getCustomPostTypesFromSettings();
    $customPostsSupportingPostThumbnails = array();
    
    foreach ($customPostTypes as $customPostType) {
      if ( (isset($customPostType['enablePostThumbnailSupport']) && (isset($customPostType['name'])) && ($customPostType['enablePostThumbnailSupport']))) {
        array_push($customPostsSupportingPostThumbnails, $customPostType['name']);
      }
    }
    
    return $customPostsSupportingPostThumbnails;
    
  }
  
  # level 3
  
  private function getCustomPostTypesFromSettings() {
    if (!isset($this->settings) || !isset($this->settings['customPostTypes'])) {
      return array();
    } else {
      return $this->settings['customPostTypes'];
    }
  }
  
  
}

?>