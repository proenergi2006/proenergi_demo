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
	$link 	= BASE_URL_CLIENT."/vendor-inven-terminal.php";

    if (isset($enk['idr']) && $enk['idr']!== ''){
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
        $sql = "select a.*, b.nama_vendor, c.jenis_produk, c.merk_dagang, d.nama_area, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal from pro_inventory_vendor a  
				join pro_master_vendor b on a.id_vendor = b.id_master join pro_master_produk c on a.id_produk = c.id_master 
				join pro_master_area d on a.id_area = d.id_master join pro_master_terminal e on a.id_terminal = e.id_master 
				where a.id_master = '".$idr."'";
        $rsm = $con->getRecord($sql);
        $action 	= "update"; 
		$section 	= "Tambah Data Awal / Adjustment Inventory";
		$class1 	= "";
		$tglinv 	= 'value="'.date("d/m/Y", strtotime($rsm['tanggal_inven'])).'" readonly'; 
		$vendorN 	= $rsm['nama_vendor']; 
		$produkN 	= $rsm['jenis_produk'].' - '.$rsm['merk_dagang']; 
		$areaN 		= $rsm['nama_area']; 
		$terminalN 	= $rsm['nama_terminal'].' '.$rsm['tanki_terminal'].', '.$rsm['lokasi_terminal']; 
    } else{ 
        $rsm 		= array();
		$action  	= "add";
		$section 	= "Tambah Data Awal / Adjustment Inventory";
		$class1 	= "datepicker";
		$tglinv 	= 'value=""'; 
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
							<form action="<?php echo ACTION_CLIENT.'/vendor-inven-terminal.php'; ?>" id="gform" name="gform" method="post" role="form">
							<?php //echo md5(uniqid("1089", "1").'-'.intval(microtime(true)).'-'.date('YmdHis')); ?>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-md-12">Jenis Penambahan *</label>
										<div class="col-md-6">
											<select id="id_jenis" name="id_jenis" class="form-control validate[required] select2">
												<option></option>
												<option value="1">Data Awal</option>
												<option value="3">Adjustment</option>
												<option value="4">Transfer Stock</option>
											</select>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-12">Produk *</label>
                                        <div class="col-md-12">
                                            <select id="id_produk" name="id_produk" class="form-control validate[required] select2">
                                                <option></option>
                                                <?php $con->fill_select("id_master","concat(jenis_produk,' - ',merk_dagang)","pro_master_produk",$rsm['id_produk'],"where is_active =1","id_master",false); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-md-12">Tanggal *</label>
										<div class="col-md-6">
											<input type="text" id="tgl" name="tgl" class="form-control validate[required, custom[date]] <?php echo $class1;?>" <?php echo $tglinv;?> />
										</div>
									</div>
								</div>
							</div>
							
							<hr style="border-top:4px double #ddd;">

							<div id="group_depot" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-md-12">Depot Terminal *</label>
                                            <div class="col-md-12">
                                                <select id="id_terminal" name="id_terminal" class="form-control validate[required] select2">
                                                    <option></option>
                                                    <?php $con->fill_select("id_master", "concat(nama_terminal,' ',tanki_terminal,', ',lokasi_terminal)", "pro_master_terminal", $rsm['id_terminal'], "where is_active=1", "id_master", false); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							</div>

							<div id="group_depot02" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-md-12">Vendor</label>
                                            <div class="col-md-12">
                                                <select id="id_vendor" name="id_vendor" class="form-control select2">
                                                    <option></option>
                                                    <?php $con->fill_select("id_master","nama_vendor","pro_master_vendor", "","where is_active=1","id_master",false); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							</div>

							<div id="group_data_awal" style="display:none;">
                                <div class="table-responsive">
                                    <table class="table table-bordered tbl_add_vendor">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="100">No</th>
                                                <th class="text-center" width="">Nama Vendor</th>
                                                <th class="text-center" width="300">Jumlah</th>
                                                <th class="text-center" width="100">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-center" colspan="2"><b>Total Data Awal</b></td>
                                                <td class="text-center">
                                                	<input type="text" id="awal_inven_total" name="awal_inven_total" class="form-control input-sm hitung" readonly />
												</td>
                                                <td class="text-center">&nbsp;</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
							</div>

							<div id="group_adjustment" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-md-12">Adjustment Inventory</label>
                                            <div class="col-md-12">
                                                <select id="adj_inven_sign" name="adj_inven_sign" class="form-control select2">
                                                    <option value="+">Bertambah / Gain (+)</option>
                                                    <option value="-">Berkurang / Loss (-)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <input type="text" name="adj_inven" id="adj_inven" class="form-control hitung validate[required]" value="<?php echo $rsm['adj_inven'];?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
							</div>
							
							<div id="group_trans_tanki_satu" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-md-12">Dari Tanki Terminal</label>
                                            <div class="col-md-12">
                                                <select id="transfer_tanki_satu_dari" name="transfer_tanki_satu_dari" class="form-control validate[required] select2">
                                                    <option></option>
                                                    <?php $con->fill_select("id_master", "concat(nama_terminal,' ',tanki_terminal,', ',lokasi_terminal)", "pro_master_terminal", $rsm['id_terminal'], "where is_active=1", "id_master", false); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-md-12">Kedalam Tanki Terminal</label>
                                            <div class="col-md-12">
                                                <select id="transfer_tanki_satu_ke" name="transfer_tanki_satu_ke" class="form-control validate[required] select2">
                                                    <option></option>
                                                    <?php $con->fill_select("id_master", "concat(nama_terminal,' ',tanki_terminal,', ',lokasi_terminal)", "pro_master_terminal", $rsm['id_terminal'], "where is_active=1", "id_master", false); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="table-responsive">
                                    <table class="table table-bordered tbl_trans_tanki_satu">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="100">No</th>
                                                <th class="text-center" width="">Nama Vendor</th>
                                                <th class="text-center" width="300">Jumlah</th>
                                                <th class="text-center" width="100">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-center" colspan="2"><b>Total Transfer</b></td>
                                                <td class="text-center">
                                                	<input type="text" id="tank_satu_total" name="tank_satu_total" class="form-control input-sm hitung" readonly />
												</td>
                                                <td class="text-center">&nbsp;</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
							</div>
							
							<hr style="border-top:4px double #ddd;">

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group row">
                                        <label class="col-md-12">Keterangan</label>
                                        <div class="col-md-12">
                                            <textarea id="keterangan" name="keterangan" class="form-control" style="min-height:100px;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="padding:15px 0px;">
                                <input type="hidden" name="act" value="<?php echo $action;?>" />
                                <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:120px;">
                                	<i class="fa fa-save jarak-kanan"></i> Simpan
								</button>
                                <a href="<?php echo $link; ?>" class="btn btn-default" style="min-width:120px;"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                            </div>
							<hr style="border-top:4px double #ddd; margin:0 0 10px;">
							<p style="margin:0px"><small>* Wajib Diisi</small></p>
							</form>

						</div>
					</div>
				</div>
			</div>

            <div id="optTerminal" class="hide">
                <?php $con->fill_select("id_master", "concat(nama_terminal,' ',tanki_terminal,', ',lokasi_terminal)", "pro_master_terminal", $rsm['id_terminal'], "where is_active=1", "id_master", false); ?>
            </div>

            <div id="optProduk" class="hide">
                <?php $con->fill_select("id_master","concat(jenis_produk,' - ',merk_dagang)","pro_master_produk", "","where is_active =1","id_master",false); ?>
            </div>

            <div id="optVendor" class="hide">
                <?php $con->fill_select("id_master","nama_vendor","pro_master_vendor", "","where is_active=1","id_master",false); ?>
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
	.table > tfoot > tr > td{
		border: 1px solid #ddd;
		padding: 5px;
		font-size: 11px;
		font-family: arial;
		vertical-align: middle;
	}
	.swal2-modal .swal2-styled{
		padding: 5px;
		min-width: 130px;
		font-family: arial;
		font-size: 14px;
		margin: 10px;
	}
</style>

<script>
$(document).ready(function(){
	var objAttach = {
		onValidationComplete: function(form, status){
			if(status == true){
				/*if($("#id_jenis").val() == "1"){
					let nilai01 = false;
					$(".tbl_add_vendor").find(".awal_inven_vendor_nilai").each(function(i, v){
						console.log($(v).val()); 
					});
					return false;
				} else{
					return false;
					//form.validationEngine('detach');
					//form.submit();
				}*/
				form.validationEngine('detach');
				form.submit();
			}
		}
	};
	$("form#gform").validationEngine('attach',objAttach);

	$(".hitung").number(true, 0, ".", ",");
	var htmlOptVendor = 
	'<tr data-id="1">'+
		'<td class="text-center"><span class="notabelawalinvenvendor" data-row-count="1">1</span></td>'+
		'<td class="text-left"><select id="awal_inven_vendor_id1" name="awal_inven_vendor_id[]" class="form-control"><option></option></select></td>'+
		'<td class="text-left"><input type="text" id="awal_inven_vendor_nilai1" name="awal_inven_vendor_nilai[]" class="form-control input-sm text-right awal_inven_vendor_nilai" /></td>'+
		'<td class="text-center">'+
			'<a class="btn btn-action btn-primary addRow jarak-kanan"><i class="fa fa-plus"></i></a>'+
			'<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'+
		'</td>'+
	'</tr>';

	var htmlOptVendorTankiSatu = 
	'<tr data-id="1">'+
		'<td class="text-center"><span class="notabeltanksatuvendor" data-row-count="1">1</span></td>'+
		'<td class="text-left"><select id="tank_satu_vendor_id1" name="tank_satu_vendor_id[]" class="form-control"><option></option></select></td>'+
		'<td class="text-left"><input type="text" id="tank_satu_vendor_nilai1" name="tank_satu_vendor_nilai[]" class="form-control input-sm text-right tank_satu_vendor_nilai" /></td>'+
		'<td class="text-center">'+
			'<a class="btn btn-action btn-primary addRow jarak-kanan"><i class="fa fa-plus"></i></a>'+
			'<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'+
		'</td>'+
	'</tr>';

	$("#id_jenis").on("change", function(){
		let nilai = $(this).val();

		$("#keterangan").val("");

		if(nilai == "1"){
			$("#group_depot").show(400, "swing", function(){
				$("#id_terminal").val("").trigger("change");
			});
			
			$("#group_depot02").hide(400, "swing", function(){
				$("#id_vendor").val("").trigger("change");
			});

			$("#group_data_awal").show("400", "swing", function(){
				$(".tbl_add_vendor > tbody").html(htmlOptVendor);
				$("#awal_inven_vendor_id1").select2({placeholder:"Pilih salah satu", allowClear:true});
				$("#awal_inven_vendor_id1").html('<option></option>'+$("#optVendor").html());
				$("#awal_inven_vendor_nilai1").number(true, 0, ".", ",");
			});
			
			$("#group_adjustment").hide("400", "swing", function(){
				$("#adj_inven_sign").val("+").trigger("change");
				$("#adj_inven").val("");
			});

			$("#group_trans_tanki_satu").hide("400", "swing", function(){
				$("#transfer_tanki_satu_dari, #transfer_tanki_satu_ke").val("").trigger("change");
				$("#tank_satu_total").val("");
				$(".tbl_trans_tanki_satu > tbody").html("");
			});
		}

		else if(nilai == "2" || nilai == "3"){
			$("#group_depot").show(400, "swing", function(){
				$("#id_terminal").val("").trigger("change");
			});

			$("#group_depot02").show(400, "swing", function(){
				$("#id_vendor").val("").trigger("change");
			});

			$("#group_data_awal").hide("400", "swing", function(){
				$("#awal_inven_total").val("");
				$(".tbl_add_vendor > tbody").html("");
			});

			$("#group_adjustment").show("400", "swing");
			$("#adj_inven_sign").val("+").trigger("change");
			$("#adj_inven").val("");

			$("#group_trans_tanki_satu").hide("400", "swing", function(){
				$("#transfer_tanki_satu_dari, #transfer_tanki_satu_ke").val("").trigger("change");
				$("#tank_satu_total").val("");
				$(".tbl_trans_tanki_satu > tbody").html("");
			});
		}

		else if(nilai == 4){
			$("#group_depot").hide(400, "swing", function(){
				$("#id_terminal").val("").trigger("change");
			});

			$("#group_depot02").hide(400, "swing", function(){
				$("#id_vendor").val("").trigger("change");
			});

			$("#group_data_awal").hide("400", "swing", function(){
				$("#awal_inven_total").val("");
				$(".tbl_add_vendor > tbody").html("");
			});

			$("#group_adjustment").hide("400", "swing", function(){
				$("#adj_inven_sign").val("+").trigger("change");
				$("#adj_inven").val("");
			});

			$("#group_trans_tanki_satu").show("400", "swing", function(){
				$(".tbl_trans_tanki_satu > tbody").html(htmlOptVendorTankiSatu);
				$("#tank_satu_vendor_id1").select2({placeholder:"Pilih salah satu", allowClear:true});
				$("#tank_satu_vendor_id1").html('<option></option>'+$("#optVendor").html());
				$("#tank_satu_vendor_nilai1").number(true, 0, ".", ",");
			});
		}

		else{
			$("#group_depot").hide(400, "swing", function(){
				$("#id_terminal").val("").trigger("change");
			});

			$("#group_depot02").hide(400, "swing", function(){
				$("#id_vendor").val("").trigger("change");
			});

			$("#group_data_awal").hide("400", "swing", function(){
				$("#awal_inven_total").val("");
				$(".tbl_add_vendor > tbody").html("");
			});

			$("#group_adjustment").hide("400", "swing", function(){
				$("#adj_inven_sign").val("+").trigger("change");
				$("#adj_inven").val("");
			});

			$("#group_trans_tanki_satu").hide("400", "swing", function(){
				$("#transfer_tanki_satu_dari, #transfer_tanki_satu_ke").val("").trigger("change");
				$("#tank_satu_total").val("");
				$(".tbl_trans_tanki_satu > tbody").html("");
			});
		}
	});

	$(".tbl_add_vendor").on("click", "a.addRow", function(){
		var tabel 	= $(".tbl_add_vendor");
		var rwTbl	= tabel.find('tbody > tr:last');
		var rwNom	= parseInt(rwTbl.find("span.notabelawalinvenvendor").data('rowCount'));
		var newId 	= (isNaN(rwNom))?1:parseInt(rwNom + 1);

		var isiHtml =
		'<tr data-id="'+newId+'">'+
			'<td class="text-center"><span class="notabelawalinvenvendor" data-row-count="'+newId+'"></span></td>'+
			'<td class="text-left"><select id="awal_inven_vendor_id'+newId+'" name="awal_inven_vendor_id[]" class="form-control"><option></option></select></td>'+
			'<td class="text-left"><input type="text" id="awal_inven_vendor_nilai'+newId+'" name="awal_inven_vendor_nilai[]" class="form-control input-sm text-right awal_inven_vendor_nilai" /></td>'+
			'<td class="text-center">'+
				'<a class="btn btn-action btn-primary addRow jarak-kanan"><i class="fa fa-plus"></i></a>'+
				'<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'+
			'</td>'+
		'</tr>';
		if(isNaN(rwNom)){
			rwTbl.remove();
			rwTbl = tabel.find('tbody');
			rwTbl.append(isiHtml);
		} else{
			rwTbl.after(isiHtml);
		}
		$("#awal_inven_vendor_id"+newId).select2({placeholder:"Pilih salah satu", allowClear:true});
		$("#awal_inven_vendor_id"+newId).html('<option></option>'+$("#optVendor").html());
		$("#awal_inven_vendor_nilai"+newId).number(true, 0, ".", ",");
		tabel.find("span.notabelawalinvenvendor").each(function(i,v){$(v).text(i+1);});
	}).on("click", "a.hRow", function(){
		var tabel 	= $(".tbl_add_vendor");
		var jTbl	= tabel.find('tbody > tr').length;
		if(jTbl > 1){
			var cRow = $(this).closest('tr');
			cRow.remove();
			tabel.find("span.notabelawalinvenvendor").each(function(i,v){$(v).text(i+1);});
		}
		$(".tbl_add_vendor .awal_inven_vendor_nilai").trigger("keyup");
	});

	$(".tbl_trans_tanki_satu").on("click", "a.addRow", function(){
		var tabel 	= $(".tbl_trans_tanki_satu");
		var rwTbl	= tabel.find('tbody > tr:last');
		var rwNom	= parseInt(rwTbl.find("span.notabeltanksatuvendor").data('rowCount'));
		var newId 	= (isNaN(rwNom))?1:parseInt(rwNom + 1);

		var isiHtml =
		'<tr data-id="'+newId+'">'+
			'<td class="text-center"><span class="notabeltanksatuvendor" data-row-count="'+newId+'"></span></td>'+
			'<td class="text-left"><select id="tank_satu_vendor_id'+newId+'" name="tank_satu_vendor_id[]" class="form-control"><option></option></select></td>'+
			'<td class="text-left"><input type="text" id="tank_satu_vendor_nilai'+newId+'" name="tank_satu_vendor_nilai[]" class="form-control input-sm text-right tank_satu_vendor_nilai" /></td>'+
			'<td class="text-center">'+
				'<a class="btn btn-action btn-primary addRow jarak-kanan"><i class="fa fa-plus"></i></a>'+
				'<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'+
			'</td>'+
		'</tr>';
		if(isNaN(rwNom)){
			rwTbl.remove();
			rwTbl = tabel.find('tbody');
			rwTbl.append(isiHtml);
		} else{
			rwTbl.after(isiHtml);
		}
		$("#tank_satu_vendor_id"+newId).select2({placeholder:"Pilih salah satu", allowClear:true});
		$("#tank_satu_vendor_id"+newId).html('<option></option>'+$("#optVendor").html());
		$("#tank_satu_vendor_nilai"+newId).number(true, 0, ".", ",");
		tabel.find("span.notabeltanksatuvendor").each(function(i,v){$(v).text(i+1);});
	}).on("click", "a.hRow", function(){
		var tabel 	= $(".tbl_trans_tanki_satu");
		var jTbl	= tabel.find('tbody > tr').length;
		if(jTbl > 1){
			var cRow = $(this).closest('tr');
			cRow.remove();
			tabel.find("span.notabeltanksatuvendor").each(function(i,v){$(v).text(i+1);});
		}
		$(".tbl_trans_tanki_satu .tank_satu_vendor_nilai").trigger("keyup");
	});
	
	$(".tbl_add_vendor").on("keyup blur", ".awal_inven_vendor_nilai", function(){
		let total = 0;
		$("input[name='awal_inven_vendor_nilai[]']").each(function(i, v){
			total = total + ($(v).val() * 1);
		});
		$("#awal_inven_total").val(total);
	});

	$(".tbl_trans_tanki_satu").on("keyup blur", ".tank_satu_vendor_nilai", function(){
		let total = 0;
		$("input[name='tank_satu_vendor_nilai[]']").each(function(i, v){
			total = total + ($(v).val() * 1);
		});
		$("#tank_satu_total").val(total);
	});

});
</script>
</body>
</html>      
