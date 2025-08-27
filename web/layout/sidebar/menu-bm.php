<li class="<?php echo (in_array($menuKey, $menuOrang)) ? 'treeview active' : 'treeview'; ?>">
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
    $jumBadgeAll += $jumBadge18;
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
            <a href="<?php echo BASE_URL_CLIENT . "/customer-admin.php"; ?>"><i class="fa"></i> <span>Data Customer</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuLcr)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/lcr.php"; ?>"><i class="fa"></i> <span>LCR</span>
                <span id="menubadge18" class="label label-primary pull-right"><?php echo ($jumBadge18 > 0) ? $jumBadge18 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['refund'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/refund.php"; ?>"><i class="fa"></i> <span>Refund</span></a>
        </li>
        <li class="">
            <a href="#"><i class="fa "></i> <span>Perhitungan Insentif</span></a>
        </li>
    </ul>
</li>
<li class="<?php echo (in_array($menuKey, $mnVer)) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $jumBadgeRole3 = 0;

    $sqlBadge2  = "select count(b.id_verification) as jum from pro_customer a 
                        join pro_customer_verification b on a.id_customer = b.id_customer and b.is_evaluated = 1 and b.is_reviewed = 1 and b.is_approved = 0 
                        join acl_user c on a.id_marketing = c.id_user 
                        where 1=1";
    $sqlBadge2 .= " and b.disposisi_result = 2 and b.sm_result = 0 and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
    $jumBadge2  = $con->getOne($sqlBadge2);

    $jumBadgeRole3 += $jumBadge2;

    $sqlBadge7  = "select count(a.id_cu) as jum from pro_customer_update a 
                       join pro_customer b on a.id_customer = b.id_customer where 1=1";
    $sqlBadge7 .= " and a.flag_disposisi > 1 and a.om_result = 0 and b.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
    $jumBadge7  = $con->getOne($sqlBadge7);
    $jumBadgeRole3 += $jumBadge7;

    $sqlBadge17 = "select count(b.id_evaluasi) as jum from pro_customer a join pro_customer_evaluasi b on a.id_customer = b.id_customer 
                    join pro_master_cabang c on a.id_group = c.id_group_cabang join acl_user d on a.id_marketing = d.id_user where 1=1";
    $sqlBadge17 .= " and b.disposisi_result = 2 and b.sm_result = 0 and c.id_master = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
    $jumBadge17     = $con->getOne($sqlBadge17);
    $jumBadgeRole3 += $jumBadge17;

    $sqlBadge3  = "select count(a.id_lcr) as jum from pro_customer_lcr a join pro_customer b on a.id_customer = b.id_customer 
                        where b.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
    $sqlBadge3 .= " and a.sm_result = 0 and a.flag_disposisi > 1";
    $jumBadge3  = $con->getOne($sqlBadge3);
    $jumBadgeRole3 += $jumBadge3;

    $sessParam  = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
    $sqlBadge1  = "
			select count(a.id_penawaran) as jum 
			from pro_penawaran a 
			join pro_master_cabang b on a.id_cabang = b.id_master 
			join pro_customer c on a.id_customer = c.id_customer 
			join acl_user d on c.id_marketing = d.id_user 
			where 1=1 and d.id_role in (11, 18, 17) 
				and (a.flag_disposisi = 2 or a.flag_disposisi = 3) and a.flag_approval = 0 
				and (
					case 
						when a.flag_disposisi = 2 then a.sm_mkt_result = 0 and d.id_wilayah = '" . $sessParam . "' 
						when a.flag_disposisi = 3 then a.sm_wil_result = 0 and (a.id_cabang = '" . $sessParam . "' or d.id_wilayah = '" . $sessParam . "') 
					end
				)
		";
    $jumBadge1  = $con->getOne($sqlBadge1);
    $jumBadgeRole3 += $jumBadge1;


    $sqlBadge10  = "
    select count(a.id_dsd) as jum 
    from pro_po_ds_detail a 
    join pro_po_ds b on a.id_ds = b.id_ds 
    join pro_master_cabang c on b.id_wilayah = c.id_master 
    where a.disposisi_losses = 1 and a.losses > 0 and a.bm_result = 0 and b.id_wilayah = '" . $sessParam . "'
        
";
    $jumBadge10  = $con->getOne($sqlBadge10);
    $jumBadgeRole13 += $jumBadge10;

    $sqlSales   = "select count(a.id) as jum from pro_sales_confirmation a join pro_po_customer aa on aa.id_poc = a.id_poc left join pro_sales_confirmation_approval s on s.id_sales = a.id where 1=1";
    $sqlSales .= " and a.flag_approval = 0 and a.disposisi = 2 and s.bm_result = 0 and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";

    $jumsales = $con->getOne($sqlSales);
    $jumBadgeRole3 += $jumsales;

    $varBadge4 = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
    $sqlBadge4 = "select count(id_ppco) as jum from pro_po_customer_om where is_executed = 0 and id_wilayah = '" . $varBadge4 . "'";
    $jumBadge4 = $con->getOne($sqlBadge4);
    $jumBadgeRole3 += $jumBadge4;

    $sqlBadge5  = "select count(id_pr) as jum from pro_pr where 1=1";
    $sqlBadge5 .= " and sm_result = 0 and disposisi_pr = 2 and id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
    $jumBadge5  = $con->getOne($sqlBadge5);
    $jumBadgeRole3 += $jumBadge5;

    $sqlBadgeRefund  = "SELECT a.*, b.nama_customer FROM pro_master_penerima_refund a JOIN pro_customer b ON a.id_customer=b.id_customer WHERE a.is_bm = 0 AND a.is_active = 1 AND b.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
    $jumBadgeRefund  = $con->getResult($sqlBadgeRefund);
    $jumBadgeRole3 += count($jumBadgeRefund);


    $totalBadge = $jumBadgeRole3 + $jumBadgeRole13;
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
        <li class="<?php echo (in_array($menuKey, $menuVerPmhn)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-permohonan.php"; ?>"><i class="fa"></i> <span>Verifikasi Pemutakhiran</span>
                <span id="menubadge7" class="label label-primary pull-right"><?php echo ($jumBadge7 > 0) ? $jumBadge7 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuVerLos)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-losses.php"; ?>"><i class="fa"></i> <span>Verifikasi Losses</span>
                <span id="menubadge10" class="label label-primary pull-right"><?php echo ($jumBadge10 > 0) ? $jumBadge10 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuVerLcr)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/verifikasi-lcr.php"; ?>"><i class="fa"></i> <span>Verifikasi LCR</span>
                <span id="menubadge3" class="label label-primary pull-right"><?php echo ($jumBadge3 > 0) ? $jumBadge3 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['master-penerima-refund-bm'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/master-penerima-refund-bm.php"; ?>"><i class="fa"></i> <span>Verifikasi Penerima Refund</span>
                <span id="menubadge_refund" class="label label-primary pull-right"><?php echo (count($jumBadgeRefund) > 0) ? count($jumBadgeRefund) : ''; ?></span></a>
        </li>
        <li class="">
            <a href="<?php echo BASE_URL_CLIENT . "/pro_sales_confirmation.php"; ?>"><i class="fa"></i> <span>Sales Confirmation</span>
                <span class="label label-primary pull-right" style="margin-top: 2.5px;"><?php echo ($jumsales > 0) ? $jumsales : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuPoOM)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/po-customer-om.php"; ?>"><i class="fa"></i> <span>Persetujuan PO ke DR</span>
                <span id="menubadge4" class="label label-primary pull-right"><?php echo ($jumBadge4 > 0) ? $jumBadge4 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuPr)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/purchase-request.php"; ?>"><i class="fa"></i> <span>Delivery Request</span>
                <span id="menubadge5" class="label label-primary pull-right"><?php echo ($jumBadge5 > 0) ? $jumBadge5 : ''; ?></span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, $menuVevCust)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/evaluasi-data-customer.php"; ?>"><i class="fa"></i> <span>Evaluasi Customer</span>
                <span id="menubadge17" class="label label-primary pull-right"><?php echo ($jumBadge17 > 0) ? $jumBadge17 : ''; ?></span></a>
        </li>
    </ul>
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
        <li class="<?php echo (in_array($menuKey, $mnM1)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/m-penawaran.php"; ?>"><i class="fa"></i> <span>Penawaran</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['marketing-volume-report'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/report/marketing-volume-report.php"; ?>"><i class="fa"></i> <span>Marketing Volume Report</span></a>
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
        <!-- <li class="<?php echo (in_array($menuKey, $mnRef14)) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/attach-harga-minyak.php"; ?>"><i class="fa"></i> <span>Attachment Harga Jual</span></a>
        </li> -->
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
        <li class="<?php echo (in_array($menuKey, ['marketing-report'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/marketing-report.php"; ?>"><i class="fa"></i> <span>Marketing Report</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['marketing-mom'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/marketing-mom.php"; ?>"><i class="fa"></i> <span>Marketing MoM</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['marketing-reimbursement'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/marketing-reimbursement.php"; ?>"><i class="fa"></i> <span>Marketing Reimbursement</span></a>
        </li>
    </ul>
</li>