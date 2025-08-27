<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
	$sql = "select a.*, b.nama_customer, b.alamat_customer, b.telp_customer, b.fax_customer, c.fullname, c.mobile_user, c.email_user, 
			d.nama_cabang, e.jenis_produk, f.nama_prov, g.nama_kab, h.jenis_produk, h.merk_dagang 
			from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join acl_user c on b.id_marketing = c.id_user 
			join pro_master_cabang d on a.id_cabang = d.id_master join pro_master_produk e on a.produk_tawar = e.id_master 
			join pro_master_provinsi f on b.prov_customer = f.id_prov join pro_master_kabupaten g on b.kab_customer = g.id_kab
			join pro_master_produk h on a.produk_tawar = h.id_master 
			where a.id_customer = '".$idr."' and a.id_penawaran = '".$idk."'";
	$rsm = $con->getRecord($sql);
	$picom = $con->getOne("select fullname from acl_user where id_role = 6 and id_wilayah = '".$rsm['id_cabang']."'");
	$arrTgl = array(1=>"I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
	$alamat = $rsm['alamat_customer']." ".str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['nama_kab'])." ".$rsm['nama_prov'];

	$arrKondInd	= array(0=>'', 1=>"Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
	$arrKondEng = array(0=>'', 1=>"After Invoice Receive", "After Delivery", "After Loading");
	$jenis_net	= $rsm['jenis_net'];
	$arrPayment = array("CREDIT"=>"CREDIT ".$rsm['jangka_waktu']." Hari ".$arrKondInd[$jenis_net], "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");

	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == '11'){
		$nama_role = "Marketing";
	} else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == '17'){
		$nama_role = "Key Account Executive";
	} else{
		$nama_role = "";
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>
<body>
<center>
	<div style="width:210mm; height:297mm; border:1px solid #000; margin:10px 0px; padding:50px 20px 20px;">

        <div style="margin-left:50px; padding-top:15px; text-align:left;">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="70%"><span style="font-size:12px;">No. Ref <b><?php echo $rsm['nomor_surat']; ?></b></span></td>
                    <td width="30%"><span style="font-size:12px;"><?php echo $rsm['nama_cabang'].", ".tgl_indo(date("Y/m/d")); ?></span></td>
                </tr>
            </table><br />

            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                	<td width="40%">Kepada Yth :</td>
                	<td width="60%">&nbsp;</td>
				</tr>
                <tr>
                	<td><b><?php echo $rsm['nama_customer']; ?></b></td>
                	<td>&nbsp;</td>
				</tr>
                <tr>
                	<td><?php echo $rsm['alamat_up']; ?></td>
                	<td>&nbsp;</td>
				</tr>
            </table><br />

            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="70%"><b>UP. <u><?php echo $rsm['gelar']." ".$rsm['nama_up']; ?></u></b></td>
                    <td width="5%"><span style="font-size:12px;">Telp.</span></td>
                    <td width="25%"><span style="font-size:12px;"><?php echo $rsm['telp_up']; ?></span></td>
                </tr>
                <tr>
                    <td width="70%" valign="top" style="padding-left:26px;"><span style="font-size:12px;"><?php echo $rsm['jabatan_up']; ?></span></td>
                    <td width="5%"><span style="font-size:12px;">Fax.</span></td>
                    <td width="25%"><span style="font-size:12px;"><?php echo $rsm['fax_up']; ?></span></td>
                </tr>
            </table><br />
            <p style="margin-bottom:0px;">Dengan Hormat, </p>
            <p align="center">Hal : Penawaran Harga <?php echo $rsm['merk_dagang'];?></p>
            <p>Bersama surat ini, perkenankan kepada kami untuk memperkenalkan, bahwa kami dari PT. Pro Energi sebagai Badan Usaha Berbadan Hukum dan memiliki Izin Niaga BBM dari ESDM, yang bergerak di bidang Bahan Bakar Minyak.</p>
            <p>Dengan pengalaman, jaminan produk, sumber daya, serta sarana, kami percaya mampu untuk memenuhi kebutuhan BBM untuk <?php echo $rsm['nama_customer']; ?>.
            Oleh karena itu, kami ingin menawarkan kepada perusahaan <?php echo $rsm['gelar']; ?>:</p>
            
            <?php 
                $rincian = json_decode($rsm['detail_rincian'], true);
                $formula = json_decode($rsm['detail_formula'], true);
                if($rsm['perhitungan'] == 1){
                    $breakdown = false;
                    foreach($rincian as $temp){
                        $breakdown = $breakdown || $temp["rinci"];
                    }
                }
            ?>
            <div style="border: 1px solid #bbb; border-radius:20px; padding:10px 20px; margin-bottom:10px;">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_rincian">
                    <tr>
                        <td width="25%">1. Produk</td>
                        <td width="3%" align="center">:</td>
                        <td width="70%"><b><?php echo $rsm['merk_dagang'];?></b></td>
                    </tr>
                    <tr>
                        <td>2. Sulphur Content (Maks.)</td>
                        <td align="center">:</td>
                        <td><b>0,25%</b></td>
                    </tr>
                    <tr>
                        <td valign="top" style="padding-top:1px;">3. Harga per liter</td>
                        <td valign="top" style="padding-top:1px;" align="center">:</td>
                        <td valign="top">
                        <?php 
                            if($rsm['perhitungan'] == 1 && !$breakdown){
                                echo '<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tr><td><b>Rp. '.number_format($rsm['harga_dasar'],0,'','.').' (Harga terima '.$rsm['lok_kirim'].')</b></td></tr>
                                        <tr><td>'.($rsm['ket_harga']?'('.$rsm['ket_harga'].')':'').'</td></tr>
                                      </table>';
                            } else if($rsm['perhitungan'] == 1 && $breakdown){
                                $textInclude = "(Harga sudah termasuk ";
                                echo '<table width="65%" border="0" cellpadding="0" cellspacing="0">';
                                foreach($rincian as $idxT=>$temp){
                                    if($temp['rinci']){
                                        $textInclude .= ($idxT > 0)?$temp['rincian'].', ':'';
                                        echo '<tr>
                                                <td width="30%" style="font-size:12px; height:16px; text-align:left;">'.$temp['rincian'].'</td>
                                                <td width="30%" style="font-size:12px; height:16px; text-align:right;">'.($temp['nilai']?$temp['nilai'].' %':'').'</td>
                                                <td width="20%" style="font-size:8pt; height:16px; text-align:center;">Rp. </td>
                                                <td width="20%" style="font-size:12px; height:16px; text-align:right;">'.($temp['biaya']?number_format($temp['biaya'],0,'','.'):'').'</td>
                                              </tr>';
                                    }
                                }
                                echo '<tr>
                                        <td width="30%" class="b1" style="font-size:12px; height:16px;"><b>Harga Per liter</b></td>
                                        <td width="30%" class="b1" style="font-size:12px; height:16px;">&nbsp;</td>
                                        <td width="20%" class="b1" style="font-size:8pt; height:16px; text-align: center;"><b>Rp. </b></td>
                                        <td width="20%" class="b1" style="font-size:12px; height:16px; text-align:right;"><b>'.number_format($rsm['harga_dasar'],0,'','.').'</b></td>
                                      </tr></table>
                                      <p style="font-size:10px;">'.substr($textInclude,0,-2).')</p>';
                            } else if($rsm['perhitungan'] == 2){
                                echo '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
                                foreach($formula as $temp){
                                    echo '<tr><td>'.$temp.'</td></tr>';
                                }
                                echo '<tr><td style="font-size:12px;">(Perhitungan menggunakan formula)</td></tr></table>';
                            } 
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td>4. Payment term</td>
                        <td align="center">:</td>
                        <td><b><?php echo $arrPayment[$rsm['jenis_payment']];?></b></td>
                    </tr>
                    <tr>
                        <td>5. Order Method</td>
                        <td align="center">:</td>
                        <td><?php echo 'PO paling lambat '.$rsm['method_order'].' hari sebelum pengiriman';?></td>
                    </tr>
                    <tr>
                        <td>6. Supply</td>
                        <td align="center">:</td>
                        <td><?php echo 'Produk akan dikirim setelah mendapatkan konfirmasi PO';?></td>
                    </tr>
                    <tr>
                        <td>7. Masa berlaku harga</td>
                        <td align="center">:</td>
                        <td><?php echo tgl_indo($rsm['masa_awal'])." s/d ".tgl_indo($rsm['masa_akhir']); ?></td>
                    </tr>
                    <tr>
                        <td>8. Toleransi</td>
                        <td align="center">:</td>
                        <td><?php echo $rsm['tol_susut']; ?>  % dari total jumlah pengiriman</td>
                    </tr>
                </table>
            </div>
            
            <p>Demikian surat penawaran, kami berharap dapat diberikan kesempatan dan kepercayaan kepada kami untuk dapat berbisnis dengan perusahaan 
            <?php echo strtolower($rsm['gelar']); ?>. Atas perhatian dan kerjasamanya, kami ucapkan terimakasih.</p><br />
            
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="35%">Hormat kami, </td>
                    <td width="15%">&nbsp;</td>
                    <td width="50%" class="b1 b2 b4" style="padding:0px 10px;">Kontak person :</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="b2 b4" style="padding:0px 10px;"><b><?php echo $rsm['fullname']; ?></b></td>
                </tr>
				<tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="b2 b4" style="padding:0px 10px;"><b><?php echo $nama_role; ?></b></td>
                </tr>
                <tr>
                    <td><b><u>Nama Persetujuan</u></b></td>
                    <td>&nbsp;</td>
                    <td class="b2 b4" style="padding:0px 10px;"><b><?php echo $rsm['mobile_user']; ?></b></td>
                </tr>
                <tr>
                    <td>Jabatan Persetujuan</td>
                    <td>&nbsp;</td>
                    <td class="b2 b3 b4" style="padding:0px 10px 5px;"><b><?php echo $rsm['email_user']; ?></b></td>
                </tr>
            </table>
        </div>

	</div>
</center>

<style>
	.b1{border-top: 1px solid #000;}
	.b2{border-right: 1px solid #000;}
	.b3{border-bottom: 1px solid #000;}
	.b4{border-left: 1px solid #000;}
</style>

</body>
</html>      
