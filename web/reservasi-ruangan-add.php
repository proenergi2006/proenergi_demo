<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$idk 	= isset($enk["idk"])?htmlspecialchars($enk["idk"], ENT_QUOTES):null;
    $idr    = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$date 	= isset($enk["date"])?htmlspecialchars($enk["date"], ENT_QUOTES):'';
	$time 	= isset($enk["time"])?htmlspecialchars($enk["time"], ENT_QUOTES):'';
	$id_user = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);

    $sql = "select nama_ruangan from pro_master_ruangan where is_active=1 and id_ruangan=".$idr;
    $ruangan = $con->getRecord($sql);

    $titleAct   = "Tambah Reservasi Ruangan";
    $action     = "add";

    $reservasi = null;
    if ($idk) {
        $titleAct   = "Edit Reservasi Ruangan";
        $action     = "update";
        $sql = "select * from pro_reservasi_ruangan where deleted_time is null and id_reservasi=".$idk;
        $reservasi = $con->getRecord($sql);
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

                                <form action="<?php echo ACTION_CLIENT.'/reservasi-ruangan.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-6">
										<label>Ruangan *</label>
                                        <input type="text" id="ruangan" name="ruangan" class="form-control validate[required]" autocomplete = 'off' value="<?=$ruangan['nama_ruangan']?>" readonly style="pointer-events: none;" />
                                    </div>
								</div>
                                <div class="form-group row">
                                    <div class="col-sm-4 col-md-3 col-sm-top">
                                        <label>Tanggal Reservasi*</label>
                                        <input type="text" id="tanggal_reservasi" name="tanggal_reservasi" class="form-control datepicker validate[required,custom[date]]" autocomplete = 'off' value="<?=$date?>" readonly style="pointer-events: none;" />
                                    </div>
                                    <div class="col-sm-4 col-md-3 col-sm-top">
                                        <label>Jam Reservasi*</label>
                                        <div class="form-group row">
                                            <div class="col-lg-5 col-md-5 col-sm-5">
                                                <input type="text" id="jam_reservasi" name="jam_reservasi" class="form-control validate[required]" autocomplete = 'off' value="<?=substr($time, 0, 5)?>" readonly style="pointer-events: none;" />
                                            </div>
                                            <div class="col-lg-1 col-md-1 col-sm-1">-</div>
                                            <div class="col-lg-5 col-md-5 col-sm-5">
                                                <select name="jam_reservasi_2" class="form-control validate[required]" style="width: 100%; <?=$reservasi?'pointer-events: none;':''?>">
                                                    <?php 
                                                        $x = ((int)substr($time, 6, 2));
                                                        if ($x==0) {
                                                            echo '<option value="00:00">00:00</option>';
                                                        } else {
                                                        for ($i=$x; $i <= 24; $i++) { 
                                                            $ti = date('H:i', strtotime('+'.$i.' hour', strtotime('00:00')));
                                                    ?>
                                                        <option value="<?=$ti?>"><?=$ti?></option>
                                                    <?php 
                                                            }
                                                        } 
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr style="border-color:#ddd" />
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Keperluan</label>
                                        <textarea name="keperluan" id="keperluan" class="form-control validate[required]"><?php echo $reservasi?str_replace('<br />', PHP_EOL, $reservasi['keperluan']):''; ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Personel</label>
                                        <textarea name="personel" id="personel" class="form-control validate[required]"><?php echo $reservasi?str_replace('<br />', PHP_EOL, $reservasi['personel']):''; ?></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <input type="hidden" id="idk" name="idk" value="<?php echo $idk;?>" />
                                            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/reservasi-ruangan.php";?>">
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
</script>
</body>
</html>      
