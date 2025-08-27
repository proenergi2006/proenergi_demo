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
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("myGrid","jqueryUI"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Harga Tebus</h1>
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
                    <select name="q2" id="q2" class="form-control">
                        <option></option>
                        <?php $con->fill_select("id_master","concat(jenis_produk,' - ',merk_dagang)","pro_master_produk",'',"where is_active=1","id_master",false); ?>
                    </select>
				</div>
                <div class="col-sm-3 col-sm-top">
                    <select name="q3" id="q3" class="form-control">
                        <option></option>
                        <?php $con->fill_select("id_master","nama_area","pro_master_area",'',"where is_active=1","id_master",false); ?>
                    </select>
				</div>
                <div class="col-sm-3 col-sm-top">
                    <select name="q4" id="q4" class="form-control">
                        <option></option>
                        <?php $con->fill_select("id_master","nama_vendor","pro_master_vendor",'',"where is_active=1","id_master",false); ?>
                    </select>
				</div>
                <div class="col-sm-3 col-sm-top">
                    <select name="q5" id="q5" class="form-control">
                        <option></option>
                        <?php $con->fill_select("id_master","concat(nama_terminal,' ',tanki_terminal,', ',lokasi_terminal)","pro_master_terminal",'',"where is_active=1","id_master",false); ?>
                    </select>
				</div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3">
                    <input type="text" class="form-control input-sm datepicker" name="q1" id="q1" placeholder="Periode.." />
                </div>
                <div class="col-sm-9 col-sm-top">
                    <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
                </div>
            </div>
			</form>

            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <div class="row">
                            	<div class="col-sm-6">
                                    <?php /*if($sesrol == '5'){ ?>
                                    <a href="<?php echo BASE_URL_CLIENT.'/add-master-harga-tebus.php'; ?>" class="btn btn-primary">
                                        <i class="fa fa-plus jarak-kanan"></i>Add Data
                                    </a>
                                    <?php }*/ ?>
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
                            <table class="table table-bordered table-hover2" id="table-grid">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="180">PERIODE</th>
                                        <th class="text-center" width="150">PRODUK</th>
                                        <th class="text-center" width="150">AREA</th>
                                        <th class="text-center" width="230">VENDOR</th>
                                        <th class="text-center" width="">TERMINAL</th>
                                        <th class="text-center" width="130">HARGA TEBUS</th>
                                        <th class="text-center" width="100">AKSI</th>
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
	.table > tbody > tr > td { padding:5px; }
</style>
<script>
$(document).ready(function(){
	$("select#q2").select2({ placeholder: "Produk", allowClear: true });
	$("select#q3").select2({ placeholder: "Area", allowClear: true });
	$("select#q4").select2({ placeholder: "Vendor", allowClear: true });
	$("select#q5").select2({ placeholder: "Terminal", allowClear: true });

	$("#table-grid").ajaxGrid({
		url	 : "./datatable/master-harga-tebus.php",
		data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val(), q4 : $("#q4").val(), q5 : $("#q5").val()},
	});
	$('#btnSearch').on('click', function(){
		$("#table-grid").ajaxGrid("draw", {data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val(), q4 : $("#q4").val(), q5 : $("#q5").val()}}); 
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
