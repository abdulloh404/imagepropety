<?php

//
//
namespace App\Models;

use App\Models\Db_model;
use App\Models\Auth_model;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Config\Services;


class Codea
{


	// public function __construct()
	// {
	// 	parent::__construct();
	// 	$this->load->helper(array('form', 'frontlink'));
	// }


	function uploadBanner($params = array())
	{
		$filename = array("bannername");

		$config['upload_path'] = '../uploads/';
		$config['allowed_types'] = 'jpg|jpeg|png|gif';
		$config['max_size'] = 0; // no limit
		$config['max_width'] = 0;
		$config['max_height'] = 0;
		$config['file_name'] = array("bannername");

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('file_field')) {
			$error = array('error' => $this->upload->display_errors());
			print_r($error);
		} else {
			$data = array('upload_data' => $this->upload->data());
			print_r($data);
		}

		// Insert the file information into the database
		// $sql = "INSERT INTO photos (filename, type, size) VALUES (?, ?, ?)";
		// $stmt = mysqli_prepare($conn, $sql);
		// mysqli_stmt_bind_param($stmt, "sss", $filename, $filetype, $filesize);
	}



	function validationUri()
	{


		$datas['success'] = 1;

		echo json_encode($datas);
	}


	function uploadUrl($params = array())
	{

		$model = 'Img_model';

		$use_package = "App\\Models\\$model";

		$MakeImage = new $use_package();

		$this->config = getConfig_($params['config_id']);

		$k_tb_name = $this->config->tb_main;

		$upload_folders = 'upload/' . $k_tb_name . '';

		$keep = array();
		foreach (explode('/', $upload_folders) as $ke => $ve) {

			$keep[] = $ve;

			if (!is_dir(implode('/', $keep))) {
				@mkdir(implode('/', $keep));
			}
		}

		$allow_extensions = array('jpg', 'jpeg', 'png');

		foreach ($_FILES as $kf => $vf) {

			$file_name = $vf['name'];

			$extension = getExtension($vf['name']);

			if (in_array(strtolower($extension), $allow_extensions)) {


				if (!empty($params['parent_id'])) {

					$parent_id = $params['parent_id'];
				} else {

					if (!empty($_SESSION['parent_id'])) {

						$parent_id = $_SESSION['parent_id'];
					} else {

						$sql = "
							SELECT
								MAX( " . $this->config->pri_key . " ) + 1 as t
							FROM " . $k_tb_name;

						$parent_id = NULL;

						$_SESSION['parent_id'] = $parent_id;
					}
				}

				$img_path = $upload_folders . '/' . md5($k_tb_name . '-' . $parent_id . '-' . $file_name . '') . '.' . $extension;

				$option['cutImg']['w'] = 1200;

				$option['cutImg']['h'] = 630;

				$MakeImage->genImg(
					$vf['tmp_name'],
					$img_path,
					$new_width = $option['cutImg']['w'],
					$new_height = $option['cutImg']['h'],
					$center_cut = true,
					$left = 0,
					$top = 0,
					$resize = true,
					$option
				);

				$sql = "
					INSERT INTO tb_ecom_file ( tb_name, img_path, file_path, file_ref_id, file_name, file_description, file_type, file_free_download, file_ordering, file_limit, file_access, file_time_limit )
					SELECT
						'" . $k_tb_name . "' as tb_name,
						'" . $img_path . "' as img_path,
						'e2.png' as file_path,
						NULL as file_ref_id,
						'" . $file_name . "' as file_name,
						NULL as file_description,
						'product' as file_type,
						0 as file_free_download,
						0 as file_ordering,
						0 as file_limit,
						'all' as file_access,
						0 as file_time_limit
				";

				$this->dao->execDatas($sql);
			}
		}


		$sql = "
			SELECT
				*
			FROM tb_ecom_file
			WHERE tb_name = '" . $k_tb_name . "'
			AND file_ref_id = " . $parent_id . "

		";

		$haveTitleFile = false;
		foreach ($this->dao->fetchAll($sql) as $ka => $va) {

			if ($va->show_at_title == 1) {

				$haveTitleFile = true;
			}
		}

		if ($haveTitleFile == false && !empty($va)) {

			$sql = "
				UPDATE tb_ecom_file
				SET show_at_title = 1
				WHERE id = " . $va->id . "
			";

			$this->dao->execDatas($sql);
		}

		$datas['redirect'] = front_link($params['id'], 'formProduct/' . $parent_id . '');
		$datas['csrfHash'] = get_token('val');
		echo json_encode($datas);
	}

	//
	// Run action add, edit data
	public function action($action_type, $pri_key = NULL, $params = array())
	{

		$this->request = service('request');

		$k_tb_name = $this->config->tb_main;

		$upload_folders = 'upload/' . $k_tb_name . '';

		$keep = array();
		foreach (explode('/', $upload_folders) as $ke => $ve) {

			$keep[] = $ve;

			if (!is_dir(implode('/', $keep))) {
				@mkdir(implode('/', $keep));
			}
		}


		$showColumns = $this->dao->showColumns($k_tb_name);

		$model = 'Img_model';
		$use_package = "App\\Models\\$model";


		$MakeImage = new $use_package();

		//$MakeImage = new MakeImage();

		$data = array();

		$getData = array();



		$keepStrPercent = array();
		$data['to_db'][$k_tb_name] = array();
		if (!empty($pri_key) && !empty($getData)) {

			$data['to_db'][$k_tb_name] = convertObJectToArray($getData);
		}

		$params['myRequest'] = array();

		$params['myRequest'] = $_REQUEST;

		if ($params['id'] == 126) {





			unset($_REQUEST['views']);
		} else {
		}



		foreach ($_REQUEST as $kr => $vr) {

			$data['to_db'][$k_tb_name][$kr] = htmlspecialchars(trim($vr));
			//$data['to_db'][$k_tb_name][$kr] = $vr;
			//;
		}

		foreach ($this->config->columns as $ka => $va) {

			$va = convertObJectToArray($va);
			if (in_array($va['inputformat'], array('time'))) {

				if (!empty($data['to_db'][$k_tb_name][$ka])) {

					$ex = explode(' ', $data['to_db'][$k_tb_name][$ka]);
					$ex_date = array();

					if (is_numeric(strpos($ex[0], '-'))) {

						$ex_date = explode('-', $ex[0]);




						if (isset($ex_date[1], $ex_date[0], $ex_date[2]) && checkdate($ex_date[1], $ex_date[0],  $ex_date[2])) {

							$data['to_db'][$k_tb_name][$ka] = gettime_($ex_date[2] . '-' . $ex_date[1] . '-' . $ex_date[0], 5) . ' ' . $ex[1];
						}
					}
				}
			} else if (in_array($va['inputformat'], array('money', 'number'))) {

				$data['to_db'][$k_tb_name][$ka] = isset($data['to_db'][$k_tb_name][$ka]) ? removeMoneyComma($data['to_db'][$k_tb_name][$ka]) : NULL;
			} else if ($va['inputformat'] == 'date') {

				if (!empty($data['to_db'][$k_tb_name][$ka])) {

					$ex_date = array();
					if (is_numeric(strpos($data['to_db'][$k_tb_name][$ka], '/'))) {

						$ex_date = explode('/', $data['to_db'][$k_tb_name][$ka]);


						if (isset($ex_date[1], $ex_date[0], $ex_date[2]) && checkdate($ex_date[1], $ex_date[0],  $ex_date[2])) {

							$data['to_db'][$k_tb_name][$ka] = gettime_($ex_date[2] . '-' . $ex_date[1] . '-' . $ex_date[0], 5);
						}
					}
				}
			}
		}


		for ($l = 1; $l <= 20; ++$l) {

			$stop_loop = true;

			//
			//
			foreach ($this->config->columns as $ka => $va) {


				$va = convertObJectToArray($va);

				$data['to_db'][$k_tb_name][$ka] = isset($data['to_db'][$k_tb_name][$ka]) ? trim($data['to_db'][$k_tb_name][$ka]) : NULL;


				if (in_array($va['inputformat'], array('help')) && in_array($va['helpdetail']->type, array('multi_check', 'help_full'))) {

					if (empty($data['to_db'][$k_tb_name][$ka])) {

						$data['to_db'][$k_tb_name][$ka] = 0;
					}
				}

				if (isset($va['auto']) && $va['auto'] == true && !empty($this->parent_id)) {

					$data['to_db'][$k_tb_name][$ka] = $this->parent_id;
				} else if ($va['fix_val'] != '') {

					$data['to_db'][$k_tb_name][$ka] = $va['fix_val'];
				} else if (!empty($va['forum']) || (!empty($va['on_update']) && $action_type == 'edit') || (!empty($va['on_insert']) && $action_type == 'add')) {

					if ($action_type == 'edit') {

						$json = json_decode($va['on_update']);
					} else if ($action_type == 'add') {

						$json = json_decode($va['on_insert']);
					}

					if (empty($json->allow_key) || ($_REQUEST[$ka] == '' && !is_numeric($_REQUEST[$ka]))) {


						if (!isset($sqlStore[$ka])) {


							$sqlStore[$ka] = genJsonSql_($json, $getData, $this->config, $this->main_data_before);
						}


						$replaces = array();
						foreach ($data['to_db'][$k_tb_name] as $kd => $vd) {

							$replaces['[' . $kd . ']'] = "'" . $vd . "'";
						}

						$sqlStore[$ka] = str_replace(array_keys($replaces), $replaces, $sqlStore[$ka]);

						$t = $this->dao->fetch($sqlStore[$ka]);

						if (!$t) {

							$stop_loop = false;
						} else {

							$data['to_db'][$k_tb_name][$ka] = $t->t;
						}
					}
				} else if ($va['inputformat'] == 'date') {

					if (!empty($data['to_db'][$k_tb_name][$ka])) {

						$ex_date = array();
						if (is_numeric(strpos($data['to_db'][$k_tb_name][$ka], '/'))) {

							$ex_date = explode('/', $data['to_db'][$k_tb_name][$ka]);


							if (isset($ex_date[1], $ex_date[0], $ex_date[2]) && checkdate($ex_date[1], $ex_date[0],  $ex_date[2])) {

								$data['to_db'][$k_tb_name][$ka] = gettime_($ex_date[2] . '-' . $ex_date[1] . '-' . $ex_date[0], 5);
							} else {

								$data['field'][$ka] = 'กรุณาระบุวันที่ให้ถูกต้อง';
							}
						} else if (is_numeric(strpos($data['to_db'][$k_tb_name][$ka], '-'))) {

							$ex_date = explode('-', $data['to_db'][$k_tb_name][$ka]);


							if (isset($ex_date[1], $ex_date[0], $ex_date[2]) && checkdate($ex_date[1], $ex_date[2],  $ex_date[0])) {
							} else {


								$data['field'][$ka] = 'กรุณาระบุวันที่ให้ถูกต้อง';
							}
						}
					}
				} else if ($va['inputformat'] == 'email') {

					if (!empty($data['to_db'][$k_tb_name][$ka]) && !validEmail($data['to_db'][$k_tb_name][$ka])) {

						$data['field'][$ka] = 'อีเมล์ไม่ถูกต้อง';
					}
				} else if ($va['inputformat'] == 'password') {


					if ($action_type == 'edit' && $data['to_db'][$k_tb_name][$ka] == '') {

						unset($data['to_db'][$k_tb_name][$ka]);
					} else {



						if (empty($data['to_db'][$k_tb_name][$ka])) {

							$data['field'][$ka] = 'กรุณากรอกรหัสผ่าน';
						} else {

							if ($data['to_db'][$k_tb_name][$ka] != $data['to_db'][$k_tb_name][$ka . '-confirm']) {

								$data['field'][$ka . '-confirm'] = 'รหัสยืนยันไม่ตรงกับรหัสผ่าน';
							} else {

								if (strlen($data['to_db'][$k_tb_name][$ka]) < 8) {



									$data['field'][$ka] = 'กรุณาใส่พาสเวิร์ดขั้นต่ำ 8 ตัวอักษร';
								} else {

									if (preg_match('@[a-z]@', $data['to_db'][$k_tb_name][$ka])) {


										$data['to_db'][$k_tb_name][$ka] = password_hash($data['to_db'][$k_tb_name][$ka], PASSWORD_DEFAULT);
									} else {

										$data['field'][$ka] = 'รหัสยืนยันต้องมีอักษร a -  z อย่างน้อย 1 ตัวอักษร';
									}
								}
							}
						}
					}
				} else if ($va['inputformat'] == 'csv') {

					if (isset($done[$ka])) {

						continue;
					}

					//MAXFileSize
					$allow_extensions = explode(',', str_replace(' ', '', $va['allow_extensions']));

					if (!empty($_FILES[$ka]['tmp_name'])) {

						$extension = getExtension($_FILES[$ka]['name']);

						if (!empty($va['allow_extensions']) && !in_array($extension, $allow_extensions)) {

							$data['field'][$ka] = 'อนุญาติไฟล์ประเภท ' . implode(', ', $allow_extensions);
						} else {


							$file_name = explode('.', $_FILES[$ka]['name']);

							if (!empty($pri_key)) {

								$file_id = $pri_key;
							} else {

								$sql = "
										SELECT
											MAX( " . $this->config->pri_key . " ) + 1 as t
										FROM " . $k_tb_name;

								$file_id = $this->dao->fetch($sql)->t;
							}



							$img_name = $upload_folders . '/' . md5($ka . '-' . $file_id) . '.' . $file_name[count($file_name) - 1];

							$file_get_contents = file_get_contents($_FILES[$ka]['tmp_name']);

							file_put_contents($img_name, $file_get_contents);

							$data['to_db'][$k_tb_name][$ka] = $img_name;
						}
					} else {
						unset($data['to_db'][$k_tb_name][$ka]);
					}

					$done[$ka] = 1;
				}


				if ((isset($va[0]) && $va[0] == 1) && empty($data['to_db'][$k_tb_name][$ka])) {

					if (in_array($va['inputformat'], array('money', 'number'))) {

						$data['field'][$ka] = 'กรุณากรอกข้อมูล และจำนวน > 0';
					} else {
						$data['field'][$ka] = 'กรุณากรอกข้อมูลนี้  ';
					}
				}

				//
				// Check duplicate field
				if (isset($va[1]) && $va[1] == 1 && !empty($data['to_db'][$k_tb_name][$ka])) {

					$sql_count_duplicate = "
						SELECT
							count(*) as t
						FROM " . $this->config->database . "" . $k_tb_name . "
						[cond]
					";

					$filters = array();
					if (in_array('admin_company_id', $showColumns)) {

						//$filters[] = "admin_company_id = ". $_SESSION['user']->company_id ."";
					}

					if ($action_type != 'add') {

						$filters[] = $ka . " = '" . addslashes($data['to_db'][$k_tb_name][$ka]) . "' AND " . $this->config->pri_key . " != '" . $pri_key . "'
						";
					} else {

						$filters[] = $ka . " = '" . addslashes($data['to_db'][$k_tb_name][$ka]) . "'";
					}

					$sql_count_duplicate = genCond($sql_count_duplicate, $filters, $condTxt = "WHERE");

					$count_duplicate = $this->dao->fetch($sql_count_duplicate)->t;

					if ($count_duplicate > 0) {

						if (!empty($va['onDruplicate'])) {

							$json = json_decode($va['onDruplicate']);

							$data['field'][$ka] = $json->message;
						} else {

							$data['field'][$ka] = 'ข้อมูลนี้ไม่สามารถซ้ำได้';
						}
					}
				}


				if (!empty($va['input_type'])) {


					$json = json_decode($va['input_type']);

					if (in_array($json->type, array('text'))) {


						$param['youtubelink'] = $data['to_db'][$k_tb_name][$ka];

						$data['to_db'][$k_tb_name][$ka] = embedvideo($param);

						// exit;

						if ($data['to_db'][$k_tb_name][$ka] == false) {
							$data['field'][$ka] = 'ลิ้งนี้ไม่สามารถแสดงภาพวีดีโอได้กรุณากรอกใหม่';
						} else {
						}
						// JAPAN UPLOAD
					}
					// else if ( in_array( $json->type, array( 'dropify' ) ) ) {					

					// 	$allow_extensions = array( 'mp4', 'mp3', 'mp4' );

					// 	$file = $this->request->getFile('upload_video');
					// 	if( !empty( $file ) ){
					// 		$getName = $file->getName();						
					// 		$extension = getExtension($getName);

					// 		if(!$file->isValid() && $file->hasMoved()){

					// 			$data['field'][$ka] = $file->getErrorString(); //Debug BY CI4

					// 		}
					// 		else {
					// 			$fileSize = $file->getSizeByUnit('mb');



					// 			if (in_array(strtolower($extension), $allow_extensions) && $fileSize <= $json->maxSize ){
					// 				$newName = $file->getRandomName();

					// 				$img_path = 'uploads/tb_posts/' . $newName;
					// 				$file->move( 'upload/tb_vdos/', $newName );					

					// 				$data['to_db'][$k_tb_name][$ka] = 'upload/tb_vdos/'. $newName;
					// 			}else{
					// 				$data['field'][$ka] = 'นามสกุลไม่ถูกต้อง หรือ ขนาดไฟล์ต้องน้อยกว่า 1024 MB';

					// 			}

					// 		}


					// 	}else{

					// 		if( !empty( $va[0] ) ) {
					// 			$data['field'][$ka] = 'กรุณาเลือกวิดีโอที่ท่านต้องการ';
					// 		}
					// 		else {
					// 			unset( $data['to_db'][$k_tb_name][$ka] );
					// 			//$data['field'][$ka] = 'กรุณาเลือกวิดีโอที่ท่านต้องการ';
					// 		}

					// 	}



					// }
					else if (in_array($json->type, array('dropifys'))) {

						$allow_extensions = array('jpg', 'png', 'jpeg');

						$file = $this->request->getFile('img');

						if (!empty($file)) {
							$getName = $file->getName();
							$extension = getExtension($getName);

							if (!$file->isValid() && $file->hasMoved()) {

								$data['field'][$ka] = $file->getErrorString(); //Debug BY CI4

							} else {
								$fileSize = $file->getSizeByUnit('mb');



								if (in_array(strtolower($extension), $allow_extensions)) {
									$newName = $file->getRandomName();

									// $img_path = 'uploads/tb_posts/' . $newName;
									$file->move('upload/tb_vdos/', $newName);

									$data['to_db'][$k_tb_name][$ka] = 'upload/tb_vdos/' . $newName;
								} else {
									$data['field'][$ka] = 'นามสกุลไม่ถูกต้อง';
								}
							}
						} else {

							if (!empty($va[0])) {
								$data['field'][$ka] = 'กรุณาเลือกรูปภาพที่ท่านต้องการ';
							} else {
								unset($data['to_db'][$k_tb_name][$ka]);
								//$data['field'][$ka] = 'กรุณาเลือกวิดีโอที่ท่านต้องการ';
							}
						}
					} else if (in_array($json->type, array('CoverNews'))) {

						$allow_extensions = array('jpg', 'png', 'jpeg');

						$file = $this->request->getFile('img');

						if (!empty($file)) {
							$getName = $file->getName();
							$extension = getExtension($getName);

							if (!$file->isValid() && $file->hasMoved()) {

								$data['field'][$ka] = $file->getErrorString(); //Debug BY CI4

							} else {
								$fileSize = $file->getSizeByUnit('mb');



								if (in_array(strtolower($extension), $allow_extensions)) {
									$newName = $file->getRandomName();

									// $img_path = 'uploads/tb_posts/' . $newName;
									$file->move($upload_folders, $newName);

									$imgs = $upload_folders . '/' . $newName;

									$data['to_db'][$k_tb_name][$ka] = $imgs;
								} else {
									$data['field'][$ka] = 'นามสกุลไม่ถูกต้อง';
								}
							}
						} else {

							if (!empty($va[0])) {
								$data['field'][$ka] = 'กรุณาเลือกรูปภาพที่ท่านต้องการ';
							} else {
								unset($data['to_db'][$k_tb_name][$ka]);
								//$data['field'][$ka] = 'กรุณาเลือกวิดีโอที่ท่านต้องการ';
							}
						}
					} else if (in_array($json->type, array('hashtag'))) {

						if (isset($_REQUEST[$ka])) {

							$text = $_REQUEST[$ka];

							$text = str_replace('#', ' ', $text);
							$text = trim($text);


							$keep = array();

							foreach (explode(' ', $text) as $ke => $ve) {

								if (empty($ve)) {
									continue;
								}

								if ($ve == '#') {
									continue;
								}


								if (strpos($ve, '#') === FALSE) {



									$keep[] = '#' . $ve;
								} else if (strpos($ve, '#') == 0) {


									$keep[] = $ve;
								}
							}

							$data['to_db'][$k_tb_name][$ka] = implode(' ', $keep);

							$data['to_db'][$k_tb_name][$ka] = htmlspecialchars($data['to_db'][$k_tb_name][$ka]);
						} else {

							$data['to_db'][$k_tb_name][$ka] = NULL;
						}
					} else if ($json->type == 'checkbox') {


						if (isset($_REQUEST[$ka]))
							$data['to_db'][$k_tb_name][$ka] = $_REQUEST[$ka];
						else
							$data['to_db'][$k_tb_name][$ka] = NULL;
					} else if ($json->type == 'file') {

						if (!empty($_FILES[$ka]['tmp_name'])) {

							$allow_extensions = array('jpg', 'jpeg', 'png');

							$extension = getExtension($_FILES[$ka]['name']);

							if (in_array(strtolower($extension), $allow_extensions)) {



								$file_name = explode('.', $_FILES[$ka]['name']);

								if (!empty($pri_key)) {

									$file_id = $pri_key;
								} else {

									$sql = "
										SELECT
											MAX( " . $this->config->pri_key . " ) + 1 as t
										FROM " . $k_tb_name;

									$file_id = $this->dao->fetch($sql)->t;
								}



								$img_name = $upload_folders . '/' . md5($ka . '-' . $file_id) . '.' . $file_name[count($file_name) - 1];


								$MakeImage->genImg(
									$_FILES[$ka]['tmp_name'],
									$img_name,
									$new_width = $json->new_width,
									$new_height = $json->new_height,
									$center_cut = $json->center_cut,
									$left = 0,
									$top = 0,
									$resize = $json->resize,
									$json
								);



								$data['to_db'][$k_tb_name][$ka] = $img_name;
							} else {

								$data['field'][$ka] = 'อนุญาติไฟล์ประเภท ' . implode(', ', $allow_extensions);
							}
						} else {

							if ($action_type == 'edit' && !empty($getData->$ka)) {
								unset($data['field'][$ka]);
							}

							unset($data['to_db'][$k_tb_name][$ka]);
						}
					} else if (in_array($json->type, array('time2'))) {
						// var_dump('adad');exit;
						if (!empty($data['to_db'][$k_tb_name][$ka])) {

							$ex = explode(' ', $data['to_db'][$k_tb_name][$ka]);
							$ex_date = array();

							if (is_numeric(strpos($ex[0], '-'))) {

								$ex_date = explode('-', $ex[0]);




								if (isset($ex_date[1], $ex_date[0], $ex_date[2]) && checkdate($ex_date[1], $ex_date[0],  $ex_date[2])) {

									$data['to_db'][$k_tb_name][$ka] = gettime_($ex_date[2] . '-' . $ex_date[1] . '-' . $ex_date[0], 5) . ' ' . $ex[1];
								}
							}
						}
					}
				}
			}

			if ($stop_loop == true)
				break;
		}



		if ($action_type == 'delete') {
			unset($data['field']);
		}

		if (!empty($data['field'])) {


			$data['success'] = 0;

			echo json_encode($data);

			exit();
		}

		///require_once  'require_valid.php';


		if (!empty($this->config->before_action)) {

			$before_action = json_decode($this->config->before_action);

			if (is_object($before_action)) {

				foreach ($before_action as $ka => $va) {

					//
					//Default param
					$param = array(
						'data' => $data,
						'k_tb_name' => $k_tb_name,
						'main_id' => $_REQUEST['pri_key'],
						'action_type' => $action_type,
						'pri_key' => $_REQUEST['pri_key'],
						'current_config' => $this->config,
					);

					$param['parent_id'] = NULL;
					if (!empty($_REQUEST['main_id'])) {
						$param['parent_id'] = $_REQUEST['main_id'];
					} else {
						$param['parent_id'] = $_REQUEST['pri_key'];
					}

					if (isset($this->main_data_before)) {
						$param['main_data_before'] = $this->main_data_before;
					}

					if (!empty($getData)) {
						$param['beforeUpdate'] = $getData;
					}


					//
					// Optional param from config
					foreach ($va as $kb => $vb) {
						$param[$kb] = $vb;
					}


					if (isset($param['close'])) {

						continue;
					}


					$data = call_user_func($ka, $param);

					if (!empty($data['field'])) {

						foreach ($data['field'] as $ka => $va) {

							if (!in_array($ka, $showColumns))
								unset($data['field'][$ka]);
						}

						if (!empty($data['field'])) {


							$data['success'] = 0;
							echo json_encode($data);
							exit();
						}
					}
				}
			}
		}

		$saveDatas = array();



		if (!empty($data['to_db'][$k_tb_name])) {

			$data['to_db'][$k_tb_name]['user_id'] = $_SESSION['user_id'];
			foreach ($data['to_db'][$k_tb_name] as $ka => $va) {

				if (in_array($ka, $showColumns)) {

					$saveDatas[$ka] = $va;
				}
			}

			//arr($saveDatas);

			if ($action_type == 'delete') {

				if (!$this->dao->delete_($this->config->tb_main, array($this->config->pri_key => $pri_key))) {

					$data['message'] = 'ไม่สามารถข้อมูลนี้ได้เนืองจากความเชื่อมโยงของเอกสาร';
					$data['field']['test'] = $data['message'];


					$data['success'] = 0;
					echo json_encode($data);
					exit();
				}


				$data['success'] = 1;
				$data['redirect'] = front_link($params['id']);
				echo json_encode($data);
				exit();
			} else if ($action_type == 'add') {

				$pri_key = $this->dao->insert_($k_tb_name, $saveDatas);

				if (empty($pri_key)) {


					$data['message'] = 'มีการกรอกข้อมูลซ้ำ';
					$data['field']['test'] = $data['message'];

					$data['success'] = 0;
					echo json_encode($data);
					exit();
				}


				$data['parent_id'] = $pri_key;
				$data['success'] = 1;
			} else if ($action_type == 'edit') {


				if (!in_array('time_update', array_keys($this->config->columns))) {
					unset($saveDatas['time_update']);
				}

				$cond = $this->config->pri_key . " = '" . $pri_key . "'";

				if (!$this->dao->update_($k_tb_name, $saveDatas, $cond)) {

					$data['message'] = 'มีการกรอกข้อมูลซ้ำ';
					$data['field']['test'] = $data['message'];


					$data['success'] = 0;
					echo json_encode($data);
					exit();
				}

				$data['success'] = 1;
				$data['parent_id'] = $pri_key;
			}


			$sql = "
				UPDATE tb_ecom_file 
				SET file_ref_id = " . $data['parent_id'] . "
				 	
				WHERE (  file_ref_id IS NULL OR file_ref_id = 0 )
				AND tb_name = '" . $k_tb_name . "'
					 
			
			";

			$this->dao->execDatas($sql);

			if (isset($data['to_db'][$k_tb_name]['status_id'])  && $data['to_db'][$k_tb_name]['status_id'] == 1 && false) {
				$data['redirect'] = front_link($params['id']);
			} else {

				$data['redirect'] = front_link($params['id'], 'formProduct/' . $data['parent_id'] . '');
			}

			echo json_encode($data);


			if (!empty($this->config->after_action)) {

				$after_action = json_decode($this->config->after_action);

				if (is_object($after_action)) {

					foreach ($after_action as $ka => $va) {


						//
						//Default param
						$param = array(
							'datas' => $data['to_db'][$k_tb_name],
							'parent_id' => $data['parent_id'],
							'myRequest' => $params['myRequest']
						);

						if (isset($param['close'])) {
							continue;
						}

						call_user_func($ka, $param);
					}
				}
			}

			exit();
		}
	}

	//
	//
	function __construct($getView = array())
	{

		//echo getSkipId( 'admin_model_config_columns', 'config_columns_id', $skip = array() );exit;

		$this->dao = new Db_model();
		$this->form_mode = 'post';
		$this->textarea_mode = 'ckeditor';


		$this->getView = $getView;
	}


	//
	//
	function load_rows($params = array())
	{

		$config = getConfig_($params['config_id']);

		$sql = "SHOW columns FROM {$config->tb_main} WHERE Field='order_number';";

		$row = $this->dao->fetch($sql);

		$has_order_number = $row != null;

		$request = \Config\Services::request();

		$GET = $request->getGet();
		$ids = @$GET['ids'];
		$orders = @$GET['orders'];

		if ($has_order_number && $ids && (count($ids) > 0) && (count($ids) == count($orders))) {
			foreach ($ids as $key => $id) {
				/*
				$sql = "UPDATE {$config->tb_main} SET order_number='".intval($orders[$key])."' WHERE id='".intval($id)."';";
				$this->dao->execDatas( $sql );*/
			}
		}

		$tds = array();
		$test = json_decode('{}');
		$test->data = 'order_number';
		$test->name = 'order_number';
		$test->title = 'ลำดับ';
		$test->width = '50px';
		$test->class = 'C dfffddfdf';
		$test->orderable = 0;
		$tds[] = $test;
		$disabledSort[] = count($tds) - 1;


		foreach (json_decode($config->multi_tables) as $kc => $column) {

			if (isset($column->type)) {

				if ($column->type == 'checkbox') {
					//continue;
					$test = json_decode('{}');
					$test->data = $kc;
					$test->name = $kc;
					$test->title = '<input type="checkbox" class="checkall" />';
					$test->width = '100px';
					$test->class = 'C';
					$tds[] = $test;
					$disabledSort[] = count($tds) - 1;
				} else {

					$test = json_decode('{}');
					$test->data = $kc;
					$test->name = $kc;
					$test->title = 'การจัดการ';
					$test->width = '150px';
					$test->class = 'C';
					$tds[] = $test;
					$disabledSort[] = count($tds) - 1;
				}
			} else {

				$dsasa[count($tds)] = $kc;

				$test = '{}';
				$test = json_decode($test);
				$test->data = $kc;
				$test->name = $kc;

				if (isset($column->label)) {

					$test->title = $column->label;
				} else if (isset($config->columns[$kc])) {

					$test->title = $config->columns[$kc]->label;
				} else {
					$test->title = $kc;
				}

				$test->width = isset($column->w) ? $column->w : '150px';
				$test->class = 'gogo ';
				$test->class .=  isset($column->a) ? $column->a : 'C';
				$tds[] = $test;
			}
		}

		/*
		$h_id = new \stdClass;
		$h_id->data = 'id';
		$h_id->name = 'id';
		$h_id->title = 'ID';
		$h_id->width = '50px';
		$h_id->class = 'C';
		$tds[] = $h_id;
		*/


		if (!empty($_REQUEST['ajax'])) {

			// var_dump($_REQUEST);
			// exit;
			//
			//load ajax datas
			$params['length'] = 10;
			$params['start'] = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$fromDate = @$_REQUEST['from_date'];
			$toDate = @$_REQUEST['to_date'];
			$sql = $config->main_sql;
			$filters = array();

			$keep = array();
			if (!empty($_REQUEST['search']['value'])) {

				foreach (json_decode($config->multi_tables) as $km => $vm) {

					if (isset($vm->type)) {
						continue;
					}

					if (preg_match('/"/', $_REQUEST['search']['value'])) {
						$texts = html_entity_decode($_REQUEST['search']['value']);
						$keep[] = "" . $km . " LIKE '%" . addslashes($texts) . "%'";
					} else if (preg_match("/'/u", $_REQUEST['search']['value'])) {
						$keep[] = "" . $km . " LIKE '%" . addslashes($_REQUEST['search']['value']) . "%'";
					} else {
						$keep[] = "" . $km . " LIKE '%" . str_replace(' ', " ", ($_REQUEST['search']['value'])) . "%'";
					}
				}
			}

			if (!empty($keep)) {
				$filters['HAVING'][] = '( ' .  implode(' OR ', $keep) . ' )';
			}


			if (!empty($config->more_filter_sql)) {

				foreach (json_decode($config->more_filter_sql) as $km => $vm) {

					$filters[$km][] = $vm->sql;
				}
			}


			//	$sort = 'ORDER BY ' . ($has_order_number?'order_number':$config->pri_key) . ' DESC';

			$sort = 'ORDER BY ' . $config->pri_key . ' DESC';


			if (isset($_REQUEST['order']) &&   isset($dsasa[$_REQUEST['order'][0]['column']])) {
				$sort = 'ORDER BY ' . $dsasa[$_REQUEST['order'][0]['column']] . ' ' . $_REQUEST['order'][0]['dir'];
			} else if (!empty($config->main_sql_sort)) {

				$sort = $config->main_sql_sort;
			}

			$filters['sort'] = $sort;

			$daterange = '';
			if ($fromDate != '' && $toDate != '') {
				if ($fromDate == $toDate) {
					$daterange = "cast(n.release_time as Date) = '" . $fromDate . "'";
				} else {
					$daterange = "cast(n.release_time as Date) BETWEEN '" . $fromDate . "' AND '" . $toDate . "'";
				}
			} else if ($fromDate != '' && $toDate == '') {
				$daterange = "cast(n.release_time as Date) >= '" . $fromDate . "'";
			} else if ($fromDate == '' && $toDate != '') {
				$daterange = "cast(n.release_time as Date) <= '" . $toDate . "'";
			}

			if (@$daterange != '') {
				$filters['WHERE'][] = $daterange;
			}

			$sql = genCond_($sql, $filters, $sort);

			$total = count($this->dao->fetchAll($sql));

			$sql .= " LIMIT " . $params['start'] . "," . $params['length'];



			$rows = array();
			foreach ($this->dao->fetchAll($sql) as $ka => $row) {

				$tds = array();
				//if($has_order_number)
				//$tds['order_number'] = $row->order_number;
				//else

				$tds['order_number'] = $params['start'] + 1 + $ka;

				foreach (json_decode($config->multi_tables) as $kc => $vc) {

					if (isset($vc->type)) {

						if ($vc->type == 'checkbox') {
							//continue;
							$tds[$kc] = '<input type="checkbox" name="checkme[]" value="' . $row->id . '" />';
						} else {


							$option['edit'] = '<a style="margin-right: 10px;" href="' . front_link($params['id'], 'formProduct/' . $row->id . '') . '"><i class="fa fa-edit"></i></a>';

							$option['delete'] = '<button id="btnDelete" type="button" class="btn p-0" style="color:red;" data-link="' . $row->id . '"><i class="fa fa-trash"></i></button>';
							// $option['delete'] = '<button id="btnDelete" type="button" class="btn p-0" style="color:red;" data-link="'. front_link( $params['id'], 'deleteData/'. $row->id  .'' ) .'"><i class="fa fa-trash"></i></button>';

							$links = array();

							if (!empty($vc->page_id)) {

								$links[] = '<a style="margin-right: 10px;" target="blank_" href="' . front_link($vc->page_id, '/' . $row->id . '') . '"><i class="fa fa-search"></i></a>';
							}

							if (!isset($vc->buttons)) {
								$vc->buttons = array('edit', 'delete');
							}

							foreach ($vc->buttons as $kb => $vb) {
								$links[] = $option[$vb];
							}


							$tds[$kc] = implode('', $links);
						}
					} else {
						$val = NULL;

						if (isset($row->$kc)) {

							if (isset($config->columns[$kc])) {

								$val = getVal($row->$kc, $config->columns[$kc], $status = 'r', $row);
							} else {


								$val = $row->$kc;
							}
						}
						// var_dump($row);exit;
						if (isset($vc->link_edit)) {
							$tds[$kc] = '<a target="blank_" href="' . front_link($params['id'], 'formProduct/' . $row->id . '') . '">' . $val . '</a>';
						} else if (isset($vc->Japan_link_news)) {

							$tds[$kc] = '<a target="blank_" href="' . newsLink($row->id) . '">' . $val . '</a>';
						} else if (isset($vc->Japan_linkCat)) {

							$tds[$kc] = '<a target="blank_" href="' . front_link(4, $row->cat_id) . '">' . $val . '</a>';
						} else if (isset($vc->Japan_linkCatVideo)) {

							if ($params['id'] == 110) {
								$tds[$kc] = '<a target="blank_"  href="' . front_link(3, $row->cat_id) . '">' . $val . '</a>';
							} else if ($params['id'] == 113) {
								$tds[$kc] = '<a target="blank_" href="' . front_link(8, $row->cat_id) . '">' . $val . '</a>';
							} else if ($params['id'] == 107) {
								$tds[$kc] = '<a target="blank_" href="' . front_link(10, $row->cat_id) . '">' . $val . '</a>';
							}
						} else if ($params['id'] == 128) {
							$tds[$kc] = htmlspecialchars($val);
						} else {

							$tds[$kc] = $val;
						}
					}
				}

				//$tds['id'] = $row->id;
				$rows[] = $tds;
				// arr($tds[$kc]);exit;
			}



			$ajaxObj = json_decode('{}');
			$ajaxObj->data = $rows;
			$ajaxObj->recordsTotal = $total;
			$ajaxObj->recordsFiltered = $total;
			$ajaxObj->draw = isset($_REQUEST['draw']) ? $_REQUEST['draw'] : 1;
			echo json_encode($ajaxObj);

			exit;
		} else {

			//
			//view table
			$params['topButtons'] = '
				<div class="d-flex my-xl-auto right-content">
					<div class="pe-1  mb-xl-0">
						<a class="btn btn-primary" href="' . front_link($params['id'], 'formProduct') . '" role="button"><i class="fas fa-plus"></i> เพิ่ม</a>
					</div>
					<div class="pe-1  mb-xl-0">
						<button type="button" class="btn btn-danger"><i class="fas fa-trash-alt"></i> ลบ</button>
					</div>
					<div class="pe-1  mb-xl-0">
						<a href="' . front_link(312) . '" type="button" class="btn btn-success"><i class="fas fa-eye"></i> ดูแบบแคตตาล๊อก</a>
					</div>

					<div class="pe-1  mb-xl-0">
						<button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="true" aria-controls="collapseExample"><i class="fas fa-filter"></i> ตัวกรอง</button>
					</div>
				</div>
			';


			$buttons = json_encode(array("excel"));

			$buttons = json_encode(array("excel"));
			//$buttons = json_encode( array() );

			$disabledSort = json_encode($disabledSort);

			$onRowReorder = $has_order_number ? 'table.on( "row-reorder", function ( e, diff, edit ) {
							ids = [];
							orders = [];
							for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
								// get id row
								let id = diff[i].node.id;
								// get position
								console.log(diff[i].node);
								//console.log([diff.length, diff[i].newPosition]);
								let position = diff[i].newPosition+1;
								ids[i] = id;
								orders[i] = position;
							}
						});' : '';
			$reorderAssets = $has_order_number ? '<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.6/css/rowReorder.dataTables.min.css" />
				<script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>' : '';

			$pages = array(105, 109, 122, 127, 110, 113, 107, 114);
			$dateFillter = '';
			if (in_array($params['id'], $pages)) {
				$dateFillter = '
					<table cellspacing="5" cellpadding="5">
						<tbody>
							<tr>
								<td>จากวันที่:</td>
								<td><input type="date" id="min" name="min"></td>
								<td>ถึงวันที่:</td>
								<td><input type="date" id="max" name="max"></td>
							</tr>
						
						</tbody>
					</table>
				';
			}


			$params['datastable'] = '
				' . $dateFillter . '
				<table id="musion-datatable" class="table table-striped mg-b-0 text-md-nowrap">

					<thead></thead>
					<tbody></tbody>

				</table>

				<script src="table/jquery.dataTables.min.js"></script>
				<script src="table/dataTables.bootstrap5.js"></script>
				<script src="table/dataTables.buttons.min.js"></script>
				<script src="table/buttons.bootstrap5.min.js"></script>
				<script src="table/jszip.min.js"></script>
				<script src="table/buttons.html5.min.js"></script>
				<script src="table/buttons.print.min.js"></script>
				<script src="table/buttons.colVis.min.js"></script>
				<script src="table/pdfmake.min.js"></script>
				<script src="table/vfs_fonts.js"></script>

				' . $reorderAssets . '

				<script>
					var editor;
					var ids = [];
					var orders = [];
					$( function() {

						$( \'.multi-delete\' ).click( function() {
							
							let vals = $(\'[name="checkme[]"]:checked\').serialize()
							url = \'' . front_link($params['id'], 'deleteRows') . '\';
							
							order_status = \'\';
							Swal.fire({
								icon: "warning",
								title: \'คุณต้องการลบ ?\',
								text: "ข้อมูลนี้อาจมีการใช้งานร่วมกันกับข้อมูลอื่น เมื่อลบแแล้วข้อมูลที่เกี่ยวข้องจะถูกลบไปด้วย",
								showDenyButton: false,
								showCancelButton: true,
								confirmButtonText: \'ใช่, ต้องการลบ!\',
								cancelButtonText: \'ยกเลิก\',								
							}).then((result) => {

								if ( result.isConfirmed ) {
									
									action = url + order_status;

									$.ajax({
										url: url,
										type: "GET",
										data : vals,
										success: function (respon){
											data = JSON.parse(respon)
											
											if(data.success == true){
												Toast.fire({
													icon: \'success\',
													title: data.msg
												}).then((result) => {
													window.location.reload();
												})
											}else{
												Swal.fire({
													icon: \'error\',
													text: data.msg,
													timer: 3000,							
												})
												
											}

											// if(data.errors == false){
											// 	window.location.reload();
											// }else{
											// 	Swal.fire({
											// 		icon: \'error\',													
											// 		text: data.msg,													
											// 	  })
											// }
											
										}
									})
							

								} else if (result.isDenied) {
									Swal.fire(\'Changes are not saved\', \'\', \'info\')
								}
							});
							
							
							
							
							//$( \'.hidden_submit\' ).trigger( \'click\' );
						});

						let table = $( "#musion-datatable" ).DataTable({
							dom: \'Bfrtip\',
							processing: true,
							serverSide: true,
							rowReorder: true,
							//order: [[ 0, "desc" ]],
							createdRow: function(row, data, dataIndex) {
								$(row).attr("idx", data.order_number);
								$(row).attr("id", data.id);
							},
							"preDrawCallback": function (data) {
								return true;
							},
							ajax: {
								url:"' . front_link($params['id'], 'load_rows', array('ajax' => 1)) . '",
								type: "GET",
								data: function ( d ) {
									return $.extend( {}, d, {
										"ids": ids,
										"orders": orders,
										"from_date" : $(\'#min\').val(),
										"to_date" : $(\'#max\').val(),
									});
								},
								complete : function( completeHtmlPage ) {
									ids = orders = [];
									jQuery(\'img[data-bs-toggle="modal"]\').click(function(e){
										e.preventDefault();
										jQuery(\'#modal-title\').html(jQuery(e.target).attr(\'data-modal-title\'));
										let html = jQuery(e.target).clone();
										jQuery(\'#modal-body\').html(\'\');
										html.appendTo(\'#modal-body\');
									});


									//
									//
									$( \'.checkall\' ).click( function () {

										var obj = {};

										obj[\'all_checkbox\'] = $( \'#musion-datatable\' ).find( \'[name="checkme[]"]\' );

										if( $( this ).filter( \':checked\' ).val()) {

											var check = true;
										}

										else {
											var check = false;
										}


										obj.all_checkbox.attr( \'checked\', check );



									});
								}
							},

							columns:' . json_encode($tds) . ',
							columnDefs: [{orderable: false, targets: ' . $disabledSort . '}],
							rowReorder: true,
							scrollY: 500,
							paging: true,
							pagingType: "simple_numbers",
							lengthChange: false,
							buttons: ' . $buttons . ',
							responsive: true,
							select: true,
							language: {
								searchPlaceholder: "Search...",
								sSearch: "",
								lengthMenu: "_MENU_ ",
							},
						});
						table
							.buttons()
							.container()
							.appendTo( "#musion-datatable-wrapper .col-md-6:eq(0)");
						' . $onRowReorder . '

						$(\'#min, #max\').on(\'change\', function () {
							table.ajax.reload();
							//console.log(min,max)
							//table.draw();
						});
					});
				</script>

				

			';

			//arr( $config );exit;
			$params['addButton'] = '';
			if (empty($config->no_add)) {
				$params['addButton'] = '
				<a class="" href="' . front_link($params['id'], 'formProduct') . '"><button class="btn btn-primary m-2">เพิ่ม</button></a>
				<button class="btn btn-danger multi-delete">ลบ</button>
				';
			}






			return $params;
		}
	}

	//
	//
	function getTable($datas, $config = array(), $params = array())
	{

		//arr( $config->config_id );exit;
		$status = 'ready';
		///$status = 'edit';
		///	$status = 'add';
		$r = 0;
		foreach ($datas as $kg => $vg) {

			$tds = array();

			foreach ($config->columns as $kc => $vc) {

				if (empty($vc->show)) {
					continue;
				}


				$tds['edit'][] = '<td class="' . $vc->a . '">dsdfdfs</td>';
			}

			$vals = convertObJectToArray($vg);

			$gogo = $this->renderBlocks($config, $vals, 2,  'ready');


			$trs['ready'][$vg->id] = '<tr>

				<td class="">' . ($r + 1) . '</td>
				' . implode('', $gogo[1]) . '

				<td class="C">


					<a data-id="' . $vg->id . '" class="delete-row" title="ลบข้อมูล"><i class="fas fa-trash-alt"></i></a> &nbsp;&nbsp;

					<a data-id="' . $vg->id . '" class="edit-row" title="แก้ไขข้อมูล"><i class="fa fa-edit"></i></a>

				</td>
			</tr>';


			$gogo = $this->renderBlocks($config, $vals, 2, 'form');

			$trs['edit'][$vg->id] = '
				<tr>
					<td class="">' . ($r + 1) . '</td>
					' . implode('', $gogo[1]) . '

					<td class="C">


						<a class="confirm-row" title="บันทึกข้อมูล"><i class="fa fa-save"></i></a>

						<a data-id="' . $vg->id . '" class="cancel-row" title="ยกเลิก"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>

					</td>
				</tr>';

			++$r;
		}

		$tds = array();


		$tds['ready'][] = '<td class=""></td>';

		foreach ($config->columns as $kc => $vc) {

			if (empty($vc->show)) {
				continue;
			}

			$val = '';



			//$tds['add'][] = '<td class="'. $vc->a .'">ัััััััััััััััั</td>';
			$tds['ready'][] = '<td class="' . $vc->a . '"></td>';
		}



		$tds['ready'][] = '
			<td class="' . @$vc->a . '"><a class="add-new-row"><i class="fa fa-plus-circle"></i></a></td>
		';

		$trs['ready']['rrrrrrrrrrr'] = '<tr>' . implode('', $tds['ready']) . '</tr>';


		$gogo = $this->renderBlocks($config, array(), 2, 'form');
		$trs['add'] = '<tr>
			<td class=""></td>
			' . implode('', $gogo[1]) . '

			<td class="' . @$vc->a . '">
				<a data-id="rrrrrrrrrrr" class="cancel-row" title="ยกเลิก"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
				<a class="confirm-row"><i class="fa fa-save"></i></a>

			</td>
		</tr>';

		$trHead = array();
		$getTrOnStep = getTrOnStep(@$config->columns);
		$maxRow = count($getTrOnStep);
		if (is_array($getTrOnStep)) foreach ($getTrOnStep as $kl => $vl) {

			$ths = array();
			if ($kl == 0)
				$ths[] = '<th style="width: 5%;" rowspan="' . $maxRow . '">#</th>';

			foreach ($vl as $kt => $vt) {

				if (empty($vt['label']))
					continue;

				$w = 'width: ' . $vt['w'] . '%;';
				if ($vt['w'] == 0) {

					$w = 'width: auto;';
				}
				$ths[] = '<th style="' . $w . '" colspan="' . $vt['merg'] . '" rowspan="' . $vt['h'] . '">' . $vt['label'] . '</th>';
			}

			if ($kl == 0)
				$ths[] = '<th rowspan="' . $maxRow . '"></th>';

			$trHead[] = '<tr>' . implode('', $ths) . '</tr>';
		}

		return '<form method="post" action="' . front_link($params['id'], 'save/' . $params['parent_id'] . '/' . $config->config_id . '', $get = array(), $token = false) . '"  enctype="multipart/form-data" >

				' . $params['secret'] . '

				<input style="display: none" type="submit" value="hidden_submit" class="sub_hidden_submit">
				<input type="hidden" name="' . PriKey . '" value="' . $params['parent_id'] . '" />
				<input type="hidden" name="action_type" value="" />
				<table data-config="' . $config->config_id . '" class="bomb-form table key-buttons text-md-nowrap">' . implode('', $trHead) . '' . implode('', $trs[$status]) . '</table>
			</form>


			<script>trs[' . $config->config_id . '] = ' . json_encode($trs) . '</script>

		';
	}



	//
	//
	function renderBlocks($config, $vals = array(), $type = 1, $status = 'form')
	{

		$replace = array();

		foreach ($config->columns as $kc => $vc) {

			if (empty($vc->show)) {
				continue;
			}

			if (!empty($vc->default_val)) {

				$val = isset($vals[$kc]) ? $vals[$kc] : $vc->default_val;
			} else {
				$val = isset($vals[$kc]) ? $vals[$kc] : NULL;
			}

			//arr( $val );




			$label = $vc->label;

			$inputName = $kc;

			$spanRequire = '<span class="text-danger" data-name="' . $inputName . '" style="color: red;"></span>';
			if (!empty($vc->require))
				$spanRequire = '<span class="text-danger" data-name="' . $inputName . '" style="color: red;">*</span>';

			if ($vc->inputformat == 'password') {
				$spanRequire = '<span class="text-danger" data-name="' . $inputName . '" style="color: red;">*</span>';

				$spanRequire2 = '<span class="text-danger" data-name="' . $inputName . '-confirm" style="color: red;">*</span>';
			}

			if ($vc->inputformat == 'time') {

				$val = gettime_($val, 19);

				$replace['[' . $kc . ']'] = '
					<div class="row row-sm" mt-3="">
						<div class="input-group">
							<div class="input-group-text">
								<div class="input-group-text">
									<i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
								</div>
							</div><input value="' . $val . '" name="' . $inputName . '" class="form-control" id="datetimepicker2" type="text" placeholder="MM/DD/YYYY">
						</div>
					</div>
				';
			} else if ($vc->inputformat == 'password') {

				$replace['[' . $kc . ']'] = '<input class="form-control"  type="password" name="' . $inputName . '" placeholder="' . $vc->label . '">';

				$replace['[' . $kc . '-confirm]'] = '<input class="form-control" type="password" name="' . $inputName . '-confirm" placeholder="ยืนยัน' . $vc->label . '">';
			} else if ($status != 'form') {

				$replace['[' . $kc . ']'] = getVal($val, $vc, 'r');
			} else if (!empty($vc->input_type)) {

				$json_decode = json_decode($vc->input_type);


				if ($json_decode->type == 'timeup') {
					$label = '';
					$spanRequire = '';
					$replace['[' . $kc . ']'] = '
						<input type="hidden" value="' . date('Y-m-d H:i:s') . '" name="' . $inputName . '" class="form-control">							
					';
				} else
				if ($json_decode->type == 'time2') {

					$val = gettime_($val, 19);

					$replace['[' . $kc . ']'] = '
						<div class="row row-sm" mt-3="">
							<div class="input-group">
								<div class="input-group-text">
									<div class="input-group-text">
										<i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
									</div>
								</div><input value="' . $val . '" name="' . $inputName . '" class="form-control" id="datetimepicker3" type="text" placeholder="MM/DD/YYYY">
							</div>
						</div>
					';
				} else 

				if ($json_decode->type == 'CoverNews') {
					//value="'. $val .'"
					$d = array();

					$d[] = '
						<div class="dropify-container">						
							<input type="file" name="' . $inputName . '" class="dropify" data-default-file="' . $val . '" data-height="200" />

							
						</div>

						
					';

					$replace['[' . $kc . ']'] = '' . implode('', $d) . '';
				} else
				if ($json_decode->type == 'dropifys') {
					//value="'. $val .'"
					$d = array();

					$d[] = '
						<div class="dropify-container">						
							<input type="file" name="' . $inputName . '" class="dropify" data-default-file="' . $val . '" data-height="200" />

							
						</div>

						
					';

					// if( !empty( $val ) ) {
					// 	$d[] = '
					// 		<div class="container">

					// 				<video style="width: 100%; background-color: black;" height="448" controls="" >
					// 				<source src="'. $val .'" type="video/mp4">

					// 				Your browser does not support HTML video.
					// 				</video>

					// 	<div>
					// 	<input data-default-file="'. $val .'" class="form-control dropify" data-height="100"  type="file" name="'. $inputName .'" placeholder="'. $vc->label .'">
					// </div>
					// 		</div>
					// 	';
					// }

					$replace['[' . $kc . ']'] = '' . implode('', $d) . '';
				} else if ($json_decode->type == 'text') {

					$replace['[' . $kc . ']'] = '<input class="form-control" value="' . $val . '" type="text" name="' . $inputName . '" placeholder="' . $vc->label . '">';
				} else if ($json_decode->type == 'hashtag') {

					$val = htmlspecialchars_decode($val);
					$val = strip_tags($val, 'a');
					$replace['[' . $kc . ']'] = '<input class="form-control" value="' . $val . '" type="text" name="' . $inputName . '" placeholder="' . $vc->label . '">';
				} else if ($json_decode->type == 'toggle') {

					$on = '';
					if ($val == $json_decode->param->options->on) {
						$on = ' on';
					}

					$replace['[' . $kc . ']'] = '
						<div class="main-toggle-group-demo">
							<div class="main-toggle' . $on . '" style="">
								<span></span><input class="toggle-val" type="hidden" value="' . $val . '" name="' . $inputName . '" />
							</div>
						</div>

						<script>
						$( function() {

							options = ' . $vc->input_type . '
							$( \'.main-toggle\' ).click( function(){

								me = $( this );

								if( me.hasClass( \'on\' ) ) {
									val = options.param.options.off;
								}
								else {

									val = options.param.options.on;
								}

								me.find( \'.toggle-val\' ).val( val );
							});
						});
						</script>
					';
				} else if ($json_decode->type == 'comment') {

					if ($json_decode->ckeditor == true) {

						$replace['[' . $kc . ']'] = '<textarea style="" name="' . $inputName . '" class="form-control" rows="5">' . $val . '</textarea>

						';
					} else {

						$replace['[' . $kc . ']'] = '<textarea name="' . $inputName . '" class="form-control" rows="5">' . $val . '</textarea>';
					}
				} else if ($json_decode->type == 'select') {

					$filter = '';

					if (!empty($json_decode->filter)) {

						$cond = 'HAVING';

						if (!empty($json_decode->cond)) {
							$cond = 'WHERE';
						}

						$filter = $cond . " " . $json_decode->filter;
					}


					$sql = str_replace(array('%filter;'), array($filter), $json_decode->sql);


					$json_decode->sql = $sql;

					$sql = genJsonSql($json_decode, NULL, NULL, NULL);

					if (isset($json_decode->replaceSql)) {

						foreach ($json_decode->replaceSql as $kr => $vr) {

							$gg[$kr] = $vr;
						}

						foreach ($res as $kk => $vk) {
							$gg[$kk] = $vk;
						}

						$sql = genCond_($sql, $gg);
					}

					$pri_key = explode('.', $json_decode->pri_key);

					$pri_key = $pri_key[count($pri_key) - 1];

					$options = array();

					$name = 0;

					if (empty($vc->$name)) {

						$options[] = '<option value="0">ไม่ระบุ</option>';
					}

					foreach ($this->dao->fetchAll($sql) as $kb => $vb) {
						$desc = getDesc($vb, $json_decode->desc);

						$options[] = '<option ' . select($vb->$pri_key, $val) . ' value="' . $vb->$pri_key . '">' . $desc . '</option>';
					}

					if (isset($json_decode->multiple)) {
						$replace['[' . $kc . ']'] = '<select multiple="multiple" class="testselect2" name="' . $inputName . '">' . implode('', $options) . '</select>';
					} else {

						$replace['[' . $kc . ']'] = '<select class="form-control select2-no-search" name="' . $inputName . '">' . implode('', $options) . '</select>';
					}
				} else {
					$replace['[' . $kc . ']'] = '<input type="file" name="' . $inputName . '" class="dropify" data-height="200" data-default-file="' . $val . '"/>';
				}
			} else if ($vc->inputformat == 'number') {
				$replace['[' . $kc . ']'] = '<input class="form-control" value="' . $val . '" type="number" name="' . $inputName . '" placeholder="' . $vc->label . '">';
			} else if ($vc->inputformat == 'date') {
				$replace['[' . $kc . ']'] = '<input class="form-control" value="' . $val . '" type="date" name="' . $inputName . '" placeholder="' . $vc->label . '">';
			} else if (!empty($vc->on_update) || !empty($vc->on_insert)) {

				$replace['[' . $kc . ']'] = getVal($val, $vc, 'r') . '';
			} else {

				$replace['[' . $kc . ']'] = '<input class="form-control" value="' . $val . '" type="text" name="' . $inputName . '" placeholder="' . $vc->label . '">';
			}

			if ($vc->inputformat == 'password') {


				if ($type == 1) {

					$gogo[$vc->position][$kc] = '
						<div class="' . $vc->div_class . '">
							<div class="form-group">
								<span>' . $label . '</span> 
								' . $spanRequire . '' . $replace['[' . $kc . ']'] . '
								
							</div>
							
						</div>
						
						<div class="' . $vc->div_class . '">
							<div class="form-group">
								<span>ยืนยัน' . $label . '</span> 
								' . $spanRequire2 . '' . $replace['[' . $kc . '-confirm]'] . '
								
							</div>
							
						</div>
					';
				} else {

					$gogo[1][$kc] = '<td>' . $replace['[' . $kc . ']'] . '</td>';
				}
			} else {

				if ($type == 1) {

					$gogo[$vc->position][$kc] = '<div class="' . $vc->div_class . '"><div class="form-group"><span>' . $label . '</span> ' . $spanRequire . '' . $replace['[' . $kc . ']'] . '</div></div>';
				} else {

					$gogo[1][$kc] = '<td>' . $replace['[' . $kc . ']'] . '</td>';
				}
			}
		}


		return $gogo;
	}



	//
	//
	function save($params = array())
	{

		$request = service('request');

		if (!empty($_REQUEST['action_type'])) {

			$this->config = getConfig_($params['config_id']);

			$this->main_data_before = json_decode('{}');

			$sql = "

				SELECT
					* FROM
				" . $this->config->tb_main . "
				WHERE " . $this->config->pri_key . " = " . $params['parent_id'] . "

			";

			foreach ($this->dao->fetchAll($sql) as $ka => $va) {

				$this->main_data_before = $va;
			}

			if (!empty($params['sub_config_id'])) {

				$this->config = getConfig_($params['sub_config_id']);


				$action_type = 'delete';

				$this->action($action_type, $_REQUEST['pri_key'], $params);
			} else {

				$action_type = 'delete';


				$this->action($action_type, $params['parent_id'], $params);
			}
		} else {

			$this->config = getConfig_($params['config_id']);

			$this->main_data_before = json_decode('{}');

			$sql = "

				SELECT
					* FROM
				" . $this->config->tb_main . "
				WHERE " . $this->config->pri_key . " = " . $params['parent_id'] . "

			";

			foreach ($this->dao->fetchAll($sql) as $ka => $va) {

				$this->main_data_before = $va;
			}


			if (!empty($params['sub_config_id'])) {
				$this->config = getConfig_($params['sub_config_id']);


				if (!empty($_REQUEST['pri_key'])) {


					$action_type = 'edit';
				} else {

					$action_type = 'add';
				}

				$this->action($action_type, $_REQUEST['pri_key'], $params);
			} else {


				if (!empty($params['parent_id'])) {


					$action_type = 'edit';
				} else {

					$action_type = 'add';
				}

				$this->action($action_type, $params['parent_id'], $params);
			}
		}
	}

	function deleteRows($params)
	{

		$rowsIds = @$_REQUEST['checkme'];

		$this->config = getConfig_($params['config_id']);

		$k_tb_name = $this->config->tb_main;
		$json = [];



		if (!empty($rowsIds)) {
			foreach ($rowsIds as $k => $v) {

				$sql = "
					DELETE FROM " . $k_tb_name . "
					WHERE id = " . $v . "
				
				";

				$result = $this->dao->execDatas($sql);
				if ($result) {
					$json = array(
						"success" => true,
						"msg" => 'ลบรายการสำเร็จ'
					);
				} else {
					$json = array(
						"success" => false,
						"msg" => 'ขออภัยไม่สามารถทำรายการได้ เนื่องจากอาจมีการใช้งานร่วมกันกับข้อมูลอื่น'
					);
				}
			}
		} else {
			$json = array(
				"success" => false,
				"msg" => 'ไม่พบรายการที่ต้องการลบ'
			);
		}

		echo json_encode($json);
		exit;
	}


	function deleteData($params = array())
	{

		$this->config = getConfig_($params['config_id']);

		$k_tb_name = $this->config->tb_main;

		if (isset($_REQUEST['val'])) {
			// $sql = "
			// 	SELECT 
			// 		n.*,v.*
			// 	FROM tb_news n
			// 	INNER JOIN tb_catalog c ON c.id = n.cat_id
			// 	INNER JOIN tb_vdos v ON c.cat_id = c.id
			// 	WHERE c.id = ".$_REQUEST['val']."
			// ";

			// arr($sql);
			// exit;

			$sql = "
				DELETE FROM " . $k_tb_name . "
				WHERE id = " . $_REQUEST['val'] . "
			";


			if ($this->dao->execDatas($sql)) {
				$errors = [
					'success' => true,
					'msg' => 'ลบรายการสำเร็จ'
				];
			} else {
				$errors = [
					'success' => false,
					'msg' => 'ขออภัยไม่สามารถทำรายการได้ เนื่องจากอาจมีการใช้งานร่วมกันกับข้อมูลอื่น'
				];
			}

			echo json_encode($errors);
		}
	}

	function deleteImgs($params = array())
	{


		//arr( $params );exit;

		$this->config = getConfig_($params['config_id']);

		$k_tb_name = $this->config->tb_main;

		$sql = "
			DELETE FROM tb_ecom_file
			WHERE id = " . $params['parent_id'] . "
			AND tb_name = '" . $k_tb_name . "'
		";

		$this->dao->execDatas($sql);

		header("Location:" . comeBack());

		exit;
	}




	function formProduct($params = array())
	{


		$imgsBlock = '';
		unset($_SESSION['parent_id']);

		$config = getConfig_($params['config_id']);


		$sql = $config->main_sql;

		$filters = array();

		$filters['HAVING'][] = "id = " . $params['parent_id'] . "";

		$sql = genCond_($sql, $filters);

		$vals = array();
		foreach ($this->dao->fetchAll($sql) as $ka => $va) {

			$vals = convertObJectToArray($va);

			break;
		}


		//	arr( $vals );exit;

		$parentDatas = array();
		foreach ($vals as $kv => $vv) {
			$parentDatas['[' . $kv . ']'] = $vv;
		}


		$gogo = $this->renderBlocks($config, $vals);

		$boxsTitle['HEAD'] = 'ทั่วไป';
		$boxsTitle['BL'] = 'ข้อมูล';

		$keep = array();

		//arr( $params );
		if (in_array($params['config_id'], array(2, 3, 5, 6, 614, 78, 19))) {

			if (empty($params['parent_id'])) {

				$sql = "
					SELECT
						*
					FROM tb_ecom_file
					WHERE (  file_ref_id IS NULL OR file_ref_id = 0 )
					AND tb_name = '" . $config->tb_main . "'
					ORDER BY
						file_ordering ASC,
						id ASC
				";
			} else {

				$sql = "
					SELECT
						*
					FROM tb_ecom_file
					WHERE ( file_ref_id = " . $params['parent_id'] . " OR file_ref_id IS NULL OR file_ref_id = 0 )
					AND tb_name = '" . $config->tb_main . "'
					ORDER BY
						file_ordering ASC,
						id ASC
				";
			}

			//arr( $sql );exit;
			$imgs = array();
			foreach ($this->dao->fetchAll($sql) as $ka => $va) {

				if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $va->img_path)) {
					//continue;
				}

				//<input'. $selected .'type="radio" value="'. $va->id .'" name="show_at_title" />
				$selected = ' ';

				$imgs[$va->img_path] = '
					<div class="row">
						<div class="col-md-12">
							<div class="post-file-upload-row dz-image-preview dz-success dz-complete pull-left" id="">
								<div class="preview" style="width:85px; display: inline">
									<img data-dz-thumbnail="" class="upload-thumbnail-sm" alt="40569.jpg" src="' . $va->img_path . '" style="height: 85px;">

									<a href="' . front_link($params['id'], 'deleteImgs/' . $va->id . '') . '">
										<span data-dz-remove="" class="delete" style="margin: 20px;">
											<i class="fas fa-trash"></i>
										</span>
									</a>

									<div class="progress upload-progress-sm active m0 progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">

									</div>
								</div>
							</div>
						</div>
					</div>

				';
			}



			$imgsBlock = '
			
			
				<script type="text/javascript">
				AppHelper = {};
				</script>

				<script src="form/dropzone.min.js"></script>
				<link rel="stylesheet" href="form/dropzone.min.css" type="text/css" />
				<link href="form/dropzone.css" rel="stylesheet" />
				
				<div class="">
					<div class="">

						<div class="">
							<label>รูปภาพเพิ่มเติม</label>
							<div class="row row-sm">
								<div class="form-group">

									<div id="notes-dropzone" class="post-dropzone">

									<div class="row">
										<div class="col-md-12">
										<div class="post-file-dropzone-scrollbar hide">
											<div class="post-file-previews clearfix b-t">
												<div class="post-file-upload-row dz-image-preview dz-success dz-complete pull-left">
													<div class="preview" style="width:85px; display: inline;">
														<img data-dz-thumbnail class="upload-thumbnail-sm" style="height: 85px;" />
																<span data-dz-remove="" class="delete" style="margin: 20px;">
																	<i class="fas fa-trash"></i>
																</span>
																<div class="progress progress-striped upload-progress-sm active m0" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
																<div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
																</div>
																</div>
																</div>
																</div>
																</div>
															</div>
											<br>
										</div>


										<button class="btn btn-default upload-file-button upload pull-left btn-sm round" type="button" style="color:#7988a2">คลิกเพื่อเลือก</button>


									</div>

								</div>

							</div>


							<div class="row row-sm">
								<div class="form-group post-dropzone">
									<div class="post-file-dropzone-scrollbar hide">
										<div class="post-file-previews clearfix b-t" id="irtbomknyhjfduy" style="align-items: center;grid-template-columns: auto auto auto auto auto auto;">  ' . implode('<br>', $imgs) . '</div>

									</div>

								</div>

							</div>
						</div>
					</div>
				</div>


				<script type="text/javascript">

				// getnerat random small alphabet
					getRandomAlphabet = function (length) {
						var result = \'\',
								chars = \'abcdefghijklmnopqrstuvwxyz\';
						for (var i = length; i > 0; --i)
							result += chars[Math.round(Math.random() * (chars.length - 1))];
						return result;
					};
					var data = {};

					AppHelper.csrfTokenName = "' . get_token('name') . '";
					AppHelper.csrfHash = "' . $params['token_val'] . '";

					data[AppHelper.csrfTokenName] = AppHelper.csrfHash;

					$.ajaxSetup({
						data: data
					});

					attachDropzoneWithForm = function (dropzoneTarget, uploadUrl, validationUrl, options) {
						var $dropzonePreviewArea = $(dropzoneTarget),
							$dropzonePreviewScrollbar = $dropzonePreviewArea.find(".post-file-dropzone-scrollbar"),
							$previews = $dropzonePreviewArea.find(".post-file-previews"),
							$postFileUploadRow = $dropzonePreviewArea.find(".post-file-upload-row"),
							$uploadFileButton = $dropzonePreviewArea.find(".upload-file-button"),
							$submitButton = $dropzonePreviewArea.find("button[type=submit]"),
							previewsContainer = getRandomAlphabet(15),
							postFileUploadRowId = getRandomAlphabet(15),
							uploadFileButtonId = getRandomAlphabet(15);

						//set random id with the previws
						$previews.attr("id", previewsContainer);
						$postFileUploadRow.attr("id", postFileUploadRowId);
						$uploadFileButton.attr("id", uploadFileButtonId);


						//get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
						var previewNode = document.querySelector("#" + postFileUploadRowId);
						previewNode.id = "";
						var previewTemplate = previewNode.parentNode.innerHTML;
						previewNode.parentNode.removeChild(previewNode);

						if ( !options )
							options = {};

						var postFilesDropzone = new Dropzone( dropzoneTarget, {
							uploadMultiple: false,
							url: uploadUrl,
							thumbnailWidth: 80,
							thumbnailHeight: 80,
							parallelUploads: 20,
							maxFilesize: 3000,
							previewTemplate: previewTemplate,

							autoQueue: true,
							previewsContainer: "#" + previewsContainer,
							clickable: "#" + uploadFileButtonId,
							maxFiles: options.maxFiles ? options.maxFiles : 1000,
							sending: function (file, xhr, formData) {
								formData.append(AppHelper.csrfTokenName, AppHelper.csrfHash);
							},
							init: function () {
								this.on("maxfilesexceeded", function (file) {
									this.removeAllFiles();
									this.addFile(file);
								});


							},

							processing: function () {
								$submitButton.prop("disabled", true);

							},
							queuecomplete: function () {
								$submitButton.prop("disabled", false);

							},
							reset: function (file) {
								$dropzonePreviewScrollbar.addClass("hide");
							},
							fallback: function () {
								//add custom fallback;
								$("body").addClass("dropzone-disabled");

								$uploadFileButton.click(function () {
									//fallback for old browser
									$(this).html("<i class=\'fa fa-camera\'></i> Add more");

									$dropzonePreviewScrollbar.removeClass("hide");
									initScrollbar($dropzonePreviewScrollbar, {setHeight: 90});

									$dropzonePreviewScrollbar.parent().removeClass("hide");
									$previews.prepend("<div class=\'clearfix p5 file-row\'><button type=\'button\' class=\'btn btn-xs btn-danger pull-left mr10 remove-file\'><i class=\'fa fa-times\'></i></button> <input class=\'pull-left\' type=\'file\' name=\'manualFiles[]\' /></div>");

								});
								$previews.on("click", ".delete", function () {
									alert( \'dsfas\' );
									$(this).parent().remove();
								});
							},
							success: function ( file, response ) {

								setTimeout(function () {
									$( file.previewElement )
										.find( ".progress-striped" )
										.removeClass( "progress-striped" )
										.addClass( "progress-bar-success" );


									data = $.parseJSON( response );
									AppHelper.csrfHash = data.csrfHash;
									window.location = data.redirect;

								}, 500 );
							}
						});

						return postFilesDropzone;
					};

					$( document ).ready(function () {
						var uploadUrl = "' . front_link($params['id'], 'uploadUrl/' . $params['parent_id'] . '',  array(), false) . '";
						var validationUri = "";
						var dropzone = attachDropzoneWithForm("#notes-dropzone", uploadUrl, validationUri );
					});
				</script>
			';
		}


		foreach ($gogo as $kp => $vp) {

			$title = isset($boxsTitle[$kp]) ? $boxsTitle[$kp] : 'ข้อมูล ' . $kp;

			$keep[] = '
					<div class="">
						<div class="">
							<div class="">
	
							</div>
							<div class="">
								<div class="row">' . implode('', $vp) . '</div>
								' . $imgsBlock . '
							</div>
						</div>
					</div>
				';
		}



		$sql = "";

		if ($params['id'] == 126) {

			$sql = "
				SELECT 
					* 
				FROM role_permission 
				WHERE role_id = " . $params['parent_id'] . " 
			";

			$keepRolePermission = array();
			foreach ($this->dao->fetchAll($sql) as $ka => $va) {

				$keepRolePermission[$va->page_id] = $va;
			}

			$sql = "
				SELECT 
					* 
				FROM aa_front_page 
				WHERE user_login = 1  
				AND ( admin_menu IS NOT NULL OR admin_menu != '0' )
				AND active = 1
			
				ORDER BY order_number ASC
			";


			// arr($sqlPro);exit;
			$roleArray[] = array('label' => 'ไม่แสดง', 'value' => 1);
			$roleArray[] = array('label' => 'แสดง', 'value' => 2);
			//$roleArray[] = array( 'label' => 'ทั้งหมด', 'value' => 3 );

			foreach (getAdminMenu(NULL, true, 'bbbbbbbbbbbb') as $ka => $va) {

				if (!empty($va->every_body_login)) {
					continue;
				}

				$check = '';
				if (isset($keepRolePermission[$va->id])) {
					$check = ' checked ';
				}



				$keepRole = array();

				$checked = '';
				$text = '';
				if ($va->id == 109) {
					$text = '( กรุณาเลือกสิทธิ์นี้ทุกครั้ง )';
					$checked = 'checked';
				}

				$keepRole[] = '				
					<input ' . $check . ' type="checkbox" id="rols' . $va->id . '" name="views[' . $va->id . ']" value="1" ' . $checked . '>
					<label for="rols' . $va->id . '">สามารถใช้ได้</label>
				';


				$role[] = '
				<div class="col-4">
					<span>จัดการสิทธิ์ <span class="text-danger">' . $va->title . ' ' . $text . '</span></span>
					<ul>' . implode('<br>', $keepRole) . '</ul>
				</div>
				
			';
			}


			$keep[] = '
				<div class="row">
					' . implode('', $role) . '
				</div>
			';
		}


		if (!empty($params['sub_config_id'])) {


			$params['params'] = $params;

			$params['token_val'] = get_token('val');

			$tabName = 'tab1';

			$active = NULL;
		} else {


			$params['params'] = $params;

			$params['token_val'] = get_token('val');

			$tabName = 'tab1';

			$active = NULL;


			$active = ' active';
		}


		$tabs[] = '<li><a href="' . front_link($params['id'], 'formProduct/' . $params['parent_id'] . '') . '" class="nav-link' . $active . '" >ทั่วไป/ข้อมูล/ลิ้งค์</a> ' . getConfigLink($params) . '</li>';


		$params['tabPanes'] = '';
		$params['tabPanes'] .= '<script>trs = []</script>';

		$params['tabPanes'] .= '
			<div class="tab-pane' . $active . '" id="tab1">
				<div class="">
					<div class="row row-sm">

						<form method="post" action="' . front_link($params['id'], 'save/' . $params['parent_id'] . '', $get = array(), $token = false) . '"  enctype="multipart/form-data" >

							' . $params['secret'] . '

							<input style="display: none" type="submit" value="hidden_submit" class="hidden_submit">

							<input type="hidden" name="' . PriKey . '" value="' . $params['parent_id'] . '" />

							

							' . implode('', $keep) . '

						</form>
					</div>
				</div>
			</div>
		';

		$buttons = array();

		$toviews[105] = newsLink($params['parent_id']);
		// var_dump($params);
		if (isset($toviews[$params['id']]) && !empty($params['parent_id'])) {

			$buttons[] = '<a target="_blank" href="' . $toviews[$params['id']] . '" class="btn btn-secondary" style="background-color: #ff6f00;">ดูตัวอย่าง</a>';
		}

		$buttons[] = '<input type="submit" value="บันทึกข้อมูล" class="btn btn-primary hidden_submit" style="">';
		$buttons[] = '<a href="' . front_link($params['id']) . '" class="btn btn-secondary">ย้อนกลับไปที่รายการ</a>';

		$linksUrls = '';
		if (!empty($params['parent_id'])) {
			$linksUrls = '/' . $params['parent_id'];
		}
		// '. $this->form_mode .'
		$params['form'] = '
			<form method="POST" action="' . front_link($params['id'], 'save' . $linksUrls . '', $get = array(), $token = false) . '"  enctype="multipart/form-data" >
			
			 
				' . $params['secret'] . '
				<input type="hidden" name="' . PriKey . '" value="' . $params['parent_id'] . '" />				
				' . implode('', $keep) . '
				<div class="form-group mb-0 mt-3 justify-content-end">
					<div>' . implode(' ', $buttons) . '</div>
				</div>
			</form>
		';



		$params['tabs_menu'] = '
			<div class="tabs-menu1">
				<ul class="nav panel-tabs main-nav-line">' . implode('', $tabs) . '</ul>
			</div>
		';

		$params['datastable '] = 'fdd';
		//$params['page'] =

		echo view('__admin/aa_form', $params);

		//return view( 'adminView', $params );

	}

	function switchAct($params)
	{

		if (!empty($_REQUEST)) {
			$vals = $_REQUEST['code'];
			$type = $_REQUEST['type'];

			$config = getConfig($params['config_id']);

			$sql = "SELECT show_at_first FROM " . $config->tb_main . " WHERE id ='" . $vals . "'";
			$query = getDb()->fetch($sql);

			$datas = [];
			if ($query) {
				if ($query->show_at_first == 2) {
					$datas = [
						'show_at_first' => 1,
						'time_update' => date('Y-m-d H:i:s')
					];
				} else {
					$datas = [
						'show_at_first' => 2,
						'time_update' => date('Y-m-d H:i:s')
					];
				}

				$res = getDb()->update_($config->tb_main, $datas, 'id = "' . $vals . '"');
				if ($res) {
					$errors = [
						'success' => true,
						'msg' => 'บันทึกสำเร็จ'
					];
				} else {
					$errors = [
						'success' => false,
						'msg' => 'ขออภัยไม่สามารถทำรายการได้'
					];
				}
				echo json_encode($errors);
				exit;
			} else {
				$errors = [
					'success' => false,
					'msg' => 'ไม่พบรายการ'
				];
				echo json_encode($errors);
				exit;
			}
		}
		exit;
	}
}