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
$idr     = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$cek = "select a.id_marketing, b.fullname, b.id_role from pro_customer a join acl_user b on a.id_marketing = b.id_user where a.id_customer = '" . $idr . "'";
$row = $con->getRecord($cek);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>

<style>
    #modal_history .modal-dialog {
        width: auto;
        max-width: 95vw !important;
        /* lebar hampir penuh layar */
    }

    #modal_history .modal-content {
        height: auto;
    }

    #modal_history .modal-body {
        max-height: calc(100vh - 200px);
        /* ruang untuk header+footer */
        overflow-y: auto;
    }

    td.clickable {
        cursor: pointer;
    }

    td.clickable:hover {
        background: rgba(0, 0, 0, .05);
    }

    td.disabled {
        cursor: default;
        opacity: .9;
    }
</style>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Ubah Marketing</h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                                <button class="box-title btn btn-primary btn-sm" id="show_history" style="float: right;">Show history</button>
                            </div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT . '/customer-admin-marketing.php'; ?>" id="gform" name="gform" class="form-validasi" method="post" role="form">
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Marketing Lama *</label>
                                            <input type="text" id="lama" name="lama" class="form-control" readonly value="<?php echo $row['fullname']; ?>" />
                                            <input type="hidden" id="id_lama" name="id_lama" readonly value="<?php echo $row['id_marketing']; ?>" />
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="checkbox icheck-primary" style="margin-top: 35px;">
                                                <input type="checkbox" name="select_all" id="select_all"> All Customer
                                                <!-- <label>Marketing Lama *</label> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Marketing Baru *</label>
                                            <select id="market" name="market" class="form-control validate[required] select2">
                                                <option></option>
                                                <?php $con->fill_select("id_user", "fullname", "acl_user", '', "where is_active=1 and id_role in(11,17)", "fullname", false); ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Alasan Mutasi *</label>
                                            <input type="text" name="reason" id="reason" class="form-control validate[required]"></input>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" name="act" value="add" />
                                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                                <a href="<?php echo BASE_URL_CLIENT . "/customer-admin-detail.php?" . paramEncrypt("idr=" . $idr); ?>" class="btn btn-default jarak-kanan">
                                                    <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                                <button type="button" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                            </div>
                                        </div>
                                    </div>
                                    <hr style="margin:5px 0" />
                                    <div class="clearfix">
                                        <div class="col-sm-12"><small>* Wajib Diisi</small></div>
                                    </div>
                                </form>
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

    <div class="modal fade" id="modal_history" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-wider" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">History Mutasi Customer</h5>
                </div>

                <div class="modal-body" id="historyContent">
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-1"><strong id="nama_pt"></strong></h5>
                            <div>Marketing Saat Ini : <strong id="marketing_existing"></strong></div>
                        </div>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:3%" class="text-center">No</th>
                                    <th style="width:20%" class="text-center">Marketing</th>
                                    <th style="width:22%" class="text-center">Periode Aktif</th>
                                    <th style="width:5%" class="text-center">Jumlah Penawaran</th>
                                    <th style="width:5%" class="text-center">Jumlah PO</th>
                                    <th style="width:20%" class="text-center">Mutasi by</th>
                                    <th style="width:25%" class="text-center">Alasan</th>
                                </tr>
                            </thead>
                            <tbody id="bodyResult">

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_list" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_list_title"></h5>
                </div>
                <div class="modal-body" id="modal_list_body">
                    <div class="text-center py-4" id="modal_list_loading" style="display:none;">Loading…</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:3%" class="text-center">No</th>
                                    <th style="width:20%" class="text-center">Nomor</th>
                                    <th style="width:5%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="listResult">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    $(document).ready(function() {
        $("#show_history").click(function() {
            var id_lama = $("#id_lama").val();
            var id_customer = `<?= $idr ?>`;

            $("#modal_history").modal({
                backdrop: "static"
            });

            $.ajax({
                type: "POST",
                url: `<?= BASE_URL . "/web/datatable/data-history-mutasi-customer.php" ?>`,
                dataType: "json",
                data: {
                    id_lama: id_lama,
                    id_customer: id_customer
                },
                success: function(result) {
                    var html = "";
                    $("#nama_pt").html(result.nama_customer);
                    $("#marketing_existing").html(result.marketing_existing);

                    if (result.data.length > 0) {
                        for (var i = 0; i < result.data.length; i++) {
                            var r = result.data[i];
                            var no = i + 1;
                            var reason = r.reason == null ? "N/A" : r.reason;

                            // pastikan API mengirim raw datetime ini:
                            var efFrom = r.effective_from || r.effective_from_raw || ""; // fallback
                            var efTo = r.effective_to || r.effective_to_raw || ""; // bisa ""

                            html += "<tr>";
                            html += "<td align='center'>" + no + "</td>";
                            html += "<td>" + r.fullname + "</td>";
                            html += "<td align='center' nowrap>" + r.effective_from_fmt + " s/d " + r.effective_to_fmt + "</td>";
                            html += `<td class="text-center link-penawaran ${+result.data[i]['total_penawaran']>0?'cell-penawaran clickable':'cell-penawaran disabled'}"
                                data-id_customer="${id_customer}"
                                data-from="${efFrom}"
                                data-to="${efTo}">
                            ${result.data[i]['total_penawaran']}
                            </td>`;

                            html += `<td class="text-center link-po ${+result.data[i]['total_po']>0?'cell-po clickable':'cell-po disabled'}"
                                data-id_customer="${id_customer}"
                                data-from="${efFrom}"
                                data-to="${efTo}">
                            ${result.data[i]['total_po']}
                            </td>`;
                            html += "<td align='center' nowrap'>" + r.mutasi_by + "</td>";
                            html += "<td align='center' nowrap'>" + reason + "</td>";
                            html += "</tr>";
                        }
                    } else {
                        html += "<tr><td align='center' colspan='7'> Data tidak ditemukan </td></tr>";
                    }

                    $("#bodyResult").html(html);
                },
                error: function() {
                    alert("Something Wrong..");
                },
            });
        });

        $(document).on("click", ".link-penawaran, .link-po", function(e) {
            e.preventDefault();
            var $a = $(this);
            var idCustomer = $a.data("id_customer");
            var from = $a.data("from"); // DATETIME
            var to = $a.data("to") || null; // bisa kosong

            var isPenawaran = $a.hasClass("link-penawaran");
            var url = isPenawaran ?
                "<?= BASE_URL . '/web/datatable/get-penawaran-history-mutasi.php' ?>" :
                "<?= BASE_URL . '/web/datatable/get-po-history-mutasi.php' ?>";

            $("#modal_list_title").text(isPenawaran ? "Daftar Penawaran" : "Daftar PO");
            $("#modal_list_table_wrap").empty();
            $("#modal_list_loading").show();
            $("#modal_list").modal({});

            $.ajax({
                    method: "POST",
                    url: url,
                    data: {
                        id_customer: idCustomer,
                        from: from,
                        to: to
                    },
                    dataType: "json",
                })
                .done(function(result) {
                    $("#modal_list_loading").hide();
                    var html = "";
                    if (result.data.length > 0) {
                        for (var i = 0; i < result.data.length; i++) {
                            var r = result.data[i];
                            var no = i + 1;

                            if (isPenawaran) {
                                var urlDetail = r.link_detail_penawaran || '#';

                            } else {
                                var urlDetail = r.link_detail || '#';

                            }

                            html += "<tr>";
                            html += "<td align='center'>" + no + "</td>";
                            html += "<td align='center' nowrap>" + r.nomor_surat + "</td>";
                            html += "<td align='center' nowrap><a href='" + urlDetail + "' target='_blank' class='btn btn-primary btn-sm'>Lihat Detail</a></td>";
                            html += "</tr>";
                        }
                    } else {
                        html += "<tr><td align='center' colspan='3'> Data tidak ditemukan </td></tr>";
                    }
                    $("#listResult").html(html);
                })
                .fail(function(xhr) {
                    $("#modal_list_loading").hide();
                    $("#modal_list_table_wrap").html(`<div class="alert alert-danger">Gagal memuat data (${xhr.status}).</div>`);
                });
        });


        // 1) Validasi + show loading saat submit (bukan saat klik tombol)
        $("#gform").on("submit", function(e) {
            // jalankan validationEngine dulu
            var ok = $("#gform").validationEngine("validate");
            if (!ok) {
                e.preventDefault(); // jangan submit & jangan tampilkan modal
                return;
            }
            $("#loading_modal").modal({
                backdrop: "static"
            }); // tampilkan kalau valid
        });

        // 2) Tombol submit: hanya konfirmasi, lalu submit form
        $("#btnSbmt").on("click", function(e) {
            e.preventDefault();
            Swal.fire({
                title: "Anda yakin?",
                showCancelButton: true,
                confirmButtonText: "Ya",
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#gform").trigger("submit"); // biarkan handler submit di atas yang urus validasi + modal
                }
            });
        });
    })
</script>