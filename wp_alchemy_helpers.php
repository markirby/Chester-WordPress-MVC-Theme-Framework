<?php
require_once(dirname(__FILE__).'/lib/wpalchemy_metaboxes/wpalchemy/MetaBox.php');

/**
 * @author   	Mark Kirby
 * @copyright	Copyright (c) 2012, Mark Kirby, http://mark-kirby.co.uk/
 * @license  	http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package  	Chester
 * @version  	0.1
 * @link     	https://github.com/markirby/Chester-WordPress-MVC-Theme-Framework
 * @link     	http://thisishatch.co.uk/
 */

class ChesterWPAlchemyHelpers {
  
  public static function showFields($mb) {
    
    $adminTemplatesFolderLocation = dirname(__FILE__).'/admin_views/';
    
    Mustache_Autoloader::register();
    $template = new Mustache_Engine(array(
        'loader' => new Mustache_Loader_FilesystemLoader($adminTemplatesFolderLocation),
        'partials_loader' => new Mustache_Loader_FilesystemLoader($adminTemplatesFolderLocation)
    ));
    
    $fields = $mb->fields;
    foreach ($fields as $field) {
      if (empty($field['name']) || empty($field['fieldType']) || empty($field['labelTitle'])) {
        continue;
      }

      $mb->the_field($field['name']);
      
      $textField = $template->render('text_field', array(
        'theValue' => $mb->get_the_value(),
        'theName' => $mb->get_the_name()
      ));
      
      echo $template->render('field_container', array(
        'labelText' => $field['labelTitle'],
        'fieldHTML' => $textField
      ));
      
    }

    
  }
  
  public static function setupFieldBlocks($customPostType, $fieldBlocks = array(), $args = array()) {
    
    if (empty($customPostType)) {
      die("ChesterWPAlchemyHelpers::setupFieldBlocks missing customPostType");
    }
    
    foreach ($fieldBlocks as $fieldBlock) {
      self::setupFieldBlock($customPostType, $fieldBlock);
    }
    
  }
  
  private static function setupFieldBlock($customPostType, $fieldBlock) {
    
    if (empty($fieldBlock)) {
      die("ChesterWPAlchemyHelpers::setupFieldBlock missing fieldBlock");
    }
    if (empty($fieldBlock['name'])) {
      die("ChesterWPAlchemyHelpers::setupFieldBlock missing fieldBlock name");
    } else {
      $fieldBlockName = $fieldBlock['name'];
    }
    if (empty($fieldBlock['blockTitle'])) {
      $fieldBlockTitle = "Other content:";
    } else {
      $fieldBlockTitle = $fieldBlock['blockTitle'];
    }
    if (empty($fieldBlock['fields'])) {
      die("ChesterWPAlchemyHelpers::setupFieldBlock missing fields");
    } else {
      $fields = $fieldBlock['fields'];
    }
    
    $id = '_chester_' . $customPostType . '_' . $fieldBlockName;

    $adminTemplatesFolderLocation = str_replace('//','/',dirname(__FILE__).'/') . '../../mvc/admin_templates/';

    $wpAlchemySettings = array(
      'id' => $id,
      'title' => $fieldBlockTitle,
      'template' => $adminTemplatesFolderLocation . $customPostType . '_' . $fieldBlockName . '.php',
      'types' => array($customPostType),
      'mode' => WPALCHEMY_MODE_EXTRACT,
      'prefix' => '_chester_',
      'fields' => $fields
    );
    
    $mb = new WPAlchemy_MetaBox($wpAlchemySettings);

  }
  
}
?>