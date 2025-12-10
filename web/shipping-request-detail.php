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

$cek = "SELECT a.*,a.created_by as sr_created,a.created_at as sr_createdat,c.*,concat(nama_terminal,' - ',tanki_terminal,' - ',lokasi_terminal) as nama_terminal,b.nomor_po, e.nama_vendor, f.nama_suplier
		FROM new_pro_inventory_vendor_po_ship_req a 
		JOIN new_pro_inventory_vendor_po b ON a.id_vendor_po = b.id_master
		LEFT JOIN pro_master_oa_kapal c ON a.id_vessel = c.id_master
		JOIN pro_master_terminal d ON a.id_terminal_discharging=d.id_master
        JOIN pro_master_vendor e ON b.id_vendor=e.id_master
        JOIN pro_master_transportir f ON c.id_transportir =f.id_master
        WHERE a.id_master = '" . $idr . "'";
$row = $con->getResult($cek);

$loading_date='';
if(date("m",strtotime($row[0]['etl_date_first']) == date("m",strtotime($row[0]['etl_date_last'])))){
    if(date("d",strtotime($row[0]['etl_date_first'])) == date("d",strtotime($row[0]['etl_date_last']))){
        $loading_date=tgl_indo($row[0]['etl_date_first']);
    }else{
        $loading_date=date("d",strtotime($row[0]['etl_date_first']))."-".tgl_indo(($row[0]['etl_date_last']));
    }
}else{
    $loading_date=($row[0]['etl_date_first'])."-".tgl_indo($row[0]['etl_date_last']);
}
// $linkCtk1 = ACTION_CLIENT . "/delivery-request-cetak.php?" . paramEncrypt('idr=' . $idr);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "formatNumber", "scrolltab"))); ?>

<style>
    .table-detail td {
        padding: 5px; /* Menambahkan padding ke sel tabel */
    }
</style>
<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Shipping Request Detail</h1>
            </section>
           
            <section class="content">
                <?php $flash->display(); ?>
                <?php if (isset($enk['idr']) and $enk['idr'] !== '') { ?>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="form-pr">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="box box-primary">
                                        <div class="box-body">

                                            <table border="0" cellpadding="10" class="table-detail" >
                                                <tr>
                                                    <td width="140">No Shipping Instruction</td>
                                                    <td width="10">:</td>
                                                    <td><?php echo $row[0]['nomor_req']; ?></td>
                                                </tr>
                                                <!-- <tr>
                                                    <td width="140">No Shipping Instruction</td>
                                                    <td width="10">:</td>
                                                    <td><?php echo $row[0]['nomor_si']; ?></td>
                                                </tr> -->
                                                <tr>
                                                    <td>Tanggal Request</td>
                                                    <td>:</td>
                                                    <td><?php echo tgl_indo($row[0]['created_at']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Estimated Loading Date</td>
                                                    <td>:</td>
                                                    <td><?php echo $loading_date; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Loss Tolerance</td>
                                                    <td>:</td>
                                                    <td><?php echo $row[0]['loss_tolerance']; ?> %</td>
                                                </tr>
                                                <tr>
                                                    <td>BL Ship on Board</td>
                                                    <td>:</td>
                                                    <td><?php echo $row[0]['bl_ship']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Transportir</td>
                                                    <td>:</td>
                                                    <td><?php echo $row[0]['nama_suplier']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Vessel Name</td>
                                                    <td>:</td>
                                                    <td><?php echo $row[0]['nama_kapal']." - ".  $row[0]['tipe_kapal']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Port of Loading</td>
                                                    <td>:</td>
                                                    <td><?php echo $row[0]['asal_angkut']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Port of Discharging</td>
                                                    <td>:</td>
                                                    <td><?php echo $row[0]['tujuan_angkut']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Catatan Purchasing</td>
                                                    <td>:</td>
                                                    <td><?php echo $row[0]['ket_ship']; ?></td>
                                                </tr>

                                            </table>
                                            <!-- <div style="overflow-y:scroll; overflow:scroll;" id="table-long"> -->
	                                            <div style="width:100%;">
                                                    <div class="table-responsive-satu">
                                                        <table class="table table-bordered table-grid3" id="table-grid3">
                                                            <thead>
                                                                <tr>
                                                                    <th>PO Supplier</th>
                                                                    <th>No PO Supplier</th>
                                                                    <th>Quantity</th>
                                                                    <th>Freight</th>
                                                                    <th>Cargo Name</th>
                                                                    <th width="300">Loading Port</th>
                                                                    <th width="300">Discharging Port</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td><?php echo $row[0]['nama_vendor']; ?></td>
                                                                    <td><?php echo $row[0]['nomor_po']; ?></td>
                                                                    <td><?php echo number_format($row[0]['quantity']); ?></td>
                                                                    <td><?php echo number_format($row[0]['freight'],2); ?></td>
                                                                    <td><?php echo $row[0]['cargo_name']; ?></td>
                                                                    <td><?php echo $row[0]['loading_port']; ?></td>
                                                                    <td><?php echo $row[0]['nama_terminal']; ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            <!-- </div> -->
                                            <form action="<?php echo ACTION_CLIENT . '/shipping-request-detail.php'; ?>" id="gform" name="gform" method="post" role="form" enctype="multipart/form-data">
                                                <!-- <div class="col-xl-12">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Vessel Name *</label>
                                                            <select id="id_vessel" name="id_vessel" tabindex="1" class="form-control select2 validate[required]">
                                                                <option></option> <?php $con->fill_select("id_master", "concat(nama_kapal,' - ',tipe_kapal)", "pro_master_oa_kapal", $row[0]['id_vessel'], "", "id_master", false); ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 col-sm-top">
                                                            <label>Freight</label>
                                                            <div class="input-group">
                                                                <input type="text" id="freight" name="freight" tabindex="4" class="form-control" value="<?php echo $row[0]['freight']; ?>" readonly/>
                                                                <span class="input-group-addon" style="font-size:12px;">/ L</span>
                                                            </div>
                                                        </div>
                                                       <div class="col-sm-3 col-sm-top">
                                                            <label>Loss Tolerance *</label>
                                                            <div class="input-group">
                                                                <input type="text" id="loss" name="loss" tabindex="9" class="form-control validate[required] hitung" value="<?php echo isset($row[0]['loss_tolerance']) ? $row[0]['loss_tolerance'] : 0; ?>" />
                                                                <span class="input-group-addon" style="font-size:12px;">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                                
                                                <?php if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 16) { ?>
                                                <div class="form-group row">
                                                    <div class="col-sm-6 col-sm-top" style="margin-top:10px;">
                                                    <label>Catatan Logistik</label>
                                                    <textarea name="ket_log" id="ket_log" class="form-control"><?php if ($row) {
                                                            echo str_replace("<br />", PHP_EOL, $row[0]['ket_log']);
                                                        } ?></textarea>
                                                    </div>
                                                </div>
                                                <?php } else if($row[0]['status'] != 0){?>
                                                    <div class="form-group row ">
                                                        <div class="col-sm-6">
                                                            <label>Catatan Logistik</label>
                                                            <div class="form-control" style="height:auto">
                                                                <?php echo $row[0]['ket_log']; ?>
                                                                <?php
                                                                $picnya = ($row[0]['log_pic']);
                                                                $tglnya = ($row[0]['log_tanggal']);
                                                                ?>
                                                                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $picnya . " - " . date("d/m/Y H:i:s", strtotime($tglnya)) . " WIB"; ?></i></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                 <?php }?>
                                                <?php if ( paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 4  && $row[0]['cfo_result'] == 0) { ?>
                                                    <div class="form-group row">
                                                        <div class="col-sm-3">
                                                            <label>Approve shipping?*</label>
                                                            <div class="radio clearfix" style="margin:0px;">
                                                                <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="approve" id="approve1" class="validate[required]" value="1" /> Ya</label>
                                                                <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="approve" id="approve2" class="validate[required]" value="2" /> Tidak</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4 col-sm-top kembalikan-cfo" <?php echo (!$fnr) ? 'disabled' : ''; ?>><label>Catatan CFO</label>
                                                            <textarea name="cfo_summary" id="cfo_summary" class="form-control"><?php if ($row) {
                                                                echo str_replace("<br />", PHP_EOL, $row[0]['cfo_summary']);
                                                            } ?></textarea>
                                                        </div>
                                                    </div>
                                                <?php }
                                                    if($row[0]['cfo_result'] != 0){ ?>
                                                        <div class="form-group row ">
                                                            <div class="col-sm-6">
                                                                <label>Catatan CFO</label>
                                                                <div class="form-control" style="height:auto">
                                                                    <?php echo $row[0]['cfo_summary']; ?>
                                                                    <?php
                                                                    $picnya = ($row[0]['cfo_pic']);
                                                                    $tglnya = ($row[0]['cfo_tanggal']);
                                                                    ?>
                                                                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $picnya . " - " . date("d/m/Y H:i:s", strtotime($tglnya)) . " WIB"; ?></i></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                <?php if ( paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 21  && $row[0]['ceo_result'] == 0) { ?>
                                                    <div class="form-group row">
                                                        <div class="col-sm-3">
                                                            <label>Approve shipping?*</label>
                                                            <div class="radio clearfix" style="margin:0px;">
                                                                <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="approve" id="approve1" class="validate[required]" value="1" /> Ya</label>
                                                                <label class="col-xs-12" style="margin-bottom:5px;"><input type="radio" name="approve" id="approve2" class="validate[required]" value="2" /> Tidak</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4 col-sm-top kembalikan-cfo" <?php echo (!$fnr) ? 'disabled' : ''; ?>><label>Catatan CEO</label>
                                                            <textarea name="ceo_summary" id="ceo_summary" class="form-control"><?php if ($row) {
                                                                echo str_replace("<br />", PHP_EOL, $row[0]['ceo_summary']);
                                                            } ?></textarea>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    }
                                                    if($row[0]['ceo_result'] != 0){ 
                                                    ?>
                                                        <div class="form-group row ">
                                                            <div class="col-sm-6">
                                                                <label>Catatan CEO</label>
                                                                <div class="form-control" style="height:auto">
                                                                    <?php echo $row[0]['ceo_summary']; ?>
                                                                    <?php
                                                                    $picnya = ($row[0]['ceo_pic']);
                                                                    $tglnya = ($row[0]['ceo_tanggal']);
                                                                    ?>
                                                                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $picnya . " - " . date("d/m/Y H:i:s", strtotime($tglnya)) . " WIB"; ?></i></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />
                                                <div style="margin-bottom:0px;">
                                                    <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                                    <a href="<?php echo BASE_URL_CLIENT . '/shipping-request-list.php'; ?>" class="btn btn-default jarak-kanan" style="min-width:90px;">Kembali</a>
                                                    <?php if (($row[0]['status'] == 0) && paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) ==16 ) { ?>
                                                        <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">Simpan</button>
                                                    <?php } ?>
                                                    <?php if (($row[0]['ceo_result'] == 0) && paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) ==21 ) { ?>
                                                        <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">Simpan</button>
                                                    <?php } ?>
                                                    <?php if ((paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 4 && $row[0]['status'] == 1)) { ?>
                                                        <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">Simpan</button>
                                                    <?php } ?>
                                                   
                                                    <?php if (($row[0]['status'] != 0 && paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 16)) { ?>
                                                    <!-- <?php if (($row[0]['status'] == 4 &&  $row[0]['ceo_result'] == 1))  ?> -->
                                                        <!-- <a href="<?php echo ACTION_CLIENT . '/shipping-instruction-cetak.php?' . paramEncrypt('idr=' . $idr); ?>" class="btn btn-success" style="min-width:90px;">
                                                            <i class="fa fa-print"></i> Cetak</a>
                                                        </div> -->
                                                        <div class="btn-group jarak-kanan">
                                                            <button type="button" class="btn btn-success">Cetak Dokumen</button>
                                                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                                                <span class="caret"></span>
                                                                <span class="sr-only">Toggle Dropdown</span>
                                                            </button>
                                                            <ul class="dropdown-menu" role="menu">
                                                                <li><a target="_blank"  href="<?php echo ACTION_CLIENT . '/shipping-instruction-cetak.php?' . paramEncrypt('idr=' . $idr. '&tipe=shipping_instruction'); ?>">Shipping Instruction</a></li>
                                                                <li><a target="_blank"  href="<?php echo ACTION_CLIENT . '/shipping-instruction-cetak.php?' . paramEncrypt('idr=' . $idr.'&tipe=LO'); ?>">LO</a></li>
                                                                <li><a target="_blank"  href="<?php echo ACTION_CLIENT . '/shipping-instruction-cetak.php?' . paramEncrypt('idr=' . $idr.'&tipe=spal'); ?>">SPAL</a></li>
                                                            </ul>
                                                        </div>
                                                    <?php }?>
                                            </form>
                                        </div>
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
                    </div>
                <?php } ?>
             
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
            $(".hitung").number(true, 2, ".", ",");
            $("form#gform").on("click", "#btnSbmt", function(e) {
                 e.preventDefault();
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
                        $("form#gform").submit();
                    }
                })
            });
            $("#id_vessel").on("change", function() {
            var id_vessel = $(this).val();

                $.ajax({
                    type: "POST",
                    url: base_url + "/web/vendor-shipping-detail.php",
                    data: {
                        "id_vessel": id_vessel
                    },
                    dataType: "json",
                    cache: false,
                    success: function(response) {
                        console.log(response)
                        var data = response.data
                        if (response.status == true) {
                            
                            $("#freight").val(data.harga_angkut);
                        }
                    }
                });

            });
        });
    </script>
</body>

</html>