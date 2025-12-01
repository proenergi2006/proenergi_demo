<style>
	table {
		font-size: 8.5pt;

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

	.td-ket,
	.td-subisi {
		padding: 1px 0px 2px;
		vertical-align: top;
	}

	.td-subisi {
		font-size: 5pt;
	}

	.td-ket {
		padding: 1px 0px;
		font-size: 8pt;
	}

	p {
		margin: 0 0 10px;
		text-align: justify;
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
	<table border="0" width="100%">
		<tr>
			<td width="30%">
				
			</td>


			<td>



			</td>
			<td width="30%" align="right">
                <div ><img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="15%" /></div>
                <br>
				<p><b>PT PRO ENERGI</b>
                <p>Gedung Graha Irama 6 G</p>
                <p>Jl. HR. Rasuna Said Blok X1, Kav 1-2</p>
                <p>Jakarta 12950 DKI Jakarta - Indonesia</p>
                <p><b>Telp</b>: (021) 5289 2321</p>
                <p><b>Fax</b>: (021) 5289 2310</p>
            </td>
		</tr>
	</table>
</div>
<br>
<div class="container">
	<table border="0" width="100%">
		<tr>
			<td width="30%">
				<div style="padding:0;"></div>
			</td>


			<td>

			<td width="35% " align="center">
				<h2>
					SHIPPING REQUEST
				</h2>
				<hr style="height: 1px; border: 1px solid black; width:75%; margin:3 auto;">
				<p>
				<div><b><?php echo $res[0]['nomor_req']; ?></div>
				</p>
			</td>

			</td>
			<td width="30%"></td>
		</tr>
	</table>
</div>
<br>


<br>



<div class="container">
	<table border="0" width="100%">
        <tr>
            <td width="20%">To</td>
            <td width="5%">:</td>
            <td></td>
        </tr>
        <tr>
            <td width="20%">Attn</td>
            <td width="5%">:</td>
            <td></td>
        </tr>
        <tr>
            <td width="20%">Subject</td>
            <td width="5%">:</td>
            <td>Shipping request for</td>
        </tr>
        <tr>
            <td width="20%">Our ref</td>
            <td width="5%">:</td>
            <td></td>
        </tr>
        <tr>
            <td width="20%">Date</td>
            <td width="5%">:</td>
            <td><?php echo date("d F Y",strtotime($res[0]['created_at'])); ?></td>
        </tr>
	</table>
    <hr style="height: 1.5px; border: 1px solid black; margin:3 auto;">

    <p style="font-size: 12px;">We appoint to arrange our shipment as below mentioned : </p>

    <table border="0" width="100%">
        <tr>
            <td width="20%">Vessel Name</td>
            <td width="5%">:</td>
            <td></td>
        </tr>
        <tr>
            <td width="20%">Flag</td>
            <td width="5%">:</td>
            <td><?php echo $res[0]['flag']; ?></td>
        </tr>
        <tr>
            <td width="20%">GNT/NRT</td>
            <td width="5%">:</td>
            <td></td>
        </tr>
        <tr>
            <td width="20%">Loading Port</td>
            <td width="5%">:</td>
            <td><?php echo $res[0]['loading_port']; ?></td>
        </tr>
        <tr>
            <td width="20%">Discharging Port</td>
            <td width="5%">:</td>
            <td><?php echo $res[0]['nama_terminal']; ?></td>
        </tr>
        <tr>
            <td width="20%">Estimated Loading Date</td>
            <td width="5%">:</td>
            <td><?php echo $loading_date; ?></td>
        </tr>
        <tr>
            <td width="20%">Cargo Name</td>
            <td width="5%">:</td>
            <td><?php echo $res[0]['cargo_name']; ?></td>
        </tr>
        <tr>
            <td width="20%">Quantity</td>
            <td width="5%">:</td>
            <td>&plusmn; <?php echo number_format($res[0]['quantity']); ?> (GSV @15C)</td>
        </tr>
        <tr>
            <td width="20%">Bill of Lading</td>
            <td width="5%">:</td>
            <td><?php echo $res[0]['bill_lading']; ?> (GSV @15C)</td>
        </tr>
        <tr>
            <td width="20%">Loss Tolerance</td>
            <td width="5%">:</td>
            <td>R2 = <?php echo $res[0]['loss_tolerance']; ?> %(SFAL VS SFBD)</td>
        </tr>
        <tr>
            <td width="20%">Freight</td>
            <td width="5%">:</td>
            <td><?php echo $res[0]['freight']; ?></td>
        </tr>
        <tr>
            <td width="20%">Country of Origin</td>
            <td width="5%">:</td>
            <td><?php echo $res[0]['country_origin']; ?></td>
        </tr>
        <tr>
            <td width="20%">Shipper</td>
            <td width="5%">:</td>
            <td><?php echo $res[0]['shipper']; ?></td>
        </tr>
        <tr>
            <td width="20%">Consignee / Nitfit Address</td>
            <td width="5%">:</td>
            <td><?php echo $res[0]['consignee']; ?></td>
        </tr>
        <tr>
            <td width="20%">BL Ship on Board</td>
            <td width="5%">:</td>
            <td><?php echo $res[0]['bl_ship']; ?></td>
        </tr>
	</table>
     <p style="font-size: 12px;">Thank you for kindly attention and cooperation</p>
</div>