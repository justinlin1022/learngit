<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

/**
 * 配置页面接口
 */
class Config extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('Config_model');
    $this->model = $this->Config_model;
  }

  public function configList() {
    // 判断权限

    // 获取分页数据
    $limit = (int)trim($this->input->get_post('limit', true));
    if (!$limit) {
      $limit = 20;
    }
    $where = $this->input->get_post('search', true);
    // 获取数据
    $res = $this->model->paginate($where, "*", $limit);
    // 返回数据
    exit(json_encode(array(
      'retcode'=>0,
      'data'=>$res,
      'msg'=>''
    )));
  }

  public function allFields() {
    // 获取数据库信息
    $db = $this->input->get_post('db', true);
    if (!is_array($db)) {
      return;
    }
    // 连接数据库，获取数据库所有字段
    $res = $this->model->allFields($db);
    
    // 返回数据
    exit(json_encode(array(
      'retcode'=>0,
      'data'=>$res,
      'msg'=>''
    )));
  }

  public function modifyFields() {
    // 获取数据
    $id = $this->input->get_post('id', true);
    $fields = $this->input->get_post('fields', true);
    if (!empty($fields)) {
      $fields = json_encode($fields);
    }
    if (!empty($id)) {
      $res = $this->model->update_data($id, $fields);
    } else {
      $res = $this->model->insert_data($fields);
    }

    // 返回数据
    exit(json_encode(array(
      'retcode'=>0,
      'data'=>$res,
      'msg'=>''
    )));
  }
}