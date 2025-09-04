<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed", "mailgen");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$enk  	= decode($_SERVER['REQUEST_URI']);
$act	= ($enk['act'] ? $enk['act'] : htmlspecialchars($_POST["act"], ENT_QUOTES));
$idr 	= isset($_POST["idr"]) ? $_POST["idr"] : null;

$dt1	= htmlspecialchars($_POST["dt1"], ENT_QUOTES);
$dt2	= htmlspecialchars($_POST["dt2"], ENT_QUOTES);
$dt3	= htmlspecialchars($_POST["dt3"], ENT_QUOTES);
$dt4	= htmlspecialchars($_POST["dt4"], ENT_QUOTES);
$dt5	= htmlspecialchars($_POST["dt5"], ENT_QUOTES);
$dt6	= htmlspecialchars($_POST["dt6"], ENT_QUOTES);
$dt6_edit = htmlspecialchars($_POST["dt6_edit"], ENT_QUOTES);
$alokasi = htmlspecialchars($_POST["alokasi"], ENT_QUOTES);
$dt7	= htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["dt7"]), ENT_QUOTES);
$dt8	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt8"]), ENT_QUOTES);
$subTotal = htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt9"]), ENT_QUOTES);
$dt10	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt10"]), ENT_QUOTES);
$ppn_11	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt11"]), ENT_QUOTES);
$pph_22	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt12"]), ENT_QUOTES);
$pbbkb	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt13"]), ENT_QUOTES);
$totalOrder	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dt14"]), ENT_QUOTES);
$dpp11_12	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["dpp11_12"]), ENT_QUOTES);
$ppn_12	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["ppn12"]), ENT_QUOTES);

$kategori_oa	= htmlspecialchars($_POST["kategori_oa"], ENT_QUOTES);
if ($kategori_oa == 1) {
	$ongkos_angkut = 0;
} else {
	$ongkos_angkut	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["ongkos_angkut"]), ENT_QUOTES);
}
$pbbkb_tawar	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["pbbkb_tawar"]), ENT_QUOTES);

$kd_tax		= htmlspecialchars($_POST["kd_tax"], ENT_QUOTES);
$terms		= htmlspecialchars($_POST["terms"], ENT_QUOTES);
$terms_day	= htmlspecialchars($_POST["terms_day"], ENT_QUOTES);
$ket	    = htmlspecialchars($_POST["ket"], ENT_QUOTES);

$cancel	    = htmlspecialchars($_POST["cancel"], ENT_QUOTES);
$tgl_close  = htmlspecialchars($_POST["tgl_close"], ENT_QUOTES);
$volume_close     = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["volume"]), ENT_QUOTES);
$kategori_plat	= htmlspecialchars($_POST["kategori_plat"], ENT_QUOTES);
$iuran_migas	= htmlspecialchars($_POST["iuran_migas"], ENT_QUOTES);
$nominal_iuran	= htmlspecialchars(str_replace(array(","), array("", ""), $_POST["nominal_iuran"]), ENT_QUOTES);
$keterangan_resubmission	= htmlspecialchars($_POST["ket_resubmission"], ENT_QUOTES);
$kode_item = $_POST["kode_item"];
$kode_oa = $_POST["kode_item2"];
$kode_pph22 = $_POST["kode_item3"];
$kode_pbbkb = $_POST["kode_biaya1"];
$kode_iuran_migas = $_POST["kode_biaya2"];
$biaya_oa = $_POST["biaya_oa"];
$biaya_lain = $_POST["biaya_lain"];
$datenow = date("d/m/Y H:i:s");


$jenis_oa = $_POST["jenis_oa"];
$jumlah_biaya_lain = htmlspecialchars(str_replace(array(".", ","), array("", ""), $_POST["jumlah_biaya"]), ENT_QUOTES);
$jumlah_biaya_oa = (int)$dt10 * (int)$ongkos_angkut;
$alokasi_barang1 = htmlspecialchars($_POST["alokasi_barang1"], ENT_QUOTES);
$alokasi_barang2 = htmlspecialchars($_POST["alokasi_barang2"], ENT_QUOTES);
$alokasi_barang3 = htmlspecialchars($_POST["alokasi_barang3"], ENT_QUOTES);
$alokasi_barang4 = htmlspecialchars($_POST["alokasi_barang4"], ENT_QUOTES);
$alokasi_barang5 = htmlspecialchars($_POST["alokasi_barang5"], ENT_QUOTES);
$keterangan_item1 = htmlspecialchars($_POST["keterangan_item1"], ENT_QUOTES);
$keterangan_item2 = htmlspecialchars($_POST["keterangan_item2"], ENT_QUOTES);
$keterangan_biaya1 = htmlspecialchars($_POST["keterangan_biaya1"], ENT_QUOTES);
$keterangan_biaya2 = htmlspecialchars($_POST["keterangan_biaya2"], ENT_QUOTES);
$keterangan_biaya3 = htmlspecialchars($_POST["keterangan_biaya3"], ENT_QUOTES);
$keterangan_biaya4 = htmlspecialchars($_POST["keterangan_biaya4"], ENT_QUOTES);
$keterangan_biaya5 = htmlspecialchars($_POST["keterangan_biaya5"], ENT_QUOTES);

if ($alokasi_barang1 == 1) {
	$allocate_biaya_oa = true;
} else {
	$allocate_biaya_oa = false;
}

if ($alokasi_barang2 == 1) {
	$allocate_biaya_lain = true;
} else {
	$allocate_biaya_lain = false;
}

if ($alokasi_barang3 == 1) {
	$allocate_biaya_pph22 = true;
} else {
	$allocate_biaya_pph22 = false;
}

if ($alokasi_barang4 == 1) {
	$allocate_biaya_pbbkb = true;
} else {
	$allocate_biaya_pbbkb = false;
}

if ($alokasi_barang5 == 1) {
	$allocate_biaya_migas = true;
} else {
	$allocate_biaya_migas = false;
}

if (strpos($kode_item, 'NS') !== false) {
	$allocate_get = true;
} else {
	$allocate_get = false;
}
// Array untuk menyimpan data yang valid
$data = array();
$data_biaya = array();

// Masukkan kode_item ke dalam array jika tidak kosong
if (!empty($kode_item)) {
	$data[] = ['kode' => $kode_item, 'keterangan' => 'kode_item'];
}

// Masukkan kode_oa ke dalam array jika tidak kosong, dan berikan keterangan "kode_oa"
if (!empty($kode_oa)) {
	$data[] = ['kode' => $kode_oa, 'keterangan' => 'kode_oa'];
}

// Masukkan kode_oa ke dalam array jika tidak kosong, dan berikan keterangan "biaya_oa"
if (!empty($biaya_oa)) {
	$data_biaya[] = ['kode' => $biaya_oa, 'nama_biaya' => 'biaya_oa'];
}

// Masukkan kode_oa ke dalam array jika tidak kosong, dan berikan keterangan "biaya_lain"
if (!empty($biaya_lain)) {
	$data_biaya[] = ['kode' => $biaya_lain, 'nama_biaya' => 'biaya_lain'];
}

// Masukkan kode_pph22 ke dalam array jika tidak kosong
if (!empty($kode_pph22)) {
	$data_biaya[] = ['kode' => $kode_pph22, 'nama_biaya' => 'kode_pph22'];
}

// Masukkan kode_pbbkb ke dalam array jika tidak kosong
if (!empty($kode_pbbkb)) {
	$data_biaya[] = ['kode' => $kode_pbbkb, 'nama_biaya' => 'biaya_pbbkb'];
}

// Masukkan kode_iuran_migas ke dalam array jika tidak kosong
if (!empty($kode_iuran_migas)) {
	$data_biaya[] = ['kode' => $kode_iuran_migas, 'nama_biaya' => 'biaya_iuran_migas'];
}
// echo json_encode($data);

if ($iuran_migas == "") {
	$iuran = "0";
} else {
	$iuran = "1";
}

$detailItems = [];
$detailExpenses = [];

foreach ($data as $item) {
	// Tentukan quantity berdasarkan kode yang ada
	$quantity = 0;

	if ($item['keterangan'] === 'kode_item') {
		$quantity = (int)$dt10;
		$unitprice = $dt8;
		$ppninclude = true;
		$jenis = 'kode_item';
		$detailNotes = $keterangan_item1;
	}

	if ($item['keterangan'] === 'kode_oa') {
		$quantity = (int)$dt10;
		$unitprice = $ongkos_angkut;
		$jenis = 'kode_oa';
		$detailNotes = $keterangan_item2;
		if ($kategori_plat == 'Hitam') {
			$ppninclude = true;
		} else {
			$ppninclude = false;
		}
	}

	// if ($item['keterangan'] === 'kode_iuran_migas') {
	// 	$quantity = 1;  // Jika kode_iuran_migas, quantity dihitung dengan rumus ini
	// 	$unitprice = $nominal_iuran;
	// }

	// Masukkan ke dalam detailItems array dengan itemNo dan quantity
	$detailItems[] = [
		'itemNo'    => $item['kode'],
		'quantity'  => $quantity,
		'unitPrice' => $unitprice,
		'useTax1' => $ppninclude,
		'jenis' => $jenis,
		'detailNotes' => $detailNotes
	];
}

foreach ($data_biaya as $biaya) {

	if ($biaya['nama_biaya'] === 'biaya_oa') {
		$expenseAmount = $jumlah_biaya_oa;
		$expenseName = 'Biaya OA';
		$allocate = $allocate_biaya_oa;
		$expenseNotes = $keterangan_biaya1;
	}

	if ($biaya['nama_biaya'] === 'biaya_lain') {
		$expenseAmount = $jumlah_biaya_lain;
		$expenseName = 'Biaya Lain';
		$allocate = $allocate_biaya_lain;
		$expenseNotes = $keterangan_biaya2;
	}

	if ($biaya['nama_biaya'] === 'kode_pph22') {
		$expenseAmount = $pph_22;
		$expenseName = 'PPH 22';
		$allocate = $allocate_biaya_pph22;
		$expenseNotes = $keterangan_biaya3;
	}

	if ($biaya['nama_biaya'] === 'biaya_pbbkb') {
		$expenseAmount = $pbbkb;
		$expenseName = 'PBBKB';
		$allocate = $allocate_biaya_pbbkb;
		$expenseNotes = $keterangan_biaya4;
	}


	if ($biaya['nama_biaya'] === 'biaya_iuran_migas') {
		$expenseAmount = $nominal_iuran;
		$expenseName = 'Iuran Migas';
		$allocate = $allocate_biaya_migas;
		$expenseNotes = $keterangan_biaya5;
	}

	// Masukkan ke dalam detailExpenses array dengan biayaNo dan quantity
	$detailExpenses[] = [
		'accountNo' => $biaya['kode'],
		'expenseAmount'  => $expenseAmount,
		// 'expenseName' => $expenseName,
		'allocateToItemCost' => $allocate,
		'expenseNotes' => $expenseNotes
	];
}

// echo json_encode($detailExpenses);

if ($act == 'cek') {
	echo json_encode(array("hasil" => true, "pesan" => ""));
	exit;
	$dt1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);
	$dt2 	= htmlspecialchars($_POST["q2"], ENT_QUOTES);
	$cek1 	= "select id_master from new_pro_inventory_vendor_po where nomor_po = '" . $dt2 . "'";
	$row1 	= $con->getRecord($cek1);

	if ($row1['id_master'] && $row1['id_master'] != $dt1) {
		echo json_encode(array("hasil" => false, "pesan" => "Nomor PO Sudah Ada..."));
		exit;
	} else {
		echo json_encode(array("hasil" => true, "pesan" => ""));
		exit;
	}
} else if ($act == 'add') {
	if ($dt1 == "" || $dt3 == "" || $dt5 == "" || $dt6 == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$kuenya = "select LPAD(cast((select nextval(new_pro_inventory_vendor_po_seq)) as varchar(10)), 9, '0') as idnya";
		$arrkue = $con->getRecord($kuenya);
		$id1nya = date("Ym") . $arrkue['idnya'];

		$sql01 = "
				select coalesce(max(cast(substr(a.nomor_po, 1, 3) as integer)), 0) as nomor, 
	         c.inisial_cabang, 
	         d.inisial_vendor  
			 from new_pro_inventory_vendor_po a  
			 join pro_master_terminal b on a.id_terminal = b.id_master 
			 join pro_master_cabang c on b.id_cabang = c.id_master 
			 join pro_master_vendor d on a.id_vendor = d.id_master 
			 where d.id_master = '" . $dt5 . "' 
	 		 and c.id_master = (select id_cabang from pro_master_terminal where id_master = '" . $dt6 . "')
	  		 and year(a.tanggal_inven) = '" . substr($dt1, 6, 4) . "'
			";
		$arrNom = $con->getRecord($sql01);
		$arrRom = array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
		$blnThn = $arrRom[intval(substr($dt1, 3, 2))] . '/' . substr($dt1, 8, 2);
		$dt2 	= str_pad(($arrNom['nomor'] + 1), 3, '0', STR_PAD_LEFT) . '/' . strtoupper($arrNom['inisial_vendor']) . '/' . strtoupper($arrNom['inisial_cabang']) . '/' . $blnThn;


		if ($id1nya) {
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$msg = "BERHASIL_MASUK";
			$ems1 = "select email_user from acl_user where id_role = 4";
			$sql = "
					insert into new_pro_inventory_vendor_po(id_master, id_vendor, id_produk, id_terminal, nomor_po, tanggal_inven, volume_po, harga_tebus, kategori_oa, is_biaya, ongkos_angkut, kategori_plat, iuran_migas, nominal_migas, kd_tax, subtotal, ppn_11, dpp_11_12, ppn_12, pph_22, nilai_pbbkb, pbbkb, total_order,  terms, terms_day, keterangan,
					created_time, created_ip, created_by, disposisi_po) values ('" . $id1nya . "', '" . $dt5 . "', '" . $dt3 . "', '" . $dt6 . "', '" . $dt2 . "', '" . tgl_db($dt1) . "', '" . $dt10 . "', '" . $dt8 . "', '" . $kategori_oa . "', '" . $jenis_oa . "', '" . $ongkos_angkut . "', '" . $kategori_plat . "', '" . $iuran . "', '" . $nominal_iuran . "', '" . $kd_tax . "', '" . $subTotal . "', '" . $ppn_11 . "', '" . $dpp11_12 . "', '" . $ppn_12 . "', '" . $pph_22 . "', '" . $pbbkb_tawar . "', '" . $pbbkb . "', '" . $totalOrder . "', '" . $terms . "', '" . $terms_day . "', '" . $ket . "', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "', 1)";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();
			// var_dump($arrkue);
			// exit;
			$ambil_alamat = "SELECT a.*,b.inisial_cabang FROM pro_master_terminal a
							JOIN pro_master_cabang b ON a.id_cabang = b.id_master 
							WHERE a.id_master = '" . $dt6 . "'";
			$alamat = $con->getRecord($ambil_alamat);

			$detail_alamat = strtoupper($alamat['nama_terminal']) . " - " . $alamat['lokasi_terminal'];

			$sbjk = "Persetujuan PO Supplier[" . date('d/m/Y H:i:s') . "]";
			$pesn = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . " meminta persetujuan untuk PO supplier";
			$pesn .= "<p>" . BASE_SERVER . "</p>";
			if ($oke) {

				$queryget = "SELECT * FROM pro_master_vendor WHERE id_master = '" . $dt5 . "'";
				$rowget = $con->getRecord($queryget);

				$id_cabang = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

				$queryget_cabang = "SELECT * FROM pro_master_cabang WHERE id_master = '" . $id_cabang . "'";
				$rowget_cabang = $con->getRecord($queryget_cabang);

				if ($rowget['id_accurate'] != null) {

					// if ($ems1) {
					// 	$rms1 = $con->getResult($ems1);
					// 	$mail = new PHPMailer;
					// 	$mail->isSMTP();
					// 	$mail->Host = 'smtp.gmail.com';
					// 	$mail->Port = 465;
					// 	$mail->SMTPSecure = 'ssl';
					// 	$mail->SMTPAuth = true;
					// 	$mail->SMTPKeepAlive = true;
					// 	$mail->Username = USR_EMAIL_PROENERGI202389;
					// 	$mail->Password = PWD_EMAIL_PROENERGI202389;

					// 	$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
					// 	foreach ($rms1 as $datms) {
					// 		$mail->addAddress($datms['email_user']);
					// 	}
					// 	$mail->Subject = $sbjk;
					// 	$mail->msgHTML($pesn);
					// 	$mail->send();
					// }

					$urlnya = 'https://zeus.accurate.id/accurate/api/purchase-order/save.do';
					// Data yang akan dikirim dalam format JSON
					$data = array(
						'transDate'        	=> $dt1,
						'vendorNo'         	=> $rowget['kode_vendor'],
						'number'           	=> $dt2,
						'branchName'        => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
						// 'documentCode'		=> 'CTAS_INVOICE',
						// 'taxType'			=> 'CTAS_DPP_NILAI_LAIN',
						'paymentTermName'  	=> $terms . ' ' . $terms_day,
						'charField1'    	=> $dt6,
						'charField2'    	=> $kategori_plat,
						'charField3'    	=> $kd_tax,
						'description'       => $ket,
						"toAddress" 		=> $detail_alamat,
						'detailItem'       	=> [],
						'detailExpense'     => []
					);

					// Menggunakan foreach untuk mengisi detailItem
					foreach ($detailItems as $item) {
						$dataItem = [
							'itemNo'     	=> $item['itemNo'],
							'quantity'   	=> $item['quantity'],
							'unitPrice'  	=> $item['unitPrice'],
							'useTax1'    	=> $item['useTax1'],
							'detailNotes'	=> $item['detailNotes'],
						];

						if ($item['jenis']) {
							$dataItem['warehouseName'] = $alamat['inisial_cabang'];
						}

						$data['detailItem'][] = $dataItem;
					}

					// Menggunakan foreach untuk mengisi detailExpense
					foreach ($detailExpenses as $expense) {
						$data['detailExpense'][] = [
							'accountNo' => $expense['accountNo'],
							'expenseAmount'  => $expense['expenseAmount'],
							// 'expenseName' => $expense['expenseName'],
							'allocateToItemCost' => $expense['allocateToItemCost'],
							'expenseNotes' => $expense['expenseNotes'],
						];
					}

					// Mengonversi data menjadi format JSON
					$jsonData = json_encode($data);

					$result = curl_post($urlnya, $jsonData);

					if ($result['s'] == true) {
						$data2 = array(
							"id"        		=> $result['r']['id'],
							"toAddress" 		=> $detail_alamat,
							'branchName'        => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
							"manualClosed" 		=> true,
							"closeReason" 		=> 'Menunggu Approve'
						);
						$jsonData2 = json_encode($data2);
						$result_close = curl_post($urlnya, $jsonData2);
						if ($result_close['s'] == true) {
							$update = "UPDATE new_pro_inventory_vendor_po set id_accurate = '" . $result['r']['id'] . "' WHERE id_master = " . $id1nya;
							$con->setQuery($update);

							$con->commit();
							$con->close();
							header("location: " . BASE_URL_CLIENT . "/vendor-po-new.php");
							exit();
						} else {
							$con->rollBack();
							$con->clearError();
							$con->close();
							$flash->add("error", $result_close["d"][0] . " - Response dari Accurate", BASE_REFERER);
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
					$flash->add("error", "Vendor pada accurate tidak ditemukan", BASE_REFERER);
				}
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $msg, BASE_REFERER);
			}
		}
	}
} else if ($act == 'update') {
	if ($dt1 == "" || $dt8 == "" || $dt10 == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		// echo json_encode($dt6_edit);
		$id1nya = $idr;

		if ($id1nya) {
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$data_po = "SELECT * FROM new_pro_inventory_vendor_po WHERE id_master = '" . $idr . "'";
			$rowget = $con->getRecord($data_po);

			$resubmit = '';
			$count_resubmit = $rowget['resubmission_count'];
			if ($rowget['is_close'] != 1 && $rowget['is_cancel'] != 1 && $rowget['ceo_result'] == 1 && $rowget['revert_ceo'] == 0) {
				if ($count_resubmit < 3) {
					$count_resubmit++;

					$resubmit = ", resubmission_date = NOW(), is_resubmission = 1, resubmission_count= $count_resubmit ";

					$sql = "
					insert into new_pro_inventory_vendor_po_history(id_po_supplier, id_vendor, id_produk, id_terminal, nomor_po, tanggal_inven, volume_po, harga_tebus, kategori_oa, ongkos_angkut, kategori_plat, iuran_migas, nominal_migas, kd_tax, subtotal, ppn_11, dpp_11_12, ppn_12, pph_22, nilai_pbbkb, pbbkb, total_order,  terms, terms_day, keterangan, created_time, created_ip, created_by, disposisi_po,is_resubmission,resubmission_count,keterangan_resubmission)
					values  ('" . $rowget['id_master'] . "', '" . $rowget['id_vendor'] . "', '" . $rowget['id_produk'] . "', '" . $rowget['id_terminal'] . "', '" . $rowget['nomor_po'] . "', '" . tgl_db($rowget['tanggal_inven']) . "', '" . $rowget['volume_po'] . "', '" . $rowget['harga_tebus'] . "', '" . $rowget['kategori_oa'] . "', '" . $rowget['ongkos_angkut'] . "', '" . $rowget['kategori_plat'] . "', '" . $rowget['iuran_migas'] . "', '" . $rowget['nominal_iuran'] . "', '" . $rowget['kd_tax'] . "', 
					'" . $rowget['subtotal'] . "', '" . $rowget['ppn_11'] . "', '" . $rowget['dpp11_12'] . "', '" . $rowget['ppn_12'] . "', '" . $rowget['pph_22'] . "', '" . $rowget['pbbkb_tawar'] . "', '" . $rowget['pbbkb'] . "', '" . $rowget['totalOrder'] . "', '" . $rowget['terms'] . "', '" . $rowget['terms_day'] . "', '" . $rowget['ket'] . "', '" . $rowget['created_time'] . "', '" . $rowget['created_ip'] . "', '" . $rowget['created_by'] . "', '" . $rowget['disposisi_po'] . "','" . $rowget['is_resubmission'] . "','" . $rowget['resubmission_count'] . "','" . $keterangan_resubmission . "')";
					$con->setQuery($sql);
					$oke  = $oke && !$con->hasError();
				}
			}

			$msg = "GAGAL_UBAH";
			$sql = "
					update new_pro_inventory_vendor_po set harga_tebus = '" . $dt8 . "', tanggal_inven = '" .   tgl_db($dt1) . "', disposisi_po = 1, cfo_result = 0, ceo_result = 0, revert_cfo = 0, revert_ceo = 0, volume_po = '" . $dt10 . "', kategori_oa = '" . $kategori_oa . "', ongkos_angkut = '" . $ongkos_angkut . "', kategori_plat = '" . $kategori_plat . "', iuran_migas = '" . $iuran . "', nominal_migas = '" . $nominal_iuran . "', kd_tax = '" . $kd_tax . "', subtotal = '" . $subTotal . "',  subtotal = '" . $subTotal . "', ppn_11 = '" . $ppn_11 . "', dpp_11_12 = '" . $dpp11_12 . "', ppn_12 = '" . $ppn_12 . "', pph_22 = '" . $pph_22 . "', nilai_pbbkb = '" . $pbbkb_tawar . "', pbbkb = '" . $pbbkb . "', total_order = '" . $totalOrder . "',
					terms = '" . $terms . "', terms_day = '" . $terms_day . "', keterangan =  '" . $ket . "',
					lastupdate_time = NOW(), lastupdate_ip = '" . $_SERVER['REMOTE_ADDR'] . "', lastupdate_by = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']) . "' " . $resubmit . "
					where id_master = '" . $idr . "'
				";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			if ($oke) {

				// $data_po = "SELECT * FROM new_pro_inventory_vendor_po WHERE id_master = '" . $idr . "'";
				// $rowget = $con->getRecord($data_po);

				$ambil_alamat = "SELECT a.*,b.inisial_cabang FROM pro_master_terminal a
							JOIN pro_master_cabang b ON a.id_cabang = b.id_master 
							WHERE a.id_master = '" . $rowget['id_terminal']  . "'";
				$alamat = $con->getRecord($ambil_alamat);

				$detail_alamat = strtoupper($alamat['nama_terminal']) . " - " . $alamat['lokasi_terminal'];

				$id_cabang = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

				$queryget_cabang = "SELECT * FROM pro_master_cabang WHERE id_master = '" . $id_cabang . "'";
				$rowget_cabang = $con->getRecord($queryget_cabang);


				$url_delete = 'https://zeus.accurate.id/accurate/api/purchase-order/delete.do';

				$id_accurate = $rowget['id_accurate'];
				$data_po_accurate = array(
					'id' => $id_accurate,
				);

				$result_po_accurate = curl_delete($url_delete, json_encode($data_po_accurate));

				if ($result_po_accurate['s'] == true) {
					$queryget = "SELECT * FROM pro_master_vendor WHERE id_master = '" . $rowget['id_vendor'] . "'";
					$rowgetvendor = $con->getRecord($queryget);

					if ($rowgetvendor['id_accurate'] != null) {

						$urlnya = 'https://zeus.accurate.id/accurate/api/purchase-order/save.do';
						// Data yang akan dikirim dalam format JSON
						$data = array(
							'transDate'        	=> $dt1,
							'vendorNo'         	=> $rowgetvendor['kode_vendor'],
							'number'           	=> $dt2,
							'branchName'        => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
							'paymentTermName'  	=> $terms . ' ' . $terms_day,
							'charField1'    	=> $dt6_edit,
							'charField2'    	=> $kategori_plat,
							'charField3'    	=> $kd_tax,
							'description'       => $ket,
							"toAddress" 		=> $detail_alamat,
							'detailItem'       	=> [],
							'detailExpense'     => []
						);

						// Menggunakan foreach untuk mengisi detailItem
						// foreach ($detailItems as $item) {
						// 	$data['detailItem'][] = [
						// 		'itemNo'       => $item['itemNo'],
						// 		'quantity'     => $item['quantity'],
						// 		'unitPrice'    => $item['unitPrice'],
						// 		'useTax1'	   => $item['useTax1'],
						// 		'warehouseName'=> $item['warehouseName'],
						// 	];
						// }

						foreach ($detailItems as $item) {
							$dataItem = [
								'itemNo'     	=> $item['itemNo'],
								'quantity'   	=> $item['quantity'],
								'unitPrice'  	=> $item['unitPrice'],
								'useTax1'    	=> $item['useTax1'],
								'detailNotes'	=> $item['detailNotes'],
							];

							if ($item['jenis']) {
								$dataItem['warehouseName'] = $alamat['inisial_cabang'];
							}

							$data['detailItem'][] = $dataItem;
						}

						// Menggunakan foreach untuk mengisi detailExpense
						foreach ($detailExpenses as $expense) {
							$data['detailExpense'][] = [
								'accountNo' => $expense['accountNo'],
								'expenseAmount'  => $expense['expenseAmount'],
								// 'expenseName' => $expense['expenseName'],
								'allocateToItemCost' => $expense['allocateToItemCost'],
								'expenseNotes' => $expense['expenseNotes'],
							];
						}

						// Mengonversi data menjadi format JSON
						$jsonData = json_encode($data);
						$result = curl_post($urlnya, $jsonData);

						if ($result['s'] == true) {
							$data2 = array(
								"id"        		=> $result['r']['id'],
								"branchName"        => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
								"toAddress" 		=> $detail_alamat,
								"manualClosed" 		=> true,
								"closeReason" 		=> 'Menunggu Approve'
							);

							$jsonData = json_encode($data2);
							$result_close = curl_post($urlnya, $jsonData);
							if ($result['s'] == true) {
								$update = "UPDATE new_pro_inventory_vendor_po set id_accurate = '" . $result['r']['id'] . "' WHERE id_master = " . $id1nya;
								$con->setQuery($update);
								// echo $jsonData;
								$con->commit();
								$con->close();
								header("location: " . BASE_URL_CLIENT . "/vendor-po-new.php");
								exit();
							} else {
								$con->rollBack();
								$con->clearError();
								$con->close();
								$flash->add("error", $result_close["d"][0] . " - Response dari Accurate", BASE_REFERER);
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
						$flash->add("error", "Vendor pada accurate tidak ditemukan", BASE_REFERER);
					}
				} else {
					$con->rollBack();
					$con->clearError();
					$con->close();
					$flash->add("error", $result_po_accurate["d"][0] . " - Response dari Accurate", BASE_REFERER);
				}
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $msg, BASE_REFERER);
			}
		}
	}
} else if ($act == 'hapus') {
	$param 	= htmlspecialchars(paramDecrypt($_POST["param"]), ENT_QUOTES);
	$post 	= explode("#|#", $param);
	$file	= isset($post[0]) ? htmlspecialchars($post[0], ENT_QUOTES) : null;
	$id1	= isset($post[1]) ? htmlspecialchars($post[1], ENT_QUOTES) : null;
	$id2	= isset($post[2]) ? htmlspecialchars($post[2], ENT_QUOTES) : null;
	$id3	= isset($post[3]) ? htmlspecialchars($post[3], ENT_QUOTES) : null;
	$id4	= isset($post[4]) ? htmlspecialchars($post[4], ENT_QUOTES) : null;

	$cek = "select id_po_supplier from new_pro_inventory_vendor_po_receive where id_po_supplier = '" . $id1 . "'";
	$row = $con->getRecord($cek);

	if (!$row['id_po_supplier']) {
		$sql = "delete from new_pro_inventory_vendor_po where id_master = '" . $id1 . "'";
		$con->setQuery($sql);

		if (!$con->hasError()) {
			$con->close();
			$arr["error"] = "";
		} else {
			$con->clearError();
			$con->close();
			$arr["error"] = "Maaf Data tidak dapat dihapus..";
		}
	} else {
		$con->close();
		$arr["error"] = "Maaf, data tidak dapat dihapus, karena sudah terdapat data inventory";
	}

	echo json_encode($arr);
	exit;
} else if ($act == 'cancel') {
	if ($cancel == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$id1nya = $idr;

		if ($id1nya) {
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$msg = "GAGAL_UBAH";
			$sql = "
					update new_pro_inventory_vendor_po set is_cancel = 1, keterangan_cancel = '" . $cancel . "' 
					where id_master = '" . $idr . "'
				";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			if ($oke) {
				$queryget = "SELECT * FROM new_pro_inventory_vendor_po WHERE id_master = '" . $idr . "'";
				$rowget = $con->getRecord($queryget);

				$ambil_alamat = "SELECT * FROM pro_master_terminal WHERE id_master = '" . $dt6 . "'";
				$alamat = $con->getRecord($ambil_alamat);

				$detail_alamat = strtoupper($alamat['nama_terminal']) . " - " . $alamat['lokasi_terminal'];

				$id_cabang = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);

				$queryget_cabang = "SELECT * FROM pro_master_cabang WHERE id_master = '" . $id_cabang . "'";
				$rowget_cabang = $con->getRecord($queryget_cabang);

				$urlnya = 'https://zeus.accurate.id/accurate/api/purchase-order/save.do';

				$data2 = array(
					"id"        		=> $rowget['id_accurate'],
					"toAddress" 		=> $detail_alamat,
					'branchName'        => $rowget_cabang['nama_cabang'] == 'Kantor Pusat' ? 'Head Office' : $rowget_cabang['nama_cabang'],
					"manualClosed" 		=> true,
					"closeReason" 		=> $cancel
				);
				$jsonData2 = json_encode($data2);
				$result_close = curl_post($urlnya, $jsonData2);
				if ($result_close['s'] == true) {

					$con->commit();
					$con->close();
					header("location: " . BASE_URL_CLIENT . "/vendor-po-new.php");
					exit();
				} else {
					$con->rollBack();
					$con->clearError();
					$con->close();
					$flash->add("error", $result_close["d"][0] . " - Response dari Accurate", BASE_REFERER);
				}
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $msg, BASE_REFERER);
			}
		}
	}
} else if ($act == 'close') {
	if ($tgl_close == "") {
		$con->close();
		$flash->add("error", "KOSONG", BASE_REFERER);
	} else {
		$id1nya = $idr;

		if ($id1nya) {
			$oke = true;
			$con->beginTransaction();
			$con->clearError();

			$msg = "GAGAL_UBAH";
			$sql = "
					update new_pro_inventory_vendor_po set is_close = 1, tanggal_close = '" . tgl_db($tgl_close) . "',  volume_close = '" . $volume_close . "' 
					where id_master = '" . $idr . "'
				";
			$con->setQuery($sql);
			$oke  = $oke && !$con->hasError();

			if ($oke) {
				$con->commit();
				$con->close();
				header("location: " . BASE_URL_CLIENT . "/vendor-po-new.php");
				exit();
			} else {
				$con->rollBack();
				$con->clearError();
				$con->close();
				$flash->add("error", $msg, BASE_REFERER);
			}
		}
	}
}
