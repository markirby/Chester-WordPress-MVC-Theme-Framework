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
  
  public static $metaKeyPrefix = "_chester_";
  
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
      switch($field['fieldType']) {
        case 'textarea':
          $fieldHtml = $template->render('text_area', array(
            'theValue' => $mb->get_the_value(),
            'theName' => $mb->get_the_name()
          ));
          break;
        case 'imageUploader':
          $fieldHtml = self::getImageUploaderHtml($mb, $template);
          break;
        default:
          $fieldHtml = $template->render('text_field', array(
            'theValue' => $mb->get_the_value(),
            'theName' => $mb->get_the_name()
          ));
      }
      
      echo $template->render('field_container', array(
        'labelText' => $field['labelTitle'],
        'fieldHTML' => $fieldHtml
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
      'prefix' => self::$metaKeyPrefix,
      'fields' => $fields
    );
    
    $mb = new WPAlchemy_MetaBox($wpAlchemySettings);

  }
  
  private static function getImageUploaderHtml($mb, $template) {
    $theName = $mb->get_the_name();
    $theValue = $mb->get_the_value();
    
    $imageUploadText = "Upload";
    $imageRemoveClass = "hidden";
    if ($theValue != "") {
      $imageUploadText = "Reupload";
      $imageRemoveClass = "";
    }
    
    return $template->render('image_uploader', array(
      'theValue' => $theValue,
      'theName' => $theName,
      'imageInsertFieldContainerClass' => $theName,
      'imageUploadText' => $imageUploadText,
      'imageRemoveClass' => $imageRemoveClass
    ));
  }
}
?>