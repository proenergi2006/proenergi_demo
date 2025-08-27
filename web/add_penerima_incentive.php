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
    $section     = "Edit Data Penerima Incentive";
    $idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
    $sql = "SELECT * from pro_penerima_incentive where id = '" . $idr . "'";
    $rsm = $con->getRecord($sql);

    $sql_cek = "SELECT * from pro_pengajuan_incentive where id_bm = '" . $rsm['id'] . "' OR id_sm = '" . $rsm['id'] . "'";
    $rsm_cek = $con->getRecord($sql_cek);

    $chk = ($rsm['status']) ? "checked" : "";
} else {
    $action     = "add";
    $section    = "Tambah Data Penerima Incentive";
    $chk        = "checked";
}

$cab = "SELECT * from pro_master_cabang where is_active = '1' AND id_master != '1'";
$res_cab = $con->getResult($cab);

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
                <h1><?php echo $section; ?></h1>
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
                                <form action="<?php echo ACTION_CLIENT . '/penerima_incentive.php'; ?>" id="gform" name="gform" method="post" class="form-validasi" role="form">
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label>Nama Penerima *</label>
                                            <input type="text" name="nama_penerima" id="nama_penerima" class="form-control validate[required]" value="<?= $rsm['nama'] ?>">
                                            <!-- <select name="nama_penerima" id="nama_penerima" class="form-control select2 validate[required]">
                                                <option value=""></option>
                                            </select> -->
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Jabatan *</label>
                                            <select name="jabatan" id="jabatan" class="form-control select2 validate[required]" <?= $rsm_cek ? 'disabled' : '' ?>>
                                                <option value=""></option>
                                                <option value="BM" <?= $rsm['jabatan'] == "BM" ? 'selected' : '' ?>>BM</option>
                                                <option value="SM" <?= $rsm['jabatan'] == "SM" ? 'selected' : '' ?>>SM</option>
                                                <option value="SPV" <?= $rsm['jabatan'] == "SPV" ? 'selected' : '' ?>>SPV</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Cabang *</label>
                                            <select name="cabang" id="cabang" class="form-control select2 validate[required]" <?= $rsm_cek ? 'disabled' : '' ?>>
                                                <option value=""></option>
                                                <?php foreach ($res_cab as $rc) : ?>
                                                    <option value="<?= $rc['id_master'] ?>" <?= $rsm['cabang'] == $rc['id_master'] ? 'selected' : '' ?>><?= $rc['nama_cabang'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Persentase *</label>
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">%</span>
                                                <input type="text" id="persen" name="persen" class="form-control validate[required]" value="<?= $rsm['persentase'] ?>" autocomplete="off" <?= $rsm_cek ? 'disabled' : '' ?> />
                                            </div>
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
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                                <a href="<?php echo BASE_URL_CLIENT . "/penerima_incentive.php"; ?>" class="btn btn-default jarak-kanan">
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
</body>

</html>

<script>
    $(document).ready(function() {
        $('#persen').on('keypress', function(e) {
            var charCode = e.which ? e.which : e.keyCode;

            // Izinkan hanya angka 0–9 (kode ASCII 48–57)
            if (charCode < 48 || charCode > 57) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>