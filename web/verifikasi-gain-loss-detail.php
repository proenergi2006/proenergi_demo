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
$cek = "
    select a.* ,b.nama_vendor, c.merk_dagang, d.nama_terminal, e.nomor_po, e.tanggal_inven
    from new_pro_inventory_gain_loss a 
    join new_pro_inventory_vendor_po e on a.id_po_supplier = e.id_master 
    join pro_master_vendor b on e.id_vendor = b.id_master 
    join pro_master_produk c on e.id_produk = c.id_master 
    join pro_master_terminal d on e.id_terminal = d.id_master   
   
	where a.id_master = '" . $idr . "'";
$row = $con->getRecord($cek);

$catatan     = ($row['catatan_transportir']) ? $row['catatan_transportir'] : '&nbsp;';
$linkCetak1    = ACTION_CLIENT . '/purchase-order-cetak.php?' . paramEncrypt('idr=' . $idr);
$linkCetak2    = ACTION_CLIENT . '/purchase-order-cetak-spj.php?' . paramEncrypt('idr=' . $idr);
$sesrol     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$simpan     = false;
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "jqueryUI", "formatNumber"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Verifikasi Gain & Loss</h1>
            </section>
            <section class="content">

                <?php if ($enk['idr'] !== '' && isset($enk['idr'])) { ?>
                    <?php $flash->display(); ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="box box-primary">
                                <div class="box-body">

                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="table-responsive">
                                                <table class="table no-border table-detail">
                                                    <tr>
                                                        <td width="70">Kode PO</td>
                                                        <td width="10">:</td>
                                                        <td><?php echo $row['nomor_po']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Supplier</td>
                                                        <td>:</td>
                                                        <td><?php echo $row['nama_vendor']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Produk</td>
                                                        <td>:</td>
                                                        <td><?php echo $row['merk_dagang']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Terminal</td>
                                                        <td>:</td>
                                                        <td><?php echo $row['nama_terminal']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal PO</td>
                                                        <td>:</td>
                                                        <td><?php echo tgl_indo($row['tanggal_inven']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Volume PO</td>
                                                        <td>:</td>
                                                        <td><?php echo number_format($row['volume_po']); ?> Ltr</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">


                                            <form action="<?php echo ACTION_CLIENT . '/po-gain-loss.php'; ?>" id="gform" name="gform" method="post" role="form">
                                                <?php
                                                if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 21)
                                                    require_once($public_base_directory . "/web/__get_data_po_gain_loss_ceo2.php");

                                                ?>



                                            </form>
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
        #table-grid3,
        .table-detail {
            margin-bottom: 15px;
        }

        #table-grid3>tbody>tr>td,
        #table-grid3>thead>tr>th {
            font-size: 11px;
            font-family: arial;
        }

        .table-detail>thead>tr>th,
        .table-detail>tbody>tr>td {
            padding: 5px;
            font-size: 12px;
        }

        .input-po {
            padding: 5px;
            height: auto;
            font-size: 11px;
            font-family: arial;
        }

        .select2-search--dropdown .select2-search__field {
            font-family: arial;
            font-size: 11px;
            padding: 4px 3px;
        }

        .select2-results__option {
            font-family: arial;
            font-size: 11px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $(".cekAksi").select2({
                placeholder: "Aksi",
                allowClear: true
            });

            $("input[name='revert']").on("ifChecked", function() {
                var nilai = $(this).val();
                if (nilai == 1) {
                    $(".persetujuan-ceo").addClass("hide");
                } else if (nilai == 2) {
                    $(".persetujuan-ceo").removeClass("hide");
                }
            });

            $("form#gform").on("click", "button:submit", function() {
                var terus = true;
                if (confirm("Apakah anda yakin?")) {
                    $("#loading_modal").modal({
                        backdrop: "static"
                    });
                    $(".cekAksi").each(function() {
                        if ($(this).val() == "") terus = false;
                    });
                    if (!terus) {
                        $("#preview_modal").find("#preview_alert").html('Kolom aksi masih ada yang kosong');
                        $("#preview_modal").modal();
                        $("#loading_modal").modal("hide");
                        return false;
                    } else {
                        $("button[type='submit']").addClass("disabled");
                        $("#gform").submit();
                    }
                    return false;
                } else return false;
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