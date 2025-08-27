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
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("myGrid"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Evaluasi Data Customer</h1>
        	</section>
			<section class="content">

            <?php $flash->display(); ?>
            <div class="alert alert-danger alert-dismissible" style="display:none">
                <div class="box-tools">
                    <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
            <div class="form-group">
                <div class="col-sm-6 col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-info" name="btnSearch" id="btnSearch"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
				</div>
            </div>
			</form>

            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <div class="row">
                            	<div class="col-sm-12">
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
                        <div class="box-body table-responsive">
                            <table class="table table-bordered table-hover" id="table-grid">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="8%">No</th>
                                        <th class="text-center" width="12%">Kode Evaluasi</th>
                                        <th class="text-center" width="12%">Kode Pelanggan</th>
                                        <th class="text-center" width="21%">Nama Customer</th>
                                        <th class="text-center" width="25%">Alamat</th>
                                        <th class="text-center" width="17%">Status</th>
                                        <th class="text-center" width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style>
	.table > thead > tr > th,
	.table > tbody > tr > td {
		font-size:12px;
	}
</style>
<script>
$(document).ready(function(){
	$("#table-grid").ajaxGrid({
		url	 : "./datatable/evaluasi-data-customer.php",
		data : {q1 : $("#q1").val()},
	});
	$('#btnSearch').on('click', function(){
		$("#table-grid").ajaxGrid("draw", {data : {q1 : $("#q1").val()}}); 
		return false;
	});
	$('#tableGridLength').on('change', function(){
		$("#table-grid").ajaxGrid("pageLen", $(this).val());
	});
});
</script>
</body>
</html>      
