<li class="<?php echo (in_array($menuKey, array_merge(['reservasi-ruangan'], ['peminjaman-mobil']))) ? 'treeview active' : 'treeview'; ?>">
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
<li class="<?php echo (in_array($menuKey, $mnRef)) ? 'treeview active' : 'treeview'; ?>">
    <a>
        <i class="fa fa-folder"></i>
        <span>Incentive</span>
        <span id="menubadgerole3" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"></span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, ['incentive'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/incentive.php"; ?>"><i class="fa"></i> <span>List Incentive</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['list_pengajuan_incentive', 'detail_incentive', 'incentive_add'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/list_pengajuan_incentive.php"; ?>"><i class="fa"></i> <span>List Pengajuan Incentive</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['penerima_incentive', 'add_penerima_incentive'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/penerima_incentive.php"; ?>"><i class="fa"></i> <span>Leader Penerima Incentive</span></a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['non_penerima_incentive', 'add_non_penerima_incentive'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/non_penerima_incentive.php"; ?>"><i class="fa"></i> <span>Non-incentive account list</span></a>
        </li>
    </ul>
</li>