<?php
//
$menuKey 	= basename(BASE_SELF, ".php");
$mnAsmMgnt  = array("assets-management");
$unUsed1 	= array("master-volume", "add-master-volume");

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
$mnRef15 	= array("sisa-stock");
$mnRef  	= array_merge($mnRef1, $mnRef2, $mnRef3, $mnRef4, $mnRef5, $mnRef6, $mnRef7, $mnRef8, $mnRef9, $mnRef10, $mnRef11, $mnRef12, $mnRef13, $mnRef14, $mnRef15);

$menuPnwran  = array("penawaran-approval", "penawaran-approval-detail");
$menuVerCust = array("verifikasi-data-customer", "verifikasi-data-customer-detail");
$menuVerPmhn = array("verifikasi-permohonan", "verifikasi-permohonan-detail", "verifikasi-permohonan-data");
$menuVevCust = array("evaluasi-data-customer", "evaluasi-data-customer-detail");
$menuVerPoc  = array("verifikasi-poc", "verifikasi-poc-detail");
$menuVerLcr  = array("lcr-add", "verifikasi-lcr", "verifikasi-lcr-detail");
$menuVerLos = array("verifikasi-losses", "verifikasi-losses-detail", "verifikasi-losses-data");
$menuVerPoa  = array("verifikasi-oa", "verifikasi-oa-detail");
$mnVer  	 = array_merge($menuPnwran, $menuVerCust, $menuVerPmhn, $menuVerLos, $menuVevCust, $menuVerPoc, $menuVerLcr, $menuVerPoa, ['pro_sales_confirmation', 'po-customer-om', 'purchase-request', 'sales_confirmation_form']);

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
$menuVenPo 	 = array("vendor-po-new", "vendor-po-new-add");
$menuBlenPo  = array("po-blending", "po-blending-add");
$menuPrAr 	 = array("purchase-request-ar", "purchase-request-ar-detail");
$mnVenInv1   = array("vendor-inven", "vendor-inven-add");
$mnVenInv2   = array("vendor-inven-terminal-new", "vendor-inven-terminal-new-list", "vendor-inven-terminal-new-add");
$mnVenInv3   = array("terminal-inventory");
$mnVenInv4   = array("inven-rssp");
$mnVenInv5   = array("adjustment-stock", "adjustment-stock-add");
$mnVenInv6  = array("inven-stock");
$mnVenInv7    = array("sisa-stock");


$mnVen  	 = array_merge($mnVenInv1, $mnVenInv2, $mnVenInv3, $mnVenInv4, $mnVenInv5, $mnVenInv6, $mnVenInv7);


$menuVenPoNew       = array("verifikasi-po", "verifikasi-po-detail");
$menuVenPoNewCR = array("verifikasi-po-crushed-stone", "verifikasi-po-crushed-stone-detail");
$menuVenLosNew     = array("verifikasi-gain-loss", "verifikasi-gain-loss-detail");

$mnVenCeo  =  array_merge($menuVenPoNew,  $menuVenPoNewCR, $menuVenLosNew);

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
$menuTmDoKpl = array("terminal-do-kapal");
$menuTmInv	 = array("terminal-inventory", "terminal-inventory-add");

$mnPoTrans 	= array("purchase-order-transportir", "purchase-order-add", "purchase-order-detail");
$mnRfTrans 	= array("referensi-transportir");
$mnPoKrm 	= array("pengiriman-list-transportir");
$mnMktKrm 	= array("pengiriman-list-marketing");
$mnTrack 	= array("tracking");
$mnRefund 	= array("refund");
$mnVerifRq  = array("verifikasi-request", "verifikasi-request-detail");
$mnVerifRqPr  = array("verifikasi-request-pr", "verifikasi-request-detail-pr");
$mnExport 	= array("export");
$menuForecast = array("forecast");
$menuStock = array("inventory-stock-terminal");
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
$mnC9 = array("c-rekap-performance");
$mnL6 = array("l-schedule-by-date");
$mnL7 = array("l-schedule-by-date-kapal");
$mnF1 = array("f-schedule-payment");
$mnF2 = array("f-refund");
$mnC1 = array("c-pembelian");
$mnC2 = array("c-area-performance");
$mnC3 = array("c-margin");
$mnC4 = array("c-harga-market");
$mnC5 = array("c-rekap-loaded");
$mnC8 = array("c-rekap-loaded-kapal");
$mnC7 = array("c-history-stock");
$mnC6 = array("rekap-pengiriman-marketing");
$mnC10 = array("c-history-stock-operational");
$mnReport = array_merge($mnM1, $mnM2, $mnM3, $mnM4, $mnM5, $mnM6,  $mnL1, $mnL2, $mnL3, $mnL4, $mnL5, $mnL6, $mnL7, $mnF1, $mnF2, $mnC1, $mnC2, $mnC3, $mnC4, $mnC5, $mnC6,  $mnC7, $mnC8, $mnC10);
$param_session = null;
if (isset($_SESSION['sinori' . SESSIONID]))
	$param_session = $_SESSION['sinori' . SESSIONID];
if ($param_session) {
	$varWilayah = paramDecrypt($param_session['id_wilayah']);
	$varGroup 	= paramDecrypt($param_session['id_group']);
	$varUser 	= paramDecrypt($param_session['id_user']);
}

?>
<!--<aside class="left-side sidebar-offcanvas" style="background:#222d32; color:#4b646f;">-->
<aside class="left-side sidebar-offcanvas">
	<section class="sidebar">
		<div class="user-panel" style="margin-bottom:0px;">
			<div class="image">
				<img src="<?= BASE_IMAGE . "/" . paramDecrypt($param_session['foto']) ?>" width:160px; height:284px; />
			</div>
			<!--<div class="info" style="padding:15px 10px; color:#f5f5f5;">-->
			<div class="info">
				<?php if ($param_session) { ?>
					<p><?= paramDecrypt($param_session['fullname']) ?></p>
					<?php
					$sql = "
						select role_name
						from acl_role
						where id_role = '" . paramDecrypt($param_session['id_role']) . "'
					";
					$res = $con->getRecord($sql);
					?>
					<small><?= ($res ? $res['role_name'] : '-') ?></small>
				<?php } ?>
			</div>
			<p class="info-menunya">NAVIGATIONS</p>
		</div>
		<ul class="sidebar-menu" style="margin-top: -1px;">
			<li class="<?php echo ($menuKey == "home") ? 'active' : ''; ?>">
				<a href="<?php echo BASE_URL_CLIENT . "/home.php"; ?>"><i class="fa fa-home"></i> <span>Home</span></a>
			</li>
			<?php
			if ($param_session) {
				if (in_array(paramDecrypt($param_session['id_role']), array("1"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-super.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("2"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-admin.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("21"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-ceo.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("3"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-coo.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("4"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-cfo.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("5"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-purc.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("6"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-om.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("7"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-bm.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("10", "15"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-fin.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("16"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-mgr_log.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("14"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-gen.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("8"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-leg.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("11", "17", "18"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-mark.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("20"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-spv.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("9"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-log.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("13"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-term.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("12"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-trans.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("19"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-cust.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("22"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-usr.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("23"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-incentif.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("24"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-adm-log.php");
				} else if (in_array(paramDecrypt($param_session['id_role']), array("25"))) {
					require_once($public_base_directory . "/web/layout/sidebar/menu-fin-ho.php");
				}
			}
			?>
		</ul>
	</section>
</aside>