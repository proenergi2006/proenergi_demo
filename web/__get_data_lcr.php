<?php
	$sesRol 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$upload_dir	= $public_base_directory."/files/uploaded_user/files/";
	$upload_url	= BASE_URL."/files/uploaded_user/files/";
	$almtlokasi = $rsm['alamat_survey']." ".str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['nama_kab'])." ".$rsm['nama_prov'];
	$surveyor 	= (json_decode($rsm['nama_surveyor'], true) === NULL)?array(""):json_decode($rsm['nama_surveyor'], true);
	$hasilsurv 	= (json_decode($rsm['hasilsurv'], true) === NULL)?array(""):json_decode($rsm['hasilsurv'], true);
	$kompetitor = (json_decode($rsm['kompetitor'], true) === NULL)?array(""):json_decode($rsm['kompetitor'], true);
	$produkvol 	= (json_decode($rsm['produkvol'], true) === NULL)?array(1):json_decode($rsm['produkvol'], true);
	$picustomer = (json_decode($rsm['picustomer'], true) === NULL)?array(1):json_decode($rsm['picustomer'], true);
	$jamOperasi = (json_decode($rsm['jam_operasional'], true) === NULL)?array(""):json_decode($rsm['jam_operasional'], true);
	$tangki 	= (json_decode($rsm['tangki'], true) === NULL)?array(1):json_decode($rsm['tangki'], true);
	$pendukung 	= (json_decode($rsm['pendukung'], true) === NULL)?array(1):json_decode($rsm['pendukung'], true);
	$kuantitas1 = (json_decode($rsm['quantity_tangki'], true) === NULL)?array(1):json_decode($rsm['quantity_tangki'], true);
	$kualitas1 	= (json_decode($rsm['quality_tangki'], true) === NULL)?array(1):json_decode($rsm['quality_tangki'], true);
	$kapal 		= (json_decode($rsm['kapal'], true) === NULL)?array(1):json_decode($rsm['kapal'], true);
	$jetty 		= (json_decode($rsm['jetty'], true) === NULL)?array(1):json_decode($rsm['jetty'], true);
	$kuantitas2 = (json_decode($rsm['quantity_kapal'], true) === NULL)?array(1):json_decode($rsm['quantity_kapal'], true);
	$kualitas2 	= (json_decode($rsm['quality_kapal'], true) === NULL)?array(1):json_decode($rsm['quality_kapal'], true);

	$file_jalan	= (json_decode($rsm['kondisi_jalan'], true) === NULL)?array():json_decode($rsm['kondisi_jalan'], true);
	$file_kntr 	= (json_decode($rsm['kantor_perusahaan'], true) === NULL)?array():json_decode($rsm['kantor_perusahaan'], true);
	$file_strg 	= (json_decode($rsm['fasilitas_storage'], true) === NULL)?array():json_decode($rsm['fasilitas_storage'], true);
	$file_inlet = (json_decode($rsm['inlet_pipa'], true) === NULL)?array():json_decode($rsm['inlet_pipa'], true);
	$file_ukur 	= (json_decode($rsm['alat_ukur_gambar'], true) === NULL)?array():json_decode($rsm['alat_ukur_gambar'], true);
	$file_media = (json_decode($rsm['media_datar'], true) === NULL)?array():json_decode($rsm['media_datar'], true);
	$file_ket 	= (json_decode($rsm['keterangan_lain'], true) === NULL)?array():json_decode($rsm['keterangan_lain'], true);
?>
<style>
	.info-data, 
	.table-data-pembongkaran td,
	.table-data td{
		font-size: 12px;
		vertical-align: top;
		background-color: #fff;
	}
	.table-data-pembongkaran > tbody > tr > td{
		padding: 8px 3px;
	}
	.table-data-pembongkaran > tbody > tr > td.rowhead{
		background-color:#ccffcc;
		font-weight: bold;
	}
	.info-data{
		padding: 8px;
		margin: 0px;
		background-color: #eee;
		border: 1px solid #ddd;
	}
	.info-gambar{
		margin-bottom: 10px;
	}
	.info-gambar a > img{
		margin: 0px 10px 10px;
	}
	ol.picstyle{
		margin: 0px;
		padding-left: 15px;
	}
	ol.picstyle > li{
		padding-left: 5px;
	}
	#canvas-peta{
		width: 90%;
		margin: 0 5%;
		height: 320px;
		display: inline-block;
	}
	#wrap-canvas-peta{
		width: 90%;
		margin: 0 5%;
		height: 320px;
		display: inline-block;
		overflow: hidden;
		position: relative;	
	}
	#wrap-canvas-peta #canvas-peta{
		position: absolute;
		top: -350px;
		transition: top 1s ease;
		-webkit-transition: top 1s ease;
		-moz-transition: top 1s ease;
		-o-transition: top 1s ease;
	}
	#wrap-canvas-peta.expanded #canvas-peta{
	  top: 0px;
	}
    .bg-light-purple{
        background-color: #56386a;
        color: #f9f9f9 !important;
    }
    .box.box-purple{
        border-top-color: #56386a;

    }
</style>

<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev"><i class="fa fa-angle-left"></i></a>
    <a class="next"><i class="fa fa-angle-right"></i></a>
    <a class="close"><i class="fa fa-times"></i></a>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
		<h3 class="box-title"><b>1. General Information</b></h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%">
                <tr>
                    <td width="200" style="background-color:#ccffcc; font-weight: bold;">Nama Perusahaan</td>
                    <td width=""><?php echo $rsm['nama_customer'];?></td>
                </tr>
                <tr>
                    <td style="background-color:#ccffcc; font-weight: bold;">Kode Pelanggan</td>
                    <td><?php echo $rsm['kode_pelanggan'];?></td>
                </tr>
                <tr>
                    <td style="background-color:#ccffcc; font-weight: bold;">Alamat Site</td>
                    <td><?php echo $almtlokasi;?></td>
                </tr>
                <tr>
                    <td style="background-color:#ccffcc; font-weight: bold;">Telepon</td>
                    <td><?php echo $rsm['telp_survey'];?></td>
                </tr>
                <tr>
                    <td style="background-color:#ccffcc; font-weight: bold;">Wilayah OA</td>
                    <td><?php echo $rsm['wilayah_angkut'];?></td>
                </tr>
                <tr>
                    <td style="background-color:#ccffcc; font-weight: bold;">Fax</td>
                    <td><?php echo $rsm['fax_survey'];?></td>
                </tr>
                <tr>
                    <td style="background-color:#ccffcc; font-weight: bold;">Website</td>
                    <td><?php echo $rsm['website'];?></td>
                </tr>
                <tr>
                    <td style="background-color:#ccffcc; font-weight: bold;">Tanggal Survey</td>
                    <td><?php echo tgl_indo($rsm['tgl_survey']);?></td>
                </tr>
                <tr>
                    <td style="background-color:#ccffcc; font-weight: bold;">Jenis Usaha</td>
                    <td><?php echo $rsm['jenis_usaha'];?></td>
                </tr>
                <tr>
                    <td style="background-color:#ccffcc; font-weight: bold;">Toleransi</td>
                    <td><?php echo $rsm['toleransi'];?></td>
                </tr>
            </table>
        </div>

        <div class="table-responsive">
    		<?php $jumbaris01 = max(count($surveyor), count($picustomer), count($kompetitor)); ?>
            <table class="table table-bordered" width="100%">
                <tr>
                    <td class="text-center" style="background-color:#ccffcc; font-weight:bold;" colspan="2">Surveyor</td>
                    <td class="text-center" style="background-color:#ccffcc; font-weight:bold;" colspan="2">Penanggungjawab / Penerima dilapangan</td>
                    <td class="text-center" style="background-color:#ccffcc; font-weight:bold;">Jabatan</td>
                    <td class="text-center" style="background-color:#ccffcc; font-weight:bold;">No.HP</td>
                    <td class="text-center" style="background-color:#ccffcc; font-weight:bold;" colspan="2">Kompetitor</td>
                </tr>
				<?php 
                    $jumbaris01 = ($jumbaris01 > 0 ? $jumbaris01 : 1);
                    $nombaris01 = 0;
                    for($i=0; $i<$jumbaris01; $i++){
                        $nombaris01++;
                        echo '
                        <tr>
                            <td class="text-center" width="50">'.($surveyor[$i] || $nombaris01 == 1 ? $nombaris01 : '&nbsp;').'</td>
                            <td class="text-left" width="200">'.($surveyor[$i] || $nombaris01 == 1 ? $surveyor[$i] : '&nbsp;').'</td>
                            <td class="text-center" width="50">'.($picustomer[$i] || $nombaris01 == 1 ? $nombaris01 : '&nbsp;').'</td>
                            <td class="text-left" width="">'.($picustomer[$i] || $nombaris01 == 1 ? $picustomer[$i]['nama'] : '&nbsp;').'</td>
                            <td class="text-left" width="200">'.($picustomer[$i] || $nombaris01 == 1 ? $picustomer[$i]['posisi'] : '&nbsp;').'</td>
                            <td class="text-left" width="200">'.($picustomer[$i] || $nombaris01 == 1 ? $picustomer[$i]['telepon'] : '&nbsp;').'</td>
                            <td class="text-center" width="50">'.($kompetitor[$i] || $nombaris01 == 1 ? $nombaris01 : '&nbsp;').'</td>
                            <td class="text-left" width="200">'.($kompetitor[$i] || $nombaris01 == 1 ? $kompetitor[$i] : '&nbsp;').'</td>
                        </tr>';
                    }
                ?>
            </table>
        </div>

        <div class="table-responsive">
            <?php $jumbaris02 = max(count($hasilsurv), count($produkvol)); ?>
            <table class="table table-bordered" width="100%">
                <tr>
                    <td class="text-center " style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;" colspan="2" rowspan="2">Hasil Produksi</td>
                    <td class="text-center " style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;" colspan="2" rowspan="2">Total Produksi / bln</td>
                    <td class="text-center" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;" colspan="3">Jam Operational</td>
                </tr>
                <tr>
                    <td class="text-center" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;">Senin - Jumat</td>
                    <td class="text-center" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;">Sabtu</td>
                    <td class="text-center" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;">Minggu</td>
                </tr>
			   <?php 
                    $jumbaris02 = ($jumbaris02 > 0 ? $jumbaris02 : 1);
                    $nombaris02 = 0;
                    for($i=0; $i<$jumbaris02; $i++){
                        $nombaris02++;
                        echo '
                        <tr>
                            <td class="text-center" width="50">'.($hasilsurv[$i] || $nombaris02 == 1 ? $nombaris02 : '&nbsp;').'</td>
                            <td class="text-left" width="200">'.($hasilsurv[$i] || $nombaris02 == 1 ? $hasilsurv[$i] : '&nbsp;').'</td>
                            <td class="text-center" width="50">'.($produkvol[$i]['volbul'] || $nombaris02 == 1 ? $nombaris02 : '&nbsp;').'</td>
                            <td class="text-left" width="">'.($produkvol[$i]['volbul'] || $nombaris02 == 1 ? $produkvol[$i]['volbul'] : '&nbsp;').'</td>
                            <td class="text-center" width="200">'.($nombaris02 == 1 ? $jamOperasi[0] : '&nbsp;').'</td>
                            <td class="text-center" width="200">'.($nombaris02 == 1 ? $jamOperasi[1] : '&nbsp;').'</td>
                            <td class="text-center" width="200">'.($nombaris02 == 1 ? $jamOperasi[2] : '&nbsp;').'</td>
                        </tr>';
                    }
                ?>
            </table>
        </div>

    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
		<h3 class="box-title"><b>2. Location Information</b></h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%">
                <tr>
                    <td class="text-left" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;" colspan="2">A. Peta</td>
                </tr>
                <tr>
                    <td class="text-left" width="65%" style="min-height:171px;">
						<?php
							echo '
							<input type="hidden" id="latPreview" name="latPreview" value="'.$rsm['latitude_lokasi'].'" />
							<input type="hidden" id="longPreview" name="longPreview" value="'.$rsm['longitude_lokasi'].'" />
							';
							echo '<a class="pull-right btn btn-info" id="switch-maps" style="margin-bottom:10px;">Maps Mode</a>';
							echo '<a class="pull-right btn btn-info hide" id="switch-image" style="margin-bottom:10px;">Image Mode</a>';
							echo '<p style="margin-bottom:25px;"></p>';
							echo '<div id="canvas-peta" class="hide"></div>';
							if($rsm['layout_lokasi'] && file_exists($upload_dir.$rsm['layout_lokasi']))
							echo '<img id="canvas-image" src="'.$upload_url.$rsm['layout_lokasi'].'" style="width:80%; margin:0 10%;" class="img-responsive" />';
                        ?>
                    </td>
                    <td class="text-left" width="">
                        <p><b>Rute :</b></p><?php echo $rsm['rute_lokasi']; ?>
                        <p>&nbsp;</p>
                        <p><b>Catatan :</b></p><?php echo $rsm['note_lokasi']; ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%">
                <tr>
                    <td class="text-left" width="200" style="background-color:#ccffcc; font-weight:bold;">Link Google Maps</td>
                    <td class="text-left" width=""><?php echo $rsm['link_google_maps'];?></td>
                </tr>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%">
                <tr>
                    <td class="text-left" width="200" style="background-color:#ccffcc; font-weight:bold;">Koordinat: Latitude</td>
                    <td class="text-left" width="180"><?php echo $rsm['latitude_lokasi'];?></td>
                    <td class="text-left" width="200" style="background-color:#ccffcc; font-weight:bold;">Longitude</td>
                    <td class="text-left" width="180"><?php echo $rsm['longitude_lokasi'];?></td>
                    <td class="text-left" width="200" style="background-color:#ccffcc; font-weight:bold;">Jarak dari Depot</td>
                    <td class="text-left" width=""><?php echo $rsm['jarak_depot']." KM";?></td>
                </tr>
            </table>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered" width="100%">
                <tr>
                    <td class="text-left" width="200" style="background-color:#ccffcc; font-weight:bold;">Max Kapasitas Truk</td>
                    <td class="text-left" width="180"><?php echo $rsm['max_truk'];?></td>
                    <td class="text-left" width="200" style="background-color:#ccffcc; font-weight:bold;">Biaya Koordinasi</td>
                    <td class="text-left" width="180"><?php echo $rsm['lsm_portal'];?></td>
                    <td class="text-left" width="200" style="background-color:#ccffcc; font-weight:bold;">Min. Vol. Pengiriman</td>
                    <td class="text-left" width=""><?php echo $rsm['min_vol_kirim']." KL";?></td>
                </tr>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%">
                <tr>
                    <td class="text-left" style="background-color:#ccffcc; font-weight:bold; vertical-align:middle;" colspan="2">B. Layout Pabrik / Site / Lokasi Proyek</td>
                </tr>
                <tr>
                    <td class="text-left" width="65%" style="min-height:171px;">
                        <?php
							if($rsm['layout_bongkar'] && file_exists($upload_dir.$rsm['layout_bongkar']))
								echo '<img src="'.$upload_url.$rsm['layout_bongkar'].'" style="width:60%; margin:0 20%;" class="img-responsive" />';
                        ?>
                    </td>
                    <td class="text-left main_padding b2 b3 b4" width="">
                        <p><b><u>Penjelasan Proses Bongkaran :</u></b></p><br /><?php echo $rsm['penjelasan_bongkar']; ?>
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
		<h3 class="box-title"><b>3. Unloading Information</b></h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-bordered table-data-pembongkaran" width="100%">
                <tr>
                    <td width="16%" class="text-left" style="background-color:#f7f7f7;"><b>Tangki</b></td>
                    <td width="14%" class="text-center rowhead" >Tipe</td>
                    <td width="14%" class="text-center rowhead" >Kapasitas</td>
                    <td width="14%" class="text-center rowhead" >Jumlah</td>
                    <td width="14%" class="text-center rowhead" >Produk</td>
                    <td width="14%" class="text-center rowhead" >Inlet Pipa</td>
                    <td width="14%" class="text-center rowhead" >Ukuran</td>
                </tr>
                <?php 
                    $row1kol1 = count($tangki);
                    $row1kol2 = count($kapal);
                    $row1kols = max($row1kol1, $row1kol2);
                    for($idt1=0; $idt1<$row1kols; $idt1++){
                ?> 
                <tr>
                    <td class="text-center" style="background-color: #f7f7f7;">&nbsp;</td>
                    <td class="text-center"><?php echo $tangki[$idt1]['tipe'];?></td>
                    <td class="text-center"><?php echo $tangki[$idt1]['kapasitas'];?></td>
                    <td class="text-center"><?php echo $tangki[$idt1]['jumlah'];?></td>
                    <td class="text-center"><?php echo $tangki[$idt1]['produk'];?></td>
                    <td class="text-center"><?php echo $tangki[$idt1]['inlet'];?></td>
                    <td class="text-center"><?php echo $tangki[$idt1]['ukuran'];?></td>
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
                </tr>
                <?php } ?>
                <tr>
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
                </tr>
                <?php } ?>
                <tr>
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
                </tr>
                <?php } ?>
                <tr>
                    <td class="text-left"><div style="min-height:100px;"><b>Catatan</b></div></td>
                    <td class="text-left" colspan="6"><?php echo $rsm['catatan_tangki'];?></td>
                </tr>
            </table>
        </div>

         <div class="table-responsive">
            <table class="table table-bordered table-data-pembongkaran" width="100%">
                <tr>
                    <td width="16%" class="text-left" style="background-color:#f7f7f7;"><b>Kapal</b></td>
                    <td width="14%" class="text-center rowhead" >Tipe</td>
                    <td width="14%" class="text-center rowhead" >Kapasitas</td>
                    <td width="14%" class="text-center rowhead" >Jumlah</td>
                    <td width="14%" class="text-center rowhead" >Inlet Pipa</td>
                    <td width="14%" class="text-center rowhead" >Ukuran</td>
                    <td width="14%" class="text-center rowhead" >Metode</td>
                </tr>
                <?php 
                    $row1kol1 = count($tangki);
                    $row1kol2 = count($kapal);
                    $row1kols = max($row1kol1, $row1kol2);
                    for($idt1=0; $idt1<$row1kols; $idt1++){
                ?> 
                <tr>
                    <td class="text-center" style="background-color:#f7f7f7;">&nbsp;</td>
                    <td class="text-center"><?php echo $kapal[$idt1]['tipe'];?></td>
                    <td class="text-center"><?php echo $kapal[$idt1]['kapasitas'];?></td>
                    <td class="text-center"><?php echo $kapal[$idt1]['jumlah'];?></td>
                    <td class="text-center"><?php echo $kapal[$idt1]['inlet'];?></td>
                    <td class="text-center"><?php echo $kapal[$idt1]['ukuran'];?></td>
                    <td class="text-center"><?php echo $kapal[$idt1]['metode'];?></td>
                </tr>
                <?php } ?>
                <tr>
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
                </tr>
                <?php 
                    $row3kol1 = count($kuantitas1);
                    $row3kol2 = count($kuantitas2);
                    $row3kols = max($row3kol1, $row3kol2);
                    for($idt3=0; $idt3<$row3kols; $idt3++){
                ?> 
                <tr>
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
                </tr>
                <?php 
                    $row4kol1 = count($kualitas1);
                    $row4kol2 = count($kualitas2);
                    $row4kols = max($row4kol1, $row4kol2);
                    for($idt4=0; $idt4<$row4kols; $idt4++){
                ?> 
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center"><?php echo $kualitas2[$idt4]['spec'];?></td>
                    <td class="text-center"><?php echo $kualitas2[$idt4]['lab'];?></td>
                    <td class="text-center" colspan="3"><?php echo $kualitas2[$idt4]['coq'];?></td>
                    <td class="text-center">&nbsp;</td>
                </tr>
                <?php } ?>
                <tr>
                    <td class="text-left"><div style="min-height:100px;"><b>Catatan</b></div></td>
                    <td class="text-left" colspan="6"><?php echo $rsm['catatan_kapal'];?></td>
                </tr>
            </table>
        </div>
    </div>
</div>


<div class="box box-primary">
    <div class="box-header with-border">
		<h3 class="box-title"><b>4. Informasi Gambar</b></h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%">
                <tr>
                    <td class="text-left" width="50%">
                        <?php 
                            echo '1. Kondisi Jalan Menuju Lokasi<br /><br />';
							echo '<div class="info-gambar">';
							foreach($file_jalan as $file7){
								$ori = $upload_url.$file7['filename'];
								$tmb = $upload_url."/thumbnail/".$file7['filename'];
								if($file7['filename'] && file_exists($upload_dir.$file7['filename']) && file_exists($upload_dir."/thumbnail/".$file7['filename'])){
									echo '<a href="'.$ori.'" title="'.$file7['deskripsi'].'" data-gallery=""><img src="'.$tmb.'" /></a>';
									echo $file7['deskripsi'];
								}
							}
							echo '</div>';
                        ?>
                    </td>
                    <td class="text-left" width="50%">
                        <?php 
                            echo '2. Pintu Gerbang &amp; Kantor Perusahaan<br /><br />';
							echo '<div class="info-gambar">';
							foreach($file_kntr as $file1){
								$ori = $upload_url.$file1['filename'];
								$tmb = $upload_url."/thumbnail/".$file1['filename'];
								if($file1['filename'] && file_exists($upload_dir.$file1['filename']) && file_exists($upload_dir."/thumbnail/".$file1['filename'])){
									echo '<a href="'.$ori.'" title="'.$file1['deskripsi'].'" data-gallery=""><img src="'.$tmb.'" /></a>';
									echo $file1['deskripsi'];
								}
							}
							echo '</div>';
                        ?>
                    </td>
                </tr>
            </table>
		</div>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%">
                <tr>
                    <td class="text-left" width="50%">
                        <?php 
                            echo '3. Fasilitas Penyimpanan<br /><br />';
							echo '<div class="info-gambar">';
							foreach($file_strg as $file2){
								$ori = $upload_url.$file2['filename'];
								$tmb = $upload_url."/thumbnail/".$file2['filename'];
								if($file2['filename'] && file_exists($upload_dir.$file2['filename']) && file_exists($upload_dir."/thumbnail/".$file2['filename'])){
									echo '<a href="'.$ori.'" title="'.$file2['deskripsi'].'" data-gallery=""><img src="'.$tmb.'" /></a>';
									echo $file2['deskripsi'];
								}
							}
							echo '</div>';
                        ?>
                    </td>
                    <td class="text-left" width="50%">
                        <?php 
                            echo '4. Inlet Pipa<br /><br />';
							echo '<div class="info-gambar">';
							foreach($file_inlet as $file3){
								$ori = $upload_url.$file3['filename'];
								$tmb = $upload_url."/thumbnail/".$file3['filename'];
								if($file3['filename'] && file_exists($upload_dir.$file3['filename']) && file_exists($upload_dir."/thumbnail/".$file3['filename'])){
									echo '<a href="'.$ori.'" title="'.$file3['deskripsi'].'" data-gallery=""><img src="'.$tmb.'" /></a>';
									echo $file3['deskripsi'];
								}
							}
							echo '</div>';
                        ?>
                    </td>
                </tr>
            </table>
		</div>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%">
                <tr>
                    <td class="text-left" width="50%">
                        <?php 
                            echo '5. Alat Ukur<br /><br />';
							echo '<div class="info-gambar">';
							foreach($file_ukur as $file4){
								$ori = $upload_url.$file4['filename'];
								$tmb = $upload_url."/thumbnail/".$file4['filename'];
								if($file4['filename'] && file_exists($upload_dir.$file4['filename']) && file_exists($upload_dir."/thumbnail/".$file4['filename'])){
									echo '<a href="'.$ori.'" title="'.$file4['deskripsi'].'" data-gallery=""><img src="'.$tmb.'" /></a>';
									echo $file4['deskripsi'];
								}
							}
							echo '</div>';
                        ?>
                    </td>
                    <td class="text-left" width="50%">
                        <?php 
                            echo '6. Media Datar<br /><br />';
							echo '<div class="info-gambar">';
							foreach($file_media as $file5){
								$ori = $upload_url.$file5['filename'];
								$tmb = $upload_url."/thumbnail/".$file5['filename'];
								if($file5['filename'] && file_exists($upload_dir.$file5['filename']) && file_exists($upload_dir."/thumbnail/".$file5['filename'])){
									echo '<a href="'.$ori.'" title="'.$file5['deskripsi'].'" data-gallery=""><img src="'.$tmb.'" /></a>';
									echo $file5['deskripsi'];
								}
							}
							echo '</div>';
                        ?>
                    </td>
                </tr>
            </table>
		</div>

        <div class="table-responsive">
            <table class="table table-bordered" width="100%">
                <tr>
                    <td class="text-left" width="50%">
                        <?php 
                            echo '7. Keterangan Penunjang Lain<br /><br />';
							echo '<div class="info-gambar">';
							foreach($file_ket as $file6){
								$ori = $upload_url.$file6['filename'];
								$tmb = $upload_url."/thumbnail/".$file6['filename'];
								if($file6['filename'] && file_exists($upload_dir.$file6['filename']) && file_exists($upload_dir."/thumbnail/".$file6['filename'])){
									echo '<a href="'.$ori.'" title="'.$file6['deskripsi'].'" data-gallery=""><img src="'.$tmb.'" /></a>';
									echo $file6['deskripsi'];
								}
							}
							echo '</div>';
                        ?>
                    </td>
                    <td class="text-left" width="50%">&nbsp;</td>
                </tr>
            </table>
		</div>

    </div>
</div>

<?php if($sesRol == 9){ ?>
<hr style="margin:15px 0px; border-top: 4px double #ddd;" />

<div style="margin-bottom:0px;">
    <a class="btn btn-default jarak-kanan" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT."/verifikasi-lcr.php"; ?>">
    <i class="fa fa-reply jarak-kanan"></i> Kembali</a>

    <a class="btn btn-primary jarak-kanan" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT."/lcr-add.php?".paramEncrypt("idr=".$idr."&idk=".$idk); ?>">
    <i class="fa fa-edit jarak-kanan"></i> Edit Data</a>

    <a class="btn btn-info jarak-kanan" style="min-width:90px;" href="<?php echo ACTION_CLIENT."/lcr-cetak.php?".paramEncrypt("idr=".$idr."&idk=".$idk); ?>" target="_blank">
    <i class="fa fa-print jarak-kanan"></i> Cetak Data</a>
</div>
<?php } ?>

<?php if($sesRol == 11 || $sesRol == 17 || $sesRol == 18){ ?>
<hr style="margin:15px 0px; border-top: 4px double #ddd;" />

<div style="margin-bottom:0px;">
    <a class="btn btn-default jarak-kanan" style="min-width:90px;" href="<?php echo $hr1; ?>">
    <i class="fa fa-reply jarak-kanan"></i> Kembali</a>

    <a class="btn btn-primary jarak-kanan" style="min-width:90px;" href="<?php echo $hr2; ?>">
    <i class="fa fa-edit jarak-kanan"></i> Edit Data</a>

    <?php if(!$rsm['flag_disposisi'] || $rsm['flag_approval'] == 2 || $rsm['flag_disposisi'] == -1){ ?>
    <a class="btn btn-success izin-pd jarak-kanan" style="min-width:90px;" href="<?php echo $hr4; ?>">
    <i class="fa fa-list jarak-kanan"></i> Verifikasi</a>
    <?php } ?>

    <a class="btn btn-info jarak-kanan" style="min-width:90px;" href="<?php echo $hr3; ?>" target="_blank">
    <i class="fa fa-print jarak-kanan"></i> Cetak Data</a>
</div>
<?php } ?>
