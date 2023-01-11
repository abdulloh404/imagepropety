<?php

//
//
namespace App\Models;

use App\Models\Db_model;

use App\Models\Auth_model;
use CodeIgniter\Config\Services;

class Front_model
{

    public $_cache_user_in_group = [];

    public $_ion_like = [];

    public $_ion_limit = null;

    public $_ion_offset = null;

    public $_ion_order = null;

    public $_ion_order_by = null;

    public $_ion_select = [];

    public $_ion_where = [];

    public $activation_code;

    public $forgotten_password_code;

    public $identity;

    public $new_password;

    public $tables = [];

    protected $_cache_groups = [];

    protected $_ion_hooks;

    protected $error_end_delimiter;

    protected $error_start_delimiter;

    protected $errors;

    protected $messages;

    protected $response = null;



    function xmlIndex()
    {

        date_default_timezone_set('Asia/Bangkok');

        $sql = "
        SELECT
            tb_news.*,
            tb_catalog.name as catalogName, tb_catalog.id as catalogId, tb_catalog.cat_type, tb_ecom_file.img_path,
            tb_sub_catalog.name as subCatNames
        FROM tb_news
        INNER JOIN tb_catalog ON tb_catalog.id = tb_news.cat_id
        LEFT JOIN tb_sub_catalog ON tb_sub_catalog.id = tb_news.sub_cat_id
        LEFT JOIN tb_ecom_file ON tb_ecom_file.file_ref_id = tb_news.id
        WHERE tb_news.release_time < NOW()
        GROUP BY tb_news.id
        ORDER BY tb_catalog.order_number ASC
        ";

        $keep = [];
        foreach ($this->dao->fetchAll($sql) as $k => $v) {
            $keep[$v->catalogId][] = $v;
        }

        header('Content-type: application/xml;');



        $html = array();
        foreach ($keep as $k => $v) {
            // $vals = convertObJectToArray($v);


            if ($v[0]->cat_type == 2) {
            } else if ($v[0]->cat_type == 3) {
            } else if ($v[0]->cat_type == 10) {
            } else {

                $xmlDatas = array();
                foreach ($v as $kv => $vv) {


                    if (count($xmlDatas) == 4) {
                        continue;
                    }


                    $xmlDatas[] = '
                        <article>
                        <ID>' . $vv->id . '</ID>
                        <nativeCountry>TH</nativeCountry>
                        <language>th</language>
                        <startYmdtUnix>' . strtotime($vv->release_time) . '000</startYmdtUnix> 
                        <endYmdtUnix>' . strtotime($vv->expired_date) . '000</endYmdtUnix>  
                        <title>' . $vv->name . '</title>
                        <category>' . $vv->catalogName . '</category>  
                        <subCategory>' . $vv->subCatNames . '</subCategory>
                        <publishTimeUnix>' . strtotime($vv->release_time) . '000</publishTimeUnix>
                        <updateTimeUnix>' . strtotime($vv->time_update) . '000</updateTimeUnix>
                        <thumbnail>' . $vv->img_path . '</thumbnail>
                        <contentType>0</contentType>
                            <contents>
                                <text>  
                                    <content>
                                            <![CDATA[
                                                <div style="width: 100%;" class="news-pra">
                                                    <div class="container-fluid">
                                                        <img src="' . $vv->img_path . '" draggable="false" ondragstart="return false;">
                                                    </div>

                                                </div>

                                                ' . html_entity_decode($vv->detail) . '
                                            ]]>
                                    </content>
                                </text>
                            </contents>
                            
                        </article>
                    
                    ';
                }
                $html[] = implode('', $xmlDatas);
            }
        }

        $sqlVdo = "
		SELECT
			tb_vdos.*,
			tb_catalog.name as catalogName,
			tb_catalog.id as catalogId,
			tb_catalog.cat_type,
			tb_ecom_file.img_path,
			aa_users.first_name,
            tb_sub_catalog.name as subCatNames
		FROM tb_vdos
		INNER JOIN tb_catalog ON tb_catalog.id = tb_vdos.cat_id
        LEFT JOIN tb_sub_catalog ON tb_sub_catalog.id = tb_vdos.sub_cat_id
		LEFT JOIN tb_ecom_file ON tb_ecom_file.file_ref_id = tb_vdos.id
		LEFT JOIN aa_users ON tb_vdos.user_id = aa_users.id
		WHERE tb_vdos.release_time < NOW()
		AND upload_video IS NOT NULL
		AND tb_vdos.status_id = 1
		GROUP BY
			tb_vdos.id
		ORDER BY
			tb_vdos.time_update DESC
		LIMIT 0,6
	";

        $xmlDatas = array();
        foreach ($this->dao->fetchAll($sqlVdo) as $kv => $vv) {

            $xmlDatas[] = '
            <article>
            <ID>' . $vv->id . '</ID>
            <nativeCountry>TH</nativeCountry>
            <language>th</language>
            <startYmdtUnix>' . strtotime($vv->release_time) . '000</startYmdtUnix> 
            <endYmdtUnix>' . strtotime($vv->expired_date) . '000</endYmdtUnix>  
            <title>' . $vv->name . '</title>
            <category>' . $vv->catalogName . '</category>  
            <subCategory>' . $vv->subCatNames . '</subCategory>
            <publishTimeUnix>' . strtotime($vv->release_time) . '000</publishTimeUnix>
            <updateTimeUnix>' . strtotime($vv->time_update) . '000</updateTimeUnix>
            <thumbnail>' . $vv->img_path . '</thumbnail>
            <contentType>0</contentType>
                <contents>
                    <text>  
                        <content>
                                <![CDATA[
                                    <div style="width: 100%;" class="news-pra">
                                        <div class="container-fluid">
                                            <img src="' . $vv->img_path . '" draggable="false" ondragstart="return false;">
                                        </div>

                                    </div>
                                    <iframe src="' . $vv->youtube_link . '" title="' . $vv->name . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe> 
                                    ' . html_entity_decode($vv->detail) . '
                                ]]>
                        </content>
                    </text>
                </contents>
                
            </article>
        
        ';
        }

        $html[] = implode('', $xmlDatas);





        echo '<articles>
            <UUID>fm91' . time() . '000</UUID>
            <time>' . time() . '000</time>
            ' . implode('', $html) . '
        </articles>';
        exit;
    }



    function editvideo()
    {

        arr($_REQUEST);
        arr($_FILES);
        $data['success'] = 1;
        echo json_encode($data);
    }

    //
    //
    function register($param, $active = false)
    {

        $request = service('request');
        $result = $request->getVar();
        $encrypter = \Config\Services::encrypter();
        // var_dump($result);exit;


        // $configs['g-recaptcha-response'] = array( 'require' => 1 );
        $configs['emailRegis'] = array(
            'require' => 1,
            'format' => 'email',
            'nodupicate' => array(
                'check_tb' => 'aa_users',
                'check_column' => 'CONCAT( email )'
            )
        );

        $configs['passwordRegis'] = array(
            'format' => 'password',
            'require' => 8
        );

        // 'same_as' => 'confirm_password'
        $configs['repasswordRegis'] = array('require' => 8);

        $check_form = check_form($result, $configs);

        if (!empty($check_form['errors'])) {

            $errors['success'] = 0;
            $errors['field'] = $check_form['errors'];
            $errors['message'] = 'กรุณากรอกข้อมูลให้ถูกต้อง และครบถ้วน';

            echo json_encode($errors);
            exit;
        }

        // $result = $check_form['result'];

        $password   = password_hash($result['passwordRegis'], PASSWORD_DEFAULT);

        // Users table.


        $sql = "
            INSERT INTO aa_users
                (email, password, active, created_on, last_login, status_id )
                VALUES ('" . $result['emailRegis'] . "', '" . $password . "',  0, '" . date('Y-m-d') . "','" . time() . "', 1)
        ";
        // arr($sql);exit;
        $data_res = $this->dao->execDatas($sql);


        $email = $result['emailRegis'];

        if ($data_res) {
            $sql_getId = "SELECT id from aa_users WHERE aa_users.email = '" . $result['emailRegis'] . "'";

            $getId = $this->dao->fetchAll($sql_getId);

            foreach ($getId as $key => $val) {

                $gen_user_id = $encrypter->encrypt($val->id);
            }





            $param['gen_user_id_code'] = array('code' => $gen_user_id);

            $param['viewName'] = "Email_template/signedSuccessfully";;
            $result['email_template'] = view('emailView', $param);
            $result['email_Subject'] = "Thank You for SignUp";
            $result['message'] = "ระบบทำการสมัครสมาชิกเรียบร้อยแล้ว";
            $result['email'] = $email;
            $result['html'] = '
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h2>สวพ.FM91</h2>
                            <div style="text-align: center; margin-top: 40px;">
                                <h3 style="padding-left:20px; padding-right: 20px;">ยินดีด้วยเราได้ทำการสร้างบัญชีผู้ใช้งานของท่านแล้ว</h3>
                                <div class="main-signup-header">
                                <h3>กรุณาตรวจสอบอีเมล์ <span style="color: green;"> Email: ' . $result['emailRegis'] . '</span> เพื่อยืนยันการสมัคร </h3>
                                </div>
                            </div>
                        </div>

                    </div>

            ';


            // echo $result['html'];
            SendEmail($result);

            // $errors = array(
            //     'success' => 1,
            //     'message' => 'สมัครสามชิกของท่านเรียบร้อนแล้ว',
            //     'redirect' => front_link(109)
            // );
        } else {
            $errors = array(
                'success' => 0,
                'message' => 'ไม่สามารถสมัครสมาชิกได้'
            );
            echo json_encode($errors);
        }
    }

    function sortTableList()
    {


        $i = 1;
        foreach ($_REQUEST['item'] as $k => $v) {

            if ($k == 'video') {
                foreach ($v as $vv) {
                    $sql = "UPDATE tb_catalog SET order_number_videoCat = " . $i . " WHERE tb_catalog.id = " . $vv . " ";

                    $this->dao->execDatas($sql);
                    $i++;
                }
            } else {
                $sql = "UPDATE tb_catalog SET order_number = " . $i . " WHERE tb_catalog.id = " . $v . " ";
                $this->dao->execDatas($sql);

                $i++;
            }
        }
    }

    function reqPassword()
    {
        date_default_timezone_set("Asia/Bangkok");


        $request = service('request');
        $result = $request->getVar();
        // var_dump($result);exit;

        $configs['email'] = array(
            'require' => 1,
            'format' => 'email'
        );
        $time = date("H:i:s", strtotime("+15 minutes"));
        $check_form = check_form($result, $configs);

        if (!empty($check_form['errors'])) {

            $errors['success'] = 0;
            $errors['field'] = $check_form['errors'];
            $errors['message'] = 'กรอกข้อมูลไม่ครบ กรุณาลองใหม่อีกครั้ง';

            echo json_encode($errors);
            exit;
        }
        // exit;




        // var_dump($request);
        $sql_user = "
            SELECT id
            FROM aa_users
            WHERE aa_users.email = '" . $result['email'] . "'
        ";

        $user_data = $this->dao->fetchAll($sql_user);
        $ids = 0;
        foreach ($user_data as $k => $v) {
            $ids = $v->id;
        }

        $refCode = getRandomString();
        $otp = strtoupper($refCode);

        if (!empty($user_data)) {

            $del_sql = "DELETE FROM tb_resetpassword WHERE email='" . $result['email'] . "';";
            $this->dao->execDatas($del_sql);

            $sql_pass = "
            INSERT INTO
                tb_resetpassword
                    (email, ref_no, user_id, end_reset_password_time)
            VALUES
                    ('" . $result['email'] . "', '" . $otp . "', " . $ids . ",'" . $time . "');
        ";

            if ($this->dao->execDatas($sql_pass)) {
                $param['email'] = $result['email'];
                $param['ref_no'] = $otp;
                $param['user_id'] = $ids;
                $param['viewName'] = "Email_template/resetPassword";
                // echo view('emailView',$param); exit;
                $result['email_template'] = view('emailView', $param);
                $result['email_Subject'] = "Reset Password";
                $result['message'] = "ระบบทำการส่งรหัสอ้างอิงไปยัง Email ของท่านเรียบร้อยแล้ว";

                if (isset($result['resetPass'])) {
                    $result['html'] = '
                    <div class="card">
                        <div class="card-body" style="text-align: center;">
                            <h2>สวพ.FM91</h2>
                            <div style="text-align: center; margin-top: 40px;">
                                <h3 style="padding-left:20px; padding-right: 20px;">ยินดีด้วยเราได้ส่งรหัสอ้างอิงให้เรียบร้อยแล้ว</h3>
                                <div class="main-signup-header">
                                    <h3>กรุณาตรวจสอบอีเมล์ <span style="color: green;">' . $result['email'] . '</span> เพื่อนำรหัสอ้างอิงมาใช้ในการรีเซตรหัสผ่าน </h3>
                                    <div class="main-signup-footer mg-t-20">
                                        <a class="btn btn-success btn-block" href="' . front_link(15) . '">รีเซ็ตรหัสผ่าน</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    ';
                } else {
                    $result['html'] = '


                    <div class="wd-100p">
                        <div class="main-signin-header">
                            <div style="text-align: center;">
                                <img src="' . base_url('front/assets/img/logo.jpg') . '" style="border-radius: 5px">
                            </div>
                            <div class="row main-signin-header">
                                        <div style="text-align: center; margin-top: 20px;">
                                            <h3 style="padding-left:20px; padding-right: 20px;">ยินดีด้วยเราได้ส่งรหัสอ้างอิงให้เรียบร้อยแล้ว</h3>
                                        </div>
                                    </div>
                            </div>
                            <div style="padding-bottom: 15px;">

                                <h3>กรุณาตรวจสอบอีเมล์ <span style="color: green; font-weight: bold;">' . $result['email'] . '</span> เพื่อนำรหัสอ้างอิงมาใช้ในการรีเซตรหัสผ่าน</h3>
                                <div class="main-signup-footer mg-t-20">
                                    <a class="btn btn-success btn-block" href="' . front_link(115) . '">รีเซ็ตรหัสผ่าน</a>
                                </div>
                            </div>
                    </div>
                    ';
                }


                // echo $result['html'];exit;
                SendEmail($result);
            } else {
                $errors = array(
                    'success' => 0,
                    'message' => 'ไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง'
                );
                echo json_encode($errors);
            }
        } else {
            $errors = array(
                'success' => 0,
                'message' => 'ไม่พบ ' . $result['email'] . ' กรุณาลองใหม่อีกครั้ง'
            );
            echo json_encode($errors);
        }
    }

    function newPassword()
    {
        $request = service('request');
        $result = $request->getVar();
        // var_dump($result);exit;
        $configs['otp'] = array('require' => 1);
        $configs['newPassword'] = array('require' => 8, 'same_as' => 'comfirmPassword', 'format' => 'password');
        $configs['comfirmPassword'] = array('require' => 8, 'format' => 'password');


        // $result['code_ref'] = $result['code1'].$result['code2'].$result['code3'].$result['code4'].$result['code5'].$result['code6'];

        $check_form = check_form($result, $configs);

        if (!empty($check_form['errors'])) {

            $errors['success'] = 0;
            $errors['field'] = $check_form['errors'];
            $errors['message'] = 'กรอกข้อมูลไม่ครบ หรือ กรอกรหัส OTP หรือ รหัสผ่านของท่านผิด';

            echo json_encode($errors);
            exit;
        }

        $sql_user = "
            SELECT *
            FROM tb_resetpassword
            WHERE tb_resetpassword.ref_no = '" . $result['otp'] . "' AND now() < end_reset_password_time
        ";

        $ref_data = $this->dao->fetchAll($sql_user);

        $password   = password_hash($result['newPassword'], PASSWORD_DEFAULT);

        foreach ($ref_data as $k => $v) {
            $uid = $v->user_id;
        }
        // arr($uid);
        // exit;

        // var_dump($result);exit;
        if ($ref_data) {
            $update_pass = "
                UPDATE aa_users SET password = '" . $password . "' WHERE aa_users.id = " . $uid . ";
            ";
            // arr($update_pass );exit;
            $update = $this->dao->execDatas($update_pass);

            if ($update) {
                $deletd_ref = "DELETE FROM tb_resetpassword WHERE tb_resetpassword.id = $uid";
                $this->dao->execDatas($deletd_ref);
                if (isset($result['memberPass'])) {
                    $links = front_link(12);
                } else {
                    $links = front_link(116);
                }
                $errors = array(
                    'success' => 1,
                    'message' => 'ระบบทำการรีเซ็ตรหัสผ่านของท่านเรียบร้อยแล้ว',
                    'redirect' => $links
                );
                echo json_encode($errors);
            } else {
                $errors = array(
                    'success' => 0,
                    'message' => 'ระบบไม่สามารถทำการรีเซ็ตรหัสผ่านของท่านได้ กรุณาลองใหม่'
                );
                echo json_encode($errors);
            }
        } else {
            $errors = array(
                'success' => 0,
                'message' => 'รหัสอ้างอิงของท่านหมดอายุแล้ว'
            );
            echo json_encode($errors);
        }
    }


    function dashboard()
    {

        $sql = "
			SELECT
				c.name,
				SUM( n.countRead ) as t

			FROM `tb_news` n
			INNER JOIN tb_catalog c ON n.cat_id = c.id

			GROUP BY
				n.cat_id

			ORDER BY
				c.order_number

		";

        $xVal = array();
        foreach ($this->dao->fetchAll($sql) as $key => $val) {
            $xVal[] = $val->name;

            $yval[] = $val->t;
        }
        $ajax['name'] = $xVal;
        $ajax['count'] = $yval;

        echo json_encode($ajax);
    }





    function test()
    {




        $test['html'] = '
 <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-xl-12">
                        <div class="card" style="margin-top: 30px;">
                            <div class="card-header">
                                <h5 class="card-title mg-b-0">ข้อมูลของฉัน</h5>
                                <p class="text-muted">จัดการข้อมูลส่วนตัวคุณเพื่อความปลอดภัยของบัญชีผู้ใช้นี้</p>
                            </div>
                            <div class="card-body">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">ชื่อผู้ใช้</span>
                                                <input type="text" class="form-control" aria-label="Sizing example input" placeholder="เอศุ" disabled>
                                            </div>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">ชื่อ-นามสกุล</span>
                                                <input type="text" class="form-control" aria-label="Sizing example input" placeholder="">
                                            </div>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">อีเมลล์</span>
                                                <input type="email" class="form-control" placeholder="su*******@hotmail.com" disabled>
                                                <span class="input-group-text"><a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal">เปลี่ยน</a></span>
                                                <!-- Modal -->
                                                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">เปลี่ยนอีเมลล์</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="email" class="form-control" placeholder="su*******@hotmail.com">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                                <button type="button" class="btn btn-primary">บันทึก</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">หมายเลขโทรศัพท์</span>
                                                <input type="text" class="form-control" placeholder="0xx-xxx-56xx" disabled>
                                                <span class="input-group-text"><a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal1">เปลี่ยน</a></span>
                                                <!-- Modal -->
                                                <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">เปลี่ยนเบอร์โทรศัพท์</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="text" class="form-control" placeholder="0xx-xxx-56xx">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                                <button type="button" class="btn btn-primary">บันทึก</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">ชื่อร้านค้า</span>
                                                <input type="text" class="form-control" aria-label="Sizing example input" placeholder="">
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="input-group mb-3" style="margin-top: 5px;">
                                                        <span class="input-group-text">วัน/เดือน/ปี เกิด</span>
                                                        <input type="text" class="form-control" type="text" name="" id="datepicker1" placeholder="DD/MM/YY">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <label>เพศ :</label>
                                                    <div class="form-group d-flex justify-content-between">
                                                        <div class="d-flex">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                                                <label class="form-check-label" for="flexRadioDefault1">
                                                                    ชาย
                                                                </label>
                                                            </div>
                                                            <div class="form-check" style="margin-left: 20px;">
                                                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                                                <label class="form-check-label" for="flexRadioDefault1">
                                                                    หญิง
                                                                </label>
                                                            </div>
                                                            <div class="form-check" style="margin-left: 20px;">
                                                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                                                                <label class="form-check-label" for="flexRadioDefault1">
                                                                    ไม่ระบุเพศ
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-5">
                                            <div class="container" style="border-left: solid 1px; text-align: center; margin-top: 20px;">
                                                <div>
                                                    <img src="./img/great.jpg" alt="" style="width: 100px; height: 100px; border-radius: 50%;">
                                                </div>
                                                <div style="margin-top: 20px;">
                                                    <button type="file" class="btn btn-outline-dark">เลือกรูป</button>
                                                    <input type="file" id="my_file" style="display: none;" />
                                                </div>
                                                <div style="margin-top: 10px;" class="text-muted">
                                                    <p>ขนาดไฟล์สูงสุด : 1 MB</p>
                                                    <p>ไฟล์ที่รองรับ : JPEG , PNG</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-5">
                                            <div style="margin-top: 20px;">
                                                <button type="submit" class="btn text-white" style="box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25); background-color: #0082C8;">บันทึกข้อมูล</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		';
        return $test;
    }

    function add_project($param){
        $request = service('request');
        $img1 = $request->getFile('img_logo');
        var_dump($_REQUEST);exit;
        return $_REQUEST;
    }



    function signin($param)
    {




        $configs['email'] = array('require' => 1, 'format' => 'email');
        $configs['password'] = array('require' => 1);

        $check_form = check_form($_REQUEST, $configs);


        if (!empty($check_form['errors'])) {

            $errors['success'] = 0;
            $errors['field'] = $check_form['errors'];
            $errors['message'] = 'กรุณากรอกข้อมูลให้ถูกต้อง';
            echo json_encode($errors);
            exit;
        }




        $password = $_REQUEST['password'];

        $sql = "

			SELECT
				*
			FROM aa_users WHERE email = '" . $_REQUEST['email'] . "'
		";


        $errors = array(
            'success' => 0,
            'message' => 'ไม่พบผู้ใช้งานในระบบ กรุณาลองใหม่อีกครั้ง'
        );
        $errors['field']['email'] = $errors['message'];

        $timeout = 10;



        foreach ($this->dao->fetchAll($sql) as $ku => $vu) {

            if (password_verify($password, $vu->password)) {

                $_SESSION['user_id'] = $vu->id;
                setcookie('user_id', $vu->id, time() + (86400), "/"); // 86400 = 1 day            

                $this->dao->getUser($vu->id);

                $getAdminMenu = getAdminMenu(NULL, true);

                $link_id = 1;
                foreach ($getAdminMenu as $km => $vm) {

                    $link_id = 14;
                    break;
                }

                $errors = array(
                    'success' => 1,
                    'message' => 'ยินดีต้อนรับ',
                    'redirect' => front_link($link_id)
                );
            } else {
                $errors = array(
                    'success' => 0,
                    'message' => 'รหัสผ่านของท่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง',
                    'token_val' => csrf_hash()
                );
                $errors['field']['password'] = $errors['message'];
            }
        }

        // exit;


        echo json_encode($errors);
    }

    public function activateUser()
    {
        $encrypter = \Config\Services::encrypter();

        $request = service('request');
        $result = $request->getVar();


        $ids_users = $result['activate_code'];
        // var_dump($ids_users);exit;
        // $exp_date = expired_date($code_id, "free");


        $sql_update = "
            UPDATE aa_users
            SET active = '1'
            WHERE aa_users.id = $ids_users;
        ";
        if ($this->dao->execDatas($sql_update)) {
            $errors = array(
                'success' => 1,
                'message' => 'ระบบทำการเปิดใช้งานบัญชีของท่านแล้ว',
                'redirect' => front_link(12)
            );
        } else {
            $errors = array(
                'success' => 0,
                'message' => 'ระบบมีปัญหากรุณาลองใหม่อีกครั้ง'
            );
        }
        echo json_encode($errors);
    }



    function ordersummary()
    {

        $request = service('request');
        $result = $request->getVar();


        $configs['name'] = array('require' => 1);
        $configs['phone'] = array('require' => 1);
        $configs['email'] = array('require' => 1, 'format' => 'email');
        $configs['description'] = array('require' => 1);
        $configs['address_street'] = array('require' => 1);
        $configs['sub_district'] = array('require' => 1);
        $configs['district'] = array('require' => 1);
        $configs['province'] = array('require' => 1);
        $configs['postcode'] = array('require' => 1);


        $check_form = check_form($result, $configs);
        if (!empty($check_form['errors'])) {

            $errors['success'] = 0;
            $errors['field'] = $check_form['errors'];
            $errors['message'] = 'กรุณากรอกข้อมูลให้ถูกต้อง';

            echo json_encode($errors);
            exit;
        }

        $sql = "INSERT INTO tb_ecom_address
            (
                address_name ,
                address_telephone,
                address_street,
                address_sub_district,
                address_district,
                address_province,
                address_post_code,
                address_default
            )
            VALUES
            (
                '" . $result['name'] . "',
                '" . $result['phone'] . "',
                '" . $result['address_street'] . "',
                '" . $result['sub_district'] . "',
                '" . $result['district'] . "',
                '" . $result['province'] . "',
                '" . $result['postcode'] . "',
                '" . $result['default_address'] . "'

            ); ";
        // arr($sql);exit;
        // $this->dao->execDatas($sql);

        if ($this->dao->execDatas($sql)) {

            $errors = array(
                'success' => 1,
                'message' => 'ระบบบันทึกข้อมูลเรียบร้อยแล้ว'
            );
        } else {
            $errors = array(
                'success' => 0,
                'message' => 'ระบบไม่สามารถบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง'
            );
        }

        echo json_encode($errors);
        exit;
        // $time = explode(":", $request['time_pay']);

        // $package_ids = explode("-",$request['package_item']);
        // $user_id = $_SESSION['user_id'];

        // if (isset($_FILES) && isset($user_id)) {
        //     foreach ($_FILES as $kf => $vf) {
        //         $file_content = file_get_contents($vf['tmp_name']);
        //         file_put_contents('assets/FILE_FOLDER/erp_payment_log/paylog-' . $request['date_pay'] . '-' . implode("-", $time) . '.jpg', $file_content);
        //     }
        //     $file = 'assets/FILE_FOLDER/erp_payment_log/paylog-' . $request['date_pay'] . '-' . implode("-", $time) . '.jpg';
        //     $convert_time = date("H:i:s",strtotime($request['time_pay']));
        //     $sql = "
        //     INSERT INTO erp_payment_log (id, file_sclip,user_id, date_pay, time_pay, amount_pay,vat, phone, company_name, company_address, company_tax_no, package_id, package_type,invoice_tax)
        //     VALUES (
        //         NULL,
        //         '" . $file . "',
        //         '" . $user_id . "',
        //         '" . $request['date_pay'] . "',
        //         '" . $convert_time . "',
        //         '" . $request['pay_amount'] . "',
        //         '" . $vat . "',
        //         '" . $request['phone'] . "',
        //         '" . $request['company_name'] . "',
        //         '" . $request['company_address'] . "',
        //         '" . $request['company_tax_no'] . "',
        //         " . $package_ids[0] . ",
        //         '" . $request['package_type'] . "' ,
        //         '" . $vat_order . "'
        //         );
        //     ";
        //     // arr($sql);exit;
        //     $result = $this->db->query($sql);

        //     if ($result) {
        //         $sql = "UPDATE sma_users SET request_group_id = '" . $package_ids[0] . "' WHERE sma_users.id = $user_id;";
        //         $request_packgae = $this->db->query($sql);
        //         if ($request_packgae) {
        //             $request['active'] = "payment_form";
        //             $request['file'] = $file;
        //             send_email($request);
        //         }
        //     } else {
        //         $errors = array(
        //             'success' => 0,
        //             'message' => 'ระบบมีปัญหากรุณาลองใหม่อีกครั้ง'

        //         );
        //         echo json_encode($errors);
        //     }
    }
    // แจ้งเตือน

    function payment()
    {
        $request = service('request');
        $result = $request->getVar();

        $configs['paymentBtn'] = array('require' => 1);

        $check_form = check_form($result, $configs);
        if (!empty($check_form['errors'])) {

            $errors['success'] = 0;
            $errors['field'] = $check_form['errors'];
            $errors['message'] = 'กรุณากรอกข้อมูลให้ถูกต้อง';

            echo json_encode($errors);
            exit;
        }

        $sql = "
        INSERT INTO tb_ecom_payments
            (
                payment_name,
                payment_description,
                payment_images,
                payment_params,
                payment_shipping_methods,
                payment_currency
            )
        VALUES
            (
                '" . $result['paymentBtn'] . "',
                '" . $result['paymentBtn'] . "',
                'aa',
                'a',
                'a',
                'บาท'
            )
        ";


        if ($this->dao->execDatas($sql)) {
            $param['viewName'] = "Email_template/order_info";

            $result['email'] = 'develop0131997@gmail.com';
            $result['email_template'] =  view('emailView', $param);
            $result['email_Subject'] =  "คำสั่งซื้อของเลขที่ XXX";
            $result['message'] = "ระบบทำการสั่งซื้อสินค้าของท่านเรียบร้อยแล้ว";
            $result['redirect'] = front_link(9);
            SendEmail($result);
        } else {
            $errors = array(
                'success' => 0,
                'message' => 'ระบบไม่สามารถบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง'
            );
            echo json_encode($errors);
        }

        exit;
    }






    //
    //
    function __construct()
    {

        $this->dao = new Db_model();
        date_default_timezone_set('Asia/Bangkok');
    }

    function index($param = array())
    {


        //arr( $_SESSION);
        if ($this->formMode == 'get') {

            $request = $this->input->get();
        } else {
            $request = $this->input->post();
        }

        $param['param'] = $param;

        // var_dump($param);
        if (isset($request['action'])) {

            call_user_func(array($this, $param['action_call']), $param);

            return;
        }

        $param['myError'] = json_encode(array());
        if (!empty($_SESSION['myError'])) {

            $param['myError'] = $_SESSION['myError'];

            $_SESSION['myError'] = array();
        }


        $param['secret'] = form_open_token() . '<input type="hidden" name="action" value="1" />' . getBombLacyForm() . '';
        $param['formMode'] = $this->formMode;
        $param['front_menus'] = $this->front_menus();
        $param['package_item'] = $this->package_item();
        $param['bType'] = $this->get_list_bussiness_type();
        $param['users_login_data'] = $this->get_users_login_data();
        // $param['get_data_user'] = $this->get_data_user();

        // var_dump($param);

        $this->load->view($param['theme'], $param);
    }


    function get_data_user()
    {

        $sql = "
            SELECT
                *
            FROM
                sma_users
            WHERE
                sma_users.id = '" . $_SESSION['user_id'] . "'
        ";
        $result = $this->Db_model->fetch($sql);
        return $result;
    }


    function front_menus($id = NULL)
    {

        $sql = "
			SELECT
				*
			FROM erp_front_page
		";

        $lis = array();
        foreach ($this->Db_model->fetchAll($sql) as $key => $val) {
            $lis[] = '
				<a href="' . front_link($val->id) . '" class="dropdown-link">
					<span class="me-2">' . $val->icon . '</span>
					<div class="drop-title">' . $val->title . '</div>
				</a>
			';
        }

        return implode('', $lis);
    }

    function get_list_bussiness_type()
    {
        $sql = "
            SELECT
                *
            FROM
                erp_bussiness_type
        ";
        $result = $this->Db_model->fetchAll($sql);
        return $result;
    }

    function get_users_login_data()
    {
        if (isset($_SESSION['user_id'])) {
            $sql = "
                SELECT
                    *
                FROM
                    sma_users
                LEFT JOIN erp_user_company ON erp_user_company.user_id = sma_users.id
                WHERE
                    sma_users.id = '" . $_SESSION['user_id'] . "'
            ";
            $result = $this->Db_model->fetchAll($sql);
            foreach ($result as $kr => $vr) {
                return $vr;
            }
        }
    }

    function package_item()
    {
        $sql = "
            SELECT
                *
            FROM sma_groups

            WHERE pagekage = 1
            ORDER BY sma_groups.order_number ASC
        ";
        $plis = array();
        foreach ($this->Db_model->fetchAll($sql) as $key => $val) {

            $plis[] = array('id' => $val->id, 'name' => $val->name, 'pagekage' => $val->pagekage, 'order_number' => $val->order_number, 'detail' => $val->detail, 'price' => $val->price, 'best_seller' => $val->best_seller);
        }

        return $plis;
        // var_dump($plis);
    }

    public function salt()
    {

        return substr(md5(uniqid(rand(), true)), 0, $this->salt_length);
    }

    protected function _call_hook($event, $name)
    {
        if (isset($this->_ion_hooks->{$event}[$name]) && method_exists($this->_ion_hooks->{$event}[$name]->class, $this->_ion_hooks->{$event}[$name]->method)) {
            $hook = $this->_ion_hooks->{$event}[$name];

            return call_user_func_array([$hook->class, $hook->method], $hook->arguments);
        }

        return false;
    }

    public function trigger_events($events)
    {
        if (is_array($events) && !empty($events)) {
            foreach ($events as $event) {
                $this->trigger_events($event);
            }
        } else {
            if (isset($this->_ion_hooks->$events) && !empty($this->_ion_hooks->$events)) {
                foreach ($this->_ion_hooks->$events as $name => $hook) {
                    $this->_call_hook($events, $name);
                }
            }
        }
    }

    public function email_check($email = '')
    {
        $this->trigger_events('email_check');

        if (empty($email)) {
            return false;
        }

        $this->trigger_events('extra_where');

        return $this->db->where('email', $email)
            ->count_all_results($this->tables['users']) > 0;
    }

    public function username_check($username = '')
    {
        $this->trigger_events('username_check');

        if (empty($username)) {
            return false;
        }

        $this->trigger_events('extra_where');

        return $this->db->where('username', $username)
            ->count_all_results($this->tables['users']) > 0;
    }

    protected function _filter_data($table, $data)
    {
        $filtered_data = [];
        $columns       = $this->db->list_fields($table);

        if (is_array($data)) {
            foreach ($columns as $column) {
                if (array_key_exists($column, $data)) {
                    $filtered_data[$column] = $data[$column];
                }
            }
        }

        return $filtered_data;
    }



    public function set_error($error)
    {
        $this->errors[] = $error;

        return $error;
    }

    protected function _prepare_ip($ip_address)
    {
        if ($this->db->platform() === 'postgre' || $this->db->platform() === 'sqlsrv' || $this->db->platform() === 'mssql' || $this->db->platform() === 'mysqli' || $this->db->platform() === 'mysql') {
            return $ip_address;
        }
        return inet_pton($ip_address);
    }

    public function hash_password($password, $salt = false, $use_sha1_override = false)
    {
        if (empty($password)) {
            return false;
        }

        //bcrypt
        if ($use_sha1_override === false && $this->hash_method == 'bcrypt') {
            return $this->bcrypt->hash($password);
        }

        if ($this->store_salt && $salt) {
            return sha1($password . $salt);
        }
        $salt = $this->salt();
        return $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
    }





    function ask_price($param)
    {
        $configs['name'] = array('require' => 1);
        $configs['phone'] = array('require' => 1);
        $configs['website'] = array('require' => 1);
        $configs['email'] = array('require' => 1, 'format' => 'email');
        $configs['company_size'] = array('require' => 1);
        $configs['message'] = array('require' => 1);
        $configs['flexCheckChecked'] = array('require' => 1);


        $request = $this->input->post();

        $check_form = check_form($request, $configs);

        if (!empty($check_form['errors'])) {

            $errors['success'] = 0;
            $errors['field'] = $check_form['errors'];
            $errors['message'] = 'กรอกข้อมูลการเข้าระบบไม่ถูกต้อง';
            echo json_encode($errors);
            exit;
        }
        getBombCapcha($request);

        $request = $check_form['result'];

        // var_dump( $request );exit;
        $sql = "INSERT INTO erp_requestquotations(phone, company_name, website, email, message, country, company_size) VALUES ('" . $request['phone'] . "', '" . $request['name'] . "', '" . $request['website'] . "', '" . $request['email'] . "', '" . $request['message'] . "', '" . $request['country'] . "', '" . $request['company_size'] . "')";
        $result = $this->db->query($sql);

        echo json_encode(array('success' => 1, 'message' => 'ขอข้อมูลสำเร็จ'));
    }

    function appointments()
    {
        $request = $this->input->post();
        $configs['com_name'] = array('require' => 1);
        $configs['tel'] = array('require' => 1);
        $configs['website'] = array('require' => 1);
        $configs['work-email'] = array('require' => 1, 'format' => 'email');
        $configs['budget'] = array('require' => 1);
        $configs['country'] = array('require' => 1);
        $configs['textarea'] = array('require' => 1);
        $configs['acceptChecked'] = array('require' => 1);

        $check_form = check_form($request, $configs);
        if (!empty($check_form['errors'])) {

            $errors['success'] = 0;
            $errors['field'] = $check_form['errors'];
            $errors['message'] = 'กรอกข้อมูลการเข้าระบบไม่ถูกต้อง';
            echo json_encode($errors);
            exit;
        }
        getBombCapcha($request);

        $data = array(
            'name' => $request['com_name'],
            'phone' => $request['tel'],
            'website' => $request['website'],
            'email'  => $request['work-email'],
            'company_size' => $request['budget'],
            'country'     => $request['country'],
            'message'     => $request['textarea']
        );
        $result = $this->Db_model->insert('erp_appointments', $data);
        if ($result) {
            $errors = array(
                'success' => 1,
                'message' => "ระบบทำการส่งเรื่องนัดหมายของท่านเรียบร้อยแล้ว"
            );
            echo json_encode($errors);
        } else {
            $errors = array(
                'success' => 0,
                'message' => "ขออภัย ระบบไม่สามารถทำการส่งเรื่องนัดหมายของท่านได้"
            );
            echo json_encode($errors);
        }
    }

    public function contact($param)
    {
        $configs['name'] = array('require' => 1);
        $configs['company'] = array('require' => 1);
        $configs['emailcon'] = array(
            'require' => 1,
            'format' => 'email'
        );
        $configs['yourMessage'] = array('require' => 1);

        $result = $this->input->post();

        $check_form = check_form($result, $configs);


        if (!empty($check_form['errors'])) {

            $errors['success'] = 0;
            $errors['field'] = $check_form['errors'];
            $errors['message'] = 'กรุณากรอกข้อมูลให้ถูกต้อง และครบถ้วน';
            echo json_encode($errors);
            exit;
        }

        getBombCapcha($result);
        $result = $check_form['result'];
        // var_dump( $request );exit;

        $sql = "
            INSERT INTO erp_contact
            (
                fullname,
                phone,
                email,
                message
            )
            VALUES
            (
                '" . $result['name'] . "',
                '" . $result['company'] . "',
                '" . $result['emailcon'] . "',
                '" . $result['yourMessage'] . "'
            )";

        $this->db->query($sql);

        echo json_encode(array('success' => 1, 'message' => 'ส่งข้อมูลเรียบร้อยแล้ว'));
    }


    function confirmUserData()
    {
        $best = 1;
        $id = 10;
        $sql = "SELECT * FROM sma_groups WHERE pagekage = 1 ORDER BY order_number";
        $a = $this->Db_model->fetchAll($sql);
        foreach ($a as $k => $v) {
            var_dump($v->best_seller);
            if ($v->best_seller == 0 && $id) {
                $update = "UPDATE sma_groups SET best_seller = " . $best . " WHERE sma_groups.id = " . $id . " ";
                $b = $this->db->query($update);
            }
            if ($v->best_seller != 0 && $v->id) {
                $update = "UPDATE sma_groups SET best_seller = 0 WHERE sma_groups.id = " . $v->id . " ";
                $b = $this->db->query($update);
            }
        }
    }

    function email_temp()
    {
        $param['viewName'] = "Email_template/order_info";
        echo view('emailView', $param);
    }




    function subscribe()
    {

        $request = $this->input->post();
        $request['active'] = "subscribe";

        $data = array(
            'email' => $request['email'],
            'date'  => date("Y-m-d H:i:s")
        );

        $result = $this->Db_model->insert('erp_subscribe', $data);
        // var_dump($result);exit;
        if ($result) {
            send_email($request);
        } else {
            $errors = array(
                'success' => 0,
                'message' => 'ไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง'
            );
            echo json_encode($errors);
        }
    }

    function get_product_by_id($params = array())
    {
        $params['product_id'] = $params['call_func'];
        // arr($params);exit;
        $sql = "SELECT * FROM aa_gcity_product WHERE id = " . $params['product_id'];
        $products = array();

        $params['product'] = $this->dao->fetch($sql);

        return $params;
    }

    function get_products($params = array())
    {
        // arr($params);exit;
        $sql = "SELECT * FROM aa_gcity_product WHERE product_parent_id = 0";
        $products = array();


        foreach (getDb()->fetchAll($sql) as $key => $val) {
            $products[] = $val;
        }

        $params['product'] = $products;

        // arr($params['product']);exit;
        return $params;
    }


    function managetemplates($param)
    {
        $request = service('request');
        $result = $request->getVar();

        // $img = $request->getFile('banner');
        $img1 = $request->getFile('banner1');

        // var_dump($result,$img,$img1);exit;

        if (empty($img1)) {
            // echo 'EMPTY';
            $errors = array(
                'success' => 1,
                'message' => 'Success',
                'redirect' => front_link(111)
            );
            // $sql = "UPDATE tb_banner SET img = '".$result['first_name']."', last_name = '".$result['last_name']."', company = '".$result['company']."', gender = '".$result['flexRadioDefault']."', email = '".$result['email']."', phone = '".$result['phone']."' WHERE id = ".$_SESSION['user_id']."";

            // $data_res = $this->dao->execDatas($sql);

            // if($data_res){
            //     // SendEmail($result);
            //     $errors = array(
            //                 'success' => 1,
            //                 'message' => 'Success',
            //                 'redirect' => front_link(111)
            //             );
            //     echo json_encode( $errors );
            // }else{
            //         $errors = array(
            //         'success' => 0,
            //         'message' => 'ERROR'
            //     );
            //     echo json_encode($errors);
            // }

        } else {
            //echo 'NOT EMPTY';
            $Newname = $img1->getRandomName();
            $img1->move('uploads', $Newname);
            $sql = "INSERT INTO tb_banner (img) VALUES ('" . $Newname . "')";

            $data_res = $this->dao->execDatas($sql);

            if ($data_res) {
                // SendEmail($result);
                $errors = array(
                    'success' => 1,
                    'message' => 'Success',
                    'redirect' => front_link(111)
                );
                echo json_encode($errors);
            } else {
                $errors = array(
                    'success' => 0,
                    'message' => 'ERROR'
                );
                echo json_encode($errors);
            }
        }
    }

    function get_newdetail($param = array())
    {
        // arr($params);exit;
        $sql = "SELECT * FROM tb_news LEFT JOIN aa_users ON tb_news.user_id = aa_users.id WHERE tb_news.id = 1";
        $news = array();


        foreach (getDb()->fetchAll($sql) as $key => $val) {
            $news[] = $val;
        }

        $params['news'] = $news;

        // arr($params['product']);exit;
        return $params;
    }

    public function catagory_page($params)
    {
        //echo 'sdfaafsdas';




        if ($params['id'] == 123) {

            //arr($params['myid']);

            $params['filters']['WHERE'][] = "tag LIKE '%" . str_replace('', '', $params['myid'])  . "%'";
            //$params['cId'] = $result['catId'];

            return $this->dashboardNews($params);
        } else {

            $params['filters']['WHERE'][] = 'tb_catalog.id = ' . $params['myid'] . '';

            $params['cId'] = $params['myid'];

            return $this->dashboardNews($params);
        }
    }

    public function getLoadNewsPage($params = array())
    {
        $pager = \Config\Services::pager();

        $record = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 0;

        $pages = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;

        $recordPerPage = 1;
        if ($record != 0) {
            $record = $record - 1 * $recordPerPage;
        }

        $sqlCount = "SELECT count(*) as allcount FROM tb_news";

        foreach ($this->dao->fetchAll($sqlCount) as $k => $v) {
            $recordCount = $v->allcount;
        }

        $sql = "
        SELECT
        n.*, ef.img_path as img,c.name as cName
            FROM tb_news n
            LEFT JOIN tb_ecom_file ef ON ef.file_ref_id = n.id AND ef.tb_name = 'tb_news'
            INNER JOIN tb_catalog c ON c.id = n.cat_id
            WHERE c.cat_type = 1
                AND n.status_id = 1
                AND n.release_time <b NOW()
            GROUP BY n.id
            ORDER BY
                n.show_at_first ASC,
                n.time_update DESC
            LIMIT 0,4 ";

        //LIMIT $record,$recordPerPage ";
        // GROUP BY tb_ecom_file.img_path

        $hNPagination = array();
        $hNPagi_dateTime = array();
        $now = date('Y-m-d H:i:s');



        foreach ($this->dao->fetchAll($sql) as $kdata => $vdata) {
            $img = 'front/assets/no-image-icon-15.png';

            if (!empty($vdata->img)) {
                $img = $vdata->img;
            }

            $hNPagination[] = '

                    <div class="card-body mobile-bg " id="NewsLastestList" style="--bs-gutter-x: 0;">
                        <a href="' . newsLink($vdata->id) . '">
                            <div class="container">
                                <img src="' . $img . '" class="img-fluidd" alt="' . $vdata->name . '" style="height: 344px; border-radius: 20px;">
                            </div>
                            <div class="margin-tophead">
                                <h2 class="limit-line-custom">' . $vdata->name . '</h2>
                            </div>
                        </a>
                    </div>





            ';

            // $hNPagi_dateTime[] = getDayHour($vdata->time_update, $now);
        }

        // <button type="button" class="btn text-warning">1</button>
        //                 <button type="button" class="btn">2</button>
        //                 <button type="button" class="btn">3</button>
        //                 <button type="button" class="btn">4</button>



        // $pager->setPath('getCount/getLoadNewsPage', 'page');
        // $data['pagination'] = $pager->makeLinks($pages, $recordPerPage, 4, 'custom_pagi', 0, 'page');
        // $data['NewsData'] = $hNPagination;
        // $data['dateTime'] = $hNPagi_dateTime;

        // echo $data['pagination'];
        // echo json_encode($data);

        return implode('', $hNPagination);
    }


    function checkSeach()
    {

        $filters = array();

        $search = !empty($_REQUEST['model_hotkey']) ? str_replace(' ', '%', $_REQUEST['model_hotkey']) : NULL;

        $arr = array('name', 'sub_name', 'titleshort');

        $keep = array();
        foreach ($arr as $ka => $va) {

            $keep[] = $va . " LIKE '%" . $search . "%'";
        }

        $filters['WHERE'][] = '(' . implode(' OR ', $keep) . ' )';
        $filters['WHERE'][] = 'active = 1';

        if ($_REQUEST['pageId'] == 1) {
            $links = "loadDataSeach/loadSeach";
        } else {
            $links = "loadSeach";
        }

        $sql = "

        SELECT id,name,sub_name,titleshort
        FROM tb_news
        [WHERE]
        ORDER BY
            tb_news.time_update ASC
        limit 0,1

		";
        $sql = genCond_($sql, $filters);

        //arr( $sql );
        foreach ($this->dao->fetchAll($sql) as $ka => $va) {

            //arr( $va );
            redirect(front_link($_REQUEST['pageId'], $links, array('newsId' => $va->id)));

            exit;
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function getLoadVdoSlide($params = array())
    {
        $pager = \Config\Services::pager();

        $record = !empty($_REQUEST['page_page']) ? $_REQUEST['page_page'] : 0;

        $recordPerPage = 1;
        if ($record != 0) {
            $record = $record - 1 * $recordPerPage;
        }

        $sqlCount = "SELECT count(*) as allcount FROM tb_vdos";

        foreach ($this->dao->fetchAll($sqlCount) as $k => $v) {
            $recordCount = $v->allcount;
        }

        $sql = "
            SELECT
				tb_vdos.*,
				c.name as catalogName,
				c.id as catalogId,
				c.cat_type,
				aa_users.first_name
			FROM tb_vdos
			INNER JOIN tb_catalog c ON c.id = tb_vdos.cat_id
			LEFT JOIN aa_users ON tb_vdos.user_id = aa_users.id
			WHERE c.cat_type NOT IN ( 10, 11 ) AND tb_vdos.release_time < NOW() AND tb_vdos.status_id = 1

			ORDER BY
				tb_vdos.time_update DESC
            LIMIT $record,$recordPerPage ";

        // GROUP BY tb_ecom_file.img_path
        // arr($sql);exit;
        $hNPagination = array();
        $hNPagi_dateTime = array();
        $now = date('Y-m-d H:i:s');
        foreach ($this->dao->fetchAll($sql) as $kdata => $vdata) {
            // var_dump($vdata);exit;
            $img = 'front/assets/no-image-icon-15.png';
            // var_dump($vdata);
            if (!empty($vdata->img)) {
                $img = $vdata->img;
            }

            if (true) {
                $ifrm1 = '
					 <iframe width="100%" height="448" src="' . $vdata->youtube_link . '" title="' . $vdata->name . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
				';
            } else {

                $ifrm1 = '
					<video style="width: 100%; background-color: black;" controls="" height="448" id="video' . $vdata->id . '">
						<source src="' . $vdata->upload_video . '" type="video/mp4">
						Your browser does not support HTML video.
					</video>
				';
            }

            $hNPagination[] = '<div>
					<div class="container">
					' . $ifrm1 . '
					</div>
					<div class="text-center mt-5">
						<h5>' . $vdata->name . '</h5>
					</div>
				</div>
            ';
            //
            $hNPagi_dateTime[] = getDayHour($vdata->time_update, $now);
        }

        // <button type="button" class="btn text-warning">1</button>
        //                 <button type="button" class="btn">2</button>
        //                 <button type="button" class="btn">3</button>
        //                 <button type="button" class="btn">4</button>


        $pager->setPath('getCount/getLoadVdoSlide', 'page');
        $data['pagination'] = $pager->makeLinks(1, $recordPerPage, $recordCount, 'custom_pagi', 0, 'page');
        $data['NewsData'] = $hNPagination;
        $data['dateTime'] = $hNPagi_dateTime;
        echo json_encode($data);
    }


    function VideoHeader($params = array())
    {
        $hHtml = array();



        $sql = "
			SELECT
                tb_vdos.*,
                c.name as catalogName,
                c.id as catalogId,
                c.cat_type,

                aa_users.first_name
            FROM tb_vdos
            INNER JOIN tb_catalog c ON c.id = tb_vdos.cat_id
            LEFT JOIN aa_users ON tb_vdos.user_id = aa_users.id

            [WHERE]

            GROUP BY
                tb_vdos.id
            ORDER BY
				tb_vdos.time_update DESC
            LIMIT 0,4";


        $filters = array();
        $filters['WHERE'][] = "tb_vdos.status_id = 1";

        if ($params['id'] == 3) {
            $filters['WHERE'][] = "c.cat_type = 10";
        } else if ($params['id'] == 8) {
            $filters['WHERE'][] = "c.cat_type = 11";
        } else {
            $filters['WHERE'][] = "c.cat_type = 1";
        }


        $sql = genCond_($sql, $filters);

        date_default_timezone_set('Asia/Bangkok');
        // arr($sql);exit;


        $now = date('Y-m-d H:i:s');
        foreach ($this->dao->fetchAll($sql) as $k => $v) {
            // var_dump($v );
            $img = !empty($v->img_path) ? $v->img_path : 'front/assets/no-image-icon-15.png';
            $hHtml[] = '
                <div class="col-lg-6 col-sm-6 p-2">
                    <div class="card" style="height: 350px;">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#Modal' . $v->id . '">
                            <iframe width="100%" height="200" src="' . $v->youtube_link . '" title="' . $v->name . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            <div class="card-body">
                                <h5>' . $v->name . '</h5>
                                </div>
                        </a>
                        <div style="position: absolute;bottom: 15px;">

                            <span>' . getDayHour($v->time_update, $now) . '</span>
                            <a href="' . front_link(10, $v->cat_id) . '"><span class="text-danger">' . $v->catalogName . '</span></a>
                        </div>
                    </div>
				</div>

				<div class="modal fade" id="Modal' . $v->id . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<iframe width="100%" height="200" src="' . $v->youtube_link . '" title="' . $v->name . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
							</div>
						</div>
					</div>
				</div>
            ';
        }
        return implode("", $hHtml);
    }

    function contactUs()
    {

        $configs['email'] = array(
            'require' => 1,
            'format' => 'email'

        );
        $configs['name'] = array(
            'require' => 1
        );
        $configs['content'] = array(
            'require' => 1
        );


        $check_form = check_form($_REQUEST, $configs);

        if (!empty($check_form['errors'])) {

            $errors['success'] = 0;
            $errors['field'] = $check_form['errors'];
            $errors['message'] = 'กรุณากรอกข้อมูลให้ถูกต้อง และครบถ้วน';

            echo json_encode($errors);
            exit;
        }


        $sql = "INSERT INTO tb_contactus (name, email, messenger, datetime, status) VALUES ('" . $_REQUEST['name'] . "', '" . $_REQUEST['email'] . "', '" . $_REQUEST['content'] . "', now(), '1');";

        $res = $this->dao->execDatas($sql);

        if ($res) {
            $errors = array(
                'success' => 1,
                'message' => 'บันทึกข้อมูลเรียบร้อย'
                // 'redirect' => front_link(109)
            );
        } else {
            $errors = array(
                'success' => 0,
                'message' => 'ไม่สามารถบันทึกข้อมูลเรียบร้อย'

            );
        }

        echo json_encode($errors);
    }

    //
    //
    function getHistory($params = array())
    {
        $sql = "
        SELECT
            n.*,
            n.time_update,
            u.first_name,
            i.img_path
        FROM tb_news n

        INNER JOIN tb_catalog c ON n.cat_id = c.id
        LEFT JOIN tb_ecom_file i ON i.file_ref_id = n.id
        LEFT JOIN tb_history h ON h.tb_id = n.id AND h.tb_name = 'tb_news'
        LEFT JOIN aa_users u on u.id = h.user_id

        WHERE h.user_id = '" . $_SESSION['user_id'] . "'
        GROUP BY n.id
        ORDER BY h.time_update DESC
        ";

        $HisHtml = array();
        foreach ($this->dao->fetchAll($sql) as $k => $v) {
            // var_dump($v);
            $HisHtml[] = '

            <div class="col-md-4 col-12 mt-2">
                <a href="' . newsLink($v->id) . '">
                    <img src="' . $v->img_path . '" class="img-fluidd  " alt="...">
                    <div class="card-body">
                        <h5 class="limit-line">' . $v->name . '</h5>
                    </div>
                </a>
            </div>

            ';
        }
        $params['foryou'] = 'FOR YOU';
        $html['HisHtml'] = implode('', $HisHtml);
        $html['newsHighlight'] = $this->NewsHighlight();
        $html['forYou'] = $this->NewsInteres($params);

        return $html;
    }


    function radioPage($params = array())
    {

        $ajax = isset($_REQUEST['ajax']) ? $_REQUEST['ajax'] : false;

        if (isset($params['limit'])) {

            $sql = "
				SELECT *
				FROM tb_radios
				[WHERE]

				ORDER BY name ASC
				[LIMIT]
			";

            $filters['WHERE'][] = 'status_id = 1';
            $filters['LIMIT'] = $params['limit'];


            $sql = genCond_($sql, $filters);

            $radioHtml = array();

            $html['playRadio'] = '';
        } else {

            $sql = "
				SELECT
					r.*,
					(
						SELECT
							img_path
						FROM tb_ecom_file
						WHERE file_ref_id = r.id
						AND tb_name = 'tb_radios'
						ORDER BY
							time_update DESC
						LIMIT 0, 1
					) as img
				FROM tb_radios r
				[WHERE]

				ORDER BY
					r.name ASC,
                    r.time_update DESC

                LIMIT 0,9
			";

            $filters['WHERE'][] = 'r.status_id = 1';

            if ($ajax) {
                $filters['WHERE'][] = 'r.id = "' . $_REQUEST['data'] . '" ';
            }

            //  $filters['WHERE'][] = 'r.id = 50';


            $sql = genCond_($sql, $filters);

            $radioHtml = array();

            $html['playRadio'] = '';
        }


        $html['img'] = '';

        $returns = [];

        foreach ($this->dao->fetchAll($sql) as $ka => $va) {

            // if (!empty($params['myid'])) {

            //     $playId = $params['myid'];
            // } else if ($ka == 0) {

            //     $playId = $va->id;
            // }

            // if ($playId == $va->id) {

            if ($ajax) {
                $sources = '
                        <audio controls="" >
                            <source src="' . $va->source . '" type="audio/mp3">
                        </audio>
                    ';
                $returns = [
                    'errors' => false,
                    'radio_name' => $va->name,
                    'sources' => $sources
                ];
            }

            if (file_exists($va->img)) {

                $html['img'] = '

						<div class="container">
							<div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-12"></div>
								<div class="col-lg-6 col-md-6 col-sm-12">
									<a href="' . front_link($params['id'], $va->id) . '"><img src="' . $va->img . '" alt="" class="imgsec1"></a>
								</div>
                                <div class="col-lg-3 col-md-3 col-sm-12"></div>
							</div>
						</div>
					';
            }

            $autoplay = '';


            $autoplay = 'autoplay="0"';

            if ($ka == 0) {
                $html['playRadio'] = '
                        <div class="container tab-view-radio">
                            <div class="row">
                                <div class="col-xl-2 col-lg-2 col-md-12 col-12"></div>
                                <div class="col-xl-8 col-lg-8 col-md-12 col-12">
                                    <div class="radio-main">
                                        <div class="radio-dvd-main">
                                            <div class="radio-dvd-sub">
                                                <img src="front/assets/img/icon/dvd.png" alt="" class="radio-dvd-img">
                                            </div>
                                        </div>
                                        <div class="container-fluid justify-content-between radio-logo">
                                            <div class="d-flex logo-radio">
                                                <img src="front/assets/img/icon/live1.png" alt="" class="radio-logo-img">
                                                <span id="radios-text" style="margin-left: 5px;" class="radio-logo-span">' . $va->name . '
                                                </span>
                                            </div>
                                            <div id="source-radio" class="d-flex justify-content-end">
                                                <audio controls="" >
                                                    <source src="' . $va->source . '" type="audio/mp3">
                                                </audio>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-12 col-12"></div>
                            </div>
                        </div>
                        <br>
                    ';
            }


            // } else {

            if (file_exists($va->img)) {
                $radioHtml[] = '
						<div class="col-xl-4 col-lg-4 col-md-6 col-12">
							<div class="text-center mt-1">
								<button type="button" class="btn radiosbtn" data-radio="' . $va->id . '" ><img src="' . $va->img . '" alt="' . $va->name . '" class="logoradio"><b style="font-size: 20px;">' . $va->name . '</b></button>
							</div>
						</div>
					';
            } else {

                $radioHtml[] = '
						<div class="col-xl-4 col-lg-4 col-md-6 col-12">
							<div class="text-center mt-1">
								<button type="button" class="btn radiosbtn" data-radio="' . $va->id . '"><img src="front/assets/img/logoradio.png" alt="logoRadio" class="logoradio" ><b style="font-size: 20px;">' . $va->name . '</b></button>
							</div>
						</div>
					';
            }
            // }
        }

        if ($ajax) {
            echo json_encode($returns);
            exit;
        }


        $html['radioHtml'] = implode('', $radioHtml);

        return $html;
    }


    public function dashboardNews($params = array())
    {


        $sql = "
			SELECT
				new_tb.*
			FROM (
				(
					SELECT
						n.show_at_first,
						n.id as new_id,
						n.cat_id,
						n.release_time,
						n.tag, n.sub_cat_id, n.link,
						n.detail,
						n.name,
						n.sub_name,
						c.name as cat_name,
						c.order_number,
						c.cat_type,
						n.img,
						n.time_update,
						u.first_name as creator

					FROM tb_news n
					INNER JOIN aa_users u ON n.user_id = u.id
					INNER JOIN tb_catalog c ON n.cat_id = c.id
					WHERE c.cat_type = 1
					AND n.status_id = 1
					AND n.release_time < NOW()
					ORDER BY
						c.order_number asc,
						n.show_at_first ASC,

						time_update DESC

				)
				UNION
				(
					SELECT
						999 as show_at_first,
						c.id as new_id,
						c.id as cat_id,
						NULL as release_time,
						NULL as tag, NULL as sub_cat_id, NULL as link,
						NULL as detail,
						NULL as name,
						NULL as sub_name,
						name as cat_name,
						c.order_number,
						c.cat_type,
						null as img,
						c.time_update,
						NULL as creator

					FROM tb_catalog c
					WHERE c.cat_type IN ( 2, 3 )
					ORDER BY
						c.order_number ASC,

						time_update DESC
				)
			) as new_tb
            ORDER BY
                
                new_tb.order_number ASC,
                time_update DESC

		";

        //order_number ASC,
        //	release_time DESC

        $keep = array();
        foreach ($this->dao->fetchAll($sql) as $kn => $vn) {


            // arr( $vn );
            $keep[$vn->cat_id][] = $vn;
        }
        // exit;
        // var_dump($params);
        foreach ($keep as $kn => $vn) {

            if ($vn[0]->cat_type == 3) {

                $param['limit'] = 'LIMIT 0, 1';
                $radio = $this->radioPage($param);
                if ($params['id'] == 19) {
                    continue;
                } else {
                    $html['newdivs'][] = '' . $radio['playRadio'];
                }
            } else if ($vn[0]->cat_type == 2) {
            } else {

                $sub = array();
                $sub['main'] = '';
                $sub['right'] = array();
                foreach ($vn as $ks => $vs) {

                    $img = 'front/assets/img/2022-06-20_083218.jpg';
                    if (!empty($vs->img)) {
                        $img = $vs->img;
                    }

                    if ($ks == 0) {

                        $sub['main'] = '
							<div class="col-xl-7 col-lg-7 col-md-7 col-12">
								<div class="card-body" style="padding: 0; margin-bottom: 10px;">
                                <a href="' . newsLink($vs->new_id) . '">
									<div class="container " style="--bs-gutter-x:0;">
										<img src="' . $img . '" class="img-fluidd" alt="' . $vs->name . '">
									</div>
									<div class="margin-tophead">
										<p class="text-muted">' . dateOnly($vs->time_update) . '</p>
									<!--	<h3 class="mt-2"><a href="' . newsLink($vs->new_id) . '">' . $vs->name . '</a></h3>-->
                                    <p class="card-text limit-line text-muted my-min-height my-big-title" style="">
									<a href="' . newsLink($vs->new_id) . '"><b class="limit-line">' . $vs->name . '</b></a>
								</p>
									</div>
                                </a>
								</div>
							</div>
						';
                        //<p class="limit-line" style="-webkit-line-clamp: 6;">' . showParagraph($vs->detail) . '</p>
                    } else {

                        if (count($sub['right']) == 4) {

                            continue;
                        }

                        $sub['right'][] = '
							<div class="mb-3" ="">
								<div class="row g-0">

									<div class="col-md-6 col-4" style="--bs-gutter-x:0; ">
										<a href="' . newsLink($vs->new_id) . '">
											<img src="' . $img . '" class="img-fluidd imgSubRight" alt="' . $vs->name . '">
										</a>
									</div>
									<div class="col-md-6 col-8">

										<div style="padding-left: 10px" >

											<p class="text-muted">' . dateOnly($vs->time_update) . '<br>

												<a href="' . newsLink($vs->new_id) . '"><b class="limit-line-custom">' . $vs->name . '</b></a></p>
										</div>



									</div>

								</div>
							</div>
						';
                    }
                }

                // var_dump( $vn[0]);exit;
                $html['newdivs'][] = '
					<div class="container">
						<h2 style="font-weight: bold;margin-bottom: -5px;"><a href="' . front_link(4, $vn[0]->cat_id) . '">' . $vn[0]->cat_name . '</a></h2>
						<hr>
						<div class="row">' . $sub['main'] . '
							<div class="col-xl-5 col-lg-5 col-md-5 col-12 mobile-margin">' . implode('', $sub['right']) . '</div>
						</div>
                        <div class="container mobileview" style="text-align:center;">
                            <a href="' . front_link(4, $vn[0]->cat_id) . '" class="btn pr" style="color: black;">อ่านทั้งหมด</a>
                        </div>
                        <br>
					</div>

					<div class="container">
						<div class="text-center">
							<img src="front/asset/png/AdsBanner-option1.jpeg" alt="" class="text-center adsimg">
						</div>
					</div>
					<br><br>

				';
            }
        }



        $sql = "
			SELECT
				v.cat_id,
				c.name as cat_name
			FROM tb_vdos v
			INNER JOIN tb_catalog c ON v.cat_id = c.id


			WHERE v.release_time < NOW() AND v.status_id = 1
			AND c.cat_type NOT IN ( 10, 11 )
			GROUP BY
				v.cat_id
			ORDER BY cat_name ASC

		";

        $bt[0] = 'btn-danger';
        $bt[1] = 'btn-warning';
        $bt[2] = 'btn-success';
        $bt[3] = 'btn-primary';

        $keep = array();
        foreach ($this->dao->fetchAll($sql) as $ka => $va) {

            $btClass = $bt[($ka % 3)];

            if (true) {
                $keep[] = '';
            } else {
                $keep[] = '<a href="' . front_link(10, $va->cat_id) . '" class="btn ' . $btClass . ' vdo-bt" style="border-radius: 99px; float: right; margin-right: 5px;">' . $va->cat_name . '</a>';
            }
        }


        //     <div class="paginations" style="border-radius: 99px;float: right;margin-right: 5px;">
        //     <a href="#" class="a move-left" style="background-color: #FA9696;color: #FFF;">❮ </a>
        //     <a href="#" class="a move-right" style="background-color: #F65050;color: #FFF;">❯</a>
        // </div>
        $html['navVideo'] = '
			<div class="container">
				<h2 style="margin-bottom: -30px; float: left; font-weight: bold;">วิดีโอ </h2>

				<a href="' . front_link(10) . '" class="btn btn-danger" style="border-radius: 99px; float: right; margin-right: 5px;">ดูทั้งหมด</a>

				<div class="bt-vdo-content">

				' . implode('', $keep) . '
				</div>

				<script>
				$( function() {
					i = 0;
					n = $( \'.vdo-bt\' ).length - 1;
					$( \'.move-left\' ).click( function() {

						$( \'.vdo-bt\' ).eq( i ).appendTo( \'.bt-vdo-content\' );



						return false;
					});

					$( \'.move-right\' ).click( function() {

						$( \'.vdo-bt\' ).eq( n ).prependTo( \'.bt-vdo-content\' );



						return false;
					});


				});
				</script>



				<style>
					.paginations {
					display: inline-block;
					}

					.paginations .a {
					color: black;
					float: left;
					padding: 8px 16px;
					text-decoration: none;
					transition: background-color .3s;
					border: 1px solid #ddd;
					}

					.paginations .a.active {
					background-color: #4CAF50;
					color: white;
					border: 1px solid #4CAF50;
					}

					.paginations .a:hover:not(.active) {background-color: #ddd;}

                    @media (max-width: 575.98px) {
                        .bt-vdo-content {
                            display: none;
                        }
                     }
				</style>

			</div>
		';


        $sqlCard = "
        SELECT
        n.*, n.img,c.name as cName,c.types
            FROM tb_news n            
            INNER JOIN tb_catalog c ON c.id = n.cat_id
            WHERE c.types IN(1,3)
                AND n.status_id = 1
                AND n.release_time < NOW()
            GROUP BY n.id
            ORDER BY
                n.show_at_first ASC,
                n.time_update DESC,
                n.release_time DESC                              
            LIMIT 0,4     
            ";

        //LIMIT $record,$recordPerPage ";
        // GROUP BY tb_ecom_file.img_path asdadad

        $hNPagination = array();
        $now = date('Y-m-d H:i:s');


        $btnCas = array();
        foreach ($this->dao->fetchAll($sqlCard) as $kdata => $vdata) {
            $img = 'front/assets/no-image-icon-15.jpg';

            if (!empty($vdata->img)) {
                $img = $vdata->img;
            }

            $active = '';
            if ($kdata == 0) {
                $active = 'active';
            }
            $btnCas[] = '
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="' . $kdata . '" class="' . $active . '" aria-current="true" aria-label="Slide 1"></button>     
            ';


            if (isset($vdata->types) && $vdata->types == 3) {
                $hNPagination[] = '
            
                <div class="carousel-item ' . $active . '">             
                <a href="' . newsLink($vdata->id) . '" style="color: #000000;">
                    <img src="' . $img . '" class="d-block w-100" alt="' . $vdata->name . '">
                    <div class="carousel-inner d-md-block">
                        <h3 class="limit-line font-mobile">' . $vdata->name . '</h3>
                        <br>
                        <div class="headnews-carousel">
                            <span class="tagspan">' . $vdata->cName . '</span>
                            <label style="margin-top: 2px;">' . getDayHour($vdata->time_update, $now) . '</label>
                        </div>
                    </div>
                    </a>
                </div>
                
                ';
            } else {
                $hNPagination[] = '
            
                <div class="carousel-item ' . $active . '">
                <a href="' . newsLink($vdata->id) . '" style="color: #000000;">
                    <img src="' . $img . '" class="d-block w-100" alt="' . $vdata->name . '">
                    <div class="carousel-inner d-md-block">
                        <h3 class="limit-line font-mobile">' . $vdata->name . '</h3>
                        <br>
                        <div class="headnews-carousel">
                            <span class="tagspan">' . $vdata->cName . '</span>
                            <label style="margin-top: 2px;">' . getDayHour($vdata->time_update, $now) . '</label>
                        </div>
                    </div>
                    </a>
                </div>
                
                ';
            }
        }


        $html['paginationNews'] = implode('', $hNPagination);
        $html['btnCas'] = implode('', $btnCas);


        $html['headerNews'] = $this->headerNews($params);

        $html['newsHighlight'] = $this->NewsHighlight();



        // arr($html);exit;
        return $html;
    }


    function headerNews($params = array())
    {

        date_default_timezone_set('Asia/Bangkok');

        $hHtml = array();


        $sql = "
            SELECT
                tb_news.*,
				c.name as cat_name,
				tb_news.img
            FROM tb_news
            INNER JOIN tb_catalog c ON tb_news.cat_id = c.id
			WHERE tb_news.status_id = 1
			AND tb_news.release_time < NOW()
            ORDER BY
                tb_news.time_update DESC,
				show_at_first ASC
				
            LIMIT 4,4
		";

        $now = date('Y-m-d H:i:s');

        foreach ($this->dao->fetchAll($sql) as $k => $v) {
            $img = !empty($v->img) ? $v->img : 'front/assets/no-image-icon-15.png';
            $hHtml[] = '
            <div class="col-xl-6 col-lg-6 col-md-12 col-12 mb-2">
                <div class="card h-100">
                        <div>
							<a href="' . newsLink($v->id) . '">
                            <img src="' . $img . '" class="card-img-top img-fluiddd" >
							</a>
                        </div>
						<div class="card-body px-2">
                            <p class="card-text limit-line-newscol text-muted my-min-height" style="">
								<a href="' . newsLink($v->id) . '"><b>' . $v->name . '</b></a>
							</p>
                            <div style="margin-top: 10px;">
                                <span style="color:#707a89; font-size: 12px; margin-right: 10px;">' . getDayHour($v->time_update, $now) . '</span>

                                <a href="' . front_link(4, $v->cat_id) . '">
								<span style="font-size: 12px; font-weight: bold;">' . $v->cat_name . '</span>

								</a>
                            </div>
						</div>

                </div>
            </div>
            ';
        }
        return implode("", $hHtml);
    }

    function NewsHighlight()
    {



        $sqlCountRead = "
			SELECT
				tb_news.*,
				tb_news.img as imgPath,
				u.first_name as creator,
                SUBSTRING(tb_news.release_time, 1, 10) as dateLasted
            FROM tb_news
            LEFT JOIN tb_ecom_file ON tb_ecom_file.file_ref_id = tb_news.id
            LEFT JOIN aa_users u ON u.id = tb_news.user_id

			WHERE tb_news.status_id = 1
			AND tb_news.release_time <= NOW()

            GROUP BY tb_news.id
            ORDER BY dateLasted DESC,tb_news.countRead DESC
            LIMIT 0,5
		";

        // arr($sqlCountRead);
        // exit;


        $CountRead = $this->dao->fetchAll($sqlCountRead);

        $newsHighlights = array();
        foreach ($CountRead as $kcr => $vcr) {

            $img =  !empty($vcr->imgPath) ? $vcr->imgPath : 'front/assets/no-image-icon-15.png';

            $newsHighlights[] = '
            <div class="mb-3" >
                <div class="row g-0">
                    <div class="col-md-6">
					<a href="' . newsLink($vcr->id) . '">
                        <img src="' . $img . '" class="img-fluidd  " alt="' . $vcr->name . '"></a>
                    </div>
                    <div class="col-md-6">
                            <div class="card-body padingpagenew">

								<p class="text-muted control-p">' . dateOnly($vcr->release_time) . '</p>



                            <p class="card-text text-muted my-min-height">
								<a href="' . newsLink($vcr->id) . '"><span class="limit-line-custom1" style="font-weight:bold;">' . $vcr->name . '</span></a>
							</p>


                            </div>

                    </div>
                </div>
            </div>
            ';
        }

        return implode("", $newsHighlights);
    }

    public function functest($params){
        $sql = "
        SELECT text1 FROM `tb_policy` WHERE `id`= 1;";
        
        $params['html'] = htmlspecialchars_decode($this->dao->fetchAll($sql)[0]->text1);
        return $params;
    }

    public function newsDetail($params)
    {

        $id = !empty($_REQUEST['newsId']) ? $_REQUEST['newsId'] : $params['myid'];
        $request = service('request');
        $result = $request->getVar();

        $sql = "
			SELECT
                tb_news.*,

				tb_ecom_file.img_path as imgPath,
                aa_users.first_name,
				aa_users.last_name,
				tb_catalog.name as catName,
                aa_users.id as userId,
                tb_catalog.id as catID,
				sc.name as subCatName
			FROM tb_news
            LEFT JOIN tb_ecom_file ON tb_ecom_file.file_ref_id = tb_news.id AND tb_ecom_file.tb_name = 'tb_news'
            INNER JOIN tb_catalog ON tb_catalog.id = tb_news.cat_id
            LEFT JOIN tb_sub_catalog sc ON tb_news.sub_cat_id = sc.id
            LEFT JOIN aa_users ON aa_users.id = tb_news.user_id
			WHERE tb_news.id=" . $id . "
		";

        $html = [];
        $imgSwiper = array();
        $ImgSwiperSide = array();
        $zoomImgSwiper = array();
        $html['tag'] = '';
        $html['sub_name'] = '';
        $html['tags'] = '';
        $html['catName'] = '';
        $html['des'] = '';

        $html['breadCum'] = '';

        foreach ($this->dao->fetchAll($sql) as $k => $v) {

            $html['sub_name'] =  '<meta name="Description" content="' . $v->sub_name . '">';
            $html['sub_title'] =  '<p><b>' . $v->sub_name . '</b><p>';


            $sql = "
				UPDATE
					tb_catalog
				SET count = count + 1
				WHERE id = " . $v->cat_id . "
			";

            $this->dao->execDatas($sql);


            /*$logs = "SELECT * FROM tb_log_count WHERE ref_id = '".$v->id."' AND ref_tb='tb_news' ";

            if(empty(getDb()->fetchAll($logs))){
                $inserts = [
                    'countRead' => 1,
                    'ref_id' => $v->id,
                    'ref_tb' => "tb_news"
                ];

                getDb()->insert_('tb_log_count',$inserts);
             
            }else{
              
                $sql = "
                    UPDATE
                        tb_log_count
                    SET countRead = countRead + 1
                    WHERE ref_id = " . $v->id . "
                ";

                getDb()->execDatas($sql);
            }*/

            $sql = "
				UPDATE
					tb_news
				SET countRead = countRead + 1
				WHERE id = " . $v->id . "
			";


            getDb()->execDatas($sql);
            if (isset($_SESSION['user_id'])) {
                $sqlHistory = "
					INSERT INTO tb_history ( user_id, tb_id )
					VALUES (" . $_SESSION['user_id'] . ", " . $v->id . ");
                ";

                $this->dao->execDatas($sqlHistory);
            }



            $tag = htmlspecialchars_decode($v->tag);
            $tag = str_replace('#', ' ', $tag);
            $tag = trim($tag);


            //	arr( $tag );

            $keep = array();

            foreach (explode(' ', $tag) as $ke => $ve) {


                $sub = str_replace('#', '', $ve);

                if ($sub == '#') {
                    continue;
                }
                if (empty($sub)) {
                    continue;
                }

                $keep[] = '<a class="btn btn-tages" style="margin: 3px;" href="' . front_link(123, $sub) . '">#' . $ve . '</a>';
            }


            $keepd = array();

            foreach (explode(' ', $tag) as $ke => $ve) {
                $sub = str_replace('#', '', $ve);

                $keepd[] = '' . $sub . '';
            }

            $html['tag'] = '<meta name="Keywords" content="' . implode(',', $keepd) . '" />';



            $html['tags'] = '
				<div class="pt-2 pb-2">
					<button type="button" class="btn btn-tages">TAGS:</button>
					' . implode(' ', $keep) . '
				</div>
			';


            $img =  !empty($v->img) ? $v->img : 'front/assets/no-image-icon-15.jpg';
            $html['title'] = $v->name;
            $html['des'] = $v->detail;
            $html['createBy'] = $v->first_name . ' ' . $v->last_name;
            $html['dateRelease'] = $v->release_time;
            $html['catName'] = $v->catName;
            $html['catID'] = $v->catID;

            $imgSwiper = '
                <img src="' . $img . '" draggable="false" ondragstart="return false;">
            ';

            $ImgSwiperSide[] = '
				<div class="swiper-slide">
					<div class="swiper-zoom-container">
						<img src="' . $img . '">
					</div>
				</div>
            ';

            $zoomImgSwiper[] = '
				<div class="swiper-slide">
					<div class="swiper-zoom-container">
						<img src="' . $img . '" draggable="false" ondragstart="return false;">
					</div>
				</div>
            ';


            $keep = array();

            $keep[] = '<a href="' . front_link(1) . '">หน้าแรก</a>';

            $page_id = 4;

            if ($v->cat_id == 10) {
                $page_id = 3;
            } else if ($v->cat_id == 11) {
                $page_id = 8;
            }


            $keep[] = '<a href="' . front_link($page_id, $v->cat_id) . '">' . $v->catName . '</a>';

            if (!empty($v->subCatName)) {
                $keep[] = '<a href="' . front_link($page_id, $v->cat_id . '/' . $v->sub_cat_id . '') . '">' . $v->subCatName . '</a>';
            }

            // $keep[] = $v->name;
            //sc.name as subCatName

            $html['breadCum'] = '<p class="">' . implode(' &gt; ', $keep) . '</p>';
        }

        $html['ImgUrl'] = base_url($img);
        $html['imgSwiper'] = $imgSwiper;
        $html['ImgSwiperSide'] = implode("", $ImgSwiperSide);
        $html['zoomImgSwiper'] = implode("", $zoomImgSwiper);

        $html['newsHighlight'] = $this->NewsHighlight();


        $html['NewsInteres'] = $this->NewsInteres();




        return $html;
    }


    function summartNews($params)
    {
        date_default_timezone_set('Asia/Bangkok');
        $htmlAllNews = array();

        $sql = "
			SELECT n.*,n.id as new_id				
			FROM tb_news n
			INNER JOIN tb_catalog c ON c.id = n.cat_id
			WHERE n.release_time < NOW() AND c.types != 3
			ORDER BY n.time_update DESC
			LIMIT 0,10
        ";
        $res = $this->dao->fetchAll($sql);


        $htmlAllNews['main'] = [];
        $htmlAllNews['sub'] = [];

        foreach ($res as $k => $v) {

            $img = 'front/assets/no-image-icon-15.jpg';
            if (!empty($v->img)) {
                $img = $v->img;
            }


            if ($k == 0) {
                $htmlAllNews['main'][] = '
					<div class="col-md-12 col-12 mt-2">
	                <a href="' . newsLink($v->new_id) . '">
						<img src="' . $img . '" class="img-fluidd" alt="' . $v->name . '">

                        </a>
						<div class="card-title mt-2">
							<p class="text-muted">' . dateOnly($v->time_update) . '</p>

								<p class="card-text my-big-title limit-line text-muted my-min-height " style="">
									<a href="' . newsLink($v->new_id) . '"><b class="limit-line">' . $v->name . '</b></a>
								</p>
						</div>
					</div>

				';
            } else {
                $htmlAllNews['sub'][] = '
					<div class="col-md-4 col-12 mt-2">
                        <a href="' . newsLink($v->new_id) . '">
						    <img src="' . $img . '" class="imgfluidd  " alt="' . $v->name . '">
                        </a>
						<div class="card-title mt-2">

                            <p class="text-muted">' . dateOnly($v->time_update) . '</p>

                            <p class="card-text limit-line-newscol text-muted my-min-height" style="">
								<a href="' . newsLink($v->new_id) . '"><b>' . $v->name . '</b></a>
							</p>


						</div>
					</div>
                ';
            }
        }

        $html['HmainNews'] = implode('', $htmlAllNews['main']);
        $html['SmainNews'] = implode('', $htmlAllNews['sub']);

        $sqlAllNewsCat = "
                SELECT
                new_tb.*
            FROM (
                (
                    SELECT
                        n.show_at_first,
                        n.id as new_id,
                        n.cat_id,
                        n.release_time,
                        n.tag, n.sub_cat_id, n.link,
                        n.detail,
                        n.name,
                        n.sub_name,
                        c.name as cat_name,
                        c.order_number,
                        c.cat_type,
                        n.img,
                        n.time_update,
                        n.status_id,
                        u.first_name as creator

                    FROM tb_news n
                    INNER JOIN aa_users u ON n.user_id = u.id
                    INNER JOIN tb_catalog c ON n.cat_id = c.id
                    WHERE c.cat_type = 1
                    AND n.status_id = 1
                    AND n.release_time < NOW()
                    ORDER BY
                        c.order_number asc,
                        n.show_at_first ASC,

                        time_update DESC

                )
                UNION
                (
                    SELECT
                        999 as show_at_first,
                        c.id as new_id,
                        c.id as cat_id,
                        NULL as release_time,
                        NULL as tag, NULL as sub_cat_id, NULL as link,
                        NULL as detail,
                        NULL as name,
                        NULL as sub_name,
                        name as cat_name,
                        c.order_number,
                        c.cat_type,
                        null as img,
                        null as status_id,
                        c.time_update,
                        NULL as creator

                    FROM tb_catalog c
                    WHERE c.cat_type IN ( 2, 3 )
                    ORDER BY
                        c.order_number ASC,

                        time_update DESC
                )
            ) as new_tb
            WHERE new_tb.status_id = 1
            ORDER BY
                time_update DESC,
                new_tb.order_number ASC
                


        ";
        $keep = array();
        foreach ($this->dao->fetchAll($sqlAllNewsCat) as $kc => $vc) {
            $keep[$vc->cat_id][] = $vc;
        }

        foreach ($keep as $kn => $vn) {
            if ($vn[0]->cat_type == 3) {
                continue;
            } else if ($vn[0]->cat_type == 7) {
            } else

            if ($vn[0]->cat_type == 2) {
            } else {



                $subCat = array();

                foreach ($vn as $vk => $vv) {
                    $img = 'front/assets/no-image-icon-15.jpg';
                    if (!empty($vv->img)) {
                        $img = $vv->img;
                    }

                    if (count($subCat) == 4) {
                        continue;
                    } else if ($vk == 0) {
                        $subCat[] = '
                        <div class="">
                            <div class="mb-2">
								<a href="' . newsLink($vv->new_id) . '">
                                <img src="' . $img . '" class="img-fluidd"></a>

                                    <div class="card-title mt-2">
                                        <p class="text-muted">' . dateOnly($vv->time_update) . '</p>
                                       <!-- <h5 class="limit-line" style="font-weight: bold;"><a href="' . newsLink($vv->new_id) . '">' . $vv->name . '</a></h5>-->
                                    
										<p class="card-text limit-line-newscol text-muted my-min-height">
									        <a href="' . newsLink($vv->new_id) . '"><b>' . $vv->name . '</b></a>
								        </p>
                                    </div>
                            </div>
                        </div>

                        ';
                    } else {
                        $subCat[] = '
                        <div class="">
                            <div class="row">
                                <div class="col-md-6 col-6 pad-mobile1" >
								<a href="' . newsLink($vv->new_id) . '">
                                    <img src="' . $img . '" class="img-fluidd subImg  "  alt="...">
                                </a>
								</div>
                                <div class="col-md-6 col-6 pad-mobile2" style="--bs-gutter-x: 0rem;">
                                    <div class="card-title">
                                        

                                        <p class="text-muted">' . dateOnly($vv->time_update) . '</p>

					            <p class="card-text limit-line-newscol text-muted my-min-height" style="">
									<a href="' . newsLink($vv->new_id) . '"><b style="padding-right: 20px;">' . $vv->name . '</b></a>
								</p>


                                    </div>
                                </div>
                            </div>
                        </div>


                        ';
                    }
                }
            }
            //mobileview
            $html['catMain'][] = '
            <div class="col-md-4 col-12">
                <div class="card h-100" style="border: 0px">
                    <div class="card-body card-moblie">

                        <div class="mb-2" style="border-bottom: 2px solid #e5e5e5; border-top: 2px solid #e5e5e5; padding-top: 10px;">
                            <a href="' . front_link(4, $vn[0]->cat_id) . '"><h2 class="text-dark" style="font-weight: bold;">' . $vn[0]->cat_name . '</h2></a>
                        </div>
                        
                            ' . implode('', $subCat) . '
                    </div>
                    <div style="text-align:center; padding: 1rem 1rem;">
                        <a href="' . front_link(4, $vn[0]->cat_id) . '" class="btn pr" style="color: black;">อ่านทั้งหมด</a>
                    </div>
                </div>

            </div>


            ';
        }
        // arr($keep);
        // exit;
        $html['subCatAllNews'] = implode('', $subCat);
        $html['newsHighlight'] = $this->NewsHighlight();

        return $html;
    }



    //
    //
    function loadSeach($params = array())
    {

        if (empty($_REQUEST['model_hotkey']) && empty($_REQUEST['term'])) {

            header('Location:' . comeBack());

            exit;
        }

        if (!empty($_REQUEST['model_hotkey'])) {

            $link = front_link(20, NULL, array('model_hotkey' => $_REQUEST['model_hotkey']));

            header('Location:' . $link);

            exit;
        } else {

            $filters = array();

            $search = !empty($_REQUEST['term']) ? str_replace(' ', '%', $_REQUEST['term']) : NULL;

            $arr = array('name', 'sub_name', 'titleshort');

            $keep = array();

            foreach ($arr as $ka => $va) {

                $keep[] = $va . " LIKE '%" . $search . "%'";
            }

            $filters['WHERE'][] = '(' . implode(' OR ', $keep) . ' )';
            $filters['WHERE'][] = 'active = 1';

            if (@$_REQUEST['pageId'] != 1) {
                $links = "";
            } else {
                $links = "newDetail";
            }

            $seachSql = "
				SELECT id,name,sub_name,titleshort
				FROM tb_news
				[WHERE]
				ORDER BY
					tb_news.time_update ASC
				";


            $seachSql = genCond_($seachSql, $filters);

            $rows = array();
            foreach ($this->dao->fetchAll($seachSql) as $ka => $va) {

                $rows[] = array(
                    'label' => $va->name,
                    'link' => newsLink($va->id)
                );
            }

            echo json_encode($rows);
        }
    }


    //
    //
    function getSearch($params = array())
    {

        $sql = "
			SELECT
				new_tb.*
			FROM (
				(
					SELECT

						n.id as new_id,
						n.cat_id,
						n.release_time,
						n.tag,
						n.sub_cat_id,
						n.link,
						n.detail,
						n.name,
						n.sub_name,
						c.name as cat_name,
						c.order_number,
						c.cat_type,
						n.img,
						n.time_update
					FROM tb_news n
					INNER JOIN tb_catalog c ON n.cat_id = c.id
					[WHERE]

				)

			) as new_tb
			ORDER BY

				time_update DESC
		";

        $filters = array();

        if (!empty($_REQUEST['model_hotkey'])) {


            $filters['WHERE'][] = "n.name LIKE '%" . str_replace(' ', '%', $_REQUEST['model_hotkey'])  . "%'";
        }

        $filters['WHERE'][] = "n.status_id = 1 AND n.release_time < NOW()";

        $data['perPage'] = 7;

        $data['sql'] = genCond_($sql, $filters);

        $test = createPage($data, true);

        $keep = array();

        foreach ($test['rows'] as $kn => $vn) {

            $keep[999][] = $vn;
        }


        $sub = array();
        $sub['main'] = '';
        $sub['right'] = array();
        foreach ($test['rows'] as $ks => $vs) {

            $img = 'front/assets/no-image-icon-15.jpg';

            if (!empty($vs->img)) {
                $img = $vs->img;
            }

            $sub['right'][] = '
                <style>
                    .limit-line-search {
                        overflow: hidden;
                        display: -webkit-box;
                        -webkit-box-orient: vertical;
                        -webkit-line-clamp: 2;
                        font-weight: bold;
                        line-height: 28px;
                    }
                </style>
				<div class="my-search-grid" >
					<div class="">
						<img src="' . $img . '" class="img-fluidd  " style="widght: 100%; " alt="' . $vs->name . '">
					</div>
					<div class="card-body" style="padding: 0px 0px 0px 15px;">
						<div class="text-muted">' . dateOnly($vs->time_update) . '</div>
						<h5 class="limit-line-search"><a href="' . newsLink($vs->new_id) . '">' . $vs->name . '</a></h5>
						<div class="card-text limit-line-search" style="">' . strip_tags(html_entity_decode($vs->detail))  . '</div>

					</div>
				</div>
			';
        }

        $json_encode['html'] = implode('<br>', $sub['right']);

        if (isset($_REQUEST['ajax'])) {
            $json_encode['html'] = '<br>' . $json_encode['html'];

            echo json_encode($json_encode);

            exit;
        }

        $test['load_url'] = front_link($params['id'], NULL, array(), false);

        $html['newdivs'] = array();
        $html['newdivs'][] = '
			<div class="load-more">' . $json_encode['html'] . '</div>

			<script>
			$( function(){
				myData = ' . json_encode($test) . ';
				q = ' . json_encode($_REQUEST) . ';
				q.ajax = 1;
				q.page = 2;
				q.' . get_token('name') . ' = \'' . get_token('val') . '\';
				loading = 0;
				$( window ).scroll( function(){

					if ( $( window ).scrollTop() > ( $( document ).height() - $( window ).height() ) * 9 / 10 ) {
						if( q.page > myData.maxPage ) {
							return false;
						}

						if( loading == 0 ) {
							loading = 1;
						}
						else {
							return false;
						}

						$.post( myData.load_url, q, function( res ){

							data = JSON.parse( res );

							$( data.html ).appendTo( \'.load-more\' );

							q.page += 1;

							loading = 0;
						});

						return false;
					}

				});

			});
			</script>

			<style>
			.my-search-grid{

				display: grid; grid-template-columns: auto 80%; justify-content: space-between;
			}
			.line-clamp-full{
				-webkit-line-clamp: 2;
			}
			.line-clamp-header{
				-webkit-line-clamp: 2;
			}


			@media screen and ( max-width: 992px ){
				.my-search-grid {
					display: grid;
					grid-template-columns: auto 64%;
					justify-content: space-between;
				}
				.line-clamp-full{
					-webkit-line-clamp: 2;
				}
				.line-clamp-header{
					-webkit-line-clamp: 1;
				}
			}
			</style>
		';

        $html['cat_name'] = 'คำค้นหาข่าว "ทั้งหมด"';
        if (!empty($_REQUEST['model_hotkey'])) {

            $html['cat_name'] = 'คำค้นหาข่าว "' . $_REQUEST['model_hotkey'] . '"';
            $filters['WHERE'][] = "n.name LIKE '%" . str_replace('', '', $_REQUEST['model_hotkey'])  . "%'";
        }


        $html['myContent'] = '
			<div class="container mt-2">
				<h1 class="text-dark">' . $html['cat_name'] . '</h1>

				<hr>

				' . implode('<br>', $html['newdivs']) . '
			</div>
		';

        return $html;
    }


    function getAboutUs($params = array())
    {




        $html['html'] = '';

        $sql = "
			SELECT
				*
			FROM tb_about_us
		";

        foreach ($this->dao->fetchAll($sql) as $ka => $va) {
            $html['html'] = '' . htmlspecialchars_decode($va->text1) . '';
        }




        return $html;
    }


    function policy_html($params = array())
    {

        $html['html'] = '';

        $sql = "
			SELECT
				*
			FROM tb_policy
		";

        foreach ($this->dao->fetchAll($sql) as $ka => $va) {
            $html['html'] = '' . htmlspecialchars_decode($va->text1) . '';
        }

        return $html;
        return array();
    }


    function NewsInteres($params = array())
    {

        $sqlCountRead = "
			SELECT
				tb_news.*,
				tb_news.img as imgPath,
				concat( u.first_name, ', ', date_format( tb_news.time_update, '%d %M %Y' ) ) as creator
            FROM tb_news
            LEFT JOIN tb_ecom_file ON tb_ecom_file.file_ref_id = tb_news.id
            LEFT JOIN aa_users u ON u.id = tb_news.user_id

			WHERE tb_news.status_id = 1
			AND tb_news.release_time < NOW()
			AND tb_news.cat_id = 15

		";

        $CountRead = $this->dao->fetchAll($sqlCountRead);

        $newsHighlight = array();
        foreach ($CountRead as $kcr => $vcr) {

            //	arr( $vcr );

            $img =  !empty($vcr->imgPath) ? $vcr->imgPath : 'front/assets/no-image-icon-15.png';

            $newsHighlight[] = '
            
				<div class="col-xl-4 col-lg-4 col-md-6 col-12">
					<!-- card เล็ก -->
                    <a href="' . newsLink($vcr->id) . '">
                        <div class="card subImgs">
                            <img class="subImgs  " src="' . $img . '" alt="Card image">
                        </div>

                    
                        <div class="card-body" style="padding: 1.5rem 0rem 0.5rem 0rem;">
						    <p class="text-muted" style="margin-bottom:0px;">' . dateOnly($vcr->time_update) . '</p>

                            <h6 class="limit-line-newscol" style="font-weight:bold;">' . $vcr->name . ' </h6>

                        </div>
                        </a>
					<!-- card เล็ก -->
				</div>
            



            ';
        }
        if (empty($newsHighlight)) {
            return NULL;
        }

        if (isset($params['foryou'])) {
            $text = '<h2 style="text-align: center;">' . $params['foryou'] . '</h2>';
        } else {
            $text = '<h4 style="font-weight: bold;text-align: center; color:black;">ข่าวที่น่าสนใจ</h4>';
        }

        return '

			<div class="container pt-4 pb-3">
				' . $text . '


				<div class="container">
					
						<hr class="pagealine ">

                        <div class="row">
						' . implode('',  $newsHighlight) . '

                        </div>


					</div>
				</div>


			</div>
		';
    }


    //
    //
    function getHashtag($params = array())
    {

        $sql = "
			SELECT
				new_tb.*
			FROM (
				(
					SELECT

						n.id as new_id,
						n.cat_id,
						n.release_time,
						n.tag,
						n.sub_cat_id,
						n.link,
						n.detail,
						n.name,
						n.sub_name,
						c.name as cat_name,
						c.order_number,
						c.cat_type,
						n.img,
						n.time_update
					FROM tb_news n
					INNER JOIN tb_catalog c ON n.cat_id = c.id
					[WHERE]

				)
				UNION
				(
					SELECT
						NULL as new_id,
						c.id as cat_id,
						NULL as release_time,
						NULL as tag,
						NULL as sub_cat_id,
						NULL as link,
						NULL as detail,
						NULL as name,
						NULL as sub_name,
						name as cat_name,
						99999 as order_number,
						c.cat_type,
						null as img,
						c.time_update
					FROM tb_catalog c
					WHERE c.cat_type IN ( 2 )

				)
			) as new_tb
			ORDER BY
				order_number ASC,
				time_update DESC
		";

        $filters = array();

        if ($params['id'] == 3) {

            $html['cat_name'] = $params['title'];

            $filters['WHERE'][] = "n.cat_id = 10";
        } else if ($params['id'] == 8) {

            $html['cat_name'] = $params['title'];

            $filters['WHERE'][] = "n.cat_id = 11";
        } else if ($params['id'] == 19) {
            $html['cat_name'] = $params['title'];
            $filters['WHERE'][] = "n.cat_id != 11 AND n.cat_id != 10";
        } else {

            if (!empty($params['parent_id'])) {

                $filters['WHERE'][] = "n.sub_cat_id = '" . $params['parent_id'] . "' ";
            } else {
                if (!empty($_REQUEST['model_hotkey'])) {


                    $filters['WHERE'][] = "n.name LIKE '%" . str_replace(' ', '%', $_REQUEST['model_hotkey'])  . "%'";
                } else {

                    if ($params['id'] == 4) {


                        if (empty($params['myid'])) {

                            $html['cat_name'] = 'dfdfafddfs';
                        } else {

                            $filters['WHERE'][] = "n.cat_id = '" . $params['myid'] . "' ";

                            $html['cat_name'] = $params['myid'];
                        }
                    } else {

                        $filters['WHERE'][] = "REPLACE( n.tag, '#', ' ' ) LIKE '%" . str_replace('#', '%', $params['myid'])  . "%'";

                        $html['cat_name'] = '#' . $params['myid'];
                    }
                }
            }
        }

        $filters['WHERE'][] = "n.status_id = 1 AND n.release_time < NOW()";

        $data['perPage'] = 7;

        $data['sql'] = genCond_($sql, $filters);

        // arr($data['sql']);
        $test = createPage($data, true);

        $keep = array();

        foreach ($test['rows'] as $kn => $vn) {

            $keep[$vn->cat_type][] = $vn;
        }

        $html['newdivs'] = array();

        foreach ($keep as $kn => $vn) {

            if ($kn == 2) {

                $html['newdivs'][] = '
					<div class="container mt-4 mt-2">
						<div class="col pt-5 text-center">
							<img src="front/asset/png/ads.png" alt="" class="w-100 text-center">
						</div>
					</div>
				';
            } else {

                if ($params['id'] == 4) {

                    if (empty($params['myid'])) {

                        $html['cat_name'] = $params['title'] . 'ทั้งหมด';
                    } else {

                        $html['cat_name'] = $vn[0]->cat_name;
                    }
                }


                $sub = array();
                $sub['main'] = '';
                $sub['right'] = array();
                $now = date('Y-m-d H:i:s');
                foreach ($vn as $ks => $vs) {

                    $img = 'front/assets/no-image-icon-15.jpg';

                    if (!empty($vs->img)) {
                        $img = $vs->img;
                    }

                    if (isset($_REQUEST['ajax'])) {

                        $sub['right'][] = '

								 <div class="col-md-4 col-12 mt-2">
                                    <a href="' . newsLink($vs->new_id) . '">
									<img src="' . $img . '" class="img-fluidd  " alt="' . $vs->name . '">


									<div class="card-title mt-2">
										<p class="text-muted">' . dateOnly($vs->time_update) . '</p>

						            <p class="card-text limit-line-newscol my-min-height" style="font-size: 14px;">
									<b>' . $vs->name . '</b>
								    </p>


									</div>
                                    </a>
								</div>
						';
                    } else {

                        if ($ks == 0) {
                            $sub['main'] = '

								<div class="col-md-12 col-12 mt-2">
                                <a href="' . newsLink($vs->new_id) . '">
									<img src="' . $img . '" class="img-fluidd  " alt="' . $vs->name . '"></a>


									<div class="card-title mt-2">
										<p class="text-muted">' . dateOnly($vs->time_update) . '</p>

						            <p class="card-text limit-line text-muted my-min-height my-big-title" style="">
									<a href="' . newsLink($vs->new_id) . '"><b class="limit-line">' . $vs->name . '</b></a>
								</p>

									</div>
								</div>
							';
                        } else {



                            $sub['right'][] = '

								 <div class="col-md-4 col-12 mt-2">
                                 <a href="' . newsLink($vs->new_id) . '">
									    <img src="' . $img . '" class="img-fluidd  " alt="' . $vs->name . '">


                                        <div class="card-title mt-2">
                                            <p class="text-muted">' . dateOnly($vs->time_update) . '</p>

                                            <p class="card-text limit-line-newscol my-min-height" style="font-size: 14px;">
                                                <b>' . $vs->name . '</b>
                                            </p>


                                        </div>
                                    </a>
								</div>




							';
                        }
                    }
                }

                if (isset($_REQUEST['ajax'])) {

                    $json_encode['html'] = implode('', $sub['right']);
                    echo json_encode($json_encode);

                    exit;
                }

                //$test['load_url'] = front_link( $params['id'], NULL, array(), false );


                $html['newdivs'][] = '
					' . $sub['main'] . '
					<div class="row mt-0 load-more" style="margin-bottom:-40px">
						' . implode('', $sub['right']) . '
					</div>
				';
            }
        }

        $html['htmlDivsCat'] = implode('<br>', $html['newdivs']);
        $html['newsHighlight'] = $this->NewsHighlight();


        if (!empty($_REQUEST['model_hotkey'])) {


            $html['cat_name'] = 'คำค้นหาข่าว "' . $_REQUEST['model_hotkey'] . '"';
            $filters['WHERE'][] = "n.name LIKE '%" . str_replace('', '', $_REQUEST['model_hotkey'])  . "%'";
        }

        $test['load_url'] = current_url();

        $html['myContent'] = '
			<div class="container mt-2">

				<h1 class="text-dark">' . $html['cat_name'] . '</h1>

				<hr>
				<div class="row">
					<div class="col-xl-8 xol-lg-8 col-md-12 col-12">
						' . implode('<br>', $html['newdivs']) . '
					</div>



					<div class="col-xl-4 xol-lg-4 col-md-12 col-12">						
						<h4 class=" text-center" style="margin-bottom: -5px;margin-top: 20px; font-weight: bold; color: black;">ข่าวยอดนิยม</h4>
						<hr>
						' . $this->NewsHighlight() . '


					</div>
				</div>
			</div>

			<br>
			<br>
			<br>
			<br>


			<script>
			$( function(){

				myData = ' . json_encode($test) . ';

				q = {};
				q.ajax = 1;
				q.page = 2;
				q.' . get_token('name') . ' = \'' . get_token('val') . '\';
				loading = 0;

				//alert( myData.maxPage);
				$( window ).scroll( function(){

					if ( $( window ).scrollTop() > ( $( document ).height() - $( window ).height() ) * 7 / 10 ) {
						if( q.page > myData.maxPage ) {
							return false;
						}

						if( loading == 0 ) {
							loading = 1;
						}
						else {
							return false;
						}

						$.get( myData.load_url, q, function( res ){

							data = JSON.parse( res )
							$( data.html ).appendTo( \'.load-more\' );

							q.page += 1;



							loading = 0;


						});

						return false;
					}

				});

			});
			</script>

		';

        return $html;
    }



    //
    //
    function vdos_html($params = array())
    {

        $sql = "
			SELECT
				tb_vdos.*,
				c.name as catalogName,
				c.id as catalogId,
				c.cat_type,
				aa_users.first_name
			FROM tb_vdos
			INNER JOIN tb_catalog c ON c.id = tb_vdos.cat_id
			LEFT JOIN aa_users ON tb_vdos.user_id = aa_users.id
            LEFT JOIN tb_types_cata tc ON tc.id = c.types
			[WHERE]

			[test]
		";

        $filters = array();
        $onCat = false;
        $onDetails = false;
        if (!empty($params['myid'])) {


            if ($params['id'] == 353) {
                $onDetails = true;
                $filters['WHERE'][] = "tb_vdos.id = " . $params['myid'] . "";
            } else {
                $onCat = true;
                $filters['WHERE'][] = "tb_vdos.cat_id = " . $params['myid'] . " ";
            }
        } else {

            if (in_array($params['id'], array(3, 8))) {


                if ($params['id'] == 8) {

                    $filters['WHERE'][] = "c.cat_type = 11";
                } else {


                    $filters['WHERE'][] = "c.cat_type = 10";
                }
            } else {

                $filters['WHERE'][] = "c.cat_type NOT IN ( 10, 11 )";
            }
        }

        $filters['WHERE'][] = "tb_vdos.release_time < NOW()";
        $filters['WHERE'][] = "tb_vdos.status_id = 1";
        $filters['WHERE'][] = "c.types = 2";


        if (empty($_REQUEST['ajax'])) {

            $filters['test'] = "
                ORDER BY
                tb_vdos.show_at_first ASC,
                tb_vdos.time_update DESC
                		
			";

            // $filters['WHERE'][] = "tb_vdos.id NOT IN ( ". implode( ',', $_SESSION['skip'] ) ." )";
        } else {

            $filters['test'] = "
				ORDER BY
                tb_vdos.show_at_first ASC,
                tb_vdos.time_update DESC
                
                
					
					
			";

            $filters['WHERE'][] = "tb_vdos.id NOT IN ( " . implode(',', $_SESSION['skip']) . " )";
        }



        $data['perPage'] = 14;
        $data['sql'] = genCond_($sql, $filters);

        $sql = genCond_($sql, $filters);



        $test = createPage($data, true);

        $keep = array();

        if ($onCat == true) {
            if (empty($_REQUEST['ajax'])) {

                $_SESSION['skip'] = array();
            }
        } else {
            if (empty($_REQUEST['ajax'])) {

                $keep['block_a'] = array();

                $_SESSION['skip'] = array();
            }
        }


        if (isset($_REQUEST['ajax'])) {
            $action_query = $test['rows'];
        } else {
            $action_query = $this->dao->fetchAll($sql);
        }

        $myVideos = array();
        foreach ($action_query  as $ka => $va) {





            $vdoImg = "front/assets/no-image-icon-15.jpg";

            //no-image-icon-15
            if (!empty($va->img)) {
                $vdoImg = $va->img;
            }


            $ex = explode('/', $va->youtube_link);

            $code = $ex[count($ex) - 1];

            // $va->myImg = '<img class="my-ifrm" style="width: 100%;" src="https://img.youtube.com/vi/' . $code . '/0.jpg" />';
            $va->myImg = '<img class="responsive_img" style="width: 100%;" src="' . $vdoImg . '" />';
            $va->myImgHead = '<img class="responsive_img headVideo" style="width: 100%;" src="https://img.youtube.com/vi/' . $code . '/0.jpg" />';

            $va->detail = html_entity_decode($va->detail);

            $link = front_link($params['id'], $va->cat_id);

            if ($onCat == true) {
                $va->nomalBlock = '
					<div class="card h-100">

						<a class="link-find" href="#" data-id="' . $va->id . '">' . $va->myImg . '</a>

						<div class="card-body" style="padding: 1rem">

							<p class="mt-1 card-text limit-line-newscol text-muted my-min-height" style="">
								<a class="link-find" href="#" data-id="' . $va->id . '"><b>' . $va->name . '</b></a>
							</p>
							<div style="margin-top: 10px; margin-bottom: 5px;">
								<span style="color:#707a89; font-size: 12px; margin-right: 10px;">' . timeToText($va->time_update) . '</span>


								<span style="font-size: 12px; font-weight: bold;">' . $va->sub_name . '</span>


							</div>
						</div>

					</div>
				';
            } else if ($onDetails == true) {
                $html['videoDetail'] = '
                        <iframe class="videoSize my-ifrm" style="width: 100%; height:500px;" src="' . $va->youtube_link . '" title="' . $va->name . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>

                        <p>' . $va->name . '</p>

                        ' . $va->detail . '
                ';
            } else {
                $va->nomalBlock = '
					<div class="card h-100">

						<a class="link-find" href="#" data-id="' . $va->id . '"><img class="my-ifrm" style="width: 100%;height: 300px;" src="' . $vdoImg . '" /></a>

						<div class="card-body">

							<p class="mt-1 card-text limit-line-newscol text-muted my-min-height" style="">
								<a class="link-find" href="#" data-id="' . $va->id . '"><b>' . $va->name . '</b></a>
                                
							</p>
							<div style="margin-top: 10px; margin-bottom: 5px;">
								<span style="color:#707a89; font-size: 12px; margin-right: 10px;">' . timeToText($va->time_update) . '</span>

								
							</div>
						</div>

					</div>
				';
            }


            $myVideos[$va->id] = '
				<div class="modal-header">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<iframe class="videoSize my-ifrm" style="width: 100%; height:245px;" src="' . $va->youtube_link . '" title="' . $va->name . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>

					<p>' . $va->name . '</p>

					' . $va->detail . '

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
				</div>
			';

            $va->name = '<b>' . $va->name . '</b>';

            if (!empty($_REQUEST['ajax'])) {

                $keep[$va->cat_id][] = $va;
            } else {
                if ($onCat == true) {

                    $keep[$va->cat_id][] = $va;
                    $_SESSION['skip'][] = $va->id;
                } else {
                    if (count($keep['block_a']) <= 4) {
                        $keep['block_a'][] = $va;
                    } else {

                        $keep[$va->cat_id][] = $va;
                    }

                    $_SESSION['skip'][] = $va->id;
                }
            }
        }
        // exit;
        // arr($_SESSION['skip']);

        $bombTest = true;
        $bombTest = false;

        $now = date('Y-m-d H:i:s');
        $cat_vdos = array();
        $list = array();
        foreach ($keep as $kn => $vn) {

            if ($kn == 'block_a') {
                $list = array();
                foreach ($vn as $ka => $va) {


                    $vdoImg = "front/assets/no-image-icon-15.jpg";

                    //no-image-icon-15
                    if (!empty($va->img)) {
                        $vdoImg = $va->img;
                    }


                    $ifrm1 = $va->myImg;



                    $ifrm2 = '<iframe class="videoSize my-ifrm" style="width: 100%; height:205px" src="' . $va->youtube_link . '" title="' . $va->name . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>';
                    $link = front_link($params['id'], $va->cat_id);

                    if ($ka == 0) {
                        $ifrm1 = !empty($va->img) ? '<img class="responsive_img" style="width: 100%; " src="' . $vdoImg . '" />'  : $va->myImg;

                        $active = '';

                        $list[1][] = '

                        <div class=" ' . $active . '">
                        <a class="link-find" href="#" data-id="' . $va->id . '">
                            <div class="d-block w-100">' . $ifrm1 . '</div>
                            <div class="d-md-block" style="padding: 10px;">
                               
                                <h3 class="limit-line font-mobile" style="color: #000000;">' . $va->name . '</h3>
                                <div class="headnews-carousel" style="padding-top: 15px;">
                                    <span class="tagspan">' . $va->catalogName . '</span>
                                    
                                    <label style="margin-top: 2px;">' . getDayHour($va->time_update, $now) . '</label>
                                </div>
                            </div>
                            </a>
                        </div>
						';


                        $myVideos[$va->id] = '
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <iframe class="videoSize my-ifrm" style="width: 100%; height:245px;" src="' . $va->youtube_link . '" title="' . $va->name . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>

                                <p>' . $va->name . '</p>

                                ' . $va->detail . '

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                            </div>
                        ';
                    } else {

                        $list[2][] = '

							<div class="col-xl-6 col-lg-6 col-md-12 col-12 mb-2">
                                <div class="card h-100">

                                    <a class="link-find" href="#" data-id="' . $va->id . '"><img class="responsive_img" src="' . $vdoImg . '" /></a>
            
                                    <div class="card-body px-2">
            
                                        <p class="p-1 card-text limit-line-newscol text-muted my-min-height" style="">
                                            <a class="link-find" href="#" data-id="' . $va->id . '"><b>' . $va->name . '</b></a>
                                            
                                        </p>
                                        <div class="pe-2 ps-2" style="margin-top: 10px; margin-bottom: 5px;">
                                            <span style="color:#707a89; font-size: 12px; margin-right: 10px;">' . timeToText($va->time_update) . '</span>           
                                            
                                        </div>
                                    </div>
            
                                </div>
                            </div>
						';
                    }
                }

                if (!isset($list[2])) {
                    $list[2] = array();
                }
                if (!isset($list[1])) {
                    $list[1] = array();
                }

                $active = '';
                if ($kn == 0) {
                    $active = 'active';
                }
                if ($ka != 'block_a') {
                    $btnCas[] = '';
                } else {
                    $btnCas[] = '
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="' . $kn . '" class="' . $active . '" aria-current="true" aria-label="Slide 1"></button>     
                    ';
                }



                $cat_vdos[$kn] = '
                <div class="container">
                    <div class="row">
                        <div class="col-xl-7 col-lg-7 col-md-12 col-12 divRes">
                            
                            <div class="card card-body" style="padding: 0;">
                                ' . implode('', $list[1]) . '
                            </div>
                            
                        </div>
                        <div class="col-xl-5 col-lg-5 col-md-12 col-12">
                            <div class="row">' . implode('', $list[2]) . '</div>
                        </div>
                        
                    </div>
                </div>
                ';
            } else {

                foreach ($vn as $ka => $va) {

                    $txt = timeToText($va->time_update);
                    $vdos_head_cat = array();



                    if (!isset($ggg[$kn])) {
                        $ggg[$kn] = array();
                    }

                    if (empty($params['myid'])) {

                        if (count($ggg[$kn]) == 3) {


                            continue;
                        }

                        $ggg[$kn][] = '<div class="col-lg-4">' . $va->nomalBlock . '</div>';
                    } else {

                        $ggg[$kn][] = '<div class="col-lg-4">' . $va->nomalBlock . '</div>';
                    }
                }



                $btn_all = '
					<h2 style="font-weight: bold; margin-bottom: 0px;">
						<a href="' . front_link($params['id'], @$vn[0]->cat_id) . '">' . @$vn[0]->catalogName . '</a>
					</h2>

					<a href="' . front_link($params['id'], @$vn[0]->cat_id) . '" class="btn btn-danger" style="border-radius: 99px;">ดูทั้งหมด</a>
				';

                $hr = '<hr style="margin-top: 6px;">';

                $load = 'load-new-more';

                if (!empty($params['myid'])) {
                    // $load = 'load-new-more';

                    $html['title'] = $params['title'] . ' : ' . $vn[0]->catalogName;
                    $btn_all = '';
                    $hr = '';

                    $cat_vdos[$kn] = '

                        <div class="container vdo-btn-control" style="
                            display: grid;
                            grid-template-columns: auto 10%;">  ' . $btn_all . ' </div>

                        <div class="container">
                        ' . $hr . '
                            <div class="row ' . $load . '" style="grid-row-gap: 20px;" data-content="' . $kn . '">' . implode('', $ggg[$kn]) . '</div>

                        </div>

                    ';
                }
            }
        }
        // var_dump($cat_vdos);
        // echo (implode('<br>', $cat_vdos) );
        // exit;

        if (isset($_REQUEST['ajax'])) {

            $json = array();
            $json['cat_vdos'] = $cat_vdos;

            // foreach ($ggg as $kl => $vl) {
            //     $json['list'][$kl] = implode('', $vl);
            // }

            echo json_encode($json);
            exit;
        }

        if (!empty($params['myid'])) {
            $link = front_link($params['id'], $params['myid']);
        } else {
            $link = front_link($params['id']);
        }

        $html['cat_vdosssss'] = '<div class="big-contents">' . implode('<br>', $cat_vdos)  . '</div>';
        $html['cat_vdos'] = '<div class="big-contents">' . implode('<br>', $cat_vdos)  . '</div>';


        if (!empty($params['myid'])) {
            $html['cat_vdos'] = '
			<br>
			<br>

			<script>
			$( function(){
				q = {};
				q.ajax = 1;
				q.page = 0;
				loading = 0;
				myData = ' . json_encode($test) . ';



				$( window ).scroll( function(){

					if ( $( window ).scrollTop() > ( $( document ).height() - $( window ).height() ) * 7 / 10 ) {

						if( loading == 0 ) {
							loading = 1;
						}
						else {
							return false;
						}

						me = $( this );

						$.getJSON( me.attr( \'href\' ), q, function( data ){

							for( x in data.cat_vdos ) {

								if( $( \'.load-new-more[data-content="\'+ x +\'"]\' ).length > 0 ) {

									$( data.list[x] ).appendTo( \'.load-new-more[data-content="\'+ x +\'"]\' );
								}
								else {

									$( data.cat_vdos[x] ).appendTo( \'.big-content\' );


								}

							}

							q.page += 1;

							if( q.page == ( myData.maxPage - 1 ) ) {

								me.remove();
							}

							loading = 0;


						});

						return false;
					}

				});

			});

			</script>
		';
        }



        $html['vdos_head_cat'] = '';
        // $html['byCats'] = $this->VideoByCatago($params);
        $html['myVideos'] = $myVideos;
        $html['newsHighlight'] = $this->NewsHighlight();
        $html['NewsInteres'] = $this->NewsInteres();

        return $html;
    }

    function VideoByCatago($params)
    {

        $start = 0;
        $rowperpage = 3;

        // var_dump($params);
        // exit;

        $loadmore = !empty($_REQUEST['loadmore']) ? true : false;

        if ($loadmore) {
            $start = $_REQUEST['start'];
            $rowperpage = $_REQUEST['rowperpage'];
        }


        $sql = "
            SELECT 
                c.* 
            FROM tb_catalog c
            WHERE c.cat_type NOT IN ( 10, 11 ) AND c.types = 2
            ORDER BY c.order_number_videoCat ASC
           
            ";

        // LIMIT $start,$rowperpage
        // echo $sql;exit;


        $total = 0;
        $count = "SELECT count(*) as allCounts FROM tb_catalog";

        foreach ($this->dao->fetchAll($count) as $kcp => $vcp) {
            $total = $vcp->allCounts;
        }


        $list = [];
        $vdoImg = "front/assets/no-image-icon-15.jpg";
        foreach ($this->dao->fetchAll($sql) as $k => $v) {


            $sqls = "
                SELECT 
                    * 
                FROM tb_vdos v
                WHERE v.cat_id = '" . $v->id . "' AND v.release_time < NOW() AND v.status_id = 1
                ORDER BY
                    v.time_update DESC
                LIMIT 0,3";

            $html = [];
            $ids = [];
            foreach ($this->dao->fetchAll($sqls) as $kv => $vv) {

                $ids[] = $vv->cat_id;

                $vdoImg = "front/assets/no-image-icon-15.jpg";

                //no-image-icon-15
                if (!empty($vv->img)) {
                    $vdoImg = $vv->img;
                }


                $html[] = '
                    <div class="col-lg-4">
                        <div class="card h-100">
    
                            <a class="link-find" href="#" data-id="' . $vv->id . '"><img class="responsive_img" src="' . $vdoImg . '" /></a>
                            <div class="card-body" style="padding: 1rem 1rem;">
    
                                <p class="mt-1 card-text limit-line-newscol text-muted my-min-height" style="">
                                    <a class="link-find" href="#" data-id="' . $vv->id . '"><b>' . $vv->name . '</b></a>
                                </p>
                                <div style="margin-top: 10px; margin-bottom: 5px;">
                                    <span style="color:#707a89; font-size: 12px; margin-right: 10px;">' . timeToText($vv->time_update) . '</span>
    
                                    
                                </div>
                            </div>
    
                        </div>
                    </div>
                    ';
            }

            if (in_array($v->id, $ids)) {
                $btn_all = '
                    <h2 style="font-weight: bold; margin-bottom: 0px;">
                        <a href="' . front_link($params['id'], @$v->id) . '">' . @$v->name . '</a>
                    </h2>

                    <a href="' . front_link($params['id'], @$v->id) . '" class="btn btn-danger" style="border-radius: 99px;">ดูทั้งหมด</a>
                ';

                $hr = '<hr style="margin-top: 6px;">';

                $list[] = '
                    <div class="container vdo-btn-control mt-3" style="
                        display: grid;
                        grid-template-columns: auto 10%;">  ' . $btn_all . ' </div>
                    <div class="container">
                    ' . $hr . '
                        <div class="row " style="grid-row-gap: 20px;" data-content="">' . implode('', $html) . '</div>

                </div>
                ';
            }

            // if(!empty($params['myid'])){
            //     $list[] ='';
            // }



        }

        if ($loadmore) {
            $json_data = array(
                'success' => 1,
                'loadmorevdo' => implode('', $list)
            );

            echo json_encode($json_data);
            exit;
        }


        $html['byCats'] = implode('', $list);
        $html['byCats'] .= '
            <input type="hidden" id="start" value="0">
            <input type="hidden" id="rowperpage" value="' . $rowperpage . '">
            <input type="hidden" id="totalrecords" value="' . $total . '">
        ';

        if (empty($params['myid'])) {
            $html['byCatss'] = '
            <script>
                checkWindowSize();
                // Check if the page has enough content or not. If not then fetch records
                function checkWindowSize() {
                    if ($(window).height() >= $(document).height()) {
                        // Fetch records
                        fetchData();
                    }
                }
        
                function fetchData() {
                    var start = Number($(\'#start\').val());
                    var allcount = Number($(\'#totalrecords\').val());
                    var rowperpage = Number($(\'#rowperpage\').val());
                
        
                    start = start + rowperpage;
        
                    if (start <= allcount) {
                        $(\'#start\').val(start);
        
                        $.ajax({
                            url: \'' . front_link($params['id'], 'VideoByCatago') . '\',
                            type: \'get\',
                            data: {
                                loadmore: 1,
                                start: start,
                                rowperpage: rowperpage                                
                            },
                            beforeSend: function() {
                            
                            },
                            success: function(response) {
                                
        
                                obj = JSON.parse(response);
                                // Add

                                $(".loadmore").append(obj.loadmorevdo)
                                
                                // Check if the page has enough content or not. If not then fetch records
                                checkWindowSize();
                            }
                        });
                    }
                }
        
                $(document).on(\'touchmove\', onScroll);
        
                function onScroll() {
        
                    if ($(window).scrollTop() > $(document).height() - $(window).height() - 100) {
                        fetchData();
                    }
                }
        
                $(window).scroll(function() {
        
                    var position = $(window).scrollTop();
                    var bottom = $(document).height() - $(window).height();
        
                    if (position == bottom) {
                        fetchData();
                    }
        
                });
            </script>
        ';
        }




        return $html;
    }


    function changeImags()
    {
        $sql = '
            SELECT 
                f.img_path,
                f.id,
                f.file_ref_id,
                f.time_update 
            FROM `tb_ecom_file` f 
            INNER JOIN tb_news n ON n.id = f.file_ref_id 
            WHERE f.tb_name = "tb_news" 
            GROUP BY f.file_ref_id 
            ORDER BY f.time_update DESC; 
        ';

        foreach ($this->dao->fetchAll($sql) as $k => $v) {
            var_dump($v);

            $sqls = "
                UPDATE `tb_news` SET `img` = '" . $v->img_path . "' WHERE `tb_news`.`id` = '" . $v->file_ref_id . "';
            ";

            $a = $this->dao->execDatas($sqls);

            var_dump($a);
        }
    }
}