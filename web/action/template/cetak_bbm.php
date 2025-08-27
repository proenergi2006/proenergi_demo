<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Voucher BBM</title>
    <style>
        /* --- Global table styling --- */
        table {
            font-size: 8.5pt;
            border-collapse: collapse;
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
            font-size: 8pt;
            padding: 1px 0px;
        }

        /* --- Borders utility --- */
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

        .b1d {
            border-top: 2px solid #000;
        }

        .b2d {
            border-right: 2px solid #000;
        }

        .b3d {
            border-bottom: 2px solid #000;
        }

        .b4d {
            border-left: 2px solid #000;
        }

        /* --- Main voucher box --- */
        .voucher-box {
            border: 1px solid #000;
            padding: 10px;
            margin: 5px;
        }

        /* --- Header with logo & title --- */
        .header-container {
            overflow: auto;
            margin-bottom: 10px;
        }

        .logo-left {
            float: left;
            width: 30%;
        }

        .logo-left img {
            max-width: 100%;
            height: auto;
        }

        .voucher-title {
            float: left;
            width: 65%;
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.5;
        }

        /* --- Detail info section --- */
        .voucher-info {
            clear: both;
            width: 100%;
            margin-bottom: 15px;
        }

        .voucher-info td {
            padding: 4px 6px;
            font-size: 9pt;
        }

        .voucher-info .label {
            width: 25%;
            font-weight: bold;
        }

        .voucher-info .sep {
            width: 2%;
        }

        /* --- Signature boxes --- */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        .signature-table td {
            width: 33.33%;
            padding: 5px;
            border: 1px solid #000;
            vertical-align: top;
            height: 80px;
            /* tinggi kotak */
            position: relative;
        }

        /* Judul di atas tiap kotak tanda tangan */
        .signature-table td .title {
            display: block;
            position: absolute;
            top: 5px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 8pt;
            font-weight: bold;
        }

        /* Garis tanda tangan di bagian bawah */
        .signature-table td .signature-line {
            display: block;
            position: absolute;
            bottom: 8px;
            left: 10%;
            width: 80%;
            border-top: 1px solid #000;
            height: 0;
            text-align: center;
        }
    </style>
</head>



<body>

    <!-- Footer untuk mPDF -->


    <div class="voucher-box">
        <!-- HEADER -->
        <div class="header-container">
            <table border="0" width="100%">
                <tr>
                    <td width="30%">
                        <div class="logo-left">
                            <img src="<?php echo BASE_IMAGE . '/logo-kiri-penawaran.png'; ?>"
                                alt="Logo Perusahaan" />
                        </div>
                    </td>
                    <td width="10%"></td>
                    <td width="50%">

                        <div class="voucher-title">

                            Voucher BBM
                        </div>
                        <br>
                        <center>
                            <h2> <?= $first['no_spj'] ?> <h2>
                        </center>
                    </td>

                    <td width="30%">

                        <h3>Tanggal : <?= tgl_indo($first['tanggal_kirim']); ?></h3>
                        <br>
                        <h3>Jam : </h3>

                    </td>
                </tr>
            </table>
        </div>
        <br>

        <!-- DETAIL INFO -->
        <table class="voucher-info" border="0">
            <tr>
                <td class="label">Nomor Plat</td>
                <td class="sep">:</td>
                <td><?= $first['nomor_plat'] ?></td>
            </tr>
            <tr>
                <td class="label">Nama Supir</td>
                <td class="sep">:</td>
                <td><?= $first['nama_sopir'] ?></td>
            </tr>
            <tr>
                <td class="label">Volume</td>
                <td class="sep">:</td>
                <td><?php echo number_format($total); ?> Liter</td>
            </tr>

            <tr>
                <td class="label">Lokasi Pengisian </td>
                <td class="sep">:</td>
                <td><?= $first['nama_terminal'] ?></td>
            </tr>
        </table>

        <!-- TANDA TANGAN -->
        <table class="signature-table">
            <tr>
                <td style="position: relative; height: 80px;">

                    <!-- Label di kiri atas -->
                    <span class="title">
                        Disiapkan:
                    </span>

                    <br><br><br><br><br>

                    <!-- Nama/created di bawah garis -->
                    <center>
                        <div>
                            <?php echo htmlspecialchars($created); ?>
                        </div>
                    </center>
    </div>

    </td>

    <td>
        <span class="title">Supir:</span>
        <br><br><br><br><br>
        <center>
            <div>
                <?= $res['nama_sopir'] ?>
            </div>
        </center>
    </td>
    <td>
        <span class="title">Petugas Pengisian:</span>
        <br><br><br><br><br>
        <center>
            <div>

            </div>
        </center>
    </td>
    </tr>
    </table>
    </div>
    <div style="margin-top:10px; text-align:right; font-size:7pt;">
        <i>(This form is valid with sign by computerized system)</i>
    </div>
    <div style="text-align:right; font-size:6pt; margin-bottom:20px;">
        Printed by <?php echo htmlspecialchars($printe); ?>
    </div>
</body>

</html>