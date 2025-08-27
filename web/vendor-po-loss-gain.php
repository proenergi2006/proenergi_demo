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

$section     = "Add Losses & Gain";
$idnya01     = ($enk["idr"] ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '');
$idnya02     = ($enk["idnya02"] ? htmlspecialchars($enk["idnya02"], ENT_QUOTES) : '');

if (!$idnya02) {
    $sql = "

            with tbl_realisasi as (
                select id_po_supplier, sum(volume_terima) as vol_terima, volume_bol  
                from new_pro_inventory_vendor_po_receive 
                group by id_po_supplier
            )
			select a.*, b.jenis_produk, b.merk_dagang, d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal, a1.id_po_supplier, a1.vol_terima, a1.volume_bol  
			from new_pro_inventory_vendor_po a 
			join pro_master_produk b on a.id_produk = b.id_master 
			join pro_master_vendor d on a.id_vendor = d.id_master 
			join pro_master_terminal e on a.id_terminal = e.id_master 
            left join tbl_realisasi a1 on a.id_master = a1.id_po_supplier  
			where a.id_master = '" . $idnya01 . "'
		";
    $rsm     = $con->getRecord($sql);
    $dt1     = date("d/m/Y", strtotime($rsm['tanggal_inven']));
    $dt8     = ($rsm['harga_tebus']) ? $rsm['harga_tebus'] : '';
    $dt10     = ($rsm['volume_po']) ? $rsm['volume_po'] : '';
    $dt9   = ($rsm['subtotal']) ? $rsm['subtotal'] : '';
    $dt11    = ($rsm['ppn_11']) ? $rsm['ppn_11'] : '';
    $dt12    = ($rsm['pph_22']) ? $rsm['pph_22'] : '';
    $dt13    = ($rsm['pbbkb']) ? $rsm['pbbkb'] : '';
    $dt14    = ($rsm['total_order']) ? $rsm['total_order'] : '';
    $dt15   = ($rsm['vol_terima']) ? $rsm['vol_terima'] : '';
    $dt19   = ($rsm['volume_bol']) ? $rsm['volume_bol'] : '';
    $ket   = ($rsm['keterangan']) ? $rsm['keterangan'] : '';


    $dt16 = $dt15 - $dt10;
    $harga_ri = $dt19 * $dt8 / $dt15;

    $dt21 = $dt10 - $dt19;
    $totalgainloss = $dt16 + $dt21;





    $dt18 = $dt15 * $harga_ri;


    $action         = "update";
    $tgl_terima     = "";
    $harga_tebus     = $rsm['harga_tebus'];

    $volume_terima     = "";
    $nama_pic         = "";
} else {
    $sql = "
			select a.nomor_po, a.tanggal_inven, a.harga_tebus, a.volume_po, a.id_terminal, b.jenis_produk, b.merk_dagang, 
			d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal, 
			a1.tgl_terima, a1.harga_tebus as harga_tebus_receive, a1.volume_terima, a1.nama_pic, 
			a1.file_upload, a1.file_upload_ori 
			from new_pro_inventory_vendor_po a 
			join new_pro_inventory_vendor_po_receive a1 on a.id_master = a1.id_po_supplier 
			join pro_master_produk b on a.id_produk = b.id_master 
			join pro_master_vendor d on a.id_vendor = d.id_master 
			join pro_master_terminal e on a.id_terminal = e.id_master 
			where a1.id_po_supplier = '" . $idnya01 . "' and a1.id_po_receive = '" . $idnya02 . "'
		";
    $rsm     = $con->getRecord($sql);
    $dt1     = date("d/m/Y", strtotime($rsm['tanggal_inven']));
    $dt8     = ($rsm['harga_tebus']) ? $rsm['harga_tebus'] : '';
    $dt9   = ($rsm['subtotal']) ? $rsm['subtotal'] : '';
    $dt11    = ($rsm['ppn_11']) ? $rsm['ppn_11'] : '';
    $dt12    = ($rsm['pph_22']) ? $rsm['pph_22'] : '';
    $dt13    = ($rsm['pbbkb']) ? $rsm['pbbkb'] : '';
    $dt14    = ($rsm['total_order']) ? $rsm['total_order'] : '';
    $dt10     =  ($rsm['volume_po']) ? $rsm['volume_po'] : '';

    $action         = "update";
    $tgl_terima     = date("d/m/Y", strtotime($rsm['tgl_terima']));
    $harga_tebus     = ($rsm['harga_tebus_receive'] ? $rsm['harga_tebus_receive'] : '');
    $volume_terima     = ($rsm['volume_terima'] ? $rsm['volume_terima'] : '');
    $nama_pic         = $rsm['nama_pic'];

    $dataIcons     = "";
    $pathnya     = $public_base_directory . '/files/uploaded_user/lampiran';
    if ($rsm['file_upload_ori'] && file_exists($pathnya . '/' . $rsm['file_upload'])) {
        $labelFile     = 'Ubah File';
        $linkPt     = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=108900&ktg=" . $rsm['file_upload'] . "&file=" . $rsm['file_upload_ori']);
        $dataIcons     = '
			<div style="margin:6px 0px 10px;">
				<a href="' . $linkPt . '" target="_blank" title="download file">
				<i class="fas fa-paperclip jarak-kanan"></i> Download File</a>
			</div>';
    }
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
                <form action="<?php echo ACTION_CLIENT . '/vendor-po-new-loss-gain.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>PO Supplier</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <em>
                                            <p style="color: red;">* Note : Merubah Volume PO & Harga Dasar Akan Mengulangi Proses PO Supplier </p>
                                        </em>
                                        <p></p>
                                        <p></p>
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
                                        <label class="control-label col-md-3">Tanggal PO *</label>
                                        <div class="col-md-4">
                                            <?php if (!$dt1) { ?>
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="font-size:12px;"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" name="dt1" id="dt1" class="form-control datepicker" required data-rule-dateNL="1" value="<?php echo $dt1; ?>" readonly />
                                                </div>
                                            <?php } else { ?>
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="font-size:12px;"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" name="dt1" id="dt1" class="form-control" required data-rule-dateNL="1" value="<?php echo $dt1; ?>" readonly />
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Produk *</label>
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
                                        <label class="control-label col-md-3">Terminal *</label>
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
                                        <label class="control-label col-md-3">Vendor *</label>
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
                                        <label class="control-label col-md-3">Kode Tax *</label>
                                        <div class="col-md-4">
                                            <select name="kd_tax" id="kd_tax" class="form-control select2" style="width:100%;" required>
                                                <option></option>
                                                <option value="E" <?php echo ($rsm['kd_tax'] == 'E' ? 'selected' : ''); ?>>E</option>
                                                <option value="EC" <?php echo ($rsm['kd_tax'] == 'EC' ? 'selected' : ''); ?>>EC</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Volume PO *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <input type="text" id="dt10" name="dt10" class="form-control hitung" value="<?php echo $dt10; ?>" required readonly />
                                                <span class="input-group-addon" style="font-size:12px;">Liter</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Volume BL *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <input type="text" id="dt19" name="dt19" class="form-control hitung" value="<?php echo $dt19; ?>" required readonly />
                                                <span class="input-group-addon" style="font-size:12px;">Liter</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Volume RI *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <input type="text" id="dt15" name="dt15" class="form-control hitung" value="<?php echo $dt15; ?>" required readonly />

                                                <span class="input-group-addon" style="font-size:12px;">Liter</span>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>





                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Harga Dasar *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt8" name="dt8" class="form-control hitung" required value="<?php echo $dt8; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Harga Dasar RI*</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="harga_ri" name="dt16" class="form-control hitung" required value="<?php echo $harga_ri; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Sub Total *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt9" name="dt9" class="form-control hitung" required value="<?php echo  $formatted_dt18; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">PPN 11% *</label>
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
                                        <label class="control-label col-md-3">PPH 22 *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt12" name="dt12" class="form-control hitung" required value="" readonly />

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">PBBKB PO *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt22" name="dt22" class="form-control hitung" value="<?php echo isset($dt13) ? $dt13 : '0'; ?>" readonly />

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">PBBKB *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt13" name="dt13" class="form-control hitung" value="<?php echo isset($dt22) ? $dt22 : '0'; ?>" />

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Total Order *</label>
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
                                        <label class="control-label col-md-3">Ket *</label>
                                        <div class="col-md-8">
                                            <textarea id="ket" name="ket" class="form-control" required><?php echo $ket; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>




                    <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />



                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Add Gain / Loss</h3>
                        </div>
                        <div class="box-body">


                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Volume PO*</label>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="text" id="volume_po_loss_gain" name="volume_po_loss_gain" class="form-control hitung" value="<?php echo $dt10; ?>" required readonly />
                                                <span class="input-group-addon">Liter</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Volume BL*</label>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="text" id="volume_bl_loss_gain" name="volume_bl_loss_gain" class="form-control hitung" value="<?php echo $dt19; ?>" required readonly />
                                                <span class="input-group-addon">Liter</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>




                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Volume Terima*</label>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="text" id="volume_terima_loss_gain" name="volume_terima_loss_gain" class="form-control hitung" value="<?php echo $dt15; ?>" required readonly />
                                                <span class="input-group-addon">Liter</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Jenis *</label>
                                        <div class="col-md-5">
                                            <select id="jenis" name="jenis" class="form-control select2">
                                                <option value="1">Bertambah / Gain (+)</option>
                                                <option value="2">Berkurang / Loss (-)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Volume Gain / Loss *</label>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="text" id="volume_loss_gain" name="volume_loss_gain" class="form-control hitung" value="<?php echo  $totalgainloss; ?>" required readonly />
                                                <span class="input-group-addon">Liter</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">File Upload *</label>
                                        <div class="col-md-8">
                                            <?php
                                            echo '
								' . $dataIcons . '
								<div class="rowuploadnya">
									<div class="simple-fileupload">
										<input type="file" name="file_template" id="file_template" class="form-inputfile" />
										<label for="file_template" class="label-inputfile">
											<div class="input-group input-group-sm">
												<div class="input-group-addon btn-primary"><i class="fa fa-upload"></i></div>
												<input type="text" class="form-control" placeholder="' . $labelFile . '" readonly />
											</div>
										</label>
									</div>
								</div>';
                                            ?>
                                            <p style="font-size:12px;" class="help-block">* Max size 5Mb | .pdf</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Ket *</label>
                                        <div class="col-md-8">
                                            <textarea id="ket_loss_gain" name="ket_loss_gain" class="form-control" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                    <div style="margin-bottom:15px;">
                        <input type="hidden" name="act" value="<?php echo $action; ?>" />
                        <input type="hidden" name="idnya01" value="<?php echo $idnya01; ?>" />
                        <input type="hidden" name="idnya02" value="<?php echo $idnya02; ?>" />
                        <input type="hidden" name="id_terminal_po" value="<?php echo $rsm['id_terminal']; ?>" />
                        <input type="hidden" name="harga_tebus_po" value="<?php echo $rsm['harga_tebus']; ?>" />
                        <?php if (!$rsm['is_selesai']) { ?>
                            <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                                <i class="fa fa-save jarak-kanan"></i> Simpan
                            </button>
                        <?php } ?>
                        <a href="<?php echo BASE_URL_CLIENT . "/vendor-po-new-terima.php?" . paramEncrypt('idr=' . $idnya01); ?>" class="btn btn-default" style="min-width:90px;">
                            <i class="fa fa-reply jarak-kanan"></i> Kembali
                        </a>
                    </div>

                    <p><small>* Wajib Diisi</small></p>
                </form>

                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>

    <script>
        $(document).ready(function() {

            // Format angka dengan plugin number

            $(".hitung1").number(true, 0, ".", ",");

            $(".hitung").number(true, 2, ".", ",");

            hitungTotal();

            function hitungTotal() {
                // Ambil nilai dari input yang dibutuhkan
                var kodeTax = $('#kd_tax').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var volumeBL = parseFloat($('#dt19').val()) || 0;
                var volumeRI = parseFloat($('#dt15').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var hargaDasarRI = parseFloat($('#harga_ri').val()) || 0;
                var pbbkb = parseFloat($('#dt13').val()) || 0;

                // Hitung sub total



                var subTotal = volumeRI * hargaDasarRI;



                // Hitung PPN 11%
                var ppn11 = ((11 * subTotal) / 100);

                // Hitung PPH 22
                var pph = 0;
                if (kodeTax == 'EC') {
                    var pph = ((subTotal * 0.3) / 100);
                }
                // Hitung total order
                var totalOrder = subTotal + ppn11 + pph + pbbkb;

                // Tampilkan hasil perhitungan pada input yang sesuai
                $('#dt9').val(subTotal.toFixed(4));
                $('#dt11').val(ppn11.toFixed(4));
                $('#dt12').val(pph.toFixed(4));
                $('#dt14').val(totalOrder.toFixed(4));
            }

            // Membuat event listener untuk input yang dihitung
            $('.hitung').on('input', function() {
                // Panggil fungsi hitung saat nilai input berubah
                hitungTotal();
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
            //     if (nilai == 'EC') {
            //         $("#dt12").removeAttr("readonly");
            //         $("#dt12").val("0.3");
            //     } else {
            //         $("#dt12").attr("readonly", "readonly");
            //         $("#dt12").val("");
            //     }
            // });

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

        });
    </script>


</body>

</html>