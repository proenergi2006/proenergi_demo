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

	$arrKategoriPerubahan = array(
		1=>"Perubahan Credit Limit",
		"Perubahan TOP",
		"Perubahan Data",
		"Perubahan Credit Limit & Data Customer",
		"Perubahan TOP & Data Customer",
		"Perubahan Credit Limit & TOP",
		"Perubahan Credit Limit & TOP & Data Customer",
	);

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
        		<h1>Permohonan Update Data Customer</h1>
        	</section>
			<section class="content">

            <?php $flash->display(); ?>
            <div class="alert alert-danger alert-dismissible" style="display:none">
                <div class="box-tools">
                    <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-3">Kata Kunci</label>
                            <div class="col-md-6">
                                <input type="text" name="q1" id="q1" class="form-control input-sm" placeholder="Keywords..." />
                            </div>
						</div>
					</div>
				</div>

                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-3">Kategori</label>
                            <div class="col-md-6">
                                <select name="q2" id="q2" class="form-control select2" style="width:100%">
                                    <option></option>
									<?php 
                                        foreach($arrKategoriPerubahan as $idx=>$val){
                                            $selected = ($rsm['kategori'] == $idx ? 'selected' : '');
                                            echo '<option value="'.$idx.'" '.$selected.'>'.$val.'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
						</div>
					</div>
				</div>

                <div class="form-group row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i> Pencarian</button>
                    </div>                
                </div>
			</form>

            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

            <div class="box box-info">
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-sm-6">
                            <a href="<?php echo BASE_URL_CLIENT.'/customer-permohonan-update-add.php'; ?>" class="btn btn-primary">
                                <i class="fa fa-plus jarak-kanan"></i>Add Permohonan
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
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover" id="table-grid">
                        <thead>
                            <tr>
                                <th class="text-center" width="80">No</th>
                                <th class="text-center" width="100">Tanggal</th>
                                <th class="text-center" width="300">Customer</th>
                                <th class="text-center" width="">Judul</th>
                                <th class="text-center" width="200">Status</th>
                                <th class="text-center" width="60"><i class="fa fa-paperclip"></i></th>
                                <th class="text-center" width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
		url	 : "./datatable/customer-permohonan-update.php",
		data : {q1 : $("#q1").val(), q2 : $("#q2").val()},
	});
	$('#btnSearch').on('click', function(){
		$("#table-grid").ajaxGrid("draw", {data : {q1 : $("#q1").val(), q2 : $("#q2").val()}}); 
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
