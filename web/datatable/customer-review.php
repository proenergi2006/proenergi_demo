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
	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "
		select a.*, b.is_approved, b.legal_result, b.finance_result, b.logistik_result, b.om_result, b.is_evaluated, b.is_reviewed, b.disposisi_result, b.role_approve,
		c.nama_customer, c.kode_pelanggan, c.alamat_customer, d.nama_prov, e.nama_kab, b.tanggal_approved, c.id_customer 
		from pro_customer_review a 
		join pro_customer_verification b on a.id_verification = b.id_verification 
		join pro_customer c on b.id_customer = c.id_customer 
		join pro_master_provinsi d on c.prov_customer = d.id_prov 
		join pro_master_kabupaten e on c.kab_customer = e.id_kab 
		where 1=1
	";
	
	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 11 || paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 17)
		$sql .= " and c.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 18) {
		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $sql .= " and (c.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."' or c.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
        else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $sql .= " and (c.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."' or c.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
	}
	
	if($q1 != "")
		$sql .= " and (upper(c.nama_customer) like '%".strtoupper($q1)."%' or c.kode_pelanggan = '".$q1."' or concat('RC',lpad(a.id_review,4,'0')) = '".strtoupper($q1)."')";

	if($q2 != "")
	{
		if($q2 == 3)
			$sql .= " and b.is_approved = 0 and b.disposisi_result > 0";
		else if($q2 == '0')
			$sql .= " and b.disposisi_result = 0";
		else
			$sql .= " and b.is_approved = ".$q2;
	}

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.review_result, a.id_review desc limit ".$position.", ".$length;

	$content = "";
	$count = 0;
	$sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);

	if($tot_record <= 0){
		$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$idr 	 = $data['id_verification'];
			$idk 	 = $data['id_review'];
			$linkSur = BASE_URL_CLIENT.'/customer-review-detail.php?'.paramEncrypt('idr='.$idr.'&idk='.$idk);
			$tmp1 	 = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamat  = $data['alamat_customer']." ".ucwords($tmp1)." ".$data['nama_prov'];
			$arrPosisi 	= array(3=>"OM",4=>"CFO",5=>"CEO");
			$arrRole 	= array(7=>"BM",3=>"CEO",4=>"CFO",15=>"MGR Finance",6=>"OM");

			if($data['is_approved'] == 1)
			{
				if($data['role_approve'])
					$disposisi = "Disetujui ".$arrRole[$data['role_approve']]."<br /><i>".date("d/m/Y H:i:s", strtotime($data['tanggal_approved']))."</i>";
				else
					$disposisi = "Disetujui ".$arrPosisi[$data['disposisi_result']]."<br /><i>".date("d/m/Y H:i:s", strtotime($data['tanggal_approved']))."</i>";
			}
			else if($data['is_approved'] == 2)
			{
				if($data['role_approve'])
					$disposisi = "Ditolak ".$arrRole[$data['role_approve']]."<br /><i>".date("d/m/Y H:i:s", strtotime($data['tanggal_approved']))."</i>";
				else
					$disposisi = "Ditolak ".$arrPosisi[$data['disposisi_result']]."<br /><i>".date("d/m/Y H:i:s", strtotime($data['tanggal_approved']))."</i>";
			}
			else if((!$data['finance_result'] || !$data['logistik_result']) && !$data['disposisi_result'])
				$disposisi = 'Review Marketing';
			else if($data['disposisi_result'] == 1)
			{
				$tahap_verifikasi = [];
				(!$data['finance_result'])? $tahap_verifikasi[] = " Admin Finance":"";
				// (!$data['legal_result'])? $tahap_verifikasi[] = " Manager Finance":"";
				(!$data['logistik_result'])? $tahap_verifikasi[] = " Logistics":"";
				
				if((!$data['finance_result'] || !$data['logistik_result']))
					$tahap = "(".implode(",", $tahap_verifikasi)." )";
				else
					$tahap = "";

				$disposisi = "Tahap Verifikasi<br>".$tahap;
			}
			else if($data['disposisi_result'] == 2)
				$disposisi = "Verifikasi BM";
			else if($data['disposisi_result'] == 3)
				$disposisi = "Verifikasi OM";
			else if($data['disposisi_result'] == 4)
				$disposisi = "Verifikasi CFO";
			else if($data['disposisi_result'] == 5)
				$disposisi = "Verifikasi CEO";
			else if($data['disposisi_result'] == 6)
				$disposisi = "Verifikasi MGR Finance";
			else $disposisi = 'Terdaftar';

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkSur.'" '.$background.'>
					<td class="text-center">'.$count.'</td>
					<td class="text-center">'.date("d/m/Y", strtotime($data['review_tanggal'])).'</td>
					<td class="text-center">RC'.str_pad($idk,4,'0',STR_PAD_LEFT).'</td>
					<td class="text-center">LC'.str_pad($idr,4,'0',STR_PAD_LEFT).'</td>
					<td>'.($data['kode_pelanggan']?$data['kode_pelanggan']:'-------').'</td>
					<td>
						<p style="margin-bottom:0px;"><b>'.$data['nama_customer'].'</b></p>
						<p style="margin-bottom:0px;">'.$alamat.'</p>
					</td>
					<td>'.$disposisi.'</td>
					<td class="text-center action"><a class="margin-sm btn btn-action btn-info" href="'.$linkSur.'"><i class="fa fa-info-circle"></i></a></td>
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
