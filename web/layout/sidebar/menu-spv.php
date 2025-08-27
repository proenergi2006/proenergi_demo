<?php
$menuSpvUrut01 = array("penawaran-approval-spv", "penawaran-approval-spv-detail");

$sessParam  = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']);
$sqlBadge1  = "
		select count(a.id_penawaran) as jum   
		from pro_penawaran a 
		join pro_master_cabang b on a.id_cabang = b.id_master 
		join pro_customer c on a.id_customer = c.id_customer 
		join acl_user d on c.id_marketing = d.id_user 
		join pro_mapping_spv e on c.id_marketing = e.id_mkt 
		where 1=1 and d.id_role in (11, 18, 17) and (a.flag_disposisi = 1) and a.flag_approval = 0 
			and e.id_spv = " . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_user']) . " 
	";
$jumBadge1  = $con->getOne($sqlBadge1);

?>
<li class="<?php echo (in_array($menuKey, $menuSpvUrut01)) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/penawaran-approval-spv.php"; ?>"><i class="fa fa-file-alt"></i> <span>Penawaran Approval</span>
        <span id="menubadge01" class="label label-primary pull-right"><?php echo ($jumBadge1 > 0) ? $jumBadge1 : ''; ?></span></a>
</li>

<!-- <li class="<?php echo (in_array($menuKey, ['marketing-volume-report'])) ? 'active' : ''; ?>">
    <a href="<?php echo BASE_URL_CLIENT . "/report/marketing-volume-report.php"; ?>"><i class="fa fa-file-alt"></i> <span>Marketing Volume</span></a>
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