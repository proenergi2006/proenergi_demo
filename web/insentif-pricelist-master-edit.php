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


    $id    = isset($enk["id"])?htmlspecialchars($enk["id"], ENT_QUOTES):null;
    
    

    // $tglAwal    = isset(paramDecrypt($enk["tglawal"]))?htmlspecialchars(paramDecrypt($enk["tglawal"]), ENT_QUOTES):null;
    // $tglAkhir   = isset(paramDecrypt($enk["tglakhir"]))?htmlspecialchars(paramDecrypt($enk["tglakhir"]), ENT_QUOTES):null;
    $id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
    $titleAct   = "Edit Tier Insentif Master";
    $action     = "update";

    $row = null;
    if ($id) {
        $sql = "
                select 
                        a.id_master,
                        a.TIER,
                        DATE_FORMAT(a.TGL_AWAL, '%d/%m/%Y') TGL_AWAL,
                        DATE_FORMAT(a.TGL_AKHIR, '%d/%m/%Y') TGL_AKHIR,
                        a.HARGA_AWAL,
                        a.HARGA_AKHIR
                    FROM pro_master_pl_insentif a
                where 
                    a.id_master = '".$id."'
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

                                <form action="<?php echo ACTION_CLIENT.'/insentif-pricelist-master.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Tier *</label>
                                        <input type="text" id="tier" readonly value="<?php echo $row['TIER']; ?>" name="tier" class="form-control validate[required]" autocomplete = 'off' style="pointer-events: none;" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label>Tanggal Periode *</label>
                                                <input readonly type="text" id="tgl_awal" name="tgl_awal" class="form-control datepicker validate[required,custom[date]]" autocomplete = 'off' value="<?php echo $row['TGL_AWAL'] ?? null; ?>" />
                                                <small class="text-muted">Tanggal awal</small>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>&nbsp;</label>
                                                <input readonly type="text" id="tgl_akhir" name="tgl_akhir" class="form-control datepicker validate[required,custom[date]]" autocomplete = 'off' value="<?php echo $row['TGL_AKHIR'] ?? null; ?>" style="pointer-events: none;" />
                                                <small class="text-muted">Tanggal akhir</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label>Range Harga *</label>
                                                <input type="text" id="harga_awal" name="harga_awal" class="form-control hitung" value="0" autocomplete = 'off' value="<?php echo $row['HARGA_AKHIR'] ?? null; ?>" />
                                                <small class="text-muted">Harga awal</small>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>&nbsp;</label>
                                                <input type="text" id="harga_akhir" name="harga_akhir" class="form-control hitung validate[required]" autocomplete = 'off' value="<?php echo $row['HARGA_AKHIR'] ?? null; ?>" />
                                                <small class="text-muted">Harga akhir</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" id="idr" name="idr" value="<?php echo $idr;?>" />
                                            <input type="hidden" id="id_master" name="id_master" value="<?php echo $id;?>" />
                                            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/insentif-pricelist-master.php";?>">
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
        $(".hitung").number(true, 0, ".", ",");
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
