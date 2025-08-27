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
$link     = BASE_URL_CLIENT . "/vendor-inven-terminal-new.php";

if (isset($enk['idr']) && $enk['idr'] !== '') {
    $idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
    $sql = "select a.*, b.nama_vendor, c.jenis_produk, c.merk_dagang, d.nama_area, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal from pro_inventory_vendor a  
				join pro_master_vendor b on a.id_vendor = b.id_master join pro_master_produk c on a.id_produk = c.id_master 
				join pro_master_area d on a.id_area = d.id_master join pro_master_terminal e on a.id_terminal = e.id_master 
				where a.id_master = '" . $idr . "'";
    $rsm = $con->getRecord($sql);
    $action     = "update";
    $section     = "Tambah Data Awal / Adjustment Inventory";
    $class1     = "";
    $tglinv     = 'value="' . date("d/m/Y", strtotime($rsm['tanggal_inven'])) . '" readonly';
    $vendorN     = $rsm['nama_vendor'];
    $produkN     = $rsm['jenis_produk'] . ' - ' . $rsm['merk_dagang'];
    $areaN         = $rsm['nama_area'];
    $terminalN     = $rsm['nama_terminal'] . ' ' . $rsm['tanki_terminal'] . ', ' . $rsm['lokasi_terminal'];
} else {
    $rsm         = array();
    $action      = "add";
    $section     = "Tambah Adjustment Stock";
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
                <form action="<?php echo ACTION_CLIENT . '/adjustment-stock.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Jenis Penambahan *</label>
                                        <div class="col-md-5">
                                            <select id="id_jenis" name="id_jenis" class="form-control select2" style="width:100%;" required>
                                                <option></option>

                                                <option value="3">Adjustment</option>

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
                                            <select id="id_produk" name="id_produk" class="form-control select2" style="width:100%;" required>
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
                                        <label class="control-label col-md-3">Tanggal *</label>
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

                            <div id="group_depot" style="display:none;">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Terminal / Depot *</label>
                                            <div class="col-md-8">
                                                <select id="id_terminal" name="id_terminal" class="form-control select2" style="width:100%;" required>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="group_data_awal" style="display:none;">
                                <div style="margin-bottom:10px;">
                                    <a class="btn btn-success btn-sm btn_add_awal"><i class="fa fa-plus jarak-kanan"></i> Tambah</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered tbl_add_vendor">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="100">Aksi</th>
                                                <th class="text-center" width="100">No</th>
                                                <th class="text-center" width="250">Nomor / Tgl PO</th>
                                                <th class="text-center" width="">Nama Vendor</th>
                                                <th class="text-center" width="150">Tgl Terima</th>
                                                <th class="text-center" width="230">Vol Terima</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-center" colspan="5"><b>Total Data Awal</b></td>
                                                <td class="text-center">
                                                    <input type="text" id="awal_inven_total" name="awal_inven_total" class="form-control input-sm hitung" readonly />
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div id="group_depot02" style="display:none;">


                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Tanggal PO *</label>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" id="tgl_po" name="tgl_po" class="form-control" readonly />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Tanggal Terima *</label>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" id="tgl_terima" name="tgl_terima" class="form-control" readonly />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Volume Terima *</label>
                                            <div class="col-md-5">
                                                <div class="input-group">
                                                    <input type="text" id="volume_terima" name="volume_terima" class="form-control hitung" readonly />
                                                    <span class="input-group-addon">Liter</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Sisa Volume *</label>
                                            <div class="col-md-5">
                                                <div class="input-group">
                                                    <input type="text" id="volume_sisa" name="volume_sisa" class="form-control hitung" readonly />
                                                    <span class="input-group-addon">Liter</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Sales *</label>
                                            <div class="col-md-5">
                                                <div class="input-group">
                                                    <input type="text" id="sales_inven" name="sales_inven" class="form-control hitung" required />
                                                    <span class="input-group-addon">Liter</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="group_adjustment" style="display:none;">

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Nomor PO *</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="text" id="nomor_po" name="nomor_po" class="form-control" readonly required />
                                                    <input type="hidden" id="id_po_supplier_sales" name="id_po_supplier_sales" value="" />
                                                    <input type="hidden" id="id_po_receive_sales" name="id_po_receive_sales" value="" />
                                                    <span class="input-group-btn">
                                                        <button type="button" class="btn btn-sm btn-primary picker_po"><i class="fa fa-search"></i></button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Adjustment *</label>
                                            <div class="col-md-5">
                                                <select id="adj_inven_sign" name="adj_inven_sign" class="form-control select2">
                                                    <option value="+">Bertambah / Gain (+)</option>
                                                    <option value="-">Berkurang / Loss (-)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Volume *</label>
                                            <div class="col-md-5">
                                                <div class="input-group">
                                                    <input type="text" id="adj_inven" name="adj_inven" class="form-control hitung" required />
                                                    <span class="input-group-addon">Liter</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                                                <th class="text-center" colspan="3">Volume (Liter)</th>
                                                <th class="text-center" rowspan="2" width="100">Aksi</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" width="150">Terima</th>
                                                <th class="text-center" width="150">Sisa</th>
                                                <th class="text-center" width="200">Transfer</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-center" colspan="5"><b>Total Transfer</b></td>
                                                <td class="text-center">
                                                    <input type="text" id="tank_satu_total" name="tank_satu_total" class="form-control input-sm hitung" readonly />
                                                </td>
                                                <td class="text-center">&nbsp;</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <hr style="border-top:4px double #ddd;">

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
                                </div>

                            </div>

                            <hr style="border-top:4px double #ddd;">

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
                    <h4 class="modal-title">List PO Receiv</h4>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>

    <!-- <div class="modal fade" id="list_po_receive_sales_modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="width:1000px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">List PO Receive</h4>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div> -->

    <div class="modal fade" id="list_po_receive_sales_modal" role="dialog" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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

                if (nilai == "1") {
                    $("#group_depot").show(400, "swing", function() {
                        $("#id_terminal").val("").trigger("change");
                    });
                    $("#group_data_awal").show("400", "swing");

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
                } else if (nilai == "2") {
                    $("#group_depot").show(400, "swing", function() {
                        $("#id_terminal").val("").trigger("change");
                    });
                    $("#group_data_awal").hide("400", "swing");

                    $("#group_depot02").show(400, "swing", function() {
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
                } else if (nilai == "3") {
                    $("#group_depot").show(400, "swing", function() {
                        $("#id_terminal").val("").trigger("change");
                    });
                    $("#group_data_awal").hide("400", "swing");

                    $("#group_depot02").hide(400, "swing", function() {
                        $("#nomor_po, #id_po_supplier_sales, #id_po_receive_sales, #tgl_po, #tgl_terima").val("");
                        $("#volume_terima, #volume_sisa, #sales_inven").val("");
                    });

                    $("#group_adjustment").show("400", "swing", function() {
                        $("#adj_inven_sign").val("+").trigger("change");
                        $("#adj_inven").val("");
                    });

                    $("#group_trans_tanki_satu").hide("400", "swing", function() {
                        $("#transfer_tanki_satu_dari").val("").trigger("change");
                        $("#transfer_tanki_satu_ke").val("").trigger("change");
                    });
                } else if (nilai == 4) {
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
                        url: base_url + "/web/getoptdepotinventory_listpo.php",
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
                }
            });

            $("#id_produk").on("change", function() {
                getDepotTerminal($("#id_jenis").val(), $("#id_produk").val());
            });

            function getDepotTerminal(id_jenis, id_produk) {
                $("#id_terminal").val("").trigger("change");
                $("#transfer_tanki_satu_dari").val("").trigger("change");
                $("#transfer_tanki_satu_ke").val("").trigger("change");

                $("#id_terminal, #transfer_tanki_satu_dari, #transfer_tanki_satu_ke").html('<option value=""></option>');
                if (id_jenis && id_produk) {
                    $("body").addClass("loading");
                    $.ajax({
                        type: "POST",
                        url: base_url + "/web/getoptdepotinventory.php",
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

            $(".btn_add_awal").on("click", function(e) {
                if ($("#id_terminal").val() == "" || $("#id_produk").val() == "" || $("#id_jenis").val() == "") {
                    swal.fire({
                        icon: "warning",
                        width: '350px',
                        allowOutsideClick: false,
                        html: '<p style="font-size:14px; font-family:arial;">Pilih [Jenis], [Produk], dan [Terminal / Depot] terlebih dahulu</p>'
                    });
                } else {
                    $("body").addClass("loading");
                    $.post(base_url + "/web/vendor-inven-terminal-new-list-receive.php", {
                        id_jenis: $("#id_jenis").val(),
                        id_terminal: $("#id_terminal").val(),
                        id_produk: $("#id_produk").val(),
                    }, function(data) {
                        $("#list_po_receive_modal").find(".modal-body").html(data);
                        $("#list_po_receive_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                    });
                }
            });
            $("#list_po_receive_modal").on('show.bs.modal', function(e) {
                $("body").addClass("loading");
            }).on('shown.bs.modal', function(e) {
                $("body").removeClass("loading");
            }).on('click', ".btn-pilih", function() {
                let index = $(this).data('detail');
                let param = index.toString().split('|-|');
                insertToRole01(index);
                $("#list_po_receive_modal").modal("hide");
            }).on('click', '#idBatallist_po_receive_modal_modal', function() {
                $("#list_po_receive_modal").modal("hide");
            });

            function insertToRole01(index) {
                var tabel = $(".tbl_add_vendor");
                var rwTbl = tabel.find('tbody > tr:last');
                var rwNom = parseInt(rwTbl.find("span.notabelawalinvenvendor").data('rowCount'));
                var newId = (isNaN(rwNom)) ? 1 : parseInt(rwNom + 1);
                var param = index.toString().split('|-|');

                var cekAda = false;
                tabel.find('tbody > tr').each(function(i, v) {
                    if ($(v).data("id") === 'undefined') {
                        cekAda = cekAda || false;
                    } else if ($(v).data("id") == decodeURIComponent(param[0]) + '|-|' + decodeURIComponent(param[1])) {
                        cekAda = cekAda || true;
                    }
                });

                if (!cekAda) {
                    var isiHtml =
                        '<tr data-id="' + decodeURIComponent(param[0]) + '|-|' + decodeURIComponent(param[1]) + '">' +
                        '<td class="text-center">' +
                        '<a class="btn btn-sm btn-danger btn-action hRow" style="padding:3px 7px;"><i class="fa fa-trash"></i></a>' +
                        '</td>' +
                        '<td class="text-center"><span class="notabelawalinvenvendor" data-row-count="' + newId + '"></span></td>' +
                        '<td class="text-left">' +
                        '<p style="margin-bottom:3px"><b>' + decodeURIComponent(param[2]) + '</b></p>' +
                        '<p style="margin-bottom:0px">Tanggal : ' + decodeURIComponent(param[3]) + '</p>' +
                        '</td>' +
                        '<td class="text-left">' + decodeURIComponent(param[4]) + '</td>' +
                        '<td class="text-center">' + decodeURIComponent(param[5]) + '</td>' +
                        '<td class="text-left">' +
                        '<input type="text" id="awal_inven_nilai' + newId + '" name="awal_inven_nilai[]" class="form-control input-sm text-right awal_inven_nilai" value="' + decodeURIComponent(param[6]) + '" readonly />' +
                        '<input type="hidden" id="id_po_supplier' + newId + '" name="id_po_supplier[]" value="' + decodeURIComponent(param[0]) + '" />' +
                        '<input type="hidden" id="id_po_receive' + newId + '" name="id_po_receive[]" value="' + decodeURIComponent(param[1]) + '" />' +
                        '<input type="hidden" id="id_invennya' + newId + '" name="id_invennya[]" value="' + decodeURIComponent(param[7]) + '" />' +
                        '</td>' +
                        '</tr>';

                    if (isNaN(rwNom)) {
                        rwTbl.remove();
                        rwTbl = tabel.find('tbody');
                        rwTbl.append(isiHtml);
                    } else {
                        rwTbl.after(isiHtml);
                    }
                    $("#awal_inven_nilai" + newId).number(true, 0, ".", ",");
                    tabel.find("span.notabelawalinvenvendor").each(function(i, v) {
                        $(v).text(i + 1);
                    });
                    $(".tbl_add_vendor .awal_inven_nilai").trigger("keyup");
                }
            }

            $(".tbl_add_vendor").on("keyup blur", ".awal_inven_nilai", function() {
                let total = 0;
                $("input[name='awal_inven_nilai[]']").each(function(i, v) {
                    total = total + ($(v).val() * 1);
                });
                $("#awal_inven_total").val(total);
            }).on("click", "a.hRow", function() {
                var tabel = $(".tbl_add_vendor");
                var jTbl = tabel.find('tbody > tr').length;
                if (jTbl > 1) {
                    var cRow = $(this).closest('tr');
                    cRow.remove();
                    tabel.find("span.notabelawalinvenvendor").each(function(i, v) {
                        $(v).text(i + 1);
                    });
                    $(".tbl_add_vendor .awal_inven_nilai").trigger("keyup");
                } else {
                    $(".tbl_add_vendor > tbody").html(htmlOptVendor);
                    $("#awal_inven_total").val("");
                }
            });

            $(".picker_po").on("click", function(e) {
                if ($("#id_terminal").val() == "" || $("#id_produk").val() == "" || $("#id_jenis").val() == "") {
                    swal.fire({
                        icon: "warning",
                        width: '350px',
                        allowOutsideClick: false,
                        html: '<p style="font-size:14px; font-family:arial;">Pilih [Jenis], [Produk], dan [Terminal / Depot] terlebih dahulu</p>'
                    });
                } else {
                    $("body").addClass("loading");
                    $.post(base_url + "/web/vendor-inven-terminal-new-list-receive.php", {
                        id_jenis: $("#id_jenis").val(),
                        id_terminal: $("#id_terminal").val(),
                        id_produk: $("#id_produk").val(),
                    }, function(data) {
                        $("#list_po_receive_sales_modal").find(".modal-body").html(data);
                        $("#list_po_receive_sales_modal").modal({
                            keyboard: false,
                            backdrop: 'static'
                        });
                    });
                }
            });
            $("#list_po_receive_sales_modal").on('show.bs.modal', function(e) {
                $("body").addClass("loading");
            }).on('shown.bs.modal', function(e) {
                $("body").removeClass("loading");
            }).on('click', ".btn-pilih", function() {
                let index = $(this).data('detail');
                insertToRole02(index);
                $("#list_po_receive_sales_modal").modal("hide");
            }).on('click', '#idBatallist_po_receive_modal_modal', function() {
                $("#list_po_receive_sales_modal").modal("hide");
            });

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
                $("input[name='tank_satu_vendor_nilai[]']").each(function(i, v) {
                    total = total + ($(v).val() * 1);
                });
                $("#tank_satu_total").val(total);
            }).on("click", "a.hRow", function() {
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

            /*$(".tbl_add_vendor").on("click", "a.addRow", function(){
            	var tabel 	= $(".tbl_add_vendor");
            	var rwTbl	= tabel.find('tbody > tr:last');
            	var rwNom	= parseInt(rwTbl.find("span.notabelawalinvenvendor").data('rowCount'));
            	var newId 	= (isNaN(rwNom))?1:parseInt(rwNom + 1);

            	var isiHtml =
            	'<tr data-id="'+newId+'">'+
            		'<td class="text-center"><span class="notabelawalinvenvendor" data-row-count="'+newId+'"></span></td>'+
            		'<td class="text-left"><select id="awal_inven_vendor_id'+newId+'" name="awal_inven_vendor_id[]" class="form-control"><option></option></select></td>'+
            		'<td class="text-left"><input type="text" id="awal_inven_vendor_nilai'+newId+'" name="awal_inven_vendor_nilai[]" class="form-control input-sm text-right awal_inven_vendor_nilai" /></td>'+
            		'<td class="text-center">'+
            			'<a class="btn btn-action btn-primary addRow jarak-kanan"><i class="fa fa-plus"></i></a>'+
            			'<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'+
            		'</td>'+
            	'</tr>';
            	if(isNaN(rwNom)){
            		rwTbl.remove();
            		rwTbl = tabel.find('tbody');
            		rwTbl.append(isiHtml);
            	} else{
            		rwTbl.after(isiHtml);
            	}
            	$("#awal_inven_vendor_id"+newId).select2({placeholder:"Pilih salah satu", allowClear:true});
            	$("#awal_inven_vendor_id"+newId).html('<option></option>'+$("#optVendor").html());
            	$("#awal_inven_vendor_nilai"+newId).number(true, 0, ".", ",");
            	tabel.find("span.notabelawalinvenvendor").each(function(i,v){$(v).text(i+1);});
            }).on("click", "a.hRow", function(){
            	var tabel 	= $(".tbl_add_vendor");
            	var jTbl	= tabel.find('tbody > tr').length;
            	if(jTbl > 1){
            		var cRow = $(this).closest('tr');
            		cRow.remove();
            		tabel.find("span.notabelawalinvenvendor").each(function(i,v){$(v).text(i+1);});
            	}
            	$(".tbl_add_vendor .awal_inven_vendor_nilai").trigger("keyup");
            });

            $(".tbl_trans_tanki_satu").on("click", "a.addRow", function(){
            	var tabel 	= $(".tbl_trans_tanki_satu");
            	var rwTbl	= tabel.find('tbody > tr:last');
            	var rwNom	= parseInt(rwTbl.find("span.notabeltanksatuvendor").data('rowCount'));
            	var newId 	= (isNaN(rwNom))?1:parseInt(rwNom + 1);

            	var isiHtml =
            	'<tr data-id="'+newId+'">'+
            		'<td class="text-center"><span class="notabeltanksatuvendor" data-row-count="'+newId+'"></span></td>'+
            		'<td class="text-left"><select id="tank_satu_vendor_id'+newId+'" name="tank_satu_vendor_id[]" class="form-control"><option></option></select></td>'+
            		'<td class="text-left"><input type="text" id="tank_satu_vendor_nilai'+newId+'" name="tank_satu_vendor_nilai[]" class="form-control input-sm text-right tank_satu_vendor_nilai" /></td>'+
            		'<td class="text-center">'+
            			'<a class="btn btn-action btn-primary addRow jarak-kanan"><i class="fa fa-plus"></i></a>'+
            			'<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>'+
            		'</td>'+
            	'</tr>';
            	if(isNaN(rwNom)){
            		rwTbl.remove();
            		rwTbl = tabel.find('tbody');
            		rwTbl.append(isiHtml);
            	} else{
            		rwTbl.after(isiHtml);
            	}
            	$("#tank_satu_vendor_id"+newId).select2({placeholder:"Pilih salah satu", allowClear:true});
            	$("#tank_satu_vendor_id"+newId).html('<option></option>'+$("#optVendor").html());
            	$("#tank_satu_vendor_nilai"+newId).number(true, 0, ".", ",");
            	tabel.find("span.notabeltanksatuvendor").each(function(i,v){$(v).text(i+1);});
            }).on("click", "a.hRow", function(){
            	var tabel 	= $(".tbl_trans_tanki_satu");
            	var jTbl	= tabel.find('tbody > tr').length;
            	if(jTbl > 1){
            		var cRow = $(this).closest('tr');
            		cRow.remove();
            		tabel.find("span.notabeltanksatuvendor").each(function(i,v){$(v).text(i+1);});
            	}
            	$(".tbl_trans_tanki_satu .tank_satu_vendor_nilai").trigger("keyup");
            });
            

            $(".tbl_trans_tanki_satu").on("keyup blur", ".tank_satu_vendor_nilai", function(){
            	let total = 0;
            	$("input[name='tank_satu_vendor_nilai[]']").each(function(i, v){
            		total = total + ($(v).val() * 1);
            	});
            	$("#tank_satu_total").val(total);
            });*/

        });
    </script>
</body>

</html>