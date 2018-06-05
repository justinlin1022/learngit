<?php
/**
 * 扩展CI_Model，定义公用方法
 * 注：
 * 1、支持打印sql语句到日志文件
 * @property CI_DB_active_record $db
 * @property Operationlog        $operationlog
 * @author linklin
 * @subpackage  CI_Model
 */
class MY_Model extends CI_Model{

	/**
	 * 是否开启调试，即输出sql语句
	 * @var boolean
	 */
	protected $_debug = TRUE;

	/**
	 * 设置查询表
	 * @var string
	 */
	protected $table;

	/**
	 * 保存当前表字段信息
	 *
	 * @var array
	 */
	protected $fields = array();

	/**
	 * 表主键，默认为id
	 * @var string
	 */
    protected $primary_key = "id";

    protected $exec_sql = array();

	public function __construct(){
		parent::__construct();
        $this->load->library('operationlog',array('model'=>$this));
	}

	/**
	 * 获取当前连接表名
	 * @return string
	 */
	public function getTable(){
		return $this->table;
	}
	
	/**
	 * 设置当前连接表名
	 * @param string $table
	 * @return void
	 */
	public function setTable($table){
		$this->table = $table;
	}
	
    /**
     * 分页查询数据显示
     * @param array $where
     * @param string $column
     * @param int $perPage
     * @return array
     */
	public function paginate($where=array(),$column="*",$perPage = 15){
        $this->db->start_cache();
        $this->setSelect($column);
        $this->setSearchWhere($where);
        $this->db->stop_cache();
	    $current_page =  (int)$this->input->get('page',true);
        if($current_page === 0){
            $current_page = 1;
        }
        //查询总数
        $total =  $this->db->from($this->table)->count_all_results();
        $from = ($current_page-1)*$perPage;
        if($from > 1000000){
            die("pageIndex error");//limit分页限制，防止limit参数溢出
        }
        $this->db->limit($perPage,$from);
        //查询记录
        $query = $this->db->get($this->table);
        if($this->_debug){
            $this->getExecSql();
        }
        if(!empty($query)){
            $ret = $query->result();
        }else{
            $ret = array();
        }
        $this->db->flush_cache();
        $result = array();
        $result['total'] = $total;
        $result['per_page'] = $perPage;
        $result['current_page'] = $current_page;
        $result['last_page'] = $perPage?ceil($total/$perPage):0;
        $result['next_page_url'] = '';
        $result['prev_page_url'] = '';
        $result['from'] = $from +1 ;
        $result['to'] = $from+$perPage;
        $result['hasMore'] = $total <= $from+$perPage ?false:true;
        $result['data'] = $ret;
        return $result;
    }

    /**
     * 分页查询数据显示(兼容join链接)
     * @param array $where
     * @param string  $field
     * @param integer $current_page
     * @param integer $limit
     * @return string[]|number[]|unknown[]|object[]
     */
    public function page($where,$field,$current_page,$limit){
    	$total= $this->fetchCount($where);
    	$ret = $this->fetchAll($where,$field,$current_page-1,$limit);
    	$from = ($current_page-1)*$limit;;
    	$result = array();
    	$result['total'] = $total;
    	$result['per_page'] = $limit;
    	$result['current_page'] = $current_page;
    	$result['last_page'] = $limit?ceil($total/$limit):0;
    	$result['next_page_url'] = '';
    	$result['prev_page_url'] = '';
    	$result['from'] = $from +1 ;
    	$result['to'] = $from+$limit;
    	$result['data'] = $ret;
    	return $result;
    }
    
    /**
     * 根据主键获取一条记录
     * @param $id
     * @return array
     */
    public function find($id){
        $this->db->where($this->primary_key,$id);
        $this->db->limit("1");
        $query = $this->db->get($this->table);
        if($this->_debug){
            $this->getExecSql();
        }
        $result = empty($query)?array():$query->row();
        return $result;
    }

    /**
     * 根据条件获取一条记录
     * @param $where
     * @return array
     */
    public function findBy($field,$value){
        $where[$field] = array('value'=>$value,'operator'=>'=');
        $this->setSearchWhere($where);
        $this->db->limit("1");
        $query = $this->db->get($this->table);
        if($this->_debug){
            $this->getExecSql();
        }
        $result = empty($query)?array():$query->row();
        return $result;
    }

    /**
     * @param $where
     * @param string $column
     */
    public function findWhere($where,$column = "*"){
        $this->setSelect($column);
        $this->setSearchWhere($where);
        $query = $this->db->get($this->table);
        if($this->_debug){
            $this->getExecSql();
        }
        $result = empty($query)?array():$query->result();
        return $result;
    }

    /**
     * 返回查询的 SQL 语句
     * @param $where
     * @param string $column
     * @return string
     */
    public function getSelectSql($where,$column = "*"){
        $this->setSelect($column);
        $this->setSearchWhere($where);
        return $this->db->get_compiled_select($this->table);
    }

    /**
     * 设置查询字段
     * @param array | string  $select_fields
     */
    public function setSelect($select_fields){
        $tempField = $select_fields;
        if(!is_array($select_fields)){
            $tempField = explode(",", $select_fields);
        }
        foreach($tempField as $key=>$val){
            if(!strstr($val,".") && !strstr($val," ")){
                $tempField[$key] = $this->table.".".$val;
            }
            if(strstr($val, 'distinct') && !strstr($val, '`')){
                $distinctstr = explode(" ", $val);
                $this->db->distinct($distinctstr[1]);
                $tempField[$key] = $this->table.".".$distinctstr[1];
            }
        }
        $field = implode(",", $tempField);
        $this->db->select($field?$field:"*");
    }

    /**
     * 设置查询条件
     * @param array $where
     * $where 参数格式如：
     *        $where['title'] = array('value'=>'test','operator'=>'=');
     *        $where['id'] = array('value'=>array(1,2,3),'operator'=>'in');
     *        $where['join'][] = array('value'=>array('t_test as a','a.id=b.id'),'operator'=>'left');
     */
    public function setSearchWhere($where){
        if(!is_array($where)){
            return false;
        }
        foreach ($where as $key=>$item) {
            if($item['value'] === ''){
                continue;
            }
            if($key == 'join'){
            	//连接查询
            	foreach ($item as $joindata){
            		$this->db->join($joindata['value'][0],$joindata['value'][1],$joindata['operator']);
            	}
            }else if($item['operator'] == 'like'){
                $this->db->like($key,$item['value'],'after');
            }else if($item['operator'] == 'likeboth'){
                $this->db->like($key,$item['value']);
            }else if($item['operator'] == 'orderby'){
                $this->db->order_by($item['value']);
            }else if($item['operator'] == 'in'){
                $this->db->where_in($key,$item['value']);
            }else if($item['operator'] == '='){
                $this->db->where($key,$item['value']);
            }else if($item['operator'] == 'range'){
                if(!empty($item['value']['sdate'])){
                    $this->db->where($key." >=",$item['value']['sdate']);
                }
                if(!empty($item['value']['edate'])){
                    $this->db->where($key." <=",$item['value']['edate']);
                }
            }else{
                $this->db->where($key." ".$item['operator'],$item['value']);
            }
        }
    }

    /**
     * 查询记录数
     * @param $where
     * @return int
     */
    public function count($where){
        $this->setSearchWhere($where);
        //查询总数
        return $this->db->from($this->table)->count_all_results();
    }

    /***********************************上面where条件处理和下面的where条件处理不一样，要注意*********************************************/

	/**
	 *
	 * 更新数据
	 * @param array           $data    要更新的字段数据
	 * @param int|array       $where   非数组系统默认根据ID号查询，如果是数组where条件，格式参考_setWhere()方法
	 * @return int|boolean             返回影响行数，或者FALSE(更新失败)
	 */
	public function update($set,$where){

		if(!is_array($where)){
            $id = $where;
			$temp = array();
			$temp[$this->primary_key] = $id;
			$where = $temp;
		}
		$where['notorder'] = true;
        $this->operationlog->updateStart($this->fetchAll($where),$this->table,$this->primary_key);
		$this->_setWhere($where);
		$ret = $this->db->update($this->table,$set);
        $this->operationlog->updateEnd($set);
        if($this->_debug){
            $this->getExecSql();
        }
		return $ret?$this->db->affected_rows():$ret;
	}

	/**
	 *
	 * 插入数据
	 * @param array $data    要插入字段数据
	 * @return int|boolean   返回ID号，或者FALSE(添加失败)。注：批量插入数据，返回的是第一条插入的ID号
	 */
	public function insert($data){
		if(count($data)==count($data,1)){
			$datas[] = $data;
		}else{
			$datas = $data;
		}
		$ret = $this->db->insert_batch($this->table,$datas);
        $id = $ret?$this->db->insert_id():$ret;
        $this->operationlog->insertEnd($data,$this->table,$id);
        if($this->_debug){
            $this->getExecSql();
        }
		return $id;
	}

	/**
	 *
	 * 删除记录
	 * @param int|array     $key  正数系统默认是ID号，如果是数组则是多条件查询删除，格式参考_setWhere()方法
	 * @return int|boolean        返回影响的行数，或者FALSE(删除失败)
	 */
	public function delete($key){
		if(!is_array($key)){
			$where[$this->primary_key] = $key;
		}else{
			$where = $key;
		}
		$where['notorder'] = true;
        $this->operationlog->deleteStart($this->fetchAll($where),$this->table,$this->primary_key);
		$this->_setWhere($where);
		$ret = $this->db->delete($this->table);
        $this->operationlog->deleteEnd();
        if($this->_debug){
            $this->getExecSql();
        }
		return $ret?$this->db->affected_rows():$ret;
	}

	/**
	 * 执行sql语句
	 * @param string $sql
	 * @return
	 */
	public function query($sql){
		$query = $this->db->query($sql);
        if($this->_debug){
            $this->getExecSql();
        }
        return $query;
	}

	/**
	 * mysql 对于唯一字段，有则更新，无则添加数据
	 * @param array $datalist   要设置的字段(支持多条数据)
	 * @param array $updatelist 要更新的字段
	 * @return boolean|unknown
	 */
	public function mysql_replace($datalist,$updatelist = array()){
		if(empty($datalist) OR !is_array($datalist)){
			return false;
		}
		$sql = 'INSERT IGNORE INTO '.$this->table.' ';
		$tmplist = $datalist;
		if(is_array(current($datalist))){
			$i = 0;
			foreach($datalist as $row){
				$values = array_values($row);
				foreach ($values as $k=>$v){
					$values[$k] = $this->db->escape_str($v);
				}
				if($i == 0){
					$tmplist = $row;
					$sql .= '(`'.implode('`,`', array_keys($row)).'`) ';
					$sql .= "VALUES ('".implode("','", $values)."') ";
				}else{
					$sql .= ",('".implode("','", $values)."') ";
				}
				$i ++;
			}
		}else{
			$values = array_values($datalist);
			foreach ($values as $k=>$v){
				$values[$k] = $this->db->escape_str($v);
			}
			$sql .= '(`'.implode('`,`', array_keys($datalist)).'`) ';
			$sql .= "VALUES ('".implode("','", $values)."') ";
		}

		//$updatelist =  array_unique(array_merge(array_keys($tmplist),$updatelist));
		if(!empty($updatelist) && is_array($updatelist)){
			$sql .= 'ON DUPLICATE KEY UPDATE ';
			foreach($updatelist as $field){
				$sql .= "`$field`=VALUES($field),";
			}
			$sql = substr($sql, 0,-1);
		}
		$ret = $this->db->query($sql);
        if($this->_debug){
            $this->getExecSql();
        }
		return $ret?$this->db->affected_rows():$ret;
	}

	/**
	 *
	 * 设置where条件
	 * @param array $where
	 *              $where['field']        设置=语句，可以在field+空格+操作符。如$where['field >=']，可用于时间段查询
	 *              $where['orderby']      设置排序
	 *              $where['notorder']     去除默认排序，即不进行任何排序
	 *              $where['having']       设置分组查询条件 array('field'=>'value')
	 *              $where['groupby']      设置分组统计
	 *              $where['in']           设置in语句                array('field'=>'value') value：数组 或 字符串
	 *              $where['notin']        设置not in语句     array('field'=>'value') value：数组 或 字符串
	 *              $where['or']           设置or语句                array('field'=>'value')
	 *              $where['like']         设置like语句           array('field'=>'value') 注： 这里右边模糊查询like 'value%'
	 *              $where['likeboth']     设置like语句           array('field'=>'value') 注： 这里两边模糊查询like '%value%'
	 *              $where['join']         设置连接查询语句  如：'join'=>array(0=>array('log as a','a.logid=tablename.id AND a.addtime>=xxx','left'))
	 *              关于涉及调用的方法详细说明，请参考CI手册
	 * @return void
	 */
	protected function _setWhere($where){
		$isJoin = false;
		if(isset($where['join'])){
			$isJoin = true;
			foreach ($where['join'] as $joindata){
				$type = '';
				if(isset($joindata[2]))$type = $joindata[2];
				$this->db->join($joindata[0],$joindata[1],$type);
			}
			unset($where['join']);
		}
		if(isset($where['orderby'])){
			$where['orderby']?$this->db->order_by($where['orderby']):'';
			unset($where['orderby']);
			if(isset($where['notorder']))unset($where['notorder']);
		}else if(isset($where['notorder'])){
			unset($where['notorder']);
		}else if($this->primary_key){
			//$this->db->order_by($this->table.".".$this->primary_key." desc");
		}
		if(isset($where['having']) && $where['having']){
			foreach ($where['having'] AS $key=>$val){
				if($isJoin && !strstr($key,".")){
					$key = $this->table.".".$key;
				}
				$this->db->having($key,$val);
			}
			unset($where['having']);
		}
		if(isset($where['groupby'])){
			if($isJoin && !strstr($where['groupby'],".")){
				$where['groupby'] = $this->table.".".$where['groupby'];
			}
			$where['groupby']?$this->db->group_by($where['groupby']):'';
			unset($where['groupby']);
		}
		foreach($where AS $key=>$val){
			if($key=='in'){
				foreach ($val AS $key1=>$val1){
					if($isJoin && !strstr($key1,".")){
						$key1 = $this->table.".".$key1;
					}
					if($val1 !== '')$this->db->where_in($key1,$val1);
				}
			}else if($key=='notin'){
				foreach ($val AS $key1=>$val1){
					if($isJoin && !strstr($key1,".")){
						$key1 = $this->table.".".$key1;
					}
					if($val1 !== '')$this->db->where_not_in($key1,$val1);
				}
			}else if($key=='like'){
				foreach ($val AS $key1=>$val1){
					if($isJoin && !strstr($key1,".")){
						$key1 = $this->table.".".$key1;
					}
					$this->db->like($key1,trim($val1),'after');
				}
			}else if($key=='likeboth'){
				foreach ($val AS $key1=>$val1){
					if($isJoin && !strstr($key1,".")){
						$key1 = $this->table.".".$key1;
					}
					$this->db->like($key1,trim($val1));
				}
			}else if($key=='or'){
				foreach ($val AS $key1=>$val1){
					if($isJoin && !strstr($key1,".")){
						$key1 = $this->table.".".$key1;
					}
					$this->db->or_where($key1,$val1);
				}
			}else{
				if($isJoin && !strstr($key,".")){
					$key = $this->table.".".$key;
				}
				$this->db->where($key,trim($val));
			}
		}
	}

	/**
	 * 获取当前表的字段
	 * @return array
	 */
	private function _getEditableFields(){
		if (empty($this->fields)){
			$this->db->cache_on();
			$this->fields = $this->db->list_fields($this->table);
			$this->db->cache_off();
		}
		return $this->fields;
	}

	/**
	 * 获取查询sql语句(执行前)
	 * @param string $select_override
	 * @return string
	 */
	protected function _compile_select($select_override = FALSE)
	{
		// Combine any cached components with the current statements
		//$this->db->_merge_cache();

		$this->db->from($this->table);
		// ----------------------------------------------------------------
		// Write the "select" portion of the query
		if ($select_override !== FALSE)
		{
			$sql = $select_override;
		}
		else
		{
			$sql = ( ! $this->db->ar_distinct) ? 'SELECT ' : 'SELECT DISTINCT ';

			if (count($this->db->ar_select) == 0)
			{
				$sql .= '*';
			}
			else
			{
				// Cycle through the "select" portion of the query and prep each column name.
				// The reason we protect identifiers here rather then in the select() function
				// is because until the user calls the from() function we don't know if there are aliases
				foreach ($this->db->ar_select as $key => $val)
				{
					$no_escape = isset($this->db->ar_no_escape[$key]) ? $this->db->ar_no_escape[$key] : NULL;
					$this->db->ar_select[$key] = $this->db->_protect_identifiers($val, FALSE, $no_escape);
				}

				$sql .= implode(', ', $this->db->ar_select);
			}
		}
		// ----------------------------------------------------------------
		// Write the "FROM" portion of the query
		if (count($this->db->ar_from) > 0)
		{
			$sql .= "\nFROM ";

			$sql .= $this->db->_from_tables($this->db->ar_from);
		}
		// ----------------------------------------------------------------
		// Write the "JOIN" portion of the query
		if (count($this->db->ar_join) > 0)
		{
			$sql .= "\n";

			$sql .= implode("\n", $this->db->ar_join);
		}
		// ----------------------------------------------------------------
		// Write the "WHERE" portion of the query
		if (count($this->db->ar_where) > 0 OR count($this->db->ar_like) > 0)
		{
			$sql .= "\nWHERE ";
		}
		$sql .= implode("\n", $this->db->ar_where);
		// ----------------------------------------------------------------
		// Write the "LIKE" portion of the query

		if (count($this->db->ar_like) > 0)
		{
			if (count($this->db->ar_where) > 0)
			{
				$sql .= "\nAND ";
			}

			$sql .= implode("\n", $this->db->ar_like);
		}
		// ----------------------------------------------------------------
		// Write the "GROUP BY" portion of the query
		if (count($this->db->ar_groupby) > 0)
		{
			$sql .= "\nGROUP BY ";

			$sql .= implode(', ', $this->db->ar_groupby);
		}
		// ----------------------------------------------------------------
		// Write the "HAVING" portion of the query
		if (count($this->db->ar_having) > 0)
		{
			$sql .= "\nHAVING ";
			$sql .= implode("\n", $this->db->ar_having);
		}
		// ----------------------------------------------------------------
		// Write the "ORDER BY" portion of the query
		if (count($this->db->ar_orderby) > 0)
		{
			$sql .= "\nORDER BY ";
			$sql .= implode(', ', $this->db->ar_orderby);

			if ($this->db->ar_order !== FALSE)
			{
				$sql .= ($this->db->ar_order == 'desc') ? ' DESC' : ' ASC';
			}
		}
		// ----------------------------------------------------------------
		// Write the "LIMIT" portion of the query
		if (is_numeric($this->db->ar_limit))
		{
			$sql .= "\n";
			$sql = $this->_limit($sql, $this->db->ar_limit, $this->db->ar_offset);
		}
		return $sql;
	}

	/**
	 * Resets the active record values.  Called by the get() function
	 *
	 * @param	array	An array of fields to reset
	 * @return	void
	 */
	protected function _reset_run($ar_reset_items)
	{
		foreach ($ar_reset_items as $item => $default_value)
		{
			if ( ! in_array($item, $this->db->ar_store_array))
			{
				$this->db->$item = $default_value;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Resets the active record values.  Called by the get() function
	 *
	 * @return	void
	 */
	protected function _reset_select()
	{
		$ar_reset_items = array(
				'ar_select'			=> array(),
				'ar_from'			=> array(),
				'ar_join'			=> array(),
				'ar_where'			=> array(),
				'ar_like'			=> array(),
				'ar_groupby'		=> array(),
				'ar_having'			=> array(),
				'ar_orderby'		=> array(),
				'ar_wherein'		=> array(),
				'ar_aliased_tables'	=> array(),
				'ar_no_escape'		=> array(),
				'ar_distinct'		=> FALSE,
				'ar_limit'			=> FALSE,
				'ar_offset'			=> FALSE,
				'ar_order'			=> FALSE,
		);

		$this->_reset_run($ar_reset_items);
	}

	/**
	 * 处理请求参数，为空的删除掉
	 * @param array   $datalist
	 * @param boolean $trim      是否去掉左右空格
	 * @param boolean $delempay  是否删除空数据
	 * @return array
	 */
	public function _dealPostData($datalist,$trim = true,$delempay = true){
		foreach ($datalist as $key=>$val){
			if($trim && !is_array($val))$val = trim($val);
			if($delempay && empty($val)){
				unset($datalist[$key]);
			}else{
				$datalist[$key] = $val;
			}
		}
		return $datalist;
	}

    /********************************************以下方法不再使用，而是使用上面的方法代替*********************************************************/

    /**
     *
     * 获取单条记录
     * @param int|array $where   非数组系统默认根据ID号查询，如果是数组where条件，格式参考_setWhere()方法
     * @return object
     */
    public function fetchOne($where=array(),$field=""){
        if(!is_array($where)){
            $temp = array();
            $temp['id'] = $where;
            $where = $temp;
        }
        $where['notorder'] = true;
        $this->_setWhere($where);
        if($field){
            $this->db->select($field);
        }
        $this->db->limit("1");
        $ret = $this->db->get($this->table);
        if($this->_debug){
            $this->getExecSql();
        }
        if(!empty($ret)){
            return $ret->row();
        }
        return array();
    }

    /**
     *
     * 查询多记录（此方法支持多表(join)查询）
     * @param array  $where       设置where条件，格式参考_setWhere()方法
     * @param string $field       设置查询字段
     * @param int    $pageIndex   分页页码，第一页即为0，默认为0。注：如果这里为-1，则返回sql语句，不进行查询
     * @param int    $limit       默认每页显示记录数
     * @return object
     */
    public function fetchAll($where=array(),$field="*",$pageIndex=0,$limit=0){
        if($pageIndex > 1000000){
            die("pageIndex error");//limit分页限制，防止limit参数溢出
        }
        if(strstr($field,",")){
            $tempField = explode(",", $field);
            foreach($tempField as $key=>$val){
                if(!strstr($val,".") && !strstr($val," ")){
                    $tempField[$key] = $this->table.".".$val;
                }
                if(strstr($val, 'distinct') && !strstr($val, '`')){
                    $distinctstr = explode(" ", $val);
                    $this->db->distinct($distinctstr[1]);
                    $tempField[$key] = $this->table.".".$distinctstr[1];
                }
            }
            $field = implode(",", $tempField);
        }
        $this->db->select($field?$field:"*");
        if($pageIndex==-1)$where['notorder'] = true;
        $this->_setWhere($where);
        if($pageIndex==-1){
            $sql = $this->_compile_select();//返回执行前的查询语句可用于导出数据方法直接使用sql语句做查询
            $this->_reset_select();
            return $sql;
        }
        if($limit){
            $this->db->limit($limit,$pageIndex*$limit);
        }
        $ret = $this->db->get($this->table);
        if(!empty($ret)){
            $ret = $ret->result();
        }else{
            $ret = array();
        }
        if($this->_debug){
            $this->getExecSql();
        }
        return $ret;
    }

    /**
     * 获取表记录数
     * @param array  $where  设置where条件，格式参考_setWhere()方法
     * @return int
     */
    public function getCount($where=array()){
        $this->db->select("count(*) as count");
        $where['notorder'] = true;
        $this->_setWhere($where);
        $ret = $this->db->get($this->table)->row();
        if($this->_debug){
            $this->getExecSql();
        }
        return !empty($ret)?$ret->count:0;
    }

    /**
     * 获取表记录数(和getCount方法是一样的)
     * @param array  $where  设置where条件，格式参考_setWhere()方法
     * @param string $field  自定义查询字段，默认为count(*) as count
     * @return int
     */
    public function fetchCount($where=array(),$field=''){
        if(!$field)$field = "count(*) as count";
        $this->db->select($field);
        $where['notorder'] = true;
        $this->_setWhere($where);
        $ret = $this->db->get($this->table);
        if(!empty($ret)){
            $ret = $ret->row();
        }else{
            $ret = array();
        }
        if($this->_debug){
            $this->getExecSql();
        }
        return !empty($ret)?$ret->count:0;
    }

    /**
     * 处理执行的sql用于写sql日志
     */
    protected function getExecSql(){
        $useTime = $this->db->elapsed_time();
        //$useTime = $useTime*1000;
        $sql = $this->db->last_query();
        $sql = str_replace(array("\n","\r"), ' ', $sql);
        $sql = "[".date("Y-m-d H:i:s")."] ".$this->session->nick." | ".$useTime." | ".$sql;
        if($useTime >= 1){
            $this->exec_sql['slow'][] = $sql;
		}else if(ENVIRONMENT != 'production'){
            $this->exec_sql['common'][] = $sql;
        }
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        //如果配置有日志目录，则记录sql日志
        if (defined('SYSTEM_LOGS_DIR') && !empty($this->exec_sql)) {
            $sql_log_dir = SYSTEM_LOGS_DIR;
            if(empty($sql_log_dir)){
                //
                return false;
            }
            if (!is_dir($sql_log_dir)) {
                @mkdir($sql_log_dir, 0777, true);
            }

            //写sql日志
            $sqls = '';
            $fp = false;
            foreach ($this->exec_sql as $key=>$item) {
                if($key == 'slow'){
                    $fp = fopen($sql_log_dir."slowsql.log",'a');
                }else{
                    $fp = fopen($sql_log_dir."sql.log",'a');
                }
                $sqls .= implode("\n",$item)."\n";
            }
            if( $fp ) {
                if($sqls){
                    flock($fp, LOCK_EX);
                    fwrite($fp,$sqls);
                    flock($fp, LOCK_UN);
                }
                fclose($fp);
            }
        }
    }

}