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
	
	$p = new paging;
	$sql = "select a.*, b.nama_customer, b.kode_pelanggan, b.alamat_customer, b.email_customer, b.id_wilayah, b.id_group, c.fullname, d.nama_prov, e.nama_kab 
			from pro_customer_verification a join pro_customer b on a.id_customer = b.id_customer join acl_user c on b.id_marketing = c.id_user 
			join pro_master_provinsi d on b.prov_customer = d.id_prov join pro_master_kabupaten e on b.kab_customer = e.id_kab where 1=1";

	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 11 || paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 17)
		$sql .= " and b.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'";
	else if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 18) {
		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
			$sql .= " and (b.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."' or b.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
		else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
			$sql .= " and (b.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."' or b.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
	}

	if($q1 != "")
		$sql .= " and (upper(b.nama_customer) like '%".strtoupper($q1)."%' or b.kode_pelanggan = '".$q1."' or concat('LC',lpad(a.id_verification,4,'0')) = '".strtoupper($q1)."')";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.id_verification desc , a.is_active desc, a.disposisi_result asc, a.tanggal_approved desc limit ".$position.", ".$length;

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
			$idr 	 	= $data['id_customer'];
			$idk 	 	= $data['id_verification'];
			$token 	 	= $data['token_verification'];
			$linkRes 	= ACTION_CLIENT."/customer-resubmit.php?".paramEncrypt('idr='.$idr.'&idk='.$idk);
			$linkSur 	= BASE_URL_CLIENT.'/customer-generate-link-email.php?'.paramEncrypt('idr='.$idr.'&idk='.$idk.'&token='.$token);
			$linkCus 	= BASE_URL.'/customer/update-customer.php?'.paramEncrypt('idr='.$idr.'&idk='.$idk.'&token='.$token);
			$isRevisi 	= (!$data['legal_result'] || !$data['finance_result'] || !$data['logistik_result']) && $data['is_evaluated']?true:false;
			$tmp1 	 	= strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamat  	= $data['alamat_customer']." ".ucwords($tmp1)." ".$data['nama_prov'];
			$arrPosisi 	= array(3=>"OM",4=>"CFO",5=>"CEO");
			if($data['is_approved'] == 1 and in_array($data['disposisi_result'], [3,4,5])) {
				$disposisi = "Disetujui ".$arrPosisi[$data['disposisi_result']]."<br /><i>".date("d/m/Y H:i:s", strtotime($data['tanggal_approved']))."</i>";
			} else 
			if($data['is_approved'] == 2 and in_array($data['disposisi_result'], [3,4,5])) {
				$disposisi = "Ditolak ".$arrPosisi[$data['disposisi_result']]."<br /><i>".date("d/m/Y H:i:s", strtotime($data['tanggal_approved']))."</i>";
			}
			
			if((!$data['legal_result'] || !$data['finance_result'] || !$data['logistik_result']) && !$data['disposisi_result'])
				$disposisi = 'Review Marketing';

			if($data['disposisi_result'] == 1) {
				$disposisi = "Tahap Verifikasi";
			} else 
			if($data['disposisi_result'] == 2) {
				$disposisi = "Verifikasi BM";
				$isRevisi = false;
			} else 
			if($data['disposisi_result'] == 3) {
				$disposisi = "Verifikasi OM";
				$isRevisi = false;
			} else 
			if($data['disposisi_result'] == 4) {
				$disposisi = "Verifikasi CFO";
				$isRevisi = false;
			} else 
			if($data['disposisi_result'] == 5) {
				$disposisi = "Verifikasi CEO";
				$isRevisi = false;
			} else 
				$disposisi = 'Terdaftar';
			
        	$content .= '
				<tr>
					<td class="text-center">'.$count.'</td>
					<td><a href="'.$linkCus.'" target="_blank">LC'.str_pad($idk,4,'0',STR_PAD_LEFT).'</a></td>
					<td>'.($data['kode_pelanggan']?$data['kode_pelanggan']:'-------').'</td>
					<td>
						<p style="margin-bottom:0px;"><b>'.$data['nama_customer'].'</b></p>
						<p style="margin-bottom:0px;">'.$data['email_customer'].'</p>
					</td>
					<td>'.$alamat.'</td>
					<td>'.$disposisi.'</td>
					<td class="text-center">
						<a class="btn btn-sm btn-primary" style="padding:3px 8px;" '.($data['is_active'] == 1 ?'href="'.$linkSur.'"' :'disabled' ).'>Email</a>
					</td>
					<td class="text-center">
						<a class="btn btn-sm btn-info konfirmasi jarak-kiri" style="padding:3px 8px;" '.($isRevisi ?'href="'.$linkRes.'"' :'disabled').'>Revisi</a>
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
