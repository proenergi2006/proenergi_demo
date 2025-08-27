<?php 
	$tmp1 	= isset($rsm['kabupaten_customer'])?strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['kabupaten_customer'])):null;
	$alamat = isset($rsm['propinsi_customer'])?$rsm['alamat_customer']." ".ucwords($tmp1)." ".$rsm['propinsi_customer']:null;

	$arrTipeBisnis 	= array(
		1=>"Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", 
		"Finance & Insurance", "Food & hospitally", "Gaming", "Health Services", 
		"Motor Vehicle", $rsm['tipe_bisnis_lain'], "Natural Resources / Environmental", "Personal Service", "Manufacture"
	);
	$tipebisnis 	= ($arrTipeBisnis[$rsm['tipe_bisnis']] ? $arrTipeBisnis[$rsm['tipe_bisnis']] : '-');
	
	$arrOwnership 	= array(1=>"Affiliation", "National Private", "Foreign Private", "Joint Venture", "BUMN / BUMD", "Foundation", "Personal", $rsm['ownership_lain']);
	$ownership 		= ($arrOwnership[$rsm['ownership']] ? $arrOwnership[$rsm['ownership']] : '-');

	$dt1 	= ($rsm['review1']);
	$dt2 	= ($rsm['review2']);
	$dt3 	= ($rsm['review3']);
	$dt4 	= ($rsm['review4']);
	$dt5 	= ($rsm['review5']);
	$dt6 	= ($rsm['review6']);
	$dt7 	= ($rsm['review7']);
	$dt8 	= ($rsm['review8']);
	$dt9 	= ($rsm['review9']);
	$dt10 	= ($rsm['review10']);
	$dt11 	= ($rsm['review11']);
	$dt12 	= ($rsm['review12']);
	$dt13 	= ($rsm['review13']);
	$dt14 	= ($rsm['review14']);
	$dt15 	= ($rsm['review15']);
	$dt16 	= ($rsm['review16']);

    $jenis_asset 				= ($rsm['jenis_asset']);
    $kelengkapan_dok_tagihan 	= ($rsm['kelengkapan_dok_tagihan']);
    $alur_proses_periksaan 		= ($rsm['alur_proses_periksaan']);
    $jadwal_penerimaan 			= ($rsm['jadwal_penerimaan']);
    $background_bisnis 			= ($rsm['background_bisnis']);
    $lokasi_depo 				= ($rsm['lokasi_depo']);
    $opportunity_bisnis 		= ($rsm['opportunity_bisnis']);
	$summary 					= ($rsm['review_summary']);

?>
<div class="form-horizontal" style="margin:0px 50px;">
	<hr style="margin:20px 0px; border-top:4px double #c5c5c5;" />
    <h2 style="font-size:24px; font-weight:bold; margin:20px 0px; text-align:center;">- CUSTOMER REVIEW FORM -</h2>
	<hr style="margin:20px 0px; border-top:4px double #c5c5c5;" />

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">1. Rincian Customer</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-3">Customer *</label>
                        <div class="col-md-9">
                            <input type="text" id="nama_customer" name="nama_customer" class="form-control" value="<?php echo $rsm['nama_customer']; ?>" readonly />
                        </div>
                    </div>
                </div>
            </div>
    
    
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-3">Company Status</label>
                        <div class="col-md-9">
                            <input type="text" id="tipe_bisnis" name="tipe_bisnis" class="form-control" value="<?php echo $tipebisnis; ?>" readonly />
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-3">Company Type</label>
                        <div class="col-md-9">
                            <input type="text" id="ownership" name="ownership" class="form-control" value="<?php echo $ownership; ?>" readonly />
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-3">Alamat</label>
                        <div class="col-md-9">
                            <textarea id="alamat" name="alamat" class="form-control" readonly><?php echo $alamat; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
    
        </div>
    </div>
    
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">2. Informasi Umum</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">2.1</div> 
                            <div style="margin-left:30px;">
                                Sejak kapan perusahaan itu menjalankan bisnisnya ?
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt2;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">2.2</div> 
                            <div style="margin-left:30px;">
                                Jumlah cabang yang dimiliki <small><i>(Sebutkan lokasi kabupaten / kota saja jika ada)</i></small>
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt7;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">&nbsp;</div> 
                            <div style="margin-left:30px;">
                                Berapa Jumlah Karyawan Saat Ini ?
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt5;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">2.3</div> 
                            <div style="margin-left:30px;">
                                Perusahaan tersebut berbisnis/kerjasama dengan siapa saja ? <small><i>(Vendor / customer)</i></small>
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt10;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">2.4</div> 
                            <div style="margin-left:30px;">
                                Berapa lama rata-rata hari penerimaan pembayaran dari pemberi kerja / hasil transaksi tersebut ?
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt16;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">2.5</div> 
                            <div style="margin-left:30px;">
                                Jenis Assets yang dimiliki oleh perusahaan ? 
                                <small><i>(sebutkan jenis, jumlah, status kepemilikannya dan bukti kepemilikan) milik/sewa/leasing</i></small>
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $jenis_asset;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">2.6</div> 
                            <div style="margin-left:30px;">
                                Kelengkapan dokumen tagihan yang dibutuhkan untuk proses pembayaran
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $kelengkapan_dok_tagihan;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">2.7</div> 
                            <div style="margin-left:30px;">
                                Alur proses pemeriksaan/review kelengkapan dokumen dan rata-rata waktu yang dibutuhkan sampai proses pembayaran di lakukan 
                                <small><i>(gambarkan)</i></small>
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $alur_proses_periksaan;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">2.8</div> 
                            <div style="margin-left:30px;">
                                Apakah customer memiliki jadwal penerimaan invoice & Jadwal Pembayaran tagihan? <small><i>(Jika ada mohon diinformasikan detailnya)</i></small>
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $jadwal_penerimaan;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">2.9</div> 
                            <div style="margin-left:30px;">
                                Siapa yang memiliki Authority terkait pembayaran yang harus dilakukan? <small><i>(Nama, Posisi & No. HP)</i></small>
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt8;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">2.10</div> 
                            <div style="margin-left:30px;">
                                Existing fuel vendor yang melakukan bisnis dengan perusahaan ? <small><i>(Nama, Creditm term)</i></small>
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt11;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">2.11</div> 
                            <div style="margin-left:30px;">
                                Historical/ background bisnis yang pernah dimiliki oleh perusahaan / Group tersebut dengan PT. Pro Energi 
                                <small><i>(Jika ada)</i></small>
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $background_bisnis;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">2.12</div> 
                            <div style="margin-left:30px;">
                                Lokasi depo sumber produk <small><i>(terminal)</i></small>
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $lokasi_depo;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            <div style="float:left; width:30px;">2.13</div> 
                            <div style="margin-left:30px;">
                                Opportunity bussiness apa saja yang bisa dilakukan dengan perusahaan itu
                            </div>
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $opportunity_bisnis;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
        </div>
    </div>
    
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">3. Informasi detail tentang pelanggan yang disesuaikan dengan industrial type (Opportunity vs Risk)</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            Credit Limit Proposed
                        </label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-addon">Rp.</span>
                                <input type="text" class="form-control text-right" value="<?php echo number_format($rsm['credit_limit_diajukan']);?>" readonly />
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            Catatan Marketing/Key Account 
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $summary;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="box box-primary collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title">4. Informasi Data Lama (Optional)</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            Jenis Usaha Customer ?
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt1;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            Siapa Pemilik Perusahaan Tersebut ?
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt3;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            Lokasi Perusahaan Saat ini Milik Sendiri Atau Sewa ?
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt4;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            Apakah Setiap Tahun Ada Salary Adjustment <br />Dan Bonus Bagi Karyawan ?
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt6;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            Track Record Pembayaran Atas Supplier Sebelumnya ?
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt12;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            Alasan Yang Membuat Customer Tersebut Memilih Pro Energi ?
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt13;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            Bank Yang Saat Ini Active Digunakan ?
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt14;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            Apakah Mempunyai Facility Dari Bank Tersebut ?
                        </label>
                        <div class="col-md-8">
                            <div class="form-control" style="height:auto; min-height:90px;">
                                <?php echo $dt15;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <label class="control-label col-md-4">
                            Potensi Volume Dalam 1 Bulan ?
                        </label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control text-right" value="<?php echo number_format($rsm['dt9']);?>" readonly />
                                <span class="input-group-addon">Liter</span>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
    
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">5. Lampiran</h3>
        </div>
        <div class="box-body">
        <?php
            if(count($rsm_file) > 0){
                echo '<div id="tmp_file" style="margin:15px 0px;">';
                $nomornya = 0;
                foreach ($rsm_file as $key=>$rows){
                    $nomornya++;
                    $nom_urut 	= $rows['no_urut'];
                    $linknya 	= ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=RA_".$rsm['id_review']."_".$nom_urut."_&file=".$rows['review_attach_ori']);
    
                    echo '
                    <div class="row wrapper_file_pendukung">
                        <div class="col-md-8 num_file">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <span class="input-group-addon frmnodasar" style="min-width:50px;" data-urut="'.$nomornya.'">'.$nomornya.'</span>
                                        <input type="text" class="form-control" readonly value="'.$rows['review_attach_ori'].'" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" />
                                        <span class="input-group-btn">
                                            <a href="'.$linknya.'" class="btn btn-primary" target="_blank">
                                            &nbsp;<i class="fa fa-download"></i>&nbsp;</a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
                echo '</div>';
            }
        ?>
        </div>
    </div>
</div>
