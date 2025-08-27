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
		select a.id_customer, a.kode_pelanggan, a.nama_customer, a.email_customer, a.telp_customer, a.fax_customer, 
		a.alamat_customer, b.nama_prov, c.nama_kab, d.nama_cabang 
		from pro_customer a 
		join pro_master_provinsi b on a.prov_customer = b.id_prov 
		join pro_master_kabupaten c on a.kab_customer = c.id_kab
		join pro_master_cabang d on a.id_wilayah = d.id_master 
		where 1=1 AND a.need_update = 1 and a.is_generated_link = 0
	";
	if ($sesrol == 18) {
		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
			$sql .= " and (a.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."' or a.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
		else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
			$sql .= " and (a.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."' or a.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
	} else if ($sesrol == 17 || $sesrol == 11) {
		$sql .= " and a.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'";
	}

	if($q1 != "")
		$sql .= " and (upper(a.nama_customer) like '%".strtoupper($q1)."%' or a.kode_pelanggan = '".$q1."')";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.id_customer desc limit ".$position.", ".$length;

	$content = "";
	$count = 0;
	if($tot_record <= 0){
		$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$idr 	= $data['id_customer'];
			$lin 	= ACTION_CLIENT."/customer-generate-link.php?".paramEncrypt("idr=".$idr);
			$tmp1 	= strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$addr 	= $data['alamat_customer']." ".ucwords($tmp1)." ".$data['nama_prov'];

        	$content .= '
			<tr>
				<td class="text-center">'.$count.'</td>
				<td>
					<p style="margin-bottom:0px;"><b>'.($data['kode_pelanggan'] ? $data['kode_pelanggan'] : '-------').'</b></p>
					<p style="margin-bottom:0px;">'.$data['nama_customer'].'</p>
				</td>
				<td>'.$data['nama_cabang'].'</td>
				<td>'.$addr.'</td>
				<td>'.$data['email_customer'].'</td>
				<td>
					<p style="margin-bottom:0px;">Telp : '.$data['telp_customer'].'</p>
					<p style="margin-bottom:0px;">Fax &nbsp;&nbsp;: '.$data['fax_customer'].'</p>
				</td>
				<td class="text-center"><a href="'.$lin.'" style="padding:3px 8px;" class="btn btn-primary btn-sm">Get Link</a></td>
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
