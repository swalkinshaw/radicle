<?php

  namespace ts;

  class Blog {
    private $blog_id;

    function __construct($blog_id) {
      $this->id     = (int) $blog_id;

      $blog_details = get_blog_details($this->id);

      $this->name   = $blog_details->blogname;
      $this->domain = $blog_details->domain;
      $this->url    = $blog_details->siteurl;
      $this->path   = str_replace('/', '', $blog_details->path);
      $this->posts  = array();
    }

    function latest_post($offset = 0) {
      global $wpdb;

      $post = $wpdb->get_row(
        $wpdb->prepare(
          "SELECT ID, post_author, post_date, post_date_gmt, post_content, post_title, post_name, post_excerpt, comment_count
           FROM wp_%d_posts
           WHERE post_status = 'publish' && post_type = 'post'
           ORDER BY post_date DESC LIMIT 1 OFFSET %d",
           $this->id, $offset
        )
      );

      return new Post($this, $post);
    }

    function setPosts($posts) {
      $self = $this;

      $this->posts = array_map(function($post) use ($self) {
        return new Post($self, $post);
      }, $posts);
    }
  }

?>
