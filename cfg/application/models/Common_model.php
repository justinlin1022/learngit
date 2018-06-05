<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * 通用配置模型
 */

 class Common_model extends MY_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database('default');
    $this->table = 't_cfg';
  }

 }