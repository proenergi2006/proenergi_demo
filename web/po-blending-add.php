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
$link     = BASE_URL_CLIENT . "/po-blending.php";

if (isset($enk['idr']) && $enk['idr'] !== '') {
    $idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
    $sql = "select a.*, b.nama_vendor, c.jenis_produk, c.merk_dagang, d.nama_area, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal from pro_inventory_vendor a  
				join pro_master_vendor b on a.id_vendor = b.id_master join pro_master_produk c on a.id_produk = c.id_master 
				join pro_master_area d on a.id_area = d.id_master join pro_master_terminal e on a.id_terminal = e.id_master 
				where a.id_master = '" . $idr . "'";
    $rsm = $con->getRecord($sql);
    $action     = "update";
    $section     = "Tambah Data Blending/ Proses Blending";
    $class1     = "";
    $tglinv     = 'value="' . date("d/m/Y", strtotime($rsm['tanggal_inven'])) . '" readonly';
    $vendorN     = $rsm['nama_vendor'];
    $produkN     = $rsm['jenis_produk'] . ' - ' . $rsm['merk_dagang'];
    $areaN         = $rsm['nama_area'];
    $terminalN     = $rsm['nama_terminal'] . ' ' . $rsm['tanki_terminal'] . ', ' . $rsm['lokasi_terminal'];
} else {
    $rsm         = array();
    $action      = "add";
    $section     = "Tambah Data Blending / Proses Blending";
    $class1     = "datepicker";
    $tglinv     = 'value=""';
}
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
                <h1><?php echo $section; ?></h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <form action="<?php echo ACTION_CLIENT . '/po-blending.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Proses*</label>
                                        <div class="col-md-5">
                                            <select id="id_jenis" name="id_jenis" class="form-control select2" style="width:100%;" required>
                                                <option></option>

                                                <option value="8">Blending</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Produk *</label>
                                        <div class="col-md-5">
                                            <select id="id_produk" name="id_produk[]" class="form-control select2" style="width:100%;" multiple required>
                                                <option></option>
                                                <?php $con->fill_select("id_master", "concat(jenis_produk,' - ',merk_dagang)", "pro_master_produk", $rsm['id_produk'], "where is_active=1 and id_master IN(9,10)", "no_urut", false); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Tanggal Blending *</label>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" id="tgl" name="tgl" class="form-control <?php echo $class1; ?>" required data-rule-dateNL="1" <?php echo $tglinv; ?> />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-top:4px double #ddd;">

                            <div id="group_trans_tanki_satu" style="display:none;">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Dari Terminal / Depot *</label>
                                            <div class="col-md-8">
                                                <select id="transfer_tanki_satu_dari" name="transfer_tanki_satu_dari" class="form-control select2" style="width:100%" required>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered tbl_trans_tanki_satu">
                                        <thead>
                                            <tr>
                                                <th class="text-center" rowspan="2" width="80">No</th>
                                                <th class="text-center" rowspan="2" width="">Nomor / Tgl PO</th>
                                                <th class="text-center" rowspan="2" width="150">Tgl Terima</th>
                                                <th class="text-center" colspan="4">Volume (Liter)</th>
                                                <th class="text-center" rowspan="2" width="100">Aksi</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" width="120">Harga Tebus</th>
                                                <th class="text-center" width="120">Terima</th>
                                                <th class="text-center" width="120">Sisa</th>
                                                <th class="text-center" width="200">Blending</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-center" colspan="3"><b>Average Harga</b></td>
                                                <td class="text-center">
                                                    <input type="text" id="avg_harga" name="avg_harga" class="form-control input-sm hitung" readonly />
                                                </td>
                                                <td class="text-center">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center" colspan="6"><b>Total Blending</b></td>
                                                <td class="text-center">
                                                    <input type="text" id="tank_satu_total" name="tank_satu_total" class="form-control input-sm hitung" readonly />
                                                </td>
                                                <td class="text-center">&nbsp;</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <hr style="border-top:4px double #ddd;">

                                <!-- <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Tanggal Blending*</label>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" id="tgl_penerimaan" name="tgl_penerimaan" class="form-control <?php echo $class1; ?>" required data-rule-dateNL="1" <?php echo $tglinv; ?> />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                <!-- 
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Kedalam Terminal / Depot *</label>
                                            <div class="col-md-8">
                                                <select id="transfer_tanki_satu_ke" name="transfer_tanki_satu_ke" class="form-control select2" style="width:100%" required>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->

                            </div>



                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group row">
                                        <label class="col-md-12">Keterangan</label>
                                        <div class="col-md-12">
                                            <textarea id="keterangan" name="keterangan" class="form-control" style="min-height:100px;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="padding:15px 0px;">
                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                                    <i class="fa fa-save jarak-kanan"></i> Simpan
                                </button>
                                <a href="<?php echo $link; ?>" class="btn btn-default" style="min-width:90px;"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                            </div>
                            <hr style="border-top:4px double #ddd; margin:0 0 10px;">
                            <p style="margin:0px"><small>* Wajib Diisi</small></p>

                        </div>
                    </div>
                </form>

                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>

    <div class="modal fade" id="list_po_receive_modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="width:1000px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">List PO Receive</h4>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="list_po_receive_sales_modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="width:1000px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">List PO Receive</h4>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>

    <style type="text/css">
        .form-title {
            font-size: 18px;
            margin: 0 0 10px;
            font-weight: 700;
            text-decoration: underline;
        }

        .table>tfoot>tr>td {
            border: 1px solid #ddd;
            padding: 5px;
            font-size: 11px;
            font-family: arial;
            vertical-align: middle;
        }

        .swal2-modal .swal2-styled {
            padding: 5px;
            min-width: 130px;
            font-family: arial;
            font-size: 14px;
            margin: 10px;
        }
    </style>

    <script>
        $(document).ready(function() {
            $(".hitung").number(true, 0, ".", ",");

            var formValidasiCfg = {
                submitHandler: function(form) {
                    if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
                        swal.fire({
                            icon: "warning",
                            width: '350px',
                            allowOutsideClick: false,
                            html: '<p style="font-size:14px; font-family:arial;">Kolom [Ongkos Angkut] pada tabel rincian harga belum diisi</p>'
                        });
                    } else {
                        $("body").addClass("loading");
                        form.submit();
                    }
                }
            };
            $("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

            var htmlOptVendor = '<tr><td class="text-left" colspan="6" style="height:35px;">Silahkan Tambahkan Data</td></tr>';

            var htmlOptVendorTankiSatu = '<tr><td class="text-left" colspan="7" style="height:35px;">Silahkan Pilih Terminal / Depot</td></tr>';

            $("#id_jenis").on("change", function() {
                let nilai = $(this).val();
                $("#keterangan").val("");
                $("#id_produk").trigger("change");
                if (nilai == "8") {

                    $("#group_depot").hide(400, "swing", function() {
                        $("#id_terminal").val("").trigger("change");
                    });
                    $("#group_data_awal").hide("400", "swing");

                    $("#group_depot02").hide(400, "swing", function() {
                        $("#nomor_po, #id_po_supplier_sales, #id_po_receive_sales, #tgl_po, #tgl_terima").val("");
                        $("#volume_terima, #volume_sisa, #sales_inven").val("");
                    });

                    $("#group_adjustment").hide("400", "swing", function() {
                        $("#adj_inven_sign").val("+").trigger("change");
                        $("#adj_inven").val("");
                    });

                    $("#group_trans_tanki_satu").show("400", "swing", function() {
                        $("#transfer_tanki_satu_dari").val("").trigger("change");
                        $("#transfer_tanki_satu_ke").val("").trigger("change");
                    });
                } else {
                    $("#group_depot").hide(400, "swing", function() {
                        $("#id_terminal").val("").trigger("change");
                    });
                    $("#group_data_awal").hide("400", "swing");

                    $("#group_depot02").hide(400, "swing", function() {
                        $("#nomor_po, #id_po_supplier_sales, #id_po_receive_sales, #tgl_po, #tgl_terima").val("");
                        $("#volume_terima, #volume_sisa, #sales_inven").val("");
                    });

                    $("#group_adjustment").hide("400", "swing", function() {
                        $("#adj_inven_sign").val("+").trigger("change");
                        $("#adj_inven").val("");
                    });

                    $("#group_trans_tanki_satu").hide("400", "swing", function() {
                        $("#transfer_tanki_satu_dari").val("").trigger("change");
                        $("#transfer_tanki_satu_ke").val("").trigger("change");
                    });
                }
            });

            $("#id_terminal").on("change", function() {
                $(".tbl_add_vendor > tbody").html(htmlOptVendor);
                $("#awal_inven_total").val("");
                $("#nomor_po, #id_po_supplier_sales, #id_po_receive_sales, #tgl_po, #tgl_terima").val("");
                $("#volume_terima, #volume_sisa, #sales_inven").val("");
            });

            $("#transfer_tanki_satu_dari").on("change", function() {
                let id_terminal = $(this).val();
                let id_produk = $("#id_produk").val();

                if (id_terminal && id_produk) {
                    $("body").addClass("loading");
                    $.ajax({
                        type: "POST",
                        url: base_url + "/web/getoptdepotinventory_listpoblending.php",
                        data: {
                            "jenis": 1,
                            "id_terminal": id_terminal,
                            "id_produk": id_produk
                        },
                        success: function(data) {
                            $("body").removeClass("loading");
                            $(".tbl_trans_tanki_satu > tbody").html(data);
                            $(".tbl_trans_tanki_satu").find(".tank_satu_vendor_nilai").number(true, 0, ".", ",");
                            $(".tbl_trans_tanki_satu").find("span.notabeltanksatuvendor").each(function(i, v) {
                                $(v).text(i + 1);
                            });
                            $(".tbl_trans_tanki_satu .tank_satu_vendor_nilai").trigger("keyup");
                        }
                    });
                } else {
                    $(".tbl_trans_tanki_satu > tbody").html(htmlOptVendorTankiSatu);
                    $("#tank_satu_total").val("");
                    $("#avg_harga").val("");
                }
            });

            $("#id_produk").on("change", function() {
                let id_produk = $(this).val(); // Mengambil array dari produk yang dipilih
                let id_jenis = $("#id_jenis").val();
                getDepotTerminal(id_jenis, id_produk);
            });

            function getDepotTerminal(id_jenis, id_produk) {
                $("#id_terminal").val("").trigger("change");
                $("#transfer_tanki_satu_dari").val("").trigger("change");
                $("#transfer_tanki_satu_ke").val("").trigger("change");

                $("#id_terminal, #transfer_tanki_satu_dari, #transfer_tanki_satu_ke").html('<option value=""></option>');
                if (id_jenis && id_produk.length > 0) {
                    $("body").addClass("loading");
                    $.ajax({
                        type: "POST",
                        url: base_url + "/web/getoptdepotinventory_blending.php",
                        data: {
                            "id_jenis": id_jenis,
                            "id_produk": id_produk
                        },
                        dataType: "json",
                        cache: false,
                        success: function(data) {
                            $("body").removeClass("loading");
                            var optnya = '<option value=""></option>';
                            $.each(data, function(index, value) {
                                optnya += '<option value="' + value.id_master + '">' + value.nama_terminal + '</option>';
                            });
                            $("#id_terminal").html(optnya);
                            $("#transfer_tanki_satu_dari, #transfer_tanki_satu_ke").html(optnya);
                        }
                    });
                }
            }







            function insertToRole02(index) {
                let param = index.toString().split('|-|');
                $("#nomor_po").val(decodeURIComponent(param[0]));
                $("#id_po_supplier_sales").val(decodeURIComponent(param[1]));
                $("#id_po_receive_sales").val(decodeURIComponent(param[2]));
                $("#tgl_po").val(decodeURIComponent(param[3]));
                $("#tgl_terima").val(decodeURIComponent(param[4]));
                $("#volume_terima").val(decodeURIComponent(param[5]));
                $("#volume_sisa").val(decodeURIComponent(param[6]));
            }

            $(".tbl_trans_tanki_satu").on("keyup blur", ".tank_satu_vendor_nilai", function() {
                let total = 0;
                let totalWeighted = 0; // Ini untuk menyimpan jumlah hasil kali harga tebus dengan sisa inven
                let totalSisaInven = 0; // Total sisa inventaris dari semua baris

                // Hitung total dari tank_satu_vendor_nilai
                // $("input[name='tank_satu_vendor_nilai[]']").each(function(i, v) {
                //     total += ($(v).val() * 1);
                // });

                // // Hitung total dari tank_satu_vendor_avg
                // $("input[name='tank_satu_vendor_avg[]']").each(function(i, v) {
                //     totalAvg += ($(v).val().replace(/,/g, '') * 1); // Hilangkan koma untuk perhitungan
                //     if ($(v).val()) count++; // Hitung jumlah input yang valid
                // });

                // // Set total nilai ke input
                // $("#tank_satu_total").val(total);

                // // Hitung rata-rata harga tebus jika count lebih dari 0
                // let avg = count > 0 ? (totalAvg / count) : 0;

                // // Set rata-rata harga ke input
                // $("#avg_harga").val(avg.toFixed(2)); // Format ke 2 desimal


                // Loop melalui setiap baris untuk menghitung total dari perkalian harga tebus dan sisa inven
                $("input[name='tank_satu_vendor_nilai[]']").each(function(i, v) {
                    let sisaInven = $(v).val() * 1; // Ambil nilai sisa inven dari input
                    let hargaTebus = $("input[name='tank_satu_vendor_avg[]']").eq(i).val().replace(/,/g, '') * 1; // Ambil nilai harga tebus dan hilangkan koma
                    totalWeighted += (sisaInven * hargaTebus); // Hitung total hasil kali harga tebus * sisa inven
                    totalSisaInven += sisaInven; // Hitung total sisa inven
                });

                // Set total sisa inven ke input
                $("#tank_satu_total").val(totalSisaInven);

                // Hitung rata-rata harga berdasarkan total weighted dan total sisa inven
                let avg = totalSisaInven > 0 ? (totalWeighted / totalSisaInven) : 0;

                // Set rata-rata harga ke input
                $("#avg_harga").val(avg.toFixed(2)); // Format ke 2 desimal
            }).
            on("click", "a.hRow", function() {
                var tabel = $(".tbl_trans_tanki_satu");
                var jTbl = tabel.find('tbody > tr').length;
                if (jTbl > 1) {
                    var cRow = $(this).closest('tr');
                    cRow.remove();
                    tabel.find("span.notabeltanksatuvendor").each(function(i, v) {
                        $(v).text(i + 1);
                    });
                    $(".tbl_trans_tanki_satu .tank_satu_vendor_nilai").trigger("keyup");
                } else {
                    $("#transfer_tanki_satu_dari").val("").trigger("change");
                }
            });

        });
    </script>
</body>

</html>