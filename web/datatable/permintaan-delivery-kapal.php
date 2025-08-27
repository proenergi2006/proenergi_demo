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
	$sql = "select a.*, c.tanggal_kirim, d.produk_poc, e.nama_customer, f.nama_suplier, b.pr_terminal, g.id_area, h.alamat_survey, i.nama_prov, j.nama_kab, k.wilayah_angkut, 
			m.nama_terminal, m.tanki_terminal, m.lokasi_terminal, b.produk, b.pr_vendor, d.nomor_poc 
			from pro_po_ds_kapal a 
			join pro_pr_detail b on a.id_prd = b.id_prd 
			join pro_po_customer_plan c on b.id_plan = c.id_plan 
			join pro_po_customer d on c.id_poc = d.id_poc 
			join pro_customer e on d.id_customer = e.id_customer 
			join pro_master_transportir f on a.transportir = f.id_master 
			join pro_penawaran g on d.id_penawaran = g.id_penawaran 
			join pro_customer_lcr h on c.id_lcr = h.id_lcr
			join pro_master_provinsi i on h.prov_survey = i.id_prov 
			join pro_master_kabupaten j on h.kab_survey = j.id_kab 
			join pro_master_wilayah_angkut k on h.id_wil_oa = k.id_master and h.prov_survey = k.id_prov and h.kab_survey = k.id_kab 
			join pro_master_area l on g.id_area = l.id_master 
			join pro_master_terminal m on a.terminal = m.id_master 
			where e.id_customer = '".paramDecrypt($_SESSION["sinori".SESSIONID]["customer"])."' and a.is_loaded = 1";
	/*$sql = "select a.*, c.tanggal_kirim, d.produk_poc, d.nomor_poc, e.nama_customer, f.nama_suplier 
			from pro_po_ds_kapal a join pro_pr_detail b on a.id_prd = b.id_prd 
			join pro_po_customer_plan c on b.id_plan = c.id_plan join pro_po_customer d on c.id_poc = d.id_poc 
			join pro_customer e on d.id_customer = e.id_customer join pro_master_transportir f on a.transportir = f.id_master 
			where e.id_customer = '".paramDecrypt($_SESSION["sinori".SESSIONID]["customer"])."' and (a.is_cancel = 1 or a.is_delivered = 1)";*/

	if($q1 != "")
		$sql .= " and (upper(a.nomor_dn_kapal) like '".strtoupper($q1)."%' or upper(a.notify_nama) like '%".strtoupper($q1)."%' 
				or upper(a.vessel_name) like '%".strtoupper($q1)."%' or upper(a.kapten_name) like '%".strtoupper($q1)."%' or upper(e.nama_customer) like '%".strtoupper($q1)."%' 
				or upper(d.nomor_poc) like '%".strtoupper($q1)."%' or upper(m.nama_terminal) like '%".strtoupper($q1)."%' or upper(m.tanki_terminal) like '%".strtoupper($q1)."%' 
				or upper(m.lokasi_terminal) like '%".strtoupper($q1)."%')";
	if($q2 != "" && $q3 == "")
		$sql .= " and c.tanggal_kirim = '".tgl_db($q2)."'";
	else if($q2 != "" && $q3 != "")
		$sql .= " and c.tanggal_kirim between '".tgl_db($q2)."' and '".tgl_db($q3)."'";

	if($q4 != "" && $q4 == "1")
		$sql .= " and a.is_loaded = 0 and a.is_delivered = 0 and a.is_cancel = 0";
	else if($q4 != "" && $q4 == "2")
		$sql .= " and a.is_loaded = 1 and a.is_delivered = 0 and a.is_cancel = 0";
	else if($q4 != "" && $q4 == "3")
		$sql .= " and a.is_loaded = 1 and a.is_delivered = 1";
	else if($q4 != "" && $q4 == "4")
		$sql .= " and a.is_loaded = 1 and a.is_cancel = 1";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= "  order by c.tanggal_kirim desc, a.id_dsk limit ".$position.", ".$length; echo $sql; exit;
	$count = 0;

	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="7" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$idp 		= $data["id_dsk"];
			$volpo		= $data['bl_lo_jumlah'];
			$tempal 	= strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamat		= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];

			$terminal1 	= $data['nama_terminal'];
			$terminal2 	= ($data['tanki_terminal'])?' - '.$data['tanki_terminal']:'';
			$terminal3 	= ($data['lokasi_terminal'])?'<br />'.$data['lokasi_terminal']:'';
			$terminal 	= $terminal1.$terminal2.$terminal3;

			$linkParam 	= paramEncrypt($data['id_dsk']."|#|".$volpo);
			$linkList 	= paramEncrypt($data['id_dsk']."|#|2|#|".$data['nomor_poc']."[]".$data['nama_customer']."|#|".$data['mobil_po']."|#|".$data['bl_lo_jumlah']);
			
			if($data['is_delivered']){
				$status = '<p style="margin-bottom:0px;"><b>Delivered</b><br/>'.date("d/m/Y H:i", strtotime($data['tanggal_delivered'])).'</p>';
			} else if($data['is_cancel']){
				$status = '<p style="margin-bottom:0px;" class="text-red"><b>Canceled</b><br/>'.date("d/m/Y H:i", strtotime($data['tanggal_cancel'])).'</p>';
			} else{
				$bmp = json_decode($data['status_pengiriman'], true);
				$idb = count($bmp)-1;
				$status = '<p style="margin-bottom:2px;"><b>'.$bmp[$idb]['tanggal'].'</b></p><span>'.$bmp[$idb]['status'].'</span>';
			}

			$content .= '
				<tr>
					<td class="text-center">'.$count.'</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>'.$data['nomor_dn_kapal'].'</b></p>
						<p style="margin-bottom:0px">'.$data['nama_customer'].'</p>
						<p style="margin-bottom:0px">'.$alamat.'</p>
						<p style="margin-bottom:0px">Wilayah OA : '.$data['wilayah_angkut'].'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>'.$data['nomor_poc'].'</b></p>
						<p style="margin-bottom:0px">'.number_format($volpo).' Liter '.$data['produk'].'</p>
						<p style="margin-bottom:0px">Tgl Kirim : '.tgl_indo($data['tanggal_kirim'], 'short').'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>'.$data['nama_suplier'].'</b></p>
						<p style="margin-bottom:0px">Vessel &nbsp;: '.$data['vessel_name'].'</p>
						<p style="margin-bottom:0px">Captain : '.$data['kapten_name'].'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>'.$terminal.'</b></p>
						<p style="margin-bottom:0px">'.$data['notify_nama'].'</p>
					</td>
					<td class="text-left">'.$status.'</td>
					<td class="text-center action">
						<a class="listStsT margin-sm btn btn-action btn-info" title="History Pengiriman" data-param="'.$linkList.'"><i class="fa fa-info-circle"></i></a>
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
