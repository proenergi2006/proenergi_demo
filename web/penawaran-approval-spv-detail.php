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

	$sesrole = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	if($sesrole != '20'){ 
		header("location: ".BASE_URL_CLIENT.'/home.php'); exit();
	}

    $date_min_2_months = date('Y-m-d', strtotime("-2 months"));

	$sql = "
		select a.*, b.nama_customer, b.top_payment, b.status_customer, c.fullname, d.nama_cabang, e.jenis_produk, e.merk_dagang, f.harga_normal, f.harga_sm, f.harga_om, g.nama_area,  
		i.harga_normal as harga_normal_new, f.harga_sm as harga_sm_new, f.harga_om as harga_om_new
		from pro_penawaran a 
		join pro_customer b on a.id_customer = b.id_customer 
		join acl_user c on b.id_marketing = c.id_user 
		join pro_master_cabang d on a.id_cabang = d.id_master 
		join pro_master_produk e on a.produk_tawar = e.id_master 
		join pro_master_area g on a.id_area = g.id_master 
		left join pro_master_harga_minyak f on a.masa_awal = f.periode_awal and a.masa_akhir = f.periode_akhir and a.id_area = f.id_area and a.pbbkb_tawar = f.pajak and f.is_approved = 1 
		left join pro_master_harga_minyak i on a.masa_awal = i.periode_awal and a.masa_akhir = i.periode_akhir and a.id_area = i.id_area and i.pajak = 1 and i.is_approved = 1 
		where 1=1 and a.id_customer = '".$idr."' 
		order by a.created_time desc
	";
    $rsms = $con->getResult($sql);

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
        		<h1>Detil Approval Penawaran</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                    	<a href="#form-approval" aria-controls="form-approval" role="tab" data-toggle="tab">Form Approval</a>
					</li>
                    <li role="presentation" class="">
                    	<a href="#data-approval" aria-controls="data-approval" role="tab" data-toggle="tab">Data Penawaran</a>
                    </li>
                    <li role="presentation" class="">
                        <a href="#history-data-approval" aria-controls="history-data-approval" role="tab" data-toggle="tab">History Approval Penawaran</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="form-approval">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="box box-primary">
                                    <div class="box-body">
										<?php
                                            $simpan  = false;
                                            foreach($rsms as $key => $rsm){
                                                if($rsm['id_penawaran'] == $idk){
                                                    $sesrole = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
                                                    $rincian = json_decode($rsm['detail_rincian'], true);
                                                    $formula = json_decode($rsm['detail_formula'], true);
                                                    $sesrole = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
                                                    $seswil  = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
                                                    $notespv = ($rsm['spv_mkt_summary']);
                                                    $notesm1 = ($rsm['sm_mkt_summary']);
                                                    $notesm2 = ($rsm['sm_wil_summary']);
                                                    $noteopm = ($rsm['om_summary']);
                                                    $notecoo = ($rsm['coo_summary']);
                                                    $noteceo = ($rsm['ceo_summary']);
                                                    $arrStat = array(1=>"Disetujui", "Ditolak");

													$rsm['harga_normal'] = ($rsm['harga_normal'] ? $rsm['harga_normal'] : $rsm['harga_normal_new']);
													$rsm['harga_sm'] = ($rsm['harga_sm'] ? $rsm['harga_sm'] : $rsm['harga_sm_new']);
													$rsm['harga_om'] = ($rsm['harga_om'] ? $rsm['harga_om'] : $rsm['harga_om_new']);
                                                    
                                                    
													$arrKondInd	= array(0=>'', 1=>"Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
													$arrKondEng = array(0=>'', 1=>"After Invoice Receive", "After Delivery", "After Loading");
                                                    $jenis_net  = $rsm['jenis_net'];

													$tmp_calc 	= (json_decode($rsm['kalkulasi_oa'], true) === NULL)?array(1):json_decode($rsm['kalkulasi_oa'], true);
													$calcoa1 	= ($tmp_calc[0]['transportir'] ? $tmp_calc[0]['transportir'] : '');
													$calcoa2 	= ($tmp_calc[0]['wiloa_po'] ? $tmp_calc[0]['wiloa_po'] : '');
													$calcoa3 	= ($tmp_calc[0]['voloa_po'] ? $tmp_calc[0]['voloa_po'] : 'N/A');
													$calcoa4 	= ($tmp_calc[0]['ongoa_po'] ? $tmp_calc[0]['ongoa_po'] : 'N/A');
                                                
                                        ?>
                                        <form action="<?php echo ACTION_CLIENT.'/penawaran-approval-spv.php'; ?>" id="gform" name="gform" class="form-horizontal" method="post" role="form">
                                        <div class="form-group row">
                                            <div class="col-sm-8">
                                                <div class="table-responsive">
                                                    <input type="hidden" name="keterangan_pengajuan" id="keterangan_pengajuan" value="<?php echo $rsm['catatan'];?>" />
                                                    <input type="hidden" name="volume" id="volume" value="<?php echo $rsm['volume_tawar'];?>" />

                                                    <table class="table table-bordered table-summary">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2" style="background-color:#f4f4f4; vertical-align:middle; padding:8px 5px;">
                                                                    <b>PRICELIST</b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Masa Berlaku Harga</td>
                                                                <td><?php echo tgl_indo($rsm['masa_awal'])." - ".tgl_indo($rsm["masa_akhir"]);?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Rekomendasi Harga Dasar</td>
                                                                <td><?php echo number_format($rsm['harga_normal']); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Rekomendasi Ongkos Angkut</td>
                                                                <td><?php echo number_format($rsm['oa_kirim']); ?></td>
                                                            </tr>
                                                            <?php if($sesrole == 21){ ?>
                                                            <tr>
                                                                <td>Harga Terendah CEO</td>
                                                                <td><?php echo number_format($rsm['harga_ceo']); ?></td>
                                                            </tr>
                                                            <?php } if($sesrole == 3 || $sesrole == 21){ ?>
                                                            <tr>
                                                                <td>Harga Terendah COO</td>
                                                                <td><?php echo number_format($rsm['harga_coo']); ?></td>
                                                            </tr>
                                                            <?php } if($sesrole == 6 || $sesrole == 3 || $sesrole == 21){ ?>
                                                            <tr>
                                                                <td>Harga Terendah OM</td>
                                                                <td><?php echo number_format($rsm['harga_om']); ?></td>
                                                            </tr>
                                                            <?php } if($sesrole == 7 || $sesrole == 6 || $sesrole == 3 || $sesrole == 21){ ?>
                                                            <tr>
                                                                <td>Harga Terendah BM</td>
                                                                <td><?php echo number_format($rsm['harga_sm']); ?></td>
                                                            </tr>
                                                            <?php } ?>
                                                            <tr>
                                                                <td colspan="2" style="background-color:#f4f4f4; vertical-align:middle; padding:8px 5px;">
                                                                    <b>SUMMARY</b>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="200">Nama Customer</td>
                                                                <td><?php echo $rsm['nama_customer'];?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Lokasi Pengiriman</td>
                                                                <td><?php echo $rsm['lok_kirim']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Volume</td>
                                                                <td><?php echo number_format($rsm['volume_tawar'])." Liter";?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Refund</td>
                                                                <td><?php echo ($rsm['refund_tawar'])?number_format($rsm['refund_tawar']):'-'; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Other Cost</td>
                                                                <td><?php echo ($rsm['other_cost'])?number_format($rsm['other_cost']):'-'; ?></td>
                                                            </tr>
                                    
                                                            <?php
                                                                $cnt_rincian = 0;
                                                                foreach($rincian as $arr1){
                                                                    $cnt_rincian++;
                                                                    $biaya = ($arr1['biaya'])?$arr1['biaya']:'';
                                    
                                                                    if($cnt_rincian == '1') {
                                                                        echo '<input type="hidden" name="harga_dasar" id="harga_dasar" value="'.$arr1['biaya'].'" />';
                                                                    } else if($cnt_rincian == '2'){
                                                                        echo '<input type="hidden" name="oa_kirim" id="oa_kirim" value="'.$arr1['biaya'].'" />';
                                                                    } else if($cnt_rincian == '3'){
                                                                        echo '<input type="hidden" name="ppn" id="ppn" value="'.$arr1['biaya'].'" />';
                                                                    } else if($cnt_rincian == '4'){
                                                                        echo '<input type="hidden" name="pbbkb" id="pbbkb" value="'.$arr1['biaya'].'" />';
                                                                    }
                                                                } 
                                                            ?>
                                    
                                    
                                                            <?php if($rsm['perhitungan'] == 1){ ?>
                                                            <tr>
                                                                <td>Harga Penawaran</td>
                                                                <td><?php echo number_format($rsm['harga_dasar']); ?>
                                                                </td>
                                                            </tr>
                                                            <?php } else{ ?>
                                                            <tr>
                                                                <td colspan="2">Perhitungan menggunakan formula</td>
                                                            </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>

												<?php
                                                    $breakdown = false;
                                                    foreach($rincian as $temp){
                                                        $breakdown = $breakdown || 1;
                                                    }
                                                    if($breakdown && $rsm['perhitungan'] == 1){
                                                        $nom = 0;
                                                ?>
                                                <p style="margin:10px 0px;">Dengan rincian sebagai berikut:</p>
                                                
                                                <div class="form-group row">
                                                    <div class="col-sm-8">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <th class="text-center" width="50">NO</th>
                                                                    <th class="text-center" width="">RINCIAN</th>
                                                                    <th class="text-center" width="100">NILAI</th>
                                                                    <th class="text-center" width="130">HARGA</th>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                    foreach($rincian as $arr1){
                                                                        $nom++;
                                                                        $cetak = 1;
                                                                        $nilai = $arr1['nilai'];
                                                                        $biaya = ($arr1['biaya'])?$arr1['biaya']:'';
                                                                        $biaya = ($rsm['pembulatan']) ? number_format($arr1['biaya']) : number_format($arr1['biaya'], 2);
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
                                                            <?php if($rsm['pembulatan']) { ?>
                                                            <p style="margin:0px 0px 5px;"><i>*) Perhitungan menggunakan pembulatan</i></p>
                                                            <?php } else{ ?>
                                                            <p style="margin:0px 0px 5px;"><i>*) Perhitungan tidak menggunakan pembulatan</i></p>
                                                            <?php } ?>

                                                            <?php if($rsm['gabung_oa']) { ?>
                                                            <p style="margin:0px 0px 15px;"><i>*) Cetakan Harga Dasar Termasuk Ongkos Angkut</i></p>
                                                            <?php } else{ ?>
                                                            <p style="margin:0px 0px 15px;"><i>*) Cetakan Harga Dasar Tidak Termasuk Ongkos Angkut</i></p>
                                                            <?php } ?>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php } ?>

                                            </div>
                                        </div>

										<?php 
											if($rsm['flag_disposisi'] == 1 && !$rsm['spv_mkt_result']){
												$simpan = true;
												echo '
												<div class="row">
													<div class="col-md-8">
														<div class="form-group form-group-sm">
															<label class="control-label col-md-12">Catatan Supervisor Marketing</label>
															<div class="col-md-12">
																<textarea name="spv_mkt_summary" id="spv_mkt_summary" class="form-control" style="height:90px;" required></textarea>
															</div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-4">
														<div class="form-group form-group-sm">
															<label class="control-label col-md-12">Persetujuan</label>
															<div class="col-md-12">
																<div class="radio">
																	<label class="rtl"><input type="radio" name="approval" id="approval1" value="1" required /> Ya</label>
																</div>
																<div class="radio">
																	<label class="rtl"><input type="radio" name="approval" id="approval2" value="2" required /> Tidak</label>
																</div>
															</div>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group form-group-sm">
															<label class="control-label col-md-12">Diteruskan ke SM</label>
															<div class="col-md-12">
																<div class="radio">
																	<label class="rtl"><input type="radio" name="extend" id="extend1" value="1" required /> Ya</label>
																</div>
																<div class="radio">
																	<label class="rtl"><input type="radio" name="extend" id="extend2" value="2" required /> Tidak</label>
																</div>
															</div>
														</div>
													</div>
												</div>';
											} else{
												if($rsm['spv_mkt_result'] && $rsm['spv_mkt_pic'] && $rsm['spv_mkt_tanggal']){
													echo '
													<div class="form-group row">
														<div class="col-sm-8">
															<label>Catatan Supervisor Marketing</label>
															<div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
																'.nl2br($notespv).'
																<p style="margin:10px 0 0; font-size:12px;">
																	<i>'.($rsm['spv_mkt_pic'] ? $rsm['spv_mkt_pic'].' - ' : '&nbsp;').
																	($rsm['spv_mkt_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['spv_mkt_tanggal'])).' WIB' : '').'</i>
																</p>
															</div>
														</div>
													</div>';
												}
												
												if($rsm['sm_mkt_result'] && $rsm['sm_mkt_pic'] && $rsm['sm_mkt_tanggal']){
													echo '
													<div class="form-group row">
														<div class="col-sm-8">
															<label>Catatan Branch Manager Marketing</label>
															<div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
																'.nl2br($notesm1).'
																<p style="margin:10px 0 0; font-size:12px;">
																	<i>'.($rsm['sm_mkt_pic'] ? $rsm['sm_mkt_pic'].' - ' : '&nbsp;').
																	($rsm['sm_mkt_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['sm_mkt_tanggal'])).' WIB' : '').'</i>
																</p>
															</div>
														</div>
													</div>';
												}

												if($rsm['sm_wil_result'] && $rsm['sm_wil_pic'] && $rsm['sm_wil_tanggal']){
													echo '
													<div class="form-group row">
														<div class="col-sm-8">
															<label>Catatan Branch Manager</label>
															<div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
																'.nl2br($notesm2).'
																<p style="margin:10px 0 0; font-size:12px;">
																	<i>'.($rsm['sm_wil_pic'] ? $rsm['sm_wil_pic'].' - ' : '&nbsp;').
																	($rsm['sm_wil_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['sm_wil_tanggal'])).' WIB' : '').'</i>
																</p>
															</div>
														</div>
													</div>';
												}

												if($rsm['om_result'] && $rsm['om_pic'] && $rsm['om_tanggal']){
													echo '
													<div class="form-group row">
														<div class="col-sm-8">
															<label>Catatan Operation Manager</label>
															<div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
																'.nl2br($noteopm).'
																<p style="margin:10px 0 0; font-size:12px;">
																	<i>'.($rsm['om_pic'] ? $rsm['om_pic'].' - ' : '&nbsp;').
																	($rsm['om_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['om_tanggal'])).' WIB' : '').'</i>
																</p>
															</div>
														</div>
													</div>';
												}

												if($rsm['coo_result'] && $rsm['coo_pic'] && $rsm['coo_tanggal']){
													echo '
													<div class="form-group row">
														<div class="col-sm-8">
															<label>Catatan COO</label>
															<div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
																'.nl2br($notecoo).'
																<p style="margin:10px 0 0; font-size:12px;">
																	<i>'.($rsm['coo_pic'] ? $rsm['coo_pic'].' - ' : '&nbsp;').
																	($rsm['coo_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['coo_tanggal'])).' WIB' : '').'</i>
																</p>
															</div>
														</div>
													</div>';
												}

												if($rsm['ceo_result'] && $rsm['ceo_pic'] && $rsm['ceo_tanggal']){
													echo '
													<div class="form-group row">
														<div class="col-sm-8">
															<label>Catatan CEO</label>
															<div class="form-control" style="height:auto; min-height:90px; font-size:12px;">
																'.nl2br($noteceo).'
																<p style="margin:10px 0 0; font-size:12px;">
																	<i>'.($rsm['ceo_pic'] ? $rsm['ceo_pic'].' - ' : '&nbsp;').
																	($rsm['ceo_tanggal'] ? date("d/m/Y H:i:s", strtotime($rsm['ceo_tanggal'])).' WIB' : '').'</i>
																</p>
															</div>
														</div>
													</div>';
												}
											}
                                        ?> 

                                        <hr style="margin:15px 0px; border-top:4px double #ddd;" />
                                    
                                        <div style="margin-bottom:0px;">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                            <input type="hidden" name="idk" value="<?php echo $idk;?>" />
                                            <?php if($simpan === true){ ?>
                                                <button type="submit" name="btnSbmt" id="btnSbmt" class="btn btn-primary jarak-kanan" style="min-width:90px;">
                                                <i class="fa fa-save jarak-kanan"></i> Simpan</button> 
                                            <?php } ?>
                                            <a class="btn btn-default" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT."/penawaran-approval-spv.php";?>">
                                            <i class="fa fa-reply jarak-kanan"></i> Batal</a>
                                        </div>
                                        </form>
										<?php } } /* END FOREACH */?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="data-approval">
                        <?php require_once($public_base_directory."/web/penawaran-history-data.php"); ?>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="history-data-approval">
                        <?php require_once($public_base_directory."/web/penawaran-history-approval.php"); ?>
                    </div>

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
	.table{
		margin-bottom: 10px;
	}
	.table > tbody > tr > td{
		padding: 5px;
	}
	h3.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
	}
	.table-summary > tbody > tr > td{
		padding: 3px 5px;
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
	});

	var formValidasiCfg = {
		submitHandler: function(form) {
			$("#loading_modal").modal({keyboard:false, backdrop:'static'});

			if($("#cekkolnup").is(":checked") && $("#nup_fee").val() == ""){
				$("#loading_modal").modal("hide");
				$.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
				setErrorFocus($("#nup_fee"), $("form#gform"), false);
			} else{
				form.submit();
			}
		}	
	};
	$("form#gform").validate($.extend(true,{},config.validation,formValidasiCfg));

});		
</script>
</body>
</html>      
