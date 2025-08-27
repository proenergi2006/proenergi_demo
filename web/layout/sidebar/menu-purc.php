<?php
//
$menuPr         = array("purchase-request", "purchase-request-detail", "purchase-request-detail-all");
$menuForecast   = array("forecast");
$menuVenPo      = array("vendor-po-new", "vendor-po-new-add");
$menuVenPoCs      = array("vendor-po-new-crushed-stone", "vendor-po-new-crushed-stone-add");
$menuBlenPO     = array("po-blending", "po-blending-add");
$mnFolder01     = array_merge($menuPr, $menuForecast, $menuVenPo, $menuVenPoCs, $menuBlenPO);

$mnVenInv1       = array("vendor-inven", "vendor-inven-add");
$mnVenInv2       = array("vendor-inven-terminal-new", "vendor-inven-terminal-new-list", "vendor-inven-terminal-new-add");
$mnVenInv3         = array("adjustment-stock", "adjustment-stock-add");
$mnVenInv4       = array("inven-rssp");
$mnVenInv7    = array("sisa-stock");
$mnFolder02      = array_merge($mnVenInv1, $mnVenInv2, $mnVenInv4, $mnVenInv3, $mnVenInv7);

$mnM1 = array("m-penawaran");
$mnM2 = array("m-total-customer");
$mnM3 = array("m-customer");
$mnM4 = array("m-realisasi-order");
$mnM5 = array("m-po-customer");
$mnM6 = array("m-sales-performance");
$mnL1 = array("l-losses");
$mnL2 = array("master-ongkos-angkut");
$mnL3 = array("l-volume-angkut");
$mnL4 = array("l-lead-time");
$mnL5 = array("l-loading-order");
$mnL6 = array("l-schedule-by-date");
$mnF1 = array("f-schedule-payment");
$mnF2 = array("f-refund");
$mnC1 = array("c-pembelian");
$mnC2 = array("c-area-performance");
$mnC3 = array("c-margin");
$mnC4 = array("c-harga-market");
$mnC5 = array("c-rekap-loaded");
$mnC7 = array("c-history-stock");
$mnC10 = array("c-history-stock-operational");
$mnReport = array_merge($mnM1, $mnM2, $mnM3, $mnM4, $mnM5, $mnM6, $mnL1, $mnL2, $mnL3, $mnL4, $mnL5, $mnF1, $mnF2, $mnC1, $mnC2, $mnC3, $mnC4, $mnC5, $mnC7, $mnC10);

$mnRef1     = array("master-group-cabang", "add-master-group-cabang");
$mnRef2     = array("master-cabang", "add-master-cabang", "detil-master-cabang");
$mnRef3        = array("master-terminal", "add-master-terminal", "detil-master-terminal");
$mnRef4_1     = array("master-transportir", "add-master-transportir", "detil-master-transportir");
$mnRef4_2    = array("master-transportir-sopir", "add-master-transportir-sopir", "detil-master-transportir-sopir");
$mnRef4_3    = array("master-transportir-mobil", "add-master-transportir-mobil", "detil-master-transportir-mobil");
$mnRef4_4     = array("gps-truck");
$mnRef4     = array_merge($mnRef4_1, $mnRef4_2, $mnRef4_3, $mnRef4_4);
$mnRef5_1     = array("master-ongkos-angkut", "add-master-ongkos-angkut");
$mnRef5_2    = array("master-oa-kapal", "add-master-oa-kapal");
$mnRef5_3    = array("master-wilayah-angkut", "add-master-wilayah-angkut");
$mnRef5_4    = array("master-volume-angkut", "add-master-volume-angkut");
$mnRef5     = array_merge($mnRef5_1, $mnRef5_2, $mnRef5_3, $mnRef5_4);
$mnRef6        = array("master-pbbkb", "add-master-pbbkb");
$mnRef7     = array("master-harga-minyak", "add-master-harga-minyak", "detil-master-harga-minyak");
$mnRef8     = array("master-approval-harga", "list-approval-harga", "add-master-harga-minyak", "detil-master-harga-minyak");
$mnRef9        = array("master-produk", "add-master-produk", "detil-master-produk");
$mnRef10    = array("master-vendor", "add-master-vendor", "detil-master-vendor");
$mnRef11    = array("master-harga-tebus", "add-master-harga-tebus", "detil-master-harga-tebus");
$mnRef12    = array("master-area", "add-master-area", "detil-master-area");
$mnRef13    = array("master-harga-pertamina", "add-master-harga-pertamina", "detil-master-harga-pertamina");
$mnRef14     = array("attach-harga-minyak", "add-attach-harga-minyak", "detil-attach-harga-minyak");
$mnMktKrm     = array("pengiriman-list-view");
$mnVerifRqPr  = array("verifikasi-request-pr", "verifikasi-request-detail-pr");
$mnRef      = array_merge($mnRef1, $mnRef2, $mnRef3, $mnRef4, $mnRef5, $mnRef6, $mnRef7, $mnRef8, $mnRef9, $mnRef10, $mnRef11, $mnRef12, $mnRef13, $mnRef14);

?>

<li class="<?php echo (in_array($menuKey, $mnFolder01)) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $jumBadgeRole3 = 0;
    $sqlBadge5  = "select count(id_pr) as jum from pro_pr where 1=1";
    $sqlBadge5 .= " and purchasing_result = 0 and disposisi_pr = 3 and tanggal_pr >= '2023-09-01'";
    $jumBadge5  = $con->getOne($sqlBadge5);
    $jumBadgeRole3 += $jumBadge5;

    $jumBadgeRole4 = 0;
    $sqlBadge8  = "select count(id_master) as jum from new_pro_inventory_vendor_po where 1=1";
    $sqlBadge8 .= " and disposisi_po = 3";
    $jumBadge8  = $con->getOne($sqlBadge8);
    $jumBadgeRole4 += $jumBadge8;



    ?>
    <a>
        <i class="fa fa-folder"></i>
        <span>Verifikasi</span>
        <span id="menubadgerole3" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($jumBadgeRole3 > 0) ? $jumBadgeRole3 : ''; ?></span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $menuForecast)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/forecast.php"; ?>"><i class="fa"></i> <span>Forecast Cabang</span>
            </a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuVenPo)) ? 'active' : ''; ?>">
            <!-- <a href="<?php echo BASE_URL_CLIENT . "/vendor-po.php"; ?>"><i class="fa"></i> <span>PO Suplier</span></a> -->
            <a href="<?php echo BASE_URL_CLIENT . "/vendor-po-new.php"; ?>"><i class="fa"></i> <span>PO Suplier Fuel</span></a>
            <span id="menubadge8" class="label label-primary pull-right"><?php echo ($jumBadge8 > 0) ? $jumBadge8 : ''; ?></span></a>

        </li>

        <li class="<?php echo (in_array($menuKey, $menuVenPoCs)) ? 'active' : ''; ?>">

            <a href="<?php echo BASE_URL_CLIENT . "/vendor-po-new-crushed-stone.php"; ?>"><i class="fa"></i> <span>PO Suplier Crushed Stone</span></a>
            <span id="menubadge8" class="label label-primary pull-right"><?php echo ($jumBadge9 > 0) ? $jumBadge9 : ''; ?></span></a>

        </li>
        <li class="<?php echo (in_array($menuKey, $menuPr)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/purchase-request.php"; ?>"><i class="fa"></i> <span>Delivery Request</span>
                <span id="menubadge5" class="label label-primary pull-right"><?php echo ($jumBadge5 > 0) ? $jumBadge5 : ''; ?></span></a>
        </li>

        <li class="<?php echo (in_array($menuKey, $menuBlenPO)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/po-blending.php"; ?>"><i class="fa"></i> <span>Blending</span></a>

        </li>
    </ul>
</li>

<li class="<?php echo (in_array($menuKey, $mnFolder02)) ? 'treeview active' : 'treeview'; ?>">
    <a><i class="fa fa-folder"></i> <span>Inventory</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $mnVenInv4)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/inven-rssp.php"; ?>"><i class="fa"></i> <span>Mutasi Stock</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnVenInv2)) ? 'active' : ''; ?>">
            <!-- <a href="<?php echo BASE_URL_CLIENT . "/vendor-inven-terminal.php"; ?>"><i class="fa"></i> <span>Inventory By Depot</span></a> -->
            <a href="<?php echo BASE_URL_CLIENT . "/vendor-inven-terminal-new.php"; ?>"><i class="fa"></i> <span>Inventory By Depot</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnVenInv1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/vendor-inven.php"; ?>"><i class="fa"></i> <span>Inventory By Vendor</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnVenInv3)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/adjustment-stock.php"; ?>"><i class="fa"></i> <span>Adjustment Stock</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnVenInv7)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/sisa-stock.php"; ?>"><i class="fa"></i> <span>Sisa Stock</span></a>
        </li>

    </ul>
</li>

<li class="<?php echo (in_array($menuKey, $mnMktKrm)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/pengiriman-list-view.php"; ?>"><i class="fa fa-file-alt"></i> <span>List Pengiriman</span></a>
</li>
<li class="<?php echo (in_array($menuKey, $mnVerifRqPr)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-request-pr.php"; ?>"><i class="fa fa-file-alt"></i> <span>Verifikasi Request</span></a>

</li>

<li class="<?php echo (in_array($menuKey, array_merge(['reservasi-ruangan'], ['peminjaman-mobil'], ['peminjaman-zoom']))) ? 'treeview active' : 'treeview'; ?>">
    <a>
        <i class="fa fa-folder"></i> <span>Request</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, ['reservasi-ruangan'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/reservasi-ruangan.php"; ?>"><i class="fa"></i> <span>Reservasi Ruang Meeting</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['peminjaman-mobil'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/peminjaman-mobil.php"; ?>"><i class="fa"></i> <span>Peminjaman Mobil Opr.</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['peminjaman-zoom'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/peminjaman-zoom.php"; ?>"><i class="fa"></i> <span>Peminjaman Akun Zoom</span></a>
        </li>
    </ul>
</li>

<li class="<?php echo (in_array($menuKey, $mnReport)) ? 'treeview active' : 'treeview'; ?>">
    <a><i class="fa fa-folder"></i> <span>Report</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $mnM3)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/m-customer.php"; ?>"><i class="fa"></i> <span>Customer</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnM4)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/m-realisasi-order.php"; ?>"><i class="fa"></i> <span>Realisasi Order</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnM5)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/m-po-customer.php"; ?>"><i class="fa"></i> <span>PO Customer</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnM6)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/m-sales-performance.php"; ?>"><i class="fa"></i> <span>Sales Performance</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnL1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/l-losses.php"; ?>"><i class="fa"></i> <span>Losses</span></a>
        </li>
        <li>
            <a href="<?php echo BASE_URL_CLIENT . "/master-ongkos-angkut.php?" . paramEncrypt("is_report=1"); ?>"><i class="fa"></i> <span>Rekap OA</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnL3)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/l-volume-angkut.php"; ?>"><i class="fa"></i> <span>Volume Angkut Transportir</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnL4)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/l-lead-time.php"; ?>"><i class="fa"></i> <span>Lead Time</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnL5)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/l-loading-order.php"; ?>"><i class="fa"></i> <span>Loading Order</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnL6)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/l-schedule-by-date.php"; ?>"><i class="fa"></i> <span>Schedule By Date</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnC1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/c-pembelian.php"; ?>"><i class="fa"></i> <span>Pembelian</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnC2)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/c-area-performance.php"; ?>"><i class="fa"></i> <span>Area Performance</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnC3)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/c-margin.php"; ?>"><i class="fa"></i> <span>Margin</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnC4)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/c-harga-market.php"; ?>"><i class="fa"></i> <span>Tren Harga Market</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnC5)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/c-rekap-loaded.php"; ?>"><i class="fa"></i> <span>Rekap Loaded Truck</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnC8)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/c-rekap-loaded-kapal.php"; ?>"><i class="fa"></i> <span>Rekap Loaded Kapal</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnC7)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/c-history-stock.php"; ?>"><i class="fa"></i> <span>History Stock Truck</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnC10)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/c-history-stock-operational.php"; ?>"><i class="fa"></i> <span>History Stock Operational</span></a>
        </li>
    </ul>
</li>

<li class="<?php echo (in_array($menuKey, $mnRef)) ? 'treeview active' : 'treeview'; ?>">
    <a>
        <i class="fa fa-folder"></i>
        <span>Referensi Data</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $mnRef3)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-terminal.php"; ?>"><i class="fa"></i> <span>Terminal</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef10)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-vendor.php"; ?>"><i class="fa"></i> <span>Vendor</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef7)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-harga-minyak.php"; ?>"><i class="fa"></i> <span>Daftar Harga Jual</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef14)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/attach-harga-minyak.php"; ?>"><i class="fa"></i> <span>Attachment Harga Jual</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef11)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-harga-tebus.php"; ?>"><i class="fa"></i> <span>Harga Tebus</span></a>
        </li>

    </ul>
</li>