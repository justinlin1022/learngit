<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class MY_Controller extends CI_Controller {
  /**
	 * 当前访问url
	 */
  public $curr_url = "";
  
  public function __construct() {
    parent::__construct();
    $this->load->library('session');
    // 本地跨越测试
    if (ENVIRONMENT === 'development') {
      header("Access-Control-Allow-Origin: *");
      header("Access-Control-Allow-Credentials: true");
      header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie");
      header("Access-Control-Allow-Methods: PUT,POST, GET, DELETE, OPTIONS");
      header("Access-Control-Expose-Headers: *");
      if (isset($_SERVER['HTTP_ORIGIN']) && strstr($_SERVER['HTTP_ORIGIN'], 'localhost')) {
        $this->curr_user = $this->session->nick = 'melvinlin';
      }
      if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
        exit();
      }
    }
  }

}