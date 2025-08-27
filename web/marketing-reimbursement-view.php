<?php
    session_start();
    $privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
    $public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
    require_once ($public_base_directory."/libraries/helper/load.php");
    load_helper("autoload");

    $auth   = new MyOtentikasi();
    $enk    = decode($_SERVER['REQUEST_URI']);
    $con    = new Connection();
    $flash  = new FlashAlerts;
    $idr    = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):null;
    $sesrole  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);

    $titleAct   = "Preview Marketing Reimbursement";
    $sql = "select * from pro_marketing_reimbursement where deleted_time is null and id_marketing_reimbursement=".$idr;
    $marketing_reimbursement = $con->getRecord($sql);
    $sql1 = "select * from pro_marketing_reimbursement_item where deleted_time is null and id_marketing_reimbursement=".$idr;
    $marketing_reimbursement_item = $con->getResult($sql1);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber", "jqueryUI"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory."/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1><?php echo $titleAct; ?></h1>
            </section>
            <section class="content">
                <?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/marketing-reimbursement.php'; ?>" id="gform" name="gform" method="post" role="form">
                                    <div class="col-md-8">
                                        <div class="panel" style="border: 1px solid #ddd; border-radius: 0;">
                                            <div class="panel-heading text-center" style="background-color: #f4f4f4; font-size: 20px;">Form Reimbursement</div>
                                            <div class="panel-body">
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <label style="min-width: 100px;">Tanggal</label>
                                                        <lable> : </lable>
                                                        <?=date('d/m/Y', strtotime($marketing_reimbursement['marketing_reimbursement_date']))?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <label style="min-width: 100px;">No Polisi</label>
                                                        <lable> : </lable>
                                                        <?=$marketing_reimbursement['no_polisi']?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <label style="min-width: 100px;">User</label>
                                                        <lable> : </lable>
                                                        <?=$marketing_reimbursement['user']?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <label style="min-width: 100px;">KM Awal</label>
                                                        <lable> : </lable>
                                                        <?=$marketing_reimbursement['km_awal']?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <label style="min-width: 100px;">KM Akhir</label>
                                                        <lable> : </lable>
                                                        <?=$marketing_reimbursement['km_akhir']?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 5%;">No</th>
                                                                    <th style="width: 15%;">Item</th>
                                                                    <th style="width: 30%;">Keterangan</th>
                                                                    <th style="width: 20%;">Nilai</th>
                                                                    <th style="width: 20%;">Jumlah</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php 
                                                                    if (count($marketing_reimbursement_item)) {
                                                                        foreach($marketing_reimbursement_item as $i => $row) {
                                                                            $sql2 = "select * from pro_marketing_reimbursement_keterangan where deleted_time is null and id_marketing_reimbursement_item=".$row['id_marketing_reimbursement_item'];
                                                                            $marketing_reimbursement_keterangan = $con->getResult($sql2);
                                                                            if (!$marketing_reimbursement_keterangan) $marketing_reimbursement_keterangan = [];
                                                                ?>
                                                                <tr>
                                                                    <td><?=($i+1)?></td>
                                                                    <td><?=$row['item']?></td>
                                                                    <td>
                                                                        <ul style="margin-left: -20px;">
                                                                        <?php foreach ($marketing_reimbursement_keterangan as $k => $v) { ?>
                                                                            <li><?=$v['keterangan']?></li>
                                                                        <?php } ?>
                                                                        </ul>
                                                                    </td>
                                                                    <td>
                                                                        <ul style="margin-left: -30px; list-style-type: none;">
                                                                        <?php foreach ($marketing_reimbursement_keterangan as $k => $v) { ?>
                                                                            <li>Rp <span class="pull-right"><?=number_format($v['nilai'])?></span></li>
                                                                        <?php } ?>
                                                                        </ul>
                                                                    </td>
                                                                    <td>Rp <span class="pull-right"><?=number_format($row['jumlah'])?></span></td>
                                                                </tr>
                                                                <?php 
                                                                        }
                                                                    } 
                                                                ?>
                                                            </tbody>
                                                            <tfoot>
                                                                <th colspan="4" style="font-size: 13px; text-align: center;">Total</th>
                                                                <th>Rp <span class="pull-right"><?=number_format($marketing_reimbursement['total'])?></span></th>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 33%;">Dibuat</th>
                                                                    <th style="width: 33%;">Mengetahui</th>
                                                                    <th style="width: 33%;">Menyetujui</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td><p style="min-height: 80px;"></p></td>
                                                                    <td><p style="min-height: 80px;"></p></td>
                                                                    <td><p style="min-height: 80px;"></p></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <label>Bukti Transaksi : </label>
                                                        <p class="form-control" style="min-height: 400px;"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" id="idr" name="idr" value="<?php echo $idr;?>" />
                                                <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/marketing-reimbursement.php";?>">
                                                <i class="fa fa-reply jarak-kanan"></i>Kembali</a>
                                                <a class="btn btn-success jarak-kanan" href="<?php echo ACTION_CLIENT."/marketing-reimbursement-cetak.php?".paramEncrypt("idr=".$idr);?>" target="_blank">
                                                <i class="fa fa-print jarak-kanan"></i>Cetak</a>
                                                <?php if ($sesrole==11 || $sesrole==17) { ?>
                                                <a class="btn btn-warning jarak-kanan" href="<?php echo BASE_URL_CLIENT."/marketing-reimbursement-add.php?".paramEncrypt("idr=".$idr);?>">
                                                <i class="fa fa-edit jarak-kanan"></i>Edit</a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <div id="optCabang" class="hide"><?php $con->fill_select("id_master","nama_cabang","pro_master_cabang","","where is_active=1 and id_master <> 1","",false); ?></div>
            <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Loading Data ...</h4>
                        </div>
                        <div class="modal-body text-center modal-loading"></div>
                    </div>
                </div>
            </div>
            <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
        </aside>
    </div>

<style type="text/css">
    h3.form-title {
         font-size: 18px;
         margin: 0 0 10px;
         font-weight: 700;
    }
</style>
</body>
</html>      
