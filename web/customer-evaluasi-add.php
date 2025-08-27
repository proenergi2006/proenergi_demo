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
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$idk 	= isset($enk["idk"])?htmlspecialchars($enk["idk"], ENT_QUOTES):null;
	$sqlCek = "select a.nama_customer, a.alamat_customer, a.telp_customer, a.fax_customer, b.nama_kab, c.nama_prov from pro_customer a 
			   join pro_master_kabupaten b on a.kab_customer = b.id_kab join pro_master_provinsi c on a.prov_customer = c.id_prov where a.id_customer = '".$idr."'";
	$resCek = $con->getRecord($sqlCek);
	$tmp1 	= strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $resCek['nama_kab']));
	$alamat = $resCek['alamat_customer']." ".ucwords($tmp1)." ".$resCek['nama_prov'];
	$arrRes = array(1=>"Supply Delivery","Supply Delivery With Note","Revised and Resubmitted");

	if($idk){
		$action = "update";
		$sql = "select * from pro_customer_evaluasi where id_customer = '".$idr."' and id_evaluasi = '".$idk."'";
		$rsm = $con->getRecord($sql);
		$dt1 = str_replace('<br />', PHP_EOL, $rsm['marketing_evaluasi1']);
		$dt2 = str_replace('<br />', PHP_EOL, $rsm['marketing_evaluasi2']);
		$dt3 = str_replace('<br />', PHP_EOL, $rsm['marketing_evaluasi3']);
		$tmp = json_decode($rsm['marketing_summary'], true);
		$summary = str_replace('<br />', PHP_EOL, $tmp['summary']);
	} else{
		$action = "add";
		$rsm = array();
		$dt1 = "";
		$dt2 = "";
		$dt3 = "";
		$dt4 = "";
		$summary = "";
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>
<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Evaluasi Data Customer</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <p style="margin-bottom:0px;"><b><?php echo $resCek['nama_customer'];?></b></p>
                                <p style="margin-bottom:5px;"><?php echo $alamat;?></p>
                                <p style="margin-bottom:0px;"><?php echo "&bull; Telp : ".$resCek['telp_customer'];?></p>
                                <p style="margin-bottom:0px;"><?php echo "&bull; Fax&nbsp;&nbsp; : ".$resCek['fax_customer'];?></p>
                            </div>
                            <div class="box-body">
                                <?php if(!$rsm['marketing_result']){ ?>
                                <form action="<?php echo ACTION_CLIENT.'/customer-evaluasi.php'; ?>" id="gform" name="gform" method="post">
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                        	<label>Bulan ke-1</label>
                                            <textarea name="dt1" id="dt1" class="form-control validate[required]"><?php echo $dt1;?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                        	<label>Bulan ke-2</label>
                                            <textarea name="dt2" id="dt2" class="form-control validate[required]"><?php echo $dt2;?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                        	<label>Bulan ke-3</label>
                                            <textarea name="dt3" id="dt3" class="form-control validate[required]"><?php echo $dt3;?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                        	<label>Summary</label>
                                            <textarea name="summary" id="summary" class="form-control validate[required]"><?php echo $summary;?></textarea>
                                        </div>
                                        <div class="col-sm-6 col-sm-top">
                                        	<label>Result</label>
                                            <div class="radio clearfix" style="margin:0px;">
                                                <label class="col-xs-12" style="margin-bottom:5px;">
                                                    <input type="radio" name="result" id="result1" class="validate[required]" value="1" /> Supply Delivery
                                                </label>
                                                <label class="col-xs-12" style="margin-bottom:5px;">
                                                    <input type="radio" name="result" id="result2" class="validate[required]" value="2" /> Supply Delivery With Note
                                                </label>
                                                <label class="col-xs-12" style="margin-bottom:5px;">
                                                    <input type="radio" name="result" id="result3" class="validate[required]" value="3" /> Revised and Resubmitted
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" name="act" value="<?php echo $action;?>" />
                                                <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                                <input type="hidden" name="idk" value="<?php echo $idk;?>" />
                                                <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/customer-evaluasi-list.php";?>">
                                                <i class="fa fa-reply jarak-kanan"></i>Kembali</a>
                                                <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <?php } else{ ?>
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                        	<label>Bulan ke-1</label>
                                            <div class="form-control" style="height:auto"><?php echo $dt1;?></div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                        	<label>Bulan ke-2</label>
                                            <div class="form-control" style="height:auto"><?php echo $dt2;?></div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                        	<label>Bulan ke-3</label>
                                            <div class="form-control" style="height:auto"><?php echo $dt3;?></div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                        	<label>Summary</label>
                                            <div class="form-control" style="height:auto">
                                                <?php echo $summary; ?>
                                                <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $tmp['pic']." - ".$tmp['tanggal']." WIB";?></i></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-sm-top">
                                        	<label>Result</label>
                                            <div class="radio clearfix" style="margin:0px;">
                                                <label class="col-xs-12">
                                                	<input type="radio" name="result" id="result" value="1" checked /><?php echo $arrRes[$rsm['marketing_result']];?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <a href="<?php echo BASE_URL_CLIENT."/customer-evaluasi.php"; ?>" class="btn btn-default jarak-kanan">
                                                <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>
            <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
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
