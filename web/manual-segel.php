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
	$cek = "select urut_segel, inisial_segel, stok_segel from pro_master_cabang where id_master = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_wilayah"])."'";
	$row = $con->getRecord($cek);
	$sg1 = ($row['urut_segel'])?$row['inisial_segel']."-".str_pad($row['urut_segel'],4,'0',STR_PAD_LEFT):'Tidak ada';
	$sg2 = ($row['stok_segel'])?number_format($row['stok_segel'],0,'','.'):'Tidak ada';
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("myGrid", "jqueryUI"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Manual Segel</h1>
        	</section>
			<section class="content">

            <?php $flash->display(); ?>
            <div class="alert alert-danger alert-dismissible" style="display:none">
                <div class="box-tools">
                    <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
            <div class="form-group row">
                <div class="col-sm-4">
                    <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
                </div>
                <div class="col-sm-3 col-sm-top">
                	<div class="input-group">
                        <span class="input-group-addon">Tanggal</span>
                        <input type="text" class="form-control input-sm datepicker" name="q2" id="q2" />
					</div>
                </div>
                <div class="col-sm-3 col-sm-top">
                	<div class="input-group">
                        <span class="input-group-addon">S/D</span>
                        <input type="text" class="form-control input-sm datepicker" name="q3" id="q3" />
					</div>
                </div>
                <div class="col-sm-2 col-sm-top">
					<button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search"></i> Cari</button>
                </div>
            </div>
			</form>

            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <div class="row">
                            	<div class="col-sm-6">
                                    <a href="<?php echo BASE_URL_CLIENT.'/manual-segel-add.php'; ?>" class="btn btn-primary">
                                        <i class="fa fa-plus jarak-kanan"></i>Add Data
                                    </a>
                                </div>
                            	<div class="col-sm-6">
                                    <div class="text-right" style="margin-top: 10px">Show 
                                        <select name="tableGridLength" id="tableGridLength">
                                            <option value="10">10</option>
                                            <option value="25" selected>25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>                        
                                        </select> Data
                                    </div>
                                </div>
							</div>
						</div>
                        <div class="box-body">
                        	<p style="font-size:12px;"><i>Nomor Segel Terakhir : <?php echo $sg1; ?><span class="marginX">&nbsp;</span>Stock Segel : <?php echo $sg2; ?></i></p>
                            <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="table-grid">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="8%">NO</th>
                                        <th class="text-center" width="19%">NOMOR BA</th>
                                        <th class="text-center" width="10%">TANGGAL BA</th>
                                        <th class="text-center" width="17%">SEGEL</th>
                                        <th class="text-center" width="23%">PIC</th>
                                        <th class="text-center" width="13%">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            </div>
                        </div>
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
	$("#table-grid").ajaxGrid({
		url	 : "./datatable/manual-segel.php",
		data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val()},
	});
	$('#btnSearch').on('click', function(){
		$("#table-grid").ajaxGrid("draw", {data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val()}}); 
		return false;
	});
	$('#tableGridLength').on('change', function(){
		$("#table-grid").ajaxGrid("pageLen", $(this).val());
	});
	$('#table-grid tbody').on('click', '[data-action="deleteGrid"]', function(e){
		e.preventDefault();
		if(confirm("Apakah anda yakin ?")){
			var param 	= $(this).data("param-idx");
			var handler	= function(data){
				if(data.error == ""){
					$(".alert").slideUp();
					$("#table-grid").ajaxGrid("draw");
				} else{
					$(".alert").slideUp();
					var a = $(".alert > .box-tools");
					a.next().remove();
					a.after("<p>"+data.error+"</p>");
					$(".alert").slideDown();
				}
			};
			$.post("./datatable/deleteTable.php", {param : param}, handler, "json");
		}
	});

});
</script>
</body>
</html>      
