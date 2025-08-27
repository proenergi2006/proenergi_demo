<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$sesrole = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$id_wilayah = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);

	$sql = "
		select 
		a.id_customer, a.id_marketing, a.id_wilayah, a.id_group, a.kode_pelanggan, a.nama_customer, a.alamat_customer, a.prov_customer, a.kab_customer, a.postalcode_customer, 
		a.telp_customer, a.fax_customer, a.email_customer, a.website_customer, a.tipe_bisnis, a.tipe_bisnis_lain, a.ownership, a.ownership_lain, 
		a.nomor_sertifikat, a.nomor_sertifikat_file, a.nomor_npwp, a.nomor_npwp_file, a.nomor_siup, a.nomor_siup_file, 
		a.nomor_tdp, a.nomor_tdp_file, a.dokumen_lainnya, a.dokumen_lainnya_file, 
		a.need_update, a.is_generated_link, a.count_update, a.is_verified, a.status_customer, 
		a.prospect_customer_date, a.prospect_evaluated, a.fix_customer_since, a.fix_customer_redate, 
		a.jenis_payment, a.top_payment, a.jenis_net, a.credit_limit, a.credit_limit_diajukan, 
		a.id_verification, a.ajukan, a.jenis_customer, a.induk_perusahaan, a.kecamatan_customer, a.kelurahan_customer, 
		a.lastupdate_time, a.lastupdate_ip, a.lastupdate_by, 		
				
		b.pic_decision_name, b.pic_decision_position, b.pic_decision_telp, b.pic_decision_mobile, b.pic_decision_email, 
		b.pic_ordering_name, b.pic_ordering_position, b.pic_ordering_telp, b.pic_ordering_mobile, b.pic_ordering_email, 
		b.pic_billing_name, b.pic_billing_position, b.pic_billing_telp, b.pic_billing_mobile, b.pic_billing_email, 
		b.pic_invoice_name, b.pic_invoice_position, b.pic_invoice_telp, b.pic_invoice_mobile, b.pic_invoice_email, 
		b.product_delivery_address, b.invoice_delivery_addr_primary, b.invoice_delivery_addr_secondary, 
		b.pic_fuelman_name, b.pic_fuelman_position, b.pic_fuelman_telp, b.pic_fuelman_mobile, b.pic_fuelman_email, 
				
		d.email_billing, d.alamat_billing, d.prov_billing, d.kab_billing, d.postalcode_billing, d.telp_billing, d.fax_billing, 
		d.payment_schedule, d.payment_schedule_other, d.payment_method, d.payment_method_other, d.invoice, d.ket_extra, 
		d.kecamatan_billing, d.kelurahan_billing, d.calculate_method, d.bank_name, d.curency, d.bank_address, d.account_number, 
		d.credit_facility, d.creditor, 
		
		e.logistik_area, e.logistik_bisnis, e.logistik_env, e.logistik_env_other, e.logistik_storage, e.logistik_storage_other, e.logistik_hour, e.logistik_hour_other, 
		e.logistik_volume, e.logistik_volume_other, e.logistik_quality, e.logistik_quality_other, e.logistik_truck, e.logistik_truck_other, 
		e.desc_stor_fac, e.desc_condition, e.supply_shceme, e.specify_product, e.volume_per_month, e.operational_hour_from, e.operational_hour_to, e.nico, 

		f.nama_prov as propinsi_customer, 
		g.nama_kab as kabupaten_customer, 
		h.nama_prov as propinsi_payment, 
		i.nama_kab as kabupaten_payment, 
		
		j.token_verification, j.is_evaluated, j.is_reviewed, j.is_active, 
		j.legal_data, j.legal_summary, j.legal_result, j.legal_tgl_proses, j.legal_pic, 
		j.finance_data, j.finance_summary, j.finance_result, j.finance_tgl_proses, j.finance_pic, j.jenis_datanya, j.finance_data_kyc, 
		j.logistik_data, j.logistik_summary, j.logistik_result, j.logistik_tgl_proses, j.logistik_pic, 
		j.sm_summary, j.sm_result, j.sm_tgl_proses, j.sm_pic, 
		j.om_summary, j.om_result, j.om_tgl_proses, j.om_pic, 
		j.cfo_summary, j.cfo_result, j.cfo_tgl_proses, j.cfo_pic, 
		j.ceo_summary, j.ceo_result, j.ceo_tgl_proses, j.ceo_pic, 
		j.disposisi_result, j.is_approved, j.role_approve, j.tanggal_approved, 
		
		k.nama_cabang as wilayah, 
		
		l.id_review, l.review1, l.review2, l.review3, l.review4, l.review5, l.review6, l.review7, l.review8, l.review9, l.review10, 
		l.review11, l.review12, l.review13, l.review14, l.review15, l.review16, 
		l.review_result, l.review_pic, l.review_tanggal, l.review_summary, l.review_attach, l.review_attach_ori, 
		l.jenis_asset, l.kelengkapan_dok_tagihan, l.alur_proses_periksaan, 
		l.jadwal_penerimaan, l.background_bisnis, l.lokasi_depo, l.opportunity_bisnis, 

		'' as testajabos 

		from pro_customer a 
		left join pro_customer_contact b on a.id_customer = b.id_customer 
		left join pro_customer_payment d on a.id_customer = d.id_customer 
		left join pro_customer_logistik e on a.id_customer = e.id_customer 
		left join pro_master_provinsi f on a.prov_customer = f.id_prov 
		left join pro_master_kabupaten g on a.kab_customer = g.id_kab 
		left join pro_master_provinsi h on d.prov_billing = h.id_prov 
		left join pro_master_kabupaten i on d.kab_billing = i.id_kab 
		left join pro_customer_verification j on a.id_customer = j.id_customer 
		left join pro_customer_review l on j.id_verification = l.id_verification 
		left join pro_master_cabang k on a.id_wilayah = k.id_master and a.id_group = k.id_group_cabang 
		where j.id_verification = '".$idr."'
	";
	$rsm = $con->getRecord($sql);
	
	$sql_file = "
		select * from pro_customer_review_attchment 
		where id_review = '".$rsm['id_review']."' and id_verification = '".$idr."'
	";
    $rsm_file = $con->getResult($sql_file);

	$_SESSION['sinori'.SESSIONID]['id_customer'] = $rsm["id_customer"];
	$base_directory		= $public_base_directory."/files/uploaded_user/images";
	$alldokumenraw 		= explode(",", $rsm['dokumen_lainnya_file']);
	$alldokumen 		= array();
	$alldokumen_link 	= array();
	for($i = 0; $i < count($alldokumenraw); $i++) {
		$alldokumen[] = $alldokumenraw[$i];
		$alldokumen_link[] = $base_directory."/dokumen_lainnya_file".$rsm["id_customer"]."_".$alldokumenraw[$i];
	}

	$pnwrn_sql = "
		select a.*, 
		b.nama_customer, b.top_payment, b.status_customer, 
		c.fullname, 
		d.nama_cabang, 
		e.jenis_produk, e.merk_dagang, 
		f.harga_normal, f.harga_sm, f.harga_om, 
		g.nama_area 
		from pro_penawaran a 
		join pro_customer b on a.id_customer = b.id_customer 
		join acl_user c on b.id_marketing = c.id_user 
		join pro_master_cabang d on a.id_cabang = d.id_master 
		join pro_master_produk e on a.produk_tawar = e.id_master 
		join pro_master_area g on a.id_area = g.id_master 
		left join pro_master_harga_minyak f on a.masa_awal = f.periode_awal and a.masa_akhir = f.periode_akhir and a.id_area = f.id_area and a.pbbkb_tawar = f.pajak and f.is_approved = 1 
		where a.id_customer = ".$rsm["id_customer"]." 
		ORDER BY a.id_penawaran DESC
	";
	$rpnwrnarr 	= $con->getResult($pnwrn_sql);
	$rpnwrn 	= $rpnwrnarr[0] ?? [];
	$history 	= $rpnwrnarr;
	unset($history[0]);
	$link_email 	= BASE_URL_CLIENT."/send-email-customer.php?".paramEncrypt("idv=".$idr."&idk=".$rsm['id_customer']);
	$link_email_hrd	= BASE_URL_CLIENT."/send-email-hrd.php?".paramEncrypt("cust_id=".$rsm['id_customer']);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber"))); ?>
<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1><?php echo 'Verifikasi Data '.$rsm['nama_customer']; ?></h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
				<ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                    	<a href="#form-evaluation" aria-controls="form-evaluation" role="tab" data-toggle="tab">Evaluasi Calon Customer</a>
                    </li>
                    <li role="presentation">
                    	<a href="#data-evaluation" aria-controls="data-evaluation" role="tab" data-toggle="tab">Data Customer</a>
                    </li>
                    <li role="presentation">
                        <a href="#data-review" aria-controls="data-review" role="tab" data-toggle="tab">Review Customer</a>
                    </li>
					<?php if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10) { ?>
					<li role="presentation">
                        <a href="#data-view" aria-controls="data-view" role="tab" data-toggle="tab">Penawaran</a>
                    </li>
					<?php } ?>
                </ul>
                
                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane active" id="form-evaluation">
                        <form action="<?php echo ACTION_CLIENT.'/evaluation-prospective.php'; ?>" id="gform" name="gform" class="form-horizontal" enctype="multipart/form-data" method="post">
                        <input type="hidden" name="idc" value="<?php echo $rsm['id_customer'];?>" />
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Form Evaluasi</h3>
                            </div>
                            <div class="box-body">
                                <?php
                                    if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 9)
                                        require_once($public_base_directory."/web/__get_data_customer_logistik.php");
                                    else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10)
                                        require_once($public_base_directory."/web/__get_data_customer_finance.php");
                                    else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 7)
                                        require_once($public_base_directory."/web/__get_data_customer_sm.php");
                                    else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 6)
                                        require_once($public_base_directory."/web/__get_data_customer_om.php");
                                ?>
                            </div>
                        </div>
                        </form>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="data-evaluation">
                    	<?php require_once($public_base_directory."/web/__get_data_customer.php"); ?>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="data-review">
                    	<?php require_once($public_base_directory."/web/__get_review_customer.php"); ?>
                    </div>
					<?php if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 10) { ?>
						<div role="tabpanel" class="tab-pane with-border" id="data-view">
							<div class="box box-primary">
								<div class="box-header with-border">
									<h3 class="box-title"><label>Detail Penawaran</label></h3>
								</div>
								<div class="box-body">
									<div class="col-sm-6">
										<h4>Data Penawaran di tanggal : <?php echo date('d/m/Y', strtotime($rpnwrn['created_time'])); ?></h4>
										<div class="table-responsive">
											<table class="table table-bordered table-summary">
												<thead>
													<tr>
														<th colspan="2">SUMMARY</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td width="150">Nama Customer</td>
														<td><?php echo $rpnwrn['nama_customer'];?></td>
													</tr>
													<tr>
														<td>Volume</td>
														<td><?php echo number_format($rpnwrn['volume_tawar'])." Liter";?></td>
													</tr>
													<tr>
														<td>Refund</td>
														<td><?php echo ($rpnwrn['refund_tawar'])?number_format($rpnwrn['refund_tawar']):'-'; ?></td>
													</tr>
													<tr>
														<td>Ongkos Angkut</td>
														<td><?php echo number_format($rpnwrn['oa_kirim']);?></td>
													</tr>
													<tr>
														<td>Other Cost</td>
														<td><?php echo number_format($rpnwrn['other_cost']);?></td>
													</tr>
													<?php //if($rpnwrn['perhitungan'] == 1){ ?>
													<tr>
														<td>Harga Penawaran</td>
														<td><?php echo number_format($rpnwrn['harga_dasar']); ?></td>
													</tr>
													<?php //} else { ?>
													<tr>
														<td colspan="2">
															<p style="margin:0px 5px 5px;">Dengan rincian sebagai berikut:</p>
															<div class="clearfix">
																<div class="col-sm-10 col-md-8">
																	<div class="table-responsive">
																		<table class="table table-bordered">
																			<thead>
																				<th class="text-center" width="10%">NO</th>
																				<th class="text-center" width="40%">RINCIAN</th>
																				<th class="text-center" width="10%">NILAI</th>
																				<th class="text-center" width="40%">HARGA</th>
																			</thead>
																			<tbody>
																			<?php
																				$rincian = json_decode($rpnwrn['detail_rincian'], true);
																				$nom=0;
																				foreach($rincian as $arr1){
																					$nom++;
																					$cetak = 1;// $arr1['rinci'];
																					$nilai = $arr1['nilai'];
																					$biaya = ($arr1['biaya'])?number_format($arr1['biaya']):'';
																					$jenis = $arr1['rincian'];
																					if($cetak) {
																			?>
																				<tr>
																					<td class="text-center"><?php echo $nom;?></td>
																					<td class="text-left"><?php echo $jenis;?></td>
																					<td class="text-right"><?php echo ($nilai ? $nilai." %" : "");?></td>
																					<td class="text-right"><?php echo $biaya;?></td>
																				</tr>
																			<?php } } ?>
																			</tbody>
																		</table>
																	</div>
																</div>
															</div>
														</td>
													</tr>
													
													<?php //} ?>
													<tr>
														<td>Pricelist</td>
														<td><?php echo number_format($rpnwrn['harga_normal']); ?></td>
													</tr>
													<?php if($rpnwrn['id_cabang']){ ?>
													<tr>
														<td>Harga BM</td>
														<td><?php echo number_format($rpnwrn['harga_sm']); ?></td>
													</tr>
													<?php } if(($sesrole == 6 || $sesrole == 3)){ ?>
													<tr>
														<td>Harga OM</td>
														<td><?php echo number_format($rpnwrn['harga_om']); ?></td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
										<div class="form-group row">
											<div class="col-sm-8">
												<small style="font-weight: bold;">Catatan Marketing/Key Account</small>
												<div class="form-control" style="height:auto; font-size: 12px;"><?php echo ($rpnwrn['catatan']?$rpnwrn['catatan']:'&nbsp;'); ?></div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-sm-8">
												<small style="font-weight: bold;">Catatan Branch Manager Cabang</small>
												<div class="form-control" style="height:auto; font-size: 12px;">
													<?php  echo 'Status : '.($rpnwrn['sm_wil_result']=='1'?'Disetujui':'Ditolak') ?><br>
													<?php echo $rpnwrn['sm_wil_summary']; ?>
													<p style="margin:10px 0 0; font-size:12px;"><i>
														<?php echo $rpnwrn['sm_wil_pic']." - ".date("d/m/Y H:i:s", strtotime($rpnwrn['sm_wil_tanggal']))." WIB";?>
													</i></p>
												</div>
											</div>
										</div>
										<?php if($rpnwrn['om_summary']) {?>
										<hr style="margin:0 0 15px;">
										<div class="form-group row">
                                            <div class="col-sm-8">
                                                <small style="font-weight: bold;">Catatan Operation Manager</small>
                                                <div class="form-control" style="height:auto; font-size: 12px;">
													<?php  echo 'Status : '.($rpnwrn['om_result']=='1'?'Disetujui':'Ditolak') ?><br>
													<?php echo $rpnwrn['om_summary']; ?>
                                                    <p style="margin:10px 0 0; font-size:12px;"><i>
														<?php echo $rpnwrn['om_pic']." - ".date("d/m/Y H:i:s", strtotime($rpnwrn['om_tanggal']))." WIB";?>
													</i></p>
                                                </div>
                                            </div>
                                        </div>
										<?php } ?>
										
										<?php if($rpnwrn['ceo_summary']) {?>
										<hr style="margin:0 0 15px;">
										<div class="form-group row">
                                            <div class="col-sm-8">
                                                <small style="font-weight: bold;">Catatan COO</small>
                                                <div class="form-control" style="height:auto; font-size: 12px;">
													<?php  echo 'Status : '.($rpnwrn['ceo_result']=='1'?'Disetujui':'Ditolak') ?><br>
													<?php echo $rpnwrn['ceo_summary']; ?>
                                                    <p style="margin:10px 0 0; font-size:12px;"><i>
														<?php echo $rpnwrn['ceo_pic']." - ".date("d/m/Y H:i:s", strtotime($rpnwrn['ceo_tanggal']))." WIB";?>
													</i></p>
                                                </div>
                                            </div>
                                        </div>
										<?php } ?>
									</div>
									<?php // if ($id_wilayah==2) { // for jkt ?>
									<div class="col-sm-6">
										<h4>History Penawaran</h4>
										<div style="height: 800px; overflow-y: scroll;">
											<div class="row">
												<?php
					                                foreach ($history as $key => $rsm) {
					                                    $rsm = (array) $rsm;
					                                    $rincian = json_decode($rsm['detail_rincian'], true);
					                                    $formula = json_decode($rsm['detail_formula'], true);
					                                    $sesrole = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
					                                    $seswil  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
					                                    $notesm1 = ($rsm['sm_mkt_summary']);
					                                    $notesm2 = ($rsm['sm_wil_summary']);
					                                    $noteopm = ($rsm['om_summary']);
					                                    $noteceo = ($rsm['ceo_summary']);
					                                    $arrStat = array(1=>"Disetujui", "Ditolak");
					                                    $simpan  = true;
					                                    
					                                    $arrKondInd = array(1=>"Setelah Invoice diterima", "Setelah pengiriman");
					                                    $arrKondEng = array(1=>"After Invoice Receive", "After Delivery");
					                                    $jenis_net  = $rsm['jenis_net'];
					                                    $arrPayment = array("CREDIT"=>"CREDIT ".$rsm['jangka_waktu']." hari ".$arrKondInd[$jenis_net], "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");
					                            ?>
					                                    <div class="col-sm-12">
					                                        <div class="box box-primary">
					                                            <div class="box-body">
					                                                <label>- Data Penawaran di tanggal : <?php echo date('d/m/Y', strtotime($rsm['created_time'])); ?></label>
					                                                <div class="table-responsive">
					                                                    <table class="table no-border">
					                                                        <tr>
					                                                            <td width="180">Nama Customer</td>
					                                                            <td width="10" class="text-center">:</td>
					                                                            <td><?php echo $rsm['nama_customer'];?></td>
					                                                        </tr>
					                                                        <tr>
					                                                            <td>Cabang Invoice</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo $rsm['nama_cabang'];?></td>
					                                                        </tr>
					                                                        <tr>
					                                                            <td>Marketing</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo $rsm['fullname'];?></td>
					                                                        </tr>
					                                                        <tr>
					                                                            <td>PIC Customer</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo $rsm['gelar'].' '.$rsm['nama_up']; echo ($rsm['jabatan_up'])?" (<i>".$rsm['jabatan_up']."</i>)":""; ?></td>
					                                                        </tr>
					                                                        <tr>
					                                                            <td>Alamat Korespondensi</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo $rsm['alamat_up'];?></td>
					                                                        </tr>
					                                                        <tr>
					                                                            <td>Telepon</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo $rsm['telp_up'];?></td>
					                                                        </tr>
					                                                        <tr>
					                                                            <td>Fax</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo $rsm['fax_up'];?></td>
					                                                        </tr>
					                                                        <tr>
					                                                            <td>TOP Customer</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo $arrPayment[$rsm['jenis_payment']];?></td>
					                                                        </tr>
					                                                    </table>
					                                                    <hr style="margin:0px 0px 10px; color:#ccc;" />
					                                                    <table class="table no-border">
					                                                        <tr>
					                                                            <td width="180">Nomor Referensi</td>
					                                                            <td width="10" class="text-center">:</td>
					                                                            <td><?php echo $rsm['nomor_surat'];?></td>
					                                                        </tr>
					                                                        <tr>
					                                                            <td>Area</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo $rsm['nama_area'];?></td>
					                                                        </tr>
					                                                        <tr>
					                                                            <td>Produk</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo $rsm['merk_dagang'];?></td>
					                                                        </tr>
					                                                        <tr>
					                                                            <td>Volume</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo number_format($rsm['volume_tawar'])." Liter";?></td>
					                                                        </tr>
					                                                        <tr>
					                                                            <td>Order Method</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo $rsm['method_order']." hari sebelum pickup";?></td>
					                                                        </tr>
					                                                        <tr>
					                                                            <td>Masa berlaku harga</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo tgl_indo($rsm['masa_awal'])." - ".tgl_indo($rsm["masa_akhir"]);?></td>
					                                                        </tr>
					                                                        <?php if(($sesrole == 6 || $sesrole == 3)){ ?>
					                                                        <tr>
					                                                            <td>Harga Terendah OM</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo number_format($rsm['harga_om']); ?></td>
					                                                        </tr>
					                                                        <?php } if($rsm['id_cabang']){ ?>
					                                                        <tr>
					                                                            <td>Harga Terendah BM</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo number_format($rsm['harga_sm']); ?></td>
					                                                        </tr>
					                                                        <?php } if($rsm['perhitungan'] == 1){ ?>
					                                                        <tr>
					                                                            <td>Harga perliter</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo number_format($rsm['harga_dasar']); ?></td>
					                                                        </tr>
					                                                        <?php } ?>
					                                                        <tr>
					                                                            <td>Refund</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo ($rsm['refund_tawar'])?number_format($rsm['refund_tawar']):'-'; ?></td>
					                                                        </tr>
					                                                        <tr>
					                                                            <td>Keterangan Harga</td>
					                                                            <td class="text-center">:</td>
					                                                            <td><?php echo ($rsm['ket_harga'])?$rsm['ket_harga']:'-';?></td>
					                                                        </tr>
					                                                    </table>
					                                                </div>
					                
					                                                <?php
					                                                    $breakdown = true;
					                                                    // $breakdown = false;
					                                                    // foreach($rincian as $temp){
					                                                    //     $breakdown = $breakdown || $temp["rinci"];
					                                                    // }
					                                                    // if($breakdown && $rsm['perhitungan'] == 1){
					                                                    if($breakdown){
					                                                        $nom = 0;
					                                                ?>
					                                                <p style="margin:0px 5px 5px;">Dengan rincian sebagai berikut:</p>
					                                                <div class="clearfix">
					                                                    <div class="col-sm-10 col-md-8">
					                                                        <div class="table-responsive">
					                                                            <table class="table table-bordered">
					                                                                <thead>
					                                                                    <th class="text-center" width="10%">NO</th>
					                                                                    <th class="text-center" width="40%">RINCIAN</th>
					                                                                    <th class="text-center" width="10%">NILAI</th>
					                                                                    <th class="text-center" width="40%">HARGA</th>
					                                                                </thead>
					                                                                <tbody>
					                                                                <?php
					                                                                    foreach($rincian as $arr1){
					                                                                        $nom++;
					                                                                        $cetak = $arr1['rinci'] || true;
					                                                                        $nilai = $arr1['nilai'];
					                                                                        $biaya = ($arr1['biaya'])?number_format($arr1['biaya']):'';
					                                                                        $jenis = $arr1['rincian'];
					                                                                        if($cetak){
					                                                                ?>
					                                                                    <tr>
					                                                                        <td class="text-center"><?php echo $nom;?></td>
					                                                                        <td class="text-left"><?php echo $jenis;?></td>
					                                                                        <td class="text-right"><?php echo ($nilai ? $nilai." %" : "");?></td>
					                                                                        <td class="text-right"><?php echo $biaya;?></td>
					                                                                    </tr>
					                                                                <?php } } ?>
					                                                                </tbody>
					                                                            </table>
					                                                        </div>
					                                                    </div>
					                                                </div>
					                                                <?php } else if($rsm['perhitungan'] == 2){ ?>
					                                                <p style="margin:0px 5px 5px;">Perhitungan menggunakan formula</p>
					                                                <?php if(count($formula) > 0){ $nom = 0; ?>
					                                                <div class="clearfix">
					                                                    <div class="col-sm-8">
					                                                        <div class="table-responsive">
					                                                            <table class="table table-bordered">
					                                                            <?php foreach($formula as $arr1){ $nom++; ?>
					                                                                <tr>
					                                                                    <td width="10%" class="text-center"><?php echo $nom; ?></td>
					                                                                    <td width="90%"><?php echo $arr1; ?></td>
					                                                                </tr>
					                                                            <?php } ?>
					                                                            </table>
					                                                        </div>
					                                                    </div>
					                                                </div>
					                                                <?php } } ?>

					                                                <hr style="margin:0px 0px 10px; color:#ccc;" />
					                                                <div class="form-group clearfix">
					                                                    <div class="col-sm-8">
					                                                        <label>Status Approval</label>
					                                                        <div class="form-control" style="height:auto">
					                                                            <?php 
					                                                                $status = '';
					                                                                $arrPosisi  = array(1=>"BM","BM Cabang","OM","CEO");
					                                                                $arrSetuju  = array(1=>"Disetujui","Ditolak");
					                                                                if($rsm['flag_approval'] == 0 && $rsm['flag_disposisi'] == 0)
					                                                                    $status = "Terdaftar";
					                                                                else if($rsm['flag_approval'] == 0 && $rsm['flag_disposisi'])
					                                                                    $status = "Verifikasi ".$arrPosisi[$rsm['flag_disposisi']];
					                                                                else if($rsm['flag_approval'])
					                                                                    $status = $arrSetuju[$rsm['flag_approval']]." ".$arrPosisi[$rsm['flag_disposisi']]."<br/><i>".date("d/m/Y H:i:s",strtotime($rsm['tgl_approval']))." WIB</i>";
					                                                                echo $status;
					                                                            ?>
					                                                        </div>
					                                                    </div>
					                                                </div>
					                                                <div class="form-group clearfix">
					                                                    <div class="col-sm-8">
					                                                        <label>Summary Result</label>
					                                                        <div class="form-control" style="height:auto"><?php echo ($rsm['sm_wil_summary']?$rsm['sm_wil_summary']:'&nbsp;'); ?></div>
					                                                    </div>
					                                                </div>
					                                                <div class="form-group clearfix">
					                                                    <div class="col-sm-8">
					                                                        <label>Catatan Marketing/Key Account</label>
					                                                        <div class="form-control" style="height:auto"><?php echo ($rsm['catatan']?$rsm['catatan']:'&nbsp;'); ?></div>
					                                                    </div>
					                                                </div>

					                                            </div>
					                                        </div>
					                                    </div>
					                            <?php
					                                } 
					                            ?>
			                        		</div>
			                        	</div>
									</div>
									<?php } ?>
								</div>
								<!-- <div class="row">
									<div class="col-sm-12">
										<div class="pad bg-gray">
											<a href="<?php echo ACTION_CLIENT."/send-email-to-hrd.php?".paramEncrypt("cust_id=".$rsm["id_customer"]); ?>" class="btn btn-default jarak-kanan">
											<i class="fa fa-envelope jarak-kanan"></i> Kirim Ke HRD</a>
										</div>
									</div>
								</div> -->
								<div class="row">
									<div class="col-sm-12">
										<div class="pad bg-gray">
											<!-- <button type="submit" class="btn btn-primary" name="btnSbmt" id="send-to-hrd"><i class="fa fa-envelope jarak-kanan"></i> Kirim Ke HRD</button> -->
											<button type="submit" class="btn btn-success" name="btnCustomer" id="send-to-customer"><i class="fa fa-envelope jarak-kanan"></i> Kirim Ke Customer</button>
										</div>
									</div>
								</div>
							</div>
                		</div>
					<?php //} ?>



			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

    <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <h4 class="modal-title">Loading Data ...</h4>
                </div>
                <div class="modal-body text-center modal-loading"></div>
            </div>
        </div>
    </div>

<script>
$(document).ready(function(){
	$("#send-to-hrd").click(function(e){
		window.location.href = "<?php echo $link_email_hrd; ?>";
	})

	$("#send-to-customer").click(function(e){
		window.location.href = "<?php echo $link_email; ?>";
	})

	var formValidasiCfg = {
		submitHandler: function(form) {
			$("#loading_modal").modal({keyboard:false, backdrop:'static'});
			
			let is_upload = true;
			if($("#jenis_datanya").length > 0 && $("#jenis_datanya").val() == "2"){
				$(".pic").each(function(i, v){
					let idnya = $(v).attr("id").replace("nama_file_kyc", "");
					if($("#nama_file_kyc"+idnya).val() == "" || $("#attach_file_kyc1"+idnya).val() == ""){
						is_upload = is_upload && false;
					}
				});
			}

			if($("#cekkolnup").is(":checked") && $("#nup_fee").val() == ""){
				$("#loading_modal").modal("hide");
				$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
				setErrorFocus($("#nup_fee"), $("form#gform"), false);
			} else if($("#jenis_datanya").length > 0 && $("#jenis_datanya").val() == ""){
				$("#loading_modal").modal("hide");
				$.validator.showErrorField('jenis_datanya', "Kolom ini belum diisi atau dipilih");
				setErrorFocus($("#jenis_datanya"), $("form#gform"), false);
			} else if($("#jenis_datanya").length > 0 && $("#jenis_datanya").val() == "2" && $("#credit_limit").val() == "0"){
				$("#loading_modal").modal("hide");
				$.validator.showErrorField('credit_limit', "Kolom ini belum diisi atau dipilih");
				setErrorFocus($("#credit_limit"), $("form#gform"), false);
			} else if($("#jenis_datanya").length > 0 && $("#jenis_datanya").val() == "2" && !is_upload){
				$("#loading_modal").modal("hide");
				swal.fire("Lampiran Dokumen KYC belum semuanya terisi");
			} else{
				//return false;
				form.submit();
			}
		}	
	};
	$("form#gform").validate($.extend(true,{},config.validation,formValidasiCfg));

	$("input[name='dokumen[]']").on("ifChecked", function(){
		var nilai = $(this).val();
		if(nilai == 9) $("#dok_lain").removeAttr("disabled");
	}).on("ifUnchecked", function(){
		var nilai = $(this).val();
		if(nilai == 9) $("#dok_lain").val("").attr("disabled","disabled");
	});
	$("input[name='evaluationA']").on("ifChecked", function(){
		var nilai = $(this).val();
		if(nilai == 3) $("#a1").removeAttr("disabled");
		else  $("#a1").val("").attr("disabled","disabled");
	});
	$("input[name='evaluationB']").on("ifChecked", function(){
		var nilai = $(this).val();
		if(nilai == 3) $("#a2").removeAttr("disabled");
		else  $("#a2").val("").attr("disabled","disabled");
	});
	$("input[name='evaluationC']").on("ifChecked", function(){
		var nilai = $(this).val();
		if(nilai == 3) $("#a3").removeAttr("disabled");
		else  $("#a3").val("").attr("disabled","disabled");
	});
	$("input[name='evaluationD']").on("ifChecked", function(){
		var nilai = $(this).val();
		if(nilai == 3) $("#a4").removeAttr("disabled");
		else  $("#a4").val("").attr("disabled","disabled");
	});
	$("input[name='evaluationE']").on("ifChecked", function(){
		var nilai = $(this).val();
		if(nilai == 2) $("#a5").removeAttr("disabled");
		else  $("#a5").val("").attr("disabled","disabled");
	});
	$("input[name='evaluationF']").on("ifChecked", function(){
		var nilai = $(this).val();
		if(nilai == 5) $("#a6").removeAttr("disabled");
		else  $("#a6").val("").attr("disabled","disabled");
	});
});
</script>
</body>
</html>      
