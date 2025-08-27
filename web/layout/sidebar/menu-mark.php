<li class="<?php echo (in_array($menuKey, $menuOrang)) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $whereBadge = "a.id_marketing = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . "'";
    if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == "18") {
        $whereBadge = "1=1";
        if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
            $whereBadge = "a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
        else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
            $whereBadge = " a.id_group = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']) . "'";
    }
    $jumBadgeAll = 0;

    $sqlBadge13    = "select count(b.id_verification) as jum from pro_customer a 
            join pro_customer_verification b on a.id_customer = b.id_customer and b.is_evaluated = 1 and b.is_reviewed = 0 and b.is_active = 1 
            where " . $whereBadge;
    //$jumBadge13 = $con->getOne($sqlBadge13);
    //tolak
    $sqlreview    = "select count(b.id_verification) as jum from pro_customer a 
            join pro_customer_verification b on a.id_customer = b.id_customer
            where " . $whereBadge . " and b.is_approved = 2";
    //$jumBadge13 += $con->getOne($sqlreview);

    //$jumBadgeAll += $jumBadge13;
    $varBadge14 = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
    $whereBadge14 = "pic_user = '" . $varBadge14 . "'";
    if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == "18") {
        $whereBadge14 = "1=1";
        if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) or paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group'])) {
            $varBadge14Arr = [];
            if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
                $sqlCustomerWilayah = "select id_user from acl_user where id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
            else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
                $sqlCustomerWilayah = "select id_user from acl_user where id_group = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_group"]) . "'";
            $jumCustomerWilayah = $con->getResult($sqlCustomerWilayah);
            foreach ($jumCustomerWilayah as $k => $v) $varBadge14Arr[$k] = $v['id_user'];
            $varBadge14Arr = implode(',', $varBadge14Arr);
            $whereBadge14 = "pic_user in (" . $varBadge14Arr . ")";
        }
    }
    $sqlBadge16    = "select count(a.id_customer) as jum from pro_customer a where a.status_customer = 2 and " . $whereBadge . " 
					and timestampdiff(month, a.prospect_customer_date, date_format(now(),'%Y/%m/%d')) >= 3 and a.prospect_evaluated = 0";
    $jumBadge16 = $con->getOne($sqlBadge16);
    $jumBadgeAll += $jumBadge16;
    // Tambahan
    $sqlBadge17 = "select count(a.id_customer) as jum from pro_customer a where " . $whereBadge . " and a.need_update = 1 and a.is_generated_link = 0";
    $jumBadge17 = $con->getOne($sqlBadge17);
    $jumBadgeAll += $jumBadge17;

    $sqlpenawaran    = "select count(*) as jum from pro_penawaran p join pro_customer a on a.id_customer = p.id_customer 
                        where p.flag_approval = 2 and view = 'No' and " . $whereBadge;
    $jumpenawaran = $con->getOne($sqlpenawaran);
    $jumBadgeAll += $jumpenawaran;
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
        <li class="<?php echo (in_array($menuKey, $menuPCust)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/penawaran.php"; ?>"><i class="fa"></i> <span>Penawaran</span>
                <span class="label label-primary pull-right"><?php echo ($jumpenawaran > 0) ? $jumpenawaran : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuPLCust)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/customer-generate-link.php"; ?>"><i class="fa"></i> <span>Generate Link</span>
                <span id="menubadge17" class="label label-primary pull-right"><?php echo ($jumBadge17 > 0) ? $jumBadge17 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuPRCust)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/customer-review.php"; ?>"><i class="fa"></i> <span>Review Data Customer</span>
                <span id="menubadge13" class="label label-primary pull-right"><?php echo ($jumBadge13 > 0) ? $jumBadge13 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuCustEV)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/customer-evaluasi.php"; ?>"><i class="fa"></i> <span>Evaluasi Customer</span>
                <span id="menubadge16" class="label label-primary pull-right"><?php echo ($jumBadge16 > 0) ? $jumBadge16 : ''; ?></span></a>
        </li>
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, $menuLcr)) ? 'active' : ''; ?>">
    <?php
    $whereBadge18 = "b.id_marketing = '" . $varBadge14 . "'";
    if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == "18") {
        $whereBadge18 = "1=1";
        if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
            $whereBadge18 = "b.id_wilayah = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_wilayah"]) . "'";
        else if (!paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori' . SESSIONID]['id_group']))
            $whereBadge18 = "b.id_group = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_group"]) . "'";
    }
    $sqlBadge18    = "select count(*) as jum from pro_customer_lcr a join pro_customer b on a.id_customer = b.id_customer where a.flag_disposisi = -1 and " . $whereBadge18;
    $jumBadge18 = $con->getOne($sqlBadge18);
    //po tolak
    $sqlpo    = "select count(*) as jum from pro_po_customer a join pro_customer b on a.id_customer = b.id_customer 
						where (a.poc_approved = 2 or a.po_notif = 1) 
						and (select COALESCE((SELECT 
										            volume_close
										        FROM
										            pro_po_customer_close
										        WHERE
										            id_poc = a.id_poc AND st_Aktif = 'Y'),0) from dual) =0 
						and " . $whereBadge18;

    $jumpo = $con->getOne($sqlpo);
    ?>
    <a href="<?php echo BASE_URL_CLIENT . "/lcr.php"; ?>"><i class="fa fa-file-alt"></i> <span>LCR</span>
        <span id="menubadge18" class="label label-primary pull-right"><?php echo ($jumBadge18 > 0) ? $jumBadge18 : ''; ?></span></a>
</li>
<li class="<?php echo (in_array($menuKey, $menuPoCust)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/po-customer.php"; ?>"><i class="fa fa-file-alt"></i> <span>PO Customer</span>
        <?php /*if(in_array(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']), array("11", "18"))) { ?>
        <span id="menubadge18" class="label label-primary pull-right"><?php echo ($jumpo > 0)?$jumpo:''; ?></span>
    <?php }*/ ?>
    </a>
</li>
<li class="<?php echo (in_array($menuKey, array_merge($menuLgPl, $mnTrack))) ? 'treeview active' : 'treeview'; ?>">
    <a>
        <i class="fa fa-folder"></i> <span>Delivery</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $menuLgPl)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/pengiriman-list-marketing.php"; ?>"><i class="fa"></i> <span>List Pengiriman</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnTrack)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/tracking.php"; ?>"><i class="fa"></i> <span>Tracking</span></a>
        </li>
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, $mnRefund)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/refund-mkt.php"; ?>"><i class="fa fa-file-alt"></i> <span>Refund</span></a>
</li>

<?php
$sqlBadge21    = "select count(*) as jum from pro_po_ds_detail where is_request IN (2,3) and disposisi_request = 1 and is_approved = 0";
$jumBadge21 = $con->getOne($sqlBadge21);
?>
<?php if (paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == "18" || paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == "11" ||  paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) == "17") { ?>
    <li class="<?php echo (in_array($menuKey, $mnVerifRq)) ? 'active' : ''; ?>">
        <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-request.php"; ?>"><i class="fa fa-file-alt"></i> <span>Verifikasi Request</span>
            <span id="menubadge21" class="label label-primary pull-right"><?php echo ($jumBadge21 > 0) ? $jumBadge21 : ''; ?></span></a>
    </li>
<?php } ?>
<li class="<?php echo (in_array($menuKey, array_merge($menuPUCust, $menuCustPN, $menuCustPO, ['reservasi-ruangan'], ['peminjaman-mobil']))) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $jumBadgeAll = 0;
    $sqlBadge14    = "select count(*) as jum from pro_permintaan_penawaran where " . $whereBadge14 . " and is_delivered = 0";
    $jumBadge14 = $con->getOne($sqlBadge14);
    $jumBadgeAll += $jumBadge14;
    $sqlBadge15    = "select count(*) as jum from pro_permintaan_order where " . $whereBadge14 . " and is_delivered = 0";
    $jumBadge15 = $con->getOne($sqlBadge15);
    $jumBadgeAll += $jumBadge15;
    ?>
    <a>
        <i class="fa fa-folder"></i> <span>Request</span>
        <?php if ($jumBadgeAll > 0) { ?>
            <span class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;">
                <?php echo $jumBadgeAll; ?>
            </span>
        <?php } ?>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $menuPUCust)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/customer-permohonan-update.php"; ?>"><i class="fa"></i> <span>Permohonan Update Data</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuCustPN)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/customer-penawaran.php"; ?>"><i class="fa"></i> <span>Request Penawaran Customer</span>
                <span id="menubadge14" class="label label-primary pull-right"><?php echo ($jumBadge14 > 0) ? $jumBadge14 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuCustPO)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/customer-order.php"; ?>"><i class="fa"></i> <span>Request Order Customer</span>
                <span id="menubadge15" class="label label-primary pull-right"><?php echo ($jumBadge15 > 0) ? $jumBadge15 : ''; ?></span></a>
        </li>
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
        <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("6", "7", "11", "18", "17", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnM1)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/m-penawaran.php"; ?>"><i class="fa"></i> <span>Penawaran</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("7", "3", "4", "6", "21"))) { ?>
            <li class="<?php echo (in_array($menuKey, ['marketing-volume-report'])) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/marketing-volume-report.php"; ?>"><i class="fa"></i> <span>Marketing Volume</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("7", "3", "4", "6", "21"))) { ?>
            <li class="<?php echo (in_array($menuKey, ['marketing-active-customer'])) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/marketing-active-customer.php"; ?>"><i class="fa"></i> <span>Active Customer</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("7", "3", "4", "6", "21"))) { ?>
            <li class="<?php echo (in_array($menuKey, ['marketing-new-customer'])) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/marketing-new-customer.php"; ?>"><i class="fa"></i> <span>New Customer</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("3", "4", "6", "7", "10", "11", "18", "17", "15", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnM2)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/m-total-customer.php"; ?>"><i class="fa"></i> <span>Total Customer</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("3", "4", "5", "6", "7", "10", "11", "18", "17", "15", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnM3)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/m-customer.php"; ?>"><i class="fa"></i> <span>Customer</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("5", "6", "7", "11", "18", "17", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnM4)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/m-realisasi-order.php"; ?>"><i class="fa"></i> <span>Realisasi Order</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("5", "6", "7", "11", "18", "17", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnM5)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/m-po-customer.php"; ?>"><i class="fa"></i> <span>PO Customer</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("3", "4", "5", "6", "7", "11", "18", "17", "15", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnM6)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/m-sales-performance.php"; ?>"><i class="fa"></i> <span>Sales Performance</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("3", "4", "5", "9", "10", "13", "15", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnL1)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/l-losses.php"; ?>"><i class="fa"></i> <span>Losses</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("5", "9", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnL2)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/master-ongkos-angkut.php?" . paramEncrypt("is_report=1"); ?>"><i class="fa"></i> <span>Rekap OA</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("3", "4", "5", "9", "15", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnL3)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/l-volume-angkut.php"; ?>"><i class="fa"></i> <span>Volume Angkut Transportir</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("3", "4", "5", "6", "9", "13", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnL4)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/l-lead-time.php"; ?>"><i class="fa"></i> <span>Lead Time</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("3", "4", "5", "6", "9", "10", "13", "15", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnL5)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/l-loading-order.php"; ?>"><i class="fa"></i> <span>Loading Order</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("3", "4", "10", "15"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnF1)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/f-schedule-payment.php"; ?>"><i class="fa"></i> <span>Schedule Payment</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("3", "4", "10", "15"))) { ?>
            <!-- <li class="<?php echo (in_array($menuKey, $mnF2)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/f-refund.php"; ?>"><i class="fa"></i> <span>Refund</span></a>
            </li> -->
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("3", "4", "5", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnC1)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/c-pembelian.php"; ?>"><i class="fa"></i> <span>Pembelian</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("3", "4", "5", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnC2)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/c-area-performance.php"; ?>"><i class="fa"></i> <span>Area Performance</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("3", "4", "5", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnC3)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/c-margin.php"; ?>"><i class="fa"></i> <span>Margin</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("3", "4", "5", "16"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnC4)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/report/c-harga-market.php"; ?>"><i class="fa"></i> <span>Tren Harga Market</span></a>
            </li>
        <?php }
        if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("18"))) { ?>
            <li class="<?php echo (in_array($menuKey, $mnC6)) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/rekap-pengiriman-marketing.php"; ?>"><i class="fa"></i> <span>Rekap Pengiriman</span></a>
            </li>
        <?php } ?>
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, $mnRef7)) ? 'treeview active' : 'treeview'; ?>">
    <a>
        <i class="fa fa-folder"></i>
        <span>Referensi Data</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $mnRef7)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-harga-minyak.php"; ?>"><i class="fa"></i> <span>Daftar Harga Jual</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, array('master-penerima-refund'))) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-penerima-refund.php"; ?>"><i class="fa"></i> <span>Daftar Penerima Refund</span></a>
        </li>
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, ['database-fuel', 'database-lubricant-oil'])) ? 'treeview active' : 'treeview'; ?>">
    <a>
        <i class="fa fa-folder"></i>
        <span>Database</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, ['database-fuel'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/database-fuel.php"; ?>"><i class="fa"></i> <span>Fuel</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['database-lubricant-oil'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/database-lubricant-oil.php"; ?>"><i class="fa"></i> <span>Lubricant Oil</span></a>
        </li>
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, ['marketing-report', 'marketing-mom', 'marketing-reimbursement'])) ? 'treeview active' : 'treeview'; ?>">
    <a>
        <i class="fa fa-folder"></i>
        <span>Marketing Activity</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <?php if (in_array(paramDecrypt($param_session['id_role']), array("11", "17"))) { ?>
            <li class="<?php echo (in_array($menuKey, ['marketing-report'])) ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL_CLIENT . "/marketing-report.php"; ?>"><i class="fa"></i> <span>Marketing Report</span></a>
            </li>
        <?php } ?>
        <li class="<?php echo (in_array($menuKey, ['marketing-mom'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/marketing-mom.php"; ?>"><i class="fa"></i> <span>Marketing MoM</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['marketing-reimbursement'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/marketing-reimbursement.php"; ?>"><i class="fa"></i> <span>Marketing Reimbursement</span></a>
        </li>
    </ul>
</li>