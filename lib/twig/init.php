<?php

  require_once locate_template('vendor/twig/lib/Twig/Autoloader.php');

  Twig_Autoloader::register();

  $loader = new Twig_Loader_Filesystem(get_template_directory() . '/templates');
  $twig   = new Twig_Environment($loader, array(
    'cache' => false,
    'debug' => true,
  ));

  require_once locate_template('lib/twig/extensions/wp.php');

  $twig->addExtension(new WordpressTwigExtension());

?>
