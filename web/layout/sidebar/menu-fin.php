<?php
$menuKey     = basename(BASE_SELF, ".php");
$unUsed1     = array("master-volume", "add-master-volume");

$mnInvoice     = array("invoice_customer", "invoice_customer_add", "invoice_customer_bayar");
$mnAdmCust     = array("customer-admin", "customer-admin-add", "customer-admin-detail", "customer-admin-edit");
$mnAkses1     = array("acl-menu", "add-acl-menu");
$mnAkses2     = array("acl-roles", "add-acl-roles", "acl-roles-menu");
$mnAkses3     = array("acl-user", "add-acl-user", "acl-user-roles", "acl-user-permission");
$mnAkses4     = array("mapping-spv-mkt", "mapping-spv-mkt-add");
$mnAkses      = array_merge($mnAkses1, $mnAkses2, $mnAkses3, $mnAkses4);

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
$menuVevCust = array("evaluasi-data-customer", "evaluasi-data-customer-detail");
$menuVerPoc  = array("verifikasi-poc", "verifikasi-poc-detail");
$menuVerLcr  = array("lcr-add", "verifikasi-lcr", "verifikasi-lcr-detail");
$menuVerPoa  = array("verifikasi-oa", "verifikasi-oa-detail");
$mnVer       = array_merge($menuPnwran, $menuVerCust, $menuVerPmhn, $menuVevCust, $menuVerPoc, $menuVerLcr, $menuVerPoa);

$menuPo      = array("purchase-order", "purchase-order-add", "purchase-order-detail");
$menuPoDs      = array("delivery-loading", "delivery-loading-detail");
$menuPoDk       = array("delivery-kapal", "delivery-kapal-add", "delivery-kapal-detail");
$menuMnSg       = array("manual-segel", "manual-segel-add", "manual-segel-detail");
$menuLgPl       = array("pengiriman-list-logistik");
$mnDel       = array_merge($menuPo, $menuPoDs, $menuPoDk, $menuMnSg, $menuLgPl);

$menuCust       = array("customer", "customer-add", "customer-detail");
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
$mnVenInv2   = array("vendor-inven-terminal-new", "vendor-inven-terminal-list", "vendor-inven-terminal-add");
$mnVenInv3   = array("terminal-inventory");
$mnVenInv4   = array("inven-rssp");
$mnVen       = array_merge($mnVenInv1, $mnVenInv2, $mnVenInv3, $mnVenInv4);

$mnInvoice    = array("invoice_customer", "invoice_customer_add", "invoice_customer_bayar");
$mnRefund     = array("refund");
$mnInsentif   = array("perhitungan-insentif", "perhitungan-insentif-raw");
$mnBpuj       = array("list_bpuj", "detail_bpuj");
$mnPembayaran = array_merge($mnInvoice, $mnRefund, $mnInsentif, $mnBpuj);

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
$mnRefund     = array("refund");
$mnExport     = array("export");
$menuForecast = array("forecast");
$mnRekPeng = array("rekap-pengiriman");
$menuGN = array("generate-number");
$menuMapping = array("mapping-marketing");

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
$mnL6 = array("c-rekap-loaded");
$mnL7 = array("c-rekap-loaded-kapal");
$mnL8 = array("c-rekap-performance");
$mnF1 = array("f-schedule-payment");
$mnF2 = array("f-refund");
$mnC1 = array("c-pembelian");
$mnC2 = array("c-area-performance");
$mnC3 = array("c-margin");
$mnC4 = array("c-harga-market");
$mnReport = array_merge($mnM1, $mnM2, $mnM3, $mnM4, $mnM5, $mnM6, $mnL1, $mnL2, $mnL3, $mnL4, $mnL5, $mnL6, $mnL7, $mnL8, $mnF1, $mnF2, $mnC1, $mnC2, $mnC3, $mnC4);
$param_session = null;
if (isset($_SESSION['sinori' . SESSIONID]))
    $param_session = $_SESSION['sinori' . SESSIONID];
if ($param_session) {
    $varWilayah = paramDecrypt($param_session['id_wilayah']);
    $varGroup     = paramDecrypt($param_session['id_group']);
    $varUser     = paramDecrypt($param_session['id_user']);
}

?>



<?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("15"))) { ?>
    <li class="<?php echo (in_array($menuKey, array_merge($menuOrang, ['perhitungan-insentif', 'perhitungan-insentif-raw']))) ? 'treeview active' : 'treeview'; ?>">
        <?php
        $jumBadgeAll = 0;

        $varBadge14 = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
        $whereBadge18 = "b.id_marketing = '" . $varBadge14 . "'";
        if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == "18") {
            $whereBadge18 = "1=1";
            if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
                $whereBadge18 = "b.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
            else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
                $whereBadge18 = "b.id_group = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_group"]) . "'";
        }
        $sqlBadge18 = "select count(*) as jum from pro_customer_lcr a join pro_customer b on a.id_customer = b.id_customer where a.flag_disposisi = -1 and " . $whereBadge18;
        $jumBadge18 = $con->getOne($sqlBadge18);
        $jumBadgeAll += $jumBadge18
        ?>
        <a>
            <i class="fa fa-folder"></i> <span>Customer</span>
            <?php if ($jumBadgeAll > 0) { ?>
                <span class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;">
                    <?php echo $jumBadgeAll; ?>
                </span>
            <?php } ?>
            <div class="icon"><i class="fa fa-plus"></i></div>
        </a>
        <ul class="treeview-menu">
            <li class="<?php echo (in_array($menuKey, $menuCust)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/customer.php"; ?>"><i class="fa"></i> <span>Data Customer</span></a>
            </li>
            <li class="<?php echo (in_array($menuKey, $menuLcr)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/lcr.php"; ?>"><i class="fa"></i> <span>LCR</span>
                    <span id="menubadge18" class="label label-primary pull-right"><?php echo ($jumBadge18 > 0) ? $jumBadge18 : ''; ?></span></a>
            </li>
            <li class="<?php echo (in_array($menuKey, $mnRefund)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/refund.php"; ?>"><i class="fa"></i> <span>Refund</span></a>
            </li>
            <li class="<?php echo (in_array($menuKey, ['perhitungan-insentif', 'perhitungan-insentif-raw'])) ? 'active' : ''; ?>">
                <?php if ((getenv('APP_ENV') != 'production') or (getenv('APP_NAME') == 'proEnergi-demo')) { ?>
                    <a href="<?php echo BASE_URL_CLIENT . "/perhitungan-insentif.php"; ?>"><i class="fa "></i> <span>Perhitungan Insentif</span></a>
                <?php } else { ?>
                    <a href="#"><i class="fa "></i> <span>Perhitungan Insentif</span></a>
                <?php } ?>
            </li>
        </ul>
    </li>
<?php } ?>
<li class="<?php echo (in_array($menuKey, array_merge($mnVer, [(in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("15")) ? 'pro_sales_confirmation' : '')]))) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $jumBadgeRole3 = 0;
    $sqlBadge2    = "
			select count(b.id_verification) as jum 
			from pro_customer a 
			join pro_customer_verification b on a.id_customer = b.id_customer 
				and b.is_evaluated = 1 and b.is_reviewed = 1 and b.is_approved = 0 
			join acl_user c on a.id_marketing = c.id_user 
			where 1=1
		";
    if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 15)
        $sqlBadge2 .= " and b.disposisi_result = 6";
    else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 10)
        $sqlBadge2 .= " and b.finance_result = 0 and b.disposisi_result = 1 and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
    $jumBadge2     = $con->getOne($sqlBadge2);

    $sqlBadge3 = "SELECT * FROM pro_pengisian_solar_mobil_opr WHERE disposisi = 0 OR is_admin_realisasi = 0";
    $jumBadge3 = $con->getResult($sqlBadge3);


    $jumBadgeRole3 += $jumBadge2;

    $jumBadgeRole3 += count($jumBadge3);

    if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("10"))) {
        $sqlBadge7    = "select count(a.id_cu) as jum from pro_customer_update a 
                       join pro_customer b on a.id_customer = b.id_customer where 1=1";
        if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 10)
            $sqlBadge7 .= " and ((a.flag_disposisi > 0 and a.finance_result = 0) or (a.flag_approval = 1 and a.flag_edited = 0)) and b.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
        $jumBadge7     = $con->getOne($sqlBadge7);
        $jumBadgeRole3 += $jumBadge7;
    } else
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("15"))) {
        $sqlSales   = "select count(a.id) as jum from pro_sales_confirmation a join pro_po_customer aa on aa.id_poc = a.id_poc left join pro_sales_confirmation_approval s on s.id_sales = a.id where 1=1";
        if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 10)
            $sqlSales .= " and a.disposisi = 1 and s.adm_result = 0 and a.flag_approval = 0 and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
        else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 15)
            $sqlSales .= " and a.flag_approval = 0 and a.disposisi = 4 and s.mgr_result = 0";

        $jumsales = $con->getOne($sqlSales);
    }
    ?>

    <?php

    $sqlBadge12  = "
    select count(a.id_dsd) as jum 
    from pro_po_ds_detail a 
    join pro_po_ds b on a.id_ds = b.id_ds 
    join pro_master_cabang c on b.id_wilayah = c.id_master 
    where a.disposisi_losses = 3 and a.om_result = 1 
        
    ";
    $jumBadge12  = $con->getOne($sqlBadge12);
    $jumBadgeRole15 += $jumBadge12;

    $jumtot = $jumBadge2 + $jumBadge12;

    ?>
    <a>
        <i class="fa fa-folder"></i>
        <span>Verifikasi</span>
        <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("10"))) { ?>
            <span id="menubadgerole3" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($jumBadgeRole3 > 0) ? $jumBadgeRole3 : ''; ?></span>
        <?php } elseif (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("15"))) { ?>
            <span id="menubadgerole3" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($jumtot > 0) ? $jumtot : ''; ?></span>
        <?php } ?>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">

        <li class="<?php echo (in_array($menuKey, $menuVerCust)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-data-customer.php"; ?>"><i class="fa"></i> <span>Verifikasi Data Customer</span>
                <span id="menubadge2" class="label label-primary pull-right"><?php echo ($jumBadge2 > 0) ? $jumBadge2 : ''; ?></span></a>
        </li>

        <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("10"))) { ?>
            <li class="<?php echo (in_array($menuKey, $menuVerPmhn)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-permohonan.php"; ?>"><i class="fa"></i> <span>Verifikasi Pemutakhiran</span>
                    <span id="menubadge7" class="label label-primary pull-right"><?php echo ($jumBadge7 > 0) ? $jumBadge7 : ''; ?></span></a>
            </li>
        <?php } ?>

        <?php if ($varWilayah == "4" || $varWilayah == "11") : ?>
            <li class="<?php echo (in_array($menuKey, ['pengisian_solar_mobil'])) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/pengisian_solar_mobil.php"; ?>">
                    <i class="fa"></i> <span>Verifikasi Pengisian Solar</span>
                    <span id="menubadge2" class="label label-primary pull-right"><?php echo (count($jumBadge3) > 0) ? count($jumBadge3) : ''; ?></span>
                </a>
            </li>
        <?php endif ?>



        <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("15"))) { ?>
            <li class="<?php echo (in_array($menuKey, $menuVerLos)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-losses.php"; ?>"><i class="fa"></i> <span>Verifikasi Losses</span>
                    <span id="menubadge19" class="label label-primary pull-right"><?php echo ($jumBadge12 > 0) ? $jumBadge12 : ''; ?></span></a>
            </li>
        <?php } ?>
    </ul>
</li>
<?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("10", "15"))) { ?>
    <li class="<?php echo (in_array($menuKey, ['pro_sales_confirmation'])) ? 'active' : ''; ?>">
        <?php
        $sqlSales   = "select count(a.id) as jum from pro_sales_confirmation a join pro_po_customer aa on aa.id_poc = a.id_poc left join pro_sales_confirmation_approval s on s.id_sales = a.id where 1=1";
        if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 10)
            $sqlSales .= " and a.disposisi = 1 and s.adm_result = 0 and a.flag_approval = 0 and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
        else if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == 15)
            $sqlSales .= " and a.flag_approval = 0 and a.disposisi = 4 and s.mgr_result = 0";

        $jumsales = $con->getOne($sqlSales);
        ?>
        <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("15"))) { ?>
            <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-po.php"; ?>"><i class="fa fa-file-alt"></i> <span>PO Supplier</span>
                <span class="label label-primary pull-right" style="margin-top: 2.5px;"></span></a>
        <?php } ?>
        <a href="<?php echo BASE_URL_CLIENT . "/pro_sales_confirmation.php"; ?>"><i class="fa fa-file-alt"></i> <span>Sales Confirmation</span>
            <span class="label label-primary pull-right" style="margin-top: 2.5px;"><?php echo ($jumsales > 0) ? $jumsales : ''; ?></span></a>
    </li>
<?php } ?>
<?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("10"))) { ?>
    <li class="<?php echo (in_array($menuKey, array_merge($menuPoLog, $menuPr, $menuPo, $menuPoDs, $menuLgPl, $mnTrack, $menuPoDk, $menuMnSg))) ? 'treeview active' : 'treeview'; ?>">
        <?php
        $jumBadgeDelivery = 0;
        $sqlBadge5  = "select count(id_pr) as jum from pro_pr where 1=1";
        $sqlBadge5 .= " and finance_result = 0 and disposisi_pr = 1 and id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
        $jumBadge5  = $con->getOne($sqlBadge5);
        $jumBadgeDelivery += $jumBadge5;
        ?>
        <a>
            <i class="fa fa-folder"></i> <span>Delivery</span>
            <span id="menubadgedelivery" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($jumBadgeDelivery > 0) ? $jumBadgeDelivery : ''; ?></span>
            <div class="icon"><i class="fa fa-plus"></i></div>
        </a>
        <ul class="treeview-menu">
            <li class="<?php echo (in_array($menuKey, $menuPr)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/purchase-request.php"; ?>"><i class="fa"></i> <span>Delivery Request</span>
                    <span id="menubadge5" class="label label-primary pull-right"><?php echo ($jumBadge5 > 0) ? $jumBadge5 : ''; ?></span></a>
            </li>
        </ul>
    </li>
    <li class="<?php echo (in_array($menuKey, $mnPembayaran)) ? 'treeview active' : 'treeview'; ?>">
        <?php
        $jumBadgePembayaran = 0;
        $sqlBadgeBpuj       = "SELECT count(id_bpuj) as jum, id_bpuj from pro_bpuj where disposisi_bpuj = '1' AND cabang = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "' AND is_active='1'";
        $resBpuj            = $con->getResult($sqlBadgeBpuj);
        $jumBadgeBpuj       = $con->getOne($sqlBadgeBpuj);
        $jumBadgePembayaran += $jumBadgeBpuj;

        $sqlBadgeRefund       = "SELECT a.* FROM pro_refund a JOIN pro_invoice_admin b ON a.id_invoice=b.id_invoice JOIN pro_customer c ON b.id_customer=c.id_customer WHERE a.disposisi = 1 AND c.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
        $resRefund            = $con->getResult($sqlBadgeRefund);
        $jumBadgeRefund       = count($resRefund);
        $jumBadgePembayaran += $jumBadgeRefund;
        ?>
        <a>
            <i class="fa fa-folder"></i> <span>Pembayaran</span>
            <span id="menubadgedelivery" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($jumBadgePembayaran > 0) ? $jumBadgePembayaran : ''; ?></span>
            <div class="icon"><i class="fa fa-plus"></i></div>
        </a>
        <ul class="treeview-menu">
            <li class="<?php echo (in_array($menuKey, $mnBpuj)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/list_bpuj.php"; ?>"><i class="fa"></i> <span>RPUJ</span>
                    <span id="menubadge5" class="label label-primary pull-right"><?php echo ($jumBadgeBpuj > 0) ? $jumBadgeBpuj : ''; ?></span>
                </a>
            </li>
            <li class="<?php echo (in_array($menuKey, $mnInvoice)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/invoice_customer.php"; ?>"><i class="fa"></i> <span>Invoice</span>
                    <span id="menubadge5" class="label label-primary pull-right"></span>
                </a>
            </li>
            <li class="<?php echo (in_array($menuKey, $mnRefund)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/refund.php"; ?>"><i class="fa"></i> <span>Refund</span>
                    <span id="menubadge5" class="label label-primary pull-right"><?php echo ($jumBadgeRefund > 0) ? $jumBadgeRefund : ''; ?></span>
                </a>
            </li>
            <li class="<?php echo (in_array($menuKey, $mnInsentif)) ? 'active' : ''; ?>">
                <?php if ((getenv('APP_ENV') != 'production') or (getenv('APP_NAME') == 'proEnergi-demo')) { ?>
                    <a href="<?php echo BASE_URL_CLIENT . "//perhitungan-insentif.php"; ?>"><i class="fa"></i> <span>Perhitungan Insentif</span>
                        <span id="menubadge5" class="label label-primary pull-right"></span>
                    </a>
                <?php } else { ?>
                    <a href="#"><i class="fa fa-file-alt"></i> <span>Perhitungan Insentif</span></a>
                <?php } ?>
            </li>
        </ul>
    </li>
<?php } ?>
<li class="<?php echo (in_array($menuKey, $mnExport)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/export.php"; ?>"><i class="fa fa-file-alt"></i> <span>AR Customer</span></a>
</li>
<li class="<?php echo (in_array($menuKey, $mnMktKrm)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/pengiriman-list-view.php"; ?>"><i class="fa fa-file-alt"></i> <span>List Pengiriman</span></a>
</li>
<li class="<?php echo (in_array($menuKey, array_merge($mnVen, $menuForecast))) ? 'treeview active' : 'treeview'; ?>">
    <a><i class="fa fa-folder"></i> <span>Inventory</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("10"))) { ?>
            <li class="<?php echo (in_array($menuKey, $menuForecast)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/forecast.php"; ?>"><i class="fa"></i> <span>Request Forecast</span>
                </a>
            </li>
        <?php } ?>
        <li class="<?php echo (in_array($menuKey, $mnVenInv1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/vendor-inven.php"; ?>"><i class="fa"></i> <span>Inventory Vendor</span></a>
        </li>
        <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("15"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnVenInv2)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/vendor-inven-terminal-new.php"; ?>"><i class="fa"></i> <span>Inventory Vendor By Depot</span></a>
            </li>
        <?php } ?>
        <li class="<?php echo (in_array($menuKey, $mnVenInv3)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/terminal-inventory.php"; ?>"><i class="fa"></i> <span>Inventory Depot</span></a>
        </li>
    </ul>
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
        <li class="<?php echo (in_array($menuKey, $mnM2)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/m-total-customer.php"; ?>"><i class="fa"></i> <span>Total Customer</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnM3)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/m-customer.php"; ?>"><i class="fa"></i> <span>Customer</span></a>
        </li>
        <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("15"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnM6)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/m-sales-performance.php"; ?>"><i class="fa"></i> <span>Sales Performance</span></a>
            </li>
        <?php } ?>
        <li class="<?php echo (in_array($menuKey, $mnL1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/l-losses.php"; ?>"><i class="fa"></i> <span>Losses</span></a>
        </li>
        <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("15"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnL3)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/l-volume-angkut.php"; ?>"><i class="fa"></i> <span>Volume Angkut Transportir</span></a>
            </li>
        <?php } ?>
        <li class="<?php echo (in_array($menuKey, $mnL5)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/l-loading-order.php"; ?>"><i class="fa"></i> <span>Loading Order</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnF1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/f-schedule-payment.php"; ?>"><i class="fa"></i> <span>Schedule Payment</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnL6)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/c-rekap-loaded.php"; ?>"><i class="fa"></i> <span>Rekap Loaded Truck</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnL7)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/c-rekap-loaded-kapal.php"; ?>"><i class="fa"></i> <span>Rekap Loaded Kapal</span></a>
        </li>

        <li class="<?php echo (in_array($menuKey, $mnL8)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/c-rekap-performance.php"; ?>"><i class="fa"></i> <span>Rekap Performance</span></a>
        </li>
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, $mnRef)) ? 'treeview active' : 'treeview'; ?>">
    <?php $jumBadgeRole3 = 0; ?>
    <a>
        <i class="fa fa-folder"></i>
        <span>Referensi Data</span>
        <span id="menubadgerole3" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($jumBadgeRole3 > 0) ? $jumBadgeRole3 : ''; ?></span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $mnRef7)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-harga-minyak.php"; ?>"><i class="fa"></i> <span>Daftar Harga Jual</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef14)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/attach-harga-minyak.php"; ?>"><i class="fa"></i> <span>Attachment Harga Jual</span></a>
        </li>
    </ul>
</li>