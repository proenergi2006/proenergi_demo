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
    $sesrol = paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]);
    $seswil     = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);

    $titleAct   = "Tambah Marketing Report";
    $action     = "add";

    $marketing_report = null;



    if ($idr) {
        $titleAct   = "Apporve Marketing Report";
        $action     = "update";
         $sqlCust = "select * from pro_marketing_report_master where id_mkt_report=".$idr;
         $rowcust = $con->getRecord($sqlCust);

        $sql = "
        select 
            a.*,d.nama_customer,d.alamat_customer,d.email_customer,d.telp_customer,d.status_customer,
            b.fullname as user_name,
            b.id_role as user_role,";
        $cek1 = "select id_wilayah from pro_customer where id_customer = '".$rowcust['id_customer']."'";
            $row1 = $con->getRecord($cek1);
        if($sesrol=='7'){
            if($row1['id_wilayah'] != $seswil){
                $sql .=" (select result from pro_marketing_report_master_disposisi where id_mkt_report=a.id_mkt_report and disposisi=2) as result,";
                $sql .=" (select catatan from pro_marketing_report_master_disposisi where id_mkt_report=a.id_mkt_report and disposisi=2) as catatan";
            }else{
                $sql .=" (select result from pro_marketing_report_master_disposisi where id_mkt_report=a.id_mkt_report and disposisi=3) as result,";
                $sql .=" (select catatan from pro_marketing_report_master_disposisi where id_mkt_report=a.id_mkt_report and disposisi=3) as catatan";
            }
        }else if($sesrol=='20'){
            $sql .=" (select result from pro_marketing_report_master_disposisi where id_mkt_report=a.id_mkt_report and disposisi=1) as result,";
            $sql .=" (select catatan from pro_marketing_report_master_disposisi where id_mkt_report=a.id_mkt_report and disposisi=1) as catatan";
        }
            

        $sql .=" from 
            pro_marketing_report_master a 
            join acl_user b on b.id_user = a.create_by
            join pro_master_area c on c.id_master = b.id_wilayah
            join pro_customer d on a.id_customer = d.id_customer
        where 
            1=1 
            and a.id_mkt_report=".$idr;
           
            //join pro_marketing_report_master_disposisi e on a.id_mkt_report=e.id_mkt_report
        // if($sesrol=='7'){
        //     $cek1 = "select id_wilayah from pro_customer where id_customer = '".$rowcust['id_customer']."'";
        //     $row1 = $con->getRecord($cek1);
        //     if($row1['id_wilayah'] != $seswil){
        //         $sql .=" AND e.disposisi=3";
        //     }else{
        //         $sql .=" AND e.disposisi=2";
        //     }
        // }else if($sesrol=='20'){
        //     $sql .=" AND e.disposisi=1";
        // }
        
        $marketing_report = $con->getRecord($sql);

        $sqlDispo="select*from pro_marketing_report_master_disposisi where id_mkt_report='".$idr."'";
        $res_dispo = $con->getResult($sqlDispo);
        
    }
?>
<!-- ctt: ''=> draf
           1=> diajukan(ke spv)
           2=> diajukan(ke bm wilayah)
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
                                <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Apporve Kegiatan Marketing</h3>
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
                                                        }else  if($value['disposisi']==3 AND $value['result']==1){
                                                            echo "Approval BM Cabang <span class='fa fa-check'></span><br>";
                                                        }
                                                    } ?>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8 col-md-8 col-sm-top">
                                        <label>Catatan</label>
                                        <textarea name="disposisi_catatan" id="disposisi_catatan" class="form-control validate[required]"><?php echo $marketing_report['catatan']; ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" id="idr" name="idr" value="<?php echo $idr;?>" />
                                            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/marketing-report.php";?>">
                                            <i class="fa fa-reply jarak-kanan"></i>Batal</a>
                                            <?php if($marketing_report['result']==''){ ?>
                                            <?php if($sesrol=='20' OR $sesrol=='7'){ ?>
                                            <button type="button" class="btn btn-success" name="btnApprove" id="btnApprove">
                                            <i class="fa fa-check jarak-kanan"></i>Approve</button>
                                            <?php }?>
                                            <?php }?>
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

            <div class="modal fade" id="approveConfirm" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Confirmation</h4>
                        </div>
                        <form action="<?php echo ACTION_CLIENT.'/marketing-report.php'; ?>" name="approveform" method="post" role="form">
                        <div class="modal-body">
                                <input type="hidden" class="form-control input-sm" name="act" value="approve"/>
                                <input type="hidden" id="idr_approve" name="idr" value="<?php echo $idr;?>" />
                                <input type="hidden" id="catatan_approve" name="catatan" value="" />
                                <input type="hidden" id="idc_approve" name="profile_customer_nama_customer" value="<?=($marketing_report?$marketing_report['id_customer']:'')?>" />
                                 <p style="font-size:14px;"><i>* Apakah anda yakin akan menyetujui report marketing?</i></p>
                        </div>
                        <div class="modal-footer">
                             <button type="button" class="btn btn-default" name="btnBatal" id="btnBatal" data-dismiss="modal"><i class="fa fa-reply jarak-kanan"></i>Batal</button>
                             <button type="submit" class="btn btn-success" name="btnDoApprove" ><i class="fa fa-check jarak-kanan"></i>Ok</button>
                        </div>
                        </form>
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

        $('#btnApprove').click(function(){
            var ctt=$('#disposisi_catatan').val();
            $('#catatan_approve').val(ctt);
            $('#approveConfirm').modal('show');
        })
    }); 
    
</script>
</body>
</html>      
