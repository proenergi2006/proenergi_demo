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
	$sql = "select a.*, b.nama_customer, b.top_payment, b.status_customer, c.fullname, d.nama_cabang, e.jenis_produk, e.merk_dagang, f.id_poc, g.harga_sm, g.harga_om, 
			h.nama_area, i.nilai_pbbkb
			from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join acl_user c on b.id_marketing = c.id_user 
			join pro_master_cabang d on a.id_cabang = d.id_master join pro_master_produk e on a.produk_tawar = e.id_master 
			join pro_master_area h on a.id_area = h.id_master left join pro_po_customer f on a.id_penawaran = f.id_penawaran 
			left join pro_master_harga_minyak g on a.masa_awal = g.periode_awal and a.masa_akhir = g.periode_akhir and a.id_area = g.id_area and a.pbbkb_tawar = g.pajak 
				and g.is_approved = 1
			left join pro_master_pbbkb i on a.pbbkb_tawar = i.id_master
			where a.id_customer = '".$idr."' and a.id_penawaran = '".$idk."'";
	$rsm = $con->getRecord($sql);
	$rincian = json_decode($rsm['detail_rincian'], true);
	$formula = json_decode($rsm['detail_formula'], true);
	$link1	 = BASE_URL_CLIENT.'/penawaran.php';
	$link2	 = BASE_URL_CLIENT.'/penawaran-add.php?'.paramEncrypt('idr='.$idr.'&idk='.$idk);
	$link3	 = ACTION_CLIENT.'/penawaran-izin.php?'.paramEncrypt('idr='.$idr.'&idk='.$idk);
	$link4	 = ACTION_CLIENT.'/penawaran-cetak.php?'.paramEncrypt('idr='.$idr.'&idk='.$idk.'&bhs=ind');
	$link4_2 = ACTION_CLIENT.'/penawaran-cetak.php?'.paramEncrypt('idr='.$idr.'&idk='.$idk.'&bhs=eng');
	$link5	 = BASE_URL_CLIENT.'/penawaran-preview.php?'.paramEncrypt('idr='.$idr.'&idk='.$idk);
	$link5_2 = BASE_URL_CLIENT.'/penawaran-preview-eng.php?'.paramEncrypt('idr='.$idr.'&idk='.$idk);
	$arrPosisi	= array(1=>"BM","BM Cabang","OM","CEO");
	$arrAlasan	= array(1=>"sm_mkt_summary","sm_wil_summary","om_summary","ceo_summary");
	$arrSetuju	= array(1=>"Disetujui","Ditolak");

	if($rsm['flag_approval'] == 0 && $rsm['flag_disposisi'] == 0) {
		$status = "Terdaftar";
    } else if($rsm['flag_approval'] == 0 && $rsm['flag_disposisi']) {
		$status = "Verifikasi ".$arrPosisi[$rsm['flag_disposisi']];
    } else if($rsm['flag_approval'] && $rsm['flag_approval'] == 1) {
		$status = $arrSetuju[$rsm['flag_approval']]." ".$arrPosisi[$rsm['flag_disposisi']]."<br/><i>".date("d/m/Y H:i:s",strtotime($rsm['tgl_approval']))." WIB</i>";
    } else if($rsm['flag_approval'] && $rsm['flag_approval'] == 2){
		$status = $arrSetuju[$rsm['flag_approval']]." ".$arrPosisi[$rsm['flag_disposisi']]."<br/><i>".date("d/m/Y H:i:s",strtotime($rsm['tgl_approval']))." WIB</i><br/><br/><b>Alasan Penolakan</b><br/>".$rsm[$arrAlasan[$rsm['flag_disposisi']]];
        if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == '11')
        {            
            $sql1 = "update pro_penawaran set view = 'Yes' where id_penawaran = '".$idk."'";  
            $con->setQuery($sql1);
            // $con->commit();  
        }
    }
    
    $arrKondInd	= array(0=>"", 1=>"Setelah Invoice diterima", "Setelah pengiriman");
	$arrKondEng = array(1=>"After Invoice Receive", "After Delivery");
	$jenis_net	= $rsm['jenis_net'];
	$arrPayment = array("CREDIT"=>"CREDIT ".$rsm['jangka_waktu']." hari ".$arrKondInd[$jenis_net], "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");

	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == '11'){
		$nama_role = "Marketing";
	} else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == '17'){
		$nama_role = "Key Account Executive";
	} else{
		$nama_role = "";
	}
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
        		<h1>Detil Penawaran</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#penawaran-detil" aria-controls="penawaran-detil" role="tab" data-toggle="tab">Penawaran</a>
                    </li>
                    <li role="presentation" class="">
                        <a href="#history-data-approval" aria-controls="history-data-approval" role="tab" data-toggle="tab">History Approval Penawaran</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="penawaran-detil">
                        <div class="row">                
                            <div class="col-sm-12">
                                <div class="box box-primary">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Data Penawaran</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table class="table no-border">
                                                <tr>
                                                    <td width="180">Company Name</td>
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
                                                    <td>Masa berlaku harga</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo tgl_indo($rsm['masa_awal'])." - ".tgl_indo($rsm["masa_akhir"]);?></td>
                                                </tr>
        										<tr>
                                                    <td>Produk</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['merk_dagang'];?></td>
                                                </tr>
        										<tr>
                                                    <td>PBBKB</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['nilai_pbbkb']." %";?></td>
                                                </tr>
                                                <tr>
                                                    <td>Order Method</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['method_order']." hari sebelum pickup";?></td>
                                                </tr>
        										<tr>
                                                    <td>Toleransi Penyusutan</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['tol_susut']." %";?></td>
                                                </tr>
        										<tr>
                                                    <td>Lokasi Pengiriman</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo $rsm['lok_kirim'];?></td>
                                                </tr>
        										<tr>
                                                    <td>Ongkos Angkut Kirim</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo number_format($rsm['oa_kirim']);?></td>
                                                </tr>
        										<tr>
                                                    <td>Refund</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo ($rsm['refund_tawar'])?number_format($rsm['refund_tawar']):'-'; ?></td>
                                                </tr>
        										<tr>
                                                    <td>Other Cost</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo number_format($rsm['other_cost']);?></td>
                                                </tr>
        										<tr>
                                                    <td>Volume</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo number_format($rsm['volume_tawar'])." Liter";?></td>
                                                </tr>
                                                <?php if($rsm['perhitungan'] == 1){ ?>
                                                <tr>
                                                    <td>Harga perliter</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo number_format($rsm['harga_dasar']); ?></td>
                                                </tr>
                                                <?php } ?>
                                                
                                                <tr>
                                                    <td>Keterangan Harga</td>
                                                    <td class="text-center">:</td>
                                                    <td><?php echo ($rsm['ket_harga'])?$rsm['ket_harga']:'-';?></td>
                                                </tr>
                                            </table>
                                        </div>

        								<?php
                                            $breakdown = false;
                                            foreach($rincian as $temp){
                                                $breakdown = $breakdown || $temp["rinci"];
                                            }
                                            if($breakdown && $rsm['perhitungan'] == 1){
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
                                                                $cetak = $arr1['rinci'];
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
                                                <label>Catatan <?=$nama_role?></label>
                                                <div class="form-control" style="height:auto">
        											<?php echo ($rsm['catatan'])?str_replace('<br />', PHP_EOL, $rsm['catatan']):'&nbsp;'; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <p style="margin:0px 15px 15px;"><b>Status Penawaran</b><br /><?php echo $status; ?></p>
                                        
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="pad bg-gray">
                                                    <a class="btn btn-default jarak-kanan" style="width:80px;" href="<?php echo $link1;?>">Kembali</a>
                                                    <?php 
        												if(!$rsm['id_poc']){ 
        													echo '<a class="btn btn-primary jarak-kanan" style="width:80px;" href="'.$link2.'">Edit</a>';
                                                        	if(!$rsm['flag_disposisi']){
        														echo '<a class="btn btn-success jarak-kanan izin-pd" href="'.$link3.'">Persetujuan</a>';
        													}
        												}
        												if($rsm['flag_approval'] == 1){
        													echo '<a class="btn btn-info jarak-kanan" target="_blank" href="'.$link4.'">Cetak Ind</a>';
        													echo '<a class="btn btn-info jarak-kanan" target="_blank" href="'.$link4_2.'">Cetak Eng</a>';
        												} else{
        													echo '<a class="btn btn-info jarak-kanan" target="_blank" href="'.$link5.'">Preview Ind</a>';
        													echo '<a class="btn btn-info jarak-kanan" target="_blank" href="'.$link5_2.'">Preview Eng</a>';
        												}
        											?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
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
                                        and a.id_penawaran='".$rsm['id_penawaran']."'
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

            <?php } ?>
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
            <?php 
            $con->commit(); 
            $con->close(); 
            ?>
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
		$(".izin-pd").click(function(){
			if(confirm("Apakah anda yakin?")){
				$('#loading_modal').modal({backdrop:"static"});
				return true;
			}else{
				return false;
			}
		});
	});		
</script>
</body>
</html>      
