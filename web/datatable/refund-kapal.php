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
	$q4	= paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]);
	
	$p = new paging;
	$sql = "select a.*, c.tanggal_kirim, d.nomor_poc, d.tanggal_poc, e.nama_customer, e.kode_pelanggan, f.fullname, g.refund_tawar, h.nama_area, j.wilayah_angkut  
			from pro_po_ds_kapal a 
			join pro_pr_detail b on a.id_prd = b.id_prd 
			join pro_po_customer_plan c on b.id_plan = c.id_plan 
			join pro_po_customer d on c.id_poc = d.id_poc 
			join pro_customer e on d.id_customer = e.id_customer 
			join acl_user f on e.id_marketing = f.id_user 
			join pro_penawaran g on d.id_penawaran = g.id_penawaran	
			join pro_master_area h on g.id_area = h.id_master 
			join pro_customer_lcr i on c.id_lcr = i.id_lcr
			join pro_master_wilayah_angkut j on i.id_wil_oa = j.id_master and i.prov_survey = j.id_prov and i.kab_survey = j.id_kab
			where a.is_delivered = 1 and g.refund_tawar != 0";

	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6)
		$sql .= "  and ((f.id_role = 11 and e.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."') or (f.id_role = 17 and f.id_om = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'))";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7)
		$sql .= "  and e.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10)
		$sql .= "  and e.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 11 || paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 17)
		$sql .= "  and e.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 18) {
		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $sql .= " and (e.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."' or e.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
        else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $sql .= " and (e.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."' or e.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."')";
	}

	if($q1 != "")
		$sql .= " and (upper(d.nomor_poc) like '".strtoupper($q1)."%' or upper(e.nama_customer) like '%".strtoupper($q1)."%')";
	if($q2 != "")
		$sql .= " and a.tanggal_delivered between '".tgl_db($q2)." 00:00:00' and '".tgl_db($q2)." 23:59:59'";
	if($q3 != "")
		$sql .= " and a.is_bayar = '".$q3."'";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= "  order by a.is_bayar, a.tanggal_delivered desc limit ".$position.", ".$length;

	$content = "";
	$count = 0;
	if($tot_record <= 0){
		$content .= '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		$refund = 0;
		$total_refund = 0;
		foreach($result as $data){
			$count++;
			$idp 		= $data["id_dsk"];
			$volpo		= $data['bl_lo_jumlah'];
			$linkList 	= paramEncrypt($data['id_dsk']."|#|2|#|".$data['nomor_dn_kapal']);
			
			if($data['is_bayar']){
				$status = '<p style="margin-bottom:0px;"><b>Terbayar tanggal '.date("d/m/Y", strtotime($data['tanggal_bayar'])).'</b></p>'.$data['ket_bayar'];
			} else{
				$status = '<p style="margin-bottom:0px;"><b>Diproses</b></p>';
			}

			$content .= '
				<tr>
					<td class="text-center">'.$count.'</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>'.$data['kode_pelanggan'].'</b></p>
						<p style="margin-bottom:0px">'.$data['nama_customer'].'</p>
						<p style="margin-bottom:0px"><i>'.$data['fullname'].'</i></p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>'.$data['nama_area'].'</b></p>
						<p style="margin-bottom:0px">'.$data['port_discharge'].'</p>
						<p style="margin-bottom:0px">Wilayah Angkut : '.$data['wilayah_angkut'].'</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px"><b>'.$data['nomor_poc'].'</b></p>
						<p style="margin-bottom:0px">'.date("d/m/Y H:i", strtotime($data['tanggal_delivered'])).'</p>
						<p style="margin-bottom:0px">'.number_format($volpo).' Liter</p>
					</td>
					<td class="text-right">'.number_format($data['refund_tawar']).'</td>
					<td class="text-right">'.number_format(($data['refund_tawar'] * $volpo)).'</td>
					<td class="text-left">'.$status.'</td>
					<td class="text-center action">
					'.(!$data['is_bayar'] && $q4 == 10?'<a class="editStsT margin-sm btn btn-action btn-info" data-param="'.$linkList.'"><i class="fa fa-info-circle"></i></a>':'').'
            		</td>
				</tr>';
			$refund += $data['refund_tawar'];
			$total_refund += $data['refund_tawar'] * $volpo;
		} 
		$content .= '
				<tr>
					<td class="text-center" colspan="4"><b>Total</b></td>
					<td class="text-right">'.number_format($refund).'</td>
					<td class="text-right">'.number_format($total_refund).'</td>
					<td class="text-left" colspan="2"></td>
            		</td>
				</tr>';
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
