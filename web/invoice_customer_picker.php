<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$conSub = new Connection();
	$flash	= new FlashAlerts;
	$sesgr	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);

?>
<div class="wrap-table-histori-status">
    <form name="searchFormModal" id="searchFormModal" role="form" class="form-horizontal" method="post">
    <div class="form-group row">
        <div class="col-sm-6">
            <input type="text" class="form-control input-sm" placeholder="Keywords" name="q1Modal" id="q1Modal" />
        </div>
        <div class="col-sm-4 col-md-5 col-sm-top">
            <button type="submit" class="btn btn-info btn-sm" name="btnSearchModal" id="btnSearchModal"><i class="fa fa-search jarak-kanan"></i> Search</button>
        </div>
    </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered" id="table-histori-status">
            <thead>
                <tr>
                    <th class="text-center" width="80">No</th>
                    <th class="text-center" width="250">Nama Customer</th>
                    <th class="text-center" width="">Alamat Customer</th>
                    <th class="text-center" width="150">Cabang Penagihan</th>
                    <th class="text-center" width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<?php $conSub->close(); $conSub = NULL; ?>
<style type="text/css">
	.wrap-table-histori-status .box-footer{
		padding: 0px;
		border: none;
	}
	.wrap-table-histori-status .box-footer .col-sm-5,
	.wrap-table-histori-status .box-footer .col-md-7,
	.wrap-table-histori-status .box-footer .col-sm-5,
	.wrap-table-histori-status .box-footer .col-md-7{
		width: 100%;
	}
	.wrap-table-histori-status .text-left-rsp,
	.wrap-table-histori-status .text-right-rsp{
		text-align: center !important;
		font-family: arial;
		font-size: 11px;
	}
	
	/*#table-histori-status > thead > tr > th,
	#table-histori-status > tbody > tr > td{
		font-family: arial;
		font-size: 11px;
		padding: 5px;
	}*/
</style>

<script>
$(document).ready(function(){
	$("#table-histori-status").ajaxGrid({
		url	: "./invoice_customer_picker_list.php",
		data : {q1 : $("#q1Modal").val()},
		infoPageCenter : false,
		modal : ".wrap-table-histori-status",
	});	
	$("#btnSearchModal").on('click', function(){
		$("#table-histori-status").ajaxGrid("draw", {data : {q1 : $("#q1Modal").val()}}); 
		return false;
	});
	
	$("#table-histori-status").on("click", ".btn-pilih", function(){
		let index = $(this).data('detail');
		let param = index.toString().split('|#|');
		$("#id_customer").val(decodeURIComponent(param[0]));
		$("#nm_customer").val(decodeURIComponent(param[1]));
		$("#user_modal").modal("hide");
	});

});		
</script>
