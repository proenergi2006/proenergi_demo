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
    $id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);

    $titleAct   = "Edit Generate Number";
    $action     = "update";

    $row = null;
    if ($idr) {
        $sql = "select * from pro_master_cabang where is_active = 1 and id_master=".$idr;
        $row = $con->getRecord($sql);
        $row['urut_spj'] = (int)$row['urut_spj']+1;
        $row['urut_dn'] = (int)$row['urut_dn']+1;
        $row['urut_po'] = (int)$row['urut_po']+1;
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

                                <form action="<?php echo ACTION_CLIENT.'/generate-number.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Nama Cabang</label>
                                        <input type="text" id="nama_cabang" name="nama_cabang" class="form-control validate[required]" autocomplete = 'off' value="<?=($row?$row['nama_cabang']:'')?>" readonly />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Urut SPJ</label>
                                        <input type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==6) return false;" id="urut_spj" name="urut_spj" class="form-control validate[required]" autocomplete = 'off' value="<?=($row?$row['urut_spj']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Urut DN</label>
                                        <input type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==6) return false;" id="urut_dn" name="urut_dn" class="form-control validate[required]" autocomplete = 'off' maxlength="6" value="<?=($row?$row['urut_dn']:'')?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Urut PO</label>
                                        <input type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==6) return false;" id="urut_po" name="urut_po" class="form-control validate[required]" autocomplete = 'off' maxlength="6" value="<?=($row?$row['urut_po']:'')?>" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" id="idr" name="idr" value="<?php echo $idr;?>" />
                                            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/generate-number.php";?>">
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
