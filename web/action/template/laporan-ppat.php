<style>
.tabel_header td{
	padding: 1px 3px;
	font-size: 8pt;
}
.tabel_data td{
	padding: 3px 4px 5px;
	font-size: 6pt;
	vertical-align: top;
}
.tabel_data th{
	padding: 3px;
	font-size: 6pt;
	font-weight: bold;
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
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td width="70%" align="left">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_header">
            	<tr>
                	<td width="10%">Nama PPAT</td>
                	<td width="2%">:</td>
                	<td width="88%">NEILLY IRALITA ISWARI, SH. MSI. MKn</td>
                </tr>
            	<tr>
                	<td>Alamat</td>
                	<td>:</td>
                	<td>Ruko Mutiara Faza RD 2</td>
                </tr>
            	<tr>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>Jalan Condet Raya No. 27, Pasar Rebo, Jakarta Timur 13760</td>
                </tr>
            	<tr>
                	<td>N P W P</td>
                	<td>:</td>
                	<td>09.538.746.746.0-017.000</td>
                </tr>
            	<tr>
                	<td>Wilayah Kerja</td>
                	<td>:</td>
                	<td>Kota Administrasi Jakarta Timur</td>
                </tr>
            </table>
        </td>
        <td width="30%" align="left" valign="top">
        	<p style="font-size:8pt;">Kepada Yth</p>
        	<p style="font-size:8pt;">1. Kepala Kantor Pertanahan Kota Administrasi Jakarta Timur</p>
        	<p style="font-size:8pt;">2. Kepala Kantor Wilayah Badan Pertanahan Nasional DKI Jakarta</p>
        </td>
    </tr>
</table>
<p style="font-size:9pt; text-align:center; margin:0 0 5px;"><b>LAPORAN BULANAN PEMBUATAN AKTA OLEH PPAT</b></p>
<p style="font-size:8pt; text-align:center; margin:0 0 10px;"><b><?php echo 'BULAN '.strtoupper(getBulan($bln)).' '.$thn; ?></b></p>

<table border="1" cellpadding="0" cellspacing="0" width="100%" class="tabel_data" style="border-collapse:collapse;">
    <tr>
        <th rowspan="2">NO</th>
        <th colspan="2" style="height:20px">AKTA</th>
        <th rowspan="2">BENTUK PERBUATAN HUKUM</th>
        <th colspan="2">NAMA, ALAMAT, DAN NPWP</th>
        <th rowspan="2">JENIS DAN NOMOR HAK</th>
        <th rowspan="2">LETAK TANAH DAN BANGUNAN</th>
        <th colspan="2">LUAS (M<sup>2</sup>)</th>
        <th rowspan="2" style="font-size:5pt;">HARGA TRANSAKSI PEROLEHAN / PENGALIHAN HAK<br />(RP 000)</th>
        <th colspan="2">SPPT PBB</th>
        <th colspan="2">SSP</th>
        <th colspan="2">SSB</th>
        <th rowspan="2">KET</th>
    </tr>
    <tr>
        <th style="height:20px">NOMOR</th>
        <th>TANGGAL</th>
        <th>PIHAK YANG MENGALIHKAN</th>
        <th>PIHAK YANG MEMPEROLEH</th>
        <th>TANAH</th>
        <th>BGN</th>
        <th>NOP/<br />TAHUN</th>
        <th>NJOP<br />(RP 000)</th>
        <th>TANGGAL</th>
        <th>(RP)<br />(RP 000)</th>
        <th>TANGGAL</th>
        <th>(RP)<br />(RP 000)</th>
    </tr>
    <tr>
        <th width="4%" style="height:20px">1</th>
        <th width="4%">2</th>
        <th width="5%">3</th>
        <th width="8%">4</th>
        <th width="9%">5</th>
        <th width="9%">6</th>
        <th width="7%">7</th>
        <th width="7%">8</th>
        <th width="4%">9</th>
        <th width="4%">10</th>
        <th width="7%">11</th>
        <th width="4%">12</th>
        <th width="5%">13</th>
        <th width="5%">14</th>
        <th width="5%">15</th>
        <th width="5%">16</th>
        <th width="5%">17</th>
        <th width="3%">18</th>
    </tr>
	<?php
        if(count($res1) == 0){
            echo '<tr><td colspan="18" align="center" style="height:18px">Tidak ada data</td></tr>';
        }
        else{
            $data = array();
			foreach($res1 as $data){
    ?>
        <tr>
            <td align="center" style="height:18px"><?php echo $data['nomor_urut']; ?></td>
            <td align="center"><?php echo $data['nomor_akta'].'/'.date('Y', strtotime($data['tanggal_akta'])); ?></td>
            <td align="center"><?php echo date('d-m-Y', strtotime($data['tanggal_akta'])); ?></td>
            <td align="left"><?php echo $data['nama_pekerjaan']; ?></td>
            <td align="left"><?php echo $data['pihak1']; ?></td>
            <td align="left"><?php echo $data['pihak2']; ?></td>
            <td align="left"><?php echo $data['nomor_hak']; ?></td>
            <td align="left"><?php echo $data['letak_tanah']; ?></td>
            <td align="center"><?php echo $data['luas_tanah']; ?></td>
            <td align="center"><?php echo $data['luas_bangunan']; ?></td>
            <td align="right"><?php echo number_format($data['transaksi'],0,'','.'); ?></td>
            <td align="center"><?php echo $data['sppt_nop']; ?></td>
            <td align="right"><?php echo number_format($data['sppt_njop'],0,'','.'); ?></td>
            <td align="center"><?php echo tgl_indo($data['ssp_tanggal'],'normal','db','-'); ?></td>
            <td align="right"><?php echo number_format($data['ssp_nilai'],0,'','.'); ?></td>
            <td align="center"><?php echo tgl_indo($data['ssb_tanggal'],'normal','db','-'); ?></td>
            <td align="right"><?php echo number_format($data['ssb_nilai'],0,'','.'); ?></td>
            <td align="left">&nbsp;</td>
        </tr>					
    <?php } } ?>
</table>
