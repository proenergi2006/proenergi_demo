<style>
.tabel_data td{
	padding: 5px 3px;
	font-size: 11pt;
	vertical-align: top;
	text-align: left;
}
.tabel_data th{
	padding: 5px 3px;
	font-size: 11pt;
	font-weight: bold;
	text-align: center;
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
<p style="font-size:16pt; font-family:times; text-align:center; margin:0 0 5px;"><?php echo 'DAFTAR AKTA NOTARIS BULAN '.strtoupper(getBulan($bln)).' TAHUN '.$thn; ?></p>
<p style="font-size:16pt; font-family:times; text-align:center; margin:0 0 5px;">DIBUAT OLEH <b>NEILLY IRALITA ISWARI, SH, MSi, MKn.</b></p>
<p style="font-size:14pt; font-family:times; text-align:center; margin:0 0 20px;">NOTARIS DI KOTA ADMINISTRASI JAKARTA TIMUR</p>

<table border="1" cellpadding="0" cellspacing="0" width="100%" class="tabel_data" style="border-collapse:collapse;">
    <tr>
        <th width="8%" style="height:20px">No. Urut</th>
        <th width="6%">No. Akta</th>
        <th width="10%">Tanggal Akta</th>
        <th width="51%">Sifat Akta</th>
        <th width="25%"><u>NAMA PENGHADAP</u><br />dan atau yang diwakili/kuasa</th>
    </tr>
	<?php
        if(count($res2) == 0){
            echo '<tr><td colspan="5" align="center" style="height:20px">Tidak ada data</td></tr>';
        }
        else{
            $data = array();
			foreach($res2 as $data){
    ?>
        <tr>
            <td align="center" style="height:20px"><?php echo $data['nomor_urut']; ?></td>
            <td align="center"><?php echo str_pad($data['nomor_akta'],2,'0',STR_PAD_LEFT); ?></td>
            <td align="center"><?php echo date('d-m-Y', strtotime($data['tanggal_akta'])); ?></td>
            <td align="left"><?php echo $data['nama_pekerjaan']; ?></td>
            <td align="left"><?php echo $data['pihak1']; ?></td>
        </tr>					
    <?php } } ?>
</table>
