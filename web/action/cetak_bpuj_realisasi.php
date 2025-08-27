<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash   = new FlashAlerts;
$enk     = decode($_SERVER['REQUEST_URI']);
$idr     = isset($enk["id_bpuj"]) ? htmlspecialchars($enk["id_bpuj"], ENT_QUOTES) : '';
$sql     = "SELECT a.*, b.nomor_do, c.no_spj FROM pro_bpuj a JOIN pro_po_ds_detail b ON a.id_dsd=b.id_dsd JOIN pro_po_detail c ON b.id_pod=c.id_pod WHERE a.id_bpuj='" . $idr . "'";
$res     = $con->getRecord($sql);
$queryRealisasi = "SELECT * FROM pro_bpuj_realisasi WHERE id_bpuj='" . $idr . "'";
$realisasi = $con->getRecord($queryRealisasi);

$sql_foto = "SELECT * FROM pro_foto_realisasi_bpuj WHERE id_realisasi='" . $realisasi['id'] . "'";
$res_foto = $con->getResult($sql_foto);

$printe  = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
ob_start();
require_once(realpath("./template/cetak_bpuj_realisasi.php"));
$content = ob_get_clean();
ob_end_flush();
$con->close();

$mpdf = null;
$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($content);

$showFoto = "";
if ($res_foto) {
    foreach ($res_foto as $key) {
        $showFoto .= '
            <span>Keterangan: ' . ucwords($key['keterangan']) . '</span>
            <br>
            <img src="' . BASE_URL . '/files/uploaded_user/lampiran_realisasi_bpuj/' . $key['foto'] . '" alt="image" width="450px" height="300px">
            <br><br>
        ';
    }

    // Tambahkan halaman baru
    $mpdf->AddPage();
    // Tambahkan HTML ke PDF
    $mpdf->WriteHTML($showFoto);
}

$filename = "Realisasi BPUJ-" . $res['nomor_do'];
$mpdf->Output($filename . '.pdf', 'I');
exit;
