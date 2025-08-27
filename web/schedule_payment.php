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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $q1 = $_POST['q1'];
    $q2 = $_POST['q2'];
    $q3 = $_POST['q3'];



    $sql = "
        select 
            o.*, 
            a.id_prd, 
            a.volume, 
            a.transport, 
            a.schedule_payment, 
            c.tanggal_kirim, 
            a.is_approved, 
            h.nama_customer, 
            h.id_customer, 
            d.harga_poc, 
            b.nomor_pr, 
            h.kode_pelanggan, 
            a.pr_kredit_limit,
            d.st_bayar_po
        from 
            pro_pr_detail a 
            join pro_pr b on a.id_pr = b.id_pr 
            join pro_po_customer_plan c on a.id_plan = c.id_plan 
            join pro_po_customer d on c.id_poc = d.id_poc 
            join pro_customer h on d.id_customer = h.id_customer 
            join pro_sales_confirmation o on o.id_poc = d.id_poc
        where 
            o.flag_approval = 1
            and o.type_customer='Customer Commitment'
           
            and o.customer_date is not null 
            -- and o.customer_date >= '" . date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -7 day')) . "' 
            and a.is_approved = '1' 
            and o.id_wilayah = " . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "
        ";

    if (!empty($_POST['datestart']) && !empty($_POST['dateend'])) {
        $tanggalAwal = date_create_from_format('d/m/Y', $_POST['datestart']);
        $tanggalAkhir = date_create_from_format('d/m/Y', $_POST['dateend']);

        if ($tanggalAwal && $tanggalAkhir) {
            // Konversi berhasil, lanjutkan penggunaan tanggal
            $tanggalAwal = $tanggalAwal->format('Y-m-d');
            $tanggalAkhir = $tanggalAkhir->format('Y-m-d');
            $sql .= " AND o.customer_date BETWEEN '$tanggalAwal' AND '$tanggalAkhir'";
        }
    }
    if (!empty($_POST['q3'])) {
        $namaCustomer = $_POST['q3'];
        $sql .= " AND h.nama_customer LIKE '%$namaCustomer%'";
    }

    $sql .= "
            ORDER BY c.tanggal_kirim DESC
            LIMIT 5
        ";
} else {

    $sql = "
        SELECT 
            o.*, 
            a.id_prd, 
            a.volume, 
            a.transport, 
            a.schedule_payment, 
            c.tanggal_kirim, 
            a.is_approved, 
            h.nama_customer, 
            h.id_customer, 
            d.harga_poc, 
            b.nomor_pr, 
            h.kode_pelanggan, 
            a.pr_kredit_limit,
            d.st_bayar_po
        FROM 
            pro_pr_detail a 
            JOIN pro_pr b ON a.id_pr = b.id_pr 
            JOIN pro_po_customer_plan c ON a.id_plan = c.id_plan 
            JOIN pro_po_customer d ON c.id_poc = d.id_poc 
            JOIN pro_customer h ON d.id_customer = h.id_customer 
            JOIN pro_sales_confirmation o ON o.id_poc = d.id_poc
        WHERE 
            o.flag_approval = 1
            and o.type_customer='Customer Commitment'
            and o.customer_date is not null 
            and o.customer_date >= '" . date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -7 day')) . "' 
            and a.is_approved = '1' 
            and o.id_wilayah = " . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "
        ORDER BY c.tanggal_kirim DESC
        LIMIT 5
    ";
}


$res = $con->getResult($sql);

$query = '
        select 
            b.kode_pelanggan,
            b.nama_customer,
            b.email_customer,
            a.not_yet,
            a.ov_under_30,
            a.ov_under_60,
            a.ov_under_90,
            a.reminding,
            a.customer_date,
            a.customer_amount,
            a.add_top,
            a.add_cl 
        from pro_sales_confirmation_log a 
        left join pro_customer b on a.id_customer = b.id_customer 
        where 
            reminding > 0 
            and type_customer = "Customer Commitment" 
            and proposed_status = 1 
            and customer_amount is not null 
            -- and customer_date >= "' . date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -7 day')) . '" 
        order by id desc 
        limit 10
    ';

$sales_log = $con->getResult($query);

$query_limit = " select a.*,
                a.credit_limit-COALESCE((select sum(b.add_cl) from pro_sales_confirmation_log b where a.id_customer = b.id_customer and  b.proposed_status=1 group by b.id_customer ),0) as sisa,
    COALESCE((select sum(b.add_cl) from pro_sales_confirmation_log b where a.id_customer = b.id_customer group by b.id_customer),0) as terpakai
            from pro_customer a

            where 1=1  and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'
            Limit 10";

$credit_limit = $con->getResult($query_limit);
?>


<!-- Scedule Payment -->
<div class="row">
    <div class="col-sm-12">
        <h3>Schedule Payment</h3>
        <div class="box box-info">
            <div class="box-header with-border">
                <form name="sFrm" id="sFrm" method="post">
                    <div class="row">
                        <div class="col-sm-5">
                            <label>Tgl </label>
                            <input type="text" name="datestart" class="datepicker input-cr-sm" value="<?php echo date('d/m/Y'); ?>" /> s/d
                            <input type="text" name="dateend" class="datepicker input-cr-sm" value="<?php echo @$_POST['q2']; ?>" autocomplete="off" />
                        </div>

                        <div class="col-sm-5">

                            <input type="text" name="q3" id="q3" class="form-control input-sm" placeholder="Keywords Customer Name, Marketing" value="<?php echo @$_POST['q3']; ?>" />

                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-info btn-sm" name="btnSc"><i class="fa fa-search jarak-kanan"></i>Search</button>
                            <a href="<?php echo BASE_URL_CLIENT . '/report/f-schedule-payment.php'; ?>" class="btn btn-info btn-sm">
                                <i class="fa fa-plus jarak-kanan"></i>More
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover" id="table-grid">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th class="text-center" width="10%">Customer</th>
                            <th class="text-center" width="10%">Kode PR</th>
                            <th class="text-center" width="15%">Tanggal dan Volume Kirim </th>
                            <th class="text-center" width="10%">Schedule Payment</th>
                            <th class="text-center" width="10%">Tipe Customer</th>
                            <th class="text-center" width="20%">Customer Item</th>
                            <th class="text-center" width="20%">Customer Amount</th>
                            <th class="text-center" width="15%">Status</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($res == NULL) {
                            echo '<tr><td colspan="21" style="text-align:center">Data tidak ditemukan</td></tr>';
                        } else {
                            $nom = 0;
                            foreach ($res as $data) :
                                $linkList   = paramEncrypt($data['id'] . "|#|" . $data['id_poc'] . "|#|" . $data['id_customer']);
                                $nom++;
                                $linkDetail    = BASE_URL_CLIENT . '/sales_confirmation_form.php?' . paramEncrypt('id=' . $data['id'] . '&idp=' . $data['id_poc'] . '&idc=' . $data['id_customer']);
                        ?>
                                <tr class="clickable-row">
                                    <td class="text-center"><?php echo $nom; ?></td>
                                    <td><?php echo $data['nama_customer']; ?></td>
                                    <td><?php echo $data['nomor_pr']; ?></td>
                                    <td><?php echo date("d/m/Y", strtotime($data['tanggal_kirim'])) . " - " . number_format($data['volume']) . " Liter"; ?></td>
                                    <td><?php echo date('d-M-Y', strtotime($data['customer_date'])); ?></td>
                                    <td><?php echo $data['type_customer']; ?></td>
                                    <td><?php echo $data['customer_items']; ?></td>

                                    <td><?php echo number_format($data['customer_amount']); ?></td>
                                    <td>
                                        <?php
                                        if ($data['st_bayar_po'] == 'Y') {
                                            echo '<strong>Sudah Dibayar</strong>';
                                        } else {
                                            echo '<strong>Belum Dibayar</strong>';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center action">

                                        <?php
                                        if ($data['st_bayar_po'] == 'Y') {
                                            echo '';
                                        } else {
                                            echo '<a class="editStsT margin-sm btn btn-action btn-info" data-param="' . $linkList . '"><i class="fa fa-info-circle"></i></a>';
                                        }
                                        ?>
                                        <!-- <a class="margin-sm btn btn-action btn-info" title="Detail" href="<?php echo $linkDetail; ?>"><i class="fa fa-info-circle"></i></a> -->
                                    </td>
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
                            <a href="<?php echo BASE_URL_CLIENT . '/export.php'; ?>" class="btn btn-info btn-sm">
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
                                // $linkDetail  = BASE_URL_CLIENT.'/sales_confirmation_form.php?'.paramEncrypt('id='.$data['id'].'&idp='.$data['id_poc'].'&idc='.$data['id_customer']);
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
<a class="btn btn-action btn-info input-sm" title="Jadwal" href="<?php echo BASE_URL_CLIENT; ?>/calender_admin.php"><i class="fa fa-info-circle"></i> Lihat Jadwal</a>


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
            url: "./datatable/export-ar-customer.php",
            data: '',
        });
    });
</script>