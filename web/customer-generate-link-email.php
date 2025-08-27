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
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
	$token 	= htmlspecialchars($enk["token"], ENT_QUOTES);
	$sqlCek = "select a.nama_customer, a.alamat_customer, a.telp_customer, a.fax_customer, a.email_customer, a.need_update, b.nama_kab, c.nama_prov from pro_customer a 
			   join pro_master_kabupaten b on a.kab_customer = b.id_kab join pro_master_provinsi c on a.prov_customer = c.id_prov where a.id_customer = '".$idr."'";
	$resCek = $con->getRecord($sqlCek);
	$alamat = $resCek['alamat_customer']." ".str_replace(array("KABUPATEN ","KOTA "), array("",""), $resCek['nama_kab'])." ".$resCek['nama_prov'];
	$tautan = 'Kepada Yth.<br />'.$resCek['nama_customer'].'<br />Di Tempat
	<p>Bersama ini kami sampaikan Link Database sebagai customer kami, mohon kesediaan Bapak/Ibu untuk mengisi dan melengkapi data tersebut.</p>
	<p>'.BASE_URL.'/customer/update-customer.php?'.paramEncrypt('idr='.$idr.'&idk='.$idk.'&token='.$token).'</p>
	<p>Demikian penyampaian dari kami.</p><p>Terima kasih atas perhatian dan kerjasamanya.</p><p>Salam,<br />Managemen PT. Pro Energi</p>';
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("ckeditor419"))); ?>
<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Generate Link</h1>
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
                                <form action="<?php echo ACTION_CLIENT.'/customer-generate-link-email.php'; ?>" id="gform" name="gform" class="form-validasi" method="post">
                                    <div class="row">
                                        <div class="col-sm-10 col-md-8">
                                            <div class="form-group">
                                            	<label>Kepada</label>
                                            	<input type="text" name="to" id="to" class="form-control" required data-rule-email="1" value="<?php echo $resCek['email_customer'];?>" />
											</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-md-8">
                                            <div class="form-group">
                                            	<label>CC</label>
                                            	<input type="text" name="cc" id="cc" class="form-control" />
											</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10 col-md-8">
                                            <div class="form-group">
                                            	<label>Judul</label>
                                            	<input type="text" name="judul" id="judul" class="form-control" required value="Link Pemutakhiran Data" />
											</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <div class="form-group">
                                            	<label>Pesan</label>
                                            	<textarea name="pesan" id="pesan" class="form-control wysiwyg" required><a href=""><?php echo $tautan; ?></a></textarea>
											</div>
                                        </div>
                                    </div>

									<hr style="margin:15px 0px; border-top:4px double #ddd;" />

                                    <div style="margin-bottom:0px;">
                                        <input type="hidden" name="act" value="<?php echo $action;?>" />
                                        <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                        <button type="submit" name="btnSbmt" id="btnSbmt" class="btn btn-primary jarak-kanan" style="min-width:90px;">
                                        <i class="fa fa-envelope jarak-kanan"></i> Kirim</button> 
                                        <a class="btn btn-default" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT."/customer-generate-link.php";?>">
                                        <i class="fa fa-reply jarak-kanan"></i> Batal</a>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>




            <?php } ?>
			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
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

<style type="text/css">
	h3.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
	}
</style>
<script>
$(document).ready(function(){
	$(window).on("load resize", function(){
		if($(this).width() < 977){
			$(".vertical-tab").addClass("collapsed-box");
			$(".vertical-tab").find(".box-tools").show();
			$(".vertical-tab > .vertical-tab-body").hide();
		} else{
			$(".vertical-tab").removeClass("collapsed-box");
			$(".vertical-tab").find(".box-tools").hide();
			$(".vertical-tab > .vertical-tab-body").show();
		}
	});
	$(".wysiwyg").ckeditor();

	var formValidasiCfg = {
		submitHandler: function(form) {
			$("#loading_modal").modal({keyboard:false, backdrop:'static'});

			for(instance in CKEDITOR.instances){
				CKEDITOR.instances[instance].updateElement();
			}

			if($("#cekkolnup").is(":checked") && $("#nup_fee").val() == ""){
				$("#loading_modal").modal("hide");
				$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
				setErrorFocus($("#nup_fee"), $("form#gform"), false);
			} else if($("#pesan").val() == ""){
				$("#loading_modal").modal("hide");
				swal.fire({
					allowOutsideClick: false, icon: "warning", width: '350px',
					html:'<p style="font-size:14px; font-family:arial;">Kolom Pesan belum diisi</p>'
				});
			} else{
				form.submit();
			}
		}	
	};
	$("form#gform").validate($.extend(true,{},config.validation,formValidasiCfg));
});		
</script>
</body>
</html>      
