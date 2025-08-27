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

$sql_cabang = "SELECT * FROM pro_master_cabang WHERE is_active = '1'";
$cabang = $con->getResult($sql_cabang);

if ($idr != "") {
    $sql = "SELECT a.*, b.nama_cabang FROM pro_master_approval_invoice a JOIN pro_master_cabang b ON a.cabang=b.id_master WHERE a.id_master = '" . $idr . "'";
    $rsm = $con->getRecord($sql);

    $chk = ($rsm['is_active']) ? "checked" : "";
    $dis = "readonly";
    $action = "update";
} else {
    $rsm = array();
    $chk = "checked";
    $dis = "";
    $action = "add";
}

?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>

<body class="skin-blue fixed">
    <style>
        .content-data {
            padding: 0px;
        }
    </style>
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Add Approval Invoice</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border bg-light-blue">
                                <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Please fill in this form below</h3>
                            </div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT . '/approval-inv.php'; ?>" id="gform_3" name="gform_3" method="post" class="form-validasi" role="form">
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label>Nama *</label>
                                            <input type="text" id="nama" name="nama" class="form-control validate[required]" value="<?php echo isset($rsm['nama']) ? $rsm['nama'] : ''; ?>" autocomplete="off" />
                                        </div>
                                        <div class="col-sm-4 col-sm-top">
                                            <label>Jabatan *</label>
                                            <input type="text" id="jabatan" name="jabatan" class="form-control validate[required]" value="<?php echo isset($rsm['jabatan']) ? $rsm['jabatan'] : ''; ?>" autocomplete="off" />
                                        </div>
                                        <div class="col-sm-4 col-sm-top">
                                            <label>Cabang *</label>
                                            <select class="form-control validate[required]" name="cabang" id="cabang">
                                                <option value=""></option>
                                                <?php foreach ($cabang as $key) : ?>
                                                    <option <?= isset($rsm['cabang']) && $rsm['cabang'] == $key['id_master'] ? 'selected' : '' ?> value="<?= $key['id_master'] ?>"><?= $key['nama_cabang'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="checkbox">
                                                <label class="rtl">
                                                    <input type="checkbox" name="active" id="active" value="1" class="form-control" <?php echo $chk; ?> /> Active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                                <a href="./approval-inv.php" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                                <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                            </div>
                                        </div>
                                    </div>
                                    <hr style="margin:5px 0" />
                                    <div class="clearfix">
                                        <div class="col-sm-12"><small>* Wajib Diisi</small></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title"></h4>
                            </div>
                            <div class="modal-body">
                                <div id="preview_menu"></div>
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
        $(document).ready(function() {
            $("#id_role").on("change", function() {
                $("#chk-cs").addClass("hide");
                var nilai = $(this).val();
                if (nilai == "12") {
                    $(".transportir").removeClass("hide");
                    $(".terminal").addClass("hide");
                    $(".omnya").addClass("hide");
                } else if (nilai == "13") {
                    $(".transportir").addClass("hide");
                    $(".terminal").removeClass("hide");
                    $(".omnya").addClass("hide");
                } else if (nilai == "17") {
                    $(".transportir").addClass("hide");
                    $(".terminal").addClass("hide");
                    $(".omnya").removeClass("hide");
                } else if (nilai == "18") {
                    $("#chk-cs").removeClass("hide");
                } else {
                    $(".transportir").addClass("hide");
                    $(".terminal").addClass("hide");
                    $(".omnya").addClass("hide");
                }


                $("select#id_wilayah").val("").trigger('change').select2('close');
                $("select#id_wilayah option").remove();
                $.ajax({
                    type: "POST",
                    url: "./__get_wilayah.php",
                    data: {
                        q1: nilai
                    },
                    dataType: 'json',
                    cache: false,
                    success: function(data) {
                        if (data.items != "") {
                            $("select#id_wilayah").select2({
                                data: data.items,
                                placeholder: "Pilih Wilayah",
                                allowClear: true,
                            });
                            return false;
                        }
                    }
                });

            });
            $('.inp-chk-cs').on('ifChecked', function(e) {
                $("select#id_wilayah").val("").trigger('change').select2('close');
                $("select#id_wilayah option").remove();
                $("#div-wilayah").removeClass('hide')
                let value = $(this).val()
                let nilai = 1;
                if (value == 2)
                    nilai = 6
                else if (value == 3)
                    $("#div-wilayah").addClass('hide')
                if (value != 3) {
                    $.ajax({
                        type: "POST",
                        url: "./__get_wilayah.php",
                        data: {
                            q1: nilai
                        },
                        dataType: 'json',
                        cache: false,
                        success: function(data) {
                            if (data.items != "") {
                                $("select#id_wilayah").select2({
                                    data: data.items,
                                    placeholder: "Pilih Wilayah",
                                    allowClear: true,
                                });
                                return false;
                            }
                        }
                    });
                }
            })
        });
    </script>
</body>

</html>