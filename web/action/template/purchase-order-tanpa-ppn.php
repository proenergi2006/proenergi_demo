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
                <div style="padding:0;"><img src="<?php echo BASE_IMAGE . "/logo-kiri-penawaran.png"; ?>" width="20%" /></div>
            </td>


            <td>



            </td>
            <td width="30%"><img src="<?php echo BASE_IMAGE . "/logo-kanan-penawaran.png"; ?>" width="20%" /></td>
            <!-- <td align="center">
                        <h1 style="color:#ffcc99">
                            PURCHASE ORDER
                            <hr style="height: 1px; border: 1px solid black; width:100%; margin:3 auto;">
                        </h1>
                    </td> -->
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
                    PURCHASE ORDER
                </h2>
                <hr style="height: 1px; border: 1px solid black; width:75%; margin:3 auto;">
                <p>
                <div><b><?php echo $res[0]['nomor_po']; ?></div>
                </p>
            </td>

            </td>
            <td width="30%"></td>
            <!-- <td align="center">
                        <h1 style="color:#ffcc99">
                            PURCHASE ORDER
                            <hr style="height: 1px; border: 1px solid black; width:100%; margin:3 auto;">
                        </h1>
                    </td> -->
        </tr>
    </table>
</div>
<br>


<br>



<div class="container">
    <table border="0" width="100%">
        <tr>
            <th width="30%" rowspan="3">
                <table class="table_rincian" width="100%" cellspacing="0" cellpadding="5">
                    <tr>
                        <td align="left">
                            <hr style="height: 1px; border: 1px solid black; width:100%; margin:3 auto;">
                            PT PRO ENERGI
                            <hr style="height: 1px; border: 1px solid black; width:100%; margin:3 auto;">
                        </td>

                    </tr>

                    <tr>

                        <td height="100px" valign="top" align="left">


                            GRAHA IRAMA BUILDING LT.6 UNIT G
                            JL. HR RASUNA SAID KAV 1-2
                            KUNINGAN TIMUR JAKARTA SELATAN



                        </td>
                    </tr>
                </table>
            </th>
            <th width="30%" rowspan="3">
                <table class="table_rincian" width="100%" cellspacing="0" cellpadding="5">
                    <tr>
                        <td align="left">
                            <hr style="height: 1px; border: 1px solid black; width:100%; margin:3 auto;">
                            VENDOR
                            <hr style="height: 1px; border: 1px solid black; width:100%; margin:3 auto;">
                        </td>
                    </tr>
                    <tr>
                        <td height="100px" valign="top" align="left">

                            <?php echo $res[0]['nama_suplier']; ?> <p>
                                <?php echo $res[0]['alamat_suplier']; ?>
                            </p>

                        </td>
                    </tr>
                </table>
            </th>
            <th width="10%" rowspan="3">
                <table class="table_rincian" width="100%" cellspacing="0" cellpadding="5">
                    <tr>
                        <td align="left">

                        </td>
                    </tr>
                    <tr>
                        <td height="100px" valign="top" align="left">



                        </td>
                    </tr>
                </table>
            </th>
            <th rowspan="3" width="35%" valign="top">
                <table width="100%" style="border:1 solid #888;" cellspacing="0" cellpadding="5">
                    <tr>
                        <td align="left">Terms </td>
                        <td align="left">:</td>
                        <td align="left">
                            <?php echo $res[0]['terms_suplier']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="left">Vendor Is Taxable</td>
                        <td align="left">:</td>
                        <td align="left">YES</td>
                    </tr>
                    <tr>
                        <td align="left">PO Date</td>
                        <td align="left">:</td>
                        <td align="left">
                            <?php echo tgl_indo($res[0]['tanggal_po']); ?>
                        </td>
                    </tr>
                </table>
                <br>

            </th>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>
<br>
<div style="clear:both"></div>
<table border="1" style="border: 1px solid #888; border-collapse: collapse;" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th align="center" class="b1 b3 b4" width="20%" style="">Origin</th>
            <th align="center" class="b1 b3 b4" width="30%" style="">Destination</th>
            <th align="center" class="b1 b3 b4" width="20%" style="">SPJ </th>
            <th align="center" class="b1 b3 b4" width="10%" style="">Volume (L) </th>
            <th align="center" class="b1 b3 b4" width="12%" style=" ">Price (IDR) </th>
            <th align="center" class="b1  b2  b3 b4" width="20%" style=" ">Total (IDR)</th>

        </tr>
    </thead>
    <tbody>
        <?php
        if (count($res) > 0) {
            $nom = 0;
            $total1 = 0;
            $total2 = 0;
            $total3 = 0;
            foreach ($res as $data) {
                $nom++;
                $volume_po = $data['volume_po'];
                $ongkos_po = $data['ongkos_po_real'];
                $jumlah_po = $volume_po * $ongkos_po;
                $tempal = strtolower(str_replace(array("KABUPATEN ", "KOTA "), array("", ""), $data['nama_kab']));
                $alamat    = $data['alamat_survey'] . " " . ucwords($tempal) . " " . $data['nama_prov'];
                $picust    = json_decode($data['picustomer'], true);

                $tmn1     = ($data['nama_terminal']) ? $data['nama_terminal'] : '';
                $tmn2     = ($data['tanki_terminal']) ? '<br />' . $data['tanki_terminal'] : '';
                $tmn3     = ($data['lokasi_terminal']) ? ', ' . $data['lokasi_terminal'] : '';
                $depot     = $tmn1 . $tmn2 . $tmn3;

                $tmn4     = ($data['trip_po']) ? 'trip: ' . $data['trip_po'] : '';
                $tmn5     = ($data['multidrop_po']) ? '<br />multidrop: ' . $data['multidrop_po'] : '';

                $trip     = $tmn4 . $tmn5;


                if ($data['pod_approved']) {
                    $total1 = $total1 + $volume_po;
                    $total2 = $total2 + $ongkos_po;
                    $total3 = $total3 + $jumlah_po;
                }
        ?>

                <tr>
                    <td valign="top" align="left" style="font-size: 7pt;"> <?php echo $data['nama_terminal']; ?> - <?php echo $data['lokasi_terminal']; ?>
                    </td>
                    <td valign="top" align="left" style="font-size: 7pt;"> <?php echo $data['nama_customer']; ?> - <?php echo $data['alamat_survey']; ?>
                    </td>
                    <td height="30px" style="font-size: 7pt;" valign="top" align="left">
                        SPJ :<?php echo $data['no_spj']; ?> <p>
                            Supir : <?php echo $data['nama_sopir']; ?>
                        <p>
                            No.Plat : <?php echo $data['nomor_plat']; ?>
                        </p>
                        </p>
                    </td>
                    <td valign="top" align="center">
                        <?php echo number_format($volume_po, 0, '', '.'); ?>
                    </td>

                    <td valign="top" align="center">
                        <?php echo number_format($ongkos_po, 0, '', '.'); ?> </td>

                    <td valign="top" align="center">
                        <?php echo number_format($jumlah_po, 0, '', '.'); ?></td>
                </tr>
            <?php } ?>
        <?php } ?>

    </tbody>

</table>


<br>





<table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
        <th width="60%">
            <table width="100%" align="left" border="0">
                <tr>
                    <td width="30%">
                        <table style="border:1 solid #888;" width="100%" cellspacing="0" cellpadding="5">
                            <tr>
                                <th align="left" class="b1 b3 b4"> Remark</th>

                            </tr>

                            <tr>
                                <td valign="top" height="130px" align="left" class="b1 b3 b4">
                                    <b>a.</b> Invoice / Kwitansi / Faktur Pajak (Bila ada PPN) masing - masing 2 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; lembar dan atas nama : <p>
                                        &nbsp; &nbsp; <b>PT.PRO ENERGI</b>
                                    <p>
                                        &nbsp; &nbsp; Graha Irama Building Lantai 6 Unit G Jalan H.R Rasuna Said KAV. 1-2 &nbsp; &nbsp; X-1 RT.006 RW.004

                                        Kuningan Timur Setiabudi

                                        Jakarta Selatan DKI &nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp; Jakarta
                                    <p>
                                        &nbsp; &nbsp;&nbsp;<b>No. NPWP : 02.527.322.8-062.000</b>
                                    <p>
                                        <b>b.</b> Pembayaran melalui transfer (setelah di potong PPH 23)
                                    <p>
                                        <b>c.</b> Mohon cantumkan No.PO dari kami & nomor rekening tujuan &nbsp;&nbsp;&nbsp;&nbsp;pembayaran dengan jelas

                                </td>
                            </tr>
                        </table>
                    </td>




                </tr>
            </table>
        </th>
        <th align="right">
            <table width="100%" style="border:1 solid #888;" cellspacing=" 0" cellpadding="5">
                <tr>
                    <td align="left">Total Volume </td>
                    <td>:</td>
                    <td>
                        <?php echo number_format($total1, 0, '', '.'); ?>
                    </td>
                </tr>
                <tr>
                    <td align="left">Total Price </td>
                    <td>:</td>
                    <td><?php echo number_format($total2, 0, '', '.'); ?></td>
                </tr>
                <tr>
                    <td align="left">Sub Total</td>
                    <td>:</td>
                    <td>
                        <?php echo number_format($total3, 0, '', '.'); ?>
                    </td>
                </tr>

                <tr>
                    <td align="left">Grand Total</td>
                    <td>:</td>
                    <td><b> <?php echo number_format(($total3), 0, '', '.'); ?></b></td>
                </tr>


            </table>
        </th>
    </tr>
</table>
<br>
<table width="100%" border="0">
    <tr>
        <td width="10%">
            Say
        </td>
        <td>
            <table width="100%" style="border:1 solid #888;" cellspacing=" 0" cellpadding="5">
                <tr style="color: #888;">
                    <td width="100%" align="left">
                        <b> <?php echo terbilang_inggris(($total3 + $diskon), 0, '', '.') ?> </b>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>