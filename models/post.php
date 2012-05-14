<?php

  namespace ts;

  class Post {
    private $blog;
    private $post;

    const link_format = '%link';

    function __construct($blog, $post) {
      $this->blog          = $blog;
      $this->id            = $post->ID;
      $this->comment_count = (int) $post->comment_count;
      $this->title         = $post->post_title;
      $this->date          = $post->post_date;
      $this->excerpt       = $post->post_excerpt;
      $this->raw_content   = $post->post_content;
      $this->author        = new Author($post->post_author);
      $this->permalink     = get_blog_permalink($this->blog->id, $this->id);
    }

    function content() {
      return apply_filters('the_content', $this->raw_content);
    }

    function comments() {
      $self     = $this;
      $comments = $this->fetch_comments();

      return array_map(function($comment) use ($self) {
        return new Comment($self, $comment);
      }, $comments);
    }

    function thumbnail_url() {
      global $wpdb;

      $wpdb->set_blog_id($this->blog->id);

      $attachment = wp_get_attachment_image_src(get_post_thumbnail_id($this->id), 'thumbnail');
      $image_url  = $attachment[0];

      if ($image_url) {
        // work-around since wp_get_attachment_image_src doesn't understand WPMU paths
        $image_url = str_replace('wp-content/uploads', $this->blog->path . '/files', $image_url);
      } else {
        $image_url = get_template_directory_uri() . '/assets/img/logos/s.png';
      }

      return $image_url;
    }

    function next_post_link($format = self::link_format, $text = 'Next Post') {
      $link = Utils::capture(function() use ($format, $text) {
        next_post_link($format, $text);
      });

      $disabled_link = '<a href="#" class="disabled" rel="next">Next Page</a>';

      if ($link) {
        return $link;
      } else {
        return $disabled_link;
      }
    }

    function previous_post_link($format = self::link_format, $text = 'Prev Post') {
      return previous_post_link($format, $text);
    }

    private function fetch_comments() {
      return get_comments(array(
        'post_id' => $this->id,
        'status'  => 'approve'
      ));
    }
  }

?>
