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

if (isset($enk['idr']) && $enk['idr'] !== '') {
    $action     = "update";
    $section     = "PO Suplier";
    $idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
    $sql = "
			select a.*, a1.id_po_supplier, b.jenis_produk, b.merk_dagang, d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal 
			from new_pro_inventory_vendor_po a 
			join pro_master_produk b on a.id_produk = b.id_master 
			join pro_master_vendor d on a.id_vendor = d.id_master 
			join pro_master_terminal e on a.id_terminal = e.id_master 
			left join new_pro_inventory_vendor_po_receive a1 on a.id_master = a1.id_po_supplier 
			where a.id_master = '" . $idr . "'
		";
    $rsm     = $con->getRecord($sql);

    $dt1     = date("d/m/Y", strtotime($rsm['tanggal_inven']));
    $dt8     = ($rsm['harga_tebus']) ? $rsm['harga_tebus'] : '';
    $ket     =  ($rsm['keterangan']) ? $rsm['keterangan'] : '';
    $ceo    =  ($rsm['ceo_summary']) ? $rsm['ceo_summary'] : '';
    $is_ceo   =  ($rsm['ceo_result']) ? $rsm['ceo_result'] : '';
    $ceo_pic    =  ($rsm['ceo_pic']) ? $rsm['ceo_pic'] : '';
    $ceo_tanggal    =  ($rsm['ceo_tanggal']) ? $rsm['ceo_tanggal'] : '';
    $cfo    =  ($rsm['cfo_summary']) ? $rsm['cfo_summary'] : '';
    $cfo_pic    =  ($rsm['cfo_pic']) ? $rsm['cfo_pic'] : '';
    $cfo_tanggal    =  ($rsm['cfo_tanggal']) ? $rsm['cfo_tanggal'] : '';
    $kategori_oa     = ($rsm['kategori_oa']) ? $rsm['kategori_oa'] : '';
    $ongkos_angkut     = ($rsm['ongkos_angkut']) ? $rsm['ongkos_angkut'] : 0;
    $nilai_pbbkb     = ($rsm['nilai_pbbkb']) ? $rsm['nilai_pbbkb'] : 0;

    $revert_cfo    =  ($rsm['revert_cfo_summary']) ? $rsm['revert_cfo_summary'] : '';
    $revert_ceo    =  ($rsm['revert_ceo_summary']) ? $rsm['revert_ceo_summary'] : '';
    $revert    =  ($rsm['revert_ceo']) ? $rsm['revert_ceo'] : '';


    $dt9   = ($rsm['subtotal']) ? $rsm['subtotal'] : '';
    $dt10    = ($rsm['volume_po']) ? $rsm['volume_po'] : '';
    $dt11    = ($rsm['ppn_11']) ? $rsm['ppn_11'] : '';
    $dt12    = ($rsm['pph_22']) ? $rsm['pph_22'] : '';
    $dt13    = ($rsm['pbbkb']) ? $rsm['pbbkb'] : '';
    $dt14    = ($rsm['total_order']) ? $rsm['total_order'] : '';
    $iuran_migas = ($rsm['iuran_migas']) ? $rsm['iuran_migas'] : '';
    $nominal_iuran = ($rsm['nominal_migas']) ? $rsm['nominal_migas'] : '';
    $kategori_plat = ($rsm['kategori_plat']) ? $rsm['kategori_plat'] : '';
} else {
    $idr = null;
    $action     = "add";
    $section     = "PO Suplier";
    $rsm         = array();
    $dt1         = "";
    $dt8         = "";
    $ket        = "";
    $dt10         = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "ckeditor"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1><?php echo $section; ?></h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <form action="<?php echo ACTION_CLIENT . '/vendor-po-new.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Nomor PO *</label>
                                        <div class="col-md-8">
                                            <input type="text" name="dt2" id="dt2" class="form-control" value="<?php echo $rsm['nomor_po'] ?? null; ?>" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Tanggal PO *</label>
                                        <div class="col-md-4">
                                            <?php if (!$dt1) { ?>
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="font-size:12px;"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" name="dt1" id="dt1" class="form-control datepicker" required data-rule-dateNL="1" value="<?php echo $dt1; ?>" autocomplete="off" />
                                                </div>
                                            <?php } else { ?>
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="font-size:12px;"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" name="dt1" id="dt1" class="form-control" required data-rule-dateNL="1" value="<?php echo $dt1; ?>" autocomplete="off" />
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Produk *</label>
                                        <div class="col-md-8">
                                            <select name="dt3" id="dt3" class="form-control select2" style="width:100%;" required <?php echo ($rsm['id_produk'] ? 'disabled' : ''); ?>>
                                                <option></option>
                                                <?php $con->fill_select("id_master", "concat(jenis_produk,' - ',merk_dagang)", "pro_master_produk", $rsm['id_produk'], "where is_active=1", "no_urut", false); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Terminal *</label>
                                        <div class="col-md-8">
                                            <select name="dt6" id="dt6" class="form-control select2" style="width:100%;" required <?php echo ($rsm['id_terminal'] ? 'disabled' : ''); ?>>
                                                <option></option>
                                                <?php
                                                $sqlOpt01 = "
													select a.id_master, concat(a.nama_terminal,' - ',a.tanki_terminal,' - ',a.lokasi_terminal) as terminal 
													from pro_master_terminal a 
													join pro_master_cabang b on a.id_cabang = b.id_master 
													where a.is_active = 1 
													order by a.id_master 
												";
                                                $resOpt01 = $con->getResult($sqlOpt01);
                                                if (count($resOpt01) > 0) {
                                                    foreach ($resOpt01 as $arrOpt01) {
                                                        $selected = ($rsm['id_terminal'] == $arrOpt01['id_master'] ? 'selected' : '');
                                                        echo '<option value="' . $arrOpt01['id_master'] . '" ' . $selected . '>' . $arrOpt01['terminal'] . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Vendor *</label>
                                        <div class="col-md-8">
                                            <select name="dt5" id="dt5" class="form-control select2" style="width:100%;" required <?php echo ($rsm['id_vendor'] ? 'disabled' : ''); ?>>
                                                <option></option>
                                                <?php $con->fill_select("id_master", "nama_vendor", "pro_master_vendor", $rsm['id_vendor'], "where is_active=1", "id_master", false); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Terms *</label>
                                        <div class="col-md-4">
                                            <select name="terms" id="terms" class="form-control select2" style="width:100%;" required>
                                                <option></option>
                                                <option value="COD" <?php echo ($rsm['terms'] == 'COD' ? 'selected' : ''); ?>>C.O.D</option>
                                                <option value="NET" <?php echo ($rsm['terms'] == 'NET' ? 'selected' : ''); ?>>NET</option>
                                                <option value="CBD" <?php echo ($rsm['terms'] == 'CBD' ? 'selected' : ''); ?>>CBD</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="text" id="terms_day" name="terms_day" class="form-control hitung1" <?php echo ($rsm['terms'] != 'NET' ? 'readonly' : ''); ?> value="<?php echo $rsm['terms_day']; ?>" maxlength="3" />
                                                <span class="input-group-addon" style="font-size:12px;">Hari</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Kode Tax *</label>
                                        <div class="col-md-4">
                                            <select name="kd_tax" id="kd_tax" class="form-control select2" style="width:100%;" required>
                                                <option></option>
                                                <option value="E" <?php echo ($rsm['kd_tax'] == 'E' ? 'selected' : ''); ?>>E</option>
                                                <option value="EC" <?php echo ($rsm['kd_tax'] == 'EC' ? 'selected' : ''); ?>>EC</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Volume PO *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <input type="text" id="dt10" name="dt10" class="form-control hitung1" value="<?php echo $dt10; ?>" required />
                                                <span class="input-group-addon" style="font-size:12px;">Liter</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Harga Dasar *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt8" name="dt8" class="form-control hitung" required value="<?php echo $dt8; ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="kategori_oa" id="kategori_oa" class="form-control">
                                                <option <?php echo $kategori_oa == 1 ? "selected" : "" ?> value="1">Tanpa OA</option>
                                                <option <?php echo $kategori_oa == 2 ? "selected" : "" ?> value="2">Dengan OA</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row <?php echo $kategori_oa == 2 ? "" : "hide" ?>" id="row_oa">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Ongkos Angkut *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="ongkos_angkut" name="ongkos_angkut" class="form-control hitung" value="<?php echo $ongkos_angkut ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row <?php echo $kategori_oa == 2 ? "" : "hide" ?>" id="row-plat">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Kategori Plat *</label>
                                        <div class="col-md-4">
                                            <select name="kategori_plat" id="kategori_plat" class="form-control select2" style="width:100%;">
                                                <option></option>
                                                <option value="Hitam" <?= $kategori_plat == "Hitam" ? 'selected' : '' ?>>Hitam</option>
                                                <option value="Kuning" <?= $kategori_plat == "Kuning" ? 'selected' : '' ?>>Kuning</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Sub Total *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt9" name="dt9" class="form-control hitung" required value="<?php echo $dt9; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="row-dt11">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">PPN 11% *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt11" name="dt11" class="form-control hitung" required value="<?php echo $dt11; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="row-dt12">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">PPH 22 *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt12" name="dt12" class="form-control hitung" required value="<?php echo $dt12; ?>" readonly />

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">PBBKB *</label>
                                        <div class="col-md-6">
                                            <select name="pbbkb_tawar" id="pbbkb_tawar" class="form-control select2" required>
                                                <option></option>
                                                <?php $con->fill_select("nilai_pbbkb", "concat(nilai_pbbkb, ' %')", "pro_master_pbbkb", $rsm['nilai_pbbkb'], "", "", false); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt13" name="dt13" class="form-control hitung" value="<?php echo isset($dt13) ? $dt13 : '0'; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Iuran Migas</label>
                                        <div class="col-md-5">
                                            <input type="checkbox" id="iuran_migas" name="iuran_migas" value="1" <?= $iuran_migas == '1' ? 'checked' : '' ?>> Centang jika PO ini ada iuran migas
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="nominal_iuran" name="nominal_iuran" class="form-control text-right hitung1" readonly autocomplete="off" value="<?= $nominal_iuran ? $nominal_iuran : 0 ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Total Order *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt14" name="dt14" class="form-control hitung" required value="<?php echo $dt14; ?>" readonly />

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Catatan PO*</label>
                                        <div class="col-md-8">
                                            <textarea id="ket" name="ket" class="form-control" required><?php echo $ket; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />
                            <?php if ($rsm['revert_cfo'] == 1) { ?>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Catatan Pengembalian CFO</label>
                                            <div class="col-md-8">
                                                <div class="form-control" style="height:auto"><?php echo $revert_cfo; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if ($rsm['revert_ceo'] == 1) { ?>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Catatan Pengembalian CEO</label>
                                            <div class="col-md-8">
                                                <div class="form-control" style="height:auto"><?php echo $revert_ceo; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>



                            <?php if ($rsm['cfo_result'] == 1 && $rsm['revert_cfo'] == 0) { ?>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Catatan CFO *</label>
                                            <div class="col-md-8">
                                                <div class="form-control" style="height:auto"><i><?php echo $cfo . "<br>" . $cfo_pic . " - " . date("d/m/Y H:i:s", strtotime($cfo_tanggal)) . " WIB"; ?></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>

                            <?php if ($rsm['ceo_result'] == 1 && $rsm['revert_ceo'] == 0) { ?>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Catatan CEO *</label>
                                            <div class="col-md-8">
                                                <div class="form-control" style="height:auto"><i><?php echo $ceo . "<br>" . $ceo_pic . " - " . date("d/m/Y H:i:s", strtotime($ceo_tanggal)) . " WIB"; ?></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>

                            <div style="margin-bottom:15px;">
                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                <button type="submit" class="btn btn-primary jarak-kanan <?= ($is_ceo == '1' && $revert == '0') ? 'hide' : '' ?>" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                                    <i class="fa fa-save jarak-kanan"></i> Simpan</button>

                                <a href="<?php echo BASE_URL_CLIENT . '/vendor-po-new.php'; ?>" class="btn btn-default" style="min-width:90px;">
                                    <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                            </div>

                            <p><small>* Wajib Diisi</small></p>

                        </div>
                    </div>
                </form>

                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>




    <div class="modal fade" id="validasi_vol_terima" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-md">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Informasi</h4>
                </div>
                <div class="modal-body vol_info"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function customRound(num) {
                // Check if the number is negative
                if (num < 0) {
                    return Math.ceil(num - 0.5); // For negative numbers, round up
                }

                // For positive numbers
                const decimalPart = num - Math.floor(num); // Get the decimal part

                if (decimalPart < 0.5) {
                    return Math.floor(num); // Round down
                } else {
                    return Math.floor(num) + 1; // Round up
                }
            }

            $("#iuran_migas").on("ifChecked", function() {
                var plat = $('#kategori_plat').val();
                var kodeTax = $('#kd_tax').val();
                var pbbkb_tawar = $('#pbbkb_tawar').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var pbbkb = parseFloat($('#dt13').val()) || 0;
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;

                var kat_oa = $('#kategori_oa').val();
                // var iuran_migas = (customRound((hargaDasar * 0.25) / 100) * volumePO);
                var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;

                if (kat_oa == 1) {
                    var subTotal = (volumePO * hargaDasar) + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = ((volumePO * hargaDasar) * 0.3) / 100;
                    }
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                } else {
                    var subTotal = (volumePO * (hargaDasar + ongkos_angkut)) + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = ((volumePO * hargaDasar) * 0.3) / 100;
                    }
                    if (plat == "Hitam" || plat == "") {
                        var ppn11 = (11 * (volumePO * (hargaDasar + ongkos_angkut))) / 100;
                    } else {
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                    }
                }

                if (pbbkb_tawar != "") {
                    var total = volumePO * hargaDasar;
                    var hasil = (total * pbbkb_tawar) / 100;
                } else {
                    var hasil = pbbkb;
                }

                var totalOrder = (subTotal + ppn11 + pph + hasil);

                // // Tampilkan hasil di input Sub Total
                $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt13').val(hasil.toFixed(0));
                $('#nominal_iuran').val(iuran_migas.toFixed(0));
                $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#iuran_migas').attr("checked", true);
                $('#nominal_iuran').removeAttr("readonly", true);
            }).on("ifUnchecked", function() {
                var plat = $('#kategori_plat').val();
                var kodeTax = $('#kd_tax').val();
                var pbbkb_tawar = $('#pbbkb_tawar').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var pbbkb = parseFloat($('#dt13').val()) || 0;
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;

                var kat_oa = $('#kategori_oa').val();

                if (kat_oa == 1) {
                    var subTotal = volumePO * hargaDasar;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100;
                    }
                    var ppn11 = (11 * subTotal) / 100;
                } else {
                    var subTotal = volumePO * (hargaDasar + ongkos_angkut);
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = ((volumePO * hargaDasar) * 0.3) / 100;
                    }
                    if (plat == "Hitam" || plat == "") {
                        var ppn11 = (11 * subTotal) / 100;
                    } else {
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                    }
                }

                if (pbbkb_tawar != "") {
                    var total = volumePO * hargaDasar;
                    var hasil = (total * pbbkb_tawar) / 100;
                } else {
                    var hasil = pbbkb;
                }

                var iuran_migas = 0;

                var totalOrder = (subTotal + ppn11 + pph + hasil + iuran_migas);

                // // Tampilkan hasil di input Sub Total
                $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt13').val(hasil.toFixed(0));
                $('#nominal_iuran').val(iuran_migas.toFixed(0));
                $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#iuran_migas').attr("checked", false);
                $('#nominal_iuran').attr("readonly", true);
            });

            $("#kategori_plat").change(function() {
                var val = $(this).val();
                var kodeTax = $('#kd_tax').val();
                var pbbkb_tawar = $('#pbbkb_tawar').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var pbbkb = parseFloat($('#dt13').val()) || 0;
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;
                var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;
                var kat_oa = $('#kategori_oa').val();

                if (kat_oa == 1) {
                    var subTotal = volumePO * hargaDasar + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100; // Pembulatan tanpa desimal
                    }

                    if (pbbkb_tawar != "") {
                        var total = volumePO * hargaDasar;
                        var hasil = (total * pbbkb_tawar) / 100;
                    } else {
                        var hasil = pbbkb;
                    }
                    // Hitung Sub Total
                    // var ppn11 = Math.round((11 * subTotal) / 100);
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;

                    var totalOrder = (subTotal + ppn11 + pph + hasil);

                    // // Tampilkan hasil di input Sub Total
                    $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                    $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                    $('#dt13').val(hasil.toFixed(0));
                    // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                    $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    // $('#iuran_migas').attr("checked", false);
                    // $('.icheckbox_square-blue').removeClass("checked");
                } else {
                    if (val == "Hitam" || val == "") {
                        var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                        var pph = 0;
                        if (kodeTax == 'EC') {
                            pph = ((volumePO * hargaDasar) * 0.3) / 100; // Pembulatan tanpa desimal
                        }

                        if (pbbkb_tawar != "") {
                            var total = volumePO * hargaDasar;
                            var hasil = (total * pbbkb_tawar) / 100;
                        } else {
                            var hasil = pbbkb;
                        }
                        // Hitung Sub Total
                        // var ppn11 = Math.round((11 * subTotal) / 100);
                        var ppn11 = (11 * (volumePO * (hargaDasar + ongkos_angkut))) / 100;

                        var totalOrder = (subTotal + ppn11 + pph + hasil);

                        // // Tampilkan hasil di input Sub Total
                        $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                        $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                        $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                        $('#dt13').val(hasil.toFixed(0));
                        // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                        $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                        // $('#iuran_migas').attr("checked", false);
                        // $('.icheckbox_square-blue').removeClass("checked");
                    } else {
                        $("#dt11").val("");
                        $("#dt12").val("");

                        var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                        var pph = 0;
                        if (kodeTax == 'EC') {
                            pph = ((volumePO * hargaDasar) * 0.3) / 100; // Pembulatan tanpa desimal
                        }
                        if (pbbkb_tawar != "") {
                            var total = volumePO * hargaDasar;
                            var hasil = (total * pbbkb_tawar) / 100;
                        } else {
                            var hasil = pbbkb;
                        }
                        // Hitung Sub Total
                        // var ppn11 = Math.round((11 * subTotal) / 100);
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;

                        var totalOrder = (subTotal + ppn11 + pph + hasil);

                        // // Tampilkan hasil di input Sub Total
                        $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                        $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                        $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                        $('#dt13').val(hasil.toFixed(0));
                        // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                        $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                        // $('#iuran_migas').attr("checked", false);
                        // $('.icheckbox_square-blue').removeClass("checked");
                    }
                }
            })

            // Format angka dengan plugin number
            $(".hitung1").number(true, 0, ".", ",");
            $(".hitung").number(true, 2, ".", ",");

            $('.hitung, .hitung1').on('input', function() {
                // Ambil nilai Volume PO dan Harga Dasar
                var plat = $('#kategori_plat').val();
                var kodeTax = $('#kd_tax').val();
                var pbbkb_tawar = $('#pbbkb_tawar').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var pbbkb = parseFloat($('#dt13').val()) || 0;
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;
                var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;

                var kat_oa = $('#kategori_oa').val();

                if (kat_oa == 1) {
                    var subTotal = volumePO * hargaDasar + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100;
                    }
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                } else {
                    var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = ((volumePO * hargaDasar) * 0.3) / 100;
                    }
                    if (plat == "Hitam" || plat == "") {
                        var ppn11 = (11 * (volumePO * (hargaDasar + ongkos_angkut))) / 100;
                    } else {
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                    }
                }

                if (pbbkb_tawar != "") {
                    var total = volumePO * hargaDasar;
                    var hasil = (total * pbbkb_tawar) / 100;
                } else {
                    var hasil = pbbkb;
                }
                var iuran_migas = 0;

                // Hitung Sub Total
                // var ppn11 = Math.round((11 * subTotal) / 100);

                var totalOrder = (subTotal + ppn11 + pph + hasil);

                // // Tampilkan hasil di input Sub Total
                $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt13').val(hasil.toFixed(0));
                $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                // $('#iuran_migas').attr("checked", false);
                // $('.icheckbox_square-blue').removeClass("checked");
            });

            var formValidasiCfg = {
                submitHandler: function(form) {
                    if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
                        swal.fire({
                            icon: "warning",
                            width: '350px',
                            allowOutsideClick: false,
                            html: '<p style="font-size:14px; font-family:arial;">Kolom [Ongkos Angkut] pada tabel rincian harga belum diisi</p>'
                        });
                    } else if ($("#kd_tax").val() == 'EC' && $("#pphnya").val() == '') {
                        swal.fire({
                            icon: "warning",
                            width: '350px',
                            allowOutsideClick: false,
                            html: '<p style="font-size:14px; font-family:arial;">Kolom [PPH 22] belum diisi</p>'
                        });
                    } else if ($("#terms").val() == 'NET' && $("#terms_day").val() == '') {
                        swal.fire({
                            icon: "warning",
                            width: '350px',
                            allowOutsideClick: false,
                            html: '<p style="font-size:14px; font-family:arial;">Kolom [Hari untuk terms NET] belum diisi</p>'
                        });
                    } else if ($("#iuran_migas").is(":checked") && $("#nominal_iuran").val() == 0) {
                        swal.fire({
                            icon: "warning",
                            width: '350px',
                            allowOutsideClick: false,
                            html: '<p style="font-size:14px; font-family:arial;">Nominal iuran migas belum di isi</p>'
                        });
                    } else {
                        $("body").addClass("loading");
                        $.ajax({
                            type: 'POST',
                            url: base_url + "/web/action/vendor-po-new.php",
                            data: {
                                act: 'cek',
                                q1: $("input[name='idr']").val(),
                                q2: $("#dt2").val()
                            },
                            cache: false,
                            dataType: 'json',
                            success: function(data) {
                                if (!data.hasil) {
                                    $("body").removeClass("loading");
                                    swal.fire({
                                        icon: "warning",
                                        width: '350px',
                                        allowOutsideClick: false,
                                        html: '<p style="font-size:14px; font-family:arial;">' + data.pesan + '</p>'
                                    });
                                } else {
                                    form.submit();
                                }
                            }
                        });
                    }
                }
            };
            $("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

            $("#kd_tax").on("change", function() {
                var plat = $('#kategori_plat').val();
                let nilai = $(this).val();
                var kategori_oa = $('#kategori_oa').val();
                var kodeTax = $('#kd_tax').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;
                var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;
                var pbbkb = $("#pbbkb_tawar").val();

                if (kategori_oa == 1) {
                    var subTotal = volumePO * hargaDasar + iuran_migas;
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                } else {
                    var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                    if (plat == "Hitam" || plat == "") {
                        var ppn11 = (11 * (volumePO * (hargaDasar + ongkos_angkut))) / 100;
                    } else {
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                    }
                }

                var pph = 0;
                if (kodeTax == 'EC') {
                    pph = ((volumePO * hargaDasar) * 0.3) / 100; // Pembulatan tanpa desimal
                } else {
                    pph = 0;
                }

                var iuran_migas = 0;

                var total = volumePO * hargaDasar;
                var hasil = (total * pbbkb) / 100;

                var totalOrder = (subTotal + ppn11 + pph + hasil + iuran_migas);

                $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#dt12').val(pph.toFixed(0));
                $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                // // $('#iuran_migas').attr("checked", false);
                // // $('.icheckbox_square-blue').removeClass("checked");
            });

            // $("#kd_tax").on("change", function() {
            //     let nilai = $(this).val();
            //     if (nilai == 'E') {
            //         $(".form-group:has(#dt12)").hide(); // Menghilangkan semua elemen terkait PPH 22
            //     } else {
            //         $(".form-group:has(#dt12)").show(); // Menampilkan kembali semua elemen terkait PPH 22
            //         $("#dt12").attr("readonly", "readonly").val(""); // Menambah readonly dan menghapus nilai
            //     }
            // });
            $("#terms").on("change", function() {
                let nilai = $(this).val();
                if (nilai == 'NET') $("#terms_day").removeAttr("readonly");
                else $("#terms_day").attr("readonly", "readonly");
            });

            $("#pbbkb_tawar").change(function() {
                var plat = $('#kategori_plat').val();
                var kategori_oa = $('#kategori_oa').val();
                var kodeTax = $('#kd_tax').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var pbbkb = $(this).val();
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;
                var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;

                if (kategori_oa == 1) {
                    var subTotal = volumePO * hargaDasar + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100; // Pembulatan tanpa desimal
                    }
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                } else {
                    var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = ((volumePO * hargaDasar) * 0.3) / 100; // Pembulatan tanpa desimal
                    }
                    if (plat == "Hitam" || plat == "") {
                        var ppn11 = (11 * (volumePO * (hargaDasar + ongkos_angkut))) / 100;
                    } else {
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                    }
                }
                var iuran_migas = 0;
                var total = volumePO * hargaDasar;
                var hasil = (total * pbbkb) / 100;

                var totalOrder = (subTotal + ppn11 + pph + hasil + iuran_migas);

                $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#dt13').val(hasil.toFixed(0));
                $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                // alert(hasil)
                // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                // $('#iuran_migas').attr("checked", false);
                // $('.icheckbox_square-blue').removeClass("checked");
            })

            $("#kategori_oa").change(function() {
                var val = $(this).val();

                if (val == 1) {
                    var kodeTax = $('#kd_tax').val();
                    var volumePO = parseFloat($('#dt10').val()) || 0;
                    var hargaDasar = parseFloat($('#dt8').val()) || 0;
                    var pbbkb = parseFloat($('#dt13').val()) || 0;
                    var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;

                    var subTotal = volumePO * hargaDasar + iuran_migas;
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100; // Pembulatan tanpa desimal
                    }

                    var totalOrder = (subTotal + ppn11 + pph + pbbkb);

                    $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                    $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal

                    $("#ongkos_angkut").val(0);
                    $("#row_oa").addClass("hide");
                    $("#row-plat").addClass("hide");
                    $("#kategori_plat").val(null).trigger("change");
                    $("#ongkos_angkut").removeAttr("required", true);
                    $("#kategori_plat").removeAttr("required", true);
                    // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                    // $('#iuran_migas').attr("checked", false);
                    // $('.icheckbox_square-blue').removeClass("checked");
                } else {
                    var kodeTax = $('#kd_tax').val();
                    var volumePO = parseFloat($('#dt10').val()) || 0;
                    var hargaDasar = parseFloat($('#dt8').val()) || 0;
                    var pbbkb = parseFloat($('#dt13').val()) || 0;
                    var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;

                    var subTotal = volumePO * hargaDasar + iuran_migas;
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100; // Pembulatan tanpa desimal
                    }

                    var totalOrder = (subTotal + ppn11 + pph + pbbkb);

                    $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                    $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal

                    $("#ongkos_angkut").val(0);
                    $("#row_oa").addClass("hide");
                    $("#row-plat").addClass("hide");
                    $("#kategori_plat").val(null).trigger("change");
                    $("#ongkos_angkut").removeAttr("required", true);
                    $("#kategori_plat").removeAttr("required", true);

                    $("#row_oa").removeClass("hide");
                    $("#row-plat").removeClass("hide");
                    $("#ongkos_angkut").prop("required", true);
                    $("#kategori_plat").prop("required", true);

                    // var iuran_migas = 0;
                    // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                    // $('#iuran_migas').attr("checked", false);
                    // $('.icheckbox_square-blue').removeClass("checked");
                }
            })

        });
    </script>
</body>

</html>