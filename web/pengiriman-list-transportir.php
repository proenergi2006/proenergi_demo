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
	$cek = "select tipe_angkutan from pro_master_transportir where id_master = '".paramDecrypt($_SESSION["sinori".SESSIONID]["suplier"])."'";
	$akt = $con->getOne($cek);
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
        		<h1>List Pengiriman</h1>
        	</section>
			<section class="content">

            <?php $flash->display(); ?>
            <form name="searchForm" id="searchForm" role="form" class="form-horizontal">
                <div class="form-group row">
                    <div class="col-sm-3">
                        <input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords..." />
                    </div>
                    <div class="col-sm-3 col-sm-top">
                        <select id="q2" name="q2" class="form-control">
                            <option></option>
                            <option value="1">Tanggal Issued PO</option>
                            <option value="2">Tanggal Kirim</option>
                            <option value="3">Tanggal ETL</option>
                        </select>
                    </div>
                    <div class="col-sm-3 col-sm-top">
                        <div class="input-group">
                            <span class="input-group-addon">Periode</span>
                            <input type="text" name="q3" id="q3" class="form-control input-sm datepicker" disabled />
                        </div>
                    </div>
                    <div class="col-sm-3 col-sm-top">
                        <div class="input-group">
                            <span class="input-group-addon">S/D</span>
                            <input type="text" name="q4" id="q4" class="form-control input-sm datepicker" disabled />
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <select id="q5" name="q5" class="form-control">
                            <option></option>
                            <option value="3">Delivered</option>
                            <option value="4">Cancel</option>
                        </select>
                    </div>
                    <div class="col-sm-9 col-sm-top">
                        <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch" style="width:80px;">Cari</button>
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
                            <table class="table table-bordered" id="data-truck-table">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="7%">No</th>
                                        <th class="text-center" width="24%">Customer</th>
                                        <th class="text-center" width="16%">Detail PO</th>
                                        <th class="text-center" width="15%">Transportir</th>
                                        <th class="text-center" width="16%">Depot/ Seal</th>
                                        <th class="text-center" width="17%">Status</th>
                                        <th class="text-center" width="5%">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="status_history_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-histori">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Histori Status Pengiriman</h4>
                        </div>
                        <div class="modal-body">
							<p id="jdlKirim"></p>
                            <div class="table-responsive">
                            	<form name="modal-form-histori" id="modal-form-histori">
                                <table class="table table-bordered" id="listHistoriLP">
                                	<thead>
                                    	<tr>
                                        	<th class="text-center" width="50">No</th>
                                        	<th class="text-center" width="180">Tanggal</th>
                                        	<th class="text-center" width="">Status</th>
                                        	<th class="text-center" width="45"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                </form>
                            </div>
                            <div id="detilHistoriLp"></div>
						</div>
                    </div>
                </div>
            </div>
            
            <div class="modal fade" id="status_kirim_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Status Pengiriman</h4>
                        </div>
                        <div class="modal-body">
                            <div id="infoStatLP1"></div>
                            <div id="errStatLP"></div>
                            <table class="table no-border" style="margin-bottom:10px;">
                            	<tbody>
                                	<tr>
                                    	<td style="padding:0px 5px;" width="100">
                                        	<p style="font-weight:bold; margin-bottom:5px;">Tanggal</p>
                                            <input type="text" name="dt1" id="dt1" class="input-sm datepicker form-control" />
                                        </td>
                                    	<td width="10">&nbsp;</td>
                                    	<td style="padding:0px 5px;">
                                        	<p style="font-weight:bold; margin-bottom:5px;">Jam</p>
                                            <select name="dt2" id="dt2" style="height:30px; line-height:1.5; width:50px;">
                                            	<option></option>
                                            	<?php for($i=0;$i<24;$i++) echo '<option>'.str_pad($i,2,'0',STR_PAD_LEFT).'</option>';?>
                                        	</select>
                                            <span style="font-size:14px; padding:0px 2px;">:</span> 
                                            <select name="dt3" id="dt3" style="height:30px; line-height:1.5; width:50px;">
                                            	<option></option>
                                            	<?php for($j=0;$j<60;$j++) echo '<option>'.str_pad($j,2,'0',STR_PAD_LEFT).'</option>'; ?>
                                        	</select>
                                            <span style="font-size:14px; padding:0px 5px;">&nbsp;</span>
                                            <a class="btn btn-sm btn-info" id="load_now_modal">NOW</a>                                       
                                        </td>
									</tr>
                                	<tr>
                                    	<td colspan="3" style="padding:10px 5px;">
                                        	<p style="font-weight:bold; margin-bottom:5px;">Status</p>
                                            <input type="text" name="stat_kirim" id="stat_kirim" class="form-control" />
                                        </td>
									</tr>
                                </tbody>
                            </table>
                            <hr style="border-top: 4px double #ccc; margin: 10px 0px 15px;">
                            <div class="text-left">
                                <input type="hidden" name="idLP" id="idLP" value="" />
                                <input type="hidden" name="tipeLP" id="tipeLP" value="" />
                                <button type="button" class="btn btn-default jarak-kanan" data-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-primary jarak-kanan" name="btnLP1" id="btnLP1" value="1">Update Status</button>
                                <button type="button" class="btn btn-success jarak-kanan" name="btnLP2" id="btnLP2" value="1">Pengiriman Selesai</button>
                            </div>
						</div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="error_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Informasi</h4>
                        </div>
                        <div class="modal-body"><p class="text-center" id="error-preview"></p></div>
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

<style type="text/css">
	.table{
		border:1px solid #ddd;
		margin-bottom:15px;
		border-collapse:collapse;
		border-spacing:0px;
	}
	.table > thead > tr > th, 
	.table > tbody > tr > td{
		border:1px solid #ddd;
		padding: 5px;
		font-size:11px;
		font-family:arial;
		vertical-align:top;
	}
	.table > thead > tr > th{
		background-color: #f4f4f4;
		vertical-align: middle;
		padding: 8px 5px;
	}
	a.editStsT, a.fa-ubah-sts{
		padding: 0px 7px;
	}
	a.fa-simpan-sts{
		padding: 3px 10px;
	}
	div.status-kirim{
		padding-top: 5px;
	}
	div.status-kirim p{
		padding-right: 30px;
		margin-bottom: 3px;
		font-weight: bold;
		text-decoration: underline;
	}
	.input-list, .input-date{
		border: 1px solid #ccc;
		height: auto;
		padding: 5px;
		-webkit-border-radius: 0;
				border-radius: 0;
	}
	.input-date{
		width: 70px;
	}
	.input-list{
		width: 100%;
	}
	@media screen and (min-width: 992px) {
		.modal-dialog-histori{
			width: 70%;
		}
	}
</style>
<script>
$(document).ready(function(){
	$("select#q2").select2({placeholder: "Pilih Tanggal", allowClear:true});
	$("select#q5, select#q4k").select2({placeholder: "Pilih Status", allowClear:true});
	$("select#q2").on("change", function(){
		if($(this).val() == "") $("#q3, #q4").val("").prop("disabled", "disabled");
		else $("#q3, #q4").removeProp("disabled");
	});

	$("#data-truck-table").ajaxGrid({
		url	 : "./datatable/pengiriman-list-transportir-truck.php",
		data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val(), q4 : $("#q4").val(), q5 : $("#q5").val()},
	});
	$("#btnSearch").on("click", function(){
		$("#data-truck-table").ajaxGrid("draw", {data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val(), q4 : $("#q4").val(), q5 : $("#q5").val()}}); 
		return false;
	});
	$('#tableGridLength1').on('change', function(){
		$("#data-truck-table").ajaxGrid("pageLen", $(this).val());
	});

	$('#data-truck-table tbody').on('click', '.editStsT', function(e){
		var param = $(this).data("param");
		var infor = $(this).data("info");

		$.post("./datatable/get_info_loading.php", {param:infor}, function(data){
			$("#status_kirim_modal").find("#infoStatLP1").html(data);
			$("#status_kirim_modal").find("#idLP").val(param);
			$("#status_kirim_modal").find("#tipeLP").val("1");
			$("#status_kirim_modal").modal();
		});
	});
	
	$("#status_kirim_modal").on("shown.bs.modal", function(){
		$("#status_history_modal").modal("hide");
	}).on("hidden.bs.modal", function(){
		$("#status_kirim_modal").find("#dt1, #dt2, #dt3, #idLP, #tipeLP, #stat_kirim").val("");
		$("#status_kirim_modal").find("#errStatLP, #infoStatLP1").html("");
	}).on("click", "#load_now_modal", function(){
		var handler	= function(data){
			$("#status_kirim_modal").find("#dt1").val(data.tanggal);
			$("#status_kirim_modal").find("#dt2").val(data.jam);
			$("#status_kirim_modal").find("#dt3").val(data.menit);
		};
		$.post("./datatable/get_tanggal.php", {}, handler, "json");
	}).on("click", "#btnLP1", function(){
		if($("#stat_kirim").val() == "" || $("#dt1").val() == "" || $("#dt2").val() == "" || $("#dt3").val() == ""){
			$("#errStatLP").html('<p class="text-red">Status, tanggal dan jam harus diisi...</p>');
		} else{
			var tipe = $("#tipeLP").val(), idnya = $("#idLP").val(), status = $("#stat_kirim").val(), dt1 = $("#dt1").val(), dt2 = $("#dt2").val(), dt3 = $("#dt3").val();
			$("#loading_modal").modal({backdrop:"static"});
			$("#status_kirim_modal").modal("hide");
			$.ajax({
				type	: 'POST',
				url		: "./action/pengiriman-list.php",
				data	: {"file":"logistik", "aksi":"ubah", "status":status, "dt1":dt1, "dt2":dt2, "dt3":dt3, "param":idnya, "tipe":tipe},
				cache	: false,
				dataType: "json",
				success : function(data){
					$("#loading_modal").modal("hide");					
					$("#data-truck-table").ajaxGrid("draw");
					if(data.error){
						$("#error_modal").find("#error-preview").html(data.error);
						$("#error_modal").modal();
					}
				}
			});
		}
	}).on("click", "#btnLP2", function(){
		if(confirm("Produk telah diterima customer.\nApakah anda yakin ?")){
			if($("#dt1").val() == "" || $("#dt2").val() == "" || $("#dt3").val() == ""){
				$("#errStatLP").html('<p class="text-red">Tanggal dan jam harus diisi...</p>');
			} else{
				var tipe = $("#tipeLP").val(), idnya = $("#idLP").val(), dt1 = $("#dt1").val(), dt2 = $("#dt2").val(), dt3 = $("#dt3").val();
				$("#loading_modal").modal({backdrop:"static"});
				$("#status_kirim_modal").modal("hide");
				$.ajax({
					type	: 'POST',
					url		: "./action/pengiriman-list.php",
					data	: {"file":"logistik", "aksi":"selesai", "dt1":dt1, "dt2":dt2, "dt3":dt3, "param":idnya, "tipe":tipe},
					cache	: false,
					dataType: "json",
					success : function(data){
						$("#loading_modal").modal("hide");					
						$("#data-truck-table").ajaxGrid("draw");
						if(data.error){
							$("#error_modal").find("#error-preview").html(data.error);
							$("#error_modal").modal();
						}
					}
				});
			}
		}
	});


	$('#data-truck-table tbody').on('click', '.listStsT', function(e){
		var param = $(this).data("param");
		$("#loading_modal").modal({backdrop:"static"});
		$.ajax({
			type	: 'POST',
			url		: "./__get_pengiriman_list.php",
			data	: {"file":"transportir", "aksi":param},
			cache	: false,
			dataType: "json",
			success : function(data){
				$("#loading_modal").modal("hide");
				$("#status_history_modal").find("#listHistoriLP > tbody").html(data.items);
				$("#status_history_modal").find("#jdlKirim").html(data.judul);
				$("#status_history_modal").find("#detilHistoriLp").html(data.extras);
				$(".input-date").datepicker({
					dateFormat: 'dd/mm/yy',
					changeMonth: true,
					changeYear: true,
					yearRange: "c-80:c+10",
					dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
					monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
				});
			}
		});
		$("#status_history_modal").modal();
	});
	$("#status_history_modal").on("shown.bs.modal", function(){
		$("#status_kirim_modal").modal("hide");
	}).on("hidden.bs.modal", function(){
		$("#status_history_modal").find("#listHistoriLP > tbody, #jdlKirim").html("");
	}).on("click", ".fa-ubah-sts", function(){
		var idx = $(this).data("idx");
		$(".histori-form"+idx).removeClass("hide");
		$(".histori-text"+idx).addClass("hide");
	}).on("click", ".fa-simpan-sts", function(){
		var jns	= $(this).data("jns"), idnya = $(this).data("ids");
		var prm = $("#modal-form-histori").serializeArray();
		prm.push({name: 'file', value: 'logtrans'},{name: 'aksi', value: 'ubah'},{name: 'param', value: idnya},{name: 'tipe', value: jns});

		$("#loading_modal").modal({backdrop:"static"});
		$.ajax({
			type	: 'POST',
			url		: "./action/pengiriman-list.php",
			data	: prm,
			cache	: false,
			success : function(data){
				if(data){ 
					$("#data-truck-table").ajaxGrid("draw");
					$("#status_history_modal").find("#listHistoriLP > tbody").html(data);
					$(".input-date").datepicker({
						dateFormat: 'dd/mm/yy',
						changeMonth: true,
						changeYear: true,
						yearRange: "c-80:c+10",
						dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
						monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
					});
				}
				$("#loading_modal").modal("hide");
			}
		});
	});

});
</script>
</body>
</html>      
