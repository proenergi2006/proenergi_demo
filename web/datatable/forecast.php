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
	$seswil = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
	
	$p = new paging;
	$sql = "
	select * 
	from forecast a
	where 1 = 1 and id_wilayah = ".$seswil."";
	if($q1 != "" && $q2 != "")
		$sql .= " and month(a.tanggal) = '".$q1."' and year(a.tanggal) = '".$q2."'";
	if($q3 != "")
		$sql .= " and a.keterangan = '".$q3."'";
	if($q4 != "")
		$sql .= " and a.no_po = '".$q4."'";
	
	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$length);
	$page		= ($start > $tot_page)?$start-1:$start; 
	$position 	= $p->findPosition($length, $tot_record, $page);
 	$sql .= " order by created_at desc, tanggal desc limit " . $position . ", " . $length;

	$content = "";
	$count = 0;
	if ($tot_record <= 0) {
		$content .= '<tr><td colspan="11" style="text-align:center">Data tidak ditemukan</td></tr>';
	} else {
		$count 		= $position;
		$tot_page 	= ceil($tot_record / $length);
		$result 	= $con->getResult($sql);

		foreach ($result as $data) {
			$count++;
			$linkApprove = ACTION_CLIENT.'/forecast-approve.php?'.paramEncrypt('idr='.$data['id']);
			$linkEdit = BASE_URL_CLIENT.'/forecast-add.php?'.paramEncrypt('idr='.$data['id']);
			$linkHapus = paramEncrypt("forecast#|#".$data['id']);
			$linkPO = BASE_URL_CLIENT.'/vendor-po-add.php?'.paramEncrypt('nomor_po='.$data['no_po']);

        	$content .= '
				<tr class="clickable-row" data-href="'.$linkEdit.'">
					<td class="text-center">'.date("d/m/Y", strtotime($data['tanggal'])).'</td>
					<td>'.$data['keterangan'].'</td>
					<td>
						<p style="margin-bottom:0px;"><b>'.$data['no_po'].'</b></p>
					</td>
					<td>
						<p style="margin-bottom:0px;"><b>'.number_format($data['quantity']).'</b></p>
					</td>
					<td>
						<p style="margin-bottom:0px;"><b>'.$data['so_no'].'</b></p>
					</td>
					<td>
						<p style="margin-bottom:0px;"><b>'.$data['so_depo'].'</b></p>
					</td>
					<td>
						<p style="margin-bottom:0px;"><b>'.number_format($data['quantity_terima']).'</b></p>
					</td>
					<td>
						<p style="margin-bottom:0px;"><b>'.number_format($data['quantity_keluar']).'</b></p>
					</td>
					<td>
						<p style="margin-bottom:0px;"><b>'.$data['sisa'].'</b></p>
					</td>
					<td>
						<p style="margin-bottom:0px;"><b>'.($data['status'] == 1 ? 'Approved' : 'Pending').'</b></p>
					</td>
					<td class="text-center action">
			';
			if (in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']), array('5'))) {
				if (!$data['status'])
					$content .= '<a class="margin-sm btn btn-action btn-success" title="Approve" href="'.$linkApprove.'"><i class="fa fa-check"></i></a>';
			}

			if (in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']), array('5', '9')))
				$content .= '<a class="margin-sm btn btn-action btn-warning" title="Edit" href="'.$linkEdit.'"><i class="fa fa-edit"></i></a>';
			if ($data['status'] == 1)
				$content .= '<a class="margin-sm btn btn-action btn-info" title="Create PO Suplier" href="'.$linkPO.'"><i class="fa fa-plus-circle"></i></a>';

			$content .= '
						<a class="margin-sm delete btn btn-action btn-danger" title="Delete" data-param-idx="'.$linkHapus.'" data-action="deleteGrid">
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
