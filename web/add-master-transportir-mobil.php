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
		$section 	= "Edit Master Mobil Transportir";
        $idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
        $sql = "select a.*, b.nama_transportir, b.nama_suplier, b.lokasi_suplier from pro_master_transportir_mobil a 
				join pro_master_transportir b on a.id_transportir = b.id_master where a.id_master = '".$idr."'";
        $rsm = $con->getRecord($sql);
		$chk = ($rsm['is_active'])?"checked":"";
		$kompar = json_decode($rsm['komp_tanki'], true);
		$pathPt = $public_base_directory.'/files/uploaded_user/lampiran/'.$rsm['photo'];
    } else{ 
        $idr = 0;
        $rsm = null;
		$action 	= "add";
		$section 	= "Tambah Master Mobil Transportir";
		$chk		= "checked";
		$kompar 	= array();
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
                        	<div class="box-header with-border bg-light-blue">
                            	<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
							</div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/master-transportir-mobil.php'; ?>" id="gform" name="gform" method="post" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <div class="col-sm-5">
                                        <label>Nomor Plat *</label>
                                        <input type="text" id="nomor_plat" name="nomor_plat" class="form-control validate[required]" value="<?php echo $rsm['nomor_plat'] ?? null;?>" />
                                    </div>
                                    <div class="col-sm-3 col-sm-top">
                                        <label>Kapasitas Max.</label>
                                        <div class="input-group">
                                        	<input type="text" id="max_kap" name="max_kap" class="form-control hitung" value="<?php echo $rsm['max_kap'];?>" />
                                    		<span class="input-group-addon">KL</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-10 col-md-8">
										<div class="table-responsive">
                                        	<table class="table table-bordered table-tanki" style="margin-top:10px;">
                                            	<thead>
                                                	<tr>
                                                    	<th class="text-center" width="45%">Kompartemen</th>
                                                    	<th class="text-center" width="45%">Kapasitas Tanki</th>
                                                    	<th class="text-center" width="5%">
                                                        <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button></th>
                                                    </tr>
                                                </thead>
                                            	<tbody>
												<?php 
                                                    if(count($kompar) == 0){
                                                        echo '<tr><td colspan="3" class="text-center">Tidak ada kompartemen</td></tr>';
                                                    } else{
                                                        $d=0;
                                                        foreach($kompar as $dat3){
                                                            $d++;
                                                ?>
                                                <tr>
                                                    <td>
                                                 	<input type="text" name="fkomp[]" id="<?php echo 'fkomp_'.$d;?>" class="form-control" value="<?php echo $dat3['kompart'];?>" />
                                                    </td>
                                                    <td>
                                                 	<input type="text" name="ftank[]" id="<?php echo 'ftank'.$d;?>" class="form-control" value="<?php echo $dat3['tanki'];?>" />
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="frmid" data-row-count="<?php echo $d;?>"></span>
                                                        <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                    </td>
                                                </tr>
                                                <?php } } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Transportir *</label>
										<?php if($action == "add"){ ?>
										<select name="transportir" id="transportir" class="form-control validate[required] select2">
                                        	<option></option>
											<?php 
												if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7){
													$con->fill_select("p.id_master","concat(nama_suplier,' - ',nama_transportir,', ',lokasi_suplier)","pro_master_transportir p
													join pro_master_cabang m on m.nama_cabang = p.lokasi_suplier and m.id_master = ".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']),$rsm['id_transportir'],"where p.is_active=1 and tipe_angkutan in (1,3)","p.id_master",false); 											
												}else
													$con->fill_select("id_master","concat(nama_suplier,' - ',nama_transportir,', ',lokasi_suplier)","pro_master_transportir",$rsm['id_transportir'],"where is_active=1 and tipe_angkutan in (1,3)","id_master",false); 
											?>
                                        </select>
                                        <?php } else if($action == "update"){ ?>
                                        <input type="hidden" name="transportir" id="transportir" value="<?php echo $rsm['id_transportir'];?>" />
                                        <div class="form-control"><?php echo $rsm['nama_suplier'].' - '.$rsm['nama_transportir'].', '.$rsm['lokasi_suplier'];?></div>
										<?php } ?>
                                    </div>
                                    <div class="col-sm-6 col-sm-top">
                                        <label>Link GPS</label>
                                        <input type="text" id="link_gps" name="link_gps" class="form-control" value="<?php echo $rsm['link_gps'] ?? null;?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Username GPS</label>
                                        <input type="text" id="user_gps" name="user_gps" class="form-control" value="<?php echo $rsm['user_gps'] ?? null;?>" />
                                    </div>
                                    <div class="col-sm-6 col-sm-top">
                                        <label>Password GPS</label>
                                        <input type="text" id="pass_gps" name="pass_gps" class="form-control" value="<?php echo $rsm['pass_gps'] ?? null;?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
									<div class="col-sm-6">
                                        <label>Member Code</label>
                                        <input type="text" id="membercode_gps" name="membercode_gps" class="form-control" value="<?php echo $rsm['membercode_gps'] ?? null;?>" />
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Photo</label>
										<?php
                                            if($rsm && $rsm['photo'] && file_exists($pathPt)){
                                                $urliPt = BASE_URL.'/files/uploaded_user/lampiran/'.$rsm['photo'];
                                                echo '<div style="margin-bottom:10px;"><img src="'.$urliPt.'" title="'.$rsm['photo_ori'].'" style="width:25%" /></div>';
                                            }
                                        ?>
                                        <input type="file" name="photo" id="photo" class="validate[funcCall[potoCheck]]" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
										<div class="table-responsive">
                                        	<table class="table table-bordered table-dokumen" style="margin-top:10px;">
                                            	<thead>
                                                	<tr>
                                                    	<th class="text-center" width="35%">Nama Dokumen</th>
                                                    	<th class="text-center" width="15%">Masa Berlaku</th>
                                                    	<th class="text-center" width="45%">Lampiran</th>
                                                    	<th class="text-center" width="5%">
                                                        <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button></th>
                                                    </tr>
                                                </thead>
                                            	<tbody>
                                                	<?php 
                                                        if ($idr) {
														$cek1 = "select * from pro_master_transportir_mobil_detail where id_transportir_mobil = '".$idr."' order by id_tmd";
														$row1 = $con->getResult($cek1);
														if(count($row1) == 0){
															echo '<tr><td colspan="4" class="text-center">Tidak ada dokumen</td></tr>';
														} else{
															$d=0;
															foreach($row1 as $dat1){
																$d++;
																$idd 	= $dat1['id_tmd'];
																$linkAt = "";
																$textAt = "";
																$pathAt = $public_base_directory.'/files/uploaded_user/lampiran/'.$dat1['lampiran'];
																$nameAt = $dat1['lampiran_ori'];
																if($dat1['lampiran'] && file_exists($pathAt)){
																	$linkAt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=mobil_".$idr."_".$idd."_&file=".$nameAt);
																	$textAt = '<a href="'.$linkAt.'"><i class="fa fa-paperclip jarak-kanan"></i>'.$nameAt.'</a>';
																}
													?>
                                                    <tr>
                                                        <td><?php echo $dat1['dokumen']; ?></td>
                                                        <td><?php echo tgl_indo($dat1['masa_berlaku'],'normal','db','/'); ?></td>
                                                    	<td><?php echo $textAt; ?></td>
                                                    	<td class="text-center">
                                                            <input type="hidden" name="<?php echo 'doknya['.$idd.']';?>" value="1" />
                                                            <span class="frmid" data-row-count="<?php echo $d;?>"></span>
                                                        	<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                        </td>
                                                    </tr>
                                                    <?php } } } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
								<?php if ($idr) { if(count($row1) > 0){ foreach($row1 as $dat2){ echo '<input type="hidden" name="doksup['.$dat2['id_tmd'].']" value="1" />'; } } } ?>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <div class="checkbox">
                                            <label class="rtl">
                                                <input type="checkbox" name="active" id="active" value="1" class="form-control" <?php echo $chk; ?> /> Active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <a href="<?php echo BASE_URL_CLIENT."/master-transportir-mobil.php"; ?>" class="btn btn-default jarak-kanan">
                                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
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
		var objAttach = {
			onValidationComplete: function(form, status){
				if(status == true){
					$('#loading_modal').modal({backdrop:"static"});
					form.validationEngine('detach');
					form.submit();
				}
			}
		};
		var objSettingDate = {								  
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			changeYear: true,
			yearRange: "c-80:c+10",
			dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
			monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
		};
		$("form#gform").validationEngine('attach',objAttach);
		$(".hitung").number(true, 0, ".", ",");

		$(".table-dokumen").on("click", "button.addRow", function(){
			$("form#gform").validationEngine('detach');
			var tabel 	= $(this).parents(".table-dokumen");
			var rwTbl	= tabel.find('tbody > tr:last');
			var rwNom	= parseInt(rwTbl.find("span.frmid").data('rowCount'));
			var newId 	= (isNaN(rwNom))?1:parseInt(rwNom + 1);

			var objTr 	= $("<tr>");
			var objTd1 	= $("<td>", {class:"text-left"}).appendTo(objTr);
			var objTd2 	= $("<td>", {class:"text-left"}).appendTo(objTr);
			var objTd3 	= $("<td>", {class:"text-left"}).appendTo(objTr);
			var objTd4 	= $("<td>", {class:"text-center"}).appendTo(objTr);
			objTd1.html('<input type="text" name="newdok1['+newId+']" id="newdok1_'+newId+'" class="form-control" autocomplete="off" />');
			objTd2.html('<input type="text" name="newdok2['+newId+']" id="newdok2_'+newId+'" class="form-control" autocomplete="off" />');
			objTd3.html('<input type="file" name="newdok3['+newId+']" id="newdok3_'+newId+'" class="validate[funcCall[fileCheck]]" />');
			objTd4.html('<span class="frmid" data-row-count="'+newId+'"></span><a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
			if(isNaN(rwNom)){
				rwTbl.remove();
				rwTbl = $(".table-dokumen > tbody");
				rwTbl.append(objTr);
			} else{
				rwTbl.after(objTr);
			}
			$("#newdok2_"+newId).datepicker(objSettingDate);
			$("form#gform").validationEngine('attach',objAttach);
		});
		$(".table-dokumen").on("click", "a.hRow", function(){
			var tabel 	= $(this).parents(".table-dokumen");
			var jTbl	= tabel.find("tr").length;
			if(jTbl > 1){
				var cRow = $(this).closest('tr');
				cRow.remove();
			}
			if(jTbl == 2){ 
				var nRow = $(".table-dokumen > tbody");
				nRow.append('<tr><td colspan="4" class="text-center">Tidak ada dokumen</td></tr>');
			}
		});

		$(".table-tanki").on("click", "button.addRow", function(){
			var tabel 	= $(this).parents(".table-tanki");
			var rwTbl	= tabel.find('tbody > tr:last');
			var rwNom	= parseInt(rwTbl.find("span.frmid").data('rowCount'));
			var newId 	= (isNaN(rwNom))?1:parseInt(rwNom + 1);

			var objTr 	= $("<tr>");
			var objTd1 	= $("<td>", {class:"text-left"}).appendTo(objTr);
			var objTd2 	= $("<td>", {class:"text-left"}).appendTo(objTr);
			var objTd3 	= $("<td>", {class:"text-center"}).appendTo(objTr);
			objTd1.html('<input type="text" name="fkomp[]" id="fkomp_'+newId+'" class="form-control" autocomplete="off" />');
			objTd2.html('<input type="text" name="ftank[]" id="ftank_'+newId+'" class="form-control" autocomplete="off" />');
			objTd3.html('<span class="frmid" data-row-count="'+newId+'"></span><a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
			if(isNaN(rwNom)){
				rwTbl.remove();
				rwTbl = $(".table-tanki > tbody");
				rwTbl.append(objTr);
			} else{
				rwTbl.after(objTr);
			}
		});
		$(".table-tanki").on("click", "a.hRow", function(){
			var tabel 	= $(this).parents(".table-tanki");
			var jTbl	= tabel.find("tr").length;
			if(jTbl > 1){
				var cRow = $(this).closest('tr');
				cRow.remove();
			}
			if(jTbl == 2){ 
				var nRow = $(".table-tanki > tbody");
				nRow.append('<tr><td colspan="3" class="text-center">Tidak ada kompartemen</td></tr>');
			}
		});
	});		
</script>
</body>
</html>      
