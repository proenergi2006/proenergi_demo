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
    $date   = isset($enk["date"])?htmlspecialchars($enk["date"], ENT_QUOTES):'';
    $time   = isset($enk["time"])?htmlspecialchars($enk["time"], ENT_QUOTES):'';
    $id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);

    $titleAct   = "Tambah Marketing Report";
    $action     = "add";

    $marketing_report = null;

    if ($idr) {
        $titleAct   = "Detail Marketing Report";
        $action     = "update";
        // $sql = "select * from pro_marketing_report where deleted_time is null and id_marketing_report=".$idr;
        $sql = "
        select 
            a.*,d.nama_customer,d.alamat_customer,d.email_customer,d.telp_customer,d.status_customer,
            b.fullname as user_name,
            b.id_role as user_role
        from 
            pro_marketing_report_master a 
            join acl_user b on b.id_user = a.create_by
            join pro_master_area c on c.id_master = b.id_wilayah
            join pro_customer d on a.id_customer = d.id_customer
        where 
            1=1 
            and a.id_mkt_report=".$idr;
        $marketing_report = $con->getRecord($sql);

        $sqlDispo="select*from pro_marketing_report_master_disposisi where id_mkt_report='".$idr."'";
        $res_dispo = $con->getResult($sqlDispo);
        // print_r($marketing_report);
        // exit();
    }
?>
<!-- ctt: ''=> draf
           1=> diajukan(ke spv)
           2=> diajukan(ke bm)
           3=> diajukan(ke bm cabang)
         -->
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber", "jqueryUI","myGrid","ckeditor"), "css"=>array("jqueryUI"))); ?>

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
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Detail Kegiatan Marketing</h3>
                            </div>
                            <div class="box-body">
                                <div class="box box-purple">
                                    <div class="box-header with-border">
                                        <p style="margin-bottom:3px;"><b><?=($marketing_report?$marketing_report['nama_customer']:'')?></b></p>
                                        <p style="margin-bottom:0px;"><?php echo $marketing_report?str_replace('<br />', PHP_EOL, $marketing_report['alamat_customer']):''; ?></p>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <td>PIC</td>
                                                <td><?=($marketing_report?$marketing_report['pic']:'')?></td>
                                            </tr>
                                            <tr>
                                                <td>No Telepon</td>
                                                <td><?=($marketing_report?$marketing_report['telp_customer']:'')?></td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td><?=($marketing_report?$marketing_report['email_customer']:'')?></td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal</td>
                                                <td><?=($marketing_report?date('d/m/Y', strtotime($marketing_report['tanggal'])):'')?></td>
                                            </tr>
                                            <tr>
                                                <td>Kegiatan</td>
                                                <td><?=($marketing_report?$marketing_report['kegiatan']:'')?></td>
                                            </tr>
                                            <tr>
                                                <td>Hasil Kegiatan</td>
                                                <td><?=($marketing_report? strip_tags(html_entity_decode($marketing_report['hasil_kegiatan'])):'')?></td>
                                            </tr>
                                             <tr>
                                                <td style="width: 20%;">History Approval</td>
                                                <td>
                                                    <?php foreach ($res_dispo as $key => $value) {
                                                        if($value['disposisi']==1 AND $value['result']==1){
                                                            echo "Approval SPV <span class='fa fa-check'></span><br>";
                                                        }else  if($value['disposisi']==2 AND $value['result']==1){
                                                            echo "Approval BM <span class='fa fa-check'></span><br>";
                                                        }else  if($value['disposisi']==2 AND $value['result']==1){
                                                            echo "Approval BM Cabang <span class='fa fa-check'></span><br>";
                                                        }
                                                    } ?>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                         <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/marketing-report.php";?>">
                                            <i class="fa fa-reply jarak-kanan"></i>Batal</a>
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
    #harga_dasar { 
        text-align: right;
    }
</style>
<script>
    $(document).ready(function(){
        $("form#gform").validationEngine('attach',{
            onValidationComplete: function(form, status){
                if(status == true){
                    $('#loading_modal').modal({backdrop:"static"});
                    form.validationEngine('detach');
                    form.submit();
                }
            }
        });

    }); 
   
</script>
</body>
</html>      
