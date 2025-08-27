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

    $titleAct   = "Preview Marketing MoM";
    $path_odometer_pergi = null;
    $path_odometer_pulang = null;
    $path_meeting_customer = null;
    $path_tambahan = null;
    $sql = "select * from pro_marketing_mom where deleted_time is null and id_marketing_mom=".$idr;
    $marketing_mom = $con->getRecord($sql);
    if ($marketing_mom && $marketing_mom['odometer_pergi'])
        $path_odometer_pergi = getenv('APP_HOST').getenv('APP_NAME').'/files/uploaded_user/lampiran/'.$marketing_mom['odometer_pergi'];
    if ($marketing_mom && $marketing_mom['odometer_pulang'])
        $path_odometer_pulang = getenv('APP_HOST').getenv('APP_NAME').'/files/uploaded_user/lampiran/'.$marketing_mom['odometer_pulang'];
    if ($marketing_mom && $marketing_mom['meeting_customer'])
        $path_meeting_customer = getenv('APP_HOST').getenv('APP_NAME').'/files/uploaded_user/lampiran/'.$marketing_mom['meeting_customer'];
    if ($marketing_mom && $marketing_mom['tambahan'])
        $path_tambahan = getenv('APP_HOST').getenv('APP_NAME').'/files/uploaded_user/lampiran/'.$marketing_mom['tambahan'];
    $sql1 = "select * from pro_marketing_mom_participant where deleted_time is null and id_marketing_mom=".$idr;
    $marketing_mom_participant = $con->getResult($sql1);
    $sql1 = "select * from pro_database_fuel where deleted_time is null and is_mom = 1 and id_marketing_mom=".$idr;
    $database_fuel = $con->getResult($sql1);
    $sql1 = "select * from pro_database_lubricant_oil where deleted_time is null and is_mom = 1 and id_marketing_mom=".$idr;
    $database_lubricant_oil = $con->getResult($sql1);

    $sql1 = "select * from pro_marketing_mom_file where deleted_time is null and id_marketing_mom=".$idr;
    $marketing_mom_file = $con->getResult($sql1);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber", "jqueryUI",'ckeditor'), "css"=>array("jqueryUI"))); ?>

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
                                <form action="<?php echo ACTION_CLIENT.'/marketing-mom.php'; ?>" id="gform" name="gform" method="post" role="form">
                                    <div class="col-md-12">
                                        <div class="panel" style="border: 1px solid #ddd; border-radius: 0;">
                                            <div class="panel-heading text-center" style="background-color: #f4f4f4; font-size: 20px;">Minutes of Meeting</div>
                                            <div class="panel-body">
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <label style="min-width: 100px;">Tanggal</label>
                                                        <lable> : </lable>
                                                        <?=date('d/m/Y', strtotime($marketing_mom['date']))?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <label style="min-width: 100px;">Place</label>
                                                        <lable> : </lable>
                                                        <?=$marketing_mom['place']?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <label style="min-width: 100px;">Title</label>
                                                        <lable> : </lable>
                                                        <?=$marketing_mom['title']?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <label style="min-width: 100px;">Customer</label>
                                                        <lable> : </lable>
                                                        <?=$marketing_mom['customer']?>
                                                    </div>
                                                </div>
                                                <center><h4>Kehadiran</h4></center>
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 10%;">No</th>
                                                                    <th style="width: 45%;">Nama</th>
                                                                    <th style="width: 45%;">Jabatan</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php 
                                                                    if (count($marketing_mom_participant)) {
                                                                        foreach($marketing_mom_participant as $i => $row) {
                                                                ?>
                                                                <tr>
                                                                    <td><?=($i+1)?></td>
                                                                    <td><?=$row['name']?></td>
                                                                    <td><?=$row['position']?></td>
                                                                </tr>
                                                                <?php 
                                                                        }
                                                                    } 
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-sm-12 col-md-12 col-sm-top">
                                                        <label style="min-width: 100px;">Hasil Rapat</label>
                                                        <textarea id="hasil_rapat" name="hasil_rapat" class="form-control validate[required]" ><?=$marketing_mom['hasil_rapat']?></textarea>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-md-12 col-sm-top">
                                                        <table class="table table-bordered table-dokumen">
                                                            <thead>
                                                                <th style="width: 3%;">No</th>
                                                                <th>Keterangan</th>
                                                                <th style="width: 30%;">Attachment</th>
                                                            </thead>
                                                            <tbody class="tb_file_upload">
                                                                <?php 
                                                                        $cek1 = "select * from pro_marketing_mom_file where id_marketing_mom = '".$idr."' order by id_marketing_mom_file";
                                                                        $row1 = $con->getResult($cek1);
                                                                        if(count($row1) == 0){
                                                                            echo '<tr><td colspan="4" class="text-center">Tidak ada dokumen</td></tr>';
                                                                        } else{
                                                                            $d=0;
                                                                            foreach($row1 as $dat1){
                                                                                $d++;
                                                                                $idd    = $dat1['id_marketing_mom_file'];
                                                                                $linkAt = "";
                                                                                $textAt = "";
                                                                                $pathAt = $public_base_directory.'/files/uploaded_user/lampiran/'.$dat1['file_upload'];
                                                                                $nameAt = $dat1['file_ori'];
                                                                                if($dat1['file_upload'] && file_exists($pathAt)){
                                                                                    $linkAt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=mkt_report_".$idr."_".$idd."_&file=".$nameAt);
                                                                                    $textAt = '<a href="'.$linkAt.'"><i class="fa fa-paperclip jarak-kanan"></i>'.$nameAt.'</a>';
                                                                                }
                                                                    ?>
                                                                    <tr>
                                                                        <td><?php echo $d; ?></td>
                                                                        <td><?php echo $dat1['keterangan']; ?></td>
                                                                        <td><?php echo $textAt; ?></td>
                                                                        
                                                                    </tr>
                                                                    <?php } } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" id="idr" name="idr" value="<?php echo $idr;?>" />
                                                <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/marketing-mom.php";?>">
                                                <i class="fa fa-reply jarak-kanan"></i>Kembali</a>
                                                <a class="btn btn-success jarak-kanan" href="<?php echo ACTION_CLIENT."/marketing-mom-cetak.php?".paramEncrypt("idr=".$idr);?>" target="_blank">
                                                <i class="fa fa-print jarak-kanan"></i>Cetak</a>
                                                <?php if ($sesrole==11 || $sesrole==17) { ?>
                                                <a class="btn btn-warning jarak-kanan" href="<?php echo BASE_URL_CLIENT."/marketing-mom-add.php?".paramEncrypt("idr=".$idr);?>">
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
<script type="text/javascript">
    $(document).ready(function(){
        CKEDITOR.replace('hasil_rapat',{
            language :'id',
            removeButton :'pasteFormWord',
        })
    });
</script>