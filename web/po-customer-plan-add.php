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
$action = "add";
$section = "Tambah Data";
$idr     = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
$idk     = htmlspecialchars($enk["idk"], ENT_QUOTES);
$cek = "select a.id_poc, lpad(a.id_poc,4,'0') as kode_po, a.nomor_poc, a.tanggal_poc, a.volume_poc, b.nama_customer, c.id_cabang, d.vol_plan, d.realisasi, a.created_time,  e.masa_awal, e.masa_akhir, e.nomor_surat
			from pro_po_customer a join pro_customer b on a.id_customer = b.id_customer join pro_penawaran c on a.id_penawaran = c.id_penawaran 
			left join (
				select id_poc, sum(if(realisasi_kirim = 0,volume_kirim, realisasi_kirim)) as vol_plan, sum(realisasi_kirim) as realisasi 
				from pro_po_customer_plan where id_poc = '" . $idk . "' and status_plan not in (2,3) group by id_poc
			) d on a.id_poc = d.id_poc 
            join pro_penawaran e on a.id_penawaran =  e.id_penawaran
			where a.poc_approved = 1 and a.id_customer = '" . $idr . "' and a.id_poc = '" . $idk . "'";
$row = $con->getRecord($cek);
$asf = $row['volume_poc'] - $row['vol_plan'];
$masa_akhir = $row['masa_akhir'];

$sqlGetWil = "SELECT * FROM pro_master_cabang WHERE id_master='" . $row['id_cabang'] . "'";
$rowWil = $con->getRecord($sqlGetWil);


$year                 = date("y");
$month                 = date("m");
$arrRomawi             = array("1" => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
$monthnow_romawi     = $arrRomawi[intval($month)];
$query_no_so = "SELECT * FROM pro_po_customer_plan WHERE no_so LIKE '%" . "/" . $rowWil['inisial_cabang'] . "/" . $year . "/" . $monthnow_romawi . "/" . "%' ORDER BY no_so DESC ";
$row2 = $con->getRecord($query_no_so);

if ($row2) {
    $no_so = $row2['no_so'];
    $explode = explode("/", $no_so);
    $year_so = $explode[3];
    $month_so = $explode[4];

    $urut_so = $explode[5] + 1;
    $no_so = sprintf("%03s", $urut_so);
    $noms_so = 'SO/' . 'PE/' . $rowWil['inisial_cabang'] . '/' . $year_so . '/' . $arrRomawi[intval($month)] . '/' . $no_so;
} else {
    $urut_so    = 1;
    $no_so    = sprintf("%03s", $urut_so);
    $noms_so    = 'SO/' . 'PE/' . $rowWil['inisial_cabang'] . '/' . $year . '/' . $arrRomawi[intval($month)] . '/' . $no_so;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1><?php echo $section . " PO Plan"; ?></h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th colspan="3"><?php echo "Kode Dokumen PO-" . $row['kode_po']; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td width="160">Kode Penawaran</td>
                                                        <td><?php echo $row['nomor_surat']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="160">Periode Penawaran</td>
                                                        <td><?php echo date("d/m/Y", strtotime($row['masa_awal'])); ?> - <?php echo date("d/m/Y", strtotime($row['masa_akhir'])); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="160">Nama Customer</td>
                                                        <td><?php echo $row['nama_customer']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Nomor PO</td>
                                                        <td><?php echo $row['nomor_poc']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal PO</td>
                                                        <td><?php echo tgl_indo($row['tanggal_poc']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Order</td>
                                                        <td><?php echo number_format($row['volume_poc']) . " Liter"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal Kirim</td>
                                                        <td><?php echo tgl_indo($row['created_time']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Kirim</td>
                                                        <td><?php echo number_format($row['realisasi']) . " Liter"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sisa Aktual</td>
                                                        <td><?php echo number_format(($row['volume_poc'] - $row['realisasi'])) . " Liter"; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sisa Buku</td>
                                                        <td><?php echo number_format(($row['volume_poc'] - $row['vol_plan'])) . " Liter"; ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <form action="<?php echo ACTION_CLIENT . '/po-customer-plan.php'; ?>" id="gform" name="gform" method="post" class="form-validasi" role="form">
                                    <div class="form-group row" hidden>
                                        <div class="col-sm-6">
                                            <label>Periode Penawaran *</label>
                                            <input type="date" id="periode_penawaran" name="periode_penawaran" class="form-control " value="<?php echo $masa_akhir; ?>" autocomplete="off" />
                                        </div>

                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Alamat Kirim *</label>
                                            <select id="alamat_kirim" name="alamat_kirim" class="form-control validate[required]">
                                                <option></option>
                                                <?php $con->fill_select("a.id_lcr", "concat(a.alamat_survey, '#', c.nama_kab, '#', b.nama_prov, '#|#', a.latitude_lokasi, ', ',  a.longitude_lokasi)", "pro_customer_lcr a join pro_master_provinsi b on a.prov_survey = b.id_prov join pro_master_kabupaten c on a.kab_survey = c.id_kab ", $rsm['id_marketing'], "where a.flag_approval = 1 and a.id_customer = '" . $idr . "'", "a.id_lcr", false); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label>Tanggal Kirim *</label>
                                            <input type="text" id="tanggal_kirim" name="tanggal_kirim" class="form-control datepicker validate[required,custom[date]]" autocomplete="off" />
                                        </div>
                                        <div class="col-sm-3 col-sm-top">
                                            <label>Volume *</label>
                                            <div class="input-group">
                                                <input type="text" id="vol_kir" name="vol_kir" class="form-control hitung validate[required,funcCall[maxnya[<?php echo $asf; ?>]]]" autocomplete="off" />
                                                <span class="input-group-addon">Liter</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="control-label col-md-2">No Sales Order</label>
                                        <div class="col-md-4">
                                            <input type="text" id="no_so" name="no_so" class="form-control" value="<?php echo (isset($row['no_so'])) ?  $row['no_so'] : $noms_so; ?>" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Catatan</label>
                                            <input type="text" id="catatan" name="catatan" class="form-control" autocomplete="off" />
                                        </div>
                                    </div>
                                    <?php /*<div class="form-group row">
                                    <div class="col-sm-6">
                                        <div class="checkbox">
                                            <label class="rtl"><input type="checkbox" name="is_urgent" id="is_urgent" value="1" class="form-control" /></label>
                                            <b>Penting</b> <i>(Cheklist kotak ini bila tanggal kirim sama dengan tanggal hari ini)</i>
                                        </div>
                                    </div>
                                </div>*/ ?>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="pad bg-gray">
                                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                                <input type="hidden" name="idk" value="<?php echo $idk; ?>" />
                                                <input type="hidden" name="idc" value="<?php echo $row['id_cabang']; ?>" />
                                                <a href="<?php echo BASE_REFERER; ?>" class="btn btn-default jarak-kanan"><i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                                <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
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

                <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-blue">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Loading Data ...</h4>
                            </div>
                            <div class="modal-body text-center modal-loading"></div>
                        </div>
                    </div>
                </div>
                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>

    <style type="text/css">
        h3.form-title {
            font-size: 18px;
            margin: 0 0 10px;
            font-weight: 700;
        }

        #table-detail {
            margin-bottom: 15px;
        }

        #table-detail td {
            padding-bottom: 3px;
            font-size: 12px;
        }
    </style>
    <script>
        $(document).ready(function() {


            $("#btnSbmt").click(function(e) {
                e.preventDefault(); // Mencegah form langsung submit

                // Ambil nilai dari input periode penawaran dan tanggal kirim
                var periodePenawaran = document.getElementById('periode_penawaran').value;
                var tanggalKirim = document.getElementById('tanggal_kirim').value;

                // Ubah format tanggal kirim dari DD/MM/YYYY ke YYYY-MM-DD
                var parts = tanggalKirim.split('/');
                var tanggalKirimFormatted = parts[2] + '-' + parts[1] + '-' + parts[0]; // Format ke YYYY-MM-DD

                // Ubah menjadi objek Date
                var masaAkhir = new Date(periodePenawaran); // dari input format YYYY-MM-DD
                var tanggalKirimDate = new Date(tanggalKirimFormatted); // dari input format DD/MM/YYYY

                // Validasi apakah tanggal kirim lebih besar dari masa akhir (periode penawaran)
                if (tanggalKirimDate > masaAkhir) {
                    // Jika tanggal kirim melebihi masa akhir, tampilkan SweetAlert dan cegah submit
                    event.preventDefault(); // Mencegah form submit
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak bisa disimpan',
                        text: 'Tanggal kirim melebihi periode penawaran!',
                    });
                } else {
                    Swal.fire({
                        title: "Anda yakin?",
                        showCancelButton: true,
                        confirmButtonText: "Ya",
                        cancelButtonText: "Tidak",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (tanggalKirim != "" && alamat_kirim != "" && vol_kir != "") {
                                $("#loading_modal").modal({
                                    backdrop: 'static'
                                });
                                // Validasi apakah tanggal kirim lebih besar dari masa akhir (periode penawaran)
                                if (tanggalKirimDate > masaAkhir) {
                                    // Jika tanggal kirim melebihi masa akhir, tampilkan SweetAlert dan cegah submit
                                    event.preventDefault(); // Mencegah form submit
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Tidak bisa disimpan',
                                        text: 'Tanggal kirim melebihi periode penawaran!',
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'Form berhasil disimpan.',
                                    }).then(function() {
                                        // Submit form setelah user klik OK di SweetAlert
                                        $("#gform").submit();
                                    });
                                }
                            } else {
                                $("#gform").submit();
                            }
                        }
                    });
                }
            });

            $(".hitung").number(true, 0, ".", ",");
            $("select#alamat_kirim").select2({
                placeholder: "Pilih salah satu",
                allowClear: true,
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(repo) {
                    var result = repo.text.split("#|#");
                    var almtmp = result[0].split("#");
                    var alamat = almtmp[0] + (almtmp[1] ? ' ' + ucwords(almtmp[1].replace(/(KOTA|KABUPATEN)/g, "").toLowerCase()) : '') + (almtmp[2] ? ' ' + almtmp[2] : '');
                    var display = '<div><p style="margin-bottom:0px;">' + alamat + '</p><p style="margin-bottom:0px;">' + result[1] + '</p></div>';
                    return display;
                },
                templateSelection: function(repo) {
                    var result = repo.text.split("#|#");
                    var almtmp = result[0].split("#");
                    var alamat = almtmp[0] + (almtmp[1] ? ' ' + ucwords(almtmp[1].replace(/(KOTA|KABUPATEN)/g, "").toLowerCase()) : '') + (almtmp[2] ? ' ' + almtmp[2] : '');
                    return alamat;
                },
            });
        });
    </script>
</body>

</html>