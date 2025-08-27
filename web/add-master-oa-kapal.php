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

    if (isset($enk['idr']) && $enk['idr']!== ''){
        $action 	= "update"; 
		$section 	= "Edit Master OA Kapal";
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
        $sql = "select a.*, b.nama_suplier, b.lokasi_suplier from pro_master_oa_kapal a join pro_master_transportir b on a.id_transportir = b.id_master 
				where a.id_master = '".$idr."';";
        $rsm = $con->getRecord($sql);
		$trs = $rsm['nama_suplier'].', '.$rsm['lokasi_suplier'];
    } else{ 
		$action 	= "add";
		$section 	= "Tambah Master OA Kapal";
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber"))); ?>

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
                                <form action="<?php echo ACTION_CLIENT.'/master-oa-kapal.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <?php if($action == "add"){ ?>
                                <div class="table-responsive">
                                	<table class="table table-bordered table-form">
                                    	<thead>
                                        	<tr>
                                            	<th class="text-center" colspan="8">OA KAPAL</th>
                                            	<th class="text-center"><a class="btn btn-action btn-primary addRow"><i class="fa fa-plus"></i></a></th>
                                            </tr>
                                        </thead>
										<tbody>
                                        	<tr>
                                                <td width="7%" class="text-right">Transportir</td>
                                                <td width="21%">
                                                <select name="dt1[]" id="dt1_1" class="form-control validate[required] select2">
                                                    <option></option>
                        							<?php $con->fill_select("id_master","concat(nama_suplier,', ',lokasi_suplier)","pro_master_transportir","","where is_active=1 and tipe_angkutan in(2,3)","nama_suplier",false); ?>
                                                </select></td>
                                                <td width="7%" class="text-right">Nama Kapal</td>
                                                <td width="14%"><input type="text" name="dt6[]" id="dt6_1" class="form-control input-lt" /></td>
                                                <td width="8%" class="text-right">Tipe Kapal</td>
                                                <td width="14%"><input type="text" name="dt7[]" id="dt7_1" class="form-control input-lt" /></td>
                                                <td width="10%" class="text-right">Volume Max. Kapal (Liter)</td>
                                                <td width="14%"><input type="text" name="dt8[]" id="dt8_1" class="form-control input-lt hitung" /></td>
                                                <td width="5%" rowspan="2" class="bts">&nbsp;</td>
                                        	</tr>
                                        	<tr>
                                                <td class="text-right bts">Asal</td>
                                                <td class="bts"><input type="text" name="dt2[]" id="dt2_1" class="nomnya form-control input-lt validate[required]" /></td>
                                                <td class="text-right bts">Tujuan</td>
                                                <td class="bts"><input type="text" name="dt3[]" id="dt3_1" class="form-control input-lt validate[required]" /></td>
                                                <td class="text-right bts">Volume Angkut (Liter)</td>
                                                <td class="bts"><input type="text" name="dt4[]" id="dt4_1" class="form-control input-lt validate[required] hitung" /></td>
                                                <td class="text-right bts">Harga/Liter</td>
                                                <td class="bts"><input type="text" name="dt5[]" id="dt5_1" class="form-control input-lt validate[required] hitung" /></td>
                                            </tr>
                                        </tbody>
									</table>
								</div>

                                <?php } else if($action == "update"){ ?>
                                <div class="form-group row">
                                	<div class="col-sm-6">
                                    	<label>Transportir</label>
                                        <input type="hidden" name="dt1" id="dt1" value="<?php echo $rsm['id_transportir'];?>" />
                                        <input type="text" name="dt1n" id="dt1n" class="form-control" value="<?php echo $trs;?>" readonly />
                                    </div>
                                </div>
                                <div class="form-group row">
                                	<div class="col-sm-6">
                                    	<label>Nama Kapal</label>
                                        <input type="text" id="dt6" name="dt6" class="form-control" value="<?php echo $rsm['nama_kapal'];?>" />
                                    </div>
                                	<div class="col-sm-6 col-sm-top">
                                    	<label>Tipe Kapal</label>
                                        <input type="text" id="dt7" name="dt7" class="form-control" value="<?php echo $rsm['tipe_kapal'];?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                	<div class="col-sm-6">
                                    	<label>Volume Max</label>
                                        <div class="input-group">
                                        	<input type="text" id="dt8" name="dt8" class="form-control hitung" value="<?php echo $rsm['max_kapal'];?>" />
                                            <span class="input-group-addon">Liter</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                	<div class="col-sm-6">
                                    	<label>Asal *</label>
                                        <input type="text" id="dt2" name="dt2" class="form-control validate[required]" value="<?php echo $rsm['asal_angkut'];?>" />
                                    </div>
                                	<div class="col-sm-6 col-sm-top">
                                    	<label>Tujuan *</label>
                                        <input type="text" id="dt3" name="dt3" class="form-control validate[required]" value="<?php echo $rsm['tujuan_angkut'];?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                	<div class="col-sm-6">
                                    	<label>Volume Angkut *</label>
                                        <div class="input-group">
                                        	<input type="text" id="dt4" name="dt4" class="form-control validate[required] hitung" value="<?php echo $rsm['volume_angkut'];?>" />
                                            <span class="input-group-addon">Liter</span>
                                        </div>
                                    </div>
                                	<div class="col-sm-6 col-sm-top">
                                    	<label>Harga/Liter *</label>
                                        <input type="text" id="dt5" name="dt5" class="form-control validate[required] hitung" value="<?php echo $rsm['harga_angkut'];?>" />
                                    </div>
                                </div>
								<?php } ?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <a href="<?php echo BASE_URL_CLIENT."/master-oa-kapal.php"; ?>" class="btn btn-default jarak-kanan">
                                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
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

			<div class="hide" id="optSuplier"><?php $con->fill_select("id_master","concat(nama_suplier,', ',lokasi_suplier)","pro_master_transportir","","where is_active=1 and tipe_angkutan in(2,3)","nama_suplier",false); ?></div>
			
			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style type="text/css">
	.table-form td, .table-form th {
		font-size:11px; 
		font-family:arial; 
	}
	.input-lt{
		font-size:11px; 
		font-family:arial;
		padding:4px 3px;
		height:auto;		
	}
	.bts {border-bottom:5px solid #ddd !important;}
	.select2-search--dropdown .select2-search__field{
		font-family: arial;
		font-size: 11px;
		padding: 4px 3px;
	}
	.select2-results__option{
		font-family: arial;
		font-size: 11px;
	}
</style>
<script>
	$(document).ready(function(){
		$(".hitung").number(true, 0, ".", ",");
		$("form#gform").validationEngine('attach');
		
		$(".table-form").on("click", "a.addRow", function(){
			$("form#gform").validationEngine('detach');
			var tabel = $(this).parents(".table-form");
			var rwTbl	= tabel.find('tbody > tr:last');
			var rwNom	= parseInt(rwTbl.find(".nomnya").attr('id').split("_")[1]);
			var newId 	= parseInt(rwNom + 1);

			var objTr1 	= $("<tr>");
			var objTd1 	= $("<td>", {class:"text-right"}).html('Transportir').appendTo(objTr1);
			var objTd2 	= $("<td>", {class:"text-left"}).appendTo(objTr1);
			var objTd3 	= $("<td>", {class:"text-right"}).html('Nama Kapal').appendTo(objTr1);
			var objTd4 	= $("<td>", {class:"text-left"}).appendTo(objTr1);
			var objTd5 	= $("<td>", {class:"text-right"}).html('Tipe Kapal').appendTo(objTr1);
			var objTd6 	= $("<td>", {class:"text-left"}).appendTo(objTr1);
			var objTd7 	= $("<td>", {class:"text-right"}).html('Volume Max. Kapal (Liter)').appendTo(objTr1);
			var objTd8 	= $("<td>", {class:"text-left"}).appendTo(objTr1);
			var objTd9 	= $("<td>", {class:"text-center bts", rowspan:"2"}).appendTo(objTr1);
			objTd2.html('<select name="dt1[]" id="dt1_'+newId+'" class="form-control validate[required]"><option></option>'+$("#optSuplier").html()+'</select>');
			objTd4.html('<input type="text" name="dt6[]" id="dt6_'+newId+'" class="form-control input-lt" />');
			objTd6.html('<input type="text" name="dt7[]" id="dt7_'+newId+'" class="form-control input-lt" />');
			objTd8.html('<input type="text" name="dt8[]" id="dt8_'+newId+'" class="form-control input-lt text-right" />');
			objTd9.html('<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');

			var objTr2 	= $("<tr>");
			var objTd10 = $("<td>", {class:"bts text-right"}).html('Asal').appendTo(objTr2);
			var objTd11 = $("<td>", {class:"bts text-left"}).appendTo(objTr2);
			var objTd12 = $("<td>", {class:"bts text-right"}).html('Tujuan').appendTo(objTr2);
			var objTd13 = $("<td>", {class:"bts text-left"}).appendTo(objTr2);
			var objTd14 = $("<td>", {class:"bts text-right"}).html('Volume Angkut (Liter)').appendTo(objTr2);
			var objTd15 = $("<td>", {class:"bts text-left"}).appendTo(objTr2);
			var objTd16 = $("<td>", {class:"bts text-right"}).html('Harga/Liter').appendTo(objTr2);
			var objTd17 = $("<td>", {class:"bts text-left"}).appendTo(objTr2);
			objTd11.html('<input type="text" name="dt2[]" id="dt2_'+newId+'" class="form-control input-lt validate[required] nomnya" />');
			objTd13.html('<input type="text" name="dt3[]" id="dt3_'+newId+'" class="form-control input-lt validate[required]" />');
			objTd15.html('<input type="text" name="dt4[]" id="dt4_'+newId+'" class="form-control input-lt validate[required] text-right" />');
			objTd17.html('<input type="text" name="dt5[]" id="dt5_'+newId+'" class="form-control input-lt validate[required] text-right" />');
			
			rwTbl.after(objTr2).after(objTr1);
			$("#dt4_"+newId+", #dt5_"+newId+", #dt8_"+newId).number(true, 0, ".", ",");
			$("#dt1_"+newId).select2({placeholder:"Pilih Salah Satu", allowClear:true});
			$("form#gform").validationEngine('attach');
		});

		$(".table-form").on("click", "a.hRow", function(){
			var tabel 	= $(this).parents(".table-form");
			var jTbl	= tabel.find("tr").length;
			if(jTbl > 2){
				var cRow = $(this).closest('tr');
				cRow.next().remove();
				cRow.remove();
			}
		});
	});		
</script>
</body>
</html>      
