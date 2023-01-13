<?php

//defined('BASEPATH') or exit('No direct script access allowed');

namespace App\Models;


use CodeIgniter\Model;

class Db_model extends Model
{

	/**  */
	protected $table   = 'tb_banners';

	public function __construct()
	{
		$this->db = db_connect();
		$db = \Config\Database::connect();
	}

	function __destruct()
	{
		unset($_SESSION['fetchAll']);
	}

	function getBanners($db)

	{
		/** Database conect */
		$this->db = db_connect();
		$banners = $this->table('tb_banners')
			->get()
			->getResultObject();
		return view('admin/bannerManage', ['banners' => $banners]);
	}




	function getRowsCount($sql)
	{


		return count($this->fetchAll($sql));
	}

	public function fetchAll($sql, $param = array())
	{

		//$query = $this->db->query( $sql );

		//return $query->getResultObject();

		if (!isset($_SESSION['fetchAll'][$sql])) {

			$query = $this->db->query($sql);

			if (!$query) {
				return array();
			}

			$_SESSION['fetchAll'][$sql] = $query->getResultObject();
		}

		//	arr( $sql );
		return $_SESSION['fetchAll'][$sql];
	}

	//
	//
	public function update_($table, $arr, $condition = NULL)
	{
		//
		$sql = "
			UPDATE " . $table . " SET ";
		$i = 1;
		foreach ($arr as $k => $v) {

			if ($v == '' && !is_numeric($v)) {
				$sql .= "	" . $k . " =  NULL";
			} else
				// if($k == "detail"){
				// 	$sql .= "	". $k ." = '". htmlspecialchars( $v ) ."'";	
				// }
				$sql .= "	" . $k . " = '" . addslashes($v) . "'";

			if ($i != count($arr))
				$sql .= ",";

			++$i;
		}
		if (!empty($condition))
			$sql .= " WHERE " . $condition;



		///exit;
		if ($this->execDatas($sql))
			return true;

		return false;
	}


	//
	//
	public function insert_($table_name, $data, $upDuplicate = false, $action = 'INSERT')
	{

		if (false) {
		} else {

			$val = '';
			$keep = array();
			$i = 0;
			foreach ($data as $ka => $va) {

				++$i;

				//	$val .= "?";
				$val .= "'" . addslashes($va) . "'";

				$keep[] = addslashes($va);
				//$keep[] = $va;


				if ($i != count($data))
					$val .= ",";
			}


			$sql = $action . " INTO " . $table_name . " ";

			$sql .= "(" . implode(',', array_keys($data)) . ") VALUES (" . $val . ") ";

			if ($upDuplicate)
				$sql .= "ON DUPLICATE KEY UPDATE " . $upDuplicate;


			//file_put_contents( 'log.sql', $sql );

			//$this->execDatas( $sql, $keep );

			if ($this->execDatas($sql, $keep))
				return $this->db->insertID();

			return false;
		}

		//exam: INSERT INTO a (name) VALUES ('x') ON DUPLICATE KEY UPDATE time_update = NOW()
	}

	//
	//
	public function showColumns($table)
	{

		$sql = "SHOW COLUMNS FROM " . $table;

		$keep = array();
		foreach ($this->fetchAll($sql) as $v) {
			$keep[] = $v->Field;
		}
		return $keep;
	}




	public function fetch($sql, $param = array())
	{


		//arr( $sql );
		foreach ($this->fetchAll($sql) as $ka => $res) {

			return $res;
		}
	}

	function getUser($user_id = NULL)
	{


		$sql = "
			SELECT 
				u.role_id,
				u.last_menu_id, 
				u.id as user_id, 
				u.user_company_id, 
				u.username, 
				u.first_name,
				u.request_group_id,
				u.end_date, 
				u.group_id, 
				u.usignal, 
				u.phtotalk, 
				u.last_ip_address, 
				u.ip_address, 
				u.password, 
				u.salt, 
				u.email, 
				u.activation_code, 
				u.forgotten_password_code, 
				u.forgotten_password_time, 
				u.remember_code, 
				u.created_on, 
				u.last_login, 
				u.active, u.last_name, u.company, u.phone, 
				u.avatar, u.gender, u.warehouse_id, u.biller_id, 
				u.company_id, u.show_cost, u.show_price, u.award_points, 
				u.view_right, 
				u.edit_right, 
				u.allow_discount,
				u.admin,
				u.super_admin,
				DATEDIFF( u.end_date, NOW() ) AS ddiff
			FROM aa_users u
			WHERE u.id = " . $user_id . "
		";

		//arr($param['sql']);

		foreach ($this->fetchAll($sql) as $ku => $vu) {

			if (empty($vu->user_company_name)) {

				$vu->user_company_name = 'ERP: MUSION Co.,Ltd.';
			} else {

				$company_name = $vu->user_company_name;
				$company_name = htmlspecialchars_decode($company_name);
				$company_name = strip_tags($company_name);
				$company_name = str_replace(array(' ', '.', ','), '-', $company_name) . '';

				$vu->user_company_name = $company_name;
			}

			$vu->user_company_logo_img = '';

			if (!empty($vu->user_company_logo)  && file_exists($vu->user_company_logo)) {
				$vu->user_company_logo_img = '<img src="' . $vu->user_company_logo . '" style="width: 60px;">';
			}

			$_SESSION['u'] = $vu;

			return $vu;
		}
	}

	//
	//
	public function delete_($tb_name, $cond = array())
	{

		$condition = '';

		$i = 1;
		foreach ($cond as $k => $v) {
			$condition .= $k . " = '" . $v . "'";
			if ($i != count($cond))
				$condition .= " AND ";

			++$i;
		}
		$sql = "
			DELETE FROM " . $tb_name . "
			WHERE " . $condition;
		//arr( $sql );
		//exit;
		if ($this->execDatas($sql)) {


			return true;
		}


		return false;
	}

	public function execDatas($sql, $param = array())
	{

		//arr( $sql );

		if ($this->db->query($sql)) {

			unset($_SESSION['fetchAll']);
			return true;
		}
		return false;
	}
}
