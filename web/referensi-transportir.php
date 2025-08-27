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
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber", "jqueryUI", "myGrid"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Data Referensi</h1>
        	</section>
			<section class="content">

            <?php $flash->display(); ?>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#data-plat" aria-controls="data-plat" role="tab" data-toggle="tab">Truck</a></li>
                <li role="presentation" class=""><a href="#data-sopir" aria-controls="data-sopir" role="tab" data-toggle="tab">Sopir</a></li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="data-plat">
                    <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-info btn-sm" name="btnSearch1" id="btnSearch1"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    </form>

                    <div class="row">
                        <div class="col-sm-10 col-md-8">
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="text-right" style="margin-top: 10px">Show 
                                                <select name="tableGridLength1" id="tableGridLength1">
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
                                    <table class="table table-bordered" id="data-plat-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="12%">No</th>
                                                <th class="text-center" width="53%">Truck</th>
                                                <th class="text-center" width="20%">KAPASITAS (MAX)</th>
                                                <th class="text-center" width="15%">Active</th>
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

                <div role="tabpanel" class="tab-pane" id="data-sopir">
                    <form name="searchForm2" id="searchForm2" role="form" class="form-horizontal">
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="text" class="form-control input-sm" name="q2" id="q2" placeholder="Keywords..." />
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-info btn-sm" name="btnSearch2" id="btnSearch2"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    </form>

                    <div class="row">
                        <div class="col-sm-8">
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="text-right" style="margin-top: 10px">Show 
                                                <select name="tableGridLength2" id="tableGridLength2">
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
                                    <table class="table table-bordered" id="data-sopir-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="15%">No</th>
                                                <th class="text-center" width="65%">Sopir</th>
                                                <th class="text-center" width="20%">Active</th>
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

            </div>

			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<script>
$(document).ready(function(){
	$("#data-plat-table").ajaxGrid({
		url	 : "./datatable/referensi-transportir-mobil.php",
		data : {q1 : $("#q1").val()},
		infoPageCenter : true,
	});
	$("#btnSearch1").on("click", function(){
		$("#data-plat-table").ajaxGrid("draw", {data : {q1 : $("#q1").val()}}); 
		return false;
	});
	$('#tableGridLength1').on('change', function(){
		$("#data-plat-table").ajaxGrid("pageLen", $(this).val());
	});

	$("[data-toggle='tab']").click(function(){
		var $this 	= $(this);
		var idnya 	= $this.attr('href');
		var urlnya 	= (idnya == "#data-plat")?"./datatable/referensi-transportir-mobil.php":"./datatable/referensi-transportir-sopir.php";
		$(idnya+"-table").ajaxGrid({
			url	 : urlnya,
			data : {q1 : ""},
			infoPageCenter : true,
		});		
		$this.tab('show');
		return false;	
	});

	$("#btnSearch2").on("click", function(){
		$("#data-sopir-table").ajaxGrid("draw", {data : {q1 : $("#q2").val()}}); 
		return false;
	});
	$('#tableGridLength2').on('change', function(){
		$("#data-sopir-table").ajaxGrid("pageLen", $(this).val());
	});

});
</script>
</body>
</html>      
