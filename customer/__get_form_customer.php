<?php
	$alamat_customer 	= $rsm['alamat_customer'];
	$alamat_payment 	= $rsm['alamat_billing'];
	$arrTipeBisnis 		= array(1=>"Agriculture & Forestry / Horticulture", "Business & Information", "Construction/Utilities/Contracting", "Education", "Finance & Insurance", "Food & hospitally", "Gaming", "Health Services", "Motor Vehicle", $rsm['tipe_bisnis_lain'],"Natural Resources / Environmental","Personal Service", "Manufacture");
	$arrOwnership 	 	= array(1=>"Affiliation", "National Private", "Foreign Private", "Joint Venture", "BUMN / BUMD", "Foundation", "Personal", $rsm['ownership_lain']);
	$arrPaymentJadwal 	= array(1=>"Every Day", $rsm['payment_schedule_other']);
	$arrPaymentMethod 	= array(1=>"Cash", "Transfer", "Cheque / Giro", "Bank Guarantee", $rsm['payment_method_other']);
	$arrBuktiPotPPN 	= array("_________________", "Bukti Pot. PPn");
	$arrLogistikEnv 	= array(1=>"Industri", "Pemukiman", $rsm['logistik_env_other']);
	$arrLogistikStorage = array(1=>"Indoor", "Outdoor", $rsm['logistik_storage_other']);
	$arrLogistikHour 	= array(1=>"08.00 - 17.00", "24 Hours", $rsm['logistik_hour_other']);
	$arrLogistikVolume 	= array(1=>"PRO ENERGY'S TANK LORRY", "FLOWMETER", $rsm['logistik_volume_other']);
	$arrLogistikQuality = array(1=>"DENSITY", $rsm['logistik_quality_other']);
	$arrLogistikTruck 	= array(1=>"5 KL", "8 KL", "10 KL", "16 KL", $rsm['logistik_truck_other']);
	$arr_payment = array("COD"=>"COD (Cash On Delivery)","CBD"=>"CBD (Cash Before Delivery)");
	$tmp_addr1 			= ucwords(strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['kabupaten_customer'])));
	$tmp_addr2 			= ucwords(strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rsm['kabupaten_payment'])));
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

	$arrTermPayment 	= array("CREDIT"=>"CREDIT", "CBD"=>"CBD (Cash Before Delivery)", "COD"=>"COD (Cash On Delivery)");
	$arrConditionInd 	= array(1=>"Setelah Invoice diterima", "Setelah pengiriman", "Setelah loading");
	$arrConditionEng 	= array(1=>"After Invoice Receive", "After Delivery", "After Loading");
?>

<div style="margin:0px 50px;">
	<p><i>Data dimutakhirkan terakhir kali oleh <?php echo $rsm['lastupdate_by'];?> tanggal <?php echo tgl_indo($rsm['lastupdate_time']);?></i></p>
    <div style="background-color:#c6e0b3; border:2px solid #343399; padding:15px; margin-bottom:10px;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
                <td class="text-left" width="20%"><img src="<?php echo BASE_IMAGE."/logo-kiri-penawaran.png"; ?>" /></td>
                <td class="text-center" width=""><h4 style="font-family:arial; font-size:28px;"><b>APPLICATION CUSTOMER FORM</b></h4></td>
                <td class="text-right" width="20%"><img src="<?php echo BASE_IMAGE."/logo-kanan-penawaran.png"; ?>" /></td>
            </tr>
        </table>
    </div>

    <div style="border:2px solid #343399;">
        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50%; padding:5px 15px; background-color:#343399; color:#fff;">
                	<b>1. Corporate Details</b>
                </div>
            	<div style="display:table-cell; width:50%; padding:5px 15px;">
                	<b>* Isi dengan huruf CETAK</b>
                </div>
            </div>
        </div>
    </div>
    <div style="border:2px solid #343399; border-top:0px; margin-bottom:10px;">
        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>1.1</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	<b>Full Registered Company Name / Nama lengkap perusahaan yang terdaftar</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['nama_customer'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>1.2</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	<b>Holding / Induk Perusahaan</b> (Jika ada)
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['induk_perusahaan'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>1.3</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	<b>Registered Street Address / Alamat Kantor terdaftar</b> (NPWP Address)
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['alamat_billing'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>Sub-Districts / Kelurahan</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $kelurahan_billing;?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>Districts / Kecamatan</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $kecamatan_billing;?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>City / Kota (Kabupaten)</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $tmp_addr2;?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>Province / Provinsi</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['propinsi_payment'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>Country / Negara</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	<?php echo "Indonesia";?>
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>Post Code / Kode Pos</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['postalcode_billing'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>Telephone Number</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['telp_billing'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px 15px;">
                	<b>Fax Number</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px 10px;">
                	<?php echo $rsm['fax_billing'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>1.4</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	<b>Address of Head Office / Alamat Kantor Pusat</b> (Isi jika alamat tidak sama dengan alamat NPWP)
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['alamat_customer'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>Sub-Districts / Kelurahan</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $kelurahan_customer;?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>Districts / Kecamatan</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $kecamatan_customer;?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>City / Kota (Kabupaten)</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $tmp_addr1;?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>Province / Provinsi</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['propinsi_customer'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>Country / Negara</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	<?php echo "Indonesia";?>
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>Post Code / Kode Pos</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['postalcode_customer'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>Telephone Number</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['telp_customer'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px 15px;">
                	&nbsp;
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px 15px;">
                	<b>Fax Number</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px 10px;">
                	<?php echo $rsm['fax_customer'];?>
                </div>
            </div>
		</div>

        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px 15px;">
                	<b>1.5</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px 15px;">
                	<b>Product Delivery Full Address or site address / Alamat Lengkap pengiriman produk atau alamat proyek</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px 15px;">
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

        	<div style="display:table-row;">
            	<div style="display:table-cell; width:50px; padding:5px 15px 15px;">
                	<b>1.6</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px 15px;">
                	<b>Invoice delivery address / Alamat pengiriman Invoice</b> (Please Tick)
                </div>
            	<div style="display:table-cell; padding:5px 15px 15px;">
                	<?php echo $invoice_delivery_addr_primary;?>
                </div>
            </div>
		</div>

        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px 15px;">
                	<b>PIC Name for received Invoice</b>
                </div>
            	<div style="display:table-cell; width:350px; padding:5px 15px 15px;">
                	<?php echo $rsm['pic_invoice_name'];?>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px 15px;">
                	<b>Mobile Number</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px 15px;">
                	<?php echo $rsm['pic_invoice_mobile'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px 15px;">
                	<b>Division / Bagian</b>
                </div>
            	<div style="display:table-cell; width:350px; padding:5px 15px 15px;">
                	<?php echo $rsm['pic_invoice_position'];?>
                </div>
            	<div style="display:table-cell; width:280px; padding:5px 15px 15px;">
                	<b>E-Mail Address</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px 15px;">
                	<?php echo $rsm['pic_invoice_email'];?>
                </div>
            </div>
        </div>
    </div>

    <div style="border:2px solid #343399;">
        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50%; padding:5px 15px; background-color:#343399; color:#fff;">
                	<b>File Pendukung</b>
                </div>
            	<div style="display:table-cell; width:50%; padding:5px 15px;">
                	&nbsp;
                </div>
            </div>
        </div>
    </div>
    <div style="border:2px solid #343399; border-top:0px; margin-bottom:10px;">
        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:350px; padding:5px 15px 15px;">
                	<b>CERTIFICATE NUMBER (Akta Pendirian)</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px 15px;">
					<?php 
                    	echo '<p>'.($rsm['nomor_sertifikat'] ? $rsm['nomor_sertifikat'] : '&nbsp;').'</p>';
                    	if($rsm['nomor_sertifikat_file'] && file_exists($file_path_sert)){
							$link1 = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=sert_file".$idr."_&file=".$rsm['nomor_sertifikat_file']);
							echo '
							<div class="preview-file">
								<a href="'.$link1.'"><i class="'.$arrIkon[$extIkon1].'"></i>'.str_replace("_"," ",$rsm['nomor_sertifikat_file']).'</a>
							</div>';
                    	}
                    ?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:350px; padding:5px 15px 15px;">
                	<b>NPWP NUMBER</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px 15px;">
					<?php 
                    	echo '<p>'.$rsm['nomor_npwp'].'</p>';
                    	if($rsm['nomor_npwp_file'] && file_exists($file_path_npwp)){
                    		$linkNpwp = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=npwp_file".$idr."_&file=".$rsm['nomor_npwp_file']);
                    		echo '
							<div class="preview-file">
                    			<a href="'.$linkNpwp.'"><i class="'.$arrIkon[$extIkon2].'"></i>'.str_replace("_"," ",$rsm['nomor_npwp_file']).'</a>
							</div>';
                    	}
                    ?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:350px; padding:5px 15px 15px;">
                	<b>SIUP NUMBER</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px 15px;">
					<?php 
                    	echo '<p>'.$rsm['nomor_siup'].'</p>';
                    	if($rsm['nomor_siup_file'] && file_exists($file_path_siup)){
							$linkSiup = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=siup_file".$idr."_&file=".$rsm['nomor_siup_file']);
							echo '
							<div class="preview-file">
								<a href="'.$linkSiup.'"><i class="'.$arrIkon[$extIkon3].'"></i>'.str_replace("_"," ",$rsm['nomor_siup_file']).'</a>
							</div>';
                    	}
                    ?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:350px; padding:5px 15px 15px;">
                	<b>TDP NUMBER</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px 15px;">
					<?php 
                    	echo '<p>'.$rsm['nomor_tdp'].'</p>';
                    	if($rsm['nomor_tdp_file'] && file_exists($file_path_tdpn)){
							$linkTdp = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=tdp_file".$idr."_&file=".$rsm['nomor_tdp_file']);
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

    <div style="border:2px solid #343399;">
        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50%; padding:5px 15px; background-color:#343399; color:#fff;">
                	<b>2. Person In Charge Details</b>
                </div>
            	<div style="display:table-cell; width:50%; padding:5px 15px;">
                	&nbsp;
                </div>
            </div>
        </div>
    </div>
    <div style="border:2px solid #343399; border-top:0px; margin-bottom:10px;">
        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>2.1</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<b>Director or Owner / Direktur atau Pemilik Perusahaan</b>
                </div>
            </div>
		</div>
        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	Full Name / Nama Lengkap
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['pic_decision_name'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	Title / Jabatan
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['pic_decision_position'];?>
                </div>
            </div>
		</div>

        <div style="display:table; width:100%; margin-bottom:10px;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	Mobile Number / Nomor HP
                </div>
            	<div style="display:table-cell; width:450px; padding:5px 15px;">
                	<?php echo $rsm['pic_decision_mobile'];?>
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	E-mail
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['pic_decision_email'];?>
                </div>
            </div>
		</div>

        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>2.2</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<b>Procurement / Pembelian</b>
                </div>
            </div>
		</div>
        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	Full Name / Nama Lengkap
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['pic_ordering_name'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	Title / Jabatan
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['pic_ordering_position'];?>
                </div>
            </div>
		</div>

        <div style="display:table; width:100%; margin-bottom:10px;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	Mobile Number / Nomor HP
                </div>
            	<div style="display:table-cell; width:450px; padding:5px 15px;">
                	<?php echo $rsm['pic_ordering_mobile'];?>
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	E-mail
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['pic_ordering_email'];?>
                </div>
            </div>
		</div>


        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>2.3</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<b>Finance</b>
                </div>
            </div>
		</div>
        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	Full Name / Nama Lengkap
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['pic_billing_name'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	Title / Jabatan
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['pic_billing_position'];?>
                </div>
            </div>
		</div>

        <div style="display:table; width:100%; margin-bottom:10px;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	Mobile Number / Nomor HP
                </div>
            	<div style="display:table-cell; width:450px; padding:5px 15px;">
                	<?php echo $rsm['pic_billing_mobile'];?>
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	E-mail
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['pic_billing_email'];?>
                </div>
            </div>
		</div>

        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>2.4</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<b>Site / Fuelman PIC</b>
                </div>
            </div>
		</div>
        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	Full Name / Nama Lengkap
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['pic_fuelman_name'];?>
                </div>
            </div>

        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	Title / Jabatan
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['pic_fuelman_position'];?>
                </div>
            </div>
		</div>

        <div style="display:table; width:100%; margin-bottom:10px;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>&nbsp;</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	Mobile Number / Nomor HP
                </div>
            	<div style="display:table-cell; width:450px; padding:5px 15px;">
                	<?php echo $rsm['pic_fuelman_mobile'];?>
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	E-mail
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['pic_fuelman_email'];?>
                </div>
            </div>
		</div>

	</div>

    <div style="border:2px solid #343399;">
        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50%; padding:5px 15px; background-color:#343399; color:#fff;">
                	<b>3. Payment Term &amp; Banking Detail</b>
                </div>
            	<div style="display:table-cell; width:50%; padding:5px 15px;">
                	&nbsp;
                </div>
            </div>
        </div>
    </div>
    <div style="border:2px solid #343399; border-top:0px; margin-bottom:10px;">
        <div style="display:table; width:100%; margin-bottom:10px;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>3.1</b>
                </div>
            	<div style="display:table-cell; width:350px; padding:5px 15px;">
                	<b>Pricing Method Calculation / Metode Perhitungan harga</b> 
                </div>
            	<div style="display:table-cell; width:350px; padding:5px 15px;">
					<?php echo ($rsm['calculate_method']==1 ? 'Discount Pricelist' : ($rsm['calculate_method']==2 ? 'Formula MOPS' : ''));?>
                </div>
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>3.2</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	<b>Payment Metode/ cara pembayaran</b> 
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
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

        <div style="display:table; width:100%; margin-bottom:10px;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>3.3</b>
                </div>
            	<div style="display:table-cell; width:350px; padding:5px 15px;">
                	<b>Payment Term / Termin Pembayaran</b> 
                </div>
            	<div style="display:table-cell; width:350px; padding:5px 15px;">
                	<?php 
						if($rsm['jenis_payment'] == 'CBD') echo 'CBD (Cash Before Delivery)';
						else if($rsm['jenis_payment'] == 'COD') echo 'COD (Cash On Delivery)';
						else if($rsm['jenis_payment'] == 'CREDIT') echo 'CREDIT';
					?>
                </div>
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>3.4</b>
                </div>
            	<div style="display:table-cell; width:300px; padding:5px 15px;">
                	<b>Term of Payment / Jangka waktu Pembayaran</b> <i>(if credit)</i> 
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php 
						if($rsm['jenis_payment'] == 'CREDIT'){
							$arrJenisNetNew = array(1=>"After Invoice Receive", "After Delivery", "After Loading");
							echo $rsm['top_payment'].' Days '.($rsm['jenis_net'] ? $arrJenisNetNew[$rsm['jenis_net']] : '');
						} else echo '&nbsp;';
					?>
                </div>
            </div>
		</div>

        <div style="display:table; width:100%; margin-bottom:10px;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>3.5</b>
                </div>
            	<div style="display:table-cell; width:350px; padding:5px 15px;">
                	<b>Bank Name / Nama Bank</b> 
                </div>
            	<div style="display:table-cell; width:500px; padding:5px 15px;">
					<?php echo $rsm['bank_name'];?>
                </div>
            	<div style="display:table-cell; width:200px; padding:5px 15px;">
                	<b>Curency / Mata Uang</b> 
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
					<?php echo $rsm['curency'];?>
                </div>
            </div>
		</div>

        <div style="display:table; width:100%; margin-bottom:10px;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>3.6</b>
                </div>
            	<div style="display:table-cell; width:350px; padding:5px 15px;">
                	<b>Bank Address / Alamat Bank</b> 
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
					<?php echo $rsm['bank_address'];?>
                </div>
            </div>
		</div>

        <div style="display:table; width:100%; margin-bottom:10px;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>3.7</b>
                </div>
            	<div style="display:table-cell; width:350px; padding:5px 15px;">
                	<b>Account Number / Nomor Rekening</b> 
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
					<?php echo $rsm['account_number'];?>
                </div>
            </div>
		</div>

        <div style="display:table; width:100%; margin-bottom:10px;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>3.8</b>
                </div>
            	<div style="display:table-cell; width:350px; padding:5px 15px;">
                	<b>Have Credit Facility or Bank Loan? / Punya Fasilitas Kredit atau Pinjaman Bank ?</b> 
                </div>
            	<div style="display:table-cell; width:150px; padding:5px 15px;">
					<?php echo ($rsm['credit_facility'] == 1 ? 'Yes' : 'No');?>
                </div>
            	<div style="display:table-cell; width:400px; padding:5px 15px; text-align:right;">
                	<b>Please provide the creditor(s) who provide the loan / credit facility / Harap menginformasikan nama penyedia fasilitas kredit atau pinjaman tersebut</b> 
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
					<?php echo $rsm['creditor'];?>
                </div>
            </div>
		</div>

	</div>

    <div style="border:2px solid #343399;">
        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50%; padding:5px 15px; background-color:#343399; color:#fff;">
                	<b>4. Supply Scheme </b>
                </div>
            	<div style="display:table-cell; width:50%; padding:5px 15px;">
                	&nbsp;
                </div>
            </div>
        </div>
    </div>

    <div style="border:2px solid #343399; border-top:0px; margin-bottom:10px;">
        <div style="display:table; width:100%; margin-bottom:10px;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>4.1</b>
                </div>
            	<div style="display:table-cell; width:350px; padding:5px 15px;">
                	<b>What are the envisaged supply scheme details ? / Bagaimana skema rincian pasokan yang diharapkan ?</b>
                </div>
            	<div style="display:table-cell; width:450px; padding:5px 15px;">
                	<?php echo ($rsm['supply_shceme'] == 1 ? 'Trucking' : 'SPOB / Vessel');?>
                </div>
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>4.2</b>
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>Specify Product / Jenis Produk</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $specify_product;?>
                </div>
            </div>
        </div>

        <div style="display:table; width:100%; margin-bottom:10px;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>4.3</b>
                </div>
            	<div style="display:table-cell; width:350px; padding:5px 15px;">
                	<b>Volume per Month / Jumlah per bulan </b> 
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo $rsm['volume_per_month'];?>
                </div>
            </div>
		</div>

        <div style="display:table; width:100%;">
        	<div style="display:table-row">
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>4.4</b>
                </div>
            	<div style="display:table-cell; width:350px; padding:5px 15px;">
                	<b>Operational hour for receiving product on site / Jam operasional penerimaan produk di lokasi site</b> 
                </div>
            	<div style="display:table-cell; width:450px; padding:5px 15px;">
                	<?php echo 'From '.$rsm['operational_hour_from'].' To '.$rsm['operational_hour_to'];?>
                </div>
            	<div style="display:table-cell; width:50px; padding:5px 15px;">
                	<b>4.5</b>
                </div>
            	<div style="display:table-cell; width:250px; padding:5px 15px;">
                	<b>INCO terms</b>
                </div>
            	<div style="display:table-cell; padding:5px 15px;">
                	<?php echo ($rsm['nico'] == 1 ? 'Loco' : 'Delivered');?>
                </div>
            </div>
		</div>

	</div>
</div>

<hr style="margin:25px 0px 20px; border-top:4px double #ddd;" />

<div style="margin:0px 50px;">
    <div style="margin-bottom:0px;">
        <a class="btn btn-success jarak-kanan" style="min-width:90px;" target="_blank" href="<?=ACTION_CLIENT.'/customer-cetak.php?'.paramEncrypt('idr='.$idr)?>">
        <i class="fa fa-print jarak-kanan"></i> Cetak</a>
		<?php 	
			 if(($rsm['is_approved'] == 0 && $rsm['count_update'] < 2) || $rsm['disposisi_result'] == 0){ 
				$linkCus = BASE_URL.'/customer/update-customer.php?'.paramEncrypt('idr='.$idr.'&idk='.$idk.'&token='.$token.'&edit=1');
				
				$small_ket = '
				<a class="btn btn-primary jarak-kanan" style="min-width:90px;" href="'.$linkCus.'">
				<i class="fa fa-edit jarak-kanan"></i> Edit</a>';

			 	if($rsm['count_update'] == 0){
					$small_ket .= '<p style="margin:15px 0px 0px;"><small><i>* Maksimal edit sebanyak 2 kali</i></small></p>';
				} else if($rsm['count_update'] == 1){
					$small_ket .= '<p style="margin:15px 0px 0px;"><small><i>* Maksimal edit sebanyak 2 kali dan Anda sudak melakukan sebanyak 1 kali</i></small></p>';
				} 
				if($rsm['need_update'] == 1){
					$small_ket .= '<p style="margin:15px 0px 0px;"><small><i>* Edit Revisi</i></small></p>';
				}
				echo $small_ket;
			 }
		?>
	</div>

</div>

<style type="text/css">
	.table > tbody > tr > td{
		padding: 5px;
	}
	.tipe-bisnis > tbody > tr > td{
		padding: 0px 0px 2px;
	}
	h3.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
	}
	.preview-file{
		background-color: rgb(244, 244, 244);
		border: 1px solid rgb(221, 221, 221);
		padding: 5px 25px 5px 10px;
		margin-bottom: 0px;
	}
    .bg-light-purple{
        background-color: #56386a;
        color: #f9f9f9 !important;
    }
    .box.box-purple{
        border-top-color: #56386a;

    }
</style>
