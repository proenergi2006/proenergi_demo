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
	$idv 	= isset($enk["idv"])?htmlspecialchars($enk["idv"], ENT_QUOTES):'';
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
	$sqlCek = "select a.nama_customer, a.credit_limit, a.alamat_customer, a.telp_customer, a.fax_customer, a.email_customer, a.need_update, b.nama_kab, c.nama_prov from pro_customer a 
			   join pro_master_kabupaten b on a.kab_customer = b.id_kab join pro_master_provinsi c on a.prov_customer = c.id_prov where a.id_customer = '".$idk."'";
	$resCek = $con->getRecord($sqlCek);
	$alamat = $resCek['alamat_customer']." ".str_replace(array("KABUPATEN ","KOTA "), array("",""), $resCek['nama_kab'])." ".$resCek['nama_prov'];
	$tautan = 'Kepada Yth.<br />'.$resCek['nama_customer'].'<br />Di Tempat
	<p></p>
	<p>Dengan ini kami memberitahukan bahwa Credit Limit yang Bapak/Ibu ajukan telah kami setujui dengan nominal Rp.'.number_format($resCek['credit_limit']).'</p><br/>
	<p>Salam,<br />Managemen PT. Pro Energi</p>';
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("ckeditor"))); ?>
<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Send Email Review Customer</h1>
        	</section>
			<section class="content">

				<?php if($enk['idk'] !== '' && isset($enk['idk'])){ ?>
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
                                <form action="<?php echo ACTION_CLIENT.'/send-email-customer.php'; ?>" id="gform" name="gform" class="form-validasi" method="post">
                                    <div class="row">
                                        <div class="col-sm-10 col-md-8">
                                            <div class="form-group">
                                            	<label>Kepada</label>
                                            	<input type="text" name="to" id="to" class="form-control validate[required]" value="<?php echo $resCek['email_customer'];?>" />
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
                                            	<input type="text" name="judul" id="judul" class="form-control" value="Information for Credit Limit" />
											</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <div class="form-group">
                                            	<label>Pesan</label>
                                            	<textarea name="pesan" id="pesan" class="form-control wysiwyg"><a href=""><?php echo $tautan; ?></a></textarea>
											</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" name="idr" value="<?php echo $idk;?>" />
												<?php 	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 11)
															$link = "/customer-review-detail.php?".paramEncrypt("idr=".$idv."&idk=".$idr);
														else 
															$link = "/verifikasi-data-customer-detail.php?".paramEncrypt("idr=".$idv);
												?>
													<a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT.$link;?>">
													<i class="fa fa-reply jarak-kanan"></i>Kembali</a>
                                                <button type="button" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-envelope-o jarak-kanan"></i>Kirim</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>
			<?php $con->close(); ?>
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
    </style>
	<script>
		$(document).ready(function(){
			$("#btnSbmt").click(function(e){
				$('#loading_modal').modal({backdrop:"static"});
				e.preventDefault();

				var p1 = new Promise(function(resolve, reject){
					$.ajax({
						type: "post",
						url: "<?php echo ACTION_CLIENT.'/send-email-customer.php';?>",
						data: $('#gform').serialize(),
						success: function(data){
							resolve(data);
						},
						error: function (error) {
							reject(error);
						}
					});
				}).then(function(result){
					$('#loading_modal').modal('hide');
					data = JSON.parse(result);
					alert(data['message']);
				});
				
			});			

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
		});		
	</script>
</body>
</html>      
