<?php

  namespace ts;

  abstract class View {
    abstract protected function data();

    function render($template) {
      $template = $this->load_template($template);
      $template->display($this->data());
    }

    private function load_template($template) {
      global $twig;
      return $twig->loadTemplate($template);
    }
  }

?>
