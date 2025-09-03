<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();

$q2        = htmlspecialchars($_POST["periode"], ENT_QUOTES);
$q3        = htmlspecialchars($_POST["cabang"], ENT_QUOTES);
$kategori  = htmlspecialchars($_POST["kategori"], ENT_QUOTES);
$seswil = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

if ($kategori == "cek_penerima_refund") {
    $sql_cek = "SELECT * FROM pro_penerima_incentive WHERE cabang = '" . $q3 . "' AND status = '1'";
    $res_cek = $con->getResult($sql_cek);

    echo json_encode($res_cek);
} else {
    $exp = explode("-", $q2);
    $bulan = $exp[1];
    $tahun = $exp[0];

    $p = new paging;
    $sql = "SELECT 
      a.*, 
      a.id_dsd as id_dsdnya, 
      a.id_invoice as id_invoicenya, 
      a.id as id_incentive, 
      a.total_incentive, 
      a.disposisi as statusnya, 
      i.nama_customer, 
      i.kode_pelanggan, 
      i.jenis_payment, 
      i.top_payment, 
      i.id_customer as id_customernya, 
      e.alamat_survey, 
      f.nama_prov, 
      g.nama_kab, 
      j.fullname, 
      j.id_role, 
      h.nomor_poc, 
      h.tanggal_poc, 
      h.id_poc as id_pocnya, 
      h.produk_poc, 
      k.refund_tawar, 
      l.nama_area, 
      l.id_master as id_areanya, 
      k.id_penawaran, 
      k.harga_asli as harga_dasarnya, 
      ppdd.tanggal_delivered, 
      n.no_invoice, 
      n.tgl_invoice_dikirim, 
      n.tgl_invoice, 
      k.masa_awal, 
      k.masa_akhir, 
      CONCAT(o.jenis_produk,' - ', o.merk_dagang) as nama_produk, 
      p.vol_kirim as volume_invoice, 
      n.is_lunas,
      m.tanggal_bayar -- Ambil dari hasil join, bukan subquery lagi
    FROM 
      pro_incentive a
    JOIN 
      pro_po_ds_detail ppdd ON ppdd.id_dsd = a.id_dsd
    JOIN 
      pro_po_customer_plan d ON ppdd.id_plan = d.id_plan 
    JOIN 
      pro_customer_lcr e ON d.id_lcr = e.id_lcr
    JOIN 
      pro_master_provinsi f ON e.prov_survey = f.id_prov 
    JOIN 
      pro_master_kabupaten g ON e.kab_survey = g.id_kab
    JOIN 
      pro_po_customer h ON d.id_poc = h.id_poc 
    JOIN 
      pro_customer i ON h.id_customer = i.id_customer 
    JOIN 
      acl_user j ON i.id_marketing = j.id_user 
    JOIN 
      pro_penawaran k ON h.id_penawaran = k.id_penawaran
    JOIN 
      pro_master_area l ON k.id_area = l.id_master
    JOIN 
      pro_invoice_admin n ON a.id_invoice = n.id_invoice
    JOIN 
      pro_master_produk o ON o.id_master = h.produk_poc
    JOIN 
      pro_invoice_admin_detail p ON a.id_invoice = p.id_invoice
    LEFT JOIN 
      (SELECT id_invoice, MAX(tgl_bayar) AS tanggal_bayar FROM pro_invoice_admin_detail_bayar GROUP BY id_invoice) m 
    ON 
      m.id_invoice = a.id_invoice
    WHERE 
      a.disposisi = '1'
      AND MONTH(m.tanggal_bayar) = '" . $bulan . "'
      AND YEAR(m.tanggal_bayar) = '" . $tahun . "'
      AND i.id_wilayah = '" . $q3 . "'
    GROUP BY a.id
    ORDER BY 
      a.id DESC";

    $result = $con->getResult($sql);

    $content = (count($result) > 0) ? $result : array();
    $json_data = array("items" => $result);
    echo json_encode($json_data);
}
