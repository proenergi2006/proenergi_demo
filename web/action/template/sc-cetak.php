<style>
 .header{
  font-size:16.0pt;
  font-weight:700;
  font-style:normal;
  /*text-decoration:none;*/
  font-family:Calibri, sans-serif;
  text-align: right;
  /*vertical-align:middle;*/
  /*white-space:nowrap;*/
  width: 465px;
  display: inline-block;
  padding-bottom: 30px;
  float: left;
}

.logo {
  float : right;
  height : 52px; 
  width : 95px; 
}

#project {
  width: 700px;
  padding-bottom: 20px;
}


span {
    padding:0px;
    font-size:9.0pt;
    font-weight:700;
    font-style:normal;
    text-decoration:none;
    font-family:Calibri, sans-serif;
    text-align:left;
    vertical-align:middle;
    text-align: left;
    width: 102px;
    margin-right: 10px;
    display: inline-block;
}

h1 {
  color: #5D6975;
  font-size: 2.4em;
  line-height: 1.4em;
  font-weight: normal;
  text-align: center;
  margin: 0 0 20px 0;
}

body {
  width: 21cm;  
  height: 700px !important; 
  margin: 0 auto; 
  background: #FFFFFF; 
  font-family:Calibri, sans-serif;
}

.date_periode {
  float: left;
  width: 200px;
}

header {
  padding: 10px 0;
  margin-bottom: 30px;
}

#company {
  float: right;
  text-align: right;
}

.balance{
  font-size:9.0pt;
  font-weight:700;
  width: 360px;
  text-align: right;
}

.table {
  width: 800px;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 20px;
}

.table th {
  color: black;
  font-size:9.0pt;
  border-top:.5pt solid windowtext;
  border-right:.5pt solid windowtext;
  border-bottom:.5pt solid windowtext;
  border-left:.5pt solid windowtext;
  background:#C5D9F1;
  /*padding: 5px 20px;*/
}

.table th,
.table td {
  text-align: center;
}

.table td {
  color: black;
  font-size:9.0pt;
  border-top:.5pt solid windowtext;
  border-right:.5pt solid windowtext;
  border-bottom:.5pt solid windowtext;
  border-left:.5pt solid windowtext;
  /*padding: 5px 20px;*/
  height: 40;      
}

.table2 {
  width: 800px;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 5px;
}

.table2 th {
  color: black;
  font-size:9.0pt;
  border-top:.5pt solid windowtext;
  border-right:.5pt solid windowtext;
  border-bottom:.5pt solid windowtext;
  border-left:.5pt solid windowtext;
  background:#C5D9F1;
  text-align: left;
  /*padding: 5px 20px;*/
}

.table2 td {
  color: black;
  font-size:9.0pt;
  border-top:.5pt solid windowtext;
  border-right:.5pt solid windowtext;
  border-bottom:.5pt solid windowtext;
  border-left:.5pt solid windowtext;
  /*padding: 5px 20px;*/
  height: 60;
  vertical-align: top;      
}

.table3 {
  width: 800px;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 5px;
}

.table3 th {
  color: black;
  font-size:9.0pt;
  border-top:.5pt solid windowtext;
  border-right:.5pt solid windowtext;
  border-bottom:.5pt solid windowtext;
  border-left:.5pt solid windowtext;
  background:#D9D9D9;
  text-align: center;
  /*padding: 5px 20px;*/
}

.table3 td {
  color: black;
  font-size:9.0pt;
  border-top:.5pt solid windowtext;
  border-right:.5pt solid windowtext;
  border-bottom:.5pt solid windowtext;
  border-left:.5pt solid windowtext;
  /*padding: 5px 20px;*/
  /*vertical-align: top;      */
  text-align: center;
}

.border {
  border-top:.5pt solid windowtext;
  border-right:.5pt solid windowtext;
  border-bottom:.5pt solid windowtext;
  border-left:.5pt solid windowtext;
  height: auto;  
  color: black;
  font-size:9.0pt;  
  font-weight: 700;  
  /* padding: 2px 10px 2px 10px; */
}

p {
  font-size:10.0pt;
}

</style>
<!DOCTYPE html>
<htmlpagefooter name="myHTMLFooter1">
  <!-- Created by Alvin -->
  <p style="margin:0; text-align:right;"><barcode code="<?php echo $barcod;?>" type="C39" size="0.8" /></p>
  <p style="margin:0; text-align:right; font-size:6pt; padding-right:70px;"><?php echo $barcod;?></p>
  <p style="margin:0; text-align:right; font-size:7pt;"><i>(This form is valid with sign by computerized system)</i></p>
  <p style="margin:0; text-align:right; font-size:6pt;">Printed by <?php echo $printe;?></p>
  <!--  -->
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
<div class="clearfix">
  <div class="header">SALES CONFIRMATION</div>
  <div class="logo">
      <img src="<?php echo BASE_IMAGE."/proenergi.png"; ?>" alt="proenergil(logo)" v:shapes="Picture_x0020_20">
  </div>
  <div id="project">
    <div class="date_peiode" style="width:330px; float: left">
      <span>DATE/PERIODE  </span>:
      <span><?php echo tgl_indo($row['period_date']); ?></span> 
    </div>
    <div class="date_peiode" style="width:330px;">
      <span style="padding-left: 130px">SUPPLY DATE </span>:
      <span><?php echo tgl_indo($row['supply_date']); ?></span>
      
    </div>
  </div>
  
</div>
<main>
  <table class="table">
    <thead>
      <tr>
        <th width="130">CUSTOMER CODE</th>
        <th>CUSTOMER NAME</th>
        <th width="50">TOP</th>
        <th width="100">CREDIT LIMIT</th>
        <th width="150">BUSINESS</th>
        <th width="100">MARKETING</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td ><?php echo $row['kode_pelanggan']; ?></td>
        <td ><?php echo $row['nama_customer']; ?></td>
        <td ><?php echo $row['top_payment']; ?></td>
        <td ><?php echo number_format($row['credit_limit'],0,",","."); ?></td>
        <td ><?php echo $arrTipeBisnis[$row['tipe_bisnis']]; ?></td>
        <td ><?php echo $row['marketing']; ?></td>
      </tr>
    </tbody>
  </table>

  <div class="balance">BALANCE AR</div>
  <table class="table">
    <thead>
      <tr>
        <th width="130">NOT YET</th>
        <th width="100">OVERDUE 1-30 DAYS</th>
        <th width="100">OVERDUE 31-60 DAYS</th>
        <th width="100">OVERDUE 61-90 DAYS</th>
        <th width="150">OVERDUE > 90 DAYS</th>
        <th width="100">REMINDING</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td ><?php echo number_format($row['not_yet'],0,",","."); ?></td>
        <td ><?php echo number_format($row['ov_under_30'],0,",","."); ?></td>
        <td ><?php echo number_format($row['ov_under_60'],0,",","."); ?></td>
        <td ><?php echo number_format($row['ov_under_90'],0,",","."); ?></td>
        <td ><?php echo number_format($row['ov_up_90'],0,",","."); ?></td>
        <td><?php echo number_format($row['reminding'],0,",","."); ?></td>
      </tr>
    </tbody>
  </table>

  <div>
    <div style="width: 330px;float: left; padding-top: 10px">
      <table width="100%">
        <tr>
          <td class="border" style="width: 20px;"><?php echo ($row['type_customer'] == 'Customer Commitment')?'X':''; ?></td>
          <td colspan="3" style="font-size:9.0pt;">CUSTOMER COMMITMENT</td>
        </tr>
        <tr>
          <td></td>
          <td style="font-size:9.0pt;width: 70px;">DATE</td>
          <td style="font-size:9.0pt;width: 20px;">:</td>
          <td style="font-size:9.0pt;float: right;"><?php echo($row['customer_date'])?date('d/m/Y', strtotime($row['customer_date'])):''; ?></td>
        </tr>
        <tr>
          <td></td>
          <td style="font-size:9.0pt;width: 70px;">AMOUNT</td>
          <td style="font-size:9.0pt;">:</td>
          <td style="font-size:9.0pt; float: right;"> <?php echo ($row['customer_amount'])?number_format($row['customer_amount'],0,",","."):''; ?></td>
        </tr>
      </table>
    </div>
    <div style="width: 330px; float: left; padding-top: 10px;">
      <table width="100%">
        <tr>
          <td class="border" style="width: 20px;"><?php echo ($row['type_customer'] == 'Customer Colleteral')?'X':''; ?></td>
          <td colspan="3" style="font-size:9.0pt;">CUSTOMER COLLETERAL</td>
        </tr>
        <tr>
          <td></td>
          <td style="font-size:9.0pt;width: 70px;">ITEM</td>
          <td style="font-size:9.0pt;width: 20px">:</td>
          <td style="font-size:9.0pt;float: right;"><?php echo ($row3[0]['item'])?$row3[0]['item']:''; ?></td>
        </tr>
        <tr>
          <td></td>
          <td style="font-size:9.0pt;width: 70px;">DATE</td>
          <td style="font-size:9.0pt;">:</td>
          <td style="font-size:9.0pt; float: right;"> <?php echo($row3[0]['date'])?date('d/m/Y', strtotime($row3[0]['date']) ):''; ?></td>
        </tr>
        <tr>
          <td></td>
          <td style="font-size:9.0pt;width: 70px;">AMOUNT</td>
          <td style="font-size:9.0pt;">:</td>
          <td style="font-size:9.0pt; float: right;"> <?php echo ($row3[0]['amount'])?number_format($row3[0]['amount'],0,",","."):''; ?></td>
        </tr>
        <?php 
          if(count($row3) > 1){
              foreach($row3 as $i => $data){
                if($i > 0){
        ?>
            <tr>
              <td></td>
              <td style="font-size:9.0pt;width: 70px;">ITEM</td>
              <td style="font-size:9.0pt;width: 20px">:</td>
              <td style="font-size:9.0pt;float: right;"><?php echo ($data['item'])?$data['item']:''; ?></td>
            </tr>
            <tr>
              <td></td>
              <td style="font-size:9.0pt;width: 70px;">DATE</td>
              <td style="font-size:9.0pt;">:</td>
              <td style="font-size:9.0pt; float: right;"> <?php echo($data['date'])?date('d/m/Y', strtotime($data['date'])):''; ?></td>
            </tr>
            <tr>
              <td></td>
              <td style="font-size:9.0pt;width: 70px;">AMOUNT</td>
              <td style="font-size:9.0pt;">:</td>
              <td style="font-size:9.0pt; float: right;"> <?php echo ($data['amount'])?number_format($data['amount'],0,",","."):''; ?></td>
            </tr>
        <?php
                }
              }
            }
        ?>
      </table>
    </div>
  </div>

  <div>
    <div style="width: 330px;float: left; padding-top: 30px">
      <table width="100%">
        <tr>
          <td class="border" style="width: 20px;"><?php echo ($row['po_status'] == '1')?'X':''; ?></td>
          <td colspan="3" style="font-size:9.0pt;">NEW PO</td>
        </tr>
        <tr>
          <td></td>
          <td style="font-size:9.0pt;width: 70px;">VOL</td>
          <td style="font-size:9.0pt;" style="width: 20px;">:</td>
          <td style="font-size:9.0pt;float: right;"><?php echo ($row['po_status'] == '1')?$row['volume_poc']." Liter":''; ?></td>
        </tr>
        <tr>
          <td></td>
          <td style="font-size:9.0pt;width: 70px;">AMOUNT</td>
          <td style="font-size:9.0pt;">:</td>
          <td style="font-size:9.0pt; float: right;"> <?php echo ($row['po_status'] == '1')?number_format(($row['harga_poc'] * $row['volume_poc']),0,",","."):'';  ?></td>
        </tr>
      </table>
    </div>

    <div style="width: 330px;float: left; padding-top: 30px">
      <table width="100%">
        <tr>
          <td class="border" style="width: 20px;"><?php echo ($row['po_status'] == '2')?'X':''; ?></td>
          <td colspan="3" style="font-size:9.0pt;">PARTIAL PO</td>
        </tr>
        <tr>
          <td></td>
          <td style="font-size:9.0pt;width: 70px;">VOL</td>
          <td style="font-size:9.0pt;width: 20px;">:</td>
          <td style="font-size:9.0pt;float: right;"><?php echo ($row['po_status'] == '2')?$row['volume_poc']." Liter":''; ?></td>
        </tr>
        <tr>
          <td></td>
          <td style="font-size:9.0pt;width: 70px;">AMOUNT</td>
          <td style="font-size:9.0pt;">:</td>
          <td style="font-size:9.0pt; float: right;"><?php echo ($row['po_status'] == '2')?number_format(($row['harga_poc'] * $row['volume_poc']),0,",","."):'';  ?></td>
        </tr>
      </table>
    </div>
  </div>

  <div>
    <div style="width: 330px; padding-top: 30px; float: left;">
        <table width="100%">
          <tr>
            <td class="border" style="width: 20px;"><?php echo ($row['proposed_status'] == '0')?'X':''; ?></td>
            <td colspan="3" style="font-size:9.0pt;">NOT PROPOSED</td>
          </tr>
          <tr>
            <td></td>
          </tr>
        </table>
    </div>
    <div style="width: 330px;float: left; padding-top: 30px">
      <table width="100%">
        <tr>
          <td class="border" style="width: 20px;"><?php echo ($row['proposed_status'] == '1')?'X':''; ?></td>
          <td colspan="3" style="font-size:9.0pt;">PROPOSED</p></td>
        </tr>
        <tr>
          <td></td>
          <td style="font-size:9.0pt;width: 70px;">ADD TOP</td>
          <td style="font-size:9.0pt;width: 20px;">:</td>
          <td style="font-size:9.0pt;float: right;"><?php echo ($row['proposed_status'] == '1')?$row['add_top']:''; ?></td>
        </tr>
        <tr>
          <td></td>
          <td style="font-size:9.0pt;width: 70px;">ADD CL</td>
          <td style="font-size:9.0pt;">:</td>
          <td style="font-size:9.0pt; float: right;"><?php echo ($row['proposed_status'] == '1')?number_format($row['add_cl'],0,",","."):''; ?></td>
        </tr>
      </table>
    </div>
  </div>

  <div>
     <table class="table2" style="float: left;">
      <thead>
        <tr>
          <th>NOTE BRANCH ADM</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td height="200px"><?php echo $row['adm_summary']; ?></td>
        </tr>
      </tbody>
    </table>

    <table>
      <tr>
        <td width="200"><p style="font-weight: 800; font-size:9.0pt; ">ADM RECOMENDED</p></td>
        <td class="border" style="width: 20px;"><?php echo ($row['adm_result'] == '1')?'X':''; ?></td>
        <td style="font-size:9.0pt;padding-right: 20px;">SUPPLY</td>
        <td class="border" style="width: 20px;"><?php echo ($row['adm_result'] == '2')?'X':''; ?></td>
        <td style="font-size:9.0pt;">NOT SUPPLY</td>
      </tr>
    </table>
  </div>

  <div>
     <table class="table2" style="float: left;">
      <thead>
        <tr>
          <th>NOTE BM/OM</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td height="200px"><?php echo $row['bm_summary']; ?></td>
        </tr>
      </tbody>
    </table>

    <table>
      <tr>
        <td width="200"><p style="font-weight: 800; font-size:9.0pt; ">APPROVAL BM/OM</p></td>
        <td class="border" style="width: 20px;"><?php echo ($row['bm_result'] == '1')?'X':''; ?></td>
        <td style="font-size:9.0pt;padding-right: 20px;">SUPPLY</td>
        <td class="border" style="width: 20px;"><?php echo ($row['bm_result'] == '2')?'X':''; ?></td>
        <td style="font-size:9.0pt;">NOT SUPPLY</td>
      </tr>
    </table>
  </div>

  <div>
    <table class="table3">
      <thead>
        <tr>
          <th width="130">PROPOSED</th>
          <th colspan="5">APPROVED</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td height="70"></td>
          <td height="70" width="110"></td>
          <td height="70" width="110"></td>
          <td height="70" width="110"></td>
          <td height="70" width="110"></td>
          <td height="70" width="110"></td>
        </tr>
        <tr>
          <td>BRANCH ADH</td>
          <td>BM</td>
          <td>OM</td>
          <td>FM</td>
          <td>CFO</td>
          <td>COO</td>
        </tr>
      </tbody>
    </table>
  </div>
</main>