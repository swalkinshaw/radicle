<?php

  namespace ts;

  class Author {
    private $user_id;
    private $user;

    function __construct($user_id) {
      $this->id   = $user_id;
      $this->user = $this->get_user_data();
      $this->name = $this->user->display_name;
    }

    private function get_user_data() {
      return get_userdata($this->id);
    }
  }

?>
