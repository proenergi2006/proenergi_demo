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
	$id_poc_sc = array();
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("myGrid","formatNumber"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Delivery Request Detail</h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                    	<a href="#form-pr" aria-controls="form-pr" role="tab" data-toggle="tab">Data DR</a>
					</li>
                    <li role="presentation" class="">
                    	<a href="#form-ar" aria-controls="form-ar" role="tab" data-toggle="tab">Data AR</a>
                    </li>
                    <li role="presentation" class="">
                    	<a href="#form-note" aria-controls="form-note" role="tab" data-toggle="tab">Persetujuan</a>
					</li>
                </ul>
                
                <form action="<?php echo ACTION_CLIENT.'/purchase-request-all.php'; ?>" id="gform" name="gform" method="post" role="form">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="form-pr">
                        <div class="row">                
                            <div class="col-sm-12">
                                <div class="box box-primary">
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-sm-12">                                                
                                                <?php require_once($public_base_directory."/web/__get_data_pr_cfo_all.php"); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>

                    <div role="tabpanel" class="tab-pane" id="form-ar">
                        <div class="row">                
                            <div class="col-sm-12">
								<?php //require($public_base_directory."/web/__get_data_ar_all.php"); ?>
								<?php require($public_base_directory."/web/__get_data_sc_all.php"); ?>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="form-note">
                        <div class="row">                
                            <div class="col-sm-12">
								<?php require($public_base_directory."/web/__get_data_pr_note_all.php"); ?>
                            </div>
                        </div>
                    </div>
                
                </div>
                </form>



            <div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Peringatan</h4>
                        </div>
                        <div class="modal-body">
                        	<div id="preview_alert" class="text-center"></div>
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

<style type="text/css">
	h3.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
	}
	#table-long, #table-grid2, #table-grid3, .table-detail, .table-ar-grid {margin-bottom: 15px;}
	#table-grid2 td, #table-grid2 th {
		font-size:11px; 
		font-family:arial; 
	}

	#table-grid3 th,
	#table-grid3 td {
		font-size: 11px; 
		font-family:arial; 
	}
	
	.table-detail td { 
		padding-bottom:3px; 
		font-size: 12px;
	}
	.table-ar-grid > thead > tr > th,
	.table-ar-grid > tbody > tr > td{
		font-size: 11px; 
		font-family:arial;
	}
	.table-ar-grid > thead > tr > th{
		padding:8px 5px;
	}
</style>
<script>
	$(document).ready(function(){
		$(".hitung").number(true, 0, ".", ",");
		$("#cekAll").on("ifChecked", function(){
			$(".chkp").iCheck("check");
			console.log('asas')
		}).on("ifUnchecked", function(){
			$(".chkp").iCheck("uncheck");
		});

		$(".revert").on("ifChecked", function(){
			var nilai = $(this).val();
			var idnya = $(this).attr("id").substr(7);
			if(nilai == 1){
				$("textarea[name='summary"+idnya+"']").attr('readonly', 'readonly');
				$("textarea[name='summary_revert"+idnya+"']").removeAttr('readonly');
				$("input[name='extend"+idnya+"']").iCheck('disable');
			} else if(nilai == 2){
				$("textarea[name='summary"+idnya+"']").removeAttr('readonly');
				$("textarea[name='summary_revert"+idnya+"']").attr('readonly', 'readonly');
				$("input[name='extend"+idnya+"']").iCheck('enable');
			}
		});

		$("form#gform").on("click", "#btnSbmt", function(){
			if(confirm("Apakah anda yakin?")){
				if($("#gform").find("input.chkp:checked").length > 0){
					$("#loading_modal").modal({backdrop:"static"});
					$.ajax({
						type	: 'POST',
						url		: "./__cek_pr_customer_purchasing.php",
						dataType: "json",
						data	: $("#gform").serializeArray(),
						cache	: false,
						success : function(data){
							if(data.error){
								$(".nav-tabs").children("li").removeClass("active")
								$(".nav-tabs a[href='#form-pr']").parents("li").addClass("active");
								$(".tab-pane").removeClass("active")
								$("#form-pr").addClass('active');

								$("#preview_modal").find("#preview_alert").html(data.error);
								$("#preview_modal").modal();
								$("#loading_modal").modal("hide");					
								return false;
							} else{
								$("form#gform").submit();
							}
						}
					});
					return false;
				} else{
					$("#preview_modal").find("#preview_alert").text("Data DR Belum dipilih..");
					$("#preview_modal").modal();					
					return false;
				}
			} else return false;
		});

		var x,y,top,left,down;
		$("#table-long").mousedown(function(e){
			if(e.target.nodeName != "INPUT" && e.target.nodeName != "SELECT"){
				down = true;
				x = e.pageX;
				y = e.pageY;
				top = $(this).scrollTop();
				left = $(this).scrollLeft();
			}
		});			
		$("body").mousemove(function(e){
			if(down){
				var newX = e.pageX;
				var newY = e.pageY;
				$("#table-long").scrollTop(top-newY+y);    
				$("#table-long").scrollLeft(left-newX+x);    
			}
		});
		$("body").mouseup(function(e){down=false;});
	});		
</script>
</body>
</html>      
