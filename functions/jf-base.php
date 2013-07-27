<?php

function jf_template_path() {
  return My_Wrapping::$main_template;
}

function jf_template_base() {
  return My_Wrapping::$base;
}


class My_Wrapping {

  /**
   * Stores the full path to the main template file
   */
  static $main_template;

  /**
   * Stores the base name of the template file; e.g. 'page' for 'page.php' etc.
   */
  static $base;

  static function jf_wrap( $template ) {
    self::$main_template = $template;

    self::$base = substr( basename( self::$main_template ), 0, -4 );

    if ( 'index' == self::$base )
      self::$base = false;

    $templates = array( 'wrapper.php' );

    if ( self::$base )
      array_unshift( $templates, sprintf( 'wrapper-%s.php', self::$base ) );

    return locate_template( $templates );
  }
}

add_filter( 'template_include', array( 'My_Wrapping', 'jf_wrap' ), 99 );


function jf_get_main_header_template_part( $templates ) {
  if( !empty( $templates ) ) {
    print '<div class="main-header">';
    foreach( $templates as $template ) {
      get_template_part( $template );
    }
    print '</div>';
  }
}

function jf_get_main_template_part( $templates ){
  print '<article class="content">';
  foreach ( $templates as $template ) {
    get_template_part( $template );
  }
  print '</article>';
}

function jf_get_sidebar_template_part( $templates ){
  if( !empty($templates) ) {
    print '<aside class="sidebar">';
    foreach ( $templates as $template ) {
      get_template_part( $template );
    }
    print '</aside>';
  }
}


// Enqueue all Stylesheets and Javascript files
function jf_enqueue_scripts_styles() {
  if( ENVIRONMENT == 'prod' ) { 
    wp_enqueue_style( 'style-min', get_template_directory_uri() .'/assets/css/style.min.css', false );
  } else {
    wp_enqueue_style( 'style', get_template_directory_uri() .'/assets/css/style.css', false );
  }

  if ( !is_admin() ) {
    wp_deregister_script('jquery');
    wp_register_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js', false, null, false);
  }
  wp_register_script('modernizr-respond', get_template_directory_uri() . '/assets/js/vendor/modernizr-2.6.2-respond-1.2.0.min.js', false, null, false);
  wp_register_script('main', get_template_directory_uri() . '/assets/js/main.js', false, null, true);

  wp_enqueue_script('jquery');
  wp_enqueue_script('modernizr-respond');
  wp_enqueue_script('main');
}
add_action('wp_enqueue_scripts', 'jf_enqueue_scripts_styles', 100);


// Add body classes
function jf_add_body_class( $classes ) {
  unset($classes[1]);

  if( is_singular() ){
    $classes[] = 'single-' . get_post_type();
  }

  if( is_post_type_archive() ){
    $classes[] = 'archive-' . get_post_type();
  }

  return $classes;
}
add_filter( 'body_class', 'jf_add_body_class' );


// Adds RSS feed links to HTML head
add_theme_support( 'automatic-feed-links' );


// Load translation files || Read more about this in /lang/readme.txt
load_theme_textdomain('jumping-frog', get_template_directory() .'/lang' );