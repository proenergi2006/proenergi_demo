<style>
.tabel_data td{
	padding: 3px 5px;
	vertical-align: middle;
	font-family: arial;
	font-size: 8pt;
}
div{
	font-family: arial;
	font-size: 9pt;
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

.c1{
	border-top: 1px dotted #000;
}
.c2{
	border-right: 1px dotted #000;
}
.c3{
	border-bottom: 1px dotted #000;
}
.c4{
	border-left: 1px dotted #000;
}

.text-justify{
	text-align: justify;
}
.text-left{
	text-align: left;
}
.text-center{
	text-align: center;
}
.text-right{
	text-align: right;
}

.table_display{
	 display: table; 
	 width: 100%;
}
.table_row{
	 display: table-row; 
}
.table_cell{
	display: table-cell;
	float: left;
}

.main_padding{
	padding: 3px 5px;
}
.soalnya_padding{
	padding: 3px 10px 3px 5px;
}
.title_section{
	 padding: 10px;  
	 font-size: 14px; 
}

.table_review{
	page-break-inside: avoid;
}
.table_review td{
	font-family: arial;
	font-size: 8pt;
	vertical-align: top;
}
.table_review td.kotak_isian{
	height: 20px; 
	border: 1px solid #000; 
}

.table_approval, .table_cl{
	page-break-inside: avoid;
}
.table_approval td{
	font-family: arial;
	font-size: 8pt;
	vertical-align: top;
}
.table_cl td{
	font-family: arial;
	font-size: 8pt;
	font-weight: bold;
	vertical-align: middle;
}

.table-data-pembongkaran td{
	font-family: arial;
	font-size: 8pt;
	padding: 5px 3px;
	vertical-align: top;
}
.table-data-pembongkaran td.rowhead{
	background-color: #ccffcc;
	font-weight: bold;
}
.table-data-pembongkaran td.text-left{
	text-align: left;
}
.table-data-pembongkaran td.text-center{
	text-align: center;
}
</style>
<?php /*
<htmlpagefooter name="myHTMLFooter1">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="20%" align="left" style="font-size:6pt;">Hal. {PAGENO}</td>
        <td width="80%" align="right" style="font-size:6pt;">Printed by <?php echo $printe;?></td>
    </tr>
</table>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
*/ ?>

<htmlpageheader name="myHTMLHeader1">
<div style="background-color:#c6e0b3; border:1px solid #343399; padding:5px; margin-bottom:3px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td class="text-left" width="20%"><img src="<?php echo BASE_IMAGE."/logo-kiri-penawaran.png"; ?>" style="width:90px;" /></td>
            <td class="text-center" width=""><h4 style="font-family:arial; font-size:14pt;"><b>LOCATION CUSTOMER REVIEW (LCR)</b></h4></td>
            <td class="text-right" width="20%"><img src="<?php echo BASE_IMAGE."/logo-kanan-penawaran.png"; ?>" style="width:130px;" /></td>
        </tr>
    </table>
</div>
</htmlpageheader>
<sethtmlpageheader name="myHTMLHeader1" page="ALL" value="on" show-this-page="1" />

<div style="border:1px solid #343399;">
    <div class="table_display">
        <div class="table_row">
            <div class="table_cell main_padding" style="width:30%; background-color:#343399; color:#fff;">
                <b>1. General Information</b>
            </div>
            <div class="table_cell main_padding">&nbsp;</div>
        </div>
    </div>
</div>
<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review" style="margin:8px 10px 0px;">
        <tr>
            <td class="text-left main_padding b1 b3 b4" width="120" style="background-color:#ccffcc;"><b>Nama Perusahaan</b></td>
            <td class="text-justify main_padding b1 b2 b3 b4"><?php echo $rsm['nama_customer'];?></td>
        </tr>
        <tr>
            <td class="text-left main_padding b3 b4" style="background-color:#ccffcc;"><b>Alamat Site / Pabrik / Proyek</b></td>
            <td class="text-justify main_padding b2 b3 b4"><?php echo $almtlokasi;?></td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review" style="margin:0px 10px;">
        <tr>
            <td class="text-left main_padding b3 b4" width="120" style="background-color:#ccffcc;"><b>Tanggal Survey</b></td>
            <td class="text-left main_padding b3 b4" width="130"><?php echo tgl_indo($rsm['tgl_survey']);?></td>
            <td class="text-left main_padding b3 b4" width="80" style="background-color:#ccffcc;"><b>Jenis Usaha</b></td>
            <td class="text-left main_padding b3 b4" width="150"><?php echo $rsm['jenis_usaha'];?></td>
            <td class="text-left main_padding b3 b4" width="130" style="background-color:#ccffcc;"><b>Toleransi Penerimaan</b></td>
            <td class="text-left main_padding b2 b3 b4" width=""><?php echo $rsm['toleransi'];?></td>
        </tr>
    </table>

    <?php $jumbaris01 = max(count($surveyor), count($picustomer), count($kompetitor)); ?>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review" style="margin:0px 10px;">
        <tr>
            <td class="text-left main_padding" width="25">&nbsp;</td>
            <td class="text-left main_padding" width="125">&nbsp;</td>
            <td class="text-left main_padding" width="25">&nbsp;</td>
            <td class="text-left main_padding" width="125">&nbsp;</td>
            <td class="text-left main_padding" width="125">&nbsp;</td>
            <td class="text-left main_padding" width="125">&nbsp;</td>
            <td class="text-left main_padding" width="25">&nbsp;</td>
            <td class="text-left main_padding" width="">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-center main_padding b1 b3 b4" style="background-color:#ccffcc; font-weight:bold;" colspan="2">Surveyor</td>
            <td class="text-center main_padding b1 b3 b4" style="background-color:#ccffcc; font-weight:bold;" colspan="2">PIC / Penerima dilapangan</td>
            <td class="text-center main_padding b1 b3 b4" style="background-color:#ccffcc; font-weight:bold;">Jabatan</td>
            <td class="text-center main_padding b1 b3 b4" style="background-color:#ccffcc; font-weight:bold;">No.HP</td>
            <td class="text-center main_padding b1 b2 b3 b4" style="background-color:#ccffcc; font-weight:bold;" colspan="2">Kompetitor</td>
        </tr>
        <?php 
			$jumbaris01 = ($jumbaris01 > 0 ? $jumbaris01 : 1);
			$nombaris01 = 0;
			for($i=0; $i<$jumbaris01; $i++){
				$nombaris01++;
				echo '
				<tr>
					<td class="text-left main_padding b3 b4">'.($surveyor[$i] || $nombaris01 == 1 ? $nombaris01 : '&nbsp;').'</td>
					<td class="text-left main_padding b3 b4">'.($surveyor[$i] || $nombaris01 == 1 ? $surveyor[$i] : '&nbsp;').'</td>
					<td class="text-left main_padding b3 b4">'.($picustomer[$i] || $nombaris01 == 1 ? $nombaris01 : '&nbsp;').'</td>
					<td class="text-left main_padding b3 b4">'.($picustomer[$i] || $nombaris01 == 1 ? $picustomer[$i]['nama'] : '&nbsp;').'</td>
					<td class="text-left main_padding b3 b4">'.($picustomer[$i] || $nombaris01 == 1 ? $picustomer[$i]['posisi'] : '&nbsp;').'</td>
					<td class="text-left main_padding b3 b4">'.($picustomer[$i] || $nombaris01 == 1 ? $picustomer[$i]['telepon'] : '&nbsp;').'</td>
					<td class="text-left main_padding b3 b4">'.($kompetitor[$i] || $nombaris01 == 1 ? $nombaris01 : '&nbsp;').'</td>
					<td class="text-left main_padding b2 b3 b4">'.($kompetitor[$i] || $nombaris01 == 1 ? $kompetitor[$i] : '&nbsp;').'</td>
				</tr>';
			}
		?>
    </table>

    <?php $jumbaris02 = max(count($hasilsurv), count($produkvol)); ?>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review" style="margin:0px 10px 8px;">
        <tr>
            <td class="text-left main_padding" width="25">&nbsp;</td>
            <td class="text-left main_padding" width="">&nbsp;</td>
            <td class="text-left main_padding" width="25">&nbsp;</td>
            <td class="text-left main_padding" width="">&nbsp;</td>
            <td class="text-left main_padding" width="100">&nbsp;</td>
            <td class="text-left main_padding" width="100">&nbsp;</td>
            <td class="text-left main_padding" width="100">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-center main_padding b1 b3 b4" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;" colspan="2" rowspan="2">Hasil Produksi</td>
            <td class="text-center main_padding b1 b3 b4" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;" colspan="2" rowspan="2">Total Produksi / bln</td>
            <td class="text-center main_padding b1 b2 b3 b4" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;" colspan="3">Jam Operational</td>
        </tr>
        <tr>
            <td class="text-center main_padding b3 b4" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;">Senin - Jumat</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;">Sabtu</td>
            <td class="text-center main_padding b2 b3 b4" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;">Minggu</td>
        </tr>
       <?php 
			$jumbaris02 = ($jumbaris02 > 0 ? $jumbaris02 : 1);
			$nombaris02 = 0;
			for($i=0; $i<$jumbaris02; $i++){
				$nombaris02++;
				echo '
				<tr>
					<td class="text-left main_padding b3 b4">'.($hasilsurv[$i] || $nombaris02 == 1 ? $nombaris02 : '&nbsp;').'</td>
					<td class="text-left main_padding b3 b4">'.($hasilsurv[$i] || $nombaris02 == 1 ? $hasilsurv[$i] : '&nbsp;').'</td>
					<td class="text-left main_padding b3 b4">'.($produkvol[$i]['volbul'] || $nombaris02 == 1 ? $nombaris02 : '&nbsp;').'</td>
					<td class="text-left main_padding b3 b4">'.($produkvol[$i]['volbul'] || $nombaris02 == 1 ? $produkvol[$i]['volbul'] : '&nbsp;').'</td>
					<td class="text-left main_padding b3 b4">'.($nombaris02 == 1 ? $jamOperasi[0] : '&nbsp;').'</td>
					<td class="text-left main_padding b3 b4">'.($nombaris02 == 1 ? $jamOperasi[1] : '&nbsp;').'</td>
					<td class="text-left main_padding b2 b3 b4">'.($nombaris02 == 1 ? $jamOperasi[2] : '&nbsp;').'</td>
				</tr>';
			}
		?>
    </table>
</div>

<div style="border:1px solid #343399;">
    <div class="table_display">
        <div class="table_row">
            <div class="table_cell main_padding" style="width:30%; background-color:#343399; color:#fff;">
                <b>2. Location Information</b>
            </div>
            <div class="table_cell main_padding">&nbsp;</div>
        </div>
    </div>
</div>
<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review" style="margin:8px 10px 0px;">
        <tr>
            <td class="text-left main_padding b1 b2 b3 b4" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;" colspan="2">A. Peta</td>
        </tr>
        <tr>
            <td class="text-left main_padding b3 b4" width="65%" style="min-height:171px;">
				<?php
                    if($rsm['layout_lokasi'] && file_exists($upload_dir.$rsm['layout_lokasi'])){
                        $sizeA 	= getimagesize($upload_url.$rsm['layout_lokasi']);
                        $styleA = ($sizeA[0] > 400)?'width:400px; margin:10px':'width:'.$sizeA[0].'px; margin:10px';
                        echo '<p><img src="'.$upload_url.$rsm['layout_lokasi'].'" style="'.$styleA.'" /></p>';
                    }
                ?>
            </td>
            <td class="text-left main_padding b2 b3 b4" width="35%">
            	<p><b>Rute :</b></p><?php echo $rsm['rute_lokasi']; ?>
                <p>&nbsp;</p>
                <p><b>Catatan :</b></p><?php echo $rsm['note_lokasi']; ?>
			</td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review" style="margin:8px 10px 0px;">
        <tr>
            <td class="text-left main_padding b1 b3 b4" width="120" style="background-color:#ccffcc; font-weight:bold;">Koordinat: Latitude</td>
            <td class="text-left main_padding b1 b3 b4" width="120"><?php echo $rsm['latitude_lokasi'];?></td>
            <td class="text-left main_padding b1 b3 b4" width="100" style="background-color:#ccffcc; font-weight:bold;">Longitude</td>
            <td class="text-left main_padding b1 b3 b4" width="120"><?php echo $rsm['longitude_lokasi'];?></td>
            <td class="text-left main_padding b1 b3 b4" width="100" style="background-color:#ccffcc; font-weight:bold;">Jarak dari Depot</td>
            <td class="text-left main_padding b1 b2 b3 b4" width=""><?php echo $rsm['jarak_depot']." KM";?></td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review" style="margin:8px 10px 0px;">
        <tr>
            <td class="text-left main_padding b1 b3 b4" width="120" style="background-color:#ccffcc; font-weight:bold;">Max Kapasitas Truk</td>
            <td class="text-left main_padding b1 b3 b4" width="120"><?php echo $rsm['max_truk'];?></td>
            <td class="text-left main_padding b1 b3 b4" width="100" style="background-color:#ccffcc; font-weight:bold;">Biaya Koordinasi</td>
            <td class="text-left main_padding b1 b3 b4" width="120"><?php echo $rsm['lsm_portal'];?></td>
            <td class="text-left main_padding b1 b3 b4" width="100" style="background-color:#ccffcc; font-weight:bold;">Min. Vol. Pengiriman</td>
            <td class="text-left main_padding b1 b2 b3 b4" width=""><?php echo $rsm['min_vol_kirim']." KL";?></td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review" style="margin:0px 10px 8px;">
        <tr>
            <td class="text-left main_padding b1 b2 b3 b4" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;" colspan="2">B. Layout Pabrik / Site / Lokasi Proyek</td>
        </tr>
        <tr>
            <td class="text-left main_padding b3 b4" width="65%" style="min-height:171px;">
				<?php
					if($rsm['layout_bongkar'] && file_exists($upload_dir.$rsm['layout_bongkar'])){
						$sizeB 	= getimagesize($upload_url.$rsm['layout_bongkar']);
						$styleB = ($sizeB[0] > 400)?'width:400px; margin:10px':'width:'.$sizeB[0].'px; margin:10px';
						echo '<p><img src="'.$upload_url.$rsm['layout_bongkar'].'" style="'.$styleB.'" /></p>';
					}
                ?>
            </td>
            <td class="text-left main_padding b2 b3 b4" width="35%">
            	<p><b><u>Penjelasan Proses Bongkaran :</u></b></p><br /><?php echo $rsm['penjelasan_bongkar']; ?>
			</td>
        </tr>
    </table>
</div>

<div style="border:1px solid #343399;">
    <div class="table_display">
        <div class="table_row">
            <div class="table_cell main_padding" style="width:30%; background-color:#343399; color:#fff;">
                <b>3. Unloading Information</b>
            </div>
            <div class="table_cell main_padding">&nbsp;</div>
        </div>
    </div>
</div>
<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
    <table width="100%" border="1" cellpadding="0" cellspacing="0" class="table-data-pembongkaran" style="border-collapse:collapse; page-break-inside:avoid; margin:8px 10px 0px;">
        <tr>
            <td width="" class="text-left rowhead"><span style="font-size:8pt;"><b>Tangki</b></span></td>
            <td width="14%" class="text-center rowhead">Tipe</td>
            <td width="14%" class="text-center rowhead">Kapasitas</td>
            <td width="14%" class="text-center rowhead">Jumlah</td>
            <td width="14%" class="text-center rowhead">Produk</td>
            <td width="14%" class="text-center rowhead">Inlet Pipa</td>
            <td width="14%" class="text-center rowhead">Ukuran</td>
        </tr>
        <?php 
            $row1kol1 = count($tangki);
            $row1kol2 = count($kapal);
            $row1kols = max($row1kol1, $row1kol2);
            for($idt1=0; $idt1<$row1kols; $idt1++){
        ?> 
        <tr>
            <td class="text-center rowhead">&nbsp;</td>
            <td class="text-center"><?php echo $tangki[$idt1]['tipe'];?></td>
            <td class="text-center"><?php echo $tangki[$idt1]['kapasitas'];?></td>
            <td class="text-center"><?php echo $tangki[$idt1]['jumlah'];?></td>
            <td class="text-center"><?php echo $tangki[$idt1]['produk'];?></td>
            <td class="text-center"><?php echo $tangki[$idt1]['inlet'];?></td>
            <td class="text-center"><?php echo $tangki[$idt1]['ukuran'];?></td>
        </tr>
        <?php } ?>
        <tr>
            <td class="text-left rowhead"><b>Pendukung</b></td>
            <td class="text-center rowhead">Pompa</td>
            <td class="text-center rowhead">Laju Alir</td>
            <td class="text-center rowhead">P.Selang</td>
            <td class="text-center rowhead">Vapour Valve</td>
            <td class="text-center rowhead">Grounding</td>
            <td class="text-center rowhead">Sinyal HP</td>
        </tr>
        <?php 
            $row2kol1 = count($pendukung);
            $row2kol2 = count($jetty);
            $row2kols = max($row2kol1, $row2kol2);
            for($idt2=0; $idt2<$row2kols; $idt2++){
        ?> 
        <tr>
            <td class="text-center">&nbsp;</td>
            <td class="text-center"><?php echo $pendukung[$idt2]['pompa'];?></td>
            <td class="text-center"><?php echo $pendukung[$idt2]['aliran'];?></td>
            <td class="text-center"><?php echo $pendukung[$idt2]['selang'];?></td>
            <td class="text-center"><?php echo $pendukung[$idt2]['valve'];?></td>
            <td class="text-center"><?php echo $pendukung[$idt2]['ground'];?></td>
            <td class="text-center"><?php echo $pendukung[$idt2]['sinyal'];?></td>
        </tr>
        <?php } ?>
        <tr>
            <td class="text-left rowhead"><b>Quantity</b></td>
            <td class="text-center rowhead">Alat Ukur</td>
            <td class="text-center rowhead">Merk</td>
            <td class="text-center rowhead">Tera</td>
            <td class="text-center rowhead">Masa Berlaku</td>
            <td class="text-center rowhead" colspan="2">Flowmeter Tiap Pengiriman</td>
        </tr>
        <?php 
            $row3kol1 = count($kuantitas1);
            $row3kol2 = count($kuantitas2);
            $row3kols = max($row3kol1, $row3kol2);
            for($idt3=0; $idt3<$row3kols; $idt3++){
        ?> 
        <tr>
            <td class="text-center">&nbsp;</td>
            <td class="text-center"><?php echo $kuantitas1[$idt3]['alat'];?></td>
            <td class="text-center"><?php echo $kuantitas1[$idt3]['merk'];?></td>
            <td class="text-center"><?php echo $kuantitas1[$idt3]['tera'];?></td>
            <td class="text-center"><?php echo $kuantitas1[$idt3]['masa'];?></td>
            <td class="text-center" colspan="2"><?php echo $kuantitas1[$idt3]['flowmeter'];?></td>
        </tr>
        <?php } ?>
        <tr>
            <td class="text-left rowhead"><b>Quanlity</b></td>
            <td class="text-center rowhead">Min. Spec</td>
            <td class="text-center rowhead">Uji Lab</td>
            <td class="text-center rowhead" colspan="3">COQ Tiap Pengiriman</td>
            <td class="text-center rowhead">&nbsp;</td>
        </tr>
        <?php 
            $row4kol1 = count($kualitas1);
            $row4kol2 = count($kualitas2);
            $row4kols = max($row4kol1, $row4kol2);
            for($idt4=0; $idt4<$row4kols; $idt4++){
        ?> 
        <tr>
            <td class="text-center">&nbsp;</td>
            <td class="text-center"><?php echo $kualitas1[$idt4]['spec'];?></td>
            <td class="text-center"><?php echo $kualitas1[$idt4]['lab'];?></td>
            <td class="text-center" colspan="3"><?php echo $kualitas1[$idt4]['coq'];?></td>
            <td class="text-center">&nbsp;</td>
        </tr>
        <?php } ?>
        <tr>
            <td class="text-left"><b>Catatan</b></td>
            <td class="text-left" colspan="6"><?php echo $rsm['catatan_tangki'];?></td>
        </tr>
    </table>

    <table width="100%" border="1" cellpadding="0" cellspacing="0" class="table-data-pembongkaran" style="border-collapse:collapse; page-break-inside:avoid; margin:8px 10px;">
        <tr>
            <td width="16%" class="text-left rowhead"><span style="font-size:8pt;"><b>Kapal</b></span></td>
            <td width="14%" class="text-center rowhead">Tipe</td>
            <td width="14%" class="text-center rowhead">Kapasitas</td>
            <td width="14%" class="text-center rowhead">Jumlah</td>
            <td width="14%" class="text-center rowhead">Inlet Pipa</td>
            <td width="14%" class="text-center rowhead">Ukuran</td>
            <td width="14%" class="text-center rowhead">Metode</td>
        </tr>
        <?php 
            $row1kol1 = count($tangki);
            $row1kol2 = count($kapal);
            $row1kols = max($row1kol1, $row1kol2);
            for($idt1=0; $idt1<$row1kols; $idt1++){
        ?> 
        <tr>
            <td class="text-center rowhead">&nbsp;</td>
            <td class="text-center"><?php echo $kapal[$idt1]['tipe'];?></td>
            <td class="text-center"><?php echo $kapal[$idt1]['kapasitas'];?></td>
            <td class="text-center"><?php echo $kapal[$idt1]['jumlah'];?></td>
            <td class="text-center"><?php echo $kapal[$idt1]['inlet'];?></td>
            <td class="text-center"><?php echo $kapal[$idt1]['ukuran'];?></td>
            <td class="text-center"><?php echo $kapal[$idt1]['metode'];?></td>
        </tr>
        <?php } ?>
        <tr>
            <td class="text-left rowhead"><b>Jetty</b></td>
            <td class="text-center rowhead">Max. LOA</td>
            <td class="text-center rowhead">Min. PBL</td>
            <td class="text-center rowhead">Draft (LWS)</td>
            <td class="text-center rowhead">Kekuatan (DWT)</td>
            <td class="text-center rowhead">Izin</td>
            <td class="text-center rowhead">Persyaratan</td>
        </tr>
        <?php 
            $row2kol1 = count($pendukung);
            $row2kol2 = count($jetty);
            $row2kols = max($row2kol1, $row2kol2);
            for($idt2=0; $idt2<$row2kols; $idt2++){
        ?> 
        <tr>
            <td class="text-center">&nbsp;</td>
            <td class="text-center"><?php echo $jetty[$idt2]['loa'];?></td>
            <td class="text-center"><?php echo $jetty[$idt2]['pbl'];?></td>
            <td class="text-center"><?php echo $jetty[$idt2]['lws'];?></td>
            <td class="text-center"><?php echo $jetty[$idt2]['sandar'];?></td>
            <td class="text-center"><?php echo $jetty[$idt2]['izin'];?></td>
            <td class="text-center"><?php echo $jetty[$idt2]['syarat'];?></td>
        </tr>
        <?php } ?>
        <tr>
            <td class="text-left rowhead"><b>Quantity</b></td>
            <td class="text-center rowhead">Alat Ukur</td>
            <td class="text-center rowhead">Merk</td>
            <td class="text-center rowhead">Tera</td>
            <td class="text-center rowhead">Masa Berlaku</td>
            <td class="text-center rowhead" colspan="2">Flowmeter Tiap Pengiriman</td>
        </tr>
        <?php 
            $row3kol1 = count($kuantitas1);
            $row3kol2 = count($kuantitas2);
            $row3kols = max($row3kol1, $row3kol2);
            for($idt3=0; $idt3<$row3kols; $idt3++){
        ?> 
        <tr>
            <td class="text-center">&nbsp;</td>
            <td class="text-center"><?php echo $kuantitas2[$idt3]['alat'];?></td>
            <td class="text-center"><?php echo $kuantitas2[$idt3]['merk'];?></td>
            <td class="text-center"><?php echo $kuantitas2[$idt3]['tera'];?></td>
            <td class="text-center"><?php echo $kuantitas2[$idt3]['masa'];?></td>
            <td class="text-center" colspan="2"><?php echo $kuantitas2[$idt3]['flowmeter'];?></td>
        </tr>
        <?php } ?>
        <tr>
            <td class="text-left rowhead"><b>Quanlity</b></td>
            <td class="text-center rowhead">Min. Spec</td>
            <td class="text-center rowhead">Uji Lab</td>
            <td class="text-center rowhead" colspan="3">COQ Tiap Pengiriman</td>
            <td class="text-center rowhead">&nbsp;</td>
        </tr>
        <?php 
            $row4kol1 = count($kualitas1);
            $row4kol2 = count($kualitas2);
            $row4kols = max($row4kol1, $row4kol2);
            for($idt4=0; $idt4<$row4kols; $idt4++){
        ?> 
        <tr>
            <td class="text-center">&nbsp;</td>
            <td class="text-center"><?php //echo $kualitas2[$idt4]['spec'];?></td>
            <td class="text-center"><?php //echo $kualitas2[$idt4]['lab'];?></td>
            <td class="text-center" colspan="3"><?php //echo $kualitas2[$idt4]['coq'];?></td>
            <td class="text-center">&nbsp;</td>
        </tr>
        <?php } ?>
        <tr>
            <td class="text-left"><b>Catatan</b></td>
            <td class="text-left" colspan="6"><?php //echo $rsm['catatan_kapal'];?></td>
        </tr>
    </table>
</div>

<div style="border:1px solid #343399;">
    <div class="table_display">
        <div class="table_row">
            <div class="table_cell main_padding" style="width:30%; background-color:#343399; color:#fff;">
                <b>4. Picture Information</b>
            </div>
            <div class="table_cell main_padding">&nbsp;</div>
        </div>
    </div>
</div>
<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review" style="margin:8px 10px 0px;">
        <tr>
            <td class="text-left main_padding" width="50%">
				<?php 
					echo '1. Kondisi Jalan Menuju Lokasi<br /><br />';
                    foreach($file_jalan as $file7){
                        $tmb = $upload_url."/thumbnail/".$file7['filename'];
                        if($file7['filename'] && file_exists($upload_dir.$file7['filename']) && file_exists($upload_dir."/thumbnail/".$file7['filename'])){
                            echo '<img src="'.$tmb.'" style="margin:5px;" />';
                        }
                    }
                ?>
            </td>
            <td class="text-left main_padding" width="50%">
				<?php 
					echo '2. Pintu Gerbang &amp; Kantor Perusahaan<br /><br />';
                    foreach($file_kntr as $file1){
                        $tmb = $upload_url."/thumbnail/".$file1['filename'];
                        if($file1['filename'] && file_exists($upload_dir.$file1['filename']) && file_exists($upload_dir."/thumbnail/".$file1['filename'])){
                            echo '<img src="'.$tmb.'" style="margin:5px;" />';
                        }
                    }
                ?>
            </td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review" style="margin:0px 10px 0px;">
        <tr>
            <td class="text-left main_padding" width="50%">
				<?php 
					echo '3. Fasilitas Penyimpanan<br /><br />';
					foreach($file_strg as $file2){
						$tmb = $upload_url."/thumbnail/".$file2['filename'];
						if($file2['filename'] && file_exists($upload_dir.$file2['filename']) && file_exists($upload_dir."/thumbnail/".$file2['filename'])){
							echo '<img src="'.$tmb.'" style="margin:5px;" />';
						}
					}
                ?>
            </td>
            <td class="text-left main_padding" width="50%">
				<?php 
					echo '4. Inlet Pipa<br /><br />';
					foreach($file_inlet as $file3){
						$tmb = $upload_url."/thumbnail/".$file3['filename'];
						if($file3['filename'] && file_exists($upload_dir.$file3['filename']) && file_exists($upload_dir."/thumbnail/".$file3['filename'])){
							echo '<img src="'.$tmb.'" style="margin:5px;" />';
						}
					}
                ?>
            </td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review" style="margin:0px 10px 0px;">
        <tr>
            <td class="text-left main_padding" width="50%">
				<?php 
					echo '5. Alat Ukur<br /><br />';
					foreach($file_ukur as $file4){
						$tmb = $upload_url."/thumbnail/".$file4['filename'];
						if($file4['filename'] && file_exists($upload_dir.$file4['filename']) && file_exists($upload_dir."/thumbnail/".$file4['filename'])){
							echo '<img src="'.$tmb.'" style="margin:5px;" />';
						}
					}
                ?>
            </td>
            <td class="text-left main_padding" width="50%">
				<?php 
					echo '6. Media Datar<br /><br />';
					foreach($file_media as $file5){
						$tmb = $upload_url."/thumbnail/".$file5['filename'];
						if($file5['filename'] && file_exists($upload_dir.$file5['filename']) && file_exists($upload_dir."/thumbnail/".$file5['filename'])){
							echo '<img src="'.$tmb.'" style="margin:5px;" />';
						}
					}
                ?>
            </td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review" style="margin:0px 10px 8px;">
        <tr>
            <td class="text-left main_padding" width="50%">
				<?php 
					echo '7. Keterangan Penunjang Lain<br /><br />';
					foreach($file_ket as $file6){
						$tmb = $upload_url."/thumbnail/".$file6['filename'];
						if($file6['filename'] && file_exists($upload_dir.$file6['filename']) && file_exists($upload_dir."/thumbnail/".$file6['filename'])){
							echo '<img src="'.$tmb.'" style="margin:5px;" />';
						}
					}
                ?>
            </td>
            <td class="text-left main_padding" width="50%">&nbsp;</td>
        </tr>
    </table>
</div>
<div style="border:1px solid #343399; padding:0px 5px 10px; margin-bottom:10px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_data">
        <tr>
            <td class="text-left" width="38%" style="">Fill by,</td>
            <td class="text-left" width="" style="">Review by,</td>
            <td class="text-left" width="30%" style="">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-left" style="">Marketing</td>
            <td class="text-left" style="">Logistik</td>
            <td class="text-left" style="">Area Sales Manager</td>
        </tr>
        <tr>
            <td class="text-left" height="50" colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-left" style="">
            	<div style="border-bottom:1px solid #000;">
                	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
            </td>
            <td class="text-left" style="">
            	<div style="border-bottom:1px solid #000;">
                	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
            </td>
            <td class="text-left" style="">
            	<div style="border-bottom:1px solid #000;">
                	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
            </td>
        </tr>
        <tr>
            <td class="text-left" style="">Name</td>
            <td class="text-left" style="">Name</td>
            <td class="text-left" style="">Name</td>
        </tr>
        <tr>
            <td class="text-left" style="">Date</td>
            <td class="text-left" style="">Date</td>
            <td class="text-left" style="">Date</td>
        </tr>
    </table>
</div>





