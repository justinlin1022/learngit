<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 通用配置接口
 */

 class Common extends MY_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('Common_model');
    $this->model = $this->Common_model;
  }

  /**
   * 获取列表数据
   */
  public function getList($type) {
    $limit = (int)trim($this->input->get('limit',true));
    if(!$limit) {
      $limit = 20;
    }
    $where = $this->input->get('search', true);
    if(isset($where['create_time'])){
        $sdate = $where['create_time']['value']['sdate'];// 获取开始时间
        if($sdate) {
            $where['create_time']['value']['sdate'] = $sdate.' 00:00:00';
        }else{
            unset($where['create_time']['value']['sdate']);// 因为天台即使没填时间也会传来这个变量，所以如果传来的是空值，则释放这个变量内存
        }
        $edate = $where['create_time']['value']['edate'];
        if($edate) {
            $where['create_time']['value']['edate'] = $edate. ' 23:59:59';
        }else{
            unset($where['create_time']['value']['edate']);
        }
    }
    var_dump($where);exit;
    if ($where['is_del']) {
      $where['is_del']['value'] = '0'; // is_del字段为
    }
    $where['orderby'] = array('value'=>'id desc', 'operator'=>'orderby');

    
    $ret = $this->model->paginate($where, "*", $limit);

    exit(json_encode(array(
        'retcode'=>0,
        'data'=>$ret,
        'msg'=>''
    )));
  }

  public function getItem($type) {

  }
  public function modifyItem($type) {
    
  }
  public function deleteItem($type) {
    
  }
 }