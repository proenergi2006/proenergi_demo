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
$id_pengajuan = isset($enk["id_pengajuan"]) ? htmlspecialchars($enk["id_pengajuan"], ENT_QUOTES) : '';
$id_pengajuan_enc = paramEncrypt($id_pengajuan);
$id_role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$datenow = date("Y-m-d");
$link1     = BASE_URL_CLIENT . '/list_pengajuan_incentive.php';
$total_incentive = 0;

$sql2 = "SELECT * FROM pro_bundle_incentive WHERE id_pengajuan = '" . $id_pengajuan . "'";
$res = $con->getResult($sql2);

if (empty($res) || $res == "") {
    $flash->add("warning", "Pengajuan Incentive tidak ditemukan", BASE_URL_CLIENT . "/list_pengajuan_incentive.php");
}

$array = [];
foreach ($res as $r) {
    $sql4 = "SELECT id_marketing FROM pro_incentive WHERE id = '" . $r['id_incentive'] . "'";
    $res3 = $con->getRecord($sql4);

    if ($res3) {
        array_push($array, $res3['id_marketing']);
    }
}

// Check if the array is not empty to avoid SQL errors
if (!empty($array)) {
    // Convert the array to a comma-separated string for the SQL IN clause
    $ids = implode(',', array_map(function ($id) {
        return "'" . intval($id) . "'";
    }, $array));

    $sql_mkt = "SELECT * FROM acl_user WHERE id_user IN ($ids) AND id_role IN ('11', '17', '20') AND is_active = '1' ORDER BY fullname ASC";

    $result_mkt = $con->getResult($sql_mkt);

    $sql3 = "SELECT * FROM acl_user WHERE id_user IN ($ids) AND id_role IN ('11', '17', '20') AND is_active = '1' ORDER BY fullname ASC";

    $res2 = $con->getResult($sql3);
} else {
    $result_mkt = []; // Handle the case where no IDs are found
    $res2 = []; // Handle the case where no IDs are found
}

if (isset($_POST['q0'])) {
    $q0 = paramDecrypt($_POST['q0']);
    // $q1 = $_POST['q1'];
    $q2 = $_POST['q2'];

    // echo json_encode($q2);

    $sql1 = "SELECT *, id as id_pengajuannya FROM pro_pengajuan_incentive WHERE id = '" . $id_pengajuan . "'";
    $row1 = $con->getRecord($sql1);

    $array = [];
    foreach ($q2 as $r) {
        $sql4 = "SELECT id_marketing FROM pro_incentive WHERE id_marketing = '" . $r . "'";
        $res3 = $con->getRecord($sql4);

        if ($res3) {
            array_push($array, $res3['id_marketing']);
        }
    }

    // Check if the array is not empty to avoid SQL errors
    if (!empty($array)) {
        // Convert the array to a comma-separated string for the SQL IN clause
        $ids = implode(',', array_map(function ($id) {
            return "'" . intval($id) . "'";
        }, $array));

        $sql3 = "SELECT * FROM acl_user WHERE id_user IN ($ids) AND id_role IN ('11', '17', '20') AND is_active = '1' ORDER BY fullname ASC";

        $res2 = $con->getResult($sql3);
    } else {
        $res2 = []; // Handle the case where no IDs are found
    }

    $sql_incentive_all = "SELECT c.*, d.fullname, e.no_invoice, e.tgl_invoice, e.tgl_invoice_dikirim, f.kode_pelanggan, f.nama_customer, (SELECT MAX(tgl_bayar) FROM pro_invoice_admin_detail_bayar WHERE id_invoice=e.id_invoice) as tgl_lunas, g.nama_cabang FROM pro_bundle_incentive a JOIN pro_pengajuan_incentive b ON a.id_pengajuan=b.id JOIN pro_incentive c ON a.id_incentive=c.id JOIN acl_user d ON c.id_marketing=d.id_user JOIN pro_invoice_admin e ON c.id_invoice=e.id_invoice JOIN pro_customer f ON e.id_customer=f.id_customer JOIN pro_master_cabang g ON f.id_wilayah=g.id_master WHERE a.id_pengajuan='" . $q0 . "' AND c.id_marketing IN ($ids)";
    $res_all = $con->getResult($sql_incentive_all);

    switch ($row1['periode_bulan']) {
        case '01':
            $nama_bulan = "Januari";
            break;
        case '02':
            $nama_bulan = "Februari";
            break;
        case '03':
            $nama_bulan = "Maret";
            break;
        case '04':
            $nama_bulan = "April";
            break;
        case '05':
            $nama_bulan = "Mei";
            break;
        case '06':
            $nama_bulan = "Juni";
            break;
        case '07':
            $nama_bulan = "Juli";
            break;
        case '08':
            $nama_bulan = "Agustus";
            break;
        case '09':
            $nama_bulan = "September";
            break;
        case '10':
            $nama_bulan = "Oktober";
            break;
        case '11':
            $nama_bulan = "November";
            break;
        case '12':
            $nama_bulan = "Desember";
            break;
    }
} else {
    $sql1 = "SELECT *, id as id_pengajuannya FROM pro_pengajuan_incentive WHERE id = '" . $id_pengajuan . "'";
    $row1 = $con->getRecord($sql1);

    $sql_incentive_all = "SELECT c.*, d.fullname, d.id_role, e.no_invoice, e.tgl_invoice, e.tgl_invoice_dikirim, f.kode_pelanggan, f.nama_customer, (SELECT MAX(tgl_bayar) FROM pro_invoice_admin_detail_bayar WHERE id_invoice=e.id_invoice) as tgl_lunas, g.nama_cabang FROM pro_bundle_incentive a JOIN pro_pengajuan_incentive b ON a.id_pengajuan=b.id JOIN pro_incentive c ON a.id_incentive=c.id JOIN acl_user d ON c.id_marketing=d.id_user JOIN pro_invoice_admin e ON c.id_invoice=e.id_invoice JOIN pro_customer f ON e.id_customer=f.id_customer JOIN pro_master_cabang g ON f.id_wilayah=g.id_master WHERE a.id_pengajuan='" . $id_pengajuan . "' ORDER BY d.fullname, e.no_invoice ASC";
    $res_all = $con->getResult($sql_incentive_all);

    switch ($row1['periode_bulan']) {
        case '01':
            $nama_bulan = "Januari";
            break;
        case '02':
            $nama_bulan = "Februari";
            break;
        case '03':
            $nama_bulan = "Maret";
            break;
        case '04':
            $nama_bulan = "April";
            break;
        case '05':
            $nama_bulan = "Mei";
            break;
        case '06':
            $nama_bulan = "Juni";
            break;
        case '07':
            $nama_bulan = "Juli";
            break;
        case '08':
            $nama_bulan = "Agustus";
            break;
        case '09':
            $nama_bulan = "September";
            break;
        case '10':
            $nama_bulan = "Oktober";
            break;
        case '11':
            $nama_bulan = "November";
            break;
        case '12':
            $nama_bulan = "Desember";
            break;
    }
}


$sql_cabang = "SELECT * FROM pro_master_cabang WHERE id_master = '" . $row1['wilayah'] . "'";
$cabang = $con->getRecord($sql_cabang);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "formatNumber", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<style>
    th {
        /* table cells */
        padding: 10px;
    }

    td {
        /* table cells */
        padding: 5px;
    }
</style>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Detail Pengajuan Incentive</h1>
            </section>
            <section class="content">
                <?php $flash->display(); ?>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#form-marketing" aria-controls="form-marketing" role="tab" data-toggle="tab">Detail</a>
                    </li>
                    <li role="presentation" class="">
                        <a href="#data-all" aria-controls="data-all" role="tab" data-toggle="tab">ALL</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="form-marketing">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-body">
                                        <table width="100%" border="0">
                                            <tr>
                                                <td>
                                                    <a class="btn btn-danger btn-md" target="_blank" href="<?= ACTION_CLIENT . '/cetak-incentive.php?' . paramEncrypt('id=' . $id_pengajuan . '&kategori=detail') ?>"><i class="fas fa-file-pdf"></i> Cetak PDF</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="15%" style="padding: 10px;">
                                                    Nomor Pengajuan
                                                </td>
                                                <td width="2%">
                                                    :
                                                </td>
                                                <td>
                                                    <strong>
                                                        <?= $row1['nomor_pengajuan'] ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="15%" style="padding: 10px;">
                                                    Cabang
                                                </td>
                                                <td width="2%">
                                                    :
                                                </td>
                                                <td>
                                                    <strong>
                                                        <?= $cabang['nama_cabang'] ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="15%" style="padding: 10px;">
                                                    Tanggal Pengajuan
                                                </td>
                                                <td width="2%">
                                                    :
                                                </td>
                                                <td>
                                                    <strong>
                                                        <?= tgl_indo($row1['tgl_pengajuan']) ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="15%" style="padding: 10px;">
                                                    Periode Pengajuan
                                                </td>
                                                <td width="2%">
                                                    :
                                                </td>
                                                <td>
                                                    <strong>
                                                        <?= $nama_bulan . " " . $row1['periode_tahun'] ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="15%" style="padding: 10px;">
                                                    Status
                                                </td>
                                                <td width="2%">
                                                    :
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($row1['is_ceo'] == 1) {
                                                        $status = "Approved by " . $row1['ceo_by'] . " | " . tgl_indo($row1['ceo_date']);
                                                    } else {
                                                        $status = "Verifikasi CEO";
                                                    }
                                                    ?>
                                                    <strong>
                                                        <?= $status ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="15%" style="padding: 10px;">Total Incentive</td>
                                                <td>:</td>
                                                <td><b>Rp. <span id="total_detail_incentive">0</span></b></td>
                                            </tr>
                                            <?php if ($row1['is_ceo'] == 0 && $id_role == '21') : ?>
                                                <tr>
                                                    <td>

                                                    </td>
                                                    <td>

                                                    </td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm" id="approved" type="button"><i class="fas fa-thumbs-up"></i> Approve</button>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo $link1; ?>" class="btn btn-default" style="min-width:90px; float:right;">
                                                            <i class="fa fa-reply jarak-kanan"></i> Kembali
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php else : ?>
                                                <tr>
                                                    <td>

                                                    </td>
                                                    <td>

                                                    </td>
                                                    <td>

                                                    </td>
                                                    <td>
                                                        <a href="<?php echo $link1; ?>" class="btn btn-default" style="min-width:90px; float:right;">
                                                            <i class="fa fa-reply jarak-kanan"></i> Kembali
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endif ?>
                                        </table>
                                        <hr>
                                        <form name="searchForm" id="searchForm" role="form" class="form-horizontal" action="<?= BASE_URL_CLIENT . '/detail_incentive.php?' . paramEncrypt('id_pengajuan=' . $id_pengajuan) ?>" method="POST">
                                            <div class="form-group row">
                                                <div class="col-sm-3">
                                                    <select class="form-control select2" name="q2[]" id="q2" multiple required>
                                                        <option></option>
                                                        <?php foreach ($result_mkt as $key) : ?>
                                                            <option value="<?= $key['id_user'] ?>"><?= $key['fullname'] ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-1 col-sm-top">
                                                    <button type="submit" class="btn btn-info btn-sm" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
                                                </div>
                                                <div class="col-sm-2 col-sm-top">
                                                    <?php if (isset($_POST['q0'])) : ?>
                                                        <a href="<?= BASE_URL_CLIENT . '/detail_incentive.php?' . paramEncrypt('id_pengajuan=' . $id_pengajuan) ?>" class="btn btn-primary btn-sm"><i class="fa fa-table jarak-kanan"></i>Tampilkan semua</a>
                                                    <?php endif ?>
                                                </div>
                                                <div class="col-sm-2">
                                                    <input type="hidden" class="form-control input-sm" name="q0" id="q0" value="<?= paramEncrypt($id_pengajuan) ?>" />
                                                    <!-- <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." /> -->
                                                </div>
                                            </div>
                                        </form>
                                        <div style="max-height: 400px; overflow-y: auto;">
                                            <div class="table-responsive">
                                                <?php $grand_total_incentive = 0; ?>
                                                <?php foreach ($res2 as $key) : ?>
                                                    <?php
                                                    $sql_incentive = "SELECT a.*, b.no_invoice, b.tgl_invoice, b.tgl_invoice_dikirim, c.nama_customer, c.kode_pelanggan, (SELECT MAX(tgl_bayar) FROM pro_invoice_admin_detail_bayar WHERE id_invoice=a.id_invoice) as tgl_lunas, d.nama_cabang FROM pro_incentive a JOIN pro_invoice_admin b ON a.id_invoice=b.id_invoice JOIN pro_customer c ON b.id_customer=c.id_customer JOIN pro_master_cabang d ON c.id_wilayah=d.id_master WHERE a.id_marketing = '" . $key['id_user'] . "' AND a.disposisi > 1 and d.id_master = '" . $row1['wilayah'] . "' ORDER BY a.id DESC";
                                                    $res_incentive = $con->getResult($sql_incentive);
                                                    ?>

                                                    <?php
                                                    if ($key['id_role'] == '11') {
                                                        $role = "Marketing";
                                                    } elseif ($key['id_role'] == '17') {
                                                        $role = "Key Account Executive";
                                                    } elseif ($key['id_role'] == '6') {
                                                        $role = "Branch Manager";
                                                    } elseif ($key['id_role'] == '7') {
                                                        $role = "Operation manager";
                                                    }
                                                    ?>
                                                    <span><strong><?= strtoupper($key['fullname']) ?></strong></span>
                                                    <br>
                                                    <span><?= $role ?></span>
                                                    <table width="1600px" border="1">
                                                        <thead style="background-color: yellow;">
                                                            <tr>
                                                                <th style="padding: 10px;" class="text-center">Invoice</th>
                                                                <th class="text-center">Customer Name</th>
                                                                <th class="text-center">Inv Date</th>
                                                                <th class="text-center">Inv Send Date</th>
                                                                <th class="text-center">Inv Paid Date</th>
                                                                <th class="text-center">Lunas</th>
                                                                <th class="text-center">Quantity</th>
                                                                <th class="text-center">Basic Price</th>
                                                                <th class="text-center">Tier</th>
                                                                <th class="text-center">Point</th>
                                                                <th class="text-center">Incentive</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if ($res_incentive) : ?>
                                                                <?php
                                                                $sub_total_incentive = 0;
                                                                ?>
                                                                <?php foreach ($res_incentive as $ri) : ?>
                                                                    <?php
                                                                    $sub_total_incentive += $ri['total_incentive'];

                                                                    $startDate = new DateTime($ri['tgl_invoice_dikirim']);
                                                                    $endDate = new DateTime($ri['tgl_lunas']);

                                                                    // Menghitung selisih
                                                                    $interval = $startDate->diff($endDate);
                                                                    $daysDiff = $interval->days;
                                                                    $daysNetto = $daysDiff - 5;
                                                                    ?>
                                                                    <tr>
                                                                        <td align="center" width="100px">
                                                                            <b><?= $ri['no_invoice'] ?></b>
                                                                            <?php if ($ri['is_edit'] == 1) : ?>
                                                                                <span class="label label-primary pull-right">Edited</span>
                                                                            <?php endif ?>
                                                                        </td>
                                                                        <td width="400px">
                                                                            <b><?= $ri['kode_pelanggan'] ?> | <?= $ri['nama_customer'] ?>
                                                                            </b>
                                                                        </td>
                                                                        <td align="center" width="100px">
                                                                            <?= date("d-M-Y", strtotime($ri['tgl_invoice'])) ?>
                                                                        </td>
                                                                        <td align="center" width="100px">
                                                                            <?= date("d-M-Y", strtotime($ri['tgl_invoice_dikirim'])) ?>
                                                                        </td>
                                                                        <td align="center" width="100px">
                                                                            <?= date("d-M-Y", strtotime($ri['tgl_lunas'])) ?>
                                                                        </td>
                                                                        <td align="center" width="60px">
                                                                            <?= $daysDiff ?> Hari
                                                                        </td>
                                                                        <?php if ($id_role == 21 && $ri['disposisi'] == 2) : ?>
                                                                            <td align="center" width="60px">
                                                                                <?= number_format($ri['volume']) ?>
                                                                                <input type="hidden" name="volume_incentive" id="volume_incentive<?= $ri['id'] ?>" value="<?= $ri['volume'] ?>">
                                                                            </td>
                                                                            <td align="center" width="100px">
                                                                                <?= number_format($ri['harga_dasar']) ?>
                                                                            </td>
                                                                            <td align="center" width="50px">
                                                                                <?= $ri['tier'] ?>
                                                                            </td>
                                                                            <td align="center" width="50px" id="point<?= $ri['id'] ?>" title="double click untuk Edit">
                                                                                <input class="hide" type="number" name="point_incentive" id="point_incentive<?= $ri['id'] ?>" value="<?= $ri['point_incentive'] ?>" style="width: 50px;">
                                                                                <input type="hidden" name="id_incentive" id="id_incentive<?= $ri['id'] ?>" value="<?= $ri['id'] ?>" style="width: 50px;">
                                                                                <br class="hide" id="br<?= $ri['id'] ?>">
                                                                                <span class="" id="point_incentive_text<?= $ri['id'] ?>"> <?= $ri['point_incentive'] ?> </span>
                                                                            </td>
                                                                            <td align="right" width="100px">
                                                                                <span class="" id="total_incentive_text<?= $ri['id'] ?>"><?= number_format($ri['total_incentive']) ?></span>
                                                                            </td>
                                                                        <?php else : ?>
                                                                            <td align="center" width="60px">
                                                                                <?= number_format($ri['volume']) ?>
                                                                            </td>
                                                                            <td align="center" width="100px">
                                                                                <?= number_format($ri['harga_dasar']) ?>
                                                                            </td>
                                                                            <td align="center" width="50px">
                                                                                <?= $ri['tier'] ?>
                                                                            </td>
                                                                            <td align="center" width="50px" id="point<?= $ri['id'] ?>">
                                                                                <?= $ri['point_incentive'] ?>
                                                                            </td>
                                                                            <td align="right" width="100px">
                                                                                <?= number_format($ri['total_incentive']) ?>
                                                                            </td>
                                                                        <?php endif ?>
                                                                    </tr>
                                                                    <script>
                                                                        $(document).ready(function() {
                                                                            $("#point<?= $ri['id'] ?>").dblclick(function() {
                                                                                $("#point_incentive<?= $ri['id'] ?>").removeClass("hide")
                                                                                $("#point_incentive_text<?= $ri['id'] ?>").addClass("hide")
                                                                            })
                                                                            $("#point_incentive<?= $ri['id'] ?>").keyup(function() {
                                                                                var point = $(this).val();
                                                                                var total_incentive = point * volume_incentive;
                                                                                $("#total_incentive_text<?= $ri['id'] ?>").html(new Intl.NumberFormat("ja-JP").format(total_incentive));
                                                                            })

                                                                            var initialValue = $("#point_incentive<?= $ri['id'] ?>").val();
                                                                            var id_incentive = $("#id_incentive<?= $ri['id'] ?>").val();
                                                                            var volume_incentive = $("#volume_incentive<?= $ri['id'] ?>").val();
                                                                            $(document).click(function(event) {
                                                                                if (!$(event.target).closest("#point<?= $ri['id'] ?>").length) {
                                                                                    var currentValue = $("#point_incentive<?= $ri['id'] ?>").val().trim();
                                                                                    var originalValue = $("#point_incentive_text<?= $ri['id'] ?>").text().trim();
                                                                                    $("#point_incentive<?= $ri['id'] ?>").addClass("hide");
                                                                                    $("#point_incentive_text<?= $ri['id'] ?>").removeClass("hide");
                                                                                    $("#point_incentive<?= $ri['id'] ?>").val(originalValue);

                                                                                    if (currentValue !== initialValue) {
                                                                                        $("#loading_modal").modal({
                                                                                            keyboard: false,
                                                                                            backdrop: 'static'
                                                                                        });
                                                                                        $.ajax({
                                                                                            type: 'POST',
                                                                                            url: base_url + "/web/action/incentive_update_point.php",
                                                                                            data: {
                                                                                                point: currentValue,
                                                                                                id_incentive: id_incentive,
                                                                                                volume_incentive: volume_incentive
                                                                                            },
                                                                                            cache: false,
                                                                                            dataType: 'json',
                                                                                            success: function(data) {
                                                                                                if (data.status == 200) {
                                                                                                    setTimeout(function() {
                                                                                                        Swal.fire({
                                                                                                            title: "Berhasil",
                                                                                                            text: data.pesan,
                                                                                                            icon: "success"
                                                                                                        }).then((data) => {
                                                                                                            // Reload the Page
                                                                                                            location.reload();
                                                                                                        });
                                                                                                    }, 2000);
                                                                                                } else {
                                                                                                    setTimeout(function() {
                                                                                                        Swal.fire({
                                                                                                            title: "Ooppss",
                                                                                                            text: data.pesan,
                                                                                                            icon: "warning"
                                                                                                        }).then((data) => {
                                                                                                            // Reload the Page
                                                                                                            location.reload();
                                                                                                        });
                                                                                                    }, 2000);
                                                                                                }
                                                                                            }
                                                                                        });
                                                                                    }
                                                                                }
                                                                            });
                                                                        })
                                                                    </script>
                                                                <?php endforeach ?>
                                                                <tr>
                                                                    <td colspan="10" align="center">
                                                                        <b>TOTAL</b>
                                                                    </td>
                                                                    <td align="right">
                                                                        <b>
                                                                            <?= number_format($sub_total_incentive) ?>
                                                                        </b>
                                                                    </td>
                                                                </tr>
                                                            <?php else : ?>
                                                                <tr align="center">
                                                                    <td colspan="12">
                                                                        Tidak ada data
                                                                    </td>
                                                                </tr>
                                                            <?php endif ?>
                                                        </tbody>
                                                    </table>
                                                    <br>
                                                    <br>
                                                    <?php $grand_total_incentive += $sub_total_incentive; ?>
                                                <?php endforeach ?>
                                            </div>
                                        </div>
                                        <input type="hidden" value="<?= $grand_total_incentive ?>" id="grand_total_incentive">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="data-all">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <div class="box-body">
                                        <table width="100%" border="0">
                                            <tr>
                                                <td>
                                                    <a class="btn btn-danger btn-md" target="_blank" href="<?= ACTION_CLIENT . '/cetak-incentive.php?' . paramEncrypt('id=' . $id_pengajuan . '&kategori=all') ?>"><i class="fas fa-file-pdf"></i> Cetak PDF</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="15%" style="padding: 10px;">
                                                    Nomor Pengajuan
                                                </td>
                                                <td width="2%">
                                                    :
                                                </td>
                                                <td>
                                                    <strong>
                                                        <?= $row1['nomor_pengajuan'] ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="15%" style="padding: 10px;">
                                                    Cabang
                                                </td>
                                                <td width="2%">
                                                    :
                                                </td>
                                                <td>
                                                    <strong>
                                                        <?= $cabang['nama_cabang'] ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="15%" style="padding: 10px;">
                                                    Tanggal Pengajuan
                                                </td>
                                                <td width="2%">
                                                    :
                                                </td>
                                                <td>
                                                    <strong>
                                                        <?= tgl_indo($row1['tgl_pengajuan']) ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="15%" style="padding: 10px;">
                                                    Periode Pengajuan
                                                </td>
                                                <td width="2%">
                                                    :
                                                </td>
                                                <td>
                                                    <strong>
                                                        <?= $nama_bulan . " " . $row1['periode_tahun'] ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="15%" style="padding: 10px;">
                                                    Status
                                                </td>
                                                <td width="2%">
                                                    :
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($row1['is_ceo'] == 1) {
                                                        $status = "Approved by " . $row1['ceo_by'] . " | " . tgl_indo($row1['ceo_date']);
                                                    } else {
                                                        $status = "Verifikasi CEO";
                                                    }
                                                    ?>
                                                    <strong>
                                                        <?= $status ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="15%" style="padding: 10px;">Total Incentive</td>
                                                <td>:</td>
                                                <td><b>Rp. <span id="total_all_incentive">0</span></b></td>
                                            </tr>
                                        </table>
                                        <hr>
                                        <div style="max-height: 400px; overflow-y: auto;">
                                            <div class="table-responsive">
                                                <table width="1600px" border="1">
                                                    <thead style="background-color: yellow;">
                                                        <tr>
                                                            <th style="padding: 10px;" class="text-center">Marketing</th>
                                                            <th class="text-center">Invoice</th>
                                                            <th class="text-center">Customer Name</th>
                                                            <th class="text-center">Inv Date</th>
                                                            <th class="text-center">Inv Send Date</th>
                                                            <th class="text-center">Inv Paid Date</th>
                                                            <th class="text-center">Lunas</th>
                                                            <th class="text-center">Quantity</th>
                                                            <th class="text-center">Basic Price</th>
                                                            <th class="text-center">Tier</th>
                                                            <th class="text-center">Point</th>
                                                            <th class="text-center">Incentive</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $grand_total_all_incentive = 0; ?>
                                                        <?php foreach ($res_all as $ra) : ?>
                                                            <?php
                                                            $grand_total_all_incentive += $ra['total_incentive'];

                                                            $startDate = new DateTime($ra['tgl_invoice_dikirim']);
                                                            $endDate = new DateTime($ra['tgl_lunas']);

                                                            // Menghitung selisih
                                                            $interval = $startDate->diff($endDate);
                                                            $daysDiff = $interval->days;
                                                            $daysNetto = $daysDiff - 5;

                                                            if ($ra['id_role'] == '11') {
                                                                $role = "Marketing";
                                                            } elseif ($ra['id_role'] == '17') {
                                                                $role = "Key Account Executive";
                                                            } elseif ($ra['id_role'] == '6') {
                                                                $role = "Branch Manager";
                                                            } elseif ($ra['id_role'] == '7') {
                                                                $role = "Operation manager";
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td align="center" width="250px">
                                                                    <b><?= strtoupper($ra['fullname']) ?></b>
                                                                    <br>
                                                                    <h6 style="font-size: 10px;"><i><?= strtoupper($role) ?></i></h6>
                                                                    <?php if ($ra['is_edit'] == 1) : ?>
                                                                        <span class="label label-primary pull-right">Edited</span>
                                                                    <?php endif ?>
                                                                </td>
                                                                <td align="center" width="150px">
                                                                    <b><?= $ra['no_invoice'] ?></b>
                                                                    <!-- <br>
																	Date : <?= tgl_indo($ra['tgl_invoice']) ?>
																	<hr>
																	<table width="100%">
																		<tr>
																			<td width="10%">Send</td>
																			<td width="2%">:</td>
																			<td><?= tgl_indo($ra['tgl_invoice_dikirim']) ?></td>
																		</tr>
																		<tr>
																			<td>Paid</td>
																			<td>:</td>
																			<td><?= tgl_indo($ra['tgl_lunas']) ?></td>
																		</tr>
																		<tr>
																			<td width="35%">Jumlah Hari</td>
																			<td>:</td>
																			<td><?= $daysDiff ?></td>
																		</tr>
																	</table> -->
                                                                </td>
                                                                <td width="400px">
                                                                    <b><?= $ra['kode_pelanggan'] ?> | <?= $ra['nama_customer'] ?></b>

                                                                </td>
                                                                <td align="center" width="200px">
                                                                    <?= date("d-M-Y", strtotime($ra['tgl_invoice'])) ?>
                                                                </td>
                                                                <td align="center" width="200px">
                                                                    <?= date("d-M-Y", strtotime($ra['tgl_invoice_dikirim'])) ?>
                                                                </td>
                                                                <td align="center" width="200px">
                                                                    <?= date("d-M-Y", strtotime($ra['tgl_lunas'])) ?>
                                                                </td>
                                                                <td align="center" width="100px">
                                                                    <?= $daysDiff ?>
                                                                </td>
                                                                <td align="center" width="100px">
                                                                    <?= number_format($ra['volume']) ?>
                                                                </td>
                                                                <td align="center" width="100px">
                                                                    <?= number_format($ra['harga_dasar']) ?>
                                                                </td>
                                                                <td align="center" width="100px">
                                                                    <?= $ra['tier'] ?>
                                                                </td>
                                                                <td align="center" width="100px">
                                                                    <?= $ra['point_incentive'] ?>
                                                                </td>
                                                                <td align="right" width="100px">
                                                                    <?= number_format($ra['total_incentive']) ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach ?>
                                                        <tr>
                                                            <td colspan="11" align="center">
                                                                <b>TOTAL</b>
                                                            </td>
                                                            <td align="right">
                                                                <b>
                                                                    <?= number_format($grand_total_all_incentive) ?>
                                                                </b>
                                                            </td>
                                                        </tr>
                                                        <input type="hidden" value="<?= $grand_total_all_incentive ?>" id="grand_total_all_incentive">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
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
        .table {
            margin-bottom: 10px;
        }

        .table>tbody>tr>td {
            padding: 5px;
        }

        h3.form-title {
            font-size: 18px;
            margin: 0 0 10px;
            font-weight: 700;
        }

        .table-summary>tbody>tr>td {
            padding: 3px 5px;
        }
    </style>
    <script>
        $(document).ready(function() {
            var grand_total_incentive = $("#grand_total_incentive").val();
            var grand_total_all_incentive = $("#grand_total_all_incentive").val();
            $("#total_detail_incentive").html(new Intl.NumberFormat("ja-JP").format(grand_total_incentive));
            $("#total_all_incentive").html(new Intl.NumberFormat("ja-JP").format(grand_total_all_incentive));

            $("#q2").select2({
                placeholder: "Pilih Karyawan",
                allowClear: true
            });

            $("#approved").click(function() {
                var param = `<?= $id_pengajuan_enc ?>`;
                var jenis = "approve_pengajuan";
                Swal.fire({
                    title: "Approve Pengajuan?",
                    showCancelButton: true,
                    confirmButtonText: "YA",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        $.ajax({
                            method: 'post',
                            url: '<?php echo ACTION_CLIENT ?>/incentive_bundling.php',
                            data: {
                                "jenis": jenis,
                                "id_pengajuan": param
                            },
                            dataType: 'json',
                            success: function(result) {
                                // console.log(result)
                                if (result.status == false) {
                                    setTimeout(function() {
                                        Swal.fire({
                                            title: "Ooppss",
                                            text: result.pesan,
                                            icon: "warning"
                                        }).then((result) => {
                                            // Reload the Page
                                            location.reload();
                                        });
                                    }, 2000);
                                } else {
                                    setTimeout(function() {
                                        Swal.fire({
                                            title: "Berhasil",
                                            text: result.pesan,
                                            icon: "success"
                                        }).then((result) => {
                                            location.reload();
                                        });
                                    }, 2000);
                                }
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert("Error");
                                // console.log(errorThrown)
                            }
                        })
                    }
                });
            })
        });
    </script>
</body>

</html>