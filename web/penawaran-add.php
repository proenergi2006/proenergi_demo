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
$sesuser = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesgroup = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);

$id_cabang = null;
if ($idk != "") {
    $sql = "select a.*, if(b.kode_pelanggan = '',b.nama_customer,concat(b.kode_pelanggan,' - ',b.nama_customer)) as nm_customer, b.status_customer, b.top_payment, 
				b.jenis_payment as jenis_waktu, c.nama_cabang, d.nama_area 
				from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join pro_master_cabang c on a.id_cabang = c.id_master 
				join pro_master_area d on a.id_area = d.id_master where a.id_penawaran = '" . $idk . "' and a.id_customer = '" . $idr . "'";
    $rsm = $con->getRecord($sql);
    //other cost iwan* 
    $sql_other_cost = "select * from pro_other_cost_detail WHERE id_penawaran = '" . $idk . "'";
    $other_coast = $con->getResult($sql_other_cost);
    //akhiran 

    $action     = "update";
    $titleAct     = "Ubah Penawaran";
    $rincian     = (json_decode($rsm['detail_rincian'], true) === NULL) ? array(1) : json_decode($rsm['detail_rincian'], true);
    $formula     = (json_decode($rsm['detail_formula'], true) === NULL || count(json_decode($rsm['detail_formula'], true)) == 0) ? array("") : json_decode($rsm['detail_formula'], true);
    $harga         = $rsm['harga_dasar'];
    $cara_order = $rsm['method_order'];
    $vol_tawar     = $rsm['volume_tawar'];
    $id_cabang     = $rsm['id_cabang'];
    $nm_cabang     = $rsm['nama_cabang'];
    $pembulatan = $rsm['pembulatan'];
    $gabung_pbbkb = $rsm['gabung_pbbkb'];
    $gabung_pbbkboa = $rsm['gabung_pbbkboa'];
    $harga_tier = $rsm['harga_tier'];

    $tmp_calc     = (json_decode($rsm['kalkulasi_oa'], true) === NULL) ? array(1) : json_decode($rsm['kalkulasi_oa'], true);
    $calcoa1     = $tmp_calc[0]['transportir'];
    $calcoa2     = $tmp_calc[0]['wiloa_po'];
    $calcoa3     = $tmp_calc[0]['voloa_po'];
    $calcoa4     = $tmp_calc[0]['ongoa_po'];
} else {
    $rsm         = array();
    $action     = "add";
    $titleAct     = "Tambah Penawaran";
    $rincian    = array(array("rincian" => "Harga Dasar"), array("rincian" => "Ongkos Angkut"), array("rincian" => "PPN", "nilai" => "11"), array("rincian" => "PBBKB"));
    $formula     = array("");
    $other_coast  = array("");
    $cara_order = 2;
    $vol_tawar     = "";
    if ($idc) {
        $sql = "select a.id_wilayah, b.nama_cabang from pro_customer a left join pro_master_cabang b on a.id_wilayah = b.id_master where a.id_customer = '" . $idc . "'";
        $rsm = $con->getRecord($sql);
        $id_cabang     = $rsm['id_wilayah'];
        $nm_cabang     = $rsm['nama_cabang'];
    }
    $pembulatan = 1;
    $calcoa1     = "";
    $calcoa2     = "";
    $calcoa3     = "";
    $calcoa4     = "";
}
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
                <h1><?php echo $titleAct; ?></h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <form action="<?php echo ACTION_CLIENT . '/penawaran.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                        </div>
                        <div class="box-body">
                            <?php if (isset($rsm['flag_disposisi']) || isset($rsm['flag_approval'])) {
                                $reFlag = 1; ?>
                                <div style="padding:15px; margin-bottom:15px; background-color:#00a7d0; color:#fff;">
                                    PERHATIAN!! Merubah data ini akan mengulang proses persetujuan data penawaran
                                </div>
                            <?php } ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Customer *</label>
                                        <div class="col-md-8">
                                            <?php
                                            if ($action == "add") {
                                                $where = "where id_marketing = '" . $sesuser . "'";
                                                if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 18) {
                                                    $where = "where 1=1";
                                                    if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
                                                        $where = "where (id_wilayah = '" . $seswil . "' or id_marketing = '" . $sesuser . "')";
                                                    else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
                                                        $where = "where (id_group = '" . $sesgroup . "' or id_marketing = '" . $sesuser . "')";
                                                }
                                            ?>
                                                <select name="idr" id="idr" class="form-control select2" required>
                                                    <option></option>
                                                    <?php $con->fill_select("id_customer", "if(kode_pelanggan = '',nama_customer,concat(kode_pelanggan,' - ',nama_customer))", "pro_customer", $idc, $where, "id_customer desc, nama", false); ?>
                                                </select>
                                                <p id="infoin" style="margin-bottom:0px;" class="help-block"></p>
                                            <?php } else if ($action == "update") { ?>
                                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                                <input type="text" id="nm_customer_txt" name="nm_customer_txt" class="form-control" value="<?php echo $rsm['nm_customer']; ?>" readonly />
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Area *</label>
                                        <div class="col-md-8">
                                            <?php if ($action == "add") { ?>
                                                <select name="area" id="area" class="form-control select2" required>
                                                    <option></option>
                                                    <?php $con->fill_select("id_master", "nama_area", "pro_master_area", "", "where is_active=1", "nama_area", false); ?>
                                                </select>
                                            <?php } else if ($action == "update") { ?>
                                                <input type="hidden" name="area" id="area" value="<?php echo $rsm['id_area']; ?>" />
                                                <input type="text" id="nama_area_txt" name="nama_area_txt" class="form-control" value="<?php echo $rsm['nama_area']; ?>" readonly />
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Cabang Invoice *</label>
                                        <div class="col-md-8">
                                            <div id="wrCabang">
                                                <?php if ($id_cabang) { ?>
                                                    <input type="hidden" name="cabang" id="cabang" value="<?php echo $id_cabang; ?>" />
                                                    <input type="text" id="nm_cabang_txt" name="nm_cabang_txt" class="form-control" value="<?php echo $nm_cabang; ?>" readonly />
                                                <?php } else { ?>
                                                    <select name="cabang" id="cabang" class="form-control" required>
                                                        <option></option>
                                                        <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $id_cabang, "where is_active=1 and id_master <> 1", "", false); ?>
                                                    </select>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                            <p id="pemberitahuan" class="infoMinyak"></p>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Masa berlaku harga *</label>
                                        <div class="col-md-6">
                                            <input type="text" id="masa_awal" name="masa_awal" class="form-control datepicker" required data-rule-dateNL="1" value="<?php echo isset($rsm['masa_awal']) ? tgl_indo($rsm['masa_awal'], 'normal', 'db', '/') : ''; ?>" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Sampai dengan *</label>
                                        <div class="col-md-6">
                                            <input type="text" id="masa_akhir" name="masa_akhir" class="form-control datepicker" required data-rule-dateNL="1" value="<?php echo isset($rsm['masa_akhir']) ? tgl_indo($rsm['masa_akhir'], 'normal', 'db', '/') : ''; ?>" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Produk *</label>
                                        <div class="col-md-6">
                                            <select name="produk_tawar" id="produk_tawar" class="form-control select2" required>
                                                <option></option>
                                                <?php $con->fill_select("id_master", "concat(jenis_produk,' - ',merk_dagang)", "pro_master_produk", $rsm['produk_tawar'], "where is_active =1", "id_master", false); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">PBBKB *</label>
                                        <div class="col-md-6">
                                            <select name="pbbkb_tawar" id="pbbkb_tawar" class="form-control select2" required>
                                                <option></option>
                                                <?php $con->fill_select("id_master", "concat(nilai_pbbkb, ' %')", "pro_master_pbbkb", $rsm['pbbkb_tawar'], "", "", false); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Kepada *</label>
                                        <div class="col-md-8">
                                            <select name="gelar" id="gelar" class="form-control select2" required>
                                                <option></option>
                                                <option value="Bapak" <?php echo (isset($rsm['gelar']) && $rsm['gelar'] == 'Bapak') ? ' selected' : ''; ?>>Bapak</option>
                                                <option value="Ibu" <?php echo (isset($rsm['gelar']) && $rsm['gelar'] == 'Ibu') ? ' selected' : ''; ?>>Ibu</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Nama *</label>
                                        <div class="col-md-8">
                                            <input type="text" id="nama_up" name="nama_up" class="form-control" required value="<?php echo $rsm['nama_up'] ?? null; ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Jabatan</label>
                                        <div class="col-md-8">
                                            <input type="text" name="jabatan_up" id="jabatan_up" class="form-control" value="<?php echo $rsm['jabatan_up'] ?? null; ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Alamat Korespondensi</label>
                                        <div class="col-md-8">
                                            <input type="text" name="alamat_up" id="alamat_up" class="form-control" value="<?php echo $rsm['alamat_up'] ?? null; ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Telepon</label>
                                        <div class="col-md-8">
                                            <input type="text" name="telp_up" id="telp_up" class="form-control" value="<?php echo $rsm['telp_up']; ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Fax</label>
                                        <div class="col-md-8">
                                            <input type="text" name="fax_up" id="fax_up" class="form-control" value="<?php echo $rsm['fax_up']; ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Tipe Pembayaran *</label>
                                        <div class="col-md-6">
                                            <input type="hidden" name="jenis_waktu" id="jenis_waktu" value="" />
                                            <?php if (isset($rsm['status_customer']) && $rsm['status_customer'] > 1) { ?>
                                                <input type="text" name="jenis_payment" id="jenis_payment" class="form-control" value="<?php echo $rsm['jenis_waktu']; ?>" readonly />
                                            <?php } else { ?>
                                                <select name="jenis_payment" id="jenis_payment" class="form-control select2" required>
                                                    <option></option>
                                                    <option value="CBD" <?php echo (isset($rsm['jenis_payment']) && $rsm['jenis_payment'] == 'CBD') ? ' selected' : ''; ?>>CBD (Cash Before Delivery)</option>
                                                    <option value="COD" <?php echo (isset($rsm['jenis_payment']) && $rsm['jenis_payment'] == 'COD') ? ' selected' : ''; ?>>COD (Cash On Delivery)</option>
                                                    <option value="CREDIT" <?php echo (isset($rsm['jenis_payment']) && $rsm['jenis_payment'] == 'CREDIT') ? ' selected' : ''; ?>>CREDIT</option>
                                                </select>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6<?php echo ($rsm['jenis_payment'] == "CREDIT" || $rsm['jenis_waktu'] == "CREDIT" ? '' : ' hide'); ?>" id="jwp">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Jangka Waktu Pembayaran *</label>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="text" name="top" id="top" class="form-control text-right" required <?php echo (isset($rsm['status_customer']) and $rsm['status_customer'] > 1) ? 'value="' . $rsm['top_payment'] . '" readonly' : 'value="' . $rsm['jangka_waktu'] . '"'; ?> />
                                                <span class="input-group-addon input-sm">Hari</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-md-offset-6<?php echo ($rsm['jenis_payment'] == "CREDIT" || $rsm['jenis_waktu'] == "CREDIT" ? '' : ' hide'); ?>" id="jwp2">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">&nbsp;</label>
                                        <div class="col-md-6">
                                            <select name="jenis_net" id="jenis_net" class="form-control select2" required>
                                                <option></option>
                                                <option value="3" <?php echo ($rsm['jenis_net'] == '3') ? 'selected' : ''; ?>>Setelah Loading</option>
                                                <option value="2" <?php echo ($rsm['jenis_net'] == '2') ? 'selected' : ''; ?>>Setelah Pengiriman (Barang Diterima)</option>
                                                <option value="1" <?php echo ($rsm['jenis_net'] == '1') ? 'selected' : ''; ?>>Setelah Invoice Diterima</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Order Method *</label>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="text" name="order_method" id="order_method" class="form-control text-right" value="<?php echo $cara_order; ?>" />
                                                <span class="input-group-addon input-sm">Hari sebelum pickup</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Toleransi Penyusutan *</label>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="text" name="tol_susut" id="tol_susut" class="form-control text-right" required value="<?php echo (empty($rsm['tol_susut']) || $rsm['tol_susut'] == '') ? '0.5' : $rsm['tol_susut']; ?>" />
                                                <span class="input-group-addon input-sm">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-12">Lokasi Pengiriman (Harga Terima..) *</label>
                                        <div class="col-md-12">
                                            <input type="text" name="lok_kirim" id="lok_kirim" class="form-control" required value="<?php echo $rsm['lok_kirim'] ?? null; ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Metode *</label>
                                        <div class="col-md-6">
                                            <select id="metode" name="metode" class="form-control select2" required>
                                                <option></option>
                                                <option value="Loco" <?php echo ($rsm['metode'] == 'Loco' ? 'selected' : '') ?>>Loco</option>
                                                <option value="Franco" <?php echo ($rsm['metode'] == 'Franco' ? 'selected' : '') ?>>Franco</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Refund</label>
                                        <div class="col-md-6">
                                            <input type="text" id="refund" name="refund" class="form-control text-right" value="<?php echo $rsm['refund_tawar']; ?>" />
                                        </div>
                                    </div>
                                </div>
                                <?php if ($action == "add") { ?>
                                    <div class="col-md-6">

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover tbl-other-cost">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="10%">No</th>
                                                        <th class="text-center" width="35%">Keterangan Other Cost</th>
                                                        <th class="text-center" width="35%">Nominal</th>
                                                        <th class="text-center" width="15%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $nomA = 0;
                                                    foreach ($other_coast as $dataA) {
                                                        $nomA++; ?>
                                                        <tr>
                                                            <td class="text-center">
                                                                <span class="noOther_cost" data-row-count="<?php echo $nomA; ?>"><?php echo $nomA; ?></span>
                                                            </td>
                                                            <td class="text-left"><input type="text" name="other_cost_keterangan[]" id="<?php echo "other_cost" . $nomA; ?>" class="form-control  input-sm" value="<?php echo $dataA; ?>" /></td>
                                                            <td class="text-left"><input type="text" name="nominal[]" id="<?php echo "nominal" . $nomA; ?>" class="form-control  input-sm" value="0" /></td>
                                                            <td class="text-center">
                                                                <button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button>
                                                                <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="text-right">Grand Total</th>
                                                        <th class="text-left">
                                                            <input type="text" id="grandTotal" name="other_cost" class="form-control" value="0" readonly />
                                                        </th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                <?php } else if ($action == "update") { ?>
                                    <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover tbl-other-cost">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="10%">No</th>
                                                        <th class="text-center" width="35%"> Keterangan Other Cost</th>
                                                        <th class="text-center" width="35%">Nominal</th>
                                                        <th class="text-center" width="15%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $nomA = 0;
                                                    foreach ($other_coast as $dataA) {
                                                        $nomA++;
                                                    ?>
                                                        <tr>
                                                            <td class="text-center">
                                                                <span class="noOther_cost" data-row-count="<?php echo $nomA; ?>"><?php echo $nomA; ?></span>
                                                            </td>
                                                            <td class="text-left">
                                                                <input type="hidden" name="id_other_cost[]" id="" class="form-control  input-sm" value="<?php echo $dataA['id_master']; ?>" />
                                                                <input type="text" name="other_cost_keterangan[]" id="<?php echo "other_cost" . $nomA; ?>" class="form-control  input-sm" value="<?php echo $dataA['keterangan']; ?>" />
                                                            </td>
                                                            <td class="text-left">
                                                                <input type="text" name="nominal[]" id="<?php echo "nominal" . $nomA; ?>" class="form-control validate[required] input-sm" value="<?php echo $dataA['nominal']; ?>" />
                                                            </td>
                                                            <td class="text-center">
                                                                <button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button>
                                                                <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="text-right">Grand Total</th>
                                                        <th class="text-left">
                                                            <input type="text" id="grandTotal" name="other_cost" class="form-control" value="0" readonly />
                                                        </th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                <?php } ?>

                                <!-- <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Other Cost</label>
                                        <div class="col-md-6">
                                            <input type="text" id="other_cost" name="other_cost" class="form-control text-right" value="<?php echo $rsm['other_cost']; ?>" />
                                        </div>
                                    </div>
                                </div> -->
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Volume Order *</label>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="text" name="volume" id="volume" class="form-control text-right" required value="<?php echo $vol_tawar; ?>" />
                                                <span class="input-group-addon input-sm">Liter</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($action == "add") { ?>
                                    <div class="col-md-4" hidden>
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-4">Harga Tier *</label>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <input type="text" name="totnyatier" id="totnyatier" class="form-control input-sm text-right" readonly />
                                                    <span class="input-group-addon input-sm">Liter</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        <?php } else if ($action == "update") { ?>
                            <div class="col-md-4" hidden>
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Harga Tier *</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" name="totnyatier" id="totnyatier" class="form-control input-sm text-right" readonly />
                                            <span class=" input-group-addon input-sm">Liter</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-responsive" style="border:1px solid #ddd; margin-bottom:20px;">
                                <table class="table no-border table-detail">
                                    <tr>
                                        <td colspan="2" style="background-color:#f4f4f4; border-bottom:1px solid #ddd;"><b>Kalkulasi OA</b></td>
                                    </tr>
                                    <tr>
                                        <td width="150">Transportir</td>
                                        <td><select name="cb_transportir" id="cb_transportir" class="form-control">
                                                <option></option>
                                                <?php $con->fill_select("id_master", "concat(nama_transportir, ' - ',  lokasi_suplier, ' (', nama_suplier, ')')", "pro_master_transportir", $calcoa1, "where 1=1 and (upper(nama_suplier) = 'PRO ENERGI' or owner_suplier = 1)", "nama_suplier", false); ?>
                                            </select></td>
                                    </tr>
                                    <tr>
                                        <td>Wilayah</td>
                                        <td><select name="wiloa_po" id="wiloa_po" class="form-control">
                                                <option></option>
                                                <?php $con->fill_select("a.id_master", "upper(concat(a.wilayah_angkut,'#',c.nama_kab,' ',b.nama_prov))", "pro_master_wilayah_angkut a join pro_master_provinsi b on a.id_prov = b.id_prov join pro_master_kabupaten c on a.id_kab = c.id_kab", $calcoa2, "where a.is_active=1", "nama", false); ?>
                                            </select></td>
                                    </tr>
                                    <tr>
                                        <td>Kapasitas Tanki</td>
                                        <td><select name="voloa_po" id="voloa_po" class="form-control select2">
                                                <option></option>
                                                <?php $con->fill_select("volume_angkut", "volume_angkut", "pro_master_volume_angkut", $calcoa3, "where is_active = 1", "", false); ?>
                                            </select></td>
                                    </tr>
                                    <tr>
                                        <td>Ongkos Angkut</td>
                                        <td><input type="text" name="ongoa_po" id="ongoa_po" class="form-control text-right" value="<?php echo $calcoa4; ?>" readonly /></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-group-sm">
                                <label class="control-label col-md-4">Ongkos Angkut Pengiriman *</label>
                                <div class="col-md-6">
                                    <input type="text" id="oa_kirim" name="oa_kirim" class="form-control text-right" required value="<?php echo $rsm['oa_kirim']; ?>" readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group form-group-sm">
                                <label class="control-label col-md-4">Perhitungan *</label>
                                <div class="col-md-6">
                                    <select id="perhitungan" name="perhitungan" class="form-control select2" required>
                                        <option></option>
                                        <option value="1" <?php echo (isset($rsm['perhitungan']) && $rsm['perhitungan'] == 1) ? ' selected' : ''; ?>>Harga</option>
                                        <option value="2" <?php echo (isset($rsm['perhitungan']) && $rsm['perhitungan'] == 2) ? ' selected' : ''; ?>>Formula</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="byHarga" class="<?php echo ($rsm['perhitungan'] == 1) ? '' : 'hide'; ?>">

                        <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Harga perliter *</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-addon input-sm">Rp.</span>
                                            <input type="text" id="harga_dasar" name="harga_dasar" class="form-control" required value="<?php echo $harga ?? null; ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <label class="control-label col-md-4">Ketentuan Harga</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <select name="pembulatan" id="pembulatan" class="form-control">
                                                <option value="1" <?= $pembulatan == 1 ? 'selected' : '' ?>>Pembulatan</option>
                                                <option value="0" <?= $pembulatan == 0 ? 'selected' : '' ?>>2 Angka dibelakang koma</option>
                                                <option value="2" <?= $pembulatan == 2 ? 'selected' : '' ?>>4 Angka dibelakang koma</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label col-md-12">
                                    <b>
                                        *Ketentuan ini akan berlaku untuk cetakan Invoice
                                    </b>
                                </label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group form-group-sm">
                                    <div class="col-md-12">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon" style="background-color:#fff;">
                                                <input type="checkbox" name="gabung_oa" id="gabung_oa" value="1" <?php echo ($rsm['gabung_oa'] == 1) ? 'checked' : ''; ?> <?= $seswil == 4 ? 'disabled' : '' ?> />
                                            </span>
                                            <div class="form-control" style="min-height:34px;">Harga Dasar Termasuk Ongkos Angkut ? <i>(Beri Tanda check jika memang digabung)</i></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-sm">
                                    <div class="col-md-12">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon" style="background-color:#fff;">
                                                <input type="checkbox" name="all_in" id="all_in" value="1" <?php echo ($rsm['all_in'] == 1) ? 'checked' : ''; ?> />
                                            </span>
                                            <div class="form-control" style="min-height:34px;">All In </i></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <div class="col-md-12">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon" style="background-color:#fff;">
                                                <input type="checkbox" name="gabung_pbbkb" id="gabung_pbbkb" value="1" <?php echo ($rsm['gabung_pbbkb'] == 1) ? 'checked' : ''; ?> />
                                            </span>
                                            <div class="form-control" style="min-height:34px;">Harga Dasar Termasuk PBBKB ? <i>(Beri Tanda check jika memang digabung)</i></div>

                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group form-group-sm">
                                    <div class="col-md-12">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon" style="background-color:#fff;">
                                                <input type="checkbox" name="gabung_pbbkboa" id="gabung_pbbkboa" value="1" <?php echo ($rsm['gabung_pbbkboa'] == 1) ? 'checked' : ''; ?> />
                                            </span>
                                            <div class="form-control" style="min-height:34px;">Harga Dasar Termasuk PBBKB + OA ? <i>(Beri Tanda check jika memang digabung)</i></div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>



                        <div class="table-responsive">
                            <table class="table table-bordered tblHarga">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="80">
                                            <?php /*<input type="checkbox" name="is_cetak_all" id="is_cetak_all" value="1" class="is_cetak_all" /> */ ?>
                                        </th>
                                        <th class="text-center" width="80">No</th>
                                        <th class="text-center" width="">Rincian</th>
                                        <th class="text-center" width="200">Nilai</th>
                                        <th class="text-center" width="200">Harga</th>
                                        <th class="text-center" width="80">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $nom = 0;
                                    $totnya = 0;
                                    foreach ($rincian as $idx1 => $arr1) {
                                        $nom++;
                                        $cetak = $arr1['rinci'] ?? null;
                                        $nilai = $arr1['nilai'] ?? 0;
                                        $biaya = $arr1['biaya'] ?? 0;
                                        $jenis = $arr1['rincian'] ?? null;
                                        $arrte = array("0", "2", "3");
                                        $chkd1 = ($cetak) ? 'checked' : 'checked';
                                        $totnya = $biaya + $totnya;
                                    ?>
                                        <tr>
                                            <td class="text-center">
                                                <!-- <input type="checkbox" name="<?php echo 'is_cetak[' . $idx1 . ']'; ?>" id="<?php echo 'is_cetak' . $nom; ?>" value="1" class="is_cetak" <?php echo $chkd1; ?> /> -->
                                            </td>
                                            <td class="text-center">
                                                <span id="<?php echo 'noHarga' . $nom; ?>" class="noHarga" data-row-count="<?php echo $nom; ?>"><?php echo $nom; ?></span>
                                            </td>
                                            <td class="text-left">
                                                <input type="text" name="<?php echo 'jnsHarga[' . $idx1 . ']'; ?>" id="<?php echo 'jnsHarga' . $nom; ?>" class="form-control input-sm" value="<?php echo $jenis; ?>" <?php echo ($idx1 < 4) ? 'readonly' : ''; ?> />
                                            </td>
                                            <td class="text-right">
                                                <?php if ($idx1 < 2) echo '&nbsp;';
                                                else { ?>
                                                    <div class="input-group">
                                                        <input type="text" name="<?php echo 'clcHarga[' . $idx1 . ']'; ?>" id="<?php echo 'clcHarga' . $nom; ?>" class="<?php echo ($idx1 == 3 ? 'form-control input-sm text-right ncpkb' : 'form-control input-sm text-right'); ?>" value="<?php echo $nilai; ?>" readonly />
                                                        <span class="input-group-addon input-sm">%</span>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                            <td class="text-right">
                                                <input type="text" name="<?php echo 'rncHarga[' . $idx1 . ']'; ?>" id="<?php echo 'rncHarga' . $nom; ?>" class="<?php echo ($idx1 == 1 ? 'form-control input-sm hitung ncoa' : 'form-control input-sm hitung'); ?>" value="<?php echo $biaya; ?>" />
                                            </td>
                                            <td class="text-center">
                                                <?php echo ($idx1 < 4) ? '&nbsp;' : '<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>' ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-center" colspan="4"><b>TOTAL</b></td>
                                        <td>
                                            <input type="text" name="totnya" id="totnya" class="form-control input-sm text-right" value="<?php echo $totnya; ?>" readonly />
                                        </td>
                                        <td class="text-center">&nbsp;</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>



                    <div id="byFormula" class="<?php echo ($rsm['perhitungan'] == 2) ? '' : 'hide'; ?>">

                        <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                        <div class="table-responsive">
                            <table class="table table-bordered tblFormula">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="80">No</th>
                                        <th class="text-center" width="">Rincian</th>
                                        <th class="text-center" width="100">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $nom = 0;
                                    foreach ($formula as $arr1) {
                                        $nom++; ?>
                                        <tr>
                                            <td class="text-center">
                                                <span id="<?php echo 'noFormula' . $nom; ?>" class="noFormula" data-row-count="<?php echo $nom; ?>"><?php echo $nom; ?></span>
                                            </td>
                                            <td class="text-left">
                                                <input type="text" name="jnsfor[]" id="<?php echo 'jnsfor' . $nom; ?>" class="form-control input-sm" value="<?php echo $arr1; ?>" />
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
                    </div>

                    <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group form-group-sm">
                                <label class="control-label col-md-12">Keterangan Harga</label>
                                <div class="col-md-12">
                                    <input type="text" name="ket_harga" id="ket_harga" class="form-control" value="<?php echo $rsm['ket_harga'] ?? null; ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group form-group-sm">
                                <label class="control-label col-md-12">Catatan</label>
                                <div class="col-md-12">
                                    <textarea name="catatan" id="catatan" class="form-control" style="height:90px;"><?php echo isset($rsm['catatan']) ? str_replace('<br />', PHP_EOL, $rsm['catatan']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group form-group-sm">
                                <label class="control-label col-md-12">
                                    <input type="checkbox" name="syarat_ketentuan" id="syarat_ketentuan" value="1" <?php echo ($rsm['term_condition'] != '' ? 'checked' : '') ?>>
                                    Syarat &amp; Ketentuan Tambahan (jika pilih ini, akan tercetak dihalaman kedua)
                                </label>
                                <div class="col-md-12">
                                    <textarea name="term_condition" id="term_condition" class="form-control" style="height:90px;" <?php echo ($rsm['term_condition'] != '' ? '' : 'readonly') ?>><?php echo isset($rsm['term_condition']) ? str_replace('<br />', PHP_EOL, $rsm['term_condition']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                    <div style="margin-bottom:15px;">
                        <input type="hidden" name="act" value="<?php echo $action; ?>" />
                        <input type="hidden" name="idk" value="<?php echo $idk; ?>" />
                        <input type="hidden" id="tmc" name="tmc" value="<?php echo $idc; ?>" />
                        <input type="hidden" id="reflag" name="reflag" value="<?php echo $reFlag; ?>" />
                        <button type="submit" class="btn btn-primary jarak-kanan <?php echo ($action == "add") ? 'disabled' : ''; ?>" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                            <i class="fa fa-save jarak-kanan"></i> Simpan</button>
                        <a href="<?php echo BASE_URL_CLIENT . '/penawaran.php'; ?>" class="btn btn-default" style="min-width:90px;">
                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                    </div>

                    <p style="margin:15px 0px;"><i>Form bisa disimpan, jika harga minyak tersedia dan ongkos angkut dari kalkulasi OA tersedia</i></p>
                    <p><small>* Wajib Diisi</small></p>
                    </div>
    </div>
    </form>

    <div id="optCabang" class="hide"><?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", "", "where is_active=1 and id_master <> 1", "", false); ?></div>
    <?php $con->close(); ?>
    </section>
    <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
    </aside>
    </div>

    <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <h4 class="modal-title">Loading Data ...</h4>
                </div>
                <div class="modal-body text-center modal-loading"></div>
            </div>
        </div>
    </div>

    <style type="text/css">
        h3.form-title {
            font-size: 18px;
            margin: 0 0 10px;
            font-weight: 700;
        }

        #harga_dasar {
            text-align: right;
        }

        .table>tfoot>tr>td {
            border: 1px solid #ddd;
            padding: 5px;
            font-size: 11px;
            font-family: arial;
            vertical-align: top;
        }
    </style>

    <script>
        $(document).ready(function() {

            // validasi oa dan metode iwan*
            document.getElementById("btnSbmt").addEventListener("click", function(event) {
                let oaKirim = document.getElementById("oa_kirim").value;
                let metode = document.getElementById("metode").value; // Ambil nilai metode
                // let idMaster = document.getElementById("area").value;


                // Daftar id_master yang dikecualikan dari validasi
                // let excludedIds = ["37", "41", "46", "48", "50"];

                if (metode !== "Loco" && parseFloat(oaKirim) === 0) {
                    event.preventDefault(); // Mencegah submit form
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gagal Menyimpan!',
                        text: 'Ongkos Angkut Pengiriman 0, Harap Hubungi Team Logistik',
                        confirmButtonText: 'OK'
                    });
                }
            });
            //detail other cost

            let tabel = $(".tbl-other-cost tbody");

            // Tambahkan baris kosong jika tabel masih kosong
            if (tabel.find("tr").length === 0) {
                addEmptyRow();
            }

            calculateGrandTotal();
            toggleDeleteButton();

            // Tambah baris baru
            $("#gform").on("click", ".tbl-other-cost button.addRow", function() {
                $("form#gform").validationEngine("detach");
                var tabel = $(this).closest(".tbl-other-cost");
                var rwTbl = tabel.find("tbody > tr:last");
                var rwNom = parseInt(rwTbl.find("span.noOther_cost").data("rowCount")) || 0;
                var newId = rwNom + 1;

                // Tambahkan baris baru
                var objTr = $("<tr>");
                var objTd1 = $("<td>", {
                    class: "text-center"
                }).appendTo(objTr);
                var objTd2 = $("<td>", {
                    class: "text-left"
                }).appendTo(objTr);
                var objTd3 = $("<td>", {
                    class: "text-left"
                }).appendTo(objTr);
                var objTd4 = $("<td>", {
                    class: "text-center"
                }).appendTo(objTr);

                objTd1.html('<span class="noOther_cost" data-row-count="' + newId + '">' + newId + "</span>");
                objTd2.html('<input type="text" name="other_cost_keterangan[]" id="other_cost' + newId + '" class="form-control validate[required] input-sm" />');
                objTd3.html('<input type="text" name="nominal[]" id="nominal' + newId + '" class="form-control validate[required] input-sm" />');
                objTd4.html('<button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button> ');
                objTd4.append('<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');

                // Tambahkan baris setelah baris terakhir
                rwTbl.after(objTr);

                // Perbarui nomor baris
                tabel.find(".noOther_cost").each(function(i) {
                    $(this).text(i + 1);
                });

                toggleDeleteButton();
                $("form#gform").validationEngine("attach");
            });

            // Hapus baris
            $("#gform").on("click", ".tbl-other-cost a.hRow", function() {
                var tabel = $(this).closest(".tbl-other-cost");
                var rowCount = tabel.find("tbody tr").length;

                if (rowCount > 1) {
                    $(this).closest("tr").remove();

                    tabel.find(".noOther_cost").each(function(i) {
                        $(this).text(i + 1);
                    });

                    calculateGrandTotal();
                    toggleDeleteButton();
                }
            });

            // Fungsi untuk menampilkan atau menyembunyikan tombol hapus
            function toggleDeleteButton() {
                var tabel = $(".tbl-other-cost tbody");
                var rowCount = tabel.find("tr").length;

                if (rowCount <= 1) {
                    tabel.find(".hRow").hide();
                } else {
                    tabel.find(".hRow").show();
                }
            }

            // Tambahkan baris kosong jika tabel masih kosong
            function addEmptyRow() {
                var objTr = $("<tr>");
                var objTd1 = $("<td>", {
                    class: "text-center"
                }).appendTo(objTr);
                var objTd2 = $("<td>", {
                    class: "text-left"
                }).appendTo(objTr);
                var objTd3 = $("<td>", {
                    class: "text-left"
                }).appendTo(objTr);
                var objTd4 = $("<td>", {
                    class: "text-center"
                }).appendTo(objTr);

                objTd1.html('<span class="noOther_cost" data-row-count="1">1</span>');
                objTd2.html('<input type="text" name="other_cost_keterangan[]" id="other_cost1" class="form-control validate[required] input-sm" />');
                objTd3.html('<input type="text" name="nominal[]" id="nominal1" class="form-control validate[required] input-sm" />');
                objTd4.html('<button class="btn btn-action btn-primary addRow jarak-kanan" type="button"><i class="fa fa-plus"></i></button> ');
                objTd4.append('<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');

                $(".tbl-other-cost tbody").append(objTr);
                toggleDeleteButton();
            }

            // Hitung Grand Total
            function calculateGrandTotal() {
                let grandTotal = 0;

                $('input[name="nominal[]"]').each(function() {
                    let nominalValue = parseFloat($(this).val().replace(/,/g, "")) || 0;
                    grandTotal += nominalValue;
                });

                let refundValue = parseFloat($("#refund").val().replace(/,/g, "")) || 0;

                let hargaDasar = 0;

                $(".tblHarga tbody tr").each(function() {
                    const jenis = $(this).find("input[name^='jnsHarga']").val();
                    if (jenis === "Harga Dasar") {
                        let biayaStr = $(this).find("input[name^='rncHarga']").val();

                        // Hapus koma ribuan (misalnya 17,000.0000 menjadi 17000.0000)
                        biayaStr = biayaStr.replace(/,/g, '');
                        console.log("Setelah menghapus koma ribuan:", biayaStr);

                        // Konversi ke angka dan hilangkan desimal (misalnya 17000.0000 menjadi 17000)
                        let biaya = parseInt(parseFloat(biayaStr)); // Ambil hanya bilangan bulat
                        console.log("Setelah diubah menjadi angka bulat:", biaya);

                        hargaDasar += biaya;
                    }
                });

                // Hitung finalTotal setelah hargaDasar sudah bulat
                let finalTotal = hargaDasar - refundValue - grandTotal;
                finalTotal = finalTotal < 0 ? 0 : finalTotal;
                console.log("Final Total:", finalTotal);

                // Tampilkan angka tanpa format ribuan
                $("#grandTotal").val($.number(grandTotal, 0, ".", ","));
                $("#totnyatier").val($.number(finalTotal, 0, ".", ","));

            }

            // Event listener untuk input nominal, refund, dan tabel harga
            $(document).on("input", 'input[name="nominal[]"], #refund, .tblHarga input', function() {
                calculateGrandTotal();
            });

            //akhiran

            <?php if ($action == 'update') { ?>
                getHargaMinyak($("#masa_awal").val(), $("#masa_akhir").val(), $("#area").val(), $("#pbbkb_tawar").val(), $('#produk_tawar').val());
            <?php } ?>

            var formValidasiCfg = {
                submitHandler: function(form) {
                    if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
                        $.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
                        setErrorFocus($("#nup_fee"), $("form#gform"), false);
                    } else if ($("#clcHarga4").val() != $("#pbbkb_tawar option:selected").text().slice(0, -2)) {
                        $.validator.showErrorField('pbbkb_tawar', "Harga PBBKB Tidak Sama Dengan Harga PBBKB di Tabel Harga");
                        setErrorFocus($("#pbbkb_tawar"), $("form#gform"), false);
                    } else if ($("#perhitungan").val() == '1' && $("#metode").val() == 'Franco' && ($("#rncHarga2").val() == '0' || $("#rncHarga2").val() == '')) {
                        swal.fire({
                            icon: "warning",
                            width: '350px',
                            allowOutsideClick: false,
                            html: '<p style="font-size:14px; font-family:arial;">Kolom [Ongkos Angkut] pada tabel rincian harga belum diisi</p>'
                        });
                    } else {
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        form.submit();
                    }
                }
            };
            $("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

            $("select#cb_transportir").select2({
                placeholder: "Pilih Transportir",
                allowClear: true,
            });
            $("select#wiloa_po").select2({
                placeholder: "Pilih salah satu",
                allowClear: true,
                templateResult: function(repo) {
                    if (repo.loading) return repo.text;
                    var text1 = repo.text.split("#");
                    var $returnString = $('<span>' + text1[0] + '<br />' + text1[1].replace("KOTA", "").replace("KABUPATEN", "") + '</span>');
                    return $returnString;
                },
                templateSelection: function(repo) {
                    var text1 = repo.text.split("#");
                    var $returnString = $('<span>' + text1[0] + ' ' + (text1[1] ? text1[1].replace("KOTA", "").replace("KABUPATEN", "") : '') + '</span>');
                    return $returnString;
                },
            });

            $(".table-detail").on("change", "select#cb_transportir, select#wiloa_po, select#voloa_po, #volume", getOngkosAngkut);
            $("#volume").on("blur", getOngkosAngkut);

            function getOngkosAngkut() {
                var elmTa = $("select#cb_transportir").val();
                var elmVa = $("select#voloa_po").val();
                var elmOa = $("select#wiloa_po").val();
                var elmVl = $("#volume").val();

                if (elmTa != "" && elmVa != "" && elmOa != "" && elmVl != "") {
                    $("#loading_modal").modal();
                    $.ajax({
                        type: 'POST',
                        url: "./__get_ongkos_angkut.php",
                        data: {
                            q1: elmTa,
                            q2: elmOa,
                            q3: elmVa
                        },
                        cache: false,
                        success: function(data) {
                            $("input#ongoa_po").val(data).trigger("change");
                            if ((elmVl * 1) < (elmVa * 1)) {
                                let oaBaru = (((elmVa * 1) / (elmVl * 1)) * (data * 1));
                                $("input#oa_kirim").val(parseInt(oaBaru));
                            } else {
                                $("input#oa_kirim").val(data);
                            }
                        }
                    });
                    $("#loading_modal").modal("hide");
                }
            }

            $("#volume").number(true, 0, ".", ",");
            $("#ongoa_po").number(true, 0, ".", ",");
            $("#oa_kirim").number(true, 0, ".", ",");
            $("#refund").number(true, 0, ".", ",");
            $("#other_cost").number(true, 0, ".", ",");

            <?php if ($pembulatan == 1) { ?>
                $(".hitung").number(true, 0, ".", ",");
                $("#totnya").number(true, 0, ".", ",");
            <?php } else if ($pembulatan == 0) { ?>
                $(".hitung").number(true, 2, ".", ",");
                $("#totnya").number(true, 2, ".", ",");
            <?php } else { ?>
                $(".hitung").number(true, 4, ".", ",");
                $("#totnya").number(true, 4, ".", ",");

            <?php } ?>

            $("select#cabang").select2({
                placeholder: "Pilih salah satu",
                allowClear: true
            });

            $("#perhitungan").on("change", function() {
                var nilai = $(this).val();
                $("#loading_modal").modal();
                if (nilai == 1) {
                    $("#byHarga").removeClass("hide");
                    $("#byFormula").addClass("hide");
                } else if (nilai == 2) {
                    $("#byHarga").addClass("hide");
                    $("#byFormula").removeClass("hide");
                } else {
                    $("#byHarga").addClass("hide");
                    $("#byFormula").addClass("hide");
                }
                //$("#is_rinci").trigger("ifUnChecked");
                $("#harga_dasar").val("0").trigger("keyup");
                $("#loading_modal").modal("hide");
            });

            $(".is_cetak_all").on("ifChecked", function() {
                $(".is_cetak").iCheck('check');
            }).on("ifUnchecked", function() {
                $(".is_cetak").iCheck("uncheck");
            });

            $("#is_rinci").on("ifChecked", function() {
                $("#clcHarga4").val("");
                $("#rncHarga1, #rncHarga2, #rncHarga3, #rncHarga4").val("").removeAttr("readonly");
                hitungTotal();
            }).on("ifUnchecked", function() {
                var tbl = $(".tblHarga");
                var row = tbl.find('tbody > tr').length;
                while (row > 4) {
                    tbl.find('tbody > tr:last').remove();
                    row--;
                }
                $("#clcHarga4, #rncHarga2, #rncHarga3, #rncHarga4").val("");
                //$("#rncHarga1").attr("readonly", "readonly");
                $("#harga_dasar").trigger("keyup");
            });

            $("#pembulatan").change(function() {
                var value = $(this).val();
                if (value == 1) {
                    $(".tblHarga").find(".hitung").number(true, 0, ".", ",");
                    $("#harga_dasar").number(true, 0, ".", ",");
                    $("#totnya").number(true, 0, ".", ",");
                    $("#harga_dasar").trigger("keyup");
                } else if (value == 0) {
                    $(".tblHarga").find(".hitung").number(true, 2, ".", ",");
                    $("#harga_dasar").number(true, 2, ".", ",");
                    $("#totnya").number(true, 2, ".", ",");
                    $("#harga_dasar").trigger("keyup");
                } else {
                    $(".tblHarga").find(".hitung").number(true, 4, ".", ",");
                    $("#harga_dasar").number(true, 4, ".", ",");
                    $("#totnya").number(true, 4, ".", ",");
                    $("#harga_dasar").trigger("keyup");
                }
            })

            // $("#pembulatan").on("ifChecked", function() {
            //     $(".tblHarga").find(".hitung").number(true, 0, ".", ",");
            //     $("#totnya").number(true, 0, ".", ",");
            //     $("#harga_dasar").trigger("keyup");
            // }).on("ifUnchecked", function() {
            //     $(".tblHarga").find(".hitung").number(true, 2, ".", ",");
            //     $("#totnya").number(true, 2, ".", ",");
            //     $("#harga_dasar").trigger("keyup");
            // });

            $(".tblHarga").on("click", "button.addRow", function() {
                var tabel = $(this).parents(".tblHarga");
                //if($("#is_rinci").iCheck('update')[0].checked === true){
                var rwTbl = tabel.find('tbody > tr:last');
                var rwNom = parseInt(rwTbl.find("span.noHarga").data('rowCount'));
                var newId = parseInt(rwNom + 1);

                var objTr = $("<tr>");
                var objTd1 = $("<td>", {
                    class: "text-center"
                }).appendTo(objTr);
                var objTd2 = $("<td>", {
                    class: "text-center"
                }).appendTo(objTr);
                var objTd3 = $("<td>", {
                    class: "text-left"
                }).appendTo(objTr);
                var objTd4 = $("<td>", {
                    class: "text-right"
                }).appendTo(objTr);
                var objTd5 = $("<td>", {
                    class: "text-right"
                }).appendTo(objTr);
                var objTd6 = $("<td>", {
                    class: "text-center"
                }).appendTo(objTr);
                objTd1.html('<input type="checkbox" name="is_cetak[' + newId + ']" id="is_cetak' + newId + '" value="1" class="is_cetak" />');
                objTd2.html('<span id="noHarga' + newId + '" class="noHarga" data-row-count="' + newId + '"></span>');
                objTd3.html('<input type="text" name="jnsHarga[' + newId + ']" id="jnsHarga' + newId + '" class="form-control input-sm" />');
                objTd4.html('<div class="input-group"><input type="text" name="clcHarga[' + newId + ']" id="clcHarga' + newId + '" class="form-control input-sm text-right" /><span class="input-group-addon input-sm">%</span></div>');
                objTd5.html('<input type="text" name="rncHarga[' + newId + ']" id="rncHarga' + newId + '" class="form-control input-sm hitung" />');
                objTd6.html('<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
                rwTbl.after(objTr);
                tabel.find(".noHarga").each(function(i, v) {
                    $(this).text(i + 1);
                });
                $("#rncHarga" + newId).number(true, 0, ".", ",");
                $("#is_cetak" + newId).iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue'
                });
                //}
            });
            $(".tblHarga").on("click", "a.hRow", function() {
                var tabel = $(this).parents(".tblHarga");
                var jTbl = tabel.find("tr").length;
                if (jTbl > 2) {
                    var cRow = $(this).closest('tr');
                    cRow.remove();
                    tabel.find(".noHarga").each(function(i, v) {
                        $(this).text(i + 1);
                    });
                    hitungTotal();
                }
            });
            $(".tblHarga").on("keyup", ".hitung", hitungTotal);

            $("#gabung_oa").on("ifChanged", function(e) {
                hitungTotal();
            });
            $("#gabung_pbbkb").on("ifChanged", function(e) {
                hitungTotal();
            });

            function hitungTotal() {
                var pendapatan = 0,
                    t1, t2, t3, t4;
                var gabung_oa = $("#gabung_oa").is(":checked");
                var gabung_pbbkb = $("#gabung_pbbkb").is(":checked");
                var gabung_pbbkboa = $("#gabung_pbbkboa").is(":checked");

                $(".tblHarga").find(".hitung").each(function(index1, element1) {
                    //pendapatan += parseInt($(element1).val().replace(/[.][,]+/g, "")*1); 
                    if ($(element1).attr("id") == 'rncHarga1') {
                        t1 = $(element1).val() * 1;
                    }
                    if ($(element1).attr("id") == 'rncHarga2') {
                        t2 = $(element1).val() * 1;
                    }
                    if ($(element1).attr("id") == 'rncHarga3') {
                        if (gabung_pbbkb) {
                            t4 = t1 * (($("#clcHarga4").val() * 1) / 100);
                            t3 = (t1 + t2 + t4) * (($("#clcHarga3").val() * 1) / 100);
                        } else if (gabung_pbbkboa) {
                            t4 = t1 * (($("#clcHarga4").val() * 1) / 100);
                            t3 = (t1 + t2 + t4) * (($("#clcHarga3").val() * 1) / 100);
                        } else {
                            t3 = (t1 + t2) * (($("#clcHarga3").val() * 1) / 100);
                        }
                        $(element1).val(t3);
                    }
                    if ($(element1).attr("id") == 'rncHarga4') {
                        if (gabung_oa) {
                            t4 = (t1 + t2) * (($("#clcHarga4").val() * 1) / 100);
                        } else {
                            t4 = t1 * (($("#clcHarga4").val() * 1) / 100);
                        }
                        $(element1).val(t4);
                    }
                    pendapatan += $(element1).val() * 1;
                });
                $("#totnya").val(pendapatan);
                $("#harga_dasar").val(pendapatan).attr("readonly", "readonly");
            }

            $(".tblFormula").on("click", "button.addRow", function() {
                var tabel = $(this).parents(".tblFormula");
                var rwTbl = tabel.find('tbody > tr:last');
                var rwNom = parseInt(rwTbl.find("span.noFormula").data('rowCount'));
                var newId = parseInt(rwNom + 1);

                var objTr = $("<tr>");
                var objTd1 = $("<td>", {
                    class: "text-center"
                }).appendTo(objTr);
                var objTd2 = $("<td>", {
                    class: "text-left"
                }).appendTo(objTr);
                var objTd3 = $("<td>", {
                    class: "text-center"
                }).appendTo(objTr);
                objTd1.html('<span id="noFormula' + newId + '" class="noFormula" data-row-count="' + newId + '"></span>');
                objTd2.html('<input type="text" name="jnsfor[]" id="jnsfor' + newId + '" class="form-control input-sm" />');
                objTd3.html('<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button> ');
                objTd3.append('<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
                rwTbl.after(objTr);
                tabel.find(".noFormula").each(function(i, v) {
                    $(this).text(i + 1);
                });
            });
            $(".tblFormula").on("click", "a.hRow", function() {
                var tabel = $(this).parents(".tblFormula");
                var jTbl = tabel.find("tr").length;
                if (jTbl > 2) {
                    var cRow = $(this).closest('tr');
                    cRow.remove();
                    tabel.find(".noFormula").each(function(i, v) {
                        $(this).text(i + 1);
                    });
                }
            });

            $("#gform").on("keyup blur", ".ncoa, .ncpkb, #harga_dasar", function() {
                //if($("#is_rinci").iCheck('update')[0].checked === false){
                /*var tmphrgdsr = $("#pemberitahuan").text().replace('Harga minyak Rp. ', '');
                var tmpnilai1 = $("#oa_kirim").val();
                (tmpnilai1 ? $("#rncHarga2").val(tmpnilai1) : 0);
                var t1, t2, t3,
                hdasar = $("#rncHarga1"),
                angkut = $("#rncHarga2").val() * 1,
                ppnPsn = $("#clcHarga3").val() * 1,
                pkbPsn = $("#clcHarga4").val() * 1;
                angkutPPN = angkut * (11/100);
                t1 = ($("#harga_dasar").val() - (angkut + angkutPPN)) / ((pkbPsn / 100) + 1.11);
                //t1 = tmphrgdsr;
                t2 = t1 * (pkbPsn/100);
                t3 = (t1 + angkut) * (11/100);
                hdasar.val(t1);
                $("#rncHarga4").val(t2);
                $("#rncHarga3").val(t3);*/
                hitungTotal();
                //}
            });

            $("select#jenis_payment").on("change", function() {
                if ($(this).val() != "CREDIT") {
                    $("#jwp").addClass("hide");
                    $("#jwp2").addClass("hide");
                    $("#top").val("");
                } else {
                    $("#jwp").removeClass("hide");
                    $("#jwp2").removeClass("hide");
                    $("#top").val("");
                }
            });

            $("#masa_awal, #masa_akhir, #pbbkb_tawar").on("change", function() {
                if ($("#pbbkb_tawar").val() != "") {
                    $("#clcHarga4").val($("#pbbkb_tawar option:selected").text().slice(0, -2));
                    $("#gform").find(".ncpkb").trigger("blur");
                } else {
                    $("#clcHarga4").val('');
                    $("#gform").find(".ncpkb").trigger("blur");
                }
                if ($("#masa_awal").val() != "" && $("#masa_akhir").val() != "" && $("#area").val() != "" && $("#pbbkb_tawar").val() != "" && $('#produk_tawar').val() != '') {
                    $("#loading_modal").modal();
                    getHargaMinyak($("#masa_awal").val(), $("#masa_akhir").val(), $("#area").val(), $("#pbbkb_tawar").val(), $('#produk_tawar').val());
                    $("#loading_modal").modal("hide");
                } else {
                    $("#pemberitahuan").removeClass("text-red").html('');
                }
            });

            <?php if ($idc) { ?>
                cekPenawaran($("select#idr").val(), $("#area").val());
            <?php } ?>
            $("select#idr").on("change", function() {
                if ($("select#idr").val() != "") {
                    $("#loading_modal").modal();
                    cekPenawaran($("select#idr").val(), $("#area").val());
                    $("#loading_modal").modal("hide");
                } else {
                    $("#infoin").html('');
                    $("#top").val('').removeAttr("readonly");
                    $("#nama_up, #jabatan_up, #alamat_up, #telp_up, #fax_up, #jenis_waktu").val('');
                    $("#gelar, #jenis_payment").val('').trigger('change');
                    $("#jenis_payment").prop("disabled", false);
                }
            });
            $("#area").on("change", function() {
                $("#loading_modal").modal();
                if ($("select#idr").val() != "") {
                    cekPenawaran($("select#idr").val(), $("#area").val())
                } else {
                    $("#infoin").html('');
                    $("#top").val('').removeAttr("readonly");
                    $("#nama_up, #jabatan_up, #alamat_up, #telp_up, #fax_up, #jenis_waktu").val('');
                    $("#gelar, #jenis_payment").val('').trigger('change');
                    $("#jenis_payment").prop("disabled", false);
                }
                if ($("#masa_awal").val() != "" && $("#masa_akhir").val() != "" && $("#area").val() != "" && $("#pbbkb_tawar").val() != "" && $('#produk_tawar').val() != "") {
                    getHargaMinyak($("#masa_awal").val(), $("#masa_akhir").val(), $("#area").val(), $("#pbbkb_tawar").val(), $('#produk_tawar').val());
                } else {
                    $("#pemberitahuan").removeClass("text-red").html('');
                }
                $("#loading_modal").modal("hide");
            });

            $("#produk_tawar").on("change", function() {
                $("#loading_modal").modal();
                if ($("select#idr").val() != "") {
                    cekPenawaran($("select#idr").val(), $("#area").val())
                } else {
                    $("#infoin").html('');
                    $("#top").val('').removeAttr("readonly");
                    $("#nama_up, #jabatan_up, #alamat_up, #telp_up, #fax_up, #jenis_waktu").val('');
                    $("#gelar, #jenis_payment").val('').trigger('change');
                    $("#jenis_payment").prop("disabled", false);
                }
                if ($("#masa_awal").val() != "" && $("#masa_akhir").val() != "" && $("#area").val() != "" && $("#pbbkb_tawar").val() != "" && $('#produk_tawar').val() != "") {
                    getHargaMinyak($("#masa_awal").val(), $("#masa_akhir").val(), $("#area").val(), $("#pbbkb_tawar").val(), $('#produk_tawar').val());
                } else {
                    $("#pemberitahuan").removeClass("text-red").html('');
                }
                $("#loading_modal").modal("hide");
            });

            $("#syarat_ketentuan").on('ifChanged', function() {
                if ($(this).is(':checked')) {
                    $("#term_condition").val('');
                    $("#term_condition").removeAttr("readonly");
                } else {
                    $("#term_condition").val('');
                    $("#term_condition").attr('readonly', 'readonly');


                }
            });

            function cekPenawaran(customer, area) {
                $.ajax({
                    type: "POST",
                    url: "./__cek_penawaran_customer.php",
                    data: {
                        "q1": customer,
                        "q2": area
                    },
                    dataType: "json",
                    cache: false,
                    success: function(data) {
                        (data.items ? $("#infoin").html(data.items).iCheck({
                            radioClass: 'iradio_square-blue'
                        }) : $("#infoin").html(data.items));
                        if (data.glr)
                            $("#gelar").val(data.glr).trigger('change');
                        if (data.jenis)
                            $("#jenis_payment").val(data.jenis).trigger('change');
                        if (data.top)
                            $("#top").val(data.top);
                        if (data.jenis)
                            $("#jenis_waktu").val(data.jenis);
                        if (data.nama)
                            $("#nama_up").val(data.nama);
                        if (data.jbtn)
                            $("#jabatan_up").val(data.jbtn);
                        if (data.almt)
                            $("#alamat_up").val(data.almt);
                        if (data.telp)
                            $("#telp_up").val(data.telp);
                        if (data.fax)
                            $("#fax_up").val(data.fax);
                        (data.stat > 1 ? $("#top").attr("readonly", "readonly") : $("#top").removeAttr("readonly"));
                        (data.stat > 1 ? $("#jenis_payment").prop("disabled", true) : $("#jenis_payment").prop("disabled", false));
                        if (data.idcb) {
                            var isinya =
                                '<input type="hidden" name="cabang" id="cabang" value="' + data.idcb + '" />' +
                                '<input type="text" id="nm_cabang_txt" name="nm_cabang_txt" class="form-control" value="' + data.nmcb + '" readonly />';
                            $("#wrCabang").html(isinya);
                        } else {
                            // $("#wrCabang").html('');
                            // $("#wrCabang").html('<select name="cabang" id="cabang" class="form-control validate[required]"><option></option>'+$("#optCabang").html()+'</select>');
                            // $("select#cabang").select2({placeholder:"Pilih salah satu", allowClear:true });
                        }
                        return false;
                    }
                });
            }

            function getHargaMinyak(masa_awal, masa_akhir, area, pbbkb, product) {
                $.ajax({
                    type: "POST",
                    url: "./__cek_harga_minyak.php",
                    data: {
                        "q1": masa_awal,
                        "q2": masa_akhir,
                        "q3": area,
                        "q4": pbbkb,
                        "q5": product
                    },
                    dataType: "json",
                    cache: false,
                    success: function(data) {
                        if (data.error == "") {
                            var tmphrgdsr = data.harga;
                            var tmpnilai1 = $("#oa_kirim").val();
                            (tmpnilai1 ? tmpnilai1 : 0);
                            var t1, t2, t3, tmpharga,
                                angkut = tmpnilai1,
                                ppnPsn = $("#clcHarga3").val() * 1,
                                pkbPsn = $("#clcHarga4").val() * 1;
                            t1 = parseInt(tmphrgdsr) * 1;
                            t2 = t1 * (pkbPsn / 100);
                            t3 = (t1 + angkut) * (ppnPsn / 100);
                            t4 = $("#rncHarga1").val();
                            t5 = $("#rncHarga2").val();

                            tmpharga = (t4 ? t4 : t1);
                            tmpnilai1 = (t5 ? t5 : tmpnilai1);
                            $("#rncHarga1").val(tmpharga);
                            $("#rncHarga2").val(tmpnilai1)
                            $("#rncHarga3").val(t3);
                            $("#rncHarga4").val(t2);

                            $("#pemberitahuan").removeClass("text-red").html('<i class="fa fa-check jarak-kanan"></i><b>' + data.items + '</b>');
                            $("#btnSbmt").removeClass("disabled").removeAttr("disabled");
                        } else {
                            $("#rncHarga1").val('');
                            $("#rncHarga2").val('')
                            $("#rncHarga3").val('');
                            $("#rncHarga4").val('');

                            $("#pemberitahuan").addClass("text-red").html('<i class="fa fa-exclamation-triangle jarak-kanan"></i><b>' + data.error + '</b>');
                            $("#btnSbmt").addClass("disabled").attr("disabled", "disabled");
                        }
                        $("#ongoa_po").trigger("change");
                        hitungTotal();
                        return false;
                    }
                });
            }

            cekOngkosOaPo();
            $("#ongoa_po").on("change", function() {
                $("#btnSbmt").addClass("disabled").attr("disabled", "disabled");
                if ($("#pemberitahuan").html() != "") {
                    if ($("#pemberitahuan").hasClass("text-red") === false) {
                        cekOngkosOaPo();
                    }
                }
            });

            function cekOngkosOaPo() {
                if ($("#metode").val() == "Franco") {
                    $("#btnSbmt").removeClass("disabled").removeAttr("disabled");
                } else if ($("#metode").val() == "Loco") {
                    $("#btnSbmt").removeClass("disabled").removeAttr("disabled");
                } else {
                    $("#btnSbmt").addClass("disabled").attr("disabled", "disabled");
                }
            }

            $("#metode").on("change", function() {
                var metode = $('#metode').val();
                if (metode == 'Franco') {
                    $("#rncHarga2").val($("#oa_kirim").val()).removeAttr("readonly");
                } else if (metode == 'Loco') {
                    $("#rncHarga2").val('').attr("readonly", "readonly");
                }
                hitungTotal();
                $("#ongoa_po").trigger("change");
            });


        });
    </script>
</body>

</html>