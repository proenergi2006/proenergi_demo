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
	$q5	= isset($_POST["q5"])?htmlspecialchars($_POST["q5"], ENT_QUOTES):'';
	$q6	= isset($_POST["q6"])?htmlspecialchars($_POST["q6"], ENT_QUOTES):'';
	$q7	= isset($_POST["q7"])?htmlspecialchars($_POST["q7"], ENT_QUOTES):'';
	$q8	= isset($_POST["q8"])?htmlspecialchars($_POST["q8"], ENT_QUOTES):'';
	
	$p = new paging;
	$sql = "select a.*, b.nama_customer, c.nama_cabang, d.nama_area, e.harga_minyak, f.fullname 
			from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join pro_master_cabang c on a.id_cabang = c.id_master 
			join pro_master_area d on a.id_area = d.id_master join acl_user f on b.id_marketing = f.id_user 
			left join pro_master_harga_pertamina e on a.masa_awal = e.periode_awal and a.masa_akhir = e.periode_akhir and a.id_area = e.id_area and a.produk_tawar = e.id_produk 
			where 1=1";
	if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 11 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 17)
		$sql .= " and b.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'";
	else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 7 || paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 18) {
		if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $sql .= " and (b.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."' or b.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";
        else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $sql .= " and (b.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."' or b.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";
	}
	else if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) == 6)
		$sql .= " and (b.id_group = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_group"])."' or f.id_om = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."')";

	if($q1 && !$q2){
		$t1 = explode("/",$q1);
		$m1 = $t1[2]."/".$t1[1]."/01";
		$sql .= " and a.masa_awal = '".$m1."'";
	} else if($q1 && $q2){
		$t1 = explode("/",$q1);
		$m1 = $t1[2]."/".$t1[1]."/01";
		$t2 = explode("/",$q2);
		$m2 = $t2[2]."/".$t2[1]."/15";
		$sql .= " and a.masa_awal between '".$m1."' and '".$m2."'";
	}
	if($q3) $sql .= " and upper(b.nama_customer) like '%".strtoupper($q3)."%'";
	if($q4 && $q5){
		$arrOp = array(1=>"=", ">=", "<=");
		$sql .= " and a.volume_tawar ".$arrOp[$q4]." '".str_replace(array(".",","),array("",""),$q5)."'";
	}
	if($q6) $sql .= " and a.id_area = '".$q6."'";
	if($q7) $sql .= " and a.id_cabang = '".$q7."'";
	if($q8) $sql .= " and b.id_marketing = '".$q8."'";

	if(is_numeric($length)){
		$tot_record = $con->num_rows($sql);
		$tot_page 	= ceil($tot_record/$length);
		$page		= ($start > $tot_page)?$start-1:$start; 
		$position 	= $p->findPosition($length, $tot_record, $page);
		$sql .= " order by a.id_penawaran desc limit ".$position.", ".$length;
	} else{
		$tot_record = $con->num_rows($sql);
		$page		= 1; 
		$position 	= 0;
		$sql .= " order by a.id_penawaran desc";
	}
	$link = BASE_URL_CLIENT.'/report/m-penawaran-exp.php?'.paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$q4.'&q5='.$q5.'&q6='.$q6.'&q7='.$q7.'&q8='.$q8);

	$content = "";
	$count = 0;
	if($tot_record == 0){
		$content .= '<tr><td colspan="9" style="text-align:center"><input type="hidden" id="uriExp" value="'.$link.'" />Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= (is_numeric($length))?ceil($tot_record/$length):1;
		$result 	= $con->getResult($sql);
		$tot1 		= 0;
		$total 		= 0;
		$nom1 		= 0;
		foreach($result as $data){
			$count++;
			$masa = date("d/m/Y",strtotime($data['masa_awal']))." s/d ".date("d/m/Y",strtotime($data['masa_akhir']));
			$tot1 = $tot1 + $data['volume_tawar'];
			$arHj = json_decode($data['detail_rincian'], true);
			//$disc = ($data['harga_minyak'] && $data['harga_asli'])?(1-($data['harga_asli']/$data['harga_minyak'])) * 100:'';
			//Lasamba
			if($data['harga_minyak'] && $data['harga_asli']){
				if($data['harga_asli'] > $data['harga_minyak']){ // Jika Harga Jual Dasar lebih besar dari harga pertamina
					$disc = (((abs($data['harga_minyak']-$data['harga_asli'])) / $data['harga_minyak']) + 1) * 100;
				}else{
					$disc = (($data['harga_minyak']-$data['harga_asli']) / $data['harga_minyak']) * 100;
					
				}
				$nom1++;
				$total = $total + $disc;
			}else{
				$disc = '';
			}
			
			$harga_ppn = $data['harga_asli'] + $arHj[2]['biaya'];

        	$content .= '
				<tr>
					<td class="text-center">'.$masa.'</td>
					<td class="text-left">'.$data['nama_customer'].'</td>
					<td class="text-left">'.$data['fullname'].'</td>
					<td class="text-center">'.$data['nama_cabang'].'</td>
					<td class="text-center">'.$data['nama_area'].'</td>
					<td class="text-right">'.number_format($data['volume_tawar']).'</td>
					<td class="text-right">'.number_format($data['harga_minyak']).'</td>
					<td class="text-right">'.number_format($data['harga_asli']).'</td>
					<td class="text-right">'.number_format($harga_ppn).'</td>
					<td class="text-right">'.number_format($data['oa_kirim']).'</td>
					<td class="text-right">'.($disc?number_format($disc).'%':'-').'</td>
					<td class="text-right">'.number_format($data['refund_tawar']).'</td>
				</tr>';
		}
		$nom1 = ($nom1)?$nom1:1;
		$content .= '
			<tr>
				<td class="text-center bg-gray" colspan="5"><b>TOTAL</b></td>
				<td class="text-right bg-gray"><b>'.number_format($tot1).'</b></td>
				<td class="text-center bg-gray" colspan="4"><input type="hidden" id="uriExp" value="'.$link.'" /><b>AVERAGE</b></td>
				<td class="text-right bg-gray"><b>'.number_format($total/$nom1).'</b></td>
				<td class="text-right bg-gray"></td>
			</tr>';
	} 

	$json_data = array(
					"items"		=> $content,
					"pages"		=> $tot_page,
					"page"		=> $page,
					"totalData"	=> $tot_record,
					"infoData"	=> "Showing ".($position+1)." - ".$count." of ".$tot_record." entries",
				);
	echo json_encode($json_data);
?>
