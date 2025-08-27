<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();

$draw 	= isset($_POST["element"]) ? htmlspecialchars($_POST["element"], ENT_QUOTES) : 0;
$start 	= isset($_POST["start"]) ? htmlspecialchars($_POST["start"], ENT_QUOTES) : 0;
$length	= isset($_POST['length']) ? htmlspecialchars($_POST["length"], ENT_QUOTES) : 5;

$q1				= htmlspecialchars($_POST["q1"], ENT_QUOTES);
$id_jenis		= htmlspecialchars($_POST["q2"], ENT_QUOTES);
$id_terminal	= htmlspecialchars($_POST["q3"], ENT_QUOTES);
$id_produk		= htmlspecialchars($_POST["q4"], ENT_QUOTES);
$seswil 		= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

if ($id_jenis == '1') {
	$paging = new pagination_bootstrap;
	$sqlnya = "
			select a.nomor_po, a.tanggal_inven as tgl_po, 
			a.id_produk, b.jenis_produk, b.merk_dagang, 
			a.id_terminal, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal, 
			a.id_vendor, d.nama_vendor, 
			a1.id_po_supplier, a1.id_po_receive, a1.tgl_terima, a1.volume_terima, a2.id_master as id_invennya 
			from new_pro_inventory_vendor_po a 
			join new_pro_inventory_vendor_po_receive a1 on a.id_master = a1.id_po_supplier 
			join new_pro_inventory_depot a2 on a1.id_po_supplier = a2.id_po_supplier and a1.id_po_receive = a2.id_po_receive and a2.id_jenis = 21 
			join pro_master_produk b on a.id_produk = b.id_master 
			join pro_master_vendor d on a.id_vendor = d.id_master 
			join pro_master_terminal e on a.id_terminal = e.id_master 
			where a.id_produk = '" . $id_produk . "' and a.id_terminal = '" . $id_terminal . "' 
		";


	if ($q1 != "")
		$sqlnya .= " and (upper(a.nomor_po) like '%" . strtoupper($q1) . "%')";

	$tot_record = $con->num_rows($sqlnya);

	$config["total_rows"] 	= $tot_record;
	$config["per_page"] 	= $length;
	$config["getparams"] 	= array("page" => $start);
	$config["pageonly"] 	= true;

	$hasilnya 	= $paging->initialize($config);
	$position  	= $paging->get_offset();
	$infonya 	= $paging->create_info_bootstrap();
	//$linknya 	= $paging->create_links_bootstrap();

	$sqlnya .= " order by a1.tgl_terima desc, a1.id_po_receive desc limit " . $position . ", " . $length;

	$content = "";
	$count = 0;
	if ($tot_record <= 0) {
		$content .= '<tr><td colspan="6" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else {
		$count 		= $position;
		$result 	= $con->getResult($sqlnya);
		foreach ($result as $data) {
			$count++;
			$linkDetail	=
				$data['id_po_supplier'] . '|-|' . $data['id_po_receive'] . '|-|' . $data['nomor_po'] . '|-|' . date('d/m/Y', strtotime($data['tgl_po'])) . '|-|' .
				$data['nama_vendor'] . '|-|' . date('d/m/Y', strtotime($data['tgl_terima'])) . '|-|' . $data['volume_terima'] . '|-|' . $data['id_invennya'];

			$content .= '
					<tr>
						<td class="text-center">' . $count . '</td>
						<td class="text-left">
							<p style="margin-bottom:3px"><b>' . $data['nomor_po'] . '</b></p>
							<p style="margin-bottom:0px">Tanggal : ' . date('d/m/Y', strtotime($data['tgl_po'])) . '</p>
						</td>
						<td class="text-left">' . $data['nama_vendor'] . '</td>
						<td class="text-center">' . date('d/m/Y', strtotime($data['tgl_terima'])) . '</td>
						<td class="text-right">' . number_format($data['volume_terima']) . ' Liter</td>
						<td class="text-center">
							<button type="button" class="btn btn-sm btn-success btn-pilih" data-detail="' . $linkDetail . '" style="padding:3px 20px; font-weight:bold">Pilih</button>
						</td>
					</tr>';
		}
	}

	$json_data = array(
		"items"		=> $content,
		"totalData"	=> $tot_record,
		"infoData"	=> $infonya,
		"hasilnya" 	=> $hasilnya,
	);
	echo json_encode($json_data);
} else if ($id_jenis == '2') {
	$paging = new pagination_bootstrap;
	$sqlnya = "
			with tbl_realisasi as (
				select 
				a.id_po_supplier, a.id_po_receive, 
				c.nomor_po, c.tanggal_inven as tgl_po, c.id_vendor,  
				a.tgl_terima, a.volume_terima, 
				sum(in_inven) as in_inven, sum(out_inven) as out_inven, 
				sum(adj_inven) as adj_inven, sum(out_inven_virtual) as out_inven_virtual   
				from new_pro_inventory_depot b 
				join new_pro_inventory_vendor_po_receive a on a.id_po_supplier = b.id_po_supplier and a.id_po_receive = b.id_po_receive 
				join new_pro_inventory_vendor_po c on a.id_po_supplier = c.id_master 
				where b.id_produk = '" . $id_produk . "' and b.id_terminal = '" . $id_terminal . "' and a.is_aktif = 1 and b.id_po_supplier is not null 
				group by b.id_po_supplier, b.id_po_receive
			), tbl_utama as (
				select 
				a.nomor_po, a.tgl_po, a.id_vendor, b.nama_vendor, a.id_po_supplier, a.id_po_receive, a.tgl_terima, a.volume_terima, 
				((a.in_inven + a.adj_inven) - (a.out_inven + a.out_inven_virtual)) as sisa_inven  
				from tbl_realisasi a 
				join pro_master_vendor b on a.id_vendor = b.id_master 
			) 
			select a.* from tbl_utama a 
			where sisa_inven > 0
		";


	if ($q1 != "")
		$sqlnya .= " and (upper(a.nomor_po) like '%" . strtoupper($q1) . "%')";

	$tot_record = $con->num_rows($sqlnya);

	$config["total_rows"] 	= $tot_record;
	$config["per_page"] 	= $length;
	$config["getparams"] 	= array("page" => $start);
	$config["pageonly"] 	= true;

	$hasilnya 	= $paging->initialize($config);
	$position  	= $paging->get_offset();
	$infonya 	= $paging->create_info_bootstrap();
	//$linknya 	= $paging->create_links_bootstrap();

	$sqlnya .= " order by a.tgl_terima desc, a.id_po_receive desc limit " . $position . ", " . $length;

	$content = "";
	$count = 0;
	if ($tot_record <= 0) {
		$content .= '<tr><td colspan="6" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else {
		$count 		= $position;
		$result 	= $con->getResult($sqlnya);
		foreach ($result as $data) {
			$count++;

			$volume_sisa 	= ($data['volume_terima'] - $data['out_inven'] - $data['out_inven_virtual']);
			$linkDetail		=
				$data['nomor_po'] . '|-|' . $data['id_po_supplier'] . '|-|' . $data['id_po_receive'] . '|-|' . date('d/m/Y', strtotime($data['tgl_po'])) . '|-|' .
				date('d/m/Y', strtotime($data['tgl_terima'])) . '|-|' . $data['volume_terima'] . '|-|' . $data['sisa_inven'];

			$content .= '
					<tr>
						<td class="text-center">' . $count . '</td>
						<td class="text-left">
							<p style="margin-bottom:3px"><b>' . $data['nomor_po'] . '</b></p>
							<p style="margin-bottom:3px">Tanggal : ' . date('d/m/Y', strtotime($data['tgl_po'])) . '</p>
							<p style="margin-bottom:0px">' . $data['nama_vendor'] . '</p>
						</td>
						<td class="text-center">' . date('d/m/Y', strtotime($data['tgl_terima'])) . '</td>
						<td class="text-right">' . number_format($data['volume_terima']) . '</td>
						<td class="text-right">' . number_format($data['sisa_inven']) . '</td>
						<td class="text-center">
							<button type="button" class="btn btn-sm btn-success btn-pilih" data-detail="' . $linkDetail . '" style="padding:3px 20px; font-weight:bold">Pilih</button>
						</td>
					</tr>';
		}
	}

	$json_data = array(
		"items"		=> $content,
		"totalData"	=> $tot_record,
		"infoData"	=> $infonya,
		"hasilnya" 	=> $hasilnya,
	);
	echo json_encode($json_data);
} else if ($id_jenis == '3') {
	$paging = new pagination_bootstrap;
	$sqlnya = "
			with tbl_realisasi as (
				select 
				a.id_po_supplier, a.id_po_receive, 
				c.nomor_po, c.tanggal_inven as tgl_po, c.id_vendor,  
				a.tgl_terima, a.volume_terima, 
				sum(in_inven) as in_inven, sum(out_inven) as out_inven, 
				sum(adj_inven) as adj_inven, sum(out_inven_virtual) as out_inven_virtual   
				from new_pro_inventory_depot b 
				join new_pro_inventory_vendor_po_receive a on a.id_po_supplier = b.id_po_supplier and a.id_po_receive = b.id_po_receive 
				join new_pro_inventory_vendor_po c on a.id_po_supplier = c.id_master 
				where b.id_produk = '" . $id_produk . "' and b.id_terminal = '" . $id_terminal . "' and a.is_aktif = 1 and b.id_po_supplier is not null 
				group by b.id_po_supplier, b.id_po_receive

				UNION ALL

				select 
				a.id_po_supplier, a.id_po_receive, 
				a.nomor_blending_po as nomor_po, a.tanggal_blending as tgl_po, a.id_vendor_blending as id_vendor,  
				a.tanggal_blending as tgl_terima, a.volume_total as volume_terima, 
				sum(in_inven) as in_inven, sum(out_inven) as out_inven, 
				sum(adj_inven) as adj_inven, sum(out_inven_virtual) as out_inven_virtual   
				from new_pro_inventory_depot b 
				join pro_blending_po a on a.id_po_supplier = b.id_po_supplier and a.id_po_receive = b.id_po_receive 
				where b.id_produk = '" . $id_produk . "' and b.id_terminal = '" . $id_terminal . "'   and  b.id_po_supplier is not null 
				group by b.id_po_supplier, b.id_po_receive
				
			), tbl_utama as (
				select 
				a.nomor_po, a.tgl_po, a.id_vendor, b.nama_vendor, a.id_po_supplier, a.id_po_receive, a.tgl_terima, a.volume_terima, 
				((a.in_inven + a.adj_inven) - (a.out_inven + a.out_inven_virtual)) as sisa_inven  
				from tbl_realisasi a 
				join pro_master_vendor b on a.id_vendor = b.id_master 
			) 
			select a.* from tbl_utama a 
			where sisa_inven > 0
		";


	if ($q1 != "")
		$sqlnya .= " and (upper(a.nomor_po) like '%" . strtoupper($q1) . "%')";

	$tot_record = $con->num_rows($sqlnya);

	$config["total_rows"] 	= $tot_record;
	$config["per_page"] 	= $length;
	$config["getparams"] 	= array("page" => $start);
	$config["pageonly"] 	= true;

	$hasilnya 	= $paging->initialize($config);
	$position  	= $paging->get_offset();
	$infonya 	= $paging->create_info_bootstrap();
	//$linknya 	= $paging->create_links_bootstrap();

	$sqlnya .= " order by a.tgl_terima desc, a.id_po_receive desc limit " . $position . ", " . $length;

	$content = "";
	$count = 0;
	if ($tot_record <= 0) {
		$content .= '<tr><td colspan="6" style="text-align:center">Data tidak ditemukan </td></tr>';
	} else {
		$count 		= $position;
		$result 	= $con->getResult($sqlnya);
		foreach ($result as $data) {
			$count++;

			$volume_sisa 	= ($data['volume_terima'] - $data['out_inven'] - $data['out_inven_virtual']);
			$linkDetail		=
				$data['nomor_po'] . '|-|' . $data['id_po_supplier'] . '|-|' . $data['id_po_receive'] . '|-|' . date('d/m/Y', strtotime($data['tgl_po'])) . '|-|' .
				date('d/m/Y', strtotime($data['tgl_terima'])) . '|-|' . $data['volume_terima'] . '|-|' . $data['sisa_inven'];

			$content .= '
					<tr>
						<td class="text-center">' . $count . '</td>
						<td class="text-left">
							<p style="margin-bottom:3px"><b>' . $data['nomor_po'] . '</b></p>
							<p style="margin-bottom:3px">Tanggal : ' . date('d/m/Y', strtotime($data['tgl_po'])) . '</p>
							<p style="margin-bottom:0px">' . $data['nama_vendor'] . '</p>
						</td>
						<td class="text-center">' . date('d/m/Y', strtotime($data['tgl_terima'])) . '</td>
						<td class="text-right">' . number_format($data['volume_terima']) . '</td>
						<td class="text-right">' . number_format($data['sisa_inven']) . '</td>
						<td class="text-center">
							<button type="button" class="btn btn-sm btn-success btn-pilih" data-detail="' . $linkDetail . '" style="padding:3px 20px; font-weight:bold">Pilih</button>
						</td>
					</tr>';
		}
	}

	$json_data = array(
		"items"		=> $content,
		"totalData"	=> $tot_record,
		"infoData"	=> $infonya,
		"hasilnya" 	=> $hasilnya,
	);
	echo json_encode($json_data);
}
