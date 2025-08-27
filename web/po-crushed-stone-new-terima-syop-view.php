<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "pdfgen");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk      = decode($_SERVER['REQUEST_URI']);
$idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';

$sql = "select a.*, a1.id_po_supplier, b.jenis_produk, b.merk_dagang, d.nama_vendor, d.pic_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal 
from new_pro_inventory_vendor_po_crushed_stone a 
join pro_master_produk b on a.id_produk = b.id_master 
join pro_master_vendor d on a.id_vendor = d.id_master 
join pro_master_terminal e on a.id_terminal = e.id_master 
left join new_pro_inventory_vendor_po_crushed_stone_receive a1 on a.id_master = a1.id_po_supplier 
where a.id_master = '" . $idr . "'";

$data = $con->getRecord($sql);
$printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";
$barcod =   '00' . str_pad($idr, 6, '0', STR_PAD_LEFT);

ob_start();
require_once(realpath("./action/template/po-crushed-stone-supplier-vendor-syop.php"));
$content = ob_get_clean();
ob_end_flush();
$con->close();

// $mpdf = new mPDF('c','A4',10,'arial',10,10,20,10,0,5); 
// $mpdf->SetDisplayMode('fullpage');
// $mpdf->WriteHTML($content);
// $filename = "DO_TRUCK_";
// $mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'D');
// exit;

$mpdf = null;
if (PHP_VERSION >= 5.6) {
    $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
} else
    $mpdf = new mPDF('c', 'A4', 10, 'arial', 10, 10, 20, 10, 0, 5);
$mpdf->AddPage('P');
$mpdf->SetDisplayMode('fullpage');
// $mpdf->SetWatermarkImage(BASE_IMAGE."/watermark-penawaran.png", 0.2, "P", array(0,0));
// $mpdf->showWatermarkImage = true;
$mpdf->showWatermarkImage = true;
$mpdf->WriteHTML($content);
// if ($data['terms_condition'] != '' or $data['terms_condition'] != null) {
//     $mpdf->AddPage('P');
//     $mpdf->WriteHTML('

//      <table border="0" width="100%">
//         <tr>
//             <td width="30%">
//                 <div style="padding:0;"></div>
//             </td>


//             <td>



//             </td>
//             <td width="25%">
//               <div style="padding:0;"><td width="25%" align="right">
//                 <img src="' . BASE_IMAGE . '/logo-text.png" width="20%" />
//             </td>


//             </td>
//         </tr>
//     </table>
//     <br>

//      <table border="0" width="100%">
//         <tr>
//             <td width="30%">
//                 <div style="padding:0;"></div>
//             </td>


//             <td>



//             </td>
//             <td width="35%">
//               <div style="padding:0; ">
//               <td align="right">
//                 <p style="font-size: 10pt; font-weight: bold">PT.Pro Energi </p>

//                 <p>Graha Irama Building 6 G</p>
//                 <p>Jl.HR.Rasuna Said Blox X - 1 Kav.1-2</p>
//                 <p>Jakarta, 12950 Indonesia</p>
//                 <p><strong>Telp</strong> : (021) 5289 2321</p>
//                 <p><strong>Fax</strong>  : (021) 5289 2310</p>
//             </td>


//             </td>
//         </tr>
//     </table>

//   <br>

//     <table width="100%">
//         <tr>
//             <td style="text-align: center; font-size: 10pt; font-weight: bold;">Syarat & Ketentuan Pembelian</td>
//         </tr>
//     </table>
//     <br>
//     <p style="font-size:10pt; text-align:justify; text-indent:20px;">
//     ' . htmlspecialchars_decode($data['terms_condition']) . '
// </p>

//     <br>

//      <table width="100%" border="0">
//         <tr>
//             <!-- Bagian Kiri -->
//             <td width="50%" align="left">
//                 Menyetujui,<br>
//                 <strong>PT. Pro Energi</strong>
//                 <br><br><br>
//                 <barcode code="' . $barcod . '" type="QR" size="1" />
//                 <br><br>
//                 <p><strong><u>Vica Krisdianatha</u></strong></p>
//                 Direktur Utama
//             </td>

//             <!-- Bagian Kanan -->
//             <td width="50%" align="right">

//                 <strong>' . $data['nama_vendor'] . '</strong>
//                 <br><br><br><br>

//                 <br><br><br><br>
//                 <p><strong><u>' . $data['pic_vendor'] . '</u></strong></p>
//                 Direktur
//             </td>
//         </tr>
//     </table>
//            ');
// }

if (!empty($data['terms_condition'])) {
    $mpdf->AddPage('P');

    // 1) Logo di atas
    $mpdf->WriteHTML('
      <table border="0" width="100%">
        <tr>
             <td width="30%">
                 <div style="padding:0;"></div>
            </td>


             <td>



            </td>
             <td width="25%">
               <div style="padding:0;"><td width="25%" align="right">
                <img src="' . BASE_IMAGE . '/logo-text.png" width="20%" />
            </td>


            </td>
        </tr>
     </table>
     <br>

     <table border="0" width="100%">
        <tr>
            <td width="30%">
                <div style="padding:0;"></div>
             </td>


             <td>



            </td>
            <td width="35%">
              <div style="padding:0; ">
              <td align="right">
                <p style="font-size: 10pt; font-weight: bold">PT.Pro Energi </p>

                 <p>Graha Irama Building 6 G</p>
                <p>Jl.HR.Rasuna Said Blox X - 1 Kav.1-2</p>
                <p>Jakarta, 12950 Indonesia</p>
                <p><strong>Telp</strong> : (021) 5289 2321</p>
                 <p><strong>Fax</strong>  : (021) 5289 2310</p>
            </td>


            </td>
        </tr>
   </table>

   <br>
    ');

    // 2) Judul syarat & ketentuan
    $mpdf->WriteHTML('
      <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td style="text-align:center; font-size:10pt; font-weight:bold;">
            Syarat &amp; Ketentuan Pembelian
          </td>
        </tr>
      </table>
      <br>
    ');

    // 3) Pecah field terms_condition menjadi list
    $raw   = htmlspecialchars_decode($data['terms_condition']);
    $lines = preg_split('/<br\s*\/?>|\r\n|\n/', trim($raw), -1, PREG_SPLIT_NO_EMPTY);

    // 4) Bangun HTML ordered list
    $html  = '<ol style="
                font-size:10pt;
                text-align:justify;
                padding-left:20px;
                margin:0;
             ">';
    foreach ($lines as $line) {
        $text = trim($line);
        if ($text === '') continue;
        // Hilangkan awalan angka
        $text = preg_replace('/^\d+\.\s*/', '', $text);
        $html .= '<li style="margin-bottom:0.5em;">' . $text . '</li>';
    }
    $html .= '</ol>';

    // 5) Tulis list ke PDF
    $mpdf->WriteHTML($html);
    $mpdf->WriteHTML('<br>');

    // 6) Signature block
    $mpdf->WriteHTML('
      <table width="100%" border="0">
        <tr>
            <!-- Bagian Kiri -->
             <td width="50%" align="left">
                 Menyetujui,<br>
               <strong>PT. Pro Energi</strong>
                <br><br><br>
                <barcode code="' . $barcod . '" type="QR" size="1" />
                <br><br>
                <p><strong><u>Vica Krisdianatha</u></strong></p>
                Direktur Utama
             </td>

            <!-- Bagian Kanan -->
            <td width="50%" align="right">

                <strong>' . $data['nama_vendor'] . '</strong>
                 <br><br><br><br>

                <br><br><br><br>
                <p><strong><u>' . $data['pic_vendor'] . '</u></strong></p>
                Direktur
             </td>
        </tr>
   </table>
    ');
}

$filename = "PO_SUPP_";
$mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
exit;
