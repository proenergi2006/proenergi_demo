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
	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select * from pro_permintaan_order where id_customer = '".paramDecrypt($_SESSION["sinori".SESSIONID]["customer"])."'";
	
	if($q1 != "")
		$sql .= " and (nomor_order = '".$q1."' or tanggal_order = '".tgl_db($q1)."')";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by id_pmnt_order desc limit ".$position.", ".$length;
	$count = 0;

	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="6" class="text-center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			if($data['attachment_order']==''){
				$attachment = '';
			}else{
				$attachment = '<i class="fa fa-paperclip"></i>';
			}
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/permintaan-order-detail.php?'.paramEncrypt('idr='.$data['id_pmnt_order']);
			$linkHapus	= paramEncrypt("master_permintaan_order#|#".$data['id_pmnt_order']);
			if($data['is_delivered'] == 2)
				$status	= "Dieksekusi";
			else if($data['is_delivered'] == 1)
				$status	= "Diterima Marketing";
			else $status = "Dikirim";
        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'">
					<td class="text-center">'.$count.'</td>
					<td>'.$data['nomor_order'].'</td>
					<td class="text-center">'.date("d/m/Y", strtotime($data['tanggal_order'])).'</td>
					<td>'.$data['pic_name'].'</td>
					<td>'.$status.'</td>
					<td class="text-center">'.$attachment.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detil" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
						<a class="margin-sm delete btn btn-action btn-danger" title="Delete" data-param-idx="'.$linkHapus.'" data-action="deleteGrid">
						<i class="fa fa-trash"></i></a>
            		</td>
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
	echo json_encode($json_data);
?>
