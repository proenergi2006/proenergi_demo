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
    $action     = "update";
    $section     = "PO Suplier Crushed Stone";
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
    $description     =  ($rsm['description']) ? $rsm['description'] : '';
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
} else {
    $idr = null;
    $action     = "add";
    $section     = "Add PO Suplier Crushed Stone";
    $rsm         = array();
    $dt1         = "";
    $dt8         = "";
    $ket        = "";
    $description = "";
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
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                        </div>
                        <div class="box-body">
                            <?php if (isset($rsm['disposisi_po'])) {
                                $reFlag = 1; ?>
                                <div style="padding:15px; margin-bottom:15px; background-color:#ff0000; color:#fff;">
                                    PERHATIAN!! Merubah data ini akan mengulang proses persetujuan data po supplier
                                </div>
                            <?php } ?>
                            <div class="row" hidden>
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Nomor PO *</label>
                                        <div class="col-md-8">
                                            <input type="text" name="dt2" id="dt2" class="form-control" value="<?php echo $rsm['nomor_po'] ?? null; ?>" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Tanggal PO *</label>
                                        <div class="col-md-4">
                                            <?php if (!$dt1) { ?>
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="font-size:12px;"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" name="dt1" id="dt1" class="form-control datepicker" required data-rule-dateNL="1" value="<?php echo $dt1; ?>" autocomplete="off" />
                                                </div>
                                            <?php } else { ?>
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="font-size:12px;"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" name="dt1" id="dt1" class="form-control" required data-rule-dateNL="1" value="<?php echo $dt1; ?>" autocomplete="off" />
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Produk *</label>
                                        <div class="col-md-8">
                                            <select name="dt3" id="dt3" class="form-control select2" style="width:100%;" required <?php echo ($rsm['id_produk'] ? 'disabled' : ''); ?>>
                                                <option></option>
                                                <?php $con->fill_select("id_master", "concat(jenis_produk,' - ',merk_dagang)", "pro_master_produk", $rsm['id_produk'], "where is_active=1", "no_urut", false); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Terminal *</label>
                                        <div class="col-md-8">
                                            <select name="dt6" id="dt6" class="form-control select2" style="width:100%;" required <?php echo ($rsm['id_terminal'] ? 'disabled' : ''); ?>>
                                                <option></option>
                                                <?php
                                                $sqlOpt01 = "
													select a.id_master, concat(a.nama_terminal,' - ',a.tanki_terminal,' - ',a.lokasi_terminal) as terminal 
													from pro_master_terminal a 
													join pro_master_cabang b on a.id_cabang = b.id_master 
													where a.is_active = 1 
													order by a.id_master 
												";
                                                $resOpt01 = $con->getResult($sqlOpt01);
                                                if (count($resOpt01) > 0) {
                                                    foreach ($resOpt01 as $arrOpt01) {
                                                        $selected = ($rsm['id_terminal'] == $arrOpt01['id_master'] ? 'selected' : '');
                                                        echo '<option value="' . $arrOpt01['id_master'] . '" ' . $selected . '>' . $arrOpt01['terminal'] . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Vendor *</label>
                                        <div class="col-md-8">
                                            <select name="dt5" id="dt5" class="form-control select2" style="width:100%;" required <?php echo ($rsm['id_vendor'] ? 'disabled' : ''); ?>>
                                                <option></option>
                                                <?php $con->fill_select("id_master", "nama_vendor", "pro_master_vendor", $rsm['id_vendor'], "where is_active=1", "id_master", false); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Terms *</label>
                                        <div class="col-md-4">
                                            <select name="terms" id="terms" class="form-control select2" style="width:100%;" required>
                                                <option></option>
                                                <option value="COD" <?php echo ($rsm['terms'] == 'COD' ? 'selected' : ''); ?>>C.O.D</option>
                                                <option value="NET" <?php echo ($rsm['terms'] == 'NET' ? 'selected' : ''); ?>>NET</option>
                                                <option value="CBD" <?php echo ($rsm['terms'] == 'CBD' ? 'selected' : ''); ?>>CBD</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="text" id="terms_day" name="terms_day" class="form-control hitung1" <?php echo ($rsm['terms'] != 'NET' ? 'readonly' : ''); ?> value="<?php echo $rsm['terms_day']; ?>" maxlength="3" />
                                                <span class="input-group-addon" style="font-size:12px;">Hari</span>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Kode Tax *</label>
                                        <div class="col-md-8">
                                            <select name="kd_tax" id="kd_tax" class="form-control select2" style="width:100%;" required>
                                                <option></option>
                                                <option value="E" <?php echo ($rsm['kd_tax'] == 'E' ? 'selected' : ''); ?>>E</option>

                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Volume PO *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <input type="text" id="dt10" name="dt10" class="form-control hitung1" value="<?php echo $dt10; ?>" required />
                                                <span class="input-group-addon" style="font-size:12px;">m&sup3;</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Harga Dasar *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt8" name="dt8" class="form-control hitung" required value="<?php echo $dt8; ?>" />
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>





                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Sub Total *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt9" name="dt9" class="form-control hitung" required value="<?php echo $dt9; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="row-dt11">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">PPN 11% *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt11" name="dt11" class="form-control hitung" required value="<?php echo $dt11; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>







                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Total Order *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt14" name="dt14" class="form-control hitung" required value="<?php echo $dt14; ?>" readonly />

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Deskripsi *</label>
                                        <div class="col-md-8">
                                            <textarea id="deskripsi" name="deskripsi" class="form-control" required><?php echo $description; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Catatan PO*</label>
                                        <div class="col-md-8">
                                            <textarea id="ket" name="ket" class="form-control" required><?php echo $ket; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-12">
                                            <input type="checkbox" name="syarat_ketentuan" id="syarat_ketentuan" value="1" <?php echo ($rsm['terms_condition'] != '' ? 'checked' : '') ?>>
                                            Syarat &amp; Ketentuan Tambahan (jika pilih ini, akan tercetak dihalaman kedua)
                                        </label>
                                        <div class="col-md-12">
                                            <textarea name="terms_condition" id="terms_condition" class="form-control" style="height:90px;" <?php echo ($rsm['terms_condition'] != '' ? '' : 'readonly') ?>><?php echo isset($rsm['terms_condition']) ? str_replace('<br />', PHP_EOL, $rsm['terms_condition']) : ''; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

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



                            <?php if ($rsm['cfo_result'] == 1 && $rsm['revert_cfo'] == 0) { ?>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Catatan CFO *</label>
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

                            <div style="margin-bottom:15px;">
                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                                    <i class="fa fa-save jarak-kanan"></i> Simpan</button>

                                <a href="<?php echo BASE_URL_CLIENT . '/vendor-po-new-crushed-stone.php'; ?>" class="btn btn-default" style="min-width:90px;">
                                    <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                            </div>

                            <p><small>* Wajib Diisi</small></p>

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


            $("#syarat_ketentuan").on('ifChanged', function() {
                if ($(this).is(':checked')) {
                    $("#terms_condition").val('');
                    $("#terms_condition").removeAttr("readonly");
                } else {
                    $("#terms_condition").val('');
                    $("#terms_condition").attr('readonly', 'readonly');


                }
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