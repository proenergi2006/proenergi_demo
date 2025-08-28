<?php
$fileSizeLimit = 5 * 1024 * 1024; // 5 MB dalam byte
ini_set('upload_max_filesize', $fileSizeLimit);
ini_set('post_max_size', $fileSizeLimit);
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed", "mailgen");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk      = decode($_SERVER['REQUEST_URI']);
$act    = ($enk['act'] ? $enk['act'] : htmlspecialchars($_POST["act"], ENT_QUOTES));
$idr    = isset($_POST["idnya01"]) ? $_POST["idnya01"] : null;

$dt1    = htmlspecialchars($_POST["dt1"], ENT_QUOTES);
$dt2    = htmlspecialchars($_POST["dt2"], ENT_QUOTES);
$dt3    = htmlspecialchars($_POST["dt3"], ENT_QUOTES);
$dt4    = htmlspecialchars($_POST["dt4"], ENT_QUOTES);
$dt5    = htmlspecialchars($_POST["dt5"], ENT_QUOTES);
$dt6    = htmlspecialchars($_POST["dt6"], ENT_QUOTES);
$dt7    = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["dt7"]), ENT_QUOTES);
$dt8    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt8"]), ENT_QUOTES);
$dt16    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt16"]), ENT_QUOTES);
$dt22   = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt22"]), ENT_QUOTES);

$subTotal    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt9"]), ENT_QUOTES);
$dt10    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt10"]), ENT_QUOTES);
$dt15    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt15"]), ENT_QUOTES);
$dt19    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt19"]), ENT_QUOTES);
$ppn_11    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt11"]), ENT_QUOTES);
$pph_22    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt12"]), ENT_QUOTES);
$pbbkb    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt13"]), ENT_QUOTES);
$totalOrder    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt14"]), ENT_QUOTES);

$kd_tax        = htmlspecialchars($_POST["kd_tax"], ENT_QUOTES);

$ket        = htmlspecialchars($_POST["ket"], ENT_QUOTES);

$volume_po_loss_gain    = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["volume_po_loss_gain"]), ENT_QUOTES);
$volume_terima_loss_gain   = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["volume_terima_loss_gain"]), ENT_QUOTES);
$jenis    = htmlspecialchars($_POST["jenis"], ENT_QUOTES);
$ket_loss_gain    = htmlspecialchars($_POST["ket_loss_gain"], ENT_QUOTES);
$volume_loss_gain   = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["volume_loss_gain"]), ENT_QUOTES);
$volume_bl_loss_gain   = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["volume_bl_loss_gain"]), ENT_QUOTES);



$filePhoto1     = htmlspecialchars($_FILES['file_template']['name'], ENT_QUOTES);
$sizePhoto1     = htmlspecialchars($_FILES['file_template']['size'], ENT_QUOTES);
$tempPhoto1     = htmlspecialchars($_FILES['file_template']['tmp_name'], ENT_QUOTES);
$tipePhoto1     = htmlspecialchars($_FILES['file_template']['type'], ENT_QUOTES);

$folder         = date("Ym");
$pathnya         = $public_base_directory . '/files/uploaded_user/lampiran';

//echo $act; exit;
if ($act == 'update') {
    if ($dt1 == "" || $dt8 == "" || $dt10 == "") {
        $con->close();
        $flash->add("error", "KOSONG", BASE_REFERER);
    } else {
        $id1nya = $idr;

        if ($id1nya) {

            if ($filePhoto1) {
                $fileExt         = strtolower(pathinfo($filePhoto1, PATHINFO_EXTENSION));
                $fileName         = $folder . '/BOL_' . $idnya01 . '_' . md5(basename($filePhoto1, $fileExt)) . '.' . $fileExt;
                $fileOriginName = sanitize_filename($filePhoto1);
                $isUpload         = true;
            } else {
                $fileName         = $arrget[$idx]['filenya'];
                $fileOriginName = $arrget[$idx]['file_upload_ori'];
                $isUpload         = false;
            }

            $oke = true;
            $con->beginTransaction();
            $con->clearError();

            $msg = "GAGAL_UBAH";

            if ($jenis == 1) {
                $sql = "
					update new_pro_inventory_vendor_po set harga_tebus = '" . $dt16 . "', harga_po = '" . $dt8 . "',  volume_ri = '" . $dt15 . "', disposisi_po = 1, cfo_result= 0, cfo_pic= '',  cfo_summary = '', ceo_result = 0, ceo_pic = '', ceo_summary = '', volume_po = '" . $dt10 . "', subtotal = '" . $subTotal . "',  ppn_11 = '" . $ppn_11 . "', pph_22 = '" . $pph_22 . "', pbbkb = '" . $pbbkb . "', pbbkb_po = '" . $dt22 . "', total_order = '" . $totalOrder . "',
					 keterangan =  '" . $ket . "',
					lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' 
					where id_master = '" . $idr . "'
				";
                $con->setQuery($sql);
                $oke  = $oke && !$con->hasError();
            }






            $sql2 =
                "
                        insert into new_pro_inventory_gain_loss (id_po_supplier, volume_po,  volume_terima, jenis, volume, file_upload, file_upload_ori, ket, disposisi_gain_loss, created_time, created_ip, 
                         created_by) (
                            select '" . $idr . "', '" . $volume_po_loss_gain  . "', '" . $volume_terima_loss_gain  . "', '" . $jenis  . "', '" . $volume_loss_gain  . "', '" . $fileName . "', '" . $fileOriginName . "', '" . $ket_loss_gain . "', 1,NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' 
                        )";
            $con->setQuery($sql2);
            $oke  = $oke && !$con->hasError();

            if ($oke) {
                //Save ke Accurate
                $queryget = "SELECT c.nama_terminal,c.alamat_terminal,a.*,b.id_accurate AS id_vendor_accurate, b.kode_vendor, c.lokasi_terminal
							FROM new_pro_inventory_vendor_po a
							JOIN pro_master_vendor b ON a.id_vendor = b.id_master
							JOIN pro_master_terminal c ON a.id_terminal = c. id_master
							WHERE a.id_master =  '" . $idr . "'";
				$rowget = $con->getRecord($queryget);

				$querygetReceive = "SELECT volume_bol, id_accurate, no_terima, tgl_terima FROM new_pro_inventory_vendor_po_receive WHERE id_po_supplier = '" . $idr . "'";
				$rowReceive = $con->getRecord($querygetReceive);

				$id_cabang = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
				$ambil_alamat = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $dt6 . "'";

				$detail_alamat = strtoupper($rowget['nama_terminal']) . " - " . $rowget['lokasi_terminal'];

				$queryget_cabang = "SELECT * FROM pro_master_cabang WHERE id_master = '" . $id_cabang . "'";
				$rowget_cabang = $con->getRecord($queryget_cabang);

				$newkode =  explode("/", $rowget['nomor_po']);

				// Tambahkan titik ke bagian pertama
				$newkode[0] .= ".";

				// Gabungkan lagi dengan "/"
				$new_nopo = implode("/", $newkode);

				$jenis_det = '';
				if ($jenis == 1) {
					$jenis_det = 'gain';
				} else {
					$jenis_det = 'loss';
				}
				$urlnya = 'https://zeus.accurate.id/accurate/api/purchase-order/save.do';
                	if ($rowget['id_accurate'] != null) {
					// Data yang akan dikirim dalam format JSON
					$data = array(
						'id'         		=> $rowget['id_accurate'],
						'vendorNo'         	=> $rowget['kode_vendor'],
						'number'           	=> $rowget['nomor_po'],
						'branchName'        => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
						'description'       => $ket,
						'toAddress'     	=> $detail_alamat,
						'manualClosed' 		=> true,
						'closeReason' 		=> 'Close PO - ' . $jenis_det
					);

					// Mengonversi data menjadi format JSON
					$jsonData = json_encode($data);

					$result = curl_post($urlnya, $jsonData);
					// $result = true;

					if ($result['d'] == true) {

						$query_delete = array(
							'id' => $rowReceive['id_accurate'],
						);

						$urlnya_delete = 'https://zeus.accurate.id/accurate/api/receive-item/delete.do?' . $query_delete;

						$result_delete = curl_delete($urlnya_delete, json_encode($query_delete));

						$result_delete = true;

						if ($result_delete == false) {
							$con->rollBack();
							$con->clearError();
							$con->close();
							$flash->add("error", $result_delete["d"][0] . " - Response dari Accurate", BASE_REFERER);
						} else {
							$query = http_build_query([
								'id' => $rowget['id_accurate'],
							]);
							$urlnya_detail = 'https://zeus.accurate.id/accurate/api/purchase-order/detail.do?' . $query;

							$result_detail = curl_get($urlnya_detail);

							if ($result_detail['s'] == true) {

								$data_save = array(
									'transDate'        	=> date("d/m/Y"),
									'vendorNo'         	=> $rowget['kode_vendor'],
									'number'           	=> $new_nopo,
									'branchName'        => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
									'paymentTermName'  	=> $rowget['terms'] . ' ' . $rowget['terms_day'],
									'charField1'    	=> $rowget['id_terminal'],
									'charField2'    	=> $rowget['kategori_plat'],
									'charField3'    	=> $rowget['kd_tax'],
									'description'       => $ket,
									"toAddress" 		=> $detail_alamat,
									'detailItem'       	=> [],
									'detailExpense'     => []
								);

								// Menggunakan foreach untuk mengisi detailItem
								foreach ($result_detail['d']['detailItem'] as $item) {
									$unitPriceLossGain = $item["item"]["itemType"] === 'INVENTORY' ? $dt16 : $item['unitPrice'];
									$quantity = $item["item"]["itemType"] === 'INVENTORY' ? $rowReceive['volume_bol'] : $item['quantity'];


									if (isset($item["item"]["itemType"]) && $item["item"]["itemType"] === 'INVENTORY') {
										$data_save['detailItem'][] = [
											'itemNo'       => $item['item']['no'],
											'quantity'     => $quantity,
											'unitPrice'    => $unitPriceLossGain,
											'useTax1'    => $item['useTax1'],
											'warehouseName' => $item['warehouse']['name']
										];
									}
									if (isset($item["item"]["itemType"]) && $item["item"]["itemType"] === 'INVENTORY') {
										$data_save['detailItem'][] = [
											'itemNo'    => $item['item']['no'],
											'quantity'  => $volume_loss_gain,
											'unitPrice' => $dt16,
											'useTax1'   => $item['useTax1'],
											'warehouseName' => $item['warehouse']['name']
										];
									}else if (isset($item["item"]["itemType"]) && $item["item"]["itemType"] !== 'INVENTORY'){
										$data_save['detailItem'][] = [
											'itemNo'    => $item['item']['no'],
											'quantity'  => $volume_loss_gain,
											'unitPrice' => $dt16,
											'useTax1'   => $item['useTax1'],
										];
									}
								}
							
								// Menggunakan foreach untuk mengisi detailExpense
								foreach ($result_detail['d']['detailExpense'] as $expense) {
									if ($expense["expenseName"] === 'PBBKB') {
										$data_save['detailExpense'][] = [
											'accountNo' => $expense['account']['no'],
											'expenseAmount'  => $pbbkb,
											// 'expenseName' => $expense['expenseName'],
											'allocateToItemCost' => $expense['allocateToItemCost']
										];
									} else if (strpos($expense["expenseName"], '22') !== false) {
										$data_save['detailExpense'][] = [
											'accountNo' => $expense['account']['no'],
											'expenseAmount'  => $pph_22,
											// 'expenseName' => $expense['expenseName'],
											'allocateToItemCost' => $expense['allocateToItemCost']
										];
									} else {
										$data_save['detailExpense'][] = [
											'accountNo' => $expense['account']['no'],
											'expenseAmount'  => $expense['expenseAmount'],
											// 'expenseName' => $expense['expenseName'],
											'allocateToItemCost' => $expense['allocateToItemCost']
										];
									}
								}

								// Mengonversi data_save menjadi format JSON
								$jsonData_save = json_encode($data_save);

								$result_save = curl_post($urlnya, $jsonData_save);

								if ($result_save['s'] == true) {
									$update = "UPDATE new_pro_inventory_vendor_po set id_accurate = '" . $result_save['r']['id'] . "' WHERE id_master = '" . $idr . "'";
									$con->setQuery($update);

									$urlnya_receive = 'https://zeus.accurate.id/accurate/api/receive-item/save.do';

									$data_receive = array(
										"receiveNumber" => $rowReceive['no_terima'],
										"number" => $rowReceive['no_terima'],
										"transDate" => date("d/m/Y", strtotime($rowReceive['tgl_terima'])),
										"vendorNo" => $result_save['r']['vendor']['vendorNo'],
										"description" => "Terima barang dari PO " . $result_save['r']['number'],
										"toAddress" => $detail_alamat,
										'branchName'  => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
										'detailItem' => []
									);

									foreach ($result_save['r']['detailItem'] as $item) {
										if ($item["item"]["itemType"] === 'INVENTORY') {
											$data_receive['detailItem'][] = [
												'itemNo'    => $item['item']['no'],
												'quantity'  => $item['quantity'],
												'useTax1'   => $item['useTax1'],
												'purchaseOrderNumber' => $result_save['r']['number']
											];
										}
									}

									$jsonData_receive = json_encode($data_receive);

									$result_receive = curl_post($urlnya_receive, $jsonData_receive);

									if ($result_receive['s'] == true) {
										$update_receive = "UPDATE new_pro_inventory_vendor_po_receive set id_accurate = '" . $result_receive['r']['id'] . "' WHERE id_po_supplier = '" . $idr . "'";
										$con->setQuery($update_receive);

										$urlnya_closePO = 'https://zeus.accurate.id/accurate/api/purchase-order/save.do';

										$data_tutup_po = array(
											'id'         		=> $result_save['r']['id'],
											'vendorNo'         	=> $result_save['r']['vendor']['vendorNo'],
											'number'           	=> $result_save['r']['number'],
											'branchName'        => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
											'toAddress'     	=> $detail_alamat,
											'manualClosed' 		=> true,
											'closeReason' 		=> 'Menunggu Approve'
										);

										// Mengonversi data menjadi format JSON
										$jsonData_tutupPO = json_encode($data_tutup_po);

										$result_tutupPO = curl_post($urlnya_closePO, $jsonData_tutupPO);

										if ($result_tutupPO['s'] == true) {
											$con->commit();
											$con->close();
											header("location: " . BASE_URL_CLIENT . "/vendor-po-new.php");
											exit();
										} else {
											$con->rollBack();
											$con->clearError();
											$con->close();
											$flash->add("error", $result_tutupPO["d"][0] . " - Response dari Accurate 1", BASE_REFERER);
										}
									} else {
										$con->rollBack();
										$con->clearError();
										$con->close();
										$flash->add("error", $result_receive["d"][0] . " - Response dari Accurate 2", BASE_REFERER);
									}
								} else {
									$con->rollBack();
									$con->clearError();
									$con->close();
									$flash->add("error", $result_save["d"][0] . " - Response dari Accurate 3", BASE_REFERER);
								}
							} else {
								$con->rollBack();
								$con->clearError();
								$con->close();
								$flash->add("error", $result_detail["d"][0] . " - Response dari Accurate 4", BASE_REFERER);
							}
						}
					} else {
						$con->rollBack();
						$con->clearError();
						$con->close();
						$flash->add("error", $result["d"][0] . " - Response dari Accurate 5", BASE_REFERER);
					}
				}
                // $con->commit();
                // $con->close();


                if ($isUpload) {
                    if (!file_exists($pathnya . '/' . $folder . '/')) mkdir($pathnya . '/' . $folder, 0777);

                    $tujuan  = $pathnya . '/' . $fileName;
                    $mantab  = move_uploaded_file($tempPhoto1, $tujuan);
                }

                // header("location: " . BASE_URL_CLIENT . "/vendor-po-new.php");
                // exit();
            } else {
                $con->rollBack();
                $con->clearError();
                $con->close();
                $flash->add("error", 'Maaf Data Gagal Disimpan...', BASE_REFERER);
            }
        }
    }
}
