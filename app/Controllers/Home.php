<?php

namespace App\Controllers;

use App\Models\Db_model;
use App\Models\Front_model;
use App\Models\User_model;
use App\Models\ConfigForm;

class Home extends BaseController
{

	public function __construct()
	{
		parent::__construct();

		$this->dao = new Db_model();
		$this->front_model = new Front_model();
		$this->user_model = new User_model();
		$this->ConfigForm = new ConfigForm();
		//$this->codea = new Codea();
	}


	public function index($alias = NULL, $call_func = NULL, $parent_id = NULL, $sub_config_id = NULL)
	{


		//arr($parent_id);



		if ($this->request->getMethod() == 'get') {
			$_REQUEST = $this->request->getGet();
		} else {

			$_REQUEST = $this->request->getPost();
		}


		if ($alias == 'logout') {

			// $_SESSION = array();			
			unset($_SESSION);
			unset($_COOKIE['user_id']);
			setcookie('user_id', null, -1, '/');
			session_destroy();

			//header( 'Location: '. comeBack() .'' );
			header('Location:' . front_link(116) . '');

			exit;
		}

		if (isset($_COOKIE['user_id'])) {
			unset($_SESSION['user_id']);
			$_SESSION['user_id'] = $_COOKIE['user_id'];
		}

		if (!empty($_SESSION['user_id'])  && $alias == 'signin') {

			header('Location:' . base_url() . '/dashboard' . '');
			// $this->load->view($param['theme'], $param);
			exit;
		}



		$sql = "
			SELECT 
				* 
			FROM aa_front_page 
			WHERE alias = '" . $alias . "'
			AND active = 1
			LIMIT 0, 1
		";

		$params = array();
		foreach ($this->dao->fetchAll($sql) as $ka => $va) {

			$model = $va->modelName;

			if (!isset($this->$model)) {

				$use_package = "App\\Models\\" . ucfirst(strtolower($model));

				$this->$model = new $use_package();
			}


			$params = convertObJectToArray($va);

			if (!empty($va->user_login)) {


				if (empty($_SESSION['user_id'])) {


					$sql = "
						SELECT 
							* 
						FROM aa_front_page 
						WHERE id = 13
					";

					foreach ($this->dao->fetchAll($sql) as $ka => $vaa) {

						$model = $vaa->modelName;

						if (!empty($_REQUEST['action'])) {

							$call_func = $vaa->action_call;

							return call_user_func(array($this->$model, $call_func), $params);
						}

						$params = convertObJectToArray($vaa);

						$params['formMode'] = 'get';

						$params['secret'] = csrf_field() . '<input type="hidden" name="action" value="1" />' . getBombLacyForm() . '';

						if (!empty($vaa->theme)) {

							$params['page'] = view($vaa->page, $params);

							return view($vaa->theme, $params);
						}

						return view($vaa->page, $params);
					}

					exit;
				} else {

					$userid = empty($_SESSION['user_id']) ? $_COOKIE['user_id'] : $_SESSION['user_id'];

					$this->dao->getUser($userid);

					if (!empty($va->admin_menu)) {

						$getAdminMenu = getAdminMenu(NULL, true);


						$pass = false;
						foreach ($getAdminMenu as $km => $vm) {

							if ($vm->id ==  $va->id) {

								$pass = true;
							}
						}



						if (!$pass) {

							$params['page'] = view('500', $params);

							return view('frontView', $params);
							exit;
						}
					}





					if ($va->id == 340) {

						$params['call_func'] = $call_func;
						$params['formMode'] = 'get';
						$params['secret'] = csrf_field() . '<input type="hidden" name="action" value="1" />' . getBombLacyForm() . '';
						$params['page'] = call_user_func(array($this->$model, 'index'), $params);
						return view($va->theme, $params);
						exit;
					}
				}
			}



			$params['parent_id'] = $parent_id;
			$params['formMode'] = 'post';
			$params['token_val'] = get_token('val');

			$params['secret'] = '<input type="hidden" name="' . get_token('name') . '" value="' . $params['token_val'] . '"><input type="hidden" name="action" value="1" />' . getBombLacyForm() . '';

			$params['sub_config_id'] = $sub_config_id;

			$params['call_func'] = $call_func;
			$params['myid'] = $call_func;



			if (!empty($call_func) && method_exists($this->$model, $call_func)) {


				//arr($params);
				return call_user_func(array($this->$model, $call_func), $params);
			} else if (!empty($_REQUEST['action']) && method_exists($this->$model, $va->action_call)) {

				$call_func = $va->action_call;

				return call_user_func(array($this->$model, $call_func), $params);
			} else {


				//arr( $params);		
				if (!empty($va->set_page_html)) {

					foreach (json_decode($va->set_page_html) as $ks => $vs) {

						$model = $vs->model;

						if (!isset($this->$model)) {
							$use_package = "App\\Models\\" . ucfirst(strtolower($model));
							$this->$model = new $use_package();
						}

						foreach ($vs as $kp => $vp) {
							$params[$kp] = $vp;
						}


						$call_user_func = call_user_func(array($this->$model, $vs->call_func), $params);

<<<<<<< Updated upstream
=======

						//  comment เพราะ เวลาใส่บรรทัดนี้แล้ว error ไม่สารถส่งค่าแบบ Object and array ได้
>>>>>>> Stashed changes
						// foreach ($call_user_func as $kc => $vc) {
						// 	$params[$kc] = $vc;
						// }
					}
				}

				$params['params'] = $params;

				if (!empty($va->theme)) {

					$params['page'] = view($va->page, $params);

					return view($va->theme, $params);
				} else {
					return view($va->page, $params);
				}
			}
		}

		$params['page'] = view('500', $params);

		return view('frontView', $params);
	}



	function setFileWhenStartProgram()
	{

		exit;
		//C:\wamp64\www\fm91-news-portal\app\Views\admin

		$getFilesInDirectory = getFilesInDirectory($paths = array('C:\wamp64\www\fm91-news-portal\app\Views\admin'));


		// arr($getFilesInDirectory );

		//exit;
		foreach ($getFilesInDirectory['files_path'] as $kf => $vf) {

			arr($vf);

			arr($getFilesInDirectory['files'][$kf]);

			$ex = explode('.', $getFilesInDirectory['files'][$kf]);

			$fdasdf = file_get_contents($vf);

			file_put_contents('C:/wamp64/www/fm91-news-portal/New_folder/' . $ex[0] . '.php', $fdasdf);
		}
		exit;
		if (false) {
			$getFilesInDirectory = getFilesInDirectory($paths = array('C:\wamp64\www\fm91-news-portal\app\Views\admin'));

			$i = 101;
			foreach ($getFilesInDirectory['files'] as $kf => $vf) {

				$ex = explode('.', $getFilesInDirectory['files'][$kf]);



				$sql = "
					REPLACE INTO aa_front_page ( id, alias, page, theme, set_page_html, modelName, modelFunction, user_login, admin_login, action_call, title, icon, config_id, active, time_update) 
					
					VALUES
					( " . $i . ", '" . $ex[0] . "', 'admin/" . $ex[0] . "', NULL, NULL, 'front_model', 'index', 0, 0, 'Add_Attribute', 'Add_Attribute', '<i class=\"far fa-exclamation-triangle\"></i>', 0, 1, NOW() );

				";

				$this->dao->execDatas($sql);


				++$i;
			}



			exit;
		}
	}


	public function task()
	{

		$sql = "
			REPLACE INTO admin_model_config ( config_id, on_line, report_type_code, tb_main, config_detail, config_detail_, config_empty_form, config_doc_head_id, config_use, config_order, config_database, config_comment )
			SELECT 
				594 as config_id, on_line, report_type_code, tb_main, config_detail, config_detail_, config_empty_form, config_doc_head_id, config_use, config_order, config_database, config_comment 
			FROM admin_model_config 
			WHERE config_id = 398
		";



		$sql = "
			replace INTO admin_model_config_columns ( config_id, c, config_columns_w, config_columns_name, config_columns_label, config_columns_detail, config_columns_position, config_columns_order, config_override_id )

			SELECT
			    615, NULL as c, config_columns_w, config_columns_name, config_columns_label, config_columns_detail, config_columns_position, config_columns_order, config_override_id
			FROM admin_model_config_columns
			WHERE `config_id` = 32
			
			
		";
	}
}