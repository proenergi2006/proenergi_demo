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
$idcust     = isset($enk["idcust"]) ? htmlspecialchars($enk["idcust"], ENT_QUOTES) : '';
$id_role = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);
$id_wilayah = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$id_user = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);

if ($id_role == '11' || $id_role == '17') {
    $customer = "SELECT id_customer, nama_customer, kode_pelanggan FROM pro_customer WHERE id_marketing = '" . $id_user . "' AND kode_pelanggan != ''";
    $result     = $con->getResult($customer);
} elseif ($id_role == '18') {
    $customer = "SELECT id_customer, nama_customer, kode_pelanggan FROM pro_customer WHERE id_wilayah='" . $id_wilayah . "' AND kode_pelanggan != ''";
    $result     = $con->getResult($customer);
}

if (isset($enk['idr']) && $enk['idr'] !== '') {
    $action     = "update";
    $section     = "Edit Penerima Refund";
    $idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
    $sql = "select * from pro_master_penerima_refund where id = '" . $idr . "'";
    $rsm = $con->getRecord($sql);

    if ($rsm['is_active'] == 1) {
        $chk        = "checked";
    } else {
        $chk        = "";
    }
} else {
    $action     = "add";
    $section     = "Tambah Penerima Refund";
    $chk        = "checked";
}

if ($idcust) {
    $sqlcust = "SELECT nama_customer, kode_pelanggan FROM pro_customer WHERE id_customer = '" . $idcust . "'";
    $rsm_cust = $con->getRecord($sqlcust);
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber"))); ?>

<style>
    .thumbnail {
        max-width: 300px;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .thumbnail:hover {
        transform: scale(1.05);
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 5000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.8);
    }

    .modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
    }
</style>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1><?php echo $section; ?></h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="box box-primary">
                    <div class="box-header with-border bg-light-blue">
                        <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                    </div>
                    <div class="box-body">
                        <form action="<?php echo ACTION_CLIENT . '/master-penerima-refund.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Customer *</label>
                                        <div class="col-md-6">
                                            <?php if ($idcust) : ?>
                                                <input type="text" class="form-control" value="<?= $rsm_cust['kode_pelanggan'] . ' - ' .  $rsm_cust['nama_customer'] ?>" readonly>
                                                <input type="hidden" id="customer" name="customer" class="form-control" value="<?= $idcust ?>" readonly>
                                            <?php else : ?>
                                                <select id="customer" name="customer" class="form-control select2" required <?= $rsm['is_bm'] != 0 ? 'disabled' : '' ?>>
                                                    <option value=""></option>
                                                    <?php foreach ($result as $key) : ?>
                                                        <option <?= $rsm['id_customer'] == $key['id_customer'] ? 'selected' : '' ?> value="<?= $key['id_customer'] ?>"><?= $key['kode_pelanggan'] . ' - ' . $key['nama_customer'] ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Nama Penerima *</label>
                                        <div class="col-md-6">
                                            <input type="text" id="nama_penerima" name="nama_penerima" class="form-control" required value="<?php echo $rsm['nama']; ?>" <?= $rsm['is_bm'] != 0 ? 'disabled' : '' ?> autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Divisi *</label>
                                        <div class="col-md-6">
                                            <input type="text" id="divisi" name="divisi" class="form-control" required value="<?php echo $rsm['divisi']; ?>" <?= $rsm['is_bm'] != 0 ? 'disabled' : '' ?> autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Nomor KTP *</label>
                                        <div class="col-md-6">
                                            <input type="text" id="no_ktp" name="no_ktp" class="form-control" required value="<?php echo $rsm['no_ktp']; ?>" <?= $rsm['is_bm'] != 0 ? 'disabled' : '' ?> autocomplete="off" onpaste="handlePaste(event)" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Foto KTP *</label>
                                        <div class="col-md-6">
                                            <input type="file" id="foto_ktp" name="foto_ktp" class="form-control" required value="<?php echo $rsm['foto_ktp']; ?>" accept="image/png, image/jpeg, image/jpg, application/pdf" <?= $rsm['is_bm'] != 0 ? 'disabled' : '' ?> />
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4"></label>
                                        <div class="col-md-6">
                                            <?php if ($rsm) : ?>
                                                <?php
                                                $encrypt_url = paramEncrypt($rsm['foto_ktp'])
                                                ?>
                                                <a href="<?php echo ACTION_CLIENT . '/view_file.php?url=' . $encrypt_url . '&tipe=ktp'; ?>" target="_blank">Preview File</a>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Foto NPWP (Optional)</label>
                                        <div class="col-md-6">
                                            <input type="file" id="foto_npwp" name="foto_npwp" class="form-control" required value="<?php echo $rsm['foto_npwp']; ?>" accept="image/png, image/jpeg, image/jpg, application/pdf" <?= $rsm['is_bm'] != 0 ? 'disabled' : '' ?> />
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4"></label>
                                        <div class="col-md-6">
                                            <?php if ($rsm && $rsm['foto_npwp']) : ?>
                                                <!-- <a href="<?= BASE_URL . "/files/uploaded_user/npwp_penerima_refund/" . $rsm['foto_npwp'] ?>" target="_blank">Preview File</a> -->
                                                <?php
                                                $encrypt_url_npwp = paramEncrypt($rsm['foto_npwp'])
                                                ?>
                                                <a href="<?php echo ACTION_CLIENT . '/view_file.php?url=' . $encrypt_url_npwp . '&tipe=npwp'; ?>" target="_blank">Preview File</a>
                                            <?php else : ?>
                                                Tidak ada foto NPWP
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Nama Bank *</label>
                                        <?php
                                        $json_object = file_get_contents($public_base_directory . "/libraries/js/indonesia-bank.json");
                                        $bank = json_decode($json_object, true);
                                        ?>
                                        <div class="col-md-6">
                                            <select id="bank" name="bank" class="form-control select2" required <?= $rsm['is_bm'] != 0 ? 'disabled' : '' ?>>
                                                <option value=""></option>
                                                <?php foreach ($bank as $key) : ?>
                                                    <option <?= $rsm['kode_bank'] == $key['code'] ? 'selected' : '' ?> value="<?= $key['name'] ?>" data-kode="<?= $key['code'] ?>"><?= $key['name'] ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Kode Bank *</label>
                                        <div class="col-md-6">
                                            <input type="text" id="kode_bank" name="kode_bank" class="form-control" required value="<?php echo $rsm['kode_bank']; ?>" readonly <?= $rsm['is_bm'] != 0 ? 'disabled' : '' ?> />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Nomor Rekening *</label>
                                        <div class="col-md-6">
                                            <input type="text" id="no_rekening" name="no_rekening" class="form-control" required value="<?php echo $rsm['no_rekening']; ?>" <?= $rsm['is_bm'] != 0 ? 'disabled' : '' ?> autocomplete="off" onpaste="handlePaste(event)" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">Atas Nama *</label>
                                        <div class="col-md-6">
                                            <input type="text" id="atas_nama" name="atas_nama" class="form-control" required value="<?php echo $rsm['atas_nama']; ?>" <?= $rsm['is_bm'] != 0 ? 'disabled' : '' ?> autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <div class="col-md-12">
                                            <div class="checkbox">
                                                <label class="rtl">
                                                    <input type="checkbox" name="active" id="active" value="1" class="form-control" <?php echo $chk; ?> /> Active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                            <div style="margin-bottom:15px;">
                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                <?php if ($idr) : ?>
                                    <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                                        <i class="fa fa-save jarak-kanan"></i> Simpan
                                    </button>
                                    <a href="<?php echo BASE_URL_CLIENT . '/master-penerima-refund.php'; ?>" class="btn btn-default" style="min-width:90px;">
                                        <i class="fa fa-reply jarak-kanan"></i> Kembali
                                    </a>
                                <?php else : ?>
                                    <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                                        <i class="fa fa-save jarak-kanan"></i> Simpan
                                    </button>
                                    <a href="<?php echo BASE_URL_CLIENT . '/master-penerima-refund.php'; ?>" class="btn btn-default" style="min-width:90px;">
                                        <i class="fa fa-reply jarak-kanan"></i> Kembali
                                    </a>
                                <?php endif ?>
                            </div>

                            <p class="<?= $idr ? 'hide' : '' ?>"><small>* Wajib Diisi</small></p>
                        </form>
                    </div>
                </div>

                <?php if ($idr) : ?>
                    <?php
                    if ($rsm['is_bm'] == 1) {
                        $status_bm = "Approved by " . $rsm['bm_by'];
                        $status_bm2 = tgl_indo($rsm['bm_date']) . " " . date("H:i:s", strtotime($rsm['bm_date']));
                    } elseif ($rsm['is_bm'] == 2) {
                        $status_bm = "Rejected by " . $rsm['bm_by'];
                        $status_bm2 = tgl_indo($rsm['bm_date']) . " " . date("H:i:s", strtotime($rsm['bm_date']));
                    } else {
                        $status_bm = "Verifikasi BM";
                    }
                    if ($rsm['is_ceo'] == 1) {
                        $status_ceo = "Approved by " . $rsm['ceo_by'];
                        $status_ceo2 = tgl_indo($rsm['ceo_date']) . " " . date("H:i:s", strtotime($rsm['ceo_date']));
                    } elseif ($rsm['is_ceo'] == 2) {
                        $status_ceo = "Rejected by " . $rsm['ceo_by'];
                        $status_ceo2 = tgl_indo($rsm['ceo_date']) . " " . date("H:i:s", strtotime($rsm['ceo_date']));
                    } else {
                        $status_ceo = "Verifikasi CEO";
                    }
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Catatan BM</label>
                                <textarea style="resize: none; height:100px;" class="form-control" name="" id="" readonly><?= $status_bm . " " . $status_bm2 ?>&#13;&#10;&#13;&#10;<?= $rsm['catatan_bm'] ?>
                                </textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Catatan CEO</label>
                                <textarea style="resize: none; height:100px;" class="form-control" name="" id="" readonly><?= $status_ceo . " " . $status_ceo2 ?>&#13;&#10;&#13;&#10;<?= $rsm['catatan_ceo'] ?>
                                </textarea>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

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

    <div id="modal" class="modal">
        <span class="close" id="closeModal">&times;</span>
        <img class="modal-content" id="modalImage">
        <div id="caption"></div>
    </div>

    <script>
        // Tidak boleh paste huruf
        function handlePaste(event) {
            // Mengambil data yang akan di-paste
            const pasteData = event.clipboardData.getData('text');

            // Menghapus semua karakter selain angka
            const numericData = pasteData.replace(/\D/g, ''); // \D berarti semua yang bukan angka

            // Mencegah data yang tidak valid agar tidak masuk
            event.preventDefault();

            // Menempelkan hanya angka yang sudah difilter
            document.getElementById('no_ktp').value = numericData;
            document.getElementById('no_rekening').value = numericData;
        }

        $("#no_rekening, #no_ktp").on("keypress", function(e) {
            if (e.keyCode != 8 && e.keyCode != 0 && e.keyCode < 48 || e.keyCode > 57)
                e.preventDefault();
        });

        // function onlyNumberKey(evt) {
        //     let ASCIICode = (evt.which) ? evt.which : evt.keyCode
        //     if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
        //         return false;
        //     return true;
        // }
        $(document).ready(function() {
            $('#myImage').click(function() {
                $('#modal').css('display', 'block');
                $('#modalImage').attr('src', $(this).attr('src'));
                $('#caption').text($(this).attr('alt'));
            });

            $('#closeModal').click(function() {
                $('#modal').css('display', 'none');
            });

            $(window).click(function(event) {
                if ($(event.target).is('#modal')) {
                    $('#modal').css('display', 'none');
                }
            });

            $('#bank').change(function() {
                var kode = $(this).find(':selected').attr('data-kode');
                if ($(this).val() == "") {
                    $('#kode_bank').val("");
                } else {
                    $('#kode_bank').val(kode);
                }
            })

            $('#btnSbmt').on('click', function(e) {
                e.preventDefault();
                var action = `<?= $action ?>`;
                var form = $(this).parents('form');
                var customer = $("#customer").val();
                var nama_penerima = $("#nama_penerima").val();
                var divisi = $("#divisi").val();
                var no_ktp = $("#no_ktp").val();
                var foto_ktp = $("#foto_ktp").val();
                var bank = $("#bank").val();
                var kode_bank = $("#kode_bank").val();
                var no_rekening = $("#no_rekening").val();
                var atas_nama = $("#atas_nama").val();
                Swal.fire({
                    title: "Anda yakin?",
                    showCancelButton: true,
                    confirmButtonText: "Ya",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loading_modal").modal({
                            backdrop: 'static'
                        });
                        if (action == 'add') {
                            if (customer == "" || nama_penerima == "" || divisi == "" || no_ktp == "" || foto_ktp == "" || bank == "" || kode_bank == "" || no_rekening == "" || atas_nama == "") {
                                Swal.fire({
                                    icon: "warning",
                                    title: "Oops...",
                                    text: "Customer, Nama Penerima, Divisi, Nomor KTP, Foto KTP, Bank, Kode Bank, No Rekening dan Atas Nama tidak boleh kosong"
                                });
                            } else {
                                form.submit();
                            }

                        } else {
                            form.submit();
                        }
                    }
                });
            });

            // $("#btnSbmt").on("click", function(e) {
            //     e.preventDefault();
            //     Swal.fire({
            //         title: "Anda yakin?",
            //         showCancelButton: true,
            //         confirmButtonText: "Ya",
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             var formValidasiCfg = {
            //                 submitHandler: function(form) {
            //                     $("#loading_modal").modal({
            //                         keyboard: false,
            //                         backdrop: 'static'
            //                     });

            //                     if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
            //                         $("#loading_modal").modal("hide");
            //                         $.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
            //                         setErrorFocus($("#nup_fee"), $("form#gform"), false);
            //                     } else {
            //                         form.submit();
            //                     }
            //                 }
            //             };
            //             $("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));
            //         }
            //     });
            // })
        });
    </script>
</body>

</html>