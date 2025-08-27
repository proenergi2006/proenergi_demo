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
	$q3	= isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):'';
	$q4	= isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
	
	$p = new paging;
	$cek = "select * from pro_master_pbbkb where id_master = 1";
	$row = $con->getResult($cek);
	$tmp = array();
	if(count($row) > 0){
		foreach($row as $res){
			array_push($tmp, array($res['id_master'], $res['nilai_pbbkb']));
		}
	}
	$sql = "select a.periode_awal, a.periode_akhir, a.id_area, a.produk, a.is_approved, a.is_evaluated, a.nama_area, a.jenis_produk, a.merk_dagang";
	foreach($tmp as $que){
		$sql .= ", coalesce(sum(a.nm".$que[0]."), 0) as 'nm".$que[0]."'";
		$sql .= ", coalesce(sum(a.sm".$que[0]."), 0) as 'sm".$que[0]."'";
		$sql .= ", coalesce(sum(a.om".$que[0]."), 0) as 'om".$que[0]."'";
	}
	$sql .= " from (select a.periode_awal, a.periode_akhir, a.id_area, a.produk, a.is_approved, a.is_evaluated, b.nama_area, c.jenis_produk, c.merk_dagang";
	foreach($tmp as $que){
		$sql .= ", case when a.pajak = ".$que[0]." then a.harga_normal end as 'nm".$que[0]."'";
		$sql .= ", case when a.pajak = ".$que[0]." then a.harga_sm end as 'sm".$que[0]."'";
		$sql .= ", case when a.pajak = ".$que[0]." then a.harga_om end as 'om".$que[0]."'";
	}
	$sql .= " from pro_master_harga_minyak a join pro_master_area b on a.id_area = b.id_master join pro_master_produk c on a.produk = c.id_master) a where a.is_evaluated = 1 and a.is_approved = 1";
	if($q1 != "")
		$sql .= " and a.periode_awal = '".tgl_db($q1)."'";
	if($q2 != "")
		$sql .= " and a.id_area = '".$q2."'";
	if($q4 != "")
		$sql .= " and a.produk = '".$q4."'";
	$sql .= " group by a.periode_awal, a.periode_akhir, a.id_area, a.produk, a.is_approved, a.is_evaluated";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.periode_awal desc, a.id_area, a.produk limit ".$position.", ".$length;

	$content = "";
	if($tot_record <= 0){
		$content .= '';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $data){
			$count++;
			$linkParam 	= paramEncrypt('idr='.$data['periode_awal'].'#*#'.$data['periode_akhir'].'#*#'.$data['id_area'].'#*#'.$data['produk']);
			$linkDetail	= BASE_URL_CLIENT.'/detil-master-harga-minyak.php?'.$linkParam;
			$linkEdit	= BASE_URL_CLIENT.'/add-master-harga-minyak.php?'.$linkParam;
			$linkHapus	= paramEncrypt("master_harga_minyak#|#".$data['periode_awal']."#|#".$data['periode_akhir']."#|#".$data['id_area']."#|#".$data['produk']);

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkDetail.'">
					<td class="text-center">'.date("d/m/Y", strtotime($data['periode_awal'])).' - '.date("d/m/Y", strtotime($data['periode_akhir'])).'</td>
					<td>'.$data['nama_area'].'</td>
					<td>'.$data['jenis_produk'].' - '.$data['merk_dagang'].'</td>';
			foreach($tmp as $que){
				$content .= '<td class="text-right">'.number_format($data['nm'.$que[0]],0,'','.').'</td>';
			}
			$content .= '
					<td class="text-center action">
						<a class="margin-sm btn btn-action btn-info" title="Detail" href="'.$linkDetail.'"><i class="fa fa-info-circle"></i></a>
						<a class="margin-sm btn btn-action btn-info" title="Edit" href="'.$linkEdit.'"><i class="fa fa-edit"></i></a>
						<a class="margin-sm btn btn-action btn-danger" data-param-idx="'.$linkHapus.'" data-action="deleteGrid">
						<i class="fa fa-trash"></i></a>
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
