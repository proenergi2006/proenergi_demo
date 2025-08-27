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
	$link1 	= BASE_URL_CLIENT.'/master-harga-pertamina.php';

    if (isset($enk['idr']) and $enk['idr'] !== ''){
        $action 	= "update"; 
		$section 	= "Edit Harga Dasar Pertamina";
		$idr = htmlspecialchars($enk['idr'], ENT_QUOTES);
		list($id1, $id2, $id3, $id4) = explode("#*#", $idr);
		$cek = "select a.*, b.nama_area, c.jenis_produk, c.merk_dagang from pro_master_harga_pertamina a join pro_master_area b on a.id_area = b.id_master 
				join pro_master_produk c on a.id_produk = c.id_master where a.periode_awal = '".$id1."' and a.periode_akhir = '".$id2."' 
				and a.id_area = '".$id3."' and a.id_produk = '".$id4."'";
		$row = $con->getRecord($cek);
    } else{ 
		$action 	= "add";
		$section 	= "Tambah Harga Dasar Pertamina";
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber","jqueryUI"), "css"=>array("jqueryUI"))); ?>

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
                                <form action="<?php echo ACTION_CLIENT.'/master-harga-pertamina.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-4 col-md-3">
                                        <label>Periode Awal *</label>
                                        <?php if($action == "add"){ ?>
                                        <input type="text" name="periode_awal" id="periode_awal" class="form-control validate[required,custom[date]] datepicker" autocomplete = 'off'/>
                                        <?php } else{ ?>
                                        <input type="text" name="periode_awal" class="form-control" readonly value="<?php echo date("d/m/Y", strtotime($id1)); ?>" />
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-4 col-md-3 col-sm-top">
                                        <label>Periode Akhir *</label>
                                        <?php if($action == "add"){ ?>
                                        <input type="text" name="periode_akhir" id="periode_akhir" class="form-control validate[required,custom[date]] datepicker" autocomplete = 'off'/>
                                        <?php } else{ ?>
                                        <input type="text" name="periode_akhir" class="form-control" readonly value="<?php echo date("d/m/Y", strtotime($id2)); ?>" />
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php if($action == "add"){ ?>
                                <div class="row">
                                    <div class="col-sm-12">
										<div class="table-responsive">
                                        	<table class="table table-lampiran">
                                            	<thead>
                                                	<tr>
                                                    	<th class="text-center" width="30%">Area</th>
                                                    	<th class="text-center" width="30%">Produk</th>
                                                    	<th class="text-center" width="30%">Harga</th>
                                                    	<th class="text-center" width="10%">
                                                        <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button></th>
                                                    </tr>
                                                </thead>
                                            	<tbody>
                                                	<tr>
                                                    	<td>
															<select name="area[]" id="area1" class="form-control validate[required] select2">
                                    							<option></option>
                                    							<?php $con->fill_select("id_master","nama_area","pro_master_area","","where is_active=1","",false); ?>
                                        					</select>
                                                        </td>
                                                    	<td>
                                                            <select name="produk[]" id="produk1" class="form-control validate[required] select2">
                                                                <option></option>
                                                                <?php $con->fill_select("id_master","concat(jenis_produk,' - ',merk_dagang)","pro_master_produk","","where is_active=1","1",false);?>
                                                            </select>
                                                        </td>
                                                    	<td><input type="text" name="harga[]" id="harga1" class="form-control validate[required] hitung" /></td>
                                                    	<td class="text-center"><span class="frmid" data-row-count="1"></span></td>
                                                    </tr>
                                                </tbody>
											</table>
                                        </div>
									</div>
                                </div>
                                <?php } else{ ?>
                                <div class="form-group row">
                                    <div class="col-sm-4 col-md-3">
                                        <label>Area *</label>
                                        <input type="hidden" name="area" id="area" value="<?php echo $id3; ?>" />
                                        <div class="form-control"><?php echo $row['nama_area']; ?></div>
                                    </div>
                                    <div class="col-sm-4 col-md-3 col-sm-top">
                                        <label>Produk *</label>
                                        <input type="hidden" name="produk" id="produk" value="<?php echo $id4; ?>" />
                                        <div class="form-control"><?php echo $row['jenis_produk'].' - '.$row['merk_dagang']; ?></div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                	<div class="col-sm-4 col-md-3">
                                    	<label>Harga *</label>
                                        <input type="text" name="harga" id="harga" class="form-control hitung" value="<?php echo $row['harga_minyak'];?>" />
									</div>
								</div>
                                <?php } ?>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <a href="<?php echo $link1; ?>" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
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

            <div class="hide" id="optArea"><?php $con->fill_select("id_master","nama_area","pro_master_area","","where is_active=1","",false); ?></div>
            <div class="hide" id="optProduk">
				<?php $con->fill_select("id_master","concat(jenis_produk,' - ',merk_dagang)","pro_master_produk","","where is_active=1","1",false);?>
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

		$(".table-lampiran").on("click", "button.addRow", function(){
			$("form#gform").validationEngine('detach');
			var tabel 	= $(this).parents(".table-lampiran");
			var rwTbl	= tabel.find('tbody > tr:last');
			var rwNom	= parseInt(rwTbl.find("span.frmid").data('rowCount'));
			var newId 	= parseInt(rwNom + 1);
			
			var objTr 	= $("<tr>");
			var objTd1 	= $("<td>", {class:"text-left"}).appendTo(objTr);
			var objTd2 	= $("<td>", {class:"text-left"}).appendTo(objTr);
			var objTd3 	= $("<td>", {class:"text-left"}).appendTo(objTr);
			var objTd4 	= $("<td>", {class:"text-center"}).appendTo(objTr);
			objTd1.html('<select name="area[]" id="area'+newId+'" class="form-control validate[required]"><option></option>'+$("#optArea").html()+'</select>');
			objTd2.html('<select name="produk[]" id="produk'+newId+'" class="form-control validate[required]"><option></option>'+$("#optProduk").html()+'</select>');
			objTd3.html('<input type="text" name="harga[]" id="harga'+newId+'" class="form-control validate[required] text-right" />');
			objTd4.html('<span class="frmid" data-row-count="'+newId+'"></span><a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
			rwTbl.after(objTr);
			$("#harga"+newId).number(true, 0, ".", ",");
			$("#area"+newId).select2({allowClear:true, placeholder:"Pilih salah satu"});
			$("#produk"+newId).select2({allowClear:true, placeholder:"Pilih salah satu"});
			$("form#gform").validationEngine('attach');
		});
		$(".table-lampiran").on("click", "a.hRow", function(){
			var tabel 	= $(this).parents(".table-lampiran");
			var jTbl	= tabel.find("tr").length;
			if(jTbl > 2){
				var cRow = $(this).closest('tr');
				cRow.remove();
			}
		});
	});
</script>
</body>
</html>      
