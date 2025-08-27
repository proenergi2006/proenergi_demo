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
	$sesgr	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
    $seswil  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $sesrol  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
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
        		<h1>Persetujuan PO ke PR</h1>
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
                    <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="keywords..." />
                </div>
				<div class="col-sm-3 col-sm-top" <?=($sesrol=='7'?'hidden':'')?>>
                    <select id="q2" name="q2" class="form-control select2">
                        <option></option>
                        <?php $con->fill_select("id_master","nama_cabang","pro_master_cabang",$seswil,"where is_active=1 and id_master <> 1","nama_cabang",false); ?>
                    </select>
                </div>
				<div class="col-sm-3 col-sm-top">
					<div class="input-group">
						<span class="input-group-addon">Periode</span>
                    	<input type="text" name="q3" id="q3" class="form-control input-sm validate[required,custom[date]] datepicker" autocomplete = 'off'/>
                    </div>
				</div>
				<div class="col-sm-3 col-sm-top">
					<div class="input-group">
						<span class="input-group-addon">S/D</span>
						<input type="text" name="q4" id="q4" class="form-control input-sm validate[required,custom[date]] datepicker" autocomplete = 'off'/>
                    </div>
				</div>
            </div>
			<div class="form-group row">
				<div class="col-sm-12">
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
                                        <th class="text-center" width="10%">No</th>
                                        <th class="text-center" width="20%">Kode Dokumen</th>
                                        <th class="text-center" width="20%">Tanggal Issued</th>
                                        <th class="text-center" width="40%">Cabang Invoice</th>
                                        <th class="text-center" width="10%">Aksi</th>
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

<script>
$(document).ready(function(){
	$("select#q2").select2({ placeholder: "Cabang", allowClear: true });
	$("#table-grid").ajaxGrid({
		url	 : "./datatable/po-customer-om.php",
		data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val(), q4 : $("#q4").val()},
	});
	$('#btnSearch').on('click', function(){
		$("#table-grid").ajaxGrid("draw", {data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val(), q4 : $("#q4").val()}}); 
		return false;
	});
	$('#tableGridLength').on('change', function(){
		$("#table-grid").ajaxGrid("pageLen", $(this).val());
	});

});
</script>
</body>
</html>      
