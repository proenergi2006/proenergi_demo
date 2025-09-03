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
                            <div class="d-flex justify-content-end mb-2">
                                <div>
                                    Tampilkan
                                    <select id="pageSize" class="form-control d-inline-block" style="width:120px">
                                        <option value="10" selected>10</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="all">All</option>
                                    </select>
                                </div>
                            </div>
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
                                    <tfoot>
                                        <tr class="table-secondary">
                                            <th colspan="4" class="text-center">Total Incentive</th>
                                            <th class="text-center" id="grandTotalCell">0</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="pagination-controls">
                                <button type="button" id="prevPage" disabled class="btn btn-primary btn-sm">Previous</button>
                                <span id="currentPage">Page 1</span>
                                <button type="button" id="nextPage" class="btn btn-primary btn-sm">Next</button>
                                <span style="margin-left: 20px;" id="total-data">Total Data : 0</span>
                            </div>

                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                            <div id="all_id_incentive" style="display: none;"></div>

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
        let itemsPerPage = 2; // default awal, akan di-override oleh #pageSize jika ada
        let paginatedItems = [];

        /* =============================
           Helper tambahan (BARU)
        ============================= */
        // ambil nilai total dari beragam kemungkinan kolom
        function getTotalRaw(row) {
            return row.total_incentive ?? row.total ?? row.total_incentif ?? row.nilai_incentive ?? row.incentive ?? 0;
        }

        // parser angka fleksibel: "12.345,67", "12,345.67", "Rp 12.345", "12345.67"
        function parseNumberFlexible(v) {
            if (v == null) return 0;
            if (typeof v === 'number') return v;
            let s = String(v).trim();
            if (s === '') return 0;
            s = s.replace(/[^\d.,-]/g, '');
            const lastDot = s.lastIndexOf('.');
            const lastCom = s.lastIndexOf(',');
            let decimalSep = null;
            if (lastDot !== -1 || lastCom !== -1) {
                decimalSep = (lastDot > lastCom) ? '.' : ',';
            }
            if (decimalSep) {
                const thousandSep = (decimalSep === '.') ? ',' : '.';
                s = s.split(thousandSep).join('');
                if (decimalSep === ',') s = s.replace(',', '.');
            } else {
                s = s.replace(/[.,]/g, '');
            }
            const n = parseFloat(s);
            return isNaN(n) ? 0 : n;
        }

        function formatIDR(n) {
            return new Intl.NumberFormat('id-ID').format(Number(n || 0));
        }

        // dapatkan page size efektif berdasar dropdown (jika ada)
        function getEffectivePageSize() {
            const totalItems = paginatedItems.length;
            const pageSizeVal = ($("#pageSize").val() || itemsPerPage).toString();
            if (pageSizeVal === 'all') return totalItems || 1; // biar tidak 0
            const ps = parseInt(pageSizeVal, 10);
            return isNaN(ps) ? (itemsPerPage || 10) : ps;
        }

        /* =============================
           AJAX generate (asli kamu)
        ============================= */
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
                        if (data.items && data.items.length > 0) {
                            $("#btnSbmt").removeAttr("disabled");

                            // simpan semua item (PENTING)
                            paginatedItems = data.items;

                            build_all_id_incentive();

                            // set itemsPerPage sesuai dropdown (jika ada)
                            itemsPerPage = getEffectivePageSize();

                            // render & update pager
                            currentPage = 1; // Reset ke halaman pertama
                            renderTable(currentPage);
                            updatePaginationControls();

                            $("#total-data").html("Total semua data : " + data.items.length);
                        } else {
                            paginatedItems = [];
                            $(".table-dasar").find('tbody').html('<tr><td class="text-center" colspan="5">Tidak Ada Data</td></tr>');
                            $("#prevPage, #nextPage").prop("disabled", true);
                            // reset grand total
                            $("#grandTotalCell").text('0');
                            $("#total-data").html("Total semua data : 0");
                            $("#currentPage").text("Page 1");

                            ensureHiddenContainer();
                            $("#all_id_incentive").empty();
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

        /* =============================
           RENDER TABLE (dipertahankan, hanya tambah sedikit)
        ============================= */
        function renderTable(page) {
            // tentukan page size efektif (10/20/50/100/All)
            const totalItems = paginatedItems.length;
            const pageSizeVal = ($("#pageSize").val() || itemsPerPage).toString();
            const effectivePageSize = getEffectivePageSize();

            // hitung batas halaman + normalisasi currentPage
            const totalPages = Math.max(1, Math.ceil((totalItems || 1) / (effectivePageSize || 1)));
            currentPage = Math.min(Math.max(1, page), totalPages);

            // hitung slice
            const start = (pageSizeVal === 'all') ? 0 : (currentPage - 1) * effectivePageSize;
            const end = (pageSizeVal === 'all') ? totalItems : start + effectivePageSize;
            const itemsToDisplay = paginatedItems.slice(start, end);

            const tabel = $(".table-dasar tbody");
            tabel.empty();

            if (itemsToDisplay.length > 0) {
                itemsToDisplay.forEach((row, idx) => {
                    const date = row.tanggal_bayar ? new Date(row.tanggal_bayar) : null;
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
                    const role = roleMap[row.id_role] || "Unknown Role";

                    const totalNum = parseNumberFlexible(getTotalRaw(row)); // <- pakai parser fleksibel

                    var isiHtml =
                        '<tr>' +
                        '<td class="text-center"><span>' + (start + idx + 1) + '</span></td>' +
                        '<td class="text-center">' +
                        '<p style="margin-bottom:3px;"><b>' + (row.no_invoice || '-') + '</b></p>' +
                        (date ? '<p style="margin-bottom:3px;">Date Payment : ' + date.toLocaleDateString('id-ID', options) + '</p>' : '') +
                        '</td>' +
                        '<td class="text-center">' +
                        '<p style="margin-bottom:3px;"><b>' + (row.kode_pelanggan || '-') + '</b></p>' +
                        '<p style="margin-bottom:3px;">' + (row.nama_customer || '-') + '</p>' +
                        '<p style="margin-bottom:3px;">Area : ' + (row.nama_area || '-') + '</p>' +
                        '</td>' +
                        '<td class="text-center">' +
                        '<p style="margin-bottom:3px;"><b>' + (row.fullname || '-') + '</b></p>' +
                        '<p style="margin-bottom:3px;">' + role + '</p>' +
                        '</td>' +
                        '<td class="text-center">' +
                        '<p style="margin-bottom:3px;"><b>Rp. ' + new Intl.NumberFormat('id-ID').format(totalNum) + '</b></p>' +
                        '</td>' +
                        '</tr>';
                    tabel.append(isiHtml);
                });
            } else {
                tabel.html('<tr><td class="text-center" colspan="5">Tidak Ada Data</td></tr>');
            }

            // === TOTAL SESUAI HALAMAN (itemsToDisplay), BUKAN SEMUA ROW ===
            const pageTotal = itemsToDisplay.reduce((sum, it) => {
                return sum + parseNumberFlexible(getTotalRaw(it));
            }, 0);
            $("#grandTotalCell").text("Rp. " + new Intl.NumberFormat('id-ID').format(pageTotal));

            // Update teks halaman (gaya kamu tetap)
            $("#currentPage").text("Page " + currentPage + (pageSizeVal === 'all' ? "" : " of " + totalPages));
        }

        /* =============================
           Pagination controls (minor tweak)
        ============================= */
        function updatePaginationControls() {
            const totalItems = paginatedItems.length;
            const pageSizeVal = ($("#pageSize").val() || itemsPerPage).toString();
            const effectivePageSize = getEffectivePageSize();
            const totalPages = Math.max(1, Math.ceil((totalItems || 1) / (effectivePageSize || 1)));

            $("#currentPage").text("Page " + currentPage + (pageSizeVal === 'all' ? "" : " of " + totalPages));

            // disable ketika 'all' atau halaman mentok
            const disablePager = (pageSizeVal === 'all') || (totalPages <= 1);
            $("#prevPage").prop("disabled", disablePager || currentPage === 1);
            $("#nextPage").prop("disabled", disablePager || currentPage === totalPages);
        }

        /* =============================
           Buttons (tetap gaya kamu)
        ============================= */
        $("#prevPage").on("click", function() {
            if (currentPage > 1) {
                currentPage--;
                renderTable(currentPage);
                updatePaginationControls();
            }
        });

        $("#nextPage").on("click", function() {
            const totalItems = paginatedItems.length;
            const totalPages = Math.max(1, Math.ceil((totalItems || 1) / (getEffectivePageSize() || 1)));
            if (currentPage < totalPages) {
                currentPage++;
                renderTable(currentPage);
                updatePaginationControls();
            }
        });

        /* =============================
           Page size handler (BARU)
           - otomatis render ulang saat dropdown diganti
        ============================= */
        $(document).on('change', '#pageSize', function() {
            itemsPerPage = getEffectivePageSize(); // sinkronkan nilai global
            currentPage = 1;
            renderTable(currentPage);
            updatePaginationControls();
        });

        function ensureHiddenContainer() {
            if ($("#all_id_incentive").length === 0) {
                // jika belum ada (misal HTML tak diubah), buat otomatis
                const $form = $("#gform").length ? $("#gform") : $("form").first();
                $form.append('<div id="all_id_incentive" style="display: none;"></div>');
            }
        }

        function build_all_id_incentive() {
            ensureHiddenContainer();
            const $box = $("#all_id_incentive");
            $box.empty();
            // isi semua id_incentive dari seluruh data
            paginatedItems.forEach(it => {
                if (it && it.id_incentive != null && it.id_incentive !== '') {
                    $box.append(
                        `<input type="hidden" name="id_incentive[]" value="${String(it.id_incentive).replace(/"/g,'&quot;')}">`
                    );
                }
            });
        }

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