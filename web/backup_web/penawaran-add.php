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
	$sesuser = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
    $seswil = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $sesgroup = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);

    $id_cabang = null;
	if($idk != ""){
		$sql = "select a.*, if(b.kode_pelanggan = '',b.nama_customer,concat(b.kode_pelanggan,' - ',b.nama_customer)) as nm_customer, b.status_customer, b.top_payment, 
				b.jenis_payment as jenis_waktu, c.nama_cabang, d.nama_area 
				from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join pro_master_cabang c on a.id_cabang = c.id_master 
				join pro_master_area d on a.id_area = d.id_master where a.id_penawaran = '".$idk."' and a.id_customer = '".$idr."'";
		$rsm = $con->getRecord($sql);
		$action 	= "update";
		$titleAct 	= "Ubah Penawaran";
		$rincian 	= (json_decode($rsm['detail_rincian'], true) === NULL)?array(1):json_decode($rsm['detail_rincian'], true);
		$formula 	= (json_decode($rsm['detail_formula'], true) === NULL || count(json_decode($rsm['detail_formula'], true)) == 0)?array(""):json_decode($rsm['detail_formula'], true);
		$harga 		= $rsm['harga_dasar'];
		$cara_order = $rsm['method_order'];
		$vol_tawar 	= $rsm['volume_tawar'];
		$id_cabang 	= $rsm['id_cabang'];
		$nm_cabang 	= $rsm['nama_cabang'];
	} else{
		$rsm 		= array();
		$action 	= "add";
		$titleAct 	= "Tambah Penawaran";
		$rincian	= array(array("rincian"=>"Harga Dasar"),array("rincian"=>"Ongkos Angkut"),array("rincian"=>"PPN","nilai"=>"10"),array("rincian"=>"PBBKB"));
		$formula 	= array("");
		$cara_order = 2;
		$vol_tawar 	= "";
		if($idc){
			$sql = "select a.id_wilayah, b.nama_cabang from pro_customer a left join pro_master_cabang b on a.id_wilayah = b.id_master where a.id_customer = '".$idc."'";
			$rsm = $con->getRecord($sql);
			$id_cabang 	= $rsm['id_wilayah'];
			$nm_cabang 	= $rsm['nama_cabang'];
		}
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
        		<h1><?php echo $titleAct; ?></h1>
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
                                <?php if(isset($rsm['flag_disposisi']) || isset($rsm['flag_approval'])){ $reFlag = 1; ?>
                                <div style="padding:15px; margin-bottom:15px; background-color:#00a7d0; color:#fff;">
                                	PERHATIAN!! Merubah data ini akan mengulang proses persetujuan data penawaran
                                </div>
                                <?php } ?>

                                <form action="<?php echo ACTION_CLIENT.'/penawaran.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div class="form-group row">
                                    <div class="col-sm-6">
										<label>Customer *</label>
                                        <?php if($action == "add"){ ?>
                                        <?php
                                            $where = "where id_marketing = '".$sesuser."'";
                                            if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 18) {
                                                $where = "where 1=1";
                                                if (paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
                                                    $where = "where (id_wilayah = '".$seswil."' or id_marketing = '".$sesuser."')";
                                                else if (!paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']) and paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']))
                                                    $where = "where (id_group = '".$sesgroup."' or id_marketing = '".$sesuser."')";
                                            }
                                        ?>
                                        <select name="idr" id="idr" class="form-control validate[required] select2">
                                            <option></option>
                                            <?php $con->fill_select("id_customer","if(kode_pelanggan = '',nama_customer,concat(kode_pelanggan,' - ',nama_customer))","pro_customer",$idc,$where,"id_customer desc, nama",false); ?>
                                        </select>
                                        <p id="infoin" style="margin-bottom:0px;" class="help-block"></p>
                                        <?php } else if($action == "update"){ ?>
										<input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                        <div class="form-control"><?php echo $rsm['nm_customer']; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
								 <div class="form-group row">
                                    <div class="col-sm-6">
										<label>Area *</label>
                                        <?php if($action == "add"){ ?>
                                        <select name="area" id="area" class="form-control validate[required] select2">
                                            <option></option>
                                            <?php $con->fill_select("id_master","nama_area","pro_master_area","","where is_active=1","nama_area",false); ?>
                                        </select>
                                        <?php } else if($action == "update"){ ?>
										<input type="hidden" name="area" id="area" value="<?php echo $rsm['id_area'];?>" />
                                        <div class="form-control"><?php echo $rsm['nama_area']; ?></div>
                                        <?php } ?>
                                    </div>
								</div>
								
                                <div class="form-group row">
                                    <div class="col-sm-6">
										<label>Cabang Invoice *</label>
										<div id="wrCabang">
                                        <?php if($id_cabang){ ?>
                                        <input type="hidden" name="cabang" id="cabang" value="<?php echo $id_cabang;?>" />
                                        <div class="form-control"><?php echo $nm_cabang; ?></div>
                                        <?php } else{ ?>
                                        <select name="cabang" id="cabang" class="form-control validate[required]">
                                            <option></option>
                                            <?php $con->fill_select("id_master","nama_cabang","pro_master_cabang",$id_cabang,"where is_active=1 and id_master <> 1","",false); ?>
                                        </select>
                                        <?php } ?>
                                        </div>
                                    </div>
								</div>

                                <hr style="border-color:#ddd" />
                                <div class="form-group row">
                                    <div class="col-sm-3">
										<label>Kepada *</label>
                                        <select name="gelar" id="gelar" class="form-control validate[required] select2">
                                            <option></option>
                                            <option value="Bapak" <?php echo (isset($rsm['gelar']) && $rsm['gelar'] == 'Bapak')?' selected':''; ?>>Bapak</option>
                                            <option value="Ibu" <?php echo (isset($rsm['gelar']) && $rsm['gelar'] == 'Ibu')?' selected':''; ?>>Ibu</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-5 col-sm-top">
                                        <label>Nama *</label>
                                        <input type="text" name="nama_up" id="nama_up" class="form-control validate[required]" value="<?php echo $rsm['nama_up'] ?? null; ?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Jabatan</label>
                                        <input type="text" name="jabatan_up" id="jabatan_up" class="form-control" value="<?php echo $rsm['jabatan_up'] ?? null; ?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Alamat Korespondensi</label>
                                        <input type="text" name="alamat_up" id="alamat_up" class="form-control" value="<?php echo $rsm['alamat_up'] ?? null; ?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <label>Telepon</label>
                                        <input type="text" name="telp_up" id="telp_up" class="form-control" value="<?php echo $rsm['telp_up']; ?>" />
                                    </div>
                                    <div class="col-sm-4 col-sm-top">
                                        <label>Fax</label>
                                        <input type="text" name="fax_up" id="fax_up" class="form-control" value="<?php echo $rsm['fax_up']; ?>" />
                                    </div>
                                </div>
                                <hr style="border-color:#ddd" />

                                <p id="pemberitahuan" class="infoMinyak"></p>
								
								 <div class="form-group row">
                                    <div class="col-sm-3">
                                        <label>Masa berlaku harga *</label>
                                        <input type="text" name="masa_awal" id="masa_awal" autocomplete="off" class="form-control validate[required,custom[date]] datepicker" value="<?php echo isset($rsm['masa_awal'])?tgl_indo($rsm['masa_awal'], 'normal', 'db', '/'):''; ?>" />
                                    </div>
                                    <div class="col-sm-3 col-sm-top">
                                        <label>Sampai dengan *</label>
                                        <input type="text" name="masa_akhir" id="masa_akhir" autocomplete="off" class="form-control validate[required,custom[date]] datepicker" value="<?php echo isset($rsm['masa_akhir'])?tgl_indo($rsm['masa_akhir'], 'normal', 'db', '/'):''; ?>" />
                                    </div>
                                </div>
								
								<div class="form-group row">
                                    <div class="col-sm-3">
										<label>Produk *</label>
                                        <select name="produk_tawar" id="produk_tawar" class="form-control validate[required] select2">
                                            <option></option>
                                            <?php $con->fill_select("id_master","concat(jenis_produk,' - ',merk_dagang)", "pro_master_produk", $rsm['produk_tawar'], "where is_active =1","id_master",false); ?>
                                        </select>
                                    </div>
									<div class="col-sm-3">
										<label>PBBKB *</label>
                                        <select name="pbbkb_tawar" id="pbbkb_tawar" class="form-control validate[required] select2">
                                            <option></option>
                                            <?php $con->fill_select("id_master","concat(nilai_pbbkb, ' %')","pro_master_pbbkb",$rsm['pbbkb_tawar'],"","",false); ?>
                                        </select>
                                    </div>
                                </div>
								
                                <div class="form-group row">
                                    <div class="col-sm-3">
										<label>Tipe Pembayaran *</label>
                                        <input type="hidden" name="jenis_waktu" id="jenis_waktu" value="" />
                                        <?php if(isset($rsm['status_customer']) && $rsm['status_customer'] > 1){ ?>
                                        <input type="text" name="jenis_payment" id="jenis_payment" class="form-control" value="<?php echo $rsm['jenis_waktu']; ?>" readonly  />
                                        <?php } else { ?>
                                        <select name="jenis_payment" id="jenis_payment" class="form-control validate[required] select2">
                                            <option></option>
                                            <option value="CBD" <?php echo (isset($rsm['jenis_payment']) && $rsm['jenis_payment'] == 'CBD')?' selected':''; ?>>CBD (Cash Before Delivery)</option>
                                            <option value="COD" <?php echo (isset($rsm['jenis_payment']) && $rsm['jenis_payment'] == 'COD')?' selected':''; ?>>COD (Cash On Delivery)</option>
                                            <option value="CREDIT" <?php echo (isset($rsm['jenis_payment']) && $rsm['jenis_payment'] == 'CREDIT')?' selected':''; ?>>CREDIT</option>
                                        </select>
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-3 col-sm-top<?php echo ($rsm['jenis_payment'] == "CREDIT" || $rsm['jenis_waktu'] == "CREDIT"?'':' hide');?>" id="jwp">
                                        <label>Jangka Waktu Pembayaran *</label>
                                        <div class="input-group">
                                        	<input type="text" name="top" id="top" class="form-control validate[required] text-right" <?php echo (isset($rsm['status_customer']) and $rsm['status_customer'] > 1)?'value="'.$rsm['top_payment'].'" readonly':'value="'.$rsm['jangka_waktu'].'"'; ?> />
											<span class="input-group-addon">Hari</span>
                                        </div>
										
                                    </div>
									<div class="col-sm-3 col-sm-top<?php echo ($rsm['jenis_payment'] == "CREDIT" || $rsm['jenis_waktu'] == "CREDIT"?'':' hide');?>" id="jwp2">
										<label>&nbsp;</label>
										<div class="input-group">
											 <select name="jenis_net" id="jenis_net" class="form-control validate[required] select2">
                                            <option></option>
                                            <option value="1" <?php echo ($rsm['jenis_net'] == '1')?'selected':''; ?>>Setelah Invoice Diterima</option>
                                            <option value="2" <?php echo ($rsm['jenis_net'] == '2')?'selected':''; ?>>Setelah Pengiriman</option>
                                        </select>
										</div>
									</div>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-sm-3">
										<label>Order Method *</label>
                                        <div class="input-group">
                                            <input type="text" name="order_method" id="order_method" class="form-control text-right" value="<?php echo $cara_order; ?>" />
                                            <span class="input-group-addon">Hari sebelum pickup</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-sm-top">
										<label>Toleransi Penyusutan *</label>
                                        <div class="input-group">
                                            <input type="text" name="tol_susut" id="tol_susut" class="form-control validate[required] text-right" value="<?php echo (empty($rsm['tol_susut']) || $rsm['tol_susut']=='')?'0.5':$rsm['tol_susut'];?>" />
                                            <span class="input-group-addon">%</span>
                                        </div>  
                                    </div>
									<div class="col-sm-3 col-sm-top">
										<label>Lokasi Pengiriman (Harga Terima..) *</label>
                                            <input type="text" name="lok_kirim" id="lok_kirim" class="form-control validate[required]" value="<?php echo $rsm['lok_kirim'] ?? null;?>" />
                                    </div>
                                </div>
								
								<div class="form-group row">
									<div class="col-sm-6 col-sm-top">
                                        <div class="table-responsive" style="border:1px solid #ddd;">
                                            <table class="table no-border table-detail">
                                                <tr>
                                                    <td colspan="2" style="background-color:#f4f4f4; border-bottom:1px solid #ddd;"><b>Kalkulasi OA</b></td>
                                                </tr>
												 <tr>
                                                    <td width="150">Transportir</td>
                                                    <td><select name="cb_transportir" id="cb_transportir" class="form-control">
                                                        <option></option>
													
                                                        <?php $con->fill_select("id_master","concat(nama_transportir, ' - ',  lokasi_suplier, ' (', nama_suplier, ')')","pro_master_transportir","","nama_suplier",false); ?>
													</select></td>
                                                </tr>
                                                <tr>
                                                    <td>Wilayah</td>
                                                    <td><select name="wiloa_po" id="wiloa_po" class="form-control">
                                                        <option></option>
                                                        <?php $con->fill_select("a.id_master","upper(concat(a.wilayah_angkut,'#',c.nama_kab,' ',b.nama_prov))","pro_master_wilayah_angkut a join pro_master_provinsi b on a.id_prov = b.id_prov join pro_master_kabupaten c on a.id_kab = c.id_kab","","where a.is_active=1","nama",false); ?>
													</select></td>
                                                </tr>
                                                <tr>
                                                    <td>Volume</td>
                                                    <td><select name="voloa_po" id="voloa_po" class="form-control select2">
                                                		<option></option>
                                                		<?php $con->fill_select("volume_angkut","volume_angkut","pro_master_volume_angkut","","where is_active = 1","",false); ?>
                                            		</select></td>
                                                </tr>
												<tr>
                                                    <td>Ongkos Angkut</td>
                                                    <td><input type="text" name="ongoa_po" id="ongoa_po" class="form-control hitung" readonly /></td>
                                                </tr>
                                            </table>
                                		</div>
                                	</div>
                                </div>
								
								<div class="form-group row">
									<div class="col-sm-3 col-sm-top">
										<label>Ongkos Angkut Pengiriman *</label>
                                        <input type="text" name="oa_kirim" id="oa_kirim" class="form-control hitung validate[required]" value="<?php echo $rsm['oa_kirim']; ?>" />
                                    </div>
									<div class="col-sm-3 col-sm-top">
										<label>Refund</label>
                                        <input type="text" name="refund" id="refund" class="form-control hitung " value="<?php echo $rsm['refund_tawar']; ?>" />
                                    </div>
									<div class="col-sm-3 col-sm-top">
										<label>Other Cost</label>
                                        <input type="text" name="other_cost" id="other_cost" class="form-control hitung" value="<?php echo $rsm['other_cost']; ?>" />
                                    </div>
								</div>
                                
                                <div class="form-group row">
									<div class="col-sm-3 col-sm-top">
                                        <label>Volume *</label>
                                        <div class="input-group">
                                            <input type="text" name="volume" id="volume" class="form-control validate[required] text-right" value="<?php echo $vol_tawar;?>" />
                                            <span class="input-group-addon">Liter</span>
                                        </div>                                        
                                    </div>
                                    <div class="col-sm-3 col-sm-top">
                                        <label>Perhitungan *</label>
                                        <select name="perhitungan" id="perhitungan" class="form-control validate[required] select2">
                                            <option></option>
                                            <option value="1"<?php echo (isset($rsm['perhitungan']) && $rsm['perhitungan'] == 1)?' selected':''; ?>>Harga</option>
                                            <option value="2"<?php echo (isset($rsm['perhitungan']) && $rsm['perhitungan'] == 2)?' selected':''; ?>>Formula</option>
                                        </select>
                                    </div>
                                </div>
								<div id="byHarga" class="<?php echo ($rsm['perhitungan'] == 1)?'':'hide'; ?>">
									<div class="form-group row">
                                        <div class="col-sm-4">
                                            <label>Harga perliter *</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">Rp.</span>
                                                <input type="text" name="harga_dasar" id="harga_dasar" class="form-control validate[required]" value="<?php echo $harga ?? null;?>" />
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="checkbox checkbox-single">
                                                <label class="rtl">
												<input type="checkbox" name="is_rinci" id="is_rinci" value="1" <?php echo (isset($rsm['is_rinci']) and $rsm['is_rinci'] == 1)?'checked':''; ?> />Customize
                                                </label>
                                            </div>
                                        </div>
                                    </div>
									
                                    <div class="row"><div class="col-sm-10"><div class="table-responsive">
                                        <table class="table table-bordered table-hover tblHarga">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="7%">
                                                    	<input type="checkbox" name="is_cetak_all" id="is_cetak_all" value="1" class="is_cetak_all" />
                                                    </th>
                                                    <th class="text-center" width="7%">No</th>
                                                    <th class="text-center" width="34%">Rincian</th>
                                                    <th class="text-center" width="15%">Nilai</th>
                                                    <th class="text-center" width="30%">Harga</th>
                                                    <th class="text-center" width="7%">
                                                    	<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php 
												$nom = 0;
												$totnya = 0;
												foreach($rincian as $idx1=>$arr1){
													$nom++;
													$cetak = $arr1['rinci'] ?? null;
													$nilai = $arr1['nilai'] ?? 0;
													$biaya = $arr1['biaya'] ?? 0;
													$jenis = $arr1['rincian'] ?? null;
													$arrte = array("0","2","3");
													$chkd1 = ($cetak)?'checked':'';
													$totnya= $biaya + $totnya;
											?>
                                            <tr>
                                                <td class="text-center">
                                                	<input type="checkbox" name="<?php echo 'is_cetak['.$idx1.']';?>" id="<?php echo 'is_cetak'.$nom;?>" value="1" class="is_cetak" <?php echo $chkd1;?> />
                                                </td>
                                                <td class="text-center">
                                                    <span id="<?php echo 'noHarga'.$nom;?>" class="noHarga" data-row-count="<?php echo $nom;?>"><?php echo $nom;?></span>
												</td>
                                                <td class="text-left">
                                                    <input type="text" name="<?php echo 'jnsHarga['.$idx1.']';?>" id="<?php echo 'jnsHarga'.$nom;?>" class="form-control input-sm" value="<?php echo $jenis;?>" <?php echo ($idx1 < 4)?'readonly':'';?> />
												</td>
                                                <td class="text-right">
													<?php if($idx1 < 2) echo '&nbsp;'; else{ ?>
                                                    <div class="input-group">
                                                        <input type="text" name="<?php echo 'clcHarga['.$idx1.']';?>" id="<?php echo 'clcHarga'.$nom;?>" class="<?php echo ($idx1 == 3 ? 'form-control input-sm text-right ncpkb' : 'form-control input-sm text-right');?>" value="<?php echo $nilai;?>" <?php echo ($idx1 == 2 ? 'readonly' : '');?> />
                                                        <span class="input-group-addon">%</span>
                                                    </div>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right">
                                                	<input type="text" name="<?php echo 'rncHarga['.$idx1.']';?>" id="<?php echo 'rncHarga'.$nom;?>" class="<?php echo ($idx1 == 1 ? 'form-control input-sm hitung ncoa' : 'form-control input-sm hitung');?>" value="<?php echo $biaya;?>" <?php echo (!isset($rsm['is_rinci']) && $idx1 == 0 ? 'readonly' : '');?> />
												</td>
                                                <td class="text-center">
                                                	<?php echo ($idx1 < 4)?'&nbsp;':'<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>' ?>
                                                </td>
                                            </tr>
											<?php } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="text-center" colspan="4"><b>TOTAL</b></td>
                                                    <td>
                                                    <input type="text" name="totnya" id="totnya" class="form-control input-sm text-right" value="<?php echo $totnya;?>" readonly />                                                    </td>
                                                    <td class="text-center">&nbsp;</td>
                                                </tr>
                                            </tfoot>
                                        </table>
									</div></div></div>
								</div>
								<div id="byFormula" class="<?php echo ($rsm['perhitungan'] == 2)?'':'hide'; ?>">
									<div class="row"><div class="col-sm-8"><div class="table-responsive">
                                        <table class="table table-bordered table-hover tblFormula">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="7%">No</th>
                                                    <th class="text-center" width="83%">Rincian</th>
                                                    <th class="text-center" width="10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
											<?php $nom = 0; foreach($formula as $arr1){ $nom++; ?>
                                            <tr>
                                                <td class="text-center">
                                                    <span id="<?php echo 'noFormula'.$nom;?>" class="noFormula" data-row-count="<?php echo $nom;?>"><?php echo $nom;?></span>
                                                </td>
                                                <td class="text-left">
                                            		<input type="text" name="jnsfor[]" id="<?php echo 'jnsfor'.$nom;?>" class="form-control input-sm" value="<?php echo $arr1;?>" />
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button>
                                                    <a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div></div></div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
										<label>Keterangan Harga</label>
                                        <input type="text" name="ket_harga" id="ket_harga" class="form-control" value="<?php echo $rsm['ket_harga'] ?? null;?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-8">
                                        <label>Catatan</label>
                                        <textarea name="catatan" id="catatan" class="form-control"><?php echo isset($rsm['catatan'])?str_replace('<br />', PHP_EOL, $rsm['catatan']):''; ?></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="act" value="<?php echo $action;?>" />
                                            <input type="hidden" name="idk" value="<?php echo $idk;?>" />
                                            <input type="hidden" id="tmc" name="tmc" value="<?php echo $idc;?>" />
                                            <input type="hidden" id="reflag" name="reflag" value="<?php echo $reFlag;?>" />
                                            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/penawaran.php";?>">
                                            <i class="fa fa-reply jarak-kanan"></i>Batal</a>
                                            <button type="submit" class="btn btn-primary <?php echo ($action == "add")?'disabled':''; ?>" name="btnSbmt" id="btnSbmt">
                                            <i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
                                        </div>
                                    </div>
                                </div>
                                </form>
                                
                                
                            </div>
                        </div>
                    </div>
                </div>

            <div id="optCabang" class="hide"><?php $con->fill_select("id_master","nama_cabang","pro_master_cabang","","where is_active=1 and id_master <> 1","",false); ?></div>
            <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
	#harga_dasar { 
		text-align: right;
	}
</style>
<script>
	$(document).ready(function(){
		var objAttach = {
			onValidationComplete: function(form, status){
				if(status == true){
					if($("#clcHarga4").val() != $("#pbbkb_tawar option:selected").text().slice(0,-2)){
						alert('Harga PBBKB Tidak Sama Dengan Harga PBBKB di Tabel Harga');
						return false;
					}else{
						form.validationEngine('detach');
						form.submit();
					}
				}
			}
		};
		$("form#gform").validationEngine('attach',objAttach);
		$("select#cb_transportir").select2({
			placeholder	: "Pilih Transportir",
			allowClear	: true,
		});
		$("select#wiloa_po").select2({
			placeholder	: "Pilih salah satu",
			allowClear	: true,
			templateResult : function(repo){ 
				if(repo.loading) return repo.text;
				var text1 = repo.text.split("#");
				var $returnString = $('<span>'+text1[0]+'<br />'+text1[1].replace("KOTA","").replace("KABUPATEN","")+'</span>');
				return $returnString;
			},
			templateSelection : function(repo){ 
				var text1 = repo.text.split("#");
				var $returnString = $('<span>'+text1[0]+' '+(text1[1]?text1[1].replace("KOTA","").replace("KABUPATEN",""):'')+'</span>');
				return $returnString;
			},
		});
		
		$(".table-detail").on("change", "select#cb_transportir, select#wiloa_po, select#voloa_po", getOngkosAngkut)
		function getOngkosAngkut(){
			var elmTa = $("select#cb_transportir").val();
			var elmVa = $("select#voloa_po").val();
			var elmOa = $("select#wiloa_po").val();
			if(elmTa != "" && elmVa != "" && elmOa != ""){
				$("#loading_modal").modal();
				$.ajax({
					type	: 'POST',
					url		: "./__get_ongkos_angkut.php",
					data	: {q1:elmTa, q2:elmOa, q3:elmVa},
					cache	: false,
					success : function(data){
						$("input#ongoa_po").val(data);
					}
				});
				$("#loading_modal").modal("hide");
			}
		}
		
		
		$(".hitung").number(true, 0, ".", ",");
		$("#harga_dasar").number(true, 0, ".", ",");
		$("#volume").number(true, 0, ".", ",");
		$("#totnya").number(true, 0, ".", ",");
		$("#ongoa_po").number(true, 0, ".", ",");
		$("select#cabang").select2({placeholder:"Pilih salah satu", allowClear:true });
		
		$("#perhitungan").on("change", function(){
			var nilai = $(this).val();
			$("#loading_modal").modal();
			if(nilai == 1){
				$("#byHarga").removeClass("hide");
				$("#byFormula").addClass("hide");
			} else if(nilai == 2){
				$("#byHarga").addClass("hide");
				$("#byFormula").removeClass("hide");
			} else{
				$("#byHarga").addClass("hide");
				$("#byFormula").addClass("hide");
			}
			$("#loading_modal").modal("hide");
		});

		$(".is_cetak_all").on("ifChecked", function(){
			$(".is_cetak").iCheck('check');
		}).on("ifUnchecked", function(){
			$(".is_cetak").iCheck("uncheck");
		});

		$("#is_rinci").on("ifChecked", function(){
			$("#clcHarga4").val("");				
			$("#rncHarga1, #rncHarga2, #rncHarga3, #rncHarga4").val("").removeAttr("readonly");
			hitungTotal();
		}).on("ifUnchecked", function(){
			var tbl = $(".tblHarga");
			var row = tbl.find('tbody > tr').length;
			while(row > 4){
				tbl.find('tbody > tr:last').remove();
				row--;
			}
			$("#clcHarga4, #rncHarga2, #rncHarga3, #rncHarga4").val("");
			$("#rncHarga1").attr("readonly", "readonly");
			$("#harga_dasar").trigger("keyup");
		});

		$(".tblHarga").on("click", "button.addRow", function(){
			var tabel = $(this).parents(".tblHarga");
			if($("#is_rinci").iCheck('update')[0].checked === true){
				var rwTbl	= tabel.find('tbody > tr:last');
				var rwNom	= parseInt(rwTbl.find("span.noHarga").data('rowCount'));
				var newId 	= parseInt(rwNom + 1);

				var objTr 	= $("<tr>");
				var objTd1 	= $("<td>", {class:"text-center"}).appendTo(objTr);
				var objTd2 	= $("<td>", {class:"text-center"}).appendTo(objTr);
				var objTd3 	= $("<td>", {class:"text-left"}).appendTo(objTr);
				var objTd4 	= $("<td>", {class:"text-right"}).appendTo(objTr);
				var objTd5 	= $("<td>", {class:"text-right"}).appendTo(objTr);
				var objTd6 	= $("<td>", {class:"text-center"}).appendTo(objTr);
				objTd1.html('<input type="checkbox" name="is_cetak['+newId+']" id="is_cetak'+newId+'" value="1" class="is_cetak" />');
				objTd2.html('<span id="noHarga'+newId+'" class="noHarga" data-row-count="'+newId+'"></span>');
				objTd3.html('<input type="text" name="jnsHarga['+newId+']" id="jnsHarga'+newId+'" class="form-control input-sm" />');
				objTd4.html('<div class="input-group"><input type="text" name="clcHarga['+newId+']" id="clcHarga'+newId+'" class="form-control input-sm text-right" /><span class="input-group-addon">%</span></div>');
				objTd5.html('<input type="text" name="rncHarga['+newId+']" id="rncHarga'+newId+'" class="form-control input-sm hitung" />');
				objTd6.html('<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
				rwTbl.after(objTr);
				tabel.find(".noHarga").each(function(i,v){
					$(this).text(i+1);
				});
				$("#rncHarga"+newId).number(true, 0, ".", ",");
				$("#is_cetak"+newId).iCheck({ checkboxClass:'icheckbox_square-blue', radioClass:'iradio_square-blue' });
			}
		});
		$(".tblHarga").on("click", "a.hRow", function(){
			var tabel 	= $(this).parents(".tblHarga");
			var jTbl	= tabel.find("tr").length;
			if(jTbl > 2){
				var cRow = $(this).closest('tr');
				cRow.remove();
				tabel.find(".noHarga").each(function(i,v){
					$(this).text(i+1);
				});
				hitungTotal();
			}
		});
		$(".tblHarga").on("keyup", ".hitung", hitungTotal);

		function hitungTotal(){
			var pendapatan = 0;
			$(".tblHarga").find(".hitung").each(function(index1, element1){
				pendapatan += parseInt($(element1).val().replace(/[.][,]+/g, "")*1); 
			});
			$("#totnya").val(pendapatan);
		}

		$(".tblFormula").on("click", "button.addRow", function(){
			var tabel 	= $(this).parents(".tblFormula");
			var rwTbl	= tabel.find('tbody > tr:last');
			var rwNom	= parseInt(rwTbl.find("span.noFormula").data('rowCount'));
			var newId 	= parseInt(rwNom + 1);
			
			var objTr 	= $("<tr>");
			var objTd1 	= $("<td>", {class:"text-center"}).appendTo(objTr);
			var objTd2 	= $("<td>", {class:"text-left"}).appendTo(objTr);
			var objTd3 	= $("<td>", {class:"text-center"}).appendTo(objTr);
			objTd1.html('<span id="noFormula'+newId+'" class="noFormula" data-row-count="'+newId+'"></span>');
			objTd2.html('<input type="text" name="jnsfor[]" id="jnsfor'+newId+'" class="form-control input-sm" />');
			objTd3.html('<button class="btn btn-action btn-primary addRow" type="button"><i class="fa fa-plus"></i></button> ');
			objTd3.append('<a class="btn btn-danger btn-action hRow"><i class="fa fa-times"></i></a>');
			rwTbl.after(objTr);
			tabel.find(".noFormula").each(function(i,v){
				$(this).text(i+1);
			});
		});
		$(".tblFormula").on("click", "a.hRow", function(){
			var tabel 	= $(this).parents(".tblFormula");
			var jTbl	= tabel.find("tr").length;
			if(jTbl > 2){
				var cRow = $(this).closest('tr');
				cRow.remove();
				tabel.find(".noFormula").each(function(i,v){
					$(this).text(i+1);
				});
			}
		});
		
		$("#gform").on("keyup blur", ".ncoa, .ncpkb, #harga_dasar", function(){
			if($("#is_rinci").iCheck('update')[0].checked === false){
				var t1, t2, t3,
				hdasar = $("#rncHarga1"),
				angkut = $("#rncHarga2").val() * 1,
				ppnPsn = $("#clcHarga3").val() * 1,
				pkbPsn = $("#clcHarga4").val() * 1;
				angkutPPN = angkut * 10/100;
				t1 = ($("#harga_dasar").val() - (angkut + angkutPPN)) / ((pkbPsn / 100) + 1.1);
				t2 = t1 * (pkbPsn/100);
				t3 = (t1 + angkut) * (10/100);
				hdasar.val(t1);
				$("#rncHarga4").val(t2);
				$("#rncHarga3").val(t3);
				hitungTotal();
			}
		});

		$("select#jenis_payment").on("change", function(){
			if($(this).val() != "CREDIT"){
				$("#jwp").addClass("hide");
				$("#jwp2").addClass("hide");
				$("#top").val("");
			} else{ 
				$("#jwp").removeClass("hide");
				$("#jwp2").removeClass("hide");
				$("#top").val("");
			}
		});
		
		$("#masa_awal, #masa_akhir, #pbbkb_tawar").on("change", function(){
			if($("#pbbkb_tawar").val()!=""){
				$("#clcHarga4").val($("#pbbkb_tawar option:selected").text().slice(0,-2));
				$("#gform").find(".ncpkb").trigger("blur");
			}else{
				$("#clcHarga4").val('');
				$("#gform").find(".ncpkb").trigger("blur");
			}
			if($("#masa_awal").val() != "" && $("#masa_akhir").val() != "" && $("#area").val() != "" && $("#pbbkb_tawar").val() != "" && $('#produk_tawar').val() != ''){
				$("#loading_modal").modal();
				getHargaMinyak($("#masa_awal").val(), $("#masa_akhir").val(), $("#area").val(), $("#pbbkb_tawar").val(), $('#produk_tawar').val());
				$("#loading_modal").modal("hide");
			} else{
				$("#pemberitahuan").removeClass("text-red").html('');
			}
		});
		$("select#idr").on("change", function(){
			if($("select#idr").val() != ""){
				$("#loading_modal").modal();
				cekPenawaran($("select#idr").val(), $("#area").val())
				$("#loading_modal").modal("hide");
			} else{
				$("#infoin").html('');
				$("#top").val('').removeAttr("readonly");
				$("#nama_up, #jabatan_up, #alamat_up, #telp_up, #fax_up, #jenis_waktu").val('');
				$("#gelar, #jenis_payment").val('').trigger('change');
				$("#jenis_payment").prop("disabled", false);
			}
		});
		$("#area").on("change", function(){
			$("#loading_modal").modal();
			if($("select#idr").val() != ""){
				cekPenawaran($("select#idr").val(), $("#area").val())
			} else{
				$("#infoin").html('');
				$("#top").val('').removeAttr("readonly");
				$("#nama_up, #jabatan_up, #alamat_up, #telp_up, #fax_up, #jenis_waktu").val('');
				$("#gelar, #jenis_payment").val('').trigger('change');
				$("#jenis_payment").prop("disabled", false);
			}
			if($("#masa_awal").val() != "" && $("#masa_akhir").val() != "" && $("#area").val() != "" && $("#pbbkb_tawar").val() != "" && $('#produk_tawar').val() != ""){
				getHargaMinyak($("#masa_awal").val(), $("#masa_akhir").val(), $("#area").val(), $("#pbbkb_tawar").val(), $('#produk_tawar').val());
			} else{
				$("#pemberitahuan").removeClass("text-red").html('');
			}
			$("#loading_modal").modal("hide");
		});

		$("#produk_tawar").on("change", function(){
			$("#loading_modal").modal();
			if($("select#idr").val() != ""){
				cekPenawaran($("select#idr").val(), $("#area").val())
			} else{
				$("#infoin").html('');
				$("#top").val('').removeAttr("readonly");
				$("#nama_up, #jabatan_up, #alamat_up, #telp_up, #fax_up, #jenis_waktu").val('');
				$("#gelar, #jenis_payment").val('').trigger('change');
				$("#jenis_payment").prop("disabled", false);
			}
			if($("#masa_awal").val() != "" && $("#masa_akhir").val() != "" && $("#area").val() != "" && $("#pbbkb_tawar").val() != "" && $('#produk_tawar').val() != ""){
				getHargaMinyak($("#masa_awal").val(), $("#masa_akhir").val(), $("#area").val(), $("#pbbkb_tawar").val(), $('#produk_tawar').val());
			} else{
				$("#pemberitahuan").removeClass("text-red").html('');
			}
			$("#loading_modal").modal("hide");
		});

		function cekPenawaran(customer, area){
			$.ajax({
				type	: "POST",
				url		: "./__cek_penawaran_customer.php",
				data	: { "q1":customer, "q2":area },
				dataType: "json",
				cache	: false,
				success : function(data){ 
					(data.items ? $("#infoin").html(data.items).iCheck({ radioClass:'iradio_square-blue' }) : $("#infoin").html(data.items));
					if(data.glr)
						$("#gelar").val(data.glr).trigger('change');
					if(data.jenis)
						$("#jenis_payment").val(data.jenis).trigger('change');
					if(data.top)
						$("#top").val(data.top);
					if(data.jenis)
						$("#jenis_waktu").val(data.jenis);
					if(data.nama)
						$("#nama_up").val(data.nama);
					if(data.jbtn)
						$("#jabatan_up").val(data.jbtn);
					if(data.almt)
						$("#alamat_up").val(data.almt);
					if(data.telp)
						$("#telp_up").val(data.telp);
					if(data.fax)
						$("#fax_up").val(data.fax);
					(data.stat > 1 ? $("#top").attr("readonly","readonly") : $("#top").removeAttr("readonly"));
					(data.stat > 1 ? $("#jenis_payment").prop("disabled", true) : $("#jenis_payment").prop("disabled", false));
					if(data.idcb){
						$("#wrCabang").html('<input type="hidden" name="cabang" id="cabang" value="'+data.idcb+'" /><div class="form-control">'+data.nmcb+'</div>');
					} else{
						// $("#wrCabang").html('');
						// $("#wrCabang").html('<select name="cabang" id="cabang" class="form-control validate[required]"><option></option>'+$("#optCabang").html()+'</select>');
						// $("select#cabang").select2({placeholder:"Pilih salah satu", allowClear:true });
					}
					return false;
				}
			});
		}
		function getHargaMinyak(masa_awal, masa_akhir, area, pbbkb, product){
			$.ajax({
				type	: "POST",
				url		: "./__cek_harga_minyak.php",
				data	: { "q1":masa_awal, "q2":masa_akhir, "q3":area, "q4":pbbkb, "q5":product },
				dataType: "json",
				cache	: false,
				success : function(data){ 
					if(data.error == ""){
						$("#pemberitahuan").removeClass("text-red").html('<i class="fa fa-check jarak-kanan"></i><b>'+data.items+'</b>');
						$("#btnSbmt").removeClass("disabled")
					} else{
						$("#pemberitahuan").addClass("text-red").html('<i class="fa fa-exclamation-triangle jarak-kanan"></i><b>'+data.error+'</b>');
						$("#btnSbmt").addClass("disabled")
					}
					return false;
				}
			});
		}

	});		
</script>
</body>
</html>      
