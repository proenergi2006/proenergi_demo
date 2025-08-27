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
	$id1 	= isset($enk["id1"])?htmlspecialchars($enk["id1"], ENT_QUOTES):'';
	$id1 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);

	$sesuser 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
    $seswil 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $sesgroup 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);

	$sesrole = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	if($sesrole != '1' && $sesrole != '2'){ 
		header("location: ".BASE_URL_CLIENT.'/home.php'); exit();
	}

	if($id1 != ""){
		$sql = "
			select a.*, b.fullname as nama_mkt, b.username as user_mkt, 
			c.fullname as nama_spv, c.username as user_spv 
			from pro_mapping_spv a 
			join acl_user b on a.id_mkt = b.id_user 
			join acl_user c on a.id_spv = c.id_user 
			where 1=1 
			order by a.id_spv, a.no_urut 
		";
		$rsm = $con->getResult($sql);
		$titleAct 	= "Mapping Marketing";
	}
	
	$sqlListData01 = "
		select id_user, concat(fullname, ', Username : ', username) as nama_mkt 
		from acl_user 
		where is_active = 1 and id_role = 11  
		order by fullname
	";
	$resListData01 = $con->getResult($sqlListData01);

	$sqlListData02 = "
		select id_user, concat(fullname, ', Username : ', username) as nama_spv 
		from acl_user 
		where is_active = 1 and id_role = 20   
		order by fullname
	";
	$resListData02 = $con->getResult($sqlListData02);
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
                            	<h3 class="box-title"><i class="fa fa-pencil jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
							</div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/mapping-spv-mkt.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div class="row">
                                	<div class="col-md-10">
                                    	<div class="table-responsive">
                                            <table class="table table-bordered tblFormula">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="80">No</th>
                                                        <th class="text-center" width="350">Nama Supervisor</th>
                                                        <th class="text-center" width="">Nama Marketing</th>
                                                        <th class="text-center" width="100">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
												<?php
                                                    $arrMarketing = $rsm;
                                                    if(count($arrMarketing) > 0){
                                                        $nom = 0;
                                                        foreach($arrMarketing as $data){
                                                            $nom++;
                            
                                                            echo '
                                                            <tr data-id="'.$nom.'">
                                                                <td class="text-center"><span class="frmnodasar" data-row-count="'.$nom.'">'.$nom.'</span></td>
                                                                <td class="text-left">
																	<input type="hidden" id="id_spv'.$nom.'" name="id_spv[]" value="'.$data['id_spv'].'" />
																	'.$data['nama_spv'].'<br /><i>username : '.$data['user_spv'].'</i>
																</td>
                                                                <td class="text-left">
																	<input type="hidden" id="id_mkt'.$nom.'" name="id_mkt[]" value="'.$data['id_mkt'].'" />
																	'.$data['nama_mkt'].'<br /><i>username : '.$data['user_mkt'].'</i>
																</td>
																<td class="text-center">
																	<a class="btn btn-action btn-primary addRow jarak-kanan"><i class="fa fa-plus"></i></a>
																	<a class="btn btn-action btn-danger delRow"><i class="fa fa-times"></i></a>
																</td>
                                                            </tr>';
                                                        }
                                                    } else{
														$listData01 = "";
														foreach($resListData01 as $idx=>$data){
															$selected 	= ($currval == $data['id_user'] ? '' : '');
															$listData01 .= '<option value="'.$data['id_user'].'" '.$selected.'>'.$data['nama_mkt'].'</option>';
														}
														echo '
														<tr data-id="1">
															<td class="text-center"><span class="frmnodasar" data-row-count="1">1</span></td>
															<td class="text-left">
																<select id="id_mkt1" name="id_mkt[1]" class="form-control select2" required>
																	<option></option>
																	'.$listData01.'
																</select>
															</td>
															<td class="text-center">
																<a class="btn btn-action btn-primary addRow jarak-kanan"><i class="fa fa-plus"></i></a>
																<a class="btn btn-action btn-danger delRow"><i class="fa fa-times"></i></a>
															</td>
														</tr>';
                                                    }
                                                ?>
                                                </tbody>
                                            </table>
										</div>
									</div>
								</div>
                            
                                <hr style="margin:15px 0px; border-top:4px double #ddd;" />
                            
                                <div style="margin-bottom:0px;">
                                    <input type="hidden" name="act" value="<?php echo $action;?>" />
                                    <input type="hidden" name="id1" value="<?php echo $id1;?>" />
                                    <button type="submit" name="btnSbmt" id="btnSbmt" class="btn btn-primary jarak-kanan" style="min-width:90px;">
                                    <i class="fa fa-save jarak-kanan"></i> Save</button> 
                                    <a class="btn btn-default" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT."/mapping-spv-mkt.php";?>">
                                    <i class="fa fa-reply jarak-kanan"></i> Batal</a>
                                </div>
                                </form>
                                
                                
                            </div>
                        </div>
                    </div>
                </div>

            <div id="optMarketing" class="hide">
				<?php 
                    foreach($resListData01 as $idx=>$data){
                        echo '<option value="'.$data['id_user'].'">'.$data['nama_mkt'].'</option>';
                    }
                ?>
            </div>
            <div id="optSupervisor" class="hide">
				<?php 
                    foreach($resListData02 as $idx=>$data){
                        echo '<option value="'.$data['id_user'].'">'.$data['nama_spv'].'</option>';
                    }
                ?>
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

<style type="text/css">
	h3.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
	}
	#harga_dasar { 
		text-align: right;
	}
	.table > thead > tr > th, 
	.table > tbody > tr > td{
		font-size: 12px;
	}
</style>
<script>
$(document).ready(function(){
	var formValidasiCfg = {
		submitHandler: function(form) {
			$("#loading_modal").modal({keyboard:false, backdrop:'static'});

			if($("#cekkolnup").is(":checked") && $("#nup_fee").val() == ""){
				$("#loading_modal").modal("hide");
				$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
				setErrorFocus($("#nup_fee"), $("form#gform"), false);
			} else{
				form.submit();
			}
		}	
	};
	$("form#gform").validate($.extend(true,{},config.validation,formValidasiCfg));

	$("select#cb_transportir").select2({
		placeholder	: "Pilih Transportir",
		allowClear	: true,
	});
	
	$(".tblFormula").on("click", "a.addRow", function(){
		var tabel 	= $(".tblFormula");
		var arrId 	= tabel.find("tbody > tr").map(function(){ 
			return parseFloat($(this).data("id")) || 0; 
		}).toArray();
		var rwNom 	= Math.max.apply(Math, arrId);
		var newId 	= (rwNom == 0) ? 1 : (rwNom+1);
		
		var isiHtml = 
		'<tr data-id="'+newId+'">'+
			'<td class="text-center"><span class="frmnodasar" data-row-count="'+newId+'"></span></td>'+
			'<td class="text-left">'+
				'<select id="id_spv'+newId+'" name="id_spv['+newId+']" class="form-control" required>'+
					'<option></option>'+
					''+$("#optSupervisor").html()+''+
				'</select>'+
			'</td>'+
			'<td class="text-left">'+
				'<select id="id_mkt'+newId+'" name="id_mkt['+newId+']" class="form-control" required>'+
					'<option></option>'+
					''+$("#optMarketing").html()+''+
				'</select>'+
			'</td>'+
			'<td class="text-center">'+
				'<a class="btn btn-action btn-primary addRow jarak-kanan"><i class="fa fa-plus"></i></a> '+
				'<a class="btn btn-action btn-danger delRow"><i class="fa fa-times"></i></a>'+
			'</td>'+
		'</tr>';
		if(rwNom == 0){
			tabel.find('tbody').html(isiHtml);
		} else{
			$(this).closest('tr').after(isiHtml);
		}

		$("#id_mkt"+newId).select2({placeholder: "Pilih salah satu", allowClear: true});
		$("#id_spv"+newId).select2({placeholder: "Pilih salah satu", allowClear: true});
		tabel.find("span.frmnodasar").each(function(i,v){$(v).text(i+1);});
	}).on("click", "a.delRow", function(){
		var tabel 	= $(".tblFormula");
		var jTbl	= tabel.find('tbody > tr').length;
		if(jTbl > 1){
			var cRow = $(this).closest('tr');
			cRow.remove();
			tabel.find("span.frmnodasar").each(function(i,v){$(v).text(i+1);});
		}
	});
	
});		
</script>
</body>
</html>      
