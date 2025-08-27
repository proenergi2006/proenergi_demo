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
	$sql = "select a.id_customer, a.id_marketing, a.nama_customer, a.alamat_customer, a.prov_customer, a.kab_customer, a.telp_customer, a.fax_customer, a.email_customer, 
			a.website_customer, a.tipe_bisnis, a.tipe_bisnis_lain, a.ownership, a.ownership_lain, a.need_update, a.count_update, a.status_customer, a.fix_customer_since, 
			a.fix_customer_redate, a.top_payment, a.lastupdate_time, a.lastupdate_ip, a.lastupdate_by, b.pic_decision_name, b.pic_decision_position, b.pic_decision_telp, 
			b.pic_decision_mobile, b.pic_decision_email, b.pic_ordering_name, b.pic_ordering_position, b.pic_ordering_telp, b.pic_ordering_mobile, b.pic_ordering_email, 
			b.pic_billing_name, b.pic_billing_position, b.pic_billing_telp, b.pic_billing_mobile, b.pic_billing_email, b.pic_invoice_name, b.pic_invoice_position, 
			b.pic_invoice_telp, b.pic_invoice_mobile, b.pic_invoice_email, a.nomor_sertifikat, a.nomor_sertifikat_file, a.nomor_npwp, a.nomor_npwp_file, a.nomor_siup, 
			a.nomor_siup_file, a.nomor_tdp, a.nomor_tdp_file, d.email_billing, d.alamat_billing, d.prov_billing, d.kab_billing, d.telp_billing, d.fax_billing, 
			d.payment_schedule, d.payment_schedule_other, d.payment_method, d.payment_method_other, d.invoice, d.ket_extra, e.logistik_area, e.logistik_bisnis, e.logistik_env, 
			e.logistik_env_other, e.logistik_storage, e.logistik_storage_other, e.logistik_hour, e.logistik_hour_other, e.logistik_volume, e.logistik_volume_other, 
			e.logistik_quality, e.logistik_quality_other, e.logistik_truck, f.nama_prov as propinsi_customer, g.nama_kab as kabupaten_customer, a.jenis_payment, 
			h.nama_prov as propinsi_payment, e.logistik_truck_other, i.nama_kab as kabupaten_payment, j.fullname as nama_marketing, 
			a.postalcode_customer, d.postalcode_billing, a.jenis_net, a.credit_limit_diajukan, a.credit_limit, e.desc_stor_fac, e.desc_condition  
			from pro_customer a 
			left join pro_customer_contact b on a.id_customer = b.id_customer left join acl_user j on a.id_marketing = j.id_user 
			left join pro_customer_payment d on a.id_customer = d.id_customer left join pro_customer_logistik e on a.id_customer = e.id_customer 
			left join pro_master_provinsi f on a.prov_customer = f.id_prov left join pro_master_kabupaten g on a.kab_customer = g.id_kab 
			left join pro_master_provinsi h on d.prov_billing = h.id_prov left join pro_master_kabupaten i on d.kab_billing = i.id_kab where a.id_customer = '".$idr."'";
	$rsm = $con->getRecord($sql);
	$base_directory	= $public_base_directory."/files/uploaded_user/images";
	$file_path_sert	= $base_directory."/sert_file".$idr."_".$rsm['nomor_sertifikat_file'];
	$file_path_npwp	= $base_directory."/npwp_file".$idr."_".$rsm['nomor_npwp_file'];
	$file_path_siup	= $base_directory."/siup_file".$idr."_".$rsm['nomor_siup_file'];
	$file_path_tdpn	= $base_directory."/tdp_file".$idr."_".$rsm['nomor_tdp_file'];

	$extIkon1 	= strtolower(substr($rsm['nomor_sertifikat_file'],strrpos($rsm['nomor_sertifikat_file'],'.')));
	$extIkon2 	= strtolower(substr($rsm['nomor_npwp_file'],strrpos($rsm['nomor_npwp_file'],'.')));
	$extIkon3 	= strtolower(substr($rsm['nomor_siup_file'],strrpos($rsm['nomor_siup_file'],'.')));
	$extIkon4 	= strtolower(substr($rsm['nomor_tdp_file'],strrpos($rsm['nomor_tdp_file'],'.')));
	$arrIkon	= array(".jpg"=>"fa fa-file-image-o jarak-kanan", ".jpeg"=>"fa fa-file-image-o jarak-kanan", ".png"=>"fa fa-file-image-o jarak-kanan", 
						".gif"=>"fa fa-file-image-o jarak-kanan", ".pdf"=>"fa fa-file-pdf-o jarak-kanan", ".zip"=>"fa fa-file-archive-o jarak-kanan");

	$tmp_addr1 			= strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['kabupaten_customer']));
	$alamat_customer 	= $rsm['alamat_customer']." ".ucwords($tmp_addr1)." ".$rsm['propinsi_customer'];
	$tmp_addr2 			= strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['kabupaten_payment']));
	$alamat_payment 	= $rsm['alamat_billing']." ".ucwords($tmp_addr2)." ".$rsm['propinsi_payment'];

	$arrTermPayment 	= array("CREDIT"=>"CREDIT", "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");
	$arrConditionInd	= array(0=>'', 1=>"Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
	$arrConditionEng 	= array(0=>'', 1=>"After Invoice Receive", "After Delivery", "After Loading");
    $id_role = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
    
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Detil Customer</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                    	<a href="#customer-data" aria-controls="customer-data" role="tab" data-toggle="tab">Data Customer</a>
					</li>
                    <li role="presentation" class="<?php echo($id_role==1?'':'hide'); ?>" >
                    	<a href="#customer-list" aria-controls="customer-list" role="tab" data-toggle="tab">User List</a>
					</li>
                </ul>
                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane" id="customer-list">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <a href="<?php echo BASE_URL_CLIENT.'/customer-admin-add.php?'.paramEncrypt("idr=".$idr); ?>" class="btn btn-primary jarak-kanan">
                                        <i class="fa fa-plus jarak-kanan"></i>Add User</a>
                                        <a href="<?php echo BASE_URL_CLIENT.'/customer-admin-edit.php?'.paramEncrypt("idr=".$idr); ?>" class="btn btn-success jarak-kanan">
                                        <i class="fa fa-recycle jarak-kanan"></i>Ubah Marketing</a>
                                    </div>
                                    <div class="box-body table-responsive">
                                        <table class="table table-bordered table-hover" id="table-grid">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="10%">NO</th>
                                                    <th class="text-center" width="20%">USERNAME</th>
                                                    <th class="text-center" width="20%">EMAIL</th>
                                                    <th class="text-center" width="20%">TELEPON</th>
                                                    <th class="text-center" width="10%">STATUS</th>
                                                    <th class="text-center" width="20%">ACTIONS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
												$sqlUser = "select * from acl_user where id_role = 14 and id_customer = '".$idr."'";
												$resUser = $con->getResult($sqlUser);
												if(count($resUser) <= 0){
													echo '<tr><td colspan="6" style="text-align:center">Data tidak ditemukan </td></tr>';
												} else{
													$count = 0;
													foreach($resUser as $datau){
														$count++;
														$linkReset	= ACTION_CLIENT.'/customer-admin.php?'.paramEncrypt('act=reset&idr='.$idr.'&idc='.$datau['id_user']);
														$linkAktif	= ACTION_CLIENT.'/customer-admin.php?'.paramEncrypt('act=aktif&idr='.$idr.'&idc='.$datau['id_user']);
														$linkNonef	= ACTION_CLIENT.'/customer-admin.php?'.paramEncrypt('act=nonef&idr='.$idr.'&idc='.$datau['id_user']);
														$linkHapus	= ACTION_CLIENT.'/customer-admin.php?'.paramEncrypt('act=hapus&idr='.$idr.'&idc='.$datau['id_user']);
														$active		= ($datau["is_active"] == 1)?"Active":"Not Active";
														$linknya	= ($datau["is_active"] == 1)?$linkNonef:$linkAktif;
														$textnya	= ($datau["is_active"] == 1)?"Deactivate":"Activate";
														$resetnya	= ($datau["is_active"] == 1)?'href="'.$linkReset.'"':"";
														$deadlink	= ($datau["is_active"] == 1)?"":"disabled";
											?>
                                                <tr>
                                                    <td class="text-center"><?php echo $count; ?></td>
                                                    <td><?php echo $datau['username']; ?></td>
                                                    <td><?php echo $datau['email_user']; ?></td>
                                                    <td><?php echo $datau['mobile_user']; ?></td>
                                                    <td><?php echo $active; ?></td>
                                                    <td class="text-center action">
                                                        <a class="margin-sm konfirmasi btn btn-action btn-success" href="<?php echo $linknya;?>"><?php echo $textnya;?></a>
                                                        <?php if($datau["is_active"] == 1){ ?>
                                                        <a class="margin-sm konfirmasi btn btn-action btn-info" href="<?php echo $linkReset;?>">Reset</a>
                                                        <?php } ?>
                                                        <a class="margin-sm konfirmasi btn btn-action btn-danger" href="<?php echo $linkHapus;?>">Hapus</a>
                                                    </td>
                                                </tr>
											<?php } } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane active" id="customer-data">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box box-primary no-shadow" style="border-width:3px 0px 0px 0px;">
                                    <div class="box-header bg-light-blue">
                                        <div class="text-center"><h3 class="box-title"><b>COMPANY DATA</b></h3></div>
                                    </div>
                                    <div class="box-body" style="border:1px solid #ddd;">
                                        <div class="table-responsive">
                                            <table class="table no-border">
                                                <tr>
                                                    <td colspan="4"><h3 class="form-title"><u>I. COMPANY DETAILS</u></h3></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">COMPANY NAME</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['nama_customer'];?></td>
                                                </tr>
                                                <tr>
                                                    <td width="80">ADDRESS</td>
                                                    <td width="100"><b><i>Road</i></b></td>
                                                    <td width="10" class="text-center">:</td>
                                                    <td><?php echo $rsm['alamat_customer'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><span><b><i>Province</i></b></span></td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['propinsi_customer'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><span><b><i>City</i></b></span></td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $tmp_addr1;?></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><span><b><i>Postal Code</i></b></span></td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['postalcode_customer'];?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">TELEPHONE</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['telp_customer'];?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">FAX</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['fax_customer'];?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">EMAIL</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['email_customer'];?></td>
                                                </tr>
                                                <?php if($rsm['status_customer'] > 1){ ?>
                                                <tr>
                                                    <td colspan="2">WEBSITE</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['website_customer'];?></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                        </div>
										
										<?php if($rsm['status_customer'] > 1){ ?>
                                        <div class="table-responsive">
                                            <table class="table no-border tipe-bisnis">
                                                <tr>
                                                    <td colspan="2"><h3 class="form-title"><u>II. TYPE OF BUSINESS</u></h3></td>
                                                    <td colspan="2"><h3 class="form-title"><u>III. OWNERSHIP</u></h3></td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 1?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="310">Agriculture &amp; Forestry / Horticulture</td>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 1?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Affiliation</td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 2?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="310">Business &amp; Information</td>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 2?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>National Private</td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 3?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="310">Construction/Utilities/Contracting</td>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 3?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Foreign Private</td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 4?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="310">Education</td>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 4?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Joint Venture</td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 5?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="310">Finance &amp; Insurance</td>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 5?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>BUMN/BUMD</td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 6?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="310">Food &amp; hospitally</td>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 6?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Foundation</td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 7?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="310">Gaming</td>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 7?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Personal</td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 8?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="310">Health Services</td>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 8?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Other (Specify): <?php echo $rsm['ownership_lain']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 9?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="310">Motor Vehicle</td>
                                                    <td colspan="2">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 11?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="310">Natural Resources / Environmental</td>
                                                    <td colspan="2">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 12?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="310">Personal Service</td>
                                                    <td colspan="2">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 13?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="310">Manufacture</td>
                                                    <td colspan="2">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 10?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="310">Other (Specify): <?php echo $rsm['tipe_bisnis_lain']; ?></td>
                                                    <td colspan="2">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </div>
										<?php } ?>
                                        
                                        <?php if($rsm['status_customer'] > 1){ ?>
                                        <h3 class="form-title"><u>IV. DOCUMENTATION (Include a copy of this following documents with the delivery of this form)</u></h3>
                                        <div class="table-responsive">
                                            <table class="table no-border">
                                                <tr>
                                                    <td width="160">CERTIFICATE NUMBER (Akta Pendirian)</td>
                                                    <td width="10" class="text-center">:</td>
                                                    <td><?php 
													echo '<p>'.$rsm['nomor_sertifikat'].'</p>';
													if($rsm['nomor_sertifikat_file'] && file_exists($file_path_sert)){
                                                    $link1 = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=sert_file".$idr."_&file=".$rsm['nomor_sertifikat_file']);
													echo '<div class="preview-file">
															<a href="'.$link1.'"><i class="'.$arrIkon[$extIkon1].'"></i>'.str_replace("_"," ",$rsm['nomor_sertifikat_file']).'</a>
														  </div>';
													}
                                                    ?></td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>NPWP NUMBER</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php 
													echo '<p>'.$rsm['nomor_npwp'].'</p>';
													if($rsm['nomor_npwp_file'] && file_exists($file_path_npwp)){
														$linkNpwp = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=npwp_file".$idr."_&file=".$rsm['nomor_npwp_file']);
														echo '<div class="preview-file">
																<a href="'.$linkNpwp.'"><i class="'.$arrIkon[$extIkon2].'"></i>'.str_replace("_"," ",$rsm['nomor_npwp_file']).'</a>
															  </div>';
													}
                                                    ?></td>
                                                </tr>
                                                <tr>
                                                    <td>SIUP NUMBER</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php 
													echo '<p>'.$rsm['nomor_siup'].'</p>';
													if($rsm['nomor_siup_file'] && file_exists($file_path_siup)){
														$linkSiup = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=siup_file".$idr."_&file=".$rsm['nomor_siup_file']);
														echo '<div class="preview-file">
																<a href="'.$linkSiup.'"><i class="'.$arrIkon[$extIkon3].'"></i>'.str_replace("_"," ",$rsm['nomor_siup_file']).'</a>
															  </div>';
													}
                                                    ?></td>
                                                </tr>
                                                <tr>
                                                    <td>TDP NUMBER</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php 
													echo '<p>'.$rsm['nomor_tdp'].'</p>';
													if($rsm['nomor_tdp_file'] && file_exists($file_path_tdpn)){
														$linkTdp = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=tdp_file".$idr."_&file=".$rsm['nomor_tdp_file']);
														echo '<div class="preview-file">
																<a href="'.$linkTdp.'"><i class="'.$arrIkon[$extIkon4].'"></i>'.str_replace("_"," ",$rsm['nomor_tdp_file']).'</a>
															  </div>';
													}
                                                    ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <?php } ?>
        
                                        <?php if($rsm['status_customer'] > 1){ ?>
                                        <h3 class="form-title"><u>V. PERSON IN CHARGE</u></h3>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td colspan="2" class="bg-gray"><b>1. Decision Makers</b></td>
                                                    <td colspan="2" class="bg-gray"><b>2. Ordering Goods</b></td>
                                                </tr>
                                                <tr>
                                                    <td width="20%">Name</td>
                                                    <td width="30%"><?php echo $rsm['pic_decision_name'];?></td>
                                                    <td width="20%">Name</td>
                                                    <td width="30%"><?php echo $rsm['pic_ordering_name'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>Position</td>
                                                    <td><?php echo $rsm['pic_decision_position'];?></td>
                                                    <td>Position</td>
                                                    <td><?php echo $rsm['pic_ordering_position'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>Telephone</td>
                                                    <td><?php echo $rsm['pic_decision_telp'];?></td>
                                                    <td>Telephone</td>
                                                    <td><?php echo $rsm['pic_ordering_telp'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>Mobile</td>
                                                    <td><?php echo $rsm['pic_decision_mobile'];?></td>
                                                    <td>Mobile</td>
                                                    <td><?php echo $rsm['pic_ordering_mobile'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td><?php echo $rsm['pic_decision_email'];?></td>
                                                    <td>Email</td>
                                                    <td><?php echo $rsm['pic_ordering_email'];?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="bg-gray"><b>3. Billing Receiver</b></td>
                                                    <td colspan="2" class="bg-gray"><b>4. Invoice Payment</b></td>
                                                </tr>
                                                <tr>
                                                    <td>Name</td>
                                                    <td><?php echo $rsm['pic_billing_name'];?></td>
                                                    <td>Name</td>
                                                    <td><?php echo $rsm['pic_invoice_name'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>Position</td>
                                                    <td><?php echo $rsm['pic_billing_position'];?></td>
                                                    <td>Position</td>
                                                    <td><?php echo $rsm['pic_invoice_position'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>Telephone</td>
                                                    <td><?php echo $rsm['pic_billing_telp'];?></td>
                                                    <td>Telephone</td>
                                                    <td><?php echo $rsm['pic_invoice_telp'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>Mobile</td>
                                                    <td><?php echo $rsm['pic_billing_mobile'];?></td>
                                                    <td>Mobile</td>
                                                    <td><?php echo $rsm['pic_invoice_mobile'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td><?php echo $rsm['pic_billing_email'];?></td>
                                                    <td>Email</td>
                                                    <td><?php echo $rsm['pic_invoice_email'];?></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <?php } ?>
        
                                    </div>
                                </div>
                            </div>
                        </div>
        
                        <?php if($rsm['status_customer'] > 1){ ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box box-primary no-shadow" style="border-width:3px 0px 0px 0px;">
                                    <div class="box-header bg-light-blue">
                                        <div class="text-center"><h3 class="box-title"><b>PAYMENT</b></h3></div>
                                    </div>
                                    <div class="box-body" style="border:1px solid #ddd;">
                                        <div class="table-responsive">
                                            <table class="table no-border">
                                                <tr>
                                                    <td colspan="4"><h3 class="form-title"><u>I. BILLING ADDRESS</u></h3></td>
                                                </tr>
                                                <tr>
                                                    <td width="80">ADDRESS</td>
                                                    <td width="100"><b><i>Road</i></b></td>
                                                    <td width="10" class="text-center">:</td>
                                                    <td><?php echo $rsm['alamat_billing'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><span><b><i>Province</i></b></span></td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['propinsi_payment'];?></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><span><b><i>City</i></b></span></td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $tmp_addr2;?></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><span><b><i>Postal Code</i></b></span></td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['postalcode_billing'];?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">TELEPHONE</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['telp_billing'];?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">FAX</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['fax_billing'];?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">EMAIL</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['email_billing'];?></td>
                                                </tr>
                                            </table>
										</div>

                                        <div class="table-responsive">
                                            <table class="table no-border tipe-bisnis">
                                                <tr>
                                                    <td colspan="4"><h3 class="form-title"><u>II. CASHIER AND PAYMENT SCHEDULE</u></h3></td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['payment_schedule'] == 1?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="150">EVERY DAY</td>
                                                    <td width="40">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['payment_schedule'] == 2?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Other (Specify): <?php echo $rsm['payment_schedule_other']; ?></td>
                                                </tr>
											</table>
										</div>

                                        <div class="table-responsive">
                                            <table class="table no-border tipe-bisnis">
                                                <tr>
                                                    <td colspan="6"><h3 class="form-title"><u>III. PAYMENT METHOD</u></h3></td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['payment_method'] == 1?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="150">CASH</td>
                                                    <td width="40">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['payment_method'] == 2?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="150">TRANSFER</td>
                                                    <td width="40">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['payment_method'] == 5?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Other (Specify): <?php echo $rsm['payment_method_other']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['payment_method'] == 3?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>CHEQUE/GIRO</td>
                                                    <td>
														<?php echo '<img src="'.BASE_IMAGE.($rsm['payment_method'] == 4?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>BANK GUARANTEE</td>
                                                    <td colspan="2">&nbsp;</td>
                                                </tr>
											</table>
										</div>

                                        <div class="table-responsive">
                                            <table class="table no-border tipe-bisnis">
                                                <tr>
                                                    <td colspan="2"><h3 class="form-title"><u>IV. INVOICES</u></h3></td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['invoice']?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Tax Invoice (Faktur Pajak)</td>
                                                </tr>
											</table>
										</div>

                                        <div class="table-responsive">
                                            <table class="table no-border tipe-bisnis">
                                                <tr>
                                                    <td colspan="8"><h3 class="form-title"><u>V. PAYMENT PROPOSED</u></h3></td>
                                                </tr>
                                                <tr>
                                                    <td width="60">&nbsp;</td>
                                                    <td width="100"><b>Payment Type</b></td>
                                                    <td width="10">&nbsp;</td>
                                                    <td width="90">&nbsp;</td>
                                                    <td width="50">&nbsp;</td>
                                                    <td width="180"><b>TOP (Term of Payment)</b></td>
                                                    <td width="50">&nbsp;</td>
                                                    <td><b>Conditions</b></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td colspan="3"><div class="form-control"><?php echo $arrTermPayment[$rsm['jenis_payment']];?></div></td>
                                                    <td>&nbsp;</td>
                                                    <td><div class="form-control"><?php echo ($rsm['jenis_payment']=='CREDIT')?$rsm['top_payment'].' days':'&nbsp;';?></div></td>
                                                    <td>&nbsp;</td>
                                                    <td><div class="form-control"><?php echo ($rsm['jenis_payment']=='CREDIT')?$arrConditionEng[$rsm['jenis_net']]:'';?></div></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><b>Note</b></td>
                                                    <td>:</td>
                                                    <td colspan="5"><?php echo $rsm['ket_extra'];?></td>
                                                </tr>
											</table>
										</div>

                                        <div class="table-responsive">
                                            <table class="table no-border tipe-bisnis">
                                                <tr>
                                                    <td colspan="8"><h3 class="form-title"><u>VI. CREDIT LIMIT</u></h3></td>
                                                </tr>
                                                <tr>
                                                    <td width="60">&nbsp;</td>
                                                    <td width="250"><b>CREDIT LIMIT PROPOSED</b></td>
                                                    <td width="50">&nbsp;</td>
                                                    <td width="250"><b>CREDIT LIMIT APPROVED</b></td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><div class="form-control text-right"><?php echo number_format($rsm['credit_limit_diajukan']);?></div></td>
                                                    <td>&nbsp;</td>
                                                    <td><div class="form-control text-right"><?php echo number_format($rsm['credit_limit']);?></div></td>
                                                </tr>
											</table>
										</div>

                                    </div>
                                </div>
                            </div>
                        </div>
        
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box box-primary no-shadow" style="border-width:3px 0px 0px 0px;">
                                    <div class="box-header bg-light-blue">
                                        <div class="text-center"><h3 class="box-title"><b>LOGISTICS</b></h3></div>
                                    </div>
                                    <div class="box-body" style="border:1px solid #ddd;">
                                        <div class="table-responsive">
                                            <table class="table no-border">
                                                <tr>
                                                    <td colspan="6"><h3 class="form-title"><u>1. LOCATION DETAIL</u></h3></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">AREA <i>(luas lokasi)</i></td>
                                                    <td class="text-center">:</td>
                                                    <td colspan="3"><?php echo $rsm['logistik_area'];?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">CONDITIONS AROUND LOCATIONS</td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_env'] == 1?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="100">INDUSTRY</td>
                                                    <td width="40">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_env'] == 2?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="100">PEMUKIMAN</td>
                                                    <td width="40">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_env'] == 3?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Other (Specify) : <?php echo $rsm['logistik_env_other']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">DESCRIPTION OF CONDITION</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6"><div class="form-control" style="min-height:80px; height:auto;">
													<?php echo $rsm['desc_condition'];?></div></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">STORAGE FACILITY</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_storage'] == 1?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>INDOOR</td>
                                                    <td>
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_storage'] == 2?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>OUTDOOR</td>
                                                    <td>
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_storage'] == 3?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Other (Specify) : <?php echo $rsm['logistik_storage_other']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">DESCRIPTION OF STORAGE FACILITY</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6"><div class="form-control" style="min-height:80px; height:auto;">
													<?php echo $rsm['desc_stor_fac'];?></div></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">SECURITY ENVIRONMENT / BUSINESS AREA (Explain):</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6"><div class="form-control" style="min-height:80px; height:auto;">
													<?php echo $rsm['logistik_bisnis'];?></div></td>
                                                </tr>
											</table>
										</div>

                                        <div class="table-responsive">
                                            <table class="table no-border">
                                                <tr>
                                                    <td colspan="6"><h3 class="form-title"><u>2. DELIVERY DETAIL</u></h3></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">OPERATING HOURS</td>
                                                </tr>
                                                <tr>
                                                    <td width="100" class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_hour'] == 1?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="180">08.00 - 17.00</td>
                                                    <td width="40">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_hour'] == 2?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td width="180">24 HOURS</td>
                                                    <td width="40">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_hour'] == 3?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Other (Specify) : <?php echo $rsm['logistik_hour_other']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">VOLUME MEASUREMENT</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_volume'] == 1?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>PRO ENERGY'S TANK LORRY</td>
                                                    <td>
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_volume'] == 2?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>FLOWMETER</td>
                                                    <td>
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_volume'] == 3?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Other (Specify) : <?php echo $rsm['logistik_volume_other']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">QUALITY CHECKING</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_quality'] == 1?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>DENSITY</td>
                                                    <td>
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_quality'] == 3?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td colspan="3">Other (Specify) : <?php echo $rsm['logistik_quality_other']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">MAX. TRUCK CAPACITY</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_truck'] == 1?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>5 KL</td>
                                                    <td>
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_truck'] == 3?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>10 KL</td>
                                                    <td>
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_truck'] == 5?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>Other (Specify) : <?php echo $rsm['logistik_truck_other']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right">
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_truck'] == 2?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>8 KL</td>
                                                    <td>
														<?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_truck'] == 4?"/img_checked.png":"/img_uncheck.png").'" />';?>
													</td>
                                                    <td>16 KL</td>
                                                    <td colspan="2">&nbsp;</td>
                                                </tr>
											</table>
										</div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

				</div>

            <div class="row">
            	<div class="col-sm-12">
                	<div class="pad bg-gray">
                    	<a href="<?php echo BASE_URL_CLIENT."/customer-admin.php";?>" class="btn btn-default"><i class="fa fa-caret-left jarak-kanan"></i>Kembali</a>
                    </div>
                </div>
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
            <?php } ?>
			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style type="text/css">
	.table > tbody > tr > td{
		padding: 5px;
	}
	.tipe-bisnis > tbody > tr > td{
		padding: 0px 0px 2px;
	}
	h3.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
	}
	.preview-file{
		background-color: rgb(244, 244, 244);
		border: 1px solid rgb(221, 221, 221);
		padding: 5px 25px 5px 10px;
		margin-bottom: 0px;
	}
</style>
<script>
	$(document).ready(function(){
		$(window).on("load resize", function(){
			if($(this).width() < 977){
				$(".vertical-tab").addClass("collapsed-box");
				$(".vertical-tab").find(".box-tools").show();
				$(".vertical-tab > .vertical-tab-body").hide();
			} else{
				$(".vertical-tab").removeClass("collapsed-box");
				$(".vertical-tab").find(".box-tools").hide();
				$(".vertical-tab > .vertical-tab-body").show();
			}
		})

		$(".konfirmasi").on("click", function(){
			if(confirm("Apakah anda yakin ?")){
				$("#loading_modal").modal({backdrop:"static"});
			} else{
				$("#loading_modal").modal("hide");
				return false;
			}
		});
	});		
</script>
</body>
</html>      
