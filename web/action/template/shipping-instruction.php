
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        color: #333;
        margin: 20px;
    }

    .title-section {
        text-align: center;
        margin-bottom: 20px;
    }

    .title-text {
        font-size: 18px;
        font-weight: bold;
    }

    .title-line {
        width: 80px;
        height: 2px;
        background: #222;
        margin: 6px auto 0 auto;
    }

    .info-box {
        border: 1px solid #404040ff;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 15px;
    }

    .row {
        display: flex;
        justify-content: space-between;
        /* margin-bottom: 10px; */
    }

    .col {
        width: 48%;
        margin-bottom: 10px;
    }

    .label {
        font-weight: bold;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    table th {
        background: #ffcc99;
        padding: 3px;
        border: 1px solid #717171ff;
        text-align: left;
    }
    table td {
        padding: 8px;
        border: 1px solid #717171ff;
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
    <div>
        <img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="15%"   style="float: right;"/>
    </div>
    <!-- HEADER -->
    <div class="title-section">
        <div class="title-text">SHIPPING INSTRUCTION</div>
        <div class="title-line"></div>
    </div>

    <!-- BOX INFO -->
     
    <div class="info-box">
        <div class="row">
            <div class="col">
                <div class="label">Number:</div>
                <?= $res[0]['nomor_si']; ?>
            </div>
            <div class="col">
                <div class="label">Date:</div>
                <?= date('d M Y', strtotime($res[0]['log_tanggal'])); ?>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="label">Shipper:</div>
                  <?= $res[0]['shipper']; ?>
            </div>
            <div class="col">
                <div class="label">Consignee:</div>
                 <?= $res[0]['consignee']; ?>
            </div>
        </div>
    </div>

    <!-- TABLE SHIPPING DATA -->
    <table>
        <tr>
            <th>Vessel/Ship</th>
            <td><?= $res[0]['nama_kapal']." & ". $res[0]['tipe_kapal']; ?></td>
        </tr>
        <tr>
            <th>Port of Loading</th>
            <td><?= $res[0]['asal_angkut']; ?></td>
        </tr>
        <tr>
            <th>Port of Discharge</th>
            <td><?= $res[0]['tujuan_angkut']; ?></td>
        </tr>
        <tr>
            <th>ETD</th>
            <td><?= date('d M Y', strtotime($res[0]['etl_date_first'])); ?></td>
        </tr>
        <tr>
            <th>ETA</th>
            <td><?= date('d M Y', strtotime($res[0]['etl_date_last'])); ?></td>
        </tr>
        <tr>
            <th>Quantity</th>
            <td><?= number_format($res[0]['quantity']); ?> Liter'15 (GSV)</td>
        </tr>
        <tr>
            <th>Freight</th>
            <td><?= $res[0]['freight']; ?> </td>
        </tr>
    </table>


