<?php

  // redirect /?s to /search/
  // http://txfx.net/wordpress-plugins/nice-search/
  function roots_nice_search_redirect() {
    if (is_search() && strpos($_SERVER['REQUEST_URI'], '/wp-admin/') === false && strpos($_SERVER['REQUEST_URI'], '/search/') === false) {
      wp_redirect(home_url('/search/' . str_replace(array(' ', '%20'), array('+', '+'), urlencode(get_query_var('s')))), 301);
        exit();
    }
  }

  add_action('template_redirect', 'roots_nice_search_redirect');

  function roots_search_query($escaped = true) {
    $query = apply_filters('roots_search_query', get_query_var('s'));
    if ($escaped) {
        $query = esc_attr($query);
    }
    return urldecode($query);
  }

  add_filter('get_search_query', 'roots_search_query');

  // fix for empty search query
  // http://wordpress.org/support/topic/blank-search-sends-you-to-the-homepage#post-1772565
  function roots_request_filter($query_vars) {
    if (isset($_GET['s']) && empty($_GET['s'])) {
      $query_vars['s'] = " ";
    }
    return $query_vars;
  }

  add_filter('request', 'roots_request_filter');

  // remove WordPress version from RSS feed
  function roots_no_generator() { return ''; }
  add_filter('the_generator', 'roots_no_generator');

  // cleanup gallery_shortcode()
  function roots_gallery_shortcode($attr) {
    global $post, $wp_locale;

    static $instance = 0;
    $instance++;

    // Allow plugins/themes to override the default gallery template.
    $output = apply_filters('post_gallery', '', $attr);
    if ($output != '') {
      return $output;
    }

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    if (isset($attr['orderby'])) {
      $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
      if (!$attr['orderby']) {
        unset($attr['orderby']);
      }
    }

    extract(shortcode_atts(array(
      'order'      => 'ASC',
      'orderby'    => 'menu_order ID',
      'id'         => $post->ID,
      'icontag'    => 'li',
      'captiontag' => 'p',
      'columns'    => 3,
      'size'       => 'thumbnail',
      'include'    => '',
      'exclude'    => ''
    ), $attr));

    $id = intval($id);
    if ('RAND' == $order) {
      $orderby = 'none';
    }

    if (!empty($include)) {
      $include = preg_replace( '/[^0-9,]+/', '', $include );
      $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

      $attachments = array();
      foreach ($_attachments as $key => $val) {
        $attachments[$val->ID] = $_attachments[$key];
      }
    } elseif (!empty($exclude)) {
      $exclude = preg_replace('/[^0-9,]+/', '', $exclude);
      $attachments = get_children(array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
    } else {
      $attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
    }

    if (empty($attachments)) {
      return '';
    }

    if (is_feed()) {
      $output = "\n";
      foreach ($attachments as $att_id => $attachment)
        $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
      return $output;
    }

    $captiontag = tag_escape($captiontag);
    $columns = intval($columns);
    $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $gallery_style = $gallery_div = '';
    if (apply_filters('use_default_gallery_style', true)) {
      $gallery_style = "";
    }
    $size_class = sanitize_html_class($size);
    $gallery_div = "<ul id='$selector' class='thumbnails gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
    $output = apply_filters('gallery_style', $gallery_style . "\n\t\t" . $gallery_div);

    $i = 0;
    foreach ($attachments as $id => $attachment) {
      $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

      $output .= "
        <{$icontag} class=\"gallery-item\">
          $link
        ";
      if ($captiontag && trim($attachment->post_excerpt)) {
        $output .= "
          <{$captiontag} class=\"gallery-caption hidden\">
          " . wptexturize($attachment->post_excerpt) . "
          </{$captiontag}>";
      }
      $output .= "</{$icontag}>";
      if ($columns > 0 && ++$i % $columns == 0) {
        $output .= '';
      }
    }

    $output .= "</ul>\n";

    return $output;
  }

  remove_shortcode('gallery');
  add_shortcode('gallery', 'roots_gallery_shortcode');

  function roots_attachment_link_class($html) {
    $postid = get_the_ID();
    $html = str_replace('<a', '<a class="thumbnail"', $html);
    return $html;
  }
  add_filter('wp_get_attachment_link', 'roots_attachment_link_class', 10, 1);

  // http://justintadlock.com/archives/2011/07/01/captions-in-wordpress
  function roots_caption($output, $attr, $content) {
    /* We're not worried abut captions in feeds, so just return the output here. */
    if ( is_feed()) {
      return $output;
    }

    /* Set up the default arguments. */
    $defaults = array(
      'id' => '',
      'align' => 'alignnone',
      'width' => '',
      'caption' => ''
    );

    /* Merge the defaults with user input. */
    $attr = shortcode_atts($defaults, $attr);

    /* If the width is less than 1 or there is no caption, return the content wrapped between the [caption]< tags. */
    if (1 > $attr['width'] || empty($attr['caption'])) {
      return $content;
    }

    /* Set up the attributes for the caption <div>. */
    $attributes = (!empty($attr['id']) ? ' id="' . esc_attr($attr['id']) . '"' : '' );
    $attributes .= ' class="thumbnail wp-caption ' . esc_attr($attr['align']) . '"';
    $attributes .= ' style="width: ' . esc_attr($attr['width']) . 'px"';

    /* Open the caption <div>. */
    $output = '<div' . $attributes .'>';

    /* Allow shortcodes for the content the caption was created for. */
    $output .= do_shortcode($content);

    /* Append the caption text. */
    $output .= '<div class="caption"><p class="wp-caption-text">' . $attr['caption'] . '</p></div>';

    /* Close the caption </div>. */
    $output .= '</div>';

    /* Return the formatted, clean caption. */
    return $output;
  }

  add_filter('img_caption_shortcode', 'roots_caption', 10, 3);

  // http://www.deluxeblogtips.com/2011/01/remove-dashboard-widgets-in-wordpress.html
  function roots_remove_dashboard_widgets() {
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
    remove_meta_box('dashboard_primary', 'dashboard', 'normal');
    remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
  }

  add_action('admin_init', 'roots_remove_dashboard_widgets');

  // excerpt cleanup
  function roots_excerpt_length($length) {
    return POST_EXCERPT_LENGTH;
  }

  function roots_excerpt_more($more) {
    return ' &hellip; <a href="' . get_permalink() . '">' . __( 'Continued', 'roots' ) . '</a>';
  }

  add_filter('excerpt_length', 'roots_excerpt_length');
  add_filter('excerpt_more', 'roots_excerpt_more');

  // we don't need to self-close these tags in html5:
  // <img>, <input>
  function roots_remove_self_closing_tags($input) {
    return str_replace(' />', '>', $input);
  }

  add_filter('get_avatar', 'roots_remove_self_closing_tags');
  add_filter('comment_id_fields', 'roots_remove_self_closing_tags');
  add_filter('post_thumbnail_html', 'roots_remove_self_closing_tags');

  // set the post revisions to 5 unless the constant
  // was set in wp-config.php to avoid DB bloat
  if (!defined('WP_POST_REVISIONS')) { define('WP_POST_REVISIONS', 5); }

  // allow more tags in TinyMCE including <iframe> and <script>
  function roots_change_mce_options($options) {
    $ext = 'pre[id|name|class|style],iframe[align|longdesc|name|width|height|frameborder|scrolling|marginheight|marginwidth|src],script[charset|defer|language|src|type]';
    if (isset($initArray['extended_valid_elements'])) {
      $options['extended_valid_elements'] .= ',' . $ext;
    } else {
      $options['extended_valid_elements'] = $ext;
    }
    return $options;
  }

  add_filter('tiny_mce_before_init', 'roots_change_mce_options');

?>
