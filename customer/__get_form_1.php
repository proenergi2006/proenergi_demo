<fieldset id="set-company1"> 
    <div class="form-title">
    	<h3>1. CORPORATE DETAILS</h3><small>Step 1/5</small>
	</div>
    <div class="form-main">

        <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> FULL REGISTERED COMPANY NAME *</h3>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" name="nama_customer" id="nama_customer" class="form-control" required value="<?php echo changeValue($rsm, 'nama_customer') ?>" />
                </div>
            </div>
        </div>

        <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> HOLDING <small>(Jika ada)</small></h3>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" name="induk_perusahaan" id="induk_perusahaan" class="form-control" value="<?php echo changeValue($rsm, 'induk_perusahaan') ?>" />
                </div>
            </div>
        </div>

        <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> ADDRESS OF HEAD OFFICE  <small>(Isi jika alamat tidak sama dengan alamat NPWP)</small></h3>
        <div style="margin:0px 30px;">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="text" name="email_customer" id="email_customer" class="form-control" required data-rule-email="1" value="<?php echo changeValue($rsm, 'email_customer') ?>" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Website</label>
                        <input type="text" name="website_customer" id="website_customer" class="form-control" value="<?php echo changeValue($rsm, 'website_customer') ?>" />
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Address *</label>
                        <textarea name="alamat_customer" id="alamat_customer" class="form-control" required><?php echo changeValue($rsm, 'alamat_customer') ?></textarea>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Province / Provinsi *</label>
                        <select id="prov_customer" name="prov_customer" class="form-control select2" required>
                            <option></option>
                            <?php $con->fill_select("id_prov","nama_prov","pro_master_provinsi",$rsm['prov_customer'],"","nama",false); ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>City / Kota (Kabupaten) *</label>
                        <select id="kab_customer" name="kab_customer" class="form-control select2" required>
                            <option></option>
                            <?php 
                                $con->fill_select("id_kab","nama_kab","pro_master_kabupaten",$rsm['kab_customer'],"where id_prov='".$rsm['prov_customer']."'","nama",false); 
                            ?>
                        </select>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Districts / Kecamatan</label>
                        <input type="text" name="kecamatan_customer" id="kecamatan_customer" class="form-control" value="<?php echo changeValue($rsm, 'kecamatan_customer') ?>" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Sub-Districts / Kelurahan</label>
                        <input type="text" name="kelurahan_customer" id="kelurahan_customer" class="form-control" value="<?php echo changeValue($rsm, 'kelurahan_customer') ?>" />
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Postal Code</label>
                        <input type="text" name="postalcode_customer" id="postalcode_customer" class="form-control" value="<?php echo changeValue($rsm, 'postalcode_customer') ?>" />
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Telephone *</label>
                        <input type="text" name="telp_customer" id="telp_customer" class="form-control phone-number" required value="<?php echo changeValue($rsm, 'telp_customer') ?>" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Fax</label>
                        <input type="text" name="fax_customer" id="fax_customer" class="form-control fax" value="<?php echo changeValue($rsm, 'fax_customer') ?>" />
                    </div>
                </div>
            </div>
        </div>

        <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> REGISTERED STREET ADDRESS <small>(NPWP Address)</small></h3>
        <div style="margin:0px 30px;">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="text" name="email_billing" id="email_billing" class="form-control" required data-rule-email="1" value="<?php echo changeValue($rsm, 'email_billing') ?>" />
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Address *</label>
                        <textarea name="alamat_billing" id="alamat_billing" class="form-control" required><?php echo changeValue($rsm, 'alamat_billing') ?></textarea>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Province / Provinsi *</label>
                        <select id="prov_billing" name="prov_billing" class="form-control select2" required>
                            <option></option>
                            <?php 
                                $prov_bill = ($rsm['prov_billing'])?$rsm['prov_billing']:$_SESSION['post'][$idr]['prov_billing'];
                                $con->fill_select("id_prov","nama_prov","pro_master_provinsi",$prov_bill,"","nama_prov",false); 
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>City / Kota (Kabupaten) *</label>
                        <select id="kab_billing" name="kab_billing" class="form-control select2" required>
                            <option></option>
                            <?php 
                            $kab_billing = ($rsm['kab_billing'])?$rsm['kab_billing']:$_SESSION['post'][$idr]['kab_billing'];
                            $con->fill_select("id_kab","nama_kab","pro_master_kabupaten",$kab_billing,"where id_prov='".$rsm['prov_billing']."'","nama_kab",false); ?>
                        </select>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Districts / Kecamatan</label>
                        <input type="text" name="kecamatan_billing" id="kecamatan_billing" class="form-control" value="<?php echo changeValue($rsm, 'kecamatan_billing') ?>" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Sub-Districts / Kelurahan</label>
                        <input type="text" name="kelurahan_billing" id="kelurahan_billing" class="form-control" value="<?php echo changeValue($rsm, 'kelurahan_billing') ?>" />
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Postal Code</label>
                        <input type="text" name="postalcode_billing" id="postalcode_billing" class="form-control" value="<?php echo changeValue($rsm, 'postalcode_billing') ?>" />
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Telephone *</label>
                        <input type="text" name="telp_billing" id="telp_billing" class="form-control phone-number" required value="<?php echo changeValue($rsm, 'telp_billing') ?>" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Fax</label>
                        <input type="text" name="fax_billing" id="fax_billing" class="form-control fax" value="<?php echo changeValue($rsm, 'fax_billing') ?>" />
                    </div>
                </div>
            </div>
		</div>
        
        <?php  
			if($rsm['product_delivery_address']!=''){
				$product_delivery = json_decode($rsm['product_delivery_address'],TRUE);
			} else{
				$product_delivery['product_delivery_address'] = [];
			}
        ?>
        <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> PRODUCT DELIVERY FULL ADDRESS OR SITE ADDRESS</h3>
        <div style="margin:0px 30px;">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Alamat 1</label>
                         <textarea name="product_delivery_address[]" class="form-control product_delivery_address"><?php echo ((array_key_exists(0,$product_delivery['product_delivery_address']))?$product_delivery['product_delivery_address'][0]:'')?></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Alamat 2</label>
                         <textarea name="product_delivery_address[]" class="form-control product_delivery_address"><?php echo ((array_key_exists(1,$product_delivery['product_delivery_address']))?$product_delivery['product_delivery_address'][1]:'')?></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Alamat 3</label>
                         <textarea name="product_delivery_address[]" class="form-control product_delivery_address"><?php echo ((array_key_exists(2,$product_delivery['product_delivery_address']))?$product_delivery['product_delivery_address'][2]:'')?></textarea>
                    </div>
                </div>
            </div>
		</div>

        <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> INVOICE DELIVERY ADDRESS *</h3>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <select id="invoice_delivery_addr_primary" name="invoice_delivery_addr_primary" class="form-control select2" required>
                        <option></option>
                        <option value="NPWP Address" <?php echo ($rsm['invoice_delivery_addr_primary']=='NPWP Address'?'selected':'') ?>>NPWP Address</option>
                        <option value="Head Office Address" <?php echo ($rsm['invoice_delivery_addr_primary']=='Head Office Address'?'selected':'') ?>>Head Office Address</option>
                    </select>
                </div>
            </div>
        </div>

        <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> PIC FOR RECEIVED INVOICE</h3>
        <div style="margin:0px 30px;">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="pic_invoice_name" id="pic_invoice_name" class="form-control" required value="<?php echo changeValue($rsm, 'pic_invoice_name') ?>" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Position *</label>
                        <input type="text" name="pic_invoice_position" id="pic_invoice_position" class="form-control" required value="<?php echo changeValue($rsm, 'pic_invoice_position') ?>" />
                    </div>
                </div>
            </div>
    
            <div class="row">
               <div class="col-sm-6">
                    <div class="form-group">
                        <label>Telephone *</label>
                        <input type="text" name="pic_invoice_telp" id="pic_invoice_telp" class="form-control phone-number" required value="<?php echo changeValue($rsm, 'pic_invoice_telp') ?>" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Mobile *</label>
                        <input type="text" name="pic_invoice_mobile" id="pic_invoice_mobile" class="form-control phone-number" required value="<?php echo changeValue($rsm, 'pic_invoice_mobile') ?>" />
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="text" name="pic_invoice_email" id="pic_invoice_email" class="form-control" required data-rule-email="1" value="<?php echo changeValue($rsm, 'pic_invoice_email') ?>" />
                    </div>
                </div>
            </div>
		</div>

		<p>&nbsp;</p>
		<?php $tipe_bisnis = isset($rsm['tipe_bisnis'])?$rsm['tipe_bisnis']:$_SESSION['post'][$idr]['tipe_bisnis']; ?>
        <?php $ownership = isset($rsm['ownership'])?$rsm['ownership']:$_SESSION['post'][$idr]['ownership']; ?>
        <div class="row">
        	<div class="col-sm-6">
                <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> TYPE OF BUSINESS</h3>
                <div style="margin:0px 15px;">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="radio clearfix">
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="tipe_bisnis" id="tipe_bisnis1" value="1" class="form-control"<?php echo($tipe_bisnis == 1)?' checked="checked"':''; ?> /> 
                                        Agriculture &amp; Foresty / Horticulture
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="tipe_bisnis" id="tipe_bisnis2" value="2" class="form-control"<?php echo($tipe_bisnis == 2)?' checked="checked"':''; ?> /> 
                                        Business &amp; Information
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="tipe_bisnis" id="tipe_bisnis3" value="3" class="form-control"<?php echo($tipe_bisnis == 3)?' checked="checked"':''; ?> /> 
                                        Construction / Utilities / Contracting
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="tipe_bisnis" id="tipe_bisnis4" value="4" class="form-control"<?php echo($tipe_bisnis == 4)?' checked="checked"':''; ?> /> 
                                        Education
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="tipe_bisnis" id="tipe_bisnis5" value="5" class="form-control"<?php echo($tipe_bisnis == 5)?' checked="checked"':''; ?> /> 
                                        Finance &amp; Insurance
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="tipe_bisnis" id="tipe_bisnis6" value="6" class="form-control"<?php echo($tipe_bisnis == 6)?' checked="checked"':''; ?> /> 
                                        Food &amp; hospitally
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="tipe_bisnis" id="tipe_bisnis7" value="7" class="form-control"<?php echo($tipe_bisnis == 7)?' checked="checked"':''; ?> /> 
                                        Gaming
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="tipe_bisnis" id="tipe_bisnis8" value="8" class="form-control"<?php echo($tipe_bisnis == 8)?' checked="checked"':''; ?> /> 
                                        Health Services
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="tipe_bisnis" id="tipe_bisnis9" value="9" class="form-control"<?php echo($tipe_bisnis == 9)?' checked="checked"':''; ?> /> 
                                        Motor Vehicle
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="tipe_bisnis" id="tipe_bisnis11" value="11" class="form-control"<?php echo($tipe_bisnis == 11)?' checked="checked"':''; ?> /> 
                                        Natural Resources / Environmental 
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="tipe_bisnis" id="tipe_bisnis11" value="12" class="form-control"<?php echo($tipe_bisnis == 12)?' checked="checked"':''; ?> /> 
                                        Personal Service 
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="tipe_bisnis" id="tipe_bisnis11" value="13" class="form-control"<?php echo($tipe_bisnis == 13)?' checked="checked"':''; ?> /> 
                                        Manufacture 
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="tipe_bisnis" id="tipe_bisnis10" value="10" class="form-control"<?php echo($tipe_bisnis == 10)?' checked="checked"':''; ?> /> 
                                        Other, 
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                		<input type="text" name="tipe_bisnis_lain" id="tipe_bisnis_lain" class="form-control" placeholder="Specify..." <?php echo ($tipe_bisnis_lain)?$tipe_bisnis_lain:$_SESSION['post'][$idr]['tipe_bisnis_lain']; ?> />
                                    </label>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>

        	<div class="col-sm-6">
                <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> OWNERSHIP</h3>
                <div style="margin:0px 15px;">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="radio clearfix">
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="ownership" id="ownership1" value="1" class="form-control"<?php echo($ownership == 1)?' checked="checked"':''; ?> /> 
                                        Affiliation
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="ownership" id="ownership2" value="2" class="form-control"<?php echo($ownership == 2)?' checked="checked"':''; ?> /> 
                                        National Private
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="ownership" id="ownership3" value="3" class="form-control"<?php echo($ownership == 3)?' checked="checked"':''; ?> /> 
                                        Foreign Private
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="ownership" id="ownership4" value="4" class="form-control"<?php echo($ownership == 4)?' checked="checked"':''; ?> /> 
                                        Joint Venture
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="ownership" id="ownership5" value="5" class="form-control"<?php echo($ownership == 5)?' checked="checked"':''; ?> /> 
                                        BUMN / BUMD
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="ownership" id="ownership6" value="6" class="form-control"<?php echo($ownership == 6)?' checked="checked"':''; ?> /> 
                                        Foundation
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="ownership" id="ownership7" value="7" class="form-control"<?php echo($ownership == 7)?' checked="checked"':''; ?> /> 
                                        Personal
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                        <input type="radio" name="ownership" id="ownership8" value="8" class="form-control"<?php echo($ownership == 8)?' checked="checked"':''; ?> /> 
                                        Other, 
                                    </label>
                                    <label class="col-sm-12" style="margin-bottom:5px;">
                                    	<input type="text" name="ownership_lain" id="ownership_lain" class="form-control" placeholder="Specify" <?php echo ($ownership_lain)?$ownership_lain:$_SESSION['post'][$idr]['ownership_lain']; ?> />
                                    </label>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>

        <h3 class="form-main-title"><i class="fa fa-list jarak-kanan"></i> DOCUMENTATION <small><i>(include a copy of this following documents)</i></small></h3>
        <div style="margin:0px 15px;">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Certificate Number (Akta Pendirian)</label>
                        <input type="text" name="nomor_sertifikat" id="nomor_sertifikat" class="form-control" value="<?php echo changeValue($rsm, 'nomor_sertifikat') ?>" />
                    </div>
                    <div id="sert_file_wrap" class="file-wrap">
                    <?php 
                        if($rsm['nomor_sertifikat_file'] && file_exists($file_path_sert)){
                            $linkSert = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=sert_file".$idr."_&file=".$rsm['nomor_sertifikat_file']);
                            echo '<div class="preview-file"><a href="'.$linkSert.'">'.str_replace("_", " ", $rsm['nomor_sertifikat_file']).'</a>
                                    <span class="sert_file_del"><i class="fa fa-times"></i></span>
                                  </div>';
                        } else{
                            echo '<div class="form-group">
                                    <label class="sr-only"></label>
                                    <div class="input-group">
                                        <input type="file" name="sert_file" id="sert_file" class="form-control-file" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-info btn-flat file-upload" data-file="sert_file">
                                            <i class="fa fa-upload jarak-kanan"></i> Upload</button>
                                        </span>
                                    </div>
                                    <div class="info-status hide"></div>
                                  </div>';
                        }
                    ?>
                    </div>
                    <p style="font-size:12px; margin:-10px 0px 10px;">* Max size 10MB</p>
                </div>
                
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>NPWP Number *</label>
                        <input type="text" name="nomor_npwp" id="nomor_npwp" class="form-control" required value="<?php echo changeValue($rsm, 'nomor_npwp') ?>" />
                    </div>
                    <div id="npwp_file_wrap" class="file-wrap">
                    <?php 
                        if($rsm['nomor_npwp_file'] && file_exists($file_path_npwp)){
                            $linkNpwp = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=npwp_file".$idr."_&file=".$rsm['nomor_npwp_file']);
                            echo '<div class="preview-file"><a href="'.$linkNpwp.'">'.str_replace("_", " ", $rsm['nomor_npwp_file']).'</a>
                                    <span class="npwp_file_del"><i class="fa fa-times"></i></span>
                                  </div>';
                        } else{
                            echo '<div class="form-group">
                                    <label class="sr-only"></label>
                                    <div class="input-group">
                                        <input type="file" name="npwp_file" id="npwp_file" class="form-control-file" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-info btn-flat file-upload" data-file="npwp_file">
                                            <i class="fa fa-upload jarak-kanan"></i> Upload</button>
                                        </span>
                                    </div>
                                    <div class="info-status hide"></div>
                                  </div>';
                        }
                    ?>
                    </div>
                    <p style="font-size:12px; margin:-10px 0px 10px;">* Max size 2MB</p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>SIUP Number *</label>
                        <input type="text" name="nomor_siup" id="nomor_siup" class="form-control" required value="<?php echo changeValue($rsm, 'nomor_siup') ?>" />
                    </div>
                    <div id="siup_file_wrap" class="file-wrap">
                    <?php 
                        if($rsm['nomor_siup_file'] && file_exists($file_path_siup)){
                            $linkSiup = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=siup_file".$idr."_&file=".$rsm['nomor_siup_file']);
                            echo '<div class="preview-file"><a href="'.$linkSiup.'">'.str_replace("_", " ", $rsm['nomor_siup_file']).'</a>
                                    <span class="siup_file_del"><i class="fa fa-times"></i></span>
                                  </div>';
                        } else{
                            echo '<div class="form-group">
                                    <label class="sr-only"></label>
                                    <div class="input-group">
                                        <input type="file" name="siup_file" id="siup_file" class="form-control-file" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-info btn-flat file-upload" data-file="siup_file">
                                            <i class="fa fa-upload jarak-kanan"></i> Upload</button>
                                        </span>
                                    </div>
                                    <div class="info-status hide"></div>
                                  </div>';
                        }
                    ?>            	
                    </div>
                    <p style="font-size:12px; margin:-10px 0px 10px;">* Max size 2MB</p>
                </div>
    
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>TDP Number *</label>
                        <input type="text" name="nomor_tdp" id="nomor_tdp" class="form-control" required value="<?php echo changeValue($rsm, 'nomor_tdp') ?>" />
                    </div>
                    <div id="tdp_file_wrap" class="file-wrap">
                    <?php 
                        if($rsm['nomor_tdp_file'] && file_exists($file_path_tdpn)){
                            $linkTdp = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=tdp_file".$idr."_&file=".$rsm['nomor_tdp_file']);
                            echo '<div class="preview-file"><a href="'.$linkTdp.'">'.str_replace("_", "", $rsm['nomor_tdp_file']).'</a>
                                    <span class="tdp_file_del"><i class="fa fa-times"></i></span>
                                  </div>';
                        } else{
                            echo '<div class="form-group">
                                    <label class="sr-only"></label>
                                    <div class="input-group">
                                        <input type="file" name="tdp_file" id="tdp_file" class="form-control-file" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-info btn-flat file-upload" data-file="tdp_file">
                                            <i class="fa fa-upload jarak-kanan"></i> Upload</button>
                                        </span>
                                    </div>
                                    <div class="info-status hide"></div>
                                  </div>';
                        }
                    ?>
                    </div>
                    <p style="font-size:12px; margin:-10px 0px 10px;">* Max size 2MB</p>
                </div>
            </div>

            <div class="row">    
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Dokumen Lainnya</label>
                        <input type="text" name="dokumen_lainnya" id="dokumen_lainnya" class="form-control" value="<?php echo $rsm['dokumen_lainnya'] ?? null ?>" />
                    </div>
                    <div id="dokumen_lainnya_file_wrap" class="file-wrap">
                    <?php 
                        if(isset($rsm['dokumen_lainnya_file']) && file_exists($file_path_dokumen_lainnya)){
                            $linkDokumenLainnya = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=1&ktg=dokumen_lainnya_file".$idr."_&file=".$rsm['dokumen_lainnya_file']);
                            echo '<div class="preview-file"><a href="'.$linkDokumenLainnya.'">'.str_replace("_", "", $rsm['dokumen_lainnya_file']).'</a>
                                    <span class="dokumen_lainnya_file_del"><i class="fa fa-times"></i></span>
                                  </div>';
                        } else{
                            echo '<div class="form-group">
                                    <label class="sr-only"></label>
                                    <div class="input-group">
                                        <input type="file" name="dokumen_lainnya_file" id="dokumen_lainnya_file" class="form-control-file" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-info btn-flat file-upload" data-file="dokumen_lainnya_file">
                                            <i class="fa fa-upload jarak-kanan"></i> Upload</button>
                                        </span>
                                    </div>
                                    <div class="info-status hide"></div>
                                  </div>';
                        }
                    ?>
                    </div>
                    <p style="font-size:12px; margin:-10px 0px 10px;">* Max size 2MB</p>
                </div>
            </div>

            <hr style="margin:10px 0px; border-top:4px double #ddd;" />
            <p style="font-size:12px; margin:0px;">** Please upload your files before saving data</p>
            <p style="font-size:12px; margin:0;">** Allowed file extension .jpg, .jpeg, .png, .zip, .pdf, .rar</p>
		</div>

	</div>
</fieldset>