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

$enkq1 = isset($_POST["q1"]) ? $_POST["q1"] : (isset($enk["q1"]) ? $enk["q1"] : null);
$q1    = !($enkq1) ? date("d/m/Y") : htmlspecialchars($enkq1, ENT_QUOTES);
$enkq2 = isset($_POST["q2"]) ? $_POST["q2"] : (isset($enk["q2"]) ? $enk["q2"] : null);
$q2    = !($enkq2) ? date("d/m/Y", strtotime("+1 day")) : htmlspecialchars($enkq2, ENT_QUOTES);
$link = BASE_URL_CLIENT . '/report/po-customer-logistik-exp.php?' . paramEncrypt('q1=' . $q1 . '&q2=' . $q2);
$link02 = BASE_URL_CLIENT . '/calender_admin.php';
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>PO Customer Plan</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="alert alert-danger alert-dismissible" style="display:none">
                    <div class="box-tools">
                        <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                    </div>
                </div>

                <form name="searchForm" id="searchForm" role="form" class="form-horizontal" method="post" action="<?php echo BASE_URL_CLIENT . '/po-customer-logistik.php'; ?>">
                    <div class="box box-solid box-primary" style="border:none;">
                        <div class="box-header">
                            <h3 class="box-title">Pencarian Data</h3>
                        </div>
                        <div class="box-body" style="border:1px solid #ccc; padding:15px;">
                            <div class="form-group row">
                                <div class="col-sm-4 col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-addon">Tanggal Kirim</span>
                                        <input type="text" name="q1" id="q1" class="form-control datepicker input-sm" value="<?php echo $q1; ?>" />
                                    </div>
                                </div>
                                <div class="col-sm-4 col-md-3 col-sm-top">
                                    <div class="input-group">
                                        <span class="input-group-addon">Sampai dengan</span>
                                        <input type="text" name="q2" id="q2" class="form-control datepicker input-sm" value="<?php echo $q2; ?>" />
                                    </div>
                                </div>
                            </div>

                            <hr style="border-top:4px double #ddd; margin:0px 0px 15px;" />

                            <button type="submit" class="btn btn-sm btn-primary jarak-kanan" name="btnSearch" id="btnSearch" style="min-width:100px;">
                                <i class="fa fa-search jarak-kanan"></i> Cari</button>

                            <a class="btn btn-sm btn-info jarak-kanan" href="<?php echo $link02; ?>" style="min-width:100px;">
                                <i class="far fa-calendar-alt jarak-kanan"></i> Lihat Jadwal</a>

                            <a class="btn btn-sm btn-success" href="<?php echo $link; ?>" target="_blank" style="min-width:100px;">
                                <i class="far fa-file-excel jarak-kanan"></i> Export Data</a>

                        </div>
                    </div>
                </form>

                <?php if ($sesrol != '9') { ?>
                    <form action="<?php echo ACTION_CLIENT . '/po-customer-logistik.php'; ?>" id="gform" name="gform" method="post" role="form">
                        <div style="overflow-x: scroll" id="table-long">
                            <div style="width:1500px; height:auto;">
                                <div class="table-responsive-satu">
                                    <table class="table table-bordered" id="table-grid2">
                                        <thead>
                                            <tr>
                                                <th width="50" rowspan="2" class="text-center"><input type="checkbox" name="cekAll" id="cekAll" value="1" /></th>
                                                <th width="50" rowspan="2" class="text-center">No</th>
                                                <th width="200" rowspan="2" class="text-center">Nama Customer</th>
                                                <th width="230" rowspan="2" class="text-center">Area/ Alamat Kirim/ Wilayah OA</th>
                                                <th width="190" rowspan="2" class="text-center">PO Customer</th>
                                                <th width="150" rowspan="2" class="text-center">Catatan</th>
                                                <th width="100" rowspan="2" class="text-center">Tanggal Issued</th>
                                                <th width="80" rowspan="2" class="text-center">Term of Payment</th>
                                                <th colspan="4" class="text-center">Harga (Rp/Liter)</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" width="90">Harga</th>
                                                <th class="text-center" width="200">Rincian Harga</th>
                                                <th class="text-center" width="80">Refund</th>
                                                <th class="text-center" width="80">Other Cost</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $whereadd = '';
                                            if ($sesrol > 1) {
                                                $whereadd = " and f.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
                                            }
                                            $sql = "select a.*, b.id_customer, c.alamat_survey, c.id_wil_oa, c.jenis_usaha, d.nama_prov, e.nama_kab, i.refund_tawar, 
                                            f.nama_customer, f.kode_pelanggan, f.top_payment, g.fullname, j.nama_area, k.jenis_produk, b.nomor_poc, b.harga_poc, 
                                            b.lampiran_poc, k.merk_dagang, h.nama_cabang, l.wilayah_angkut, i.oa_kirim, m.nilai_pbbkb, f.jenis_payment, 
                                            f.top_payment, f.jenis_net, b.lampiran_poc_ori, i.other_cost, i.perhitungan, i.detail_rincian, i.harga_dasar        
                                            from pro_po_customer_plan a 
                                            join pro_po_customer b on a.id_poc = b.id_poc 
                                            join pro_customer_lcr c on a.id_lcr = c.id_lcr
                                            join pro_master_provinsi d on c.prov_survey = d.id_prov 
                                            join pro_master_kabupaten e on c.kab_survey = e.id_kab
                                            join pro_customer f on b.id_customer = f.id_customer 
                                            join acl_user g on f.id_marketing = g.id_user 
                                            join pro_master_cabang h on f.id_wilayah = h.id_master 
                                            join pro_penawaran i on b.id_penawaran = i.id_penawaran  
                                            join pro_master_area j on i.id_area = j.id_master 
                                            join pro_master_produk k on b.produk_poc = k.id_master 
                                            join pro_master_wilayah_angkut l on c.id_wil_oa = l.id_master and c.prov_survey = l.id_prov and c.kab_survey = l.id_kab
                                            join pro_master_pbbkb m on i.pbbkb_tawar = m.id_master
                                            where 1=1 " . $whereadd . " and (a.status_plan = 0 or a.status_plan = 1) and a.is_approved = 1";
                                            if ($q1 && !$q2)
                                                $sql .= " and a.tanggal_kirim = '" . tgl_db($q1) . "'";
                                            else if ($q1 && $q2)
                                                $sql .= " and a.tanggal_kirim between '" . tgl_db($q1) . "' and '" . tgl_db($q2) . "'";
                                            $sql .= " order by a.status_plan, a.tanggal_kirim, a.id_plan";
                                            $res = $con->getResult($sql);
                                            if (count($res) == 0) {
                                                echo '<tr><td colspan="12" style="text-align:center">Data tidak ditemukan </td></tr>';
                                            } else {
                                                $nom = 0;
                                                foreach ($res as $data) {
                                                    $nom++;
                                                    $jns_payment = $data['jenis_payment'];
                                                    $top_payment = $data['top_payment'];
                                                    $jenisCredit = $data['jenis_net'];
                                                    $arr_payment = array("" => "", "CREDIT" => "NET " . $top_payment, "COD" => "COD", "CBD" => "CBD");
                                                    $termPayment = $arr_payment[$jns_payment];

                                                    $idp     = $data['id_plan'];
                                                    $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
                                                    $alamat    = $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
                                                    $vkirim    = $data['volume_kirim'];

                                                    $pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
                                                    $lampPt = $data['lampiran_poc_ori'];
                                                    if ($data['lampiran_poc'] && file_exists($pathPt)) {
                                                        $linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $data['id_poc'] . "_&file=" . $lampPt);
                                                        $attach = '<a href="' . $linkPt . '"><i class="fa fa-paperclip" title="' . $lampPt . '"></i> Lampiran</a>';
                                                    } else {
                                                        $attach = '';
                                                    }
                                                    $rincian = json_decode($data['detail_rincian'], true);

                                                    $tabel_harga = '<table border="0" cellpadding="" cellspacing="0" width="200">';
                                                    foreach ($rincian as $arr1) {
                                                        $cetak = 1;
                                                        $nilai = $arr1['nilai'];
                                                        $biaya = ($arr1['biaya']) ? $arr1['biaya'] : '';
                                                        $biaya = ($rsm['pembulatan']) ? number_format($arr1['biaya']) : number_format($arr1['biaya'], 2);
                                                        $jenis = $arr1['rincian'];
                                                        $tabel_harga .= '
												<tr>
													<td align="left" witdh="110">' . $jenis . ($nilai ? " " . $nilai . "%" : "") . '</td>
													<td align="right">' . $biaya . '</td>
												</tr>
												';
                                                    }
                                                    $tabel_harga .= '</table>';
                                            ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <?php
                                                            echo ($data['status_plan'] == 0 ? '<input type="checkbox" name="cek[' . $idp . ']" id="cek' . $nom . '" class="chkp" value="1" />' : '&nbsp;');
                                                            ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <p class="noFormula"><?php echo $nom; ?></p>
                                                        </td>
                                                        <td>
                                                            <p style="margin-bottom:0px"><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'] : '------'); ?></p>
                                                            <p style="margin-bottom:0px"><?php echo $data['nama_customer']; ?></b></p>
                                                            <p style="margin-bottom:0px"><i><?php echo $data['fullname']; ?></i></p>
                                                        </td>
                                                        <td>
                                                            <p style="margin-bottom:0px"><b><?php echo $data['nama_area']; ?></b></p>
                                                            <p style="margin-bottom:0px"><?php echo $alamat; ?></p>
                                                            <p style="margin-bottom:0px"><?php echo 'Wilayah OA : ' . $data['wilayah_angkut']; ?></p>
                                                        </td>
                                                        <td>
                                                            <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc']; ?></b></p>
                                                            <p style="margin-bottom:0px"><?php echo $data['merk_dagang'] . ' ' . number_format($vkirim) . ' Liter'; ?></p>
                                                            <p style="margin-bottom:0px"><?php echo 'Tgl Kirim ' . tgl_indo($data['tanggal_kirim']); ?></p>
                                                            <p style="margin-bottom:0px"><?php echo $attach; ?></p>
                                                        </td>
                                                        <td>
                                                            <p style="margin-bottom:10px;"><?php echo $data['status_jadwal']; ?></p>
                                                            <p style="margin-bottom:0px;"><?php echo ($data['ask_approval'] == 1) ? '<i>* Disetujui BM</i>' : ''; ?></p>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            echo '<input type="hidden" name="dt1[' . $idp . ']" id="dt1' . $nom . '" value="' . $data['merk_dagang'] . '" />';
                                                            echo '<input type="hidden" name="dt2[' . $idp . ']" id="dt2' . $nom . '" value="' . $vkirim . '" />';
                                                            echo '<input type="hidden" name="dt3[' . $idp . ']" id="dt3' . $nom . '" value="' . $data['oa_kirim'] . '" />';
                                                            echo '<input type="hidden" name="volplan' . $idp . '" value="' . $data['volume_kirim'] . '" />';
                                                            echo date("d/m/Y H:i:s", strtotime($data['created_time'])) . " WIB";
                                                            ?>
                                                        </td>
                                                        <td class="text-center"><?php echo $termPayment; ?></td>
                                                        <td class="text-right"><?php echo number_format($data['harga_dasar']); ?></td>
                                                        <td class="text-left"><?php echo $tabel_harga; ?></td>
                                                        <td class="text-right"><?php echo number_format($data['refund_tawar']); ?></td>
                                                        <td class="text-right"><?php echo number_format($data['other_cost']); ?></td>
                                                    </tr>
                                            <?php }
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <?php if (count($res) > 0) { ?>
                            <hr style="margin:0 0 10px" />
                            <div class="form-group row">
                                <div class="col-sm-offset-6 col-sm-6">
                                    <label>Catatan
                                        <p style="font-size:12px; margin-bottom:0px; font-weight:400;"><i>Untuk catatan approve/reject</i></p>
                                    </label>
                                    <textarea name="catatan_logistik" id="catatan_logistik" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <input type="hidden" name="idq1" value="<?php echo $q1; ?>" />
                                    <input type="hidden" name="idq2" value="<?php echo $q2; ?>" />
                                    <input type="hidden" name="tombol_klik" id="tombol_klik" value="" />
                                </div>
                                <div class="col-sm-6 col-sm-top">
                                    <div class="text-right">
                                        <?php /* <button type="submit" class="btn btn-success jarak-kanan" name="btnSbmt2" id="btnSbmt2" value="1">Persetujuan BM</button> */ ?>
                                        <?php /* <button type="submit" class="btn btn-danger jarak-kanan" name="btnSbmt3" id="btnSbmt3" value="1">Re-schedule</button> */ ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </form>

                <?php } else { ?>
                    <form action="<?php echo ACTION_CLIENT . '/po-customer-logistik.php'; ?>" id="gform" name="gform" method="post" role="form">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-grid2">
                                <thead>
                                    <tr>
                                        <th class="text-center" rowspan="2" width="100">Split</th>
                                        <th class="text-center" rowspan="2" width="70"><input type="checkbox" name="cekAll" id="cekAll" value="1" /></th>
                                        <th class="text-center" rowspan="2" width="70">No</th>
                                        <th class="text-center" rowspan="2" width="230">Nama dan PO Customer</th>
                                        <th class="text-center" rowspan="2" width="230">Area/ Alamat Kirim/ Wilayah OA</th>
                                        <th class="text-center" colspan="2">Quantity</th>
                                        <th class="text-center" rowspan="2" width="120">Tgl Loading</th>
                                        <th class="text-center" rowspan="2" width="">Catatan</th>
                                        <th class="text-center" rowspan="2" width="100">Tanggal Issued</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" width="80">Volume (Liter)</th>
                                        <th class="text-center" width="100">Edit (Liter)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $whereadd = '';
                                    if ($sesrol > 1) {
                                        $whereadd = " and f.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
                                    }
                                    $sql = "select a.*, b.id_customer, c.alamat_survey, c.id_wil_oa, c.jenis_usaha, d.nama_prov, e.nama_kab, i.refund_tawar, 
                                            f.nama_customer, f.kode_pelanggan, f.top_payment, g.fullname, j.nama_area, k.jenis_produk, b.nomor_poc, b.harga_poc, 
                                            b.lampiran_poc, k.merk_dagang, h.nama_cabang, l.wilayah_angkut, i.oa_kirim, m.nilai_pbbkb, f.jenis_payment, 
                                            f.top_payment, f.jenis_net, b.lampiran_poc_ori, i.other_cost, i.perhitungan, i.detail_rincian  
                                            from pro_po_customer_plan a 
                                            join pro_po_customer b on a.id_poc = b.id_poc 
                                            join pro_customer_lcr c on a.id_lcr = c.id_lcr
                                            join pro_master_provinsi d on c.prov_survey = d.id_prov 
                                            join pro_master_kabupaten e on c.kab_survey = e.id_kab
                                            join pro_customer f on b.id_customer = f.id_customer 
                                            join acl_user g on f.id_marketing = g.id_user 
                                            join pro_master_cabang h on f.id_wilayah = h.id_master 
                                            join pro_penawaran i on b.id_penawaran = i.id_penawaran  
                                            join pro_master_area j on i.id_area = j.id_master 
                                            join pro_master_produk k on b.produk_poc = k.id_master 
                                            join pro_master_wilayah_angkut l on c.id_wil_oa = l.id_master and c.prov_survey = l.id_prov and c.kab_survey = l.id_kab
                                            join pro_master_pbbkb m on i.pbbkb_tawar = m.id_master
                                            where 1=1 " . $whereadd . " and (a.status_plan = 0 or a.status_plan = 1) and a.is_approved = 1";
                                    if ($q1 && !$q2)
                                        $sql .= " and a.tanggal_kirim = '" . tgl_db($q1) . "'";
                                    else if ($q1 && $q2)
                                        $sql .= " and a.tanggal_kirim between '" . tgl_db($q1) . "' and '" . tgl_db($q2) . "'";
                                    $sql .= " order by a.status_plan, a.tanggal_kirim, a.id_plan"; //echo $sql;
                                    $res = $con->getResult($sql);
                                    if (count($res) == 0) {
                                        echo '<tr><td colspan="9" style="text-align:center">Data tidak ditemukan </td></tr>';
                                    } else {
                                        $nom = 0;
                                        foreach ($res as $data) {
                                            $nom++;
                                            $form_split_plan = $data['form_split_plan'];

                                            $jns_payment = $data['jenis_payment'];
                                            $top_payment = $data['top_payment'];
                                            $jenisCredit = $data['jenis_net'];
                                            $arr_payment = array("" => "", "CREDIT" => "NET " . $top_payment, "COD" => "COD", "CBD" => "CBD");
                                            $termPayment = $arr_payment[$jns_payment];

                                            $idp     = $data['id_plan'];
                                            $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
                                            $alamat    = $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
                                            $vkirim    = $data['volume_kirim'];
                                            $pbbkbT = ($data['nilai_pbbkb'] / 100) + 1.11;
                                            $oildus = $data['harga_poc'] / $pbbkbT * 0.003;
                                            $pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb'] / 100);
                                            $tmphrg = $data['refund_tawar'] + $oildus + $data['oa_kirim'] + $pbbkbN + $data['other_cost'];
                                            $nethrg = $data['harga_poc'] - $tmphrg;

                                            $pathPt = $public_base_directory . '/files/uploaded_user/lampiran/' . $data['lampiran_poc'];
                                            $lampPt = $data['lampiran_poc_ori'];
                                            if ($data['lampiran_poc'] && file_exists($pathPt)) {
                                                $linkPt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=POC_" . $data['id_poc'] . "_&file=" . $lampPt);
                                                $attach = '<a href="' . $linkPt . '"><i class="fa fa-paperclip" title="' . $lampPt . '"></i> Lampiran</a>';
                                            } else {
                                                $attach = '';
                                            }

                                            $rincian = json_decode($data['detail_rincian'], true);
                                            $oa_penawaran = 0;
                                            $nomTW = 0;
                                            foreach ($rincian as $arr1) {
                                                $nomTW++;
                                                if ($nomTW == '2') $oa_penawaran = $arr1['biaya'];
                                            }

                                    ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?php if ($data['status_plan'] == 0) { ?>
                                                        <button type="button" class="btn btn-action btn-primary addRow" id="ert<?php echo $nom; ?>" data-cnt="1" value="<?php echo $idp; ?>">
                                                            <i class="fa fa-plus"></i></button>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    echo ($data['status_plan'] == 0 ? '<input type="checkbox" name="cek[' . $idp . ']" id="cek' . $nom . '" class="chkp" value="1" />' : '&nbsp;');
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <p class="noFormula"><?php echo $nom; ?></p>
                                                </td>
                                                <td>
                                                    <p style="margin-bottom:0px"><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'] : '------'); ?></p>
                                                    <p style="margin-bottom:0px"><?php echo $data['nama_customer']; ?></b></p>
                                                    <p style="margin-bottom:5px"><i><?php echo $data['fullname']; ?></i></p>

                                                    <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc']; ?></b></p>
                                                    <p style="margin-bottom:0px"><?php echo $data['merk_dagang']; ?></p>
                                                    <?php /*<p style="margin-bottom:0px"><?php echo $attach;?></p> */ ?>
                                                </td>
                                                <td>
                                                    <p style="margin-bottom:0px"><b><?php echo $data['nama_area']; ?></b></p>
                                                    <p style="margin-bottom:0px"><?php echo $alamat; ?></p>
                                                    <p style="margin-bottom:0px"><?php echo 'Wilayah OA : ' . $data['wilayah_angkut']; ?></p>
                                                </td>
                                                <td class="text-right"><?php echo number_format($vkirim); ?></td>
                                                <td class="text-right volume_oil">
                                                    <?php if ($data['status_plan'] == 0) {  ?>
                                                        <input type="text" class="text-right input-po toa hitung" name="<?php echo 'dt4[' . $idp . ']'; ?>" id="<?php echo 'dt4' . $nom; ?>" value="<?php echo $data['volume_kirim']; ?>" style="width:100%;" />
                                                    <?php } else echo number_format($data['volume_kirim']); ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($data['status_plan'] == 0) : ?>
                                                        <input type="text" id="<?php echo 'tgl_loading' . $nom; ?>" name="<?php echo 'tgl_loading[' . $idp . ']'; ?>" class="form-control datepicker tgl_loading" autocomplete="off" />
                                                    <?php else : ?>
                                                        <?= date("d/m/Y", strtotime($data['tanggal_loading'])) ?>
                                                    <?php endif ?>
                                                </td>
                                                <td>
                                                    <p style="margin-bottom:3px"><?php echo 'Tgl Kirim ' . tgl_indo($data['tanggal_kirim']); ?></p>
                                                    <p style="margin-bottom:3px;"><?php echo $data['status_jadwal']; ?></p>
                                                    <p style="margin-bottom:0px;"><?php echo ($data['ask_approval'] == 1) ? '<i>* Disetujui BM</i>' : ''; ?></p>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo '<input type="hidden" name="dt1[' . $idp . ']" id="dt1' . $nom . '" value="' . $data['merk_dagang'] . '" />';
                                                    echo '<input type="hidden" name="dt2[' . $idp . ']" id="dt2' . $nom . '" value="' . $vkirim . '" />';
                                                    echo '<input type="hidden" name="dt3[' . $idp . ']" id="dt3' . $nom . '" value="' . $oa_penawaran . '" />';
                                                    echo '<input type="hidden" name="volplan' . $idp . '" value="' . $data['volume_kirim'] . '" />';
                                                    echo date("d/m/Y H:i:s", strtotime($data['created_time'])) . " WIB";
                                                    ?>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (count($res) > 0) { ?>
                            <?php
                            $status_disabled = "";
                            $tgl_sekarang = strtotime(date("Y-m-d H:i:s"));
                            $wilayah = paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]);

                            if ($wilayah == '4' || $wilayah == '7') {
                                // Samarinda, Banjarmasin zona WITA +1 dari WIB
                                $waktu_sekarang = date("H:i:s", strtotime("+1 hour"));
                                $waktu_tutup = date("17:01:00");
                                $zona_waktu = "WITA";
                                $waktu_buka = date("06:59:00");
                                $tgl_buka = date("Y-m-d 06:59:00", strtotime("+14 hour", $tgl_sekarang));
                            } else {
                                $waktu_sekarang = date("H:i:s");
                                $waktu_tutup = date("16:01:00");
                                $zona_waktu = "WIB";
                                $waktu_buka = date("06:59:00");
                                $tgl_buka = date("Y-m-d 06:59:00", strtotime("+15 hour", $tgl_sekarang));
                            }


                            if ($waktu_sekarang >= $waktu_buka && $waktu_sekarang <= $waktu_tutup) {
                                $status_disabled = "";
                            } elseif ($waktu_sekarang >= $waktu_tutup || $waktu_sekarang < $waktu_buka) {
                                $status_disabled = "disabled";
                            }
                            ?>
                            <hr style="margin:0 0 10px" />
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label>Catatan
                                        <p style="font-size:12px; margin-bottom:0px; font-weight:400;"><i>Untuk catatan approve/reject</i></p>
                                    </label>
                                    <textarea name="catatan_logistik" id="catatan_logistik" class="form-control"></textarea>
                                </div>
                            </div>
                            <?php if ($status_disabled == "disabled") : ?>
                                <span style="color: red;"><b>DP sudah ditutup, Akan dibuka kembali pada : <?= $tgl_buka . " " . $zona_waktu ?></b>
                                </span>
                                <br><br>
                            <?php endif ?>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <input type="hidden" name="idq1" value="<?php echo $q1; ?>" />
                                    <input type="hidden" name="idq2" value="<?php echo $q2; ?>" />
                                    <input type="hidden" name="tombol_klik" id="tombol_klik" value="" />
                                    <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt1" id="btnSbmt1" value="1">Masuk DR</button>
                                    <button type="submit" class="btn btn-danger jarak-kanan" name="btnSbmt3" id="btnSbmt3" value="1">Re-schedule</button>
                                </div>
                            </div>
                        <?php } ?>
                    </form>
                <?php } ?>


                <div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Peringatan</h4>
                            </div>
                            <div class="modal-body">
                                <div id="preview_alert" class="text-center"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
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
        h3.form-title {
            font-size: 18px;
            margin: 0 0 10px;
            font-weight: 700;
        }

        #table-grid2 {
            margin-bottom: 15px;
        }

        #table-grid2 th {
            font-size: 11px;
            font-family: arial
        }

        #table-grid2 td {
            font-size: 11px;
            font-family: arial
        }

        .input-po {
            padding: 5px;
            height: auto;
            font-size: 11px;
            font-family: arial;
        }
    </style>
    <script>
        $(document).ready(function() {
            $.datepicker.setDefaults({
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true,
                showAnim: "fadeIn"
            });
            $(".hitung").number(true, 0, ".", ",");
            $("form#gform").on("click", "button:submit", function() {
                if (confirm("Apakah anda yakin?")) {
                    var tombol = $(this).attr("id").split("btnSbmt");
                    $("#tombol_klik").val(tombol[1]);
                    if ($("#gform").find("input:checked").length > 0) {
                        var valid = true;
                        var kosongKe = null;

                        if (tombol[1] == '1') {
                            // Hanya baris data yang dicek (checkbox baris)
                            const $rowsChecked = $("#gform tr").has('input[type="checkbox"]:checked');

                            let adaKosong = false;
                            $rowsChecked.each(function() {
                                const $date = $(this).find('input.tgl_loading, input.newtgl_loading');
                                if ($date.length === 0) return; // ini kemungkinan baris header → lewati

                                if (!$date.first().val()) {
                                    adaKosong = true;
                                    $date.first().addClass("is-invalid").focus();
                                    return false;
                                }
                            });
                            if (adaKosong) {
                                $("#preview_modal #preview_alert")
                                    .text("Tanggal Loading wajib diisi pada baris yang dicentang.");
                                $("#preview_modal").modal("show");
                                return false;
                            }
                        }

                        if (tombol[1] != -1) {
                            $("#loading_modal").modal({
                                backdrop: "static"
                            });
                            $.ajax({
                                type: 'POST',
                                url: "./__cek_po_customer_logistik.php",
                                dataType: "json",
                                data: $("#gform").serializeArray(),
                                cache: false,
                                success: function(data) {
                                    if (data.error) {
                                        $("#loading_modal").modal("hide");
                                        $("#loading_modal").on("hidden.bs.modal", function() {
                                            $("#preview_modal").find("#preview_alert").html(data.error);
                                            $("#preview_modal").modal();
                                            return false;
                                        });
                                    } else {
                                        $("button[type='submit']").addClass("disabled");
                                        $("#gform").submit();
                                    }
                                }
                            });
                            return false;
                        } else {
                            $("#loading_modal").modal({
                                backdrop: "static"
                            });
                            $("button[type='submit']").addClass("disabled");
                            $("#gform").submit();
                        }
                    } else {
                        $("#preview_modal").find("#preview_alert").text("Data Po Belum dipilih..");
                        $("#preview_modal").modal();
                        return false;
                    }
                } else return false;
            });

            $("#cekAll, .chkp").iCheck({
                checkboxClass: 'icheckbox_square-blue no-margin'
            });
            $("#cekAll").on("ifChecked", function() {
                $(".chkp").iCheck("check");
            }).on("ifUnchecked", function() {
                $(".chkp").iCheck("uncheck");
            });
            $("#gform").on("ifChecked", ".chkp", function() {
                $(this).parents("tr").first().css("background-color", "#f6f6f6");
            }).on("ifUnchecked", ".chkp", function() {
                $(this).parents("tr").first().css("background-color", "#ffffff");
            });

            $("#gform").on("click", "#table-grid2 button.addRow", function() {
                var row = $(this).closest('tr');
                var idl = $(this).val();
                var gfd = $(this).data("cnt");
                $(this).data("cnt", (gfd + 1));
                var tmpId = row.find("input.toa").attr("id");
                var newId = tmpId.substr(4) + "a" + gfd;
                var newId2 = tmpId.substr(3) + "a" + gfd;

                var cloning = row.clone();
                cloning.find('td').each(function(i, v) {
                    var el = $(this).find("input");
                    var id = el.attr("id") || null;
                    switch (i) {
                        case 0:
                            $(v).html('<button type="button" class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></button>');
                            break;
                        case 1:
                            $(v).html('<input type="checkbox" name="newcek[' + idl + '][]" id="newcek' + newId2 + '" class="chkp" value="1" />');
                            break;
                    }
                    if (id && i > 1) {
                        el.each(function(i, v) {
                            cloning.find(".tgl_loading").each(function() {
                                $(this)
                                    .removeClass("tgl_loading")
                                    .addClass("newtgl_loading");
                            });

                            cloning.find(".vol_kirim").each(function() {
                                $(this)
                                    .removeClass("vol_kirim")
                                    .addClass("newvol_kirim");
                            });
                            var elName = "new" + $(this).attr("name").substr(0, 3) + "[" + idl + "][]";
                            var elId = $(this).attr("id");
                            $(this).attr("id", elId + newId);
                            $(this).attr('name', elName);
                        });
                    }
                });
                cloning.find('.newtgl_loading').val("");
                cloning.find('input:text').val("");
                cloning.find(".hdn").remove();
                let tmp_elm = row.find("input[name^='dt3']");
                let elName = "newSplit" + tmp_elm.attr("name");
                let elemen = '<input type="hidden" name="' + elName + '" value="1" />';

                cloning.find("td:last-child").append(elemen);
                row.find("td:last-child").append(elemen);
                row.after(cloning);

                $("#newcek" + newId2).iCheck({
                    checkboxClass: 'icheckbox_square-blue no-margin'
                });
                $(".hitung").number(true, 0, ".", ",");
                $("#table-grid2").find(".noFormula").each(function(i, v) {
                    $(this).text(i + 1);
                });

                cloning.find(".datepicker").removeClass("hasDatepicker").removeAttr("id");
                cloning.find(".datepicker").datepicker("destroy");
                cloning.find(".datepicker").datepicker({});
            }).on("click", "#table-grid2 button.hRow", function() {
                var cRow = $(this).closest('tr');
                cRow.remove();
                $("#table-grid2").find(".noFormula").each(function(i, v) {
                    $(this).text(i + 1);
                });
            });

            var x, y, top, left, down;
            $("#table-long").mousedown(function(e) {
                if (e.target.nodeName != "INPUT" && e.target.nodeName != "SELECT") {
                    down = true;
                    x = e.pageX;
                    y = e.pageY;
                    top = $(this).scrollTop();
                    left = $(this).scrollLeft();
                }
            });
            $("body").mousemove(function(e) {
                if (down) {
                    var newX = e.pageX;
                    var newY = e.pageY;
                    $("#table-long").scrollTop(top - newY + y);
                    $("#table-long").scrollLeft(left - newX + x);
                }
            });
            $("body").mouseup(function(e) {
                down = false;
            });
        });
    </script>
</body>

</html>