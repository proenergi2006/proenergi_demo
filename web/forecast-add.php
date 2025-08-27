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

    if (isset($enk['idr']) && $enk['idr'] !== ''){
        $action 	= "update"; 
		$section 	= "Edit Request Forecast";
        $idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
        $sql = "select * from forecast where id = " . $idr;
		$rsm = $con->getRecord($sql);
		$dt1 = date("d/m/Y", strtotime($rsm['tanggal']));
		// $dt7 = ($rsm['in_inven'])?$rsm['in_inven']:'';
		// $dt8 = ($rsm['harga_tebus'])?$rsm['harga_tebus']:'';
    } else { 
		$action 	= "add";
		$section 	= "Tambah Request Forecast";
        $rsm 		= array();
		$dt1 		= "";
		$dt7 		= "";
		$dt8 		= "";
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber","jqueryUI","ckeditor"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1><?php echo $section; ?></h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                        	<div class="box-header with-border bg-light-blue">
                            	<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
							</div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/forecast.php'; ?>" id="gform" name="gform" method="post" role="form">
									<div class="form-group row">
										<div class="col-sm-3">
											<label>Tanggal *</label>
											<?php if($action == "add"){ ?>
											<input type="text" name="dt1" id="dt1" class="form-control validate[required] datepicker" />
											<?php } else if($action == "update"){ ?>
											<input type="hidden" name="dt1" id="dt1" value="<?php echo $dt1;?>" />
											<div class="form-control"><?php echo $dt1;?></div>
											<?php } ?>
										</div>
										<?php if (in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']), ['5', '9'])): ?>
										<div class="col-sm-offset-1 col-sm-6 col-sm-top">
											<label>Nomor PO</label>
											<input type="text" name="dt2" id="dt2" class="form-control" value="<?php echo isset($rsm['no_po']) ? $rsm['no_po'] : '';?>" />
										</div>
										<?php endif ?>
									</div>
									<div class="form-group row">
										<div class="col-sm-4">
											<label>Keterangan *</label>
											<input type="text" class="form-control validate[required]" name="dt3" id="dt3" value="<?php echo isset($rsm['keterangan']) ? $rsm['keterangan'] : '' ?>">
										</div>
										<div class="col-sm-6">
											<label>Quantity *</label>
											<input type="text" class="form-control hitung validate[required]" name="dt4" id="dt4" value="<?php echo isset($rsm['quantity']) ? $rsm['quantity'] : '' ?>">
										</div>
									</div>
									<div class="form-group row">
										<?php if (in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']), ['5', '9'])): ?>
										<div class="col-sm-4">
											<label>SO No *</label>
											<input type="text" class="form-control validate[required]" name="dt5" id="dt5" value="<?php echo isset($rsm['so_no']) ? $rsm['so_no'] : '' ?>">
										</div>
										<?php endif ?>
										<div class="col-sm-6">
											<label>SO Depo *</label>
											<input type="text" class="form-control validate[required]" name="dt6" id="dt6" value="<?php echo isset($rsm['so_depo']) ? $rsm['so_depo'] : '' ?>">
										</div>
									</div>
									<div class="form-group row">
										<?php if(in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']), array('5', '9'))): ?>
										<div class="col-sm-4">
											<label>Quantity Terima</label>
											<input type="text" class="form-control hitung" name="dt7" id="dt7" value="<?php echo isset($rsm['quantity_terima']) ? $rsm['quantity_terima'] : '' ?>">
										</div>
										<?php endif ?>
										<div class="col-sm-6">
											<label>Quantity Keluar *</label>
											<input type="text" class="form-control hitung validate[required]" name="dt8" id="dt8" value="<?php echo isset($rsm['quantity_keluar']) ? $rsm['quantity_keluar'] : '' ?>">
										</div>
									</div>
									<?php if(in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']), array('9'))): ?>
									<div class="form-group row">
										<div class="col-sm-12">
											<label>Keterangan</label>
											<textarea class="form-control" name="dt9" id="dt9"><?php echo isset($rsm['note']) ? $rsm['note'] : '' ?></textarea>
										</div>
									</div>
									<?php endif ?>

									<div class="row">
										<div class="col-sm-12">
											<div class="pad bg-gray">
												<input type="hidden" name="act" value="<?php echo $action; ?>" />
												<input type="hidden" name="idr" value="<?php echo isset($idr) ? $idr : ''; ?>" />
												<a href="<?php echo BASE_URL_CLIENT."/forecast.php"; ?>" class="btn btn-default jarak-kanan">
												<i class="fa fa-reply jarak-kanan"></i> Kembali</a>
												<button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
												<?php if(in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']), array('5'))): ?>
												<a class="btn btn-success" title="Approve" href="<?php echo ACTION_CLIENT.'/forecast-approve.php?idr='.paramEncrypt('idr='.$idr) ?>"><i class="fa fa-check"></i> Approve</a>
												<?php endif ?>
											</div>
										</div>
									</div>
									<hr style="margin:5px 0" />
									<div class="clearfix">
										<div class="col-sm-12"><small>* Wajib Diisi</small></div>
									</div>
                                </form>
                            </div>

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
			$(".hitung").number(true, 0, ".", ",");
			$("form#gform").validationEngine('attach');
			$("select#dt6").select2({
				placeholder	: "Pilih salah satu",
				allowClear	: true,
				templateResult : function(repo){ 
					if(repo.loading) return repo.text;
					var text1 = repo.text.split("#");
					var $returnString = $('<span>'+text1[0]+(text1[1]?' - '+text1[1]:'')+(text1[2]?', '+text1[2]:'')+'</span>');
					return $returnString;
				},
				templateSelection : function(repo){ 
					var text1 = repo.text.split("#");
					var $returnString = $('<span>'+text1[0]+(text1[1]?' - '+text1[1]:'')+(text1[2]?', '+text1[2]:'')+'</span>');
					return $returnString;
				},
			});
		});		
	</script>
</body>
</html>      
