<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$enk      = decode($_SERVER['REQUEST_URI']);
$con     = new Connection();
$flash    = new FlashAlerts;

$idr         = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$sesuser     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
$seswil     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sesgroup     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
$month         = date("m");
$year         = date("Y");

// Cek peran pengguna
$required_role = ['1', '2', '23'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
    // Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
    $flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
    // exit();
}

$query = "SELECT * FROM pro_pengajuan_incentive ORDER BY id DESC";
$row = $con->getRecord($query);

$query2 = "SELECT * FROM pro_master_cabang WHERE is_active = '1' AND id_master NOT IN('1','10') ORDER BY nama_cabang ASC";
$cabang = $con->getResult($query2);

?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "formatNumber", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Pengajuan Persetujuan Incentive</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                    </div>
                    <div class="box-body">
                        <form action="<?php echo ACTION_CLIENT . '/incentive_bundling.php'; ?>" id="gform" name="gform" method="post" class="form-validasi form-horizontal" role="form">
                            <input type="hidden" name="jenis" id="jenis" value="add_incentive">

                            <!-- <div class="row" id="row-periode">
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Periode Awal *</label>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												<input type="text" id="periode_awal" name="periode_awal" class="form-control datepicker" autocomplete="off" required onkeydown="preventInput(event)" />
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group form-group-sm">
										<label class="control-label col-md-4">Periode Akhir *</label>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												<input type="text" id="periode_akhir" name="periode_akhir" class="form-control datepicker" autocomplete="off" required onkeydown="preventInput(event)" />
											</div>
										</div>
									</div>
								</div>
							</div> -->

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Periode *</label>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="month" id="periode" name="periode" class="form-control" autocomplete="off" required onkeydown="preventInput(event)" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Cabang *</label>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <select name="cabang" id="cabang" class="form-control" required>
                                                    <option value="">Pilih salah satu</option>
                                                    <?php foreach ($cabang as $key) : ?>
                                                        <option value="<?= $key['id_master'] ?>"><?= ucwords($key['nama_cabang']) ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-sm">
                                        <div class="col-md-12">
                                            <button type="button" name="btn-generate" id="btn-generate" class="btn btn-sm btn-info">Generate</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered hide" id="table-incentive">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="50">id incentive</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="8">Tidak Ada Data</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="table-responsive">
                                <table class="table table-bordered table-dasar" id="table_incentive">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="50">No</th>
                                            <th class="text-center" width="150">No Invoice</th>
                                            <th class="text-center" width="200">Customer</th>
                                            <th class="text-center" width="200">Marketing</th>
                                            <th class="text-center" width="150">Total Incentive</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center" colspan="8">Tidak Ada Data</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination-controls">
                                <button type="button" id="prevPage" disabled class="btn btn-primary btn-sm">Previous</button>
                                <span id="currentPage">Page 1</span>
                                <button type="button" id="nextPage" class="btn btn-primary btn-sm">Next</button>
                                <span style="margin-left: 20px;" id="total-data">Total Data : 0</span>
                            </div>


                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                            <div style="margin-bottom:15px;">
                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                <a href="<?php echo BASE_URL_CLIENT . '/incentive.php'; ?>" class="btn btn-default" style="min-width:90px;">
                                    <i class="fa fa-reply jarak-kanan"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" disabled style="min-width:90px;">
                                    <i class="fa fa-save jarak-kanan"></i> Simpan
                                </button>
                            </div>
                            <p style="margin:0px;"><small>* Wajib Diisi</small></p>
                        </form>
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

    <style type="text/css">
        #table-grid3 {
            margin-bottom: 15px;
        }

        #table-grid3 td,
        #table-grid3 th {
            font-size: 11px;
            font-family: arial;
        }
    </style>
</body>

<script>
    // function preventInput(event) {
    // 	// 8 adalah kode untuk Backspace
    // 	if (event.keyCode !== 8) {
    // 		event.preventDefault(); // Mencegah input jika bukan Backspace
    // 	}
    // }

    $(document).ready(function() {

        $("#periode").change(function() {
            $("#btnSbmt").attr("disabled", true);
            var isiHtml =
                '<tr><td class="text-center" colspan="5">Tidak Ada Data</td></tr>';
            $(".table-dasar").find('tbody').html(isiHtml);
        });

        $("#cabang").change(function() {
            $("#btnSbmt").attr("disabled", true);
            var isiHtml =
                '<tr><td class="text-center" colspan="5">Tidak Ada Data</td></tr>';
            $(".table-dasar").find('tbody').html(isiHtml);
        });

        let currentPage = 1;
        let itemsPerPage = 5; // Adjust this number as needed
        let paginatedItems = [];
        $("#btn-generate").on("click", function(e) {
            let periode = $("#periode").val();
            let cabang = $("#cabang").val();

            if (periode && cabang) {
                $("#loading_modal").modal({
                    keyboard: false,
                    backdrop: 'static'
                });

                $.ajax({
                    type: 'POST',
                    url: "./incentive_list_generate.php",
                    data: {
                        periode: periode,
                        cabang: cabang,
                        kategori: "generate_incentive"
                    },
                    cache: false,
                    dataType: "json",
                    success: function(data) {
                        $("#loading_modal").modal("hide");
                        let tabel = $("#table-incentive");
                        let rows = [];
                        if (data.items.length > 0) {
                            $("#btnSbmt").removeAttr("disabled");
                            $.each(data.items, function(idx, row) {

                                var isiHtml =
                                    '<tr>' +
                                    '<td class="text-center"><input class="hidden" name="id_incentive[]" value="' + row.id_incentive + '"></td>' +
                                    '</tr>';
                                rows.push(isiHtml);
                            });
                            tabel.find('tbody').html(rows.join(''));
                            // Store items for pagination
                            paginatedItems = data.items;
                            currentPage = 1; // Reset to first page
                            renderTable(currentPage);
                            updatePaginationControls();
                            $("#total-data").html("Total semua data : " + data.items.length);
                        } else {
                            $(".table-dasar").find('tbody').html('<tr><td class="text-center" colspan="5">Tidak Ada Data</td></tr>');
                            $("#prevPage, #nextPage").prop("disabled", true);
                        }
                    },
                    error: function() {
                        $("#loading_modal").modal("hide");
                        swal.fire("Error", "There was a problem generating the report.", "error");
                    }
                });
            } else {
                swal.fire("Silahkan Pilih Periode dan Cabang terlebih dahulu");
            }
        });

        function renderTable(page) {
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const itemsToDisplay = paginatedItems.slice(start, end);

            const tabel = $(".table-dasar tbody");
            tabel.empty(); // Clear existing rows

            if (itemsToDisplay.length > 0) {
                itemsToDisplay.forEach((row, idx) => {
                    const date = new Date(row.tanggal_bayar);
                    const options = {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    const roleMap = {
                        '7': "Branch Manager",
                        '11': "Marketing",
                        '17': "Key Account Executive",
                        '20': "SPV Marketing"
                    };
                    var role = roleMap[row.id_role] || "Unknown Role";

                    var isiHtml =
                        '<tr>' +
                        '<td class="text-center"><span>' + (start + idx + 1) + '</span></td>' +
                        '<td class="text-center">' +
                        '<p style="margin-bottom:3px;"><b>' + row.no_invoice + '</b></p>' +
                        '<p style="margin-bottom:3px;">Date Payment : ' + date.toLocaleDateString('id-ID', options) + '</p>' +
                        '</td>' +
                        '<td class="text-center">' +
                        '<p style="margin-bottom:3px;"><b>' + row.kode_pelanggan + '</b></p>' +
                        '<p style="margin-bottom:3px;">' + row.nama_customer + '</p>' +
                        '<p style="margin-bottom:3px;">Area : ' + row.nama_area + '</p>' +
                        '</td>' +
                        '<td class="text-center">' +
                        '<p style="margin-bottom:3px;"><b>' + row.fullname + '</b></p>' +
                        '<p style="margin-bottom:3px;">' + role + '</p>' +
                        '</td>' +
                        '<td class="text-center">' +
                        '<p style="margin-bottom:3px;"><b>Rp. ' + new Intl.NumberFormat(navigator.language).format(row.total_incentive) + '</b></p>' +
                        '</td>' +
                        '</tr>';
                    tabel.append(isiHtml);
                });
            } else {
                tabel.html('<tr><td class="text-center" colspan="5">Tidak Ada Data</td></tr>');
            }
        }

        function updatePaginationControls() {
            const totalPages = Math.ceil(paginatedItems.length / itemsPerPage);
            $("#currentPage").text("Page " + currentPage);
            $("#prevPage").prop("disabled", currentPage === 1);
            $("#nextPage").prop("disabled", currentPage === totalPages);
        }

        $("#prevPage").on("click", function() {
            if (currentPage > 1) {
                currentPage--;
                renderTable(currentPage);
                updatePaginationControls();
            }
        });

        $("#nextPage").on("click", function() {
            const totalPages = Math.ceil(paginatedItems.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderTable(currentPage);
                updatePaginationControls();
            }
        });
        // $("#btn-generate").on("click", function(e) {
        // 	let no_pengajuan = $("#no_pengajuan").val();
        // 	let periode = $("#periode").val();

        // 	if (periode) {
        // 		$("#loading_modal").modal({
        // 			keyboard: false,
        // 			backdrop: 'static'
        // 		});

        // 		$.ajax({
        // 			type: 'POST',
        // 			url: "./incentive_list_generate.php",
        // 			data: {
        // 				no_pengajuan: no_pengajuan,
        // 				periode: periode
        // 			},
        // 			cache: false,
        // 			dataType: "json",
        // 			success: function(data) {
        // 				let tabel = $(".table-dasar");
        // 				let rows = [];

        // 				if (data.items.length > 0) {
        // 					$("#btnSbmt").removeAttr("disabled");
        // 					$.each(data.items, function(idx, row) {
        // 						const date = new Date(row.tanggal_bayar);
        // 						const options = {
        // 							year: 'numeric',
        // 							month: 'long',
        // 							day: 'numeric'
        // 						};

        // 						const roleMap = {
        // 							'7': "Branch Manager",
        // 							'11': "Marketing",
        // 							'17': "Key Account Executive",
        // 							'20': "SPV Marketing"
        // 						};
        // 						var role = roleMap[row.id_role] || "Unknown Role";

        // 						var isiHtml =
        // 							'<tr>' +
        // 							'<td class="text-center"><span>' + (idx + 1) + '</span></td>' +
        // 							'<td class="text-center hide"><input class="hidden" name="id_incentive[]" value="' + row.id_incentive + '"></td>' +
        // 							'<td class="text-center">' +
        // 							'<p style="margin-bottom:3px;"><b>' + row.no_invoice + '</b></p>' +
        // 							'<p style="margin-bottom:3px;">Date Payment : ' + date.toLocaleDateString('id-ID', options) + '</p>' +
        // 							'</td>' +
        // 							'<td class="text-center">' +
        // 							'<p style="margin-bottom:3px;"><b>' + row.kode_pelanggan + '</b></p>' +
        // 							'<p style="margin-bottom:3px;">' + row.nama_customer + '</p>' +
        // 							'<p style="margin-bottom:3px;">Area : ' + row.nama_area + '</p>' +
        // 							'</td>' +
        // 							'<td class="text-center">' +
        // 							'<p style="margin-bottom:3px;"><b>' + row.fullname + '</b></p>' +
        // 							'<p style="margin-bottom:3px;">' + role + '</p>' +
        // 							'</td>' +
        // 							'<td class="text-center">' +
        // 							'<p style="margin-bottom:3px;"><b>Rp. ' + new Intl.NumberFormat(navigator.language).format(row.total_incentive) + '</b></p>' +
        // 							'</td>' +
        // 							'</tr>';
        // 						rows.push(isiHtml);
        // 					});
        // 					tabel.find('tbody').html(rows.join(''));
        // 				} else {
        // 					tabel.find('tbody').html('<tr><td class="text-center" colspan="5">Tidak Ada Data</td></tr>');
        // 				}
        // 				$("#loading_modal").modal("hide");
        // 			},
        // 			error: function() {
        // 				$("#loading_modal").modal("hide");
        // 				swal.fire("Error", "There was a problem generating the report.", "error");
        // 			}
        // 		});
        // 	} else {
        // 		swal.fire("Silahkan Pilih Periode terlebih dahulu");
        // 	}
        // });

        $('#btnSbmt').on('click', function(e) {
            e.preventDefault();
            var form = $(this).parents('form');
            var periode = $("#periode").val();
            var cabang = $("#cabang").val();
            Swal.fire({
                title: "Anda yakin?",
                showCancelButton: true,
                confirmButtonText: "Ya",
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#loading_modal").modal({
                        backdrop: 'static'
                    });
                    $.ajax({
                        type: 'POST',
                        url: "./incentive_list_generate.php",
                        data: {
                            periode: periode,
                            cabang: cabang,
                            kategori: "cek_penerima_refund"
                        },
                        cache: false,
                        dataType: "json",
                        success: function(data) {
                            $("#loading_modal").modal("hide");
                            // console.log(data)
                            if (data.length > 0) {
                                if (periode == "" || cabang == "") {
                                    Swal.fire({
                                        icon: "warning",
                                        title: "Oops...",
                                        text: "Periode dan Cabang tidak boleh kosong"
                                    });
                                    $("#btnSbmt").attr("disabled", true);
                                    $("#loading_modal").modal("hide");
                                } else {
                                    form.submit();
                                }
                                // console.log("Kesini")
                            } else {
                                // console.log("Kesono")
                                Swal.fire({
                                    title: "Data BM, SM dan SPV belum lengkap, lanjutkan?",
                                    showCancelButton: true,
                                    confirmButtonText: "Ya",
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        form.submit();
                                    }
                                });
                            }
                        },
                        error: function() {
                            $("#loading_modal").modal("hide");
                            swal.fire("Error", "There was a problem generating the data.", "error");
                        }
                    });
                }
            });
        });
    });
</script>

</html>