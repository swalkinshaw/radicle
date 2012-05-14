<?php

  namespace ts;

  class BlogView extends View {
    protected function data() {
      global $wp_query;

      $blog = new Blog(Utils::current_blog());

      $blog->setPosts($wp_query->posts);

      return array('blog' => $blog);
    }
  }

  $view = new BlogView();
  $view->render('blog/index.twig');

?>
