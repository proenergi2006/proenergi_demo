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
<li class="<?php echo (in_array($menuKey, $mnPembayaran)) ? 'treeview active' : 'treeview'; ?>">
    <?php
    $jumBadgePembayaran = 0;
    // $sqlBadgeBpuj       = "SELECT count(id_bpuj) as jum, id_bpuj from pro_bpuj where disposisi_bpuj = '1' AND is_active='1'";
    // $resBpuj            = $con->getResult($sqlBadgeBpuj);
    // $jumBadgeBpuj       = $con->getOne($sqlBadgeBpuj);
    // $jumBadgePembayaran += $jumBadgeBpuj;

    // $sqlBadgeRefund       = "SELECT a.* FROM pro_refund a JOIN pro_invoice_admin b ON a.id_invoice=b.id_invoice JOIN pro_customer c ON b.id_customer=c.id_customer WHERE a.disposisi = 1";
    // $resRefund            = $con->getResult($sqlBadgeRefund);
    // $jumBadgeRefund       = count($resRefund);
    // $jumBadgePembayaran += $jumBadgeRefund;
    ?>
    <a>
        <i class="fa fa-folder"></i> <span>Pembayaran</span>
        <span id="menubadgedelivery" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($jumBadgePembayaran > 0) ? $jumBadgePembayaran : ''; ?></span>
        <div class="icon"><i class="fa fa-plus"></i></div>
    </a>
    <ul class="treeview-menu">
        <li class="<?php echo (in_array($menuKey, ['list_bpuj', 'detail_bpuj'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/list_bpuj.php"; ?>"><i class="fa"></i> <span>RPUJ</span>
                <span id="menubadge5" class="label label-primary pull-right"><?php echo ($jumBadgeBpuj > 0) ? $jumBadgeBpuj : ''; ?></span>
            </a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['invoice_customer', 'invoice_customer_bayar'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/invoice_customer.php"; ?>"><i class="fa"></i> <span>Invoice</span>
                <span id="menubadge5" class="label label-primary pull-right"></span>
            </a>
        </li>
        <li class="<?php echo (in_array($menuKey, ['refund', 'detail_refund'])) ? 'active' : ''; ?>">
            <a href="<?php echo BASE_URL_CLIENT . "/refund.php"; ?>"><i class="fa"></i> <span>Refund</span>
                <span id="menubadge5" class="label label-primary pull-right"></span>
            </a>
        </li>
    </ul>
</li>