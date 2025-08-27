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
	$link1 	= BASE_URL_CLIENT.'/master-harga-tebus.php';

    if ($idr['idr'] !== '' && isset($enk['idr'])){
        $action 	= "update"; 
		$section 	= "Edit Harga Tebus";
		$idr = htmlspecialchars($enk['idr'], ENT_QUOTES);
		$cek = "select a.*, b.nama_cabang, c.jenis_produk, c.merk_dagang, d.nama_vendor from pro_master_harga_tebus a join pro_master_cabang b on a.id_cabang = b.id_master 
				join pro_master_produk c on a.id_produk = c.id_master join pro_master_vendor d on a.id_vendor = d.id_master where a.id_master = '".$idr."'";
		$row = $con->getRecord($cek);
    } else{ 
		$action 	= "add";
		$section 	= "Tambah Harga Tebus";
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
                                <form action="<?php echo ACTION_CLIENT.'/master-harga-tebus.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-5 col-md-4">
                                        <label>Periode Awal *</label>
                                        <?php if($action == "add"){ ?>
                                        <input type="text" name="periode_awal" id="periode_awal" class="form-control validate[required,custom[date]] datepicker" autocomplete = 'off'/>
                                        <?php } else{ ?>
                                        <input type="text" name="periode_awal" id="periode_awal" class="form-control" value="<?php echo date("d/m/Y", strtotime($row['periode_awal'])); ?>" readonly />
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-5 col-md-4 col-sm-top">
                                        <label>Periode Akhir *</label>
                                        <?php if($action == "add"){ ?>
                                        <input type="text" name="periode_akhir" id="periode_akhir" class="form-control validate[required,custom[date]] datepicker" autocomplete = 'off'/>
                                        <?php } else{ ?>
                                        <input type="text" name="periode_akhir" id="periode_akhir" class="form-control" value="<?php echo date("d/m/Y", strtotime($row['periode_akhir'])); ?>" readonly />
                                        <?php } ?>
                                    </div>
                                </div>

								<?php if($action == "add"){ ?>
                                <div class="row"><div class="col-sm-12"><div class="table-responsive">
                                    <table class="table table-bordered" id="table-tebus">
                                    	<thead>
                                        	<tr>
                                            	<th class="text-center" width="8%">No</th>
                                            	<th class="text-center" width="21%">Cabang</th>
                                            	<th class="text-center" width="21%">Vendor</th>
                                            	<th class="text-center" width="21%">Produk</th>
                                            	<th class="text-center" width="21%">Harga</th>
                                            	<th class="text-center" width="8%"><a class="btn btn-action btn-primary addRow"><i class="fa fa-plus"></i></a></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        	<tr>
                                            	<td class="text-center"><span class="noTebus" data-row-count="1">1</span></td>
                                            	<td><select id="cabang1" name="cabang[]" class="form-control validate[required] select2"><option></option>
                                                <?php $con->fill_select("id_master","nama_cabang","pro_master_cabang","","where is_active=1 and id_master not in(1,4)","",false); ?>
                                                </select></td>
                                            	<td><select id="vendor1" name="vendor[]" class="form-control validate[required] select2"><option></option>
                                                <?php $con->fill_select("id_master","nama_vendor","pro_master_vendor",'',"where is_active=1","id_master",false); ?>
                                                </select></td>
                                            	<td><select id="produk1" name="produk[]" class="form-control validate[required] select2"><option></option>
                                                <?php $con->fill_select("id_master","concat(jenis_produk,' ',merk_dagang)","pro_master_produk","","where is_active=1","1",false);?>
                                                </select></td>
                                            	<td><input type="text" id="harga1" name="harga[]" class="form-control validate[required] hitung" /></td>
                                            	<td class="text-center">&nbsp;</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div></div></div>
                                <?php } else if($action == "update"){ ?>
                                <div class="form-group row">
                                    <div class="col-sm-5 col-md-4">
                                        <label>Cabang *</label>
                                        <input type="hidden" name="cabang" id="cabang" value="<?php echo $row['id_cabang']; ?>" />
                                        <input type="text" name="cabangNama" id="cabangNama" class="form-control" value="<?php echo $row['nama_cabang']; ?>" readonly />
                                    </div>
                                    <div class="col-sm-5 col-md-4 col-sm-top">
                                        <label>Produk *</label>
                                        <input type="hidden" name="produk" id="produk" value="<?php echo $row['id_produk']; ?>" />
                                        <input type="text" name="prodnm" id="prodnm" class="form-control" value="<?php echo $row['jenis_produk'].' - '.$row['merk_dagang']; ?>" readonly />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-5 col-md-4">
                                        <label>Vendor *</label>
                                        <input type="hidden" name="vendor" id="vendor" value="<?php echo $row['id_vendor']; ?>" />
                                        <input type="text" name="vendorNama" id="vendorNama" class="form-control" value="<?php echo $row['nama_vendor']; ?>" readonly />
                                    </div>
                                    <div class="col-sm-5 col-md-4 col-sm-top">
                                        <label>Harga Tebus *</label>
                                        <input type="text" id="harga" name="harga" class="form-control validate[required] hitung" value="<?php echo $row['harga_tebus'];?>" />
                                    </div>
                                </div>
                                <?php } ?>

                                
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
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

            <div class="hide" id="optCabang">
				<?php $con->fill_select("id_master","nama_cabang","pro_master_cabang","","where is_active=1 and id_master not in(1,4)","",false); ?>
			</div>
            <div class="hide" id="optProduk">
				<?php $con->fill_select("id_master","concat(jenis_produk,' ',merk_dagang)","pro_master_produk","","where is_active=1","1",false);?>
            </div>
            <div class="hide" id="optVendor"><?php $con->fill_select("id_master","nama_vendor","pro_master_vendor",'',"where is_active=1","id_master",false); ?></div>

			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>
<script>
	$(document).ready(function(){
		$("form#gform").validationEngine('attach');
		$(".hitung").number(true, 0, ".", ",");
		$("#table-tebus").on("click", "a.addRow", function(){
			$("form#gform").validationEngine('detach');
			var tabel = $("#table-tebus");
			var rwTbl = tabel.find('tbody > tr:last');
			var rwNom = parseInt(rwTbl.find("span.noTebus").data('rowCount'));
			var newId = parseInt(rwNom + 1);

			var objTr 	= $("<tr>");
			var objTd1 	= $("<td>", {class:"text-center"}).appendTo(objTr);
			var objTd2 	= $("<td>", {class:"text-left"}).appendTo(objTr);
			var objTd3 	= $("<td>", {class:"text-left"}).appendTo(objTr);
			var objTd4 	= $("<td>", {class:"text-left"}).appendTo(objTr);
			var objTd5 	= $("<td>", {class:"text-left"}).appendTo(objTr);
			var objTd6 	= $("<td>", {class:"text-center"}).appendTo(objTr);
			objTd1.html('<span class="noTebus" data-row-count="'+newId+'"></span>');
			objTd2.html('<select id="cabang'+newId+'" name="cabang[]" class="form-control validate[required]"><option></option>'+$("#optCabang").html()+'</select>');
			objTd3.html('<select id="vendor'+newId+'" name="vendor[]" class="form-control validate[required]"><option></option>'+$("#optVendor").html()+'</select>');
			objTd4.html('<select id="produk'+newId+'" name="produk[]" class="form-control validate[required]"><option></option>'+$("#optProduk").html()+'</select>');
			objTd5.html('<input type="text" id="harga'+newId+'" name="harga[]" class="form-control validate[required] hitung" />');
			objTd6.html('<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
			rwTbl.after(objTr);
			tabel.find(".noTebus").each(function(i,v){
				$(this).text(i+1);
			});
			$("#cabang"+newId).select2({ placeholder: "Pilih Salah Satu", allowClear: true });
			$("#vendor"+newId).select2({ placeholder: "Pilih Salah Satu", allowClear: true });
			$("#produk"+newId).select2({ placeholder: "Pilih Salah Satu", allowClear: true });
			$("#harga"+newId).number(true, 0, ".", ",");
			$("form#gform").validationEngine('attach');
		});
		$("#table-tebus").on("click", "a.hRow", function(){
			var tabel 	= $("#table-tebus");
			var jTbl	= tabel.find("tr").length;
			var cRow = $(this).closest('tr');
			cRow.remove();
			tabel.find(".noTebus").each(function(i,v){
				$(this).text(i+1);
			});
		});
	});
</script>
</body>
</html>      
