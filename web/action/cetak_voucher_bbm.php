<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");

$auth       = new MyOtentikasi();
$con        = new Connection();
$flash      = new FlashAlerts;
$enk        = decode($_SERVER['REQUEST_URI']);
$id         = isset($enk["id"]) ? htmlspecialchars($enk["id"], ENT_QUOTES) : '';
$kategori   = isset($enk["kategori"]) ? htmlspecialchars($enk["kategori"], ENT_QUOTES) : '';


$sql = "SELECT a.*, CONCAT(b.nama_mobil,' - ', b.plat_mobil) as nama_mobil, CONCAT(e.nama_transportir,' - ', d.nomor_plat) as nama_truck, c.nama_terminal, c.tanki_terminal FROM pro_pengisian_solar_mobil_opr a LEFT JOIN pro_master_mobil b ON a.id_mobil=b.id_mobil LEFT JOIN pro_master_terminal c ON a.id_terminal=c.id_master LEFT JOIN pro_master_transportir_mobil d ON a.id_truck=d.id_master LEFT JOIN pro_master_transportir e ON d.id_transportir=e.id_master WHERE a.id = '" . $id . "'";
$data = $con->getRecord($sql);

if ($data['nama_mobil'] == NULL) {
    $unit = $data['nama_truck'];
} else {
    $unit = $data['nama_mobil'];
}
// echo json_encode($sql);
// exit();
if ($kategori == "pengajuan_awal") {
    $judul = "Voucher BBM";
    if (fmod($data['volume'], 1) == 0.0) {
        $volume = number_format($data['volume'], 0, '.', ',');
    } else {
        $volume = number_format($data['volume'], 4, '.', ',');
    }
    $driver = $data['driver'];
    $keterangan = $data['keterangan'];
} else {
    $judul = "Voucher BBM Realisasi";
    if (fmod($data['volume'], 1) == 0.0) {
        $volume = number_format($data['volume_realisasi'], 0, '.', ',');
    } else {
        $volume = number_format($data['volume_realisasi'], 4, '.', ',');
    }
    $driver = $data['driver_realisasi'];
    $keterangan = $data['keterangan_realisasi'];
}

$printe  = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";

$mpdf = new \Mpdf\Mpdf([
    'format' => 'A4-P',
    'default_font' => 'Arial',
]);

$mpdf->SetDisplayMode('fullpage');
$mpdf->use_kwt = true;
$mpdf->autoPageBreak = true;
$mpdf->setAutoTopMargin = 'stretch';
$mpdf->setAutoBottomMargin = 'stretch';
$mpdf->shrink_tables_to_fit = 1;

$html = '';

$html .= '
	<style>
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
	</style>';

$html .= '
<div class="voucher-box">
    <div class="header-container">
        <table border="0" width="100%">
            <tr>
                <td width="30%">
                    <div class="logo-left">
                        <img src="' . BASE_IMAGE . '/logo-kiri-penawaran.png" alt="Logo Perusahaan" />
                    </div>
                </td>
                <td width="10%"></td>
                <td width="50%">
                    <div class="voucher-title">' . $judul . '</div>
                    <br>
                    <center>
                        <h2>' . $data['nomor'] . '</h2>
                    </center>
                </td>
                <td width="30%">
                    <h3>Tanggal: ' . tgl_indo($data['date_admin']) . '</h3>
                    <br>
                    <h3>Jam: ' . date('H:i', strtotime($data['date_admin'])) . '</h3>
                </td>
            </tr>
        </table>
    </div>

    <br>

    <table class="voucher-info" border="0">
        <tr>
            <td class="label">Nomor Plat</td>
            <td class="sep">:</td>
            <td>' . $unit . '</td>
        </tr>
        <tr>
            <td class="label">Driver</td>
            <td class="sep">:</td>
            <td>' . strtoupper($driver) . '</td>
        </tr>
        <tr>
            <td class="label">Tujuan</td>
            <td class="sep">:</td>
            <td>' . strtoupper($data['tujuan']) . '</td>
        </tr>
        <tr>
            <td class="label">Volume</td>
            <td class="sep">:</td>
            <td>' . $volume . ' Liter</td>
        </tr>
        <tr>
            <td class="label">Lokasi Pengisian</td>
            <td class="sep">:</td>
            <td>' . $data['nama_terminal'] . ' - ' . $data['tanki_terminal'] . '</td>
        </tr>
        <tr>
            <td class="label">Keterangan</td>
            <td class="sep">:</td>
            <td>' . $keterangan . '</td>
        </tr>
    </table>

    <table class="signature-table">
        <tr>
            <td style="position: relative; height: 80px;">
                <span class="title">Disiapkan:</span>
                <br><br><br><br><br>
                <center>
                    <div>' . $data['createdby'] . '</div>
                </center>
            </td>
            <td>
                <span class="title">Menyetujui:</span>
                <br><br><br><br><br>
                <center>
                    <div>' . $data["admin_pic"] . '</div>
                </center>
            </td>
            <td>
                <span class="title">Petugas Pengisian:</span>
                <br><br><br><br><br>
                <center>
                    <div>' . ucfirst($driver) . '</div>
                </center>
                <center>
                    <div></div>
                </center>
            </td>
        </tr>
    </table>
</div>

<div style="margin-top:10px; text-align:right; font-size:7pt;">
    <i>(This form is valid with sign by computerized system)</i>
</div>
<div style="text-align:right; font-size:6pt; margin-bottom:20px;">
    Printed by ' . htmlspecialchars($printe) . '
</div>';


$mpdf->WriteHTML($html);
$filename = "Voucher BBM " . $data['nomor'] . ".pdf";
$mpdf->Output($filename, 'I');
exit;
