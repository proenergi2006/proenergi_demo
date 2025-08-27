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
    $action     = "cancel";
    $section     = "Cancel PO Crushed Stone";
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
    $ket     =  ($rsm['keterangan_cancel']) ? $rsm['keterangan_cancel'] : '';
    $dt10    = ($rsm['volume_po']) ? $rsm['volume_po'] : '';
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

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Keterangan Cancel *</label>
                                        <div class="col-md-8">
                                            <textarea id="cancel" name="cancel" class="form-control" required><?php echo $ket; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

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
            var formValidasiCfg = {
                submitHandler: function(form) {
                    Swal.fire({
                        title: "Apakah Anda yakin?",
                        text: "Anda akan menyimpan data ini.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya, simpan!",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $("body").addClass("loading");
                            $.ajax({
                                type: 'POST',
                                url: base_url + "/web/action/vendor-po-new-crushed-stone.php",
                                data: {
                                    act: 'cek',
                                    q1: $("input[name='idr']").val(),
                                },
                                cache: false,
                                dataType: 'json',
                                success: function(data) {
                                    $("body").removeClass("loading");
                                    if (!data.hasil) {
                                        Swal.fire({
                                            icon: "warning",
                                            width: '350px',
                                            allowOutsideClick: false,
                                            html: '<p style="font-size:14px; font-family:arial;">' + data.pesan + '</p>'
                                        });
                                    } else {
                                        form.submit(); // Kirim form setelah cek sukses
                                    }
                                }
                            });
                        }
                    });
                }
            };

            $("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));
        });
    </script>
</body>

</html>