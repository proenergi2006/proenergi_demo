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

$id_role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

// Cek peran pengguna
$required_role = ['1', '23', '21', '3'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
    // Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
    $flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
    // exit();
}

$sql = "SELECT * FROM pro_top_incentive WHERE top != '76' ORDER BY top ASC";
$result = $con->getResult($sql);

$sql2 = "SELECT REPLACE(b.role_name, 'Role ', '') AS role_name, a.id_role FROM pro_point_incentive as a JOIN acl_role as b ON a.id_role=b.id_role GROUP BY a.id_role";
$result2 = $con->getResult($sql2);

$sql3 = "SELECT tier, id FROM pro_point_incentive WHERE tier != 'Tier IV' AND id_top = 1 AND id_role = 11";
$tier_top_cbd = $con->getResult($sql3);

$sql4 = "SELECT tier, id FROM pro_point_incentive WHERE tier != 'Tier IV' AND id_top = 2 AND id_role = 11";
$tier_top_cod = $con->getResult($sql4);

$sql5 = "SELECT tier, id FROM pro_point_incentive WHERE tier != 'Tier IV' AND id_top = 3 AND id_role = 11";
$tier_top = $con->getResult($sql5);

$sql6 = "SELECT tier, id FROM pro_point_incentive WHERE tier != 'Tier IV' AND id_top = 4 AND id_role = 11";
$tier_top2 = $con->getResult($sql6);

$sql7 = "SELECT tier, id FROM pro_point_incentive WHERE tier != 'Tier IV' AND id_top = 5 AND id_role = 11";
$tier_top3 = $con->getResult($sql7);

// echo json_encode($result2);
// exit();
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "myGrid"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>List Point Incentive</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="alert alert-danger alert-dismissible" style="display:none">
                    <div class="box-tools">
                        <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <!-- <button class="btn btn-primary btn-md" type="button" id="btnEdit"><i class="fas fa-edit"></i> Edit</button>
                                        <button class="btn btn-danger btn-md hide" type="button" id="btnCancel"><i class="fas fa-window-close"></i> Cancel</button>
                                        <button class="btn btn-success btn-md hide" type="button" id="btnSimpan"><i class="fas fa-save"></i> Simpan</button> -->
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-right" style="margin-top: 10px">Show
                                            <select name="tableGridLength" id="tableGridLength">
                                                <option value="10" selected>10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select> Data
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="overflow-x: auto" id="table-long">
                                <div style="width:2000px; height:auto;">
                                    <div class="table-responsive-satu">
                                        <table class="table table-bordered" id="data-incentive" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="30px" rowspan="2"> POSISI </th>
                                                    <?php foreach ($result as $r) : ?>
                                                        <th class="text-center" width="50px"><?= $r['keterangan'] ?></th>
                                                    <?php endforeach ?>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        <table border="1">
                                                            <tr align="center">
                                                                <?php foreach ($tier_top_cbd as $dt) : ?>
                                                                    <td width="50px"><?= $dt['tier'] ?></td>
                                                                <?php endforeach ?>
                                                            </tr>
                                                        </table>
                                                    </th>
                                                    <th>
                                                        <table border="1">
                                                            <tr align="center">
                                                                <?php foreach ($tier_top_cod as $dt2) : ?>
                                                                    <td width="50px"><?= $dt2['tier'] ?></td>
                                                                <?php endforeach ?>
                                                            </tr>
                                                        </table>
                                                    </th>
                                                    <th>
                                                        <table border="1">
                                                            <tr align="center">
                                                                <?php foreach ($tier_top as $dt3) : ?>
                                                                    <td width="50px"><?= $dt3['tier'] ?></td>
                                                                <?php endforeach ?>
                                                            </tr>
                                                        </table>
                                                    </th>
                                                    <th>
                                                        <table border="1">
                                                            <tr align="center">
                                                                <?php foreach ($tier_top2 as $dt4) : ?>
                                                                    <td width="50px"><?= $dt4['tier'] ?></td>
                                                                <?php endforeach ?>
                                                            </tr>
                                                        </table>
                                                    </th>
                                                    <th>
                                                        <table border="1">
                                                            <tr align="center">
                                                                <?php foreach ($tier_top3 as $dt5) : ?>
                                                                    <td width="50px"><?= $dt5['tier'] ?></td>
                                                                <?php endforeach ?>
                                                            </tr>
                                                        </table>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <form id="updateForm">
                                                    <?php foreach ($result2 as $r2) : ?>
                                                        <?php
                                                        $sql8 = "SELECT point, id FROM pro_point_incentive WHERE tier != 'Tier IV' AND id_top = 1 AND id_role = '" . $r2['id_role'] . "'";
                                                        $data_tier_top = $con->getResult($sql8);

                                                        $sql9 = "SELECT point, id FROM pro_point_incentive WHERE tier != 'Tier IV' AND id_top = 2 AND id_role = '" . $r2['id_role'] . "'";
                                                        $data_tier_top2 = $con->getResult($sql9);

                                                        $sql10 = "SELECT point, id FROM pro_point_incentive WHERE tier != 'Tier IV' AND id_top = 3 AND id_role = '" . $r2['id_role'] . "'";
                                                        $data_tier_top3 = $con->getResult($sql10);

                                                        $sql11 = "SELECT point, id FROM pro_point_incentive WHERE tier != 'Tier IV' AND id_top = 4 AND id_role = '" . $r2['id_role'] . "'";
                                                        $data_tier_top4 = $con->getResult($sql11);

                                                        $sql12 = "SELECT point, id FROM pro_point_incentive WHERE tier != 'Tier IV' AND id_top = 5 AND id_role = '" . $r2['id_role'] . "'";
                                                        $data_tier_top5 = $con->getResult($sql12);
                                                        ?>
                                                        <tr>
                                                            <td><?= $r2['role_name'] ?></td>
                                                            <td>
                                                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                    <tr align="center">
                                                                        <?php foreach ($data_tier_top as $dtt) : ?>
                                                                            <td>
                                                                                <input type="number" name="point_<?= $dtt['id'] ?>" value="<?= $dtt['point'] ?>" class="form-control text-center points" disabled>
                                                                            </td>
                                                                        <?php endforeach ?>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td>
                                                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                    <tr align="center">
                                                                        <?php foreach ($data_tier_top2 as $dtt2) : ?>
                                                                            <td>
                                                                                <input type="number" name="point_<?= $dtt2['id'] ?>" value="<?= $dtt2['point'] ?>" class="form-control text-center points" disabled>
                                                                            </td>
                                                                        <?php endforeach ?>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td>
                                                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                    <tr align="center">
                                                                        <?php foreach ($data_tier_top3 as $dtt3) : ?>
                                                                            <td>
                                                                                <input type="number" name="point_<?= $dtt3['id'] ?>" value="<?= $dtt3['point'] ?>" class="form-control text-center points" disabled>
                                                                            </td>
                                                                        <?php endforeach ?>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td>
                                                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                    <tr align="center">
                                                                        <?php foreach ($data_tier_top4 as $dtt4) : ?>
                                                                            <td>
                                                                                <input type="number" name="point_<?= $dtt4['id'] ?>" value="<?= $dtt4['point'] ?>" class="form-control text-center points" disabled>
                                                                            </td>
                                                                        <?php endforeach ?>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td>
                                                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                    <tr align="center">
                                                                        <?php foreach ($data_tier_top5 as $dtt5) : ?>
                                                                            <td>
                                                                                <input type="number" name="point_<?= $dtt5['id'] ?>" value="<?= $dtt5['point'] ?>" class="form-control text-center points" disabled>
                                                                            </td>
                                                                        <?php endforeach ?>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach ?>
                                                </form>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .tier-header {
                        text-align: center;
                        font-weight: bold;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    td {
                        padding: 5px;
                        text-align: center;
                    }
                </style>

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
    <script>
        $(document).ready(function() {
            // Hanya izinkan angka (0-9)
            $('.numberOnly').on('input', function() {
                // Hapus semua karakter yang bukan angka
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            $('#btnEdit').click(function() {
                $('#btnEdit').addClass('hide');
                $('#btnSimpan').removeClass('hide');
                $('#btnCancel').removeClass('hide');
                $('.points').removeAttr('disabled');
            })

            $('#btnCancel').click(function() {
                Swal.fire({
                    title: "Anda yakin cancel?",
                    showCancelButton: true,
                    confirmButtonText: "Ya Cancel",
                    cancelButtonText: "Batal",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        window.location.reload();
                    }
                })
            });

            $('#btnSimpan').click(function(e) {
                e.preventDefault();
                // Ambil data dari form
                var formData = $("#updateForm").serialize();

                Swal.fire({
                    title: "Anda yakin simpan?",
                    showCancelButton: true,
                    confirmButtonText: "Simpan",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        $.ajax({
                            method: 'post',
                            url: '<?php echo ACTION_CLIENT ?>/incentive_point.php',
                            data: formData,
                            dataType: 'json',
                            success: function(response) {
                                Swal.fire({
                                    title: "Success",
                                    icon: "success",
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    text: response.pesan,
                                    position: "center",
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then((result) => {
                                    // Reload the Page
                                    $("#loading_modal").modal({
                                        keyboard: false,
                                        backdrop: 'static'
                                    });
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    title: "Ooppss",
                                    text: result.pesan,
                                    icon: "warning"
                                }).then((result) => {
                                    // Reload the Page
                                    $("#loading_modal").modal({
                                        keyboard: false,
                                        backdrop: 'static'
                                    });
                                    location.reload();
                                });
                            }
                        });
                    }
                });
            })
        });
    </script>
</body>

</html>