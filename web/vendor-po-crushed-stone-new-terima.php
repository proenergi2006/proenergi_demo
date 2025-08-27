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
    $section     = "PO Suplier Crushed Stone Receive";
    $idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
    $sql = "
			select a.*, b.jenis_produk, b.merk_dagang, d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal 
			from new_pro_inventory_vendor_po_crushed_stone a 
			join pro_master_produk b on a.id_produk = b.id_master 
			join pro_master_vendor d on a.id_vendor = d.id_master 
			join pro_master_terminal e on a.id_terminal = e.id_master 
			where a.id_master = '" . $idr . "'
		";

    $rsm     = $con->getRecord($sql);
    $dt1     = date("d/m/Y", strtotime($rsm['tanggal_inven']));
    $dt8     = ($rsm['harga_tebus'] ? 'Rp. ' . number_format($rsm['harga_tebus'], 0, ',', '.') : '');
    $dt10     = ($rsm['volume_po'] ? number_format($rsm['volume_po'], 0, ',', '.') . ' Liter' : '');
    $dt11 = ($rsm['volume_po'] ? str_replace(',', '', number_format($rsm['volume_po'], 0, ',', '')) : '');
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
                <table class="table no-border tablea-bordered" style="width:100%;">
                    <tr>
                        <td class="text-left" width="150">Nomor PO</td>
                        <td class="text-center" width="20">:</td>
                        <td class="text-left" width=""><?php echo $rsm['nomor_po']; ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">Tanggal PO</td>
                        <td class="text-center">:</td>
                        <td class="text-left"><?php echo $dt1; ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">Produk</td>
                        <td class="text-center">:</td>
                        <td class="text-left"><?php echo $rsm['jenis_produk'] . ' - ' . $rsm['merk_dagang']; ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">Deskripsi</td>
                        <td class="text-center">:</td>
                        <td class="text-left"><?php echo $rsm['description'] ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">Terminal / Depot</td>
                        <td class="text-center">:</td>
                        <td class="text-left">
                            <?php
                            $terminal1     = $rsm['nama_terminal'];
                            $terminal2     = ($rsm['tanki_terminal'] ? ' - ' . $rsm['tanki_terminal'] : '');
                            $terminal3     = ($rsm['lokasi_terminal'] ? ', ' . $rsm['lokasi_terminal'] : '');
                            echo $terminal1 . $terminal2 . $terminal3;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">Vendor</td>
                        <td class="text-center">:</td>
                        <td class="text-left"><?php echo $rsm['nama_vendor']; ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">Volume PO</td>
                        <td class="text-center">:</td>
                        <td class="text-left"><?php echo $dt10; ?></td>
                    </tr>
                    <tr>
                        <td class="text-left">Harga Dasar</td>
                        <td class="text-center">:</td>
                        <td class="text-left"><?php echo $dt8; ?></td>
                    </tr>
                </table>

                <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />


                <!-- <div style="margin:15px 0px;">
					<a href="<?php echo BASE_URL_CLIENT . '/vendor-po-new-terima-add.php?' . paramEncrypt('idr=' . $idr); ?>" class="btn btn-primary">
						<i class="fa fa-plus jarak-kanan"></i>Add Receive
					</a>
				</div> -->







                <div class="table-responsive">
                    <table class="table table-bordered table-hovera" id="table-grid2">
                        <thead>
                            <tr>
                                <th class="text-center" width="80" style="height:40px;">No</th>
                                <th class="text-center" width="120">Tanggal Terima</th>
                                <th class="text-center" width="">Nama PIC</th>
                                <th class="text-center" width="180">Harga Tebus</th>
                                <th class="text-center" width="180">Volume BL</th>

                                <th class="text-center" width="180">Volume Terima</th>
                                <th class="text-center" width="100">File</th>
                                <th class="text-center" width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sqlnya = "
							select a1.id_master, a.* 
							from new_pro_inventory_vendor_po_crushed_stone_receive a 
							left join new_pro_inventory_depot a1 on a.id_po_supplier = a1.id_po_supplier and a.id_po_receive = a1.id_po_receive and a1.id_jenis = 1   
							where a.id_po_supplier = '" . $idr . "'
						";
                            $resnya = $con->getResult($sqlnya);
                            if (count($resnya) > 0) {
                                $nom = 0;
                                $totalVolumeTerima = 0;
                                foreach ($resnya as $data1) {
                                    $nom++;

                                    if ($data1['id_master']) {
                                        $tombolUbah     = "";
                                        $tombolHapus     = "";
                                    } else {
                                        $linkEdit         = BASE_URL_CLIENT . '/vendor-po-crushed-stone-new-terima-add.php?' . paramEncrypt('idr=' . $data1['id_po_supplier'] . '&idnya02=' . $data1['id_po_receive']);
                                        $linkHapus         = paramEncrypt($data1['id_po_supplier'] . "|#|" . $data1['id_po_receive']);
                                        $tombolUbah     = '
									<a class="btn btn-sm btn-info jarak-kanan" title="Edit" href="' . $linkEdit . '" style="padding:3px 7px;">
									<i class="fa fa-pencil-alt"></i></a>';
                                        $tombolHapus     = '
									<a class="btn btn-sm btn-danger" title="Delete" data-param-idx="' . $linkHapus . '" data-action="deleteList" style="padding:3px 7px;">
									<i class="fa fa-trash"></i></a>';
                                        $tombolDiterima     = '
									<a class="btn btn-sm btn-success" title="Receive" data-param-idx="' . $linkDiterima . '"  style="padding:3px 7px;">
									<i class="fa fa-paper-plane"></i></a>';
                                    }

                                    $dataIcons     = "";
                                    $pathnya     = $public_base_directory . '/files/uploaded_user/lampiran';
                                    if ($data1['file_upload_ori'] && file_exists($pathnya . '/' . $data1['file_upload'])) {
                                        $labelFile     = 'Ubah File';
                                        $linkPt     = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=108900&ktg=" . $data1['file_upload'] . "&file=" . $data1['file_upload_ori']);
                                        $dataIcons     = '
									<a href="' . $linkPt . '" target="_blank" title="download file"> 
									<i class="far fa-file-alt jarak-kanan" style="font-size:14px;"></i> Download</a>';
                                    }
                                    $totalVolumeTerima += $data1['volume_terima'];
                                    echo '
								<tr data-id="' . $nom . '">
									<td class="text-center">' . $nom . '</td>
									<td class="text-center">' . date("d/m/Y", strtotime($data1['tgl_terima'])) . '</td>
									<td class="text-left">' . $data1['nama_pic'] . '</td>
									<td class="text-right">Rp. ' . number_format($data1['harga_tebus']) . '</td>
									<td class="text-right">' . number_format($data1['volume_bol']) . ' m³</td>

									<td class="text-right">' . number_format($data1['volume_terima']) . ' m³</td>
									<td class="text-center">' . $dataIcons . '</td>
									<td class="text-center">' . $tombolUbah . '  </td>
								</tr>';
                                }
                                echo '<tr>
								<td colspan="5" class="text-right"><strong>TOTAL :</strong></td>
								<td class="text-right"><strong>' . number_format($totalVolumeTerima) . ' m³</strong></td>
								<td colspan="2"></td>
							  </tr>';
                            } else {
                                echo '<tr><td colspan="7" style="height:35px;">Data Tidak Ditemukan</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hovera" id="table-grid2">
                        <thead>
                            <tr>
                                <th class="text-center" width="80" style="height:40px;">No</th>
                                <th class="text-center" width="120">Jenis</th>
                                <th class="text-center" width="100">Volume</th>
                                <th class="text-center" width="180">Keterangan</th>
                                <th class="text-center" width="100">Status</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sqlnya1 = "
							select *
							from new_pro_inventory_gain_loss_crushed_stone 
							
							where id_po_supplier = '" . $idr . "'
						";
                            $resnya = $con->getResult($sqlnya1);
                            if (count($resnya) > 0) {
                                $nom = 0;
                                $totalVolumeTerima = 0;
                                foreach ($resnya as $data1) {
                                    $nom++;

                                    if ($sesrol == '5') {
                                        $background = ($data1['ceo_result'] == 0) ? ' style="background-color:#f5f5f5"' : '';
                                    }

                                    if ($data1['jenis'] == 1)
                                        $jenis = 'Gain';
                                    else if ($data1['jenis'] == 2)
                                        $jenis = 'Loss';

                                    if ($data1['disposisi_gain_loss'] == 1)
                                        $status = 'Verifikasi CEO';
                                    elseif ($data1['disposisi_gain_loss'] == 3)
                                        $status = 'Dikembalikan<br><i>';
                                    elseif ($data1['disposisi_gain_loss'] == 2)
                                        $status = 'Terverifikasi<br><i>' . date("d/m/Y H:i:s", strtotime($data1['ceo_tanggal'])) . ' WIB</i>';

                                    else $status = '';

                                    $dataIcons     = "";
                                    $pathnya     = $public_base_directory . '/files/uploaded_user/lampiran';
                                    if ($data1['file_upload_ori'] && file_exists($pathnya . '/' . $data1['file_upload'])) {
                                        $labelFile     = 'Ubah File';
                                        $linkPt     = ACTION_CLIENT . "/download-file.php?" . paramEncrypt("tipe=108900&ktg=" . $data1['file_upload'] . "&file=" . $data1['file_upload_ori']);
                                        $dataIcons     = '
									<a href="' . $linkPt . '" target="_blank" title="download file"> 
									<i class="far fa-file-alt jarak-kanan" style="font-size:14px;"></i> Download</a>';
                                    }
                                    $totalVolumeTerima += $data1['volume_terima'];
                                    echo '
								<tr data-id="' . $nom . '">
									<td class="text-center">' . $nom . '</td>
									<td class="text-left">' . $jenis . '</td>
									<td class="text-right">' . number_format($data1['volume']) . ' m³</td>
									
									<td class="text-left">' . $data1['ket'] . '</td>
									<td class="text-center">' . $status . '</td>
								</tr>';
                                }
                                echo '<tr>
								
							  </tr>';
                            } else {
                                echo '<tr><td colspan="7" style="height:35px;">Data Tidak Ditemukan</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>


                <?php if ($totalVolumeTerima == $dt11) { ?>
                    <div style="margin: 15px 0px;">
                        <a href="<?php echo BASE_URL_CLIENT . '/vendor-po-new-crushed-stone.php'; ?>" class="btn btn-default" style="min-width:90px;">
                            <i class="fa fa-reply jarak-kanan"></i> Kembali
                        </a>
                    </div>
                <?php } elseif ($totalVolumeTerima >= $dt11) { ?>
                    <div style="margin: 15px 0px;">
                        <a href="<?php echo BASE_URL_CLIENT . '/vendor-po-crushed-stone-loss-gain.php?' . paramEncrypt('idr=' . $idr); ?>" class="btn btn-success">
                            <i class="fa fa-plus jarak-kanan"></i> Add Losses & Gain
                        </a>
                        <a href="<?php echo BASE_URL_CLIENT . '/vendor-po-new-crushed-stone.php'; ?>" class="btn btn-default" style="min-width:90px;">
                            <i class="fa fa-reply jarak-kanan"></i> Kembali
                        </a>

                    </div>
                <?php } elseif ($totalVolumeTerima < $dt11 && $totalVolumeTerima > 0) { ?>
                    <a href="<?php echo BASE_URL_CLIENT . '/vendor-po-crushed-stone-loss-gain.php?' . paramEncrypt('idr=' . $idr); ?>" class="btn btn-success">
                        <i class="fa fa-plus jarak-kanan"></i> Add Losses & Gain
                    </a>
                    <a href="<?php echo BASE_URL_CLIENT . '/vendor-po-crushed-stone-new-terima-add.php?' . paramEncrypt('idr=' . $idr); ?>" class="btn btn-primary">
                        <i class="fa fa-plus jarak-kanan"></i> Add Receive
                    </a>
                    <a href="<?php echo BASE_URL_CLIENT . '/vendor-po-new-crushed-stone.php'; ?>" class="btn btn-default" style="min-width:90px;">
                        <i class="fa fa-reply jarak-kanan"></i> Kembali
                    </a>


                <?php } else { ?>
                    <div style="margin: 15px 0px;">
                        <a href="<?php echo BASE_URL_CLIENT . '/vendor-po-crushed-stone-new-terima-add.php?' . paramEncrypt('idr=' . $idr); ?>" class="btn btn-primary">
                            <i class="fa fa-plus jarak-kanan"></i> Add Receive
                        </a>
                        <a href="<?php echo BASE_URL_CLIENT . '/vendor-po-new-crushed-stone.php'; ?>" class="btn btn-default" style="min-width:90px;">
                            <i class="fa fa-reply jarak-kanan"></i> Kembali
                        </a>
                    </div>
                <?php } ?>

                <?php /*
            <form action="<?php echo ACTION_CLIENT.'/vendor-po-terima.php'; ?>" id="gform" name="gform" method="post" role="form" enctype="multipart/form-data">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                </div>
                <div class="box-body">
                    <?php if($action == "update"){ ?>
                    <div class="table-responsive">
                        <table id="tb_vol_terima" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" width="80">NO</th>
                                    <th class="text-center" width="150">TANGGAL TERIMA</th>
                                    <th class="text-center" width="300">PIC</th>
                                    <th class="text-center" width="180">VOLUME TERIMA</th>
                                    <th class="text-center" width="">FILE PENDUKUNG</th>
                                    <th class="text-center" width="80">
                                        <a class="btn btn-primary btn-sm add_volume"><i class="fa fa-plus"></i></a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
							<?php 
                                $rowTerima 	= json_decode($rsm['in_inven_po_detail'], true);
								$arrTerima 	= (is_array($rowTerima) && count($rowTerima) > 0) ? $rowTerima : array(array(""));
								$no_urut = 0;
								foreach($arrTerima as $idx=>$value){ 
									$no_urut++;
									$nom 		= ($value['id_detail']) ? $value['id_detail'] : $no_urut;
									$pathFile 	= $value['filenya'];
									$labelFile 	= 'Unggah File';
									$dataIcons 	= '<div style="width:45px; float:left;">&nbsp;</div>';
									
									if($value['file_upload_ori'] && file_exists($pathFile)){
										$labelFile 	= 'Ubah File';
										$linkPt 	= ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=3&ktg=".$value['filenya']."&file=".$value['file_upload_ori']);
										$dataIcons 	= '
										<div style="width:45px; float:left;">
											<a href="'.$linkPt.'" target="_blank" class="btn btn-sm btn-success" title="download file" style="color: #fff;"> 
											<i class="fa fa-download"></i></a>
										</div>';
									}

									echo '
									<tr data-id="'.$nom.'">
										<td class="text-center"><span class="frmnodasar" data-row-count="'.$nom.'">'.$no_urut.'</span></td>
										<td class="text-left">
											<input type="text" id="tgl_terima'.$nom.'" name="tgl_terima['.$nom.']" class="form-control tgl_terima datepicker" value="'.$value['tgl_terima'].'" />
										</td>
										<td class="text-left">
											<input type="text" id="pic'.$nom.'" name="pic['.$nom.']" class="form-control pic" value="'.$value['pic'].'" />
										</td>
										<td class="text-left">
											<input type="text" id="vol_terima'.$nom.'" name="vol_terima['.$nom.']" class="form-control vol_terima hitung" value="'.$value['vol_terima'].'" />
										</td>
										<td class="text-left">
											<div class="rowuploadnya">
												'.$dataIcons.'
												<div class="simple-fileupload" style="margin-left:45px;">
													<input type="file" name="file_template['.$nom.']" id="file_template'.$nom.'" class="form-inputfile" />
													<label for="file_template'.$nom.'" class="label-inputfile">
														<div class="input-group input-group-sm">
															<div class="input-group-addon btn-primary"><i class="fa fa-upload"></i></div>
															<input type="text" class="form-control" placeholder="'.$labelFile.'" readonly />
														</div>
													</label>
												</div>
											</div>
										</td>
										<td class="text-center">
											<a class="btn btn-danger btn-sm del_volume"><span class="fa fa-trash"></span></a>
										</td>
									</tr>';
								}
							?>
                            </tbody>
                            <tfoot>
                                <tr style="border-top:3px solid #ddd;">
                                    <td class="text-left">&nbsp;</td>
                                    <td class="text-center" colspan="2"><b>Total</b></td>
                                    <td class="text-right">
                                    	<input type="hidden" id="vol_total" name="dt9" value="<?php echo $dt7;?>" />
                                        <input type="text" id="vol_total_cek" name="vol_total_cek" class="form-control text-right" value="<?php echo number_format($dt7); ?>" readonly />
									</td>
                                    <td class="text-left">
                                        <div style="margin:0px 15px;">
                                            <label class="rtl">
                                            	<input type="checkbox" name="is_selesai" id="is_selesai" value="1" <?php echo ($rsm['is_selesai'] == '1')?'checked':''; ?> /> 
                                                Selesai Diterima
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-right">&nbsp;</td>
                                </tr>
                            </tfoot>
                        </table> 
                    </div>
                    <?php } ?>

                    <div style="padding:15px 0px;">
                        <input type="hidden" name="act" value="<?php echo $action;?>" />
                        <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                        <?php if(!$rsm['is_selesai']){ ?>
                        <button type="submit" class="btn btn-primary jarak-kanan" name="btnSbmt" id="btnSbmt" style="min-width:120px;">
                            <i class="fa fa-save jarak-kanan"></i> Simpan
                        </button>
                        <?php } ?>
                        <a href="<?php echo BASE_URL_CLIENT."/vendor-po.php"; ?>" class="btn btn-default" style="min-width:120px;">
                            <i class="fa fa-reply jarak-kanan"></i> Kembali
                        </a>
                    </div>
                    <hr style="border-top:4px double #ddd; margin:0 0 10px;">
                    <p style="margin:0px"><small>* Wajib Diisi</small></p>
                </div>
            </div>
            </form>
			*/ ?>

                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>

    <style>
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

        .form-control {
            height: 30px;
            padding: 5px 10px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 3px;
        }
    </style>
    <script>
        $(document).ready(function() {



            var objAttach = {
                onValidationComplete: function(form, status) {
                    if (status == true) {
                        let jml_po = "<?php echo $dt9; ?>",
                            nilaicek = $("#vol_total_cek").val();
                        let cekKolom = true;

                        $("#tb_vol_terima > tbody > tr").each(function(i, v) {
                            let idnya = $(this).data("id");
                            let kolom1 = $("#tgl_terima" + idnya).val();
                            let kolom2 = $("#vol_terima" + idnya).val();
                            cekKolom = cekKolom && (kolom1 && kolom2);
                        });

                        if (!cekKolom) {
                            swal.fire({
                                icon: "warning",
                                width: '350px',
                                allowOutsideClick: false,
                                html: '<p style="font-size:14px; font-family:arial;">Kolom [Tgl Terima] dan [Volume Terima]<br />belum diisi</p>'
                            });
                            return false;
                        } else if (parseInt(nilaicek) > parseInt(jml_po)) {
                            swal.fire({
                                icon: "warning",
                                width: '350px',
                                allowOutsideClick: false,
                                html: '<p style="font-size:14px; font-family:arial;">Volume yang diterima melebihi dari volume PO</p>'
                            });
                            return false;
                        } else {
                            form.validationEngine('detach');
                            form.submit();
                        }
                    }
                }
            };
            $("form#gform").validationEngine('attach', objAttach);

            $('#table-grid2 tbody').on('click', '[data-action="deleteList"]', function(e) {
                swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes...!!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("body").addClass("loading");
                        var param = $(this).data("param-idx");
                        var handler = function(data) {
                            if (data.error == "") {
                                $("body").removeClass("loading");
                                swal.fire({
                                    title: "Information",
                                    icon: "success",
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    html: '<p style="font-size:14px; font-family:arial;">Data Berhasil Dihapus...</p>',
                                    position: "center",
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then((result) => {
                                    if (result.isDismissed) {
                                        $("body").addClass("loading");
                                        location.reload();
                                    }
                                });
                            } else {
                                $("body").removeClass("loading");
                                swal.fire({
                                    icon: "warning",
                                    width: '350px',
                                    allowOutsideClick: false,
                                    html: '<p style="font-size:14px; font-family:arial;">' + data.error + '</p>'
                                });
                            }
                        };
                        $.post(base_url + "/web/action/vendor-po-new-terima.php", {
                            act: "hapus",
                            param: param
                        }, handler, "json");
                    }
                });
            });

            var objSettingDate = {
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: "c-80:c+10",
                dayNamesMin: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                monthNamesShort: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            };

            $(".hitung").number(true, 0, ".", ",");

            $("#tb_vol_terima").on("click", ".add_volume", function() {
                var tabel = $("#tb_vol_terima");
                var arrId = tabel.find("tbody > tr").map(function() {
                    return parseFloat($(this).data("id")) || 0;
                }).toArray();
                var rwNom = Math.max.apply(Math, arrId);
                var newId = (rwNom == 0) ? 1 : (rwNom + 1);

                var isiHtml =
                    '<tr data-id="' + newId + '">' +
                    '<td class="text-center"><span class="frmnodasar" data-row-count="' + newId + '"></span></td>' +
                    '<td class="text-left">' +
                    '<input type="text" id="tgl_terima' + newId + '" name="tgl_terima[' + newId + ']" class="form-control tgl_terima" />' +
                    '</td>' +
                    '<td class="text-left">' +
                    '<input type="text" id="pic' + newId + '" name="pic[' + newId + ']" class="form-control pic" />' +
                    '</td>' +
                    '<td class="text-left">' +
                    '<input type="text" id="vol_terima' + newId + '" name="vol_terima[' + newId + ']" class="form-control vol_terima text-right" />' +
                    '</td>' +
                    '<td class="text-left">' +
                    '<div class="rowuploadnya">' +
                    '<div style="width:45px; float:left;">&nbsp;</div>' +
                    '<div class="simple-fileupload" style="margin-left:45px;">' +
                    '<input type="file" name="file_template[' + newId + ']" id="file_template' + newId + '" class="form-inputfile" />' +
                    '<label for="file_template' + newId + '" class="label-inputfile">' +
                    '<div class="input-group input-group-sm">' +
                    '<div class="input-group-addon btn-primary"><i class="fa fa-upload"></i></div>' +
                    '<input type="text" class="form-control" placeholder="Unggah File" readonly />' +
                    '</div>' +
                    '</label>' +
                    '</div>' +
                    '</div>' +
                    '</td>' +
                    '<td class="text-center">' +
                    '<a class="btn btn-danger btn-sm del_volume"><span class="fa fa-trash"></span></a>' +
                    '</td>' +
                    '</tr>';
                if (rwNom == 0) {
                    tabel.find('tbody').html(isiHtml);
                } else {
                    tabel.find('tbody > tr:last').after(isiHtml);
                }

                $("#tgl_terima" + newId).datepicker(objSettingDate);
                $("#vol_terima" + newId).number(true, 0, ".", ",");
                tabel.find("span.frmnodasar").each(function(i, v) {
                    $(v).text(i + 1);
                });
            }).on("click", ".del_volume", function() {
                var tabel = $("#tb_vol_terima");
                var jTbl = tabel.find('tbody > tr').length;
                if (jTbl > 1) {
                    var cRow = $(this).closest('tr');
                    cRow.remove();
                    tabel.find("span.frmnodasar").each(function(i, v) {
                        $(v).text(i + 1);
                    });
                    calculate_volterima();
                }
            }).on("keyup blur", ".vol_terima", function() {
                calculate_volterima();
            });

            function calculate_volterima() {
                let grandTotal = 0;
                $(".vol_terima").each(function(i, v) {
                    grandTotal = grandTotal + ($(v).val() * 1);
                });
                $('#vol_total').val(grandTotal);
                $('#vol_total_cek').val(grandTotal);
                $("#vol_total_cek").number(true, 0, ".", ",");
            }

        });
    </script>
</body>

</html>