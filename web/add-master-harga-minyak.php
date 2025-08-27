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

$sesrole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
// if($sesrole != '21'){ 
// 	header("location: ".BASE_URL_CLIENT.'/home.php'); exit();
// }

$uRole     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$link1     = ($uRole == 6) ? BASE_URL_CLIENT . '/master-harga-minyak.php' : BASE_URL_CLIENT . '/master-approval-harga.php';
if ($idr['idr'] !== '' && isset($enk['idr'])) {
    $action     = "update";
    $section     = "Edit Harga Jual";
    $idr = htmlspecialchars($enk['idr'], ENT_QUOTES);
    list($id1, $id2, $id3, $id4) = explode("#*#", $idr);
    $cek = "select a.jenis_produk, a.merk_dagang, b.nama_area from pro_master_produk a, pro_master_area b where a.id_master = '" . $id4 . "' and b.id_master = '" . $id3 . "'";
    $row = $con->getRecord($cek);

    $cek2 = "select b.* from pro_master_harga_minyak b 
                where b.periode_awal = '" . $id1 . "' and periode_akhir = '" . $id2 . "' and b.id_area = '" . $id3 . "' and b.produk = '" . $id4 . "'";
    $row2 = $con->getRecord($cek2);
} else {
    $action     = "add";
    $section     = "Tambah Harga Jual";
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "ckeditor419"), "css" => array("jqueryUI"))); ?>

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
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                    </div>
                    <div class="box-body">
                        <form action="<?php echo ACTION_CLIENT . '/master-harga-minyak.php'; ?>" id="gform" name="gform" class="form-horizontal" method="post" role="form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Periode Awal *</label>
                                        <div class="col-md-4">
                                            <?php if ($action == "add") { ?>
                                                <input type="text" name="periode_awal" id="periode_awal" class="form-control datepicker" required data-rule-dateNL="1" />
                                            <?php } else if ($action != "add" && $row2['is_edited'] == 1) { ?>
                                                <input type="hidden" name="periode_awal" id="periode_awal" value="<?php echo date("d/m/Y", strtotime($id1)); ?>" />
                                                <div class="form-control"><?php echo date("d/m/Y", strtotime($id1)); ?></div>
                                            <?php } else { ?>
                                                <input type="text" name="periode_awal_edit" id="periode_awal" class="form-control datepicker" required data-rule-dateNL="1" value="<?php echo date("d/m/Y", strtotime($id1)); ?>" />
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Periode Akhir *</label>
                                        <div class="col-md-4">
                                            <?php if ($action == "add") { ?>
                                                <input type="text" name="periode_akhir" id="periode_akhir" class="form-control datepicker" required data-rule-dateNL="1" />
                                            <?php } else if ($action != "add" && $row2['is_edited'] == 1) { ?>
                                                <input type="hidden" name="periode_akhir" id="periode_akhir" value="<?php echo date("d/m/Y", strtotime($id2)); ?>" />
                                                <div class="form-control"><?php echo date("d/m/Y", strtotime($id2)); ?></div>
                                            <?php } else { ?>
                                                <input type="text" name="periode_akhir_edit" id="periode_akhir" class="form-control datepicker" required data-rule-dateNL="1" value="<?php echo date("d/m/Y", strtotime($id2)); ?>" />
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="harga_jual_error_txt"></div>
                            <?php if ($action == "add") { ?>
                                <hr style="margin:10px 0px 20px; border-top:4px double #ddd" />

                                <div class="row list-harga">
                                    <div class="col-md-12">
                                        <div class="pad bg-gray table-jual">
                                            <a class="btn btn-action btn-primary addRow pull-right"><i class="fa fa-plus"></i></a>
                                            <p class="frmid" data-row-count="1"><b>List Harga Jual</b></p>
                                        </div>
                                        <div class="jual-detil">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group form-group-sm">
                                                        <label class="control-label col-md-4">Area *</label>
                                                        <div class="col-md-8">
                                                            <select name="area[1]" id="area1" class="form-control select2" required>
                                                                <option></option>
                                                                <?php $con->fill_select("id_master", "nama_area", "pro_master_area", "", "where is_active=1", "", false); ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group form-group-sm">
                                                        <label class="control-label col-md-4">Produk *</label>
                                                        <div class="col-md-8">
                                                            <select name="produk[1]" id="produk1" class="form-control select2" required>
                                                                <option></option>
                                                                <?php $con->fill_select("id_master", "concat(jenis_produk,' - ',merk_dagang)", "pro_master_produk", "", "where is_active=1", "1", false); ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <p class="text-left" style="font-size:14px; font-weight:bold;">Tabel Harga</p>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive table-harga">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center" rowspan="2" width="130">Harga Dasar<br />(Pricelist yang dishare)</th>
                                                                    <th class="text-center">TIER I</th>
                                                                    <th class="text-center" width="130">TIER II</th>
                                                                    <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("21"))) { ?>
                                                                        <th class="text-center" colspan="2">TIER III</th>
                                                                    <?php } ?>
                                                                    <!-- <th class="text-center" colspan="2">KET. LAIN</th> -->
                                                                </tr>
                                                                <tr>
                                                                    <th class="text-center" width="130">BM</th>

                                                                    <th class="text-center">OM</th>
                                                                    <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("21"))) { ?>
                                                                        <th class="text-center" width="130">COO</th>
                                                                        <th class="text-center" width="130">CEO</th>
                                                                    <?php } ?>
                                                                    <!-- <th class="text-center" width="130">LOCO</th>
                                                                    <th class="text-center" width="130">SKP</th> -->
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $cek = "
                                                            select a.id_master, a.nilai_pbbkb, b.harga_normal, b.loco, b.skp, b.harga_sm, b.harga_om 
                                                            from pro_master_pbbkb a 
                                                            left join pro_master_harga_minyak b on a.id_master = b.pajak and b.periode_awal = '" . $id1 . "' and periode_akhir = '" . $id2 . "' 
                                                                and b.id_area = '" . $id3 . "' and b.produk = '" . $id4 . "' 
                                                            where a.id_master = 1 
                                                            order by 1
                                                        ";
                                                                $row = $con->getResult($cek);
                                                                if (count($row) > 0) {
                                                                    $nom = 0;
                                                                    foreach ($row as $thead) {
                                                                        $nom++;
                                                                        $idv = $thead['id_master'];
                                                                        echo '
                                                                <tr>
                                                                            <td>
                                                                                <input type="text" name="harga_nm[1][' . $idv . ']" id="harga_nm_' . $idv . '_1" class="form-control hitung input-sm" />
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="harga_sm[1][' . $idv . ']" id="harga_sm_' . $idv . '_1" class="form-control hitung input-sm" />
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="harga_om[1][' . $idv . ']" id="harga_om_' . $idv . '_1" class="form-control hitung input-sm" />
                                                                            </td>';
                                                                        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("21"))) {
                                                                            echo '
                                                                            <td>
                                                                                <input type="text" name="harga_coo[1][' . $idv . ']" id="harga_coo_' . $idv . '_1" class="form-control hitung input-sm" />
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="harga_ceo[1][' . $idv . ']" id="harga_ceo_' . $idv . '_1" class="form-control hitung input-sm" />
                                                                            </td>';
                                                                        }
                                                                        echo '</tr>';
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <p class="text-left" style="font-size:14px; font-weight:bold;">Catatan</p>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-sm">
                                                        <div class="col-md-12">
                                                            <textarea name="note[1]" id="note1" class="form-control wysiwyg"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            <?php } else if ($action == "update") { ?>
                                <hr style="margin:10px 0px 20px; border-top:4px double #ddd" />

                                <div class="row list-harga">
                                    <div class="col-md-12">
                                        <div class="pad bg-gray table-jual">
                                            <p class="frmid" data-row-count="1"><b>List Harga Jual</b></p>
                                        </div>
                                        <div class="jual-detil">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group form-group-sm">
                                                        <label class="control-label col-md-4">Area *</label>
                                                        <div class="col-md-8">
                                                            <input type="hidden" name="area" id="area" value="<?php echo $id3; ?>" />
                                                            <div class="form-control"><?php echo $row['nama_area']; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group form-group-sm">
                                                        <label class="control-label col-md-4">Produk *</label>
                                                        <div class="col-md-8">
                                                            <input type="hidden" name="produk" id="produk" value="<?php echo $id4; ?>" />
                                                            <div class="form-control"><?php echo $row['jenis_produk'] . ' - ' . $row['merk_dagang']; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <p class="text-left" style="font-size:14px; font-weight:bold;">Tabel Harga</p>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive table-harga">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center" rowspan="2" width="">Harga Dasar<br />(Pricelist yang dishare)</th>
                                                                    <th class="text-center">TIER I</th>
                                                                    <th class="text-center" width="130">TIER II</th>
                                                                    <th class="text-center" colspan="2">TIER III</th>

                                                                </tr>
                                                                <tr>
                                                                    <th class="text-center">BM</th>
                                                                    <th class="text-center">OM</th>
                                                                    <th class="text-center" width="130">COO</th>
                                                                    <th class="text-center" width="130">CEO</th>

                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $cek = "
                                                            select a.id_master, a.nilai_pbbkb, b.note_jual, b.harga_normal, b.loco, b.skp, b.harga_sm, b.harga_om, b.harga_coo, b.harga_ceo 
                                                            from pro_master_pbbkb a 
                                                            left join pro_master_harga_minyak b on a.id_master = b.pajak and b.periode_awal = '" . $id1 . "' and periode_akhir = '" . $id2 . "' 
                                                                and b.id_area = '" . $id3 . "' and b.produk = '" . $id4 . "' 
                                                            where a.id_master = 1 order by 1
                                                        ";
                                                                $row = $con->getResult($cek);
                                                                if (count($row) > 0) {
                                                                    $nom = 0;
                                                                    $dt1 = "";
                                                                    foreach ($row as $thead) {
                                                                        $nom++;
                                                                        $idv = $thead['id_master'];
                                                                        $dt1 = ($thead['note_jual']) ? $thead['note_jual'] : "";

                                                                        echo '
                                                                <tr>
                                                                    <td>
                                                                        <input type="text" name="harga_nm[' . $idv . ']" id="harga_nm_' . $idv . '" class="form-control hitung input-sm" value="' . $thead['harga_normal'] . '" />
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="harga_sm[' . $idv . ']" id="harga_sm_' . $idv . '" class="form-control hitung input-sm" value="' . $thead['harga_sm'] . '" />
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="harga_om[' . $idv . ']" id="harga_om_' . $idv . '" class="form-control hitung input-sm" value="' . $thead['harga_om'] . '" />
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="harga_coo[' . $idv . ']" id="harga_coo_' . $idv . '" class="form-control hitung input-sm" value="' . $thead['harga_coo'] . '" />
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="harga_ceo[' . $idv . ']" id="harga_ceo_' . $idv . '" class="form-control hitung input-sm" value="' . $thead['harga_ceo'] . '" />
                                                                    </td>
                                                                   
                                                                </tr>';
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <p class="text-left" style="font-size:14px; font-weight:bold;">Catatan</p>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-sm">
                                                        <div class="col-md-12">
                                                            <textarea name="note" id="note" class="form-control wysiwyg"><?php echo $dt1; ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <hr style="border-top:4px double #ddd; margin:25px 0 20px;" />

                            <div style="margin-bottom:15px;">
                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                                    <i class="fa fa-save jarak-kanan"></i> Simpan</button>
                                <a href="<?php echo $link1; ?>" class="btn btn-default" style="min-width:90px;">
                                    <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                            </div>
                            <p><small>* Wajib Diisi</small></p>
                        </form>
                    </div>
                </div>

                <div class="hide" id="optArea">
                    <?php $con->fill_select("id_master", "nama_area", "pro_master_area", "", "where is_active=1", "", false); ?>
                </div>
                <div class="hide" id="optProduk">
                    <?php $con->fill_select("id_master", "concat(jenis_produk,' - ',merk_dagang)", "pro_master_produk", "", "where is_active=1", "1", false); ?>
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
                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>

    <style type="text/css">
        .table-jual {
            border: 1px solid #ddd;
        }

        .table-jual>p {
            margin: 0px;
            padding-right: 35px;
        }

        .jual-detil {
            padding: 20px;
            border: 1px solid #ddd;
        }
    </style>
    <script>
        $(document).ready(function() {
            var formValidasiCfg = {
                submitHandler: function(form) {
                    Swal.fire({
                        title: "Konfirmasi",
                        text: "Apakah Anda yakin ingin menyimpan data ini?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, Simpan",
                        cancelButtonText: "Batal",
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika pengguna mengonfirmasi, tampilkan loading modal
                            $("#loading_modal").modal({
                                keyboard: false,
                                backdrop: 'static'
                            });

                            for (instance in CKEDITOR.instances) {
                                CKEDITOR.instances[instance].updateElement();
                            }

                            let kosong = true;
                            let rowemp = true;
                            $("input[name*=harga_nm]").each(function(i, v) {
                                kosong = kosong && ($(v).val() == "");
                                if ($(v).val() != "") {
                                    let arrTmp = $(v).attr("id").split("harga_nm");
                                    if ($("#harga_sm" + arrTmp[1]).val() && $("#harga_om" + arrTmp[1]).val()) {
                                        rowemp = rowemp && true;
                                    } else {
                                        rowemp = rowemp && false;
                                    }
                                }
                            });

                            if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
                                $("#loading_modal").modal("hide");
                                $.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
                                setErrorFocus($("#nup_fee"), $("form#gform"), false);
                            } else if (kosong == true) {
                                $("#loading_modal").modal("hide");
                                Swal.fire({
                                    allowOutsideClick: false,
                                    icon: "warning",
                                    width: '350px',
                                    html: '<p style="font-size:14px; font-family:arial;">Kolom harga jual belum diisi sama sekali</p>'
                                });
                            } else if (rowemp == false) {
                                $("#loading_modal").modal("hide");
                                Swal.fire({
                                    allowOutsideClick: false,
                                    icon: "warning",
                                    width: '350px',
                                    html: '<p style="font-size:14px; font-family:arial;">Jika kolom harga jual pada PBBKB-nya sudah diisi,' +
                                        'maka kolom harga untuk BM, OM</p>'
                                });
                            } else {
                                // Submit formulir setelah semua validasi
                                form.submit();
                            }
                        }
                    });
                }

                // submitHandler: function(form) {


                //     $("#loading_modal").modal({
                //         keyboard: false,
                //         backdrop: 'static'
                //     });

                //     for (instance in CKEDITOR.instances) {
                //         CKEDITOR.instances[instance].updateElement();
                //     }

                //     let kosong = true;
                //     let rowemp = true;
                //     $("input[name*=harga_nm]").each(function(i, v) {
                //         kosong = kosong && ($(v).val() == "");
                //         if ($(v).val() != "") {
                //             let arrTmp = $(v).attr("id").split("harga_nm");
                //             if ($("#harga_sm" + arrTmp[1]).val() && $("#harga_om" + arrTmp[1]).val()) {
                //                 rowemp = rowemp && true;
                //             } else {
                //                 rowemp = rowemp && false;
                //             }
                //         }
                //     })

                //     if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
                //         $("#loading_modal").modal("hide");
                //         $.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
                //         setErrorFocus($("#nup_fee"), $("form#gform"), false);
                //     } else if (kosong == true) {
                //         $("#loading_modal").modal("hide");
                //         swal.fire({
                //             allowOutsideClick: false,
                //             icon: "warning",
                //             width: '350px',
                //             html: '<p style="font-size:14px; font-family:arial;">Kolom harga jual belum diisi sama sekali</p>'
                //         });
                //     } else if (rowemp == false) {
                //         $("#loading_modal").modal("hide");
                //         swal.fire({
                //             allowOutsideClick: false,
                //             icon: "warning",
                //             width: '350px',
                //             html: '<p style="font-size:14px; font-family:arial;">Jika kolom harga jual pada PBBKB-nya sudah diisi,' +
                //                 'maka kolom harga untuk BM, OM</p>'
                //         });
                //     } else {
                //         //return false;
                //         form.submit();
                //     }
                // }
            };
            $("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

            $(".hitung").number(true, 0, ".", ",");
            $(".wysiwyg").ckeditor();

            $(".table-jual").on("click", "a.addRow", function() {
                var tabel = $(this).parents(".list-harga");
                var rwTbl = $(".list-harga:last");
                var rwNom = parseInt(rwTbl.find("p.frmid").data('rowCount'));
                var newId = (isNaN(rwNom)) ? 1 : parseInt(rwNom + 1);
                var objHrg = tabel.find(".table-harga").clone();

                objHrg.find("input").each(function() {
                    var oID = $(this).attr('id');
                    var tID = oID.split('_');
                    var nID = tID[0] + '_' + tID[1] + '_' + tID[2] + '_' + newId;
                    var nNM = tID[0] + '_' + tID[1] + '[' + newId + '][' + tID[2] + ']';
                    $(this).attr('id', nID);
                    $(this).attr('name', nNM);
                });

                var objJual =
                    '<hr class="batasnyabos' + newId + '" style="margin:30px 0px 20px; border-top:4px double #ddd" />' +
                    '<div class="row list-harga">' +
                    '<div class="col-md-12">' +
                    '<div class="pad bg-gray table-jual">' +
                    '<a class="btn btn-action btn-danger hRow pull-right"><i class="fa fa-times"></i></a>' +
                    '<p class="frmid" data-row-count="' + newId + '"><b>List Harga Jual</b></p>' +
                    '</div>' +
                    '<div class="jual-detil">' +
                    '<div class="row">' +
                    '<div class="col-md-8">' +
                    '<div class="form-group form-group-sm">' +
                    '<label class="control-label col-md-4">Area *</label>' +
                    '<div class="col-md-8">' +
                    '<select name="area[' + newId + ']" id="area' + newId + '" class="form-control select2" required>' +
                    '<option></option>' + $("#optArea").html() + '' +
                    '</select>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-8">' +
                    '<div class="form-group form-group-sm">' +
                    '<label class="control-label col-md-4">Produk *</label>' +
                    '<div class="col-md-8">' +
                    '<select name="produk[' + newId + ']" id="produk' + newId + '" class="form-control select2" required>' +
                    '<option></option>' + $("#optProduk").html() + '' +
                    '</select>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +

                    '<p class="text-left" style="font-size:14px; font-weight:bold;">Tabel Harga</p>' +
                    '<div class="row">' +
                    '<div class="col-md-12">' +
                    '<div class="table-responsive table-harga">' +
                    objHrg.html() + '' +
                    '</div>' +
                    '</div>' +
                    '</div>' +

                    '<p class="text-left" style="font-size:14px; font-weight:bold;">Catatan</p>' +
                    '<div class="row">' +
                    '<div class="col-md-12">' +
                    '<div class="form-group form-group-sm">' +
                    '<div class="col-md-12">' +
                    '<textarea name="note[' + newId + ']" id="note' + newId + '" class="form-control"></textarea>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +

                    '</div>' +
                    '</div>' +
                    '</div>';

                rwTbl.after(objJual);
                $("#area" + newId + ", #produk" + newId).select2({
                    placeholder: "Pilih Salah Satu",
                    allowClear: true
                });
                $("#note" + newId).ckeditor();
                $(".hitung").number(true, 0, ".", ",");

                tabel.parent().find(".frmid").each(function(i, v) {
                    $(v).html('<b>List Harga Jual Ke-' + (i + 1) + '</b>');
                });
            });

            $("#gform").on("click", ".list-harga a.hRow", function() {
                var tabel = $(this).parents(".list-harga");
                var rwNum = $(this).siblings().data('rowCount');

                tabel.remove();
                $(".batasnyabos" + rwNum).remove();
                $("#gform").find(".frmid").each(function(i, v) {
                    $(v).html('<b>List Harga Jual Ke-' + (i + 1) + '</b>');
                });
            });
        });
    </script>
</body>

</html>