<?php
$sql2 = "
		select a.*, b.nama_customer, b.id_wilayah, c.nomor_surat, c.perhitungan, c.harga_dasar, c.detail_formula, c.volume_tawar, c.jenis_payment, c.jangka_waktu, e.jenis_produk, e.merk_dagang, f.pembulatan
		from pro_po_customer a 
		join pro_customer b on a.id_customer = b.id_customer 
		join pro_penawaran c on a.id_penawaran = c.id_penawaran 
		join pro_master_produk e on a.produk_poc = e.id_master
        join pro_penawaran f on f.id_penawaran = a.id_penawaran
		where a.id_customer = '" . $row['id_customer'] . "' and a.id_poc = '" . $row['id_poc'] . "'
	";
$rsm = $con->getRecord($sql2);
$formula = json_decode($rsm['detail_formula'], true);

if ($rsm['perhitungan'] == 1) {
    if ($rsm['pembulatan'] == 0) {
        $harganya = number_format($rsm['harga_dasar'], 2);
        $harga_pocnya = number_format($rsm['harga_poc'], 2);
    } elseif ($rsm['pembulatan'] == 1) {
        $harganya = number_format($rsm['harga_dasar'], 0);
        $harga_pocnya = number_format($rsm['harga_poc'], 0);
    } elseif ($rsm['pembulatan'] == 2) {
        $harganya = number_format($rsm['harga_dasar'], 4);
        $harga_pocnya = number_format($rsm['harga_poc'], 4);
    }
    $nilainya = $rsm['harga_dasar'];
} else {
    $harganya = '';
    $nilainya = '';
    foreach ($formula as $jenis) {
        $harganya .= '<p style="margin-bottom:0px">' . $jenis . '</p>';
    }
}
$pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $rsm['lampiran_poc'];
$lampPt = $rsm['lampiran_poc_ori'];

// $arr_payment = array("COD" => "COD (Cash On Delivery)", "CBD" => "CBD (Cash Before Delivery)");
?>

<div class="row">
    <div class="col-sm-12">
        <div>
            <div class="box-header with-border">
                <h3 class="box-title">Data PO Customer</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="3"><?php echo "Kode Dokumen PO-" . str_pad($rsm['id_poc'], 4, '0', STR_PAD_LEFT); ?></th>
                            </tr>
                        </thead>
                        <tr>
                            <td width="180">Nama Customer</td>
                            <td width="10">:</td>
                            <td><?php echo $rsm['nama_customer']; ?></td>
                        </tr>
                        <tr>
                            <td>TOP Customer</td>
                            <td>:</td>
                            <td>
                                <!-- <?php echo (is_numeric($rsm['top_poc'])) ? $rsm['top_poc'] . " Hari" : $arr_payment[$rsm['top_poc']]; ?> -->
                                <?php echo $rsm['jenis_payment'] == 'CREDIT' ? $rsm['jangka_waktu'] . ' Hari' : $rsm['jenis_payment'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Penawaran</td>
                            <td>:</td>
                            <td><?php echo $rsm['nomor_surat']; ?></td>
                        </tr>
                        <tr>
                            <td>Volume Penawaran</td>
                            <td>:</td>
                            <td><?php echo number_format($rsm['volume_tawar']) . ' Liter'; ?></td>
                        </tr>
                        <tr>
                            <td>Harga Penawaran</td>
                            <td>:</td>
                            <td><?php echo $harganya; ?></td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Nomor PO</td>
                            <td>:</td>
                            <td><?php echo $rsm['nomor_poc']; ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal PO</td>
                            <td>:</td>
                            <td><?php echo tgl_indo($rsm['tanggal_poc']); ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal Pengiriman</td>
                            <td>:</td>
                            <td><?php echo tgl_indo($rsm['supply_date']); ?></td>
                        </tr>
                        <tr>
                            <td>Produk</td>
                            <td>:</td>
                            <td><?php echo $rsm['jenis_produk'] . " - " . $rsm['merk_dagang']; ?></td>
                        </tr>
                        <tr>
                            <td>Harga/Liter</td>
                            <td>:</td>
                            <td><?php echo $harga_pocnya; ?></td>
                        </tr>
                        <tr>
                            <td>Jumlah Volume</td>
                            <td>:</td>
                            <td><?php echo number_format($rsm['volume_poc']) . " Liter"; ?></td>
                        </tr>
                        <tr>
                            <td>Total Order</td>
                            <td>:</td>
                            <td><?php echo number_format(($rsm['volume_poc'] * $rsm['harga_poc'])); ?></td>
                        </tr>
                        <tr>
                            <td>Lampiran</td>
                            <td>:</td>
                            <td>
                                <?php
                                if ($rsm['lampiran_poc'] && file_exists($pathPt)) {
                                    $linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $row['id_poc'] . "_&file=" . $lampPt);
                                    echo '<a href="' . $linkPt . '" target="_blank"><i class="fa fa-file-alt jarak-kanan"></i>' . $lampPt . '</a>';
                                } else echo '-';
                                ?></td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>