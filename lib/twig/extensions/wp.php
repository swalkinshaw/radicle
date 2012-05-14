<?php

  class WordpressTwigExtension extends Twig_Extension {
    function getName() {
      return 'wordpress';
    }

    function getGlobals() {
      return array(
        'wp' => $this
      );
    }

    function __call($function, $arguments) {
      if (!function_exists($function)) {
        trigger_error("Call to unexist Wordpress function \"{$function}\"", E_USER_WARNING);
        return null;
      }

      return call_user_func_array($function, $arguments);
    }
  }

?>
