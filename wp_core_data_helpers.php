<?php

class ChesterWPCoreDataHelpers {
  
  public static function getBlogInfoData() {
    return array(
      'blog_title' => self::getBlogTitle(),
      'name' => get_bloginfo('name'),
      'description' => get_bloginfo('description'),
      'admin_email' => get_bloginfo('admin_email'),

      'url' => get_bloginfo('url'),
      'wpurl' => get_bloginfo('wpurl'),

      'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
      'stylesheet_url' => get_bloginfo('stylesheet_url'),
      'template_directory' => get_bloginfo('template_directory'),
      'template_url' => get_bloginfo('template_url'),

      'atom_url' => get_bloginfo('atom_url'),
      'rss2_url' => get_bloginfo('rss2_url'),
      'rss_url' => get_bloginfo('rss_url'),
      'pingback_url' => get_bloginfo('pingback_url'),
      'rdf_url' => get_bloginfo('rdf_url'),

      'comments_atom_url' => get_bloginfo('comments_atom_url'),
      'comments_rss2_url' => get_bloginfo('comments_rss2_url'),

      'charset' => get_bloginfo('charset'),
      'html_type' => get_bloginfo('html_type'),
      'language' => get_bloginfo('language'),
      'text_direction' => get_bloginfo('text_direction'),
      'version' => get_bloginfo('version'),
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
          'excerpt' => get_the_excerpt(),
          'author' => get_the_author(),
          'author_link' => get_the_author_link(),
          'the_tags' => get_the_tags(),
          'the_category' => get_the_category(),
        );
        array_push($posts, $post);
      }
    }
    
    return $posts;
  }
  
  private static function getBlogTitle() {
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