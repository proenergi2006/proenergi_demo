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

	$idr 	= htmlspecialchars($enk['idr'], ENT_QUOTES);
	list($id1, $id2, $id3, $id4) = explode("#*#", $idr);
	$cek 	= "select a.jenis_produk, a.merk_dagang, b.nama_area from pro_master_produk a, pro_master_area b where a.id_master = '".$id4."' and b.id_master = '".$id3."'";
	$row 	= $con->getRecord($cek);
	
	$uRole 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$link1 	= ($uRole == '21') ? BASE_URL_CLIENT.'/master-approval-harga.php' : BASE_URL_CLIENT.'/master-harga-minyak.php';
	$link2 	= BASE_URL_CLIENT.'/add-master-harga-minyak.php?'.paramEncrypt("idr=".$idr);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatUang","jqueryUI"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Detil Harga Jual</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>

                <p style="margin:0"><b><?php echo $row['jenis_produk'].' - '.$row['merk_dagang']; ?></b></p>
                <p style="margin:0">Cabang <?php echo $row['nama_area']; ?></p>
                <p>Periode <?php echo tgl_indo($id1); ?> - <?php echo tgl_indo($id2); ?></p>

                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="">Harga Dasar/Franco</th>
                                        <th class="text-center" width="150">LOCO</th>
                                        <th class="text-center" width="150">SKP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $cek = "select a.id_master, a.nilai_pbbkb, b.note_jual, b.harga_normal, b.loco, b.skp, b.harga_sm, b.harga_om, b.is_evaluated, 
                                            b.created_by, b.created_time, b.lastupdate_by, b.lastupdate_time, b.tanggal_persetujuan 
                                            from pro_master_pbbkb a left join pro_master_harga_minyak b on a.id_master = b.pajak and b.periode_awal = '".$id1."' 
                                            and periode_akhir = '".$id2."' and b.id_area = '".$id3."' and b.produk = '".$id4."' 
                                            where a.id_master = 1 order by 1";
                                    $row = $con->getResult($cek);
                                    if(count($row) > 0){ 
                                        $dt1 = ""; $dt2 = ""; $dt3 = ""; $dt4 = ""; $dt5 = "";
                                        foreach($row as $data){
                                            $dt1 = (!$dt1)?$data['created_by']:$dt1;
                                            $dt2 = (!$dt2)?$data['created_time']:$dt2;
                                            $dt3 = (!$dt3)?$data['lastupdate_by']:$dt3;
                                            $dt4 = (!$dt4)?$data['lastupdate_time']:$dt4;
                                            $dt5 = (!$dt5)?$data['tanggal_persetujuan']:$dt5;
                                            $dt6 = (!$dt6)?$data['note_jual']:$dt6;
                                ?>
                                    <tr>
                                        <td class="text-right"><?php echo number_format($data['harga_normal'],0); ?></td>
                                        <td class="text-left"><?php echo $data['loco']; ?></td>
                                        <td class="text-left"><?php echo $data['skp']; ?></td>
                                    </tr>
                                <?php } } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <p class="text-left" style="font-size:14px; font-weight:bold;">Catatan</p>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group form-group-sm">
                            <div class="form-control" style="min-height:90px; height:auto;"><?php echo ($dt6)?$dt6:'&nbsp;';?></div>
                        </div>
                    </div>
                </div>

                <?php 
                    echo '<div style="font-size:12px;">';
                    echo ($dt1)?'<p style="margin-bottom:5px;">- Dibuat oleh '.$dt1.' <i>('.date("d/m/Y H:i:s", strtotime($dt2)).' WIB)</i></p>':'';
                    echo ($dt3)?'<p style="margin-bottom:5px;">- Terakhir diupdate oleh '.$dt3.' <i>('.date("d/m/Y H:i:s", strtotime($dt4)).' WIB)</i></p>':'';
                    echo ($dt5)?'<p>- Disetujui tanggal <i>'.date("d/m/Y H:i:s", strtotime($dt5)).' WIB</i></p>':'';
                    echo '</div>';
                ?>

                <hr style="border-top:4px double #ddd; margin:25px 0 20px;" />

                <div style="margin-bottom:15px;">
                    <a href="<?php echo $link1; ?>" class="btn btn-default jarak-kanan" style="min-width:90px;">
                    <i class="fa fa-reply jarak-kanan"></i> Kembali</a> 
                    <?php if($uRole == '21'){ ?>
                    <a href="<?php echo $link2; ?>" class="btn btn-primary" style="min-width:90px;">
                    <i class="fa fa-pencil-alt jarak-kanan"></i> Edit Data</a> 
                    <?php } ?>
                </div>


            <?php } ?>
			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>
	<script>
		$(document).ready(function(){
			$(".hitung").priceFormat({prefix: '', thousandsSeparator: ','});
		});
	</script>
</body>
</html>      
