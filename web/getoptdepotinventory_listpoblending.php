<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$conSub = new Connection();

$jenis             = htmlspecialchars($_POST["jenis"], ENT_QUOTES);
$id_terminal     = htmlspecialchars($_POST["id_terminal"], ENT_QUOTES);
$id_produk = $_POST["id_produk"]; // Mengambil array id_produk

if (is_array($id_produk)) {
    // Menggunakan implode untuk menggabungkan array menjadi string
    $id_produk_imploded = implode(",", array_map('intval', $id_produk));
} else {
    // Jika hanya satu id_produk dikirim, gunakan langsung
    $id_produk_imploded = intval($id_produk);
}

if ($jenis == '1') {
    $sql01 = "
    WITH tbl_realisasi AS (
        SELECT 
            a.id_po_supplier, 
            a.id_po_receive, 
            c.nomor_po, 
            c.tanggal_inven AS tgl_po, 
            c.id_vendor, 
            c.id_produk,
            a.tgl_terima, 
            a.volume_terima, 
            c.harga_tebus,  -- Pastikan harga_tebus ditambahkan di sini
            SUM(in_inven) AS in_inven, 
            SUM(out_inven) AS out_inven, 
            SUM(adj_inven) AS adj_inven, 
            SUM(out_inven_virtual) AS out_inven_virtual   
        FROM new_pro_inventory_depot b 
        JOIN new_pro_inventory_vendor_po_receive a ON a.id_po_supplier = b.id_po_supplier AND a.id_po_receive = b.id_po_receive  
        JOIN new_pro_inventory_vendor_po c ON a.id_po_supplier = c.id_master 
        WHERE b.id_produk IN ($id_produk_imploded) 
            AND b.id_terminal = '" . $id_terminal . "' 
            AND a.is_aktif = 1 
            AND b.id_po_supplier IS NOT NULL 
        GROUP BY b.id_po_supplier, b.id_po_receive, c.harga_tebus  -- Jangan lupa tambahkan harga_tebus di GROUP BY
    ) 
    SELECT 
        a.nomor_po, 
        a.tgl_po, 
        a.id_vendor, 
        a.id_produk,
        b.nama_vendor, 
        a.id_po_supplier, 
        a.id_po_receive, 
        a.tgl_terima, 
        a.volume_terima,
        ((a.in_inven + a.adj_inven) - (a.out_inven + a.out_inven_virtual)) AS sisa_inven,
        a.harga_tebus  -- Ambil harga_tebus dari subquery
    FROM tbl_realisasi a 
    JOIN pro_master_vendor b ON a.id_vendor = b.id_master 
    ORDER BY a.tgl_terima DESC, a.id_po_receive DESC 
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
                         <td class="text-center"><input type="text" id="tank_satu_vendor_avg' . $nomnya . '" value="' . number_format($data01['harga_tebus']) . '" name="tank_satu_vendor_avg[]" class="form-control input-sm text-right tank_satu_vendor_avg" readonly/></td>
						<td class="text-right">' . number_format($data01['volume_terima']) . '</td>
						<td class="text-right">' . number_format($data01['sisa_inven']) . '</td>
						<td class="text-left">
							<input type="text" id="tank_satu_vendor_nilai' . $nomnya . '" value="' . number_format($data01['sisa_inven'], 2, '.', '') . '" name="tank_satu_vendor_nilai[]" class="form-control input-sm text-right tank_satu_vendor_nilai" readonly/>
							<input type="hidden" id="id_po_supplier_tf' . $nomnya . '" name="id_po_supplier_tf[]" value="' . $data01['id_po_supplier'] . '" />
							<input type="hidden" id="id_po_receive_tf' . $nomnya . '" name="id_po_receive_tf[]" value="' . $data01['id_po_receive'] . '" />
                            <input type="hidden" id="id_produk_tf' . $nomnya . '" name="id_produk_tf[]" value="' . $data01['id_produk'] . '" />
                             <input type="hidden" id="id_vendor_tf' . $nomnya . '" name="id_vendor_tf[]" value="' . $data01['id_vendor'] . '" />
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
