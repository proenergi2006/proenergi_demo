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
	

?>
<form action="<?php echo ACTION_CLIENT.'/customer-review.php'; ?>" id="gform" name="gform" class="form-horizontal" enctype="multipart/form-data" method="post">
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
                        <textarea name="dt2" id="dt2" class="form-control" style="min-height:90px;" required><?php echo $dt2;?></textarea>
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
                        <textarea name="dt7" id="dt7" class="form-control" style="min-height:90px;" required><?php echo $dt7;?></textarea>
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
                        <textarea name="dt5" id="dt5" class="form-control" style="min-height:90px;" required><?php echo $dt5;?></textarea>
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
                        <textarea name="dt10" id="dt10" class="form-control" style="min-height:90px;" required><?php echo $dt10;?></textarea>
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
                        <textarea name="dt16" id="dt16" class="form-control" style="min-height:90px;" required><?php echo $dt16;?></textarea>
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
                        <textarea name="dt17" id="dt17" class="form-control" style="min-height:90px;" required><?php echo $jenis_asset;?></textarea>
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
                        <textarea name="dt18" id="dt18" class="form-control" style="min-height:90px;" required><?php echo $kelengkapan_dok_tagihan;?></textarea>
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
                        <textarea name="dt19" id="dt19" class="form-control" style="min-height:90px;" required><?php echo $alur_proses_periksaan;?></textarea>
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
                        <textarea name="dt20" id="dt20" class="form-control" style="min-height:90px;" required><?php echo $jadwal_penerimaan;?></textarea>
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
                        <textarea name="dt8" id="dt8" class="form-control" style="min-height:90px;" required><?php echo $dt8;?></textarea>
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
                        <textarea name="dt11" id="dt11" class="form-control" style="min-height:90px;" required><?php echo $dt11;?></textarea>
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
                        <textarea name="dt21" id="dt21" class="form-control" style="min-height:90px;" required><?php echo $background_bisnis;?></textarea>
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
                        <textarea name="dt22" id="dt22" class="form-control" style="min-height:90px;" required><?php echo $lokasi_depo;?></textarea>
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
                        <textarea name="dt23" id="dt23" class="form-control" style="min-height:90px;" required><?php echo $opportunity_bisnis;?></textarea>
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
                            <input type="text" name="cl_aju" id="cl_aju" class="form-control text-right" value="<?php echo $cl_aju;?>" />
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
                        <textarea name="summary" id="summary" class="form-control" style="min-height:90px;"><?php echo $summary;?></textarea>
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
                        <textarea name="dt1" id="dt1" class="form-control" style="min-height:90px;"><?php echo $dt1;?></textarea>
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
                        <textarea name="dt3" id="dt3" class="form-control" style="min-height:90px;"><?php echo $dt3;?></textarea>
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
                        <textarea name="dt4" id="dt4" class="form-control" style="min-height:90px;"><?php echo $dt4;?></textarea>
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
                        <textarea name="dt6" id="dt6" class="form-control" style="min-height:90px;"><?php echo $dt6;?></textarea>
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
                        <textarea name="dt12" id="dt12" class="form-control" style="min-height:90px;"><?php echo $dt12;?></textarea>
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
                        <textarea name="dt13" id="dt13" class="form-control" style="min-height:90px;"><?php echo $dt13;?></textarea>
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
                        <textarea name="dt14" id="dt14" class="form-control" style="min-height:90px;"><?php echo $dt14;?></textarea>
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
                        <textarea name="dt15" id="dt15" class="form-control" style="min-height:90px;"><?php echo $dt15;?></textarea>
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
                            <input type="text" name="dt9" id="dt9" class="form-control text-right" value="<?php echo $dt9;?>" />
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
			if(count($rsm_file) < 3){
				echo '
				<button type="button" id="tambah" class="btn btn-sm btn-success" style="min-width:90px;">
				<i class="fa fa-plus jarak-kanan"></i> Tambah File</button>
				<hr style="border-top:4px double #ddd; margin:15px 0px;" />';
			}
			echo '<div id="tmp_file" style="margin:15px 0px;">';


			$nomornya = 0;
			foreach ($rsm_file as $key=>$rows){
				$nomornya++;
				$nom_urut 	= $rows['no_urut'];
				$linknya 	= ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=RA_".$idk."_".$nom_urut."_&file=".$rows['review_attach_ori']);

				echo '
				<div class="row wrapper_file_pendukung">
					<div class="col-md-8 num_file">
						<div class="form-group">
							<div class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon frmnodasar" style="min-width:50px;" data-urut="'.$nomornya.'">'.$nomornya.'</span>
									<input type="hidden" name="review_attach_urut[]" class="review_attach_urut" value="'.$nom_urut.'" />
									<input type="text" class="form-control" readonly value="'.$rows['review_attach_ori'].'" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" />
									<span class="input-group-btn">
										<a href="'.$linknya.'" class="btn btn-primary" target="_blank">
										&nbsp;<i class="fa fa-download"></i>&nbsp;</a>
										<button type="button" class="btn btn-danger ubah_file_pendukung">
										&nbsp;<i class="fa fa-times"></i>&nbsp;</button>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>';
			}
			echo '</div>';

		} else{
			echo '
			<button type="button" id="tambah" class="btn btn-sm btn-success" style="min-width:90px;">
			<i class="fa fa-plus jarak-kanan"></i> Tambah File</button>
			<hr style="border-top:4px double #ddd; margin:15px 0px;" />';

			echo '
			<div id="tmp_file" style="margin:15px 0px;">
				<div class="row wrapper_file_pendukung">
					<div class="col-md-8 num_file">
						<div class="form-group">
							<div class="col-md-12">
								<div class="input-group">
									<span class="input-group-addon frmnodasar" style="min-width:50px;" data-urut="1">1</span>
									<input type="file" id="review_attach_ekstra_1" name="review_attach_ekstra[]" class="form-control" />
									<span class="input-group-btn">
										<button type="button" class="btn btn-danger delete_file_pendukung">
										&nbsp;<i class="fa fa-times"></i>&nbsp;</button>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>';
		}
    ?>
    </div>
</div>

<hr style="margin:15px 0px; border-top: 4px double #ddd;" />

<div style="margin-bottom:0px;">
    <input type="hidden" name="act" value="<?php echo $action;?>" />
    <input type="hidden" name="idr" value="<?php echo $idr;?>" />
    <input type="hidden" name="idk" value="<?php echo $idk;?>" />
    <input type="hidden" name="idn" value="<?php echo $rsm['id_customer'];?>" />
    <input type="hidden" name="idc" value="<?php echo $rsm['id_wilayah'];?>" />
    <button type="submit" name="btnSbmt" id="btnSbmt" class="btn btn-primary jarak-kanan" style="min-width:90px;">
    <i class="fa fa-save jarak-kanan"></i> Simpan</button> 
    <a class="btn btn-default" style="min-width:90px;" href="<?php echo BASE_URL_CLIENT."/customer-review-list.php";?>">
    <i class="fa fa-reply jarak-kanan"></i> Kembali</a>
</div>
</form>

<style>
#tb_vol_terima > thead > tr > th,
#tb_vol_terima > tbody > tr > td{
	font-size: 14px;
}
</style>