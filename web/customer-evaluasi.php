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
    $sesrol = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$sqljum	= "select count(a.id_customer) as jum from pro_customer a where a.status_customer = 2
			and timestampdiff(month, a.prospect_customer_date, date_format(now(),'%Y/%m/%d')) >= 3 and a.prospect_evaluated = 0";
    if ($sesrol == 18) {
        if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $sqljum .= " and a.id_wilayah = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])."'";
        else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
            $sqljum .= " and a.id_group = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group'])."'";
    } else if ($sesrol == 17 || $sesrol == 11) {
        $sqljum .= " and a.id_marketing = '".paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user'])."'";
    }
	$jumlah = $con->getOne($sqljum);
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
        		<h1>Customer Evaluasi</h1>
        	</section>
			<section class="content">

            <?php $flash->display(); ?>
            <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
                <div class="form-group row">
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i></button>
                            </span>
						</div>
                    </div>
                </div>
			</form>

            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <div class="row">
                            	<div class="col-sm-6">
                                    <a href="<?php echo BASE_URL_CLIENT.'/customer-evaluasi-list.php'; ?>" class="btn btn-primary">Customer Evaluasi
									<?php echo ($jumlah > 0)?'<span class="label">'.$jumlah.'</span>':''; ?></a>
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
                            <table class="table table-bordered table-hover" id="table-grid">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="10%">No</th>
                                        <th class="text-center" width="10%">Tanggal Terdaftar</th>
                                        <th class="text-center" width="10%">Kode Evaluasi</th>
                                        <th class="text-center" width="10%">Kode Customer</th>
                                        <th class="text-center" width="24%">Customer</th>
                                        <th class="text-center" width="30%">Status</th>
                                        <th class="text-center" width="6%">Aksi</th>
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

<style type="text/css">
	.table > thead > tr > th,
	.table > tbody > tr > td{
		font-size:12px;
	}
</style>
<script>
$(document).ready(function(){
	$("#table-grid").ajaxGrid({
		url	 : "./datatable/customer-evaluasi.php",
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
