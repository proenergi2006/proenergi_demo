<?php
$menuKey 	= basename(BASE_SELF, ".php");
$unUsed1 	= array("master-volume", "add-master-volume");

$mnInvoice 	= array("invoice_customer", "invoice_customer_add", "invoice_customer_bayar");
$mnAdmCust 	= array("customer-admin", "customer-admin-add", "customer-admin-detail", "customer-admin-edit");
$mnAkses1 	= array("acl-menu", "add-acl-menu");
$mnAkses2 	= array("acl-roles", "add-acl-roles", "acl-roles-menu");
$mnAkses3 	= array("acl-user", "add-acl-user", "acl-user-roles", "acl-user-permission");
$mnAkses4 	= array("mapping-spv-mkt", "mapping-spv-mkt-add");
$mnAkses  	= array_merge($mnAkses1, $mnAkses2, $mnAkses3, $mnAkses4);

$mnRef1 	= array("master-group-cabang", "add-master-group-cabang");
$mnRef2 	= array("master-cabang", "add-master-cabang", "detil-master-cabang");
$mnRef3		= array("master-terminal", "add-master-terminal", "detil-master-terminal");
$mnRef4_1 	= array("master-transportir", "add-master-transportir", "detil-master-transportir");
$mnRef4_2	= array("master-transportir-sopir", "add-master-transportir-sopir", "detil-master-transportir-sopir");
$mnRef4_3	= array("master-transportir-mobil", "add-master-transportir-mobil", "detil-master-transportir-mobil");
$mnRef4_4 	= array("gps-truck");
$mnRef4 	= array_merge($mnRef4_1, $mnRef4_2, $mnRef4_3, $mnRef4_4);
$mnRef5_1 	= array("master-ongkos-angkut", "add-master-ongkos-angkut");
$mnRef5_2	= array("master-oa-kapal", "add-master-oa-kapal");
$mnRef5_3	= array("master-wilayah-angkut", "add-master-wilayah-angkut");
$mnRef5_4	= array("master-volume-angkut", "add-master-volume-angkut");
$mnRef5 	= array_merge($mnRef5_1, $mnRef5_2, $mnRef5_3, $mnRef5_4);
$mnRef6		= array("master-pbbkb", "add-master-pbbkb");
$mnRef7 	= array("master-harga-minyak", "add-master-harga-minyak", "detil-master-harga-minyak");
$mnRef8 	= array("master-approval-harga", "list-approval-harga", "add-master-harga-minyak", "detil-master-harga-minyak");
$mnRef9		= array("master-produk", "add-master-produk", "detil-master-produk");
$mnRef10	= array("master-vendor", "add-master-vendor", "detil-master-vendor");
$mnRef11	= array("master-harga-tebus", "add-master-harga-tebus", "detil-master-harga-tebus");
$mnRef12	= array("master-area", "add-master-area", "detil-master-area");
$mnRef13	= array("master-harga-pertamina", "add-master-harga-pertamina", "detil-master-harga-pertamina");
$mnRef14 	= array("attach-harga-minyak", "add-attach-harga-minyak", "detil-attach-harga-minyak");
$mnRef  	= array_merge($mnRef1, $mnRef2, $mnRef3, $mnRef4, $mnRef5, $mnRef6, $mnRef7, $mnRef8, $mnRef9, $mnRef10, $mnRef11, $mnRef12, $mnRef13, $mnRef14);

$menuPnwran  = array("penawaran-approval", "penawaran-approval-detail");
$menuVerCust = array("verifikasi-data-customer", "verifikasi-data-customer-detail");
$menuVerPmhn = array("verifikasi-permohonan", "verifikasi-permohonan-detail", "verifikasi-permohonan-data");
$menuVevCust = array("evaluasi-data-customer", "evaluasi-data-customer-detail");
$menuVerPoc  = array("verifikasi-poc", "verifikasi-poc-detail");
$menuVerLcr  = array("lcr-add", "verifikasi-lcr", "verifikasi-lcr-detail");
$menuVerPoa  = array("verifikasi-oa", "verifikasi-oa-detail");
$mnVer  	 = array_merge($menuPnwran, $menuVerCust, $menuVerPmhn, $menuVevCust, $menuVerPoc, $menuVerLcr, $menuVerPoa);

$menuPo 	 = array("purchase-order", "purchase-order-add", "purchase-order-detail");
$menuPoDs 	 = array("delivery-loading", "delivery-loading-detail");
$menuPoDk  	 = array("delivery-kapal", "delivery-kapal-add", "delivery-kapal-detail");
$menuMnSg  	 = array("manual-segel", "manual-segel-add", "manual-segel-detail");
$menuLgPl  	 = array("pengiriman-list-logistik");
$menuLgP2  	 = array("pengiriman-list-logistik-kapal");
$mnDel  	 = array_merge($menuPo, $menuPoDs, $menuPoDk, $menuMnSg, $menuLgPl, $menuLgP2);

$menuCust  	 = array("customer", "customer-add", "customer-detail");
$menuPCust 	 = array("penawaran", "penawaran-add", "penawaran-detail");
$menuPLCust  = array("customer-generate-link", "customer-generate-link-list", "customer-generate-link-email");
$menuPRCust  = array("customer-review", "customer-review-list", "customer-review-add", "customer-review-detail");
$menuPUCust  = array("customer-permohonan-update", "customer-permohonan-update-add", "customer-permohonan-update-detail");
$menuLcr  	 = array("lcr", "lcr-add", "lcr-detail");
$menuVerPoc  = array("verifikasi-poc", "verifikasi-poc-detail");
$menuPoCust  = array("po-customer", "po-customer-detail", "po-customer-add", "po-customer-plan", "po-customer-plan-add");
$menuPoAdm 	 = array("po-customer-admin");
$menuPoLog 	 = array("po-customer-logistik");
$menuPoOM 	 = array("po-customer-om", "po-customer-om-detail");

$menuPr 	 = array("purchase-request", "purchase-request-detail", "purchase-request-detail-all");
$menuFixPr 	 = array("perbaikan-data", "perbaikan-data-detail");
$menuVenPo 	 = array("vendor-po", "vendor-po-add");
$menuPrAr 	 = array("purchase-request-ar", "purchase-request-ar-detail");
$mnVenInv1   = array("vendor-inven", "vendor-inven-add");
$mnVenInv2   = array("vendor-inven-terminal", "vendor-inven-terminal-list", "vendor-inven-terminal-add");
$mnVenInv3   = array("terminal-inventory");
$mnVenInv4   = array("inven-rssp");
$mnVen  	 = array_merge($mnVenInv1, $mnVenInv2, $mnVenInv3, $mnVenInv4);

$menuUsrPN 	 = array("permintaan-penawaran", "permintaan-penawaran-add", "permintaan-penawaran-detail");
$menuUsrPO 	 = array("permintaan-order", "permintaan-order-add", "permintaan-order-detail");
$menuUsrLP 	 = array("permintaan-rekapitulasi", "permintaan-rekapitulasi-detail");
$menuUsrHd 	 = array("permintaan-delivery");
$menuCustPN  = array("customer-penawaran", "customer-penawaran-detail");
$menuCustPO  = array("customer-order", "customer-order-detail");
$menuCustEV  = array("customer-evaluasi", "customer-evaluasi-list", "customer-evaluasi-add", "customer-evaluasi-detail");
$menuOrang   = array_merge($menuCust, $menuPCust, $menuPLCust, $menuPRCust, $menuPUCust, $menuCustPN, $menuCustPO, $menuCustEV);
$menuTmDr  	 = array("terminal-dr");
$menuTmDo  	 = array("terminal-do");
$menuTmInv	 = array("terminal-inventory", "terminal-inventory-add");

$mnPoTrans 	= array("purchase-order-transportir", "purchase-order-add", "purchase-order-detail");
$mnRfTrans 	= array("referensi-transportir");
$mnPoKrm 	= array("pengiriman-list-transportir");
$mnMktKrm 	= array("pengiriman-list-marketing");
$mnTrack 	= array("tracking");
$mnRefund 	= array("refund");
$mnExport 	= array("export");
$menuForecast = array("forecast");
$mnRekPeng = array("rekap-pengiriman");
$mnRekPengNew = array("rekap-pengiriman-new");
$mnCabHo = array("monitoring");
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
$mnF1 = array("f-schedule-payment");
$mnF2 = array("f-refund");
$mnC1 = array("c-pembelian");
$mnC2 = array("c-area-performance");
$mnC3 = array("c-margin");
$mnC4 = array("c-harga-market");
$mnBpuj = array("list_bpuj_log", "detail_bpuj_log");
$mnReport = array_merge($mnM1, $mnM2, $mnM3, $mnM4, $mnM5, $mnM6, $mnL1, $mnL2, $mnL3, $mnL4, $mnL5, $mnF1, $mnF2, $mnC1, $mnC2, $mnC3, $mnC4);
$param_session = null;
if (isset($_SESSION['sinori' . SESSIONID]))
	$param_session = $_SESSION['sinori' . SESSIONID];
if ($param_session) {
	$varWilayah = paramDecrypt($param_session['id_wilayah']);
	$varGroup 	= paramDecrypt($param_session['id_group']);
	$varUser 	= paramDecrypt($param_session['id_user']);
}

?>

<li class="<?php echo (in_array($menuKey, $mnVer)) ? 'treeview active' : 'treeview'; ?>">
	<?php
	$jumBadgeRole3 = 0;

	$sqlBadge2	= "select count(b.id_verification) as jum from pro_customer a 
						join pro_customer_verification b on a.id_customer = b.id_customer and b.is_evaluated = 1 and b.is_reviewed = 1 and b.is_approved = 0 
						join acl_user c on a.id_marketing = c.id_user 
						where 1=1";
	$sqlBadge2 .= " and b.logistik_result = 0 and b.disposisi_result = 1 and a.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
	$jumBadge2 	= $con->getOne($sqlBadge2);
	$jumBadgeRole3 += $jumBadge2;

	$sqlBadge3	= "select count(a.id_lcr) as jum from pro_customer_lcr a join pro_customer b on a.id_customer = b.id_customer 
                        where b.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
	$sqlBadge3 .= " and a.logistik_result = 0 and a.flag_disposisi > 0";
	$jumBadge3 	= $con->getOne($sqlBadge3);
	$jumBadgeRole3 += $jumBadge3;
	?>
	<a>
		<i class="fa fa-folder"></i>
		<span>Verifikasi</span>
		<span id="menubadgerole3" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($jumBadgeRole3 > 0) ? $jumBadgeRole3 : ''; ?></span>
		<div class="icon"><i class="fa fa-plus"></i></div>
	</a>
	<ul class="treeview-menu">
		<li class="<?php echo (in_array($menuKey, $menuVerCust)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/verifikasi-data-customer.php"; ?>"><i class="fa"></i> <span>Verifikasi Data Customer</span>
				<span id="menubadge2" class="label label-primary pull-right"><?php echo ($jumBadge2 > 0) ? $jumBadge2 : ''; ?></span></a>
		</li>
		<li class="<?php echo (in_array($menuKey, $menuVerLcr)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/verifikasi-lcr.php"; ?>"><i class="fa"></i> <span>Verifikasi LCR</span>
				<span id="menubadge3" class="label label-primary pull-right"><?php echo ($jumBadge3 > 0) ? $jumBadge3 : ''; ?></span></a>
		</li>
	</ul>
</li>
<li class="<?php echo (in_array($menuKey, array_merge($menuPoLog, $menuPr, $menuPo, $menuPoDs, $menuLgPl, $mnTrack, $menuPoDk, $menuMnSg))) ? 'treeview active' : 'treeview'; ?>">
	<?php
	$jumBadgeDelivery = 0;
	$sqlBadge9	= "select count(id_po) as jum from pro_po where disposisi_po = 1 and is_new = 1 and po_approved = 0";
	$jumBadge9 	= $con->getOne($sqlBadge9);
	$jumBadge9 	= 0;
	$jumBadgeDelivery += $jumBadge9;

	$sqlBadge20	= "select count(*) as jum from pro_po_ds where is_submitted = 0 and id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
	$jumBadge20 = $con->getOne($sqlBadge20);
	$jumBadge20 = 0;
	$jumBadgeDelivery += $jumBadge20;

	$sqlBadge12	= "select count(*) as jum from pro_po_customer_plan a join pro_customer_lcr c on a.id_lcr = c.id_lcr where a.status_plan = 0 and a.is_approved = 1 and 
            c.id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
	$jumBadge12 = $con->getOne($sqlBadge12);
	$jumBadge12 = 0;
	$jumBadgeDelivery += $jumBadge12;

	$sqlBadge13	= "select count(id_ds) as jum from pro_po_ds where is_submitted = 0 and tanggal_ds >= '2023-01-01'";
	$sqlBadge13 .= " and id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
	$jumBadge13 = $con->getOne($sqlBadge13);
	$jumBadgeDelivery += $jumBadge13;

	$sqlBadge5	= "select count(id_pr) as jum from pro_pr where 1=1";
	$sqlBadge5 .= " and tanggal_pr >= '2024-01-01' and disposisi_pr = 6 and id_wilayah = '" . paramDecrypt($_SESSION['sinori' . SESSIONID]['id_wilayah']) . "'";
	$jumBadge5 	= $con->getOne($sqlBadge5);
	$jumBadgeDelivery += $jumBadge5;
	?>
	<a>
		<i class="fa fa-folder"></i> <span>Delivery</span>
		<span id="menubadgedelivery" class="label label-primary pull-right" style="margin-right: 20px; margin-top: 2.5px;"><?php echo ($jumBadgeDelivery > 0) ? $jumBadgeDelivery : ''; ?></span>
		<div class="icon"><i class="fa fa-plus"></i></div>
	</a>
	<ul class="treeview-menu">
		<li class="<?php echo (in_array($menuKey, $mnBpuj)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/list_bpuj_log.php"; ?>"><i class="fa"></i> <span>RPUJ</span>
				<span id="menubadge5" class="label label-primary pull-right"><?php echo ($jumBadgeBpuj > 0) ? $jumBadgeBpuj : ''; ?></span>
			</a>
		</li>
		<li class="<?php echo (in_array($menuKey, $menuPoLog)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/po-customer-logistik.php"; ?>"><i class="fa"></i> <span>Delivery Plan</span>
				<?php /* <span id="menubadge12" class="label label-primary pull-right"><?php echo ($jumBadge12 > 0)?$jumBadge12:''; ?></span> */ ?>
			</a>
		</li>
		<li class="<?php echo (in_array($menuKey, $menuPr)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/purchase-request.php"; ?>"><i class="fa"></i> <span>Delivery Request</span>
				<span id="menubadge5" class="label label-primary pull-right"><?php echo ($jumBadge5 > 0) ? $jumBadge5 : ''; ?></span>
				<?php /*<span id="menubadge5" class="label label-primary pull-right"><?php echo ($jumBadge5 > 0)?$jumBadge5:''; ?></span>*/ ?>
			</a>
		</li>
		<li class="<?php echo (in_array($menuKey, $menuPo)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/purchase-order.php"; ?>"><i class="fa"></i> <span>Purchase Order Transportir Truck</span>
				<?php /*<span id="menubadge9" class="label label-primary pull-right"><?php echo ($jumBadge9 > 0)?$jumBadge9:''; ?></span>*/ ?>
			</a>
		</li>



		<li class="<?php echo (in_array($menuKey, $menuPoDs)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/delivery-loading.php"; ?>"><i class="fa"></i> <span>Delivery Schedule</span>
				<span id="menubadge13" class="label label-primary pull-right"><?php echo ($jumBadge13 > 0) ? $jumBadge13 : ''; ?></span>
			</a>
		</li>
		<li class="<?php echo (in_array($menuKey, $menuLgPl)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/pengiriman-list-logistik.php"; ?>"><i class="fa"></i> <span>List Pengiriman Truck</span></a>
		</li>

		<li class="<?php echo (in_array($menuKey, $menuPoDk)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/delivery-kapal.php"; ?>"><i class="fa"></i> <span>Purchase Order Transportir Kapal</span></a>
		</li>

		<li class="<?php echo (in_array($menuKey, $menuLgP2)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/pengiriman-list-logistik-kapal.php"; ?>"><i class="fa"></i> <span>List Pengiriman Kapal</span></a>
		</li>

		<li class="<?php echo (in_array($menuKey, $mnTrack)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/tracking.php"; ?>"><i class="fa"></i> <span>Tracking</span></a>
		</li>

		<li class="<?php echo (in_array($menuKey, $menuMnSg)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/manual-segel.php"; ?>"><i class="fa"></i> <span>Manual Segel</span></a>
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
		<?php if ($varWilayah == "11") : ?>
			<li class="<?php echo (in_array($menuKey, ['pengisian_solar_mobil', 'add_pengisian_solar_mobil'])) ? 'active' : ''; ?>">
				<a href="<?php echo BASE_URL_CLIENT . "/pengisian_solar_mobil.php"; ?>"><i class="fa"></i> <span>Pengisian Solar Mobil Opr.</span></a>
			</li>
		<?php endif ?>
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
		<li class="<?php echo (in_array($menuKey, $mnL6)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/report/l-schedule-by-date.php"; ?>"><i class="fa"></i> <span>Schedule By Date Truck</span></a>
		</li>

		<li class="<?php echo (in_array($menuKey, $mnL7)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/report/l-schedule-by-date-kapal.php"; ?>"><i class="fa"></i> <span>Schedule By Date Kapal</span></a>
		</li>
	</ul>
</li>
<!-- <li class="<?php echo (in_array($menuKey, $mnRekPeng)) ? 'active' : ''; ?>">
	<a href="<?php echo BASE_URL_CLIENT . "/rekap-pengiriman.php"; ?>"><i class="fa fa-file-alt"></i> <span>Rekap Pengiriman</span></a>
</li> -->
<li class="<?php echo (in_array($menuKey, $mnRekPengNew)) ? 'active' : ''; ?>">
	<a href="<?php echo BASE_URL_CLIENT . "/rekap-pengiriman-new.php"; ?>"><i class="fa fa-file-alt"></i> <span>Rekap Pengiriman</span></a>
</li>