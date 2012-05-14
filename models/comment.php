<?php

  namespace ts;

  class Comment {
    private $post;
    private $comment;

    function __construct($post, $comment) {
      $this->post        = $post;
      $this->id          = $comment->comment_ID;
      $this->author_name = $comment->comment_author;
      $this->author_url  = $comment->comment_author_url;
      $this->content     = $comment->comment_content;
      $this->parent      = $comment->comment_parent;
      $this->date        = $comment->comment_date;
    }
  }

?>
