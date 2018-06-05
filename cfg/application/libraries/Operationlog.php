<?php

/**
 *  用于记录model的update、insert、delete日志
 * Created by PhpStorm.
 * User: linklin
 * Date: 2016/10/13
 * Time: 11:17
 *
 * @property Log_service    $log_service
 */
class Operationlog
{
    protected $oldData = '';
    protected $newData = '';
    protected $id = '';
    protected $table = '';
    protected $CI;
    public function __construct()
    {
        $this->CI = & get_instance();
    }

    public function updateStart($data,$table,$primary_key = 'id'){
        $this->table = $table;
        if(isset($data->$primary_key)){
            $this->id = $data->$primary_key;
        }else if(count($data) == 1){
            $this->id = $data[0]->$primary_key;
        }
        $this->oldData = $data;
    }

    public function updateEnd($data){
        $this->newData = $data;
        //保存日志
        $this->create('edit');
    }

    public function insertEnd($data,$table,$id){
        $this->table = $table;
        $this->id = $id;
        $this->newData = $data;
        //保存日志
        $this->create('add');
    }
    public function deleteStart($data,$table,$primary_key = 'id'){
        $this->table = $table;
        if(isset($data->$primary_key)){
            $this->id = $data->$primary_key;
        }else if(count($data) == 1){
            $this->id = $data[0]->$primary_key;
        }
        $this->oldData = $data;
    }

    public function deleteEnd(){
        //保存日志
        $this->create('delete');
    }

    public function create($oper){
    	$username = $this->CI->session->nick;
    	if(empty($username)){
    		return false;//用户名为空时，不写操作日志
    	}
        $data['oper'] = $oper;
        $data['operid'] = $this->id;
        $data['opertable'] = $this->table;
        $data['oldinfo'] = !empty($this->oldData)?json_encode($this->oldData):'';
        $data['newinfo'] = !empty($this->newData)?json_encode($this->newData):'';

        $this->CI->load->model('log_model');
        $this->CI->load->library('user_agent');
        $data['addtime'] = date("Y-m-d H:i:s");
        $data['username'] = $username?$username:'';
        $data['ip'] = $this->CI->input->ip_address();
        $data['browser'] = $this->CI->agent->browser()."(".$this->CI->agent->version().")";
        $data['url'] = current_url();
        $this->CI->log_model->db->insert($this->CI->log_model->getTable(),$data);
    }
}