<?php

  namespace ts;

  class HomeView extends View {
    protected function data() {
      $updated_blogs = get_last_updated();

      $blogs = Array();

      foreach($updated_blogs as $blog) {
        $blog    = new Blog($blog['blog_id']);
        $blogs[] = $blog;
      }

      return array('blogs' => $blogs);
    }
  }

  $view = new HomeView();
  $view->render('home/index.twig');

?>
