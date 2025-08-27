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
	$link 	= BASE_URL_CLIENT."/terminal-inventory.php?".paramEncrypt("prd=".$enk['prd']);

    if (isset($enk['idr']) && $enk['idr']!== ''){
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
        $sql = "select a.*, b.jenis_produk, b.merk_dagang from pro_master_inventory a join pro_master_produk b on a.id_produk = b.id_master where a.id_master = '".$idr."'";
        $rsm = $con->getRecord($sql);
        $action 	= "update"; 
		$section 	= "Edit Inventory ".$rsm['jenis_produk']." - ".$rsm['merk_dagang'];
		$class1 = "";
		$tglinv = 'value="'.date("d/m/Y", strtotime($rsm['tanggal_inv'])).'" readonly'; 
		$dt2 	= ($rsm['awal_jam'])?date("H:i", strtotime($rsm['awal_jam'])):'';
		$dt3 	= $rsm['awal_shrink'];
		$dt4 	= $rsm['awal_temp'];
		$dt5 	= $rsm['awal_density1'];
		$dt6 	= $rsm['awal_density2'];
		$dt7 	= $rsm['awal_vcf'];
		$level1 = number_format($rsm['awal_level1']);
		$level2 = number_format($rsm['awal_level2']);
		$level3 = number_format(($rsm['awal_level1'] + $rsm['awal_level2']));
		$voltbl = number_format($rsm['awal_volume_tabel']);
		$awlnet	= number_format($rsm['awal_nett']);
		$ship	= number_format($rsm['masuk_ship']);
		$truck	= number_format($rsm['masuk_truck']);
		$insl	= number_format($rsm['masuk_slop']);
		$pipe	= number_format($rsm['tank_pipe']);
		$outsl	= number_format($rsm['keluar_slop']);
    } else{ 
        $sql = "select jenis_produk, merk_dagang from pro_master_produk where id_master = '".$enk['prd']."'";
        $rsm = $con->getRecord($sql);
		$action  = "add";
		$section = "Tambah Inventory ".$rsm['jenis_produk']." - ".$rsm['merk_dagang'];
		$class1 = "datepicker";
		$tglinv = 'value=""'; 
		$dt2 = ""; $dt3 = ""; $dt4 = ""; $dt5 = ""; $dt6 = ""; $dt7 = "";
		$level1 = ""; $level2 = ""; $voltbl = ""; $awlnet = ""; $ship = ""; $truck = ""; $insl = ""; $pipe = ""; $outsl = "";
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
        		<h1><?php echo $section; ?></h1>
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
                                <form action="<?php echo ACTION_CLIENT.'/terminal-inventory.php'; ?>" id="gform" name="gform" method="post" class="form-validasi" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>Tanggal *</label>
                                        <input type="text" id="tgl" name="tgl" autocomplete = 'off' class="form-control validate[required,custom[date]] <?php echo $class1;?>" <?php echo $tglinv;?> />
                                    </div>
                                    <?php if(paramDecrypt($_SESSION["sinori".SESSIONID]["id_role"]) != 13){ ?>
                                    <div class="col-sm-offset-3 col-sm-6 col-sm-top">
                                        <label>Terminal *</label>
                                        <select id="terminal" name="terminal" class="form-control validate[required] select2">
                                        	<option></option>
                                            <?php $con->fill_select("id_master", "concat(nama_terminal,' ',tanki_terminal,', ',lokasi_terminal)", "pro_master_terminal", $rsm['id_terminal'], "where is_active=1", "id_master", false); ?>
                                        </select>
                                    </div>
                                    <?php } ?>
                                </div>
                                <p class="form-title">Tank Sounding (Pengukuran Tangki)</p>
                                <div class="table responsive">
                                	<table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="8%" rowspan="2">Time</th>
                                                <th class="text-center" colspan="3">Level (mm)</th>
                                                <th class="text-center" width="10%" rowspan="2">Volume Tabel (Liter)</th>
                                                <th class="text-center" width="10%" rowspan="2">Shrinkage Correction</th>
                                                <th class="text-center" width="10%" rowspan="2">Nett Observed (Liter)</th>
                                                <th class="text-center" width="8%" rowspan="2">Temp (<sup>o</sup>C)</th>
                                                <th class="text-center" width="8%" rowspan="2">Density<br><i>(kg/l)</i></th>
                                                <th class="text-center" width="8%" rowspan="2">Density<br><i>(@15<sup>o</sup>C)</i></th>
                                                <th class="text-center" width="8%" rowspan="2">VCF</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" width="8%">Sounding</th>
                                                <th class="text-center" width="8%">Datum Plate</th>
                                                <th class="text-center" width="8%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<tr>
                                            <td><input type="text" id="jam" name="jam" class="form-control validate[required] timepicker inven" value="<?php echo $dt2;?>" autocomplete="off" /></td>
                                            <td>
                                            	<input type="text" id="awlm1" name="awlm1" class="form-control validate[required] hitung inven" value="<?php echo $level1;?>" autocomplete="off" />
                                            </td>
                                            <td>
                                            	<input type="text" id="awlm2" name="awlm2" class="form-control validate[required] hitung inven" value="<?php echo $level2;?>" autocomplete="off" />
                                            </td>
                                            <td><input type="text" id="awlm3" name="awlm3" class="form-control hitung inven" readonly value="<?php echo $level3;?>" autocomplete="off" /></td>
                                            <td><input type="text" id="voltbl" name="voltbl" class="form-control hitung inven" value="<?php echo $voltbl;?>" autocomplete="off" /></td>
                                            <td><input type="text" id="shrink" name="shrink" class="form-control validate[required] inven" value="<?php echo $dt3;?>" autocomplete="off" /></td>
                                            <td><input type="text" id="awlnet" name="awlnet" class="form-control hitung inven" value="<?php echo $awlnet;?>" autocomplete="off" /></td>
                                            <td><input type="text" id="suhu" name="suhu" class="form-control validate[required] inven" value="<?php echo $dt4;?>" autocomplete="off" /></td>
                                            <td><input type="text" id="density1" name="density1" class="form-control validate[required] inven" value="<?php echo $dt5;?>" autocomplete="off" /></td>
                                            <td><input type="text" id="density2" name="density2" class="form-control validate[required] inven" value="<?php echo $dt6;?>" autocomplete="off" /></td>
                                            <td><input type="text" id="vcf" name="vcf" class="form-control validate[required] inven" value="<?php echo $dt7;?>" autocomplete="off" /></td>
                                        </tr>
                                    </table>
                                </div>

                                <p class="form-title">Stock Adjustment</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="20%" rowspan="2" class="text-center">BOOK STOCK</th>
                                                <th colspan="3" class="text-center">IN<i></i></th>
                                                <th width="20%" rowspan="2" class="text-center">OUT (Slop)</th>
                                                <th width="15%" rowspan="2" class="text-center">TANK PIPE</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" width="15%">Ship</th>
                                                <th class="text-center" width="15%">Truck</th>
                                                <th class="text-center" width="15%">Slop</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<tr>
                                            <td><input type="text" id="book_stok" name="book_stok" class="form-control hitung inven" value="<?php echo $book_stok;?>" /></td>
                                            <td><input type="text" id="ship" name="ship" class="form-control hitung inven" value="<?php echo $ship;?>" /></td>
                                            <td><input type="text" id="truck" name="truck" class="form-control hitung inven" value="<?php echo $truck;?>" /></td>
                                            <td><input type="text" id="in_slop" name="in_slop" class="form-control hitung inven" value="<?php echo $insl;?>" /></td>
                                            <td><input type="text" id="out_slop" name="out_slop" class="form-control hitung inven" value="<?php echo $outsl;?>" /></td>
                                            <td><input type="text" id="tank_pipe" name="tank_pipe" class="form-control hitung inven" value="<?php echo $pipe;?>" /></td>
                                        </tr>
                                        </tbody>
									</table>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <input type="hidden" name="prd" value="<?php echo $enk['prd'];?>" />
                                            <a href="<?php echo $link; ?>" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                    	</div>
                                    </div>
                                </div>
                                <hr style="margin:5px 0" />
                                <div class="row">
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

<style type="text/css">
	.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
		 text-decoration:underline;
	}
	.table > thead > tr > th, 
	.table > tbody > tr > td{
		padding: 5px;
		font-size:10px;
		font-family:arial;
	}
	.inven{
		padding:5px;
		font-size:10px;
		font-family:arial;
		height:auto;
	}
</style>
<script>
	$(document).ready(function(){
		$(".hitung").number(true, 0, ".", ",");
		$("#awlm1, #awlm2").on("keyup", function(){
			var nil1 = parseInt($("#awlm1").val().replace(/[,]+/g, "")*1);
			var nil2 = parseInt($("#awlm2").val().replace(/[,]+/g, "")*1);
			var nil3 = nil1 + nil2;
			$("#awlm3").val(nil3);
		});
		$("#voltbl, #shrink").on("keyup", function(){
			var nil1 = parseInt($("#voltbl").val().replace(/[,]+/g, "")*1);
			var nil2 = parseFloat($("#shrink").val()*1);
			var nil3 = nil1 * nil2;
			$("#awlnet").val(nil3);
		});
	});
</script>
</body>
</html>      
