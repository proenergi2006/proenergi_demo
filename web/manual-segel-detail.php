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
	$idr = isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	
	$sql = "
		select a.*, b.inisial_segel, b.stok_segel 
		from pro_manual_segel a 
		join pro_master_cabang b on a.id_wilayah = b.id_master 
		where a.id_master = '".$idr."'
	";
	$rsm = $con->getRecord($sql);
	
	$link1 	= BASE_URL_CLIENT.'/manual-segel.php';
	$link2 	= ACTION_CLIENT.'/manual-segel-cetak.php?'.paramEncrypt('idr='.$idr);
	$link3 	= BASE_URL_CLIENT.'/manual-segel-add.php?'.paramEncrypt('idr='.$idr);

	$seg_aw = ($rsm['segel_awal'])?str_pad($rsm['segel_awal'],4,'0',STR_PAD_LEFT):'';
	$seg_ak = ($rsm['segel_akhir'])?str_pad($rsm['segel_akhir'],4,'0',STR_PAD_LEFT):'';

	if($rsm['jumlah_segel'] == 1)
		$nomor_segel = $rsm['inisial_segel']."-".$seg_aw;
	else if($rsm['jumlah_segel'] == 2)
		$nomor_segel = $rsm['inisial_segel']."-".$seg_aw." &amp; ".$rsm['inisial_segel']."-".$seg_ak;
	else if($rsm['jumlah_segel'] > 2)
		$nomor_segel = $rsm['inisial_segel']."-".$seg_aw." s/d ".$rsm['inisial_segel']."-".$seg_ak;
	else $nomor_segel = '';
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
        		<h1>Manual Segel</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ $flash->display(); ?>
                <p class="text-center" style="margin-bottom:0px;"><b>BERITA ACARA</b></p>
                <p class="text-center"><b>Nomor : <?php echo $rsm['nomor_acara'];?></b></p>
                <hr style="margin:5px 0px; border-top: 1px solid #ddd" />
                <?php if($rsm['kategori'] == 1){ ?>
                <div class="row">
                    <div class="col-sm-offset-7 col-sm-5">
                        <div class="table-responsive">
                            <table class="table no-border table-detail">
                                <tr>
                                    <td width="110">No. Segel Terakhir</td>
                                    <td width="10">:</td>
                                    <td><?php echo $rsm['nomor_akhir'];?></td>
                                </tr>
                                <tr>
                                    <td>Stock Segel</td>
                                    <td>:</td>
                                    <td><?php echo $rsm['stok_segel'];?></td>
                                </tr>
                                <tr>
                                    <td>No. Segel Terpakai</td>
                                    <td>:</td>
                                    <td><?php echo $nomor_segel;?></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td><?php echo tgl_indo($rsm['tanggal_segel']);?></td>
                                </tr>
                            </table> 
                        </div>
                    </div>
                </div><hr style="margin:5px 0px 20px; border-top: 1px solid #ddd" />
                <?php } ?>
                
                <div class="form-group row">
                    <div class="col-sm-12">
                        <div class="form-control" style="height:auto; min-height:270px;"><?php echo $rsm['keperluan'];?></div>
                    </div>
                </div>

                <hr style="border-top:4px double #ddd; margin:5px 0 20px;" />

                <div style="margin-bottom:15px;">
                    <a href="<?php echo $link1; ?>" class="btn btn-default jarak-kanan" style="min-width:90px;">
                    <i class="fa fa-reply jarak-kanan"></i> Kembali</a> 
                    <a href="<?php echo $link3; ?>" class="btn btn-info jarak-kanan" style="min-width:90px;">
                    <i class="fa fa-edit jarak-kanan"></i> Ubah Data</a> 
                    <a href="<?php echo $link2; ?>" class="btn btn-success jarak-kanan" style="min-width:90px;">
                    <i class="fa fa-print jarak-kanan"></i> Cetak Data</a> 
                </div>

			<?php } $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style>
.table-detail{ 
	margin-bottom:0px;
}
.table-detail > tbody > tr > td { 
	padding:3px; 
	font-size: 12px;
}
</style>
</body>
</html>      
