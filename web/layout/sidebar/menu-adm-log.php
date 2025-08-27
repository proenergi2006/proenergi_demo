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
$mnDel  	 = array_merge($menuPo, $menuPoDs, $menuPoDk, $menuMnSg, $menuLgPl);

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
<li class="<?php echo (in_array($menuKey, array_merge($menuPoLog, $menuPr, $menuPo, $menuPoDs, $menuLgPl, $mnTrack, $menuPoDk, $menuMnSg))) ? 'treeview active' : 'treeview'; ?>">
	<?php
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
		<li class="<?php echo (in_array($menuKey, $menuPoDs)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/delivery-loading.php"; ?>"><i class="fa"></i> <span>Delivery Schedule</span>
				<span id="menubadge13" class="label label-primary pull-right"><?php echo ($jumBadge13 > 0) ? $jumBadge13 : ''; ?></span>
			</a>
		</li>
		<li class="<?php echo (in_array($menuKey, $menuLgPl)) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/pengiriman-list-marketing.php"; ?>"><i class="fa"></i> <span>List Pengiriman</span></a>
		</li>


	</ul>
</li>

<li class="<?php echo (in_array($menuKey, array_merge(['pengisian_solar_mobil']))) ? 'treeview active' : 'treeview'; ?>">
	<a>
		<i class="fa fa-folder"></i> <span>Request</span>
		<div class="icon"><i class="fa fa-plus"></i></div>
	</a>
	<ul class="treeview-menu">
		<li class="<?php echo (in_array($menuKey, ['pengisian_solar_mobil', 'add_pengisian_solar_mobil'])) ? 'active' : ''; ?>">
			<a href="<?php echo BASE_URL_CLIENT . "/pengisian_solar_mobil.php"; ?>"><i class="fa"></i> <span>Pengisian Solar Mobil Opr.</span></a>
		</li>
	</ul>
</li>