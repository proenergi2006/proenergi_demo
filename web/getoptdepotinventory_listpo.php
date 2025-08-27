<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$conSub = new Connection();

$jenis 			= htmlspecialchars($_POST["jenis"], ENT_QUOTES);
$id_terminal 	= htmlspecialchars($_POST["id_terminal"], ENT_QUOTES);
$id_produk 		= htmlspecialchars($_POST["id_produk"], ENT_QUOTES);

if ($jenis == '1') {
	$sql01 = "
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
			) 
			select 
			a.nomor_po, a.tgl_po, a.id_vendor, b.nama_vendor, a.id_po_supplier, a.id_po_receive, a.tgl_terima, a.volume_terima, 
			((a.in_inven + a.adj_inven) - (a.out_inven + a.out_inven_virtual)) as sisa_inven  
			from tbl_realisasi a 
			join pro_master_vendor b on a.id_vendor = b.id_master 
			order by a.tgl_terima desc, a.id_po_receive desc 
		";
	$res01 = $conSub->getResult($sql01);

	if (count($res01) > 0) {
		$nomnya = 0;
		foreach ($res01 as $data01) {
			//$sisanya = $data01['volume_terima'] - $data01['sisa_inven'];
			$sisanya = $data01['sisa_inven'];
			if ($sisanya > 0) {
				$nomnya++;

				echo '	
					<tr>
						<td class="text-center"><span class="notabeltanksatuvendor" data-row-count="' . $nomnya . '"></span></td>
						<td class="text-left">
							<p style="margin-bottom:3px"><b>' . $data01['nomor_po'] . '</b></p>
							<p style="margin-bottom:3px">Tanggal : ' . date('d/m/Y', strtotime($data01['tgl_po'])) . '</p>
							<p style="margin-bottom:0px">' . $data01['nama_vendor'] . '</p>
						</td>
						<td class="text-center">' . date('d/m/Y', strtotime($data01['tgl_terima'])) . '</td>
						<td class="text-right">' . number_format($data01['volume_terima']) . '</td>
						<td class="text-right">' . number_format($data01['sisa_inven']) . '</td>
						<td class="text-left">
							<input type="text" id="tank_satu_vendor_nilai' . $nomnya . '" name="tank_satu_vendor_nilai[]" class="form-control input-sm text-right tank_satu_vendor_nilai" />
							<input type="hidden" id="id_po_supplier_tf' . $nomnya . '" name="id_po_supplier_tf[]" value="' . $data01['id_po_supplier'] . '" />
							<input type="hidden" id="id_po_receive_tf' . $nomnya . '" name="id_po_receive_tf[]" value="' . $data01['id_po_receive'] . '" />
						</td>
						<td class="text-center">
							<a class="btn btn-sm btn-danger hRow" style="padding:3px 10px;"><i class="fa fa-trash"></i></a>
						</td>
					</tr>';
			}
		}

		if ($nomnya == 0) {
			echo '<tr><td class="text-left" colspan="7" style="height:35px;">Tidak ada inventory pada Terminal / Depot ini</td></tr>';
		}
	} else {
		echo '<tr><td class="text-left" colspan="7" style="height:35px;">Tidak ada inventory pada Terminal / Depot ini</td></tr>';
	}

	$conSub->close();
}
