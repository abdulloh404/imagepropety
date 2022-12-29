<?php
namespace App\Models;

use App\Models\Db_model;

use App\Models\Auth_model;

use CodeIgniter\Model;

class Notification_model extends Db_model{
    public function __construct() {
	   parent::__construct();
    }

    public function getAll() {
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT * FROM tb_notifications as a WHERE user_id='{$user_id}';";
        $notifications = $this->fetchAll($sql);

        $params = [];

        $params['html'] = '<div class="card-body" style="padding:20px">
				<div class="container">
					<div class="col-lg-3">
						<label class="rdiobox"><input name="rdio" type="radio"> <span>ทำเครื่องหมายอ่านทั้งหมด</span></label>
					</div>
				</div>';
        foreach($notifications as $noti) {
            $params['html'] .= '<div class="container bg-control">
						<div class="row">
							<div class="col-md-2">
								<img alt="avatar" class="" src="'.base_url().'/'.$noti->image.'" width="100px" height="100px">
							</div>
							<div class="col-md-8">
								<h6>'.trim($noti->title).'</h6>
								<p>'.trim($noti->message).'</p>
								<p>เมื่อ'.dateFormat($noti->created_date).'</p>

							</div>
							<div class="col-md-2">
								<a class="btn btn-outline-primary btn-block" href="'.front_link(107, $noti->order_product_id).'">ดูข้อมูลการสั่งซื้อ</a>
							</div>
						</div>
					</div>';
        }
        $params['html'] .= '</div>';
        return $params;
    }
}
