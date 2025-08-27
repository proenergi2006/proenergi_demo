<style>
.tabel_header td{
	padding: 1px 3px;
	font-size: 9pt;
	height: 18px;
}
.kolom-nomor{
	background-color: #f0f0f0;
	padding: 2px 5px;
	border-bottom: 1px solid #fff;
	border-right: 1px solid #fff;
	font-size: 8pt;
}
.kolom-biaya{
	background-color: #e9e9e9;
	padding: 2px 5px;
	border-bottom: 1px solid #fff;
	font-size: 8pt;
	text-align: right;
}
.b1{
	border-top: 1px solid #000;
}
.b2{
	border-right: 1px solid #000;
}
.b3{
	border-bottom: 1px solid #000;
}
.b4{
	border-left: 1px solid #000;
}
</style>
<div style="width:100%; border: 2px solid #000; padding: 1mm; margin-bottom: 1mm; border-radius: 3mm;">
	<p style="text-align:center; font-size: 12pt; margin: 0mm; font-weight:bold;">NEILLY IRALITA ISWARI, SH, MSi, MKn.</p>
	<p style="text-align:center; font-size: 10pt; margin: 0mm 0mm 1mm; font-weight:bold;">NOTARIS &amp; PPAT</p>
	<p style="text-align:center; font-size: 9pt; margin: 0mm; font-weight:bold;">Ruko Mutiara Faza RD2</p>
	<p style="text-align:center; font-size: 8pt; margin: 0mm;">Jl. Condet Raya No. 27, Pasar Rebo, Jakarta Timur 13760</p>
	<p style="text-align:center; font-size: 8pt; margin: 0mm;">Telp : 021 - 91266420, 87782165, Fax : 021 - 87787102 E-mail : neilly_iralita@yahoo.com</p>
</div>
<div style="width:100%; border: 2px solid #000; padding: 1mm; border-radius: 1mm;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_header">
        <tr>
            <td width="20%">No.</td>
            <td width="2%" align="center">:</td>
            <td width="78%"><?php echo $noKwit."/NIN-KW/".substr($rsm['tanggal_kwitansi'],5,2)."/".substr($rsm['tanggal_kwitansi'],0,4); ?></td>
        </tr>
        <tr>
            <td width="20%">Tanggal</td>
            <td width="2%" align="center">:</td>
            <td width="78%"><?php echo tgl_indo($rsm["tanggal_kwitansi"]); ?></td>
        </tr>
        <tr>
            <td width="20%">Sudah terima dari</td>
            <td width="2%" align="center">:</td>
            <td width="78%"><?php echo $rsm["nama_debitur"]; ?></td>
        </tr>
        <?php if($rsm['extend_debitur'] != ""){ ?>
        <tr>
            <td colspan="2">&nbsp;</td>
            <td width="78%"><?php echo "(".$rsm['extend_debitur'].")"; ?></td>
        </tr>
        <?php } ?>
        <tr>
            <td width="20%">Sebesar</td>
            <td width="2%" align="center">:</td>
            <td width="78%"><?php echo terbilang($total)." Rupiah"; ?></td>
        </tr>
    </table>

    <p style="margin:5px 0px; font-size: 9pt;"><b><u>Untuk Pembayaran</u></b></p>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 5px 0px 3px 20px">
	<?php 
		$nomor = 0;
		foreach($biaya as $arr){
			$nomor++;
			$biaya = number_format($arr['biaya'],0,"",".");
			$sqlKw = "select nama_pekerjaan from sinori_ref_pekerjaan where id_master = '".$arr['tagihan']."'";
			
			if($arr['tagihan'] == "legal") $kwitansi = "Legalisasi / Watermarking / Copy sesuai asli";
			else if(is_numeric($arr['tagihan'])) $kwitansi = ucwords(strtolower($con->getOne($sqlKw)));
			else $kwitansi = $arr['tagihan'];
	?>
        <tr>
            <td width="5%" class="kolom-nomor"><?php echo $nomor; ?></td>
            <td width="65%" class="kolom-nomor"><?php echo $kwitansi; ?></td>
            <td width="25%" class="kolom-biaya"><?php echo $biaya; ?></td>
        </tr>
	<?php } ?>
        <tr>
            <td width="5%" class="kolom-nomor">&nbsp;</td>
            <td width="65%" class="kolom-nomor"><b>Jumlah</b></td>
            <td width="25%" class="kolom-biaya"><b><?php echo number_format($total,0,"","."); ?></b></td>
        </tr>
    </table>

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 0px 0px 5px 20px">
    </table>

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 5px 0px 0px 0px">
        <tr>
            <td width="65%" valign="top">
				<p style="font-size: 9pt;"><b><u>Catatan</u></b></p>
				<p style="font-size: 8pt;"><?php echo "&nbsp;&nbsp;&nbsp;* ".$rsm['norek']; ?></p>
            </td>
            <td width="35%" style="font-size: 9pt;" valign="top" align="center">
				<p><?php echo "Jakarta, ".tgl_indo($rsm['tanggal_kwitansi']); ?></p>
				<p>Yang Menerima</p><br /><br />
				<p><b>(Neilly Iralita Iswari, SH, MSi, MKn)</b></p>
            </td>
		</tr>
    </table>
</div>
<div style="border-top: 1px dashed #000; margin: 25px 0px 0px"></div>
