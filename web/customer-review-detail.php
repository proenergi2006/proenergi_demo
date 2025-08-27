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

	$sql_file = "
		select * from pro_customer_review_attchment 
		where id_review = '".$idk."' and id_verification = '".$idr."'
	";
    $rsm_file = $con->getResult($sql_file);

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
		j.finance_data, j.finance_summary, j.finance_result, j.finance_tgl_proses, j.finance_pic, 
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
		where j.id_verification = '".$idr."' and l.id_review = '".$idk."'
	";
	$rsm 	= $con->getRecord($sql);

    $arrPosisi  = array(2=>"BM", 3=>"OM", 4=>"CFO", 5=>"CEO", 6=>"MGR Finance");
    $arrRole    = array(7=>"BM", 3=>"CEO", 4=>"CFO", 15=>"MGR Finance", 6=>"OM");
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
                    	<?php require_once($public_base_directory."/web/__get_customer_review_detail.php"); ?>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="data-evaluation">
                    	<?php require_once($public_base_directory."/web/__get_data_customer.php"); ?>
                    </div>

                    <?php if($rsm['is_approved'] > 0) { ?>
                    <div role="tabpanel" class="tab-pane" id="data-status">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <p style="margin-bottom:3px;"><b>Status : <br /></b><?php echo $disposisi;?></p><hr />
                                <p style="margin-bottom:0px;"><b>Catatan : <br /></b><?php echo $alasan;?></p>
                            </div>
                        </div>
                        <?php if($rsm['is_approved'] == 1) {?>
                        <div class="pad bg-gray">
                            <button type="submit" class="btn btn-success" name="btnCustomer" id="send-to-customer"><i class="fa fa-envelope jarak-kanan"></i> Kirim Ke Customer</button>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>

                </div>


            <?php } ?>
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

<style type="text/css">
	h3.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
	}
    .bg-light-purple{
        background-color: #56386a;
        color: #f9f9f9 !important;
    }
    .box.box-purple{
        border-top-color: #56386a;

    }
</style>
<script>
$(document).ready(function(){
	$("#send-to-customer").click(function(e){
		window.location.href = "<?php echo $link_email; ?>";
	})

	$(".izin-pd").on("click", function(e){
		e.preventDefault();
		var linknya = $(this).data("href");
		swal.fire({
			title: '<div style="font-weight:400; font-size:16px; line-height:25px;">Form Review tidak dapat diubah kembali, jika sudah melakukan persetujuan. Apakah anda yakin?</div>',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak',
		}).then((result) => {
			if(result.isConfirmed){
				$("#loading_modal").modal({keyboard:false, backdrop:'static'});
				window.location.href = linknya;
			}
		});
	});

});		
</script>
</body>
</html>      
