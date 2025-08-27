<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$conSub = new Connection();
$id_jenis     = htmlspecialchars($_POST["id_jenis"], ENT_QUOTES);
$id_produk     = $_POST["id_produk"]; // Mengambil array id_produk tanpa htmlspecialchars karena ini array


if (is_array($id_produk)) {
    // Menggunakan implode untuk menggabungkan array menjadi string
    $id_produk_imploded = implode(",", array_map('intval', $id_produk)); // Sanitasi setiap nilai array
} else {
    // Jika hanya satu id_produk dikirim, gunakan langsung
    $id_produk_imploded = intval($id_produk);
}

if ($id_jenis == '1') {
    $sql = "
        SELECT DISTINCT a.id_master, 
            CONCAT(a.nama_terminal, ' ', a.tanki_terminal, ', ', a.lokasi_terminal) AS nama_terminal, 
            b.id_terminal  
        FROM pro_master_terminal a 
        JOIN pro_master_cabang a1 ON a.id_cabang = a1.id_master
        LEFT JOIN new_pro_inventory_depot b ON a.id_master = b.id_terminal 
            AND b.id_jenis = 1 
            AND b.id_produk IN ($id_produk_imploded)  
        WHERE a.is_active = 1 AND b.id_terminal IS NULL 
        ORDER BY a.id_master 
    ";
    $res = $conSub->getResult($sql);

    $conSub->close();
    echo json_encode($res);
} else {
    $sql = "
        SELECT DISTINCT a.id_master, 
            CONCAT(a.nama_terminal, ' ', a.tanki_terminal, ', ', a.lokasi_terminal) AS nama_terminal, 
            b.id_terminal  
        FROM pro_master_terminal a 
        JOIN pro_master_cabang a1 ON a.id_cabang = a1.id_master
        LEFT JOIN new_pro_inventory_depot b ON a.id_master = b.id_terminal 
            AND b.id_jenis = 1 
            AND b.id_produk IN ($id_produk_imploded)  
        WHERE a.is_active = 1 AND b.id_terminal IS NOT NULL 
        ORDER BY a.id_master 
    ";
    $res = $conSub->getResult($sql);

    $conSub->close();
    echo json_encode($res);
}
