<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk      = decode($_SERVER['REQUEST_URI']);
$act    = ($enk['act'] == "") ? htmlspecialchars($_POST["act"], ENT_QUOTES) : $enk['act'];
$idr     = htmlspecialchars($_POST["idr"], ENT_QUOTES);
$picnya = paramDecrypt($_SESSION["sinori" . SESSIONID]["fullname"]);
$ipnya     = $_SERVER['REMOTE_ADDR'];

$id_jenis      = htmlspecialchars($_POST["id_jenis"], ENT_QUOTES);
$id_produk     = htmlspecialchars($_POST["id_produk"], ENT_QUOTES);
$tgl           = htmlspecialchars($_POST["tgl"], ENT_QUOTES);
$id_terminal   = htmlspecialchars($_POST["transfer_tanki_satu_dari"], ENT_QUOTES);
$keterangan    = htmlspecialchars($_POST["keterangan"], ENT_QUOTES);
$avg_harga     = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["avg_harga"]), ENT_QUOTES);


if ($act == 'add') {
    if ($id_jenis == ""  || $tgl == "") {
        $con->close();
        $flash->add("error", "KOSONG", BASE_REFERER);
    } else {
        $created_time     = date('Y-m-d H:i:s');
        $id_datanya     = md5(uniqid("1089", $id_jenis) . '-' . intval(microtime(true)) . '-' . date('YmdHis'));


        $kuenya = "select LPAD(cast((select nextval(new_pro_inventory_vendor_po_seq)) as varchar(10)), 9, '0') as idnya";
        $arrkue = $con->getRecord($kuenya);
        $id1nya = date("Ym") . $arrkue['idnya'];


        $kuenya     = "select LPAD(cast((select nextval(new_pro_inventory_vendor_po_receive_seq)) as varchar(10)), 9, '0') as idnya";
        $arrkue     = $con->getRecord($kuenya);
        $idnya02     = date("Ymd") . $arrkue['idnya'];


        if ($id_jenis == '8') {
            $oke = true;
            $con->beginTransaction();
            $con->clearError();

            $isian     = false;


            $sql01 = "
                 select coalesce(max(cast(substr(a.nomor_blending_po, 1, 3) as integer)), 0) as nomor, 
                 c.inisial_cabang, 
                 d.inisial_vendor  
                 from pro_blending_po a  
                 join pro_master_terminal b on a.id_terminal = b.id_master 
                 join pro_master_cabang c on b.id_cabang = c.id_master 
                 join pro_master_vendor d on a.id_vendor_blending = d.id_master 
                 where a.id_vendor_blending = 29 
                 and c.id_master = (select id_cabang from pro_master_terminal where id_master = '" . $id_terminal . "')
                 and year(a.tanggal_blending) = '" . substr($tgl, 6, 4) . "'
    ";
            $arrNom = $con->getRecord($sql01);
            $arrRom = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
            $blnThn = $arrRom[intval(substr($tgl, 3, 2))] . '/' . substr($tgl, 8, 2);
            $nomor_po    = str_pad(($arrNom['nomor'] + 1), 3, '0', STR_PAD_LEFT) . '/' . strtoupper($arrNom['inisial_vendor']) . '/' . strtoupper($arrNom['inisial_cabang']) . '/' . $blnThn;



            if (count($_POST["id_po_supplier_tf"]) > 1) {
                foreach ($_POST["id_po_supplier_tf"] as $idx => $val) {
                    if ($idx % 2 == 0) {
                        // Ambil dua PO sekaligus dalam sekali iterasi (dua per dua)
                        $id_po_supplier_1 = htmlspecialchars($_POST["id_po_supplier_tf"][$idx], ENT_QUOTES);
                        $id_po_supplier_2 = isset($_POST["id_po_supplier_tf"][$idx + 1]) ? htmlspecialchars($_POST["id_po_supplier_tf"][$idx + 1], ENT_QUOTES) : null;
                        $id_produk_1 = htmlspecialchars($_POST["id_produk_tf"][$idx], ENT_QUOTES);
                        $id_produk_2 = isset($_POST["id_produk_tf"][$idx + 1]) ? htmlspecialchars($_POST["id_produk_tf"][$idx + 1], ENT_QUOTES) : null;
                        $id_vendor_1 = htmlspecialchars($_POST["id_vendor_tf"][$idx], ENT_QUOTES);
                        $id_vendor_2 = isset($_POST["id_vendor_tf"][$idx + 1]) ? htmlspecialchars($_POST["id_vendor_tf"][$idx + 1], ENT_QUOTES) : null;

                        $id_po_receive_1 = htmlspecialchars($_POST["id_po_receive_tf"][$idx], ENT_QUOTES);
                        $id_po_receive_2 = isset($_POST["id_po_receive_tf"][$idx + 1]) ? htmlspecialchars($_POST["id_po_receive_tf"][$idx + 1], ENT_QUOTES) : null;

                        // Ambil nilai vendor untuk masing-masing PO
                        $tank_satu_vendor_nilai_1 = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["tank_satu_vendor_nilai"][$idx]), ENT_QUOTES);
                        $tank_satu_vendor_nilai_2 = isset($_POST["tank_satu_vendor_nilai"][$idx + 1]) ? htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["tank_satu_vendor_nilai"][$idx + 1]), ENT_QUOTES) : 0;

                        $tank_satu_vendor_nilai_1 = ($tank_satu_vendor_nilai_1 ? $tank_satu_vendor_nilai_1 : 0);
                        $tank_satu_vendor_nilai_2 = ($tank_satu_vendor_nilai_2 ? $tank_satu_vendor_nilai_2 : 0);

                        // Hitung total volume dan harga rata-rata
                        $volume_total = $tank_satu_vendor_nilai_1 + $tank_satu_vendor_nilai_2;


                        // Cek jika kedua PO ada
                        if ($id_po_supplier_1 && $id_po_supplier_2 && $volume_total > 0) {
                            $isian = $isian || true;

                            // Simpan ke database
                            $sql01 = "
                                INSERT INTO pro_blending_po (
                                    nomor_blending_po, id_po_supplier, id_po_receive, tanggal_blending, id_vendor_blending, id_terminal,  volume_total, harga_average, id_po_blending, id_po_blending_1, keterangan, created_time, created_by
                                ) VALUES (
                                '" . $nomor_po . "',  '" .  $id1nya . "',  '" . $idnya02 . "',  '" . tgl_db($tgl) . "', 29, '" . $id_terminal . "', '" . $volume_total . "', '" . $avg_harga . "', 
                                '" . $id_po_supplier_1 . "', '" . $id_po_supplier_2 . "', '" . $keterangan . "', 
                                '" . $created_time . "',  '" . $picnya . "'
                                )
                            ";

                            $con->setQuery($sql01);
                            $oke  = $oke && !$con->hasError();


                            $sql02 = "
                            		insert into new_pro_inventory_depot (
                            			id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, adj_inven, keterangan, 
                            			created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
                            		) values (
                            			'Generate Blending', 3,'" . $id_produk_1 . "', '" . $id_terminal . "', '" . $id_vendor_1 . "', '" . $id_po_supplier_1 . "', '" . $id_po_receive_1 . "', 
                            			'" . tgl_db($tgl) . "', '-" . $tank_satu_vendor_nilai_1 . "', 'Proses Blending', 
                            			'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
                            		)
                            	";
                            $con->setQuery($sql02);
                            $oke  = $oke && !$con->hasError();

                            $sql03 = "
                            insert into new_pro_inventory_depot (
                                id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, adj_inven, keterangan, 
                                created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
                            ) values (
                                'Generate Blending', 3,'" . $id_produk_2 . "', '" . $id_terminal . "', '" . $id_vendor_2 . "', '" . $id_po_supplier_2 . "', '" . $id_po_receive_2 . "', 
                                '" . tgl_db($tgl) . "', '-" . $tank_satu_vendor_nilai_2 . "', 'Proses Blending', 
                                '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
                            )
                                 ";
                            $con->setQuery($sql03);
                            $oke  = $oke && !$con->hasError();



                            $sql05 = "
                            insert into new_pro_inventory_depot (
                                id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, in_inven, keterangan, 
                                created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
                            ) values (
                                'Blending', 21,6, '" . $id_terminal . "', 29, '" .  $id1nya . "', '" .  $idnya02 . "', 
                                '" . tgl_db($tgl) . "', '" . $volume_total . "', 'Blending', 
                                '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
                            )
                                 ";
                            $con->setQuery($sql05);
                            $oke  = $oke && !$con->hasError();
                        }
                    }
                }
            }
            if ($oke) {
                $con->commit();
                $con->close();
                header("location: " . BASE_URL_CLIENT . "/po-blending.php");
                exit();
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $flash->add("error", "GAGAL_MASUK", BASE_REFERER);
            }
        }
    }
}
