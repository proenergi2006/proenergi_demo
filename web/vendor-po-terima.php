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
		$section 	= "Edit PO Suplier";
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
        $sql = "select a.*, b.jenis_produk, b.merk_dagang, c.nama_area, d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal 
				from pro_inventory_vendor_po a 
				join pro_master_produk b on a.id_produk = b.id_master 
				join pro_master_area c on a.id_area = c.id_master 
				join pro_master_vendor d on a.id_vendor = d.id_master 
				join pro_master_terminal e on a.id_terminal = e.id_master 
				where a.id_master = '".$idr."'";
        $rsm = $con->getRecord($sql);
		$dt1 = date("d/m/Y", strtotime($rsm['tanggal_inven']));
		$dt7 = ($rsm['in_inven'])?$rsm['in_inven']:'0';
		$dt8 = ($rsm['harga_tebus'])?$rsm['harga_tebus']:'';
        $dt9 = ($rsm['in_inven_po'])?$rsm['in_inven_po']:'';
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
            <form action="<?php echo ACTION_CLIENT.'/vendor-po-terima.php'; ?>" id="gform" name="gform" method="post" role="form" enctype="multipart/form-data">
            <div class="box box-primary">
                <div class="box-header with-border bg-light-blue">
                    <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-12">Tanggal</label>
                                <div class="col-md-6">
                                    <input type="hidden" name="dt1" id="dt1" value="<?php echo $dt1;?>" />
                                    <input type="text" id="txtdt1" name="txtdt1" class="form-control input-sm" value="<?php echo $dt1;?>" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-12">Nomor PO</label>
                                <div class="col-md-12">
                                    <input type="text" name="dt2" id="dt2" class="form-control input-sm" value="<?php echo $rsm['nomor_po'];?>" readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-12">Produk</label>
                                <div class="col-md-12">
                                    <input type="hidden" name="dt3" id="dt3" value="<?php echo $rsm['id_produk'];?>" />
                                    <input type="text" id="txtdt3" name="txtdt3" class="form-control input-sm" value="<?php echo $rsm['jenis_produk'].' - '.$rsm['merk_dagang'];?>" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-12">Area</label>
                                <div class="col-md-12">
                                    <input type="hidden" name="dt4" id="dt4" value="<?php echo $rsm['id_area'];?>" />
                                    <input type="text" id="txtdt4" name="txtdt4" class="form-control input-sm" value="<?php echo $rsm['nama_area'];?>" readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-12">Vendor</label>
                                <div class="col-md-12">
                                    <input type="hidden" name="dt5" id="dt5" value="<?php echo $rsm['id_vendor'];?>" />
                                    <input type="text" id="txtdt5" name="txtdt5" class="form-control input-sm" value="<?php echo $rsm['nama_vendor'];?>" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-12">Terminal</label>
                                <div class="col-md-12">
                                    <?php 
                                        $terminal1 	= $rsm['nama_terminal'];
                                        $terminal2 	= ($rsm['tanki_terminal']?' - '.$rsm['tanki_terminal']:'');
                                        $terminal3 	= ($rsm['lokasi_terminal']?', '.$rsm['lokasi_terminal']:'');
                                        $terminal 	= $terminal1.$terminal2.$terminal3;
                                        echo '<input type="hidden" name="dt6" id="dt6" value="'.$rsm['id_terminal'].'" />';
                                        echo '<input type="text" id="txtdt6" name="txtdt6" class="form-control input-sm" value="'.$terminal.'" readonly />';
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-12">Volume PO</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="hidden" id="dt7" name="dt7" class=" hitung" value="<?php echo ($dt9==''?$dt7:$dt9);?>" />
                                        <input type="text" id="dt10" name="dt10" class="form-control hitung" value="<?php echo ($dt9==''?$dt7:$dt9);?>" readonly />
                                        <span class="input-group-addon">Liter</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-12">Harga Tebus (Inc. Tax)</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">Rp.</span>
                                        <input type="text" id="dt8" name="dt8" class="form-control hitung" value="<?php echo $dt8;?>" readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if($action == "update"){ ?>
                    <div class="table-responsive">
                        <table id="tb_vol_terima" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" width="80">NO</th>
                                    <th class="text-center" width="150">TANGGAL TERIMA</th>
                                    <th class="text-center" width="300">PIC</th>
                                    <th class="text-center" width="180">VOLUME TERIMA</th>
                                    <th class="text-center" width="">FILE PENDUKUNG</th>
                                    <th class="text-center" width="80">
                                        <a class="btn btn-primary btn-sm add_volume"><i class="fa fa-plus"></i></a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
							<?php 
                                $rowTerima 	= json_decode($rsm['in_inven_po_detail'], true);
								$arrTerima 	= (is_array($rowTerima) && count($rowTerima) > 0) ? $rowTerima : array(array(""));
								$no_urut = 0;
								foreach($arrTerima as $idx=>$value){ 
									$no_urut++;
									$nom 		= ($value['id_detail']) ? $value['id_detail'] : $no_urut;
									$pathFile 	= $value['filenya'];
									$labelFile 	= 'Unggah File';
									$dataIcons 	= '<div style="width:45px; float:left;">&nbsp;</div>';
									
									if($value['file_upload_ori'] && file_exists($pathFile)){
										$labelFile 	= 'Ubah File';
										$linkPt 	= ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=3&ktg=".$value['filenya']."&file=".$value['file_upload_ori']);
										$dataIcons 	= '
										<div style="width:45px; float:left;">
											<a href="'.$linkPt.'" target="_blank" class="btn btn-sm btn-success" title="download file" style="color: #fff;"> 
											<i class="fa fa-download"></i></a>
										</div>';
									}

									echo '
									<tr data-id="'.$nom.'">
										<td class="text-center"><span class="frmnodasar" data-row-count="'.$nom.'">'.$no_urut.'</span></td>
										<td class="text-left">
											<input type="text" id="tgl_terima'.$nom.'" name="tgl_terima['.$nom.']" class="form-control tgl_terima datepicker" value="'.$value['tgl_terima'].'" />
										</td>
										<td class="text-left">
											<input type="text" id="pic'.$nom.'" name="pic['.$nom.']" class="form-control pic" value="'.$value['pic'].'" />
										</td>
										<td class="text-left">
											<input type="text" id="vol_terima'.$nom.'" name="vol_terima['.$nom.']" class="form-control vol_terima hitung" value="'.$value['vol_terima'].'" />
										</td>
										<td class="text-left">
											<div class="rowuploadnya">
												'.$dataIcons.'
												<div class="simple-fileupload" style="margin-left:45px;">
													<input type="file" name="file_template['.$nom.']" id="file_template'.$nom.'" class="form-inputfile" />
													<label for="file_template'.$nom.'" class="label-inputfile">
														<div class="input-group input-group-sm">
															<div class="input-group-addon btn-primary"><i class="fa fa-upload"></i></div>
															<input type="text" class="form-control" placeholder="'.$labelFile.'" readonly />
														</div>
													</label>
												</div>
											</div>
										</td>
										<td class="text-center">
											<a class="btn btn-danger btn-sm del_volume"><span class="fa fa-trash"></span></a>
										</td>
									</tr>';
								}
							?>
                            </tbody>
                            <tfoot>
                                <tr style="border-top:3px solid #ddd;">
                                    <td class="text-left">&nbsp;</td>
                                    <td class="text-center" colspan="2"><b>Total</b></td>
                                    <td class="text-right">
                                    	<input type="hidden" id="vol_total" name="dt9" value="<?php echo $dt7;?>" />
                                        <input type="text" id="vol_total_cek" name="vol_total_cek" class="form-control text-right" value="<?php echo number_format($dt7); ?>" readonly />
									</td>
                                    <td class="text-left">
                                        <div style="margin:0px 15px;">
                                            <label class="rtl">
                                            	<input type="checkbox" name="is_selesai" id="is_selesai" value="1" <?php echo ($rsm['is_selesai'] == '1')?'checked':''; ?> /> 
                                                Selesai Diterima
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-right">&nbsp;</td>
                                </tr>
                            </tfoot>
                        </table> 
                    </div>
                    <?php } ?>

                    <div style="padding:15px 0px;">
                        <input type="hidden" name="act" value="<?php echo $action;?>" />
                        <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                        <?php if(!$rsm['is_selesai']){ ?>
                        <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:120px;">
                            <i class="fa fa-save jarak-kanan"></i> Simpan
                        </button>
                        <?php } ?>
                        <a href="<?php echo BASE_URL_CLIENT."/vendor-po.php"; ?>" class="btn btn-default" style="min-width:120px;">
                            <i class="fa fa-reply jarak-kanan"></i> Kembali
                        </a>
                    </div>
                    <hr style="border-top:4px double #ddd; margin:0 0 10px;">
                    <p style="margin:0px"><small>* Wajib Diisi</small></p>
                </div>
            </div>
            </form>

			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style>
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
	.form-control{
		height: 30px;
		padding: 5px 10px;
		font-size: 12px;
		line-height: 1.5;
		border-radius: 3px;
	}
</style>
<script>
$(document).ready(function(){
	var objAttach = {
		onValidationComplete: function(form, status){
			if(status == true){
				let jml_po = "<?php echo $dt9; ?>", nilaicek = $("#vol_total_cek").val();
				let cekKolom = true;

				$("#tb_vol_terima > tbody > tr").each(function(i, v){
					let idnya 	= $(this).data("id");
					let kolom1 = $("#tgl_terima"+idnya).val();
					let kolom2 = $("#vol_terima"+idnya).val();
					cekKolom = cekKolom && (kolom1 && kolom2);
				});
				
				if(!cekKolom){
					swal.fire({
						icon : "warning", width : '350px', allowOutsideClick : false, 
						html : '<p style="font-size:14px; font-family:arial;">Kolom [Tgl Terima] dan [Volume Terima]<br />belum diisi</p>'
					});
					return false;
				} else if(parseInt(nilaicek) > parseInt(jml_po)){
					swal.fire({
						icon : "warning", width : '350px', allowOutsideClick : false, 
						html : '<p style="font-size:14px; font-family:arial;">Volume yang diterima melebihi dari volume PO</p>'
					});
					return false;
				} else{
					form.validationEngine('detach');
					form.submit();
				}
			}
		}
	};
	$("form#gform").validationEngine('attach',objAttach);

	var objSettingDate = {                                
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		changeYear: true,
		yearRange: "c-80:c+10",
		dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
		monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
	};

	$(".hitung").number(true, 0, ".", ",");
	
	$("#tb_vol_terima").on("click", ".add_volume", function(){
		var tabel 	= $("#tb_vol_terima");
		var arrId 	= tabel.find("tbody > tr").map(function(){ 
			return parseFloat($(this).data("id")) || 0; 
		}).toArray();
		var rwNom 	= Math.max.apply(Math, arrId);
		var newId 	= (rwNom == 0) ? 1 : (rwNom+1);

		var isiHtml = 
		'<tr data-id="'+newId+'">'+
			'<td class="text-center"><span class="frmnodasar" data-row-count="'+newId+'"></span></td>'+
			'<td class="text-left">'+
				'<input type="text" id="tgl_terima'+newId+'" name="tgl_terima['+newId+']" class="form-control tgl_terima" />'+
			'</td>'+
			'<td class="text-left">'+
				'<input type="text" id="pic'+newId+'" name="pic['+newId+']" class="form-control pic" />'+
			'</td>'+
			'<td class="text-left">'+
				'<input type="text" id="vol_terima'+newId+'" name="vol_terima['+newId+']" class="form-control vol_terima text-right" />'+
			'</td>'+
			'<td class="text-left">'+
				'<div class="rowuploadnya">'+
					'<div style="width:45px; float:left;">&nbsp;</div>'+
					'<div class="simple-fileupload" style="margin-left:45px;">'+
						'<input type="file" name="file_template['+newId+']" id="file_template'+newId+'" class="form-inputfile" />'+
						'<label for="file_template'+newId+'" class="label-inputfile">'+
							'<div class="input-group input-group-sm">'+
								'<div class="input-group-addon btn-primary"><i class="fa fa-upload"></i></div>'+
								'<input type="text" class="form-control" placeholder="Unggah File" readonly />'+
							'</div>'+
						'</label>'+
					'</div>'+
				'</div>'+
			'</td>'+
			'<td class="text-center">'+
				'<a class="btn btn-danger btn-sm del_volume"><span class="fa fa-trash"></span></a>'+
			'</td>'+
		'</tr>';
		if(rwNom == 0){
			tabel.find('tbody').html(isiHtml);
		} else{
			tabel.find('tbody > tr:last').after(isiHtml);
		}

		$("#tgl_terima"+newId).datepicker(objSettingDate);
		$("#vol_terima"+newId).number(true, 0, ".", ",");
		tabel.find("span.frmnodasar").each(function(i,v){$(v).text(i+1);});
	}).on("click", ".del_volume", function(){
		var tabel 	= $("#tb_vol_terima");
		var jTbl	= tabel.find('tbody > tr').length;
		if(jTbl > 1){
			var cRow = $(this).closest('tr');
			cRow.remove();
			tabel.find("span.frmnodasar").each(function(i,v){$(v).text(i+1);});
			calculate_volterima();
		}
	}).on("keyup blur", ".vol_terima", function(){
		calculate_volterima();
	});
	function calculate_volterima(){
		let grandTotal = 0;
		$(".vol_terima").each(function(i, v){
			grandTotal = grandTotal + ($(v).val() * 1);
		});
		$('#vol_total').val(grandTotal);
		$('#vol_total_cek').val(grandTotal);
		$("#vol_total_cek").number(true, 0, ".", ",");
	}
	
});	
</script>
</body>
</html>      
