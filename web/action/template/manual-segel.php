<style>
.tabel_header td{
	padding: 1px 3px;
	font-size: 9pt;
	height: 18px;
}
.tabel_rincian td{
	padding: 3px;
	font-size: 7pt;
	height: 18px;
}
p{
	margin: 0 0 10px;
	text-align: justify;
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
<htmlpageheader name="myHTMLHeader1">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
    	<tr>
            <td width="30%" align="left"><img src="<?php echo BASE_IMAGE."/logo-kiri-penawaran.png"; ?>" width="15%" /></td>
            <td width="40%" align="center">&nbsp;</td>
            <td width="30%" align="right"><img src="<?php echo BASE_IMAGE."/logo-kanan-penawaran.png"; ?>" width="20%" /></td>
		</tr>
	</table>
</htmlpageheader>
<sethtmlpageheader name="myHTMLHeader1" page="ALL" value="on" show-this-page="1" />
<htmlpagefooter name="myHTMLFooter1">
    <p style="margin:0; text-align:right;"><barcode code="<?php echo $barcod;?>" type="C39" size="0.8" /></p>
	<p style="margin:0; text-align:right; font-size:6pt; padding-right:70px;"><?php echo $barcod;?></p>
	<p style="margin:0; text-align:right; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
	<p style="margin:0; text-align:right; font-size:6pt;">Printed by <?php echo $printe;?></p>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />

<p style="font-size:11pt; margin:0; text-align:center"><b>BERITA ACARA</b></p>
<p style="font-size:11pt; margin:0; text-align:center"><b>Nomor: <?php echo $rsm['nomor_acara'];?></b></p>
<hr style="margin:5px 0px;" />
<?php if($rsm['kategori'] == 1){ ?>
<div style="width:100%">
	<div style="width:40%; float:right;">
    	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_rincian">
        	<tr>
            	<td width="90">No. Segel Terakhir</td>
                <td width="10">:</td>
                <td><?php echo $rsm['nomor_akhir'];?></td>
            </tr>
            <tr>
                <td>Stock Segel</td>
                <td>:</td>
                <td><?php echo $rsm['nomor_stock'];?></td>
            </tr>
            <tr>
                <td>No. Segel Terpakai</td>
                <td>:</td>
                <td><?php echo $nomor_segel;?></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td><?php echo tgl_indo($rsm['tanggal_segel']);?></td>
            </tr>
        </table>
    </div>
</div>
<div style="clear:both"></div>
<hr style="margin:5px 0px;" />
<?php } ?>
<?php echo $rsm['keperluan'];?>


