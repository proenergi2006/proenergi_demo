<?php
$unUsed1     = array("master-volume", "add-master-volume");

$mnAkses1     = array("acl-menu", "add-acl-menu");
$mnAkses2     = array("acl-roles", "add-acl-roles", "acl-roles-menu");
$mnAkses3     = array("acl-user", "add-acl-user", "acl-user-roles", "acl-user-permission");
$mnAkses4     = array("mapping-spv-mkt", "mapping-spv-mkt-add");
$mnAkses      = array_merge($mnAkses1, $mnAkses2, $mnAkses3, $mnAkses4);

$mnAdmCust         = array("customer-admin", "customer-admin-detail");
$menuLcr         = array("lcr", "lcr-detail");
$menuSalesConf     = array("pro_sales_confirmation", "sales_confirmation_form");
$mnRefund         = array("refund-ceo", "master-penerima-refund-ceo");
$mnFolder01     = array_merge($mnAdmCust, $menuLcr, $menuSalesConf, $mnRefund);
$menuVenPoNew     = array("verifikasi-po", "verifikasi-po-detail");
$menuVenPoNewCR = array("verifikasi-po-crushed-stone", "verifikasi-po-crushed-stone-detail");
$menuVenLosNew     = array("verifikasi-gain-loss", "verifikasi-gain-loss-detail");
$mnFolder03     = array_merge($menuVenPoNew, $menuVenPoNewCR,  $menuVenLosNew);
//

$mnVenInv1       = array("vendor-inven", "vendor-inven-add");
$mnVenInv2       = array("vendor-inven-terminal-new", "vendor-inven-terminal-list", "vendor-inven-terminal-add");
$mnVenInv4       = array("inven-rssp");
$mnVenInv7    = array("sisa-stock");
$mnFolder02      = array_merge($mnVenInv1, $mnVenInv2, $mnVenInv4, $mnVenInv7);

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
$mnF1 = array("f-schedule-payment");
$mnF2 = array("f-refund");
$mnC1 = array("c-pembelian");
$mnC2 = array("c-area-performance");
$mnC3 = array("c-margin");
$mnC4 = array("c-harga-market");
$mnReport = array_merge($mnM1, $mnM2, $mnM3, $mnM4, $mnM5, $mnM6, $mnL1, $mnL2, $mnL3, $mnL4, $mnL5, $mnF1, $mnF2, $mnC1, $mnC2, $mnC3, $mnC4);

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

$mnRef      = array_merge($mnRef1, $mnRef2, $mnRef3, $mnRef4, $mnRef5, $mnRef6, $mnRef7, $mnRef8, $mnRef9, $mnRef10, $mnRef11, $mnRef12, $mnRef13, $mnRef14);

$menuPnwran  = array("penawaran-approval", "penawaran-approval-detail");
$menuVerCust = array("verifikasi-data-customer", "verifikasi-data-customer-detail");
$menuVerPmhn = array("verifikasi-permohonan", "verifikasi-permohonan-detail", "verifikasi-permohonan-data");
$menuVerPoc  = array("verifikasi-poc", "verifikasi-poc-detail");
$menuVerLcr  = array("lcr-add", "verifikasi-lcr", "verifikasi-lcr-detail");
$menuVerPoa  = array("verifikasi-oa", "verifikasi-oa-detail");
$mnVer       = array_merge($menuPnwran, $menuVerCust, $menuVerPmhn, $menuVevCust, $menuVerPoc, $menuVerLcr, $menuVerPoa, ['pro_sales_confirmation', 'po-customer-om', 'purchase-request', 'sales_confirmation_form']);

$menuPo      = array("purchase-order", "purchase-order-add", "purchase-order-detail");
$menuPoDs      = array("delivery-loading", "delivery-loading-detail");
$menuPoDk       = array("delivery-kapal", "delivery-kapal-add", "delivery-kapal-detail");
$menuMnSg       = array("manual-segel", "manual-segel-add", "manual-segel-detail");
$menuLgPl       = array("pengiriman-list-logistik");
$mnDel       = array_merge($menuPo, $menuPoDs, $menuPoDk, $menuMnSg, $menuLgPl);

$menuPCust      = array("penawaran", "penawaran-add", "penawaran-detail");
$menuPLCust  = array("customer-generate-link", "customer-generate-link-list", "customer-generate-link-email");
$menuPRCust  = array("customer-review", "customer-review-list", "customer-review-add", "customer-review-detail");
$menuPUCust  = array("customer-permohonan-update", "customer-permohonan-update-add", "customer-permohonan-update-detail");
$menuLcr       = array("lcr", "lcr-add", "lcr-detail");
$menuVerPoc  = array("verifikasi-poc", "verifikasi-poc-detail");
$menuPoCust  = array("po-customer", "po-customer-detail", "po-customer-add", "po-customer-plan", "po-customer-plan-add");
$menuPoAdm      = array("po-customer-admin");
$menuPoLog      = array("po-customer-logistik");
$menuPoOM      = array("po-customer-om", "po-customer-om-detail");

$menuPr      = array("purchase-request", "purchase-request-detail", "purchase-request-detail-all");
$menuFixPr      = array("perbaikan-data", "perbaikan-data-detail");
$menuVenPo      = array("vendor-po", "vendor-po-add");
$menuPrAr      = array("purchase-request-ar", "purchase-request-ar-detail");
$mnVenInv1   = array("vendor-inven", "vendor-inven-add");
$mnVenInv2   = array("vendor-inven-terminal", "vendor-inven-terminal-list", "vendor-inven-terminal-add");
$mnVenInv3   = array("terminal-inventory");
$mnVenInv4   = array("inven-rssp");
$mnVenInv6   = array("inven-stock");
$mnVen       = array_merge($mnVenInv1, $mnVenInv2, $mnVenInv3, $mnVenInv4, $mnVenInv6);

$menuUsrPN      = array("permintaan-penawaran", "permintaan-penawaran-add", "permintaan-penawaran-detail");
$menuUsrPO      = array("permintaan-order", "permintaan-order-add", "permintaan-order-detail");
$menuUsrLP      = array("permintaan-rekapitulasi", "permintaan-rekapitulasi-detail");
$menuUsrHd      = array("permintaan-delivery");
$menuCustPN  = array("customer-penawaran", "customer-penawaran-detail");
$menuCustPO  = array("customer-order", "customer-order-detail");
$menuCustEV  = array("customer-evaluasi", "customer-evaluasi-list", "customer-evaluasi-add", "customer-evaluasi-detail");
$menuOrang   = array_merge($menuCust, $menuPCust, $menuPLCust, $menuPRCust, $menuPUCust, $menuCustPN, $menuCustPO, $menuCustEV);
$menuTmDr       = array("terminal-dr");
$menuTmDo       = array("terminal-do");
$menuTmInv     = array("terminal-inventory", "terminal-inventory-add");

$mnPoTrans     = array("purchase-order-transportir", "purchase-order-add", "purchase-order-detail");
$mnRfTrans     = array("referensi-transportir");
$mnPoKrm     = array("pengiriman-list-transportir");
$mnMktKrm     = array("pengiriman-list-view");
$mnTrack     = array("tracking");
$mnExport     = array("export");
$menuForecast = array("forecast");
$mnRekPeng = array("rekap-pengiriman");
$menuGN = array("generate-number");
$menuMapping = array("mapping-marketing");
?>

<li class="<?php echo (in_array($menuKey, $mnFolder01)) ? 'treeview active' : 'treeview'; ?>">
    <a>
        <i class="fa fa-folder"></i> <span>Customer</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $mnAdmCust)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/customer-admin.php"; ?>"><i class="fa"></i> <span>Data Customer</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuLcr)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/lcr.php"; ?>"><i class="fa"></i> <span>LCR</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuSalesConf)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/pro_sales_confirmation.php"; ?>"><i class="fa"></i> <span>Sales Confirmation</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['refund-ceo'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/refund-ceo.php"; ?>"><i class="fa"></i> <span>Refund</span></a>
        </li>
    </ul>
</li>

<li class="<?php echo (in_array($menuKey, ['penawaran-approval', 'verifikasi-data-customer', 'verifikasi-oa', 'purchase-request'])) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $jumBadgeRole3 = 0;
    $sqlBadge19 = "select count(*) as jum from pro_po where ada_selisih = 1 and f_proses_selisih = 0 and po_approved = 1";
    $jumBadge19 = $con->getOne($sqlBadge19);
    $jumBadgeRole3 += $jumBadge19;

    $sqlBadge2  = "select count(b.id_verification) as jum from pro_customer a 
                        join pro_customer_verification b on a.id_customer = b.id_customer and b.is_evaluated = 1 and b.is_reviewed = 1 and b.is_approved = 0 
                        join acl_user c on a.id_marketing = c.id_user 
                        where 1=1";
    $sqlBadge2 .= " and b.disposisi_result = 5 and b.ceo_result = 0";
    $jumBadge2  = $con->getOne($sqlBadge2);
    $jumBadgeRole3 += $jumBadge2;

    $sessParam  = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
    $sqlBadge1  = "
			select count(a.id_penawaran) as jum 
			from pro_penawaran a 
			join pro_master_cabang b on a.id_cabang = b.id_master 
			join pro_customer c on a.id_customer = c.id_customer 
			join acl_user d on c.id_marketing = d.id_user 
			where 1=1  
				and (a.flag_disposisi = 6) and a.flag_approval = 0 and a.ceo_result = 0
		";
    $jumBadge1  = $con->getOne($sqlBadge1);
    $jumBadgeRole3 += $jumBadge1;

    $sqlBadge5  = "select count(id_pr) as jum from pro_pr where 1=1";
    $sqlBadge5 .= " and ceo_result = 0 and disposisi_pr = 5 and is_ceo = 1";
    $jumBadge5  = $con->getOne($sqlBadge5);
    $jumBadgeRole3 += $jumBadge5;

    $jumBadgeRole4 = 0;
    $sqlBadge8  = "select count(id_master) as jum from pro_penawaran where 1=1";
    $sqlBadge8 .= " and flag_disposisi = 6 and flag_approval = 0";
    $jumBadge8  = $con->getOne($sqlBadge8);
    $jumBadgeRole4 += $jumBadge8;

    $jumBadgeRole15 = 0;
    $sqlBadge15  = "select count(id_master) as jum from new_pro_inventory_vendor_po where 1=1";
    $sqlBadge15 .= " and disposisi_po = 2 and cfo_result = 1";
    $jumBadge15  = $con->getOne($sqlBadge15);
    $jumBadgeRole15 += $jumBadge15;

    // $jumBadgeRole14 = 0;
    // $sqlBadge14  = "select count(id_master) as jum from new_pro_inventory_vendor_po where 1=1";
    // $sqlBadge14 .= " and flag_disposisi = 6 and flag_approval = 0";
    // $jumBadge14  = $con->getOne($sqlBadge14);
    // $jumBadgeRole14 += $jumBadge8;

    $totalBadge = $jumBadgeRole3 + $jumBadgeRole15;

    $jumBadgeRole6 = 0;
    $sqlBadge9  = "select count(id_master) as jum from new_pro_inventory_gain_loss where 1=1";
    $sqlBadge9 .= " and ceo_result = 0";
    $jumBadge9  = $con->getOne($sqlBadge9);
    $jumBadgeRole6 += $jumBadge9;

    $jumBadgeRole6 = 0;

    $sqlBadge7  = "select count(id_master) as jum from new_pro_inventory_vendor_po where 1=1";

    $sqlBadge7 .= " and cfo_result = 1 and disposisi_po = 2";
    $jumBadge7  = $con->getOne($sqlBadge7);

    $jumBadgeRole6 += $jumBadge7;

    $sqlBadgeRefund  = "SELECT a.*, b.nama_customer FROM pro_master_penerima_refund a JOIN pro_customer b ON a.id_customer=b.id_customer WHERE a.is_bm = 1 AND a.is_ceo = 0 AND a.is_active = 1";
    $jumBadgeRefund  = $con->getResult($sqlBadgeRefund);
    $jumBadgeRole4 += count($jumBadgeRefund);

    $jumBadgeRole11 = 0;
    $sqlBadge11  = "select count(id_master) as jum from new_pro_inventory_vendor_po_crushed_stone where 1=1";
    $sqlBadge11 .= " and ceo_result = 0 and disposisi_po = 2";
    $jumBadge11  = $con->getOne($sqlBadge11);
    $jumBadgeRole11 += $jumBadge11;


    ?>
    <a>
        <i class="fa fa-folder"></i>
        <span>Verifikasi</span>
        <span id="menubadgerole3" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($totalBadge > 0) ? $totalBadge : ''; ?></span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $menuPnwran)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/penawaran-approval.php"; ?>"><i class="fa"></i> <span>Penawaran</span>
                <span id="menubadge1" class="label label-primary pull-right"><?php echo ($jumBadge1 > 0) ? $jumBadge1 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuVerCust)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-data-customer.php"; ?>"><i class="fa"></i> <span>Verifikasi Data Customer</span>
                <span id="menubadge2" class="label label-primary pull-right"><?php echo ($jumBadge2 > 0) ? $jumBadge2 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuVerPoa)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-oa.php"; ?>"><i class="fa"></i> <span>Verifikasi Selisih OA</span>
                <span id="menubadge19" class="label label-primary pull-right"><?php echo (isset($jumBadge19) && $jumBadge19 > 0) ? $jumBadge19 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['master-penerima-refund-ceo'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-penerima-refund-ceo.php"; ?>"><i class="fa"></i> <span>Verifikasi Penerima Refund</span>
                <span id="menubadge_refund" class="label label-primary pull-right"><?php echo (count($jumBadgeRefund) > 0) ? count($jumBadgeRefund) : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuPr)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/purchase-request.php"; ?>"><i class="fa"></i> <span>Delivery Request</span>
                <span id="menubadge5" class="label label-primary pull-right"><?php echo ($jumBadge5 > 0) ? $jumBadge5 : ''; ?></span></a>
        </li>

        <li class="<?php echo (in_array($menuKey, $menuVenPoNew)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-po.php"; ?>"><i class="fa"></i> <span>PO Suplier Fuel</span>
                <span id="menubadge8" class="label label-primary pull-right"><?php echo ($jumBadge15 > 0) ? $jumBadge15 : ''; ?></span></a></a>
        </li>

        <li class="<?php echo (in_array($menuKey, $menuVenPoNewCR)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-po-crushed-stone.php"; ?>"><i class="fa"></i> <span>PO Suplier Crushed Stone</span>
                <span id="menubadge8" class="label label-primary pull-right"><?php echo ($jumBadge9 > 0) ? $jumBadge9 : ''; ?></span></a>
        </li>

        <li class="<?php echo (in_array($menuKey, $menuVenLosNew)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-gain-loss.php"; ?>"><i class="fa"></i> <span>Gain & Loss</span>
                <span id="menubadge9" class="label label-primary pull-right"><?php echo ($jumBadge9 > 0) ? $jumBadge9 : ''; ?></span></a>
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
            <a href="<?php echo BASE_URL_CLIENT . "/vendor-inven-terminal-new.php"; ?>"><i class="fa"></i> <span>Inventory By Depot</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnVenInv1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/vendor-inven.php"; ?>"><i class="fa"></i> <span>Inventory By Vendor</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnVenInv6)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/inven-stock.php"; ?>"><i class="fa"></i> <span>Inventory Stock</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnVenInv7)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/sisa-stock.php"; ?>"><i class="fa"></i> <span>Sisa Stock</span></a>
        </li>
    </ul>
</li>

<li class="<?php echo (in_array($menuKey, $mnRef)) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $jumBadgeRoleIncentive = 0;
    $sqlBadgeIncentive  = "SELECT * FROM pro_pengajuan_incentive WHERE is_ceo = 0";
    $jumBadgeIncentive  = $con->getResult($sqlBadgeIncentive);
    $jumBadgeRoleIncentive += count($jumBadgeIncentive);
    ?>
    <a>
        <i class="fa fa-folder"></i>
        <span>Incentive </span>
        <span id="menubadgerole3" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($jumBadgeRoleIncentive > 0) ? $jumBadgeRoleIncentive : ''; ?></span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, ['incentive'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/incentive.php"; ?>"><i class="fa"></i> <span>List Incentive</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['list_pengajuan_incentive', 'detail_incentive'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/list_pengajuan_incentive.php"; ?>"><i class="fa"></i> <span>List Pengajuan Incentive</span><span id="menubadge9" class="label label-primary pull-right"><?php echo (count($jumBadgeIncentive) > 0) ? count($jumBadgeIncentive) : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['incentive_point'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/incentive_point.php"; ?>"><i class="fa"></i> <span>List Point Incentive</span></a>
        </li>
    </ul>
</li>


<li class="<?php echo (in_array($menuKey, $mnMktKrm)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/pengiriman-list-view.php"; ?>"><i class="fa fa-file-alt"></i> <span>List Pengiriman</span></a>
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
        <li class="<?php echo (in_array($menuKey, ['marketing-volume-report'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/marketing-volume-report.php"; ?>"><i class="fa"></i> <span>Marketing Volume</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['marketing-active-customer'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/marketing-active-customer.php"; ?>"><i class="fa"></i> <span>Active Customer</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['marketing-new-customer'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/marketing-new-customer.php"; ?>"><i class="fa"></i> <span>New Customer</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnM2)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/m-total-customer.php"; ?>"><i class="fa"></i> <span>Total Customer</span></a>
        </li>
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
        <li class="<?php echo (in_array($menuKey, $mnF1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/f-schedule-payment.php"; ?>"><i class="fa"></i> <span>Schedule Payment</span></a>
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
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, $mnRef)) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $jumBadgeRole3 = 0;
    $sqlBadge6 = "select count(*) as jum from (select distinct periode_awal, periode_akhir, id_area, produk 
                  from pro_master_harga_minyak where is_evaluated = 1 and is_approved = 0) a";
    $jumBadge6 = $con->getOne($sqlBadge6);
    $jumBadgeRole3 += $jumBadge6;
    ?>
    <a>
        <i class="fa fa-folder"></i>
        <span>Referensi Data</span>
        <span id="menubadgerole3" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($jumBadgeRole3 > 0) ? $jumBadgeRole3 : ''; ?></span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>

    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $mnRef12)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-area.php"; ?>"><i class="fa"></i> <span>Area</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef3)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-terminal.php"; ?>"><i class="fa"></i> <span>Terminal</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef4)) ? 'treeview active' : 'treeview'; ?>">
            <a><i class="fa"></i> <span>Transportir </span>
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
            <a><i class="fa"></i> <span>Ongkos Angkut </span>
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
        <li class="<?php echo (in_array($menuKey, $mnRef6)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-pbbkb.php"; ?>"><i class="fa"></i> <span>PBBKB</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef9)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-produk.php"; ?>"><i class="fa"></i> <span>Produk</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef8)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-approval-harga.php"; ?>"><i class="fa"></i> <span>Daftar Harga Dasar</span>
                <span id="menubadge6" class="label label-primary pull-right"><?php echo ($jumBadge6 > 0) ? $jumBadge6 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef14)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/attach-harga-minyak.php"; ?>"><i class="fa"></i> <span>Attachment Harga Dasar</span></a>
        </li>

        <li class="<?php echo (in_array($menuKey, $mnRef10)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-vendor.php"; ?>"><i class="fa"></i> <span>Vendor</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef11)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-harga-tebus.php"; ?>"><i class="fa"></i> <span>Harga Tebus</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef13)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-harga-pertamina.php"; ?>"><i class="fa"></i> <span>Harga Dasar Pertamina</span></a>
        </li>


    </ul>
</li>
<?php /*
<li class="<?php echo(in_array($menuKey,['insentif-pricelist-master','insentif-pricelist-master-add','insentif-pricelist-master-edit']))?'treeview active':'treeview'; ?>">
    <a>
        <i class="fa fa-folder"></i> <span>Data Master</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo(in_array($menuKey,['insentif-pricelist-master','insentif-pricelist-master-add','insentif-pricelist-master-edit']))?'active':''; ?>">
            <a href="<?php echo BASE_URL_CLIENT."/insentif-pricelist-master.php"; ?>"><i class="fa"></i> <span>Master Tier Insetif</span></a>
        </li>
        <li class="<?php echo(in_array($menuKey,['insentif-poin-master']))?'active':''; ?>">
            <a href="<?php echo BASE_URL_CLIENT."/insentif-poin-master.php"; ?>"><i class="fa"></i> <span>Poin Insentif</span></a>
        </li>
        <li class="<?php echo(in_array($menuKey,['reservasi-ruangan-master']))?'active':''; ?>">
            <a href="<?php echo BASE_URL_CLIENT."/reservasi-ruangan-master.php"; ?>"><i class="fa"></i> <span>Ruang Meeting</span></a>
        </li>
        <li class="<?php echo(in_array($menuKey,['peminjaman-mobil-master']))?'active':''; ?>">
            <a href="<?php echo BASE_URL_CLIENT."/peminjaman-mobil-master.php"; ?>"><i class="fa"></i> <span>Mobil Oprastonal</span></a>
        </li>
    </ul>
</li>
*/ ?>