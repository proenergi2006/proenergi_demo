<?php
if (count($res) > 0) {
    $nom = 0;
    foreach ($res as $data) {
        $nom++;

        $volume_po = $data['volume_po'];
        $volume_ri = $data['volume_ri'];
        $volume_bol = $data['volume_bol'];
        $volume = $data['volume'];

        $harga_dasar = $data['harga_tebus'];
        $total_semua = $total_ri * $harga_dasar;
        $total = $data['harga_tebus'] * $data['volume_bol'];
        $totalgainloss = $volume * $harga_dasar;

        $ppn =  (11 * $total) / 100;
        $pph = ($total * 3) / 1000;
        $total_order = $total  + $ppn + $pph;
        $subtotal = $data['subtotal'];
        $ppn11 = $data['ppn_11'];
        $pph22 = $data['pph_22'];
        $pbbkb = $data['pbbkb'];
        $total_order = $data['total_order'];
        $terbilang =  $data['total_order'];

        $total_ri =  $data['volume_ri'] - $data['volume_bol'];


?>
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
                float: left;
                font-size: 12px;
            }
        </style>

        <htmlpagefooter name="myHTMLFooter1">

            <p style="margin:0; text-align:center; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
            <p style="margin:0; text-align:center; font-size:6pt;">Printed by <?php echo $printe; ?></p>
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
        <div style="margin-left:0px;">
            <div class="div-table" style="margin-bottom:15px;">
                <div class="div-table-row">
                    <div class="div-table-cell" style="width:70%">PO Number : <?php echo $data['nomor_po']; ?></div>
                    <div class="div-table-cell" style=""> PO Date : <?= tgl_indo($data['tanggal_inven']) ?></div>
                </div>
            </div>
        </div>
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

                                    <?php echo $data['nama_vendor']; ?>

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
                    <th rowspan="3" width="30%" valign="top">
                        <table width="100%" style="border:1 solid #888;" cellspacing="0" cellpadding="5">
                            <tr>
                                <td align="left">Terms </td>
                                <td align="left">:</td>
                                <td align="left">
                                    <?php echo $data['terms']; ?> <?php echo $data['terms_day']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="left">Vendor Is Taxable</td>
                                <td align="left">:</td>
                                <td align="left">YES</td>
                            </tr>
                            <tr>
                                <td align="left">Delivery Date</td>
                                <td align="left">:</td>
                                <td align="left">

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

        <div style="clear:both"></div>
        <table border="1" style="border: 1px solid #888; border-collapse: collapse;" cellpadding="5" cellspacing="0" width="100%">
            <tr>
                <th align="center" class="b1 b3 b4" width="10%">Item </th>
                <th align="center" class="b1 b3 b4" width="20%">Description</th>
                <th align="center" class="b1 b3 b4" width="5%">Quantity (Litre)</th>
                <th align="center" class="b1 b3 b4" width="10%">Unit Price</th>
                <th align="center" class="b1 b3 b4" width="8%">Disc %</th>
                <th align="center" class="b1 b3 b4" width="8%">Tax</th>
                <th align="center" class="b1 b2 b3 b4" width="20%">Amount</th>
            </tr>
            <tr>
                <td height="100px" valign="top" align="center">
                    <?= $data['jenis_produk'] ?>
                </td>
                <td valign="top" align="center"> <?php echo $data['merk_dagang']; ?> </td>
                <td valign="top" align="center"> <?php echo number_format($volume_bol); ?></td>
                <td valign="top" align="center">IDR <?php echo number_format($harga_dasar, 2, '.', ','); ?> </td>
                <td valign="top" align="center">IDR 0</td>
                <td valign="top" align="center"><?php echo $data['kd_tax']; ?></td>
                <td valign="top" align="center">
                    IDR <?php echo number_format($total, 2, '.', ','); ?>
                </td>
            </tr>
            <tr>
                <td valign="top" align="center">
                    <?= $data['jenis_produk'] ?>
                </td>
                <td valign="top" align="center"> <?php echo $data['merk_dagang']; ?> </td>
                <td valign="top" align="center"> <?php echo number_format($volume); ?></td>
                <td valign="top" align="center">IDR <?php echo number_format($harga_dasar, 2, '.', ','); ?> </td>
                <td valign="top" align="center">IDR 0</td>
                <td valign="top" align="center"><?php echo $data['kd_tax']; ?></td>
                <td valign="top" align="center">
                    <?php
                    if ($volume > 0) {
                        echo "IDR " . number_format($totalgainloss, 2, '.', ',');
                    } else {
                        echo "0";
                    }
                    ?> </td>
            </tr>
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
                                        <th align="left" class="b1 b3 b4"> Note </th>

                                    </tr>

                                    <tr>
                                        <td valign="top" height="130px" align="left" class="b1 b3 b4">
                                            <p> PO : <?php echo $data['keterangan']; ?></p>
                                            <br>
                                            <p> Gain/Loss : <?php echo $data['ket']; ?></p>




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
                            <td align="left"><b>Sub Total</b> </td>
                            <td>:</td>
                            <td>
                                <b> <?php echo number_format($subtotal, 2, '.', ','); ?></b>
                            </td>
                        </tr>

                        <tr>
                            <td align="left"><b>DPP Nilai Lain</b> </td>
                            <td>:</td>
                            <td>
                                <?php $jumdpp = ($subtotal / 12) * 11; ?>
                                <b> <?php echo number_format($jumdpp, 2, '.', ','); ?></b>
                            </td>
                        </tr>
                        <tr>
                            <td align="left">Discount </td>
                            <td>:</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td align="left">PPN </td>
                            <td>:</td>
                            <td>
                                IDR <?php echo number_format($ppn11, 2, '.', ','); ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="left">PPH 22 </td>
                            <td>:</td>
                            <td>
                                IDR <?php echo number_format($pph22, 2, '.', ','); ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="left">PBBKB </td>
                            <td>:</td>
                            <td> IDR <?php echo number_format($pbbkb, 2, '.', ','); ?></td>
                        </tr>
                        <tr>
                            <td align="left"> <b> Total Order </b> </td>
                            <td>:</td>
                            <td><b>IDR <?php echo number_format($total_order, 2, '.', ','); ?></b></td>
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
                                <?php echo terbilang_inggris($terbilang) ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <table width="100%" align="right" border="0" cellspacing="0" cellpadding="5">
            <tr>
                <th width="60%">

                    <table width="50%" align="right" border="0">
                        <tr>
                            <td>

                            </td>
                        </tr>
                        <tr>
                            <td><br><br><br></td>
                        </tr>
                        <tr>
                            <td>

                                <br>
                                <br>

                                <p style="margin:0; text-align:center; font-size:6pt; padding-right:70px;">></p>




                            </td>
                        </tr>
                        <tr align="left">
                            <td align="left"></td>
                        </tr>
                    </table>
                </th>
                <th align="center">
                    <table width="50%" align="right" border="0">
                        <tr>
                            <td>
                                Approved By System
                            </td>
                        </tr>
                        <tr>
                            <td><br><br></td>
                        </tr>
                        <tr>
                            <td>
                                <barcode code="<?php echo $barcod; ?>" type="QR" size="1" />
                                <br>
                                <br>

                                <p style="margin:0; text-align:center; font-size:6pt; padding-right:70px;"><?php echo $barcod; ?></p>
                                <hr style="width: 50%;">
                                Vica Krisdianatha


                            </td>
                        </tr>
                        <tr align="left">
                            <td align="left"></td>
                        </tr>
                    </table>
                </th>
            </tr>
        </table>

        <table align="left" class="table-bordered" border="0" width="100%">

        </table>

<?php if ($nom < count($res)) echo '<pagebreak sheet-size="Letter" margin-left="10mm" margin-right="10mm" margin-top="20mm" margin-bottom="10mm" />';
    }
} ?>