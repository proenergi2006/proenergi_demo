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
			a.*
		from 
			pro_marketing_reimbursement a 
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
					a.marketing_reimbursement_date like '%".$q1."%' or
					a.no_polisi like '%".$q1."%' or
					a.user like '%".$q1."%' or
					a.km_awal like '%".$q1."%' or
					a.km_akhir like '%".$q1."%' or
					a.total like '%".$q1."%'
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
			$linkView 	= BASE_URL_CLIENT.'/marketing-reimbursement-view.php?'.paramEncrypt('idr='.$data['id_marketing_reimbursement']);
			$linkForm 	= BASE_URL_CLIENT.'/marketing-reimbursement-add.php?'.paramEncrypt('idr='.$data['id_marketing_reimbursement']);
			$linkDel	= paramEncrypt("marketing_reimbursement#|#".$data['id_marketing_reimbursement']);
        	$content .= '
				<tr class="clickable-row" data-href="'.$linkView.'">
					<td class="text-center">'.$count.'</td>
					<td>'.date('d/m/Y', strtotime($data['marketing_reimbursement_date'])).'</td>
					<td>'.$data['no_polisi'].'</td>
					<td>'.$data['user'].'</td>
					<td>'.$data['km_awal'].'</td>
					<td>'.$data['km_akhir'].'</td>
					<td>Rp <span class="pull-right">'.number_format($data['total']).'</span></td>
					<td class="text-center">
						<a class="btn btn-action btn-info" href="'.$linkView.'" style="margin-right:3px;"><i class="fa fa-file"></i></a>
					</td>
				';
			if ($id_role==11 || $id_role==17) {
				$content .= '
					<td class="text-center">
						<a class="btn btn-action btn-warning" href="'.$linkForm.'" style="margin-right:3px;"><i class="fa fa-edit"></i></a>
					</td>
					<td class="text-center">
						<a class="btn btn-action btn-danger" data-param-idx="'.$linkDel.'" data-action="deleteGrid"><i class="fa fa-trash"></i></a>
            		</td>
            	';
            }
			$content .= '</tr>';
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
