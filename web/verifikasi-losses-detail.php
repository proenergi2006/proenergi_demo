<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");


// $base_directory    = $public_base_directory . "/files/uploaded_user/urgent";
// $file_path_con    = $base_directory . "/urgent" . $row[0]['id_pr'] . "_" . $row[0]['lampiran_con'];


// $extIkon1     = strtolower(substr($row[0]['lampiran_con'], strrpos($row[0]['lampiran_con'], '.')));





$sesgroup     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$sesrole = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$auth    = new MyOtentikasi();
$enk      = decode($_SERVER['REQUEST_URI']);
$con     = new Connection();
$flash    = new FlashAlerts;
$idr     = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$cek = "
 select a.*,
f.nomor_pr,
f.tanggal_pr,
i.nama_customer,
h.nomor_poc,
e.volume,
d.nomor_po,
j.nama_cabang,
e.nomor_po_supplier,
f.purchasing_tanggal

from pro_po_ds_detail a 
join pro_po_ds b on a.id_ds = b.id_ds 
join pro_po_detail c on a.id_pod = c.id_pod 
join pro_po d on a.id_po = d.id_po
join pro_pr_detail e on a.id_prd = e.id_prd
join pro_pr f on a.id_pr = f.id_pr
join pro_po_customer_plan g on a.id_plan = g.id_plan 
join pro_po_customer h on g.id_poc =  h.id_poc
join pro_customer i on h.id_customer = i.id_customer
join pro_master_cabang j on b.id_wilayah = j.id_master
where a.id_dsd = '" . $idr . "'";
$row = $con->getResult($cek);
$fnr = $row[0]['approval_losses'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "formatNumber", "scrolltab", "jqueryUI"), "css" => array("jqueryUI"))); ?>



<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Verifikasi Losses Detail</h1>
            </section>
            <section class="content">
                <?php if (isset($enk['idr']) and $enk['idr'] !== '') { ?>
                    <?php $flash->display(); ?>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#form-pr" aria-controls="form-pr" role="tab" data-toggle="tab">Data Verifikasi Losses</a>
                        </li>
                        <!-- <li role="presentation" class="">
                            <a href="#form-sc" aria-controls="form-sc" role="tab" data-toggle="tab">Data SC</a>
                        </li> -->

                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="form-pr">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="box box-primary">
                                        <div class="box-body">

                                            <table border="0" cellpadding="0" cellspacing="0" class="table-detail">
                                                <tr>
                                                    <td width="100">Kode DR</td>
                                                    <td width="10">:</td>
                                                    <td><?php echo $row[0]['nomor_pr']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Tanggal</td>
                                                    <td>:</td>
                                                    <td><?php echo tgl_indo($row[0]['tanggal_pr']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Cabang</td>
                                                    <td>:</td>
                                                    <td><?php echo $row[0]['nama_cabang']; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>Nomor DN</td>
                                                    <td>:</td>
                                                    <td><?php echo $row[0]['nomor_do']; ?></td>
                                                </tr>


                                            </table>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <form action="<?php echo ACTION_CLIENT . '/verifikasi-losses.php'; ?>" id="gform" name="gform" method="post" role="form" enctype="multipart/form-data">
                                                        <?php
                                                        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), [7, 6, 15])) {
                                                            require_once($public_base_directory . "/web/__get_data_losses_detail.php");
                                                        }
                                                        ?>
                                                    </form>
                                                </div>
                                            </div>



                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>





                    </div>
                <?php } ?>
                <div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Peringatan</h4>
                            </div>
                            <div class="modal-body">
                                <div id="preview_alert" class="text-center"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="inven_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Informasi Stock</h4>
                            </div>
                            <div class="modal-body"></div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="stock_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Informasi Stock</h4>
                            </div>
                            <div class="modal-body"></div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="inven_modal_history_approve" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Informasi</h4>
                            </div>
                            <div class="modal-body"></div>
                        </div>
                    </div>
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
        h3.form-title {
            font-size: 18px;
            margin: 0 0 10px;
            font-weight: 700;
        }

        #table-long,
        #table-grid2,
        #table-grid3,
        .table-detail,
        .table-ar-grid {
            margin-bottom: 15px;
        }

        #table-grid3 th,
        #table-grid3 td {
            font-size: 11px;
            font-family: arial;
        }

        .table-detail td {
            padding-bottom: 3px;
            font-size: 12px;
        }

        .table-ar-grid>thead>tr>th,
        .table-ar-grid>tbody>tr>td {
            font-size: 11px;
            font-family: arial;
        }

        .table-ar-grid>thead>tr>th {
            padding: 8px 5px;
        }
    </style>


    <script>
        $(document).ready(function() {

            $("#cekAll").on("ifChecked", function() {
                $(".chkp").iCheck("check");
            }).on("ifUnchecked", function() {
                $(".chkp").iCheck("uncheck");
            });

            var x, y, top, left, down;
            $("#table-long").mousedown(function(e) {
                if (e.target.nodeName != "INPUT" && e.target.nodeName != "SELECT") {
                    down = true;
                    x = e.pageX;
                    y = e.pageY;
                    top = $(this).scrollTop();
                    left = $(this).scrollLeft();
                }
            });
            $("body").mousemove(function(e) {
                if (down) {
                    var newX = e.pageX;
                    var newY = e.pageY;
                    $("#table-long").scrollTop(top - newY + y);
                    $("#table-long").scrollLeft(left - newX + x);
                }
            });
            $("body").mouseup(function(e) {
                down = false;
            });
        });
    </script>
</body>

</html>