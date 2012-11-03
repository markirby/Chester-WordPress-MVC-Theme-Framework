<?php

class ChesterWPCoreDataHelpers {
  
  public static function getHeaderData() {
    return array(
      'title' => self::getTitle(),
      'template_directory' => get_bloginfo('template_directory'),
      'charset' => get_bloginfo('charset'),
      'pingback_url' => get_bloginfo('pingback_url'),
    );
  }
  
  public static function getWordpressPostsFromLoop($dateFormat = false) {
    $posts = array();
    
    if (!$dateFormat) {
      $dateFormat = 'F jS, Y';
    }
    
    if (have_posts()) {
      while (have_posts()) {
        the_post();
        $post = array(
          'permalink' => get_permalink(),
          'title' => get_the_title(),
          'time' => get_the_time($dateFormat),
          'content' => self::getTheFilteredContentFromLoop(),
          'excerpt' => get_the_excerpt()
        );
        array_push($posts, $post);
      }
    }
    
    return $posts;
  }
  
  private static function getTitle() {
    if (is_home()) {
      return get_bloginfo('name');
    } else {
      return wp_title("-", false, "right") . get_bloginfo('name');
    }
  }
  
  private static function getTheFilteredContentFromLoop() {
    $content = apply_filters('the_content', get_the_content());
    $content = str_replace(']]>', ']]&gt;', $content);
    return $content;
  }
  
}

?>