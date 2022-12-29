<?php

namespace App\Models;


use CodeIgniter\Model;
use App\Models\Db_model;

class ConfigForm extends Model {
	
	

	
	//
	//
	public function viewOverride() {


		if ( isset( $_REQUEST['config_override_id'], $_REQUEST['ajax'] ) ) {
			
			$data['config_override_id'] = $_REQUEST['config_override_id'];
			
			foreach( $this->webCloneDb as $kdb => $vdb ) { 
			
				$this->dao->update_( ''. $vdb .'.admin_model_config_columns', $data, "config_columns_id = " . $_REQUEST['config_columns_id'] );
			}
			
			
			header( 'Location:' . getLink( $this->getView->model_id, array( ex( 2 ) ) ) );
			exit;
		}


		$cond = '';
		if ( isset( $_REQUEST['new_config_id'] ) ) {

			$cond = "WHERE config_id = ". $_REQUEST['new_config_id'];
		} else {
			$cond = "WHERE config_columns_id = ". $_REQUEST['config_columns_id'];

		}

		$sql = "
			SELECT
				config_columns_id,
				config_columns_name
			FROM admin_model_config_columns [cond]";

		$sql = str_replace( '[cond]', $cond, $sql );

		$res_columns = $this->dao->fetchAll( $sql );

		if ( isset( $_REQUEST['ajax'] ) ) {
			$html = '';
			foreach ( $res_columns as $ka => $va ) {
				$html .= '<option value="'. $va->config_columns_id .'">'. $va->config_columns_name .'</option>';
			}
			echo $html;

			exit;
		}

		$sql = "
			SELECT
				a.new_config_id,
				a.model_alias
			FROM admin_model a
			WHERE a.new_config_id IS NOT NULL
			AND a.model_name = 'a_standard_form'";

		$res_config = $this->dao->fetchAll( $sql );



		$sql = "

			SELECT
				a.config_override_id,
				b.config_columns_name,
				b.config_id
			FROM  admin_model_config_columns a
			LEFT JOIN admin_model_config_columns b ON a.config_override_id = b.config_columns_id
			WHERE a.config_columns_id = ". $_REQUEST['config_columns_id'];
		$res_columns = $this->dao->fetchAll( $sql );

		//arr( $res_columns );
		require_once 'view_override.php';

	}

	
	

	//
	//
	public function loadRequestField_( $data, $all_data = NULL ) {


		$arr = array( 'tb_sub', 'help_filter', 'form_config_id', 'help_advance_filter' );

		foreach ( $arr as $va ) {
			if ( empty( $data->$va ) ) {
				$data->$va = null;
			}
		}


        $allFiledOptionRequest = array();

		$this->delete_one_request = '<a class="web-bt delete-one-request">-</a>';
        if ( !empty( $data->request_key ) ) {


			if ( count( $data->request_key ) == 1 )
				$this->delete_one_request = '';

            foreach ( $data->request_key as $ka => $va ) {
                $allFiledOptionRequest[] = '<div class="content-all-request">' . allFiledOption( $data->tb_sub, $name = 'request[helpdetail][request_key][]', $va, 'abc' ) . ' '. $this->delete_one_request .'</div>';
            }
        }
        else if ( !empty( $data->new_request ) ) {


        }
		else {
            $allFiledOptionRequest[] = '
			<div class="content-all-request">' . allFiledOption( 'aaaaaaaaa', $name = 'request[helpdetail][request_key][]', NULL, 'abc' ) . '</div>';
        }

        $orderBy = array();
        if ( !empty( $data->orderBy ) ) {
           
            $count_order_by = 0;
            foreach ( $data->orderBy as $ka => $va ) {
                ++$count_order_by;
            }
        
            foreach ( $data->orderBy as $ka => $va ) {
                $deleteButton = '';
                if ( $count_order_by != 1 )
                    $deleteButton = $this->deleteButton;

                $orderBy[] = '<div class="content-all-field">' . allFiledOption( $data->tb_sub, '', $ka, 'select-order-by' ) . ' <span><input type="text" name="request[helpdetail][orderBy]['. $ka .']" value="'. $va .'"> '. $deleteButton .'</span> </div>';
               
            }
        } else {

            $orderBy[] = '<div class="content-all-field">' . allFiledOption( $data->tb_sub, '', '', 'select-order-by' ) . ' <span></span> </div>';

        }

		$json['html'] = '
			<script>
			var emptyField = '. json_encode( $this->gogo2( $data->tb_sub ) ) .';
			var emptyOrder = '. json_encode( $this->abc( $data->tb_sub ) ) .';

			</script>
			<div class="h-10"></div>
			<div class="content">
				<table class="table_">
					<tbody>

						<tr>
							<th style="vertical-align: top;">Help Filter</th>
							<td><textarea name="request[helpdetail][help_filter]">'. $data->help_filter .'</textarea></td>
						</tr>

						<tr>
							<th style="vertical-align: top;">Advance Filter</th>
							<td><textarea name="request[helpdetail][help_advance_filter]">'. $data->help_advance_filter .'</textarea></td>
						</tr>



						<tr>
							<th style="vertical-align: top;">Ex.</th>
							<td>{"rci_stock_products":["stock_products_code"]}</td>
						</tr>



						<tr>
							<th style="vertical-align: top;">Request Key</th>
							<td>'. implode( ' ', $allFiledOptionRequest ) .'</td>
						</tr>
						<tr>
							<th>Click to add new request</th>
							<td><a class="web-bt add-new-request">+</a></td>
						</tr>

					</tbody>
				</table>
			</div>';
        $json['empty_field'] = $this->gogo2( $data->tb_sub );

		return $json;
	}

	//
	//
	public function getFormGroup() {
		
	
		$keep = array();
		if ( !empty( $this->config->formGroup ) ) {
			foreach ( $this->config->formGroup as $ka => $va ) {
				

				$id = $va;

				$getReportFilterConfig = getReportFilterConfig( $id );

				$getReportFilterConfig = json_decode( $getReportFilterConfig );


				$name = $getReportFilterConfig->name;
				$keep[] = '

					<tr class="ui-sortable-handle">

						<td><input checked type="checkbox" value="'. $id .'" name="formGroup[]" /></td>

						<td>
							'. $id .' '. $getReportFilterConfig->label .' : '. $getReportFilterConfig->name .'<br>
							<textarea name="textReplace['. $id .']">'. $this->config->textReplace->$id .'</textarea>
						</td>
						<td>'. $getReportFilterConfig->type .'</td>
						
						<td><input type="text" name="fromPrefix['. $id .']" value="'. $this->config->fromPrefix->$id .'"></td>
					</tr>
				';
			}
			
		}

		$html[] = '
			<label>Sort Form Group</label><br>
{"report_header":"สต็อคคงเหลือในช่วง [report_date0] - [report_date1]","sql":"a.doc_date <= \'[report_date1]\'","param":{"[company_id]":{"type":"session","name":"company_id"}}}
			<table class="flexme3 sorttable">

				<tbody class="">'. implode( $keep ) .'</tbody>
			</table>
		';

		//
		//
		$keep = array();
		foreach ( getReportFilterConfig( $index = false ) as $ka => $va ) {

			if ( !empty( $this->config->formGroup ) ) {
				if ( in_array( $ka, $this->config->formGroup ) )
					continue;
			}


			$va = json_decode( $va );

			$keep[] = '

				<tr class="ui-sortable-handle">

					<td><input type="checkbox" value="'. $ka .'" name="formGroup[]" /></td>

					<td>'. $ka .''. $va->label .'</td>
					
					<td>'. $va->type .'</td>
					
					
				</tr>
			';
		}

		$html[] = '
			<label>Select Form Group</label>
			<table class="flexme3">

				<tbody class="ui-sortable">'. implode( $keep ) .'</tbody>
			</table>
		';
		return implode( '', $html );

	}




	//
	//
	public function updateColumnWidth() {

		$sql = "
			SELECT
				*
			FROM admin_model_config_columns
			WHERE config_columns_id = " . $_REQUEST['config_columns_id'];


		$res = $this->dao->fetch( $sql );

		//$detail = json_decode( stripcslashes( $res->config_columns_detail ) );

		//$detail->w = $_REQUEST['w'];


		$data['config_columns_w'] = $_REQUEST['w'];

		foreach( $this->webCloneDb as $kdb => $vdb ) {
			
			$this->dao->update_( $vdb .'.admin_model_config_columns', $data, "config_columns_id = " . $res->config_columns_id );
		
		}





	}



	//
	//
	public function updateHeadDoc() {

		$data['config_doc_head_id'] = $_REQUEST['config_doc_head_id'];

		$this->dao->update_( 'admin_model_config', $data, "config_id = " . $this->config->config_id );
		header( 'Location:' . getLink( $this->getView->model_id, array( $this->config->config_id ) ) );

	}

	//
	//
	public function viewCopy() {
		
		$table_name = 'admin_model_config_columns';


		$skip = array();
		if ( isset( $_REQUEST['config_columns_name'] ) ) {

			if(   isset( $_REQUEST['action_type'] ) && $_REQUEST['action_type'] == 1   ) {
				
				if( !empty( $_REQUEST['override'] ) ) {
					
					$config_override_id = 'config_columns_id';
					
				}
				else {
					
					$config_override_id = json_encode( NULL );
				}
				
				$ex = explode( ',', $_REQUEST['config_id'] );
				
				foreach( $ex as $kc => $vc ) {
					
					$config_columns_id = getSkipId( $table_name, 'config_columns_id', $skip );
					
					$skip[] = $config_columns_id;
					
					
					$sqlUnion[] = "
					
						SELECT
							". $config_columns_id ." as config_columns_id,
							". $vc ." as config_id,
							'". $_REQUEST['config_columns_name'] ."' as config_columns_name,
							config_columns_detail,
							'". $_REQUEST['config_columns_label'] ."' as config_columns_label,
							". $config_override_id ." as config_override_id,
							c, 
							config_columns_w, 
							config_columns_position, 
							config_columns_order
						FROM ". $table_name ."
						WHERE config_columns_id = " . ex( 4 );
				
				}
				
				
				 
					
					$sql = "
						REPLACE INTO  ". $table_name ." (
							config_columns_id,
							config_id,
							config_columns_name,
							config_columns_detail,
							config_columns_label,
							config_override_id,
							c, 
							config_columns_w, 
							config_columns_position, 
							config_columns_order
						)
						Select
							new_tb.*
						FROM(
						". implode( ' UNION ', $sqlUnion ) ."
						) as new_tb";	
						
					$this->dao->execDatas( $sql );
				
				 
				
				
				
			}
			else {
				 
					
					
					$sql = "
						UPDATE admin_model_config_columns 
						SET 
							config_id = ". $_REQUEST['config_id'] .",
							config_columns_name = 'daffdjdflk'
						WHERE config_columns_id = " . ex( 4 );
					
					$this->dao->execDatas( $sql );
				 
				
				
				
			}

			header( 'Location: '. comeBack() .'' );
			//redirect( getLink( $this->getView->model_id, array( ex( 2 )  ) ) );

			//header( 'Location:' . getLink( $this->getView->model_id, array( ex( 2 ) ) ) );

			exit;
		}
		
		$sql = "
			SELECT
				*
			FROM ". $table_name ."
			WHERE config_columns_id = " . ex( 4 );

		foreach( $this->dao->fetchAll( $sql ) as $kr => $res ) {
			
			return '
				
				<form method="get">
					<input type="hidden" name="ajax" value="1" />
					<div class="content">
					
						<label>Action</label><br>
						<label>
							<input checked type="radio" name="action_type" value="1" /> copy
						</label>
						
						<label>
							<input type="radio" name="action_type" value="2" /> move
						</label>
						
						<div class="h-10"></div>
						
						<label>override</label><br>
						<input type="checkbox" name="override" value="1" />
						
						<div class="h-10"></div>
						
						
						
						<label>Config Id</label>
						<input type="text" name="config_id" value="'. $res->config_id .'" />
						
						<div class="h-10"></div>

						
						<label>Copy Name</label>
						<input type="text" name="config_columns_name" value="'. $res->config_columns_name .'" />
						
						<div class="h-10"></div>
						
						<label>config_columns_label</label>
						<input type="text" name="config_columns_label" value="'. $res->config_columns_label .'" />
						
						<div class="h-10"></div>
						
						
						<div class="clear-fix">
							<input type="submit" value="Save" class="web-bt fl" />
							<input type="button" value="Reset" class="web-bt fl reset-form" />
							<a class="web-bt fr" href="'. comeBack() .'" >Back</a> 
						</div>
					</div>
				</form>
			';
			
		}
		

	}

	//

	//
	//
	public function viewDelete() {

		$data['config_columns_id'] = $_REQUEST['config_columns_id'];


		 
			
		$this->dao->delete_( 'admin_model_config_columns', $data );
		
		 

		header( 'Location: '. comeBack() .'' );
		
		exit;

	}


	//
	//
	public function viewAdd() {
	 

	}

    //
    //
    function __construct( $getView = array() ) {
		
		//$this->getView = json_decode( NULL );
		$this->dao = new Db_model();
		
		$this->config = getConfig( ex( 2 ) );
		
		return;
		$_REQUEST = $this->input->get();
		
		
		//arr($this->input->get());
	/*

		if ( !empty( $_REQUEST['ajax'] ) ) {
			
			arr( $_REQUEST );
			exit;
		}
*/
		$this->userData = $this->session->all_userdata();
		//arr( $this->userData['user_id'] );
		
		//exit;
		$this->load->admin_model( 'db_model' );
		
		$this->dao = $this->db_model;
		
		
		
		 
		
		$webCloneDb = array( $this->db->database );
	

		$this->webCloneDb = $webCloneDb;

		$this->deleteButton = '<a class="web-bt delete-one">-</a>';

		 

 
 
		

 
//arr( $this->config );
//exit;
		//call_user_func( array( $this, 'index' ) );
    }


	//
	//
	public function switchLine() {

		$i = 0;
		foreach ( $_REQUEST['request'] as $ka => $va ) {
			++$i;
			$data['config_columns_order'] = $i;
			
			
			$this->dao->update_( 'admin_model_config_columns', $data, "config_columns_id = " . $va );
			
			
		}
	}

	//
	//
	public function loadHelpDetail_( $data = NULL ) {

        if ( empty( $data ) ) {

            $tb_sub = null;
            $label = null;
            $type = null;

        } else {
           // $url = $data->url;
            $label = $data->label;
            $tb_sub = $data->tb_sub;

			$type = $data->type;
        }

		$loadRequestField_['html'] = '';
		
		if ( !empty( $data ) )
			$loadRequestField_ = $this->loadRequestField_( $data );
		
		$html = '
			<div class="h-10"></div>
			<div class="content">
				<table class="table_">
					<tbody>
						<tr>
							<th>Main Sql</th>
							<td><textarea name="request[helpdetail][main_sql]">'. $data->main_sql .'</textarea></td>
						</tr>
						<tr>
							<th>More Filter</th>
							<td><textarea name="request[helpdetail][more_filter_sql]">'. $data->more_filter_sql .'</textarea></td>
						</tr>

						<tr>
							<th>Ex.</th>
							<td>{"sql":"stock_count_hd_id = ( SELECT stock_count_hd_id FROM tb_stock_add_hd WHERE stock_add_hd_id = [stock_add_hd_id] )","param":{"[stock_add_hd_id]":{"type":"rq","name":"main_id"}}}</td>
						</tr>


						<tr>
							<th>Having</th>
							<td><textarea name="request[helpdetail][having]">'. $data->having .'</textarea></td>
						</tr>

						<tr>
							<th>Ex.ver 2</th>
							<td>{"WHERE":{"sql":"l.saleable = 1"}}</td>
						</tr>
						<tr>
							<th>Ex.</th>
							<td>{"HAVING":{"type":"condition","name":["avilable != 0", "1 = 1"]}}</td>
						</tr>
						<tr>
							<th>Ex.</th>
							<td>{"ap_id":{"type":"rq","name":"main_id"}}</td>
						</tr>

						<tr>
							<th>Table Sub</th>
							<td><textarea name="request[helpdetail][tb_sub]">'. $data->tb_sub .'</textarea></td>
						</tr>

						<tr>
							<th>Description</th>
							<td><textarea name="request[helpdetail][label]">'. $label .'</textarea>
							</td>
						</tr>



						<tr>
							<th>Help Type</th>
							<td>'. option( 'request[helpdetail][type]', $type, $array = array( 'look_comment' => 'look_comment', 'help_full' => 'help_full', 'insert_table' => 'insert_table', 'read_only' => 'read_only' , 'multi_check' => 'multi_check' ), $class = NULL ) .'</td>
						</tr>

						<tr>
							<th>Click Row to Box</th>
							<td><textarea name="request[helpdetail][row_to_box]">'. $data->row_to_box .'</textarea>
							</td>
						</tr>
						<tr>
							<th>Ex.</th>
							<td>( for type insert_table ) {"tables":{"tb_so_ln":{"product_id":"product_code","product_name":"product_name"}}}</td>
						</tr>
						<tr>
							<th>Pri Key</th>
							<td><input type="text" name="request[helpdetail][pri_key]" value="'. $data->pri_key .'" /></td>
						</tr>


						<tr>
							<th style="vertical-align: top;">New Request</th>
							<td><textarea name="request[helpdetail][new_request]">'. $data->new_request .'</textarea></td>
						</tr>
						<tr>
							<th style="vertical-align: top;"></th>
							<td>{"erp_product_group_price":["product_group_price_name"]}</td>
						</tr>
						
						<tr>
							<th style="vertical-align: top;">Model ID ( Link to form )</th>
							<td><input type="text" name="request[helpdetail][form_config_id]" value="'. $data->form_config_id .'" /></td>
						</tr>
						
						
						<tr>
							<th style="vertical-align: top;">อนุญาตเอกสารที่ยังไม่ได้เซ็น</th>
							<td><input type="text" name="request[helpdetail][allow_no_prove]" value="'. $data->allow_no_prove .'" /></td>
						</tr>
						

					</tbody>
				</table>
			</div>';
		return $html;
	}



	//
	//
	public function getKeyName() {
		return array( 4, 6, 2, 99, 0, 1, 5, 7, 'revert_forum', 'forum', 'fix_val', 'status', 'auto', 'on_insert', 'w', 'a' );
	}

	//
	//
    public function gogo2( $tb_name = NULL ) {

		return '<div class="content-all-request">' . allFiledOption( $tb_name, $name = 'request[helpdetail][request_key][]', NULL, 'abc' ) . ' <a class="web-bt delete-one-request">-</a> </div>';
    }

    //
	//
	public function loadRequestField( ) {
        $data = new stdClass();
        $data->tb_sub = $_REQUEST['table'];
        $data->pri_key = NULL;
		$data = $this->loadRequestField_( $data );
        echo json_encode( $data );
	}

	//
	//
	public function abc( $tb_name ) {

		$html = '<div class="content-all-field">'. allFiledOption( $tb_name, $name = NULL, $def_val = NULL, $class = 'select-order-by' ) .' <span></span> </div>';

		return $html;
	}


	//
	//
	function em( $tb_name ) {

		return '<div class="content-all-request">'. allFiledOption( $tb_name, $name = 'request[helpdetail][request_key][]', $def_val = NULL, $class = NULL ) .'</div>';
	}


	//
	//
	public function deleteConfig() {

		if ( !empty( $_REQUEST['ajax'] ) ) {

			$data['config_id'] = ex( 3 );

			$this->dao->delete( 'admin_model_config', $data );

            header( 'Location:' . getLink( $this->getView->model_id ) );

		}
	}

	//
	//
	public function addNewConfig() {

		if ( !empty( $_REQUEST['ajax'] ) ) {
			$data['config_detail'] = '{}';
			$data['config_comment'] = $_REQUEST['config_comment'];
			$id = $this->dao->insert( 'admin_model_config', $data );
            header( 'Location:' . getLink( $this->getView->model_id, array( $id ) ) );
			exit;
		}

		require_once 'view_add_new_config.php';
	}

	//
	//
    public function gogo( $tb_name ) {

        return '<div class="content-all-field">'. allFiledOption( $tb_name, '', '', 'select-order-by' ) . ' <span></span></div>';
    }

	//
	//
	public function loadHelpDetail() {
		echo $this->loadHelpDetail_();
	}

	//
	//
	public function help_fn( $request ) {
	//echo $this->genUl( $request );
		require_once 'view_help.php';
	}


	//
	//
	public function genUl( $data, $k_data = NULL ) {

		$html = '<table align="center">';
		foreach ( $data as $ka => $va ) {

			if ( !empty( $k_data ) )
				$input_name = $k_data . '['. $ka .']';
			else
				$input_name = $ka;

			$html .= '<tr><th style="vertical-align: top;color: #BB9898;">'. $ka .'</th>';
			if ( is_array( $va ) || is_object( $va ) ) {

				$html .= '<td>' . $this->genUl( $va, $input_name ) . '</td>';

			} else {
				$html .= '
					<td><input type="text" name="'. $input_name .'" value="'. $va .'" / ></td>';
			}
			$html .= '</tr>';
		}
		$html .= '</table>';

		return $html;
	}



		//
	//
	public function viewEdit() {
		
		
		
	
		if ( isset( $_REQUEST['ajax'] ) ) {
			
			
			foreach( $_REQUEST['request'] as $kr => $vr ) {
				
				//arr( $vr );
				
				if( !is_array( $vr ) ) {
					$_REQUEST['request'][$kr] = htmlspecialchars_decode($vr );
				}
				//$_REQUEST[$kr] = 
				
				
			}
			
			
			$data['config_columns_label'] = $_REQUEST['request']['label'];
			$data['config_columns_name'] = $_REQUEST['filed_name'];
			
			
			//arr($_REQUEST['request']);
			$data['config_columns_detail'] = addslashes( json_encode( $_REQUEST['request'] ) );
				
			$this->dao->update_( 'admin_model_config_columns', $data, "config_columns_id = " . $_REQUEST['config_columns_id'] );
			
			//arr($this->params );
			
			 
			
			
			header( 'Location: '. front_link( $this->params['id'], $this->params['call_func'] ) .'' );
			
			exit;
		}

		$sql = "
			SELECT
				*
			FROM admin_model_config_columns
			WHERE config_columns_id = " . $_REQUEST['config_columns_id'];


		$res = $this->dao->fetch( $sql );
		
	

		$detail = json_decode( stripcslashes( $res->config_columns_detail ) );

 
		$filed = $res->config_columns_name;

		$request = convertObJectToArray( $detail );
		
	//arr( $request);

		$request['label'] = $res->config_columns_label;

		//
		//
		foreach ( $request as $ka => $va ) {
			
			if( is_object( $va ) )
				continue;
			
			if( is_array( $va ) )
				continue;
			
			if( !empty( $va ) )
				$request[$ka] = stripcslashes( $va );
		}
		
		$arr = array( 'version', 'report_form_filter', 'column_setting', 'showOnReady', 'ReportCustomBlock' );


		foreach( $arr as $ka => $va )  {
			if( !isset( $request[$va] ) ) {
				$request[$va] = NULL;
			}
		}
		
		
		
		$request['block_class'] = empty( $request['block_class'] )? 'col-md-4': $request['block_class'];
 	 
 	
		return '
		
			<h2>'. $this->config->config_comment .'</h2>
			<form method="get" action="">

				<input type="hidden" name="ajax" value="1" />
				<input type="hidden" name="config_columns_id" value="'. $_REQUEST['config_columns_id'] .'" />
    
	<div class="content">
	
		<h3>Edit From</h3>
		
		<div class="clear-fix">
		
			<input type="submit" value="Save" class="web-bt fl" />
			
			 
		</div>
	
		<table class="table_" style="width: 100%;">
		
			<tbody>
			
				<tr>
					<th>Filed Name</th>
					<td><input type="text" name="filed_name" value="'. $filed .'" /></td>
				</tr>
				<tr>
					<th>Label</th>
					<td><input name="request[label]" type="text" value="'. $request['label'] .'" /></td>
				</tr>
				<tr>
					<th>BlcokClass</th>
					<td><input name="request[block_class]" type="text" value="'. $request['block_class'] .'" /></td>
				</tr>
			
				<tr>
					<th>Report Form Filter</th>
					<td><textarea name="request[report_form_filter]">'. $request['report_form_filter']  .'</textarea></td>
				</tr>
				
			
			
				<tr>
					<th>Sum</th>
					<td><textarea name="request[sum]">'. $request['sum']  .'</textarea></td>
				</tr>
				 
				 
		
				<tr>
					<th>ตั้งค่าคอลัมน์</th>
					<td><textarea name="request[column_setting]">'. $request['column_setting'] .'</textarea></td>
				</tr>
				<tr>
					<th>Ex. date</th>
					 
				</tr>
				
				<tr>
					<th>show on ready</th>
					<td><textarea name="request[showOnReady]">'. $request['showOnReady'] .'</textarea></td>
				</tr>
				<tr>
					<th>Ex. date</th>
					<td>ALTER TABLE sac_purchase_request ADD dfdfsasd VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL FIRST</td>
				</tr>
		

				<tr>
					<th>Report Group</th>
					<td><textarea name="request[report_group]">'. $request['report_group'] .'</textarea></td>
				</tr>
				<tr>
					<th>Ex.</th>
					<td>{"Ln":"1","headerColumnBlock":2,"bg":"red","titleTop":"yes","titleBottom":"yes","bd":"","textTitleBottom":{"cName":"doc_date","label":"total"},"cNames":{"product":{"label":"[val]"}}}</td>
				</tr>
				
				
				
				
				<tr>
					<th>ReportCustomBlock</th>
					<td><textarea name="request[ReportCustomBlock]">'. $request['ReportCustomBlock'] .'</textarea></td>
				</tr>
				<tr>
					<th>Ex. date</th>
					<td>[{"call_func_name":"getFdateBlock","param":{"test":"1"}},{"call_func_name":"b","param":{"test":"1"}}]</td>
				</tr>
				
				<tr>
					<th>Ex. get select book</th>
					<td>{"header":"warehouse [val]","condition":"HAVING","box_title":"เลือก Ware House","input_name":"warehouse","call_func_name":"getCheckBox","sql":"SELECT book_sub_code as label, book_sub_code  as val FROM erp_book WHERE def_zone_id !=0","sqlFilters":"book IN ( [val] )"}</td>
				</tr>
				<tr>
					<th>Ex. get auto book</th>
					<td>{"condition":"WHERE","call_func_name":"getUserBook","prefix":"o."}</td>
				</tr>
				<tr>
					<th>Ex. check box</th>
					<td>{"box_title":"ร้านค้า","input_name":"dsds","call_func_name":"getCheckBox","sql":"SELECT  purchase_supplier_id AS val, purchase_supplier_name AS label FROM sac_purchase_supplier","sqlFilters":"po.purchase_supplier_id IN ( [val] )"}</td>
				</tr>
				
				<tr>
					<th>Ex. replace string</th>
					<td>{"call_func_name":"getText","sqlFilters":"b.admin_company_id IN ( [admin_company_id] )","param":{"[admin_company_id]":{"type":"session","name":"company_id"}}}</td>
				</tr>
				
				
				<tr>
					<th>Wordwrap On Print Doc</th>
					<td><input type="text" name="request[wwrap]" value="'. $request['wwrap'] .'" /></td>
				</tr>
				
				
				
				
				<tr>
					<th>Input Type</th>
					<td><textarea name="request[input_type]">'. $request['input_type'] .'</textarea></td>
				</tr>
            
				<tr>
					<th>Ex.</th>
					<td>{"type":"select","sql":"SELECT * FROM erp_zone %filter;","pri_key":"zone_id","desc":"[zone_name]","filter":"zone_id IN ( [zone_ids] )","param":{"[zone_ids]":{"type":"parent_data","name":"zone_ids"}}}</td>
				</tr>
				<tr>
					<th>Ex.</th>
					<td>{"type":"file","new_width":500,"new_height":500,"cut":false,"center_cut":true,"resize":true,"crop_frame":true,"border":1,"padding":0,"rotate":0,"showExample":false,"cutImg":{"w":"200","h":"300"}}</td>
				</tr>
				
				
				<tr>
					<th>Input Format </th>
					<td>'. inputFormat( 'request[inputformat]', $request['inputformat'] ) .'</td>
				</tr>
				
				<tr>
					<th>allow_extensions</th>
					<td><input name="request[allow_extensions]" type="text" value="'. $request['allow_extensions'] .'" /></td>
				</tr>
				
				
				
				<tr>
					<th>Dot ( for money_format)</th>
					<td><input name="request[dot]" type="text" value="'. $request['dot'] .'" /></td>
				</tr>
				<tr>
					<th>Of ( for type str_percent )</th>
					<td><input name="request[of]" type="text" value="'. $request['of'] .'" /></td>
				</tr>
				<tr>
					<th>Record</th>
					<td><input name="request[record]" type="text" value="'. $request['record'] .'" /></td>
				</tr>
				<tr>
					<th>Allow Edit</th>
					<td><input name="request[allow_edit]" type="text" value="'.  $request['allow_edit'] .'" /></td>
				</tr>
				<tr>
					<th>Show</th>
					<td><input name="request[show]" type="text" value="'. $request['show'] .'" /></td>
				</tr>
				<tr>
					<th>Show On Doc</th>
					<td><input name="request[show_on_doc]" type="text" value="'. $request['show_on_doc'] .'" /></td>
				</tr>
				<tr>
					<th>Set text align ( \'C\', \'L\', \'R\')</th>
					<td><input name="request[a]" type="text" value="'. $request['a'] .'" /></td>
				</tr>
				
				<tr>
					<th>Auto key</th>
					<td><input name="request[auto]" type="text" value="'.  $request['auto'] .'" /></td>
				</tr>
				<tr>
					<th>Status</th>
					<td>'. option( 'request[status]', $request['status'], $array = array( 'eneble' => 1, 'close' => 0 ) ) .'</td>
				</tr>
				<tr>
					<th>Label For Report Line 2</th>
					<td><input name="request[label_sec_line]" type="text" value="'. $request['label_sec_line'] .'" /></td>
				</tr>

				<tr>
					<th>Require</th>
					<td>'. option( 'request[0]', $request[0] ) .'</td>
				</tr>
				<tr>
					<th>No Druplicate</th>
					<td>'. option( 'request[1]', $request[1] ) .'</td>
				</tr>
			
				
				<tr>
					<th>On Insert</th>
					<td><textarea name="request[on_insert]">'. $request['on_insert'] .'</textarea></td>
				</tr>
				<tr>
					<th>Ex.</th>
					<td>{"sql":"SELECT [shop_id] as t","param":{"[shop_id]":{"type":"rq","name":"main_id"}}}</td>
				</tr>
				<tr>
					<th>Ex.</th>
					<td>{"allow_key":"1","sql":"SELECT ( [qty_um] / [product_um_rate] ) as t","param":{}}
					</td>
				</tr>
				 
				<tr>
					<th>Field Comment</th>
					<td><textarea name="request[field_comment]">'. $request['field_comment'] .'</textarea></td>
				</tr>
				<tr>
					<th>On Update</th>
					<td><textarea name="request[on_update]">'. $request['on_update'] .'</textarea></td>
				</tr>
				<tr>
					<th>Forum</th>
					<td><textarea name="request[forum]">'. $request['forum'] .'</textarea></td>
                    <td>[tb_budget][tax_rate] * [tb_budget][budget_amount]</td>
				</tr>
				<tr>
					<th>Forum ON Ready</th>
					<td><textarea name="request[forum_on_ready]">'. $request['forum_on_ready'] .'</textarea></td>
                    <td>[tax_rate] * [budget_amount]</td>
				</tr>
			
				
				
				<tr>
					<th>Default Value</th>
					<td><textarea name="request[7]">'. $request[7] .'</textarea>
					
					
				</tr>
				<tr>
					<th>Fix Value</th>
					<td><input name="request[fix_val]" type="text" value="'. $request['fix_val'] .'" /></td>
				</tr>
				<tr>
					<th>Sub Position</th>
					<td><input name="request[subposition]" type="text" value="'.  $request['subposition'] .'" /></td>
				</tr>
				<tr>
					<th>Refferent Model id</th>
					<td><input name="request[ref_model_id]" type="text" value="'. $request['ref_model_id'] .'" /></td>
				</tr>
				<tr>
					<th>version</th>
					<td><input name="request[version]" type="text" value="'.  $request['version'] .'" /></td>
				</tr>
			</tbody>
		</table>
		
		<div class="load-help-detail">
		 
		</div>
		<div class="clear-fix">
			<input type="submit" value="Save" class="web-bt fl" />
			
			 
		</div>
	</div>
</form>
<div class="h-10"></div>


<script>

	$( function () {
	

		//
		//
		$( \'*[name="request[helpdetail][request_key][]"]\' ).live( \'change\', function () {
			var v = $( \'*[name="request[helpdetail][label]"]\' ).val();
			v += \'[\' + $( this ).val() + \']\';
			
			$( \'*[name="request[helpdetail][label]"]\' ).val( v );
		});
	
		//
		//
		$( \'.input-format-option\' ).live( \'change\', function () {
															//  alert( \'afdfdas\' );
			var e = $( \'.load-help-detail\' );
			
			if ( $.inArray( $( this ).val(), [ \'help\', \'read_only\' ] ) !== -1 ) {
				 
			}
			else {
				e.empty();
			}
		});	

	});
</script>';
		
	}

	//
	//
	public function index( $param = array() ) {

		$this->params = $param;
  

		$param = json_encode( $param );
		$this->getView = json_decode( $param );
		$this->getView->model_id = $this->getView->id;


//exit;
 
		if ( ex( 3 ) != '' ) {

			return call_user_func( array( $this, ex( 3 ) ) );
			 
		}
 

		if ( !empty( $_REQUEST['ajax'] ) ) {
			
				
			unset( $_REQUEST['ajax'] );
			
			foreach( $_REQUEST as $kr => $vr ) {
				$_REQUEST[$kr] = htmlspecialchars_decode($vr );
				strip_tags( $vr );
				
			}
			
		//arr( $_REQUEST) ;	
			
			 
			$data['config_detail'] = json_encode( $_REQUEST );

				
			//foreach( $this->webCloneDb as $kdb => $vdb ) {
				
				$this->dao->update_( 'admin_model_config', $data, "config_id = " . ex( 2 ) );
			
			//}
			
			header( 'Location: '. comeBack() .'' );
			
			exit;
			
		}

		$sql = "
			SELECT
				c.*,
				( 
					SELECT 
						CONCAT( '<a href=\"config-form/', c.config_id ,'/viewEdit.html?config_columns_id=', config_columns_id ,'\">มีการใช้งานร่วม</a>' )
						
					FROM admin_model_config_columns 
					WHERE config_override_id = c.config_columns_id 
					LIMIT 0, 1 
					
				) as beware
			FROM admin_model_config_columns c
			WHERE c.config_id = ". $this->config->config_id ."
			ORDER BY c.config_columns_order ASC";

		$this->columns = $this->dao->fetchAll( $sql );

		$keep = array();
		foreach ( $this->columns as $ka => $va ) {
			$keep[] = $va->config_columns_name;
		}
		
 		
		$otp = array();
		foreach ( $this->dao->showColumns( $this->config->tb_main ) as $vb ) {


			if( in_array( $vb, $keep ) )
				continue;
			$otp[] = '<option value="'. $vb .'">'. $vb .'</option>';
		}
		 
		
			$i = 1;
			$keep = array();
			$w = 0;
			foreach ( $this->columns as $ka => $va ) {

				$deleteButton = $va->beware;
				if( empty( $va->beware ) ) {
					$deleteButton = '<a title="ลบ"  class="web-bt" href="'. setLink( ex( 1 ) . '/' . ex( 2 ) . '/viewDelete/', array( 'config_columns_id' => $va->config_columns_id, 'ajax' => 1 ) )  .'">'. getIcon( 'gdel' ) .'</a>';
	
				}

				$editButton = '<a title="แก้ไข" class="web-bt" href="'. setLink( ex( 1 ) . '/' . ex( 2 ) . '/viewEdit/', array( 'config_columns_id' => $va->config_columns_id ) )  .'">'. getIcon( 'gedit' ) .'</a>';

				if ( empty( $va->config_override_id ) ) {
					$overrideButton = '';

				} else {

					$overrideButton = '<a title="แก้ไข" class="web-bt" href="'. getLink( $this->getView->model_id, array( ex( 2 ), 'viewEdit' ), array( 'config_columns_id' => $va->config_override_id ) ) .'">'. getIcon( 'gedit' ) .'</a>';

				}
				
				
				$keep[] = $va->config_columns_name;

				$detail = json_decode( stripcslashes( $va->config_columns_detail ) );

				$w += $va->config_columns_w;

			//	arr($detail->report_group);
				$report_group = !empty( $detail->report_group )? 'yes': NULL;
				$report_filters = !empty( $detail->report_form_filter )? 'yes': NULL;

				$trs[] = '
					<tr'. select( $detail->show, 0, ' class="not-show"' ) .'>

						<td><input type="hidden" name="request[]" value="'. $va->config_columns_id .'" />'. $va->config_columns_name .' : '. $va->config_columns_label .'</td>

						<td>'. $detail->inputformat .'</td>

						<td><input style="width: 40px;" data-id="'. $va->config_columns_id .'" type="text" name="setW[]" value="'. $va->config_columns_w .'" /></td>


						<td>
							'. $editButton .'
							 
							<a title="คัดลอก"  class="web-bt" href="'. setLink( ex( 1 ) . '/' . ex( 2 ) . '/viewCopy/'. $va->config_columns_id ) .'">'. getIcon( 'gcopy' ) .'คัดลอก</a>
							
							'. $deleteButton .'
						</td>

						<td>'. $report_group .'</td>
						<td>'. $overrideButton .'</td>
						<td>'. $report_filters .'</td>

					</tr>';

				++$i;
			}
			
			$arr = array(
				'report_top_title',
				'pdfLink',
				'showCompanyHeader',
				'printType',
				'main_sql_sort',
				'main_sql_str_replace',
				'txt_cond',
				'def_form_status',
				
				
			);
				
			foreach( $arr as $ka => $va ) {
				
				if( !isset( $this->config->$va ) ) {
					$this->config->$va = NULL;
				}
				
			}
		
		
	
			return '
		<div class="row" style="margin: 15px 0px;">

		<div class="col-md-5">
		<form action="" method="get">
			<input type="hidden" name="ajax" value="1" />
			<button type="submit" class="web-bt">'. getIcon( 'gsave' ) .'</button>
			<a class="web-bt fr" href="'. comeBack() .'" >Back</a>

			<h2>Before-After Action Form Setting</h2>
			<div class="content">
			

				<label>Main Sql</label>
				<textarea name="main_sql">'. $this->config->main_sql .'</textarea>
				<p>Ex.: SELECT a.* FROM [tb_main] a %filter; ORDER BY a.id DESC</p>
				
				
				<label>Navigate Tables</label>
				<textarea name="multi_tables">'. $this->config->multi_tables .'</textarea>
				<p>Ex.: {"tb_inv_hd":{"main_tb":true,"pri_key":"inv_hd_id","search":["inv_hd_no"]}}</p>
				
				
				<label>Tab Config</label>
				<textarea name="tab_config">'. $this->config->tab_config .'</textarea>
				<p>Ex.: [{"tab_id":78,"label":"รายการ"},{"tab_id":313,"label":"ยกเลิกรายการ","lock_tab":"1","print":"0"},{"tab_id":315,"label":"ล็อคเอกสาร","lock_tab":"1","print":"0","allow_users":[27,65]}]</p>
				
				<label>Table Main</label>
				<input type="text" value="'. $this->config->tb_main .'" name="tb_main" />
			

				<label>Pri Key</label>
				<input type="text" value="'. $this->config->pri_key .'" name="pri_key" />
			
			
				<label>More Filter Sql</label>
				<textarea name="more_filter_sql">'.  $this->config->more_filter_sql  .'</textarea>
				
				<p>{"HAVING":{"sql":"dt.admin_company_id = [company_id]","param":{"[company_id]":{"type":"session","name":"company_id"}}}}</p>
				<p>Ex.: {"sql":"stock_hd_prove_name IS NULL AND  user_id = [user_id] AND  stock_hd_checker_name IS NULL","param":{"[user_id]":{"type":"session","name":"user_id"}}}</p>
				
			
			
			


				
			
				<label>Before Action</label>
				<textarea name="before_action">'. $this->config->before_action .'</textarea>
				<p>Ex.: {"checkErpGl":{"test":1,"testb":2}}</p>
				
				<label>After Action</label>
				<textarea name="after_action">'. $this->config->after_action  .'</textarea>
				<p>Ex.: {"checkErpGl":{"test":1,"testb":2}}</p>
				 
				

			</div>

			
			<h2>Doc Setting</h2>
			<div class="content">

				<label>report text top bottom sumary</label>
				<textarea name="report_top_title">'. $this->config->report_top_title .'</textarea>
				<p>Ex.: {"titleTop":"","titleBottom":"yes","bd":"1","textTitleBottom":"รวม","bg":"orange"}</p>
				
		
				<label>pdfLink</label>
				<textarea name="pdfLink">'. $this->config->pdfLink .'</textarea>
				
				<div class="h-10"></div>


				<label>showCompanyHeader</label>
				
				
				<textarea name="showCompanyHeader">'. $this->config->showCompanyHeader .'</textarea>
				<p>{"show":0,"userSelectOption":1}</p>
				
				<div class="h-10"></div>
				
				<label>reportFontSize</label>
				<input type="text" value="'. $this->config->reportFontSize .'" name="reportFontSize" />
				<div class="h-10"></div>


				<label>reportBorder</label>
				<input type="text" value="'. $this->config->reportBorder .'" name="reportBorder" />
				<div class="h-10"></div>


  


				<label>reportPaperSize</label>
				<textarea name="reportPaperSize">'. $this->config->reportPaperSize .'</textarea>
				<p>Ex.: A3, A4, A5</p>
				<div class="h-10"></div>
				
				
				
				
				<label>printType</label>
				<textarea name="printType">'. $this->config->printType .'</textarea>
				<p>Ex.: pageBypage (ขึ้นหน้าใหม่ตามบรรทัดที่กำหนด), continue (ต่อเนื่อง)</p>
				<div class="h-10"></div>

				
				
				
				<label>แนวกระดาษ</label>
				<textarea name="reportPaperOrientation">'. $this->config->reportPaperOrientation .'</textarea>
				<p>Ex.: P, L</p>
				
			</div>
			

			<h2>Form Setting</h2>
			<div class="content">

				<label>defalt Sql sort</label>
				<textarea name="main_sql_sort">'. $this->config->main_sql_sort .'</textarea>
				<p>Ex.: ORDER BY product_code ASC</p>
				
				
				
				<label>Main Sql String Replace String</label>
				<textarea name="main_sql_str_replace">'. $this->config->main_sql_str_replace .'</textarea>
				<p>Ex.: SELECT ? FROM  tb_product %filter; ORDER BY product_code ASC</p>
				
				
				

				<label>Show Detail</label>
				<input type="checkbox" name="reportDetail" value="1" '. select( true, isset( $this->config->reportDetail ), 'checked' ) .'  />
				
				<label>Report Group Columns</label>
				<textarea name="reportGroupColumns">'. $this->config->reportGroupColumns .'</textarea>
				<p>{"purchase_items_name":{"w":"50","a":"L","label":"สินค้า"},"purchase_items_um":{"w":"50","a":"L","label":"หน่วย"}}</p>
				
			



				<label>Descscritption Call From another model</label>
				<textarea name="label">'. $this->config->label .'</textarea>
				

				
				


				


				<label>txt_cond</label>
				<textarea name="txt_cond">'. $this->config->txt_cond .'</textarea>
				<p>HAVING OR WHERE</p>
				
				

				<label>Empty New Form</label>
				<input type="text" value="'. $this->config->empty_new_form .'" name="empty_new_form" />
				<p>Ex.: 1 || 0</p>
				






				<label>Upload Folder</label>
				<input type="text" value="'. $this->config->upload_folder .'" name="upload_folder" />
				


				<label>JScript</label>
				<textarea name="jscript">'. $this->config->jscript .'</textarea>
				

				<label>PerPage In Rows Sql</label>
				<input type="text" value="'. $this->config->perpage_in_rows_sql .'" name="perpage_in_rows_sql" />
				
				<label>Default Form Status</label>
				<input type="text" name="def_form_status" value="'. $this->config->def_form_status .'" />

				<p>Ex.: a, e, r</p>
				
			 
				
				<label>Select In Rows Sql</label>
				<textarea name="select_in_rows_sql">'.  $this->config->select_in_rows_sql .'</textarea>
				<div class="h-10"></div>

				<label>Need to Prove Document</label>
				<textarea name="need_prove">'. $this->config->need_prove .'</textarea>
				<p>Ex.: {"sql":"SELECT doc_no as t FROM sac_purchase_request WHERE stock_hd_id = [stock_hd_id]","param":{"[stock_hd_id]":{"type":"rq","name":"pri_key"}},"link_to_report":"sac-request-doc/pdf_table"}</p>
			
				<label>Auto Edit Next Sub ROw</label>
				<input type="text" value="'. $this->config->auto_edit_next_sub_row .'" name="auto_edit_next_sub_row" />

				<p>Ex.: 1 || 0</p>
				
				<label>Comment</label>
				<textarea name="comment">'. $this->config->comment .'</textarea>
				
				<label>Denine Public Filter From admin_company_group</label>
				<input type="text" value="'. $this->config->denine_public_filter .'" name="denine_public_filter" />
				<p>0: admin_company_id IN (
					SELECT
						company_id
					 FROM admin_company
					 WHERE company_group_id = company_id
				)</p>
				<p>1: admin_company_id = company_id</p>
				
			</div>




			<div class="h-10"></div>
			<h2>Permission Setting</h2>
			<div class="content">
				<label>No Add</label>
				<input type="text" value="'. $this->config->no_add .'" name="no_add" />
				<p>Ex.: 1 || 0</p>
				<div class="h-10"></div>

				<label>No Edit</label>
				<input type="text" value="'. $this->config->no_edit .'" name="no_edit" />
				<p>Ex.: 1 || 0</p>
				<div class="h-10"></div>

				<label>No Delete</label>
				<input type="text" value="'. $this->config->no_delete .'" name="no_delete" />
				<p>Ex.: 1 || 0</p>
				<div class="h-10"></div>

				<label>Cancel</label>
				<input type="text" value="'. $this->config->cancel .'" name="cancel" />
				<p>Ex.: 1 || 0</p>
				<div class="h-10"></div>

				<label>No Search</label>
				<input type="text" value="'. $this->config->no_search .'" name="no_search" />
				<p>Ex.: 1 || 0</p>

			</div>

			<div class="h-10"></div>
			
			
			<button type="submit" class="web-bt"><span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span></button>


		</form>

	</div>
	<div class="col-md-7">

		<form action="'. front_link( $this->params['id'], '68/switchLine' ) .'">

			<table class="flexme3 sorttable">
				<thead>
					<tr class="tr_bg">
						<th>Name</th>
						<th>Format</th>
						<th>w</th>
						<th>Operation</th>
						<th>Group</th>
						<th>override</th>
						<th>filters</th>
					</tr>
				</thead>
				<tbody>'. implode( '', $trs  ) .'</tbody>
			</table>
			ความกว้างรวม : '. $w .'
		</form>

		<div class="h-10"></div>

		'. $this->sortColumns() .'

		<div class="h-10"></div>
		
	</div>
</div>

<script>

	$( function () {

		$( \'*[name="setW[]"]\' ).keyup( function () {

			var send = { \'ajax\': 1, \'config_columns_id\': $( this ).attr( \'data-id\' ), \'w\': $( this ).val() };
			$.getJSON( \''. getLink( $this->getView->model_id, array( ex( 2 ), 'updateColumnWidth' ) ) .'\', send, function () {

			});

		});



		$(\'.sorttable tbody\')
			.sortable()
			.disableSelection()
			.mouseup(function(){
				var form = $( this ).parents( \'form\' );

				setTimeout( function(){

					var send = form.serialize();

					send += \'&ajax=1\';

					$.getJSON( form.attr( \'action\' ), send, function () {

					});
				}, 100 );
			});



	});
</script>';


		 

	}


	 
	//
	public function sortColumns() {


		if ( !empty( $_REQUEST['send'] ) ) {

			$i = 0;
			foreach ( $_REQUEST['send'] as $ka => $va ) {
				++$i;
				$explode = explode( '-', $va );

				$data['config_columns_position'] = $explode[0];
				$data['config_columns_order'] = $i;

				$config_columns_id = $explode[1];

				//foreach( $this->webCloneDb as $kdb => $vdb ) {
					
					$this->dao->update_( 'admin_model_config_columns', $data, "config_columns_id = " . $config_columns_id );
					
				//}


			}

			exit;
		}

		$sql = "
			SELECT
				*
			FROM admin_model_config_columns
			WHERE config_id = ". $this->config->config_id ."
			ORDER BY config_columns_order ASC";


		$keep = array();
		$keep['SPL'] = $keep['SPR'] = $keep['simple'] = $keep['HEAD'] = $keep['TL'] = $keep['TR'] = $keep['BL'] = $keep['BR'] = array();
		foreach ( $this->dao->fetchAll( $sql ) as $ka => $va ) {

			$detail = json_decode( stripcslashes( $va->config_columns_detail ) );

			if( $detail->show == 0 )
				continue;

			if ( empty( $detail->position ) )
				$detail->position = 'simple';

			if ( !empty( $va->config_columns_position ) )
				$detail->position = $va->config_columns_position;

			$keep[$detail->position][] = '<li class="sortable-item" style="" data-id="'. $va->config_columns_id .'">'. $va->config_columns_name .' : '. $detail->label .'</li>';

		}

		return '
		<div id="example-1-2" class="content">


	<div class="row">
		<div class="col-md-12">
			<strong>Head</strong>
			<ul class="sortable-list ui-sortable" data-group="HEAD">'. implode( '', $keep['HEAD'] ) .'</ul>
		</div>
	</div>



	<div class="row" style="margin-top: 20px;">
		<div class="col-md-6">
			<strong>บนซ้าย</strong>
			<ul class="sortable-list ui-sortable" data-group="TL">'. implode( '', $keep['TL'] ) .'</ul>

		</div>

	
		<div class="col-md-6">
			<strong>บนขวา</strong>
			<ul class="sortable-list ui-sortable" data-group="TR">'. implode( '', $keep['TR'] ) .'</ul>

		</div>
	</div>
	
	<div class="row" style="margin-top: 20px;">
		<div class="col-md-12">
			<strong>Full Line</strong>
			<ul class="sortable-list ui-sortable" data-group="simple">'. implode( '', $keep['simple'] ) .'</ul>
		</div>

	
	</div>
	
	<div class="row" style="margin-top: 20px;">
		<div class="col-md-6">
			<strong>ล่างซ้าย</strong>
			<ul class="sortable-list ui-sortable" data-group="BL">'. implode( '', $keep['BL'] ) .'</ul>

		</div>
		<div class="col-md-6">
			<strong>ล่างขวา</strong>
			<ul class="sortable-list ui-sortable" data-group="BR">'. implode( '', $keep['BR'] ) .'</ul>

		</div>
	</div>
	
	<div class="row" style="margin-top: 20px;">
		<div class="col-md-6">
			<strong>พิเศษซ้าย</strong>
			<ul class="sortable-list ui-sortable" data-group="SPL">'. implode( '', $keep['SPL'] ) .'</ul>

		</div>
		<div class="col-md-6">
			<strong>พิเศษขวา</strong>
			<ul class="sortable-list ui-sortable" data-group="SPR">'. implode( '', $keep['SPR'] ) .'</ul>

		</div>
	</div>
	
</div>




<script type="text/javascript">

	$(document).ready(function(){
		
		 
		$(\'#example-1-2 .sortable-list\')
			.sortable({connectWith: \'#example-1-2 .sortable-list\'})
			.disableSelection()
			.mouseup(function(){
			
				setTimeout( function(){
				
					var q = [];
					//var send = {};
					$( \'.ui-sortable\' ).each( function() {
						var dataGroup = $( this ).attr( \'data-group\' );
						$( this ).find( \'li\' ).each( function () {
							if ( $( this ).attr( \'data-id\' ) )
								q.push( dataGroup + \'-\' + $( this ).attr( \'data-id\' ) );
						});
					});
					
					//send.send = q;
					
					$.getJSON( \''. front_link( $this->params['id'], ex( 2 ). '/sortColumns', array( 'ajax' => 1 ) ) .'\', {send: q}, function () {

					});
				}, 100 );
			});
	});

</script>';
	 

	}


	
	
	
	
}
