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

$idr         = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$sesrole     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$sesuser     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$seswil     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesgroup     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);

if ($idr != "") {
    $sql = "
			select a.*, b.nama_cabang 
			from pro_master_ruangan a
			left join pro_master_cabang b on b.id_master = a.id_cabang 
			where 1=1 
			and a.is_active = 1
			and a.id_ruangan = '" . $idr . "'
		";
    $row         = $con->getRecord($sql);
    $action     = "update";
    $titleAct   = "Ubah Master Data Ruangan";
} else {
    $row         = array();
    $action     = "add";
    $titleAct   = "Tambah Master Data Ruangan";
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
                <form action="<?php echo ACTION_CLIENT . '/reservasi-ruangan-master.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Cabang *</label>
                                        <?php if ($sesrole == '1' || ($sesrole == '14' && $seswil == '1')) : ?>
                                            <div class="col-md-8">
                                                <select id="id_cabang" name="id_cabang" class="form-control select2" required>
                                                    <option></option>
                                                    <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $row['id_cabang'], "", "nama_cabang", false); ?>
                                                </select>
                                            </div>
                                        <?php elseif ($sesrole == '14'): ?>
                                            <div class="col-md-8">
                                                <select class="form-control select2" required disabled>
                                                    <option></option>
                                                    <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $seswil, "", "nama_cabang", false); ?>
                                                </select>
                                                <input type="hidden" value="<?= $seswil ?>" readonly name="id_cabang" id="id_cabang">
                                            </div>
                                        <?php else : ?>
                                            <div class="col-md-8">
                                                <select id="id_cabang" name="id_cabang" class="form-control select2" required>
                                                    <option></option>
                                                    <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", $row['id_cabang'], "", "nama_cabang", false); ?>
                                                </select>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Nama Ruangan *</label>
                                        <div class="col-md-8">
                                            <input type="text" id="nama_ruangan" name="nama_ruangan" class="form-control" value="<?php echo $row['nama_ruangan']; ?>" required />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Foto Ruangan</label>
                                        <div class="col-md-9">
                                            <?php
                                            $pathFile     = $public_base_directory . '/files/uploaded_user/lampiran';
                                            $labelFile     = 'Unggah Foto';
                                            $dataIcons     = '';
                                            $marginnya     = '0px';

                                            if ($row['attach_foto'] && file_exists($pathFile . $row['attach_foto'])) {
                                                $labelFile     = 'Ubah Foto';
                                                $linkPt     = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=89&ktg=" . $value['filenya'] . "&file=" . $value['file_upload_ori']);
                                                $dataIcons     = '
												<div style="margin-bottom:15px;">
													<a href="' . BASE_URL . '/files/uploaded_user/lampiran' . $row['attach_foto'] . '" target="_blank" title="download file"> 
														<img src="' . BASE_URL . '/files/uploaded_user/lampiran' . $row['attach_foto'] . '" style="width:250px;" />
													</a>
												</div>';
                                            }
                                            echo '
											<div class="rowuploadnya">
												' . $dataIcons . '
												<div class="simple-fileupload" style="margin-left:' . $marginnya . ';">
													<input type="file" name="attach_foto" id="attach_foto" class="form-inputfile" />
													<label for="attach_foto" class="label-inputfile">
														<div class="input-group input-group-sm">
															<div class="input-group-addon btn-primary"><i class="fa fa-upload"></i></div>
															<input type="text" class="form-control" placeholder="' . $labelFile . '" readonly />
														</div>
													</label>
												</div>
											</div>';
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                            <div style="margin-bottom:0px;">
                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                <input type="hidden" id="idr" name="idr" value="<?php echo $idr; ?>" />
                                <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                                    <i class="fa fa-save jarak-kanan"></i> Simpan</button>
                                <a href="<?php echo BASE_URL_CLIENT . '/reservasi-ruangan-master.php'; ?>" class="btn btn-default" style="min-width:90px;">
                                    <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                            </div>
                        </div>
                    </div>
                </form>

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
    </style>
    <script>
        $(document).ready(function() {
            var formValidasiCfg = {
                submitHandler: function(form) {
                    $("#loading_modal").modal({
                        keyboard: false,
                        backdrop: 'static'
                    });

                    let filenya = $("#attach_foto")[0].files;
                    let allowedExt = /(\.jpg|\.jpeg|\.png|\.gif)$/i;

                    let uplnya = false;
                    let isAllowed = false;
                    let filename, extnya;

                    if (filenya.length > 0) {
                        uplnya = true;
                        filename = filenya[0].name;
                        extnya = filename.substr(filename.lastIndexOf("."));
                        isAllowed = allowedExt.test(extnya);
                    }

                    if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
                        $("#loading_modal").modal("hide");
                        $.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
                        setErrorFocus($("#nup_fee"), $("form#gform"), false);
                    } else if (uplnya && !isAllowed) {
                        $("#loading_modal").modal("hide");
                        $.validator.showErrorField('attach_foto', "Harap upload file berupa gambar");
                        setErrorFocus($("#attach_foto"), $("form#gform"), false);
                    } else {
                        form.submit();
                    }
                }
            };
            $("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));
        });
    </script>
</body>

</html>