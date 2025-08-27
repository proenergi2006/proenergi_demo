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
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);
	$sql = "select a.id_customer, a.id_marketing, a.nama_customer, a.alamat_customer, a.prov_customer, a.kab_customer, a.telp_customer, a.fax_customer, a.email_customer, 
			a.website_customer, a.tipe_bisnis, a.tipe_bisnis_lain, a.ownership, a.ownership_lain, a.need_update, a.count_update, a.status_customer, a.fix_customer_since, a.ajukan, 
			a.fix_customer_redate, a.top_payment, a.lastupdate_time, a.lastupdate_ip, a.lastupdate_by, b.pic_decision_name, b.pic_decision_position, b.pic_decision_telp, 
			b.pic_decision_mobile, b.pic_decision_email, b.pic_ordering_name, b.pic_ordering_position, b.pic_ordering_telp, b.pic_ordering_mobile, b.pic_ordering_email, 
			b.pic_billing_name, b.pic_billing_position, b.pic_billing_telp, b.pic_billing_mobile, b.pic_billing_email, b.pic_invoice_name, b.pic_invoice_position, 
			b.pic_invoice_telp, b.pic_invoice_mobile, b.pic_invoice_email, a.nomor_sertifikat, a.nomor_sertifikat_file, a.nomor_npwp, a.nomor_npwp_file, a.nomor_siup, 
			a.nomor_siup_file, a.nomor_tdp, a.nomor_tdp_file, d.email_billing, d.alamat_billing, d.prov_billing, d.kab_billing, d.telp_billing, d.fax_billing, 
			d.payment_schedule, d.payment_schedule_other, d.payment_method, d.payment_method_other, d.invoice, d.ket_extra, e.logistik_area, e.logistik_bisnis, e.logistik_env, 
			e.logistik_env_other, e.logistik_storage, e.logistik_storage_other, e.logistik_hour, e.logistik_hour_other, e.logistik_volume, e.logistik_volume_other, 
			e.logistik_quality, e.logistik_quality_other, e.logistik_truck, f.nama_prov as propinsi_customer, g.nama_kab as kabupaten_customer, a.id_wilayah, 
			h.nama_prov as propinsi_payment, i.nama_kab as kabupaten_payment, j.token_verification, j.is_evaluated, j.legal_data, j.legal_summary, j.legal_result, j.role_approve,
			j.legal_tgl_proses, j.legal_pic, j.finance_data, j.finance_summary, j.finance_result, j.finance_tgl_proses, j.finance_pic, j.logistik_data, j.logistik_summary, 
			j.logistik_result, j.logistik_tgl_proses, j.logistik_pic, j.om_summary, j.om_result, j.om_tgl_proses, j.cfo_summary, j.cfo_result, j.cfo_tgl_proses, 
			j.ceo_summary, j.ceo_result, j.ceo_tgl_proses, k.nama_cabang as wilayah, a.jenis_payment, l.review1, l.review2, l.review3, l.review4, l.review5, l.review6, l.review7, 
			l.review8, l.review9, l.review10, l.review11, l.review12, l.review13, l.review14, l.review15, l.review16, l.review_result, l.review_pic, l.review_tanggal, 
			l.review_summary, l.review_attach, l.review_attach_ori, j.om_pic, j.ceo_pic, j.cfo_pic, e.logistik_truck_other, j.is_reviewed, 
			a.credit_limit, a.credit_limit_diajukan, a.postalcode_customer, d.postalcode_billing, a.jenis_net, e.desc_stor_fac, e.desc_condition, j.is_approved, j.disposisi_result, j.id_verification
			from pro_customer a left join pro_customer_contact b on a.id_customer = b.id_customer 
			left join pro_customer_payment d on a.id_customer = d.id_customer left join pro_customer_logistik e on a.id_customer = e.id_customer 
			left join pro_master_provinsi f on a.prov_customer = f.id_prov left join pro_master_kabupaten g on a.kab_customer = g.id_kab 
			left join pro_master_provinsi h on d.prov_billing = h.id_prov left join pro_master_kabupaten i on d.kab_billing = i.id_kab 
			left join pro_customer_verification j on a.id_customer = j.id_customer left join pro_customer_review l on j.id_verification = l.id_verification 
			left join pro_master_cabang k on a.id_wilayah = k.id_master and a.id_group = k.id_group_cabang 
			where j.id_verification = '".$idr."' and l.id_review = '".$idk."'";
	$rsm 	= $con->getRecord($sql);
	$tmp1 	= isset($rsm['nama_kab'])?strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['nama_kab'])):null;
	$alamat = isset($rsm['nama_prov'])?$rsm['alamat_customer']." ".ucwords($tmp1)." ".$rsm['nama_prov']:null;
	$dt1 	= ($rsm['review1']);
	$dt2 	= ($rsm['review2']);
	$dt3 	= ($rsm['review3']);
	$dt4 	= ($rsm['review4']);
	$dt5 	= ($rsm['review5']);
	$dt6 	= ($rsm['review6']);
	$dt7 	= ($rsm['review7']);
	$dt8 	= ($rsm['review8']);
	$dt9 	= ($rsm['review9']);
	$dt10 	= ($rsm['review10']);
	$dt11 	= ($rsm['review11']);
	$dt12 	= ($rsm['review12']);
	$dt13 	= ($rsm['review13']);
	$dt14 	= ($rsm['review14']);
	$dt15 	= ($rsm['review15']);
	$dt16 	= ($rsm['review16']);
	$sumary = ($rsm['review_summary']);
	$isEdit = (((!$rsm['legal_result'] || !$rsm['finance_result'] || !$rsm['logistik_result']) && $rsm['is_evaluated'] && $rsm['is_reviewed'])?true:false);
    $isAjukanKembali = false;
	$link1 	= BASE_URL_CLIENT."/customer-review.php";
    $link2  = BASE_URL_CLIENT."/customer-review-add.php?".paramEncrypt("idr=".$idr."&idk=".$idk);
	$link3 	= ACTION_CLIENT."/customer-review-resubmit.php?".paramEncrypt("idr=".$idr."&idk=".$rsm['id_customer']);
	$link_email 	= BASE_URL_CLIENT."/send-email-customer.php?".paramEncrypt("idv=".$idr."&idk=".$rsm['id_customer']."&idr=".$idk);
	$pathRa = $public_base_directory.'/files/uploaded_user/lampiran/'.$rsm['review_attach'];

    $arrPosisi  = array(2=>"BM",3=>"OM",4=>"CFO",5=>"CEO",6=>"MGR Finance");
    $arrRole    = array(7=>"BM",3=>"CEO",4=>"CFO",15=>"MGR Finance",6=>"OM");
    if($rsm['is_approved'] == 1) {
        if(isset($data['role_approve'])) {
            $disposisi = "Disetujui ".$arrRole[$rsm['role_approve']]."<br /><i>".date("d/m/Y H:i:s", strtotime(isset($rsm['tanggal_approved'])?$rsm['tanggal_approved']:$rsm['review_tanggal']))."</i>";
        } else {
            $disposisi = "Disetujui ".$arrPosisi[$rsm['disposisi_result']]."<br /><i>".date("d/m/Y H:i:s", strtotime(isset($rsm['tanggal_approved'])?$rsm['tanggal_approved']:$rsm['review_tanggal']))."</i>";
        }
    }
    else if($rsm['is_approved'] == 2)
        $disposisi = "Ditolak ".$arrPosisi[$rsm['disposisi_result']]."<br /><i>".date("d/m/Y H:i:s", strtotime(isset($rsm['tanggal_approved'])?$rsm['tanggal_approved']:$rsm['review_tanggal']))."</i>";
    else if((!$rsm['legal_result'] || !$rsm['finance_result'] || !$rsm['logistik_result']) && !$rsm['disposisi_result'])
        $disposisi = 'Review Marketing';
    else if($rsm['disposisi_result'] == 1)
        $disposisi = "Tahap Verifikasi";
    else if($rsm['disposisi_result'] == 2)
        $disposisi = "Verifikasi BM";
    else if($rsm['disposisi_result'] == 3)
        $disposisi = "Verifikasi OM";
    else if($rsm['disposisi_result'] == 4)
        $disposisi = "Verifikasi CFO";
    else if($rsm['disposisi_result'] == 5)
        $disposisi = "Verifikasi CEO";
    else $disposisi = 'Terdaftar';

    if ($rsm['is_approved']==2) {
        $isEdit = true;
        $isAjukanKembali = true;
    }
    
    if ($rsm['disposisi_result'] == 3)
        $alasan = $rsm['om_summary'];
    else if ($rsm['disposisi_result'] == 4)
        $alasan = $rsm['cfo_summary'];
    else if ($rsm['disposisi_result'] == 5)
        $alasan = $rsm['ceo_summary'];
    else if ($rsm['disposisi_result'] == 6)
        $alasan = $rsm['legal_summary'];
    else if ($rsm['disposisi_result'] == 2)
        $alasan = isset($rsm['sm_summary'])?$rsm['sm_summary']:null; 
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("ckeditor"))); ?>
<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Review Data Customer</h1>
        	</section>
			<section class="content">

				<?php if($enk['idk'] !== '' && isset($enk['idk'])){ ?>
				<?php $flash->display(); ?>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                    	<a href="#data-review" aria-controls="data-review" role="tab" data-toggle="tab">Review Customer</a>
                    </li>
                    <li role="presentation">
                    	<a href="#data-evaluation" aria-controls="data-evaluation" role="tab" data-toggle="tab">Data Customer</a>
                    </li>
                    <?php if($rsm['is_approved'] == 1 || $rsm['is_approved'] == 2) { ?>
                    <li role="presentation">
                        <a href="#data-status" aria-controls="data-status" role="tab" data-toggle="tab">Summary</a>
                    </li>
                    <?php } ?>
                </ul>

                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane active" id="data-review">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <p style="margin-bottom:3px;"><b><?php echo $rsm['nama_customer'];?></b></p>
                                        <p style="margin-bottom:0px;"><?php echo $alamat;?></p>
                                        <p style="margin-bottom:0px;"><?php echo "Telp : ".$rsm['telp_customer'].", Fax : ".$rsm['fax_customer'];?></p>
                                    </div>
                                    <div class="box-body">
                                        <ol style="margin-bottom:20px;">
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Jenis Usaha Customer ?</span>
                                                <div style="padding:5px 0px 0px;"><?php echo $dt1;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Kapan Perusahaan Tersebut Didirikan ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo $dt2;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Siapa Pemilik Perusahaan Tersebut ?</span>                                            
                                                <div style="padding:5px 0px 0px"><?php echo $dt3;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Lokasi Perusahaan Saat ini Milik Sendiri Atau Sewa ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo $dt4;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Berapa Jumlah Karyawan Saat Ini ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo $dt5;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Apakah Setiap Tahun Ada Salary Adjustment Dan Bonus Bagi Karyawan ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo $dt6;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Apakah Ada Cabang Di Daerah Lain ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo $dt7;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Apakah Perusahaan Tersebut Menggunakan Independent Auditor ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo $dt8;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Potensi Volume Dalam 1 Bulan ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo number_format($dt9);?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Supply HSD Saat Ini Dapat Dari mana ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo $dt10;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Berapa TOP Yang Diberikan Oleh Supplier Sebelumnya ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo $dt11;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Track Record Pembayaran Atas Supplier Sebelumnya ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo $dt12;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Alasan Yang Membuat Customer Tersebut Memilih Pro Energi ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo $dt13;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Bank Yang Saat Ini Active Digunakan ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo $dt14;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Apakah Mempunyai Facility Dari Bank Tersebut ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo $dt15;?></div>
                                            </li>
                                            <li style="padding:10px 0px; border-bottom:1px solid #ddd;">
                                                <span>Bagaimana Mekanisme Pencairan Pembayaran ?</span>
                                                <div style="padding:5px 0px 0px"><?php echo $dt16;?></div>
                                            </li>
                                        </ol>
                                            
                                        <div class="form-group row">
                                            <div class="col-sm-6 col-md-4">
                                                <label>Credit Limit Proposed</label>
                                                <div class="input-group">
                                                <span class="input-group-addon">Rp.</span>
                                                <input type="text" class="form-control text-right" value="<?php echo number_format($rsm['credit_limit_diajukan']);?>" readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-8">
                                                <label>Catatan Marketing / Key Account</label>
                                                <div class="form-control" style="height:auto">
                                                    <?php echo ($sumary); ?>
                                                    <p style="margin:10px 0 0; font-size:12px;"><i>
													<?php echo $rsm['review_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['review_tanggal']))." WIB"; ?></i></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12">
												<?php
                                                    if($rsm['review_attach'] && file_exists($pathRa)){
                                                        $linkRa = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=RA_".$idk."_&file=".$rsm['review_attach_ori']);
                                                        echo '<label>Lampiran</label>';
														echo '<p><a href="'.$linkRa.'"><i class="fa fa-file-alt jarak-kanan"></i>'.$rsm['review_attach_ori'].'</a></p>';
													}
                                                ?>
                                            </div>
                                        </div>
        
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="pad bg-gray">
                                                    <a class="btn btn-default jarak-kanan" href="<?php echo $link1;?>"><i class="fa fa-reply jarak-kanan"></i>Kembali</a>
                                                    <?php if($isEdit){ ?>
                                                    <a class="btn btn-primary" href="<?php echo $link2;?>"><i class="fa fa-edit jarak-kanan"></i>Edit Data</a>
                                                    <?php } ?>
                                                    <?php if($isAjukanKembali && $rsm['ajukan'] == 0){ ?>
                                                    <a class="btn btn-success" href="<?php echo $link3;?>"><i class="fa fa-check jarak-kanan"></i>Ajukan Kembali</a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="data-evaluation">
                    	<?php require_once($public_base_directory."/web/__get_data_customer.php"); ?>
                    </div>

                    <?php if($rsm['is_approved'] > 0) { ?>
                    <div role="tabpanel" class="tab-pane" id="data-status">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <p style="margin-bottom:3px;"><b>Status : <br /></b><?php echo $disposisi;?></p><hr />
                                        <p style="margin-bottom:0px;"><b>Catatan : <br /></b><?php echo $alasan;?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if($rsm['is_approved'] == 1) {?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="pad bg-gray">
                                    <button type="submit" class="btn btn-success" name="btnCustomer" id="send-to-customer"><i class="fa fa-envelope jarak-kanan"></i> Kirim Ke Customer</button>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>

                </div>


            <?php } ?>
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
			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style type="text/css">
	h3.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
	}
</style>
<script>
	$(document).ready(function(){
        $("#send-to-customer").click(function(e){
            window.location.href = "<?php echo $link_email; ?>";
		})

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
		});
	});		
</script>
</body>
</html>      
