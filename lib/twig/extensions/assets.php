<?php

  class AssetsTwigExtension extends Twig_Extension {
    function __construct() {
    }

    function getFunctions() {
      return array(
        'asset_url' => new \Twig_Function_Method($this, 'asset_url')
      );
    }

    function getName() {
      return 'assets';
    }

    function asset_url($path) {
      $url = get_template_directory_uri();
      $home_url = get_blogaddress_by_id(1);

      $file = get_template_directory() . '/' . $path;
      $hash = md5($file);

      $base  = preg_replace('!(.*)(wp-content/.*)!', $home_url . "$2/assets", $url);

      return $base . "/{$path}?{$hash}";
    }
  }

?>
