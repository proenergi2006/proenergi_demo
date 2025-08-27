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
	$sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select a.*, b.nama_customer, b.kode_pelanggan from pro_permintaan_penawaran a join pro_customer b on a.id_customer = b.id_customer 
			where 1=1";
	if ($sesrol == 18) {
		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) or paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])) {
			$varBadge14Arr = [];
	        if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
	            $sqlCustomerWilayah = "select id_user from acl_user where id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	        else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
	            $sqlCustomerWilayah = "select id_user from acl_user where id_group = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_group"])."'";
	        $jumCustomerWilayah = $con->getResult($sqlCustomerWilayah);
	        foreach ($jumCustomerWilayah as $k => $v) $varBadge14Arr[$k] = $v['id_user'];
	        $varBadge14Arr = implode(',', $varBadge14Arr);
			$sql .= " and a.pic_user in (".$varBadge14Arr.")";
		}
	} else if ($sesrol == 17 || $sesrol == 11) {
		$sql .= " and a.pic_user = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'";
	}
	
	if($q1 != "")
		$sql .= " and (upper(a.judul_pmnt) like '%".strtoupper($q1)."%' or b.kode_pelanggan = '".$q1."' or upper(b.nama_customer) like '%".strtoupper($q1)."%' 
				or concat('INQ-',lpad(a.id_pmnt,4,'0')) = '".strtoupper($q1)."')";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.is_delivered, a.id_pmnt desc limit ".$position.", ".$length;

	$content = "";
	$count = 0;
	if($tot_record <= 0){
		$content .= '<tr><td colspan="8" class="text-center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			if($data['attachment_order_ori']==''){
				$attachment = '';
			}else{
				$attachment = '<i class="fa fa-paperclip"></i>';
			}
			$count++;
			$linkDetail	= BASE_URL_CLIENT.'/customer-penawaran-detail.php?'.paramEncrypt('idr='.$data['id_pmnt']);
			if($data['is_delivered'] == 2){
				$status		= "Dieksekusi";
				$background = '';
			} else if($data['is_delivered'] == 1){
				$status		= "Diterima Marketing";
				$background = '';
			} else{ 
				$status 	= "Dikirim";
				$background = ' style="background-color:#f5f5f5"';
			}
        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'"'.$background.'>
					<td class="text-center">'.$count.'</td>
					<td>INQ-'.str_pad($data['id_pmnt'],4,'0',STR_PAD_LEFT).'</td>
					<td>'.($data['kode_pelanggan']?$data['kode_pelanggan']:'-------').'</td>
					<td>'.$data['nama_customer'].'</td>
					<td>'.$data['judul_pmnt'].'</td>
					<td class="text-center">'.date("d/m/Y", strtotime($data['created_time'])).'</td>
					<td>'.$status.'</td>
					<td class="text-center">'.$attachment.'</td>
					<td class="text-center action"><a class="margin-sm btn btn-action btn-info" title="Detil" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a></td>
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
