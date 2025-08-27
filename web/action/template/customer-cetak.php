<style>
.tabel_data td{
	padding: 3px 5px;
	vertical-align: middle;
	font-family: arial;
	font-size: 8pt;
}
div{
	font-family: arial;
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

.main_padding{
	display: table-cell;
	float: left;
	padding: 3px 5px;
}
.table_cell{
	display: table-cell;
	float: left;
	padding: 3px 5px;
}
</style>
<div style="background-color:#c6e0b3; border:1px solid #343399; padding:5px; margin-bottom:3px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td class="text-left" width="20%"><img src="<?php echo BASE_IMAGE."/logo-kiri-penawaran.png"; ?>" style="width:90px;" /></td>
            <td class="text-center" width=""><h4 style="font-family:arial; font-size:14pt;"><b>APPLICATION CUSTOMER FORM</b></h4></td>
            <td class="text-right" width="20%"><img src="<?php echo BASE_IMAGE."/logo-kanan-penawaran.png"; ?>" style="width:130px;" /></td>
        </tr>
    </table>
</div>

<div style="border:1px solid #343399;">
    <div style="display:table; width:100%;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:50%; background-color:#343399; color:#fff;">
                <b>1. Corporate Details</b>
            </div>
            <div class="table_cell main_padding">
                <b>* Isi dengan huruf CETAK</b>
            </div>
        </div>
    </div>
</div>
<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>1.1</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                <b>Full Registered Company Name / Nama lengkap perusahaan yang terdaftar</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['nama_customer'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>1.2</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                <b>Holding / Induk Perusahaan</b> <small><i>(Jika ada)</i></small>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['induk_perusahaan'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>1.3</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                <b>Registered Street Address / Alamat Kantor terdaftar</b> <small><i>(NPWP Address)</i></small>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['alamat_billing'];?>
            </div>
        </div>
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Sub-Districts / Kelurahan</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $kelurahan_billing;?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Districts / Kecamatan</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $kecamatan_billing;?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>City / Kota (Kabupaten)</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $tmp_addr2;?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Province / Provinsi</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['propinsi_payment'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Country / Negara</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo "Indonesia";?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Post Code / Kode Pos</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['postalcode_billing'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Telephone Number</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['telp_billing'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Fax Number</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['fax_billing'];?>
            </div>
        </div>
	</div>

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>1.4</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                <b>Address of Head Office / Alamat Kantor Pusat</b> <small><i>(Isi jika alamat tidak sama dengan alamat NPWP)</i></small>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['alamat_customer'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Sub-Districts / Kelurahan</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $kelurahan_customer;?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Districts / Kecamatan</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $kecamatan_customer;?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>City / Kota (Kabupaten)</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $tmp_addr1;?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Province / Provinsi</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['propinsi_customer'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Country / Negara</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo "Indonesia";?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Post Code / Kode Pos</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['postalcode_customer'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Telephone Number</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['telp_customer'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                &nbsp;
            </div>
            <div class="table_cell main_padding" style="width:160px;">
                <b>Fax Number</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['fax_customer'];?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>1.5</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
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

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>1.6</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                <b>Invoice delivery address / Alamat pengiriman Invoice</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $invoice_delivery_addr_primary;?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                <b>PIC Name for received Invoice</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_invoice_name'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                <b>Division / Bagian</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_invoice_position'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                <b>Mobile Number</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_invoice_mobile'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:200px;">
                <b>E-Mail Address</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_invoice_email'];?>
            </div>
        </div>
    </div>
</div>

<div style="border:1px solid #343399;">
    <div style="display:table; width:100%;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:50%; background-color:#343399; color:#fff;">
                <b>2. Person In Charge Details</b>
            </div>
            <div class="table_cell main_padding">
                &nbsp;
            </div>
        </div>
    </div>
</div>
<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
    <div style="display:table; width:100%;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>2.1</b>
            </div>
            <div class="table_cell main_padding">
                <b>Director or Owner / Direktur atau Pemilik Perusahaan</b>
            </div>
        </div>
    </div>
    <div style="display:table; width:100%;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                Full Name / Nama Lengkap
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_decision_name'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                Title / Jabatan
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_decision_position'];?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                Mobile Number / Nomor HP
            </div>
            <div class="table_cell main_padding" style="width:180px;">
                <?php echo $rsm['pic_decision_mobile'];?>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                E-mail
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_decision_email'];?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>2.2</b>
            </div>
            <div class="table_cell main_padding" style="display:table-cell;">
                <b>Procurement / Pembelian</b>
            </div>
        </div>
    </div>
    <div style="display:table; width:100%;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                Full Name / Nama Lengkap
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_ordering_name'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                Title / Jabatan
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_ordering_position'];?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                Mobile Number / Nomor HP
            </div>
            <div class="table_cell main_padding" style="width:180px;">
                <?php echo $rsm['pic_ordering_mobile'];?>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                E-mail
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_ordering_email'];?>
            </div>
        </div>
    </div>


    <div style="display:table; width:100%;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>2.3</b>
            </div>
            <div class="table_cell main_padding">
                <b>Finance</b>
            </div>
        </div>
    </div>
    <div style="display:table; width:100%;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                Full Name / Nama Lengkap
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_billing_name'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                Title / Jabatan
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_billing_position'];?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                Mobile Number / Nomor HP
            </div>
            <div class="table_cell main_padding" style="width:180px;">
                <?php echo $rsm['pic_billing_mobile'];?>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                E-mail
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_billing_email'];?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>2.4</b>
            </div>
            <div class="table_cell main_padding">
                <b>Site / Fuelman PIC</b>
            </div>
        </div>
    </div>
    <div style="display:table; width:100%;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                Full Name / Nama Lengkap
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_fuelman_name'];?>
            </div>
        </div>

        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                Title / Jabatan
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_fuelman_position'];?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                Mobile Number / Nomor HP
            </div>
            <div class="table_cell main_padding" style="width:180px;">
                <?php echo $rsm['pic_fuelman_mobile'];?>
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                E-mail
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['pic_fuelman_email'];?>
            </div>
        </div>
    </div>

</div>

<div style="border:1px solid #343399;">
    <div style="display:table; width:100%;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:50%; background-color:#343399; color:#fff;">
                <b>3. Payment Term &amp; Banking Detail</b>
            </div>
            <div class="table_cell main_padding">
                &nbsp;
            </div>
        </div>
    </div>
</div>
<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>3.1</b>
            </div>
            <div class="table_cell main_padding" style="width:250px;">
                <b>Pricing Method Calculation / Metode Perhitungan harga</b> 
            </div>
            <div class="table_cell main_padding">
                <?php echo ($rsm['calculate_method']==1 ? 'Discount Pricelist' : ($rsm['calculate_method']==2 ? 'Formula MOPS' : ''));?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>3.2</b>
            </div>
            <div class="table_cell main_padding" style="width:250px;">
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

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>3.3</b>
            </div>
            <div class="table_cell main_padding" style="width:250px;">
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

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>3.4</b>
            </div>
            <div class="table_cell main_padding" style="width:250px;">
                <b>Term of Payment / Jangka waktu Pembayaran</b> <i>(if credit)</i> 
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

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>3.5</b>
            </div>
            <div class="table_cell main_padding" style="width:250px;">
                <b>Bank Name / Nama Bank</b> 
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['bank_name'];?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:250px;">
                <b>Curency / Mata Uang</b> 
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['curency'];?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>3.6</b>
            </div>
            <div class="table_cell main_padding" style="width:250px;">
                <b>Bank Address / Alamat Bank</b> 
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['bank_address'];?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>3.7</b>
            </div>
            <div class="table_cell main_padding" style="width:250px;">
                <b>Account Number / Nomor Rekening</b> 
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['account_number'];?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>3.8</b>
            </div>
            <div class="table_cell main_padding" style="width:250px;">
                <b>Have Credit Facility or Bank Loan? / Punya Fasilitas Kredit atau Pinjaman Bank ?</b> 
            </div>
            <div class="table_cell main_padding">
                <?php echo ($rsm['credit_facility'] == 1 ? 'Yes' : 'No');?>
            </div>
        </div>
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>&nbsp;</b>
            </div>
            <div class="table_cell main_padding" style="width:250px;">
                Please provide the creditor(s) who provide the loan / credit facility / Harap menginformasikan nama penyedia fasilitas kredit atau pinjaman tersebut 
            </div>
            <div class="table_cell main_padding">
                <?php echo "asasasa".$rsm['creditor'];?>
            </div>
        </div>
    </div>

</div>

<div style="border:1px solid #343399;">
    <div style="display:table; width:100%;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:50%; background-color:#343399; color:#fff;">
                <b>4. Supply Scheme </b>
            </div>
            <div class="table_cell main_padding">
                &nbsp;
            </div>
        </div>
    </div>
</div>

<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>4.1</b>
            </div>
            <div class="table_cell main_padding" style="width:250px;">
                <b>What are the envisaged supply scheme details ? / Bagaimana skema rincian pasokan yang diharapkan ?</b>
            </div>
            <div class="table_cell main_padding" style="width:90px;">
                <?php echo ($rsm['supply_shceme'] == 1 ? 'Trucking' : 'SPOB / Vessel');?>
            </div>
            <div class="table_cell main_padding" style="width:25px;">
                <b>4.2</b>
            </div>
            <div class="table_cell main_padding" style="width:110px;">
                <b>Specify Product / Jenis Produk</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo $specify_product;?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%; margin-bottom:10px;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>4.3</b>
            </div>
            <div class="table_cell main_padding" style="width:250px;">
                <b>Volume per Month / Jumlah per bulan </b> 
            </div>
            <div class="table_cell main_padding">
                <?php echo $rsm['volume_per_month'];?>
            </div>
        </div>
    </div>

    <div style="display:table; width:100%;">
        <div style="display:table-row">
            <div class="table_cell main_padding" style="width:25px;">
                <b>4.4</b>
            </div>
            <div class="table_cell main_padding" style="width:250px;">
                <b>Operational hour for receiving product on site / Jam operasional penerimaan produk di lokasi site</b> 
            </div>
            <div class="table_cell main_padding" style="width:150px;">
                <?php echo 'From '.$rsm['operational_hour_from'].' To '.$rsm['operational_hour_to'];?>
            </div>
            <div class="table_cell main_padding" style="width:25px;">
                <b>4.5</b>
            </div>
            <div class="table_cell main_padding" style="width:80px;">
                <b>INCO terms</b>
            </div>
            <div class="table_cell main_padding">
                <?php echo ($rsm['nico'] == 1 ? 'Loco' : 'Delivered');?>
            </div>
        </div>
    </div>

</div>

<div style="border:1px solid #343399; padding:0px 5px 10px; margin-bottom:10px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabel_data">
        <tr>
            <td class="text-left" height="30" width="20%" style="">
            	Customer Representative Name / Nama Perwakilan Pelanggan
            </td>
            <td class="text-left" width="30%" style="border-bottom:1px solid #343399;">&nbsp;
            	
            </td>
            <td class="text-left" width="20%" style="">
            	Sales Person Name / Nama Penjual
            </td>
            <td class="text-left" width="" style="border-bottom:1px solid #343399;">&nbsp;
            	
            </td>
        </tr>
        <tr>
            <td class="text-left" height="25">
            	Title / Jabatan
            </td>
            <td class="text-left" style="border-bottom:1px solid #343399;">&nbsp;
            	
            </td>
            <td class="text-left">
            	Title / Jabatan
            </td>
            <td class="text-left" style="border-bottom:1px solid #343399;">&nbsp;
            	
            </td>
        </tr>
        <tr>
            <td class="text-left" height="50" colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-left" height="25">
            	Signature &amp; Stamp / Tandatangan &amp; stamp
            </td>
            <td class="text-left" style="border-bottom:1px solid #343399;">&nbsp;
            	
            </td>
            <td class="text-left">
            	Signature / Tandatangan
            </td>
            <td class="text-left" style="border-bottom:1px solid #343399;">&nbsp;
            	
            </td>
        </tr>
        <tr>
            <td class="text-left" height="25">
            	Date / Tanggal
            </td>
            <td class="text-left" style="border-bottom:1px solid #343399;">&nbsp;
            	
            </td>
            <td class="text-left">
            	Date / Tanggal
            </td>
            <td class="text-left" style="border-bottom:1px solid #343399;">&nbsp;
            	
            </td>
        </tr>
    </table>
</div>
