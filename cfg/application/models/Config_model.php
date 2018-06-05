<?php

class Config_model extends MY_Model {
  private $dsn = '';

  public function __construct() {
    parent::__construct();
    $this->load->library('session');
    $this->load->database('default');
    $this->table = 't_cfg';
  }

  public function update_data($id, $data) {
    $this->db->where('id');
    // $res = $this->db->update($this->table, $data);
    // return $res ? $this->db->affected_rows() : $ret;
  }

  public function insert_data($fields) {
    $data['host'] = $this->db->hostname;
    $data['username'] = $this->db->username;
    $data['password'] = $this->db->password;
    $data['databaseName'] = $this->db->database;
    $data['tableName'] = $this->table;
    $data['create_time'] = date("Y-m-d H:i:s");
    $data['update_time'] = date("Y-m-d H:i:s");
    $data['create_user'] = $this->session->nick;
    $data['update_user'] = $this->session->nick;
    $data['fields'] = $fields;
    return $this->insert($data);
    // $res = $this->db->insert($this->table, $data);
    // return $res ? $this->db->insert_id() : $ret;
  }

  public function allFields($db) {
    $this->dsn = "dbdriver://".$db["username"].":".$db["password"]."@".$db["host"]."/".$db["database"];
    $this->load->database($this->dsn);
    $res = $this->db->list_fields($db["tableName"]);
    return count($res) ? $res : array();
  }
}
