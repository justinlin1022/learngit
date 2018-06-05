<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 框架处理类
 */
class Frame extends MY_Controller {

  public function __construct() {
    parent::__construct();
  }

  public function index() {
    $this->load->view('index');
  }

}