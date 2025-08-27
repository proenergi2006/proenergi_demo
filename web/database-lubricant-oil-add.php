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

    $titleAct   = "Tambah Database Lubricant Oil";
    $action     = "add";

    $database_lubricant_oil = null;
    if ($idr) {
        $titleAct   = "Edit Database Lubricant Oil";
        $action     = "update";
        $sql = "select * from pro_database_lubricant_oil where deleted_time is null and id_database_lubricant_oil=".$idr;
        $database_lubricant_oil = $con->getRecord($sql);
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
                                <form action="<?php echo ACTION_CLIENT.'/database-lubricant-oil.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-8 col-md-8 col-sm-top">
                                        <label>Nama Customer*</label>
                                        <input type="text" id="nama_customer" name="nama_customer" class="form-control validate[required]" autocomplete = 'off' value="<?=($database_lubricant_oil?$database_lubricant_oil['nama_customer']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8 col-md-8 col-sm-top">
                                        <label>Jenis Oil*</label>
                                        <input type="text" id="jenis_oil" name="jenis_oil" class="form-control validate[required]" autocomplete = 'off' value="<?=($database_lubricant_oil?$database_lubricant_oil['jenis_oil']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8 col-md-8 col-sm-top">
                                        <label>Spesifikasi*</label>
                                        <input type="text" id="spesifikasi" name="spesifikasi" class="form-control validate[required]" autocomplete = 'off' value="<?=($database_lubricant_oil?$database_lubricant_oil['spesifikasi']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8 col-md-8 col-sm-top">
                                        <label>Konsumsi Volume*</label>
                                        <input type="text" id="konsumsi_volume" name="konsumsi_volume" class="form-control validate[required]" autocomplete = 'off' value="<?=($database_lubricant_oil?number_format($database_lubricant_oil['konsumsi_volume']):'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8 col-md-8 col-sm-top">
                                        <label>Konsumsi Unit*</label>
                                        <input type="text" id="konsumsi_unit" name="konsumsi_unit" class="form-control validate[required]" autocomplete = 'off' value="<?=($database_lubricant_oil?$database_lubricant_oil['konsumsi_unit']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8 col-md-8 col-sm-top">
                                        <label>Kompetitor*</label>
                                        <input type="text" id="kompetitor" name="kompetitor" class="form-control validate[required]" autocomplete = 'off' value="<?=($database_lubricant_oil?$database_lubricant_oil['kompetitor']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8 col-md-8 col-sm-top">
                                        <label>Harga Kompetitor*</label>
                                        <input type="text" id="harga_kompetitor" name="harga_kompetitor" class="form-control validate[required]" autocomplete = 'off' value="<?=($database_lubricant_oil?number_format($database_lubricant_oil['harga_kompetitor']):'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8 col-md-8 col-sm-top">
                                        <label>TOP*</label>
                                        <input type="text" id="top" name="top" class="form-control validate[required]" autocomplete = 'off' value="<?=($database_lubricant_oil?$database_lubricant_oil['top']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8 col-md-8 col-sm-top">
                                        <label>PIC*</label>
                                        <input type="text" id="pic" name="pic" class="form-control validate[required]" autocomplete = 'off' value="<?=($database_lubricant_oil?$database_lubricant_oil['pic']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8 col-md-8 col-sm-top">
                                        <label>Kontak Email*</label>
                                        <input type="email" id="kontak_email" name="kontak_email" class="form-control validate[required]" autocomplete = 'off' value="<?=($database_lubricant_oil?$database_lubricant_oil['kontak_email']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8 col-md-8 col-sm-top">
                                        <label>Kontak HP/Tlpn*</label>
                                        <input type="text" id="kontak_phone" name="kontak_phone" class="form-control validate[required]" autocomplete = 'off' value="<?=($database_lubricant_oil?$database_lubricant_oil['kontak_phone']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8 col-md-8 col-sm-top">
                                        <label>Keterangan*</label>
                                        <textarea name="keterangan" id="keterangan" class="form-control validate[required]"><?php echo $database_lubricant_oil?str_replace('<br />', PHP_EOL, $database_lubricant_oil['keterangan']):''; ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" id="idr" name="idr" value="<?php echo $idr;?>" />
                                            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/database-lubricant-oil.php";?>">
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
    $('#harga_kompetitor, #konsumsi_volume').number(true, 0, ".", ",")
    $('#kontak_phone').mask('000000000000')
</script>
</body>
</html>      
