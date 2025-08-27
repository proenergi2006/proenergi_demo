<?php
	$base_directory	= $public_base_directory."/files/uploaded_user/images";
	$file_path_sert	= $base_directory."/sert_file".$rsm['id_customer']."_".$rsm['nomor_sertifikat_file'];
	$file_path_npwp	= $base_directory."/npwp_file".$rsm['id_customer']."_".$rsm['nomor_npwp_file'];
	$file_path_siup	= $base_directory."/siup_file".$rsm['id_customer']."_".$rsm['nomor_siup_file'];
	$file_path_tdpn	= $base_directory."/tdp_file".$rsm['id_customer']."_".$rsm['nomor_tdp_file'];

	$extIkon1 	= strtolower(substr($rsm['nomor_sertifikat_file'],strrpos($rsm['nomor_sertifikat_file'],'.')));
	$extIkon2 	= strtolower(substr($rsm['nomor_npwp_file'],strrpos($rsm['nomor_npwp_file'],'.')));
	$extIkon3 	= strtolower(substr($rsm['nomor_siup_file'],strrpos($rsm['nomor_siup_file'],'.')));
	$extIkon4 	= strtolower(substr($rsm['nomor_tdp_file'],strrpos($rsm['nomor_tdp_file'],'.')));
	$arrIkon	= array(".jpg"=>"fa fa-file-image-o jarak-kanan", ".jpeg"=>"fa fa-file-image-o jarak-kanan", ".png"=>"fa fa-file-image-o jarak-kanan", 
						".gif"=>"fa fa-file-image-o jarak-kanan", ".pdf"=>"fa fa-file-pdf-o jarak-kanan", ".zip"=>"fa fa-file-archive-o jarak-kanan");

	$tmp_addr1 			= ucwords(strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['kabupaten_customer'])));
	//$alamat_customer 	= $rsm['alamat_customer']." ".ucwords($tmp_addr1)." ".$rsm['propinsi_customer'];
	$alamat_customer 	= $rsm['alamat_customer'];
	$tmp_addr2 			= ucwords(strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['kabupaten_payment'])));
	//$alamat_payment 	= $rsm['alamat_billing']." ".ucwords($tmp_addr2)." ".$rsm['propinsi_payment'];
	$alamat_payment 	= $rsm['alamat_billing'];

	$arrTipeBisnis 		= array(1=>"Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", "Finance & Insurance", "Food & hospitally", "Gaming", "Health Services", "Motor Vehicle", $rsm['tipe_bisnis_lain'],"Natural Resources / Environmental","Personal Service","Manufacture");
	$arrOwnership 	 	= array(1=>"Affiliation", "National Private", "Foreign Private", "Joint Venture", "BUMN / BUMD", "Foundation", "Personal", $rsm['ownership_lain']);
	$arrPaymentJadwal 	= array(1=>"Every Day", $rsm['payment_schedule_other']);
	$arrPaymentMethod 	= array(1=>"Cash", "Transfer", "Cheque / Giro", "Bank Guarantee", $rsm['payment_method_other']);
	$arrBuktiPotPPN 	= array("_________________", "Bukti Pot. PPn");
	$arrLogistikEnv 	= array(1=>"Industri", "Pemukiman", $rsm['logistik_env_other']);
	$arrLogistikStorage = array(1=>"Indoor", "Outdoor", $rsm['logistik_storage_other']);
	$arrLogistikHour 	= array(1=>"08.00 - 17.00", "24 Hours", $rsm['logistik_hour_other']);
	$arrLogistikVolume 	= array(1=>"Flowmeter", "Stick", $rsm['logistik_volume_other']);
	$arrLogistikQuality = array(1=>"BJ", $rsm['logistik_quality_other']);
	$arrLogistikTruck 	= array(1=>"5 KL", "8 KL", "10 KL", "16 KL", $rsm['logistik_truck_other']);

	$arrTermPayment 	= array("CREDIT"=>"CREDIT", "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");
	$arrConditionInd 	= array(1=>"Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
	$arrConditionEng 	= array(1=>"After Invoice Receive", "After Delivery", "After loading");

    $kecamatan_billing  = $rsm['kecamatan_billing'];
    $kelurahan_billing  = $rsm['kelurahan_billing'];
    $kecamatan_customer = $rsm['kecamatan_customer'];
    $kelurahan_customer = $rsm['kelurahan_customer'];
	$invoice_delivery_addr_primary  	= ($rsm['invoice_delivery_addr_primary'] == "Head Office Address" ? "(1.4) " : "(1.3) ")." ".$rsm['invoice_delivery_addr_primary'];
    $invoice_delivery_addr_secondary 	= '(1.4) '.$rsm['invoice_delivery_addr_secondary'];

    if ($rsm['specify_product']=='1'){
        $specify_product    = 'Prodiesel Bio (Bio Diesel)';
    }else if($rsm['specify_product']=='2'){
        $specify_product    = 'Promarine (MFO)';
    }else if($rsm['specify_product']=='3'){
        $specify_product    = 'Eneos (Lubricant)';
    }else{
        $specify_product    = '';
    }
    
    if($rsm['product_delivery_address']!=''){
        $product_delivery=json_decode($rsm['product_delivery_address'],TRUE);
    }else{
        $product_delivery['product_delivery_address']=[];
    }
?>
<style>
	.preview-file{
		background-color: rgb(244, 244, 244);
		border: 1px solid rgb(221, 221, 221);
		padding: 5px 25px 5px 10px;
		margin-bottom: 0px;
	}
	.kyc_customer_view{
		font-family: arial;
		font-size: 12px;
	}
	.kyc_customer_view .table_display{
		 display: table; 
		 width: 100%;
	}
	.kyc_customer_view .table_row{
		 display: table-row; 
	}
	.kyc_customer_view .table_cell{
		 display: table-cell;
	}
	.kyc_customer_view .main_padding{
		 padding: 5px 15px; 
	}
	.kyc_customer_view .title_section{
		 padding: 10px;  
		 font-size: 14px; 
	}
</style>

<div class="kyc_customer_view" style="margin:0px 50px;">

	<hr style="margin:20px 0px; border-top:4px double #c5c5c5;" />
    <h2 style="font-size:24px; font-weight:bold; margin:20px 0px; text-align:center;">- APPLICATION CUSTOMER FORM -</h2>
	<hr style="margin:20px 0px; border-top:4px double #c5c5c5;" />

    <div style="border:1px solid #343399;">
        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding title_section" style="width:50%; background-color:#56386a; color:#fff;">
                	<b>1. Corporate Details</b>
                </div>
            	<div class="table_cell main_padding title_section" style="width:50%;">
                	<b>* Isi dengan huruf CETAK</b>
                </div>
            </div>
        </div>
    </div>

    <div style="border:1px solid #343399; border-top:0px; margin-bottom:15px;">
        <div>&nbsp;</div>
        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>1.1</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	<b>Full Registered Company Name / Nama lengkap perusahaan yang terdaftar</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['nama_customer'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>1.2</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	<b>Holding / Induk Perusahaan</b> <small><i>(Jika ada)</i></small>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['induk_perusahaan'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>1.3</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	<b>Registered Street Address / Alamat Kantor terdaftar</b> <small><i>(NPWP Address)</i></small>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['alamat_billing'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Sub-Districts / Kelurahan</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $kelurahan_billing;?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Districts / Kecamatan</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $kecamatan_billing;?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>City / Kota (Kabupaten)</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $tmp_addr2;?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Province / Provinsi</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['propinsi_payment'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Country / Negara</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo "Indonesia";?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Post Code / Kode Pos</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['postalcode_billing'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Telephone Number</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['telp_billing'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Fax Number</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['fax_billing'];?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>1.4</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	<b>Address of Head Office / Alamat Kantor Pusat</b> <small><i>(Isi jika alamat tidak sama dengan alamat NPWP)</i></small>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['alamat_customer'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Sub-Districts / Kelurahan</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $kelurahan_customer;?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Districts / Kecamatan</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $kecamatan_customer;?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>City / Kota (Kabupaten)</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $tmp_addr1;?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Province / Provinsi</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['propinsi_customer'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Country / Negara</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo "Indonesia";?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Post Code / Kode Pos</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['postalcode_customer'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Telephone Number</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['telp_customer'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:200px;">
                	<b>Fax Number</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['fax_customer'];?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>1.5</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	<b>Product Delivery Full Address or site address / Alamat Lengkap pengiriman produk atau alamat proyek</b>
                </div>
            	<div class="table_cell main_padding">
                	<div style="padding:5px; border:1px solid #000; background-color:#f0f0f0;">
						<b>Alamat 1 : </b>
					</div>
                	<div style="padding:5px; border:1px solid #000; border-top:0px; min-height:60px;">
						<?php echo ((array_key_exists(0,$product_delivery['product_delivery_address']))?$product_delivery['product_delivery_address'][0]:'&nbsp;')?>
					</div>
                	<div style="padding:5px; border:1px solid #000; border-top:0px; background-color:#f0f0f0;">
						<b>Alamat 2 : </b>
					</div>
                	<div style="padding:5px; border:1px solid #000; border-top:0px; min-height:60px;">
						<?php echo ((array_key_exists(1,$product_delivery['product_delivery_address']))?$product_delivery['product_delivery_address'][1]:'&nbsp;')?>
					</div>
                	<div style="padding:5px; border:1px solid #000; border-top:0px; background-color:#f0f0f0;">
						<b>Alamat 3 : </b>
					</div>
                	<div style="padding:5px; border:1px solid #000; border-top:0px; min-height:60px;">
						<?php echo ((array_key_exists(2,$product_delivery['product_delivery_address']))?$product_delivery['product_delivery_address'][2]:'&nbsp;')?>
					</div>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>1.6</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	<b>Invoice delivery address / Alamat pengiriman Invoice</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $invoice_delivery_addr_primary;?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	<b>PIC Name for received Invoice</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<?php echo $rsm['pic_invoice_name'];?>
                </div>
            	<div class="table_cell main_padding" style="width:180px;">
                	<b>Mobile Number</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_invoice_mobile'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:280px;">
                	<b>Division / Bagian</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<?php echo $rsm['pic_invoice_position'];?>
                </div>
            	<div class="table_cell main_padding" style="width:180px;">
                	<b>E-Mail Address</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_invoice_email'];?>
                </div>
            </div>
        </div>
    </div>

    <div style="border:1px solid #343399;">
        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding title_section" style="width:50%; background-color:#56386a; color:#fff;">
                	<b>File Pendukung</b>
                </div>
            	<div class="table_cell main_padding title_section" style="width:50%;">
                	&nbsp;
                </div>
            </div>
        </div>
    </div>

    <div style="border:1px solid #343399; border-top:0px; margin-bottom:15px;">
        <div>&nbsp;</div>
        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:250px;">
                	<b>CERTIFICATE NUMBER</b><br /> <small><i>(Akta Pendirian)</i></small>
                </div>
            	<div class="table_cell main_padding">
					<?php 
                    	echo '<p>'.($rsm['nomor_sertifikat'] ? $rsm['nomor_sertifikat'] : '&nbsp;').'</p>';
                    	if($rsm['nomor_sertifikat_file'] && file_exists($file_path_sert)){
						
							$link1 = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=sert_file".$rsm['id_customer']."_&file=".$rsm['nomor_sertifikat_file']);
							echo '
							<div class="preview-file">
								<a href="'.$link1.'"><i class="'.$arrIkon[$extIkon1].'"></i>'.str_replace("_"," ",$rsm['nomor_sertifikat_file']).'</a>
							</div>';
                    	}
                    ?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:250px;">
                	<b>NPWP NUMBER</b>
                </div>
            	<div class="table_cell main_padding">
					<?php 
                    	echo '<p>'.$rsm['nomor_npwp'].'</p>';
                    	if($rsm['nomor_npwp_file'] && file_exists($file_path_npwp)){
							$linkNpwp = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=npwp_file".$rsm['id_customer']."_&file=".$rsm['nomor_npwp_file']);
                    		echo '
							<div class="preview-file">
                    			<a href="'.$linkNpwp.'"><i class="'.$arrIkon[$extIkon2].'"></i>'.str_replace("_"," ",$rsm['nomor_npwp_file']).'</a>
							</div>';
                    	}
                    ?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:250px;">
                	<b>SIUP NUMBER</b>
                </div>
            	<div class="table_cell main_padding">
					<?php 
                    	echo '<p>'.$rsm['nomor_siup'].'</p>';
                    	if($rsm['nomor_siup_file'] && file_exists($file_path_siup)){
							$linkSiup = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=siup_file".$rsm['id_customer']."_&file=".$rsm['nomor_siup_file']);
							echo '
							<div class="preview-file">
								<a href="'.$linkSiup.'"><i class="'.$arrIkon[$extIkon3].'"></i>'.str_replace("_"," ",$rsm['nomor_siup_file']).'</a>
							</div>';
                    	}
                    ?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:250px;">
                	<b>TDP NUMBER</b>
                </div>
            	<div class="table_cell main_padding">
					<?php 
                    	echo '<p>'.$rsm['nomor_tdp'].'</p>';
                    	if($rsm['nomor_tdp_file'] && file_exists($file_path_tdpn)){
							$linkTdp = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=tdp_file".$rsm['id_customer']."_&file=".$rsm['nomor_tdp_file']);
							echo '
							<div class="preview-file">
								<a href="'.$linkTdp.'"><i class="'.$arrIkon[$extIkon4].'"></i>'.str_replace("_"," ",$rsm['nomor_tdp_file']).'</a>
							</div>';
                    	}
                    ?>
                </div>
            </div>

		</div>
	</div>

	

    <div style="border:1px solid #343399;">
        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding title_section" style="width:50%; background-color:#56386a; color:#fff;">
                	<b>2. Person In Charge Details</b>
                </div>
            	<div class="table_cell main_padding title_section" style="width:50%;">
                	&nbsp;
                </div>
            </div>
        </div>
    </div>

    <div style="border:1px solid #343399; border-top:0px; margin-bottom:15px;">
    	<div>&nbsp;</div>
        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>2.1</b>
                </div>
            	<div class="table_cell main_padding">
                	<b>Director or Owner / Direktur atau Pemilik Perusahaan</b>
                </div>
            </div>
		</div>
        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	Full Name / Nama Lengkap
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_decision_name'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	Title / Jabatan
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_decision_position'];?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	Mobile Number / Nomor HP
                </div>
            	<div class="table_cell main_padding" style="width:300px;">
                	<?php echo $rsm['pic_decision_mobile'];?>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	E-mail
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_decision_email'];?>
                </div>
            </div>
		</div>

        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>2.2</b>
                </div>
            	<div class="table_cell main_padding">
                	<b>Procurement / Pembelian</b>
                </div>
            </div>
		</div>
        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	Full Name / Nama Lengkap
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_ordering_name'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	Title / Jabatan
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_ordering_position'];?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	Mobile Number / Nomor HP
                </div>
            	<div class="table_cell main_padding" style="width:300px;">
                	<?php echo $rsm['pic_ordering_mobile'];?>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	E-mail
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_ordering_email'];?>
                </div>
            </div>
		</div>

        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>2.3</b>
                </div>
            	<div class="table_cell main_padding">
                	<b>Finance</b>
                </div>
            </div>
		</div>
        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	Full Name / Nama Lengkap
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_billing_name'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	Title / Jabatan
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_billing_position'];?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	Mobile Number / Nomor HP
                </div>
            	<div class="table_cell main_padding" style="width:300px;">
                	<?php echo $rsm['pic_billing_mobile'];?>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	E-mail
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_billing_email'];?>
                </div>
            </div>
		</div>

        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>2.4</b>
                </div>
            	<div class="table_cell main_padding">
                	<b>Site / Fuelman PIC</b>
                </div>
            </div>
		</div>
        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	Full Name / Nama Lengkap
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_fuelman_name'];?>
                </div>
            </div>

        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	Title / Jabatan
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_fuelman_position'];?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	Mobile Number / Nomor HP
                </div>
            	<div class="table_cell main_padding" style="width:300px;">
                	<?php echo $rsm['pic_fuelman_mobile'];?>
                </div>
            	<div class="table_cell main_padding" style="width:250px;">
                	E-mail
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['pic_fuelman_email'];?>
                </div>
            </div>
		</div>

	</div>

    <div style="border:1px solid #343399;">
        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding title_section" style="width:50%; background-color:#56386a; color:#fff;">
                	<b>3. Payment Term &amp; Banking Detail</b>
                </div>
            	<div class="table_cell main_padding title_section" style="width:50%;">
                	&nbsp;
                </div>
            </div>
        </div>
    </div>

    <div style="border:1px solid #343399; border-top:0px; margin-bottom:15px;">
    	<div>&nbsp;</div>
        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>3.1</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>Pricing Method Calculation / Metode Perhitungan harga</b> 
                </div>
            	<div class="table_cell main_padding">
					<?php echo ($rsm['calculate_method']==1 ? 'Discount Pricelist' : ($rsm['calculate_method']==2 ? 'Formula MOPS' : ''));?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>3.2</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>Payment Metode/ cara pembayaran</b> 
                </div>
            	<div class="table_cell main_padding">
                	<?php 
						if($rsm['payment_method'] == 1) echo 'Cash';
						else if($rsm['payment_method'] == 2) echo 'Transfer';
						else if($rsm['payment_method'] == 3) echo 'Cheque / Giro';
						else if($rsm['payment_method'] == 4) echo 'Bank Guarantee';
						else if($rsm['payment_method'] == 5) echo $rsm['payment_method_other'];
					?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>3.3</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>Payment Term / Termin Pembayaran</b> 
                </div>
            	<div class="table_cell main_padding">
                	<?php 
						if($rsm['jenis_payment'] == 'CBD') echo 'CBD (Cash Before Delivery)';
						else if($rsm['jenis_payment'] == 'COD') echo 'COD (Cash On Delivery)';
						else if($rsm['jenis_payment'] == 'CREDIT') echo 'CREDIT';
					?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>3.4</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>Term of Payment / Jangka waktu Pembayaran</b> <small><i>(if credit)</i></small>
                </div>
            	<div class="table_cell main_padding">
                	<?php 
						if($rsm['jenis_payment'] == 'CREDIT'){
							$arrJenisNetNew = array(1=>"After Invoice Receive", "After Delivery", "After Loading");
							echo $rsm['top_payment'].' Days '.($rsm['jenis_net'] ? $arrJenisNetNew[$rsm['jenis_net']] : '');
						} else echo '&nbsp;';
					?>
                </div>
            </div>
		</div>

        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>3.5</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>Bank Name / Nama Bank</b> 
                </div>
            	<div class="table_cell main_padding">
					<?php echo $rsm['bank_name'];?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>&nbsp;</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>Curency / Mata Uang</b> 
                </div>
            	<div class="table_cell main_padding">
					<?php echo $rsm['curency'];?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>3.6</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>Bank Address / Alamat Bank</b> 
                </div>
            	<div class="table_cell main_padding">
					<?php echo $rsm['bank_address'];?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>3.7</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>Account Number / Nomor Rekening</b> 
                </div>
            	<div class="table_cell main_padding">
					<?php echo $rsm['account_number'];?>
                </div>
            </div>
		</div>

        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>3.8</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>Have Credit Facility or Bank Loan? / Punya Fasilitas Kredit atau Pinjaman Bank ?</b> 
                </div>
            	<div class="table_cell main_padding">
					<?php echo ($rsm['credit_facility'] == 1 ? 'Yes' : 'No');?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	&nbsp;
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	Please provide the creditor(s) who provide the loan / credit facility / Harap menginformasikan nama penyedia fasilitas kredit atau pinjaman tersebut
                </div>
            	<div class="table_cell main_padding">
					<?php echo $rsm['creditor'];?>
                </div>
            </div>
		</div>

	</div>

    <div style="border:1px solid #343399;">
        <div class="table_display">
        	<div class="table_row">
            	<div class="table_cell main_padding title_section" style="width:50%; background-color:#56386a; color:#fff;">
                	<b>4. Supply Scheme </b>
                </div>
            	<div class="table_cell main_padding title_section" style="width:50%;">
                	&nbsp;
                </div>
            </div>
        </div>
    </div>

    <div style="border:1px solid #343399; border-top:0px; margin-bottom:15px;">
    	<div>&nbsp;</div>
        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>4.1</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>What are the envisaged supply scheme details ? / Bagaimana skema rincian pasokan yang diharapkan ?</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo ($rsm['supply_shceme'] == 1 ? 'Trucking' : 'SPOB / Vessel');?>
                </div>
            </div>
        </div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>4.2</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>Specify Product / Jenis Produk</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $specify_product;?>
                </div>
            </div>
        </div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>4.3</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>Volume per Month / Jumlah per bulan </b> 
                </div>
            	<div class="table_cell main_padding">
                	<?php echo $rsm['volume_per_month'];?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>4.4</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>Operational hour for receiving product on site / Jam operasional penerimaan produk di lokasi site</b> 
                </div>
            	<div class="table_cell main_padding">
                	<?php echo 'From '.$rsm['operational_hour_from'].' To '.$rsm['operational_hour_to'];?>
                </div>
            </div>
		</div>

        <div class="table_display" style="margin-bottom:10px;">
        	<div class="table_row">
            	<div class="table_cell main_padding" style="width:50px;">
                	<b>4.5</b>
                </div>
            	<div class="table_cell main_padding" style="width:350px;">
                	<b>INCO terms</b>
                </div>
            	<div class="table_cell main_padding">
                	<?php echo ($rsm['nico'] == 1 ? 'Loco' : 'Delivered');?>
                </div>
            </div>
		</div>

	</div>
</div>

