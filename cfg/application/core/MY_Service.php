<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class MY_Service {
  public function __construct() {
    
  }

  function __get($key) {
    $CI = &get_instance();
    return $CI->$key;
  }
}