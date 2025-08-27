<li class="<?php echo (in_array($menuKey, $menuOrang)) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $whereBadge = "1=1";
    $jumBadgeAll = 0;
    $sqlBadge13 = "select count(b.id_verification) as jum from pro_customer a 
            join pro_customer_verification b on a.id_customer = b.id_customer and b.is_evaluated = 1 and b.is_reviewed = 0 and b.is_active = 1 
            where " . $whereBadge;
    // $jumBadge13 = $con->getOne($sqlBadge13);
    $jumBadge13 = 0;
    //tolak
    $sqlreview  = "select count(b.id_verification) as jum from pro_customer a join pro_customer_verification b on a.id_customer = b.id_customer and b.is_evaluated = 1 and b.is_reviewed = 0 and b.is_active = 1 where " . $whereBadge;
    $jumBadge13 += $con->getOne($sqlreview);

    $jumBadgeAll += $jumBadge13;
    $varBadge14 = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']);
    $whereBadge14 = "1=1";
    $sqlBadge14 = "select count(*) as jum from pro_permintaan_penawaran where " . $whereBadge14 . " and is_delivered = 0";
    $jumBadge14 = $con->getOne($sqlBadge14);
    $jumBadgeAll += $jumBadge14;
    $sqlBadge15 = "select count(*) as jum from pro_permintaan_order where " . $whereBadge14 . " and is_delivered = 0";
    $jumBadge15 = $con->getOne($sqlBadge15);
    $jumBadgeAll += $jumBadge15;
    $sqlBadge16 = "select count(a.id_customer) as jum from pro_customer a where a.status_customer = 2 and " . $whereBadge . " 
                    and timestampdiff(month, a.prospect_customer_date, date_format(now(),'%Y/%m/%d')) >= 3 and a.prospect_evaluated = 0";
    $jumBadge16 = $con->getOne($sqlBadge16);
    $jumBadgeAll += $jumBadge16;
    // Tambahan
    $sqlBadge17 = "select count(a.id_customer) as jum from pro_customer a where " . $whereBadge . " and a.need_update = 1 and a.is_generated_link = 0";
    $jumBadge17 = $con->getOne($sqlBadge17);
    $jumBadgeAll += $jumBadge17;

    $sqlpenawaran   = "select count(*) as jum from pro_penawaran p join pro_customer a on a.id_customer = p.id_customer 
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
        <li class="<?php echo (in_array($menuKey, $menuPUCust)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/customer-permohonan-update.php"; ?>"><i class="fa"></i> <span>Permohonan Update Data</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuCustPN)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/customer-penawaran.php"; ?>"><i class="fa"></i> <span>Permintaan Penawaran</span>
                <span id="menubadge14" class="label label-primary pull-right"><?php echo ($jumBadge14 > 0) ? $jumBadge14 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuCustPO)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/customer-order.php"; ?>"><i class="fa"></i> <span>Order Customer</span>
                <span id="menubadge15" class="label label-primary pull-right"><?php echo ($jumBadge15 > 0) ? $jumBadge15 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuCustEV)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/customer-evaluasi.php"; ?>"><i class="fa"></i> <span>Evaluasi Customer</span>
                <span id="menubadge16" class="label label-primary pull-right"><?php echo ($jumBadge16 > 0) ? $jumBadge16 : ''; ?></span></a>
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
<li class="<?php echo (in_array($menuKey, $menuPoCust)) ? 'active' : ''; ?>">
    <?php
    //po tolak
    $sqlpo  = "select count(*) as jum from pro_po_customer a join pro_customer b on a.id_customer = b.id_customer 
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
    <a href="<?php echo BASE_URL_CLIENT . "/po-customer.php"; ?>"><i class="fa fa-file-alt"></i> <span>PO Customer</span>
        <?php if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array("11", "18"))) { ?>
            <span id="menubadge18" class="label label-primary pull-right"><?php echo ($jumpo > 0) ? $jumpo : ''; ?></span>
        <?php } ?>
    </a>
</li>
<li class="<?php echo (in_array($menuKey, $mnAdmCust)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/customer-admin.php"; ?>"><i class="fa fa-file-alt"></i> <span>Customer</span></a>
</li>
<li class="<?php echo (in_array($menuKey, $mnAsmMgnt)) ? 'active' : ''; ?>">
    <a href="http://syop.proenergi.com/ams/welcome?id_user=<?= paramDecrypt($param_session['id_user']) ?>&fullname=<?= paramDecrypt($param_session['fullname']) ?>&department=<?= paramDecrypt($param_session['department']) ?>&id_role=<?= paramDecrypt($param_session['id_role']) ?>"><i class="fa fa-file-alt"></i> <span>Assets Management</span></a>
</li>

<li class="<?php echo (in_array($menuKey, $mnExport)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/export.php"; ?>"><i class="fa fa-file-alt"></i> <span>Export AR</span></a>
</li>
<li class="<?php echo (in_array($menuKey, array_merge($menuPoLog, $menuPr, $menuPo, $menuPoDs, $menuLgPl, $mnTrack, $menuPoDk, $menuMnSg))) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $jumBadgeDelivery = 0;
    $sqlBadge9  = "select count(id_po) as jum from pro_po where disposisi_po = 1 and is_new = 1 and po_approved = 0";
    $jumBadge9  = $con->getOne($sqlBadge9);
    $jumBadgeDelivery += $jumBadge9;
    $sqlBadge20 = "select count(*) as jum from pro_po_ds where is_submitted = 0";
    $jumBadge20 = $con->getOne($sqlBadge20);
    $jumBadgeDelivery += $jumBadge20;
    $sqlBadge12 = "select count(*) as jum from pro_po_customer_plan a join pro_customer_lcr c on a.id_lcr = c.id_lcr where a.status_plan = 0 and a.is_approved = 1";
    $jumBadge12 = $con->getOne($sqlBadge12);
    $jumBadgeDelivery += $jumBadge12;
    // $sqlBadge5  = "select count(id_pr) as jum from pro_pr where 1=1";
    // $sqlBadge5 .= " and logistik_result = 1 and disposisi_pr = 5";
    // $jumBadge5  = $con->getOne($sqlBadge5);
    $jumBadge5  = 0;
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
            <a href="<?php echo BASE_URL_CLIENT . "/pengiriman-list-logistik.php"; ?>"><i class="fa"></i> <span>List Pengiriman</span></a>
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
<li class="<?php echo (in_array($menuKey, $menuGN)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/generate-number.php"; ?>"><i class="fa fa-random"></i> <span>Generate Number</span></a>
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
<li class="<?php echo (in_array($menuKey, array_merge(['reservasi-ruangan-master'], ['peminjaman-mobil-master'], ['peminjaman-zoom-master']))) ? 'treeview active' : 'treeview'; ?>">
    <a>
        <i class="fa fa-folder"></i> <span>Data Master</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, ['reservasi-ruangan-master'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/reservasi-ruangan-master.php"; ?>"><i class="fa"></i> <span>Ruang Meeting</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['peminjaman-mobil-master'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/peminjaman-mobil-master.php"; ?>"><i class="fa"></i> <span>Mobil Oprasional</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['peminjaman-zoom-master'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/peminjaman-zoom-master.php"; ?>"><i class="fa"></i> <span>Zoom Meeting</span></a>
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
            <a href="<?php echo BASE_URL_CLIENT . "/peminjaman-mobil.php"; ?>"><i class="fa"></i> <span>Peminjaman Mobil Opr</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['peminjaman-zoom'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/peminjaman-zoom.php"; ?>"><i class="fa"></i> <span>Peminjaman Akun Zoom</span></a>
        </li>
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, $mnAkses)) ? 'treeview active' : 'treeview'; ?>">
    <a><i class="fa fa-folder"></i> <span>Access Control</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, $mnAkses1)) ? 'active' : ''; ?>" style="display: none;">
            <a href="<?php echo BASE_URL_CLIENT . "/acl-menu.php"; ?>"><i class="fa"></i> <span>Menu Management</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnAkses2)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/acl-roles.php"; ?>"><i class="fa"></i> <span>Role Management</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnAkses3)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/acl-user.php"; ?>"><i class="fa"></i> <span>User Management</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnAkses4)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/mapping-spv-mkt.php"; ?>"><i class="fa"></i> <span>Mapping Supervisor</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnAkses5)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/approval-inv.php"; ?>"><i class="fa"></i> <span>Approval Invoice</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnAkses6)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/button-control.php"; ?>"><i class="fa"></i> <span>Button Control</span></a>
        </li>
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, $mnRef)) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $jumBadgeRole3 = 0;
    $sqlBadge6 = "select count(*) as jum from (select distinct periode_awal, periode_akhir, id_area, produk from pro_master_harga_minyak where is_evaluated = 1 and is_approved = 0) a";
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
        <li class="<?php echo (in_array($menuKey, $mnRef1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-group-cabang.php"; ?>"><i class="fa"></i> <span>Wilayah</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef2)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-cabang.php"; ?>"><i class="fa"></i> <span>Cabang Penagihan</span></a>
        </li>
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
            <a href="<?php echo BASE_URL_CLIENT . "/master-approval-harga.php"; ?>"><i class="fa"></i> <span>Persetujuan Harga Jual</span>
                <span id="menubadge6" class="label label-primary pull-right"><?php echo ($jumBadge6 > 0) ? $jumBadge6 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef7)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-harga-minyak.php"; ?>"><i class="fa"></i> <span>Daftar Harga Jual</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef14)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/attach-harga-minyak.php"; ?>"><i class="fa"></i> <span>Attachment Harga Jual</span></a>
        </li>
        <!-- <li class="<?php echo (in_array($menuKey, $mnRef4)) ? 'treeview active' : 'treeview'; ?>">
            <a><i class="fa"></i> <span>Transportir </span><div class="icon"><i class="fa fa-plus"></i></div></a>
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
            </ul>
        </li> -->
        <li class="<?php echo (in_array($menuKey, $mnRef10)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-vendor.php"; ?>"><i class="fa"></i> <span>Vendor</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef11)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-harga-tebus.php"; ?>"><i class="fa"></i> <span>Harga Tebus</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef13)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-harga-pertamina.php"; ?>"><i class="fa"></i> <span>Harga Dasar Pertamina</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnRef14)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-harga-bpuj.php"; ?>"><i class="fa"></i> <span>Harga Dasar BPUJ</span></a>
        </li>
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, $mnReport)) ? 'treeview active' : 'treeview'; ?>">
    <a><i class="fa fa-folder"></i> <span>Report</span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, ['report-monthly'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/report-monthly.php"; ?>"><i class="fa"></i> <span>Report Monthly</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $mnM1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/m-penawaran.php"; ?>"><i class="fa"></i> <span>Penawaran</span></a>
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
        <li class="<?php echo (in_array($menuKey, $mnF2)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/f-refund.php"; ?>"><i class="fa"></i> <span>Refund</span></a>
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