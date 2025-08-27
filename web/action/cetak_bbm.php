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
$id_dsd    = htmlspecialchars($enk['id_dsd'], ENT_QUOTES);



$sql     = "SELECT a.*, 
            c.nama_sopir,
            d.nomor_plat,
            b.no_spj,
            h.out_inven_virtual,
            h.out_inven,
            f.nama_terminal,
            g.tanggal_kirim

            FROM pro_po_ds_detail a 
            JOIN pro_po_detail b ON a.id_pod = b.id_pod
            JOIN pro_master_transportir_sopir c ON b.sopir_po = c.id_master
            JOIN pro_master_transportir_mobil d on b.mobil_po = d.id_master 
            JOIN pro_bpuj e ON a.id_dsd = e.id_dsd
           
            JOIN pro_po_customer_plan g on a.id_plan = g.id_plan 
            JOIN new_pro_inventory_depot h on a.id_dsd = h.id_dsd 
            JOIN pro_master_terminal f on h.id_terminal = f.id_master 
            WHERE a.id_dsd ='" . $id_dsd . "' and e.disposisi_bpuj = 2";
$res     = $con->getResult($sql);

$rows = method_exists($con, 'getRecord') ? $con->getRecord($sql) : $con->getResult($sql);
if (!$rows) {
    $con->close();
    die('Data tidak ditemukan.');
}
if (isset($rows['no_spj'])) {
    $rows = [$rows];
} // kalau 1 baris, bungkus jadi array

$first   = $rows[0];
$printe  = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
$created = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);

$sumVirtual = 0;
$sumReal = 0;
foreach ($res as $r) {
    $sumVirtual += (float)($r['out_inven_virtual'] ?? 0);
    $sumReal    += (float)($r['out_inven'] ?? 0);
}
$total = $sumVirtual + $sumReal;

ob_start();
require_once(realpath("./template/cetak_bbm.php"));
$content = ob_get_clean();
ob_end_flush();
$con->close();

$mpdf = null;
$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($content);
$filename = "BBM -" . $res['no_spj'];
$mpdf->Output($filename . '.pdf', 'I');
exit;
