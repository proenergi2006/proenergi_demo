<style>
.table-data td{
	font-family: helvetica;
	font-size: 9pt;
	vertical-align: top;
}
.no-border td{
	font-family: helvetica;
	font-size: 9pt;
}
p{
	margin: 0 0 10px;
	text-align: justify;
	font-family: helvetica;
	font-size: 9pt;
}
.b1{
	border-top: 1px solid #000;
}
.b2{
	border-right: 1px solid #000;
}
.b3{
	border-bottom: 1px solid #000;
}
.b4{
	border-left: 1px solid #000;
}
p.form-title{
	 font-size: 10pt;
	 font-weight: bold;
}
h3.box-title {
	 font-size: 12pt;
	 font-weight: bold;
	 margin: 0px;
	 padding: 5px;
}
.text-center{
	text-align: center;
}
.text-right{
	text-align: right;
}
.bg-black{
	background-color: #000;
	color: #fff;
}
</style>
<htmlpageheader name="myHTMLHeader1">
    <table width="100%" border="1" cellpadding="0" cellspacing="0">
    	<tr>
            <td width="20%" align="left"><img src="<?php echo BASE_IMAGE."/logo-kiri-penawaran.png"; ?>" width="15%" /></td>
            <td width="80%" align="center"><p style="font-size:14pt;"><b>APPLICATION FORM PROSPECTIVE CUSTOMERS</b></p>
            <p style="font-size:10pt;"><b><?php echo $inisial_cabang.'/LC'.str_pad($rsm['id_verification'],4,'0',STR_PAD_LEFT);?></b></p>
            </td>
		</tr>
	</table>
</htmlpageheader>
<sethtmlpageheader name="myHTMLHeader1" page="ALL" value="on" show-this-page="1" />
<htmlpagefooter name="myHTMLFooter1">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="20%" align="left" style="font-size:6pt;">Hal. {PAGENO} / {nbpg}</td>
        <td width="80%" align="right" style="font-size:6pt;">Printed by <?php echo $printe;?></td>
    </tr>
</table>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
<div style="padding-top: 50px;">
    <div class="text-center bg-black">
        <h3 class="box-title"><b>COMPANY DATA</b></h3>
    </div>
    <div style="border: 1px solid #000; padding:5px; margin:0px 0px 10px;">
        <table class="table-data" border="0" width="100%">
            <tr>
                <td colspan="4" style="padding:10px 0px;"><p class="form-title"><u>I. COMPANY DETAILS</u></p></td>
            </tr>
            <tr>
                <td colspan="2">COMPANY NAME</td>
                <td class="text-center">:</td>
                <td><?php echo $rsm['nama_customer'];?></td>
            </tr>
            <tr>
                <td width="10%">ADDRESS</td>
                <td width="15%"><b><i>Road</i></b></td>
                <td width="3%" class="text-center">:</td>
                <td width="72%"><?php echo $rsm['alamat_customer'];?></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><span><b><i>Province</i></b></span></td>
                <td class="text-center">:</td>
                <td><?php echo $rsm['propinsi_customer'];?></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><span><b><i>City</i></b></span></td>
                <td class="text-center">:</td>
                <td><?php echo $tmp_addr1;?></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><span><b><i>Postal Code</i></b></span></td>
                <td class="text-center">:</td>
                <td><?php echo $rsm['postalcode_customer'];?></td>
            </tr>
            <tr>
                <td colspan="2">TELEPHONE</td>
                <td class="text-center">:</td>
                <td><?php echo $rsm['telp_customer'];?></td>
            </tr>
            <tr>
                <td colspan="2">FAX</td>
                <td class="text-center">:</td>
                <td><?php echo $rsm['fax_customer'];?></td>
            </tr>
            <tr>
                <td colspan="2">EMAIL</td>
                <td class="text-center">:</td>
                <td><?php echo $rsm['email_customer'];?></td>
            </tr>
            <tr>
                <td colspan="2">WEBSITE</td>
                <td class="text-center">:</td>
                <td><?php echo $rsm['website_customer'];?></td>
            </tr>
        </table>

        <table class="table-data" width="100%" border="0">
            <tr>
                <td colspan="2" style="padding:20px 0px 10px;"><p class="form-title"><u>II. TYPE OF BUSINESS</u></p></td>
                <td colspan="2" style="padding:20px 0px 10px;"><p class="form-title"><u>III. OWNERSHIP</u></p></td>
            </tr>
            <tr>
                <td width="10%" class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 1?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td width="40%">Agriculture &amp; Forestry / Horticulture</td>
                <td width="10%" class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 1?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td width="40%">Affiliation</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 2?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Business &amp; Information</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 2?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>National Private</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 3?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Construction/Utilities/Contracting</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 3?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Foreign Private</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 4?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Education</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 4?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Joint Venture</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 5?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Finance &amp; Insurance</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 5?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>BUMN/BUMD</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 6?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Food &amp; hospitally</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 6?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Foundation</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 7?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Gaming</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 7?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Personal</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 8?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Health Services</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['ownership'] == 8?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Other (Specify): <?php echo $rsm['ownership_lain']; ?></td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 9?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Motor Vehicle</td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 11?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Natural Resources / Environmental</td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 12?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Personal Service</td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 13?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Manufacture</td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['tipe_bisnis'] == 10?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Other (Specify): <?php echo $rsm['tipe_bisnis_lain']; ?></td>
                <td colspan="2">&nbsp;</td>
            </tr>
        </table>

        <table class="table-data" width="100%" border="0">
            <tr>
                <td colspan="3" style="padding:10px 0px;">
                	<p class="form-title"><u>IV. DOCUMENTATION (Include a copy of this following documents with the delivery of this form)</u></p>
    			</td>
            </tr>
            <tr>
                <td width="22%">CERTIFICATE NUMBER <i>(Akta Pendirian)</i></td>
                <td width="3%" class="text-center">:</td>
                <td width="75%"><?php echo '<p>'.$rsm['nomor_sertifikat'].'</p>';?></td>
            </tr>
            <tr>
                <td>NPWP NUMBER</td>
                <td class="text-center">:</td>
                <td><?php echo '<p>'.$rsm['nomor_npwp'].'</p>';?></td>
            </tr>
            <tr>
                <td>SIUP NUMBER</td>
                <td class="text-center">:</td>
                <td><?php echo '<p>'.$rsm['nomor_siup'].'</p>';?></td>
            </tr>
            <tr>
                <td>TDP NUMBER</td>
                <td class="text-center">:</td>
                <td><?php echo '<p>'.$rsm['nomor_tdp'].'</p>';?></td>
            </tr>
        </table>
        
        <table class="table-data" width="100%" border="0">
            <tr>
                <td colspan="6" style="padding:10px 0px;"><p class="form-title"><u>V. PERSON IN CHARGE</u></p></td>
            </tr>
            <tr>
                <td colspan="3" style="padding:10px 0px;"><b>1. Decision Makers</b></td>
                <td colspan="3" style="padding:10px 0px;"><b>2. Ordering Goods</b></td>
            </tr>
            <tr>
                <td width="12%">Name</td>
                <td width="3%">:</td>
                <td width="35%"><?php echo $rsm['pic_decision_name'];?></td>
                <td width="12%">Name</td>
                <td width="3%">:</td>
                <td width="35%"><?php echo $rsm['pic_ordering_name'];?></td>
            </tr>
            <tr>
                <td>Position</td>
                <td>:</td>
                <td><?php echo $rsm['pic_decision_position'];?></td>
                <td>Position</td>
                <td>:</td>
                <td><?php echo $rsm['pic_ordering_position'];?></td>
            </tr>
            <tr>
                <td>Telephone</td>
                <td>:</td>
                <td><?php echo $rsm['pic_decision_telp'];?></td>
                <td>Telephone</td>
                <td>:</td>
                <td><?php echo $rsm['pic_ordering_telp'];?></td>
            </tr>
            <tr>
                <td>Mobile</td>
                <td>:</td>
                <td><?php echo $rsm['pic_decision_mobile'];?></td>
                <td>Mobile</td>
                <td>:</td>
                <td><?php echo $rsm['pic_ordering_mobile'];?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td>:</td>
                <td><?php echo $rsm['pic_decision_email'];?></td>
                <td>Email</td>
                <td>:</td>
                <td><?php echo $rsm['pic_ordering_email'];?></td>
            </tr>
            <tr>
                <td colspan="3" style="padding:10px 0px;"><b>3. Billing Receiver</b></td>
                <td colspan="3" style="padding:10px 0px;"><b>4. Invoice Payment</b></td>
            </tr>
            <tr>
                <td>Name</td>
                <td>:</td>
                <td><?php echo $rsm['pic_billing_name'];?></td>
                <td>Name</td>
                <td>:</td>
                <td><?php echo $rsm['pic_invoice_name'];?></td>
            </tr>
            <tr>
                <td>Position</td>
                <td>:</td>
                <td><?php echo $rsm['pic_billing_position'];?></td>
                <td>Position</td>
                <td>:</td>
                <td><?php echo $rsm['pic_invoice_position'];?></td>
            </tr>
            <tr>
                <td>Telephone</td>
                <td>:</td>
                <td><?php echo $rsm['pic_billing_telp'];?></td>
                <td>Telephone</td>
                <td>:</td>
                <td><?php echo $rsm['pic_invoice_telp'];?></td>
            </tr>
            <tr>
                <td>Mobile</td>
                <td>:</td>
                <td><?php echo $rsm['pic_billing_mobile'];?></td>
                <td>Mobile</td>
                <td>:</td>
                <td><?php echo $rsm['pic_invoice_mobile'];?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td>:</td>
                <td><?php echo $rsm['pic_billing_email'];?></td>
                <td>Email</td>
                <td>:</td>
                <td><?php echo $rsm['pic_invoice_email'];?></td>
            </tr>
        </table>
    </div>
    <div class="text-center bg-black">
        <h3 class="box-title"><b>PAYMENT</b></h3>
    </div>
    <div style="border: 1px solid #000; padding:5px; margin:0px 0px 10px;">
        <table class="table-data" width="100%" border="0">
            <tr>
                <td colspan="4" style="padding:10px 0px;"><p class="form-title"><u>I. BILLING ADDRESS</u></p></td>
            </tr>
            <tr>
                <td width="10%">ADDRESS</td>
                <td width="15%"><b><i>Road</i></b></td>
                <td width="3%" class="text-center">:</td>
                <td width="72%"><?php echo $rsm['alamat_billing'];?></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><span><b><i>Province</i></b></span></td>
                <td class="text-center">:</td>
                <td><?php echo $rsm['propinsi_payment'];?></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><span><b><i>City</i></b></span></td>
                <td class="text-center">:</td>
                <td><?php echo $tmp_addr2;?></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><span><b><i>Postal Code</i></b></span></td>
                <td class="text-center">:</td>
                <td><?php echo $rsm['postalcode_billing'];?></td>
            </tr>
            <tr>
                <td colspan="2">TELEPHONE</td>
                <td class="text-center">:</td>
                <td><?php echo $rsm['telp_billing'];?></td>
            </tr>
            <tr>
                <td colspan="2">FAX</td>
                <td class="text-center">:</td>
                <td><?php echo $rsm['fax_billing'];?></td>
            </tr>
            <tr>
                <td colspan="2">EMAIL</td>
                <td class="text-center">:</td>
                <td><?php echo $rsm['email_billing'];?></td>
            </tr>
        </table>

        <table class="table-data" width="100%" border="0">
            <tr>
                <td colspan="4" style="padding:10px 0px;"><p class="form-title"><u>II. CASHIER AND PAYMENT SCHEDULE</u></p></td>
            </tr>
            <tr>
                <td width="10%" class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['payment_schedule'] == 1?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td width="20%">EVERY DAY</td>
                <td width="7%" class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['payment_schedule'] == 2?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td width="63%">Other (Specify): <?php echo $rsm['payment_schedule_other']; ?></td>
            </tr>
        </table>

        <table class="table-data" width="100%" border="0">
            <tr>
                <td colspan="6" style="padding:10px 0px;"><p class="form-title"><u>III. PAYMENT METHOD</u></p></td>
            </tr>
            <tr>
                <td width="10%" class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['payment_method'] == 1?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td width="20%">CASH</td>
                <td width="7%" class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['payment_method'] == 2?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td width="20%">TRANSFER</td>
                <td width="7%" class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['payment_method'] == 5?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td width="36%">Other (Specify): <?php echo $rsm['payment_method_other']; ?></td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['payment_method'] == 3?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>CHEQUE/GIRO</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['payment_method'] == 4?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>BANK GUARANTEE</td>
                <td colspan="2">&nbsp;</td>
            </tr>
        </table>

        <table class="table-data" width="100%" border="0">
            <tr>
                <td colspan="2" style="padding:10px 0px;"><p class="form-title"><u>IV. INVOICES</u></p></td>
            </tr>
            <tr>
                <td width="10%" class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['invoice']?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td width="90%">Tax Invoice (Faktur Pajak)</td>
            </tr>
        </table>

        <table class="table-data" width="100%" border="0">
            <tr>
                <td colspan="8" style="padding:10px 0px;"><p class="form-title"><u>V. PAYMENT PROPOSED</u></p></td>
            </tr>
            <tr>
                <td width="4%">&nbsp;</td>
                <td colspan="2"><b>Payment Type</b></td>
                <td width="17%">&nbsp;</td>
                <td width="4%">&nbsp;</td>
                <td width="29%"><b>TOP (Term of Payment)</b></td>
                <td width="4%">&nbsp;</td>
                <td width="29%"><b>Conditions</b></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3"><div class="form-control"><?php echo $arrTermPayment[$rsm['jenis_payment']];?></div></td>
                <td>&nbsp;</td>
                <td><div class="form-control"><?php echo ($rsm['jenis_payment']=='CREDIT')?$rsm['top_payment'].' days':'&nbsp;';?></div></td>
                <td>&nbsp;</td>
                <td><div class="form-control"><?php echo ($rsm['jenis_payment']=='CREDIT')?$arrConditionEng[$rsm['jenis_net']]:'';?></div></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td width="10%"><b>Note</b></td>
                <td width="3%">:</td>
                <td colspan="5"><?php echo $rsm['ket_extra'];?></td>
            </tr>
        </table>

        <?php /*
        <table class="table-data" width="100%" border="0">
            <tr>
                <td colspan="2" style="padding:10px 0px;"><p class="form-title"><u>VI. CREDIT LIMIT</u></p></td>
            </tr>
            <tr>
                <td width="4%">&nbsp;</td>
                <td width="96%"><b>CREDIT LIMIT PROPOSED</b></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><div class="form-control text-right"><?php echo number_format($rsm['credit_limit_diajukan']);?></div></td>
            </tr>
        </table>
    	*/ ?>
    </div>
    <div class="text-center bg-black">
        <h3 class="box-title"><b>LOGISTICS</b></h3>
    </div>
    <div style="border: 1px solid #000; padding:5px; margin:0px 0px 10px;">
        <table class="table-data" width="100%" border="0">
            <tr>
                <td colspan="3" style="padding:10px 0px;"><p class="form-title"><u>1. LOCATION DETAIL</u></p></td>
            </tr>
            <tr>
                <td width="20%">AREA <i>(luas lokasi)</i></td>
                <td width="3%" class="text-center">:</td>
              	<td width="77%"><?php echo $rsm['logistik_area'];?></td>
            </tr>
    	</table>

        <table class="table-data" width="100%" border="0">
            <tr>
                <td colspan="2" style="padding:10px 0px 5px;">CONDITIONS AROUND LOCATIONS</td>
                <td>&nbsp;</td>
                <td colspan="2" style="padding:10px 0px 5px;">STORAGE FACILITY</td>
            </tr>
            <tr>
                <td width="6%"><?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_env'] == 1?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?></td>
                <td width="40%">INDUSTRY</td>
                <td width="8%">&nbsp;</td>
                <td width="6%"><?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_storage'] == 1?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?></td>
                <td width="40%">INDOOR</td>
            </tr>
            <tr>
                <td><?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_env'] == 2?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?></td>
                <td>PEMUKIMAN</td>
                <td>&nbsp;</td>
                <td><?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_storage'] == 2?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?></td>
                <td>OUTDOOR</td>
            </tr>
            <tr>
                <td><?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_env'] == 3?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?></td>
                <td>OTHERS</td>
                <td>&nbsp;</td>
                <td><?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_storage'] == 3?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?></td>
                <td>OTHERS</td>
            </tr>
            <tr>
                <td colspan="2" class="b3"><?php echo $rsm['logistik_env_other']; ?></td>
                <td>&nbsp;</td>
                <td colspan="2" class="b3"><?php echo $rsm['logistik_storage_other']; ?></td>
            </tr>
            <tr>
                <td colspan="2" style="padding:10px 0px 0px;">DESCRIPTION OF CONDITION</td>
                <td>&nbsp;</td>
                <td colspan="2" style="padding:10px 0px 0px;">DESCRIPTION OF STORAGE FACILITY</td>
            </tr>
            <tr>
                <td colspan="2" style="height:80px;" class="b1 b2 b3 b4"><?php echo $rsm['desc_condition'];?></td>
                <td>&nbsp;</td>
                <td colspan="2" style="height:80px;" class="b1 b2 b3 b4"><?php echo $rsm['desc_stor_fac'];?></td>
            </tr>
            <tr>
                <td colspan="5" style="padding:10px 0px 0px;">SECURITY ENVIRONMENT / BUSINESS AREA (Explain):</td>
            </tr>
            <tr>
                <td colspan="5" style="height:80px;" class="b1 b2 b3 b4"><?php echo $rsm['logistik_bisnis'];?></td>
            </tr>
        </table>

        <table class="table-data" width="100%" border="0">
            <tr>
                <td colspan="6" style="padding:10px 0px;"><p class="form-title"><u>2. DELIVERY DETAIL</u></p></td>
            </tr>
            <tr>
                <td colspan="6">OPERATING HOURS</td>
            </tr>
            <tr>
                <td width="10%" class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_hour'] == 1?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td width="20%">08.00 - 17.00</td>
                <td width="7%" class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_hour'] == 2?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td width="20%">24 HOURS</td>
                <td width="7%" class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_hour'] == 3?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td width="36%">Other (Specify) : <?php echo $rsm['logistik_hour_other']; ?></td>
            </tr>
            <tr>
                <td colspan="6" style="padding:10px 0px 0px;">VOLUME MEASUREMENT</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_volume'] == 1?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>PRO ENERGY'S TANK LORRY</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_volume'] == 2?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>FLOWMETER</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_volume'] == 3?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Other (Specify) : <?php echo $rsm['logistik_volume_other']; ?></td>
            </tr>
            <tr>
                <td colspan="6" style="padding:10px 0px 0px;">QUALITY CHECKING</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_quality'] == 1?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>DENSITY</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_quality'] == 3?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td colspan="3">Other (Specify) : <?php echo $rsm['logistik_quality_other']; ?></td>
            </tr>
            <tr>
                <td colspan="6" style="padding:10px 0px 0px;">MAX. TRUCK CAPACITY</td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_truck'] == 1?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>5 KL</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_truck'] == 3?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>10 KL</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_truck'] == 5?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>Other (Specify) : <?php echo $rsm['logistik_truck_other']; ?></td>
            </tr>
            <tr>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_truck'] == 2?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>8 KL</td>
                <td class="text-right">
                    <?php echo '<img src="'.BASE_IMAGE.($rsm['logistik_truck'] == 4?"/img_checked.png":"/img_uncheck.png").'" width="5%" />';?>
                </td>
                <td>16 KL</td>
                <td colspan="2">&nbsp;</td>
            </tr>
        </table>
    </div>
</div>
<div style="page-break-inside: avoid;">
	<div class="text-center bg-black"><h3 class="box-title"><b>AGREEMENT</b></h3></div>
	<div style="border: 1px solid #000; padding:5px; margin:0px;">
		<table class="table-data" width="100%" border="0">
			<tr>
				<td width="15%" class="text-right" style="padding:10px 0px;"><?php echo '<img src="'.BASE_IMAGE."/img_checked.png".'" width="5%" />';?></td>
				<td width="85%" style="padding:10px 0px; vertical-align:middle;">I declare that the above data is true (Dengan ini saya menyatakan bahwa data diatas benar adanya)</td>
			</tr>
			<tr>
				<td colspan="2" style="padding:5px 0px;"><?php echo $rsm['propinsi_customer'].', '.date('F d, Y');?></td>
			</tr>        
			<tr>
				<td colspan="2" style="padding:5px 0px;"><?php echo "On Behalf : ".$rsm['nama_customer'];?></td>
			</tr>        
		</table>
	</div>

	<div style="border: 1px solid #000; padding:0px; margin:0px 0px 15px;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="50%" style="height:100px;" class="b3">&nbsp;</td>
				<td width="50%" class="b3 b4">&nbsp;</td>
			</tr>
			<tr>
				<td class="text-center">NAME, SIGNATURE AND COMPANY STAMP</td>
				<td class="text-center b4">PT PRO ENERGI (ACCOUNT EXECUTIVE)</td>
			</tr>        
		</table>
	</div>
	<div style="margin:0; text-align:right;"><barcode code="<?php echo $barcod;?>" type="C39" /></div>
	<p style="margin:0; padding-right:95px; text-align:right; font-size:6pt;"><?php echo $barcod;?></p>
	<p style="margin:0; padding:0 15px 5px; text-align:right; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
</div>