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
    $section     = "Edit Data Vendor";
    $idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
    $sql = "select * from pro_master_vendor where id_master = '" . $idr . "';";
    $rsm = $con->getRecord($sql);
    $chk = ($rsm['is_active']) ? "checked" : "";
} else {
    $action     = "add";
    $section     = "Tambah Data Vendor";
    $chk        = "checked";
}


//get vendor
$data = http_build_query([
    'fields' => 'id,vendorNo,name',
    'sp.sort' => 'vendorNo|desc',
    'fisp.pageSizeelds' => 1,
]);
$urlnya = 'https://zeus.accurate.id/accurate/api/vendor/list.do?' . $data;

$result = curl_get($urlnya);

if ($result['s'] == true) {
    $vendorNo = $result['d'][0]['vendorNo'];
} else {
    echo "get list vendor accurate not found";
}

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
                                <form action="<?php echo ACTION_CLIENT . '/master-vendor.php'; ?>" id="gform" name="gform" method="post" class="form-validasi" role="form">
                                    <div class="form-group row">
                                        <div class="col-sm-9">
                                            <label>Vendor *</label>
                                            <input type="text" id="vendor" name="vendor" class="form-control validate[required]" value="<?php echo $rsm['nama_vendor']; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label>Referensi Kode Accurate Terakhir</label>
                                            <input type="text" id="vendor" name="vendor" class="form-control validate[required]" value="<?php echo $vendorNo; ?>" disabled />
                                        </div>
                                        <div class="col-sm-3">

                                            <label>Kode Vendor Accurate *</label>
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">V-</span>
                                                <input type="text" id="kode_vendor" name="kode_vendor" class="form-control validate[required]" value="<?php echo $rsm['kode_vendor']; ?>" />
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Inisial Vendor*</label>
                                            <input type="text" id="inisial_vendor" name="inisial_vendor" class="form-control validate[required]" oninput="this.value = this.value.toUpperCase()" value="<?php echo $rsm['inisial_vendor']; ?>" />
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
                                                <a href="<?php echo BASE_URL_CLIENT . "/master-vendor.php"; ?>" class="btn btn-default jarak-kanan">
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