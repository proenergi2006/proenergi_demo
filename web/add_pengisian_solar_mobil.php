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

$sesuser     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$sesrole     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$seswil     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesgroup     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);

if (isset($enk['id']) && $enk['id'] !== '') {
    $action = "update";
    $section = "Edit Data";
    $id = isset($enk["id"]) ? htmlspecialchars($enk["id"], ENT_QUOTES) : '';

    $sqlData = "SELECT * FROM pro_pengisian_solar_mobil_opr WHERE id = '" . $id . "'";
    $rowData = $con->getRecord($sqlData);
} else {
    $action = "add";
    $section = "Tambah Data";
}

$master_terminal = "SELECT * FROM pro_master_terminal WHERE kategori_terminal = '2' AND id_cabang='" . $seswil . "'";

$res_master_terminal = $con->getResult($master_terminal);
if ($res_master_terminal == false) $res_master_terminal = null;

if ($res_master_terminal) {
    $dispenser = array();
    foreach ($res_master_terminal as $key => $rmt) {
        $query_dispenser = "SELECT SUM(sisa_inven) as sisa_inven, id_terminal, nama_terminal, tanki_terminal FROM vw_terminal_inventory_receive WHERE id_terminal = '" . $rmt['id_master'] . "' AND sisa_inven > 0";
        $row_dispenser = $con->getRecord($query_dispenser);
        $dispenser[$key] = [
            "id_terminal"       => $row_dispenser['id_terminal'],
            "nama_terminal"     => $row_dispenser['nama_terminal'],
            "tanki_terminal"    => $row_dispenser['tanki_terminal'],
            "sisa_inven"        => $row_dispenser['sisa_inven'],
        ];
    }
}


$sqlWilayah = "SELECT * FROM pro_master_cabang WHERE id_master='" . $seswil . "'";
$wilayah = $con->getRecord($sqlWilayah);

$sqlnya = "SELECT a.*, a.id_master as id_truck, b.nama_transportir FROM pro_master_transportir_mobil a JOIN pro_master_transportir b ON a.id_transportir=b.id_master WHERE b.lokasi_suplier = '" . $wilayah['nama_cabang'] . "' AND b.owner_suplier = 1";
$resnya = $con->getResult($sqlnya);

$sqlnya01 = "SELECT id_mobil, plat_mobil, concat(nama_mobil, ' ', plat_mobil) as nama_mobil, id_cabang, attach_foto from pro_master_mobil where is_active = 1 and id_cabang = '" . $seswil . "'";
$resnya01 = $con->getResult($sqlnya01);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "formhelper"), "css" => array("jqueryUI", "formhelper"))); ?>

<style>
    .gambarnya-foto img {
        max-width: 100%;
        max-height: 250px;
        border: 1px solid #ccc;
    }
</style>

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
                                <form action="<?php echo ACTION_CLIENT . '/pengisian_solar_mobil.php'; ?>" id="gform" name="gform" method="post" class="form-validasi" role="form" enctype="multipart/form-data">
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label>Kategori *</label>
                                            <select name="kategori" id="kategori" class="form-control select2" required>
                                                <option value=""></option>
                                                <option value="1" <?= $rowData['id_mobil'] != 0 ? 'selected' : '' ?>>Mobil Operasional</option>
                                                <option value="2" <?= $rowData['id_truck'] != 0 ? 'selected' : '' ?>>Truck</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="row-truck" class="form-group row <?= $action == "update" && $rowData['id_truck'] != 0 ? "" : 'hide' ?>">
                                        <div class="col-sm-6">
                                            <label>Unit *</label>
                                            <select name="truck" id="truck" class="form-control select2" <?= $action == "update" ? "" : "required" ?>>
                                                <option value=""></option>
                                                <?php foreach ($resnya as $key) : ?>
                                                    <option value="<?= $key['id_truck'] ?>" <?= $rowData['id_truck'] == $key['id_truck'] ? 'selected' : '' ?>><?= $key['nama_transportir'] . ' - ' . $key['nomor_plat'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="row-mobil" class="form-group row <?= $action == "update" && $rowData['id_mobil'] != 0 ? "" : 'hide' ?>">
                                        <div class="col-sm-6">
                                            <label>Unit *</label>
                                            <select name="mobil" id="mobil" class="form-control select2" <?= $action == "update" ? "" : "required" ?>>
                                                <option value=""></option>
                                                <?php foreach ($resnya01 as $key) : ?>
                                                    <option value="<?= $key['id_mobil'] ?>" <?= $rowData['id_mobil'] == $key['id_mobil'] ? 'selected' : '' ?>><?= $key['nama_mobil'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Dispenser *</label>
                                            <select name="dispenser" id="dispenser" class="form-control select2" required>
                                                <option value=""></option>
                                                <?php foreach ($dispenser as $d) : ?>
                                                    <option value="<?= $d['id_terminal'] ?>" <?= $rowData['id_terminal'] == $d['id_terminal'] ? 'selected' : '' ?>><?= $d['nama_terminal'] . " - " . $d['sisa_inven'] . " Liter" ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <label>Volume *</label>
                                            <input type="text" id="liter_bbm" name="liter_bbm" class="form-control volume text-right" autocomplete="off" placeholder="0.0000" value="<?= $rowData ? $rowData['volume'] : '' ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Tujuan *</label>
                                            <input type="text" id="tujuan" name="tujuan" class="form-control" autocomplete="off" value="<?= $rowData ? $rowData['tujuan'] : '' ?>" required>
                                        </div>
                                        <div class="col-sm-4">
                                            <label>Driver *</label>
                                            <input type="text" id="driver" name="driver" class="form-control" autocomplete="off" value="<?= $rowData ? $rowData['driver'] : '' ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Keterangan *</label>
                                            <input type="text" id="keterangan" name="keterangan" class="form-control" value="<?= $rowData ? $rowData['keterangan'] : '' ?>" autocomplete="off" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Lampiran</label>
                                            <input type="file" id="lampiran" name="lampiran" class="form-control" accept="image/png, image/jpeg, .pdf">
                                        </div>
                                    </div>
                                    <div class=" row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" id="act" name="act" class="form-control" value="<?= $action ?>" />
                                                <input type="hidden" name="idr" value="<?= paramEncrypt($id); ?>" />
                                                <a href="<?php echo BASE_URL_CLIENT . "/pengisian_solar_mobil.php"; ?>" class="btn btn-default jarak-kanan">
                                                    <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                                <button type="button" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
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
        $("#liter_bbm").number(true, 4, ".", ",");

        $('#liter_bbm').on('keypress', function(e) {
            var charCode = e.which ? e.which : e.keyCode;

            // Izinkan hanya angka 0–9 (kode ASCII 48–57)
            if (charCode < 48 || charCode > 57) {
                e.preventDefault();
                return false;
            }
        });

        document.getElementById("btnSbmt").addEventListener("click", function(e) {
            // Ambil form
            const form = document.getElementById("gform");

            // Gunakan built-in form validation browser
            if (!form.checkValidity()) {
                // Trigger pesan validasi default browser
                form.reportValidity();
                return; // Stop proses lanjut
            }

            // Jika valid, tampilkan SweetAlert konfirmasi
            Swal.fire({
                title: 'Konfirmasi Simpan',
                text: "Anda yakin ingin menyimpan data ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#loading_modal").modal({
                        keyboard: false,
                        backdrop: 'static'
                    });
                    form.submit();
                }
            });
        });

        $("#kategori").change(function() {
            var value = $(this).val();

            if (value == 1) {
                $("#row-mobil").removeClass("hide");
                $("#row-truck").addClass("hide");
                $("#truck").val("").trigger("change")
                $("#truck").removeAttr("required", true);
            } else if (value == 2) {
                $("#row-truck").removeClass("hide");
                $("#row-mobil").addClass("hide");
                $("#mobil").val("").trigger("change")
                $("#mobil").removeAttr("required", true);
            } else {
                $("#row-truck").addClass("hide");
                $("#row-mobil").addClass("hide");
                $("#mobil").val("").trigger("change")
                $("#truck").val("").trigger("change")
            }
        })
    });
</script>