<li class="<?php echo (in_array($menuKey, $mnVer)) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $jumBadgeRole3 = 0;

    $sqlBadge2    = "select count(b.id_verification) as jum from pro_customer a 
						join pro_customer_verification b on a.id_customer = b.id_customer and b.is_evaluated = 1 and b.is_reviewed = 1 and b.is_approved = 0 
						join acl_user c on a.id_marketing = c.id_user 
						where 1=1";
    $sqlBadge2 .= " and b.logistik_result = 0 and b.disposisi_result = 1 and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
    $jumBadge2     = $con->getOne($sqlBadge2);
    $jumBadgeRole3 += $jumBadge2;

    $sqlBadge17    = "select count(b.id_evaluasi) as jum from pro_customer a join pro_customer_evaluasi b on a.id_customer = b.id_customer 
                    join pro_master_cabang c on a.id_group = c.id_group_cabang join acl_user d on a.id_marketing = d.id_user where 1=1";
    $varBadge17     = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']);
    $sqlBadge17 .= " and b.disposisi_result = 1 and b.logistik_result = 0 and c.id_master = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
    $jumBadge17     = $con->getOne($sqlBadge17);
    $jumBadgeRole3 += $jumBadge17;

    $sqlBadge3    = "select count(a.id_lcr) as jum from pro_customer_lcr a join pro_customer b on a.id_customer = b.id_customer 
                        where b.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
    $sqlBadge3 .= " and a.logistik_result = 0 and a.flag_disposisi > 0";
    $jumBadge3     = $con->getOne($sqlBadge3);
    $jumBadgeRole3 += $jumBadge3;

    $sqlBadge3a = "select count(*) as jum from pro_po where ada_selisih = 2 and f_proses_selisih = 0 and po_approved = 1";
    $jumBadge3a = $con->getOne($sqlBadge3a);
    $jumBadgeRole3 += $jumBadge3a;
    ?>
    <a>
        <i class="fa fa-folder"></i>
        <span>Verifikasi</span>
        <span id="menubadgerole3" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($jumBadgeRole3 > 0) ? $jumBadgeRole3 : ''; ?></span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $menuVerCust)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-data-customer.php"; ?>"><i class="fa"></i> <span>Verifikasi Data Customer</span>
                <span id="menubadge2" class="label label-primary pull-right"><?php echo ($jumBadge2 > 0) ? $jumBadge2 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuVevCust)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/evaluasi-data-customer.php"; ?>"><i class="fa"></i> <span>Evaluasi Customer</span>
                <span id="menubadge17" class="label label-primary pull-right"><?php echo ($jumBadge17 > 0) ? $jumBadge17 : ''; ?></span></a>
        </li>
        <!-- <li class="<?php echo (in_array($menuKey, $menuVerLcr)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-lcr.php"; ?>"><i class="fa"></i> <span>Verifikasi LCR</span>
            <span id="menubadge3" class="label label-primary pull-right"><?php echo ($jumBadge3 > 0) ? $jumBadge3 : ''; ?></span></a>
        </li> -->
        <li class="<?php echo (in_array($menuKey, $menuVerPoa)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-oa.php"; ?>"><i class="fa"></i> <span>Verifikasi Selisih OA</span>
                <span id="menubadge3a" class="label label-primary pull-right"><?php echo ($jumBadge3a > 0) ? $jumBadge3a : ''; ?></span></a>
        </li>
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, array_merge($menuPoLog, $menuPr, $menuPo, $menuPoDs, $menuLgPl, $mnTrack, $menuPoDk, $menuMnSg))) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $jumBadgeDelivery = 0;
    $sqlBadge9    = "select count(id_po) as jum from pro_po where disposisi_po = 1 and is_new = 1 and po_approved = 0";
    $jumBadge9     = $con->getOne($sqlBadge9);
    $jumBadgeDelivery += $jumBadge9;
    $sqlBadge20    = "select count(*) as jum from pro_po_ds where is_submitted = 0 and id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
    $jumBadge20 = $con->getOne($sqlBadge20);
    $jumBadgeDelivery += $jumBadge20;
    $sqlBadge12    = "select count(*) as jum from pro_po_customer_plan a join pro_customer_lcr c on a.id_lcr = c.id_lcr where a.status_plan = 0 and a.is_approved = 1 and 
            c.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
    $jumBadge12 = $con->getOne($sqlBadge12);
    $jumBadgeDelivery += $jumBadge12;
    $sqlBadge5    = "select count(id_pr) as jum from pro_pr where 1=1";
    $sqlBadge5 .= " and logistik_result = 1 and disposisi_pr = 5 and id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
    $jumBadge5     = $con->getOne($sqlBadge5);
    $jumBadgeDelivery += $jumBadge5;
    ?>
    <a>
        <i class="fa fa-folder"></i> <span>Delivery</span>
        <span id="menubadgedelivery" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($jumBadgeDelivery > 0) ? $jumBadgeDelivery : ''; ?></span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $menuPoLog)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/po-customer-logistik.php"; ?>"><i class="fa"></i> <span>Delivery Plan</span>
                <span id="menubadge12" class="label label-primary pull-right"><?php echo ($jumBadge12 > 0) ? $jumBadge12 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuPr)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/purchase-request.php"; ?>"><i class="fa"></i> <span>Delivery Request</span>
                <span id="menubadge5" class="label label-primary pull-right"><?php echo ($jumBadge5 > 0) ? $jumBadge5 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuPo)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/purchase-order.php"; ?>"><i class="fa"></i> <span>Purchase Order Transportir</span>
                <span id="menubadge9" class="label label-primary pull-right"><?php echo ($jumBadge9 > 0) ? $jumBadge9 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuPoDs)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/delivery-loading.php"; ?>"><i class="fa"></i> <span>Delivery Schedule</span>
                <span id="menubadge20" class="label label-primary pull-right"><?php echo ($jumBadge20 > 0) ? $jumBadge20 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuLgPl)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/pengiriman-list-view.php"; ?>"><i class="fa"></i> <span>List Pengiriman</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnTrack)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/tracking.php"; ?>"><i class="fa"></i> <span>Tracking</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuPoDk)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/delivery-kapal.php"; ?>"><i class="fa"></i> <span>DN Kapal</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuMnSg)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/manual-segel.php"; ?>"><i class="fa"></i> <span>Manual Segel</span></a>
        </li>
    </ul>
</li>

<li class="<?php echo (in_array($menuKey, $menuLcr)) ? 'active' : ''; ?>">
    <?php
    $whereBadge18 = "1=1";
    // $sqlBadge18 = "select count(*) as jum from pro_customer_lcr a join pro_customer b on a.id_customer = b.id_customer where a.flag_disposisi = -1 and ".$whereBadge18;
    // $jumBadge18 = $con->getOne($sqlBadge18);
    $jumBadge18 = 0;
    ?>
    <a href="<?php echo BASE_URL_CLIENT . "/lcr.php"; ?>"><i class="fa fa-file-alt"></i> <span>LCR</span>
        <span id="menubadge18" class="label label-primary pull-right"><?php echo ($jumBadge18 > 0) ? $jumBadge18 : ''; ?></span></a>
</li>
<li class="<?php echo (in_array($menuKey, $menuGN)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/generate-number.php"; ?>"><i class="fa fa-random"></i> <span>Generate Number</span></a>
</li>
<li class="<?php echo (in_array($menuKey, $mnReport)) ? 'treeview active' : 'treeview'; ?>">
    <a><i class="fa fa-folder"></i> <span>Data Master</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $mnRef4_1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-transportir.php"; ?>"><i class="fa fa-caret-right"></i> <span>Perusahaan</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef4_2)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-transportir-sopir.php"; ?>"><i class="fa fa-caret-right"></i> <span>Sopir</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef4_3)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-transportir-mobil.php"; ?>"><i class="fa fa-caret-right"></i> <span>Truck</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef4_4)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/gps-truck.php"; ?>"><i class="fa fa-caret-right"></i> <span>GPS Truck</span></a>
        </li>
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, $mnRef5)) ? 'treeview active' : 'treeview'; ?>">
    <a><i class="fa fa-folder"></i> <span>Ongkos Angkut </span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $mnRef5_1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-ongkos-angkut.php"; ?>"><i class="fa fa-caret-right"></i> <span>OA Truck</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef5_2)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-oa-kapal.php"; ?>"><i class="fa fa-caret-right"></i> <span>OA Kapal</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef5_3)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-wilayah-angkut.php"; ?>"><i class="fa fa-caret-right"></i> <span>Wilayah Angkut</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef5_4)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-volume-angkut.php"; ?>"><i class="fa fa-caret-right"></i> <span>Volume Angkut</span></a>
        </li>
    </ul>
</li>

<li class="<?php echo (in_array($menuKey, array_merge($mnVen, $menuForecast))) ? 'treeview active' : 'treeview'; ?>">
    <a><i class="fa fa-folder"></i> <span>Inventory</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $menuForecast)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/forecast.php"; ?>"><i class="fa"></i> <span>Request Forecast</span>
            </a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnVenInv1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/vendor-inven.php"; ?>"><i class="fa"></i> <span>Inventory Vendor</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnVenInv2)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/vendor-inven-terminal.php"; ?>"><i class="fa"></i> <span>Inventory Vendor By Depot</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnVenInv3)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/terminal-inventory.php"; ?>"><i class="fa"></i> <span>Inventory Depot</span></a>
        </li>
    </ul>
</li>
<!-- <li class="">
    <a href="#"><i class="fa fa-file-alt"></i> <span>Fleet Management</span></a>
</li> -->
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
        <li class="<?php echo (in_array($menuKey, ['customer-volume-report'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/customer-volume-report.php"; ?>"><i class="fa"></i> <span>Volume Customer</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnL1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/l-losses.php"; ?>"><i class="fa"></i> <span>Losses</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnL2)) ? 'active' : ''; ?>">
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
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, $mnRekPengNew)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/rekap-pengiriman-new.php"; ?>"><i class="fa fa-file-alt"></i> <span>Rekap Pengiriman</span></a>
</li>

<li class="<?php echo (in_array($menuKey, $mnCabHo)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/monitoring.php"; ?>"><i class="fa fa-folder"></i> <span>Monitoring</span></a>
</li>