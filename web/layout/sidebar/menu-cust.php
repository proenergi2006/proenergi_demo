<li class="<?php echo(in_array($menuKey,$menuTrack))?'active':''; ?>">
	<a href="<?php echo BASE_URL_CLIENT."/tracking.php"; ?>"><i class="fa fa-file-alt"></i> <span>Tracking</span></a>
</li>
<li class="<?php echo(in_array($menuKey,$mnMktKrm))?'active':''; ?>">
    <a href="<?php echo BASE_URL_CLIENT."/pengiriman-list-marketing.php"; ?>"><i class="fa fa-file-alt"></i> <span>List Pengiriman</span></a>
</li>
<li class="<?php echo(in_array($menuKey,$menuUsrHd))?'active':''; ?>">
	<a href="<?php echo BASE_URL_CLIENT."/permintaan-delivery.php"; ?>"><i class="fa fa-file-alt"></i> <span>Histori Pengiriman</span></a>
</li>
<li class="<?php echo(in_array($menuKey,$menuUsrPN))?'active':''; ?>">
	<a href="<?php echo BASE_URL_CLIENT."/permintaan-penawaran.php"; ?>"><i class="fa fa-file-alt"></i> <span>Permintaan Penawaran</span></a>
</li>
<li class="<?php echo(in_array($menuKey,$mnPoTrans))?'active':''; ?>">
	<a href="<?php echo BASE_URL_CLIENT."/purchase-order-transportir.php"; ?>"><i class="fa fa-file-alt"></i> <span>Purchase Order</span>
    <span id="menubadge10" class="label label-primary pull-right"></span></a>
</li>