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

if ($idr != "") {
    $sql = "select a.*, b.role_name, c.nama_cabang, d.nama_transportir, e.nama_terminal, f.group_wilayah, g.fullname as nama_omnya
				from acl_user a 
				join acl_role b on a.id_role = b.id_role 
				left join pro_master_cabang c on a.id_wilayah = c.id_master 
				left join pro_master_group_cabang f on a.id_group = f.id_gu 
				left join pro_master_transportir d on a.id_transportir = d.id_master 
				left join pro_master_terminal e on a.id_terminal = e.id_master 
				left join acl_user g on a.id_om = g.id_user 
				where a.id_user = '" . $idr . "'";
    $rsm = $con->getRecord($sql);
    if ($rsm['id_wilayah'] == 0 and $rsm['id_group'] == 0) {
        $rsm['nama_cabang'] = 'Nasional';
    } else
        if ($rsm['id_wilayah'] == 0 and $rsm['id_group']) {
        $rsm['nama_cabang'] = $rsm['group_wilayah'];
    }
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
                <h1>Employee</h1>
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
                                <form action="<?php echo ACTION_CLIENT . '/acl-user.php'; ?>" id="gform_3" name="gform_3" method="post" class="form-validasi" role="form">
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Username *</label>
                                            <input type="text" id="username" name="username" class="form-control validate[required]" value="<?php echo isset($rsm['username']) ? $rsm['username'] : ''; ?>" <?php echo $dis; ?> autocomplete="off" />
                                        </div>
                                        <div class="col-sm-6 col-sm-top">
                                            <label>Nama Karyawan *</label>
                                            <input type="text" id="fullname" name="fullname" class="form-control validate[required]" value="<?php echo isset($rsm['fullname']) ? $rsm['fullname'] : ''; ?>" <?php echo $dis; ?> autocomplete="off" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Email *</label>
                                            <input type="text" id="email" name="email" class="form-control validate[required, custom[email]]" value="<?php echo isset($rsm['email_user']) ? $rsm['email_user'] : ''; ?>" <?php echo $dis; ?> autocomplete="off" />
                                        </div>
                                        <div class="col-sm-6 col-sm-top">
                                            <label>Telephone *</label>
                                            <input type="text" id="telepon" name="telepon" class="form-control validate[required]" value="<?php echo isset($rsm['mobile_user']) ? $rsm['mobile_user'] : ''; ?>" <?php echo $dis; ?> autocomplete="off" />
                                        </div>
                                    </div>

                                    <?php if (!$idr) { ?>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Password *</label>
                                                <input type="password" id="pass" name="pass" class="form-control validate[required]" />
                                            </div>
                                            <div class="col-sm-6 col-sm-top">
                                                <label>Confirmation Password *</label>
                                                <input type="password" id="cpass" name="cpass" class="form-control validate[required,funcCall[confirmPassCheck[pass]]]" />
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Role Name *</label>
                                            <?php if (!$idr) { ?>
                                                <select id="id_role" name="id_role" class="form-control validate[required] select2">
                                                    <option value=""></option>
                                                    <?php $con->fill_select("id_role", "role_name", "acl_role", "", "where is_active=1", "role_name", false); ?>
                                                </select>
                                            <?php } else { ?>
                                                <input type="hidden" name="id_role" value="<?php echo $rsm['id_role']; ?>" />
                                                <input type="text" class="form-control" value="<?php echo $rsm['role_name']; ?>" <?php echo $dis; ?> />
                                            <?php } ?>
                                        </div>
                                        <div class="col-sm-6 col-sm-top">
                                            <div class="checkbox hide" id="chk-cs" style="margin-top: 25px;">
                                                <label class="rtl" style="margin-right: 10px;">
                                                    <input type="radio" class="form-control inp-chk-cs" name="inp-chk-cs" value="1" checked /> Wilayah Cabang
                                                </label>
                                                <label class="rtl" style="margin-right: 10px;">
                                                    <input type="radio" class="form-control inp-chk-cs" name="inp-chk-cs" value="2" /> Wilayah Group
                                                </label>
                                                <label class="rtl" style="margin-right: 10px;">
                                                    <input type="radio" class="form-control inp-chk-cs" name="inp-chk-cs" value="3" /> Wilayah Nasional
                                                </label>
                                            </div>
                                            <div id="div-wilayah">
                                                <label>Wilayah *</label>
                                                <?php if (!$idr) { ?>
                                                    <select id="id_wilayah" name="id_wilayah" class="form-control validate[required] select2">
                                                        <option value=""></option>
                                                        <?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", "", "where is_active = 1", "id_master", false); ?>
                                                    </select>
                                                <?php } else { ?>
                                                    <input type="hidden" name="id_wilayah" value="<?php echo $rsm['id_wilayah']; ?>" />
                                                    <input type="text" class="form-control" value="<?php echo ($rsm['id_role'] != 6) ? $rsm['nama_cabang'] : $rsm['group_wilayah']; ?>" <?php echo $dis; ?> />
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (!$idr) { ?>
                                        <div class="form-group row transportir hide">
                                            <div class="col-sm-6">
                                                <label>Transportir *</label>
                                                <?php if (!$idr) { ?>
                                                    <select id="id_transportir" name="id_transportir" class="form-control validate[required] select2">
                                                        <option value=""></option>
                                                        <?php $con->fill_select("id_master", "concat(nama_transportir,' - ',lokasi_suplier)", "pro_master_transportir", "", "where is_active=1 and tipe_angkutan in(1,3)", "", false); ?>
                                                    </select>
                                                <?php } else { ?>
                                                    <input type="hidden" name="id_transportir" value="<?php echo $rsm['id_transportir']; ?>" />
                                                    <input type="text" class="form-control" value="<?php echo $rsm['nama_transportir']; ?>" <?php echo $dis; ?> />
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group row hide terminal">
                                            <div class="col-sm-6">
                                                <label>Terminal *</label>
                                                <?php if (!$idr) { ?>
                                                    <select id="id_terminal" name="id_terminal" class="form-control validate[required] select2">
                                                        <option value=""></option>
                                                        <?php $con->fill_select("id_master", "concat(nama_terminal,', ',tanki_terminal,' - ',lokasi_terminal)", "pro_master_terminal", "", "where is_active = 1", "", false); ?>
                                                    </select>
                                                <?php } else { ?>
                                                    <input type="hidden" name="id_terminal" value="<?php echo $rsm['id_terminal']; ?>" />
                                                    <input type="text" class="form-control" value="<?php echo $rsm['nama_terminal']; ?>" <?php echo $dis; ?> />
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="form-group row hide omnya">
                                            <div class="col-sm-6">
                                                <label>Operation Manager *</label>
                                                <select id="id_om" name="id_om" class="form-control validate[required] select2">
                                                    <option value=""></option>
                                                    <?php $con->fill_select("id_user", "fullname", "acl_user", "", "where is_active = 1 and id_role = 6", "", false); ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php }
                                    if ($idr && $rsm['id_role'] == 17) { ?>
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <label>Operation Manager *</label>
                                                <select id="id_om" name="id_om" class="form-control validate[required] select2">
                                                    <option value=""></option>
                                                    <?php $con->fill_select("id_user", "fullname", "acl_user", $rsm['id_om'], "where is_active = 1 and id_role = 6", "", false); ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
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
                                                <a href="./acl-user.php" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
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