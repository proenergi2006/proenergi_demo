<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth    = new MyOtentikasi();
$enk      = decode($_SERVER['REQUEST_URI']);
$con     = new Connection();
$flash    = new FlashAlerts;

if (isset($enk['idr']) && $enk['idr'] !== '') {
    $action     = "update";
    $section     = "PO Suplier";
    $idr = isset($enk["idr"]) ? htmlspecialchars($enk["idr"], ENT_QUOTES) : '';
    $sql = "
			select a.*, a1.id_po_supplier, b.jenis_produk, b.merk_dagang, d.nama_vendor, e.nama_terminal, e.tanki_terminal, e.lokasi_terminal 
			from new_pro_inventory_vendor_po a 
			join pro_master_produk b on a.id_produk = b.id_master 
			join pro_master_vendor d on a.id_vendor = d.id_master 
			join pro_master_terminal e on a.id_terminal = e.id_master 
			left join new_pro_inventory_vendor_po_receive a1 on a.id_master = a1.id_po_supplier
            LEFT JOIN (
                SELECT *
                FROM new_pro_inventory_vendor_po_history h
                WHERE (h.id_master, h.id_po_supplier) IN (
                    SELECT 
                        MAX(h2.id_master) AS max_id, h2.id_po_supplier
                    FROM 
                        new_pro_inventory_vendor_po_history h2
                    GROUP BY 
                        h2.id_po_supplier
                )
            ) f ON a.id_master = f.id_po_supplier
			where a.id_master = '" . $idr . "'
		";
    $rsm     = $con->getRecord($sql);

    $dt1     = date("d/m/Y", strtotime($rsm['tanggal_inven']));
    $dt8     = ($rsm['harga_tebus']) ? $rsm['harga_tebus'] : '';
    $ket     =  ($rsm['keterangan']) ? $rsm['keterangan'] : '';
    $ceo    =  ($rsm['ceo_summary']) ? $rsm['ceo_summary'] : '';
    $is_ceo   =  ($rsm['ceo_result']) ? $rsm['ceo_result'] : '';
    $ceo_pic    =  ($rsm['ceo_pic']) ? $rsm['ceo_pic'] : '';
    $ceo_tanggal    =  ($rsm['ceo_tanggal']) ? $rsm['ceo_tanggal'] : '';
    $cfo    =  ($rsm['cfo_summary']) ? $rsm['cfo_summary'] : '';
    $cfo_pic    =  ($rsm['cfo_pic']) ? $rsm['cfo_pic'] : '';
    $cfo_tanggal    =  ($rsm['cfo_tanggal']) ? $rsm['cfo_tanggal'] : '';
    $kategori_oa     = ($rsm['kategori_oa']) ? $rsm['kategori_oa'] : '';
    $ongkos_angkut     = ($rsm['ongkos_angkut']) ? $rsm['ongkos_angkut'] : 0;
    $nilai_pbbkb     = ($rsm['nilai_pbbkb']) ? $rsm['nilai_pbbkb'] : 0;

    $revert_cfo    =  ($rsm['revert_cfo_summary']) ? $rsm['revert_cfo_summary'] : '';
    $revert_ceo    =  ($rsm['revert_ceo_summary']) ? $rsm['revert_ceo_summary'] : '';
    $revert    =  ($rsm['revert_ceo']) ? $rsm['revert_ceo'] : '';


    $dt9   = ($rsm['subtotal']) ? $rsm['subtotal'] : '';
    $dt10    = ($rsm['volume_po']) ? $rsm['volume_po'] : '';
    $dt11    = ($rsm['ppn_11']) ? $rsm['ppn_11'] : '';
    $dt12    = ($rsm['pph_22']) ? $rsm['pph_22'] : '';
    $dt13    = ($rsm['pbbkb']) ? $rsm['pbbkb'] : '';
    $dt14    = ($rsm['total_order']) ? $rsm['total_order'] : '';
    $dpp11_12    = ($rsm['dpp_11_12']) ? $rsm['dpp_11_12'] : '';
    $ppn12    = ($rsm['ppn_12']) ? $rsm['ppn_12'] : '';
    $iuran_migas = ($rsm['iuran_migas']) ? $rsm['iuran_migas'] : '';
    $nominal_iuran = ($rsm['nominal_migas']) ? $rsm['nominal_migas'] : '';
    $kategori_plat = ($rsm['kategori_plat']) ? $rsm['kategori_plat'] : '';
    $is_biaya = ($rsm['is_biaya']) ? $rsm['is_biaya'] : '';

    $kode_item = '';
    $kode_oa = '';
    $biaya_pbbkb = '';
    $biaya_22 = '';
    $biaya_migas = '';
    $biaya_vat = '';
    $biaya_oa = '';
    $amount_vat='';

    if ($rsm['id_accurate'] != null) {
        //get detail PO
        $query = http_build_query([
            'id' => $rsm['id_accurate'],
        ]);

        $urlnya = 'https://zeus.accurate.id/accurate/api/purchase-order/detail.do?' . $query;

        $result_detail = curl_get($urlnya);

        foreach ($result_detail['d']['detailItem'] as $item) {
            if ($item["item"]["itemType"] === 'INVENTORY') {
                $kode_item = $item["item"]["no"];
            } else {
                $kode_oa = $item["item"]["no"];
            }
        }

        $biaya = [
            'pbbkb' => null,
            '22' => null,
            'vat' => null,
            'iuran' => null,
            'oa' => null
        ];

        foreach ($result_detail['d']['detailExpense'] as $expense) {
            $name = $expense["expenseName"];
            $notes = $expense["expenseNotes"];
            $amount = $expense["expenseAmount"];
            $allocate = $expense["allocateToItemCost"];

            if ($name === 'PBBKB' && (in_array($notes, ['null', null, 'NULL',NULL]))) {
                $biaya['pbbkb'] = [
                    'name' => $name,
                    'notes' => 'null',
                    'allocate' => $allocate
                ];
            } elseif (strpos($name, '22') !== false) {
                $biaya['22'] = [
                    'name' => $name,
                    'notes' => $notes,
                    'allocate' => $allocate
                ];
            } elseif (strpos($name, 'VAT') !== false) {
                $biaya['vat'] = [
                    'name' => $name,
                    'notes' => $notes,
                    'amount' => $amount,
                    'allocate' => $allocate
                ];
            } elseif (stripos($notes, 'iuran') !== false || stripos($name, 'iuran') !== false) {
                $biaya['iuran'] = [
                    'name' => $name,
                    'notes' => $notes,
                    'allocate' => $allocate
                ];
            } elseif (stripos($name, 'cost') !== false) {
                $biaya['oa'] = [
                    'name' => $name,
                    'notes' => $notes,
                    'allocate' => $allocate
                ];
            }
        }
    }
} else {
    $idr = null;
    $action     = "add";
    $section     = "PO Suplier";
    $rsm         = array();
    $dt1         = "";
    $dt8         = "";
    $ket        = "";
    $dt10         = "";

    $produk_acc = "SELECT * FROM pro_master_produk_accurate";
    $res_produk_acc = $con->getResult($produk_acc);
}

// GET KODE ITEM ACCURATE
$query_item = http_build_query([
    'fields' => 'id,no,name',
    'filter.itemType.val' => [
        'INVENTORY',
        'NON_INVENTORY',
        'SERVICE'
    ],
]);

$urlnya = 'https://zeus.accurate.id/accurate/api/item/list.do?' . $query_item;

$result = curl_get($urlnya);

if ($result['s'] == true) {
    $data_item_get = http_build_query([
        'fields' => 'id,no,name',
        'sp.pageSize' => $result['sp']['rowCount'],
    ]);

    // Get the total number of pages
    $pageCountItem = $result['sp']['pageCount'];

    // Initialize the array to hold the accounts' details
    $item_details = [];

    // Loop through each page to fetch account details
    for ($i = 1; $i <= $pageCountItem; $i++) {
        // Update pagination for the current page
        $data_item_get_paginated = $data_item_get . '&sp.page=' . $i;

        // Make the request for the current page
        $url_item = 'https://zeus.accurate.id/accurate/api/item/list.do?' . $data_item_get_paginated;
        $result_item = curl_get($url_item);

        // If the request was successful, process the data
        if ($result_item['s'] == true) {
            foreach ($result_item['d'] as $key) {
                $item_details[] = [
                    'id' => $key['id'],
                    'kode_barang' => $key['no'],
                    'name' => $key['name']
                ];
            }
        }
    }
} else {
    $item_details = [];
}

//GET KODE AKUN ACCURATE
$url_getrow = 'https://zeus.accurate.id/accurate/api/glaccount/list.do';

// Fetch initial data to get page count
$result_getrow = curl_get($url_getrow);

if ($result_getrow['sp'] == true) {
    // Prepare data for pagination
    $data_akun_get = http_build_query([
        'fields' => 'id,no,nameWithIndent,accountTypeName,noWithIndent,name',
        'sp.pageSize' => $result_getrow['sp']['rowCount'],
    ]);

    // Get the total number of pages
    $pageCount = $result_getrow['sp']['pageCount'];

    // Initialize the array to hold the accounts' details
    $akun_details = [];

    // Loop through each page to fetch account details
    for ($i = 1; $i <= $pageCount; $i++) {
        // Update pagination for the current page
        $data_akun_get_paginated = $data_akun_get . '&sp.page=' . $i;

        // Make the request for the current page
        $url_akun = 'https://zeus.accurate.id/accurate/api/glaccount/list.do?' . $data_akun_get_paginated;
        $result_akun = curl_get($url_akun);

        // If the request was successful, process the data
        if ($result_akun['s'] == true) {
            foreach ($result_akun['d'] as $key) {
                $akun_details[] = [
                    'id' => $key['id'],
                    'no' => $key['no'],
                    'name' => $key['name'],
                    'accountTypeName' => $key['accountTypeName'],
                    'nameWithIndent' => $key['nameWithIndent'],
                    'noWithIndent' => $key['noWithIndent']
                ];
            }
        }
    }

    // Optionally, output or return the accumulated account details
    // echo json_encode($akun_details);
}

?>


<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "ckeditor"), "css" => array("jqueryUI"))); ?>

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1><?php echo $section; ?></h1>
            </section>
            <section class="content">

                <?php $flash->display(); ?>
                <form action="<?php echo ACTION_CLIENT . '/vendor-po-new.php'; ?>" id="gform" name="gform" method="post" class="form-horizontal" role="form">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Nomor PO *</label>
                                        <div class="col-md-8">
                                            <input type="text" name="dt2" id="dt2" class="form-control" value="<?php echo $rsm['nomor_po'] ?? null; ?>" readonly />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Tanggal PO *</label>
                                        <div class="col-md-4">
                                            <?php if (!$dt1) { ?>
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="font-size:12px;"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" name="dt1" id="dt1" class="form-control datepicker" required data-rule-dateNL="1" value="<?php echo $dt1; ?>" autocomplete="off" />
                                                </div>
                                            <?php } else { ?>
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="font-size:12px;"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" name="dt1" id="dt1" class="form-control" required data-rule-dateNL="1" value="<?php echo $dt1; ?>" autocomplete="off" />
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Produk *</label>
                                        <div class="col-md-8">
                                            <select name="dt3" id="dt3" class="form-control select2" style="width:100%;" required <?php echo ($rsm['id_produk'] ? 'disabled' : ''); ?>>
                                                <option></option>
                                                <?php $con->fill_select("id_master", "concat(jenis_produk,' - ',merk_dagang)", "pro_master_produk", $rsm['id_produk'], "where is_active=1", "no_urut", false); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Parameter untuk Accurate -->
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Kode Item Accurate *</label>
                                        <div class="col-md-5">
                                            <select name="kode_item" id="kode_item" class="form-control select2" style="width:100%;" required>>
                                                <option value=""></option>
                                                <?php foreach ($item_details as $key) :
                                                ?>
                                                    <option value="<?= $key['kode_barang'] ?>" <?= $kode_item == $key['kode_barang'] ? 'selected' : '' ?>><?= $key['kode_barang'] . " ( " . $key['name'] . " ) " ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" id="keterangan_item1" name="keterangan_item1" class="form-control" placeholder="Keterangan" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Terminal *</label>
                                        <div class="col-md-8">
                                            <select name="dt6" id="dt6" class="form-control select2" style="width:100%;" required <?php echo ($rsm['id_terminal'] ? 'disabled' : ''); ?>>
                                                <option></option>
                                                <?php
                                                $sqlOpt01 = "
													select a.id_master, concat(a.nama_terminal,' - ',a.tanki_terminal,' - ',a.lokasi_terminal) as terminal 
													from pro_master_terminal a 
													join pro_master_cabang b on a.id_cabang = b.id_master 
													where a.is_active = 1 
													order by a.id_master 
												";
                                                $resOpt01 = $con->getResult($sqlOpt01);
                                                if (count($resOpt01) > 0) {
                                                    foreach ($resOpt01 as $arrOpt01) {
                                                        $selected = ($rsm['id_terminal'] == $arrOpt01['id_master'] ? 'selected' : '');
                                                        echo '<option value="' . $arrOpt01['id_master'] . '" ' . $selected . '>' . $arrOpt01['terminal'] . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Vendor *</label>
                                        <div class="col-md-8">
                                            <select name="dt5" id="dt5" class="form-control select2" style="width:100%;" required <?php echo ($rsm['id_vendor'] ? 'disabled' : ''); ?>>
                                                <option></option>
                                                <?php $con->fill_select("id_master", "nama_vendor", "pro_master_vendor", $rsm['id_vendor'], "where is_active=1", "id_master", false); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Terms *</label>
                                        <div class="col-md-4">
                                            <select name="terms" id="terms" class="form-control select2" style="width:100%;" required>
                                                <option></option>
                                                <option value="COD" <?php echo ($rsm['terms'] == 'COD' ? 'selected' : ''); ?>>C.O.D</option>
                                                <option value="NET" <?php echo ($rsm['terms'] == 'NET' ? 'selected' : ''); ?>>NET</option>
                                                <option value="CBD" <?php echo ($rsm['terms'] == 'CBD' ? 'selected' : ''); ?>>CBD</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input type="text" id="terms_day" name="terms_day" class="form-control hitung1" <?php echo ($rsm['terms'] != 'NET' ? 'readonly' : ''); ?> value="<?php echo $rsm['terms_day']; ?>" maxlength="3" />
                                                <span class="input-group-addon" style="font-size:12px;">Hari</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Kode Tax *</label>
                                        <div class="col-md-4">
                                            <select name="kd_tax" id="kd_tax" class="form-control select2" style="width:100%;" required>
                                                <option></option>
                                                <option value="E" <?php echo ($rsm['kd_tax'] == 'E' ? 'selected' : ''); ?>>E</option>
                                                <option value="EC" <?php echo ($rsm['kd_tax'] == 'EC' ? 'selected' : ''); ?>>EC</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Volume PO *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <input type="text" id="dt10" name="dt10" class="form-control hitung1" value="<?php echo $dt10; ?>" required />
                                                <span class="input-group-addon" style="font-size:12px;">Liter</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Harga Dasar *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt8" name="dt8" class="form-control hitung" required value="<?php echo $dt8; ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="kategori_oa" id="kategori_oa" class="form-control">
                                                <option <?php echo $kategori_oa == 1 ? "selected" : "" ?> value="1">Tanpa OA</option>
                                                <option <?php echo $kategori_oa == 2 ? "selected" : "" ?> value="2">Dengan OA</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Parameter untuk Accurate -->
                            <div class="row <?php echo $kategori_oa == 2 ? "" : "hide" ?>" id="row_jenis_oa">
                                <hr>
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Jenis OA *</label>
                                        <div class="col-md-5">
                                            <select name="jenis_oa" id="jenis_oa" class="form-control select2" style="width:100%;" value="<?php echo $is_biaya; ?>" required>
                                                <option value=""></option>
                                                <option <?php echo $is_biaya == 1 ? "selected" : "" ?> value="1">Sebagai Biaya</option>
                                                <option <?php echo $is_biaya == 0 ? "selected" : "" ?> value="0">Sebagai Kode Item</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row <?php echo ($kategori_oa == 2)  ? "" : "hide" ?>" id="row_kode_item">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Kode OA Accurate *</label>
                                        <div class="col-md-5">
                                            <select name="kode_item2" id="kode_item2" class="form-control select2" style="width:100%;">
                                                <option value=""></option>
                                                <?php foreach ($item_details as $key) : ?>
                                                    <option value="<?= $key['kode_barang'] ?>" <?= $kode_oa == $key['kode_barang'] ? 'selected' : '' ?>><?= $key['kode_barang'] . " ( " . $key['name'] . " ) " ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" id="keterangan_item2" name="keterangan_item2" class="form-control" placeholder="Keterangan" value="" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Parameter -->

                            <div class="row <?php echo $kategori_oa == 2 ? "" : "hide" ?>" id="row_oa">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Ongkos Angkut *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="ongkos_angkut" name="ongkos_angkut" class="form-control hitung" value="<?php echo $ongkos_angkut ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row <?php echo $kategori_oa == 2 ? "" : "hide" ?>" id="row-plat">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Kategori Plat *</label>
                                        <div class="col-md-4">
                                            <select name="kategori_plat" id="kategori_plat" class="form-control select2" style="width:100%;">
                                                <option></option>
                                                <option value="Hitam" <?= $kategori_plat == "Hitam" ? 'selected' : '' ?>>Hitam</option>
                                                <option value="Kuning" <?= $kategori_plat == "Kuning" ? 'selected' : '' ?>>Kuning</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                              <!-- Parameter untuk Accurate -->
                            <div class="row <?php echo ($kategori_oa == 2 && $is_biaya ==1) ? "" : "hide" ?>" id="row_biaya_oa">
                                <div class="col-md-12">
                                    <!-- Label utama di atas -->
                                    <label class="control-label">Akun OA Accurate *</label>

                                    <div class="form-group form-group-sm row" style="align-items:center; margin-top:5px;">

                                        <!-- Select -->
                                        <div class="col-md-3">
                                            <select name="biaya_oa" id="biaya_oa" class="form-control select2" style="width:100%;">
                                                <option value=""></option>
                                                <?php foreach ($akun_details as $key) : ?>
                                                    <option value="<?= $key['no'] ?>" <?=  $biaya['oa']['name'] == $key['name'] ? 'selected' : '' ?>>
                                                        <?= $key['noWithIndent'] ?> <?= $key['nameWithIndent'] ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>

                                        <!-- Input keterangan -->
                                        <div class="col-md-3">
                                            <input type="text" id="keterangan_biaya1" name="keterangan_biaya1" class="form-control" placeholder="Keterangan" value="<?php echo $biaya['oa']['notes'] ?>"/>
                                        </div>

                                        <!-- Checkbox -->
                                        <div class="col-md-3" style="display:flex; align-items:center;">
                                            <input type="checkbox" id="alokasi_barang1" name="alokasi_barang1" value="1" <?=  $biaya['oa']['allocate'] ? 'checked' : '' ?> style="margin-right:6px;">
                                            Alokasikan ke Barang
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <!-- Label utama di atas -->
                                    <label class="control-label">Akun biaya lain *</label>

                                    <div class="form-group form-group-sm row" style="align-items:center; margin-top:5px;">

                                        <!-- Select -->
                                        <div class="col-md-3">
                                            <select name="biaya_lain" id="biaya_lain" class="form-control select2" style="width:100%;">
                                                <option value=""></option>
                                                <?php foreach ($akun_details as $key) : ?>
                                                    <option value="<?= $key['no'] ?>" <?=  $biaya['vat']['name'] == $key['name'] ? 'selected' : '' ?>>
                                                        <?= $key['noWithIndent'] ?> <?= $key['nameWithIndent'] ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>

                                        <!-- Input jumlah -->
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <span class="input-group-addon">Rp.</span>
                                                <input type="text" id="jumlah_biaya" name="jumlah_biaya" class="form-control text-right hitung1"  value="<?php echo $biaya['vat']['amount'] ?>" />
                                            </div>
                                        </div>

                                        <!-- Input keterangan -->
                                        <div class="col-md-3">
                                            <input type="text" id="keterangan_biaya2" name="keterangan_biaya2" class="form-control" placeholder="Keterangan" value="<?php echo  $biaya['vat']['notes'] ?>" />
                                        </div>

                                        <!-- Checkbox -->
                                        <div class="col-md-3" style="display:flex; align-items:center;">
                                            <input type="checkbox" id="alokasi_barang2" name="alokasi_barang2" value="1" <?=  $biaya['vat']['allocate'] ? 'checked' : '' ?> style="margin-right:6px;">
                                            Alokasikan ke Barang
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <hr>
                            <!-- end parameter -->

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Sub Total *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt9" name="dt9" class="form-control hitung" required value="<?php echo $dt9; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row hide" id="row-dt11">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">PPN 11% *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt11" name="dt11" class="form-control hitung" required value="<?php echo $dt11; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- untuk PPN 12% DPP 11/12 -->
                             <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">DPP 11/12 *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dpp11_12" name="dpp11_12" class="form-control hitung" required value="<?php echo $dpp11_12; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="row-dt11">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">PPN 12% *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="ppn12" name="ppn12" class="form-control hitung" required value="<?php echo $ppn12; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Parameter untuk Accurate -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Akun PPH22 Accurate *</label>
                                        <div class="col-md-3">
                                            <select name="kode_item3" id="kode_item3" class="form-control select2" style="width:100%;" <?php echo ($rsm && $rsm['kd_tax'] == 'E' ? 'disabled' : ''); ?> >
                                                <option value=""></option>
                                                <?php foreach ($akun_details as $key) : ?>
                                                    <option value="<?= $key['no'] ?>" <?=  $biaya['22']['name'] == $key['name'] ? 'selected' : '' ?>>
                                                        <?= $key['noWithIndent'] ?> <?= $key['nameWithIndent'] ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" id="keterangan_biaya3" name="keterangan_biaya3" class="form-control" placeholder="Keterangan" value="<?php echo  $biaya['22']['notes'] ?>" />
                                        </div>
                                        <div class="col-md-3" style="display:flex; align-items:center;">
                                            <input type="checkbox" id="alokasi_barang3" name="alokasi_barang3" value="1" <?=  $biaya['22']['allocate'] ? 'checked' : '' ?>  style="margin-right:6px;">
                                            Alokasikan ke Barang
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="row-dt12">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">PPH 22 *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt12" name="dt12" class="form-control hitung" required value="<?php echo $dt12; ?>" readonly />

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-4">PBBKB *</label>
                                        <div class="col-md-6">
                                            <select name="pbbkb_tawar" id="pbbkb_tawar" class="form-control select2" required>
                                                <option></option>
                                                <?php $con->fill_select("nilai_pbbkb", "concat(nilai_pbbkb, ' %')", "pro_master_pbbkb", $rsm['nilai_pbbkb'], "", "", false); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Parameter untuk Accurate -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Akun PBBKB Accurate *</label>
                                        <div class="col-md-3">
                                            <select name="kode_biaya1" id="kode_biaya1" class="form-control select2" style="width:100%;" required>
                                                <option value=""></option>
                                                <?php foreach ($akun_details as $key) : ?>
                                                    <option value="<?= $key['no'] ?>" <?=  $biaya['pbbkb']['name'] == $key['name'] && $biaya['pbbkb']['notes'] == "null" ? 'selected' : '' ?>>
                                                        <?= $key['noWithIndent'] ?> <?= $key['nameWithIndent'] ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" id="keterangan_biaya4" name="keterangan_biaya4" class="form-control" placeholder="Keterangan" value="<?php echo  $biaya['pbbkb']['notes'] ?>" />
                                        </div>
                                        <div class="col-md-3" style="display:flex; align-items:center;">
                                            <input type="checkbox" id="alokasi_barang4" name="alokasi_barang4" value="1" <?=  $biaya['pbbkb']['allocate'] ? 'checked' : '' ?> style="margin-right:6px;">
                                            Alokasikan ke Barang
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt13" name="dt13" class="form-control hitung" value="<?php echo isset($dt13) ? $dt13 : '0'; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Iuran Migas</label>
                                        <div class="col-md-5">
                                            <input type="checkbox" id="iuran_migas" name="iuran_migas" value="1" <?= $iuran_migas == '1' ? 'checked' : '' ?>> Centang jika PO ini ada iuran migas
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="nominal_iuran" name="nominal_iuran" class="form-control text-right hitung1" readonly autocomplete="off" value="<?= $nominal_iuran ? $nominal_iuran : 0 ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Parameter untuk Accurate -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-2">Akun Migas Accurate *</label>
                                        <div class="col-md-3">
                                            <select name="kode_biaya2" id="kode_biaya2" class="form-control select2" style="width:100%;">
                                                <option value=""></option>
                                                <?php foreach ($akun_details as $key) : ?>
                                                    <option value="<?= $key['no'] ?>" <?=  $biaya['iuran']['name'] == $key['name'] ? 'selected' : '' ?>>
                                                        <?= $key['noWithIndent'] ?> <?= $key['nameWithIndent'] ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                            <!-- <select name="kode_item4" id="kode_item4" class="form-control select2" style="width:100%;" disabled>
                                                <option value=""></option>
                                                <?php foreach ($item_details as $key) : ?>
                                                    <option value="<?= $key['kode_barang'] ?>"><?= $key['name'] ?></option>
                                                <?php endforeach ?>
                                            </select> -->
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" id="keterangan_biaya5" name="keterangan_biaya5" class="form-control" placeholder="Keterangan" value="<?php echo $biaya['iuran']['notes'] ?>" />
                                        </div>
                                        <div class="col-md-3" style="display:flex; align-items:center;">
                                            <input type="checkbox" id="alokasi_barang5" name="alokasi_barang5" value="1" <?= $biaya['iuran']['allocate'] ? 'checked' : '' ?> style="margin-right:6px;">
                                            Alokasikan ke Barang
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Total Order *</label>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="font-size:12px;">Rp.</span>
                                                <input type="text" id="dt14" name="dt14" class="form-control hitung" required value="<?php echo $dt14; ?>" readonly />

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group form-group-sm">
                                        <label class="control-label col-md-3">Catatan PO*</label>
                                        <div class="col-md-8">
                                            <textarea id="ket" name="ket" class="form-control" required><?php echo $ket; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Catatan apabila Pengajuan Ulang setelah verifikasi CEO -->
                            <?php if ($rsm['ceo_result'] == 1 && $rsm['revert_ceo'] == 0) { ?>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Catatan Pengajuan Ulang PO *</label>
                                            <div class="col-md-8">
                                                <textarea id="ket_resubmission" name="ket_resubmission" class="form-control" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-sm">
                                            <label><u>Keterangan Pengajuan Ulang Terakhir:</u></label>
                                            <p><?php echo $rsm['keterangan_resubmission']; ?></p>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>

                            <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />
                            <?php if ($rsm['revert_cfo'] == 1) { ?>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Catatan Pengembalian CFO</label>
                                            <div class="col-md-8">
                                                <div class="form-control" style="height:auto"><?php echo $revert_cfo; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if ($rsm['revert_ceo'] == 1) { ?>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Catatan Pengembalian CEO</label>
                                            <div class="col-md-8">
                                                <div class="form-control" style="height:auto"><?php echo $revert_ceo; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>



                            <?php if ($rsm['cfo_result'] == 1 && $rsm['revert_cfo'] == 0) { ?>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Catatan CFO *</label>
                                            <div class="col-md-8">
                                                <div class="form-control" style="height:auto"><i><?php echo $cfo . "<br>" . $cfo_pic . " - " . date("d/m/Y H:i:s", strtotime($cfo_tanggal)) . " WIB"; ?></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>

                            <?php if ($rsm['ceo_result'] == 1 && $rsm['revert_ceo'] == 0) { ?>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group form-group-sm">
                                            <label class="control-label col-md-3">Catatan CEO *</label>
                                            <div class="col-md-8">
                                                <div class="form-control" style="height:auto"><i><?php echo $ceo . "<br>" . $ceo_pic . " - " . date("d/m/Y H:i:s", strtotime($ceo_tanggal)) . " WIB"; ?></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>

                            <div style="margin-bottom:15px;">
                                <input type="hidden" name="act" value="<?php echo $action; ?>" />
                                <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                <button type="submit" class="btn btn-primary jarak-kanan <?= ($is_ceo == '1' && $revert == '0') ? 'hide' : '' ?>" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                                    <i class="fa fa-save jarak-kanan"></i> Simpan</button>

                                <a href="<?php echo BASE_URL_CLIENT . '/vendor-po-new.php'; ?>" class="btn btn-default" style="min-width:90px;">
                                    <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                            </div>

                            <p><small>* Wajib Diisi</small></p>

                        </div>
                    </div>
                </form>

                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>




    <div class="modal fade" id="validasi_vol_terima" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-md">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Informasi</h4>
                </div>
                <div class="modal-body vol_info"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            //Kondisi untuk Kode Accurate Iuran Migas
            var checkbox = $('#iuran_migas'); // Mengakses checkbox dengan ID
            if (checkbox.prop('checked')) { // Menggunakan .prop() untuk memeriksa status
                $('#kode_biaya2').removeAttr("disabled", true);
                $('#kode_biaya2').attr("required", true);
            } else {
                $('#kode_biaya2').attr("disabled", true);
                $('#kode_biaya2').removeAttr("required", true);
            }

            function customRound(num) {
                // Check if the number is negative
                if (num < 0) {
                    return Math.ceil(num - 0.5); // For negative numbers, round up
                }

                // For positive numbers
                const decimalPart = num - Math.floor(num); // Get the decimal part

                if (decimalPart < 0.5) {
                    return Math.floor(num); // Round down
                } else {
                    return Math.floor(num) + 1; // Round up
                }
            }

            $("#iuran_migas").on("ifChecked", function() {
                var plat = $('#kategori_plat').val();
                var kodeTax = $('#kd_tax').val();
                var pbbkb_tawar = $('#pbbkb_tawar').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var pbbkb = parseFloat($('#dt13').val()) || 0;
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;

                var kat_oa = $('#kategori_oa').val();

                //Iuran Migas langsung menghitung
                var iuran_migas = (((hargaDasar * 0.25) / 100) * volumePO);

                // var iuran_migas = (customRound((hargaDasar * 0.25) / 100) * volumePO);
                // var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;

                if (kat_oa == 1) {
                    var subTotal = (volumePO * hargaDasar) + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = ((volumePO * hargaDasar) * 0.3) / 100;
                    }
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                } else {
                    var subTotal = (volumePO * (hargaDasar + ongkos_angkut)) + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = ((volumePO * hargaDasar) * 0.3) / 100;
                    }
                    if (plat == "Hitam" || plat == "") {
                        var ppn11 = (11 * (volumePO * (hargaDasar + ongkos_angkut))) / 100;
                    } else {
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                    }
                }

                if (pbbkb_tawar != "") {
                    var total = volumePO * hargaDasar;
                    var hasil = (total * pbbkb_tawar) / 100;
                } else {
                    var hasil = pbbkb;
                }

                var totalOrder = (subTotal + ppn11 + pph + hasil);

                // // Tampilkan hasil di input Sub Total
                $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt13').val(hasil.toFixed(0));
                $('#nominal_iuran').val(iuran_migas.toFixed(0));
                $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#iuran_migas').attr("checked", true);
                $('#nominal_iuran').removeAttr("readonly", true);
                $('#kode_biaya2').removeAttr("disabled", true);
                $('#kode_biaya2').attr("required", true);
            }).on("ifUnchecked", function() {
                var plat = $('#kategori_plat').val();
                var kodeTax = $('#kd_tax').val();
                var pbbkb_tawar = $('#pbbkb_tawar').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var pbbkb = parseFloat($('#dt13').val()) || 0;
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;

                var kat_oa = $('#kategori_oa').val();

                 if (kat_oa == 1) {
                    var subTotal = volumePO * hargaDasar;
                    var dpp11_12 = (subTotal * 11) / 12;

                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100;
                    }
                    var ppn11 = (11 * subTotal) / 100;
                    var ppn12 = (12 * dpp11_12) / 100;
                } else {
                    var subTotal = volumePO * (hargaDasar + ongkos_angkut);
                    var dpp11_12 = (subTotal * 11) / 12;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = ((volumePO * hargaDasar) * 0.3) / 100;
                    }
                    if (plat == "Hitam" || plat == "") {
                        var ppn11 = (11 * subTotal) / 100;
                        var ppn12 = (12 * dpp11_12) / 100;
                    } else {
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                        var ppn12 = (12 * dpp11_12) / 100;
                    }
                }

                if (pbbkb_tawar != "") {
                    var total = volumePO * hargaDasar;
                    var hasil = (total * pbbkb_tawar) / 100;
                } else {
                    var hasil = pbbkb;
                }

                var iuran_migas = 0;

                var totalOrder = (subTotal + ppn11 + pph + hasil + iuran_migas);

                // // Tampilkan hasil di input Sub Total
                $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt13').val(hasil.toFixed(0));
                $('#nominal_iuran').val(iuran_migas.toFixed(0));
                $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#iuran_migas').attr("checked", false);
                $('#nominal_iuran').attr("readonly", true);
                $('#kode_biaya2').attr("disabled", true);
                $("#kode_biaya2").select2("val", "");
                $('#kode_biaya2').removeAttr("required", true);
            });

            $("#kategori_plat").change(function() {
                var val = $(this).val();
                var kodeTax = $('#kd_tax').val();
                var pbbkb_tawar = $('#pbbkb_tawar').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var pbbkb = parseFloat($('#dt13').val()) || 0;
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;
                var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;
                var kat_oa = $('#kategori_oa').val();
                var jenis_oa = $('#jenis_oa').val();

                if (kat_oa == 1) {
                    var subTotal = volumePO * hargaDasar + iuran_migas;
                    var pph = 0;
                    var dpp11_12 = (subTotal * 11) / 12;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100; // Pembulatan tanpa desimal
                    }

                    if (pbbkb_tawar != "") {
                        var total = volumePO * hargaDasar;
                        var hasil = (total * pbbkb_tawar) / 100;
                    } else {
                        var hasil = pbbkb;
                    }
                    // Hitung Sub Total
                    // var ppn11 = Math.round((11 * subTotal) / 100);
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                    var ppn12 = (12 * dpp11_12) / 100;

                    var totalOrder = (subTotal + ppn11 + pph + hasil);

                    // // Tampilkan hasil di input Sub Total
                    $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                    $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                    $('#dt13').val(hasil.toFixed(0));
                    // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                    $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    $('#dpp11_12').val(dpp11_12.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    $('#ppn12').val(ppn12.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    // $('#iuran_migas').attr("checked", false);
                    // $('.icheckbox_square-blue').removeClass("checked");
                } else {
                    if (val == "Hitam" || val == "") {
                        // var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                        // var pph = 0;
                        // if (kodeTax == 'EC') {
                        //     pph = ((volumePO * hargaDasar) * 0.3) / 100; // Pembulatan tanpa desimal
                        // }

                        // if (pbbkb_tawar != "") {
                        //     var total = volumePO * hargaDasar;
                        //     var hasil = (total * pbbkb_tawar) / 100;
                        // } else {
                        //     var hasil = pbbkb;
                        // }
                        // // Hitung Sub Total
                        // // var ppn11 = Math.round((11 * subTotal) / 100);
                        // var ppn11 = (11 * (volumePO * (hargaDasar + ongkos_angkut))) / 100;

                               if (jenis_oa == 1) {
                            var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                            var subTotal2 = volumePO * (hargaDasar + iuran_migas);
                            var dpp11_12 = (subTotal2 * 11) / 12;
                            var pph = 0;
                            if (kodeTax == 'EC') {
                                pph = ((volumePO * hargaDasar) * 0.3) / 100; // Pembulatan tanpa desimal
                            }

                            if (pbbkb_tawar != "") {
                                var total = volumePO * hargaDasar;
                                var hasil = (total * pbbkb_tawar) / 100;
                            } else {
                                var hasil = pbbkb;
                            }
                            // Hitung Sub Total
                            // var ppn11 = Math.round((11 * subTotal) / 100);
                            var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                            var ppn12 = (12 * dpp11_12) / 100;
                        } else {

                            var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                            var dpp11_12 = (subTotal * 11) / 12;
                            var pph = 0;
                            if (kodeTax == 'EC') {
                                pph = ((volumePO * hargaDasar) * 0.3) / 100; // Pembulatan tanpa desimal
                            }

                            if (pbbkb_tawar != "") {
                                var total = volumePO * hargaDasar;
                                var hasil = (total * pbbkb_tawar) / 100;
                            } else {
                                var hasil = pbbkb;
                            }
                            // Hitung Sub Total
                            // var ppn11 = Math.round((11 * subTotal) / 100);
                            var ppn11 = (11 * (volumePO * (hargaDasar + ongkos_angkut))) / 100;
                            var ppn12 = (12 * dpp11_12) / 100;
                        }
                        var totalOrder = (subTotal + ppn11 + pph + hasil);

                        // // Tampilkan hasil di input Sub Total
                        $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                        $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                        $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                        $('#dt13').val(hasil.toFixed(0));
                        // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                        $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                        $('#dpp11_12').val(dpp11_12.toFixed(4)); // Menampilkan dengan 2 angka desimal
                        $('#ppn12').val(ppn12.toFixed(4)); // Menampilkan dengan 2 angka desimal
                        // $('#iuran_migas').attr("checked", false);
                        // $('.icheckbox_square-blue').removeClass("checked");
                    } else {
                        $("#dt11").val("");
                        $("#dt12").val("");

                        var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                        var subTotal2 = volumePO * (hargaDasar + iuran_migas);
                        var dpp11_12 = (subTotal2 * 11) / 12;
                        var pph = 0;
                        if (kodeTax == 'EC') {
                            pph = ((volumePO * hargaDasar) * 0.3) / 100; // Pembulatan tanpa desimal
                        }
                        if (pbbkb_tawar != "") {
                            var total = volumePO * hargaDasar;
                            var hasil = (total * pbbkb_tawar) / 100;
                        } else {
                            var hasil = pbbkb;
                        }
                        // Hitung Sub Total
                        // var ppn11 = Math.round((11 * subTotal) / 100);
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                        var ppn12 = (12 * dpp11_12) / 100;
                        var totalOrder = (subTotal + ppn11 + pph + hasil);

                        // // Tampilkan hasil di input Sub Total
                        $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                        $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                        $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                        $('#dt13').val(hasil.toFixed(0));
                        // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                        $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                        $('#dpp11_12').val(dpp11_12.toFixed(4)); // Menampilkan dengan 2 angka desimal
                        $('#ppn12').val(ppn12.toFixed(4)); // Menampilkan dengan 2 angka desimal
                        // $('#iuran_migas').attr("checked", false);
                        // $('.icheckbox_square-blue').removeClass("checked");
                    }
                }
            })

            // Format angka dengan plugin number
            $(".hitung1").number(true, 0, ".", ",");
            $(".hitung").number(true, 2, ".", ",");

            $('.hitung, .hitung1').on('input', function() {
                // Ambil nilai Volume PO dan Harga Dasar
                var plat = $('#kategori_plat').val();
                var kodeTax = $('#kd_tax').val();
                var pbbkb_tawar = $('#pbbkb_tawar').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var pbbkb = parseFloat($('#dt13').val()) || 0;
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;
                var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;
                //biaya Accurate
                var jumlah_biaya = parseFloat($('#jumlah_biaya').val()) || 0;

                var kat_oa = $('#kategori_oa').val();
                var jenis_oa = $('#jenis_oa').val();

                if (kat_oa == 1) {
                    var subTotal = volumePO * hargaDasar + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100;
                    }
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                    //Hitung PPN 12% DPP 11/12
                    var dpp11_12 = ((volumePO * hargaDasar) * 11) / 12;
                    var ppn12 = (12 * dpp11_12) / 100;
                } else {
                    var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = ((volumePO * hargaDasar) * 0.3) / 100;
                    }
                    if (plat == "Hitam" || plat == "") {
                        var ppn11 = (11 * (volumePO * (hargaDasar + ongkos_angkut))) / 100;
                        //Hitung PPN 12% DPP 11/12
                        var dpp11_12 = (volumePO * (hargaDasar + ongkos_angkut) * 11) / 12;
                        var ppn12 = (12 * dpp11_12) / 100;
                    } else {
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                        //Hitung PPN 12% DPP 11/12
                        var dpp11_12 = ((volumePO * hargaDasar) * 11) / 12;
                        var ppn12 = (dpp11_12 * 12) / 100;
                    }
                }

                if (pbbkb_tawar != "") {
                    var total = volumePO * hargaDasar;
                    var hasil = (total * pbbkb_tawar) / 100;
                } else {
                    var hasil = pbbkb;
                }
                var iuran_migas = 0;

                // Hitung Sub Total
                // var ppn11 = Math.round((11 * subTotal) / 100);

                var totalOrder = (subTotal + ppn11 + pph + hasil + jumlah_biaya);

                // // Tampilkan hasil di input Sub Total
                $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt13').val(hasil.toFixed(0));
                $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal

                //Hitung untuk PPN DPP 11/12
                $('#dpp11_12').val(dpp11_12.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#ppn12').val(ppn12.toFixed(4)); // Menampilkan dengan 2 angka desimal
                // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                // $('#iuran_migas').attr("checked", false);
                // $('.icheckbox_square-blue').removeClass("checked");
            });

            var formValidasiCfg = {
                submitHandler: function(form) {
                    if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
                        swal.fire({
                            icon: "warning",
                            width: '350px',
                            allowOutsideClick: false,
                            html: '<p style="font-size:14px; font-family:arial;">Kolom [Ongkos Angkut] pada tabel rincian harga belum diisi</p>'
                        });
                    } else if ($("#kd_tax").val() == 'EC' && $("#pphnya").val() == '') {
                        swal.fire({
                            icon: "warning",
                            width: '350px',
                            allowOutsideClick: false,
                            html: '<p style="font-size:14px; font-family:arial;">Kolom [PPH 22] belum diisi</p>'
                        });
                    } else if ($("#terms").val() == 'NET' && $("#terms_day").val() == '') {
                        swal.fire({
                            icon: "warning",
                            width: '350px',
                            allowOutsideClick: false,
                            html: '<p style="font-size:14px; font-family:arial;">Kolom [Hari untuk terms NET] belum diisi</p>'
                        });
                    } else if ($("#iuran_migas").is(":checked") && $("#nominal_iuran").val() == 0) {
                        swal.fire({
                            icon: "warning",
                            width: '350px',
                            allowOutsideClick: false,
                            html: '<p style="font-size:14px; font-family:arial;">Nominal iuran migas belum di isi</p>'
                        });
                    } else {
                        Swal.fire({
                            title: "Yakin Simpan?",
                            showCancelButton: true,
                            confirmButtonText: "Simpan",
                            denyButtonText: 'Batal'
                        }).then((result) => {
                            // console.log("Dasdasd")
                            if (result.isConfirmed) {
                                $("body").addClass("loading");
                                $.ajax({
                                    type: 'POST',
                                    url: base_url + "/web/action/vendor-po-new.php",
                                    data: {
                                        act: 'cek',
                                        q1: $("input[name='idr']").val(),
                                        q2: $("#dt2").val()
                                    },
                                    cache: false,
                                    dataType: 'json',
                                    success: function(data) {
                                        if (!data.hasil) {
                                            $("body").removeClass("loading");
                                            swal.fire({
                                                icon: "warning",
                                                width: '350px',
                                                allowOutsideClick: false,
                                                html: '<p style="font-size:14px; font-family:arial;">' + data.pesan + '</p>'
                                            });
                                        } else {
                                            form.submit();
                                        }
                                    }
                                });
                            } else if (result.isDenied) {
                                Swal.fire("Batal simpan", "", "info");
                            }
                        });
                    }
                }
            };
            $("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

            $("#kd_tax").on("change", function() {
                var plat = $('#kategori_plat').val();
                let nilai = $(this).val();
                var kategori_oa = $('#kategori_oa').val();
                var kodeTax = $('#kd_tax').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;
                var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;
                var pbbkb = $("#pbbkb_tawar").val();

                if (kategori_oa == 1) {
                    var subTotal = volumePO * hargaDasar + iuran_migas;
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                    //Hitung PPN 12% DPP 11/12
                    var dpp11_12 = ((volumePO * hargaDasar) * 11) / 12;
                    var ppn12 = (12 * dpp11_12) / 100;
                } else {
                    var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                    if (plat == "Hitam" || plat == "") {
                        var ppn11 = (11 * (volumePO * (hargaDasar + ongkos_angkut))) / 100;
                        //Hitung PPN 12% DPP 11/12
                        var dpp11_12 = ((volumePO * (hargaDasar + ongkos_angkut)) * 11) / 12;
                        var ppn12 = (12 * dpp11_12) / 100;
                    } else {
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                        //Hitung PPN 12% DPP 11/12
                        var dpp11_12 = ((volumePO * hargaDasar) * 11) / 12;
                        var ppn12 = (12 * dpp11_12) / 100;
                    }
                }

                var pph = 0;
                if (kodeTax == 'EC') {
                    pph = ((volumePO * hargaDasar) * 0.3) / 100; // Pembulatan tanpa desimal
                    //Kondisi untuk Kode Accurate 
                    $("#kode_item3").removeAttr("disabled", true);
                    $("#kode_item3").attr("required", true);
                } else {
                    pph = 0;
                    //Kondisi untuk Kode Accurate 
                    $("#kode_item3").attr("disabled", true);
                    $("#kode_item3").removeAttr("required", true);
                    $("#kode_item3").select2("val", "");
                }

                var iuran_migas = 0;

                var total = volumePO * hargaDasar;
                var hasil = (total * pbbkb) / 100;

                var totalOrder = (subTotal + ppn11 + pph + hasil + iuran_migas);

                $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#dt12').val(pph.toFixed(0));
                $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                // // $('#iuran_migas').attr("checked", false);
                // // $('.icheckbox_square-blue').removeClass("checked");
            });

            // $("#kd_tax").on("change", function() {
            //     let nilai = $(this).val();
            //     if (nilai == 'E') {
            //         $(".form-group:has(#dt12)").hide(); // Menghilangkan semua elemen terkait PPH 22
            //     } else {
            //         $(".form-group:has(#dt12)").show(); // Menampilkan kembali semua elemen terkait PPH 22
            //         $("#dt12").attr("readonly", "readonly").val(""); // Menambah readonly dan menghapus nilai
            //     }
            // });
            $("#terms").on("change", function() {
                let nilai = $(this).val();
                if (nilai == 'NET') $("#terms_day").removeAttr("readonly");
                else $("#terms_day").attr("readonly", "readonly");
            });

            //Kondisi untuk Kode Accurate 
            var pbbkb_edit = $("#pbbkb_tawar").val();
            if (pbbkb_edit == 0 || pbbkb_edit == "") {
                $("#kode_biaya1").attr('disabled', true);
                $("#kode_biaya1").removeAttr('required', true);
            } else {
                $("#kode_biaya1").removeAttr('disabled', true);
                $("#kode_biaya1").attr('required', true);
            }

            var kodeTax = $('#kd_tax').val();
            if (kodeTax == 'EC') {
                $("#kode_item3").removeAttr("disabled", true);
                $("#kode_item3").attr("required", true);
            } else {
                $("#kode_item3").attr("disabled", true);
                $("#kode_item3").removeAttr("required", true);
                $("#kode_item3").select2("val", "");
            }

            var kategoriOA = $('#kategori_oa').val();
            if (kategoriOA == 1) {
                 $("#row_kode_item").addClass("hide");
            } else {
                  $("#row_kode_item").removeClass("hide");
            }
            // End kondisi
            $("#pbbkb_tawar").change(function() {
                var plat = $('#kategori_plat').val();
                var kategori_oa = $('#kategori_oa').val();
                var kodeTax = $('#kd_tax').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var pbbkb = $(this).val();
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;
                var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;

                if (kategori_oa == 1) {
                    var subTotal = volumePO * hargaDasar + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100; // Pembulatan tanpa desimal
                    }
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                    var dpp11_12 = ((volumePO * hargaDasar) * 11) / 12;
                    var ppn12 = (12 * dpp11_12) / 100;
                } else {
                    var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = ((volumePO * hargaDasar) * 0.3) / 100; // Pembulatan tanpa desimal
                    }
                    if (plat == "Hitam" || plat == "") {
                        var ppn11 = (11 * (volumePO * (hargaDasar + ongkos_angkut))) / 100;
                        //Hitung PPN 12% DPP 11/12
                        var dpp11_12 = ((volumePO * (hargaDasar + ongkos_angkut)) * 11) / 12;
                        var ppn12 = (12 * dpp11_12) / 100;
                    } else {
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                        //Hitung PPN 12% DPP 11/12
                        var dpp11_12 = ((volumePO * hargaDasar) * 11) / 12;
                        var ppn12 = (12 * dpp11_12) / 100;
                    }
                }

                //Apabila PBBKB 0 kode biaya Accurate tidak muncul
                if (pbbkb == 0) {
                    $("#kode_biaya1").val("").trigger("change");
                    $("#kode_biaya1").attr('disabled', true);
                    $("#kode_biaya1").removeAttr('required', true);
                } else {
                    $("#kode_biaya1").removeAttr('disabled', true);
                    $("#kode_biaya1").attr('required', true);
                }
                var iuran_migas = 0;
                var total = volumePO * hargaDasar;
                var hasil = (total * pbbkb) / 100;

                var totalOrder = (subTotal + ppn11 + pph + hasil + iuran_migas);

                $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#dt13').val(hasil.toFixed(0));
                $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                // alert(hasil)
                // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                // $('#iuran_migas').attr("checked", false);
                // $('.icheckbox_square-blue').removeClass("checked");
            })

            $("#kategori_oa").change(function() {
                var val = $(this).val();
                  var jenis_oa = $('#jenis_oa').val();

                if (val == 1) {
                    var kodeTax = $('#kd_tax').val();
                    var volumePO = parseFloat($('#dt10').val()) || 0;
                    var hargaDasar = parseFloat($('#dt8').val()) || 0;
                    var pbbkb = parseFloat($('#dt13').val()) || 0;
                    var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;

                    var subTotal = volumePO * hargaDasar + iuran_migas;
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                    var dpp11_12 = ((volumePO * hargaDasar) * 11) / 12;
                    var ppn12 = (12 * dpp11_12) / 100;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100; // Pembulatan tanpa desimal
                    }

                    var totalOrder = (subTotal + ppn11 + pph + pbbkb);

                    $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                    $('#dpp11_12').val(dpp11_12.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    $('#ppn12').val(ppn12.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal

                    $("#ongkos_angkut").val(0);
                    $("#row_oa").addClass("hide");
                    $("#row-plat").addClass("hide");
                    $("#kategori_plat").val(null).trigger("change");
                    $("#ongkos_angkut").removeAttr("required", true);
                    $("#kategori_plat").removeAttr("required", true);

                    //Kode Accurate kondisi
                    $("#row_jenis_oa").addClass("hide");
                    $("#row_biaya_oa").addClass("hide");
                    $("#row_kode_item").addClass("hide");
                    $("#jenis_oa").val(null).trigger("change");
                    $("#kode_item2").val(null).trigger("change");
                    $("#biaya_lain").val(null).trigger("change");
                    $("#biaya_oa").val(null).trigger("change");
                    $("#jenis_oa").removeAttr("required", true);
                    $("#kode_item2").removeAttr("required", true);
                    // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                    // $('#iuran_migas').attr("checked", false);
                    // $('.icheckbox_square-blue').removeClass("checked");
                } else {
                    var kodeTax = $('#kd_tax').val();
                    var volumePO = parseFloat($('#dt10').val()) || 0;
                    var hargaDasar = parseFloat($('#dt8').val()) || 0;
                    var pbbkb = parseFloat($('#dt13').val()) || 0;
                    var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;

                    var subTotal = volumePO * hargaDasar + iuran_migas;
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100; // Pembulatan tanpa desimal
                    }

                    var totalOrder = (subTotal + ppn11 + pph + pbbkb);

                    $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                    $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                    $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal

                    $("#ongkos_angkut").val(0);
                    $("#row_oa").addClass("hide");
                    $("#row-plat").addClass("hide");
                    $("#kategori_plat").val(null).trigger("change");
                    $("#ongkos_angkut").removeAttr("required", true);
                    $("#kategori_plat").removeAttr("required", true);

                    $("#row_oa").removeClass("hide");
                    $("#row-plat").removeClass("hide");
                    $("#ongkos_angkut").prop("required", true);
                    $("#kategori_plat").prop("required", true);

                    $("#kode_item2").removeAttr("required", true);
                    $("#row_jenis_oa").removeClass("hide");
                    $("#row_kode_item").removeClass("hide");
                    $("#kode_item2").prop("required", true);

                    // var iuran_migas = 0;
                    // $('#nominal_iuran').val(iuran_migas.toFixed(0));
                    // $('#iuran_migas').attr("checked", false);
                    // $('.icheckbox_square-blue').removeClass("checked");
                }
            })

            //Untuk jenis OA
            $("#jenis_oa").change(function() {
                var val = $(this).val();
                var plat = $('#kategori_plat').val();
                var kodeTax = $('#kd_tax').val();
                var pbbkb_tawar = $('#pbbkb_tawar').val();
                var volumePO = parseFloat($('#dt10').val()) || 0;
                var hargaDasar = parseFloat($('#dt8').val()) || 0;
                var pbbkb = parseFloat($('#dt13').val()) || 0;
                var ongkos_angkut = parseFloat($('#ongkos_angkut').val()) || 0;
                var iuran_migas = parseFloat($('#nominal_iuran').val()) || 0;
                if (val == 1) {
                    var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = ((volumePO * hargaDasar) * 0.3) / 100;
                    }
                    var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                    var dpp11_12 = ((volumePO * hargaDasar) * 11) / 12;
                    var ppn12 = (dpp11_12 * 12) / 100;

                    $("#row_kode_item").addClass("hide");
                    $("#row_biaya_oa").removeClass("hide");
                    $("#biaya_oa").prop("required", true);
                    $("#biaya_lain").prop("required", true);
                    $("#jumlah_biaya").prop("required", true);

                    $("#kode_item2").removeAttr("required", true);
                    $("#kode_item2").val(null).trigger("change");
                    $("#keterangan_item2").val(null).trigger("change");
                } else if (val == '0') {
                    var subTotal = volumePO * (hargaDasar + ongkos_angkut) + iuran_migas;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = ((volumePO * hargaDasar) * 0.3) / 100;
                    }
                    if (plat == "Hitam" || plat == "") {
                        var ppn11 = (11 * (volumePO * (hargaDasar + ongkos_angkut))) / 100;
                        var dpp11_12 = (volumePO * (hargaDasar + ongkos_angkut) * 11) / 12;
                        var ppn12 = (12 * dpp11_12) / 100;
                    } else {
                        var ppn11 = ((hargaDasar * volumePO) * 11) / 100;
                        var dpp11_12 = ((volumePO * hargaDasar) * 11) / 12;
                        var ppn12 = (dpp11_12 * 12) / 100;
                    }

                    $("#row_biaya_oa").addClass("hide");
                    $("#row_kode_item").removeClass("hide");
                    $("#kode_item2").prop("required", true);

                    $("#biaya_lain").removeAttr("required", true);
                    $("#biaya_lain").val(null).trigger("change");
                    $("#biaya_oa").removeAttr("required", true);
                    $("#biaya_oa").val(null).trigger("change");
                    $("#jumlah_biaya").removeAttr("required", true);
                    $("#jumlah_biaya").val(null).trigger("change");

                    $("#keterangan_biaya1").val(null).trigger("change");
                    $("#keterangan_biaya2").val(null).trigger("change");
                    $("#jumlah_biaya").val(null).trigger("change");
                }else{

                    var subTotal = volumePO * hargaDasar + iuran_migas;
                    var ppn11 = (11 * (volumePO * hargaDasar)) / 100;
                    var dpp11_12 = ((volumePO * hargaDasar) * 11) / 12;
                    var ppn12 = (12 * dpp11_12) / 100;
                    var pph = 0;
                    if (kodeTax == 'EC') {
                        pph = (subTotal * 0.3) / 100; // Pembulatan tanpa desimal
                    }

                    var totalOrder = (subTotal + ppn11 + pph + pbbkb);

                    $("#row_kode_item").addClass("hide");
                    $("#row_biaya_oa").addClass("hide");
                    $("#biaya_oa").removeAttr("required", true);
                    $("#biaya_lain").removeAttr("required", true);
                    $("#jumlah_biaya").removeAttr("required", true);

                    $("#kode_item2").removeAttr("required", true);
                    $("#kode_item2").val(null).trigger("change");
                    $("#keterangan_item2").val(null).trigger("change");
                }

                if (pbbkb_tawar != "") {
                    var total = volumePO * hargaDasar;
                    var hasil = (total * pbbkb_tawar) / 100;
                } else {
                    var hasil = pbbkb;
                }
                var iuran_migas = 0;

                var totalOrder = (subTotal + ppn11 + pph + hasil);

                $('#dt9').val(subTotal.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#dt11').val(ppn11.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt12').val(pph.toFixed(0)); // Menampilkan dengan 2 angka desimal
                $('#dt13').val(hasil.toFixed(0));
                $('#dt14').val(totalOrder.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#dpp11_12').val(dpp11_12.toFixed(4)); // Menampilkan dengan 2 angka desimal
                $('#ppn12').val(ppn12.toFixed(4)); // Menampilkan dengan 2 angka desimal
            })

        });
    </script>
</body>

</html>