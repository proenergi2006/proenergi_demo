<style>
    table {
        font-size: 8.5pt;
    }

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
        <barcode code="<?php echo $barcod; ?>" type="QR" size="1" />
    </p>
    <!-- <p style="margin:0; text-align:right; font-size:6pt;"><?php echo $barcod; ?></p> -->
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
            <p style="margin:0 0 5px; text-align:center; font-size:14pt; font-family:times;"><b>Delivery Request</b></p>
            <p style="margin:0; text-align:center; font-size:11pt;">No : <?php echo $row[0]['nomor_pr']; ?></p>
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
    <div style="float:left; width:80px;">
        <p style="margin:0; font-size:8pt;">Tanggal</p>
    </div>
    <div style="float:left;">
        <p style="margin:0; font-size:8pt;">: <?php echo tgl_indo($row[0]['tanggal_pr']); ?></p>
    </div>
</div>
<div style="width:100%;">
    <div style="float:left; width:80px;">
        <p style="margin:0; font-size:8pt;">Cabang</p>
    </div>
    <div style="float:left;">
        <p style="margin:0; font-size:8pt;">: <?php echo $row[0]['nama_cabang']; ?></p>
    </div>
</div>
<div style="width:100%;">
    <div style="float:left; width:80px;">
        <p style="margin:0; font-size:8pt;">Jam Submit</p>
    </div>
    <div style="float:left;">
        <p style="margin:0; font-size:8pt;">:
            <?php
            if (!empty($row[0]['jam_submit'])) {
                echo date('H:i:s', strtotime($row[0]['jam_submit'])) . ' WIB';
            } else {
                echo '-';
            }
            ?>
        </p>
    </div>
</div>
<br>
<div style="clear:both"></div>

<table border="1" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:10px;">
    <tr>
        <th align="center" class="b1 b3 b4" rowspan="2" width="3%">No</th>
        <th align="center" class="b1 b3 b4" rowspan="2" width="6%">Nama Customer</th>
        <th align="center" class="b1 b3 b4" rowspan="2" width="5%">Area/ Alamat Kirim/ Wilayah OA</th>
        <th align="center" class="b1 b3 b4" rowspan="2" width="12%">PO Customer</th>
        <th align="center" class="b1 b3 b4" rowspan="2" width="8%">Catatan</th>
        <th align="center" class="b1 b3 b4" rowspan="2" width="8%">Angkutan</th>
        <th align="center" class="b1 b3 b4" width="10%" colspan="1">Quantity</th>
        <th align="center" class="b1 b3 b4" rowspan="2" width="6%">Suplier/ Depot</th>
        <th align="center" class="b1 b3 b4" rowspan="2" width="6%">Keterangan Lain</th>
    </tr>
    <tr>
        <th align="center" class="b1 b3 b4" width="6%">Volume (Liter)</th>
    </tr>
    <?php
    $nom = 0;
    $jum = 0;
    ?>
    <?php foreach ($res as $data) : ?>
        <?php
        $id_poc_sc[] = $data['id_poc'];

        $nom++;
        $idp     = $data['id_prd'];
        $idl     = $data['id_plan'];
        $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
        $alamat    = $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];

        $pbbkbT = ($data['nilai_pbbkb'] / 100) + 1.11;
        $oildus = $data['harga_poc'] / $pbbkbT * 0.003;
        $pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb'] / 100);
        $tmphrg = $data['refund_tawar'] + $oildus + $data['transport'] + $pbbkbN + $data['other_cost'];
        $nethrg = $data['harga_poc'] - $tmphrg;
        $volume = $data['volume'];

        $volori     = ($data['vol_ori']) ? $data['vol_ori'] : $data['volume'];
        $voloripr     = ($data['vol_ori_pr']) ? $data['vol_ori_pr'] : $data['volume'];

        $netgnl = ($nethrg - $data['harga_normal']) * $volume;
        $netprt = ($nethrg - $data['pr_harga_beli']) * $volume;
        $total1 = 0;
        $total2 = 0;
        $total3 = 0;
        $total4 = 0;
        $total1 = $total1 + $volume;
        $total2 = $total2 + $data['vol_ket'];
        $total3 = $total3 + $netprt;
        $total4 = $total4 + $netgnl;
        $checked = ($data['is_approved']) ? ' checked' : '';
        $flagEd = !$data['id_pod'] && !$data['id_dsd'] && !$data['id_dsk'];
        $class1 = "form-control input-po hitung toa";

        $tmn0     = $data['pr_terminal'];
        $tmn1     = ($data['nama_terminal']) ? $data['nama_terminal'] : '';
        $tmn2     = ($data['tanki_terminal']) ? '<br />' . $data['tanki_terminal'] : '';
        $tmn3     = ($data['lokasi_terminal']) ? ', ' . $data['lokasi_terminal'] : '';
        $depot     = $tmn1 . $tmn2 . $tmn3;

        $pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
        $lampPt = $data['lampiran_poc_ori'];
        if ($data['lampiran_poc'] && file_exists($pathPt)) {
            $linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $data['id_poc'] . "_&file=" . $lampPt);
            // $attach = '<a href="'.$linkPt.'"><i class="fa fa-paperclip" title="'.$lampPt.'"></i> PO Customer</a>';
        } else {
            $attach = '';
        }
        ?>

        <tr>
            <td class="text-center"><span class="noFormula"><?php echo $nom; ?></span></td>
            <td valign="top">
                <p style="margin-bottom:0px"><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'] : '------'); ?></p>
                <p style="margin-bottom:0px"><?php echo $data['nama_customer']; ?></b></p>
                <p style="margin-bottom:0px"><i><?php echo $data['fullname']; ?></i></p>
            </td>
            <td valign="top">
                <p style="margin-bottom:0px"><b><?php echo $data['nama_area']; ?></b></p>
                <p style="margin-bottom:0px"><?php echo $alamat; ?></p>
                <p style="margin-bottom:0px"><?php echo 'Wilayah OA : ' . $data['wilayah_angkut']; ?></p>
            </td>
            <td valign="top">
                <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc']; ?></b></p>
                <p style="margin-bottom:0px"><?php echo $data['merk_dagang']; ?></p>
                <p style="margin-bottom:0px"><?php echo 'Tgl Kirim ' . tgl_indo($data['tanggal_kirim']); ?></p>
                <p style="margin-bottom:0px"><?php echo $attach; ?></p>
            </td>
            <td valign="top"><?php echo $data['status_jadwal']; ?></td>
            <td valign="top">
                <?php
                $arrMobil = array(1 => "Truck", "Kapal", "Loco");
                echo $arrMobil[$data['pr_mobil']]
                ?>
            </td>
            <td valign="top" class="text-right"><?php echo number_format($data['volume']); ?></td>
            <td valign="top">
                <p style="margin-bottom:0px"><b><?php echo $data['nama_vendor']; ?></b></p>
                <p style="margin-bottom:0px"><?php echo $depot; ?></p>
            </td>
            <td class="text-left" valign="top">
                <p style="margin-bottom:0px"><b>NO DO Accurate : </b></p>
                <p style="margin-bottom:5px"><?php echo ($data['no_do_acurate'] ? $data['no_do_acurate'] : 'N/A'); ?></p>
                <p style="margin-bottom:0px"><b>Loading Order : </b></p>
                <p style="margin-bottom:0px"><?php echo ($data['nomor_lo_pr'] ? $data['nomor_lo_pr'] : 'N/A'); ?></p>
            </td>
        </tr>

    <?php endforeach ?>

</table>