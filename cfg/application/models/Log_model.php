<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 日志model类
 */
class Log_model extends MY_Model {

  public function __construct() {
    parent::__construct();
    $this->load->database('default');
    $this->table = 't_log';
    $this->primary_key = 'id';
  }
}