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
$action = "add";
$section = "Tambah Data";
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
                <h1><?php echo $section . " Customer"; ?></h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <form action="<?php echo ACTION_CLIENT . '/customer.php'; ?>" id="gform" name="gform" class="form-horizontal" method="post" role="form">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Marketing *</label>
                                        <div class="col-md-8">
                                            <?php if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) != 11 && paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) != 17 && paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) != 18) { ?>
                                                <select id="marketing" name="marketing" class="form-control select2" required>
                                                    <option></option>
                                                    <?php $con->fill_select("id_user", "fullname", "acl_user", $rsm['id_marketing'], "where is_active=1 and id_role=3", "fullname", false); ?>
                                                </select>
                                            <?php
                                            } else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 18) {
                                                $where = ''; // Nasional
                                                if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))  $where = 'and id_wilayah = "' . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . '"'; // Cabang
                                                else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group'])) $where = 'and id_group = "' . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']) . '"'; // Wilayah
                                            ?>
                                                <select id="marketing" name="marketing" class="form-control select2" required>
                                                    <option></option>
                                                    <?php $con->fill_select("id_user", "fullname", "acl_user", $rsm['id_marketing'], "where is_active=1 and id_role=11 " . $where, "fullname", false); ?>
                                                </select>
                                            <?php } else { ?>
                                                <input type="hidden" id="marketing" name="marketing" value="<?php echo paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']); ?>" />
                                                <input type="text" id="adN" name="adN" class="form-control" value="<?php echo paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']); ?>" readonly />
                                            <?php } ?>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Nama Perusahaan *</label>
                                        <div class="col-md-8">
                                            <input type="text" id="nama_customer" name="nama_customer" class="form-control" required />
                                            <p><small style="color:red;">* (Nama perusahaan harus sesuai dengan NPWP PT./CV.)</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Email *</label>
                                        <div class="col-md-8">
                                            <input type="text" id="email_customer" name="email_customer" class="form-control" required data-rule-email="true" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Alamat Perusahaan *</label>
                                        <div class="col-md-8">
                                            <input type="text" id="alamat_customer" name="alamat_customer" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Propinsi *</label>
                                        <div class="col-md-8">
                                            <select id="prov_customer" name="prov_customer" class="form-control select2" required>
                                                <option></option>
                                                <?php $con->fill_select("id_prov", "nama_prov", "pro_master_provinsi", $rsm, "", "nama_prov", false); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Kabupaten/Kota *</label>
                                        <div class="col-md-8">
                                            <select id="kab_customer" name="kab_customer" class="form-control select2" required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Postal Code</label>
                                        <div class="col-md-4">
                                            <input type="text" id="postalcode_customer" name="postalcode_customer" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Telepon *</label>
                                        <div class="col-md-8">
                                            <input type="text" id="telp_customer" name="telp_customer" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Fax</label>
                                        <div class="col-md-8">
                                            <input type="text" id="fax_customer" name="fax_customer" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Jenis Customer *</label>
                                        <div class="col-md-4">
                                            <select id="jenis_customer" name="jenis_customer" class="form-control select2" required>
                                                <option></option>
                                                <option value="PROJECT">PROJECT</option>
                                                <option value="RETAIL">RETAIL</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                            <div style="margin-bottom:15px;">
                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                                    <i class="fa fa-save jarak-kanan"></i> Simpan</button>
                                <a href="<?php echo BASE_URL_CLIENT . '/customer.php'; ?>" class="btn btn-default" style="min-width:90px;">
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

    <script>
        $(document).ready(function() {
            var formValidasiCfg = {
                submitHandler: function(form) {
                    $("#loading_modal").modal({
                        keyboard: false,
                        backdrop: 'static'
                    });

                    if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
                        $("#loading_modal").modal("hide");
                        $.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
                        setErrorFocus($("#nup_fee"), $("form#gform"), false);
                    } else {
                        form.submit();
                    }
                }
            };
            $("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

            $("select#prov_customer").change(function() {
                $("select#kab_customer").val("").trigger('change').select2('close');
                $("select#kab_customer option").remove();
                $.ajax({
                    type: "POST",
                    url: "./__get_kabupaten.php",
                    dataType: 'json',
                    data: {
                        q1: $("select#prov_customer").val()
                    },
                    cache: false,
                    success: function(data) {
                        if (data.items != "") {
                            $("select#kab_customer").select2({
                                data: data.items,
                                placeholder: "Pilih salah satu",
                                allowClear: true,
                            });
                            return false;
                        }
                    }
                });
            });

            document.getElementById("nama_customer").addEventListener("input", function() {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
</body>

</html>