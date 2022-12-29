<?php

use App\Models\Db_model;

if (!function_exists('newsDropDown')) {
    //use App\Models\Loginmodel as Loginmodel; // use model here
    function newsDropDown() {

        $dao = getDb();

        $sql = "
			SELECT
				n.cat_id,
				c.name as cat_name
			FROM tb_news n
			INNER JOIN tb_catalog c ON n.cat_id = c.id
			WHERE c.cat_type = 1
			GROUP BY
				n.cat_id
			ORDER BY c.order_number ASC

		";

        $list = array();
        foreach ($dao->fetchAll($sql) as $ka => $va) {

            $list[] = '<li><a class="dropdown-item" href="' . front_link(4, $va->cat_id) . '">' . $va->cat_name . '</a></li>';
        }

        return 
			
			implode('<li><hr class="dropdown-divider"></li>', $list) 
			
		;
    }

}

if (!function_exists('titleWeb')) {
    function titleWeb()
    {
		return 'สวพ. FM 91 สถานีวิทยุเพื่อความปลอดภัยและการจราจร';
    }
}
if (!function_exists('validEmail')) {
    function validEmail($email)
    {

        if (preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email))
            return true;

        return false;
    }
}

if (!function_exists('vailPassword')) {
    //TO Check Password
    function vailPassword($password)
    {

        $number = preg_match('@[0-9]@', $password);
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        // $specialChars = preg_match('@[^\w]@', $password);

        if (strlen($password) < 8)
            return false;

        return true;
    }
}

if (!function_exists('check_form')) {
    function check_form($result = array(), $configs = array(), $tb_name = null)
    {

        $dao = getDb();

        $datas = array();

        $datas = array();

        foreach ($result as $kr => $vr) {

            if($kr == 'investment'){
                continue;
            }

            $vr = trim($vr);

            $vr = htmlspecialchars($vr);

            $datas[$kr] = $vr;
        }
        // exit;
        $result = $datas;

        $datas = array();

        foreach ($configs as $kc => $vc) {

            if (!empty($vc['require'])) {
                if (empty($result[$kc])) {
                    $datas['errors'][$kc] = 'กรุณากรอกข้อมูลนี้';
                }
            }

            if (isset($result[$kc])) {


                if (!empty($vc['lenght'])) {
                    if (strlen($result[$kc]) != $vc['lenght']) {
                        $datas['errors'][$kc] = 'ต้องการ  ' . $vc['lenght']    . 'อักขระ';
                    }
                }


                if (isset($vc['format']) && $vc['format'] == 'email') {

                    if (!validEmail($result[$kc])) {
                        $datas['errors'][$kc] = 'การกรอกอีเมล์ผิดรูปแบบ';
                    }
                }

                if (!empty($vc['nodupicate'])) {

                    $sql = "
                        SELECT
                            *
                        FROM " . $vc['nodupicate']['check_tb'] . "
                        WHERE " . $vc['nodupicate']['check_column'] . " = '" . $result[$kc] . "'
                    ";
                    ///arr( $sql );
                    foreach ($dao->fetchAll($sql) as $ka => $va) {
                        $datas['errors'][$kc] = 'มีการใช้ข้อมูลนี้แล้ว';
                    }
                }

                if (!empty($vc['same_as'])) {

                    if (isset($result[$vc['same_as']])) {

                        if ($result[$kc] != $result[$vc['same_as']]) {
                            $datas['errors'][$kc] = 'กรุณายืนยันข้อมูลที่ตรงกัน';
                            $datas['errors'][$vc['same_as']] = 'กรุณายืนยันข้อมูลที่ตรงกัน';
                        }
                    }
                }


                if (isset($vc['format']) && $vc['format'] == 'password') {
                    if (!vailPassword($result[$kc])) {
                        $datas['errors'][$kc] = 'กรุณากรอกรหัสผ่าน อย่างน้อย 8 ตัวขึ้น ';
                    }
                }
            }
        }

        $datas['result'] = $result;
        return $datas;
    }
}

if (!function_exists('SendEmail')) {
    function SendEmail($data = array())
    {

        // var_dump($data);exit;

        $email = service('email');

        $config = array(
            'protocol' => 'smtp', // 'mail', 'sendmail', or 'smtp',
            'SMTPHost' => 'mail.musionnext.com',
            'SMTPPort' => 465, //465
            'SMTPUser' => 'nattasit@musionnext.com', // change it to yours test@musionnext.com
            'SMTPPass' => 'EsNdPwQxiNzFIK', // change it to yours AGgA9ZDa7gZz
            'SMTPCrypto' => 'ssl', //can be 'ssl' or 'tls' for example
            'mailType' => 'html',
            'smtp_timeout' => '5', //in seconds
            'charset' => 'UTF-8',
            'wordwrap' => TRUE
        );


        $email->initialize($config);
        $email->setFrom('nattasit@musionnext.com', 'Supporter');

        $email->setTo($data['email']);
        $email->setSubject($data['email_Subject']);
        $email->setMessage($data['email_template']); //your message here

        // $email->send();
        // $dataa = $email->printDebugger(['headers']);
        // arr($dataa);
        // exit;
        // $errors = array(
        //     'success' => 1,
        //     'message' => $data['message'],
        //     'html' => $data['html']
        // );
        // echo json_encode( $errors );
        // return;

        $html = !empty($data['html']) ? $data['html'] : '';
        $redirect = !empty($data['redirect']) ? $data['redirect'] : '';

        if ($email->send()) {
            $errors = array(
                'success' => 1,
                'message' => $data['message'],
                'html' => $html,
                'redirect' => $redirect,
                'err' => $email->printDebugger()
            );
            echo json_encode($errors);
        } else {
            $errors = array(
                'success' => 0,
                'message' => 'ขออภัยระบบมีปัญหา กรุณาลองใหม่อีกครั้ง'
            );
            echo json_encode($errors);
        }


    } //end send Emaill Function
}

if (!function_exists('getThaiShortMonths')) {
    function getThaiShortMonths()
    {
        $months = [
            '01' => 'ม.ค.', '1' => 'ม.ค.', '02' => 'ก.พ.', '2' => 'ก.พ.', '03' => 'มี.ค.', '3' => 'มี.ค.', '04' => 'เม.ย.', '4' => 'เม.ย.', '05' => 'พ.ค.', '5' => 'พ.ค.', '06' => 'มิ.ย.', '6' => 'มิ.ย.', '07' => 'ก.ค.', '7' => 'ก.ค.', '08' => 'ส.ค.', '8' => 'ส.ค.', '09' => 'ก.ย.', '9' => 'ก.ย.', '10' => 'ต.ค.', '11' => 'พ.ย.', '12' => 'ธ.ค.'
        ];
        return $months;
    }
}

if (!function_exists('getThaiLongMonths')) {
    function getThaiLongMonths()
    {
        $months = [
            '01' => 'มกราคม', '1' => 'มกราคม', '02' => 'กุมภาพันธ์', '2' => 'กุมภาพันธ์', '03' => 'มีนาคม.', '3' => 'มีนาคม', '04' => 'เมษายน', '4' => 'เมษายน', '05' => 'พฤษภาคม', '5' => 'พฤษภาคม', '06' => 'มิถุนายน', '6' => 'มิถุนายน', '07' => 'กรกฎาคม', '7' => 'กรกฎาคม', '08' => 'สิงหาคม', '8' => 'สิงหาคม', '09' => 'กันยายน', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
        ];
        return $months;
    }
}

if (!function_exists('dateFormat')) {
    function dateFormat($date)
    {
        $months = getThaiLongMonths();
        $Y = substr($date, 0, 4);
        $M = substr($date, 5, 2);
        $d = intval(substr($date, 8, 2));

        $H = substr($date, 11, 2);
        $i = substr($date, 14, 2);

        return 'วันที่ ' . $d . ' ' . @$months[$M] . ' ' . $Y . ' เวลา ' . $H . ':' . $i . ' น.';
    }

    function dateOnly($date)
    {
        $months = getThaiLongMonths();
        $Y = substr($date, 0, 4);
        $M = substr($date, 5, 2);
        $d = intval(substr($date, 8, 2));

        $H = substr($date, 11, 2);
        $i = substr($date, 14, 2);

        return  $d . ' ' . @$months[$M] . ' ' . $Y;
    }
}
if (!function_exists('getDb')){
	function getDb()
	{
	
		//require_once 'app/models/admin/Db_model.php';
	
		return new Db_model();
	}
}



function getVdos( $limit = 6 ) {

    $sql = "
		SELECT
			tb_vdos.*,
			tb_catalog.name as catalogName,
			tb_catalog.id as catalogId,
			tb_catalog.cat_type,
			tb_ecom_file.img_path,
			aa_users.first_name
		FROM tb_vdos
		LEFT JOIN tb_catalog ON tb_catalog.id = tb_vdos.cat_id
		LEFT JOIN tb_ecom_file ON tb_ecom_file.file_ref_id = tb_vdos.id
		LEFT JOIN aa_users ON tb_vdos.user_id = aa_users.id
		WHERE tb_vdos.status_id = 1
		GROUP BY
			tb_vdos.id
		ORDER BY
			tb_vdos.time_update DESC
		LIMIT $limit
	";
	// tb_vdos.release_time < NOW()
	// 	AND upload_video IS NOT NULL
	
 
    $news = array();

    foreach ( getDb()->fetchAll( $sql ) as $ka => $va ) {
		
        date_default_timezone_set('Asia/Bangkok');
        $now = date('Y-m-d H:i:s');
        $date = $va->time_update;
        $test = (strtotime($now) - strtotime($date)) / (60 * 60 * 24);
        $test2 = (strtotime($now) - strtotime($date)) / (60 * 60);

        // var_dump($test,$test2);exit;

        if ($test2 < 24) {
            if ($test2 < 1) {
                $txt = 'ไม่ถึง 1 ชั่วโมง';
            }
            if ($test2 > 1) {
                $txt = intval($test2) . ' ชั่วโมงที่แล้ว';
            }
        } else {
            if ($test < 1) {
                $txt = 'ไม่ถึง 1 วัน';
            }
            if ($test > 1) {
                $txt = intval($test) . ' วันที่แล้ว';
            }
        }

		//if( !empty( $va->upload_video ) ) {
		if( false ) {
			
			$vdo1 = '
				<video style="width: 100%; background-color: black;" controls>
					  <source src="'. $va->upload_video .'" type="video/mp4">
					 
					  Your browser does not support HTML video.
				</video>	 					
			';
			
			
			$vdo2 = '
				<video width="400" style="width: 100%;" controls>
					  <source src="'. $va->upload_video .'" type="video/mp4">
					 
					  Your browser does not support HTML video.
				</video>	 					
			';
		}
		else {

			

			if(!empty($va->img)){
				$myImg = '<img class="my-ifrm" style="width: 100%; object-fit: cover;" src="'.$va->img.'"> ';
				
			}else{
				$ex = explode('/', $va->youtube_link);

				$code = $ex[count($ex) - 1];

				$myImg = '<img class="my-ifrm" style="width: 100%;" src="https://img.youtube.com/vi/' . $code . '/0.jpg" />';
				
				// $vdo1 = '
				// 	<video style="width: 100%; height:270px" src="' . $va->youtube_link . '" title="' . $va->name . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></video>
				// ';

				
			}

			$vdo1 = $myImg;
				
				
			$vdo2 = '
				<video style="width: 100%; height:400px" src="' . $va->youtube_link . '" title="' . $va->name . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></video> 					
			';
			
			
			$vdo2 = '
				<iframe style="" src="' . $va->youtube_link . '" title="' . $va->name . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe> 					
			';
			
			
		}

		
		

        $news[] = '
			<div class="col-lg-4 col-xl-4 col-md-6 col-12">
				<div class="container-fluid" style="padding: 0px;">
					<div class="">
						<a href="#" href="#" data-bs-toggle="modal" data-bs-target="#Modal' . $va->id . '">
							<div class="">

								'. $vdo1 .'

							</div>
							<div style="margin-top: 10px; height: 90px">
								<p class="text-muted">' . dateOnly($va->time_update) . '</p>
								<p class="limit-line-newscol">' . $va->name . '</p>
							</div>
							
						</a>
						<div class="row justify-content-evenly">

							<div class="col">									

								 
							</div>
							
						</div>
					</div>
				</div>
				<!-- Modal -->
				<div class="modal fade" id="Modal' . $va->id . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg" >
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">

							  '. $vdo2 .'
								<p class="text-muted">' . dateOnly($va->time_update) . '</p>
								<p class="limit-line">' . $va->name . '</p>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary close-video" data-bs-dismiss="modal">ปิด</button>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		';
    }
	
    $html['news'] = implode('', $news );
    return implode( "", $news );
}





function getDayHour($date1, $date2)
{

    // $HourLastest = NULL;
	$remain=intval(strtotime($date2)-strtotime($date1));
	$Day = floor($remain/86400);
    $l_wan = $remain%86400;
    $Hour = floor($l_wan/3600);
    $l_hour = $l_wan%3600;
    $minute = floor($l_hour/60);
    $second = $l_hour%60;

	// var_dump($second);
    // $Hour = (strtotime($date2) - strtotime($date1)) /  (60 * 60);  // 1 Hour =  60*60
	$Lastest = $second.' วินาทีแล้ว';
    if ($Hour < 24) {
        if ($Hour < 1) {
				if($minute < 1 ){
					$Lastest = $second.' วินาทีแล้ว';
				}else{
					$Lastest = $minute.' นาทีแล้ว';
				}
				
				// return $Lastest;
        } 
		else if ($Hour > 1) {
            $Lastest = intval($Hour) . ' ชั่วโมงที่แล้ว';
			// return $Lastest;
        }
    } else {
        // $Day = (strtotime($date2) - strtotime($date1)) /  (60 * 60  * 24); //  1 day = 60*60*24

        if ($Day < 1) {
            $DayLastest = 'ไม่ถึง 1 วัน';
        }else
        if ($Day > 1) {
            $DayLastest = intval($Day) . ' วันที่แล้ว';
        }
        $Lastest = $DayLastest;
		// return $Lastest;
    }
	// exit;
    return $Lastest;
}


function getTopBanner()
{


    $sql = "
		SELECT
			b.*,
			(
				SELECT
					img_path
				FROM tb_ecom_file WHERE tb_name = 'tb_banners'
				AND file_ref_id = b.id
				ORDER BY b.time_update DESC
				LIMIT 0, 1
			) as img_path
		FROM tb_banners b
		WHERE b.position = 1
		AND b.status_id = 1
		ORDER BY b.time_update DESC
		LIMIT 0, 1
	";

    foreach (getdb()->fetchAll($sql) as $ka => $va) {

        //arr( $va );
        return '
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-lg-12 col-md-12 col-sm-12 mobile-banner">
						<img src="' . $va->img_path . '" alt="" class="reponse-banner">
					</div>
				</div>
			</div>

			<style>
				@media screen and (max-width: 992px) {
					.mobile-banner{
						margin: 0;
						padding: 0;
					}
				}
			</style>

		';

	}

	return '';
	return '
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12">
					<img src="front/assets/img/header.png" alt="" style="width: 100%; height: 90%; border-radius: 3px;">
				</div>
			</div>
		</div>

	';
}

function updateBannerPosition($params = array())
{

    $sql = "

		UPDATE tb_banners
		SET position = NULL
		WHERE id != " . $params['parent_id'] . "
		AND position = " . $params['datas']['position'] . "
	";

    getDb()->execDatas($sql);
}

//$params['youtubelink']
function embedvideo($params = array())
{
    $youtube_id = NULL;

    $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
    $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';

    if (preg_match($longUrlRegex, $params['youtubelink'], $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }

    if (preg_match($shortUrlRegex, $params['youtubelink'], $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }

    if (empty($youtube_id)) {
        return false;
    }

    return 'https://www.youtube.com/embed/' . $youtube_id;

    // $embed = str_replace( "watch?v=", "embed/", $params['youtubelink'] );


}


function setRolePermission( $params = array()){


	$sql = "
		DELETE FROM role_permission WHERE role_id = ". $params['parent_id'] ."
	";
	getDb()->execDatas( $sql );

	foreach( $params['myRequest']['views'] as $kv => $vv ) {

		$sql = "

			INSERT INTO role_permission ( role_id, page_id, read_only  )

			VALUES ( ". $params['parent_id'] .", ". $kv .", 0 );
		";

		getDb()->execDatas( $sql );

	}
	//arr($params);


	//exit;
}



function getSocialIcons( $type = 1 ) {

	$dao = getDb();

	$sql = "
		SELECT

			n.*,



			( SELECT img_path FROM tb_ecom_file WHERE file_ref_id = n.id and tb_name = 'tb_social_media_links' ORDER BY file_ordering ASC LIMIT 0, 1 ) as img
		FROM tb_social_media_links n



		[WHERE]


	";



	$filters['WHERE'][] = "n.status_id = 1";



	$sql = genCond_( $sql, $filters );

//arr( $sql );


	$list = array();
	foreach ( $dao->fetchAll($sql) as $ka => $va ) {

		if( $type == 1 ) {
			$list[] = '<li class=""><a href="'. $va->link .'">
				<img style="'. $va->style .'" src="'. $va->img .'" alt="'. $va->name .'" class="imggroup"></a></li>';
		}
		else {

			$list[] = '<a href="'. $va->link .'"><img style="'. $va->style .'" src="'. $va->img .'" alt="'. $va->name .'" class="imggroup1"></a>';
		}

	}

	return implode( ' ', $list );
}


function front_menus( $params = array() ){

    $sql = "

		SELECT
			*
		FROM aa_front_page
		WHERE front_menu = 1
		ORDER BY
			order_number ASC
	";

	if( isset( $params['bb'] ) ) {
		$lis = array();

		foreach ( getDb()->fetchAll( $sql ) as $km => $vm ) {

				$lis[] = '
					<li aria-haspopup="true">
						<a href="'. front_link($vm->id) .'">' . $vm->title . '</a>
					</li>
				';

			$active = '';

			if ($params['id'] == $vm->id) {
				$active = ' active ';
			}

			if ($vm->id != 4) {

			} else {

				
			}
		}
	 

	

		return '

			' . implode('', $lis) . '
		';
	}
	else {
		
		$lis[] = '
			<li class="nav-item responhead">

				<span style="position: absolute; z-index: 999; top: 23px; left: 33px; color: #e5e5e5; font-weight: bold;" class="control-navbar-toggler"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
					<path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"></path>
					</svg>
				</span>


				<!--<div style="background-color: #FFCC00;">
					<h1 class="" style="padding-top: 20px; padding-bottom: 20px;text-align: center;border-bottom: 1px solid #FDD400;">
						<img style="width: 50%;" class="" src="'.base_url('front/assets/img/FM91transparentlogo.png').'">
					</h1>
				</div>-->
			</li>
		';

		foreach ( getDb()->fetchAll( $sql ) as $km => $vm ) {

			$active = '';

			if ($params['id'] == $vm->id) {
				$active = ' active ';
			}

			if ($vm->id != 4) {

				$lis[] = '
					<li class="nav-item">
						<a class="nav-link " aria-current="page" href="' . front_link($vm->id) . '"><span class="respon-color underline ' . $active . '">' . $vm->title . '</span></a>
					</li>
					
				';
			} 
			else if($params['id'] == 19){
				$active = ' active ';
				$lis[] = '
					
									
					<li class="nav-item dropdown MobileView">
						<a class="nav-link dropdown-toggle " aria-haspopup="true" href="' . front_link(19) . '" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
				
							<span class="respon-color underline ">' . $vm->title . '</span>
						</a>
						<ul class="dropdown-menu dropdownCus-menu" aria-labelledby="navbarDropdownMenuLink">
							<li><a class="dropdown-item" href="' . front_link(19) . '">ข่าวทั้งหมด</a></li>
							<li><hr class="dropdown-divider"></li>
							' . newsDropDown() . '
						</ul>					
						
						
					</li>

					<li class="nav-item dropdownCus webview">
					<a class="nav-link" aria-haspopup="true" href="' . front_link(19) . '"  id="navbarDropdownMenuLink" role="button" aria-expanded="false">
						<span class="respon-color underline ' . $active . '">' . $vm->title . '</span>
					</a>
					<ul class="dropdown-menu dropdownCus-menu" aria-labelledby="navbarDropdownMenuLink">
						' . newsDropDown() . '
						</ul>
			
					</li>
					
				';
								
			}
			else{									
//MobileView
				$lis[] = '

				
					<li class="nav-item dropdown MobileView">
						<a class="nav-link dropdown-toggle " aria-haspopup="true" href="' . front_link(19) . '" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							<span class="respon-color underline ' . $active . '">' . $vm->title . '</span>
						</a>
						<ul class="dropdown-menu dropdownCus-menu" aria-labelledby="navbarDropdownMenuLink">
							<li><a class="dropdown-item" href="' . front_link(19) . '">ข่าวทั้งหมด</a></li>
							<li><hr class="dropdown-divider"></li>
							' . newsDropDown() . '
						</ul>
						

						
					</li>
					

					<li class="nav-item dropdownCus webview">
						<a class="nav-link" aria-haspopup="true" href="' . front_link(19) . '"  id="navbarDropdownMenuLink" role="button" aria-expanded="false">
							<span class="respon-color underline ' . $active . '">' . $vm->title . '</span>
						</a>
						<ul class="dropdown-menu dropdownCus-menu" aria-labelledby="navbarDropdownMenuLink">
							' . newsDropDown() . '
						</ul>
					
						
					</li>

					
					
				';
			}
		}
		// exit;
	

		// if( isset( $_SESSION['user_id'] ) ) {
		// 	$lis[] = '
		// 		<li class="nav-item responhead">
		// 			<a class="nav-link " aria-current="page" href="' . base_url( 'logout' ) . '"><span class="respon-color underline">LOGOUT</span></a>
		// 		</li>
		// 	';
		// }
		// else {
			
		// 	$lis[] = '
		// 		<li class="nav-item responhead">
		// 			<a class="nav-link " aria-current="page" href="' . front_link( 12 ) . '"><span class="respon-color underline">LOG IN</span></a>
		// 		</li>
		// 	';
		// }

		return '<ul class="navbar-nav nav collapse navbar-collapse justify-content-between" id="navbarNavDropdown">

			' . implode('', $lis) . '
			<div class="MobileView">
				<div class="row">
					<ul class="socialicons d-flex justify-content-end nav-link" style="margin: 0; position: relative;">
					
						<div class="col-3 col-md-3"></div>
						<div class="col-8 col-md-8 d-flex alignment-mobile" style="text-align: center"><li style="list-style: none; text-align: center ">'.getSocialIcons().'</li></div>
						<div class="col-2 col-md-2"></div>
					
										
					</ul>
				</div>
			</div>
		</ul>';
		
	}

}



function front_menus________( $params = array() ){

    $sql = "

		SELECT
			*
		FROM aa_front_page
		WHERE front_menu = 1
		ORDER BY
			order_number ASC
	";

    $lis[] = '
		<li class="nav-item responhead" style="position: relative;">

			<span style="position: absolute;z-index: 999;top: 29px;right: 54px;" class="control-navbar-toggler"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
				<path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"></path>
				</svg>
			</span>

			<h1 class="" style="padding-top: 20px; padding-bottom: 20px;text-align: center;border-bottom: 1px solid #FDD400; font-size: 32px;">
				<span style="/* margin-left: 100px; */ border-bottom: 1px solid #FDD400;">สวพ.FM91</span>

			</h1>
		</li>
	';

    foreach ( getDb()->fetchAll( $sql ) as $km => $vm ) {

        $active = '';

        if ($params['id'] == $vm->id) {
            $active = ' active ';
        }

        if ($vm->id != 4) {

            $lis[] = '
				<li class="nav-item">
					<a class="nav-link " aria-current="page" href="' . front_link($vm->id) . '"><span class="respon-color underline ' . $active . '">' . $vm->title . '</span></a>
				</li>
			';
        } else {

            $lis[] = '
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle " href="' . front_link($vm->id) . '" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
						<span class="respon-color underline ' . $active . '">' . $vm->title . '</span>
					</a>
					' . newsDropDown() . '

				</li>
			';
        }
    }
 

	if( isset( $_SESSION['user_id'] ) ) {
		$lis[] = '
			<li class="nav-item responhead">
				<a class="nav-link " aria-current="page" href="' . base_url( 'logout' ) . '"><span class="respon-color underline">LOGOUT</span></a>
			</li>
		';
	}
	else {
		
		$lis[] = '
			<li class="nav-item responhead">
				<a class="nav-link " aria-current="page" href="' . front_link( 12 ) . '"><span class="respon-color underline">LOG IN</span></a>
			</li>
		';
	}

    return '<ul class="navbar-nav collapse navbar-collapse justify-content-between" id="navbarNavDropdown">

		' . implode('', $lis) . '
	</ul>';
}



function getUserMenu( $type = 1 ) {


	$img = 'front/assets/adminAvatar.png';
	
	
	$userLogin = '';
	if( $type == 1 ) {
		
		$userLogin = '
			<div class="mbs-container" style="padding-bottom: 0;">
				<div class="mbs-login">
					<a href="'.front_link(12).'">LOG IN</a>
					<a href="'.front_link(12,null,array('fmaction' => 'regis')).'">SIGN UP</a>
				</div>
				<div style="width: 30px;">
					<a href="javascript:void(0)" class="mbs-author" tabindex="0"><img src="'.$img.'" style="width: 30px;"> </a>
				</div>
			</div>
		';
	}

	
	
    if( isset( $_SESSION['user_id'] ) ) {

		$sql = '
			SELECT
				aa_users.first_name,aa_users.admin,
				p.img_path
			FROM aa_users
			LEFT JOIN tb_user_profile p ON p.user_id = aa_users.id
			WHERE
				aa_users.id = '. $_SESSION['user_id'] .'
		';
        
		foreach( getdb()->fetchAll( $sql ) as $k => $v ) {
			
			if( !empty( $v->img_path ) ) {
				
				$img = $v->img_path;
			}else{
				$img = 'front/assets/adminAvatar.png';
			}
			
			if( $v->admin == 1) {
				if($type==1){
					$userLogin = '
					<div style="width: 100%;" class="respon-icon">
						<div class="row">
							<div class="col">
								<div class="dropdown">
								<div class="mbs-container" style="float: right;">
									<span> สวัสดีคุณ: '.$v->first_name.' (Admin)</span>
										<div style="width: 30px;">
											<a href="javascript:void(0)" class="mbs-author dropdown-toggle" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" tabindex="0"><img src="'.$img.'" style="width: 30px;"> </a>
												<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
													<li><a class="dropdown-item" href="'.front_link(109).'">ADMINISTRATOR</a></li>
													<li><a class="dropdown-item" href="'.front_link(16).'">HISTORY</a></li>
													<li><a class="dropdown-item" href="'.front_link(124).'">EDIT PROFILE</a></li>
													<li><a class="dropdown-item" href="'.base_url( 'logout' ).'">LOGOUT</a></li>
												</ul>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
				';
				}else{
					$userLogin = '
					<a href="javascript:void(0)" class="mbs-author dropdown-toggle" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" tabindex="0"><img src="'.$img.'" style="width: 30px;"> </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1" style="">
                            <li><a class="dropdown-item" href="'.front_link(109).'">ADMINISTRATOR</a></li>
							<li><a class="dropdown-item" href="'.front_link(16).'">HISTORY</a></li>
							<li><a class="dropdown-item" href="'.front_link(124).'">EDIT PROFILE</a></li>
							<li><a class="dropdown-item" href="'.base_url( 'logout' ).'">LOGOUT</a></li>
                        </ul>
				';
				}
				
			}else if($v->admin == 0){
				if($type==1){
					$userLogin = '
					<div style="width: 100%;" class="respon-icon">
						<div class="row">
							<div class="col">
								<div class="dropdown">
								<div class="mbs-container" style="float: right;">
									<span> สวัสดีคุณ: '.$v->first_name.' (User)</span>
										<div style="width: 30px;">
											<a href="javascript:void(0)" class="mbs-author dropdown-toggle" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" tabindex="0"><img src="'.$img.'" style="width: 30px;"> </a>
												<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
													<li><a class="dropdown-item" href="' . front_link(124) . '">My ACCOUNT</a></li>
													<li><a class="dropdown-item" href="'.front_link(17).'">FOR YOU</a></li>
													<li><a class="dropdown-item" href="'.front_link(16).'">HISTORY</a></li>
													<li><a class="dropdown-item" href="'.front_link(124).'">EDIT PROFILE</a></li>
													<li><a class="dropdown-item" href="'.base_url( 'logout' ).'">LOGOUT</a></li>
												</ul>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
				';
				}else{
					$userLogin = '
					<a href="javascript:void(0)" class="mbs-author dropdown-toggle" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" tabindex="0"><img src="'.$img.'" style="width: 30px;"> </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1" style="">
							<li><a class="dropdown-item" href="' . front_link(124) . '">My ACCOUNT</a></li>
                            <li><a class="dropdown-item" href="'.front_link(17).'">FOR YOU</a></li>
							<li><a class="dropdown-item" href="'.front_link(16).'">HISTORY</a></li>
							<li><a class="dropdown-item" href="'.front_link(124).'">EDIT PROFILE</a></li>
							<li><a class="dropdown-item" href="'.base_url( 'logout' ).'">LOGOUT</a></li>
                        </ul>
				';
				}
				
			}
			
			
		}


    }
	

	 
	return $userLogin;
}



//$params['limit']  'LIMIT 0, 1';
function radioPageF( $params = array() ) {

 //arr( $params['id'] );exit;
 
	if( $params['id'] == 9 ) {
		
		return false;
	}

	if( $params['id'] == 1 ) {
		
		return false;
	}

	 

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
  

	$html['img'] = '';
	
	foreach ( getDb()->fetchAll( $sql ) as $ka => $va ) {

		if (!empty($params['myid'])) {

			$playId = $params['myid'];
		} else if ($ka == 0) {

			$playId = $va->id;
		}

		if ( $playId == $va->id ) {

		} else {

			 
		}
		
		return '
			<br><br>
			<div class="container my-radio-footer">
				<div class="row">
					<div class="col-lg-2"></div>

					<div class="col-lg-8 radio-main">
						<div class="radio-dvd-main">
							<div class="radio-dvd-sub">
								<img src="front/assets/img/icon/dvd.png" alt="" class="radio-dvd-img">
							</div>
						</div>
						<div class="container-fluid justify-content-between radio-logo">
							<div class="d-flex logo-radio">
								<img src="front/assets/img/icon/live.png" alt="" class="radio-logo-img">
								<span class="radio-logo-span">' . $va->name . '
								</span>
							</div>
							<div class="d-flex justify-content-end">
								<audio controls="" loop="" >
									<source src="' . $va->source . '" type="audio/mp3">
								</audio>
							</div>
						</div>
					</div>


					<div class="col-lg-2"></div>
				</div>
			</div>
			
			<br><br>

		';
		
	}

	//$html['radioHtml'] = implode('', $radioHtml );

	return '';
}



function getAds( $params = array() ) {
	
	// arr( $params );
	if(  in_array($params['id'],array(1,124)) ) {
		return '';
	}
	
	return '
	
	<div class="container my-radio-footer" style="">
		<img src="front/ads.png" class="img-fluidd" alt="">
	</div>
	';
}


function getAdminMenu( $parent_id = NULL, $getAdminPage = false, $getAll = 'aaaaaaaaaaaaaaa', $params = array()  ) {
	
//arr( $params );	exit;
	
	$filters = array();
	
	if( $getAll == 'bbbbbbbbbbbb' ) {
		
		 

		if( $getAdminPage == false ) {

			if( empty( $parent_id ) ) {

				$filters['WHERE'][] = " parent_id IS NULL";
			}
			else {

				$filters['WHERE'][] = "parent_id = ". $parent_id ."";
			}
		}

		$filters['WHERE'][] = "user_login = 1 AND ( admin_menu IS NOT NULL OR admin_menu != '0' ) AND active = 1 ";

		$filters['WHERE'][] = "( every_body_login != 1 )";
	}
	else {
		
		if( $getAdminPage == false ) {

			if( empty( $parent_id ) ) {

				$filters['WHERE'][] = " parent_id IS NULL";
			}
			else {

				$filters['WHERE'][] = "parent_id = ". $parent_id ."";
			}
		}

		$filters['WHERE'][] = "user_login = 1 AND ( admin_menu IS NOT NULL OR admin_menu != '0' ) AND active = 1 ";

		$filters['WHERE'][] = "
			(
				id IN ( SELECT page_id FROM role_permission WHERE role_id = ". $_SESSION['u']->role_id ."  )
				
				OR 
				
				every_body_login = 1
			)

		";
	}

	

	$lis = array();

	$sql = "
		SELECT
			*
		FROM aa_front_page
		[WHERE]
		ORDER BY
			order_number ASC
	";


	$sql = genCond_( $sql, $filters );


	if( $getAdminPage == true ) {

//arr( $sql ); 
		return getDb()->fetchAll( $sql );
	}



	foreach( getDb()->fetchAll( $sql ) as $ka => $va ) {

		$getAdminMenu = getAdminMenu( $va->id );

		$ul = '';

		if( !empty( $getAdminMenu ) ) {
			$ul = '<ul class="sub-menu">'. $getAdminMenu .'</ul>';
		}

		$menu_link = front_link( $va->id );

		$test = json_decode( $va->admin_menu );

		if( isset( $test->link_to ) ) {
			$menu_link = front_link( $test->link_to->id, $test->link_to->sub );

		}
//arr( $params );	exit;

		$active = '';
		if( isset( $params['id'] ) && $va->id == $params['id'] ) {
			$active = ' active ';
		}
		$lis[] = '
			<li aria-haspopup="true" class="'. $active .'">
				<a href="'. $menu_link .'" >'. $va->title .'</a>'. $ul .'
			</li>
		';
	}

	return implode( '', $lis );


}


