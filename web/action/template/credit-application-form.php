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

.c1{
	border-top: 1px dotted #000;
}
.c2{
	border-right: 1px dotted #000;
}
.c3{
	border-bottom: 1px dotted #000;
}
.c4{
	border-left: 1px dotted #000;
}

.text-justify{
	text-align: justify;
}
.text-left{
	text-align: left;
}
.text-center{
	text-align: center;
}
.text-right{
	text-align: right;
}

.table_display{
	 display: table; 
	 width: 100%;
}
.table_row{
	 display: table-row; 
}
.table_cell{
	display: table-cell;
	float: left;
}

.main_padding{
	padding: 3px 5px;
}
.soalnya_padding{
	padding: 3px 10px 3px 5px;
}
.title_section{
	 padding: 10px;  
	 font-size: 14px; 
}

.table_review{
	margin: 0px 10px 10px 0px; 
	page-break-inside: avoid;
}
.table_review td{
	font-family: arial;
	font-size: 8pt;
	vertical-align: top;
}
.table_review td.kotak_isian{
	height: 20px; 
	border: 1px solid #000; 
}

.table_approval, .table_cl{
	page-break-inside: avoid;
}
.table_approval td{
	font-family: arial;
	font-size: 8pt;
	vertical-align: top;
}
.table_cl td{
	font-family: arial;
	font-size: 8pt;
	font-weight: bold;
	vertical-align: middle;
}
</style>
<div style="background-color:#c6e0b3; border:1px solid #343399; padding:5px; margin-bottom:3px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td class="text-left" width="20%"><img src="<?php echo BASE_IMAGE."/logo-kiri-penawaran.png"; ?>" style="width:90px;" /></td>
            <td class="text-center" width=""><h4 style="font-family:arial; font-size:14pt;"><b>CREDIT APPROVAL FORM</b></h4></td>
            <td class="text-right" width="20%"><img src="<?php echo BASE_IMAGE."/logo-kanan-penawaran.png"; ?>" style="width:130px;" /></td>
        </tr>
    </table>
</div>

<div style="border:1px solid #343399;">
    <div class="table_display">
        <div class="table_row">
            <div class="table_cell main_padding" style="width:30%; background-color:#343399; color:#fff;">
                <b>1. Customer Information</b>
            </div>
            <div class="table_cell main_padding">&nbsp;</div>
        </div>
    </div>
</div>
<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
	<div style="margin-bottom:5px;"></div>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left soalnya_padding" width="150">
                <b>Company Name</b>
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $rsm['nama_customer'];?>
            </td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left soalnya_padding" width="150">
                <b>Address</b>
            </td>
            <td class="text-justify main_padding kotak_isian">
            	<?php echo $alamat;?>
            </td>
        </tr>
    </table>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-left soalnya_padding" width="150">
                <b>Type of Submission <br /><small>(please tick)</small></b>
            </td>
            <td class="text-justify main_padding b1 b3 b4" width="90">New customer</td>
            <td class="text-justify main_padding b1 b2 b3 b4" width="25">&nbsp;</td>
            <td class="text-justify main_padding" width="">&nbsp;</td>

            <td class="text-justify main_padding b1 b3 b4" width="90">Re-Activated</td>
            <td class="text-justify main_padding b1 b2 b3 b4" width="25">&nbsp;</td>
            <td class="text-justify main_padding" width="">&nbsp;</td>

            <td class="text-justify main_padding b1 b3 b4" width="90">Add TOP</td>
            <td class="text-justify main_padding b1 b2 b3 b4" width="25">&nbsp;</td>
            <td class="text-justify main_padding" width="">&nbsp;</td>

            <td class="text-justify main_padding b1 b3 b4" width="90">Add CL</td>
            <td class="text-justify main_padding b1 b2 b3 b4" width="25">&nbsp;</td>
        </tr>
    </table>
</div>

<div style="border:1px solid #343399;">
    <div class="table_display">
        <div class="table_row">
            <div class="table_cell main_padding" style="width:30%; background-color:#343399; color:#fff;">
                <b>2. Financial Review</b>
            </div>
            <div class="table_cell main_padding">&nbsp;</div>
        </div>
    </div>
</div>
<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
	<div style="margin-bottom:5px;"></div>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_review">
        <tr>
            <td class="text-justify main_padding" style="height:350px;"><?php echo $rsm['finance_summary'];?></td>
        </tr>
    </table>
</div>

<div style="border:1px solid #343399;">
    <div class="table_display">
        <div class="table_row">
            <div class="table_cell main_padding" style="width:30%; background-color:#343399; color:#fff;">
                <b>3. Credit Limit &amp; TOP</b>
            </div>
            <div class="table_cell main_padding">&nbsp;</div>
        </div>
    </div>
</div>
<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_cl" style="margin:8px 10px;">
        <tr>
            <td class="text-center main_padding b1 b3 b4" width="80" style="background-color:#ededed;">Product</td>
            <td class="text-center main_padding b1 b3 b4" width="100" style="background-color:#ededed;">Volume</td>
            <td class="text-center main_padding b1 b3 b4" width="" style="background-color:#ededed;">Unit</td>
            <td class="text-center main_padding b1 b3 b4" width="120" style="background-color:#ededed;">Existing Limit</td>
            <td class="text-center main_padding b1 b3 b4" width="120" style="background-color:#ededed;">Actual Payment</td>
            <td class="text-center main_padding b1 b2 b3 b4" width="120" style="background-color:#ededed;">Guarantee</td>
        </tr>
        <tr>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">HSD</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">KL</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b2 b3 b4" style="background-color:#ffff00;">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">MFO</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">KL</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b2 b3 b4" style="background-color:#ffff00;">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">LUBRICANT</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">Drum/Pail/Bottle/Cartont</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b2 b3 b4" style="background-color:#ffff00;">&nbsp;</td>
        </tr>
	</table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_cl" style="margin:8px 10px;">
        <tr>
            <td class="text-center main_padding b1 b3 b4" rowspan="2" style="background-color:#ededed;">Product</td>
            <td class="text-center main_padding b1 b3 b4" colspan="2" style="background-color:#ededed;">Credit Limit</td>
            <td class="text-center main_padding b1 b3 b4" colspan="2" style="background-color:#ededed;">TOP</td>
            <td class="text-center main_padding b1 b2 b3 b4" rowspan="2" style="background-color:#ededed;">Notes</td>
        </tr>
        <tr>
            <td class="text-center main_padding b3 b4" style="background-color:#ededed;">Request</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ededed;">Approval</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ededed;">Request</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ededed;">Approval</td>
        </tr>
        <tr>
            <td class="text-center main_padding b3 b4" width="80" style="background-color:#ffff00;">HSD</td>
            <td class="text-center main_padding b3 b4" width="100" style="background-color:#ffff00;"><?php echo number_format($rsm['credit_limit_diajukan']);?></td>
            <td class="text-center main_padding b3 b4" width="100" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b3 b4" width="100" style="background-color:#ffff00;"><?php echo $rsm['top_payment'];?></td>
            <td class="text-center main_padding b3 b4" width="100" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b2 b3 b4" width="" style="background-color:#ffff00;">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">MFO</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b2 b3 b4" style="background-color:#ffff00;">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">LUBRICANT</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b3 b4" style="background-color:#ffff00;">&nbsp;</td>
            <td class="text-center main_padding b2 b3 b4" style="background-color:#ffff00;">&nbsp;</td>
        </tr>
	</table>
</div>

<pagebreak>
<div style="background-color:#c6e0b3; border:1px solid #343399; padding:5px; margin-bottom:3px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td class="text-left" width="20%"><img src="<?php echo BASE_IMAGE."/logo-kiri-penawaran.png"; ?>" style="width:90px;" /></td>
            <td class="text-center" width=""><h4 style="font-family:arial; font-size:14pt;"><b>CREDIT APPROVAL FORM</b></h4></td>
            <td class="text-right" width="20%"><img src="<?php echo BASE_IMAGE."/logo-kanan-penawaran.png"; ?>" style="width:130px;" /></td>
        </tr>
    </table>
</div>

<div style="border:1px solid #343399;">
    <div class="table_display">
        <div class="table_row">
            <div class="table_cell main_padding" style="width:30%; background-color:#343399; color:#fff;">
                <b>4. Credit Committe Approval</b>
            </div>
            <div class="table_cell main_padding">&nbsp;</div>
        </div>
    </div>
</div>
<div style="border:1px solid #343399; border-top:0px; margin-bottom:10px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_approval" style="margin:8px 10px;">
        <tr>
            <td class="text-center main_padding b1 b2 b3 b4">Presented by,</td>
            <td class="text-center main_padding" width="">&nbsp;</td>
            <td class="text-center main_padding b1 b3 b4" colspan="2">Checked by,</td>
            <td class="text-center main_padding b1 b3 b4">Approval 1</td>
            <td class="text-center main_padding b1 b3 b4">Approval 2</td>
            <td class="text-center main_padding b1 b3 b4">Approval 3</td>
            <td class="text-center main_padding b1 b2 b3 b4">Approval 4</td>
        </tr>
        <tr>
            <td class="text-center main_padding b2 b3 b4" width="14%" height="55">&nbsp;</td>
            <td class="text-center main_padding" width="">&nbsp;</td>
            <td class="text-center main_padding b3 b4" width="14%">&nbsp;</td>
            <td class="text-center main_padding b3 b4" width="14%">&nbsp;</td>
            <td class="text-center main_padding b3 b4" width="14%">&nbsp;</td>
            <td class="text-center main_padding b3 b4" width="14%">&nbsp;</td>
            <td class="text-center main_padding b3 b4" width="14%">&nbsp;</td>
            <td class="text-center main_padding b2 b3 b4" width="14%">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-center main_padding b2 b3 b4">&nbsp;</td>
            <td class="text-center main_padding" width="">&nbsp;</td>
            <td class="text-center main_padding b3 b4">&nbsp;</td>
            <td class="text-center main_padding b3 b4">&nbsp;</td>
            <td class="text-center main_padding b3 b4">&nbsp;</td>
            <td class="text-center main_padding b3 b4">&nbsp;</td>
            <td class="text-center main_padding b3 b4">&nbsp;</td>
            <td class="text-center main_padding b2 b3 b4">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-center main_padding b2 b3 b4">Co. Head</td>
            <td class="text-center main_padding" width="">&nbsp;</td>
            <td class="text-center main_padding b3 b4">Controller &amp; Audit Internal</td>
            <td class="text-center main_padding b3 b4">Finance Manager</td>
            <td class="text-center main_padding b3 b4">COO</td>
            <td class="text-center main_padding b3 b4">CFO</td>
            <td class="text-center main_padding b3 b4">CEO</td>
            <td class="text-center main_padding b2 b3 b4">Commissioner</td>
        </tr>
    </table>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table_approval" style="margin:8px 10px;">
        <tr>
            <td class="text-left main_padding c1 c2 c3 c4" width="70%" height="70">Controller &amp; Audit Internal Notes :</td>
            <td class="text-center main_padding" width="">&nbsp;</td>
            <td class="text-left" width="28%" rowspan="6">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="text-left main_padding b1 b2 b3 b4" height="280" colspan="2"><b><u>General Notes :</u></b></td>
                    </tr>
                    <tr>
                        <td class="text-left main_padding" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="text-center main_padding b1 b3 b4" width="50%"><b>Proposed by,</b></td>
                        <td class="text-center main_padding b1 b2 b3 b4" width="50%"><b>Review by,</b></td>
                    </tr>
                    <tr>
                        <td class="text-center main_padding b3 b4" height="55">&nbsp;</td>
                        <td class="text-center main_padding b2 b3 b4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="text-center main_padding b3 b4">&nbsp;</td>
                        <td class="text-center main_padding b2 b3 b4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="text-center main_padding b3 b4">Marketing</td>
                        <td class="text-center main_padding b2 b3 b4">Administration</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="text-left main_padding c2 c3 c4" height="70">Finance Manager Notes :</td>
            <td class="text-center main_padding" width="">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-left main_padding c2 c3 c4" height="70">COO Notes :</td>
            <td class="text-center main_padding" width="">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-left main_padding c2 c3 c4" height="70">CFO Notes :</td>
            <td class="text-center main_padding" width="">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-left main_padding c2 c3 c4" height="70">CEO Notes :</td>
            <td class="text-center main_padding" width="">&nbsp;</td>
        </tr>
        <tr>
            <td class="text-left main_padding c2 c3 c4" height="70">Commissioner Notes :</td>
            <td class="text-center main_padding" width="">&nbsp;</td>
        </tr>
    </table>
</div>
