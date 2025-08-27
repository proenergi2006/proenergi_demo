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
    $sesrole  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
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
        		<h1>Marketing Reimbursement</h1>
        	</section>
			<section class="content">
            <h4 class="text-muted" style="font-style: italic;" hidden>Under Construction</h4>
            <?php $flash->display(); ?>
            <form name="searchForm" id="searchForm" role="form" class="form-horizontal" style="display: ;">
            <div class="form-group row">
                <div class="col-sm-3">
                    <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
				</div>
				<div class="col-sm-3 col-sm-top">
                    <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i> Search</button>
				</div>
			</div>
            <p style="font-size:12px;"><i>* Keywords</i></p>
			</form>

            <div class="row" style="display: ;">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <div class="row">
                            	<div class="col-sm-6">
                                    <?php if ($sesrole==11 || $sesrole==17) { ?>
                                    <a href="<?php echo BASE_URL_CLIENT.'/marketing-reimbursement-add.php'; ?>" class="btn btn-primary">
                                        <i class="fa fa-plus jarak-kanan"></i>Add Data
                                    </a>
                                    <?php } ?>
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
                            <table class="table table-bordered table-hover" id="data-marketing-reimbursement-table">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="10%">No</th>
                                        <th class="text-center" width="10%">Tanggal</th>
                                        <th class="text-center" width="10%">No Polisi</th>
                                        <th class="text-center" width="10%">User</th>
                                        <th class="text-center" width="10%">KM Awal</th>
                                        <th class="text-center" width="10%">KM Akhir</th>
                                        <th class="text-center" width="10%">Total</th>
                                        <th class="text-center" width="5%"></th>
                                        <?php if ($sesrole==11 || $sesrole==17) { ?>
                                        <th class="text-center" width="5%"></th>
                                        <th class="text-center" width="5%"></th>
                                        <?php } ?>
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
	#data-marketing-reimbursement-table td, #data-marketing-reimbursement-table th {font-size:12px;}
</style>
<script>
$(document).ready(function(){
	$("#data-marketing-reimbursement-table").ajaxGrid({
		url	 : "./datatable/marketing-reimbursement.php",
		data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val()},
	});
	$('#btnSearch').on('click', function(){
		var param = {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val()};
		$("#data-marketing-reimbursement-table").ajaxGrid("draw", {data : param}); 
		return false;
	});
	$('#tableGridLength').on('change', function(){
		$("#data-marketing-reimbursement-table").ajaxGrid("pageLen", $(this).val());
	});
	$('#data-marketing-reimbursement-table').on('click', '[data-action="deleteGrid"]', function(e){
		e.preventDefault();
		if(confirm("Apakah anda yakin ?")){
			var param 	= $(this).data("param-idx");
			var handler	= function(data){
				if(data.error == ""){
					$(".alert").slideUp();
					$("#data-marketing-reimbursement-table").ajaxGrid("draw");
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
	$('#q3').on('change', function(){ 
        let value = $(this).val()
        let url = window.location.href.split('?')[0]
        window.location = url+'?c='+value
    })
});
</script>
</body>
</html>      
