<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth   = new MyOtentikasi();
$enk    = decode($_SERVER['REQUEST_URI']);
$con    = new Connection();
$flash  = new FlashAlerts;

if (isset($enk['idr']) && $enk['idr'] !== '') {
    $action     = "update";
    $section    = "Edit Data";
    $idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
    $sql = "select * from pro_master_terminal where id_master = '" . $idr . "';";
    $rsm = $con->getRecord($sql);
    $attention = json_decode($rsm['att_terminal'], true);
    $chk = ($rsm['is_active']) ? "checked" : "";
} else {
    $rsm = null;
    $idr = null;
    $attention = [];
    $action     = "add";
    $section    = "Tambah Data";
    $chk        = "checked";
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("ckeditor", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1><?php echo $section . " Master Terminal"; ?></h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border bg-light-blue">
                                <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                            </div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT . '/master-terminal.php'; ?>" id="gform" name="gform" method="post" class="form-validasi" role="form" enctype="multipart/form-data">
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Nama Terminal *</label>
                                            <input type="text" id="nama" name="nama" class="form-control validate[required]" value="<?php echo $rsm['nama_terminal'] ?? null; ?>" autocomplete="off" />
                                        </div>
                                        <div class="col-sm-6 col-sm-top">
                                            <label>Tanki *</label>
                                            <input type="text" id="tanki" name="tanki" class="form-control validate[required]" value="<?php echo $rsm['tanki_terminal'] ?? null; ?>" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Cabang Invoice *</label>
                                            <select name="id_cabang" id="id_cabang" class="form-control validate[required] select2">
                                                <option></option>
                                                <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $rsm['id_cabang'], "where is_active=1 and id_master <> 1", "", false); ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Kategori Terminal *</label>
                                            <select name="kategori" id="kategori" class="form-control validate[required] select2">
                                                <option>--PILIH--</option>
                                                <option <?= $rsm['kategori_terminal'] == '1' ? 'selected' : '' ?> value="1">Depo</option>
                                                <option <?= $rsm['kategori_terminal'] == '2' ? 'selected' : '' ?> value="2">Dispenser</option>
                                                <option <?= $rsm['kategori_terminal'] == '3' ? 'selected' : '' ?> value="3">Truck Gantung</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 hide">
                                            <label>Area *</label>
                                            <select name="id_area" id="id_area" class="form-control validate[required] select2">
                                                <option></option>
                                                <?php $con->fill_select("id_master", "nama_area", "pro_master_area", $rsm['id_area'], "where is_active=1", "nama_area", false); ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Batas Atas Tanki (Liter)</label>
                                            <input type="text" id="batas_atas" name="batas_atas" class="form-control" value="<?php echo $rsm['batas_atas'] ?? 0; ?>" onkeypress="return onlyNumberKey(event)" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Batas Bawah Tanki (Liter)</label>
                                            <input type="text" id="batas_bawah" name="batas_bawah" class="form-control" value="<?php echo $rsm['batas_bawah'] ?? 0; ?>" onkeypress="return onlyNumberKey(event)" />
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Inisial </label>
                                            <input type="text" id="initial" name="initial" class="form-control validate[required]" value="<?php echo $rsm['initial'] ?? null; ?>" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Lokasi *</label>
                                            <input type="text" id="lokasi" name="lokasi" class="form-control validate[required]" value="<?php echo $rsm['lokasi_terminal'] ?? null; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Telp Terminal *</label>
                                            <input type="text" id="telp" name="telp" class="form-control validate[required]" value="<?php echo $rsm['telp_terminal'] ?? null; ?>" />
                                        </div>
                                        <div class="col-sm-6 col-sm-top">
                                            <label>Alamat *</label>
                                            <input type="text" id="alamat" name="alamat" class="form-control validate[required]" value="<?php echo $rsm['alamat_terminal'] ?? null; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6 col-sm-top">
                                            <label>Fax Terminal *</label>
                                            <input type="text" id="fax" name="fax" class="form-control validate[required]" value="<?php echo $rsm['fax_terminal'] ?? null; ?>" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label>PIC *</label>
                                            <input type="text" id="cc" name="cc" class="form-control validate[required]" value="<?php echo $rsm['cc_terminal'] ?? null; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6 col-sm-top">
                                            <label>Latitude *</label>
                                            <input type="text" id="latitude" name="latitude" class="form-control validate[required]" value="<?php echo $rsm['latitude'] ?? null; ?>" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Longitude *</label>
                                            <input type="text" id="longitude" name="longitude" class="form-control validate[required]" value="<?php echo $rsm['longitude'] ?? null; ?>" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-attention" style="margin-top:10px;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="25%">Attention</th>
                                                            <th class="text-center" width="23%">Posisi</th>
                                                            <th class="text-center" width="22%">No. HP</th>
                                                            <th class="text-center" width="25%">Email</th>
                                                            <th class="text-center" width="5%">
                                                                <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        if (count($attention) == 0) {
                                                            echo '<tr><td colspan="5" class="text-center">Tidak ada Attention</td></tr>';
                                                        } else {
                                                            $d = 0;
                                                            foreach ($attention as $dat3) {
                                                                $d++;
                                                                $att1 = $dat3['nama'];
                                                                $att2 = $dat3['posisi'];
                                                                $att3 = $dat3['hp'];
                                                                $att4 = $dat3['email'];
                                                        ?>
                                                                <tr>
                                                                    <td><input type="text" name="att1[]" id="<?php echo 'att1_' . $d; ?>" class="form-control" value="<?php echo $att1; ?>" /></td>
                                                                    <td><input type="text" name="att2[]" id="<?php echo 'att2_' . $d; ?>" class="form-control" value="<?php echo $att2; ?>" /></td>
                                                                    <td><input type="text" name="att3[]" id="<?php echo 'att3_' . $d; ?>" class="form-control" value="<?php echo $att3; ?>" /></td>
                                                                    <td><input type="text" name="att4[]" id="<?php echo 'att4_' . $d; ?>" class="form-control" value="<?php echo $att4; ?>" /></td>
                                                                    <td class="text-center">
                                                                        <span class="frmid" data-row-count="<?php echo $d; ?>"></span>
                                                                        <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                    </td>
                                                                </tr>
                                                        <?php }
                                                        } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-dokumen" style="margin-top:10px;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="35%">Perizinan</th>
                                                            <th class="text-center" width="15%">Masa Berlaku</th>
                                                            <th class="text-center" width="45%">Lampiran</th>
                                                            <th class="text-center" width="5%">
                                                                <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $cek1 = "select * from pro_master_terminal_detail where id_terminal = '" . $idr . "' order by id_td";
                                                        $row1 = $con->getResult($cek1);
                                                        if (count($row1) == 0) {
                                                            echo '<tr><td colspan="4" class="text-center">Tidak ada dokumen</td></tr>';
                                                        } else {
                                                            $d = 0;
                                                            foreach ($row1 as $dat1) {
                                                                $d++;
                                                                $idd    = $dat1['id_td'];
                                                                $linkAt = "";
                                                                $textAt = "";
                                                                $pathAt = $public_base_directory . '/files/uploaded_user/lampiran/' . $dat1['lampiran'];
                                                                $nameAt = $dat1['lampiran_ori'];
                                                                if ($dat1['lampiran'] && file_exists($pathAt)) {
                                                                    $linkAt = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=2&ktg=dokTerminal_" . $idr . "_" . $idd . "_&file=" . $nameAt);
                                                                    $textAt = '<a href="' . $linkAt . '"><i class="fa fa-paperclip jarak-kanan"></i>' . $nameAt . '</a>';
                                                                }
                                                        ?>
                                                                <tr>
                                                                    <td><?php echo $dat1['dokumen']; ?></td>
                                                                    <td><?php echo tgl_indo($dat1['masa_berlaku'], 'normal', 'db', '/'); ?></td>
                                                                    <td><?php echo $textAt; ?></td>
                                                                    <td class="text-center">
                                                                        <input type="hidden" name="<?php echo 'doknya[' . $idd . ']'; ?>" value="1" />
                                                                        <span class="frmid" data-row-count="<?php echo $d; ?>"></span>
                                                                        <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                                    </td>
                                                                </tr>
                                                        <?php }
                                                        } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (count($row1) > 0) {
                                        foreach ($row1 as $dat2) {
                                            echo '<input type="hidden" name="doksup[' . $dat2['id_td'] . ']" value="1" />';
                                        }
                                    } ?>
                                    <div class="form-group row">
                                        <div class="col-sm-8">
                                            <label>Catatan</label>
                                            <textarea name="note" id="note" class="form-control wysiwyg"><?php echo $rsm['catatan_terminal'] ?? null; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="checkbox">
                                                <label class="rtl">
                                                    <?php
                                                    // Set nilai default untuk checkbox
                                                    $isChecked = isset($rsm['is_active']) && $rsm['is_active'] == '1';
                                                    $chk = $isChecked ? 'checked' : '';
                                                    $value = $isChecked ? '1' : '0';
                                                    ?>
                                                    <input type="hidden" name="active" value="0" />
                                                    <input type="checkbox" name="active" id="active" value="1" class="form-control" <?php echo $chk; ?> /> Active
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                                <a href="<?php echo BASE_URL_CLIENT . "/master-terminal.php"; ?>" class="btn btn-default jarak-kanan">
                                                    <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                                <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                            </div>
                                        </div>
                                    </div>

                                    <hr style="margin:5px 0" />
                                    <div class="row">
                                        <div class="col-sm-12"><small>* Wajib Diisi</small></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>

    <script>
        function onlyNumberKey(evt) {
            let ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
                return false;
            return true;
        }
        $(document).ready(function() {
            $(".wysiwyg").ckeditor();
            var objSettingDate = {
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: "c-80:c+10",
                dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            };
            var objAttach = {
                onValidationComplete: function(form, status) {
                    if (status == true) {
                        $('#loading_modal').modal({
                            backdrop: "static"
                        });
                        for (instance in CKEDITOR.instances) {
                            CKEDITOR.instances[instance].updateElement();
                        }
                        form.validationEngine('detach');
                        form.submit();
                    }
                }
            };
            $(".table-attention").on("click", "button.addRow", function() {
                var tabel = $(this).parents(".table-attention");
                var rwTbl = tabel.find('tbody > tr:last');
                var rwNom = parseInt(rwTbl.find("span.frmid").data('rowCount'));
                var newId = (isNaN(rwNom)) ? 1 : parseInt(rwNom + 1);

                var objTr = $("<tr>");
                var objTd1 = $("<td>", {
                    class: "text-left"
                }).appendTo(objTr);
                var objTd2 = $("<td>", {
                    class: "text-left"
                }).appendTo(objTr);
                var objTd3 = $("<td>", {
                    class: "text-left"
                }).appendTo(objTr);
                var objTd4 = $("<td>", {
                    class: "text-left"
                }).appendTo(objTr);
                var objTd5 = $("<td>", {
                    class: "text-center"
                }).appendTo(objTr);
                objTd1.html('<input type="text" name="att1[]" id="att1_' + newId + '" class="form-control" autocomplete="off" />');
                objTd2.html('<input type="text" name="att2[]" id="att2_' + newId + '" class="form-control" autocomplete="off" />');
                objTd3.html('<input type="text" name="att3[]" id="att3_' + newId + '" class="form-control" autocomplete="off" />');
                objTd4.html('<input type="text" name="att4[]" id="att4_' + newId + '" class="form-control" autocomplete="off" />');
                objTd5.html('<span class="frmid" data-row-count="' + newId + '"></span><a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
                if (isNaN(rwNom)) {
                    rwTbl.remove();
                    rwTbl = $(".table-attention > tbody");
                    rwTbl.append(objTr);
                } else {
                    rwTbl.after(objTr);
                }
            });
            $(".table-attention").on("click", "a.hRow", function() {
                var tabel = $(this).parents(".table-attention");
                var jTbl = tabel.find("tr").length;
                if (jTbl > 1) {
                    var cRow = $(this).closest('tr');
                    cRow.remove();
                }
                if (jTbl == 2) {
                    var nRow = $(".table-attention > tbody");
                    nRow.append('<tr><td colspan="5" class="text-center">Tidak ada kompartemen</td></tr>');
                }
            });

            $(".table-dokumen").on("click", "button.addRow", function() {
                $("form#gform").validationEngine('detach');
                var tabel = $(this).parents(".table-dokumen");
                var rwTbl = tabel.find('tbody > tr:last');
                var rwNom = parseInt(rwTbl.find("span.frmid").data('rowCount'));
                var newId = (isNaN(rwNom)) ? 1 : parseInt(rwNom + 1);

                var objTr = $("<tr>");
                var objTd1 = $("<td>", {
                    class: "text-left"
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
                objTd1.html('<input type="text" name="newdok1[' + newId + ']" id="newdok1_' + newId + '" class="form-control" autocomplete="off" />');
                objTd2.html('<input type="text" name="newdok2[' + newId + ']" id="newdok2_' + newId + '" class="form-control" autocomplete="off" />');
                objTd3.html('<input type="file" name="newdok3[' + newId + ']" id="newdok3_' + newId + '" class="validate[funcCall[fileCheck]]" />');
                objTd4.html('<span class="frmid" data-row-count="' + newId + '"></span><a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
                if (isNaN(rwNom)) {
                    rwTbl.remove();
                    rwTbl = $(".table-dokumen > tbody");
                    rwTbl.append(objTr);
                } else {
                    rwTbl.after(objTr);
                }
                $("#newdok2_" + newId).datepicker(objSettingDate);
                $("form#gform").validationEngine('attach', objAttach);
            });
            $(".table-dokumen").on("click", "a.hRow", function() {
                var tabel = $(this).parents(".table-dokumen");
                var jTbl = tabel.find("tr").length;
                if (jTbl > 1) {
                    var cRow = $(this).closest('tr');
                    cRow.remove();
                }
                if (jTbl == 2) {
                    var nRow = $(".table-dokumen > tbody");
                    nRow.append('<tr><td colspan="4" class="text-center">Tidak ada dokumen</td></tr>');
                }
            });
        });
    </script>
</body>

</html>