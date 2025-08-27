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
    $id_role  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
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
        		<h1>Marketing Report</h1>
        	</section>
			<section class="content">
            <?php $flash->display(); ?>
            <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
            <div class="form-group row">
                <div class="col-sm-3">
                    <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
				</div>
				<div class="col-sm-3 col-sm-top">
                    <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i> Search</button>
                    <a href="javascript:;" id="btnExport" class="btn btn-success btn-sm jarak-kanan">Export File to Excel</a>
                </div>
			</div>
            <p style="font-size:12px;"><i>* Keywords</i></p>
			</form>

            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <div class="row">
                            	<div class="col-sm-6">
                                    <?php if ($id_role==11 || $id_role==17) { ?>
                                    <a href="<?php echo BASE_URL_CLIENT.'/marketing-report-add.php'; ?>" class="btn btn-primary">
                                        <i class="fa fa-plus jarak-kanan"></i>Add Data
                                    </a>
                                    <?php }?>
                                </div>
                            	<div class="col-sm-6">
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
                            <table class="table table-bordered table-hover" id="data-marketing-report-table">
                                <thead>
                                    <!-- <tr>
                                        <th class="text-center" width="10%" rowspan="2">No</th>
                                        <th class="text-center" width="10%" rowspan="2">Date</th>
                                        <th class="text-center" width="10%" colspan="3">Profil Customer</th>
                                        <th class="text-center" width="20%" colspan="2">Kegiatan Marketing</th>
                                        <th class="text-center" width="10%" rowspan="2">PIC</th>
                                        <th class="text-center" width="20%" colspan="2">Kontak</th>
                                        <?php //if ($id_role=='11' || $id_role=='17') { ?>
                                        <th class="text-center" width="20%" rowspan="2">Tech Support</th>
                                        <th class="text-center" width="5%" rowspan="2"></th>
                                        <th class="text-center" width="5%" rowspan="2"></th>
                                        <?php// } else if ($id_role=='7' || $id_role=='6') { ?>
                                        <th class="text-center" width="20%" rowspan="2">Marketing</th>
                                        <th class="text-center" width="10%" rowspan="2">Kehadiran</th>
                                        <?php //}?>
                                    </tr> -->
                                    <tr>
                                        <th class="text-center" width="3%" style="background: white;">No</th>
                                        <th class="text-center" width="20%" style="background: white;">Nama Customer</th>
                                        <th class="text-center" width="10%" style="background: white;">Tanggal</th>
                                        <th class="text-center" width="30%" style="background: white;">Kegiatan</th>
                                        <th class="text-center" width="10%" style="background: white;">Status</th>
                                        <th class="text-center" width="10%" style="background: white;">Aksi</th>
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
	#data-marketing-report-table td, #data-marketing-report-table th {font-size:12px;}
</style>
<script>
$(document).ready(function(){
	$("#data-marketing-report-table").ajaxGrid({
		url	 : "./datatable/marketing-report.php",
		data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val()},
	});
	$('#btnSearch').on('click', function(){
		var param = {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val()};
		$("#data-marketing-report-table").ajaxGrid("draw", {data : param}); 
		return false;
	});
	$('#tableGridLength').on('change', function(){
		$("#data-marketing-report-table").ajaxGrid("pageLen", $(this).val());
	});
	$('#data-marketing-report-table').on('click', '[data-action="deleteGrid"]', function(e){
		e.preventDefault();
		if(confirm("Apakah anda yakin ?")){
			var param 	= $(this).data("param-idx");
			var handler	= function(data){
				if(data.error == ""){
					$(".alert").slideUp();
					$("#data-marketing-report-table").ajaxGrid("draw");
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
    $('#data-marketing-report-table').on('click', '[data-action="confirmGrid"]', function(e){
        e.preventDefault();
        if(confirm("Apakah anda yakin ?")){
            var _url   = $(this).data("param-url")
            var param   = $(this).data("param-idr")
            $.get(_url+param, location.reload())
        }
    });
	$('#q3').on('change', function(){ 
        let value = $(this).val()
        let url = window.location.href.split('?')[0]
        window.location = url+'?c='+value
    })
    $('#btnExport').on('click', function () {
        let url = '<?php echo BASE_URL_CLIENT ?>/report/e-export-marketing-report-xls.php';
        window.open(url, '_blank');
    });
});
</script>
</body>
</html>      
