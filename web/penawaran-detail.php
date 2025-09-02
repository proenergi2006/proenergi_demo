<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$enk      = decode($_SERVER['REQUEST_URI']);
$con     = new Connection();
$flash    = new FlashAlerts;
$idr     = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$idk     = htmlspecialchars($enk["idk"], ENT_QUOTES);
$sess_wil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

$sql = "SELECT a.*, b.nama_customer, b.top_payment, b.status_customer, c.fullname, d.nama_cabang, e.jenis_produk, e.merk_dagang, f.id_poc, g.harga_sm, g.harga_om, a.pembulatan, h.nama_area, i.nilai_pbbkb
from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join acl_user c on b.id_marketing = c.id_user 
join pro_master_cabang d on a.id_cabang = d.id_master join pro_master_produk e on a.produk_tawar = e.id_master 
join pro_master_area h on a.id_area = h.id_master left join pro_po_customer f on a.id_penawaran = f.id_penawaran 
left join pro_master_harga_minyak g on a.masa_awal = g.periode_awal and a.masa_akhir = g.periode_akhir and a.id_area = g.id_area and a.pbbkb_tawar = g.pajak 
    and g.is_approved = 1
left join pro_master_pbbkb i on a.pbbkb_tawar = i.id_master
where a.id_customer = '" . $idr . "' and a.id_penawaran = '" . $idk . "'";

$rsm = $con->getRecord($sql);

$sqlOtherCost = "select keterangan, nominal 
                 FROM pro_other_cost_detail 
                 WHERE id_penawaran = '" . $idk . "'";
$rsmOtherCost = $con->getResult($sqlOtherCost);

$rincian = json_decode($rsm['detail_rincian'], true);
$formula = json_decode($rsm['detail_formula'], true);
$link1     = BASE_URL_CLIENT . '/penawaran.php';
$link2     = BASE_URL_CLIENT . '/penawaran-add.php?' . paramEncrypt('idr=' . $idr . '&idk=' . $idk);
$link3     = ACTION_CLIENT . '/penawaran-izin.php?' . paramEncrypt('idr=' . $idr . '&idk=' . $idk);
$link4     = ACTION_CLIENT . '/penawaran-cetak.php?' . paramEncrypt('idr=' . $idr . '&idk=' . $idk . '&bhs=ind');
$link4_2 = ACTION_CLIENT . '/penawaran-cetak.php?' . paramEncrypt('idr=' . $idr . '&idk=' . $idk . '&bhs=eng');
$link5     = BASE_URL_CLIENT . '/penawaran-preview.php?' . paramEncrypt('idr=' . $idr . '&idk=' . $idk);
$link5_2 = BASE_URL_CLIENT . '/penawaran-preview-eng.php?' . paramEncrypt('idr=' . $idr . '&idk=' . $idk);

$arrPosisi    = array(1 => "SPV", "BM", "BM", "OM", "COO", "CEO");
$arrAlasan    = array(1 => "spv_mkt_summary", "sm_mkt_summary", "sm_wil_summary", "om_summary", "coo_summary", "ceo_summary");
$arrSetuju    = array(1 => "Disetujui", "Ditolak");

$tmp_calc     = (json_decode($rsm['kalkulasi_oa'], true) === NULL) ? array(1) : json_decode($rsm['kalkulasi_oa'], true);
$calcoa1     = ($tmp_calc[0]['transportir'] ? $tmp_calc[0]['transportir'] : '');
$calcoa2     = ($tmp_calc[0]['wiloa_po'] ? $tmp_calc[0]['wiloa_po'] : '');
$calcoa3     = ($tmp_calc[0]['voloa_po'] ? $tmp_calc[0]['voloa_po'] : 'N/A');
$calcoa4     = ($tmp_calc[0]['ongoa_po'] ? $tmp_calc[0]['ongoa_po'] : 'N/A');

if ($rsm['pembulatan'] == 0) {
    $harganya = number_format($rsm['harga_dasar'], 2);
} elseif ($rsm['pembulatan'] == 1) {
    $harganya = number_format($rsm['harga_dasar']);
} elseif ($rsm['pembulatan'] == 2) {
    $harganya = number_format($rsm['harga_dasar'], 4);
}

$sqlnya1 = "select id_master, concat(nama_transportir, ' - ',  lokasi_suplier, ' (', nama_suplier, ')') as namanya from pro_master_transportir where id_master = '" . $calcoa1 . "'";
$sqlnya2 = "
	select a.id_master, upper(concat(a.wilayah_angkut,' ',c.nama_kab,' ',b.nama_prov)) as namanya 
	from pro_master_wilayah_angkut a 
	join pro_master_provinsi b on a.id_prov = b.id_prov 
	join pro_master_kabupaten c on a.id_kab = c.id_kab 
	where a.id_master = '" . $calcoa2 . "'";

$calcoa1 = ($calcoa1 ? $con->getRecord($sqlnya1) : array("namanya" => "N/A"));
$calcoa2 = ($calcoa2 ? $con->getRecord($sqlnya2) : array("namanya" => "N/A"));

if ($rsm['flag_approval'] == 0 && $rsm['flag_disposisi'] == 0) {
    $status = "Terdaftar";
} else if ($rsm['flag_approval'] == 0 && $rsm['flag_disposisi']) {
    if ($rsm['flag_disposisi'] > 1 && $rsm['flag_disposisi'] < 4) {
        $status = "Verifikasi " . $arrPosisi[$rsm['flag_disposisi']] . " " . $rsm['nama_cabang'];
    } else {
        $status = "Verifikasi " . $arrPosisi[$rsm['flag_disposisi']];
    }
} else if ($rsm['flag_approval']) {
    $picApproval = "";
    if ($rsm['flag_disposisi'] == '3') $picApproval = $rsm['sm_wil_pic'];
    else if ($rsm['flag_disposisi'] == '4') $picApproval = $rsm['om_pic'];
    else if ($rsm['flag_disposisi'] == '5') $picApproval = $rsm['coo_pic'];
    else if ($rsm['flag_disposisi'] == '6') $picApproval = $rsm['ceo_pic'];

    $alasanDitolak = "";
    if ($rsm['flag_approval'] == '2') {
        $alasanDitolak = "<br /><br /><b><u>Alasan Penolakan</u></b>";
        $alasanDitolak .= "<br />" . ($rsm[$arrAlasan[$rsm['flag_disposisi']]] ? nl2br($rsm[$arrAlasan[$rsm['flag_disposisi']]]) : "-") . "<br />";
    }

    if ($rsm['flag_disposisi'] > 1 && $rsm['flag_disposisi'] < 4) {
        $status = $arrSetuju[$rsm['flag_approval']] . " " . $arrPosisi[$rsm['flag_disposisi']] . " " . $rsm['nama_cabang'];
        $status .= $alasanDitolak;
        $status .= "<br /><i>" . ($picApproval ? $picApproval . " - " : "");
        $status .= ($rsm['tgl_approval'] ? date("d/m/Y H:i:s", strtotime($rsm['tgl_approval'])) . " WIB" : "") . "</i>";
    } else {
        $status = $arrSetuju[$rsm['flag_approval']] . " " . $arrPosisi[$rsm['flag_disposisi']];
        $status .= $alasanDitolak;
        $status .= "<br /><i>" . ($picApproval ? $picApproval . " - " : "");
        $status .= ($rsm['tgl_approval'] ? date("d/m/Y H:i:s", strtotime($rsm['tgl_approval'])) . " WIB" : "") . "</i>";
    }
}

if ($rsm['flag_approval'] && $rsm['flag_approval'] == 2) {
    if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == '11') {
        $sql1 = "update pro_penawaran set view = 'Yes' where id_penawaran = '" . $idk . "'";
        $con->setQuery($sql1);
    }
}

$arrKondInd    = array(0 => '', 1 => "Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
$arrKondEng = array(0 => '', 1 => "After Invoice Receive", "After Delivery", "After Loading");
$jenis_net    = $rsm['jenis_net'];
$arrPayment = array("CREDIT" => "CREDIT " . $rsm['jangka_waktu'] . " hari " . $arrKondInd[$jenis_net], "CBD" => "CBD (Cash Before Delivery)", "COD" => "COD (Cash On Delivery)");

if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == '11') {
    $nama_role = "Marketing";
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == '17') {
    $nama_role = "Key Account Executive";
} else {
    $nama_role = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Detil Penawaran</h1>
            </section>
            <section class="content">

                <?php if ($enk['idr'] !== '' && isset($enk['idr'])) { ?>
                    <?php $flash->display(); ?>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#penawaran-detil" aria-controls="penawaran-detil" role="tab" data-toggle="tab">Penawaran</a>
                        </li>
                        <li role="presentation" class="">
                            <a href="#history-data-approval" aria-controls="history-data-approval" role="tab" data-toggle="tab">History Approval Penawaran</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="penawaran-detil">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="box box-primary">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Data Penawaran</h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="table-responsive">
                                                <table class="table no-border">
                                                    <tr>
                                                        <td colspan="3"><u><b>Data Customer</b></u></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="180">Company Name</td>
                                                        <td width="10" class="text-center">:</td>
                                                        <td><?php echo $rsm['nama_customer']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Cabang Invoice</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $rsm['nama_cabang']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Marketing</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $rsm['fullname']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>PIC Customer</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $rsm['gelar'] . ' ' . $rsm['nama_up'];
                                                            echo ($rsm['jabatan_up']) ? " (<i>" . $rsm['jabatan_up'] . "</i>)" : ""; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Alamat Korespondensi</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $rsm['alamat_up']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Telepon</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $rsm['telp_up']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Fax</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $rsm['fax_up']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>TOP Customer</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $arrPayment[$rsm['jenis_payment']]; ?></td>
                                                    </tr>
                                                </table>

                                                <hr style="margin:0px 0px 10px; color:#ccc;" />
                                                <table class="table no-border">
                                                    <tr>
                                                        <td colspan="3"><u><b>Kalkulasi OA</b></u></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="180">Transportir</td>
                                                        <td width="10" class="text-center">:</td>
                                                        <td><?php echo $calcoa1['namanya']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Wilayah</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $calcoa2['namanya']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kapasitas Tanki</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $calcoa3; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Ongkos Angkut</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $calcoa4; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Rekomendasi OA</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $rsm['oa_kirim']; ?></td>
                                                    </tr>
                                                </table>

                                                <hr style="margin:0px 0px 10px; color:#ccc;" />
                                                <table class="table no-border">
                                                    <tr>
                                                        <td colspan="3"><u><b>Detail Penawaran</b></u></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="180">Nomor Referensi</td>
                                                        <td width="10" class="text-center">:</td>
                                                        <td><?php echo $rsm['nomor_surat']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Area</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $rsm['nama_area']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Masa berlaku harga</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo tgl_indo($rsm['masa_awal']) . " - " . tgl_indo($rsm["masa_akhir"]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Produk</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $rsm['merk_dagang']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>PBBKB</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $rsm['nilai_pbbkb'] . " %"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Order Method</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $rsm['method_order'] . " hari sebelum pickup"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Toleransi Penyusutan</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $rsm['tol_susut'] . " %"; ?></td>
                                                    </tr>
                                                    <?php if ($rsm['alat_ukur'] != NULL) : ?>
                                                        <tr>
                                                            <td>Alat Ukur</td>
                                                            <td class="text-center">:</td>
                                                            <td><?php echo $rsm['alat_ukur'] ?></td>
                                                        </tr>
                                                    <?php endif ?>
                                                    <tr>
                                                        <td>Lokasi Pengiriman</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo $rsm['lok_kirim']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Refund</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo ($rsm['refund_tawar']) ? number_format($rsm['refund_tawar']) : '-'; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Other Cost</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo number_format($rsm['other_cost']); ?></td>
                                                    </tr>
                                                    <?php if (!empty($rsmOtherCost) && is_array($rsmOtherCost)) : ?>
                                                        <tr>
                                                            <td>Keterangan Other Cost</td>
                                                            <td class="text-center">:</td>
                                                            <td>
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Keterangan</th>
                                                                            <th>Nominal</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($rsmOtherCost as $detail) : ?>
                                                                            <tr>
                                                                                <td><?php echo htmlspecialchars($detail['keterangan'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                                                <td><?php echo number_format($detail['nominal']); ?></td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    <?php else : ?>
                                                        <tr>
                                                            <td>Keterangan Other Cost</td>
                                                            <td class="text-center">:</td>
                                                            <td>Tidak ada data Other Cost.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    <tr>
                                                        <td>Volume Order</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo number_format($rsm['volume_tawar']) . " Liter"; ?></td>
                                                    </tr>
                                                    <?php if ($rsm['perhitungan'] == 1) { ?>
                                                        <tr>
                                                            <td>Harga perliter</td>
                                                            <td class="text-center">:</td>
                                                            <td><?php echo $harganya; ?></td>
                                                        </tr>
                                                    <?php } ?>

                                                    <tr>
                                                        <td>Keterangan Harga</td>
                                                        <td class="text-center">:</td>
                                                        <td><?php echo ($rsm['ket_harga']) ? $rsm['ket_harga'] : '-'; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;</td>
                                                        <td class="text-center">&nbsp;</td>
                                                        <td><?php echo ($rsm['gabung_oa'] ? 'Cetakan Harga Dasar Termasuk Ongkos Angkut' : 'Cetakan Harga Dasar Tidak Termasuk Ongkos Angkut'); ?></td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <?php
                                            $breakdown = false;
                                            foreach ($rincian as $temp) {
                                                $breakdown = $breakdown || 1;
                                            }
                                            if ($breakdown && $rsm['perhitungan'] == 1) {
                                                $nom = 0;
                                            ?>
                                                <p style="margin:0px 5px 5px;">Dengan rincian sebagai berikut:</p>

                                                <div class="clearfix">
                                                    <div class="col-sm-10 col-md-8">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <th class="text-center" width="10%">NO</th>
                                                                    <th class="text-center" width="40%">RINCIAN</th>
                                                                    <th class="text-center" width="10%">NILAI</th>
                                                                    <th class="text-center" width="40%">HARGA</th>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    foreach ($rincian as $arr1) {
                                                                        $nom++;
                                                                        $cetak = 1;
                                                                        $nilai = $arr1['nilai'];
                                                                        $biaya = ($arr1['biaya']) ? $arr1['biaya'] : '';
                                                                        if ($rsm['pembulatan'] == 1) {
                                                                            $biaya = number_format($arr1['biaya']);
                                                                        } elseif ($rsm['pembulatan'] == 0) {
                                                                            $biaya = number_format($arr1['biaya'], 2);
                                                                        } else {
                                                                            $biaya = number_format($arr1['biaya'], 4);
                                                                        }
                                                                        $jenis = $arr1['rincian'];
                                                                        if ($cetak) {
                                                                    ?>
                                                                            <tr>
                                                                                <td class="text-center"><?php echo $nom; ?></td>
                                                                                <td class="text-left"><?php echo $jenis; ?></td>
                                                                                <td class="text-right"><?php echo ($nilai ? $nilai . " %" : ""); ?></td>
                                                                                <td class="text-right"><?php echo $biaya; ?></td>
                                                                            </tr>
                                                                    <?php }
                                                                    } ?>
                                                                </tbody>
                                                            </table>
                                                            <?php if ($rsm['pembulatan'] == 1) { ?>
                                                                <p style="margin:0px 0px 15px;"><i>*) Perhitungan <b>MENGGUNAKAN</b> pembulatan</i></p>
                                                            <?php } elseif ($rsm['pembulatan'] == 0) { ?>
                                                                <p style="margin:0px 0px 15px;"><i>*) Perhitungan <b>TIDAK</b> menggunakan pembulatan (2 angka dibelakang koma)</i></p>
                                                            <?php } elseif ($rsm['pembulatan'] == 2) { ?>
                                                                <p style="margin:0px 0px 15px;"><i>*) Perhitungan <b>TIDAK</b> menggunakan pembulatan (4 angka dibelakang koma)</i></p>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } else if ($rsm['perhitungan'] == 2) { ?>
                                                <p style="margin:0px 5px 5px;">Perhitungan menggunakan formula</p>
                                                <?php if (count($formula) > 0) {
                                                    $nom = 0; ?>
                                                    <div class="clearfix">
                                                        <div class="col-sm-8">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <?php foreach ($formula as $arr1) {
                                                                        $nom++; ?>
                                                                        <tr>
                                                                            <td width="10%" class="text-center"><?php echo $nom; ?></td>
                                                                            <td width="90%"><?php echo $arr1; ?></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                            <?php }
                                            } ?>

                                            <hr style="margin:0px 0px 10px; color:#ccc;" />
                                            <div class="form-group clearfix">
                                                <div class="col-sm-8">
                                                    <label>Catatan <?= $nama_role ?></label>
                                                    <div class="form-control" style="min-height:90px; height:auto; font-size:12px;">
                                                        <?php echo ($rsm['catatan']) ? $rsm['catatan'] : '&nbsp;'; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group clearfix">
                                                <div class="col-sm-8">
                                                    <label>Syarat &amp; Ketentuan</label>
                                                    <div class="form-control" style="min-height:90px; height:auto; font-size:12px;">
                                                        <?php echo ($rsm['term_condition']) ? $rsm['term_condition'] : '&nbsp;'; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group clearfix">
                                                <div class="col-sm-8">
                                                    <label>Status Penawaran</label>
                                                    <div class="form-control" style="min-height:30px; height:auto; font-size:12px;">
                                                        <?php echo $status; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                                            <div style="margin-bottom:15px;">
                                                <a class="btn btn-default jarak-kanan" style="min-width:80px;" href="<?php echo $link1; ?>">Kembali</a>
                                                <?php
                                                if (!$rsm['id_poc']) {
                                                    echo '<a class="btn btn-primary jarak-kanan" style="min-width:80px;" href="' . $link2 . '">Edit</a> ';
                                                    if (!$rsm['flag_disposisi']) {
                                                        echo '<a class="btn btn-success jarak-kanan izin-pd" style="min-width:80px;" href="' . $link3 . '">Persetujuan</a> ';
                                                    }
                                                }
                                                if ($rsm['flag_approval'] == 1) {
                                                    echo '<a class="btn btn-info jarak-kanan" style="min-width:80px;" target="_blank" href="' . $link4 . '">Cetak Ind</a> ';
                                                    echo '<a class="btn btn-info jarak-kanan" style="min-width:80px;" target="_blank" href="' . $link4_2 . '">Cetak Eng</a> ';
                                                } else {
                                                    echo '<a class="btn btn-info jarak-kanan" style="min-width:80px;" target="_blank" href="' . $link5 . '">Preview Ind</a> ';
                                                    echo '<a class="btn btn-info jarak-kanan" style="min-width:80px;" target="_blank" href="' . $link5_2 . '">Preview Eng</a> ';
                                                }
                                                ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div role="tabpanel" class="tab-pane" id="history-data-approval">
                            <?php require_once($public_base_directory . "/web/penawaran-history-approval.php"); ?>
                        </div>

                    </div>
                <?php } ?>

                <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Loading Data ...</h4>
                            </div>
                            <div class="modal-body text-center modal-loading"></div>
                        </div>
                    </div>
                </div>
                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>

    <style type="text/css">
        .table {
            margin-bottom: 10px;
        }

        .table>tbody>tr>td {
            padding: 5px;
        }

        h3.form-title {
            font-size: 18px;
            margin: 0 0 10px;
            font-weight: 700;
        }
    </style>
    <script>
        $(document).ready(function() {
            $(window).on("load resize", function() {
                if ($(this).width() < 977) {
                    $(".vertical-tab").addClass("collapsed-box");
                    $(".vertical-tab").find(".box-tools").show();
                    $(".vertical-tab > .vertical-tab-body").hide();
                } else {
                    $(".vertical-tab").removeClass("collapsed-box");
                    $(".vertical-tab").find(".box-tools").hide();
                    $(".vertical-tab > .vertical-tab-body").show();
                }
            });

            $(".izin-pd").click(function() {
                if (confirm("Apakah anda yakin?")) {
                    $('#loading_modal').modal({
                        backdrop: "static"
                    });
                    return true;
                } else {
                    return false;
                }
            });
        });
    </script>
</body>

</html>