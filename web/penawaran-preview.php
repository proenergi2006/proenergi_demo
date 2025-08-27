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
$idk     = htmlspecialchars($enk["idk"], ENT_QUOTES);
$sql = "select a.*, b.nama_customer, b.alamat_customer, b.telp_customer, b.fax_customer, c.fullname, c.mobile_user, c.email_user, 
			d.nama_cabang, e.jenis_produk, f.nama_prov, g.nama_kab, h.jenis_produk, h.merk_dagang 
			from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join acl_user c on b.id_marketing = c.id_user 
			join pro_master_cabang d on a.id_cabang = d.id_master join pro_master_produk e on a.produk_tawar = e.id_master 
			join pro_master_provinsi f on b.prov_customer = f.id_prov join pro_master_kabupaten g on b.kab_customer = g.id_kab
			join pro_master_produk h on a.produk_tawar = h.id_master 
			where a.id_customer = '" . $idr . "' and a.id_penawaran = '" . $idk . "'";
$rsm = $con->getRecord($sql);
$picom = $con->getOne("select fullname from acl_user where id_role = 6 and id_wilayah = '" . $rsm['id_cabang'] . "'");
$arrTgl = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
$alamat = $rsm['alamat_customer'] . " " . str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $rsm['nama_kab']) . " " . $rsm['nama_prov'];

$arrKondInd    = array(0 => '', 1 => "Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
$arrKondEng = array(0 => '', 1 => "After Invoice Receive", "After Delivery", "After Loading");
$jenis_net    = $rsm['jenis_net'];
$arrPayment = array("CREDIT" => "CREDIT " . $rsm['jangka_waktu'] . " Hari " . $arrKondInd[$jenis_net], "CBD" => "CBD (Cash Before Delivery)", "COD" => "COD (Cash On Delivery)");

$rincian     = json_decode($rsm['detail_rincian'], true);
$formula     = json_decode($rsm['detail_formula'], true);
$breakdown     = false;
if ($rsm['perhitungan'] == 1) {
    foreach ($rincian as $idx1 => $temp) {
        $rinci = ($idx1 == 1) ? $temp["rinci"] : $temp["rinci"];
        $breakdown = $breakdown || $rinci;
    }
}

if ($rsm['perhitungan'] == 1 && !$breakdown) {
    $stylenya = ' style="width:210mm; height:297mm; border:1px solid #000; margin:0px 0px 15px; padding:0px; font-size:12pt; font-family:times; position:relative;"';
} else {
    $stylenya = ' style="width:210mm; height:297mm; border:1px solid #000; margin:0px 0px 15px; padding:0px; font-size:10pt; font-family:arial; position:relative;"';
}

$printe = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " " . date("d/m/Y H:i:s") . " WIB";
$barcod = $rsm['kode_barcode'] . '01' . str_pad($rsm['id_penawaran'], 6, '0', STR_PAD_LEFT);
$pembulatan = $rsm['pembulatan'];

if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == '11') {
    $nama_role = "Marketing";
} else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == '17') {
    $nama_role = "Key Account Executive";
} else {
    $nama_role = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>

<body>
    <center>
        <div <?php echo $stylenya; ?>>

            <div style="padding:5mm 15mm; text-align:left; height:30mm;">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                    <tr>
                        <td width="30%"><img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" /></td>
                        <td width="40%">&nbsp;</td>
                        <td width="30%"><img src="<?php echo BASE_IMAGE . "/logo-kanan-penawaran.png"; ?>" /></td>
                    </tr>
                </table>
            </div>

            <div style="padding:0mm 15mm; text-align:left;">
                <div class="div-table" style="margin-bottom:15px;">
                    <div class="div-table-row">
                        <div class="div-table-cell" style="width:70%">No. Ref <b><?php echo $rsm['nomor_surat']; ?></b></div>
                        <div class="div-table-cell"><?php echo $rsm['nama_cabang'] . ", " . tgl_indo(date("Y/m/d")); ?></div>
                    </div>
                </div>

                <div class="div-table" style="margin-bottom:15px;">
                    <div class="div-table-row">
                        <div class="div-table-cell" style="width:40%">Kepada Yth :</div>
                        <div class="div-table-cell">&nbsp;</div>
                    </div>
                    <div class="div-table-row">
                        <div class="div-table-cell" style="width:40%"><b><?php echo $rsm['nama_customer']; ?></b></div>
                        <div class="div-table-cell">&nbsp;</div>
                    </div>
                    <div class="div-table-row">
                        <div class="div-table-cell" style="width:40%"><?php echo $rsm['alamat_up']; ?></div>
                        <div class="div-table-cell">&nbsp;</div>
                    </div>
                </div>

                <div class="div-table" style="margin-bottom:15px;">
                    <div class="div-table-row">
                        <div class="div-table-cell" style="width:70%"><b>UP. <u><?php echo $rsm['gelar'] . " " . $rsm['nama_up']; ?></u></b></div>
                        <div class="div-table-cell" style="width:5%">Telp.</div>
                        <div class="div-table-cell" style="width:25%"><?php echo $rsm['telp_up']; ?></div>
                    </div>
                    <div class="div-table-row">
                        <div class="div-table-cell" style="width:70%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $rsm['jabatan_up']; ?></div>
                        <div class="div-table-cell" style="width:5%">Fax.</div>
                        <div class="div-table-cell" style="width:25%"><?php echo $rsm['fax_up']; ?></div>
                    </div>
                </div>

                <p class="text-justify" style="margin-bottom:0px;">Dengan Hormat, </p>
                <p class="text-center">Hal : Penawaran Harga <?php echo $rsm['merk_dagang']; ?></p>
                <p class="text-justify">Bersama surat ini, perkenankan kepada kami untuk memperkenalkan, bahwa kami dari PT. Pro Energi sebagai Badan Usaha Berbadan Hukum dan memiliki Izin Niaga BBM dari ESDM, yang bergerak di bidang Bahan Bakar Minyak.</p>
                <p class="text-justify">Dengan pengalaman, jaminan produk, sumber daya, serta sarana, kami percaya mampu untuk memenuhi kebutuhan BBM untuk <?php echo $rsm['nama_customer']; ?>.
                    Oleh karena itu, kami ingin menawarkan kepada perusahaan <?php echo $rsm['gelar']; ?>:</p>

                <div style="border: 1px solid #888; border-radius:10px; padding:10px 20px; margin-bottom:10px; font-size:9pt;">
                    <div class="div-table" style="margin-bottom:5px;">
                        <div class="div-table-row">
                            <div class="div-table-cell" style="width:30px;">1.</div>
                            <div class="div-table-cell" style="width:190px;">Product</div>
                            <div class="div-table-cell text-center" style="width:40px;">:</div>
                            <div class="div-table-cell"><b><?php echo $rsm['merk_dagang']; ?></b></div>
                        </div>
                        <div class="div-table-row">
                            <div class="div-table-cell" style="width:30px;">2.</div>
                            <div class="div-table-cell" style="width:190px;"><b>Sulphur Content (Maks.)</b></div>
                            <div class="div-table-cell text-center" style="width:40px;">:</div>
                            <div class="div-table-cell"><b>0,25%</b></div>
                        </div>
                        <div class="div-table-row">
                            <div class="div-table-cell" style="width:30px;">3.</div>
                            <div class="div-table-cell" style="width:190px;">Harga per liter</div>
                            <div class="div-table-cell text-center" style="width:40px;">:</div>
                            <div class="div-table-cell">
                                <?php
                                if ($rsm['perhitungan'] == 1 &&   !$breakdown) {
                                    if ($pembulatan == 0) {
                                        $harga_dasar = number_format($rsm['harga_dasar'], 2, ',', '.');
                                    } elseif ($pembulatan == 1) {
                                        $harga_dasar = number_format($rsm['harga_dasar'], 0, ',', '.');
                                    } elseif ($pembulatan == 2) {
                                        $harga_dasar = number_format($rsm['harga_dasar'], 4, ',', '.');
                                    }
                                    echo '
                                    <p style="margin:0px;"><b>Rp. ' . $harga_dasar . ' (Harga terima ' . $rsm['lok_kirim'] . ')</b></p>
                                    <p style="margin:0px;">' . ($rsm['ket_harga'] ? $rsm['ket_harga'] : '') . '</p>
                                ';
                                } else if ($rsm['perhitungan'] == 1 && $rsm['all_in'] == 0 && $breakdown) {
                                    if ($rsm['gabung_pbbkb'] == '1') {
                                        $total_pbbkb = $rincian[0]['biaya'] + $rincian[3]['biaya'];
                                        $grand_total = $rincian[0]['biaya'] + $rincian[1]['biaya'] + $rincian[2]['biaya'] + $rincian[3]['biaya'];
                                        unset($rincian[3]);
                                        // $harga_dasar = ($pembulatan ? number_format($grand_total, 0, ',', '.') : number_format($grand_total, 2, ',', '.'));
                                        if ($pembulatan == 0) {
                                            $harga_dasar = number_format($grand_total, 2, ',', '.');
                                        } elseif ($pembulatan == 1) {
                                            $harga_dasar = number_format($grand_total, 0, ',', '.');
                                        } elseif ($pembulatan == 2) {
                                            $harga_dasar = number_format($grand_total, 4, ',', '.');
                                        }
                                        $textInclude = "(Harga sudah termasuk ";
                                        $harga_gabung = 0;
                                        echo '<div class="div-table">';
                                        foreach ($rincian as $idxT => $temp) {
                                            if ($temp['rinci']) {
                                                if ($rsm['gabung_oa'] && $idxT == 0) {
                                                    $harga_gabung = $harga_gabung + ($temp['biaya'] ? $temp['biaya'] : 0);
                                                } else if ($rsm['gabung_oa'] && $idxT == 1) {
                                                    $harga_gabung = $harga_gabung + ($temp['biaya'] ? $temp['biaya'] : 0);
                                                    $harga_gabung = ($pembulatan ? number_format($harga_gabung, 0, ',', '.') : number_format($harga_gabung, 2, ',', '.'));

                                                    echo '
                                                <div class="div-table-row">
                                                    <div class="div-table-cell" style="width:35%;">Harga Dasar</div>
                                                    <div class="div-table-cell text-right" style="width:26%;">' . ($temp['nilai'] ? $temp['nilai'] . ' %' : '&nbsp;') . '</div>
                                                    <div class="div-table-cell text-right" style="width:12%;">Rp. </div>
                                                    <div class="div-table-cell text-right" style="width:26%;">' . $harga_gabung . '</div>
                                                </div>';
                                                } else {
                                                    $textInclude .= ($idxT > 0) ? $temp['rincian'] . ', ' : '';
                                                    $biayanya = ($temp['biaya'] ? $temp['biaya'] : '');
                                                    if ($biayanya) {
                                                        if ($temp['rincian'] == 'Harga Dasar') {
                                                            $biayanya = ($pembulatan ? number_format($total_pbbkb, 0, ',', '.') : number_format($total_pbbkb, 2, ',', '.'));
                                                        } else {
                                                            $biayanya = ($pembulatan ? number_format($biayanya, 0, ',', '.') : number_format($biayanya, 2, ',', '.'));
                                                        }

                                                        echo '
                                                    <div class="div-table-row">
                                                        <div class="div-table-cell" style="width:35%;">' . $temp['rincian'] . '</div>
                                                        <div class="div-table-cell text-right" style="width:26%;">' . ($temp['nilai'] ? $temp['nilai'] . ' %' : '&nbsp;') . '</div>
                                                        <div class="div-table-cell text-right" style="width:12%;">Rp. </div>
                                                        <div class="div-table-cell text-right" style="width:26%;">' . $biayanya . '</div>
                                                    </div>';
                                                    }
                                                }
                                            }
                                        }
                                        echo '
                                            <div class="div-table-row">
                                                <div class="div-table-cell b1" style="width:35%;">&nbsp;</div>
                                                <div class="div-table-cell b1 text-right" style="width:26%;">&nbsp;</div>
                                                <div class="div-table-cell b1 text-right" style="width:12%;">Rp. </div>
                                                <div class="div-table-cell b1 text-right" style="width:26%;"><b>' . $harga_dasar . '</b></div>
                                            </div>';
                                        echo '</div>';
                                        echo '<p style="margin:3px 0px;">' . ($rsm['ket_harga'] ? $rsm['ket_harga'] : '') . '</p>';
                                    } else if ($rsm['gabung_pbbkboa'] == '1') {
                                        $total_pbbkboa = $rincian[0]['biaya'] + $rincian[3]['biaya'] + $rincian[1]['biaya'];
                                        $grand_total = $rincian[0]['biaya'] + $rincian[1]['biaya'] + $rincian[2]['biaya'] + $rincian[3]['biaya'];
                                        unset($rincian[3], $rincian[1]);
                                        // $harga_dasar = ($pembulatan ? number_format($grand_total, 0, ',', '.') : number_format($grand_total, 2, ',', '.'));
                                        if ($pembulatan == 0) {
                                            $harga_dasar = number_format($grand_total, 2, ',', '.');
                                        } elseif ($pembulatan == 1) {
                                            $harga_dasar = number_format($grand_total, 0, ',', '.');
                                        } elseif ($pembulatan == 2) {
                                            $harga_dasar = number_format($grand_total, 4, ',', '.');
                                        }
                                        $textInclude = "(Harga sudah termasuk ";
                                        $harga_gabung = 0;
                                        echo '<div class="div-table">';
                                        foreach ($rincian as $idxT => $temp) {
                                            if ($temp['rinci']) {
                                                if ($rsm['gabung_oa'] && $idxT == 0) {
                                                    $harga_gabung = $harga_gabung + ($temp['biaya'] ? $temp['biaya'] : 0);
                                                } else if ($rsm['gabung_oa'] && $idxT == 1) {
                                                    $harga_gabung = $harga_gabung + ($temp['biaya'] ? $temp['biaya'] : 0);
                                                    $harga_gabung = ($pembulatan ? number_format($harga_gabung, 0, ',', '.') : number_format($harga_gabung, 2, ',', '.'));

                                                    echo '
                                                <div class="div-table-row">
                                                    <div class="div-table-cell" style="width:35%;">Harga Dasar</div>
                                                    <div class="div-table-cell text-right" style="width:26%;">' . ($temp['nilai'] ? $temp['nilai'] . ' %' : '&nbsp;') . '</div>
                                                    <div class="div-table-cell text-right" style="width:12%;">Rp. </div>
                                                    <div class="div-table-cell text-right" style="width:26%;">' . $harga_gabung . '</div>
                                                </div>';
                                                } else {
                                                    $textInclude .= ($idxT > 0) ? $temp['rincian'] . ', ' : '';
                                                    $biayanya = ($temp['biaya'] ? $temp['biaya'] : '');
                                                    if ($biayanya) {
                                                        if ($temp['rincian'] == 'Harga Dasar') {
                                                            $biayanya = ($pembulatan ? number_format($total_pbbkboa, 0, ',', '.') : number_format($total_pbbkboa, 2, ',', '.'));
                                                        } else {
                                                            $biayanya = ($pembulatan ? number_format($biayanya, 0, ',', '.') : number_format($biayanya, 2, ',', '.'));
                                                        }

                                                        echo '
                                                    <div class="div-table-row">
                                                        <div class="div-table-cell" style="width:35%;">' . $temp['rincian'] . '</div>
                                                        <div class="div-table-cell text-right" style="width:26%;">' . ($temp['nilai'] ? $temp['nilai'] . ' %' : '&nbsp;') . '</div>
                                                        <div class="div-table-cell text-right" style="width:12%;">Rp. </div>
                                                        <div class="div-table-cell text-right" style="width:26%;">' . $biayanya . '</div>
                                                    </div>';
                                                    }
                                                }
                                            }
                                        }
                                        echo '
                                            <div class="div-table-row">
                                                <div class="div-table-cell b1" style="width:35%;">&nbsp;</div>
                                                <div class="div-table-cell b1 text-right" style="width:26%;">&nbsp;</div>
                                                <div class="div-table-cell b1 text-right" style="width:12%;">Rp. </div>
                                                <div class="div-table-cell b1 text-right" style="width:26%;"><b>' . $harga_dasar . '</b></div>
                                            </div>';
                                        echo '</div>';
                                        echo '<p style="margin:3px 0px;">' . ($rsm['ket_harga'] ? $rsm['ket_harga'] : '') . '</p>';
                                    } else {
                                        $grand_total = $rincian[0]['biaya'] + $rincian[1]['biaya'] + $rincian[2]['biaya'] + $rincian[3]['biaya'];
                                        if ($pembulatan == 0) {
                                            $harga_dasar = number_format($grand_total, 2, ',', '.');
                                        } elseif ($pembulatan == 1) {
                                            $harga_dasar = number_format($grand_total, 0, ',', '.');
                                        } elseif ($pembulatan == 2) {
                                            $harga_dasar = number_format($grand_total, 4, ',', '.');
                                        }
                                        // $harga_dasar = ($pembulatan ? number_format($grand_total, 0, ',', '.') : number_format($grand_total, 2, ',', '.'));
                                        //$harga_dasar = ($pembulatan ? number_format($rsm['harga_dasar'], 0, ',', '.') : number_format($rsm['harga_dasar'], 2, ',', '.'));
                                        $textInclude = "(Harga sudah termasuk ";
                                        $harga_gabung = 0;
                                        echo '<div class="div-table">';
                                        foreach ($rincian as $idxT => $temp) {
                                            if ($temp['rinci']) {
                                                if ($rsm['gabung_oa'] && $idxT == 0) {
                                                    $harga_gabung = $harga_gabung + ($temp['biaya'] ? $temp['biaya'] : 0);
                                                } else if ($rsm['gabung_oa'] && $idxT == 1) {
                                                    $harga_gabung = $harga_gabung + ($temp['biaya'] ? $temp['biaya'] : 0);
                                                    $harga_gabung = ($pembulatan ? number_format($harga_gabung, 0, ',', '.') : number_format($harga_gabung, 2, ',', '.'));

                                                    echo '
                                                    <div class="div-table-row">
                                                        <div class="div-table-cell" style="width:35%;">Harga Dasar</div>
                                                        <div class="div-table-cell text-right" style="width:26%;">' . ($temp['nilai'] ? $temp['nilai'] . ' %' : '&nbsp;') . '</div>
                                                        <div class="div-table-cell text-right" style="width:12%;">Rp. </div>
                                                        <div class="div-table-cell text-right" style="width:26%;">' . $harga_gabung . '</div>
                                                    </div>';
                                                } else {
                                                    $textInclude .= ($idxT > 0) ? $temp['rincian'] . ', ' : '';
                                                    $biayanya = ($temp['biaya'] ? $temp['biaya'] : '');
                                                    if ($biayanya) {
                                                        $biayanya = ($pembulatan ? number_format($biayanya, 0, ',', '.') : number_format($biayanya, 2, ',', '.'));

                                                        echo '
                                                        <div class="div-table-row">
                                                            <div class="div-table-cell" style="width:35%;">' . $temp['rincian'] . '</div>
                                                            <div class="div-table-cell text-right" style="width:26%;">' . ($temp['nilai'] ? $temp['nilai'] . ' %' : '&nbsp;') . '</div>
                                                            <div class="div-table-cell text-right" style="width:12%;">Rp. </div>
                                                            <div class="div-table-cell text-right" style="width:26%;">' . $biayanya . '</div>
                                                        </div>';
                                                    }
                                                }
                                            }
                                        }
                                        echo '
                                        <div class="div-table-row">
                                            <div class="div-table-cell b1" style="width:35%;">&nbsp;</div>
                                            <div class="div-table-cell b1 text-right" style="width:26%;">&nbsp;</div>
                                            <div class="div-table-cell b1 text-right" style="width:12%;">Rp. </div>
                                            <div class="div-table-cell b1 text-right" style="width:26%;"><b>' . $harga_dasar . '</b></div>
                                        </div>';
                                        echo '</div>';
                                        echo '<p style="margin:3px 0px;">' . ($rsm['ket_harga'] ? $rsm['ket_harga'] : '') . '</p>';
                                    }
                                } else if ($rsm['perhitungan'] == 1 && $rsm['all_in'] == 1 && $breakdown) {
                                    if ($pembulatan == 0) {
                                        $harga_dasar = number_format($rsm['harga_dasar'], 2, ',', '.');
                                    } elseif ($pembulatan == 1) {
                                        $harga_dasar = number_format($rsm['harga_dasar'], 0, ',', '.');
                                    } elseif ($pembulatan == 2) {
                                        $harga_dasar = number_format($rsm['harga_dasar'], 4, ',', '.');
                                    }
                                    // $harga_dasar = ($pembulatan ? number_format($rsm['harga_dasar'], 0, ',', '.') : number_format($rsm['harga_dasar'], 2, ',', '.'));
                                    echo '<div class="div-table">';
                                    echo '
                                <div class="">
                                    <div class="" style="width:26%;">Rp. <b>' . $harga_dasar . '</b></div>
                                </div>';
                                    echo '</div>';
                                    echo '<p style="margin:3px 0px;">' . ($rsm['ket_harga'] ? $rsm['ket_harga'] : '') . '</p>';
                                } else if ($rsm['perhitungan'] == 2) {
                                    echo '<div class="div-table">';
                                    foreach ($formula as $temp) {
                                        echo '<div class="div-table-row"><div class="div-table-cell">' . $temp . '</div></div>';
                                    }
                                    echo '<div class="div-table-row"><div class="div-table-cell">(Perhitungan menggunakan formula)</div></div>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="div-table-row">
                            <div class="div-table-cell" style="width:30px;">4.</div>
                            <div class="div-table-cell" style="width:190px;">Metode pembayaran</div>
                            <div class="div-table-cell text-center" style="width:40px;">:</div>
                            <div class="div-table-cell" style=""><b><?php echo $arrPayment[$rsm['jenis_payment']]; ?></b></div>
                        </div>
                        <div class="div-table-row">
                            <div class="div-table-cell" style="width:30px;">5.</div>
                            <div class="div-table-cell" style="width:190px;">Metode pemesanan</div>
                            <div class="div-table-cell text-center" style="width:40px;">:</div>
                            <div class="div-table-cell" style=""><?php echo 'PO paling lambat ' . $rsm['method_order'] . ' hari sebelum pengiriman'; ?></div>
                        </div>
                        <div class="div-table-row">
                            <div class="div-table-cell" style="width:30px;">6.</div>
                            <div class="div-table-cell" style="width:190px;">Metode pengiriman</div>
                            <div class="div-table-cell text-center" style="width:40px;">:</div>
                            <div class="div-table-cell" style=""><?php echo 'Produk akan dikirim setelah mendapatkan konfirmasi PO'; ?></div>
                        </div>
                        <div class="div-table-row">
                            <div class="div-table-cell" style="width:30px;">7.</div>
                            <div class="div-table-cell" style="width:190px;">Masa berlaku harga</div>
                            <div class="div-table-cell text-center" style="width:40px;">:</div>
                            <div class="div-table-cell" style=""><?php echo tgl_indo($rsm['masa_awal']) . " s/d " . tgl_indo($rsm['masa_akhir']); ?></div>
                        </div>
                        <div class="div-table-row">
                            <div class="div-table-cell" style="width:30px;">8.</div>
                            <div class="div-table-cell" style="width:190px;">Toleransi</div>
                            <div class="div-table-cell text-center" style="width:40px;">:</div>
                            <div class="div-table-cell" style=""><?php echo $rsm['tol_susut']; ?> % dari total jumlah pengiriman</div>
                        </div>
                    </div>
                </div>

                <p class="text-justify" style="margin-bottom:5px;">Demikian surat penawaran, kami berharap dapat diberikan kesempatan dan kepercayaan kepada kami untuk dapat berbisnis dengan perusahaan
                    <?php echo strtolower($rsm['gelar']); ?>. Atas perhatian dan kerjasamanya, kami ucapkan terimakasih.</p><br />

                <div class="div-table" style="margin-bottom:5px;">
                    <div class="div-table-row">
                        <div class="div-table-cell" style="width:35%;">Hormat kami, </div>
                        <div class="div-table-cell" style="width:14.7%;">&nbsp;</div>
                        <div class="div-table-cell b1 b2 b4" style="width:50%;">
                            <div style="padding:0px 10px;">Kontak person :</div>
                        </div>
                    </div>
                    <div class="div-table-row">
                        <div class="div-table-cell" style="width:35%;">&nbsp;</div>
                        <div class="div-table-cell" style="width:14.7%;">&nbsp;</div>
                        <div class="div-table-cell b2 b4" style="width:50%;">
                            <div style="padding:0px 10px;"><b><?php echo $rsm['fullname']; ?></b></div>
                        </div>
                    </div>
                    <div class="div-table-row">
                        <div class="div-table-cell" style="width:35%;">&nbsp;</div>
                        <div class="div-table-cell" style="width:14.7%;">&nbsp;</div>
                        <div class="div-table-cell b2 b4" style="width:50%;">
                            <div style="padding:0px 10px;"><b><?php echo $nama_role; ?></b></div>
                        </div>
                    </div>
                    <div class="div-table-row">
                        <div class="div-table-cell" style="width:35%;"><b><u><?php echo $rsm['sm_wil_pic']; ?></u></b></div>
                        <div class="div-table-cell" style="width:14.7%;">&nbsp;</div>
                        <div class="div-table-cell b2 b4" style="width:50%;">
                            <div style="padding:0px 10px;"><b><?php echo $rsm['mobile_user']; ?></b></div>
                        </div>
                    </div>
                    <div class="div-table-row">
                        <div class="div-table-cell" style="width:35%;"><?php echo 'Branch Manager'; ?></div>
                        <div class="div-table-cell" style="width:14.7%;">&nbsp;</div>
                        <div class="div-table-cell b2 b3 b4" style="width:50%;">
                            <div style="padding:0px 10px 5px;"><b><?php echo $rsm['email_user']; ?></b></div>
                        </div>
                    </div>
                </div>

            </div>

            <div style="position:absolute; bottom:0px; width:100%">
                <div style="padding:0mm 15mm; text-align:left; height:25mm;">
                    <p style="margin:0; padding:0 15px 5px; text-align:right; font-size:9pt;"><i>(This form is valid with sign by computerized system)</i></p>
                    <div style="margin:0 10%; border-top:1px solid #000; text-align:center;">
                        <b style="font-size: 10pt;">PT. Pro Energi, </b>
                        <span style="font-size:8pt;">&bull; Gedung Graha Irama Lantai 6 unit G, Jln. HR Rasuna Said Blok X1 Kav 1-2.<br />
                            &bull; Telp. +021 5289 2321, &bull; fax +021 5289 2310 &bull; <span style="color:#0000FF;">www.proenergi.com </span></span>
                    </div>
                    <p class="text-right" style="margin:5px 0 0; font-size:7pt; font-family:arial;">Printed by <?php echo $printe; ?></p>
                </div>
            </div>

        </div>
    </center>

    <?php if ($rsm['term_condition'] != '' || $rsm['term_condition'] != null) { ?>
        <center>
            <div style="width:210mm; height:297mm; border:1px solid #000; margin:0px 0px 15px; padding:0px; font-size:10pt; font-family:arial; position:relative;">

                <div style="padding:5mm 15mm; text-align:left; height:30mm;">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                        <tr>
                            <td width="30%"><img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" /></td>
                            <td width="40%">&nbsp;</td>
                            <td width="30%"><img src="<?php echo BASE_IMAGE . "/logo-kanan-penawaran.png"; ?>" /></td>
                        </tr>
                    </table>
                </div>

                <div style="padding:5mm 15mm; text-align:left;">
                    <div class="div-table" style="margin-bottom:15px;">
                        <div class="div-table-row">
                            <div class="div-table-cell" style="width:100%"><b>Syarat &amp; Ketentuan : </b></div>
                        </div>
                    </div>
                    <div class="div-table" style="margin-bottom:15px;">
                        <div class="div-table-row">
                            <div class="div-table-cell" style="width:100%"><?php echo $rsm['term_condition']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </center>
    <?php } ?>

    <style>
        .tabel_header td {
            padding: 1px 3px;
            font-size: 10pt;
        }

        .tabel_data td {
            padding: 3px 4px 5px;
            font-size: 8pt;
            vertical-align: top;
        }

        .tabel_data th {
            padding: 3px;
            font-size: 8pt;
            font-weight: bold;
        }

        .div-table {
            padding: 0px;
            margin: 0px;
            display: table;
            width: 100%;
            border: none;
        }

        .div-table:last-child {
            border: none;
        }

        .div-table-row {
            padding: 0px;
            margin: 0px;
            display: table-row;
            width: 100%;
            clear: both;
        }

        .div-table-cell {
            padding: 0px 0px 3px;
            margin: 0px;
            display: table-cell;
            float: none;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-justify {
            text-align: justify;
        }

        .b1 {
            border-top: 1px solid #000;
        }

        .b2 {
            border-right: 1px solid #000;
        }

        .b3 {
            border-bottom: 1px solid #000;
        }

        .b4 {
            border-left: 1px solid #000;
        }

        .barcode {
            padding: 0px;
            margin: 0px;
            vertical-align: top;
            color: #000;
        }
    </style>

</body>

</html>