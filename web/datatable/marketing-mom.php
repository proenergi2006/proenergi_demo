<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$draw 	= isset($_POST["element"])?htmlspecialchars($_POST["element"], ENT_QUOTES):0;
	$start 	= isset($_POST["start"])?htmlspecialchars($_POST["start"], ENT_QUOTES):0;
	$length	= isset($_POST['length'])?htmlspecialchars($_POST["length"], ENT_QUOTES):10;
    $id_user  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
    $id_wilayah  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $id_role  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
    $id_group  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "
		select 
			a.*,
			b.fullname as user_name
		from 
			pro_marketing_mom a 
			join acl_user b on b.id_user = a.created_by
			join pro_master_area c on c.id_master = b.id_wilayah
		where 
			1=1 
			and a.deleted_time is null
	";

	if ($id_role==6) 
		$sql .= " and b.id_group = ".$id_group;
	if ($id_role==7) 
		$sql .= " and b.id_wilayah = ".$id_wilayah;
	if ($id_role==11 || $id_role==17) 
		$sql .= " and b.id_user = ".$id_user;

	if($q1 != "") {
		$sql .= "
				 and
				(
					a.date like '%".$q1."%' or
					a.place like '%".$q1."%' or
					a.customer like '%".$q1."%' 
				)
				";
	}

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.created_time desc limit ".$position.", ".$length;
	
	$content = "";
	$count = 0;
	if($tot_record ==  0){
		$content .= '<tr><td colspan="16" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$td_tech_support = '';
			$linkView 	= BASE_URL_CLIENT.'/marketing-mom-view.php?'.paramEncrypt('idr='.$data['id_marketing_mom']);
			$linkEdit 	= BASE_URL_CLIENT.'/marketing-mom-add.php?'.paramEncrypt('idr='.$data['id_marketing_mom']);
			$linkDel	= paramEncrypt("marketing_mom#|#".$data['id_marketing_mom']);
			$td_tech_support .= '
				<td class="text-center">
					<a class="btn btn-action btn-info" href="'.$linkView.'" style="margin-right:3px;"><i class="fa fa-file"></i></a>
				</td>
			';
			if ($id_role==11 || $id_role==17) {
				$td_tech_support .= '
					<td class="text-center">
						<a class="btn btn-action btn-warning" href="'.$linkEdit.'" style="margin-right:3px;"><i class="fa fa-edit"></i></a>
					</td>
				';
				$td_tech_support .= '
					<td class="text-center">
						<a class="btn btn-action btn-danger" data-param-idx="'.$linkDel.'" data-action="deleteGrid"><i class="fa fa-trash"></i></a>
	        		</td>
				';
			}

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkEdit.'">
					<td class="text-center">'.$count.'</td>
					<td>'.date('d/m/Y', strtotime($data['date'])).'</td>
					<td>'.$data['place'].'</td>
					<td>'.$data['customer'].'</td>
					'.$td_tech_support.'
				</tr>';
		} 
	} 
	
	

	$json_data = array(
					"items"		=> $content,
					"pages"		=> $tot_page,
					"page"		=> $page,
					"totalData"	=> $tot_record,
					"infoData"	=> "Showing ".($position+1)." to ".$count." of ".$tot_record." entries",
				);
	//var_dump($json_data);exit;
	
	echo json_encode($json_data);
?>
