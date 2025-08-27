<style>
    .tabel_header td {
        padding: 1px 3px;
        font-size: 9pt;
        height: 18px;
    }

    .tabel_rincian th {
        padding: 5px 3px;
        background-color: #ffcc99;
    }

    .tabel_rincian td {
        padding: 3px 2px;
    }

    .td-ket,
    .td-subisi {
        padding: 1px 0px 2px;
        vertical-align: top;
    }

    .td-subisi {
        font-size: 5pt;
    }

    .td-ket {
        padding: 1px 0px;
        font-size: 8pt;
    }

    p {
        margin: 0 0 10px;
        text-align: justify;
    }

    .b1 {
        border-top: 1px solid #000;
    }

    .b2 {
        border-right: 1px solid #000;
    }

    .b3 {
        border-bottom: 1px solid #000;
    }

    .b4 {
        border-left: 1px solid #000;
    }

    .b1d {
        border-top: 2px solid #000;
    }

    .b2d {
        border-right: 2px solid #000;
    }

    .b3d {
        border-bottom: 2px solid #000;
    }

    .b4d {
        border-left: 2px solid #000;
    }
</style>
<htmlpagefooter name="myHTMLFooter1">
    <p style="margin:0; text-align:right;">
        <barcode code="<?php echo $barcod; ?>" type="C39" size="0.8" />
    </p>
    <p style="margin:0; text-align:right; font-size:6pt; padding-right:70px;"><?php echo $barcod; ?></p>
    <p style="margin:0; text-align:right; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
    <p style="margin:0; text-align:right; font-size:6pt;">Printed by <?php echo $printe; ?></p>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
<div style="width:100%;">
    <div style="width:33%; float:left;">
        <div style="padding:0;"><img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="30%" /></div>
    </div>
    <div style="width:33%; float:left;">
        <div style="padding:0;">
            <p style="margin:0 0 5px; text-align:center; font-size:14pt; font-family:times;"><b>Delivery Request Detail</b></p>
        </div>
    </div>
    <div style="width:33%; float:left;">
        <p style="margin:0; text-align:right;"><b>PT. Pro Energi</b></p>
        <p style="margin:0; text-align:right; font-size:8pt;">
            Gd. Graha Irama Lt.6 Unit G<br />Jl. HR. Rasuna Said Kuningan,<br /> Jakarta Selatan, 12710<br />Telp: (021)-52892321<br />Fax: (021)-52892310
        </p>
    </div>
</div>
<div style="clear:both"></div>
<div style="width:100%;">
    <table border="0" cellpadding="0" cellspacing="0" class="table-detail">
        <tr>
            <td width="70">Kode DR</td>
            <td width="10">:</td>
            <td><?php echo $row[0]['nomor_pr']; ?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td><?php echo tgl_indo($row[0]['tanggal_pr']); ?></td>
        </tr>
        <tr>
            <td>Cabang</td>
            <td>:</td>
            <td><?php echo $row[0]['nama_cabang']; ?></td>
        </tr>
    </table>
</div>
<div style="clear:both"></div>
<br />
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:10px; border-right: 1px solid #000;">
    <tr>
        <th align="center" class="b1 b3 b4" rowspan="2" width="50">No</th>
        <th align="center" class="b1 b3 b4" rowspan="2" width="200">Customer/ Bidang Usaha</th>
        <th align="center" class="b1 b3 b4" rowspan="2" width="230">Area/ Alamat Kirim/ Wilayah OA</th>
        <th align="center" class="b1 b3 b4" rowspan="2" width="190">PO Customer</th>
        <th align="center" class="b1 b3 b4" colspan="2">Quantity</th>
        <th align="center" class="b1 b3 b4" colspan="7">Harga (Rp/Liter)</th>
        <th align="center" class="b1 b3 b4" rowspan="2" width="70">TOP</th>
        <th align="center" class="b1 b3 b4" rowspan="2" width="80">Actual TOP</th>
        <!-- <th align="center" class="b1 b3 b4" colspan="4">Outstanding</th> -->
        <th align="center" class="b1 b3 b4" rowspan="2" width="100">Credit Limit</th>
    </tr>
    <tr>
        <th align="center" class="b1 b3 b4" width="65">Volume (Liter)</th>
        <th align="center" class="b1 b3 b4" width="80">Edit (Liter)</th>
        <th align="center" class="b1 b3 b4" width="75">Harga Jual (Gross)</th>
        <th align="center" class="b1 b3 b4" width="60">Ongkos Angkut</th>
        <th align="center" class="b1 b3 b4" width="60">Refund</th>
        <th align="center" class="b1 b3 b4" width="60">Oil Dues</th>
        <th align="center" class="b1 b3 b4" width="60">PBBKB</th>
        <th align="center" class="b1 b3 b4" width="60">Other Cost</th>
        <th align="center" class="b1 b3 b4" width="75">Harga Jual (Nett)</th>
        <!-- <th align="center" class="b1 b3 b4" width="90">AR (Not Yet)</th>
        <th align="center" class="b1 b3 b4" width="90">AR (1 - 30)</th>
        <th align="center" class="b1 b3 b4" width="90">AR (> 30)</th> -->
        <!-- <th align="center" class="b1 b3 b4" width="100">Total</th> -->
    </tr>
    <?php
    $fnr = $res[0]['sm_result'];
    if (count($res) == 0) {
        echo '<tr><td colspan="21" style="text-align:center">Data tidak ditemukan </td></tr>';
    } else {
        $nom = 0;
        foreach ($res as $data) {
            $nom++;
            $idp    = $data['id_prd'];
            $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
            $alamat = $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
            $kirim  = date("d/m/Y", strtotime($data['tanggal_kirim']));
            $tot_ar = ($data['pr_ar_notyet'] + $data['pr_ar_satu'] + $data['pr_ar_dua']);

            $pbbkbT = ($data['nilai_pbbkb'] / 100) + 1.11;
            $oildus = $data['harga_poc'] / $pbbkbT * 0.003;
            $pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb'] / 100);
            $tmphrg = $data['refund_tawar'] + $oildus + $ongkos_angkut + $pbbkbN + $data['other_cost'];
            $nethrg = $data['harga_poc'] - $tmphrg;

            $pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
            $lampPt = $data['lampiran_poc_ori'];
            if ($data['lampiran_poc'] && file_exists($pathPt)) {
                $linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $data['id_poc'] . "_&file=" . $lampPt);
                $attach = '<a href="' . $linkPt . '"><i class="fa fa-file-alt" title="' . $lampPt . '"></i> PO Customer</a>';
            } else {
                $attach = '';
            }
    ?>
            <tr>
                <td class="b3 b4" align="center"><?php echo $nom; ?></td>
                <td class="b3 b4" align="center">
                    <p style="margin-bottom:0px"><b><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'] . ' - ' : '') . $data['nama_customer']; ?></b></p>
                    <p style="margin-bottom:0px"><?php echo $data['jenis_usaha']; ?></p>
                    <p style="margin-bottom:0px"><i><?php echo $data['fullname']; ?></i></p>
                </td>
                <td class="b3 b4" align="center">
                    <p style="margin-bottom:0px"><b><?php echo $data['nama_area']; ?></b></p>
                    <p style="margin-bottom:0px"><?php echo $alamat; ?></p>
                    <p style="margin-bottom:0px"><?php echo 'Wilayah OA : ' . $data['wilayah_angkut']; ?></p>
                </td>
                <td class="b3 b4" align="center">
                    <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc']; ?></b></p>
                    <p style="margin-bottom:0px"><?php echo $data['merk_dagang']; ?></p>
                    <p style="margin-bottom:0px"><?php echo 'Tgl Kirim ' . tgl_indo($data['tanggal_kirim']); ?></p>
                    <p style="margin-bottom:0px"><?php echo $attach; ?></p>
                </td>
                <td class="b3 b4" align="right"><?php echo number_format($data['volume']); ?></td>
                <td class="b3 b4" align="right"><?php
                                                if (!$fnr) echo '<input type="text" name="ket[' . $idp . ']" id="ket' . $nom . '" class="form-control input-po hitung" />';
                                                else echo ($data['vol_ket']) ? number_format($data['vol_ket']) : '&nbsp;';
                                                ?></td>
                <td class="b3 b4" align="right"><?php echo number_format($data['harga_poc']); ?></td>
                <td class="b3 b4" align="right"><?php echo number_format($ongkos_angkut); ?></td>
                <td class="b3 b4" align="right"><?php echo number_format($data['refund_tawar']); ?></td>
                <td class="b3 b4" align="right"><?php echo number_format($oildus); ?></td>
                <td class="b3 b4" align="right"><?php echo number_format($pbbkbN); ?></td>
                <td class="b3 b4" align="right"><?php echo number_format($data['other_cost']); ?></td>
                <td class="b3 b4" align="right"><?php echo number_format($nethrg); ?></td>
                <td class="b3 b4" align="center"><?php echo $data['pr_top']; ?></td>
                <td class="b3 b4" align="center"><?php echo $data['pr_actual_top']; ?></td>
                <!-- <td class="b3 b4" align="right"><?php echo number_format($data['pr_ar_notyet']); ?></td>
            <td class="b3 b4" align="right"><?php echo number_format($data['pr_ar_satu']); ?></td>
            <td class="b3 b4" align="right"><?php echo number_format($data['pr_ar_dua']); ?></td> -->
                <!-- <td class="b3 b4" align="right"><?php echo number_format($tot_ar); ?></td> -->
                <td class="b3 b4" align="right"><?php echo number_format($data['pr_kredit_limit']); ?></td>
            </tr>
        <?php } ?>
    <?php } ?>
</table>
<br />
<p style="margin:0 0 20px; font-size:8pt;">Request By, </p>
<p style="margin:0; font-size:8pt;"><u><?php echo $res[0]['created_by']; ?></u><br />Admin</p>