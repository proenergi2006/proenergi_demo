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
        		<h1>Penawaran</h1>
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
                <div class="col-sm-3">
                    <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
				</div>
                <div class="col-sm-3 col-sm-top">
                    <select id="q2" name="q2" class="form-control">
                        <option></option>
                        <?php $con->fill_select("id_master","nama_cabang","pro_master_cabang",$rsm['id_cabang'],"where is_active=1 and id_master <> 1","",false); ?>
                    </select>
                </div>
                <div class="col-sm-3 col-sm-top">
                    <select id="q3" name="q3" class="form-control">
                        <option></option>
                        <?php $con->fill_select("id_master","nama_area","pro_master_area","","where is_active=1","",false); ?>
                    </select>
                </div>
                <div class="col-sm-3 col-sm-top">
                    <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
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
                                            <option value="10" selected>10</option>
                                            <option value="25">25</option>
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
                                        <th class="text-center" width="17%">No. Ref</th>
                                        <th class="text-center" width="25%">Customer</th>
                                        <th class="text-center" width="10%">Cabang Invoice</th>
                                        <th class="text-center" width="10%">Area</th>
                                        <th class="text-center" width="10%">Volume</th>
                                        <th class="text-center" width="15%">Disposisi</th>
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
	#table-grid td, #table-grid th { font-size:12px; }
</style>
<script>
	$(document).ready(function(){
		$("select#q2").select2({placeholder: "Cabang Invoice", allowClear:true});
		$("select#q3").select2({placeholder: "Area", allowClear:true});
	
		$("#table-grid").ajaxGrid({
			url	 : "./datatable/penawaran-approval.php",
			data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val()},
		});
		$('#btnSearch').on('click', function(){
			$("#table-grid").ajaxGrid("draw", {data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val()}}); 
			return false;
		});
		$('#tableGridLength').on('change', function(){
			$("#table-grid").ajaxGrid("pageLen", $(this).val());
		});
	});
</script>
</body>
</html>      
