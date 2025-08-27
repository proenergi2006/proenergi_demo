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
$idk     = htmlspecialchars($enk["idk"], ENT_QUOTES);
$url     = BASE_URL_CLIENT . "/po-customer-plan-add.php?" . paramEncrypt("idr=" . $idr . "&idk=" . $idk);
$cek = "select a.id_poc, lpad(a.id_poc,4,'0') as kode_po, b.nama_customer, a.volume_poc, c.vol_plan, c.realisasi,d.masa_awal, d.masa_akhir, d.nomor_surat,
            (select volume_close from pro_po_customer_close where id_poc = '" . $idk . "' and st_Aktif='Y') volume_close_po 
			from pro_po_customer a join pro_customer b on a.id_customer = b.id_customer 
			left join (
				select id_poc, sum(if(realisasi_kirim = 0,volume_kirim, realisasi_kirim)) as vol_plan, sum(realisasi_kirim) as realisasi 
				from pro_po_customer_plan where id_poc = '" . $idk . "' and status_plan not in (2,3) group by id_poc
			) c on a.id_poc = c.id_poc 
            join pro_penawaran d on a.id_penawaran =  d.id_penawaran
			where a.poc_approved = 1 and a.id_customer = '" . $idr . "' and a.id_poc = '" . $idk . "'";

$row = $con->getRecord($cek);

$cek_sales = "select * from pro_sales_confirmation a where a.id_customer = '" . $idr . "' and a.id_poc = '" . $idk . "'";
$sales = $con->getRecord($cek_sales);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>PO Customer Plan</h1>
            </section>
            <section class="content">

                <?php if ($row['id_poc'] != '') { ?>
                    <?php $flash->display(); ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="box box-primary">
                                <div class="box-body">
                                    <div class="alert alert-danger alert-dismissible" style="display:none">
                                        <div class="box-tools">
                                            <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                    <table border="0" cellpadding="0" cellspacing="0" id="table-detail">
                                        <tr>
                                            <td width="90">Kode Dokumen</td>
                                            <td width="10">:</td>
                                            <td><?php echo "PO-" . $row['kode_po']; ?></td>
                                        </tr>
                                        <tr>
                                            <td width="110">Kode Penawaran</td>
                                            <td width="10">:</td>
                                            <td> <?php echo $row['nomor_surat']; ?></td>
                                        </tr>
                                        <tr>
                                            <td width="110">Periode Penawaran</td>
                                            <td width="10">:</td>
                                            <td> <?php echo date("d/m/Y", strtotime($row['masa_awal'])); ?> - <?php echo date("d/m/Y", strtotime($row['masa_akhir'])); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Customer</td>
                                            <td>:</td>
                                            <td><?php echo $row['nama_customer']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Total Order</td>
                                            <td>:</td>
                                            <td><?php echo number_format($row['volume_poc']) . " Liter"; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Terkirim</td>
                                            <td>:</td>
                                            <td><?php echo number_format($row['realisasi']) . " Liter"; ?></td>
                                        </tr>
                                        <!-- <tr>
                                        <td>Sisa Aktual</td>
                                        <td>:</td>
                                        <td><?php echo number_format(($row['volume_poc'] - $row['realisasi'])) . " Liter"; ?></td>
                                    </tr> -->
                                        <tr>
                                            <td>Sisa Buku</td>
                                            <td>:</td>
                                            <td><?php echo number_format(($row['volume_poc'] - $row['vol_plan']  - $row['volume_close_po'])) . " Liter"; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Close PO</td>
                                            <td>:</td>
                                            <td><?php echo number_format($row['volume_close_po']) . " Liter"; ?></td>
                                        </tr>
                                    </table>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?php if (($row['vol_plan'] < $row['volume_poc']) and (intval($row['volume_poc'] - $row['vol_plan']  - $row['volume_close_po']) > 0)) {  ?>

                                                <?php
                                                $hidden = '';
                                                $span = '';
                                                if ($sales) {
                                                    if ($sales['flag_approval'] != '1') {
                                                        $hidden = 'hidden';
                                                        $span = 'Waiting Sales Confirmation';
                                                    }
                                                } ?>

                                                <div style="margin-bottom:10px;">
                                                    <a class="btn btn-sm btn-primary <?php echo $hidden; ?>" href="<?php echo $url; ?>"><i class="fa fa-plus jarak-kanan"></i>Tambah Data</a>
                                                    <span><?php echo $span; ?></span>
                                                </div>
                                            <?php } ?>
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="table-grid2">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="5%">No</th>
                                                            <th class="text-center" width="8%">Tanggal Issued</th>
                                                            <th class="text-center" width="8%">Tanggal Kirim</th>
                                                            <th class="text-center" width="26%">Alamat Kirim</th>
                                                            <th class="text-center" width="10%">Volume (Liter)</th>
                                                            <th class="text-center" width="10%">Realisasi (Liter)</th>
                                                            <th class="text-center" width="8%">Status</th>
                                                            <th class="text-center" width="20%">Catatan</th>
                                                            <th class="text-center" width="5%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $sql = "select a.*, e.id_customer, b.alamat_survey, c.nama_prov, d.nama_kab 
															from pro_po_customer_plan a join pro_customer_lcr b on a.id_lcr = b.id_lcr
															join pro_master_provinsi c on b.prov_survey = c.id_prov join pro_master_kabupaten d on b.kab_survey = d.id_kab
															join pro_po_customer e on a.id_poc = e.id_poc 
															where 1=1 and e.id_customer = '" . $idr . "' and e.id_poc = '" . $idk . "'";
                                                        $res = $con->getResult($sql);
                                                        if (count($res) == 0) {
                                                            echo '<tr><td colspan="9" style="text-align:center">Data tidak ditemukan </td></tr>';
                                                        } else {
                                                            $count = 0;
                                                            foreach ($res as $data) {
                                                                $count++;
                                                                $params    = paramEncrypt('idr=' . $data['id_customer'] . '&idk=' . $data['id_poc'] . '&idp=' . $data['id_plan'] . '&act=delete');
                                                                $linkDel = ACTION_CLIENT . '/po-customer-plan.php?' . $params;
                                                                $tempal = str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']);
                                                                $alamat    = $data['alamat_survey'] . " " . $tempal . " " . $data['nama_prov'];
                                                                $arPenting = array("Tidak", "Ya");
                                                                $arStatus  = array("Terdaftar", "Purchase Request", "Re-Schedule", "Pending");

                                                        ?>
                                                                <tr>
                                                                    <td class="text-center">
                                                                        <?php echo $count; ?>
                                                                    </td>

                                                                    <td class="text-center">
                                                                        <?php echo date("d/m/Y H:i:s", strtotime($data['created_time'])); ?>
                                                                    </td>

                                                                    <td class="text-center">
                                                                        <?php echo date("d/m/Y", strtotime($data['tanggal_kirim'])); ?>
                                                                    </td>

                                                                    <td>
                                                                        <?php echo $alamat; ?>
                                                                    </td>

                                                                    <td class="text-right">
                                                                        <?php echo number_format($data['volume_kirim']); ?>
                                                                    </td>

                                                                    <td class="text-right">
                                                                        <?php echo ($data['realisasi_kirim']) ? number_format($data['realisasi_kirim']) : ""; ?>
                                                                    </td>

                                                                    <td class="text-center">
                                                                        <?php if ($data['realisasi_kirim']) : ?>
                                                                            Delivered
                                                                        <?php else : ?>
                                                                            <?php echo $arStatus[$data['status_plan']]; ?>
                                                                        <?php endif ?>
                                                                    </td>

                                                                    <td class="text-left">
                                                                        <?php echo $data['status_plan'] == 2 ? $data['catatan_reschedule'] : $data['status_jadwal']; ?>
                                                                    </td>

                                                                    <td class="text-center action">
                                                                        <?php if (!$data['status_plan']) { ?>
                                                                            <a class="margin-sm delete btn btn-action btn-danger" title="Delete" href="<?php echo $linkDel; ?>">
                                                                                <i class="fa fa-trash"></i></a>
                                                                        <?php } ?>
                                                                    </td>
                                                                </tr>
                                                        <?php }
                                                        } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <a class="btn btn-default" href="<?php echo BASE_URL_CLIENT . "/po-customer.php"; ?>">Kembali</a>
                                            </div>
                                        </div>
                                    </div>

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

        #table-grid2 {
            margin-bottom: 15px;
        }

        #table-grid2 td,
        #table-grid2 th {
            font-size: 12px;
        }

        #table-detail {
            margin-bottom: 10px;
        }

        #table-detail td {
            padding-bottom: 3px;
            font-size: 12px;
        }
    </style>
    <script>
        $(document).ready(function() {});
    </script>
</body>

</html>