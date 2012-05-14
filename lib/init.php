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

    // required to stop the default template loader from running after ours
    exit();
  }

  add_action('template_redirect', 'template_loader');

  function setup() {
    add_editor_style('css/editor-style.css');
    add_theme_support('post-thumbnails');
  }

  add_action('after_setup_theme', 'setup');

?>
