<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    protected $my_table;
    protected $error;
    public function __construct()
    {
        parent::__construct();
    }

    public function setTable($table) {
        $this->my_table = $table;
    }

    public function saveData($data = [], $params = [])
    {
        $this->db->reset_query();
        if ($this->db->insert($this->my_table, $data)) {
            return $this->db->insert_id();
        }
        $this->error = $this->db->error();
        return false;
    }

    public function getInsertId() {
        return $this->db->insert_id();
    }

    public function updateData($data = [], $where=[], $params = [])
    {
        if(!count($where)) {
            if(!isset($data['id'])) {
                $this->error = lang('Not_found_id_key');
                return false;
            }
            $id = $data['id'];
            $where = ['id' => $id];
        }
        $this->db->reset_query();
        if ($this->db->update($this->my_table, $data, $where)) {
            return true;
        }
        $this->error = $this->db->error();
        return false;
    }

    public function deleteData($id, $where=[])
    {
        $this->db->reset_query();
        $where = count($where)?$where:['id' => $id];
        if ($this->db->delete($this->my_table, $where)) {
            return true;
        }
        return false;
    }

    public function getData($id, $where=[])
    {
        $this->db->reset_query();
        $where = count($where)?$where:['id' => $id];
        $q = $this->db->get_where($this->my_table, $where, 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    
    public function getDataArray($id, $where=[])
    {
        $this->db->reset_query();
        $where = count($where)?$where:['id' => $id];
        $q = $this->db->get_where($this->my_table, $where, 1);
        if ($q->num_rows() > 0) {
            return $q->row_array();
        }
        return false;
    }

    public function getList($fields=[], $joins=[], $where=[], $limit=null, $order_by=['id','asc'])// 0 means all rows
    {
        $this->db->reset_query();
        if(count($fields)) {
            $this->db->select(implode(',', $fields));
        }
        if(count($joins)>0) {
            foreach($joins as $join) {
                if(is_array($join)&&(count($join)>=2)) {
                    if(isset($join[3]))
                        $this->db->join($join[0], $join[1], $join[2]);
                    else
                        $this->db->join($join[0], $join[1]);
                }
            }
        }
        //$where = (is_array($where) && count($where))?$where:null;
        $this->db->order_by($order_by[0],$order_by[1]);
        $q = $this->db->get_where($this->my_table.' a', $where, $limit);
        //$this->dump([$q, $this->my_table,  $this->db->error()], 1);
        //echo "<xmp>";var_dump($where, $q->result());echo "</xmp>";exit;
        if ($q && $q->num_rows() > 0) {
            return $q->result();
        }
        return array();
    }

    public function getError() {
        return $this->error;
    }

    public function dump($data, $exit=false) {
        echo "<xmp>";var_dump($data);echo "</xmp>";
        if($exit) exit;
    }
}
