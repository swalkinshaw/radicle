<?php

  namespace ts;

  class PostView extends View {
    protected function data() {
      global $wp_query;

      $blog = new Blog(Utils::current_blog());

      $post = new Post($blog, $wp_query->posts[0]);

      return array('blog' => $blog, 'post' => $post);
    }
  }

  $view = new PostView();
  $view->render('post/show.twig');

?>
