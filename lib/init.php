<?php

  // Bypass the default WP template loader so we can move our views out
  // of the root folder.
  //
  // Add additional mappings to this as needed in the form:
  // conditional_tag => view_file_name (without .php ext)
  function template_loader() {
    $loader = new \ts\ViewLoader();

    $loader->map(array('is_home' => 'blog'));
    $loader->map(array('is_front_page' => 'home'));
    $loader->map(array('is_single' => 'post'));

    $loader->run();

    // required to bypass default template loader
    exit();
  }

  add_action('template_redirect', 'template_loader');
  function setup() {
    // tell the TinyMCE editor to use editor-style.css
    // if you have issues with getting the editor to show your changes then
    // use this instead: add_editor_style('editor-style.css?' . time());
    add_editor_style('css/editor-style.css');

    // http://codex.wordpress.org/Post_Thumbnails
    add_theme_support('post-thumbnails');
  }

  add_action('after_setup_theme', 'setup');

?>
