<?php

  class ThumbnailsTwigExtension extends Twig_Extension {
    function __construct() {
    }

    function getFunctions() {
      return array(
        'thumbnail' => new \Twig_Function_Method($this, 'thumbnail')
      );
    }

    function getName() {
      return 'thumbnails';
    }

    function thumbnail($thumb) {
      if ($thumb) {
        return $thumb;
      } else {
        return AssetsTwigExtension::asset_url('img/default-thumb.png');
      }
    }
  }

?>
