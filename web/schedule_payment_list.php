<?php
// session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$enk      = decode($_SERVER['REQUEST_URI']);
$con     = new Connection();
$flash    = new FlashAlerts;


?>



<!-- Dev Iwan AR Customer -->
<h3>AR Customer</h3>
<form name="searchForm" id="searchForm" role="form" class="form-horizontal">



    <div class="row">
        <div class="col-sm-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords Customer Name, Marketing" />
                        </div>

                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-sm btn-info" name="btnSearch" id="btnSearch"> <i class="fa fa-search jarak-kanan"></i> Search</button>
                            <a href="<?php echo BASE_URL_CLIENT . '/export-bod.php'; ?>" class="btn btn-info btn-sm">
                                <i class="fa fa-plus jarak-kanan"></i>More
                            </a>
                        </div>
                    </div>
                </div>
</form>
<div class="box-body table-responsive">
    <table class="table table-bordered" id="table-grid1">
        <thead>
            <tr>
                <th class="text-center" width="30">No</th>
                <th class="text-center" width="250">Customer</th>
                <th class="text-center" width="100">TOP</th>
                <th class="text-center" width="100">Credit Limit</th>
                <th class="text-center" width="100">Not Yet</th>
                <th class="text-center" width="200">Overdue</th>
                <th class="text-center" width="150">Reminding</th>
                <th class="text-center" width="150">Selisih</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
</div>
</div>
</div>



<!-- Sales Confirmation Log -->
<div class="row" style="display: none;">
    <div class="col-sm-12">
        <h3>Customer Commitment</h3>
        <div class="box box-info">
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover" id="table-grid">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th class="text-center" width="20%">Kode Pelanggan</th>
                            <th class="text-center" width="20%">Customer</th>
                            <th class="text-center" width="20%">Email</th>
                            <th class="text-center" width="25%">Not Yet</th>
                            <th class="text-center" width="20%">OV Under 30</th>
                            <th class="text-center" width="20%">OV Under 60</th>
                            <th class="text-center" width="20%">OV Under 90</th>
                            <th class="text-center" width="20%">Reminding</th>
                            <th class="text-center" width="20%">Customer Date</th>
                            <th class="text-center" width="20%">Add TOP</th>
                            <th class="text-center" width="20%">Add CL</th>
                            <!-- <th class="text-center" width="10%">Aksi</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($sales_log == NULL) {
                            echo '<tr><td colspan="21" style="text-align:center">Data tidak ditemukan</td></tr>';
                        } else {
                            $nom = 0;
                            foreach ($sales_log as $data) :
                                // $linkList   = paramEncrypt($data['id']."|#|".$data['id_poc']."|#|".$data['id_customer']);
                                $nom++;
                                // $linkDetail	= BASE_URL_CLIENT.'/sales_confirmation_form.php?'.paramEncrypt('id='.$data['id'].'&idp='.$data['id_poc'].'&idc='.$data['id_customer']);
                        ?>
                                <tr class="clickable-row">
                                    <td class="text-center"><?php echo $nom; ?></td>
                                    <td><?php echo $data['kode_pelanggan']; ?></td>
                                    <td><?php echo $data['nama_customer']; ?></td>
                                    <td><?php echo $data['email_customer']; ?></td>
                                    <td><?php echo $data['not_yet']; ?></td>
                                    <td><?php echo $data['ov_under_30'] ? number_format($data['ov_under_30']) : ''; ?></td>
                                    <td><?php echo $data['ov_under_60'] ? number_format($data['ov_under_60']) : ''; ?></td>
                                    <td><?php echo $data['ov_under_90'] ? number_format($data['ov_under_90']) : ''; ?></td>
                                    <td><?php echo $data['reminding'] ? number_format($data['reminding']) : ''; ?></td>
                                    <td><?php echo date('d-M-Y', strtotime($data['customer_date'])); ?></td>
                                    <td><?php echo $data['add_top'] ? number_format($data['add_top']) : ''; ?></td>
                                    <td><?php echo $data['add_cl'] ? number_format($data['add_cl']) : ''; ?></td>
                                </tr>
                        <?php
                            endforeach;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="status_bayar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-blue">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Status Bayar</h4>
            </div>
            <div class="modal-body">
                <div id="errStatLP"></div>
                <div class="form-group row">
                    <div class="col-sm-4 col-md-3">
                        <label>Tanggal</label>
                        <input type="text" name="tgl_bayar" id="tgl_bayar" class="input-sm datepicker form-control" autocomplete="off" />
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan" id="keterangan" class="input-sm form-control" autocomplete="off" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="pad bg-gray">
                            <input type="hidden" name="idLP" id="idLP" value="" />
                            <input type="hidden" name="tipeLP" id="tipeLP" value="" />
                            <button type="button" class="btn btn-primary jarak-kanan" name="btnLP1" id="btnLP1" value="1">Simpan</button>
                        </div>
                    </div>
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


<script>
    $(document).ready(function() {
        $('#btnSearch').on('click', function() {
            $("#table-grid1").ajaxGrid("draw", {
                data: {
                    q1: $("#q1").val()
                }
            });
            return false;
        });

        $("#table-grid1").ajaxGrid({
            url: "./datatable/export-ar-customer-list.php",
            data: '',
        });
    });
</script>