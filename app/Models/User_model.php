<?php

//
//
namespace App\Models;

use App\Models\Db_model;

use App\Models\Auth_model;

class User_model{

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

	function test() {
		
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
                                                <span class="input-group-text">ชื่อ-นามสกุล</span>
                                                <input type="text" class="form-control" aria-label="Sizing example input" value="">
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
	
	function gogo() {
		
		return array();
		return array( 'html' => 'aaaaaaaaaaaaaa' );
		
		
	}

    //
    //
    function __construct()
    {
		$this->dao = new Db_model();
		
		
    }

    function index($param = array()) {
		
       
    }

    function get_profile($param = array())
    {
        // arr($params);exit;
        $sql = "SELECT * FROM aa_users WHERE id = ".$_SESSION['user_id']."";
        $profile = array();
        

        foreach ( getDb()->fetchAll($sql) as $key => $val) {
            $profile[] = $val;
        }

        $params['profile'] = $profile;

        // arr($params['product']);exit;
        return $params;
    }

    function save_profile($param)
    {
        
        // arr($result);exit;
        // echo 'TESTTTTTTTTTTTTTTT';

        $configs['first_name'] = array( 'require' => 1 );
        $configs['last_name'] = array( 'require' => 1 );
        $configs['company'] = array( 'require' => 1 );
        $configs['email'] = array( 'require' => 1, 'email' => 1 );
        $configs['phone'] = array( 'require' => 1 );

        $request = service('request');
        $result = $request->getVar();

        $img = $request->getFile('avatar');
        

        // $kk = $img->data('file_name');
        


        // var_dump($result,$img);exit;
		
		$check_form = check_form( $result, $configs );

        if( !empty( $check_form['errors'] ) ) {
			
			$errors['success'] = 0;
			$errors['field'] = $check_form['errors'];
			$errors['message'] = 'กรุณากรอกข้อมูลให้ถูกต้อง และครบถ้วน';
			echo json_encode( $errors );
			exit;
			
        }

        $result = $check_form['result'];


        if( empty( $img->getName() ) ) {
            // echo 'EMPTY';
            $sql = "UPDATE aa_users SET first_name = '".$result['first_name']."', last_name = '".$result['last_name']."', company = '".$result['company']."', gender = '".$result['flexRadioDefault']."', email = '".$result['email']."', phone = '".$result['phone']."' WHERE id = ".$_SESSION['user_id']."";
        
            $data_res = $this->dao->execDatas($sql);

            if($data_res){
                // SendEmail($result);
                $errors = array( 
                            'success' => 1, 
                            'message' => 'Success',
                            // 'html' => $html
                        );
                echo json_encode( $errors );
            }else{
                    $errors = array( 
                    'success' => 0, 
                    'message' => 'ERROR'
                );
                echo json_encode($errors);
            }
        
        } else {
            //echo 'NOT EMPTY';
            $Newname = $img->getRandomName();
            $img->move('uploads', $Newname);
            $sql = "UPDATE aa_users SET first_name = '".$result['first_name']."', last_name = '".$result['last_name']."', company = '".$result['company']."', gender = '".$result['flexRadioDefault']."', email = '".$result['email']."', phone = '".$result['phone']."', avatar = '".$Newname."' WHERE id = ".$_SESSION['user_id']."";
        
            $data_res = $this->dao->execDatas($sql);

            if($data_res){
                // SendEmail($result);
                $errors = array( 
                            'success' => 1, 
                            'message' => 'Success',
                            // 'html' => $html
                        );
                echo json_encode( $errors );
            }else{
                    $errors = array( 
                    'success' => 0, 
                    'message' => 'ERROR'
                );
                echo json_encode($errors);
            }
        }
    
    }
	
}
