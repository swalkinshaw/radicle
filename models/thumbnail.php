<?php

  namespace ts;

  class Thumbnail {
    private $post;
    private $blog;

    function __construct($post, $blog) {
      $this->post = $post;
      $this->blog = $blog;
    }

    function url() {
      if ($thumbnail = $this->get_thumbnail()) {
        return $this->fix_url($thumbnail);
      }

      if ($fallback_thumbnail = $this->get_fallback_thumbnail()) {
        return $this->fix_url($fallback_thumbnail);
      }

      if ($youtube_thumbnail = $this->get_youtube_thumbnail()) {
        return $youtube_thumbnail;
      }
    }

    private function fix_url($image_url) {
      $image_url = str_replace('wp-content/uploads', $this->blog->pathname . '/files', $image_url);
      return Utils::CDNify($image_url, $this->blog);
    }

    private function get_thumbnail() {
      global $wpdb;

      $wpdb->set_blog_id($this->blog->id);

      $attachment = wp_get_attachment_image_src(get_post_thumbnail_id($this->post->id), 'thumbnail');
      $image_url  = $attachment[0];

      return $image_url;
    }

    private function get_fallback_thumbnail() {
      $img_args = array(
        'post_type'       => 'attachment',
        'post_mime_type'  => 'image',
        'numberposts'     => 1,
        'post_parent'     => $this->post->id
      );

      if ($images = get_children($img_args)) {
        $image = array_shift($images);
        return array_shift(wp_get_attachment_image_src($image->ID, 'thumbnail'));
      }
    }

    private function get_youtube_thumbnail() {
      $youtube_thumb = '';
      $id_regexp = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';

      if (preg_match($id_regexp, $this->post->raw_content, $match)) {
        $video_id = $match[1];
      }

      if (isset($video_id)) {
        $youtube_thumb = "http://img.youtube.com/vi/{$video_id}/1.jpg";
      }

      return $youtube_thumb;
    }
  }

?>
