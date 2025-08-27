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
	$arrBln = array(1=>"Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
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
        		<h1>PO Suplier</h1>
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
                    <select name="q3" id="q3" class="form-control">
                        <option></option>
                        <?php $con->fill_select("id_master","concat(jenis_produk,' - ',merk_dagang)","pro_master_produk",'',"where is_active=1","id_master",false); ?>
                    </select>
				</div>
                <div class="col-sm-3 col-sm-top">
                    <select name="q5" id="q5" class="form-control">
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
                    <select name="q6" id="q6" class="form-control">
                        <option></option>
                        <?php $con->fill_select("id_master","concat(nama_terminal,'#',tanki_terminal,'#',lokasi_terminal)","pro_master_terminal",'',"where is_active=1","id_master",false); ?>
                    </select>
				</div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3">
                    <select name="q1" id="q1" class="form-control">
                        <option></option>
                        <?php 
							foreach($arrBln as $idOpt=>$rsOpt){
								echo '<option value="'.$idOpt.'">'.$rsOpt.'</option>';
							}
						?>
                    </select>
                </div>
                <div class="col-sm-3 col-sm-top">
                    <select name="q2" id="q2" class="form-control">
                        <option></option>
                        <?php 
							for($asd = date("Y"); $asd > date("Y")-5; $asd--){
								echo '<option value="'.$asd.'">'.$asd.'</option>';
							}
						?>
                    </select>
				</div>
                <div class="col-sm-6 col-sm-top">
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
                                    <a href="<?php echo BASE_URL_CLIENT.'/vendor-po-add.php'; ?>" class="btn btn-primary">
                                        <i class="fa fa-plus jarak-kanan"></i>Add Data
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
                            <form action="<?php echo ACTION_CLIENT.'/master-harga-tebus.php'; ?>" id="gform" name="gform" method="post">
                            <table class="table table-bordered table-hover" id="table-grid">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="9%">Tanggal</th>
                                        <th class="text-center" width="15%">No PO</th>
                                        <th class="text-center" width="12%">Area/ Produk</th>
                                        <th class="text-center" width="27%">Vendor/ Terminal</th>
                                        <th class="text-center" width="9%">Volume<br/>(Liter)</th>
                                        <th class="text-center" width="9%">Harga Tebus</th>
                                        <th class="text-center" width="5%">Per. 1</th>
                                        <th class="text-center" width="5%">Per. 2</th>
                                        <th class="text-center" width="9%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <div class="pad bg-gray">
                                <button type="submit" class="btn btn-success" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                            </div>                            	
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <h4 class="modal-title">Loading Data ...</h4>
                        </div>
                        <div class="modal-body text-center modal-loading"></div>
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
	$("select#q1").select2({ placeholder: "Bulan", allowClear: true });
	$("select#q2").select2({ placeholder: "Tahun", allowClear: true });
	$("select#q3").select2({ placeholder: "Produk", allowClear: true });
	$("select#q4").select2({ placeholder: "Vendor", allowClear: true });
	$("select#q5").select2({ placeholder: "Area", allowClear: true });
	$("select#q6").select2({
		placeholder	: "Terminal",
		allowClear	: true,
		templateResult : function(repo){ 
			if(repo.loading) return repo.text;
			var text1 = repo.text.split("#");
			var $returnString = $('<span>'+text1[0]+(text1[1]?' - '+text1[1]:'')+(text1[2]?', '+text1[2]:'')+'</span>');
			return $returnString;
		},
		templateSelection : function(repo){ 
			var text1 = repo.text.split("#");
			var $returnString = $('<span>'+text1[0]+(text1[1]?' - '+text1[1]:'')+(text1[2]?', '+text1[2]:'')+'</span>');
			return $returnString;
		},
	});

	var objAttach = {
		onValidationComplete: function(form, status){
			if(status == true){
				$('#loading_modal').modal({backdrop:"static"});
				form.validationEngine('detach');
				form.submit();
			}
		}
	};
	$("form#gform").validationEngine('attach',objAttach);	
	
	$(document).ajaxComplete(function(){
		$("input[type='checkbox']").iCheck({checkboxClass: 'icheckbox_square-blue'});	
	});
	$("#table-grid").ajaxGrid({
		url	 : "./datatable/vendor-po.php",
		data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val(), q4 : $("#q4").val(), q5 : $("#q5").val(), q6 : $("#q6").val()},
	});
	$('#btnSearch').on('click', function(){
		$("#table-grid").ajaxGrid("draw", {data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val(), q4 : $("#q4").val(), q5 : $("#q5").val(), q6 : $("#q6").val()}}); 
		return false;
	});
	$('#tableGridLength').on('change', function(){
		$("#table-grid").ajaxGrid("pageLen", $(this).val());
	});
	$('#table-grid tbody').on('click', '[data-action="deleteGrid"]', function(e){
		e.preventDefault();
		if(confirm("Menghapus data pembelian akan menghapus data harga tebus jika periode 1 atau 2 terpilih.\nApakah anda yakin?")){
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
