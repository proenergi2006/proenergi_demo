<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth = new MyOtentikasi();
$con = new Connection();
$flash = new FlashAlerts;
if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(1, 2, 3, 4, 6, 7, 15))) {
	$graph = 'volume';
	if (isset($_GET['graph']))
		$graph = $_GET['graph'];
	require_once($public_base_directory . "/web/home_head_" . $graph . ".php");
} else
	$enk = decode($_SERVER['REQUEST_URI']);
// echo json_encode(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah'])); die();
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "rating", "myGrid"), "css" => array("jqueryUI", "rating"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Home</h1>
			</section>

			<section class="content">

				<?php $flash->display(); ?>
				<?php
				if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(11, 17))) {
					require_once($public_base_directory . "/web/pengiriman-list-customer.php");
				}
				if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(9))) {
					require_once($public_base_directory . "/web/dashboard_log.php");
					// require_once($public_base_directory . "/web/maps_monitoring.php");
				}
				if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(18))) {
					require_once($public_base_directory . "/web/dashboard_cs.php");
					// require_once($public_base_directory . "/web/maps_monitoring.php");
				}
				if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(16))) {
					require_once($public_base_directory . "/web/dashboard_mgr_log.php");
					require_once($public_base_directory . "/web/graph_mgr_log.php");
				}
				if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(10))) {
					require_once($public_base_directory . "/web/schedule_payment.php");
				}
				if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(4, 15))) {
					require_once($public_base_directory . "/web/schedule_payment_list.php");
				}
				if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(21))) {
					require_once($public_base_directory . "/web/inventory_stock.php");
				}

				if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(1))) {
					require_once($public_base_directory . "/web/inventory_stock_super.php");
				}
				if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(3, 6))) {
					require_once($public_base_directory . "/web/inventory_stock_coo.php");
				}
				if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(7))) {
					require_once($public_base_directory . "/web/inventory_stock_bm.php");
				}
				if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(5))) {
					require_once($public_base_directory . "/web/inventory_stock_pr.php");
				}

				?>

				<!-- <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(1, 2, 3, 4, 6, 7, 15))) { ?>
			<div class="form-group row">
				<div class="col-sm-1 col-sm-top">
		            <label>Source: </label>
		        </div>
				<div class="col-sm-2 col-sm-top">
	            	<select id="graph" name="graph" class="form-control validate[required] select2">
	                    <option value="volume" <?= ($graph == 'volume' ? 'selected' : '') ?>>Volume</option>
	                    <option value="po" <?= ($graph == 'po' ? 'selected' : '') ?>>PO Customer</option>
	                </select>
	            </div>
	        </div>
			<?php //require_once($public_base_directory."/web/home_body_".$graph.".php"); 
			?>
            <?php } ?> -->

				<?php $con->close(); ?>
			</section>
			<?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
		</aside>
	</div>


	<script>
		$(document).ready(function() {
			// $("#table-grid").ajaxGrid({
			// 	url	 : "./datatable/refund-truck.php",
			// 	data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val()},
			// });
			// $("#btnSearch1").on("click", function(){
			// 	$("#table-grid").ajaxGrid("draw", {data : {q1 : $("#q1").val(), q2 : $("#q2").val(), q3 : $("#q3").val()}}); 
			// 	return false;
			// });
			// $('#tableGridLength1').on('change', function(){
			// 	$("#table-grid").ajaxGrid("pageLen", $(this).val());
			// });
			$('#table-grid tbody').on('click', '.editStsT', function(e) {
				var param = $(this).data("param");
				$("#status_bayar").find("#idLP").val(param);
				$("#status_bayar").find("#tgl_bayar, #keterangan").val("");
				$("#status_bayar").find("#errStatLP").html("");
				$("#status_bayar").modal();
			});

			$("#status_bayar").on("click", "#btnLP1", function() {
				if ($("#tgl_bayar").val() == "") {
					$("#errStatLP").html('<p class="text-red">Tanggal Bayar harus diisi...</p>');
				} else {
					if (confirm("Apakah anda yakin ?")) {
						var tipe = $("#tipeLP").val(),
							idnya = $("#idLP").val(),
							keterangan = $("#keterangan").val(),
							tgl_bayar = $("#tgl_bayar").val();
						// $("#loading_modal").modal({backdrop:"static"});
						$("#status_bayar").modal("hide");
						$.ajax({
							type: 'POST',
							url: "./action/status_bayar_po.php",
							data: {
								"keterangan": keterangan,
								"tgl_bayar": tgl_bayar,
								"param": idnya
							},
							cache: false,
							dataType: "json",
							success: function(data) {
								if (data.status = '1') {
									alert(data.msg);
									$("#loading_modal").modal("hide");
									$("#status_bayar").ajaxGrid("draw");
									location.reload();
								} else {
									alert(data.msg);
									$("#loading_modal").modal("hide");
									$("#status_bayar").ajaxGrid("draw");
								}
							}
						});
					} else return false;
				}
			});
		});
		$('#graph').on('change', function() {
			let val = $(this).val()
			location.href = window.location.origin + window.location.pathname + '?graph=' + val
		})
	</script>
	<?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(1, 2, 3, 4, 6, 7, 15))) { ?>
		<?php require_once($public_base_directory . "/web/home_bottom_" . $graph . ".php"); ?>
	<?php } ?>
</body>

</html>