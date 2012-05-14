<?php

  namespace ts;

  /**
   * Runs conditional tag checks and loads a view if true
   *
   * WordPress does their own template loading and checks every conditional tag.
   * It's necessary to overwrite their loader to change the path and names of the templates/views.
   * List of valid conditional tags: http://codex.wordpress.org/Conditional_Tags
   *
   * @param array Rules to map. Consists of a conditional tag and a view to
   * load if true. View is the name of the file without the .php extension.
   *
   * @return includes the view file
   */
  class ViewLoader {
    private $path;

    function __construct($path = 'views') {
      $this->rules = array();
      $this->path  = $path;
    }

    function map($rule) {
      $this->rules[] = $rule;
    }

    function run() {
      foreach($this->rules as $rule) {
        $conditional_tag = key($rule);

        if ($conditional_tag()) {
          $this->load_view($rule[$conditional_tag]);
          break;
        }
      }
    }

    private function load_view($view) {
      include(TEMPLATEPATH . "/{$this->path}/{$view}.php");
    }
  }

?>
