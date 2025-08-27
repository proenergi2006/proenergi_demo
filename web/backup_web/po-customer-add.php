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
	$idk 	= isset($enk["idk"])?htmlspecialchars($enk["idk"], ENT_QUOTES):'';
	$idc 	= isset($enk["idc"])?htmlspecialchars($enk["idc"], ENT_QUOTES):'';
	$sesid 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
    $seswil = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $sesgroup = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);

    $lsClosePo    = isset($enk["parClosePo"])?htmlspecialchars($enk["parClosePo"], ENT_QUOTES):'';
    $lsAttachment    = isset($enk["parAttachment"])?htmlspecialchars($enk["parAttachment"], ENT_QUOTES):'';
   
    if ($lsClosePo) {
        $cekClosePo = "
            SELECT id_poc,
                tgl_close,
                volume_close,
                realisasi_close,
                created_time,
                created_ip,
                created_by,
                id_user,
                id_role,
                keterangan,
                lampiran_close_po,
                lampiran_close_po_ori
            FROM pro_po_customer_close
            WHERE ST_AKTIF = 'Y'
            AND ID_POC = '" . $idk . "'
        ";

        $rowClosePo = $con->getRecord($cekClosePo);
        $pathPtClose     = $public_base_directory.'/files/uploaded_user/lampiran/'.$rowClosePo['lampiran_close_po'];
        $lampPtClose     = $rowClosePo['lampiran_close_po_ori'];

        $cekPlan = "
            select 
                a.id_poc,
                lpad(a.id_poc,4,'0') as kode_po,
                b.nama_customer,
                a.volume_poc,
                c.vol_plan,
                c.realisasi 
            from pro_po_customer a 
            join pro_customer b on a.id_customer = b.id_customer 
            left join (
                select 
                    id_poc, 
                    sum(if(realisasi_kirim = 0, volume_kirim, realisasi_kirim)) as vol_plan,
                    sum(realisasi_kirim) as realisasi 
                from pro_po_customer_plan 
                where 
                    id_poc = '" . $idk . "' 
                    and status_plan not in (2,3) 
                group by id_poc
            ) c on a.id_poc = c.id_poc 
            where 
                a.poc_approved = 1
                and a.id_customer = '" . $idr . "' 
                and a.id_poc = '" . $idk . "'
        ";
        
        $rowPlan = $con->getRecord($cekPlan);
    }

	if ($idc) {
        $cek1 = "
            select 
                b.id_penawaran, 
                nomor_surat as kode_penawaran, 
                if(a.jenis_payment = 'CREDIT', a.top_payment, a.jenis_payment) as top_customer 
            from pro_customer a 
            left join pro_penawaran b on 
                a.id_customer = b.id_customer 
                and b.flag_approval = 1 
            where a.id_customer = '" . $idc . "' 
            order by b.id_penawaran desc
        ";

        $row1 = $con->getResult($cek1);
        
		if (count($row1) > 0) {
			$rsm['top_poc'] = $row1[0]['top_customer'];
		}
	}

	if ($idr != "" && $idk != "") {
		$sql = "select a.*, b.nama_customer, c.nomor_surat, c.masa_awal, c.masa_akhir, d.nama_cabang, f.nama_area, e.jenis_produk, e.merk_dagang, c.oa_kirim, c.volume_tawar, 
				c.detail_formula, c.perhitungan, c.harga_dasar  
				from pro_po_customer a 
				join pro_customer b on a.id_customer = b.id_customer 
				join pro_penawaran c on a.id_penawaran = c.id_penawaran 
				join pro_master_cabang d on c.id_cabang = d.id_master 
				join pro_master_produk e on c.produk_tawar = e.id_master 
				join pro_master_area f on c.id_area = f.id_master
				where a.id_customer = '".$idr."' and a.id_poc = '".$idk."'";
		$rsm = $con->getRecord($sql);
		$formula = json_decode($rsm['detail_formula'], true);
		if($rsm['perhitungan'] == 1){
			$harganya = number_format($rsm['harga_dasar']);
			$nilainya = $rsm['harga_dasar'];
		} else{
			$harganya = '';
			$nilainya = '';
			foreach($formula as $jenis){
				$harganya .= '<p style="margin-bottom:0px">'.$jenis.'</p>';
			}
		} 

		$action 	= "update";
		$section 	= "Ubah";
		$pathPt 	= $public_base_directory.'/files/uploaded_user/lampiran/'.$rsm['lampiran_poc'];
		$lampPt 	= $rsm['lampiran_poc_ori'];
	} else{
		$action 	= "add";
		$section 	= "Tambah";
	}
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber", "jqueryUI"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1><?php echo $section." PO Customer"; ?></h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box box-primary">
                        	<div class="box-header with-border">
                            	<h3 class="box-title"><i class="fa fa-edit jarak-kanan"></i>Silahkan isi form dibawah ini</h3>
							</div>
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/po-customer.php'; ?>" id="gform" name="gform" method="post" role="form" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Nama Customer *</label>
                                        <?php if($action == "add"){ ?>
                                        <?php
                                            $where = "id_marketing = '".$sesid."'";
                                            if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 18) {
                                                $where = "1=1";
                                                if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
                                                    $where = "(id_wilayah = '".$seswil."' or id_marketing = '".$sesid."')";
                                                else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
                                                    $where = "(id_group = '".$sesgroup."' or id_marketing = '".$sesid."')";
                                            }
                                        ?>
                                        <select id="customer" name="customer" class="form-control select2 validate[required]">
                                        	<option></option>
                                            <?php $con->fill_select("id_customer","if(kode_pelanggan = '',nama_customer,concat(kode_pelanggan,' - ',nama_customer))","pro_customer",$idc,"where ".$where." and is_verified = 1","id_customer desc, nama",false); ?>
                                        </select>
                                        <?php } else{ ?>
										<input type="hidden" name="customer" id="customer" value="<?php echo $rsm['id_customer'];?>" />
                                        <input type="text" name="custNama" id="custNama" class="form-control" value="<?php echo $rsm['nama_customer']; ?>" readonly />
									    <?php } ?>
                                    </div>
                                    <div class="col-sm-4 col-md-3 col-sm-top">
                                        <label>TOP Payment*</label>
                                        <div class="input-group">
                                        	<input type="text" name="top" id="top" class="form-control validate[required]" value="<?php echo isset($rsm['top_poc'])?$rsm['top_poc']:''; ?>" readonly />
                                            <span class="input-group-addon">Hari</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Nomor Penawaran *</label>
										<?php if($action == "add"){ ?>
                                        <select id="penawaran" name="penawaran" class="form-control validate[required] select2">
                                        	<?php
												if($idc && count($row1) > 0){
													echo '<option></option>';
													foreach($row1 as $optx){
														echo '<option value="'.$optx['id_penawaran'].'">'.$optx['kode_penawaran'].'</option>';
													}
												}
											?>
                                        </select>
										<?php } else{ ?>
										<input type="hidden" name="penawaran" id="penawaran" value="<?php echo $rsm['id_penawaran'];?>" />
                                        <input type="text" name="a8" id="a8" class="form-control" value="<?php echo $rsm['nomor_surat'];?>" readonly />
										<?php } ?>
                                    </div>
                                    <div class="col-sm-4 col-md-3 col-sm-top" style="position: relative">
                                        <div id="keterangan_limit" style="margin-right: -20px; font-weight: bold; position: absolute; top: 0; right: 0; width: 270px; height: 200px;">
                                            
                                        </div>
                                    </div>
                                </div>

                                <div id="ket-penawaran">
                                	<?php if($action == "update"){ ?>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <td colspan="2" class="text-center bg-gray"><b>KETERANGAN</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="160">Masa berlaku harga</td>
                                                        <td><?php echo tgl_indo($rsm['masa_awal'])." - ".tgl_indo($rsm["masa_akhir"]);?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Cabang</td>
                                                        <td><?php echo $rsm['nama_cabang'];?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Area</td>
                                                        <td><?php echo $rsm['nama_area'];?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Produk</td>
                                                        <td><?php echo $rsm['jenis_produk'].' - '.$rsm['merk_dagang'];?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Ongkos Angkut</td>
                                                        <td><?php echo number_format($rsm['oa_kirim']);?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Volume</td>
                                                        <td><?php echo number_format($rsm['volume_tawar']).' Liter';?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Harga</td>
                                                        <td><?php echo $harganya;?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                	<?php } ?>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <label>Nomor PO *</label>
                                        <?php if(isset($rsm['nomor_poc'])) { ?>
											<input type="text" id="nomor_po" name="nomor_po" class="form-control validate[required]" value="<?php echo $rsm['nomor_poc']; ?>" <?php echo (!$rsm['disposisi_poc'] || $rsm['poc_approved'] == 2)?'':'readonly';?> />
										<?php } else {?>
											<input type="text" id="nomor_po" name="nomor_po" class="form-control validate[required]" />
										<?php } ?>
									</div>
                                </div>
								<div class="form-group row">
                                    <div class="col-sm-4 col-md-3 col-sm-top">
                                        <label>Tanggal PO *</label>
										<?php if(isset($rsm['tanggal_poc'])) { ?>
											<input type="text" id="tanggal_po" name="tanggal_po" class="form-control validate[required]" autocomplete = 'off' value="<?php echo tgl_indo($rsm['tanggal_poc'],'normal','db','/'); ?>" <?php echo (!$rsm['disposisi_poc'] || $rsm['poc_approved'] == 2)?'':'readonly';?> />
										<?php } else {?>
											<input type="text" id="tanggal_po" name="tanggal_po" class="form-control datepicker validate[required,custom[date]]" autocomplete = 'off' <?php echo (($idr!='' and $idk!='')?'readonly':''); ?> />
										<?php } ?>
                                    </div>
									<div class="col-sm-4 col-md-3 col-sm-top">
                                        <label>Supply Date *</label>
                                        <?php if(isset($rsm['supply_date'])) { ?>
											<input type="text" id="supply_date" name="supply_date" class="form-control validate[required]" autocomplete = 'off' value="<?php echo tgl_indo($rsm['supply_date'],'normal','db','/'); ?>" <?php echo (!$rsm['disposisi_poc'] || $rsm['poc_approved'] == 2)?'':'readonly';?> />
										<?php } else {?>
											<input type="text" id="supply_date" name="supply_date" class="form-control datepicker validate[required,custom[date]]" autocomplete = 'off' <?php echo (($idr!='' and $idk!='')?'readonly':''); ?> />
										<?php } ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>Harga/Liter *</label>
                                        <?php if(isset($rsm['harga_poc'])) { ?>
											<input type="text" id="harga_liter" name="harga_liter" class="form-control hitung validate[required]" autocomplete = 'off' value="<?php echo ($rsm['harga_poc']?$rsm['harga_poc']:"");?>" <?php echo (!$rsm['disposisi_poc'] || $rsm['poc_approved'] == 2)?'':'readonly';?> />
										<?php } else {?>
											<input type="text" id="harga_liter" name="harga_liter" class="form-control hitung validate[required]" autocomplete = 'off' <?php echo (($idr!='' and $idk!='')?'readonly':''); ?> />
										<?php } ?>
                                    </div>
                                    <div class="col-sm-3 col-sm-top">
                                        <label>Total Order *</label>
                                        <div class="input-group">
                                        	<?php if(isset($rsm['volume_poc'])) { ?>
												<input type="text" id="total_volume" name="total_volume" class="form-control hitung validate[required]" value="<?php echo ($rsm['volume_poc']?$rsm['volume_poc']:"");?>" <?php echo (!$rsm['disposisi_poc'] || $rsm['poc_approved'] == 2)?'':'readonly';?> />
											<?php } else {?>
												<input type="text" id="total_volume" name="total_volume" class="form-control hitung validate[required]" autocomplete = 'off' <?php echo (($idr!='' and $idk!='')?'readonly':''); ?> />
											<?php } ?>
											<span class="input-group-addon">Liter</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-sm-top">
                                        <label>Produk *</label>
										<?php 
										$cek_poc = isset($rsm['disposisi_poc'])?$rsm['disposisi_poc']:'';
										$cek_app = isset($rsm['poc_approved'])?$rsm['poc_approved']:'';
										if(!$cek_poc || $cek_app == 2){ ?>
											<select id="produk" name="produk" class="form-control select2 validate[required]">
												<option></option>
												<?php $con->fill_select("id_master","concat(jenis_produk,' - ',merk_dagang)","pro_master_produk",$rsm['produk_poc'],"where is_active =1","id_master",false); ?>
											</select>
                                        <?php } else{ ?>
											<input type="hidden" name="produk" id="produk" value="<?php echo $rsm['produk_poc'];?>" />
											<input type="text" name="produkTxt" id="produkTxt" class="form-control" readonly value="<?php echo $rsm['jenis_produk'].' - '.$rsm['merk_dagang'];?>" />
										<?php } ?>
                                    </div>
                                </div>
                                <?php
                                // if (!$lsAttachment) {
                                if (!$lsClosePo) {
                                ?>
                                <div class="form-group row">
                                    <div class="col-sm-12">
										<?php
											$lamp = isset($rsm['lampiran_poc'])?$rsm['lampiran_poc']:'';
                                            if($lamp && file_exists($pathPt)){
                                                $linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=POC_".$idk."_&file=".$lampPt);
                                                echo '<label>Ubah Lampiran xx</label>';
                                                echo '<p><a href="'.$linkPt.'"><i class="fa fa-file-alt jarak-kanan"></i>'.$lampPt.'</a></p>';
                                            } else{
                                                echo '<label>Lampiran</label>';													
                                            }
                                        ?>
										<input type="file" name="attachment_order" id="attachment_order" class="validate[funcCall[fileCheck]]" /></td>
                                        <p style="font-size:12px;" class="help-block">* Max size 2Mb | .jpg, .png, .rar, .pdf</p>
                                    </div>
                                </div>
                                <?php
                                }   
                                ?>

                                <?php
                                if ($lsClosePo) {
                                ?>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>Kode Dokumen : <?php echo "PO-".$rowPlan['kode_po']; ?></label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>Terkirim : <?php echo number_format($rowPlan['realisasi'])." Liter"; ?></label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>Sisa Buku : <?php echo number_format(($rowPlan['volume_poc'] - $rowPlan['vol_plan']))." Liter"; ?></label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>Tanggal Close PO *</label>
                                        <input type="text" id="tanggal_close" name="tanggal_close" class="form-control datepicker validate[required,custom[date]]" value="<?php echo tgl_indo($rowClosePo['tgl_close'],'normal','db','/'); ?>" autocomplete = 'off'/>
                                    </div>
                                    <div class="col-sm-3 col-sm-top">
                                        <label>Volume *</label>
                                        <div class="input-group">
                                            <input type="text" id="volume_close" name="volume_close" class="form-control hitung validate[required,funcCall[maxnya[<?php echo $rowClosePo['volume_poc'];?>]]]" readonly value="<?php echo number_format(($rowPlan['volume_poc'] - $rowPlan['vol_plan']))." Liter"; ?>" />
                                            <span class="input-group-addon">Liter</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Catatan</label>
                                        <input type="text" id="catatan_close" name="catatan_close" class="form-control" value="<?php echo $rowClosePo['keterangan'];?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <?php
                                            $lamp = isset($rowClosePo['lampiran_close_po'])?$rowClosePo['lampiran_close_po']:'';
                                            if($lamp && file_exists($pathPtClose)){
                                                $linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=POC_".$idk."_&file=".$lampPtClose);
                                                echo '<label>Ubah Lampiran</label>';
                                                echo '<p><a href="'.$linkPt.'"><i class="fa fa-file-alt jarak-kanan"></i>'.$lampPtClose  .'</a></p>';
                                            } else{
                                                echo '<label>Lampiran Close PO</label>';                                                 
                                            }
                                        ?>
                                        <input type="file" name="attachment_order" id="attachment_order" class="validate[funcCall[fileCheck]]" /></td>
                                        <p style="font-size:12px;" class="help-block">* Max size 2Mb | .jpg, .png, .rar, .pdf</p>
                                    </div>
                                </div>
                                <?php
                                }   
                                ?>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="closepo" value="<?php echo $lsClosePo;?>" />
                                            <input type="hidden" name="attachment" value="<?php echo $lsAttachment;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <input type="hidden" name="idk" value="<?php echo $idk;?>" />
                                            <a href="<?php echo BASE_URL_CLIENT."/po-customer.php"; ?>" class="btn btn-default jarak-kanan">
                                            <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
										</div>
                                    </div>
                                </div>
                                <hr style="margin:5px 0" />
                                <div class="clearfix">
                                    <div class="col-sm-12"><small>* Wajib Diisi</small></div>
                                </div>
                                </form>
                            </div>
						</div>
					</div>
				</div>

            <div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Peringatan</h4>
                        </div>
                        <div class="modal-body">
                        	<div id="preview_alert" class="text-center"></div>
						</div>
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
			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style type="text/css">
	.table > tr > td { font-size:12px; padding:5px;}
</style>
<script>
	$(document).ready(function(){
		$("form#gform").validationEngine('attach',{
			onValidationComplete: function(form, status){
				if(status == true){
					$("#loading_modal").modal({backdrop:'static'});
					$.ajax({
						type	: "POST",
						url		: "./__cek_po_customer.php",
						dataType: "json",
						data	: form.serializeArray(),
						cache	: false,
						success : function(data){
							if(data.error){
								$("#preview_modal").find("#preview_alert").html(data.error);
								$("#preview_modal").modal();
								$("#loading_modal").modal("hide");
								return false;
							} else{
								form.validationEngine('detach');
								form.submit();
							}
						}
					});
				}
			}
		});
		$(".hitung").number(true, 0, ".", ",");
		$("select#customer").change(function(){
			$("#loading_modal").modal();
			$("select#penawaran").val("").trigger('change').select2('close');
			$("select#penawaran option").remove();
			$("#top").val("");
			if($(this).val() != ""){
				$.ajax({
					type	: "POST",
					url		: "./__get_top_customer.php",
					dataType: "json",
					data	: { q1: $(this).val() },
					cache	: false,
					success : function(data) {
						$("#top").val(data.top_payment)
                        $('#credit_limit').val(data.credit_limit)
                        let html = `
                            <table border="1" style="width: 125%; margin-top: 0px;">
                                <tr>
                                    <td style="padding: 2px 5px; background-color: #ddd;">Credit Limit</td>
                                    <td style="padding: 2px 5px;">${data.credit_limit}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 5px; background-color: #ddd;">Not yet</td>
                                    <td style="padding: 2px 5px;">${data.not_yet}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 5px; background-color: #ddd;">Overdue 1-30 days</td>
                                    <td style="padding: 2px 5px;">${data.ov_under_30}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 5px; background-color: #ddd;">Overdue 31-60 days</td>
                                    <td style="padding: 2px 5px;">${data.ov_under_60}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 5px; background-color: #ddd;">Overdue 61-90 days</td>
                                    <td style="padding: 2px 5px;">${data.ov_under_90}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 5px; background-color: #ddd;">Overdue > 90 days</td>
                                    <td style="padding: 2px 5px;">${data.ov_up_90}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 5px; background-color: #ddd;">Reminding</td>
                                    <td style="padding: 2px 5px;">${data.reminding}</td>
                                </tr>
                            </table>
                        `
                        if (data.items.length > 0)
                            $('#keterangan_limit').html(html)

						if(data.items != ""){
							$("select#penawaran").select2({ data : data.items, placeholder : "Pilih salah satu", allowClear : true });
							return false;
						}
					}
				});
			}
			$("#loading_modal").modal("hide");
		});
		
		$("select#penawaran").change(function(){
			if($(this).val() != "" && $(this).val() != null){
				$("#loading_modal").modal();
				$.ajax({
					type	: 'POST',
					url		: "./__get_data_penawaran.php",
					dataType: "json",
					data	: { q1:$(this).val() },
					cache	: false,
					success : function(data){
						$("#ket-penawaran").html(data.items);
					}
				});
				$("#loading_modal").modal("hide");
			} else{
				$("#ket-penawaran").html("");
			}
		});
	});
</script>
</body>
</html>      
