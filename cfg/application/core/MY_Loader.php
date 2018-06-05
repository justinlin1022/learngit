<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Loader extends CI_Loader {
  protected $ci_services = array();
  protected $ci_service_paths = array();

  /**
   * 构造函数
   */
  public function __construct() {
    parent::__construct();
    load_class('Service', 'core');
    $this->ci_service_paths[] = APPPATH;
  }

  public function service($service, $params = null, $object_name = null) {
    $service = ucfirst(strtolower($service));
    // 传入的服务类为数组，遍历递归调用单个服务类
    if (is_array($service)) {
      foreach ($service as $s) {
        $this->service($s, $params);
      }
      return;
    }
    // 未调用服务类或调用的服务类不存在
    if ($service == '' or in_array($service, $this->ci_services)) {
      return false;
    }
    if (!is_null($params) && !is_array($params)) {
      $params = null;
    }
    // 如果$service为类似admin/Session_service，则截取目录和文件名字符串
    $subdir = '';
    if (($last_slash = strpos($service, '/')) !== false) {
      $subdir = substr($service, 0, $last_slash + 1);
      $service = substr($service, $last_slash + 1);
    }
    foreach ($this->ci_service_paths as $path) {
      $file_path = $path.'services/'.$subdir.$service.'.php';
      if (!file_exists($file_path)) {
        echo $file_path;
        continue;
      }
      include_once($file_path);
      $ci_service_name = lcfirst($service);
      $CI = &get_instance();
      if ($params == null) {
        $CI->$ci_service_name = new $service();
      } else {
        $CI->$ci_service_name = new $service($params);
      }
      $this->ci_services[] = $service;
      return;
    }
  }
}