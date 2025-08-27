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
	margin: 0px 10px 10px 0px; 
	page-break-inside: avoid;
}
.table_review td{
	font-family: arial;
	font-size: 9pt;
	vertical-align: top;
}
.table_review td.kotak_isian{
	height: 70px; 
	border: 1px solid #000; 
}
</style>
<div style="padding:5px; margin-bottom:3px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td class="text-right" width="75%" style="font-family:arial; font-size:10pt; padding:10px 5px;">Customer number registration: </td>
            <td class="text-right" style="border:1px solid #000;">&nbsp;</td>
        </tr>
    </table>
</div>
<div style="background-color:#c6e0b3; border:1px solid #343399; padding:5px; margin-bottom:3px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td class="text-left" width="20%"><img src="<?php echo BASE_IMAGE."/logo-kiri-penawaran.png"; ?>" style="width:90px;" /></td>
            <td class="text-center" width=""><h4 style="font-family:arial; font-size:14pt;"><b>CUSTOMER REVIEW FORM</b></h4></td>
            <td class="text-right" width="20%"><img src="<?php echo BASE_IMAGE."/logo-kanan-penawaran.png"; ?>" style="width:130px;" /></td>
        </tr>
    </table>
</div>

<div style="border:1px solid #343399;">
    <div class="table_display">
        <div class="table_row">
            <div class="table_cell main_padding" style="width:30%; background-color:#343399; color:#fff;">
                <b>1. Rincian Customer</b>
            </div>
            <div class="table_cell main_padding">
                <b>Nama Perusahaan : <?php echo $rsm['nama_customer'];?></b>
            </div>
        </div>
    </div>
</div>
<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
	<div style="margin-bottom:5px;"></div>
    <div class="table_display" style="margin-bottom:10px;">
        <div class="table_row">
            <div class="table_cell main_padding" style="width:30px;">
                <b>1.1</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                <b>Company Status</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $tipebisnis;?>
            </div>
        </div>
	</div>

    <div class="table_display" style="margin-bottom:10px;">
        <div class="table_row">
            <div class="table_cell main_padding" style="width:30px;">
                <b>1.2</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                <b>Company Type</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $ownership;?>
            </div>
        </div>
    </div>
</div>

<div style="border:1px solid #343399;">
    <div class="table_display">
        <div class="table_row">
            <div class="table_cell main_padding" style="width:30%; background-color:#343399; color:#fff;">
                <b>2. Informasi Umum</b>
            </div>
            <div class="table_cell main_padding">
                &nbsp;
            </div>
        </div>
    </div>
</div>
<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
	<div style="margin-bottom:5px;"></div>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
				2.1
			</td>
            <td class="text-left soalnya_padding" width="260">
                Sejak kapan perusahaan itu menjalankan bisnisnya ?
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['review2'];?>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
                2.2
			</td>
            <td class="text-left soalnya_padding" width="260">
                Jumlah cabang yang dimiliki <small><i>(Sebutkan lokasi kabupaten / kota saja jika ada)</i></small>
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['review7'];?>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
                &nbsp;
			</td>
            <td class="text-left soalnya_padding" width="260">
                Berapa Jumlah Karyawan Saat Ini ?
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['review5'];?>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
                2.3
			</td>
            <td class="text-left soalnya_padding" width="260">
                Perusahaan tersebut berbisnis/kerjasama dengan siapa saja ? <small><i>(Vendor / customer)</i></small>
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['review10'];?>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
                2.4
			</td>
            <td class="text-left soalnya_padding" width="260">
                Berapa lama rata-rata hari penerimaan pembayaran dari pemberi kerja / hasil transaksi tersebut ?
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['review16'];?>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
                2.5
			</td>
            <td class="text-left soalnya_padding" width="260">
                Jenis Assets yang dimiliki oleh perusahaan ? 
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['jenis_asset'];?>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
                2.6
			</td>
            <td class="text-left soalnya_padding" width="260">
                Kelengkapan dokumen tagihan yang dibutuhkan untuk proses pembayaran
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['kelengkapan_dok_tagihan'];?>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
                2.7
			</td>
            <td class="text-left soalnya_padding" width="260">
                Alur proses pemeriksaan/review kelengkapan dokumen dan rata-rata waktu yang dibutuhkan sampai proses pembayaran di lakukan 
                <small><i>(gambarkan)</i></small>
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['alur_proses_periksaan'];?>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
                2.8
			</td>
            <td class="text-left soalnya_padding" width="260">
                Apakah customer memiliki jadwal penerimaan invoice & Jadwal Pembayaran tagihan? <small><i>(Jika ada mohon diinformasikan detailnya)</i></small>
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['jadwal_penerimaan'];?>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
                2.9
			</td>
            <td class="text-left soalnya_padding" width="260">
                Siapa yang memiliki Authority terkait pembayaran yang harus dilakukan? <small><i>(Nama, Posisi & No. HP)</i></small>
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['review8'];?>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
                2.10
			</td>
            <td class="text-left soalnya_padding" width="260">
                Existing fuel vendor yang melakukan bisnis dengan perusahaan ? <small><i>(Nama, Creditm term)</i></small>
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['review11'];?>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
                2.11
			</td>
            <td class="text-left soalnya_padding" width="260">
                Historical/ background bisnis yang pernah dimiliki oleh perusahaan / Group tersebut dengan PT. Pro Energi 
                <small><i>(Jika ada)</i></small>
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['background_bisnis'];?>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
                2.12
			</td>
            <td class="text-left soalnya_padding" width="260">
                Lokasi depo sumber produk <small><i>(terminal)</i></small>
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['lokasi_depo'];?>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" width="40">
                2.13
			</td>
            <td class="text-left soalnya_padding" width="260">
                Opportunity bussiness apa saja yang bisa dilakukan dengan perusahaan itu
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['opportunity_bisnis'];?>
            </td>
        </tr>
    </table>
</div>

<div style="border:1px solid #343399;">
    <div class="table_display">
        <div class="table_row">
            <div class="table_cell main_padding" style="width:30%; background-color:#343399; color:#fff;">
                <b>3. Detail Informasi</b>
            </div>
            <div class="table_cell main_padding" style="font-size:7pt; vertical-align:middle;">
                Informasi detail tentang pelanggan yang disesuaikan dengan industrial type (Opportunity vs Risk)
            </div>
        </div>
    </div>
</div>

<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
	<div style="margin-bottom:5px;"></div>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left main_padding" style="height:130px;">
            	<?php echo $rsm['review_summary'];?>
			</td>
        </tr>
    </table>

</div>

<div style="border:1px solid #343399; padding:0px 5px 10px; margin-bottom:10px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_data">
        <tr>
            <td class="text-left" width="38%" style="">Proposed by,</td>
            <td class="text-left" width="" style="">Review by,</td>
            <td class="text-left" width="30%" style="">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-left" style="">Marketing</td>
            <td class="text-left" style="">Administration</td>
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
