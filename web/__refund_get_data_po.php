<?php
$sql2 = "
		select a.*, b.nama_customer, b.id_wilayah, c.nomor_surat, c.perhitungan, c.harga_dasar, c.detail_formula, c.volume_tawar, c.jenis_payment, c.jangka_waktu, e.jenis_produk, e.merk_dagang 
		from pro_po_customer a 
		join pro_customer b on a.id_customer = b.id_customer 
		join pro_penawaran c on a.id_penawaran = c.id_penawaran 
		join pro_master_produk e on a.produk_poc = e.id_master 
		where a.id_customer = '" . $result['id_customernya'] . "' and a.id_poc = '" . $result['id_pocnya'] . "'
	";
$rsm = $con->getRecord($sql2);
$formula = json_decode($rsm['detail_formula'], true);

if ($rsm['perhitungan'] == 1) {
    $harganya = number_format($rsm['harga_dasar']);
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

$sql_penerima_refund = "SELECT a.*, b.* FROM pro_poc_penerima_refund a JOIN pro_master_penerima_refund b ON a.penerima_refund=b.id WHERE a.id_poc = '" . $rsm['id_poc'] . "'";
$penerima_refund = $con->getResult($sql_penerima_refund);
// $arr_payment = array("COD" => "COD (Cash On Delivery)", "CBD" => "CBD (Cash Before Delivery)");
?>
<style>
    .thumbnail {
        max-width: 300px;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .thumbnail:hover {
        transform: scale(1.05);
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 5000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.8);
    }

    .modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
    }
</style>

<div id="modal" class="modal">
    <span class="close" id="closeModal">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

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
                            <td><?php echo number_format($rsm['harga_poc']); ?></td>
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
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center" colspan="6">Penerima Refund</th>
                            </tr>
                        </thead>
                        <tr class="text-center">
                            <td width="15%">Nama Penerima</td>
                            <td>Foto</td>
                            <td>Divisi</td>
                            <td>Bank</td>
                            <td>No Rekening</td>
                            <td>Status</td>
                        </tr>
                        <tbody>
                            <?php if (count($penerima_refund) > 0) : ?>
                                <?php foreach ($penerima_refund as $key) : ?>
                                    <?php
                                    $encrypt_url_ktp = paramEncrypt($key['foto_ktp']);
                                    $encrypt_url_npwp = paramEncrypt($key['foto_npwp']);
                                    ?>
                                    <tr>
                                        <td class="text-center">
                                            <?= ucwords($key['nama']) ?>
                                        </td>
                                        <td class="text-center">
                                            KTP : <a href="<?php echo ACTION_CLIENT . '/view_file.php?url=' . $encrypt_url_ktp . '&tipe=ktp'; ?>" target="_blank">Preview File</a>
                                            <br>
                                            <br>
                                            <br>
                                            <?php if ($key['foto_npwp'] == NULL) : ?>
                                                NPWP : Tidak ada foto
                                            <?php else: ?>
                                                NPWP : <a href="<?php echo ACTION_CLIENT . '/view_file.php?url=' . $encrypt_url_npwp . '&tipe=npwp'; ?>" target="_blank">Preview File</a>
                                            <?php endif ?>
                                        </td>
                                        <td class="text-center">
                                            <?= ucwords($key['divisi']) ?>
                                        </td>
                                        <td class="text-center">
                                            <span><?= $key['kode_bank'] . " - " . ucwords($key['bank']) . " a/n " . ucwords($key['atas_nama']) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?= $key['no_rekening'] ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($key['is_bm'] == 0) : ?>
                                                <span style="color: red;">Belum di approve BM</span>
                                            <?php else : ?>
                                                <?php if ($key['is_ceo'] == 0) : ?>
                                                    <strong>
                                                        <span>Approved by <?= $key['bm_by'] ?></span><br>
                                                        <span><?= tgl_indo($key['bm_date']) . " " . date("H:i", strtotime($key['bm_date'])) ?></span>
                                                        <hr>
                                                        <span style="color: red;">Belum di approve CEO</span>
                                                    </strong>
                                                <?php else : ?>
                                                    <strong>
                                                        <span>Approved by <?= $key['bm_by'] ?></span><br>
                                                        <span><?= tgl_indo($key['bm_date']) . " " . date("H:i", strtotime($key['bm_date'])) ?></span>
                                                        <hr>
                                                        <span>Approved by <?= $key['ceo_by'] ?></span><br>
                                                        <span><?= tgl_indo($key['ceo_date']) . " " . date("H:i", strtotime($key['ceo_date'])) ?></span>
                                                    </strong>
                                                <?php endif ?>
                                            <?php endif ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php else : ?>
                                <tr>
                                    <td class="text-center" colspan="5">
                                        Data tidak ditemukan
                                    </td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('.myImage').click(function() {
        $('#modal').css('display', 'block');
        $('#modalImage').attr('src', $(this).attr('src'));
    });

    $('#closeModal').click(function() {
        $('#modal').css('display', 'none');
    });

    $(window).click(function(event) {
        if ($(event.target).is('#modal')) {
            $('#modal').css('display', 'none');
        }
    });
</script>