<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$idk 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);
	//$idk 	= 33;
	
	$sql = "select a.*, b.nama_prov, c.nama_kab, d.nama_customer, d.kode_pelanggan, e.wilayah_angkut  
			from pro_customer_lcr a 
			join pro_master_provinsi b on a.prov_survey = b.id_prov join pro_master_kabupaten c on a.kab_survey = c.id_kab 
			join pro_customer d on a.id_customer = d.id_customer 
			left join pro_master_wilayah_angkut e on a.id_wil_oa = e.id_master and a.prov_survey = e.id_prov and a.kab_survey = e.id_kab
			where a.id_lcr = '".$idk."'";
	$rlc 		= $conSub->getRecord($sql);
	$tangki 	= (json_decode($rlc['tangki'], true) === NULL)?array(1):json_decode($rlc['tangki'], true);
	$pendukung 	= (json_decode($rlc['pendukung'], true) === NULL)?array(1):json_decode($rlc['pendukung'], true);
	$kuantitas1 = (json_decode($rlc['quantity_tangki'], true) === NULL)?array(1):json_decode($rlc['quantity_tangki'], true);
	$kualitas1 	= (json_decode($rlc['quality_tangki'], true) === NULL)?array(1):json_decode($rlc['quality_tangki'], true);
	$kapal 		= (json_decode($rlc['kapal'], true) === NULL)?array(1):json_decode($rlc['kapal'], true);
	$jetty 		= (json_decode($rlc['jetty'], true) === NULL)?array(1):json_decode($rlc['jetty'], true);
	$kuantitas2 = (json_decode($rlc['quantity_kapal'], true) === NULL)?array(1):json_decode($rlc['quantity_kapal'], true);
	$kualitas2 	= (json_decode($rlc['quality_kapal'], true) === NULL)?array(1):json_decode($rlc['quality_kapal'], true);
	if($rlc['id_lcr']){
?>
<style>
	.table-data > tbody > tr > td, 
	.table-data-pembongkaran > tbody > tr > td{
		font-size: 12px;
		vertical-align: top;
		background-color: #fff;
	}
	.table-data > tbody > tr > td{
		padding: 5px 0px;
	}
	.table-data-pembongkaran > tbody > tr > td{
		padding: 8px 3px;
	}
	.table-data-pembongkaran > tbody > tr > td.rowhead{
		background-color: #00FFFF;
		font-weight: bold;
	}
</style>

<h3 style="margin: 0px 0px 10px; font-weight: bold; font-size: 16px;"><?php echo $rlc['nama_customer'];?></h3>
<div class="table-responsive">
	<table class="table no-border table-data" width="100%">
    	<tr>
        	<td width="100">Biaya Koordinasi</td>
        	<td width="20" class="text-center">:</td>
        	<td><?php echo $rlc['lsm_portal'];?></td>
		</tr>
    	<tr>
        	<td>Max. Truck</td>
        	<td class="text-center">:</td>
        	<td><?php echo $rlc['max_truk'];?></td>
		</tr>
    	<tr>
        	<td>Jarak dari Depot</td>
        	<td class="text-center">:</td>
        	<td><?php echo $rlc['jarak_depot']." KM";?></td>
		</tr>
    </table>
</div>

<div class="table-responsive">
	<table class="table table-bordered table-data-pembongkaran" width="100%">
    	<tr>
        	<td colspan="14" style="background-color:#eee;"><b>Informasi Pembongkaran</b></td>
		</tr>
    	<tr>
        	<td width="7%" class="text-left rowhead"><span style="font-size:14px;"><b>Tangki</b></span></td>
        	<td width="8%" class="text-center rowhead">Tipe</td>
        	<td width="7%" class="text-center rowhead">Kapasitas</td>
        	<td width="6%" class="text-center rowhead">Jumlah</td>
        	<td width="7%" class="text-center rowhead">Produk</td>
        	<td width="8%" class="text-center rowhead">Inlet Pipa</td>
        	<td width="6%" class="text-center rowhead">Ukuran</td>
        	<td width="7%" class="text-left rowhead"><span style="font-size:14px;"><b>Kapal</b></span></td>
        	<td width="8%" class="text-center rowhead">Tipe</td>
        	<td width="6%" class="text-center rowhead">Kapasitas</td>
        	<td width="7%" class="text-center rowhead">Jumlah</td>
        	<td width="9%" class="text-center rowhead">Inlet Pipa</td>
        	<td width="6%" class="text-center rowhead">Ukuran</td>
        	<td width="9%" class="text-center rowhead">Metode</td>
		</tr>
    	<?php 
			$row1kol1 = count($tangki);
			$row1kol2 = count($kapal);
			$row1kols = max($row1kol1, $row1kol2);
			for($idt1=0; $idt1<$row1kols; $idt1++){
		?> 
        <tr>
        	<td class="text-center rowhead">&nbsp;</td>
        	<td class="text-center"><?php echo $tangki[$idt1]['tipe'];?></td>
        	<td class="text-center"><?php echo $tangki[$idt1]['kapasitas'];?></td>
        	<td class="text-center"><?php echo $tangki[$idt1]['jumlah'];?></td>
        	<td class="text-center"><?php echo $tangki[$idt1]['produk'];?></td>
        	<td class="text-center"><?php echo $tangki[$idt1]['inlet'];?></td>
        	<td class="text-center"><?php echo $tangki[$idt1]['ukuran'];?></td>
        	<td class="text-center rowhead">&nbsp;</td>
        	<td class="text-center"><?php echo $kapal[$idt1]['tipe'];?></td>
        	<td class="text-center"><?php echo $kapal[$idt1]['kapasitas'];?></td>
        	<td class="text-center"><?php echo $kapal[$idt1]['jumlah'];?></td>
        	<td class="text-center"><?php echo $kapal[$idt1]['inlet'];?></td>
        	<td class="text-center"><?php echo $kapal[$idt1]['ukuran'];?></td>
        	<td class="text-center"><?php echo $kapal[$idt1]['metode'];?></td>
		</tr>
        <?php } ?>
    	<tr>
        	<td class="text-left rowhead"><b>Pendukung</b></td>
        	<td class="text-center rowhead">Pompa</td>
        	<td class="text-center rowhead">Laju Alir</td>
        	<td class="text-center rowhead">P.Selang</td>
        	<td class="text-center rowhead">Vapour Valve</td>
        	<td class="text-center rowhead">Grounding</td>
        	<td class="text-center rowhead">Sinyal HP</td>
        	<td class="text-left rowhead"><b>Jetty</b></td>
        	<td class="text-center rowhead">Max. LOA</td>
        	<td class="text-center rowhead">Min. PBL</td>
        	<td class="text-center rowhead">Draft (LWS)</td>
        	<td class="text-center rowhead"><span style="font-size:12px;">Kekuatan (DWT)</span></td>
        	<td class="text-center rowhead">Izin</td>
        	<td class="text-center rowhead">Persyaratan</td>
		</tr>
    	<?php 
			$row2kol1 = count($pendukung);
			$row2kol2 = count($jetty);
			$row2kols = max($row2kol1, $row2kol2);
			for($idt2=0; $idt2<$row2kols; $idt2++){
		?> 
        <tr>
        	<td class="text-center">&nbsp;</td>
        	<td class="text-center"><?php echo $pendukung[$idt2]['pompa'];?></td>
        	<td class="text-center"><?php echo $pendukung[$idt2]['aliran'];?></td>
        	<td class="text-center"><?php echo $pendukung[$idt2]['selang'];?></td>
        	<td class="text-center"><?php echo $pendukung[$idt2]['valve'];?></td>
        	<td class="text-center"><?php echo $pendukung[$idt2]['ground'];?></td>
        	<td class="text-center"><?php echo $pendukung[$idt2]['sinyal'];?></td>
        	<td class="text-center">&nbsp;</td>
        	<td class="text-center"><?php echo $jetty[$idt2]['loa'];?></td>
        	<td class="text-center"><?php echo $jetty[$idt2]['pbl'];?></td>
        	<td class="text-center"><?php echo $jetty[$idt2]['lws'];?></td>
        	<td class="text-center"><?php echo $jetty[$idt2]['sandar'];?></td>
        	<td class="text-center"><?php echo $jetty[$idt2]['izin'];?></td>
        	<td class="text-center"><?php echo $jetty[$idt2]['syarat'];?></td>
		</tr>
        <?php } ?>
    	<tr>
        	<td class="text-left rowhead"><b>Quantity</b></td>
        	<td class="text-center rowhead">Alat Ukur</td>
        	<td class="text-center rowhead">Merk</td>
        	<td class="text-center rowhead">Tera</td>
        	<td class="text-center rowhead">Masa Berlaku</td>
        	<td class="text-center rowhead" colspan="2">Flowmeter Tiap Pengiriman</td>
        	<td class="text-left rowhead"><b>Quantity</b></td>
        	<td class="text-center rowhead">Alat Ukur</td>
        	<td class="text-center rowhead">Merk</td>
        	<td class="text-center rowhead">Tera</td>
        	<td class="text-center rowhead">Masa Berlaku</td>
        	<td class="text-center rowhead" colspan="2">Flowmeter Tiap Pengiriman</td>
		</tr>
    	<?php 
			$row3kol1 = count($kuantitas1);
			$row3kol2 = count($kuantitas2);
			$row3kols = max($row3kol1, $row3kol2);
			for($idt3=0; $idt3<$row3kols; $idt3++){
		?> 
        <tr>
        	<td class="text-center">&nbsp;</td>
        	<td class="text-center"><?php echo $kuantitas1[$idt3]['alat'];?></td>
        	<td class="text-center"><?php echo $kuantitas1[$idt3]['merk'];?></td>
        	<td class="text-center"><?php echo $kuantitas1[$idt3]['tera'];?></td>
        	<td class="text-center"><?php echo $kuantitas1[$idt3]['masa'];?></td>
        	<td class="text-center" colspan="2"><?php echo $kuantitas1[$idt3]['flowmeter'];?></td>
        	<td class="text-center">&nbsp;</td>
        	<td class="text-center"><?php echo $kuantitas2[$idt3]['alat'];?></td>
        	<td class="text-center"><?php echo $kuantitas2[$idt3]['merk'];?></td>
        	<td class="text-center"><?php echo $kuantitas2[$idt3]['tera'];?></td>
        	<td class="text-center"><?php echo $kuantitas2[$idt3]['masa'];?></td>
        	<td class="text-center" colspan="2"><?php echo $kuantitas2[$idt3]['flowmeter'];?></td>
		</tr>
        <?php } ?>
    	<tr>
        	<td class="text-left rowhead"><b>Quanlity</b></td>
        	<td class="text-center rowhead">Min. Spec</td>
        	<td class="text-center rowhead">Uji Lab</td>
        	<td class="text-center rowhead" colspan="3">COQ Tiap Pengiriman</td>
        	<td class="text-center rowhead">&nbsp;</td>
        	<td class="text-left rowhead"><b>Quanlity</b></td>
        	<td class="text-center rowhead">Min. Spec</td>
        	<td class="text-center rowhead">Uji Lab</td>
        	<td class="text-center rowhead" colspan="3">COQ Tiap Pengiriman</td>
        	<td class="text-center rowhead">&nbsp;</td>
		</tr>
    	<?php 
			$row4kol1 = count($kualitas1);
			$row4kol2 = count($kualitas2);
			$row4kols = max($row4kol1, $row4kol2);
			for($idt4=0; $idt4<$row4kols; $idt4++){
		?> 
        <tr>
        	<td class="text-center">&nbsp;</td>
        	<td class="text-center"><?php echo $kualitas1[$idt4]['spec'];?></td>
        	<td class="text-center"><?php echo $kualitas1[$idt4]['lab'];?></td>
        	<td class="text-center" colspan="3"><?php echo $kualitas1[$idt4]['coq'];?></td>
        	<td class="text-center">&nbsp;</td>
        	<td class="text-center">&nbsp;</td>
        	<td class="text-center"><?php echo $kualitas2[$idt4]['spec'];?></td>
        	<td class="text-center"><?php echo $kualitas2[$idt4]['lab'];?></td>
        	<td class="text-center" colspan="3"><?php echo $kualitas2[$idt4]['coq'];?></td>
        	<td class="text-center">&nbsp;</td>
		</tr>
        <?php } ?>
        <tr>
        	<td class="text-left"><div style="min-height:100px;"><b>Catatan</b></div></td>
        	<td class="text-left" colspan="6"><?php echo $rlc['catatan_tangki'];?></td>
        	<td class="text-left"><b>Catatan</b></td>
        	<td class="text-left" colspan="6"><?php echo $rlc['catatan_kapal'];?></td>
		</tr>
	</table>
</div>

<?php
	} else echo '<p class="text-center" style="margin-bottom:0px">Data tidak ditemukan</p>';
	$conSub->close();
?>
