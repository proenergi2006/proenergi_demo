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
	
	$sesrole = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	if($sesrole != '21'){ 
		header("location: ".BASE_URL_CLIENT.'/home.php'); exit();
	}
	
	$cek = "select count(*) as jum from (select distinct periode_awal, periode_akhir, id_area, produk from pro_master_harga_minyak where is_evaluated = 1 and is_approved = 0) a";
	$jum = $con->getOne($cek);
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
        		<h1>Daftar Harga Jual</h1>
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
                <div class="col-sm-2">
                    <input type="text" class="form-control input-sm datepicker" name="q1" id="q1" placeholder="Periode.." />
                </div>
                <div class="col-sm-3 col-sm-top">
                    <select name="q2" id="q2" class="form-control">
                        <option></option>
                       <?php $con->fill_select("id_master","nama_area","pro_master_area",'',"where is_active=1","",false); ?>
                    </select>
				</div>
                <div class="col-sm-3 col-sm-top">
                    <select name="q4" id="q4" class="form-control">
                        <option></option>
                        <?php $con->fill_select("id_master","concat(jenis_produk,' - ',merk_dagang)","pro_master_produk",'',"where is_active=1","id_master",false); ?>
                    </select>
				</div>
                <div class="col-sm-2 col-sm-top">
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
                                    <a href="<?php echo BASE_URL_CLIENT.'/add-master-harga-minyak.php'; ?>" class="btn btn-primary jarak-kanan">
                                        <i class="fa fa-plus jarak-kanan"></i>Add Data
                                    </a>
                                    <a href="<?php echo BASE_URL_CLIENT.'/list-approval-harga.php'; ?>" class="btn btn-success">List Approval
                                        <?php echo ($jum > 0)?'<span class="label">'.$jum.'</span>':''; ?>
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
                                        <th class="text-center" width="250">PERIODE</th>
                                        <th class="text-center" width="250">AREA</th>
                                        <th class="text-center" width="200">PRODUK</th>
                                        <?php 
											$ces = "select * from pro_master_pbbkb where id_master = 1";
											$res = $con->getResult($ces);
											if(count($res) > 0){
												foreach($res as $data){
													echo '<th class="text-center" width="">HARGA DASAR</th>';
												}
											}
										?>
                                        <th class="text-center" width="150">AKSI</th>
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
	$("select#q2").select2({ placeholder: "Nama Area", allowClear: true });
	$("select#q4").select2({ placeholder: "Produk", allowClear: true });

	$("#table-grid").ajaxGrid({
		url	 : "./datatable/master-approval-harga.php",
		data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q4 : $("#q4").val()},
	});
	$('#btnSearch').on('click', function(){
		$("#table-grid").ajaxGrid("draw", {data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q4 : $("#q4").val()}}); 
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
