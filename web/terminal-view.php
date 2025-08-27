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
$idr = htmlspecialchars($enk["idr"], ENT_QUOTES);
$file = htmlspecialchars($enk["type"], ENT_QUOTES);
$sbmt1  = isset($enk["code"]) ? htmlspecialchars($enk["code"], ENT_QUOTES) : null;

if ($file == "do_truck") {
    $sql = "select a.*, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, o.nama_terminal, o.tanki_terminal, o.lokasi_terminal, b.no_spj, k.nomor_plat, 
                l.nama_sopir, b.volume_po, j.jenis_produk, j.merk_dagang, n.nama_transportir, n.nama_suplier, p.created_by, q.kode_barcode, p.is_loco,o.initial  
                from pro_po_ds_detail a join pro_po_detail b on a.id_pod = b.id_pod 
                join pro_pr_detail c on a.id_prd = c.id_prd 
                join pro_po_customer_plan d on a.id_plan = d.id_plan 
                join pro_customer_lcr e on d.id_lcr = e.id_lcr
                join pro_master_provinsi f on e.prov_survey = f.id_prov 
                join pro_master_kabupaten g on e.kab_survey = g.id_kab
                join pro_po_customer h on d.id_poc = h.id_poc 
                join pro_customer i on h.id_customer = i.id_customer 
                join pro_master_produk j on h.produk_poc = j.id_master 
                join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
                join pro_master_transportir_sopir l on b.sopir_po = l.id_master
                join pro_po m on a.id_po = m.id_po 
                join pro_master_transportir n on m.id_transportir = n.id_master 
                join pro_master_terminal o on b.terminal_po = o.id_master 
                join pro_po_ds p on a.id_ds = p.id_ds 
                join pro_master_cabang q on p.id_wilayah = q.id_master 
                where a.id_dsd in (" . $idr . ") order by field(a.id_dsd, " . $idr . ")";

    $res = $con->getResult($sql);
    $printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";

    ob_start();
    require_once(realpath("./action/template/delivery-order-truck.php"));
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
    $mpdf->WriteHTML($content);
    $filename = "DO_TRUCK_";
    $mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
    exit;
} else if ($file == "do_kapal") {

    $sql = "select a.*, b.inisial_segel, c.nama_terminal, d.nama_suplier, e.volume, b.kode_barcode, g.nama_customer
                from pro_po_ds_kapal a
                join pro_master_cabang b on a.id_wilayah = b.id_master 
                join pro_master_terminal c on a.terminal = c.id_master 
                join pro_master_transportir d on a.transportir = d.id_master 
                join pro_pr_detail e on a.id_prd = e.id_prd
                join pro_po_customer f on a.id_poc = f.id_poc
                join pro_customer g on f.id_customer = g.id_customer
                where a.id_dsk in (" . $idr . ") order by field(a.id_dsk, " . $idr . ")";
    $res = $con->getResult($sql);
    $printe = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]) . " " . date("d/m/Y H:i:s") . " WIB";

    ob_start();
    require_once(realpath("./action/template/delivery-order-kapal.php"));
    $content = ob_get_clean();
    ob_end_flush();
    $con->close();

    // $mpdf = new mPDF('c','A4',9,'arial',10,10,10,10,0,5); 
    // $mpdf->SetDisplayMode('fullpage');
    // $mpdf->WriteHTML($content);
    // $filename = "DN_KAPAL_";
    // $mpdf->Output($filename.'_'.date('dmyHis').'.pdf', 'D');
    // exit;

    $mpdf = null;
    if (PHP_VERSION >= 5.6) {
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
    } else
        $mpdf = new mPDF('c', 'A4', 9, 'arial', 10, 10, 10, 10, 0, 5);
    $mpdf->AddPage('P');
    $mpdf->SetDisplayMode('fullpage');
    // $mpdf->SetWatermarkImage(BASE_IMAGE."/watermark-penawaran.png", 0.2, "P", array(0,0));
    $mpdf->showWatermarkImage = true;
    $mpdf->WriteHTML($content);
    $filename = "DN_KAPAL_";
    $mpdf->Output($filename . '_' . date('dmyHis') . '.pdf', 'I');
    exit;
}
