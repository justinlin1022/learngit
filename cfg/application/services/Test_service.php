<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * 测试service类是否可用
 */
class Test_service extends MY_Service {
  public function __construct() {
    parent::__construct();
  }

  public function test() {
    $this->load->helper('url');
    echo 'load url: '.site_url('test/index');
    echo 'this is Test_service class';
  }
}