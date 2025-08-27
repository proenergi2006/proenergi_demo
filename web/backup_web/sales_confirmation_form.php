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
	$id 	= isset($enk["id"])?htmlspecialchars($enk["id"], ENT_QUOTES):'';
	$idp 	= isset($enk["idp"])?htmlspecialchars($enk["idp"], ENT_QUOTES):'';
	$idc 	= isset($enk["idc"])?htmlspecialchars($enk["idc"], ENT_QUOTES):'';
	$role 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	
	$cek = "select n.*, s.*, p.supply_date, p.nomor_poc, c.kode_pelanggan, c.nama_customer, c.credit_limit, c.tipe_bisnis, c.tipe_bisnis_lain, p.volume_poc, p.harga_poc, e.fullname as marketing, c.jenis_payment, c.top_payment, p.id_poc 
			from pro_sales_confirmation n 
			left join pro_sales_confirmation_approval s on n.id = s.id_sales
			join pro_customer c on n.id_customer = c.id_customer 
			join acl_user e on e.id_user = c.id_marketing
			left join pro_po_customer p on p.id_poc = n.id_poc 
			where n.id = '".$id."'";
	$row = $con->getRecord($cek);
	$id_poc 	= htmlspecialchars($row["id_poc"], ENT_QUOTES);
	if ($row['not_yet'] == '' or $row['not_yet'] == 0) {
		$cek12 = "select n.*
				from pro_sales_confirmation n
				where n.id_customer = '".$row['id_customer']."'
				order by n.id desc limit 1";
		$row12 = $con->getRecord($cek12);
	}

	$cek2 = "select *
			from pro_sales_confirmation_log
			where id_sales = '".$id."'";
	$row2 = $con->getRecord($cek2);

	$cek3 = "select *
			from pro_sales_colleteral
			where sales_id = '".$id."'";
	$row3 = $con->getResult($cek3);
	
	$arrT = array(1=>"Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", "Finance & Insurance", 
					"Food & hospitally", "Gaming", "Health Services", "Motor Vehicle", $row['tipe_bisnis_lain'],"Natural Resources / Environmental","Personal Service","Manufacture");
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("myGrid","formatNumber","jqueryUI"), "css"=>array("jqueryUI"))); ?>
<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS."/validation/jquery.validationEngine.js"; ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS."/validation/jquery.validationEngine-id.js"; ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo BASE_PATH_JS."/validation/jquery.validationEngine.cfg.js"; ?>"></script>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Sales Confirmation</h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
				<ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                    	<a href="#form-approval" aria-controls="form-approval" role="tab" data-toggle="tab">Form Approval</a>
					</li>
					<li role="presentation" class="">
						<a href="#data-po" aria-controls="data-po" role="tab" data-toggle="tab">Data PO</a>
					</li>
					<?php if($row2 ) { //&& $role == 10?>
						<li role="presentation" class="">
							<a href="#data-approval" aria-controls="data-approval" role="tab" data-toggle="tab">Log Approval</a>
						</li>
					<?php } ?>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="form-approval">
						<div class="row">                
							<div class="col-sm-12">
								<div class="box box-primary">
									<div class="box-body">

										<div class="row">
											<div class="col-sm-12">
												<form action="<?php echo ACTION_CLIENT.'/sales_confirmation_action.php'; ?>" id="gform" name="gform" method="post" role="form">
													<table border="0" cellpadding="0" cellspacing="0" id="table-detail">
														<tr>
															<td>Date/Periode</td>
															<td width="20">:</td>
															<td><?php echo tgl_indo($row['period_date']); ?></td>
														</tr>
														<tr>
															<td>Supply Date</td>
															<td>:</td>
															<td><?php echo tgl_indo($row['supply_date']); ?></td>
														</tr>
													</table> 
													<br>
													<?php
														require_once($public_base_directory."/web/__get_sales_confirmation.php");
													?>
												</form>
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
                    </div>
                    
					<div role="tabpanel" class="tab-pane" id="data-po">
						<div class="row">                
							<div class="col-sm-12">
								<div class="box box-primary">
									<div class="box-body">
										<?php
											require_once($public_base_directory."/web/__sc_get_data_po.php");
										?>										
									</div>
								</div>
							</div>
						</div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="data-approval">
						<div class="row">                
							<div class="col-sm-12">
								<div class="box box-primary">
									<div class="box-body">

										<table border="0" cellpadding="0" cellspacing="0" id="table-detail">
											<tr>
												<td>Date/Periode</td>
												<td width="20">:</td>
												<td><?php echo $row['period_date'] ? tgl_indo($row['period_date']) : '-'; ?></td>
											</tr>
											<tr>
												<td>Supply Date</td>
												<td>:</td>
												<td><?php echo $row['nomor_pr'] ?? null; ?></td>
											</tr>
										</table> 

										<div class="row">
											<div class="col-sm-12">
												<?php
													require_once($public_base_directory."/web/__sales_confirmation_log.php");
												?>
											</div>
										</div>
										
									</div>
								</div>
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

<style type="text/css">
	h3.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
	}
	#table-grid2, #table-long {margin-bottom: 15px;}
	#table-grid2 td, #table-grid2 th {font-size: 11px; font-family: arial;}
	#table-detail {margin-bottom: 10px;}
	#table-detail td { padding-bottom:3px; font-size: 12px;}
</style>
<script>	
	$(document).ready(function(){
		$("form#gform").on("click", "button:submit", function(e){
			if(confirm("Apakah anda yakin?"))
			{
				var required = [];
				var data = [];
				var count = 0;
				// var temp = $(this).validationEngine('detach');
				$(':radio[required]').each(function() {
					if(required.indexOf($(this).attr('name')) < 0)
					{						
						if($(this).is(':checked')){
							required[$(this).attr('name')] = $(this).is(':checked');
							count++;
						}
					}

					if(data.indexOf( $(this).attr('name') ) < 0)
						data.push($(this).attr('name'))
				});
				
				// if(data.length > count){
				// 	alert('Sorry, Fill in all required data please');
				// 	return false;
				// }
				// else{
					// $('#loading_modal').modal({backdrop:"static"});
					$(this).submit();
				// }
			} else{
				return false;
			}
		});
	});
</script>
</body>
</html>      
