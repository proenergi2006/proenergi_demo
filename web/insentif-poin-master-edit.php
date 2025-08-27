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


    $idMaster    = isset($enk["id"])?htmlspecialchars($enk["id"], ENT_QUOTES):'|';
    
    
    $id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
    $titleAct   = "Edit Poin Insentif Master";
    $action     = "update";

    $row = null;
    if ($idMaster) {
        $sql = "
                select a.JENIS_PELUNASAN,
                        a.RANGE_AWAL,
                        a.RANGE_AKHIR,
                        a.TIER,
                        a.POIN
                    FROM pro_master_poin_insentif a
                where 
                    a.id_master= '".$idMaster."' 
            "
        ;
        
        $row = $con->getRecord($sql);
        
    }
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
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                            </div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/insentif-poin-master.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Jenis Pelunasan *</label>
                                        <select id="jenis_pelunasan" name="jenis_pelunasan" class="form-control validate[required] select2">
                                            <option value="">---Pilih--</option>
                                            <option value="CBD" <?=($row['JENIS_PELUNASAN'] = 'CBD'?'selected':'');?>>CBD</option>
                                            <option value="TOP" <?=($row['JENIS_PELUNASAN'] = 'TOP'?'selected':'');?>>TOP</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Range Awal @hari*</label>
                                        <input type="text" id="range_awal" name="range_awal" value="<?=$row['RANGE_AWAL'];?>" class="form-control validate[required]" autocomplete = 'off' />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Range Akhir @hari*</label>
                                        <input type="text" id="range_akhir" name="range_akhir" value="<?=$row['RANGE_AKHIR']?>" class="form-control validate[required]" autocomplete = 'off' />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Tier*</label>
                                        <select id="tier" name="tier" class="form-control validate[required] select2">
                                            <option value="">---Pilih--</option>
                                            <?php $con->fill_select("tier","tier","pro_master_pl_insentif",$row['TIER'],"","tier",false); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Poin*</label>
                                        <input type="text" id="poin" name="poin" value="<?=$row['POIN']?>"  class="form-control validate[required]" autocomplete = 'off' />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" id="idr" name="idr" value="<?php echo $idr;?>" />
                                            <input type="hidden" id="idmaster" name="idmaster" value="<?php echo $idMaster;?>" />
                                            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/insentif-poin-master.php";?>">
                                            <i class="fa fa-reply jarak-kanan"></i>Batal</a>
                                            <button type="submit" class="btn btn-primary <?php echo ($action == "add")?'':''; ?>" name="btnSbmt" id="btnSbmt">
                                            <i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                        </div>
                                    </div>
                                </div>
                                </form>
                                
                                
                            </div>
                        </div>
                    </div>
                </div>

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
