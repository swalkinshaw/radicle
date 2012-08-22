<?php

  global $config;

  require_once(TEMPLATEPATH . '/vendor/twig/lib/Twig/Autoloader.php');

  Twig_Autoloader::register();

  $loader = new Twig_Loader_Filesystem(get_template_directory() . '/templates');
  $twig   = new Twig_Environment($loader, array(
    'cache' => $config['twig_cache'],
    'debug' => false,
  ));

  require_once locate_template('lib/twig/extensions/wp.php');

  $twig->addExtension(new WordpressTwigExtension());

?>
