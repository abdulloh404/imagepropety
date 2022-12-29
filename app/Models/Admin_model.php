<?php

//
//
namespace App\Models;

use App\Models\Db_model;

use App\Models\Auth_model;

class Admin_model
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

    function listCata()
    {
        $sql = "
			SELECT 
				* 
			FROM tb_catalog 
			WHERE show_on_home = 1
            AND types = 1
			ORDER BY order_number ASC
			
		";

        $div = array();
        $i = 1;
        foreach ($this->dao->fetchAll($sql) as $k => $v) {
            $div[] = '
				<style>
					.divs{
						border : 1px solid Black;
						border-radius: 15px 50px 30px;
						background-color : #ffffcc;
						padding : 10px;
					}
				</style>

				<div class="form-group divs" id="item-' . $v->id . '">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<span>เซ็กชั่น ' . $v->name . '</span>
					
				</div>
            ';
            //$li[] = '<li id="item-' . $v->id . '"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' . $v->name . '</li>';
            $i++;
        }

        $html['listCata'] =  implode('', $div);

        return $html;
    }

    function list_themes_video()
    {
        $sql = "
			SELECT 
				* 
			FROM tb_catalog 
			WHERE show_on_home = 1
            AND types = 2
			ORDER BY order_number_videoCat ASC
			
		";

        $div = array();
        $i = 1;
        foreach ($this->dao->fetchAll($sql) as $k => $v) {
            $div[] = '
				<style>
					.divs{
						border : 1px solid Black;
						border-radius: 15px 50px 30px;
						background-color : #ffffcc;
						padding : 10px;
					}
				</style>

				<div class="form-group divs" id="item[video]-' . $v->id . '">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<span>เซ็กชั่น ' . $v->name . '</span>
					
				</div>
            ';
            //$li[] = '<li id="item-' . $v->id . '"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' . $v->name . '</li>';
            $i++;
        }
        
        $html['listCata'] =  implode('', $div);
        $html['listCata'] .= '<input class="typeThemes" type="hidden" name="types" value="video">';
        return $html;
    }

    function getNews($params = array())
    {

        $sql = "
			SELECT 
				n.name as news_name,
				n.doc_date,
				c.name as catalog_name,
				u.first_name 
			FROM tb_news n
			
			
			INNER JOIN aa_users u ON n.user_id = u.id 
			INNER JOIN tb_catalog c ON n.cat_id = c.id 
			ORDER BY  
				n.doc_date DESC
		";

        foreach ($this->dao->fetchAll($sql) as $ka => $va) {
            $trs[] = '
			
				<tr>
					<th scope="row">1</th>
					<td>โรคติดเชื้อไวรัสโคโรนาสายพันธุ์ใหม่ 2019...</td>
					<td>ข่าวกีฬา</td>
					<td>แอดมิน</td>
					<td>20/12/2020</td>
				</tr>
			';
        }


        $params['table'] = '
			<table class="table table-striped mg-b-0 text-md-nowrap">
				<thead>
					<tr>
						<th>ลำดับ</th>
						<th>หัวเรื่องข่าว</th>
						<th>หมวดหมู่</th>
						<th>ชื่อผู้ใช้งาน</th>
						<th>วันที่</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th scope="row">1</th>
						<td>โรคติดเชื้อไวรัสโคโรนาสายพันธุ์ใหม่ 2019...</td>
						<td>ข่าวกีฬา</td>
						<td>แอดมิน</td>
						<td>20/12/2020</td>
					</tr>
					<tr>
						<th scope="row">2</th>
						<td>โรคติดเชื้อไวรัสโคโรนาสายพันธุ์ใหม่ 2019...</td>
						<td>ข่าวต่างประเทศ</td>
						<td>แอดมิน</td>
						<td>20/12/2020</td>
					</tr>
					<tr>
						<th scope="row">3</th>
						<td>โรคติดเชื้อไวรัสโคโรนาสายพันธุ์ใหม่ 2019...</td>
						<td>ข่าวกีฬา</td>
						<td>แอดมิน</td>
						<td>20/12/2020</td>
					</tr>
					<tr>
						<th scope="row">4</th>
						<td>โรคติดเชื้อไวรัสโคโรนาสายพันธุ์ใหม่ 2019...</td>
						<td>ข่าวต่างประเทศ</td>
						<td>แอดมิน</td>
						<td>20/12/2020</td>
					</tr>
					<tr>
						<th scope="row">5</th>
						<td>โรคติดเชื้อไวรัสโคโรนาสายพันธุ์ใหม่ 2019...</td>
						<td>ข่าวกีฬา</td>
						<td>แอดมิน</td>
						<td>20/12/2020</td>
					</tr>
					<tr>
						<th scope="row">6</th>
						<td>โรคติดเชื้อไวรัสโคโรนาสายพันธุ์ใหม่ 2019...</td>
						<td>ข่าวต่างประเทศ</td>
						<td>แอดมิน</td>
						<td>20/12/2020</td>
					</tr>
					<tr>
						<th scope="row">7</th>
						<td>โรคติดเชื้อไวรัสโคโรนาสายพันธุ์ใหม่ 2019...</td>
						<td>ข่าวกีฬา</td>
						<td>แอดมิน</td>
						<td>20/12/2020</td>
					</tr>
					<tr>
						<th scope="row">8</th>
						<td>โรคติดเชื้อไวรัสโคโรนาสายพันธุ์ใหม่ 2019...</td>
						<td>ข่าวต่างประเทศ</td>
						<td>แอดมิน</td>
						<td>20/12/2020</td>
					</tr>
					<tr>
						<th scope="row">9</th>
						<td>โรคติดเชื้อไวรัสโคโรนาสายพันธุ์ใหม่ 2019...</td>
						<td>ข่าวกีฬา</td>
						<td>แอดมิน</td>
						<td>20/12/2020</td>
					</tr>
					<tr>
						<th scope="row">10</th>
						<td>โรคติดเชื้อไวรัสโคโรนาสายพันธุ์ใหม่ 2019...</td>
						<td>ข่าวต่างประเทศ</td>
						<td>แอดมิน</td>
						<td>20/12/2020</td>
					</tr>
				</tbody>
			</table>
		';

        return $params;
        return array('html' => 'aaaaaaaaaaaaaa');
    }


    function updateProfile()
    {
        $request = service('request');
        $result = $request->getVar();
        $imgFile = $request->getFile('profileImg');



        if (!empty($imgFile)) {
            $Newname = $imgFile->getRandomName();
            $img_path = 'uploads/tb_user_profile/' . $Newname;
            $imgFile->move('uploads/tb_user_profile', $Newname);
        } else {
            $img_path = '';
        }

        if (!empty($result['investment'])) {
            $investmentout = implode(',', $result['investment']);
        } else {
            $investmentout = '';
        }



        //$imgFile->move('uploads/tb_user_profile', $Newname)


        $configs['fnameTH'] = array('require' => 1);
        $configs['lnameTH'] = array('require' => 1);
        $configs['fnameEN'] = array('require' => 1);
        $configs['lnameEN'] = array('require' => 1);
        $configs['tel'] = array('require' => 1);
        $configs['email'] = array('require' => 1, 'format' => 'email');
        $configs['birthDay'] = array('require' => 1);
        $configs['gender'] = array('require' => 1);
        $configs['status'] = array('require' => 1);
        $configs['education'] = array('require' => 1);
        $configs['address'] = array('require' => 1);
        $configs['sub_district'] = array('require' => 1);
        $configs['district'] = array('require' => 1);
        $configs['province'] = array('require' => 1);
        $configs['postcode'] = array('require' => 1);
        $configs['occupation'] = array('require' => 1);
        $configs['income'] = array('require' => 1);
        $configs['addressWork'] = array('require' => 1);
        $configs['event'] = array('require' => 1);
        // $configs['investment[]'] = array('require' => 1);

        // // var_dump($configs);exit;

       
        
       
       


        $check_form = check_form($result, $configs);
        if (!empty($check_form['errors'])) {

            $errors['success'] = 0;
            $errors['field'] = $check_form['errors'];
            $errors['message'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';

            echo json_encode($errors);
            exit;
        }

        
        if (isset($result['addUser'])) {

            $sql = "
            INSERT INTO `tb_user_profile` 
                (`user_id`, `fnameTH`, `lnameTH`, `fnameEN`, `lnameEN`, `tel`, `email`, `birthday`, `gender`, `statusUser`, `education`, `address`, `subDistrict`, `district`, `province`, `postcode`, `occupation`, `income`, `addressWork`, `eventUser`, `investment`, `img_path`)
            VALUES
                ( '" . $_SESSION['user_id'] . "', '" . $result['fnameTH'] . "', '" . $result['lnameTH'] . "', '" . $result['fnameEN'] . "', '" . $result['lnameEN'] . "', '" . $result['tel'] . "', '" . $result['email'] . "', '" . $result['birthDay'] . "', '" . $result['gender'] . "', '" . $result['status'] . "', '" . $result['education'] . "', '" . $result['address'] . "', '" . $result['sub_district'] . "', '" . $result['district'] . "', '" . $result['province'] . "', '" . $result['postcode'] . "', '" . $result['occupation'] . "','" . $result['income'] . "', '" . $result['addressWork'] . "', '" . $result['event'] . "', '" . $investmentout . "','" . $img_path . "')";
            // arr($sql);
            if ($this->dao->execDatas($sql)) {
                $sqlUpdate = "Update aa_users SET first_name = '" . $result['fnameEN'] . "' , last_name = '" . $result['lnameEN'] . "' WHERE id='" . $_SESSION['user_id'] . "'";
                $this->dao->execDatas($sqlUpdate);

                $errors = array(
                    'success' => 1,
                    'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                    'redirect' => front_link(124)
                );
            } else {
                $errors = array(
                    'success' => 0,
                    'message' => 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง'
                );
            }
        } else if (isset($result['editUser'])) {


            $sql = "
            UPDATE `tb_user_profile` SET 
                fnameTH = '" . $result['fnameTH'] . "', 
                lnameTH = '" . $result['lnameTH'] . "', 
                fnameEN = '" . $result['fnameEN'] . "', 
                lnameEN = '" . $result['lnameEN'] . "', 
                tel = '" . $result['tel'] . "', 
                email = '" . $result['email'] . "', 
                birthday = '" . $result['birthDay'] . "',
                gender = '" . $result['gender'] . "', 
                statusUser = '" . $result['status'] . "', 
                education = '" . $result['education'] . "', 
                address = '" . $result['address'] . "',
                subDistrict = '" . $result['sub_district'] . "', 
                district = '" . $result['district'] . "', 
                province = '" . $result['province'] . "', 
                postcode = '" . $result['postcode'] . "',
                occupation = '" . $result['occupation'] . "',
                income = '" . $result['income'] . "', 
                addressWork = '" . $result['addressWork'] . "',
                eventUser = '" . $result['event'] . "', 
                investment = '" . $investmentout . "',
                img_path = '" . $img_path . "'
            WHERE 
                    user_id = " . $result['user_id'] . "";

            // INSERT INTO `tb_user_profile_dt` (`user_id`, `cat_id`) VALUES ('1', '1');

            if ($this->dao->execDatas($sql)) {
                $sqlUpdate = "Update aa_users SET first_name = '" . $result['fnameEN'] . "' , last_name = '" . $result['lnameEN'] . "' WHERE id='" . $result['user_id'] . "'";
                // arr($sqlUpdate);exit;
                $this->dao->execDatas($sqlUpdate);
                $errors = array(
                    'success' => 1,
                    'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                    'redirect' => front_link(124)
                );
            } else {
                $errors = array(
                    'success' => 0,
                    'message' => 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง'
                );
            }
        }


        echo json_encode($errors);
    }

    function getProfile($params = array())
    {

        $sql = "
        SELECT tb_user_profile.*, aa_users.email as uEmail,aa_users.id
            FROM  tb_user_profile
            LEFT JOIN aa_users ON aa_users.id = tb_user_profile.user_id
            WHERE aa_users.id = " . $_SESSION['user_id'] . " LIMIT 0,1";
        $keep = array();

       
        $gArray = array('ชาย', 'หญิง', 'อื่น ๆ');
        $statusArray = array('โสด', 'สมรส');
        $eduArray = array('ต่ำกว่าปริญญาตรี', 'ปริญญาตรี', 'ปริญญาโท', 'สูงกว่าปริญญาโท');
        $oppucationArray = array(
            'ข้าราชการ / รัฐวิสาหกิจ / สถาบันการศึกษา หรือหน่วยงานภายใต้สังกัดรัฐ',
            'เอ็นจีโอ กิจการเพื่อสังคม หรือองค์กรระหว่างประเทศ',
            'อาชีพอิสระ',
            'พนักงานบริษัท',
            'เจ้าของกิจการ',
            'อาชีพเฉพาะทาง (เช่น แพทย์/ทันตแพทย์/เภสัชกร/ผู้พิพากษา/ทนายความ/วิศวกร/สถาปนิก)',
            'นักเรียน/นักศึกษา',
            'อื่นๆ'
        );
        $incomeArray = array(
            'น้อยกว่า 14,999 บาท',
            '15,000 - 29,999 บาท',
            '30,000 - 49,999 บาท',
            '50,000 - 69,999 บาท',
            '70,000 - 199,999 บาท',
            '200,000 - 299,999 บาท',
            '300,000 - 499,999 บาท',
            '300,000 - 499,999 บาท',
            'มากกว่า 500,000 บาท'
        );

        $investmentArray = array(
            'เงินฝาก',
            'พันธบัตรรัฐบาล',
            'หุ้น (Single Stock)',
            'ทองคํา เพชร',
            'ประกันชีวิต',
            'อื่นๆ'
        );

        $investmentArray2 = array(
            'หุ้นกู้ (bond)',
            'กองทุนรวม',
            'อสังหาริมทรัพย์ เช่น บ้าน คอนโดมิเนียม ที่ดิน',
            'คริปโตเคอร์เรนซี',
            'สลากออมสิน สลาก ธ.ก.ส.',
            'ไม่สนใจ'
        );



        

        if(isset($_SESSION['u'])){
            $emails = $_SESSION['u']->email;
        }else{
            $emails = '';
        }

        // var_dump($emails);exit;
        if ($this->dao->fetchAll($sql)) {
            foreach ($this->dao->fetchAll($sql) as $key => $val) {
                $img = !empty($val->img_path) ? $val->img_path : 'admin/assets/img/photos/1.jpg';

                $investment = explode(',', $val->investment);

                $option1 = array();
                foreach ($gArray as $v) {
                    if ($val->gender == $v) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                    $option1[] = '<option ' . $select . '>' . $v . '</option>';
                }

                $option2 = array();
                foreach ($statusArray as $sv) {
                    if ($val->statusUser == $sv) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }

                    $option2[] = '<option ' . $select . '>' . $sv . '</option>';
                }

                $option3 = array();
                foreach ($eduArray as $ev) {
                    if ($val->education == $ev) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }

                    $option3[] = '<option ' . $select . '>' . $ev . '</option>';
                }

                $option4 = array();
                foreach ($oppucationArray as $ov) {
                    if ($val->occupation == $ov) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }

                    $option4[] = '<option ' . $select . '>' . $ov . '</option>';
                }

                $option5 = array();
                foreach ($incomeArray as $inv) {
                    if ($val->income == $inv) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }

                    $option5[] = '<option ' . $select . '>' . $inv . '</option>';
                }

                $checkbox = array();

                foreach ($investmentArray as $iv) {
                    // var_dump(in_array($iv,$investment));exit;
                    if (in_array($iv, $investment)) {
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }

                    $checkbox[] = '<label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="' . $iv . '" ' . $checked . '><span>' . $iv . '</span></label>';
                }

                $checkbox2 = array();

                foreach ($investmentArray2 as $iv2) {
                    if (in_array($iv2, $investment)) {
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }

                    $checkbox2[] = '<label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="' . $iv2 . '" ' . $checked . '><span>' . $iv2 . '</span></label>';
                }


                // var_dump($val);exit;




                $keep = '
                    <form method="POST" class="form-horizontal" action="' . front_link(124, 'updateProfile', array(), false) . '" enctype="multipart/form-data">
                    <input type="hidden" name="editUser" value="1">
                    <input type="hidden" name="user_id" value="' . $val->id . '">
                    ' . $params['secret'] . '
                    <div class="card-body">
                        <div class="form-group mt-3 mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-input">
                                        <div class="preview">
                                            <img id="file-ip-1-preview" alt="Responsive image" class="img-thumbnail wd-100p wd-sm-200" src="' . $img . '">
                                        </div>
                                        <label class="btn btn-secondary mt-1" for="file-ip-1" style="padding: 5px 20px;">แก้ไขรูป</label>
    
                                        <input type="file" class="btn btn-secondary mt-1" id="file-ip-1" name="profileImg" accept="image/*" value="' . $img . '" onchange="showPreview(event);">
                                    </div>
    
                                </div>
                                <div class="col-md-9">                                    
                                    <h3 class="mb-4 main-content-label mt-2 mb-2">Personal Information</h3>
                                    <h4 class="mb-2 mt-5 main-content-label">ข้อมูลส่วนตัว</h4>
                                </div>
                            </div>
                        </div>
    
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">ชื่อ (ภาษาไทย)</label> 
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="fnameTH" pattern="^[ก-๏\s]+$" title="กรุณากรอกชื่อ นามสกุล ภาษาไทย" class="form-control" value="' . $val->fnameTH . '" placeholder="" >
                                    <span class="text-danger" data-name="fnameTH">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">นามสกุล (ภาษาไทย)</label> 
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="lnameTH" pattern="^[ก-๏\s]+$" title="กรุณากรอกชื่อ นามสกุล ภาษาไทย" value="' . $val->lnameTH . '" class="form-control" placeholder="" >
                                    <span class="text-danger" data-name="lnameTH">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">ชื่อ (ภาษาอังกฤษ)</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="fnameEN" pattern="^[a-zA-Z\s]+$" title="กรุณากรอกชื่อ นามสกุล ภาษาอังกฤษ" value="' . $val->fnameEN . '" class="form-control" placeholder="" >
                                    <span class="text-danger" data-name="fnameEN">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">นามสกุล (ภาษาอังกฤษ)</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="lnameEN" pattern="^[a-zA-Z\s]+$" title="กรุณากรอกชื่อ นามสกุล ภาษาอังกฤษ" value="' . $val->lnameEN . '" class="form-control" placeholder="" >
                                    <span class="text-danger" data-name="lnameEN">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">เบอร์โทรศัพท์</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="tel" class="form-control" value="' . $val->tel . '" maxlength="10">
                                    <span class="text-danger" data-name="tel">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">อีเมล</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="email" name="email" class="form-control" value="' . $val->uEmail . '" placeholder="email@email.com">
                                    <span class="text-danger" data-name="email">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">วัน/เดือน/ปีเกิด</label> 
                                </div>
                                <div class="col-md-9">
                                    <input type="date" name="birthDay" class="form-control" value="' . $val->birthday . '" placeholder="20/01/1999">
                                    <span class="text-danger" data-name="birthDay">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">เพศ</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control select2" name="gender">
                                        <option disabled>เลือกเพศ</option>
                                        ' . implode('', $option1) . '
                                    </select>
                                    <span class="text-danger" data-name="gender">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">สถานภาพ</label> 
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control select2" name="status">
                                        <option value="" disabled selected>เลือกสถานภาพ</option>
                                        ' . implode('', $option2) . '
                                    </select>
                                    <span class="text-danger" data-name="status">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">ระดับการศึกษา</label> 
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control select2" name="education">
                                        <option value="" disabled selected>เลือกระดับการศึกษา</option>
                                        ' . implode('', $option3) . '
                                    </select>
                                    <span class="text-danger" data-name="education">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">ที่อยู่</label> 
                                </div>
                                <div class="col-md-9">
                                    <textarea class="form-control" name="address" rows="3" placeholder="">' . $val->address . '</textarea>
                                    <span class="text-danger" data-name="address">*</span>
                                </div>
                                
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">แขวง/ตำบล</label>
                                </div>
                                <div class="col-md-9">
                                    <input id="sub_district" name="sub_district" type="text" value="' . $val->subDistrict . '" class="form-control txt sub_district">
                                    <span class="text-danger" data-name="sub_district">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">เขต/อำเภอ</label>
                                </div>
                                <div class="col-md-9">
                                    <input id="district" name="district" type="text" value="' . $val->district . '" class="form-control txt district">
                                    <span class="text-danger" data-name="district">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">จังหวัด</label>
                                </div>
                                <div class="col-md-9">
                                    <input id="province" name="province" type="text" value="' . $val->province . '" class="form-control txt province">
                                    <span class="text-danger" data-name="province">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">รหัสไปรษณีย์</label>
                                </div>
                                <div class="col-md-9">
                                    <input id="postcode" name="postcode" type="text" value="' . $val->postcode . '" class="form-control txt postcode">
                                    <span class="text-danger" data-name="postcode">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">อาชีพ</label> 
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control select2" name="occupation">
                                        <option value="" disabled selected>เลือกอาชีพ</option>
                                        ' . implode('', $option4) . '
                                    </select>
                                    <span class="text-danger" data-name="occupation">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">รายได้</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-control select2" name="income">
                                        <option value="" disabled selected>เลือกระดับรายได้</option>
                                        ' . implode('', $option5) . '
                                    </select>
                                    <span class="text-danger" data-name="income">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">สถานที่ทำงาน / หน่วยงาน / สังกัด </label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="addressWork" class="form-control" value="' . $val->addressWork . '" placeholder="">
                                    <span class="text-danger" data-name="addressWork">*</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">กิจกรรมยามว่าง </label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="event" class="form-control" placeholder="" value="' . $val->eventUser . '">
                                    <span class="text-danger" data-name="event">*</span>
                                </div>
                            </div>
                        </div>
    
    
    
                        <div class="mb-4 main-content-label">ความสนใจด้านการลงทุน <span class="text-danger" data-name="investment">*</span></div> 
                        <div class="form-group mb-0">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="custom-controls-stacked">
                                        ' . implode('', $checkbox) . '
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="custom-controls-stacked">
                                        ' . implode('', $checkbox2) . '
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
    
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning waves-effect waves-light save">Update Profile</button>
                    </div>
                </form>
                ';
            }
        } else {
            $keep = '
                <form method="POST" class="form-horizontal" action="' . front_link(124, 'updateProfile', array(), false) . '" enctype="multipart/form-data">
                <input type="hidden" name="addUser" value="1"> 
                ' . $params['secret'] . '               
                <div class="card-body">
                    <div class="form-group mt-3 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-input">
                                    <div class="preview">
                                        <img id="file-ip-1-preview" alt="Responsive image" class="img-thumbnail wd-100p wd-sm-200" src="admin/assets/img/photos/1.jpg">
                                    </div>
                                    <label class="btn btn-secondary mt-1" for="file-ip-1" style="padding: 5px 20px;">แก้ไขรูป</label>

                                    <input type="file" class="btn btn-secondary mt-1" id="file-ip-1" name="profileImg" accept="image/*"  value="" onchange="showPreview(event);">
                                </div>

                            </div>
                            <div class="col-md-9">                                
                                <h3 class="mb-4 main-content-label mt-2 mb-2">Personal Information</h3>
                                <h4 class="mb-2 mt-5 main-content-label">ข้อมูลส่วนตัว</h4>
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">ชื่อ (ภาษาไทย)</label> 
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="fnameTH" pattern="^[ก-๏\s]+$" title="กรุณากรอกชื่อ นามสกุล ภาษาไทย" class="form-control" placeholder="" >
                                <span class="text-danger" data-name="fnameTH">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">นามสกุล (ภาษาไทย)</label> 
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="lnameTH" pattern="^[ก-๏\s]+$" title="กรุณากรอกชื่อ นามสกุล ภาษาไทย" class="form-control" placeholder="" >
                                <span class="text-danger" data-name="lnameTH">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">ชื่อ (ภาษาอังกฤษ)</label> 
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="fnameEN" pattern="^[a-zA-Z\s]+$" title="กรุณากรอกชื่อ นามสกุล ภาษาอังกฤษ" class="form-control" placeholder="" >
                                <span class="text-danger" data-name="fnameEN">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">นามสกุล (ภาษาอังกฤษ)</label> 
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="lnameEN" pattern="^[a-zA-Z\s]+$" title="กรุณากรอกชื่อ นามสกุล ภาษาอังกฤษ" class="form-control" placeholder="" >
                                <span class="text-danger" data-name="lnameEN">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">เบอร์โทรศัพท์</label> 
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="tel" class="form-control" maxlength="10">
                                <span class="text-danger" data-name="tel">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">อีเมล</label> 
                            </div>
                            <div class="col-md-9">
                                <input type="email" name="email" class="form-control" placeholder="email@email.com" value="'.$emails.'">
                                <span class="text-danger" data-name="email">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">วัน/เดือน/ปีเกิด</label> 
                            </div>
                            <div class="col-md-9">
                                <input type="date" name="birthDay" class="form-control" placeholder="20/01/1999">
                                <span class="text-danger" data-name="birthDay">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">เพศ</label> 
                            </div>
                            <div class="col-md-9">
                                <select class="form-control select2" name="gender">
                                    <option disabled selected>เลือกเพศ</option>
                                    <option>ชาย</option>
                                    <option>หญิง</option>
                                    <option>อื่น ๆ</option>
                                </select>
                                <span class="text-danger" data-name="gender">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">สถานภาพ</label> 
                            </div>
                            <div class="col-md-9">
                                <select class="form-control select2" name="status">
                                    <option disabled selected>เลือกสถานภาพ</option>
                                    <option>โสด</option>
                                    <option>สมรส</option>
                                </select>
                                <span class="text-danger" data-name="status">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">ระดับการศึกษา</label> 
                            </div>
                            <div class="col-md-9">
                                <select class="form-control select2" name="education">
                                    <option disabled selected>เลือกระดับการศึกษา</option>
                                    <option>ต่ำกว่าปริญญาตรี</option>
                                    <option>ปริญญาตรี</option>
                                    <option>ปริญญาโท</option>
                                    <option>สูงกว่าปริญญาโท</option>
                                </select>
                                <span class="text-danger" data-name="education">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">ที่อยู่</label>
                            </div>
                            <div class="col-md-9">
                                <textarea class="form-control" name="address" rows="3" placeholder=""></textarea>
                                <span class="text-danger" data-name="address">*</span>
                            </div>
                            
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">แขวง/ตำบล</label> 
                            </div>
                            <div class="col-md-9">
                                <input id="sub_district" name="sub_district" type="text" class="form-control txt sub_district">
                                <span class="text-danger" data-name="sub_district">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">เขต/อำเภอ</label> 
                            </div>
                            <div class="col-md-9">
                                <input id="district" name="district" type="text" class="form-control txt district">
                                <span class="text-danger" data-name="district">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">จังหวัด</label> 
                            </div>
                            <div class="col-md-9">
                                <input id="province" name="province" type="text" class="form-control txt province">
                                <span class="text-danger" data-name="province">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">รหัสไปรษณีย์
                            </div>
                            <div class="col-md-9">
                                <input id="postcode" name="postcode" type="text" class="form-control txt postcode">
                                <span class="text-danger" data-name="postcode">*</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">อาชีพ</label> 
                            </div> 
                            <div class="col-md-9">
                                <select class="form-control select2" name="occupation">
                                    <option disabled selected>เลือกอาชีพ</option>
                                    <option>ข้าราชการ / รัฐวิสาหกิจ / สถาบันการศึกษา หรือหน่วยงานภายใต้สังกัดรัฐ</option>
                                    <option>เอ็นจีโอ กิจการเพื่อสังคม หรือองค์กรระหว่างประเทศ</option>
                                    <option>อาชีพอิสระ</option>
                                    <option>พนักงานบริษัท</option>
                                    <option>เจ้าของกิจการ</option>
                                    <option>อาชีพเฉพาะทาง (เช่น แพทย์/ทันตแพทย์/เภสัชกร/ผู้พิพากษา/ทนายความ/วิศวกร/สถาปนิก)</option>
                                    <option>นักเรียน/นักศึกษา</option>
                                    <option>อื่นๆ</option>
                                </select>
                                <span class="text-danger" data-name="occupation">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">รายได้</label> 
                            </div>
                            <div class="col-md-9">
                                <select class="form-control select2" name="income">
                                    <option disabled selected>เลือกระดับรายได้</option>
                                    <option>น้อยกว่า 14,999 บาท</option>
                                    <option>15,000 - 29,999 บาท</option>
                                    <option>30,000 - 49,999 บาท</option>
                                    <option>50,000 - 69,999 บาท</option>
                                    <option>70,000 - 199,999 บาท</option>
                                    <option>200,000 - 299,999 บาท</option>
                                    <option>300,000 - 499,999 บาท</option>
                                    <option>มากกว่า 500,000 บาท</option>
                                </select>
                                <span class="text-danger" data-name="income">*</span>
                            </div>
                            
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">สถานที่ทำงาน / หน่วยงาน / สังกัด </label> 
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="addressWork" class="form-control" placeholder="">
                                <span class="text-danger" data-name="addressWork">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">กิจกรรมยามว่าง </label> 
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="event" class="form-control" placeholder="">
                                <span class="text-danger" data-name="event">*</span>
                            </div>
                        </div>
                    </div>



                    <div class="mb-4 main-content-label">ความสนใจด้านการลงทุน <span class="text-danger" data-name="investment">*</span></div> 
                    <div class="form-group mb-0">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-controls-stacked">
                                    <label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="เงินฝาก"><span>เงินฝาก</span></label>
                                    <label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="พันธบัตรรัฐบาล"><span>พันธบัตรรัฐบาล</span></label>
                                    <label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="หุ้น (Single Stock)"><span>หุ้น (Single Stock)</span></label>
                                    <label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="ทองคํา เพชร"><span>ทองคํา เพชร</span></label>
                                    <label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="ประกันชีวิต"><span>ประกันชีวิต</span></label>
                                    <label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="อื่นๆ"><span>อื่นๆ</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-controls-stacked">
                                    <label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="หุ้นกู้ (bond)"><span>หุ้นกู้ (bond)</span></label>
                                    <label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="กองทุนรวม"><span>กองทุนรวม</span></label>
                                    <label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="อสังหาริมทรัพย์ เช่น บ้าน คอนโดมิเนียม ที่ดิน"><span>อสังหาริมทรัพย์ เช่น บ้าน คอนโดมิเนียม ที่ดิน</span></label>
                                    <label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="คริปโตเคอร์เรนซี"><span>คริปโตเคอร์เรนซี</span></label>
                                    <label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="สลากออมสิน สลาก ธ.ก.ส."><span>สลากออมสิน สลาก ธ.ก.ส.</span></label>
                                    <label class="ckbox mg-b-10"><input type="checkbox" name="investment[]" value="ไม่สนใจ"><span>ไม่สนใจ</span></label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning waves-effect waves-light save">Update Profile</button>
                </div>
            </form>
            ';
        }

        $data['userData'] = $keep;
        return $data;
    }

    //
    //
    function __construct()
    {
        $this->dao = new Db_model();
    }

    function index($param = array())
    {
    }
}
