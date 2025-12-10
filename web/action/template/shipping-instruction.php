<style>
	table {
		font-size: 9.5pt;
		margin-left: 5px;
		border-collapse: separate;
		border-spacing: 0 5px; /* 5px vertical spacing */
	}
   .container {
		font-size: 9.5pt;
		padding-left: 15px;
		padding-right: 15px;
	}

	

	p {
		margin: 3px 0 8px 5px;
	}

	.tabel_header td {
		padding: 1px 3px;
		font-size: 9pt;
		height: 18px;
	}

	.tabel_rincian th {
		padding: 5px 3px;
		background-color: #ffcc99;
	}

	.tabel_rincian td {
		padding: 3px 2px;
	}


	.b1 {
		border-top: 0.5px solid #000;
	}

	.b2 {
		border-right: 0.5px solid #000;
	}

	.b3 {
		border-bottom: 0.5px solid #000;
	}

	.b4 {
		border-left: 0.5px solid #000;
	}

	.b1d {
		border-top: 0.5px solid #000;
	}

	.b2d {
		border-right: 0.5px solid #000;
	}

	.b3d {
		border-bottom: 0.5px solid #000;
	}

	.b4d {
		border-left: 0.5px solid #000;
	}

	tr.jarak-atas td {
		padding-top: 25px; /* menambah jarak di atas baris ini */
		padding-bottom: 10px; /* optional */
	}
	tr.jarak-bawah td {
		padding-bottom: 20px; /* optional */
	}
	.div-table {
		padding: 0px;
		margin: 0px;
		display: table;
		width: 100%;
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
		padding: 0px;
		margin: 0px;
		display: table-cell;
		float: right;
		font-size: 12px;
	}
</style>
<?php 
$loading_date='';
if(date("m",strtotime($res[0]['etl_date_first']) == date("m",strtotime($res[0]['etl_date_last'])))){
    if(date("d",strtotime($res[0]['etl_date_first'])) == date("d",strtotime($res[0]['etl_date_last']))){
        $loading_date=tgl_indo($res[0]['etl_date_first']);
    }else{
        $loading_date=date("d",strtotime($res[0]['etl_date_first']))." - ".tgl_indo(($res[0]['etl_date_last']));
    }
}else{
    $loading_date=($res[0]['etl_date_first'])."-".tgl_indo($res[0]['etl_date_last']);
}
?>
<htmlpagefooter name="myHTMLFooter1">
	<div style="margin:0; text-align:right;">
		<barcode code="<?php echo $barcod; ?>" type="QR" size="1" />
	</div>
	<br>
	<p style="margin:0; text-align:right; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
	<p style="margin:0; text-align:right; font-size:6pt;">Printed by <?php echo $printe; ?></p>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
<div class="container">
	<img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="15%"  style="float: right;" />
</div>
<div class="container">
	<table border="0" width="100%">
		<tr>
			<td width="30%">
				<div style="padding:0;"></div>
			</td>


			<td>

			<td width="35% " align="center">
				<h2>
					Shipping Instruction
				</h2>
				
			</td>

			</td>
			<td width="30%"></td>
		</tr>
	</table>
</div>
<br>


<div class="container">
	<table border="0" width="100%" style="margin-top: 30px; margin-bottom:20px">
        <tr>
            <td width="18%">Number</td>
            <td width="2%">:</td>
            <td><?php echo $res[0]['nomor_req']; ?></td>
        </tr>
        <tr>
            <td width="18%">Date</td>
            <td width="2%">:</td>
            <td><?php echo tgl_indo($res[0]['created_at']); ?></td>
        </tr>
	</table>
    <!-- <hr style="height: 1.5px; border: 1px solid black; margin:2 auto;"> -->

	<div style="line-height: 1; margin-bottom: 10px; max-width: 50%;">
		<p>To:</p>
		<p><b><?php echo $res[0]['nama_suplier']; ?></b></p>
		<p><?php echo nl2br($res[0]['alamat_suplier']); ?></p>
	</div>

	<div style="line-height: 1.4; margin-top: 10px">
		<p>Dear sir,</p>
		<p>  We hereby request you to arrange shipment with detail as mention below :</p>
	</div>
    <table border="0" width="100%">
        <tr>
            <td width="18%">Product Description</td>
            <td width="2%">:</td>
            <td><?php echo $res[0]['cargo_name']; ?></td>
        </tr>
        <tr>
            <td width="18%">Vessel/Ship</td>
            <td width="2%">:</td>
            <td><?= $res[0]['nama_kapal']." & ". $res[0]['tipe_kapal']; ?></td>
        </tr>
        <tr>
            <td width="18%">Laycan</td>
            <td width="2%">:</td>
            <td><?php echo $loading_date;?></td>
        </tr>
        <tr>
            <td width="18%">Quantity</td>
            <td width="2%">:</td>
            <td><?php echo number_format($res[0]['quantity']); ?> (GSV @15C)</td>
        </tr>
        <tr class="jarak-atas">
            <td width="18%" style="vertical-align: top;"><i>Shipper</i></td>
            <td width="2%" style="vertical-align: top;">:</td>
            <td>
			    <div>
					<p style="padding: 2px 0;"><?php echo $res[0]['shipper']; ?></p>
					<p style="padding: 2px 0;">Graha Irama Building Lt. 6G</p>
					<p style="padding: 2px 0;">Jl. HR. Rasuna Said Blok X1, Kav 1-2, Kuningan, Jakarta, 12950</p>
				</div>
			</td>
        </tr>
        <tr class="jarak-atas">
            <td width="18%" style="vertical-align: top;"><i>Consignee</i></td>
            <td width="2%" style="vertical-align: top;">:</td>
            <td>
				<div style="line-height: 1.4;">
					<p><?php echo $res[0]['consignee']; ?></p>
					<p>Graha Irama Building Lt. 6 Unit F</p>
					<p>Jl. HR. Rasuna Said Blok X1, Kav 1-2, Kuningan, Jakarta, 12950</p>
				</div>
			</td>
        </tr>
        <tr>
            <td width="18%">Port of Loading</td>
            <td width="2%">:</td>
            <td><?= $res[0]['asal_angkut']; ?></td>
        </tr>
        <tr class="jarak-bawah">
            <td width="18%">Port of Discharge</td>
            <td width="2%">:</td>
            <td><?= $res[0]['tujuan_angkut']; ?></td>
        </tr>
     
	</table>
    <p style="font-size: 12px;margin-bottom:30px">Regards,</p>
	<div style="margin-bottom:40px;">
		
	</div>

	<p>Muhammad Ridho Husaini</p>
	<p style="width:150px; border-top:1px solid black; margin-bottom:5px;"></p>
	<p>Management Logistik</p>

</div>