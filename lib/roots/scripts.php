<?php

  function roots_scripts() {
    wp_deregister_script('l10n');

    if (!is_admin()) {
      wp_deregister_script('jquery');
      wp_register_script('jquery', '', '', '', false);
    }

    if (is_single() && comments_open() && get_option('thread_comments')) {
      wp_enqueue_script('comment-reply');
    }
  }

  add_action('wp_enqueue_scripts', 'roots_scripts', 100);

?>
