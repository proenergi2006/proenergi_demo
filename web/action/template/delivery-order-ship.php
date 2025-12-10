<style>
    .tabel_header td {
        padding: 1px 3px;
        font-size: 8pt;
        height: 18px;
    }

    .tabel_rincian td {
        padding: 2px;
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
        border-top: 1px solid #000;
    }

    .b2 {
        border-right: 1px solid #000;
    }

    .b3 {
        border-bottom: 1px solid #000;
    }

    .b4 {
        border-left: 1px solid #000;
    }

	tr.jarak-bawah td {
		padding-bottom: 20px; /* optional */
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
    <p style="font-size:6pt; text-align:right;">Printed by <?php echo $printe; ?></p>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />

        <div >
          
            <htmlpagefooter name="myHTMLFooter1">
                <div style="margin:0; text-align:right;">
                    <barcode code="<?php echo $barcod; ?>" type="QR" size="1" />
                </div>
                <br>
                <p style="margin:0; text-align:right; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
                <p style="margin:0; text-align:right; font-size:6pt;">Printed by <?php echo $printe; ?></p>
            </htmlpagefooter>
        </div>

        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:30px; font-size: 12px;">
            <tr>
                <td colspan="10" class="b1 b2 b3 b4"><img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="15%"  style="float: left;"/></td>
            </tr>
            <tr>
                <td colspan="5" class="b4 b3">
                    <div style="padding:1px;">
                        <p style="margin:0px; font-size:13px;">Head Office:</p>
                        <p style="margin:0px; font-size:13px;">PT. Pro Energi</p>
                        <p style="margin:0px; font-size:13px;">Graha Irama Building 6G</p>
                        <p style="margin:0px; font-size:13px;">Jl HR Rasuna Said blok X-1, Kav 1-2. Kuningan,</p>
                        <p style="margin:0px; font-size:13px;">Jakarta Selatan, DKI Jakarta</p>
                        <p style="margin:0px; font-size:13px;">Telp: 021 - 5289 2321</p>
                    </div>
                </td>
                <td colspan="5" class="b2 b3" style="vertical-align:top;">
                    <div style="padding:1px;">
                        <div style="margin:0px; font-size:13px;">Representative Office:</div>
                        <div style="margin:0px; font-size:13px;">PT Pro Energi </div>
                        <div style="margin:0px; font-size:13px;">Mangkujenang Industrial Estate, Jl. Trikora, Palaran, Samarinda, Kalimantan Timur</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="10" class="b2 b3 b4" style="font-size:16px;text-align:center;padding:5px">Delivery Order</td>
            </tr>
            <tr>
                <td colspan="2" class="b4">No. Surat</td>
                <td colspan="3">: <?php echo $res[0]['nomor_req']; ?></td>
                <td colspan="2">Kepada</td>
                <td colspan="3" class="b2">: <?= $res[0]['consignee']; ?></td>
            </tr>
            <tr>
                <td colspan="2" class="b4">Transportir</td>
                <td colspan="3">: <?php echo $res[0]['nama_suplier']; ?></td>
                <td colspan="2">Site/Ditsch Port</td>
                <td colspan="3" class="b2">: <?php echo $res[0]['tujuan_angkut']; ?></td>
            </tr>
            <tr>
                <td colspan="2" class="b4">Kapal</td>
                <td colspan="3">: <?= $res[0]['nama_kapal']." & ". $res[0]['tipe_kapal']; ?></td>
                <td colspan="2">Load Port</td>
                <td colspan="3" class="b2">: <?php echo $res[0]['loading_port']; ?></td>
            </tr>
            <tr>
                <td colspan="10" class="b1 b2 b3 b4" style="font-size:12px;">Nama Produk : <b>Minyak Solar/ HSD (B40)</b></td>
            </tr>
            <tr>
                <td colspan="5" class="b3 b4" style="font-size:13px;text-align:center;">Loading</td>
                <td colspan="5" class="b2 b3 b4" style="font-size:13px;text-align:center;">Discharge</td>
            </tr>
            <tr>
                <td class="b4" width="10%" style="text-align:center;">Tanggal</td>
                <td class="b4" width="10%" style="text-align:center;">Vol. (L)</td>
                <td class="b4" width="10%" style="text-align:center;">Tinggi Cairan</td>
                <td class="b4" width="10%" style="text-align:center;">SG</td>
                <td class="b4" width="10%" style="text-align:center;">Temp</td>
                <td class="b4" width="10%" style="text-align:center;">Tanggal</td>
                <td class="b4" width="10%" style="text-align:center;">Vol. (L)</td>
                <td class="b4" width="10%" style="text-align:center;">Tinggi Cairan</td>
                <td class="b4" width="10%" style="text-align:center;">SG</td>
                <td class="b2 b4" width="10%" style="text-align:center;">Temp</td>
            </tr>

            <tr>
                <td class="b1 b3 b4" style="text-align:center;"><?php echo $loading_date; ?></td>
                <td class="b1 b3 b4" style="text-align:right;"><?php echo number_format($res[0]['quantity']); ?></td>
                <td class="b1 b3 b4"></td>
                <td class="b1 b3 b4"></td>
                <td class="b1 b3 b4"></td>

                <td class="b1 b4 b3"></td>
                <td class="b1 b3 b4"></td>
                <td class="b1 b3 b4"></td>
                <td class="b1 b3 b4"></td>
                <td class="b1 b2 b3 b4"></td>
            </tr>

            <tr class="jarak-bawah">
                <td colspan="2" class="b3 b4">Segel Atas</td>
                <td colspan="3" class="b3 b4"></td>
                <td colspan="2" class="b3 b4">Kondisi Segel</td>
                <td colspan="3" class="b2 b3 b4"></td>
            </tr>

            <tr class="jarak-bawah">
                <td colspan="2" class="b3 b4">Segel Bawah</td>
                <td colspan="3" class="b3 b4"></td>
                <td colspan="2" class="b3 b4">Jam Datang</td>
                <td colspan="3" class="b2 b3 b4"></td>
            </tr>
        </table>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tabel_rincian" style="margin-bottom:30px; font-size: 12px;text-align:center;">
            <tr>
                <td width="33%" class="b1 b2 b3 b4">Shipper</td>
                <td width="33%" class="b1 b2 b3 ">Vessel Representative</td>
                <td width="33%" class="b1 b2 b3">Customer</td>
            </tr>
            <tr>
                <td height="60px" class="b2 b4"></td>
                <td height="60px" class="b2 "></td>
                <td height="60px" class="b2 "></td>
            </tr>
            <tr>
                <td class="b1 b2 b3 b4">PT PRO ENERGI</td>
                <td class="b1 b2 b3">MASTER/ CHIEF OFFICER</td>
                <td class="b1 b2 b3">PENERIMA</td>
            </tr>
        </table>
