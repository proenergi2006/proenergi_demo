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
	if(in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']), array("9"))){
		$sql = "
			select 
				case 
					when (e.disposisi_result > 0 and e.disposisi_result = 1 and e.logistik_result = 0 and e.is_approved = 0) then 1 
					when (e.disposisi_result > 1 and e.is_approved = 0) then 2 
					else 3 
			end as ordernya, 
		";
	} 
	
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10){
		$sql = "
			select 
				case 
					when (e.disposisi_result > 0 and e.disposisi_result = 1 and e.finance_result = 0 and e.is_approved = 0) then 1 
					when (e.disposisi_result > 1 and e.is_approved = 0) then 2 
					else 3 
			end as ordernya, 
		";
	} 
	
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7){
		$sql = "
			select 
				case 
					when (e.disposisi_result > 1 and e.disposisi_result = 2 and e.sm_result = 0 and e.is_approved = 0) then 1 
					when (e.disposisi_result > 2 and e.is_approved = 0) then 2 
					else 3 
			end as ordernya, 
		";
	} 
	
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6){
		$sql = "
			select 
				case 
					when (e.disposisi_result > 2 and e.disposisi_result = 3 and e.om_result = 0 and e.is_approved = 0) then 1 
					when (e.disposisi_result > 3 and e.is_approved = 0) then 2 
					else 3 
			end as ordernya, 
		";
	}
	
	else{
		$sql = "
			select 0 as ordernya, 
		";
	}
	
	$sql .= "
		a.nama_customer, a.alamat_customer, a.kode_pelanggan, b.nama_prov, c.nama_kab, d.fullname, 
		a.top_payment, a.id_wilayah, a.id_group, f.nama_cabang, 
		e.id_verification, e.is_approved, e.legal_result, e.finance_result, e.logistik_result, e.om_result, e.sm_result, 
		e.cfo_result, e.ceo_result, e.is_active, e.disposisi_result, 
		e.legal_tgl_proses, e.finance_tgl_proses, e.logistik_tgl_proses, e.om_tgl_proses, e.sm_tgl_proses, e.cfo_tgl_proses, e.ceo_tgl_proses, 
		e.role_approve, e.tanggal_approved,
		g.id_review 
		from pro_customer a 
		join pro_master_provinsi b on a.prov_customer = b.id_prov 
		join pro_master_kabupaten c on a.kab_customer = c.id_kab 
		join acl_user d on a.id_marketing = d.id_user 
		join pro_customer_verification e on a.id_customer = e.id_customer and e.is_evaluated = 1 and e.is_reviewed = 1 
		join pro_master_cabang f on a.id_wilayah = f.id_master 
		join pro_customer_review g on e.id_verification = g.id_verification 
		join acl_user h on a.id_marketing = h.id_user 
		where 1=1 
	";

	if(in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']), array("9", "10"))){
		$sql .= " and a.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";
	} 
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7){
		$sql .= " and a.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";
	}
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6){
		$sql .= " and (
			(h.id_role in (11, 18) and a.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."') or 
			(h.id_role = 17 and h.id_om = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')
		)";
	}

	if($q1 != ""){
		$sql .= " and (upper(a.nama_customer) like '%".strtoupper($q1)."%' or a.kode_pelanggan = '".$q1."' or concat('LC',lpad(e.id_verification,4,'0')) = '".strtoupper($q1)."')";
	}

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by ordernya, g.id_review desc limit ".$position.", ".$length;

	$content = "";
	if($tot_record ==  0){
		$content .= '<tr><td colspan="6" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);

		foreach($result as $data){
			$count++;
			$temp1 		= strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamatCust = $data['alamat_customer']." ".ucwords($temp1)." ".$data['nama_prov'];
			$linkDetail	= BASE_URL_CLIENT.'/verifikasi-data-customer-detail.php?'.paramEncrypt('idr='.$data['id_verification']);
			$linkCetak	= ACTION_CLIENT.'/cetak-verifikasi-data-customer.php?'.paramEncrypt('idr='.$data['id_verification']);

			$arrPosisi 	= array(2=>"BM", 3=>"OM", 4=>"CFO", 5=>"CEO", 6=>"MGR Finance");
			$arrRole 	= array(7=>"BM", 6=>"OM", 3=>"CEO", 4=>"CFO", 15=>"MGR Finance");
			
			$background	= "";			
			if($data['disposisi_result'] == '1' && $data['ordernya'] <= 1){
				$background	= 'style="background-color:#f5f5f5"';
			} else if($data['disposisi_result'] == '2' && $data['ordernya'] <= 1){
				$background	= 'style="background-color:#f5f5f5"';
			} else if($data['disposisi_result'] == '3' && $data['ordernya'] <= 1){
				$background	= 'style="background-color:#f5f5f5"';
			}
	
			if($data['is_approved'] == 1){
				if($data['role_approve'])
					$disposisi = "Disetujui  ".$arrRole[$data['role_approve']]."<br /><i>".date("d/m/Y H:i:s", strtotime($data['tanggal_approved']))."</i>";			
				else
					$disposisi = "Disetujui ".$arrPosisi[$data['disposisi_result']]."<br /><i>".date("d/m/Y H:i:s", strtotime($data['tanggal_approved']))."</i>";
			}
			else if($data['is_approved'] == 2){
				if($data['role_approve'])
					$disposisi = "Ditolak ".$arrRole[$data['role_approve']]."<br /><i>".date("d/m/Y H:i:s", strtotime($data['tanggal_approved']))."</i>";
				else
					$disposisi = "Ditolak ".$arrPosisi[$data['disposisi_result']]."<br /><i>".date("d/m/Y H:i:s", strtotime($data['tanggal_approved']))."</i>";
			}
			else{
				if($data['disposisi_result'] == 1){
					$tahap_verifikasi = [];
					(!$data['finance_result'])? $tahap_verifikasi[] = " Admin Finance":"";
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
			}

			$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'"'.$background.'>
					<td class="text-center">'.$count.'</td>
					<td>LC'.str_pad($data['id_verification'],4,'0',STR_PAD_LEFT).'</td>
					<td>'.$data['kode_pelanggan'].'</td>
					<td>
						<p style="margin-bottom:0px"><b>'.$data['nama_customer'].'</b></p>
						<p style="margin-bottom:0px">'.$data['fullname'].'</p>
					</td>
					<td>'.$alamatCust.'</td>
					<td>'.$disposisi.'</td>
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
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
