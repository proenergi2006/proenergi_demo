<?php
// session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$enk     = decode($_SERVER['REQUEST_URI']);
$con     = new Connection();
$flash   = new FlashAlerts;
$arrBln = array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

$q1 = (isset($enk['q1']) && $enk['q1'] ? htmlspecialchars($enk['q1'], ENT_QUOTES) : NULL);
$q2 = (isset($enk['q2']) && $enk['q2'] ? htmlspecialchars($enk['q2'], ENT_QUOTES) : date('m'));
$q3 = (isset($enk['q3']) && $enk['q3'] ? htmlspecialchars($enk['q3'], ENT_QUOTES) : date('Y'));
$q4 = (isset($enk['q4']) && $enk['q4'] ? htmlspecialchars($enk['q4'], ENT_QUOTES) : NULL);
$q5 = (isset($enk['display']) && $enk['display'] ? 1 : 0);

include_once($public_base_directory . "/web/__get_inventory_stock.php");

?>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<style>
	@import "https://code.highcharts.com/css/highcharts.css";

	.highcharts-pie-series .highcharts-point {
		stroke: #ede;
		stroke-width: 2px;
	}

	.highcharts-pie-series .highcharts-data-label-connector {
		stroke: silver;
		stroke-dasharray: 2, 2;
		stroke-width: 2px;
	}

	.highcharts-figure,
	.highcharts-data-table table {
		min-width: 320px;
		max-width: 600px;
		margin: 1em auto;
	}

	.highcharts-data-table table {
		font-family: Verdana, sans-serif;
		border-collapse: collapse;
		border: 1px solid #ebebeb;
		margin: 10px auto;
		text-align: center;
		width: 100%;
		max-width: 500px;
	}

	.highcharts-data-table caption {
		padding: 1em 0;
		font-size: 1.2em;
		color: #555;
	}

	.highcharts-data-table th {
		font-weight: 600;
		padding: 0.5em;
	}

	.highcharts-data-table td,
	.highcharts-data-table th,
	.highcharts-data-table caption {
		padding: 0.5em;
	}

	.highcharts-data-table thead tr,
	.highcharts-data-table tr:nth-child(even) {
		background: #f8f8f8;
	}

	.highcharts-data-table tr:hover {
		background: #f1f7ff;
	}

	.amcharts-chart-div a[href="https://www.amcharts.com/"] {
		display: none !important;
	}

	/* Sembunyikan teks "JavaScript chart by amCharts" */
	.amcharts-chart-div div[aria-label="JavaScript chart by amCharts"] {
		display: none !important;
	}
</style>


<h3>PO VS Realisasi</h3>
<div class="row">
	<div class="col-sm-12">
		<form method="post">
			<div class="box box-info">
				<div class="box-header with-border">
					<div class="row">
						<div class="col-sm-6">
							<div style="font-size:12px;"><b>Cabang</b></div>
						</div>

					</div>

					<div class="row">
						<div class="col-sm-4">

							<select name="q4" id="q4" class="form-control select2">
								<option></option>
								<?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", '', "where is_active=1 and id_master <> 1", "nama_cabang", false); ?>

							</select>
							<p></p>
							<?php
							// Menangkap nilai tanggal yang telah dipilih setelah pencarian
							$selectedBranchId  = isset($_POST['q4']) ? $_POST['q4'] : '';


							if (!empty($selectedBranchId)) {
								// Mengambil data cabang dari database
								$query = "Select nama_cabang FROM pro_master_cabang WHERE id_master = $selectedBranchId";
								$result = $con->getRecord($query);
								$selectedBranchName = $result['nama_cabang'];
							}
							$startDate = isset($_POST['q5']) ? $_POST['q5'] : '';
							$endDate = isset($_POST['q6']) ? $_POST['q6'] : '';
							?>
							<p style="font-size:12px;"><i>(* Silahkan Pilih Cabang Dan Periode Dahulu)</i></p>
							<p style="font-size:12px;"><b>Cabang : <?php echo  $selectedBranchName; ?> </b></p>
							<p style="font-size:12px;"><b>Periode : <?php echo $startDate; ?> - <?php echo $endDate; ?> </b></p>
						</div>
						<div class="col-sm-5">
							<label>Periode</label>


							<input type="text" name="q5" id="q5" class="datepicker input-cr-sm" autocomplete='off' /> <label>S/D</label>
							<input type="text" name="q6" id="q6" class="datepicker input-cr-sm" autocomplete='off' />
						</div>


						<div class="col-sm-3">


							<button type="submit" class="btn btn-success btn-sm"> Search</button>

						</div>

					</div>

				</div>
			</div>
		</form>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<div class="box box-info content-data-search">
			<div class="box-header with-border">
				<div class="row">
					<div class="col-md-6">

						<label style="font-size:large;">Marketing PO</label>



						<div id="container"></div>

					</div>
					<div class="col-md-6">

						<label style="font-size:large;">Customer PO</label>

						<div id="container2"></div>

					</div>
				</div>
				<hr>

				<div class="row">
					<div class="col-md-6">
						<label style="font-size:large;">Realisasi Marketing</label>
						<div id="container3"></div>

					</div>
					<div class="col-md-6">
						<label style="font-size:large;">Realisasi Customer</label>
						<div id="container4"></div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Dev Iwan AR Customer -->

<form name="searchForm" id="searchForm" role="form" class="form-horizontal">
	<div class="row" hidden>
		<div class="col-sm-12">
			<div class="box box-info">
				<div class="box-header with-border">
					<div class="row">
						<div class="col-sm-4">
							<input type="text" class="form-control input-sm" name="q1" id="q1" placeholder="Keywords Customer Name, Marketing" />
						</div>

						<div class="col-sm-4">
							<button type="submit" class="btn btn-sm btn-info" name="btnSearch" id="btnSearch"> <i class="fa fa-search jarak-kanan"></i> Search</button>
							<a href="<?php echo BASE_URL_CLIENT . '/export.php'; ?>" class="btn btn-info btn-sm">
								<i class="fa fa-plus jarak-kanan"></i>More
							</a>
						</div>
					</div>
				</div>
</form>
<div class="box-body table-responsive">
	<table class="table table-bordered" id="table-grid1">
		<thead>
			<tr>
				<th class="text-center" width="80">No</th>
				<th class="text-center" width="250">Customer</th>
				<th class="text-center" width="100">TOP</th>
				<th class="text-center" width="120">Credit Limit</th>
				<th class="text-center" width="120">Not Yet</th>
				<th class="text-center" width="">Overdue</th>
				<th class="text-center" width="150">Reminding</th>
				<th class="text-center" width="150">Total AR</th>
			</tr>
		</thead>
		<tbody>

		</tbody>
	</table>
</div>
</div>
</div>
</div>




<?php include_once($public_base_directory . "/web/__sc_inventory_stock.php"); ?>