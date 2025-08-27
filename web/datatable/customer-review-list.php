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
	$length	= isset($_POST['length'])?htmlspecialchars($_POST["length"], ENT_QUOTES):25;

	$sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$q1		= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "
		select a.id_customer, a.id_verification, a.token_verification, b.nama_customer, b.email_customer, 
		b.telp_customer, b.fax_customer, b.kode_pelanggan, b.alamat_customer, c.nama_prov, d.nama_kab 
		from pro_customer_verification a 
		join pro_customer b on a.id_customer = b.id_customer 
		join pro_master_provinsi c on b.prov_customer = c.id_prov 
		join pro_master_kabupaten d on b.kab_customer = d.id_kab 
		where a.is_evaluated = 1 and a.is_reviewed = 0 and a.is_active = 1
	";
	if ($sesrol == 18) {
		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
			$sql .= " and (b.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."' or b.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
		else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
			$sql .= " and (b.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."' or b.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
	} else if ($sesrol == 17 || $sesrol == 11) {
		$sql .= " and b.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'";
	}

	if($q1 != "")
		$sql .= " and (upper(b.nama_customer) like '%".strtoupper($q1)."%' or b.kode_pelanggan = '".$q1."')";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.id_verification desc limit ".$position.", ".$length;

	$content = "";
	$count = 0;
	if($tot_record <= 0){
		$content .= '<tr><td colspan="8 style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$idr = $data['id_customer'];
			$idk = $data['id_verification'];
			$tok = $data['token_verification'];
			$lin = BASE_URL_CLIENT."/customer-review-add.php?".paramEncrypt("idr=".$idk);
			$cus = BASE_URL.'/customer/update-customer.php?'.paramEncrypt('idr='.$idr.'&idk='.$idk.'&token='.$tok);
			$kod = 'LC'.str_pad($data['id_verification'],4,'0',STR_PAD_LEFT);
			$tmp1 = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$addr = $data['alamat_customer']." ".ucwords($tmp1)." ".$data['nama_prov'];

        	$content .= '
			<tr class="clickable-row" data-href="'.$lin.'">
				<td class="text-center">'.$count.'</td>
				<td><a href="'.$cus.'" target="_blank">'.$kod.'</a></td>
				<td>'.($data['kode_pelanggan'] ? $data['kode_pelanggan'] : '-------').'</td>
				<td>'.$data['nama_customer'].'</td>
				<td>'.$addr.'</td>
				<td>'.$data['telp_customer'].'</td>
				<td>'.$data['fax_customer'].'</td>
				<td class="text-center"><a href="'.$lin.'" class="btn btn-info btn-action"><i class="fa fa-info-circle"></i></a></td>
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
