<?php

  global $config;

  require_once(TEMPLATEPATH . '/vendor/twig/lib/Twig/Autoloader.php');

  Twig_Autoloader::register();

  $loader = new Twig_Loader_Filesystem(get_template_directory() . '/templates');
  $twig   = new Twig_Environment($loader, array(
    'cache' => $config['twig_cache'],
    'debug' => false,
  ));

  require_once(TEMPLATEPATH . '/lib/twig/extensions/wp.php');
  require_once(TEMPLATEPATH . '/lib/twig/extensions/assets.php');
  require_once(TEMPLATEPATH . '/lib/twig/extensions/thumbnails.php');

  $twig->addExtension(new WordpressTwigExtension());
  $twig->addExtension(new AssetsTwigExtension());
  $twig->addExtension(new ThumbnailsTwigExtension());

?>
