<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;

$sesuser 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$sesrole 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$seswil 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesgroup 	= paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "formhelper"), "css" => array("jqueryUI", "formhelper"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory . "/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
		<aside class="right-side">
			<section class="content-header">
				<h1>Peminjaman Zoom</h1>
			</section>

			<section class="content">
				<?php $flash->display(); ?>
				<noscript>
					<h3>
						<center>Javascript anda tidak aktif mohon aktifkan javascript browser anda.</center>
					</h3>
				</noscript>

				<div class="form-horizontal">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group form-group-sm">
								<label class="control-label col-md-3">Cabang *</label>
								<div class="col-md-9">
									<select id="id_cabang" name="id_cabang" class="form-control select2" style="width:100%;" disabled>
										<option></option>
										<?php $con->fill_select("id_master", "nama_cabang", "pro_master_cabang", 1, "where is_active=1", "id_master", false); ?>
									</select>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8">
							<div class="form-group form-group-sm">
								<label class="control-label col-md-3">Zoom *</label>
								<div class="col-md-9">
									<select id="id_zoom" name="id_zoom" class="form-control select2" style="width:100%;">
										<option></option>
										<?php
										$sqlnya01 = "select id_zoom, nama_zoom, id_cabang from pro_master_zoom where is_active = 1";

										$sqlnya01 .= " and id_cabang = '1'";

										$resnya01 = $con->getResult($sqlnya01);

										if (count($resnya01) > 0) {
											foreach ($resnya01 as $idx01 => $data01) {

												echo '<option value="' . $data01['id_zoom'] . '" data-cabang="' . $data01['id_cabang'] . '">' . $data01['nama_zoom'] . '</option>';
											}
										}
										?>
									</select>
								</div>
							</div>
						</div>
					</div>
					<!-- <?php if ($sesrole == '1' || $sesrole == '14') { ?>
						
					<?php } else { ?>
						<div class="row">
							<div class="col-md-8">
								<div class="form-group form-group-sm">
									<label class="control-label col-md-3">Zoom *</label>
									<div class="col-md-9">
										<select id="id_zoom" name="id_zoom" class="form-control select2" style="width:100%;">
											<option></option>
											<?php
											$sqlnya01 = "select id_zoom, nama_zoom, id_cabang from pro_master_zoom where is_active = 1";

											$sqlnya01 .= " and id_cabang = '" . $seswil . "'";

											$resnya01 = $con->getResult($sqlnya01);

											if (count($resnya01) > 0) {
												foreach ($resnya01 as $idx01 => $data01) {

													echo '<option value="' . $data01['id_zoom'] . '" data-cabang="' . $data01['id_cabang'] . '">' . $data01['nama_zoom'] . '</option>';
												}
											}
											?>
										</select>
									</div>
								</div>
							</div>
						</div>
					<?php } ?> -->

					<div class="row gambarnya hide">
						<div class="col-md-8">
							<div class="form-group form-group-sm">
								<label class="control-label col-md-3">&nbsp;</label>
								<div class="col-md-9">
									<div class="gambarnya-foto"></div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8">
							<div class="form-group form-group-sm">
								<label class="control-label col-md-3">&nbsp;</label>
								<div class="col-md-9">
									<button type="button" class="btn btn-sm btn-primary btn-caridata" name="btnSbmt01" id="btnSbmt01" style="min-width:120px;">
										Lihat Reservasi</button>
								</div>
							</div>
						</div>
					</div>

				</div>

				<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

				<div class="box no-shadow" style="border-top-width:0px;">
					<div class="box-body no-padding">
						<div id="calendarnya">
							<input type="hidden" name="defaultDate" id="defaultDate" value="<?php echo date("Y-m-d"); ?>" />
							<div id="calendar" class="fcc-calendar no-margin"></div>
							<div id="calendar-error" class="hide">
								<span class="pull-left">
									<div class="cl-error-icon"><i class="fa fa-times"></i></div>
								</span>
								<div class="bg-red cl-error-message">Calendar Error</div>
							</div>
						</div>
					</div>
				</div>

				<?php $con->close(); ?>
			</section>
			<?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
		</aside>
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

	<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<a class="btn btn-sm btn-default close" data-dismiss="modal" style="font-size:12px; background-color:#f7f7f7; opacity:1;">
						<i class="fa fa-times"></i></a>
					<h4 class="modal-title">Informasi Data</h4>
				</div>
				<div class="modal-body">
					<div class="table-responsive">
						<table class="table no-border table-info-event no-margin">
							<tbody>
								<tr id="wrap-event-name">
									<td width="100">Reservasi Oleh</td>
									<td class="text-center" width="15">:</td>
									<td id="info-event-name"></td>
								</tr>
								<tr id="wrap-event-ruangan">
									<td>Zoom</td>
									<td class="text-center">:</td>
									<td id="info-event-ruangan"></td>
								</tr>
								<tr id="wrap-event-start">
									<td>Tanggal Mulai</td>
									<td class="text-center">:</td>
									<td id="info-event-start"></td>
								</tr>
								<tr id="wrap-event-end">
									<td>Tanggal Selesai</td>
									<td class="text-center">:</td>
									<td id="info-event-end"></td>
								</tr>
								<tr id="wrap-event-departmen">
									<td>Departmen</td>
									<td class="text-center">:</td>
									<td id="info-event-departmen"></td>
								</tr>
								<tr id="wrap-event-keperluan">
									<td>Keperluan</td>
									<td class="text-center">:</td>
									<td id="info-event-keperluan"></td>
								</tr>
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr style="border-top:4px double #ddd;">
									<td colspan="3">&nbsp;</td>
								</tr>

							</tbody>
						</table>
					</div>

					<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

					<div id="info-event-tombol" style="margin-bottom:5px;">
						<a class="btn btn-sm btn-primary jarak-kanan" id="edit-event" data-eventid="" style="min-width:90px;">Ubah</a>
						<a class="btn btn-sm btn-danger" id="hapus-event" data-eventid="" style="min-width:90px;">Hapus</a>
					</div>
					<div class="modal-loading-new"></div>

				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="agenda_form_modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<h4 class="modal-title">Jadwal Agenda</h4>
				</div>
				<div class="modal-body">
					<form id="gform" name="gform" method="post" role="form" class="form-horizontal">
						<div class="row">
							<div class="col-md-10">
								<div class="form-group form-group-sm">
									<label class="control-label col-md-3">Zoom *</label>
									<div class="col-md-9">
										<input type="text" id="nama_zoom" name="nama_zoom" class="form-control" readonly />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-10">
								<div class="form-group form-group-sm">
									<label class="control-label col-md-3">Tanggal Reservasi *</label>
									<div class="col-md-4">
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											<input type="text" id="tanggal_reservasi" name="tanggal_reservasi" class="form-control datepicker" required data-rule-dateNL="1" />
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-10">
								<div class="form-group form-group-sm">
									<label class="control-label col-md-3">Waktu Mulai *</label>
									<div class="col-md-4">
										<div class="asb-timepicker" data-id="jam_mulai" data-name="jam_mulai" data-classname="form-control"></div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-10">
								<div class="form-group form-group-sm">
									<label class="control-label col-md-3">Waktu Selesai *</label>
									<div class="col-md-4">
										<div class="asb-timepicker" data-id="jam_selesai" data-name="jam_selesai" data-classname="form-control"></div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-10">
								<div class="form-group form-group-sm">
									<label class="control-label col-md-3">Departemen *</label>
									<div class="col-md-9">
										<input type="text" id="departmen" name="departmen" class="form-control" />
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-10">
								<div class="form-group form-group-sm">
									<label class="control-label col-md-3">Keperluan *</label>
									<div class="col-md-9">
										<textarea id="keperluan" name="keperluan" class="form-control" required style="height:90px;"></textarea>
									</div>
								</div>
							</div>
						</div>


						<hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

						<div style="margin-bottom:15px;">
							<input type="hidden" id="act" name="act" />
							<input type="hidden" id="idr" name="idr" />
							<input type="hidden" id="idk" name="idk" />
							<input type="hidden" id="idc" name="idc" />
							<button type="button" class="btn btn-primary jarak-kanan" id="btnSbmt02" name="btnSbmt02" style="min-width:90px;">
								<i class="fa fa-save jarak-kanan"></i> Simpan</button>
							<a class="btn btn-default" data-dismiss="modal" style="min-width:90px;"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
						</div>
					</form>
					<div class="modal-loading-new"></div>

				</div>
			</div>
		</div>
	</div>

	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL . '/libraries/themes/calendar02/style.fullcalendar.css'; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL . '/libraries/themes/calendar02/style.fullcalendar.ext.css'; ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo BASE_URL . '/libraries/js/calendar02/jquery.moment.min.js'; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_URL . '/libraries/js/calendar02/fullcalendar.js'; ?>"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BASE_URL . '/libraries/js/calendar02/id.js'; ?>"></script>

	<style type="text/css">
		meter,
		meter::-webkit-meter-bar {
			/*to remove the default background property */
			background: none;
			width: 250px;
			height: 15px;
			border-radius: 5px;
			overflow: hidden;
			background-color: rgb(226, 226, 226);
			box-shadow: 0 5px 5px -5px rgba(0, 0, 0, 0.3) inset;
		}

		meter::-webkit-meter-low-value {
			background: none;
			background-color: #000;
		}

		:-moz-meter-low::-moz-meter-bar {
			background: none;
			background-color: #000;
		}


		meter::-webkit-meter-optimum-value {
			background: none;
			background-color: #f56954;
		}

		:-moz-meter-optimum::-moz-meter-bar {
			background: none;
			background-color: #f56954;
		}


		meter::-webkit-meter-suboptimum-value {
			background: none;
			background-color: #f56954;
		}

		:-moz-meter-sub-optimum::-moz-meter-bar {
			background: none;
			background-color: #f56954;
		}


		meter::-webkit-meter-even-less-good-value {
			background: none;
			background-color: #f56954;
		}

		:-moz-meter-sub-sub-optimum::-moz-meter-bar {
			background: none;
			background-color: #f56954;
		}

		.modal-loading-new {
			opacity: 0.4 !important;
			display: none;
			position: fixed;
			z-index: 2000;
			top: 0;
			left: 0;
			height: 100%;
			width: 100%;
			background: #333 url(<?php echo BASE_URL . '/images/loading2.gif'; ?>) 50% 50% no-repeat;
		}

		body.loading {
			overflow: hidden;
		}

		body.loading .modal-loading-new {
			display: block;
		}

		#agenda_form_modal>.loading {
			overflow: hidden;
		}

		#agenda_form_modal>.loading .modal-loading-new {
			display: block;
		}

		#eventModal>.loading {
			overflow: hidden;
		}

		#eventModal>.loading .modal-loading-new {
			display: block;
		}

		#ui-datepicker-div {
			z-index: 5000 !important;
		}

		#calendar-error .cl-error-icon {
			color: #fff;
			font-size: 35px;
			padding: 7px 15px;
		}

		#calendar-error .cl-error-message {
			font-weight: 700;
			font-size: 18px;
			border: 4px solid #ccc;
			padding: 15px 15px 15px 55px;
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
		}

		.table-info-event>tbody>tr>td {
			padding: 3px;
		}

		.fc .table-main>.fc-body>tr>td.fc-widget-content {
			border-left: 1px solid #c9c9c9;
			border-right: 1px solid #c9c9c9;
		}

		.fc-widget-header {
			background: none;
		}

		.fc th {
			border: 0px;
		}

		.fc-bg>table>tbody>tr>td {
			line-height: 25px;
			background-color: #fff;
		}

		.fc-bg>table>tbody>tr>td>.fc-head-number-day {
			padding: 0px;
		}

		.fc-content-skeleton>table>thead>tr>td {
			border: 1px solid #c9c9c9;
			text-align: right;
			line-height: 25px;
			cursor: pointer;
		}

		.fc-content-skeleton>table>thead>tr>td>.fc-day-number {
			font-weight: bold;
			font-size: 12px;
			padding: 0px 5px;
		}

		.fc .table-main .fc-day-grid>.fc-row {
			min-height: 100px;
		}

		@media screen and (max-width: 560px) {
			.fc .table-main .fc-day-grid>.fc-row {
				min-height: 45px;
			}
		}
	</style>
	<script>
		$(document).ready(function() {
			$("#id_cabang").on("change", function() {
				$("#loading_modal").modal({
					keyboard: false,
					backdrop: 'static'
				});
				$("#id_zoom").val("").trigger("change");
				getMasterDataZoom($("select#id_cabang").val());
			});

			function getMasterDataZoom(id_cabang) {
				$.ajax({
					type: "POST",
					url: "./__get_master_data_zoom.php",
					data: {
						"q1": id_cabang
					},
					cache: false,
					success: function(data) {
						$("#id_zoom").html(data);
						$("#loading_modal").modal("hide");
					}
				});
			}

			$("select#id_zoom").on("change", function() {
				$("#calendar").fullCalendar('destroy');
			});



			$("#btnSbmt01").on("click", function() {
				$("#loading_modal").modal({
					keyboard: false,
					backdrop: 'static'
				});

				if ($("#id_zoom").val()) {
					$("#calendar").fullCalendar({
						defaultDate: $("#defaultDate").val(),
						editable: false,
						contentHeight: "auto",
						height: "auto",
						timezone: "Asia/Jakarta",
						fixedWeekCount: false,
						myCustomCalendar: true,
						header: {
							left: 'prev',
							center: 'title',
							right: 'next'
						},
						events: {
							url: './__get_zoom_event.php',
							type: 'POST',
							data: function() {
								var date = $("#calendar").fullCalendar('getDate')._d;
								var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
								var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
								return {
									start: $.fullCalendar.moment(firstDay).format("YYYY-MM-DD"),
									end: $.fullCalendar.moment(lastDay).format("YYYY-MM-DD"),
									bulan: date.getMonth(),
									tahun: date.getFullYear(),
									ruangan: $("#id_zoom").val(),
									tipe: "non-order",
								}
							},
							error: function(e) {
								$("div#calendar").addClass("hide");
								$("div#calendar-error").removeClass("hide");
							},
							startParam: null,
							endParam: null,
						},
						eventRender: function(event, element, view) {
							var calDate = $("#calendar").fullCalendar("getDate")._d;
							var eventDate = event.start._d;
							if (eventDate.getMonth() !== calDate.getMonth()) {
								$(".fc-other-month .fc-day-number").hide();
								return false;
							}

						},
						eventClick: function(calEvent, jsEvent, view) {
							if (view.name == "month") {
								$("#eventModal").find("#info-event-name").html(calEvent.fullname);
								$("#eventModal").find("#info-event-ruangan").html(calEvent.ruangan);
								$("#eventModal").find("#info-event-departmen").html(calEvent.departmen);
								$("#eventModal").find("#info-event-keperluan").html(calEvent.keperluan.replace(/\n/g, "<br />"));

								var start = $.fullCalendar.moment(calEvent.startEvent).format("DD MMMM YYYY");
								start = (calEvent.allDay) ? start : start + " jam " + $.fullCalendar.moment(calEvent.startEvent).format("HH:mm") + " WIB";
								$("#eventModal").find("#info-event-start").html(start);
								if (calEvent.endEvent != null) {
									var end = $.fullCalendar.moment(calEvent.endEvent).format("DD MMMM YYYY");
									end = (calEvent.allDay) ? end : end + " jam " + $.fullCalendar.moment(calEvent.endEvent).format("HH:mm") + " WIB";
									$("#eventModal").find("#wrap-event-end").removeClass("hide");
									$("#eventModal").find("#info-event-end").html(end);
								} else {
									$("#eventModal").find("#wrap-event-end").addClass("hide");
									$("#eventModal").find("#info-event-end").html("");
								}

								if (calEvent.isOrder == true) {
									var tombolnya = '';
									tombolnya += '<a class="btn btn-sm btn-primary jarak-kanan" id="edit-event" data-eventid="' + calEvent.eventdata + '" style="min-width:90px;">Ubah</a> ';
									tombolnya += '<a class="btn btn-sm btn-danger" id="hapus-event" data-eventid="' + calEvent.eventdata + '" style="min-width:90px;">Hapus</a>';
									$("#eventModal").find("#info-event-tombol").html(tombolnya);
								} else {
									$("#eventModal").find("#info-event-tombol").html("");
								}
								$("#eventModal").modal({
									keyboard: false,
									backdrop: 'static'
								});
							}
						},
						dayRender: function(date, cell, view) {
							var cellIdx = $(cell).index();
							var content = $(cell).parents(".fc-bg").first().siblings(".fc-content-skeleton");
							var cellSbl = content.find("table > thead > tr > td").eq(cellIdx);
							$(cell).append('<div class="fc-head-number-day">&nbsp;</div>');
							cellSbl.on("click", function() {
								var defDate = $("#calendar").fullCalendar("getDate")._d;
								var clkDate = date._d;
								var datenya = $.fullCalendar.moment(clkDate).format("DD/MM/YYYY");
								if (view.name == "month" && clkDate.getMonth() === defDate.getMonth()) {
									$("form#gform").find(".form-group").removeClass("has-error");
									$("form#gform").find("label.error").remove();

									$("form#gform").find("input[type=text], textarea").val("");
									$("form#gform").find("#tanggal_reservasi").val(datenya);
									$("form#gform").find("#idr").val($("#id_zoom").val());
									$("form#gform").find("#idc").val($("#id_zoom option:selected").data('cabang'));
									$("form#gform").find("#nama_zoom").val($("#id_zoom option:selected").text());
									$("form#gform").find("#act").val("add");
									// $("form#gform").find(".colekstra01").hide();
									// $("form#gform").find("#meterbar").val(0);
									$("#agenda_form_modal").modal({
										backdrop: "static",
										keyboard: false
									});
								}
							});
						},
						views: {
							month: {
								displayEventEnd: true,
								timeFormat: 'HH:mm'
							}
						},
						loading: function(bool) {
							(bool) ? $("#loading_modal").modal({
								keyboard: false,
								backdrop: 'static'
							}): $("#loading_modal").modal("hide");
						},
					});

					$("#loading_modal").modal("hide");
				} else {
					$("#calendar").fullCalendar('destroy');
					$("#loading_modal").modal("hide");
				}
			});

			$("form#gform").validate(config.validation);
			$("#btnSbmt02").on("click", function() {
				let isValid = $("#gform").valid();
				let pattern01 = /^([01]\d|2[0-3]|[0-9])(:[0-5]\d){1,2}$/;
				let jam_mulai = $("#jam_mulai").val(),
					jam_selesai = $("#jam_selesai").val();
				if (!jam_mulai) {
					$.validator.showErrorField('jam_mulai', "Kolom ini belum diisi atau dipilih");
					isValid = isValid && false;
				}
				if (!jam_selesai) {
					$.validator.showErrorField('jam_selesai', "Kolom ini belum diisi atau dipilih");
					isValid = isValid && false;
				}
				if (jam_mulai && !pattern01.test(jam_mulai)) {
					$.validator.showErrorField('jam_mulai', "Format waktu antara 00:00 dan 23:59");
					isValid = isValid && false;
				}
				if (jam_selesai && !pattern01.test(jam_selesai)) {
					$.validator.showErrorField('jam_selesai', "Format waktu antara 00:00 dan 23:59");
					isValid = isValid && false;
				}

				if (isValid) {
					$("#agenda_form_modal > .modal-dialog").addClass("loading");
					$.post($base_url + "/web/action/peminjaman-zoom.php", $("form#gform").serializeArray(), function(data) {
						if (data.error) {
							$("#agenda_form_modal > .modal-dialog").removeClass("loading");
							swal.fire({
								allowOutsideClick: false,
								icon: "warning",
								width: '350px',
								html: '<p style="font-size:14px; font-family:arial;">' + data.pesan + '</p>'
							});
						} else {
							$("#agenda_form_modal").modal("hide");
							$("#agenda_form_modal > .modal-dialog").removeClass("loading");
							$("body").css("padding-right", "");
							$('#calendar').fullCalendar("refetchEvents");
						}
					}, "json");
				}
			});

			$("#eventModal").on("click", "#hapus-event", function() {
				swal.fire({
					title: '<div style="font-weight:400; font-size:16px; line-height:25px;">Apakah anda yakin?</div>',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Ya',
					cancelButtonText: 'Tidak',
				}).then((result) => {
					if (result.isConfirmed) {
						var eventId = $("#eventModal").find("#hapus-event").data("eventid");
						$("#eventModal > .modal-dialog").addClass("loading");
						$.post($base_url + "/web/action/peminjaman-zoom.php", {
							act: 'delete',
							idk: eventId
						}, function(data) {
							if (data.error) {
								$("#eventModal").modal("hide");
								$("#eventModal > .modal-dialog").removeClass("loading");
								swal.fire({
									allowOutsideClick: false,
									icon: "warning",
									width: '350px',
									html: '<p style="font-size:14px; font-family:arial;">' + data.pesan + '</p>'
								});
							} else {
								$("#eventModal").modal("hide");
								$("#eventModal > .modal-dialog").removeClass("loading");
								$("body").css("padding-right", "");
								$('#calendar').fullCalendar("refetchEvents");
							}
						}, "json");
					}
				});
			}).on("click", "#edit-event", function() {
				var eventId = $("#eventModal").find("#edit-event").data("eventid");
				$("#eventModal").modal("hide");
				$("#eventModal").one('hidden.bs.modal', function(e) {
					$("#agenda_form_modal").modal({
						backdrop: "static",
						keyboard: false
					});
					$("#agenda_form_modal").one('shown.bs.modal', function(e) {
						$("#agenda_form_modal > .modal-dialog").addClass("loading");
						$.post($base_url + "/web/__get_zoom_event_data.php", {
							prm: eventId
						}, function(data) {
							$("form#gform").find(".form-group").removeClass("has-error");
							$("form#gform").find("label.error").remove();

							$("form#gform").find("#nama_zoom").val(data.nama_zoom);
							$("form#gform").find("#tanggal_reservasi").val(data.tanggal_reservasi);
							$("form#gform").find("#keperluan").val(data.keperluan);
							$("form#gform").find("#departmen").val(data.departmen);

							$("form#gform").find("#idc").val(data.id_cabang);
							$.asbtimepicker.set($("form#gform").find("[data-id='jam_mulai']"), data.jam_mulai);
							$.asbtimepicker.set($("form#gform").find("[data-id='jam_selesai']"), data.jam_selesai);

							$("form#gform").find(".colekstra01").show();
							$("form#gform").find("#bensin").val(data.bensin);
							$("form#gform").find("#last_km").val(data.last_km);
							$("form#gform").find("#meterbar").val(data.bensin);


							$("form#gform").find("#act").val("update");
							$("form#gform").find("#idr").val(data.id_zoom);
							$("form#gform").find("#idk").val(data.id_peminjaman);
							$("#agenda_form_modal > .modal-dialog").removeClass("loading");
						}, "json");
					});
				});
				/*$("#agenda_form_modal > .modal-dialog").addClass("loading");
				$.post($base_url+"/web/__get_event_data.php", {prm:eventId}, function(data){
					$("form#gform").find(".form-group").removeClass("has-error");
					$("form#gform").find("label.error").remove();
					
					$("form#gform").find("#nama_ruangan").val(data.nama_ruangan);
					$("form#gform").find("#tanggal_reservasi").val(data.tanggal_reservasi);
					$("form#gform").find("#personel").val(data.personel);
					$("form#gform").find("#keperluan").val(data.keperluan);
					$.asbtimepicker.set($("form#gform").find("[data-id='jam_mulai']"), data.jam_mulai);
					$.asbtimepicker.set($("form#gform").find("[data-id='jam_selesai']"), data.jam_selesai);

					$("form#gform").find("#act").val("update");
					$("form#gform").find("#idr").val(data.id_mobil);
					$("form#gform").find("#idk").val(data.id_reservasi);
					$("#agenda_form_modal > .modal-dialog").removeClass("loading");
				}, "json");*/
			});

		});
	</script>
</body>

</html>