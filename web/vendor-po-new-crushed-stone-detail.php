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

if (isset($enk['idr']) && $enk['idr'] !== '') {
    $action     = "detail";
    $section     = "Detail PO Suplier Crushed Stone";
    $idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
    $sql = "
			select a.*, a1.id_po_supplier, b.jenis_produk, b.merk_dagang, d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal 
			from new_pro_inventory_vendor_po_crushed_stone a 
			join pro_master_produk b on a.id_produk = b.id_master 
			join pro_master_vendor d on a.id_vendor = d.id_master 
			join pro_master_terminal e on a.id_terminal = e.id_master 
			left join new_pro_inventory_vendor_po_crushed_stone_receive a1 on a.id_master = a1.id_po_supplier 
			where a.id_master = '" . $idr . "'
		";
    $rsm     = $con->getRecord($sql);

    $dt1     = date("d/m/Y", strtotime($rsm['tanggal_inven']));
    $dt8     = ($rsm['harga_tebus']) ? $rsm['harga_tebus'] : '';
    $ket     =  ($rsm['keterangan']) ? $rsm['keterangan'] : '';
    $deskripsi     =  ($rsm['description']) ? $rsm['description'] : '';
    $ceo    =  ($rsm['ceo_summary']) ? $rsm['ceo_summary'] : '';
    $is_ceo   =  ($rsm['ceo_result']) ? $rsm['ceo_result'] : '';
    $ceo_pic    =  ($rsm['ceo_pic']) ? $rsm['ceo_pic'] : '';
    $ceo_tanggal    =  ($rsm['ceo_tanggal']) ? $rsm['ceo_tanggal'] : '';
    $cfo    =  ($rsm['cfo_summary']) ? $rsm['cfo_summary'] : '';
    $cfo_pic    =  ($rsm['cfo_pic']) ? $rsm['cfo_pic'] : '';
    $cfo_tanggal    =  ($rsm['cfo_tanggal']) ? $rsm['cfo_tanggal'] : '';

    $revert_cfo    =  ($rsm['revert_cfo_summary']) ? $rsm['revert_cfo_summary'] : '';
    $revert_ceo    =  ($rsm['revert_ceo_summary']) ? $rsm['revert_ceo_summary'] : '';


    $dt9   = ($rsm['subtotal']) ? $rsm['subtotal'] : '';
    $dt10    = ($rsm['volume_po']) ? $rsm['volume_po'] : '';
    $dt11    = ($rsm['ppn_11']) ? $rsm['ppn_11'] : '';

    $dt14    = ($rsm['total_order']) ? $rsm['total_order'] : '';

    $link2     = BASE_URL_CLIENT . '/vendor-po-new-crushed-stone-add.php?' . paramEncrypt('idr=' . $rsm['id_master']);
    $link3     = ACTION_CLIENT . '/po-izin.php?' . paramEncrypt('idr=' . $idr);
    // $link5     = BASE_URL_CLIENT . '/po-preview.php?' . paramEncrypt('idr=' . $idr);
} else {
    $idr = null;
    $action     = "add";
    $section     = "Add PO Suplier Crushed Stone";
    $rsm         = array();
    $dt1         = "";
    $dt8         = "";
    $ket        = "";
    $deskripsi        = "";
    $dt10         = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "ckeditor"), "css" => array("jqueryUI"))); ?>

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
                <form action="<?php echo ACTION_CLIENT . '/vendor-po-new-crushed-stone.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form">
                    <div class="box box-primary">

                        <div class="box-body">

                            <div class="table-responsive">
                                <table class="table no-border">
                                    <tr>
                                        <td colspan="3"><u><b>Data PO Suplier</b></u></td>
                                    </tr>
                                    <tr>
                                        <td width="180">Nomor PO</td>
                                        <td width="10" class="text-center">:</td>
                                        <td><?php echo $rsm['nomor_po']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal PO</td>
                                        <td class="text-center">:</td>
                                        <td><?php echo $dt1; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Produk</td>
                                        <td class="text-center">:</td>
                                        <td><?php echo $rsm['merk_dagang']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Terminal</td>
                                        <td class="text-center">:</td>
                                        <td><?php echo $rsm['nama_terminal']; ?> - <?php echo $rsm['tanki_terminal']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Vendor</td>
                                        <td class="text-center">:</td>
                                        <td><?php echo $rsm['nama_vendor']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Terms</td>
                                        <td class="text-center">:</td>
                                        <td><?php echo $rsm['terms']; ?> - <?php echo $rsm['terms_day']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Volume PO</td>
                                        <td class="text-center">:</td>
                                        <td><?php echo number_format($dt10); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Harga Dasar</td>
                                        <td class="text-center">:</td>
                                        <td><?php echo number_format($dt8); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Subtotal</td>
                                        <td class="text-center">:</td>
                                        <td><?php echo number_format($dt9); ?></td>
                                    </tr>
                                    <tr>
                                        <td>PPN 11%</td>
                                        <td class="text-center">:</td>
                                        <td><?php echo number_format($dt11); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Total Order</td>
                                        <td class="text-center">:</td>
                                        <td><?php echo number_format($dt14); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Deskripsi</td>
                                        <td class="text-center">:</td>
                                        <td><?php echo $deskripsi; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Catatan PO</td>
                                        <td class="text-center">:</td>
                                        <td><?php echo $ket; ?></td>
                                    </tr>
                                </table>


                                <?php if ($rsm['cfo_result'] == 1 && $rsm['revert_cfo'] == 0) { ?>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group form-group-sm">
                                                <label class="control-label col-md-3">Catatan CFO</label>
                                                <div class="col-md-8">
                                                    <div class="form-control" style="height:auto"><i><?php echo $cfo . "<br>" . $cfo_pic . " - " . date("d/m/Y H:i:s", strtotime($cfo_tanggal)) . " WIB"; ?></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php } ?>

                                <?php if ($rsm['ceo_result'] == 1 && $rsm['revert_ceo'] == 0) { ?>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group form-group-sm">
                                                <label class="control-label col-md-3">Catatan CEO *</label>
                                                <div class="col-md-8">
                                                    <div class="form-control" style="height:auto"><i><?php echo $ceo . "<br>" . $ceo_pic . " - " . date("d/m/Y H:i:s", strtotime($ceo_tanggal)) . " WIB"; ?></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php } ?>





                                <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />
                                <?php if ($rsm['revert_cfo'] == 1) { ?>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group form-group-sm">
                                                <label class="control-label col-md-3">Catatan Pengembalian CFO</label>
                                                <div class="col-md-8">
                                                    <div class="form-control" style="height:auto"><?php echo $revert_cfo; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($rsm['revert_ceo'] == 1) { ?>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group form-group-sm">
                                                <label class="control-label col-md-3">Catatan Pengembalian CEO</label>
                                                <div class="col-md-8">
                                                    <div class="form-control" style="height:auto"><?php echo $revert_ceo; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>






                                <div style="margin-bottom:15px;">
                                    <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                    <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                    <a class="btn btn-primary jarak-kanan" style="min-width:80px;" href="<?php echo $link2; ?>"> <i class="fas fa-edit"></i>Edit</a>
                                    <?php if ($rsm['disposisi_po'] == 0) { ?>
                                        <a class="btn btn-success jarak-kanan izin-pd" style="min-width:80px;" href="javascript:void(0);" id="btnPersetujuan">Persetujuan</a>
                                    <?php } ?>
                                    <!-- <a class="btn btn-info jarak-kanan" style="min-width:80px;" target="_blank" href="<?php echo $link5; ?>">Preview PO</a> -->
                                    <a href="<?php echo BASE_URL_CLIENT . '/vendor-po-new-crushed-stone.php'; ?>" class="btn btn-default" style="min-width:90px;">
                                        <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                </div>


                            </div>
                        </div>
                </form>

                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>




    <div class="modal fade" id="validasi_vol_terima" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-md">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Informasi</h4>
                </div>
                <div class="modal-body vol_info"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {


            $('#btnSbmt').on('click', function(e) {
                e.preventDefault(); // Mencegah form langsung submit

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menyimpan data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#gform').submit(); // Submit form jika user menekan "Ya, Simpan!"
                    }
                });
            });


            document.getElementById("btnPersetujuan").addEventListener("click", function() {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan diarahkan untuk persetujuan PO.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "<?php echo $link3; ?>";
                    }
                });
            });



            function customRound(num) {
                // Check if the number is negative
                if (num < 0) {
                    return Math.ceil(num - 0.5); // For negative numbers, round up
                }

                // For positive numbers
                const decimalPart = num - Math.floor(num); // Get the decimal part

                if (decimalPart < 0.5) {
                    return Math.floor(num); // Round down
                } else {
                    return Math.floor(num) + 1; // Round up
                }
            }

            // Format angka dengan plugin number
            $(".hitung1").number(true, 0, ".", ",");
            $(".hitung").number(true, 2, ".", ",");

            $('.hitung, .hitung1').on('input', function() {
                // Ambil nilai Volume PO dan Harga Dasar
                //  var kodeTax = $('#kd_tax').val();
                // var volumePO = parseFloat($('#dt10').val()) || 0;
                // var hargaDasar = parseFloat($('#dt8').val()) || 0;
                // var subTotal = volumePO * hargaDasar;
                // var ppn11 = (11 * subTotal) / 100;
                // var totalOrder = (subTotal + ppn11 + pph + hasil);

                // // // Tampilkan hasil di input Sub Total
                // $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                // $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                // $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                // $('#dt13').val(hasil.toFixed(0));
                // $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
            });

            var formValidasiCfg = {
                submitHandler: function(form) {
                    if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
                        swal.fire({
                            icon: "warning",
                            width: '350px',
                            allowOutsideClick: false,
                            html: '<p style="font-size:14px; font-family:arial;">Kolom [Ongkos Angkut] pada tabel rincian harga belum diisi</p>'
                        });
                    } else if ($("#kd_tax").val() == 'EC' && $("#pphnya").val() == '') {
                        swal.fire({
                            icon: "warning",
                            width: '350px',
                            allowOutsideClick: false,
                            html: '<p style="font-size:14px; font-family:arial;">Kolom [PPH 22] belum diisi</p>'
                        });
                    } else if ($("#terms").val() == 'NET' && $("#terms_day").val() == '') {
                        swal.fire({
                            icon: "warning",
                            width: '350px',
                            allowOutsideClick: false,
                            html: '<p style="font-size:14px; font-family:arial;">Kolom [Hari untuk terms NET] belum diisi</p>'
                        });
                    } else {
                        $("body").addClass("loading");
                        $.ajax({
                            type: 'POST',
                            url: base_url + "/web/action/vendor-po-new.php",
                            data: {
                                act: 'cek',
                                q1: $("input[name='idr']").val(),
                                q2: $("#dt2").val()
                            },
                            cache: false,
                            dataType: 'json',
                            success: function(data) {
                                if (!data.hasil) {
                                    $("body").removeClass("loading");
                                    swal.fire({
                                        icon: "warning",
                                        width: '350px',
                                        allowOutsideClick: false,
                                        html: '<p style="font-size:14px; font-family:arial;">' + data.pesan + '</p>'
                                    });
                                } else {
                                    form.submit();
                                }
                            }
                        });
                    }
                }
            };
            $("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

            // $("#kd_tax").on("change", function() {
            //     let nilai = $(this).val();
            //     var kategori_oa = $('#kategori_oa').val();
            //     var kodeTax = $('#kd_tax').val();
            //     var volumePO = parseFloat($('#dt10').val()) || 0;
            //     var hargaDasar = parseFloat($('#dt8').val()) || 0;
            //     var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;
            //     var pbbkb = $("#pbbkb_tawar").val();


            //     var subTotal = volumePO * hargaDasar;


            //     var ppn11 = ((hargaDasar * volumePO) * 11) / 100;



            //     var pph = 0;
            //     var total = volumePO * hargaDasar;


            //     var totalOrder = (subTotal + ppn11);

            //     $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
            //     $('#dt12').val(pph.toFixed(0));
            //     $('#dt11').val(ppn11.toFixed(0));
            //     $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
            //     $('#nominal_iuran').val(iuran_migas.toFixed(0));
            //     $('#iuran_migas').attr("checked", false);
            //     $('.icheckbox_square-blue').removeClass("checked");
            // });

            function hitungTotal() {
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;

                var subTotal = volumePO * hargaDasar;
                var ppn11 = (subTotal * 11) / 100;
                var totalOrder = subTotal + ppn11;

                $('#dt9').val(subTotal.toFixed(4)); // Sub Total
                $('#dt11').val(ppn11.toFixed(0)); // PPN 11%
                $('#dt14').val(totalOrder.toFixed(4)); // Total Order
            }

            // Trigger hitungTotal() saat Volume PO atau Harga Dasar berubah
            $('#dt10, #dt8').on('input', function() {
                hitungTotal();
            });

            // $("#kd_tax").on("change", function() {
            //     let nilai = $(this).val();
            //     if (nilai == 'E') {
            //         $(".form-group:has(#dt12)").hide(); // Menghilangkan semua elemen terkait PPH 22
            //     } else {
            //         $(".form-group:has(#dt12)").show(); // Menampilkan kembali semua elemen terkait PPH 22
            //         $("#dt12").attr("readonly", "readonly").val(""); // Menambah readonly dan menghapus nilai
            //     }
            // });
            $("#terms").on("change", function() {
                let nilai = $(this).val();
                if (nilai == 'NET') $("#terms_day").removeAttr("readonly");
                else $("#terms_day").attr("readonly", "readonly");
            });

            $("#pbbkb_tawar").change(function() {
                var plat = $('#kategori_plat').val();
                var kategori_oa = $('#kategori_oa').val();
                var kodeTax = $('#kd_tax').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var pbbkb = $(this).val();
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;

                if (kategori_oa == 1) {
                    var subTotal = volumePO * hargaDasar;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100; // Pembulatan tanpa desimal
                    }
                    var ppn11 = (11 * subTotal) / 100;
                } else {
                    var subTotal = volumePO * (hargaDasar + ongkos_angkut);
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = ((volumePO * hargaDasar) * 0.3) / 100; // Pembulatan tanpa desimal
                    }
                    if (plat == "Hitam" || plat == "") {
                        var ppn11 = (11 * subTotal) / 100;
                    } else {
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                    }
                }
                var iuran_migas = 0;
                var total = volumePO * hargaDasar;
                var hasil = (total * pbbkb) / 100;

                var totalOrder = (subTotal + ppn11 + pph + hasil + iuran_migas);

                $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#dt13').val(hasil.toFixed(0));
                $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                // alert(hasil)
                $('#nominal_iuran').val(iuran_migas.toFixed(0));
                $('#iuran_migas').attr("checked", false);
                $('.icheckbox_square-blue').removeClass("checked");
            })

            $("#kategori_oa").change(function() {
                var val = $(this).val();

                if (val == 1) {
                    var kodeTax = $('#kd_tax').val();
                    var volumePO = parseFloat($('#dt10').val()) || 0;
                    var hargaDasar = parseFloat($('#dt8').val()) || 0;
                    var pbbkb = parseFloat($('#dt13').val()) || 0;

                    var subTotal = volumePO * hargaDasar;
                    var ppn11 = (11 * subTotal) / 100;
                    var iuran_migas = 0;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100; // Pembulatan tanpa desimal
                    }

                    var totalOrder = (subTotal + ppn11 + pph + pbbkb + iuran_migas);

                    $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                    $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal

                    $("#ongkos_angkut").val(0);
                    $("#row_oa").addClass("hide");
                    $("#row-plat").addClass("hide");
                    $("#kategori_plat").val(null).trigger("change");
                    $("#ongkos_angkut").removeAttr("required", true);
                    $("#kategori_plat").removeAttr("required", true);
                    $('#nominal_iuran').val(iuran_migas.toFixed(0));
                    $('#iuran_migas').attr("checked", false);
                    $('.icheckbox_square-blue').removeClass("checked");
                } else {
                    var kodeTax = $('#kd_tax').val();
                    var volumePO = parseFloat($('#dt10').val()) || 0;
                    var hargaDasar = parseFloat($('#dt8').val()) || 0;
                    var pbbkb = parseFloat($('#dt13').val()) || 0;

                    var subTotal = volumePO * hargaDasar;
                    var ppn11 = (11 * subTotal) / 100;
                    var iuran_migas = 0;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100; // Pembulatan tanpa desimal
                    }

                    var totalOrder = (subTotal + ppn11 + pph + pbbkb + iuran_migas);

                    $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                    $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal

                    $("#ongkos_angkut").val(0);
                    $("#row_oa").addClass("hide");
                    $("#row-plat").addClass("hide");
                    $("#kategori_plat").val(null).trigger("change");
                    $("#ongkos_angkut").removeAttr("required", true);
                    $("#kategori_plat").removeAttr("required", true);

                    $("#row_oa").removeClass("hide");
                    $("#row-plat").removeClass("hide");
                    $("#ongkos_angkut").prop("required", true);
                    $("#kategori_plat").prop("required", true);

                    var iuran_migas = 0;
                    $('#nominal_iuran').val(iuran_migas.toFixed(0));
                    $('#iuran_migas').attr("checked", false);
                    $('.icheckbox_square-blue').removeClass("checked");
                }
            })

        });
    </script>
</body>

</html>