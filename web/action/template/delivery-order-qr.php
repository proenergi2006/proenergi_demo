<?php
// File: template/delivery-order-qr.php
// Template untuk mencetak QR-code satu per halaman di mPDF, dengan absolute centering
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Order QR</title>
    <style>
        /* Reset margin dan padding */
        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        /* Kontainer halaman untuk positioning relatif */
        .page {
            position: relative;
            width: 100%;
            height: 100%;
        }

        /* QR terpusat absolute di tengah halaman */
        .qr-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Sembunyikan footer default */
        htmlpagefooter[name=myHTMLFooter1] {
            display: none;
        }
    </style>
    <htmlpagefooter name="myHTMLFooter1"></htmlpagefooter>
    <sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
</head>

<body>
    <?php
    // Ambil jumlah segel dan kode QR dari skrip induk
    $jumlah = (int)$res[0]['jumlah_segel'];
    $kode   = htmlspecialchars($barcod);

    for ($i = 1; $i <= $jumlah; $i++): ?>

        <div align="center" style="text-align:center; padding-top:50px;margin-left:-120px;">
            <!-- Cetak satu QR per halaman, absolute centered -->
            <barcode
                code="<?= $kode ?>"
                type="QR"
                size="0.7"
                height="0.7"
                class="qr-center" />
        </div>

        <?php if ($i < $jumlah): ?>
            <pagebreak />
        <?php endif; ?>

    <?php endfor; ?>

</body>

</html>