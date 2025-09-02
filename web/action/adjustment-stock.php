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

$id_jenis            = htmlspecialchars($_POST["id_jenis"], ENT_QUOTES);
$id_produk            = htmlspecialchars($_POST["id_produk"], ENT_QUOTES);
$tgl                = htmlspecialchars($_POST["tgl"], ENT_QUOTES);
$id_terminal        = htmlspecialchars($_POST["id_terminal"], ENT_QUOTES);
$adj_inven_sign        = htmlspecialchars($_POST["adj_inven_sign"], ENT_QUOTES);
$adj_inven            = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["adj_inven"]), ENT_QUOTES);
$adj_inven             = ($adj_inven ? $adj_inven : 0);

//Tambahan fields untuk accurate
$kode_vendor         = htmlspecialchars($_POST["kode_vendor"], ENT_QUOTES);
$kode_ri            = htmlspecialchars($_POST["receive_number"], ENT_QUOTES);
$array_id           = explode(',', $kode_ri);
$receive_number     = $array_id[1];
$kode_item            = htmlspecialchars($_POST["kode_item"], ENT_QUOTES);
$kode_item_terima     = htmlspecialchars($_POST["kode_item_terima"], ENT_QUOTES);
$akun_penyesuaian      = htmlspecialchars($_POST["akun_penyesuaian"], ENT_QUOTES);
$id_terminal_return        = htmlspecialchars($_POST["id_terminal_return"], ENT_QUOTES);
$harga_liter            = htmlspecialchars(str_replace(array(","), array(""), $_POST["harga_liter"]), ENT_QUOTES);
$harga_liter             = ($harga_liter ? $harga_liter : 0);
$adj_inven_return            = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["adj_inven_return"]), ENT_QUOTES);
$adj_inven_return             = ($adj_inven_return ? $adj_inven_return : 0);
$harga_liter_return            = htmlspecialchars(str_replace(array(","), array(""), $_POST["harga_liter_return"]), ENT_QUOTES);
$harga_liter_return             = ($harga_liter_return ? $harga_liter_return : 0);
$harga_transfer            = htmlspecialchars(str_replace(array(","), array(""), $_POST["harga_transfer"]), ENT_QUOTES);
$harga_transfer             = ($harga_transfer ? $harga_transfer : 0);
$tgl_penerimaan        = htmlspecialchars($_POST["tgl_penerimaan"], ENT_QUOTES);
$transfer_tanki_satu_dari    = htmlspecialchars($_POST["transfer_tanki_satu_dari"], ENT_QUOTES);
$transfer_tanki_satu_ke        = htmlspecialchars($_POST["transfer_tanki_satu_ke"], ENT_QUOTES);
$tank_satu_total            = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["tank_satu_total"]), ENT_QUOTES);
$id_cabang = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$queryget_cabang = "SELECT * FROM pro_master_cabang WHERE id_master = '" . $id_cabang . "'";
$rowget_cabang = $con->getRecord($queryget_cabang);

$keterangan    = htmlspecialchars($_POST["keterangan"], ENT_QUOTES);
//id_master, id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, awal_inven, in_inven, out_inven, adj_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
if ($act == 'add') {
    if ($id_jenis == "" || $id_produk == "" || $tgl == "") {
        $con->close();
        $flash->add("error", "KOSONG", BASE_REFERER);
    } else {
        $created_time     = date('Y-m-d H:i:s');
        $id_datanya     = md5(uniqid("1089", $id_jenis) . '-' . intval(microtime(true)) . '-' . date('YmdHis'));

        if ($id_jenis == '3') {

            $id_po_supplier = htmlspecialchars($_POST["id_po_supplier_sales"], ENT_QUOTES);
            $id_po_receive     = htmlspecialchars($_POST["id_po_receive_sales"], ENT_QUOTES);

            $oke = true;
            $con->beginTransaction();
            $con->clearError();

            if ($adj_inven > 0) {
                $sql01 = "
						insert into new_pro_inventory_depot (
							id_datanya, id_jenis, id_produk, id_terminal, id_po_supplier, id_po_receive, tanggal_inven, adj_inven, keterangan, 
							created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
						) values (
							'" . $id_datanya . "', '" . $id_jenis . "', '" . $id_produk . "', '" . $id_terminal . "',  '" . $id_po_supplier . "', '" . $id_po_receive . "',  '" . tgl_db($tgl) . "', 
							'" . ($adj_inven_sign == '-' ? $adj_inven_sign : '') . $adj_inven . "', '" . $keterangan . "', 
							'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
						)
					";
                $lastId = $con->setQuery($sql01);
                $oke  = $oke && !$con->hasError();
            } else {
                $sql01 = "update new_pro_inventory_depot set keterangan = '' where id_master = '0'";
                $con->setQuery($sql01);
                $oke  = $oke && !$con->hasError();
            }
        }else if ($id_jenis == '4') {
            $oke = true;
            $con->beginTransaction();
            $con->clearError();

            $isian     = false;
            if (count($_POST["id_po_supplier_tf"]) > 0) {
                foreach ($_POST["id_po_supplier_tf"] as $idx => $val) {
                    $id_po_supplier            = htmlspecialchars($_POST["id_po_supplier_tf"][$idx], ENT_QUOTES);
                    $id_po_receive            = htmlspecialchars($_POST["id_po_receive_tf"][$idx], ENT_QUOTES);
                    $tank_satu_vendor_nilai = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["tank_satu_vendor_nilai"][$idx]), ENT_QUOTES);
                    $tank_satu_vendor_nilai = ($tank_satu_vendor_nilai ? $tank_satu_vendor_nilai : 0);

                    if ($id_po_supplier && $id_po_receive && $tank_satu_vendor_nilai > 0) {
                        $isian = $isian || true;
                        $sql01 = "
								insert into new_pro_inventory_depot (
									id_datanya, id_jenis, id_produk, id_terminal, id_po_supplier, id_po_receive, tanggal_inven, adj_inven, keterangan, 
									created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
								) values (
									'" . $id_datanya . "', '" . $id_jenis . "', '" . $id_produk . "', '" . $transfer_tanki_satu_dari . "', '" . $id_po_supplier . "', '" . $id_po_receive . "', 
									'" . tgl_db($tgl) . "', '-" . $tank_satu_vendor_nilai . "', '" . $keterangan . "', 
									'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
								)
							";
                        $con->setQuery($sql01);
                        $oke  = $oke && !$con->hasError();

                        $sql02 = "
								insert into new_pro_inventory_depot (
									id_datanya, id_jenis, id_produk, id_terminal, id_po_supplier, id_po_receive, tanggal_inven, adj_inven, harga, keterangan, 
									created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
								) values (
									'" . $id_datanya . "', '" . $id_jenis . "', '" . $id_produk . "', '" . $transfer_tanki_satu_ke . "', '" . $id_po_supplier . "', '" . $id_po_receive . "', 
									'" . tgl_db($tgl_penerimaan) . "', '" . $tank_satu_vendor_nilai . "', '" . $harga_transfer . "', '" . $keterangan . "', 
									'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
								)
							";
                        $con->setQuery($sql02);
                        $oke  = $oke && !$con->hasError();
                    }
                }
            }
            if (!$isian) {
                $sql01 = "update new_pro_inventory_depot set keterangan = '' where id_master = '0'";
                $con->setQuery($sql01);
                $oke  = $oke && !$con->hasError();
            }
        } else if ($id_jenis == '5') {
            //id jenis 5 = purchase return
            $id_po_supplier = htmlspecialchars($_POST["id_po_supplier_return"], ENT_QUOTES);
            $id_po_receive     = htmlspecialchars($_POST["id_po_receive_return"], ENT_QUOTES);
            $id_terminal = $id_terminal_return;

            $oke = true;
            $con->beginTransaction();
            $con->clearError();

            if ($adj_inven_return > 0) {
                $sql01 = "
						insert into new_pro_inventory_depot (
							id_datanya, id_jenis, id_produk, id_terminal, id_po_supplier, id_po_receive, tanggal_inven, adj_inven, harga, keterangan, 
							created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
						) values (
							'" . $id_datanya . "', '" . $id_jenis . "', '" . $id_produk . "', '" . $id_terminal_return . "',  '" . $id_po_supplier . "', '" . $id_po_receive . "',  '" . tgl_db($tgl) . "', 
							'-" . $adj_inven_return . "', '" . $harga_liter_return . "', '" . $keterangan . "', 
							'" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', 'tes', '" . $created_time . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $picnya . "'
						)
					";

                $lastId = $con->setQuery($sql01);
                $oke  = $oke && !$con->hasError();
            } else {
                $sql01 = "update new_pro_inventory_depot set keterangan = '' where id_master = '0'";
                $con->setQuery($sql01);
                $oke  = $oke && !$con->hasError();
            }
        }
    }

    if ($oke) {
        $get_gudang = "SELECT b.* FROM pro_master_terminal a JOIN pro_master_cabang b ON a.id_cabang=b.id_master
        WHERE a.id_master = $id_terminal";
        $nama_gudang = $con->getRecord($get_gudang);
        //get cabang 
        $id_cabang = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

        $queryget_cabang = "SELECT * FROM pro_master_cabang WHERE id_master = '" . $id_cabang . "'";
        $rowget_cabang = $con->getRecord($queryget_cabang);

        $urlnya = 'https://zeus.accurate.id/accurate/api/item-adjustment/save.do';

        // $con->commit();
        // $con->close();
        // header("location: " . BASE_URL_CLIENT . "/adjustment-stock.php");
        // exit();

        //kondisi untuk ke accurate
        if ($id_jenis == '3') {
            // Data yang akan dikirim dalam format JSON
            if ($adj_inven_sign == "+") {
                $detailItem = array(
                    'itemAdjustmentType' => "ADJUSTMENT_IN",
                    'itemNo'             => $kode_item,
                    'quantity'           => $adj_inven,
                    'unitCost'           => $harga_liter,
                    'warehouseName'      => $nama_gudang['inisial_cabang']
                );
            } else {
                $detailItem = array(
                    'itemAdjustmentType' => "ADJUSTMENT_OUT",
                    'itemNo'             => $kode_item,
                    'quantity'           => $adj_inven,
                    'warehouseName'      => $nama_gudang['inisial_cabang']
                );
            }

            $data = array(
                'adjustmentAccountNo'   => $akun_penyesuaian,
                'transDate'             => $tgl,
                'description'           => $keterangan,
                'detailItem'            => array($detailItem),
                'branchName'  => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
            );

            // Mengonversi data menjadi format JSON
            $jsonData = json_encode($data);

            $result = curl_post($urlnya, $jsonData);

            if ($result['s'] == true) {
                var_dump($result['s']);
                $update = "UPDATE new_pro_inventory_depot set id_accurate = '" . $result['r']['id'] . "' WHERE id_master = '".$lastId."'" ;
                $con->setQuery($update);

                $con->commit();
                $con->close();
                header("location: " . BASE_URL_CLIENT . "/adjustment-stock.php");
                exit();
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $flash->add("error", $result["d"][0] . " - Response dari Accurate", BASE_REFERER);
            }
        } else if ($id_jenis == '4') {
            $get_gudang = "SELECT b.* FROM pro_master_terminal a JOIN pro_master_cabang b ON a.id_cabang=b.id_master
            WHERE a.id_master = $transfer_tanki_satu_dari";
            $nama_gudang = $con->getRecord($get_gudang);

            // Data yang akan dikirim dalam format JSON
            $data = array(
                'adjustmentAccountNo'   => $akun_penyesuaian,
                'transDate'             => $tgl,
                'description'           => $keterangan,
                'branchName'  => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
                'detailItem'            => array([
                    'itemAdjustmentType' => "ADJUSTMENT_OUT",
                    'itemNo'             => $kode_item,
                    'quantity'           => $tank_satu_total,
                    'warehouseName'      => $nama_gudang['inisial_cabang']
                ]),
            );
            // Mengonversi data menjadi format JSON
            $jsonData = json_encode($data);

            $result = curl_post($urlnya, $jsonData);
            if ($result['s'] == true) {
                $update = "UPDATE new_pro_inventory_depot SET id_accurate = '" . $result['r']['id'] . "' WHERE id_datanya ='" . $id_datanya . "' AND adj_inven < 0 ";
                $con->setQuery($update);

                $get_gudang2 = "SELECT b.* FROM pro_master_terminal a JOIN pro_master_cabang b ON a.id_cabang=b.id_master
                WHERE a.id_master = $transfer_tanki_satu_ke";
                $nama_gudang2 = $con->getRecord($get_gudang2);

                $data2 = array(
                    'adjustmentAccountNo'   => $akun_penyesuaian,
                    'transDate'             => $tgl_penerimaan,
                    'description'           => $keterangan,
                    'branchName'  => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
                    'detailItem'            => array([
                        'itemAdjustmentType' => "ADJUSTMENT_IN",
                        'itemNo'             => $kode_item_terima,
                        'quantity'           => $tank_satu_total,
                        'unitCost'           => $harga_transfer,
                        'warehouseName'      => $nama_gudang2['inisial_cabang']
                    ]),
                );
                // Mengonversi data menjadi format JSON
                $jsonData2 = json_encode($data2);

                $result2 = curl_post($urlnya, $jsonData2);

                if ($result2['s'] == true) {
                    $update = "UPDATE new_pro_inventory_depot SET id_accurate = '" . $result2['r']['id'] . "' WHERE id_datanya ='" . $id_datanya . "' AND adj_inven > 0 ";
                    $con->setQuery($update);

                    $con->commit();
                    $con->close();
                    header("location: " . BASE_URL_CLIENT . "/adjustment-stock.php");
                    exit();
                } else {
                    $con->rollBack();
                    $con->clearError();
                    $con->close();
                    $flash->add("error", $result2["d"][0] . " - Response dari Accurate", BASE_REFERER);
                }
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $flash->add("error", $result["d"][0] . " - Response dari Accurate", BASE_REFERER);
            }
        } else if ($id_jenis == '5') {

            $query_item = http_build_query([
                'number' => "$receive_number"
            ]);

            $urlnya = 'https://zeus.accurate.id/accurate/api/receive-item/detail.do?' . $query_item;

            $result_item = curl_get($urlnya);

            if ($result_item['s'] == true) {
                $url_retur = 'https://zeus.accurate.id/accurate/api/purchase-return/save.do';
                $data = array(
                    'receiveItemNumber' => $receive_number,
                    'returnType'        => 'RECEIVE',
                    'transDate'         => $tgl,
                    'description'       => $keterangan,
                    'vendorNo'          => $kode_vendor,
                    'branchName'        => ($rowget_cabang['nama_cabang'] == 'HO' ? 'Head Office' : $rowget_cabang['nama_cabang']),
                    'detailItem'        => array([
                        'itemNo'       => strval($result_item['d']['detailItem'][0]['item']['no']),
                        'quantity'     => $adj_inven_return
                    ]),
                );


                // Mengonversi data menjadi format JSON
                $jsonData = json_encode($data);

                $result = curl_post($url_retur, $jsonData);

                if ($result['s'] == true) {
                    $cek = true;
                    $id_pr_accurate = $result['r']['id'];
                    $update = "UPDATE new_pro_inventory_depot set id_accurate = '" . $result['r']['id'] . "' WHERE id_master = '" . $lastId . "'";
                    $con->setQuery($update);
                    $cek = $cek && !$con->hasError();

                    if ($cek) {
                        $con->commit();
                        $con->close();
                        header("location: " . BASE_URL_CLIENT . "/adjustment-stock.php");
                        exit();
                    } else {
                        $url_delete_ri = 'https://zeus.accurate.id/accurate/api/purchase-return/delete.do';
                        $data_ri = array(
                            'id' => $id_pr_accurate,
                        );
                        $jsonData_ri = json_encode($data_ri);
                        $result_ri = curl_post($url_delete_ri, $jsonData_ri);

                        if ($result_ri['s'] == true) {
                            $con->rollBack();
                            $con->clearError();
                            $con->close();
                            $flash->add("error", "GAGAL_MASUK", BASE_REFERER);
                        } else {
                            $con->rollBack();
                            $con->clearError();
                            $con->close();
                            $flash->add("error", $result_ri["d"][0] . " - Response dari Accurate", BASE_REFERER);
                        }
                    }
                } else {
                    $con->rollBack();
                    $con->clearError();
                    $con->close();
                    $flash->add("error", $result["d"][0] . " - Response dari Accurate", BASE_REFERER);
                }
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $flash->add("error", $result_item["d"][0] . " - Response dari Accurate", BASE_REFERER);
            }
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $flash->add("error", "GAGAL_MASUK", BASE_REFERER);
        }
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        $flash->add("error", "GAGAL_MASUK", BASE_REFERER);
    }
} else if ($act == 'hapus') {
    $param     = htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
    $post     = explode("#|#", $param);
    $file    = isset($post[0]) ? htmlspecialchars($post[0], ENT_QUOTES) : null;
    $id1    = isset($post[1]) ? htmlspecialchars($post[1], ENT_QUOTES) : null;
    $id2    = isset($post[2]) ? htmlspecialchars($post[2], ENT_QUOTES) : null;
    $id3    = isset($post[3]) ? htmlspecialchars($post[3], ENT_QUOTES) : null;
    $id4    = isset($post[4]) ? htmlspecialchars($post[4], ENT_QUOTES) : null;
    if ($id1 == "Data Awal") {
        $created_time = date('Y-m-d H:i:s');

        $cek01 = "
				select a.id_master, a.tanggal_inven, a.id_po_supplier, a.id_po_receive 
				from new_pro_inventory_depot a 
				where a.id_produk = '" . $id3 . "' and a.id_terminal = '" . $id4 . "' and a.id_jenis = 1 
			";
        $res01 = $con->getResult($cek01);
        if (count($res01) > 0) {
            $oke = true;
            $con->beginTransaction();
            $con->clearError();

            foreach ($res01 as $data01) {
                if ($data01['id_po_supplier'] && $data01['id_po_receive']) {
                    $sql01 = "
							update new_pro_inventory_depot a  
							join new_pro_inventory_vendor_po_receive b on a.id_po_supplier = b.id_po_supplier and a.id_po_receive = b.id_po_receive 
							set a.id_jenis = 21, a.tanggal_inven = b.tgl_terima, 
							a.lastupdate_time = '" . $created_time . "', a.lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', a.lastupdate_by = '" . $picnya . "'  
							where a.id_master = '" . $data01['id_master'] . "' 
						";
                    $con->setQuery($sql01);
                    $oke  = $oke && !$con->hasError();
                } else {
                    $sql01 = "delete from new_pro_inventory_depot where id_master = '" . $data01['id_master'] . "'";
                    $con->setQuery($sql01);
                    $oke  = $oke && !$con->hasError();
                }
            }

            $sql02 = "
					update new_pro_inventory_vendor_po a  
					join new_pro_inventory_vendor_po_receive a1 on a.id_master = a1.id_po_supplier    
					set a1.is_aktif = 1 
					where a.id_produk = '" . $id3 . "' and a.id_terminal = '" . $id4 . "' and a1.tgl_terima < '" . $res01[0]['tanggal_inven'] . "' 
				";
            $con->setQuery($sql02);
            $oke  = $oke && !$con->hasError();

            if ($oke) {
                $con->commit();
                $con->close();
                $arr["error"] = "";
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $arr["error"] = "Maaf Data tidak dapat dihapus..";
            }
        }
    }  else if ($id1 == "Purchase Return") {
        $sql02 = "SELECT id_accurate FROM new_pro_inventory_depot WHERE id_datanya = '" . $id2 . "'";
        $res02 = $con->getRecord($sql02);

        $oke = true;
        $con->beginTransaction();
        $con->clearError();

        $urlnya = 'https://zeus.accurate.id/accurate/api/purchase-return/delete.do';

        if ($res02['id_accurate'] != null) {
            // Data yang akan dikirim dalam format JSON
            $data = array(
                'id' => $res02['id_accurate'],
            );

            // Mengonversi data menjadi format JSON
            $jsonData = json_encode($data);
            $result = curl_post($urlnya, $jsonData);

            if ($result['s'] == true) {
                $sql01 = "delete from new_pro_inventory_depot where id_datanya = '" . $id2 . "' and id_accurate = '" . $res02['id_accurate'] . "'";
                $con->setQuery($sql01);

                $con->commit();
                $con->close();
                $arr["error"] = "";
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $arr["error"] = $result["d"][0] . " - Response dari Accurate";
            }
        } else {
            $sql01 = "delete from new_pro_inventory_depot where id_datanya = '" . $id2 . "'";
            $con->setQuery($sql01);
            $oke  = $oke && !$con->hasError();

            if ($oke) {
                $con->commit();
                $con->close();
                $arr["error"] = "";
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $arr["error"] = "Maaf Data tidak dapat dihapus..";
            }
        }
    } else {
        //tambahan untuk ke accurate
        $oke = true;
        $con->beginTransaction();
        $con->clearError();
        $sql02 = "SELECT DISTINCT id_accurate FROM new_pro_inventory_depot WHERE id_datanya = '" . $id2 . "'";
        $res02 = $con->getResult($sql02);

        
        $urlnya = 'https://zeus.accurate.id/accurate/api/item-adjustment/delete.do';

        foreach ($res02 as $res) {
         
            if ($res['id_accurate'] != null) {
                // Data yang akan dikirim dalam format JSON
                $data = array(
                    'id' => $res['id_accurate'],
                );

                // Mengonversi data menjadi format JSON
                $jsonData = json_encode($data);
                $result = curl_post($urlnya, $jsonData);

                if ($result['s'] == true) {
                    $sql01 = "delete from new_pro_inventory_depot where id_datanya = '" . $id2 . "' and id_accurate = '" . $res['id_accurate'] . "'";
                    $con->setQuery($sql01);
                    $oke  = $oke && !$con->hasError();
                    $error = '';
                } else {
                    $oke = false;
                    // $con->clearError();
                    // $con->close();
                    $error = $result["d"][0] . " - Response dari Accurate";
                }
            } else {
                $sql01 = "delete from new_pro_inventory_depot where id_datanya = '" . $id2 . "'";
                $con->setQuery($sql01);
                $oke  = $oke && !$con->hasError();
                $error = '';
            }
        }

        if ($oke) {
            $con->commit();
            $con->close();
            $arr["error"] = "";
        } else {
            $con->rollBack();
            $con->clearError();
            $con->close();
            $arr["error"] = $error ? $error : "Maaf Data tidak dapat dihapus..";
        }
        // $sql01 = "delete from new_pro_inventory_depot where id_datanya = '" . $id2 . "'";
        // $con->setQuery($sql01);
        // if (!$con->hasError()) {
        //     $con->close();
        //     $arr["error"] = "";
        // } else {
        //     $con->clearError();
        //     $con->close();
        //     $arr["error"] = "Maaf Data tidak dapat dihapus..";
        // }
    }

    echo json_encode($arr);
    exit;
}
