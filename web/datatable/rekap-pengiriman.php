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
	$arrTgl = array(1=>"m.tanggal_po", "b.tgl_kirim_po", "a.tanggal_loading");

	$q1	= isset($_POST["q1"])?htmlspecialchars($_POST["q1"], ENT_QUOTES):'';
	$q2	= isset($_POST["q2"])?htmlspecialchars($_POST["q2"], ENT_QUOTES):'';
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "
		SELECT
		    JSON_EXTRACT(a.status_pengiriman, CONCAT('$[', num.n - 1, '].status')) AS _status_pengiriman,
		    JSON_EXTRACT(a.status_pengiriman, CONCAT('$[', num.n - 1, '].tanggal')) AS _tanggal_pengiriman,
		    a.*, 
		    c.pr_pelanggan, 
			i.nama_customer, 
			e.alamat_survey, 
			f.nama_prov, 
			g.nama_kab, 
			j.fullname, 
			n.nama_transportir, 
			n.nama_suplier, 
			b.no_spj, 
			k.nomor_plat, 
			l.nama_sopir, 
			b.volume_po, 
			h.produk_poc, 
			p.id_area, 
			c.pr_vendor, 
			r.nama_terminal, 
			r.tanki_terminal, 
			r.lokasi_terminal, 
			s.wilayah_angkut, 
			m.nomor_po, 
			m.tanggal_po, 
			c.produk, 
			b.tgl_kirim_po, 
			b.mobil_po 
		FROM 
			(
			    SELECT @row := @row + 1 AS n FROM 
			    (SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) t2,
			    (SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) t1,
			    (SELECT @row:=0) T0
			) num
			join pro_po_ds_detail a ON num.n <= JSON_LENGTH(a.status_pengiriman)
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
		where 
			a.is_loaded = 1 
			and o.id_wilayah = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'
	";

	if($q1 != "")
		$sql .= " and (upper(a.nomor_do) like '".strtoupper($q1)."%' or upper(b.no_spj) = '".strtoupper($q1)."' or upper(k.nomor_plat) = '".strtoupper($q1)."' 
					or upper(l.nama_sopir) like '%".strtoupper($q1)."%' or upper(i.nama_customer) like '%".strtoupper($q1)."%')";
	if($q2 != "" && $q3 != "" && $q4 == "")
		$sql .= " and ".$arrTgl[$q2]." = '".tgl_db($q3)."'";
	else if($q2 != "" && $q3 != "" && $q4 != "")
		$sql .= " and ".$arrTgl[$q2]." between '".tgl_db($q3)."' and '".tgl_db($q4)."'";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= "  order by a.tanggal_loading desc, a.jam_loading, a.nomor_urut_ds, a.id_dsd limit ".$position.", ".$length;
	$link = BASE_URL_CLIENT.'/report/rekap-pengiriman-exp.php?'.paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$q4);

	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="7" style="text-align:center"><input type="hidden" id="uriExp1" value="'.$link.'" />Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$idp 		= $data["id_dsd"];
			$tempal 	= strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamat		= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
			$volpo		= $data['volume_po'];
			$produk_poc	= $data['produk_poc'];
			$tgl_loaded	= $data['tanggal_loaded'];
			$jam_loaded	= $data['jam_loaded'];
			$pr_vendor	= $data['pr_vendor'];
			$id_area	= $data['id_area'];

			$terminal1 	= $data['nama_terminal'];
			$terminal2 	= ($data['tanki_terminal'])?' - '.$data['tanki_terminal']:'';
			$terminal3 	= ($data['lokasi_terminal'])?'<br />'.$data['lokasi_terminal']:'';
			$terminal 	= $terminal1.$terminal2.$terminal3;

			$seg_aw 	= ($data['nomor_segel_awal'])?str_pad($data['nomor_segel_awal'],4,'0',STR_PAD_LEFT):'';
			$seg_ak 	= ($data['nomor_segel_akhir'])?str_pad($data['nomor_segel_akhir'],4,'0',STR_PAD_LEFT):'';
			$eta_po		= (isset($data['tgl_eta_po']) and isset($data['jam_eta_po'])) ?date("d/m/Y", strtotime($data['tgl_eta_po'])).($data['jam_eta_po']?' '.date("H:i", strtotime($data['jam_eta_po'])):''):'';
			if($data['jumlah_segel'] == 1)
				$nomor_segel = $data['pre_segel']."-".$seg_aw;
			else if($data['jumlah_segel'] == 2)
				$nomor_segel = $data['pre_segel']."-".$seg_aw." &amp; ".$data['pre_segel']."-".$seg_ak;
			else if($data['jumlah_segel'] > 2)
				$nomor_segel = $data['pre_segel']."-".$seg_aw." s/d ".$data['pre_segel']."-".$seg_ak;
			else $nomor_segel = '';
        	
			$content .= '
				<tr>
					<td class="text-center">'.$count.'</td>
					<td class="text-left">'.date('d-m-Y', strtotime($data['tanggal_loading'])).'</td>
					<td class="text-left">'.$data['nama_customer'].'</td>
					<td class="text-left">'.$data['alamat_survey'].'</td>
					<td class="text-left">'.$data['nomor_plat'].'</td>
					<td class="text-left">'.$data['nama_sopir'].'</td>
					<td class="text-left">'.$data['volume_po'].'</td>
					<td class="text-left">'.$data['fullname'].'</td>
					<td class="text-left">'.$data['nomor_po'].'</td>
					<td class="text-left">'.$data['produk'].'</td>
					<td class="text-left">'.$data['no_spj'].'</td>
					<td class="text-left">'.$nomor_segel.'</td>
					<td class="text-left">'.$data['nama_suplier'].'</td>
					<td class="text-left">'.$terminal.'</td>
					<td class="text-left">'.$data['nomor_do'].'</td>
					<td class="text-left"> - </td>
					<td class="text-left"> - </td>
					<td class="text-left"> - </td>
					<td class="text-left"> - </td>
					<td class="text-left">'.$data['nomor_order'].'</td>
					<td class="text-left"> - </td>
					<td class="text-left">'.str_replace('"', '', $data['_status_pengiriman']).'</td>
				</tr>
			';
		} 
		$content .= '<tr class="hide"><td colspan="7"><input type="hidden" id="uriExp1" value="'.$link.'" />&nbsp;</td></tr>';
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
