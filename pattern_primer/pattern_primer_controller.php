<?php

/**
 * @author    Mark Kirby
 * @copyright Copyright (c) 2012, Mark Kirby, http://mark-kirby.co.uk/
 * @license   http://en.wikipedia.org/wiki/MIT_License The MIT License
 * @package   Chester
 * @version   0.1
 * @link      https://github.com/markirby/Chester-WordPress-MVC-Theme-Framework
 * @link      http://thisishatch.co.uk/
 */
 
class ChesterPatternPrimerController{
  
  private $patternPrimerTemplateLoader;
  private $coreTemplateLoader;
  
  public function __construct() {
    $patternPrimerViewsLocation = dirname(__FILE__).'/templates/';
    $templatesFolderLocation = ChesterBaseController::getTemplatesFolderLocation();

    Mustache_Autoloader::register();

    $this->patternPrimerTemplateLoader = new Mustache_Engine(array(
      'loader' => new Mustache_Loader_FilesystemLoader($patternPrimerViewsLocation),
      'partials_loader' => new Mustache_Loader_FilesystemLoader($patternPrimerViewsLocation)
    ));

    $this->coreTemplateLoader = new Mustache_Engine(array(
      'loader' => new Mustache_Loader_FilesystemLoader($templatesFolderLocation),
      'partials_loader' => new Mustache_Loader_FilesystemLoader($templatesFolderLocation)
    ));
    
  }
  
  // names of folders inside mvc/templates you wish to include e.g. array('templates', 'grids')
  public function showPatternPrimer($foldersToConvert = array(), $patternsHTML = "") {
    
    $processedPatternSets = array(
      array(
        'pathToTemplates' => dirname(__FILE__) . '/templates/',
        'patternFolder' => 'patterns/',
        'patternsTemplateLoader' => $this->patternPrimerTemplateLoader
      )
    );
    
    foreach ($foldersToConvert as $folderToConvert) {
      $processedPatternSet = array(
        'pathToTemplates' => str_replace('//','/', dirname(__FILE__) . '/') . '../../../mvc/templates/',
        'patternFolder' => $folderToConvert . '/',
        'patternsTemplateLoader' => $this->coreTemplateLoader
      );
      array_push($processedPatternSets, $processedPatternSet);
    }
    
    $blogInfo = ChesterWPCoreDataHelpers::getBlogInfoData();
    
    echo $this->coreTemplateLoader->render('header', ChesterWPCoreDataHelpers::getBlogInfoData());
    echo $this->patternPrimerTemplateLoader->render('pattern_primer_header', array(
      'syntaxhighlighter_directory' => $blogInfo['template_url'] . '/lib/Chester/lib/syntaxhighlighter_3.0.83/'
    ));
    echo $this->coreTemplateLoader->render('header_close', FALSE);
    echo $this->patternPrimerTemplateLoader->render('pattern_primer_page', array(
      'patterns' => $this->renderPatterns($processedPatternSets) . $patternsHTML
    ));
    echo $this->coreTemplateLoader->render('footer', FALSE);

  }

  public function renderCustomPatternGroup($patternsHTML, $patternTitle) {
    return $this->patternPrimerTemplateLoader->render('pattern_primer_group', array(
      'patterns' => $patternsHTML,
      'patternTitle' => $patternTitle
    ));
  }
  
  public function renderPattern($templateName, $templateVars = false, $templateLoader = false) {
    if (empty($templateLoader)) {
      $templateLoader = $this->coreTemplateLoader;
    }
    return $this->patternPrimerTemplateLoader->render('pattern_primer_object', array(
      'pattern' => $templateLoader->render($templateName, $templateVars),
      'patternName' => $templateName
    ));
  }
  
  private function renderPatterns($patternSets = array()) {
    $patterns = "";
    foreach ($patternSets as $patternSet) {
      if ( (empty($patternSet['pathToTemplates'])) || (empty($patternSet['patternsTemplateLoader'])) || (empty($patternSet['patternFolder'])) ) {
        continue;
      }
      $patterns .= $this->renderGroupOfPatterns($patternSet['pathToTemplates'], $patternSet['patternFolder'], $patternSet['patternsTemplateLoader']);
    }
    
    return $patterns;
    
  }
  
  private function renderGroupOfPatterns($pathToTemplates, $patternFolder, $patternsTemplateLoader) {
    
    $files = array();
    $pathToPatterns = opendir($pathToTemplates . $patternFolder);
    
    if (!$pathToPatterns) {
      die ("Could not open path " . $pathToTemplates . $patternFolder);
    }
    $extension = ".mustache";

    while (false !== ($file = readdir($pathToPatterns))) {
      if(stristr($file, $extension)) {
        $files[] = substr($file, 0, (0 - strlen($extension)));
      }
    }
    sort($files);
    
    $patterns = "";
    $templateVars = false;
    
    if ($patternFolder == "grids/") {
      $templateVars = array(
        'content_block_1' => $this->patternPrimerTemplateLoader->render('sample_grid', false),
        'content_block_2' => $this->patternPrimerTemplateLoader->render('sample_grid', false),
        'content_block_3' => $this->patternPrimerTemplateLoader->render('sample_grid', false),
        'content_block_4' => $this->patternPrimerTemplateLoader->render('sample_grid', false),
        'content_block_5' => $this->patternPrimerTemplateLoader->render('sample_grid', false),
        'content_block_6' => $this->patternPrimerTemplateLoader->render('sample_grid', false),
        'content_block_7' => $this->patternPrimerTemplateLoader->render('sample_grid', false),
        'content_block_8' => $this->patternPrimerTemplateLoader->render('sample_grid', false)        
      );
    }
    
    foreach ($files as $file) {
      $patterns .= $this->renderPattern($patternFolder . $file, $templateVars, $patternsTemplateLoader);
    }
    
    return $this->renderCustomPatternGroup($patterns, $patternFolder);
    
  }

}
?>