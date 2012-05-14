<?php

  namespace ts;

  class Utils {
    const CDN_DOMAIN = 'http://cdn.com';

    static function current_blog() {
      global $wpdb;

      $uri  = $_SERVER['REQUEST_URI'];
      $path = explode('/', $uri);
      $path = '/'. $path[1] . '/';

      $blog = $wpdb->get_row(
        $wpdb->prepare(
          "SELECT blog_id, site_id, domain, path
           FROM wp_blogs
           WHERE path = %s",
           $path
        )
      );

      return $blog->blog_id;
    }

    static function convert_quotes($html_string) {
      return str_replace("'", '"', $html_string);
    }

    static function CDNify($image_url, $blog) {
      return str_replace($blog->domain, self::CDN_DOMAIN, $image_url);
    }

    /**
     * Creates an output buffer and captures it
     *
     * Many WordPress functions use echo to display the output to the buffer.
     * Usually those functions use a related one prefixed with get_* which don't output.
     * Unfortunately, there's some functions that only echo without an option.
     *
     * @param function Function whose output you want to capture
     *
     * @return string Function output (often an HTML string)
     */
    static function capture($output) {
      ob_start();
      $output();
      return ob_get_clean();
    }
  }

?>
