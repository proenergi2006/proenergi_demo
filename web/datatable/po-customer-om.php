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
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select a.*, b.nama_cabang from pro_po_customer_om a join pro_master_cabang b on a.id_wilayah = b.id_master 
			where a.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."'";
	
	if($q1 != "")
		$sql .= " and (concat('POPR',lpad(a.id_ppco,4,'0')) = '".strtoupper($q1)."')";
	if($q2 != ""){
		$sql .= " and b.id_master=".$q2." ";
	}
	if($q3 !="" && $q4 !=""){
		$sql .= " and (a.tanggal_issued between '".tgl_db($q3)."' and  '".tgl_db($q4)."')";
	}else{
		if($q3 !="")
			$sql .= " and (a.tanggal_issued = '".tgl_db($q3)."')";
		if($q4 !="")
			$sql .= " and (a.tanggal_issued = '".tgl_db($q4)."')";
	}
	

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.is_executed, a.tanggal_issued desc limit ".$position.", ".$length;

	$count = 0;
	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="5" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/po-customer-om-detail.php?'.paramEncrypt('idr='.$data['id_ppco']);
			$background = (!$data['is_executed'])?' style="background-color:#f5f5f5"':'';

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'"'.$background.'>
					<td class="text-center">'.$count.'</td>
					<td class="text-center">POPR'.str_pad($data['id_ppco'],4,'0',STR_PAD_LEFT).'</td>
					<td class="text-center">'.date("d/m/Y", strtotime($data['tanggal_issued'])).'</td>
					<td>'.$data['nama_cabang'].'</td>
					<td class="text-center action"><a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a></td>
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
