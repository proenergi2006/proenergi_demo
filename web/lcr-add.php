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
$idk     = isset($enk["idk"]) ? htmlspecialchars($enk["idk"], ENT_QUOTES) : '';
$idc     = isset($enk["idc"]) ? htmlspecialchars($enk["idc"], ENT_QUOTES) : '';
$sesRol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

if ($sesRol == "11" || $sesRol == "17") $kembali = BASE_URL_CLIENT . "/lcr.php";
else if ($sesRol == "9") $kembali = BASE_URL_CLIENT . '/verifikasi-lcr-detail.php?' . paramEncrypt('idr=' . $idr . '&idk=' . $idk);

if (isset($enk['idk']) && $enk['idk'] !== '') {
    $action     = "update";
    $section     = "Edit Data";
    $sql = "select a.*, b.nama_prov, c.nama_kab, d.nama_customer, e.nama_cabang, f.wilayah_angkut 
				from pro_customer_lcr a join pro_master_provinsi b on a.prov_survey = b.id_prov join pro_master_kabupaten c on a.kab_survey = c.id_kab 
				join pro_customer d on a.id_customer = d.id_customer join pro_master_cabang e on a.id_wilayah = e.id_master 
				left join pro_master_wilayah_angkut f on a.id_wil_oa = f.id_master
				where a.id_lcr = '" . $idk . "' and a.id_customer = '" . $idr . "'";
    $rsm         = $con->getRecord($sql);
    $tipe_bisnis_lain         = ($rsm['jenis_usaha'] == 10) ? 'value="' . $rsm['jenis_usaha_lain'] . '"' : 'disabled';
    $surveyor     = (json_decode($rsm['nama_surveyor'], true) === NULL) ? array("") : json_decode($rsm['nama_surveyor'], true);
    $hasilsurv     = (json_decode($rsm['hasilsurv'], true) === NULL) ? array("") : json_decode($rsm['hasilsurv'], true);
    $kompetitor = (json_decode($rsm['kompetitor'], true) === NULL) ? array("") : json_decode($rsm['kompetitor'], true);
    $produkvol     = (json_decode($rsm['produkvol'], true) === NULL) ? array(1) : json_decode($rsm['produkvol'], true);
    $picustomer = (json_decode($rsm['picustomer'], true) === NULL) ? array(1) : json_decode($rsm['picustomer'], true);
    $jamOperasi = (json_decode($rsm['jam_operasional'], true) === NULL) ? array("") : json_decode($rsm['jam_operasional'], true);
    $tangki     = (json_decode($rsm['tangki'], true) === NULL) ? array(1) : json_decode($rsm['tangki'], true);
    $pendukung     = (json_decode($rsm['pendukung'], true) === NULL) ? array(1) : json_decode($rsm['pendukung'], true);
    $kuantitas1 = (json_decode($rsm['quantity_tangki'], true) === NULL) ? array(1) : json_decode($rsm['quantity_tangki'], true);
    $kualitas1     = (json_decode($rsm['quality_tangki'], true) === NULL) ? array(1) : json_decode($rsm['quality_tangki'], true);
    $kapal         = (json_decode($rsm['kapal'], true) === NULL) ? array(1) : json_decode($rsm['kapal'], true);
    $jetty         = (json_decode($rsm['jetty'], true) === NULL) ? array(1) : json_decode($rsm['jetty'], true);
    $kuantitas2 = (json_decode($rsm['quantity_kapal'], true) === NULL) ? array(1) : json_decode($rsm['quantity_kapal'], true);
    $kualitas2     = (json_decode($rsm['quality_kapal'], true) === NULL) ? array(1) : json_decode($rsm['quality_kapal'], true);
} else {
    $action     = "add";
    $section     = "Tambah Data";
    $rsm        = array();
    $surveyor     = array("");
    $hasilsurv     = array("");
    $kompetitor = array("");
    $produkvol     = array(1);
    $picustomer = array(1);
    $jamOperasi = array("");
    $tangki     = array(1);
    $pendukung     = array(1);
    $kuantitas1 = array(1);
    $kualitas1     = array(1);
    $kapal         = array(1);
    $jetty         = array(1);
    $kuantitas2 = array(1);
    $kualitas2     = array(1);
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("jqueryUI", "fileupload", "gmaps", "formatNumber"), "css" => array("jqueryUI", "fileupload"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1><?php echo "Location Customer Review"; ?></h1>
            </section>

            <section class="content">

                <?php $flash->display(); ?>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#form-lcr" aria-controls="form-evaluation" role="tab" data-toggle="tab">Form LCR</a>
                    </li>
                    <li role="presentation" class="">
                        <a href="#gambar-lcr" aria-controls="data-evaluation" role="tab" data-toggle="tab">Gambar</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="form-lcr">
                        <form action="<?php echo ACTION_CLIENT . '/lcr.php'; ?>" id="gform" name="gform" method="post" role="form">
                            <div class="box box-purple">
                                <div class="box-header bg-light-purple with-border">
                                    <h3 class="box-title">1. General Information</h3>
                                </div>
                                <div class="box-body">
                                    <?php if (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 9 && $rsm['flag_approval'] && $rsm['flag_disposisi'] > 1) { ?>
                                        <input type="hidden" name="rever" id="rever" value="1" />
                                        <p style="font-size:16px;" class="bg-red pad"><b><i>Merubah LCR, maka LCR akan diverifikasi ulang oleh BM</i></b></p>
                                    <?php } ?>

                                    <?php if (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 11 && (isset($rsm['flag_approval']) && $rsm['flag_approval'] != '0') || (isset($rsm['flag_disposisi']) && $rsm['flag_disposisi'] != '0')) {
                                    ?>
                                        <input type="hidden" name="forceEdit" id="forceEdit" value="1" />
                                        <p style="font-size:16px;" class="bg-red pad"><b><i>Merubah LCR, maka LCR akan diverifikasi ulang oleh Logistik dan BM</i></b></p>
                                    <?php } ?>

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Nama Perusahaan *</label>
                                            <?php if ($action == "add") { ?>
                                                <?php
                                                $where = "id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "'";
                                                if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 18) {
                                                    $where = "1=1";
                                                    if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
                                                        $where = "(id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "' or id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
                                                    else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
                                                        $where = "(id_group = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']) . "' or id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "')";
                                                }
                                                ?>
                                                <select name="idr" id="idr" class="form-control validate[required] select2">
                                                    <option></option>
                                                    <?php $con->fill_select("id_customer", "if(kode_pelanggan = '',nama_customer,concat(kode_pelanggan,' - ',nama_customer))", "pro_customer", $idc, "where " . $where . " and id_wilayah != 0", "id_customer desc, nama", false); ?>
                                                </select>
                                            <?php } else if ($action == "update") { ?>
                                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                                <div class="form-control"><?php echo $rsm['nama_customer']; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Alamat Lokasi *</label>
                                            <textarea id="alamat_lokasi" name="alamat_lokasi" class="form-control validate[required]" <?php echo (isset($rsm['flag_approval']) && $rsm['flag_approval'] > 0) ? 'readonly' : ''; ?>><?php echo $rsm['alamat_survey'] ?? null; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Propinsi *</label>
                                            <?php if (!isset($rsm['flag_approval'])) { ?>
                                                <select id="prov_lokasi" name="prov_lokasi" class="form-control validate[required] select2">
                                                    <option></option>
                                                    <?php $con->fill_select("id_prov", "nama_prov", "pro_master_provinsi", $rsm['prov_survey'], "", "nama_prov", false); ?>
                                                </select>
                                            <?php } else { ?>
                                                <input type="hidden" name="prov_lokasi" id="prov_lokasi" value="<?php echo $rsm['prov_survey']; ?>" />
                                                <input type="text" name="provNama" id="provNama" class="form-control" value="<?php echo $rsm['nama_prov']; ?>" readonly />
                                            <?php } ?>
                                        </div>
                                        <div class="col-sm-6 col-sm-top">
                                            <label>Kabupaten/Kota *</label>
                                            <?php if (!isset($rsm['flag_approval'])) { ?>
                                                <select id="kab_lokasi" name="kab_lokasi" class="form-control validate[required] select2">
                                                    <?php $con->fill_select("id_kab", "nama_kab", "pro_master_kabupaten", $rsm['kab_survey'], "where id_prov = '" . $rsm['prov_survey'] . "'", "nama_kab", false); ?>
                                                </select>
                                            <?php } else { ?>
                                                <input type="hidden" name="kab_lokasi" id="kab_lokasi" value="<?php echo $rsm['kab_survey']; ?>" />
                                                <input type="text" name="kabNama" id="kabNama" class="form-control" value="<?php echo $rsm['nama_kab']; ?>" readonly />
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <?php if (paramDecrypt($_SESSION["sinori" . SESSIONID]["id_role"]) == 9) { ?>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Wilayah Ongkos Angkut</label>
                                                <?php if (!$rsm['flag_approval'] || !$rsm['id_wil_oa']) { ?>
                                                    <select name="id_wil_oa" id="id_wil_oa" class="form-control validate[required] select2">
                                                        <option></option>
                                                        <?php $con->fill_select("id_master", "wilayah_angkut", "pro_master_wilayah_angkut", $rsm['id_wil_oa'], "where is_active=1 and id_prov = '" . $rsm['prov_survey'] . "' and id_kab = '" . $rsm['kab_survey'] . "'", "", false); ?>
                                                    </select>
                                                <?php } else { ?>
                                                    <input type="hidden" name="id_wil_oa" id="id_wil_oa" value="<?php echo $rsm['id_wil_oa']; ?>" />
                                                    <input type="text" name="wiloaNama" id="wiloaNama" class="form-control" value="<?php echo $rsm['wilayah_angkut']; ?>" readonly />
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Telepon Lokasi</label>
                                            <input type="text" id="telp_lokasi" name="telp_lokasi" class="form-control phone-number" value="<?php echo $rsm['telp_survey'] ?? null; ?>" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Fax Lokasi</label>
                                            <input type="text" id="fax_lokasi" name="fax_lokasi" class="form-control fax" value="<?php echo $rsm['fax_survey'] ?? null; ?>" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label>Tanggal Survey *</label>
                                            <input type="text" id="tgl_survey" name="tgl_survey" autocomplete='off' class="form-control validate[required,custom[date]] datepicker" value="<?php echo isset($rsm['tgl_survey']) ? tgl_indo($rsm['tgl_survey'], 'normal', 'db', '/') : null; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label>Surveyor</label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover tbl-surveyor">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="10%">No</th>
                                                            <th class="text-center" width="75%">Nama Surveyor</th>
                                                            <th class="text-center" width="15%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $nomA = 0;
                                                        foreach ($surveyor as $dataA) {
                                                            $nomA++; ?>
                                                            <tr>
                                                                <td class="text-center">
                                                                    <span class="noSurveyor" data-row-count="<?php echo $nomA; ?>"><?php echo $nomA; ?></span>
                                                                </td>
                                                                <td class="text-left"><input type="text" name="surveyor[]" id="<?php echo "surveyor" . $nomA; ?>" class="form-control validate[required] input-sm" value="<?php echo $dataA; ?>" /></td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button>
                                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Review</label>
                                            <textarea id="review_lokasi" name="review_lokasi" class="form-control"><?php echo isset($rsm['review']) ? str_replace('<br />', PHP_EOL, $rsm['review']) : null; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Jenis Usaha *</label>
                                            <select name="jenis_usaha" id="jenis_usaha" class="form-control validate[required] select-other">
                                                <option></option>
                                                <?php
                                                $arrJnsUsaha = array(
                                                    "Agriculture &amp; Forestry / Horticulture",
                                                    "Business &amp; Information",
                                                    "Construction/Utilities/Contracting",
                                                    "Education",
                                                    "Finance &amp; Insurance",
                                                    "Food &amp; hospitally",
                                                    "Gaming",
                                                    "Health Services",
                                                    "Motor Vehicle",
                                                    "Natural Resources / Environmental",
                                                    "Personal Service",
                                                    "Manufacture"
                                                );
                                                foreach ($arrJnsUsaha as $dataxJns) {
                                                    $selected = ($rsm['jenis_usaha'] == $dataxJns) ? 'selected' : '';
                                                    echo '<option value="' . $dataxJns . '" ' . $selected . '>' . $dataxJns . '</option>';
                                                    if ($rsm['jenis_usaha'] && !in_array($rsm['jenis_usaha'], $arrJnsUsaha))
                                                        echo '<option selected>' . $rsm['jenis_usaha'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Website</label>
                                            <input type="text" id="website_lokasi" name="website_lokasi" class="form-control" value="<?php echo $rsm['website'] ?? null; ?>" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover tbl-hasil">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="10%">No</th>
                                                            <th class="text-center" width="75%">Hasil</th>
                                                            <th class="text-center" width="15%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $nomE = 0;
                                                        foreach ($hasilsurv as $dataE) {
                                                            $nomE++; ?>
                                                            <tr>
                                                                <td class="text-center"><span class="noHasil" data-row-count="<?php echo $nomE; ?>"><?php echo $nomE; ?></span></td>
                                                                <td class="text-left"><input type="text" name="hasilsurv[]" id="<?php echo "hasilsurv" . $nomE; ?>" class="form-control input-sm" value="<?php echo $dataE; ?>" /></td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button>
                                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-8">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover tbl-produkvol">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="7%">No</th>
                                                            <th class="text-center" width="40%">Produk</th>
                                                            <th class="text-center" width="40%">Volume/Bulan</th>
                                                            <th class="text-center" width="13%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $nomB = 0;
                                                        foreach ($produkvol as $dataB) {
                                                            $nomB++; ?>
                                                            <tr>
                                                                <td class="text-center">
                                                                    <span class="noProdukvol" data-row-count="<?php echo $nomB; ?>"><?php echo $nomB; ?></span>
                                                                </td>
                                                                <td class="text-left"><input type="text" name="produk[]" id="<?php echo "produk" . $nomB; ?>" class="form-control input-sm" value="<?php echo $dataB['produk'] ?? null; ?>" /></td>
                                                                <td class="text-left"><input type="text" name="volbul[]" id="<?php echo "volbul" . $nomB; ?>" class="form-control input-sm" value="<?php echo $dataB['volbul'] ?? null; ?>" /></td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button>
                                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-10">
                                            <label>Penanggung Jawab</label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover tbl-picustomer">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="7%">No</th>
                                                            <th class="text-center" width="30%">Nama</th>
                                                            <th class="text-center" width="30%">Posisi</th>
                                                            <th class="text-center" width="23%">Telepon</th>
                                                            <th class="text-center" width="10%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $nomD = 0;
                                                        foreach ($picustomer as $dataD) {
                                                            $nomD++; ?>
                                                            <tr>
                                                                <td class="text-center"><span class="noPicustomer" data-row-count="<?php echo $nomD; ?>"><?php echo $nomD; ?></span></td>
                                                                <td class="text-left"><input type="text" name="namacus[]" id="<?php echo "namacus" . $nomD; ?>" class="form-control input-sm validate[required]" value="<?php echo $dataD['nama'] ?? null; ?>" /></td>
                                                                <td class="text-left"><input type="text" name="posisicus[]" id="<?php echo "posisicus" . $nomD; ?>" class="form-control input-sm validate[required]" value="<?php echo $dataD['posisi'] ?? null; ?>" /></td>
                                                                <td class="text-left"><input type="text" name="telpcus[]" id="<?php echo "telpcus" . $nomD; ?>" class="form-control input-sm telepon validate[required]" value="<?php echo $dataD['telepon'] ?? null; ?>" /></td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button>
                                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Komitmen Penerimaan</label>
                                            <select name="alat_ukur" id="alat_ukur" class="form-control select2">
                                                <option></option>
                                                <option value="Jembatan Timbang" <?php echo ($rsm['alat_ukur'] == "Jembatan Timbang") ? 'selected' : ''; ?>>Jembatan Timbang</option>
                                                <option value="TUM" <?php echo ($rsm['alat_ukur'] == "TUM") ? 'selected' : ''; ?>>TUM</option>
                                                <option value="Flow Meter" <?php echo ($rsm['alat_ukur'] == "Flow Meter") ? 'selected' : ''; ?>>Flow Meter</option>
                                                <option value="Tanki Pelanggan" <?php echo ($rsm['alat_ukur'] == "Tanki Pelanggan") ? 'selected' : ''; ?>>Tanki Pelanggan</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <label>Toleransi</label>
                                            <input type="text" id="toleransi" name="toleransi" class="form-control" value="<?php echo $rsm['toleransi'] ?? null; ?>" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label>Kompetitor</label>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover tbl-kompetitor">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="10%">No</th>
                                                            <th class="text-center" width="75%">Nama Kompetitor</th>
                                                            <th class="text-center" width="15%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $nomC = 0;
                                                        foreach ($kompetitor as $dataC) {
                                                            $nomC++; ?>
                                                            <tr>
                                                                <td class="text-center"><span class="noKompetitor" data-row-count="<?php echo $nomC; ?>"><?php echo $nomC; ?></span></td>
                                                                <td class="text-left"><input type="text" name="kompetitor[]" id="<?php echo "kompetitor" . $nomC; ?>" class="form-control input-sm" value="<?php echo $dataC; ?>" /></td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button>
                                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <h3 class="form-title">JAM OPERASIONAL</h3>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label>Senin-Jumat</label>
                                            <input type="text" id="jam_operasional1" name="jam_operasional1" class="form-control" value="<?php echo $jamOperasi[0] ?? null; ?>" />
                                        </div>
                                        <div class="col-sm-4">
                                            <label>Sabtu</label>
                                            <input type="text" id="jam_operasional2" name="jam_operasional2" class="form-control" value="<?php echo $jamOperasi[1] ?? null; ?>" />
                                        </div>
                                        <div class="col-sm-4">
                                            <label>Minggu</label>
                                            <input type="text" id="jam_operasional3" name="jam_operasional3" class="form-control" value="<?php echo $jamOperasi[2] ?? null; ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="box box-purple">
                                        <div class="box-header bg-light-purple with-border">
                                            <h3 class="box-title">2. Location Information</h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <label>Latitude *</label>
                                                    <input type="text" id="latitude" name="latitude" class="form-control validate[required]" value="<?php echo $rsm['latitude_lokasi'] ?? null; ?>" <?php echo (isset($rsm['flag_approval']) && $rsm['flag_approval'] > 0) ? '' : ''; ?> />
                                                    <span style="font-weight: bold;">Harap mengisi Latitude dan Longitude dengan benar agar terbaca oleh aplikasi driver</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Longitude *</label>
                                                    <div class="input-group">
                                                        <input type="text" id="longitude" name="longitude" class="form-control validate[required]" value="<?php echo $rsm['longitude_lokasi'] ?? null; ?>" <?php echo (isset($rsm['flag_approval']) && $rsm['flag_approval'] > 0) ? '' : ''; ?> />
                                                        <div class="input-group-btn">
                                                            <button type="button" class="btn btn-info" id="lihat_map" title="Preview Map"><i class="fa fa-search"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Jarak dari Depot</label>
                                                    <div class="input-group">
                                                        <input type="text" id="jarak_depot" name="jarak_depot" class="form-control" value="<?php echo $rsm['jarak_depot'] ?? null; ?>" />
                                                        <span class="input-group-addon">KM</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <br />
                                                    <label>Link Google Maps</label>
                                                    <input type="text" id="link_google_maps" name="link_google_maps" class="form-control" value="<?php echo $rsm['link_google_maps'] ?? null; ?>" <?php echo (isset($rsm['flag_approval']) && $rsm['flag_approval'] > 0) ? 'readonly' : ''; ?> />
                                                    <!-- <div class="input-group">
                                                        <input type="text" id="link_google_maps" name="link_google_maps" class="form-control" value="<?php echo $rsm['link_google_maps'] ?? null; ?>" <?php echo (isset($rsm['flag_approval']) && $rsm['flag_approval'] > 0) ? 'readonly' : ''; ?> />
                                                        <div class="input-group-btn">
                                                            <button type="button" class="btn btn-info" id="lihat_map2" title="Preview Map"><i class="fa fa-search"></i></button>
                                                        </div>
                                                    </div> -->
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label>Preview Map</label>
                                                        <div id="map_canvas"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Rute</label>
                                                        <textarea id="rute_lokasi" name="rute_lokasi" class="form-control" style="height:180px;"><?php echo isset($rsm['rute_lokasi']) ? str_replace('<br />', PHP_EOL, $rsm['rute_lokasi']) : null; ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Catatan</label>
                                                        <textarea id="note_lokasi" name="note_lokasi" class="form-control" style="height:180px;"><?php echo isset($rsm['note_lokasi']) ? str_replace('<br />', PHP_EOL, $rsm['note_lokasi']) : null; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-4 col-sm-top">
                                                    <label>Max Kapasitas Truk</label>
                                                    <select name="max_truk" id="max_truk" class="form-control select2">
                                                        <option></option>
                                                        <option value="5 KL" <?php echo ($rsm['max_truk'] == "5 KL") ? 'selected' : ''; ?>>5 KL</option>
                                                        <option value="8 KL" <?php echo ($rsm['max_truk'] == "8 KL") ? 'selected' : ''; ?>>8 KL</option>
                                                        <option value="10 KL" <?php echo ($rsm['max_truk'] == "10 KL") ? 'selected' : ''; ?>>10 KL</option>
                                                        <option value="16 KL" <?php echo ($rsm['max_truk'] == "16 KL") ? 'selected' : ''; ?>>16 KL</option>
                                                        <option value="24 KL" <?php echo ($rsm['max_truk'] == "24 KL") ? 'selected' : ''; ?>>24 KL</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-4 col-sm-top">
                                                    <label>Biaya Koordinasi</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">Rp</span>
                                                        <input type="text" id="lsm_portal" name="lsm_portal" class="form-control hitung" value="<?php echo $rsm['lsm_portal'] ?? null; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-sm-top">
                                                    <label>Min Kapasitas Truck</label>
                                                    <div class="input-group">
                                                        <input type="text" id="min_vol_kirim" name="min_vol_kirim" class="form-control" value="<?php echo $rsm['min_vol_kirim'] ?? null; ?>" />
                                                        <span class="input-group-addon">KL</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <label>Penjelasan Proses Bongkaran</label>
                                                    <textarea id="penjelasan_bongkar" name="penjelasan_bongkar" class="form-control"><?php echo isset($rsm['penjelasan_bongkar']) ? str_replace('<br />', PHP_EOL, $rsm['penjelasan_bongkar']) : null; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="box box-purple">
                                        <div class="box-header bg-light-purple with-border">
                                            <h3 class="box-title">3. Unloading Information</h3>
                                        </div>
                                        <div class="box-body">

                                            <p style="border:1px solid #ccc; background-color:#ddd; margin:15px 0px; padding:10px;"><b>INFORMASI PERMBONGKARAN MEDIA TANGKI</b></p>
                                            <p>TANGKI</p>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover tbl-tangki">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="5%">No</th>
                                                            <th class="text-center" width="15%">Tipe</th>
                                                            <th class="text-center" width="15%">Kapasitas</th>
                                                            <th class="text-center" width="12%">Jumlah</th>
                                                            <th class="text-center" width="15%">Produk</th>
                                                            <th class="text-center" width="15%">Inlet Pipa</th>
                                                            <th class="text-center" width="15%">Ukuran</th>
                                                            <th class="text-center" width="8%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $arT1 = array("Mobile", "Inline", "Underground", "Drum", "IBC");
                                                        $arT2 = array("HSD", "Bensin", "Oli", "Zat Kimia");
                                                        $arT3 = array("Manhole", "Pipa", "Camlock", "Flange");
                                                        $arT4 = array("1 In", "1.5 In", "2 In", "3 In");
                                                        $nom1 = 0;
                                                        foreach ($tangki as $data1) {
                                                            $nom1++;
                                                        ?>
                                                            <tr>
                                                                <td class="text-center"><span class="noTangki" data-row-count="<?php echo $nom1; ?>"><?php echo $nom1; ?></span></td>
                                                                <td class="text-left">
                                                                    <select name="tangki[tipe][]" id="<?php echo "tangkiTipe_" . $nom1; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data1['tipe'] == 'Mobile') ? ' selected' : ''; ?>>Mobile</option>
                                                                            <option<?php echo ($data1['tipe'] == 'Inline') ? ' selected' : ''; ?>>Inline</option>
                                                                                <option<?php echo ($data1['tipe'] == 'Underground') ? ' selected' : ''; ?>>Underground</option>
                                                                                    <option<?php echo ($data1['tipe'] == 'Drum') ? ' selected' : ''; ?>>Drum</option>
                                                                                        <option<?php echo ($data1['tipe'] == 'IBC') ? ' selected' : ''; ?>>IBC</option>
                                                                                            <?php
                                                                                            if ($data1['tipe'] && !in_array($data1['tipe'], $arT1))
                                                                                                echo '<option selected>' . $data1['tipe'] . '</option>';
                                                                                            ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" name="tangki[kapasitas][]" id="<?php echo "tangkiKapasitas_" . $nom1; ?>" class="form-control input-sm" value="<?php echo $data1['kapasitas'] ?? null; ?>" />
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" name="tangki[jumlah][]" id="<?php echo "tangkiJumlah_" . $nom1; ?>" class="form-control input-sm" value="<?php echo $data1['jumlah'] ?? null; ?>" />
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="tangki[produk][]" id="<?php echo "tangkiProduk_" . $nom1; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data1['produk'] == 'HSD') ? ' selected' : ''; ?>>HSD</option>
                                                                            <option<?php echo ($data1['produk'] == 'Bensin') ? ' selected' : ''; ?>>Bensin</option>
                                                                                <option<?php echo ($data1['produk'] == 'Oli') ? ' selected' : ''; ?>>Oli</option>
                                                                                    <option<?php echo ($data1['produk'] == 'Zat Kimia') ? ' selected' : ''; ?>>Zat Kimia</option>
                                                                                        <?php
                                                                                        if ($data1['produk'] && !in_array($data1['produk'], $arT2))
                                                                                            echo '<option selected>' . $data1['produk'] . '</option>';
                                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="tangki[inlet][]" id="<?php echo "tangkiInlet_" . $nom1; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data1['inlet'] == 'Camlock') ? ' selected' : ''; ?>>Camlock</option>
                                                                            <option<?php echo ($data1['inlet'] == 'Pipa') ? ' selected' : ''; ?>>Pipa</option>
                                                                                <option<?php echo ($data1['inlet'] == 'Manhole') ? ' selected' : ''; ?>>Manhole</option>
                                                                                    <option<?php echo ($data1['inlet'] == 'Flange') ? ' selected' : ''; ?>>Flange</option>
                                                                                        <?php
                                                                                        if ($data1['inlet'] && !in_array($data1['inlet'], $arT3))
                                                                                            echo '<option selected>' . $data1['inlet'] . '</option>';
                                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="tangki[ukuran][]" id="<?php echo "tangkiUkuran_" . $nom1; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data1['ukuran'] == '1 In') ? ' selected' : ''; ?>>1 In</option>
                                                                            <option<?php echo ($data1['ukuran'] == '1.5 In') ? ' selected' : ''; ?>>1.5 In</option>
                                                                                <option<?php echo ($data1['ukuran'] == '2 In') ? ' selected' : ''; ?>>2 In</option>
                                                                                    <option<?php echo ($data1['ukuran'] == '3 In') ? ' selected' : ''; ?>>3 In</option>
                                                                                        <?php
                                                                                        if ($data1['ukuran'] && !in_array($data1['ukuran'], $arT4))
                                                                                            echo '<option selected>' . $data1['ukuran'] . '</option>';
                                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
                                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <p>PENDUKUNG</p>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover tbl-support">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="5%">No</th>
                                                            <th class="text-center" width="15%">Pompa</th>
                                                            <th class="text-center" width="15%">Laju Aliran</th>
                                                            <th class="text-center" width="12%">P.Selang</th>
                                                            <th class="text-center" width="15%">Vapour Valve</th>
                                                            <th class="text-center" width="15%">Grounding</th>
                                                            <th class="text-center" width="15%">Sinyal HP</th>
                                                            <th class="text-center" width="8%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $arP1 = array("Pelanggan", "Transportir");
                                                        $arP2 = array("300 LPM", "500 LPM", "N/A");
                                                        $arP3 = array("5 M", "10 M", "15 M", "20 M");
                                                        $arP4 = array("Ada", "Tidak Ada");
                                                        $arP5 = array("Ada", "Tidak");
                                                        $arP6 = array("Telkomsel", "XL", "Indosat", "N/A");
                                                        $nom2 = 0;
                                                        foreach ($pendukung as $data2) {
                                                            $nom2++;
                                                        ?>
                                                            <tr>
                                                                <td class="text-center"><span class="noSupport" data-row-count="<?php echo $nom2; ?>"><?php echo $nom2; ?></span></td>
                                                                <td class="text-left">
                                                                    <select name="support[pompa][]" id="<?php echo "supportPompa_" . $nom2; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data2['pompa'] == 'Pelanggan') ? ' selected' : ''; ?>>Pelanggan</option>
                                                                            <option<?php echo ($data2['pompa'] == 'Transportir') ? ' selected' : ''; ?>>Transportir</option>
                                                                                <?php
                                                                                if ($data2['pompa'] && !in_array($data2['pompa'], $arP1))
                                                                                    echo '<option selected>' . $data2['pompa'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="support[aliran][]" id="<?php echo "supportAliran_" . $nom2; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data2['aliran'] == '300 LPM') ? ' selected' : ''; ?>>300 LPM</option>
                                                                            <option<?php echo ($data2['aliran'] == '500 LPM') ? ' selected' : ''; ?>>500 LPM</option>
                                                                                <option<?php echo ($data2['aliran'] == 'N/A') ? ' selected' : ''; ?>>N/A</option>
                                                                                    <?php
                                                                                    if ($data2['aliran'] && !in_array($data2['aliran'], $arP2))
                                                                                        echo '<option selected>' . $data2['aliran'] . '</option>';
                                                                                    ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="support[selang][]" id="<?php echo "supportSelang_" . $nom2; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data2['selang'] == '5 M') ? ' selected' : ''; ?>>5 M</option>
                                                                            <option<?php echo ($data2['selang'] == '10 M') ? ' selected' : ''; ?>>10 M</option>
                                                                                <option<?php echo ($data2['selang'] == '15 M') ? ' selected' : ''; ?>>15 M</option>
                                                                                    <option<?php echo ($data2['selang'] == '20 M') ? ' selected' : ''; ?>>20 M</option>
                                                                                        <?php
                                                                                        if ($data2['selang'] && !in_array($data2['selang'], $arP3))
                                                                                            echo '<option selected>' . $data2['selang'] . '</option>';
                                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="support[valve][]" id="<?php echo "supportValve_" . $nom2; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data2['valve'] == 'Ada') ? ' selected' : ''; ?>>Ada</option>
                                                                            <option<?php echo ($data2['valve'] == 'Tidak Ada') ? ' selected' : ''; ?>>Tidak Ada</option>
                                                                                <?php
                                                                                if ($data2['valve'] && !in_array($data2['valve'], $arP4))
                                                                                    echo '<option selected>' . $data2['valve'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="support[grounding][]" id="<?php echo "supportGrounding_" . $nom2; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data2['ground'] == 'Ada') ? ' selected' : ''; ?>>Ada</option>
                                                                            <option<?php echo ($data2['ground'] == 'Tidak') ? ' selected' : ''; ?>>Tidak</option>
                                                                                <?php
                                                                                if ($data2['ground'] && !in_array($data2['ground'], $arP5))
                                                                                    echo '<option selected>' . $data2['ground'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="support[sinyal][]" id="<?php echo "supportSinyal_" . $nom2; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data2['sinyal'] == 'Telkomsel') ? ' selected' : ''; ?>>Telkomsel</option>
                                                                            <option<?php echo ($data2['sinyal'] == 'XL') ? ' selected' : ''; ?>>XL</option>
                                                                                <option<?php echo ($data2['sinyal'] == 'Indosat') ? ' selected' : ''; ?>>Indosat</option>
                                                                                    <option<?php echo ($data2['sinyal'] == 'N/A') ? ' selected' : ''; ?>>N/A</option>
                                                                                        <?php
                                                                                        if ($data2['sinyal'] && !in_array($data2['sinyal'], $arP6))
                                                                                            echo '<option selected>' . $data2['sinyal'] . '</option>';
                                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
                                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <p>QUANTITY</p>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover tbl-kuantitas1">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="5%">No</th>
                                                            <th class="text-center" width="15%">Alat Ukur</th>
                                                            <th class="text-center" width="15%">Merk</th>
                                                            <th class="text-center" width="12%">Tera</th>
                                                            <th class="text-center" width="15%">Masa Berlaku</th>
                                                            <th class="text-center" width="30%">Flowmeter tiap Pengiriman</th>
                                                            <th class="text-center" width="8%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $arA1 = array("Jembatan Timbang", "TUM", "Flow Meter");
                                                        $arA2 = array("LC M10", "Tokico");
                                                        $arA3 = array("Ada", "Tidak");
                                                        $arA4 = array("Berlaku", "Kadaluarsa");
                                                        $arA5 = array("Ya", "Tidak");
                                                        $arA6 = array("Tanki Darat", "Tanki Kapal", "FLow Meter");
                                                        $nom3 = 0;
                                                        foreach ($kuantitas1 as $data3) {
                                                            $nom3++;
                                                        ?>
                                                            <tr>
                                                                <td class="text-center"><span class="noKuantitas1" data-row-count="<?php echo $nom3; ?>"><?php echo $nom3; ?></span></td>
                                                                <td class="text-left">
                                                                    <select name="kuantitas1[alat][]" id="<?php echo "kuantitas1Alat_" . $nom3; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data3['alat'] == 'Jembatan Timbang') ? ' selected' : ''; ?>>Jembatan Timbang</option>
                                                                            <option<?php echo ($data3['alat'] == 'TUM') ? ' selected' : ''; ?>>TUM</option>
                                                                                <option<?php echo ($data3['alat'] == 'Flowmeter') ? ' selected' : ''; ?>>Flowmeter</option>
                                                                                    <?php
                                                                                    if ($data3['alat'] && !in_array($data3['alat'], $arA1))
                                                                                        echo '<option selected>' . $data3['alat'] . '</option>';
                                                                                    ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kuantitas1[merk][]" id="<?php echo "kuantitas1Merk_" . $nom3; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data3['merk'] == 'LC M10') ? ' selected' : ''; ?>>LC M10</option>
                                                                            <option<?php echo ($data3['merk'] == 'Tokico') ? ' selected' : ''; ?>>Tokico</option>
                                                                                <?php
                                                                                if ($data3['merk'] && !in_array($data3['merk'], $arA2))
                                                                                    echo '<option selected>' . $data3['merk'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kuantitas1[tera][]" id="<?php echo "kuantitas1Tera_" . $nom3; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data3['tera'] == 'Ada') ? ' selected' : ''; ?>>Ada</option>
                                                                            <option<?php echo ($data3['tera'] == 'Tidak') ? ' selected' : ''; ?>>Tidak</option>
                                                                                <?php
                                                                                if ($data3['tera'] && !in_array($data3['tera'], $arA3))
                                                                                    echo '<option selected>' . $data3['tera'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kuantitas1[masa][]" id="<?php echo "kuantitas1Masa_" . $nom3; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data3['masa'] == 'Berlaku') ? ' selected' : ''; ?>>Berlaku</option>
                                                                            <option<?php echo ($data3['masa'] == 'Kadaluarsa') ? ' selected' : ''; ?>>Kadaluarsa</option>
                                                                                <?php
                                                                                if ($data3['masa'] && !in_array($data3['masa'], $arA4))
                                                                                    echo '<option selected>' . $data3['masa'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kuantitas1[flowmeter][]" id="<?php echo "kuantitas1Flowmeter_" . $nom3; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data3['flowmeter'] == 'Ya') ? ' selected' : ''; ?>>Ya</option>
                                                                            <option<?php echo ($data3['flowmeter'] == 'Tidak') ? ' selected' : ''; ?>>Tidak</option>
                                                                                <?php
                                                                                if ($data3['flowmeter'] && !in_array($data3['flowmeter'], $arA5))
                                                                                    echo '<option selected>' . $data3['flowmeter'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
                                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <p>QUALITY</p>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover tbl-kualitas1">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="5%">No</th>
                                                            <th class="text-center" width="15%">Min. Spec.</th>
                                                            <th class="text-center" width="15%">Uji Lab</th>
                                                            <th class="text-center" width="27%">COQ Tiap Permintaan</th>
                                                            <th class="text-center" width="30%">&nbsp;</th>
                                                            <th class="text-center" width="8%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $arB1 = array("Migas");
                                                        $arB2 = array("Ya", "Tidak");
                                                        $arB3 = array("Ya", "Tidak");
                                                        $nom4 = 0;
                                                        foreach ($kualitas1 as $data4) {
                                                            $nom4++;
                                                        ?>
                                                            <tr>
                                                                <td class="text-center"><span class="noKualitas1" data-row-count="<?php echo $nom4; ?>"><?php echo $nom4; ?></span></td>
                                                                <td class="text-left">
                                                                    <select name="kualitas1[spec][]" id="<?php echo "kualitas1Spec_" . $nom4; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data4['spec'] == 'Migas') ? ' selected' : ''; ?>>Migas</option>
                                                                            <?php
                                                                            if ($data4['spec'] && !in_array($data4['spec'], $arB1))
                                                                                echo '<option selected>' . $data4['spec'] . '</option>';
                                                                            ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kualitas1[lab][]" id="<?php echo "kualitas1Lab_" . $nom4; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data4['lab'] == 'Ya') ? ' selected' : ''; ?>>Ya</option>
                                                                            <option<?php echo ($data4['lab'] == 'Tidak') ? ' selected' : ''; ?>>Tidak</option>
                                                                                <?php
                                                                                if ($data4['lab'] && !in_array($data4['lab'], $arB2))
                                                                                    echo '<option selected>' . $data4['lab'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kualitas1[coq][]" id="<?php echo "kualitas1Coq_" . $nom4; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data4['coq'] == 'Ya') ? ' selected' : ''; ?>>Ya</option>
                                                                            <option<?php echo ($data4['coq'] == 'Tidak') ? ' selected' : ''; ?>>Tidak</option>
                                                                                <?php
                                                                                if ($data4['coq'] && !in_array($data4['coq'], $arB3))
                                                                                    echo '<option selected>' . $data4['coq'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">&nbsp;</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
                                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <label>Catatan</label>
                                                    <textarea name="catatan_tangki" id="catatan_tangki" class="form-control"><?php echo isset($rsm['catatan_tangki']) ? str_replace('<br />', PHP_EOL, $rsm['catatan_tangki']) : null; ?></textarea>
                                                </div>
                                            </div>

                                            <p style="border:1px solid #ccc; background-color:#ddd; margin:15px 0px; padding:10px;"><b>INFORMASI PERMBONGKARAN MEDIA KAPAL</b></p>
                                            <p>KAPAL</p>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover tbl-kapal">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="5%">No</th>
                                                            <th class="text-center" width="15%">Tipe</th>
                                                            <th class="text-center" width="15%">Kapasitas</th>
                                                            <th class="text-center" width="12%">Jumlah</th>
                                                            <th class="text-center" width="15%">Inlet Pipa</th>
                                                            <th class="text-center" width="15%">Ukuran</th>
                                                            <th class="text-center" width="15%">Metode</th>
                                                            <th class="text-center" width="8%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $arK1 = array("Tugboat", "SPOB", "Tanker");
                                                        $arK2 = array("Manhole", "Pipa", "Camlock", "Flange");
                                                        $arK3 = array("1 In", "1.5 In", "2 In", "3 In");
                                                        $arK4 = array("STS", "Truck to Ship");
                                                        $nom5 = 0;
                                                        foreach ($kapal as $data5) {
                                                            $nom5++;
                                                        ?>
                                                            <tr>
                                                                <td class="text-center"><span class="noKapal" data-row-count="<?php echo $nom5; ?>"><?php echo $nom5; ?></span></td>
                                                                <td class="text-left">
                                                                    <select name="kapal[tipe][]" id="<?php echo "kapalTipe_" . $nom5; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data5['tipe'] == 'Tugboat') ? ' selected' : ''; ?>>Tugboat</option>
                                                                            <option<?php echo ($data5['tipe'] == 'SPOB') ? ' selected' : ''; ?>>SPOB</option>
                                                                                <option<?php echo ($data5['tipe'] == 'Tanker') ? ' selected' : ''; ?>>Tanker</option>
                                                                                    <?php
                                                                                    if ($data5['tipe'] && !in_array($data5['tipe'], $arK1))
                                                                                        echo '<option selected>' . $data5['tipe'] . '</option>';
                                                                                    ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" name="kapal[kapasitas][]" id="<?php echo "kapalKapasitas_" . $nom5; ?>" class="form-control input-sm" value="<?php echo $data5['kapasitas'] ?? null; ?>" />
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" name="kapal[jumlah][]" id="<?php echo "kapalJumlah_" . $nom5; ?>" class="form-control input-sm" value="<?php echo $data5['jumlah'] ?? null; ?>" />
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kapal[inlet][]" id="<?php echo "kapalInlet_" . $nom5; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data5['inlet'] == 'Manhole') ? ' selected' : ''; ?>>Manhole</option>
                                                                            <option<?php echo ($data5['inlet'] == 'Pipa') ? ' selected' : ''; ?>>Pipa</option>
                                                                                <option<?php echo ($data5['inlet'] == 'Camlock') ? ' selected' : ''; ?>>Camlock</option>
                                                                                    <option<?php echo ($data5['inlet'] == 'Flange') ? ' selected' : ''; ?>>Flange</option>
                                                                                        <?php
                                                                                        if ($data5['inlet'] && !in_array($data5['inlet'], $arK2))
                                                                                            echo '<option selected>' . $data5['inlet'] . '</option>';
                                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kapal[ukuran][]" id="<?php echo "kapalUkuran_" . $nom5; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data5['ukuran'] == '1 In') ? ' selected' : ''; ?>>1 In</option>
                                                                            <option<?php echo ($data5['ukuran'] == '1.5 In') ? ' selected' : ''; ?>>1.5 In</option>
                                                                                <option<?php echo ($data5['ukuran'] == '2 In') ? ' selected' : ''; ?>>2 In</option>
                                                                                    <option<?php echo ($data5['ukuran'] == '3 In') ? ' selected' : ''; ?>>3 In</option>
                                                                                        <?php
                                                                                        if ($data5['ukuran'] && !in_array($data5['ukuran'], $arK3))
                                                                                            echo '<option selected>' . $data5['ukuran'] . '</option>';
                                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kapal[metode][]" id="<?php echo "kapalMetode_" . $nom5; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data5['metode'] == 'STS') ? ' selected' : ''; ?>>STS</option>
                                                                            <option<?php echo ($data5['metode'] == 'Truck to Ship') ? ' selected' : ''; ?>>Truck to Ship</option>
                                                                                <?php
                                                                                if ($data5['metode'] && !in_array($data5['metode'], $arK4))
                                                                                    echo '<option selected>' . $data5['metode'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
                                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <p>JETTY</p>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover tbl-jetty">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="5%">No</th>
                                                            <th class="text-center" width="15%">Max LOA</th>
                                                            <th class="text-center" width="15%">Min PBL</th>
                                                            <th class="text-center" width="12%">Draft (LWS)</th>
                                                            <th class="text-center" width="15%">Kekuatan (DWT)</th>
                                                            <th class="text-center" width="15%">Izin</th>
                                                            <th class="text-center" width="15%">Persyaratan</th>
                                                            <th class="text-center" width="8%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $arJ1 = array("50 M", "100 M", "N/A");
                                                        $arJ2 = array("20 M", "30 M", "40 M", "N/A");
                                                        $arJ3 = array("Max 5.000", "Max 10.000");
                                                        $arJ4 = array("Telsus", "TUKS", "N/A");
                                                        $arJ5 = array("Q88", "Depot Approval", "PSA",);
                                                        $nom6 = 0;
                                                        foreach ($jetty as $data6) {
                                                            $nom6++;
                                                        ?>
                                                            <tr>
                                                                <td class="text-center"><span class="noJetty" data-row-count="<?php echo $nom6; ?>"><?php echo $nom6; ?></span></td>
                                                                <td class="text-left">
                                                                    <select name="jetty[loa][]" id="<?php echo "jettyLoa_" . $nom6; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data6['loa'] == '50 M') ? ' selected' : ''; ?>>50 M</option>
                                                                            <option<?php echo ($data6['loa'] == '100 M') ? ' selected' : ''; ?>>100 M</option>
                                                                                <option<?php echo ($data6['loa'] == 'N/A') ? ' selected' : ''; ?>>N/A</option>
                                                                                    <?php
                                                                                    if ($data6['loa'] && !in_array($data6['loa'], $arJ1))
                                                                                        echo '<option selected>' . $data6['loa'] . '</option>';
                                                                                    ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="jetty[pbl][]" id="<?php echo "jettyPbl_" . $nom6; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data6['pbl'] == '20 M') ? ' selected' : ''; ?>>20 M</option>
                                                                            <option<?php echo ($data6['pbl'] == '30 M') ? ' selected' : ''; ?>>30 M</option>
                                                                                <option<?php echo ($data6['pbl'] == '40 M') ? ' selected' : ''; ?>>40 M</option>
                                                                                    <option<?php echo ($data6['pbl'] == 'N/A') ? ' selected' : ''; ?>>N/A</option>
                                                                                        <?php
                                                                                        if ($data6['pbl'] && !in_array($data6['pbl'], $arJ2))
                                                                                            echo '<option selected>' . $data6['pbl'] . '</option>';
                                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <input type="text" name="jetty[lws][]" id="<?php echo "jettyLws_" . $nom6; ?>" class="form-control input-sm" value="<?php echo $data6['lws'] ?? null; ?>" />
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="jetty[sandar][]" id="<?php echo "jettySandar_" . $nom6; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data6['sandar'] == 'Max 5.000') ? ' selected' : ''; ?>>Max 5.000</option>
                                                                            <option<?php echo ($data6['sandar'] == 'Max 10.000') ? ' selected' : ''; ?>>Max 10.000</option>
                                                                                <?php
                                                                                if ($data6['sandar'] && !in_array($data6['sandar'], $arJ3))
                                                                                    echo '<option selected>' . $data6['sandar'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="jetty[izin][]" id="<?php echo "jettyIzin_" . $nom6; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data6['izin'] == 'Telsus') ? ' selected' : ''; ?>>Telsus</option>
                                                                            <option<?php echo ($data6['izin'] == 'TUKS') ? ' selected' : ''; ?>>TUKS</option>
                                                                                <option<?php echo ($data6['izin'] == 'N/A') ? ' selected' : ''; ?>>N/A</option>
                                                                                    <?php
                                                                                    if ($data6['izin'] && !in_array($data6['izin'], $arJ4))
                                                                                        echo '<option selected>' . $data6['izin'] . '</option>';
                                                                                    ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="jetty[syarat][]" id="<?php echo "jettySyarat_" . $nom6; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data6['syarat'] == 'Q88') ? ' selected' : ''; ?>>Q88</option>
                                                                            <option<?php echo ($data6['syarat'] == 'Depot Approval') ? ' selected' : ''; ?>>Depot Approval</option>
                                                                                <option<?php echo ($data6['syarat'] == 'PSA') ? ' selected' : ''; ?>>PSA</option>
                                                                                    <?php
                                                                                    if ($data6['syarat'] && !in_array($data6['syarat'], $arJ5))
                                                                                        echo '<option selected>' . $data6['syarat'] . '</option>';
                                                                                    ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
                                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <p>QUANTITY</p>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover tbl-kuantitas2">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="5%">No</th>
                                                            <th class="text-center" width="15%">Alat Ukur</th>
                                                            <th class="text-center" width="15%">Merk</th>
                                                            <th class="text-center" width="12%">Tera</th>
                                                            <th class="text-center" width="15%">Masa Berlaku</th>
                                                            <th class="text-center" width="30%">Flowmeter tiap Pengiriman</th>
                                                            <th class="text-center" width="8%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $nom7 = 0;
                                                        foreach ($kuantitas2 as $data7) {
                                                            $nom7++;
                                                        ?>
                                                            <tr>
                                                                <td class="text-center"><span class="noKuantitas2" data-row-count="<?php echo $nom7; ?>"><?php echo $nom7; ?></span></td>
                                                                <td class="text-left">
                                                                    <select name="kuantitas2[alat][]" id="<?php echo "kuantitas2Alat_" . $nom7; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data7['alat'] == 'Tanki Darat') ? ' selected' : ''; ?>>Tanki Darat</option>
                                                                            <option<?php echo ($data7['alat'] == 'Tanki Kapal') ? ' selected' : ''; ?>>Tanki Kapal</option>
                                                                                <option<?php echo ($data7['alat'] == 'Flow Meter') ? ' selected' : ''; ?>>Flow Meter</option>
                                                                                    <?php
                                                                                    if ($data7['alat'] && !in_array($data7['alat'], $arA6))
                                                                                        echo '<option selected>' . $data7['alat'] . '</option>';
                                                                                    ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kuantitas2[merk][]" id="<?php echo "kuantitas2Merk_" . $nom7; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data7['merk'] == 'LC M10') ? ' selected' : ''; ?>>LC M10</option>
                                                                            <option<?php echo ($data7['merk'] == 'Tokico') ? ' selected' : ''; ?>>Tokico</option>
                                                                                <?php
                                                                                if ($data7['merk'] && !in_array($data7['merk'], $arA2))
                                                                                    echo '<option selected>' . $data7['merk'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kuantitas2[tera][]" id="<?php echo "kuantitas2Tera_" . $nom7; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data7['tera'] == 'Ada') ? ' selected' : ''; ?>>Ada</option>
                                                                            <option<?php echo ($data7['tera'] == 'Tidak') ? ' selected' : ''; ?>>Tidak</option>
                                                                                <?php
                                                                                if ($data7['tera'] && !in_array($data7['tera'], $arA3))
                                                                                    echo '<option selected>' . $data7['tera'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kuantitas2[masa][]" id="<?php echo "kuantitas2Masa_" . $nom7; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data7['masa'] == 'Berlaku') ? ' selected' : ''; ?>>Berlaku</option>
                                                                            <option<?php echo ($data7['masa'] == 'Kadaluarsa') ? ' selected' : ''; ?>>Kadaluarsa</option>
                                                                                <?php
                                                                                if ($data7['masa'] && !in_array($data7['masa'], $arA4))
                                                                                    echo '<option selected>' . $data7['masa'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kuantitas2[flowmeter][]" id="<?php echo "kuantitas2Flowmeter_" . $nom7; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data7['flowmeter'] == 'Ya') ? ' selected' : ''; ?>>Ya</option>
                                                                            <option<?php echo ($data7['flowmeter'] == 'Tidak') ? ' selected' : ''; ?>>Tidak</option>
                                                                                <?php
                                                                                if ($data7['flowmeter'] && !in_array($data7['flowmeter'], $arA5))
                                                                                    echo '<option selected>' . $data7['flowmeter'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
                                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <p>QUALITY</p>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover tbl-kualitas2">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="5%">No</th>
                                                            <th class="text-center" width="15%">Min. Spec.</th>
                                                            <th class="text-center" width="15%">Uji Lab</th>
                                                            <th class="text-center" width="27%">COQ tiap Pengiriman</th>
                                                            <th class="text-center" width="30%">&nbsp;</th>
                                                            <th class="text-center" width="8%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $nom8 = 0;
                                                        foreach ($kualitas2 as $data8) {
                                                            $nom8++;
                                                        ?>
                                                            <tr>
                                                                <td class="text-center"><span class="noKualitas2" data-row-count="<?php echo $nom8; ?>"><?php echo $nom8; ?></span></td>
                                                                <td class="text-left">
                                                                    <select name="kualitas2[spec][]" id="<?php echo "kualitas2Spec_" . $nom8; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data8['spec'] == 'Migas') ? ' selected' : ''; ?>>Migas</option>
                                                                            <?php
                                                                            if ($data8['spec'] && !in_array($data8['spec'], $arB1))
                                                                                echo '<option selected>' . $data8['spec'] . '</option>';
                                                                            ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kualitas2[lab][]" id="<?php echo "kualitas2Lab_" . $nom8; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data8['lab'] == 'Ya') ? ' selected' : ''; ?>>Ya</option>
                                                                            <option<?php echo ($data8['lab'] == 'Tidak') ? ' selected' : ''; ?>>Tidak</option>
                                                                                <?php
                                                                                if ($data8['lab'] && !in_array($data8['lab'], $arB2))
                                                                                    echo '<option selected>' . $data8['lab'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">
                                                                    <select name="kualitas2[coq][]" id="<?php echo "kualitas2Coq_" . $nom8; ?>" class="form-control select-other">
                                                                        <option></option>
                                                                        <option<?php echo ($data8['coq'] == 'Ya') ? ' selected' : ''; ?>>Ya</option>
                                                                            <option<?php echo ($data8['coq'] == 'Tidak') ? ' selected' : ''; ?>>Tidak</option>
                                                                                <?php
                                                                                if ($data8['coq'] && !in_array($data8['coq'], $arB3))
                                                                                    echo '<option selected>' . $data8['coq'] . '</option>';
                                                                                ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-left">&nbsp;</td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
                                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <label>Catatan</label>
                                                    <textarea name="catatan_kapal" id="catatan_kapal" class="form-control"><?php echo isset($rsm['catatan_kapal']) ? str_replace('<br />', PHP_EOL, $rsm['catatan_kapal']) : null; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <div class="pad bg-gray">
                                        <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                        <input type="hidden" name="idk" value="<?php echo $idk; ?>" />
                                        <a href="<?php echo $kembali; ?>" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                        <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt" <?= $action == 'update' ? '' : 'disabled' ?>>
                                            <i class="fa fa-floppy-o jarak-kanan"></i>Save
                                        </button>&nbsp;
                                        <br>
                                        <br>
                                        <span style="font-weight: bold;"> Harap mengisi data LCR ini dengan baik dan benar.</span>
                                        <br>
                                        <span style="font-weight: bold;"> Jika button save terkunci, pastikan Anda melakukan preview map terlebih dahulu.</span>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin:5px 0" />
                            <div class="clearfix">
                                <div class="col-sm-12"><small>* Wajib Diisi</small></div>
                            </div>
                        </form>


                    </div>

                    <div role="tabpanel" class="tab-pane" id="gambar-lcr">
                        <?php if ($idk) { ?>
                            <script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/file-upload/main.js"; ?>"></script>
                            <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
                                <div class="slides"></div>
                                <h3 class="title"></h3>
                                <a class="prev"><i class="fa fa-angle-left"></i></a>
                                <a class="next"><i class="fa fa-angle-right"></i></a>
                                <a class="close"><i class="fa fa-times"></i></a>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="box box-purple">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Keterangan Gambar <small style="font-size:12px;">Max. Size: 2MB | Ext (.jpg, .png)</small></h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div style="margin-bottom:15px;">
                                                        <h3 class="form-title">Peta <small style="font-size:12px;"><i>Recomended Landscape Image</i></small></h3>
                                                        <form id="petafile" name="petafile" method="post" enctype="multipart/form-data">
                                                            <div class="row fileupload-buttonbar">
                                                                <div class="col-sm-12">
                                                                    <div style="margin-bottom:10px;">
                                                                        <span class="btn btn-success btn-sm fileinput-button">
                                                                            <i class="fa fa-plus jarak-kanan"></i> Browse files
                                                                            <input type="file" name="files">
                                                                        </span>
                                                                        <span class="fileupload-process"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-center" width="35%">Gambar</th>
                                                                            <th class="text-center" width="65%">Info Gambar</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="files"></tbody>
                                                                </table>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div style="margin-bottom:15px;">
                                                        <h3 class="form-title">Rute Pembongkaran <small style="font-size:12px;"><i>Recomended Landscape Image</i></small></h3>
                                                        <form id="bongkarfile" name="bongkarfile" method="post" enctype="multipart/form-data">
                                                            <div class="row fileupload-buttonbar">
                                                                <div class="col-sm-12">
                                                                    <div style="margin-bottom:10px;">
                                                                        <span class="btn btn-success btn-sm fileinput-button">
                                                                            <i class="fa fa-plus jarak-kanan"></i> Browse files
                                                                            <input type="file" name="files">
                                                                        </span>
                                                                        <span class="fileupload-process"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-center" width="35%">Gambar</th>
                                                                            <th class="text-center" width="65%">Info Gambar</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="files"></tbody>
                                                                </table>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr style="margin:0px -10px 20px; border-color:#ddd;" />


                                            <?php
                                            $arrKtgFile = array(
                                                "jalan" => array("title" => "Kondisi Jalan Menuju Lokasi", "form" => "jalanfile"),
                                                "kantor" => array("title" => "Pintu Gerbang &amp; Kantor Perusahaan", "form" => "kantorfile"),
                                                "storage" => array("title" => "Fasilitas Penyimpanan", "form" => "storagefile"),
                                                "inlet" => array("title" => "Inlet Pipa", "form" => "inletfile"),
                                                "ukur" => array("title" => "Alat Ukur", "form" => "ukurfile"),
                                                "media" => array("title" => "Media Datar", "form" => "mediafile"),
                                                "keterangan" => array("title" => "Keterangan Penunjang Lain", "form" => "keteranganfile")
                                            );
                                            foreach ($arrKtgFile as $idf => $vaf) {
                                            ?>
                                                <h3 class="form-title"><?php echo $vaf['title']; ?></h3>
                                                <form id="<?php echo $vaf['form']; ?>" name="<?php echo $vaf['form']; ?>" method="post" enctype="multipart/form-data">
                                                    <div class="row fileupload-buttonbar">
                                                        <div class="col-sm-12">
                                                            <div style="margin-bottom:10px;">
                                                                <span class="btn btn-success btn-sm fileinput-button">
                                                                    <i class="fa fa-plus jarak-kanan"></i> Add files
                                                                    <input type="file" name="files[]" multiple>
                                                                </span>
                                                                <span class="fileupload-process"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center" width="15%">Gambar</th>
                                                                    <th class="text-center" width="35%">Info Gambar</th>
                                                                    <th class="text-center" width="30%">Keterangan Gambar</th>
                                                                    <th class="text-center" width="20%">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="files"></tbody>
                                                        </table>
                                                    </div>
                                                </form>
                                                <hr style="margin:0px -10px 20px; border-color:#ddd;" />
                                            <?php } ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } else echo '<p class="pad text-center">Gambar dapat diupload jika data di form lcr telah disimpan</p>'; ?>
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

        #map_canvas {
            border: 1px solid #ddd;
            height: 400px;
        }

        .bg-light-purple {
            background-color: #56386a;
            color: #f9f9f9 !important;
        }

        .box.box-purple {
            border-top-color: #56386a;

        }
    </style>
    <script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS . "/jquery.lcr.js"; ?>"></script>
    <script type="text/javascript">
        //$('.phone-number').mask('00000000000000')
        //$('.fax').mask('00000000000000')
    </script>
</body>

</html>