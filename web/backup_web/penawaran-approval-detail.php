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
    $date_min_2_months = date('Y-m-d', strtotime("-2 months"));
    // $sql = "select a.*, b.nama_customer, b.top_payment, b.status_customer, c.fullname, d.nama_cabang, e.jenis_produk, e.merk_dagang, f.harga_normal, f.harga_sm, f.harga_om, g.nama_area from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join acl_user c on b.id_marketing = c.id_user join pro_master_cabang d on a.id_cabang = d.id_master join pro_master_produk e on a.produk_tawar = e.id_master join pro_master_area g on a.id_area = g.id_master left join pro_master_harga_minyak f on a.masa_awal = f.periode_awal and a.masa_akhir = f.periode_akhir and a.id_area = f.id_area and a.pbbkb_tawar = f.pajak and f.is_approved = 1 where a.id_customer = '".$idr."' and a.id_penawaran = '".$idk."'";

	$sql = "select a.*, b.nama_customer, b.top_payment, b.status_customer, c.fullname, d.nama_cabang, e.jenis_produk, e.merk_dagang, f.harga_normal, f.harga_sm, f.harga_om, g.nama_area from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join acl_user c on b.id_marketing = c.id_user join pro_master_cabang d on a.id_cabang = d.id_master join pro_master_produk e on a.produk_tawar = e.id_master join pro_master_area g on a.id_area = g.id_master left join pro_master_harga_minyak f on a.masa_awal = f.periode_awal and a.masa_akhir = f.periode_akhir and a.id_area = f.id_area and a.pbbkb_tawar = f.pajak and f.is_approved = 1 where 1 = 1 and a.id_customer = '".$idr."' order by a.created_time desc";
    // -- and a.id_penawaran = '".$idk."' recent query
    // a.id_customer = '".$idr."' and a.created_time > '".$date_min_2_months."' // old
    // $get_penawaran = $con->getRecord($query);
    $rsms = $con->getResult($sql);
    // echo $sql; die();


    
    /*
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
	
	$arrKondInd	= array(1=>"Setelah Invoice diterima", "Setelah pengiriman");
	$arrKondEng = array(1=>"After Invoice Receive", "After Delivery");
	$jenis_net	= $rsm['jenis_net'];
	$arrPayment = array("CREDIT"=>"CREDIT ".$rsm['jangka_waktu']." hari ".$arrKondInd[$jenis_net], "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");
    */

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
									<form action="<?php echo ACTION_CLIENT.'/penawaran-approval.php'; ?>" id="gform" name="gform" class="form-validasi" method="post" role="form">
                                        <?php
                                            $simpan  = false;
                                            foreach ($rsms as $key => $rsm) 
                                            {
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
                                                
                                                
                                                $arrKondInd = array(1=>"Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
                                                $arrKondEng = array(1=>"After Invoice Receive", "After Delivery", "After Loading");
                                                $jenis_net  = $rsm['jenis_net'];
                                                $arrPayment = array("CREDIT"=>"CREDIT ".$rsm['jangka_waktu']." hari ".$arrKondInd[$jenis_net], "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");
                                                
                                            // if ($key == count($rsms)-1) {
                                            if ($rsm['id_penawaran'] == $idk) {
                                            // if ($key == 0) {
                                        ?>
                                                <div class="form-group row">
                                                    <div class="col-sm-8">
                                                        <div class="table-responsive">
                                                            <input type="hidden" name="keterangan_pengajuan" id="keterangan_pengajuan" value="<?php echo $rsm['catatan'];?>" />
                                                            <input type="hidden" name="volume" id="volume" value="<?php echo $rsm['volume_tawar'];?>" />

                                                            <table class="table table-bordered table-summary">
                                                                <thead>
                                                                    <tr>
                                                                        <th colspan="2">SUMMARY</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td width="150">Nama Customer</td>
                                                                        <td><?php echo $rsm['nama_customer'];?></td>
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
                                                                        <td>Ongkos Angkut</td>
                                                                        <td><?php echo number_format($rsm['oa_kirim']);?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Other Cost</td>
                                                                        <td><?php echo number_format($rsm['other_cost']);?></td>
                                                                    </tr>

                                                                    <?php
                                                                    $cnt_rincian=0;
                                                                    foreach($rincian as $arr1){
                                                                        $cnt_rincian++;
                                                                        $biaya = ($arr1['biaya'])?$arr1['biaya']:'';

                                                                        if ($cnt_rincian
                                                                         == 1) {
                                                                        ?>
                                                                            <input type="hidden" name="harga_dasar" id="harga_dasar" value="<?php echo $arr1['biaya'];?>" />
                                                                        <?php    
                                                                        }else if ($cnt_rincian
                                                                         == 2){
                                                                        ?>
                                                                            <input type="hidden" name="oa_kirim" id="oa_kirim" value="<?php echo $arr1['biaya'];?>" />
                                                                        <?php  
                                                                        }else if ($cnt_rincian
                                                                         == 3){
                                                                        ?>
                                                                            <input type="hidden" name="ppn" id="ppn" value="<?php echo $arr1['biaya'];?>" />
                                                                        <?php     
                                                                        }else if ($cnt_rincian
                                                                         == 4){
                                                                        ?>
                                                                            <input type="hidden" name="pbbkb" id="pbbkb" value="<?php echo $arr1['biaya'];?>" />
                                                                        <?php  
                                                                        } 
                                                                    } ?>


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
                                                                    <tr>
                                                                        <td>Pricelist</td>
                                                                        <td><?php echo number_format($rsm['harga_normal']); ?></td>
                                                                    </tr>
                                                                    <?php if($rsm['id_cabang']){ ?>
                                                                    <tr>
                                                                        <td>Harga BM</td>
                                                                        <td><?php echo number_format($rsm['harga_sm']); ?></td>
                                                                    </tr>
                                                                    <?php } if(($sesrole == 6 || $sesrole == 3)){ ?>
                                                                    <tr>
                                                                        <td>Harga OM</td>
                                                                        <td><?php echo number_format($rsm['harga_om']); ?></td>
                                                                    </tr>
                                                                    <?php } ?>
                                                                    
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php // echo "(".$sesrole ."== 7 && ". $rsm['id_cabang'] ."!=". $seswil." && ".!$rsm['sm_mkt_result'].")"; ?>
                                                <?php if($sesrole == 7 && $rsm['id_cabang'] != $seswil && !$rsm['sm_mkt_result']){ $simpan = true; ?>
                                                <?php //if($sesrole == 7 && $rsm['id_cabang'] == $seswil && !$rsm['sm_mkt_result']){ $simpan = true; ?>
                                                    <div class="form-group row">
                                                        <div class="col-sm-8">
                                                            <label>Catatan Branch Manager Marketing</label>
                                                            <textarea name="sm_mkt_summary" id="sm_mkt_summary" class="form-control"></textarea>
                                                            <input type="hidden" name="approval" id="approval" value="1" />
                                                            <input type="hidden" name="is_mkt" id="is_mkt" value="1" />
                                                            <input type="hidden" name="tmp_cabang" id="tmp_cabang" value="<?php echo $rsm['id_cabang']; ?>" />
                                                        </div>
                                                    </div>
                                                <?php } else if($sesrole == 7 && $rsm['sm_mkt_result']){ ?>
                                                    <div class="form-group row">
                                                        <div class="col-sm-8">
                                                            <label>Catatan Branch Manager Marketing</label>
                                                            <div class="form-control" style="height:auto">
                                                                <?php echo $notesm1; ?>
                                                                <p style="margin:10px 0 0; font-size:12px;"><i>
                                                                    <?php echo $rsm['sm_mkt_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['sm_mkt_tanggal']))." WIB";?>
                                                                </i></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php //echo "(".$sesrole ."== 7 && ". $rsm['id_cabang'] ."==". $seswil." && ".!$rsm['sm_wil_result'].")"; ?>
                                                <?php if($sesrole == 7 && $rsm['id_cabang'] == $seswil && !$rsm['sm_wil_result']){ $simpan = true; ?>
                                                    <div class="form-group row">
                                                        <div class="col-sm-8">
                                                            <label>Catatan Branch Manager Cabang</label>
                                                            <textarea name="sm_wil_summary" id="sm_wil_summary" class="form-control"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-sm-6">
                                                            <label>Persetujuan</label>
                                                            <div class="radio">
                                                                <label class="rtl"><input type="radio" name="approval" id="approval1" class="validate[required]" value="1" /></label> Ya
                                                            </div>
                                                            <div class="radio">
                                                                <label class="rtl"><input type="radio" name="approval" id="approval2" class="validate[required]" value="2" /></label> Tidak
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-sm-top">
                                                            <label>Diteruskan ke OM</label>
                                                            <div class="radio">
                                                                <label class="rtl"><input type="radio" name="extend" id="extend1" class="validate[required]" value="1" <?php echo ($rsm['harga_dasar'] < $rsm['harga_sm']?' checked':'');?> /></label> Ya
                                                            </div>
                                                            <div class="radio">
                                                                <label class="rtl"><input type="radio" name="extend" id="extend2" class="validate[required]" value="2" <?php echo ($rsm['harga_dasar'] > $rsm['harga_sm']?' checked':'');?> /></label> Tidak
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } else if($sesrole == 7 && $rsm['id_cabang'] != 4 && $rsm['sm_wil_result']){ ?>
                                                    <div class="form-group row">
                                                        <div class="col-sm-8">
                                                            <label>Catatan Branch Manager Cabang</label>
                                                            <div class="form-control" style="height:auto">
                                                                <?php echo $notesm2; ?>
                                                                <p style="margin:10px 0 0; font-size:12px;"><i>
                                                                    <?php echo $rsm['sm_wil_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['sm_wil_tanggal']))." WIB";?>
                                                                </i></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php if($rsm['om_result']){ ?>
                                                        <hr style="margin:0 0 15px;"><div class="form-group row">
                                                            <div class="col-sm-8">
                                                                <label>Catatan Operation Manager</label>
                                                                <div class="form-control" style="height:auto">
                                                                    <?php echo $noteopm; ?>
                                                                    <p style="margin:10px 0 0; font-size:12px;"><i>
                                                                        <?php echo $rsm['om_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['om_tanggal']))." WIB";?>
                                                                    </i></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } if($rsm['ceo_result']){ ?>
                                                        <hr style="margin:0 0 15px;"><div class="form-group row">
                                                            <div class="col-sm-8">
                                                                <label>Catatan CEO</label>
                                                                <div class="form-control" style="height:auto">
                                                                    <?php echo $noteceo; ?>
                                                                    <p style="margin:10px 0 0; font-size:12px;"><i>
                                                                        <?php echo $rsm['ceo_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['ceo_tanggal']))." WIB";?>
                                                                    </i></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                                
                                                <?php /*BUAT APPROVAL OM*/ if($sesrole == 6){ ?> 
                                                    <?php if($rsm['sm_mkt_result']){ ?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-8">
                                                                <label>Catatan Branch Manager Marketing</label>
                                                                <div class="form-control" style="height:auto">
                                                                    <?php echo $notesm1; ?>
                                                                    <p style="margin:10px 0 0; font-size:12px;"><i>
                                                                        <?php echo $rsm['sm_mkt_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['sm_mkt_tanggal']))." WIB";?>
                                                                    </i></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr style="margin:0 0 15px;">
                                                    <?php } ?> 
                                                    
                                                    <?php if($rsm['sm_wil_result']){ ?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-8">
                                                                <label>Catatan Branch Manager Cabang</label>
                                                                <div class="form-control" style="height:auto">
                                                                    <?php echo $notesm2; ?>
                                                                    <p style="margin:10px 0 0; font-size:12px;"><i>
                                                                        <?php echo $rsm['sm_wil_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['sm_wil_tanggal']))." WIB";?>
                                                                    </i></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr style="margin:0 0 15px;">
                                                    <?php } ?>

                                                    <?php if(!$rsm['om_result'] && $rsm['flag_approval'] == 0){ $simpan = true; ?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-8">
                                                                <label>Catatan Operation Manager</label>
                                                                <textarea name="om_summary" id="om_summary" class="form-control"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-sm-6">
                                                                <label>Persetujuan</label>
                                                                <div class="radio">
                                                                    <label class="rtl"><input type="radio" name="approval" id="approval1" class="validate[required]" value="1" /></label> Ya
                                                                </div>
                                                                <div class="radio">
                                                                    <label class="rtl"><input type="radio" name="approval" id="approval2" class="validate[required]" value="2" /></label> Tidak
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6 col-sm-top">
                                                                <label>Diteruskan ke CEO</label>
                                                                <div class="radio">
                                                                    <label class="rtl"><input type="radio" name="extend" id="extend1" class="validate[required]" value="1" <?php echo ($rsm['harga_dasar'] < $rsm['harga_om']?' checked':'');?> /></label> Ya
                                                                </div>
                                                                <div class="radio">
                                                                    <label class="rtl"><input type="radio" name="extend" id="extend2" class="validate[required]" value="2" <?php echo ($rsm['harga_dasar'] > $rsm['harga_om']?' checked':'');?> /></label> Tidak
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } else if($rsm['om_result']){ ?>

                                                        <div class="form-group row">
                                                            <div class="col-sm-8">
                                                                <label>Catatan Operation Manager</label>
                                                                <div class="form-control" style="height:auto">
                                                                    <?php echo $noteopm; ?>
                                                                    <p style="margin:10px 0 0; font-size:12px;"><i>
                                                                        <?php echo $rsm['om_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['om_tanggal']))." WIB";?>
                                                                    </i></p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php if($rsm['ceo_result']){ ?>
                                                            <hr style="margin:0 0 15px;"><div class="form-group row">
                                                                <div class="col-sm-8">
                                                                    <label>Catatan CEO</label>
                                                                    <div class="form-control" style="height:auto">
                                                                        <?php echo $noteceo; ?>
                                                                        <p style="margin:10px 0 0; font-size:12px;"><i>
                                                                            <?php echo $rsm['ceo_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['ceo_tanggal']))." WIB";?>
                                                                        </i></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php  } ?>

                                                    <?php } ?>  
                                                <?php } ?>

                                                <?php /*BUAT APPROVAL CEO*/ if($sesrole == 3){ ?> 
                                                    <?php if($rsm['sm_mkt_result']){ ?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-8">
                                                                <label>Catatan Branch Manager Marketing</label>
                                                                <div class="form-control" style="height:auto">
                                                                    <?php echo $notesm1; ?>
                                                                    <p style="margin:10px 0 0; font-size:12px;"><i>
                                                                        <?php echo $rsm['sm_mkt_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['sm_mkt_tanggal']))." WIB";?>
                                                                    </i></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr style="margin:0 0 15px;">
                                                    <?php } ?>
                                                
                                                    <?php if($rsm['sm_wil_result']){ ?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-8">
                                                                <label>Catatan Branch Manager Cabang</label>
                                                                <div class="form-control" style="height:auto">
                                                                    <?php echo $notesm2; ?>
                                                                    <p style="margin:10px 0 0; font-size:12px;"><i>
                                                                        <?php echo $rsm['sm_wil_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['sm_wil_tanggal']))." WIB";?>
                                                                    </i></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr style="margin:0 0 15px;">
                                                    <?php } ?>
                                                
                                                    <div class="form-group row">
                                                        <div class="col-sm-8">
                                                            <label>Catatan Operation Manager</label>
                                                            <div class="form-control" style="height:auto">
                                                                <?php echo $noteopm; ?>
                                                                <p style="margin:10px 0 0; font-size:12px;"><i>
                                                                    <?php echo $rsm['om_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['om_tanggal']))." WIB";?>
                                                                </i></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr style="margin:0 0 15px;">
                                                    
                                                    <?php if(!$rsm['ceo_result'] && $rsm['flag_approval'] == 0){ $simpan = true; ?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-8">
                                                                <label>Catatan CEO</label>
                                                                <textarea name="ceo_summary" id="ceo_summary" class="form-control"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-sm-12">
                                                                <label>Persetujuan</label>
                                                                <div class="radio">
                                                                    <label class="rtl"><input type="radio" name="approval" id="approval1" class="validate[required]" value="1" /></label> Ya
                                                                </div>
                                                                <div class="radio">
                                                                    <label class="rtl"><input type="radio" name="approval" id="approval2" class="validate[required]" value="2" /></label> Tidak
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } else if($rsm['ceo_result']){ ?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-8">
                                                                <label>Catatan CEO</label>
                                                                <div class="form-control" style="height:auto">
                                                                    <?php echo $noteceo; ?>
                                                                    <p style="margin:10px 0 0; font-size:12px;"><i>
                                                                        <?php echo $rsm['ceo_pic']." - ".date("d/m/Y H:i:s", strtotime($rsm['ceo_tanggal']))." WIB";?>
                                                                    </i></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?> 
                                                <?php } ?>

                                            <?php } /* END IF */ ?>

                                        <?php } /* END FOREACH */?>
                                        
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="pad bg-gray">
                                                    <input type="hidden" name="act" value="<?php echo $action;?>" />
                                                    <input type="hidden" name="idr" value="<?php echo $idr;?>" />
                                                    <input type="hidden" name="idk" value="<?php echo $idk;?>" />
                                                    <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/penawaran-approval.php";?>">
                                                    <i class="fa fa-reply jarak-kanan"></i>Kembali</a>
                                                    <?php if($simpan === true){ ?>
                                                        <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt">
                                                        <i class="fa fa-floppy-o jarak-kanan"></i>Submit</button>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="data-approval">
                        <div class="row">
                            <?php
                                $_param = 1;
                                foreach ($rsms as $key => $rsm) {
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
                                    
                                    $arrKondInd = array(1=>"Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
                                    $arrKondEng = array(1=>"After Invoice Receive", "After Delivery", "After Loading");
                                    $jenis_net  = $rsm['jenis_net'];
                                    $arrPayment = array("CREDIT"=>"CREDIT ".$rsm['jangka_waktu']." hari ".$arrKondInd[$jenis_net], "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");
                                    if ($rsm['id_penawaran'] <= $idk && $_param <= 3) {
                            ?>
                                    <div class="col-sm-12">
                                        <div class="box box-primary">
                                            <div class="box-body">
                                                <label>- Data Penawaran di tanggal : <?php echo date('d/m/Y', strtotime($rsm['created_time'])); ?></label>
                                                <div class="table-responsive">
                                                    <table class="table no-border">
                                                        <tr>
                                                            <td width="180">Nama Customer <?php echo $_param; ?></td>
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
                                        $_param ++;
                                    }
                                } 
                            ?>
                        </div>
                    </div>



                    <div role="tabpanel" class="tab-pane" id="history-data-approval">
                        <?php
                        $sqlHist = "SELECT count(1) ST_HIST_APPROVAL
                                        FROM
                                            pro_approval_hist a
                                        where   a.kd_approval = 'P001'
                                            and a.id_customer= '".$idr."'
                                            and a.id_penawaran='".$idk."'
                                        order by tgl_approval asc
                                    ";
                        $rsmHist = $con->getRecord($sqlHist);
                        $ctHistApproval = $rsmHist['ST_HIST_APPROVAL'];
                        
                        ?>

                        <?php
                        $sqlHist = "SELECT 
                                        a.kd_approval,
                                    case when a.result ='1'
                                        then 'Disetujui'
                                        when a.result ='2'
                                        then 'Ditolak'
                                        else 
                                        ''
                                    end
                                    result, 
                                    a.summary, a.id_user, DATE_FORMAT(a.tgl_approval, '%d-%m-%Y') tgl_approval,
                                    (select fullname  from acl_user where id_user=a.id_user) fullname,
                                    (select role_name from acl_role where id_role=a.id_role) role_name,
                                    harga_dasar,
                                    oa_kirim,
                                    ppn,
                                    pbbkb,
                                    keterangan_pengajuan,
                                    volume
                                    FROM
                                        pro_approval_hist a
                                    where   a.kd_approval = 'P001'
                                        and a.id_customer= '".$idr."'
                                        and a.id_penawaran='".$idk."'
                                    order by tgl_approval asc
                                ";
                        

                        $resHist     = $con->getResult($sqlHist);
                        ?>
                                             <?php
                                            if ($ctHistApproval == '0') {
                                            ?>
                                                <p style="margin:0px 5px 5px;">Tidak terdapat history approval.</p>
                                            
                                            <?php
                                            }else{
                                            ?>

                                            <p style="margin:0px 5px 5px;"></p>
                                                <div class="clearfix">
                                                    <div class="col-sm-10 col-md-20">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <th class="text-center" width="5%">NO</th>
                                                                    <th class="text-center" width="10%">User Approval</th>
                                                                    <th class="text-center" width="10%">Role</th>
                                                                    <th class="text-center" width="5%">Volume(liter)</th>
                                                                    <th class="text-center" width="10%">Harga Dasar</th>
                                                                    <th class="text-center" width="10%">Ongkos Angkut</th>
                                                                    <th class="text-center" width="10%">PPN</th>
                                                                    <th class="text-center" width="10%">PBBKB</th>
                                                                    <th class="text-center" width="10%">Tgl Approval</th>
                                                                    <th class="text-center" width="10%">Status</th>
                                                                    <th class="text-center" width="10%">Keterangan Pengajuan</th>
                                                                    <th class="text-center" width="10%">Keterangan Approval</th>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                    $nomor=0;
                                                                    foreach($resHist as $arr1){
                                                                        $nomor++;
                                                                ?>
                                                                    <tr>
                                                                        <td class="text-center"><?php echo $nomor;?></td>
                                                                        <td class="text-left"><?php echo $arr1['fullname'];?></td>
                                                                        <td class="text-left"><?php echo $arr1['role_name'];?></td>
                                                                        <td class="text-right"><?php echo number_format($arr1['volume']);?></td>
                                                                        <td class="text-right"><?php echo number_format($arr1['harga_dasar']);?></td>
                                                                        <td class="text-right"><?php echo number_format($arr1['oa_kirim']);?></td>
                                                                        <td class="text-right"><?php echo number_format($arr1['ppn']);?></td>
                                                                        <td class="text-right"><?php echo number_format($arr1['pbbkb']);?></td>
                                                                        <td class="text-center"><?php echo $arr1['tgl_approval'];?></td>
                                                                        <td class="text-left"><?php echo $arr1['result'];?></td>
                                                                        <td class="text-left"><?php echo $arr1['summary'];?></td>
                                                                        <td class="text-left"><?php echo $arr1['keterangan_pengajuan'];?></td>
                                                                    </tr>
                                                                <?php  } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            ?>
                    </div>

				</div>

            <?php } ?>
			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
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
	});		
</script>
</body>
</html>      
