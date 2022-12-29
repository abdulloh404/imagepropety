<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->my_table = 'admin_menus';
        $this->load->library("Nested_set");
        $this->nested_set->setControlParams($this->my_table, 'id', 'lft', 'rgt', 'parent_id', 'title', 'level', 'path', 'has_child');
    }

    public function reConstruction() {
        $this->nested_set->recalculateLevelPath();
    }

    public function getMaxCode() {
        $this->db->reset_query();
        $this->db->limit(1);
        $q = $this->db->select('max(code) as maxcode')
            ->get($this->my_table)
        ;
        return $q->row()->maxcode;
    }

    public function getDataByID($id, $fields=[]) {
        $this->db->reset_query();
        $q = $this->db->select((count($fields)==0)?'*':implode(',', $fields))
            ->get_where($this->my_table, ['id' => $id], 1)
            ;
        if ($q && $q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getList($fields=[],$joins=[],$where=[], $limit=null, $order_by=['ordering','asc'])// 0 means all rows
    {
        $childs = [];
        $list = parent::getList($fields,$joins, $where,$limit,$order_by);
        $list2=[];
        foreach($list as $itm) $list2[$itm->id] = $itm;
        foreach($list as $itm) {
            $childs[$itm->parent_id] = isset($childs[$itm->parent_id])?$childs[$itm->parent_id]:[];
            $childs[$itm->parent_id][] = $itm;
        }
        return $childs;
    }

    //$this->Menu_model->saveData($data) // add new item to tree
    //$this->Menu_model->updateData($data) // update an item in tree

    public function saveData($data = [], $params = [])
    {
        if(@$params['direct_save']) {
            if(@!$data['id']) {
                $newItem = parent::saveData($data);
                return $newItem;
            }else{
                parent::updateData($data);
                return;
            }
        }
        // new parent
        $parentObj = $this->getDataByID(intval($params['parent_id']));
        $parent = (array)$parentObj;
        if($parentObj) {
            $parent['has_child'] = $parent['has_child']+1;
            $result = parent::updateData($parent);
            if(!$result) {
                return false;
            }

            //$data['level'] = $parent['level']+1;
            //$data['parent_id'] = $parent['id'];
            //$data['path'] = $parent['path']?$parent['path'].'/'.$parent['id']:$parent['id'];
        }
//$this->dump([$parent, $data]);
        //if(intval($data['id'])) unset($data['id']);
        $nexttoObj = null;
        $nexttoNode = [];
        if(@$params['nextto_id']) {
            $nexttoObj = $this->getDataByID(intval($params['nextto_id']));
            $nexttoNode = ['id'=>$nexttoObj->id, 'parent_id'=>$nexttoObj->parent_id, 'lft'=>$nexttoObj->lft, 'rgt'=>$nexttoObj->rgt];
        }
        if(!intval($data['id'])) {//add new item
            $data['has_child'] = '0';
            if($parentObj) {
                $data['lft']=$parent['rgt'];
                $data['rgt']=$parent['rgt']+1;
                $data['parent_id']=$params['parent_id'];
                $data['level'] = $parent['level']+1;
                $new_insert_id = parent::saveData($data);
                $newDataObj = $this->getDataByID($new_insert_id);
                $newData = (array)$newDataObj;
                $newData['parent_id']=$params['parent_id'];
                if(!$nexttoObj) {
                    $result = $this->nested_set->setNodeAsLastChild($newData, $parent);
                }else{
                    $result = $this->nested_set->setNodeAsNextSibling($newData, $nexttoNode, $parent);
                }
                $newData['lft']=$result['lft'];
                $newData['rgt']=$result['rgt'];
                $result=$newData;
            }else{
                $result = $this->nested_set->insertNewTree($data);
                $data['lft']=$result['lft'];
                $data['rgt']=$result['rgt'];
                $result=$data;
            }
        }else{
            if(!$parentObj) {
                return false;
            }
            parent::updateData($data);
            //$this->dump([$parent['id'], $parent['level']]);
            $data['level'] = $parent['level']+1;
            if(!$nexttoObj)
                $result = $this->nested_set->setNodeAsFirstChild($data, $parent);
            else
                $result = $this->nested_set->setNodeAsNextSibling($data, $nexttoNode, $parent);
            /* $oldNodeObj = $this->getDataByID(intval($data['id']));
            $oldParentNodeObj = $this->getDataByID(intval($oldNodeObj->parent_id));
            $oldParentNodeObj = is_object($oldParentNodeObj)?$oldParentNodeObj:$oldNodeOb;
            $oldNode = ['id'=>$oldNodeObj->id, 'parent_id'=>$oldNodeObj->parent_id, 'lft'=>$oldNodeObj->lft, 'rgt'=>$oldNodeObj->rgt];
            $oldParentNode = ['id'=>$oldParentNodeObj->id, 'parent_id'=>$oldParentNodeObj->parent_id, 'lft'=>$oldParentNodeObj->lft, 'rgt'=>$oldParentNodeObj->rgt];
            unset($data['lft']);
            unset($data['rgt']);
            $this->nested_set->updateNode($parent, $oldParentNode, $oldNode, $data);
            $result = true; */
        }
        return $result;
    }

    public function getInsertId() {
        return $this->nested_set->getInsertId();
    }

    public function deleteData($id, $where=[])
    {
        $item = $this->getDataArray($id);
        $result = $this->nested_set->deleteNode($item);
        if(!$result)
            return false;
        $parent = $this->getDataArray($item['parent_id']);
        $numchilds = (($parent['rgt']-$parent['lft'])-1)/2;
        if($numchilds>0) {
            $firstChild = $this->nested_set->getFirstChild($parent);
            $lastChild = $this->nested_set->getLastChild($parent);
            $parent['has_child'] = $numchilds;
        }else{
            $parent['has_child'] = '0';
        }
        parent::updateData($parent);
        return $result;
    }

    public function getTreeOptions($items) {
        return $this->nested_set->getTreeOptions($items);
    }

    public function updateHasChild($data) {
        $parentObj = $this->getDataByID(intval($data['parent_id']));
        $parent = [];
        if($parentObj) {
            $parent = (array)$parentObj;
            $parent['has_child'] = $parent['has_child']+1;
            $result = parent::updateData($parent);
            
            if(!$result) return false;
            else return true;
        }
        return false;
    }

    public function updatePath($data) {
        $parentObj = $this->getDataByID(intval($data['parent_id']));
        if(!intval($data['parent_id']) || !$parentObj)
            return "";
        if($parentObj) {
            $path = $this->updatePath((array)$parentObj);
            $path = $path?$path."/".$parentObj->id:$parentObj->id;
            $data['path'] = $path;
            $result = parent::updateData($data);
            
            return $path;
        }
        return "";
    }

    public function updateLevel($data) {
        $parentObj = $this->getDataByID(intval($data['parent_id']));
        if(!intval($data['parent_id']) || !$parentObj) {
            $data['level'] = 1;
            $result = parent::updateData($data);
            return 1;
        }
        if($parentObj) {
            $level = $this->updateLevel((array)$parentObj);
            $level = $level+1;
            $data['level'] = $level;
            $result = parent::updateData($data);
            return $level;
        }
        return 1;
    }
}