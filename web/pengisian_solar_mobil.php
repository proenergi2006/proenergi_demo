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

// Cek peran pengguna
$required_role = ['1', '2', '24', '9', '10'];
// Misalnya halaman ini hanya untuk superadmin
if (!in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), $required_role)) {
    // Pengguna tidak memiliki peran yang tepat, redirect ke halaman lain atau tampilkan pesan akses ditolak
    $flash->add("warning", "Akses ditolak.", BASE_URL_CLIENT . "/home.php");
    // exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("myGrid", "jqueryUI", "formatNumber"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Pengisian Solar Mobil Operasional</h1>
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
                            <input type="text" class="form-control input-sm" placeholder="Keywords : nomor voucher, driver" name="q1" id="q1" />
                        </div>
                        <div class="col-sm-3">
                            <select name="q2" id="q2" class="form-control select2">
                                <option value="">Pilih Status</option>
                                <option value="0">Verifikasi Admin Finance</option>
                                <option value="1">Terverifikasi</option>
                                <option value="2">Cancel</option>
                            </select>
                        </div>
                        <div class="col-sm-3 col-sm-top">
                            <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i> Search</button>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php
                                        $role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
                                        if (in_array($role, [24, 9])) : ?>
                                            <a href="<?= BASE_URL_CLIENT . '/add_pengisian_solar_mobil.php'; ?>" class="btn btn-primary">
                                                <i class="fa fa-plus jarak-kanan"></i>Add Data
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-right" style="margin-top: 10px">Show
                                            <select name="tableGridLength" id="tableGridLength">
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
                                <table class="table table-bordered" id="table-grid">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="50">No</th>
                                            <th class="text-center" width="180">Nomor Voucher</th>
                                            <th class="text-center" width="200">Mobil</th>
                                            <th class="text-center" width="200">Volume</th>
                                            <th class="text-center" width="150">Keterangan</th>
                                            <th class="text-center" width="140">Status</th>
                                            <th class="text-center" width="80">Aksi</th>
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

    <!-- Modal Realisasi -->
    <div class="modal fade" id="RealisasiModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Realisasi</h5>
                </div>

                <div class="modal-body">
                    <form action="<?php echo ACTION_CLIENT . '/pengisian_solar_mobil.php'; ?>" id="realisasiForm" name="realisasiForm" method="post" class="form-validasi" role="form" enctype="multipart/form-data">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">Volume Realisasi *</label>
                                    <input type="text" class="form-control text-right" id="vol_realisasi" name="vol_realisasi" placeholder="0.0000" required>
                                    <input type="hidden" name="id_pengisian" id="id_pengisian">
                                </div>
                                <div class="col-md-3">
                                    <label for="">Tanggal Realisasi *</label>
                                    <input type="text" class="form-control datepicker" id="tgl_realisasi" name="tgl_realisasi" autocomplete="off" value="<?= date("d/m/Y") ?>" required>
                                    <input type="hidden" name="act" id="act" value="realisasi">
                                </div>
                                <div class="col-md-6">
                                    <label>Lampiran *</label>
                                    <input type="file" id="lampiran" name="lampiran" class="form-control" required accept="image/png, image/jpeg, .pdf">
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="">Driver *</label>
                                    <input type="text" class="form-control" id="driver_realisasi" name="driver_realisasi" required>
                                </div>
                                <div class="col-md-8">
                                    <label for="">Keterangan *</label>
                                    <input type="text" class="form-control" id="keterangan_realisasi" name="keterangan_realisasi" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnSimpan">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>

            </div>
        </div>
    </div>

    <style>
        #table-grid td,
        #table-grid th {
            font-size: 12px;
        }

        .table>tbody>tr>td {
            padding: 5px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $("#vol_realisasi").number(true, 4, ".", ",");

            $('#vol_realisasi').on('keypress', function(e) {
                var charCode = e.which ? e.which : e.keyCode;

                // Izinkan hanya angka 0–9 (kode ASCII 48–57)
                if (charCode < 48 || charCode > 57) {
                    e.preventDefault();
                    return false;
                }
            });

            $("#table-grid").ajaxGrid({
                url: "./datatable/pengisian_solar_mobil.php",
                data: {
                    q1: $("#q1").val(),
                    q2: $("#q2").val(),
                },
            });
            $('#btnSearch').on('click', function() {
                $("#table-grid").ajaxGrid("draw", {
                    data: {
                        q1: $("#q1").val(),
                        q2: $("#q2").val()
                    }
                });
                return false;
            });
            $('#tableGridLength').on('change', function() {
                $("#table-grid").ajaxGrid("pageLen", $(this).val());
            });
            $('#table-grid tbody').on('click', '[data-action="deleteGrid"]', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "Anda yakin hapus?",
                    showCancelButton: true,
                    confirmButtonText: "Hapus",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        var param = $(this).data("param-idx");
                        $.ajax({
                            method: 'post',
                            url: '<?php echo ACTION_CLIENT ?>/pengisian_solar_mobil.php',
                            data: {
                                "param": param,
                                "act": "hapus",
                            },
                            dataType: 'json',
                            success: function(result) {
                                // console.log(result)
                                if (result.status == false) {
                                    setTimeout(function() {
                                        Swal.fire({
                                            title: "Ooppss",
                                            text: result.pesan,
                                            icon: "warning"
                                        }).then((result) => {
                                            location.reload();
                                        });
                                    }, 2000);
                                } else {
                                    setTimeout(function() {
                                        Swal.fire({
                                            title: "Berhasil",
                                            text: result.pesan,
                                            icon: "success"
                                        }).then((result) => {
                                            location.reload();
                                        });
                                    }, 2000);
                                }
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert("Error");
                                // console.log(errorThrown)
                            }
                        })
                    }
                });
            });

            $('#table-grid tbody').on('click', '[data-action="cancelGrid"]', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "Anda yakin cancel?",
                    showCancelButton: true,
                    confirmButtonText: "YA",
                    cancelButtonText: "Tidak",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        var param = $(this).data("param-idx");
                        $.ajax({
                            method: 'post',
                            url: '<?php echo ACTION_CLIENT ?>/pengisian_solar_mobil.php',
                            data: {
                                "param": param,
                                "act": "cancel",
                            },
                            dataType: 'json',
                            success: function(result) {
                                // console.log(result)
                                if (result.status == false) {
                                    setTimeout(function() {
                                        Swal.fire({
                                            title: "Ooppss",
                                            text: result.pesan,
                                            icon: "warning"
                                        }).then((result) => {
                                            location.reload();
                                        });
                                    }, 2000);
                                } else {
                                    setTimeout(function() {
                                        Swal.fire({
                                            title: "Berhasil",
                                            text: result.pesan,
                                            icon: "success"
                                        }).then((result) => {
                                            location.reload();
                                        });
                                    }, 2000);
                                }
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert("Error");
                                // console.log(errorThrown)
                            }
                        })
                    }
                });
            });

            $('#table-grid tbody').on('click', '[data-action="verifGrid"]', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "Anda yakin approve?",
                    showCancelButton: true,
                    confirmButtonText: "Approve",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        var param = $(this).data("param-idx");
                        $.ajax({
                            method: 'post',
                            url: '<?php echo ACTION_CLIENT ?>/pengisian_solar_mobil.php',
                            data: {
                                "param": param,
                                "act": "approve",
                            },
                            dataType: 'json',
                            success: function(result) {
                                // console.log(result)
                                if (result.status == false) {
                                    setTimeout(function() {
                                        Swal.fire({
                                            title: "Ooppss",
                                            text: result.pesan,
                                            icon: "warning"
                                        }).then((result) => {
                                            location.reload();
                                        });
                                    }, 2000);
                                } else {
                                    setTimeout(function() {
                                        Swal.fire({
                                            title: "Berhasil",
                                            text: result.pesan,
                                            icon: "success"
                                        }).then((result) => {
                                            location.reload();
                                        });
                                    }, 2000);
                                }
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert("Error");
                                // console.log(errorThrown)
                            }
                        })
                    }
                });
            });
            $('#table-grid tbody').on('click', '[data-action="verifRealisasiGrid"]', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "Anda yakin approve?",
                    showCancelButton: true,
                    confirmButtonText: "Approve",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        var param = $(this).data("param-idx");
                        $.ajax({
                            method: 'post',
                            url: '<?php echo ACTION_CLIENT ?>/pengisian_solar_mobil.php',
                            data: {
                                "param": param,
                                "act": "approve_realisasi",
                            },
                            dataType: 'json',
                            success: function(result) {
                                // console.log(result)
                                if (result.status == false) {
                                    setTimeout(function() {
                                        Swal.fire({
                                            title: "Ooppss",
                                            text: result.pesan,
                                            icon: "warning"
                                        }).then((result) => {
                                            location.reload();
                                        });
                                    }, 2000);
                                } else {
                                    setTimeout(function() {
                                        Swal.fire({
                                            title: "Berhasil",
                                            text: result.pesan,
                                            icon: "success"
                                        }).then((result) => {
                                            location.reload();
                                        });
                                    }, 2000);
                                }
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert("Error");
                                // console.log(errorThrown)
                            }
                        })
                    }
                });
            });

            $('#table-grid tbody').on('click', '.btnRealisasi', function() {
                const id = $(this).data("param-idx");
                const volume = $(this).data("param-vol");
                const driver = $(this).data("param-driver");
                const keterangan = $(this).data("param-ket");
                $("#id_pengisian").val(id);
                $("#vol_realisasi").val(volume);
                $("#driver_realisasi").val(driver);
                $("#keterangan_realisasi").val(keterangan);
                // Tampilkan modal
                $("#RealisasiModal").modal("show");
            });

            document.getElementById("btnSimpan").addEventListener("click", function(e) {
                // Ambil form
                const form = document.getElementById("realisasiForm");

                // Gunakan built-in form validation browser
                if (!form.checkValidity()) {
                    // Trigger pesan validasi default browser
                    form.reportValidity();
                    return; // Stop proses lanjut
                }

                // Jika valid, tampilkan SweetAlert konfirmasi
                Swal.fire({
                    title: 'Konfirmasi Simpan',
                    text: "Anda yakin ingin menyimpan data ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#RealisasiModal").modal("hide");
                        $("#loading_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                        form.submit();
                    }
                });
            });

            $('#RealisasiModal').on('hidden.bs.modal', function() {
                $('#realisasiForm')[0].reset(); // reset semua input
            });

        });
    </script>
</body>

</html>