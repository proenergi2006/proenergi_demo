<?php
$param21	= paramDecrypt($_SESSION['sinori' . SESSIONID]['terminal']);
$sqlBadge21	= "
		select count(*) from 
		(
			select a.id_dsd 
			from pro_po_ds_detail a 
			join pro_po_ds b on a.id_ds = b.id_ds
			where b.id_terminal = '" . $param21 . "' and b.is_submitted = 1 and a.is_loaded = 0
			union all
			select a.id_dsk 
			from pro_po_ds_kapal a 
			where a.terminal = '" . $param21 . "' and a.is_loaded = 0
		) a";
$jumBadge21 = $con->getOne($sqlBadge21);
?>
<li class="<?php echo (in_array($menuKey, $menuTmDr)) ? 'active' : ''; ?>">
	<a href="<?php echo BASE_URL_CLIENT . "/terminal-dr.php"; ?>"><i class="fa fa-file-alt"></i> <span>Delivery Request</span></a>
</li>
<li class="<?php echo (in_array($menuKey, $menuTmDo)) ? 'active' : ''; ?>">
	<a href="<?php echo BASE_URL_CLIENT . "/terminal-do.php"; ?>"><i class="fa fa-file-alt"></i> <span>Status Loading Truck</span>
		<span id="menubadge21" class="label label-primary pull-right"><?php echo ($jumBadge21 > 0) ? $jumBadge21 : ''; ?></span></a>
</li>
<li class="<?php echo (in_array($menuKey, $menuTmDoKpl)) ? 'active' : ''; ?>">
	<a href="<?php echo BASE_URL_CLIENT . "/terminal-do-kapal.php"; ?>"><i class="fa fa-file-alt"></i> <span>Status Loading Kapal</span>
	</a>
</li>
<li class="<?php echo (in_array($menuKey, $mnVen)) ? 'treeview active' : 'treeview'; ?>">
	<a><i class="fa fa-folder"></i> <span>Inventory</span>
		<div class="icon"><i class="fa fa-plus"></i></div>
	</a>
	<ul class="treeview-menu">
		<li class="<?php echo (in_array($menuKey, $mnVenInv3)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/terminal-inventory.php"; ?>"><i class="fa"></i> <span>Inventory Depot</span></a>
		</li>
	</ul>
</li>
<li class="<?php echo (in_array($menuKey, $mnReport)) ? 'treeview active' : 'treeview'; ?>">
	<a><i class="fa fa-folder"></i> <span>Report</span>
		<div class="icon"><i class="fa fa-plus"></i></div>
	</a>
	<ul class="treeview-menu">
		<li class="<?php echo (in_array($menuKey, $mnL1)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/report/l-losses.php"; ?>"><i class="fa"></i> <span>Losses</span></a>
		</li>
		<li class="<?php echo (in_array($menuKey, $mnL4)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/report/l-lead-time.php"; ?>"><i class="fa"></i> <span>Lead Time</span></a>
		</li>
		<li class="<?php echo (in_array($menuKey, $mnL5)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/report/l-loading-order.php"; ?>"><i class="fa"></i> <span>Loading Order</span></a>
		</li>
	</ul>
</li>