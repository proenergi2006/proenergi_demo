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
	$arrTgl = array(1=>"h.tanggal_poc", "b.tgl_kirim_po", "a.tanggal_loading", "b.tgl_eta_po");

	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	$q5	= isset($_POST["q5"])?htmlspecialchars($_POST["q5"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select a.*, c.pr_pelanggan, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, j.fullname, n.nama_transportir, n.nama_suplier, b.no_spj, k.nomor_plat, 
			l.nama_sopir, b.volume_po, h.produk_poc, p.id_area, c.pr_vendor, r.nama_terminal, r.tanki_terminal, r.lokasi_terminal, s.wilayah_angkut, m.nomor_po, m.tanggal_po, 
			c.produk, b.tgl_kirim_po, h.nomor_poc, h.tanggal_poc, b.tgl_eta_po, b.jam_eta_po, b.mobil_po 
			from pro_po_ds_detail a 
			join pro_po_ds o on a.id_ds = o.id_ds 
			join pro_po_detail b on a.id_pod = b.id_pod 
			join pro_po m on a.id_po = m.id_po 
			join pro_pr_detail c on a.id_prd = c.id_prd 
			join pro_po_customer_plan d on a.id_plan = d.id_plan 
			join pro_po_customer h on d.id_poc = h.id_poc 
			join pro_customer_lcr e on d.id_lcr = e.id_lcr
			join pro_customer i on h.id_customer = i.id_customer 
			join acl_user j on i.id_marketing = j.id_user 
			join pro_master_provinsi f on e.prov_survey = f.id_prov 
			join pro_master_kabupaten g on e.kab_survey = g.id_kab
			join pro_penawaran p on h.id_penawaran = p.id_penawaran  
			join pro_master_area q on p.id_area = q.id_master 
			join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
			join pro_master_transportir_sopir l on b.sopir_po = l.id_master
			join pro_master_transportir n on m.id_transportir = n.id_master 
			join pro_master_terminal r on o.id_terminal = r.id_master 
			join pro_master_wilayah_angkut s on e.id_wil_oa = s.id_master and e.prov_survey = s.id_prov and e.kab_survey = s.id_kab
			where a.is_loaded = 1 and i.id_customer = '".paramDecrypt($_SESSION["sinori".SESSIONID]["customer"])."'";
	/*$sql = "select a.*, c.pr_pelanggan, i.nama_customer, i.kode_pelanggan, e.alamat_survey, f.nama_prov, g.nama_kab, j.fullname, h.nomor_poc, 
			n.nama_transportir, n.nama_suplier, n.lokasi_suplier, b.no_spj, b.tgl_eta_po, b.jam_eta_po, k.nomor_plat, l.nama_sopir, b.volume_po 
			from pro_po_ds_detail a join pro_po_detail b on a.id_pod = b.id_pod join pro_pr_detail c on a.id_prd = c.id_prd 
			join pro_po_customer_plan d on a.id_plan = d.id_plan join pro_customer_lcr e on d.id_lcr = e.id_lcr
			join pro_master_provinsi f on e.prov_survey = f.id_prov join pro_master_kabupaten g on e.kab_survey = g.id_kab
			join pro_po_customer h on d.id_poc = h.id_poc join pro_customer i on h.id_customer = i.id_customer join acl_user j on i.id_marketing = j.id_user 
			join pro_master_transportir_mobil k on b.mobil_po = k.id_master join pro_master_transportir_sopir l on b.sopir_po = l.id_master
			join pro_po m on a.id_po = m.id_po join pro_master_transportir n on m.id_transportir = n.id_master join pro_po_ds o on a.id_ds = o.id_ds
			where i.id_customer = '".paramDecrypt($_SESSION["sinori".SESSIONID]["customer"])."' and (a.is_cancel = 1 or a.is_delivered = 1)";*/

	if($q1 != "")
		$sql .= " and (upper(h.nomor_poc) like '%".strtoupper($q1)."%' or upper(b.no_spj) = '".strtoupper($q1)."' or upper(k.nomor_plat) = '".strtoupper($q1)."' 
					or upper(l.nama_sopir) like '%".strtoupper($q1)."%' or upper(i.nama_customer) like '%".strtoupper($q1)."%' or upper(a.nomor_do) like '%".strtoupper($q1)."%' 
					or upper(r.nama_terminal) like '%".strtoupper($q1)."%' or upper(r.tanki_terminal) like '%".strtoupper($q1)."%' 
					or upper(r.lokasi_terminal) like '%".strtoupper($q1)."%')";
	if($q2 != "" && $q3 != "" && $q4 == "")
		$sql .= " and ".$arrTgl[$q2]." = '".tgl_db($q3)."'";
	else if($q2 != "" && $q3 != "" && $q4 != "")
		$sql .= " and ".$arrTgl[$q2]." between '".tgl_db($q3)."' and '".tgl_db($q4)."'";

	if($q5 != "" && $q5 == "1")
		$sql .= " and a.is_loaded = 0 and a.is_delivered = 0 and a.is_cancel = 0";
	else if($q5 != "" && $q5 == "2")
		$sql .= " and a.is_loaded = 1 and a.is_delivered = 0 and a.is_cancel = 0";
	else if($q5 != "" && $q5 == "3")
		$sql .= " and a.is_loaded = 1 and a.is_delivered = 1";
	else if($q5 != "" && $q5 == "4")
		$sql .= " and a.is_loaded = 1 and a.is_cancel = 1";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= "  order by a.tanggal_loading desc, a.jam_loading, a.nomor_urut_ds, a.id_dsd limit ".$position.", ".$length;
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
			$idp 		= $data["id_dsd"];
			$volpo		= $data['volume_po'];
			$tempal 	= strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamat		= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];

			$terminal1 	= $data['nama_terminal'];
			$terminal2 	= ($data['tanki_terminal'])?' - '.$data['tanki_terminal']:'';
			$terminal3 	= ($data['lokasi_terminal'])?'<br />'.$data['lokasi_terminal']:'';
			$terminal 	= $terminal1.$terminal2.$terminal3;

			$linkParam 	= paramEncrypt($data['id_dsd']."|#|".$data['volume_po']);
			$linkList 	= paramEncrypt($data['id_dsd']."|#|1|#|".$data['nomor_poc']."[]".$data['nama_customer']."|#|".$data['mobil_po']."|#|".$data['volume_po']);

			$seg_aw 	= ($data['nomor_segel_awal'])?str_pad($data['nomor_segel_awal'],4,'0',STR_PAD_LEFT):'';
			$seg_ak 	= ($data['nomor_segel_akhir'])?str_pad($data['nomor_segel_akhir'],4,'0',STR_PAD_LEFT):'';
			if($data['jumlah_segel'] == 1)
				$nomor_segel = $data['pre_segel']."-".$seg_aw;
			else if($data['jumlah_segel'] == 2)
				$nomor_segel = $data['pre_segel']."-".$seg_aw." &amp; ".$data['pre_segel']."-".$seg_ak;
			else if($data['jumlah_segel'] > 2)
				$nomor_segel = $data['pre_segel']."-".$seg_aw." s/d ".$data['pre_segel']."-".$seg_ak;
			else $nomor_segel = '';
			
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
						<p style="margin-bottom:0px"><b>'.$data['nomor_do'].'</b></p>
						<p style="margin-bottom:0px">'.$data['nama_customer'].'</p>
						<p style="margin-bottom:0px">'.$alamat.'</p>
						<p style="margin-bottom:0px">Wilayah OA : '.$data['wilayah_angkut'].'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>'.$data['nomor_poc'].'</b></p>
						<p style="margin-bottom:0px">'.number_format($volpo).' Liter '.$data['produk'].'</p>
						<p style="margin-bottom:0px">Tgl PO &nbsp;&nbsp;&nbsp;: '.tgl_indo($data['tanggal_poc'], 'short').'</p>
						<p style="margin-bottom:0px">Tgl Kirim : '.tgl_indo($data['tgl_kirim_po'], 'short').'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>'.$data['nama_suplier'].'</b></p>
						<p style="margin-bottom:0px">'.$data['no_spj'].'</p>
						<p style="margin-bottom:0px">Truck &nbsp;: '.$data['nomor_plat'].'</p>
						<p style="margin-bottom:0px">Driver : '.$data['nama_sopir'].'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>'.$terminal.'</b></p>
						<p style="margin-bottom:0px">'.$nomor_segel.'</p>
						<p style="margin-bottom:0px">ETL : '.tgl_indo($data['tanggal_loading'], 'short').' '.date("H:i", strtotime($data['jam_loading'])).'</p>
						<p style="margin-bottom:0px">ETA : '.tgl_indo($data['tgl_eta_po'], 'short').' '.date("H:i", strtotime($data['jam_eta_po'])).'</p>
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
