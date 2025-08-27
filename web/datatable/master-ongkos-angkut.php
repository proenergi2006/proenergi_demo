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
	
	$p = new paging;
	$cek = "select * from pro_master_volume_angkut where is_active = 1";
	$row = $con->getResult($cek);
	$tmp = array();
	if(count($row) > 0){
		foreach($row as $res){
			array_push($tmp, array($res['id_master'], $res['volume_angkut']));
		}
	}
	$sql = "select a.id_wil_angkut, a.id_transportir, a.nama_transportir, a.lokasi_suplier, a.wilayah_angkut, a.nama_kab, a.nama_prov";
	foreach($tmp as $que){
		$sql .= ", coalesce(sum(a.".$que[1]."), 0) as '".$que[1]."'";
	}
	$sql .= " from (select a.id_prov_angkut,a.id_kab_angkut,a.id_wil_angkut, a.id_transportir, b.nama_transportir, b.lokasi_suplier, c.wilayah_angkut, e.nama_kab, d.nama_prov";
	foreach($tmp as $que){
		$sql .= ", case when a.id_vol_angkut = ".$que[0]." then a.ongkos_angkut end as '".$que[1]."'";
	}
	$sql .= " from pro_master_ongkos_angkut a join pro_master_transportir b on a.id_transportir = b.id_master join pro_master_wilayah_angkut c on a.id_wil_angkut = c.id_master 
			join pro_master_provinsi d on c.id_prov = d.id_prov join pro_master_kabupaten e on c.id_kab = e.id_kab) a where 1=1";
	if($q1 != "")
		$sql .= " and a.wilayah_angkut like '%".strtoupper($q1)."%'";
	if($q2 != "")
		$sql .= " and a.id_transportir =  '".$q2."'";
	if($q3 != "")
		$sql .= " and a.id_prov_angkut =  '".$q3."'";
	if($q4 != "")
		$sql .= " and a.id_kab_angkut =  '".$q4."'";
	
	$sql .= " group by a.id_wil_angkut, a.id_transportir";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.id_transportir limit ".$position.", ".$length;
	
	$count = 0;
	$linkExp = BASE_URL_CLIENT.'/report/master-ongkos-angkut-exp.php?'.paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$q4);
	$content = "";
	if($tot_record <= 0){
		$content .= '<tr><td colspan="100" style="text-align:center"><input type="hidden" id="uriExp" value="'.$linkExp.'" />Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkEdit	= BASE_URL_CLIENT.'/add-master-ongkos-angkut.php?'.paramEncrypt('idr='.$data['id_transportir'].'&idk='.$data['id_wil_angkut']);
			$linkHapus	= paramEncrypt("master_ongkos_angkut#|#".$data['id_transportir']."#|#".$data['id_wil_angkut']);
        	$tujuan 	= $data['wilayah_angkut']."<br>".str_replace(array("KOTA","KABUPATEN"), array("",""), $data['nama_kab'])." ".$data['nama_prov'];
			$content .= '
				<tr class="clickable-row" data-href="'.$linkEdit.'">
					<td class="text-center">'.$count.'</td>
					<td>
						<p style="margin-bottom:0px;">'.$data['nama_transportir'].'</p>
						<p style="margin-bottom:0px;">'.$data['lokasi_suplier'].'</p>
					</td>
					<td>'.strtoupper($tujuan).'</td>';

			foreach($tmp as $que){
				$content .= '<td class="text-right">'.number_format($data[$que['1']]).'</td>';
			}
        	if(!$q5){
				$content .= '
						<td class="text-center action">
							<a class="margin-sm btn btn-action btn-info" title="Edit" href="'.$linkEdit.'"><i class="fa fa-edit"></i></a>
							<a class="margin-sm delete btn btn-action btn-danger" title="Delete" data-param-idx="'.$linkHapus.'" data-action="deleteGrid">
							<i class="fa fa-trash"></i></a>
						</td>
					</tr>';
			}
		} 
		$content .= '<tr class="hide"><td colspan="100" style="text-align:center"><input type="hidden" id="uriExp" value="'.$linkExp.'" /></td></tr>';
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
