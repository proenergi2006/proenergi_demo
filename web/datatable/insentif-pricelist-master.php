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
	$sql = "
			select 
					a.id_master,
				 	TIER,
				 	DATE_FORMAT(a.TGL_AWAL, '%d %M %Y') TGL_AWAL,
				    DATE_FORMAT(a.TGL_AKHIR, '%d %M %Y') TGL_AKHIR,
				    a.HARGA_AWAL,
				    a.HARGA_AKHIR,
				    DATE_FORMAT(a.TGL_REKAM, '%d/%m/%Y') TGL_REKAM,
				    DATE_FORMAT(a.TGL_UBAH, '%d/%m/%Y') TGL_UBAH
				FROM pro_master_pl_insentif a
			where 
				1=1 
	";

	if($q1 != "")
		$sql .= " and (DATE_FORMAT(a.TGL_AWAL, '%d/%m/%Y') LIKE '%".$q1."%' 
					or DATE_FORMAT(a.TGL_AKHIR, '%d/%m/%Y') LIKE '%".$q1."%'
					or a.HARGA_AWAL LIKE '%".$q1."%'
					or a.HARGA_AKHIR LIKE '%".$q1."%')";

	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
	$sql .= " order by a.TGL_AWAL desc, TIER asc limit ".$position.", ".$length;
	
	$content = "";	
	$count = 0;
	if($tot_record ==  0){
		$content .= '<tr><td colspan="6" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else{
		$count 		= $position;
		$tot_page 	= ceil($tot_record/$length);
		$result 	= $con->getResult($sql);
		foreach($result as $i => $data){
			$count++;
			$linkView 	= BASE_URL_CLIENT.'/insentif-pricelist-master-edit.php?'.paramEncrypt('id='.$data['id_master']);
			$linkHapus	= paramEncrypt("master_insentif_pricelist#|#".$data['id_master']);
			$periode = $data['TGL_AWAL'].' - '.$data['TGL_AKHIR'];
			$price = 'Rp '.number_format($data['HARGA_AWAL']).' - Rp '.number_format($data['HARGA_AKHIR']);
			if ($data['HARGA_AWAL']==$data['HARGA_AKHIR'])
				$price = 'Rp '.number_format($data['HARGA_AWAL']);

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkView.'>
					<td class="text-center"></td>
					<td class="text-center">'.($i+1).'</td>
					<td class="text-center">'.$data['TIER'].'</td>
					<td class="text-center">'.$periode.'</td>
					<td class="text-center">'.$price.'</td>
					<td class="text-center">
						<a class="btn btn-action btn-warning" href="'.$linkView.'" style="margin-right:3px;"><i class="fa fa-edit"></i></a>
						<a class="margin-sm btn btn-action btn-danger " title="Delete" data-param-idx="'.$linkHapus.'" data-action="deleteGrid">
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
