<?php
	$que1 = "select a.id_pr, a.nomor_pr, a.tanggal_pr, a.cfo_result, b.nama_cabang, c.id_par, c.finance_pic, c.finance_summary, c.finance_tanggal, c.sm_pic, c.sm_summary, 
			 c.sm_tanggal, c.om_pic, c.om_summary, c.om_tanggal, c.disposisi_ar, c.mgr_pic, c.mgr_summary, c.mgr_tanggal 
			 from pro_pr a join pro_master_cabang b on a.id_wilayah = b.id_master join pro_pr_ar c on a.id_pr = c.id_pr 
			 where 1=1";
	if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 4)
		$que1 .= " and a.disposisi_pr > 3 and a.cfo_result = 0";
	else if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) == 3)
		$que1 .= " and a.disposisi_pr > 3 and a.is_ceo = 1 and a.ceo_result = 0";
	$que1 .= " order by a.id_pr";
	$has1 = $con->getResult($que1);
	if(count($has1) > 0){
		foreach($has1 as $rec1){
?>
<div class="box box-primary">
    <div class="box-header with-border">
    	<h3 class="box-title" style="font-size:14px;"><?php echo '['.strtoupper($rec1['nama_cabang']).'] '.$rec1['nomor_pr']; ?></h3>
    </div>
    <div class="box-body">
    	<p><b><?php echo 'AR '.str_pad($rec1['id_par'],4,'0',STR_PAD_LEFT);?></b></p>
        <div class="table-responsive">
            <table class="table table-bordered table-ar-grid">
                <thead>
                    <tr>
                        <th class="text-center" width="4%">No</th>
                        <th class="text-center" width="15%">Customer</th>
                        <th class="text-center" width="19%">Alamat Kirim</th>
                        <th class="text-center" width="6%">Tgl Kirim</th>
                        <th class="text-center" width="5%">TOP</th>
                        <th class="text-center" width="7%">Overdue</th>
                        <th class="text-center" width="7%">AR (Not Yet)</th>
                        <th class="text-center" width="7%">AR (1 - 30)</th>
                        <th class="text-center" width="7%">AR (> 30)</th>
                        <th class="text-center" width="8%">Total</th>
                        <th class="text-center" width="15%">Schedule Payment</th>
                    </tr>
                </thead>
                <tbody>
				<?php 
                    $que2 = "select j.*, k.id_prd, a.pr_top, a.pr_pelanggan, a.pr_ar_notyet, a.pr_ar_satu, a.pr_ar_dua, b.tanggal_kirim, c.alamat_survey, d.nama_prov, e.nama_kab, 
                            g.nama_customer, h.fullname, a.schedule_payment, m.nama_area 
                            from pro_pr_ar j 
							join pro_pr_ar_detail k on j.id_par = k.id_par 
							join pro_pr_detail a on k.id_prd = a.id_prd 
                            join pro_po_customer_plan b on a.id_plan = b.id_plan 
							join pro_customer_lcr c on b.id_lcr = c.id_lcr
                            join pro_master_provinsi d on c.prov_survey = d.id_prov 
							join pro_master_kabupaten e on c.kab_survey = e.id_kab
                            join pro_po_customer f on b.id_poc = f.id_poc 
                            join pro_customer g on f.id_customer = g.id_customer 
							join acl_user h on g.id_marketing = h.id_user 
							join pro_master_cabang i on g.id_wilayah = i.id_master 
							join pro_penawaran l on f.id_penawaran = l.id_penawaran  
							join pro_master_area m on l.id_area = m.id_master 
							join pro_master_produk n on f.produk_poc = n.id_master 
                            where j.id_par = '".$rec1['id_par']."'";
                    $has2 = $con->getResult($que2);
                    if(count($has2) == 0){
                        echo '<tr><td colspan="11" style="text-align:center">Data tidak ditemukan</td></tr>';
                    } else{
                        $nil = 0;
                        foreach($has2 as $rec2){
                            $nil++;
                            $idk 	= $rec2['id_prd'];
                            $tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $rec2['nama_kab']));
                            $alamat	= $rec2['alamat_survey']." ".ucwords($tempal)." ".$rec2['nama_prov'];
                            $kirim	= date("d/m/Y", strtotime($rec2['tanggal_kirim']));
                            $dt1 	= $rec2['pr_top'];
                            $dt2 	= $rec2['pr_pelanggan'];
                            $spy 	= $rec2['schedule_payment'];
                            $dt3 	= number_format($rec2['pr_ar_notyet'],0);
                            $dt4 	= number_format($rec2['pr_ar_satu'],0);
                            $dt5 	= number_format($rec2['pr_ar_dua'],0);
                            $ovr	= number_format(($rec2['pr_ar_dua'] + $rec2['pr_ar_satu']),0);
                            $ovt	= number_format(($rec2['pr_ar_dua'] + $rec2['pr_ar_satu'] + $rec2['pr_ar_notyet']),0);
                ?>
                    <tr>
                        <td class="text-center"><?php echo $nil; ?></td>
                        <td>
                            <p style="margin-bottom:0px"><?php echo ($dt2?'<b>'.$dt2.'</b>':'');?></p>
                            <p style="margin-bottom:0px"><b><?php echo $rec2['nama_customer'];?></b></p>
                            <p style="margin-bottom:0px"><i><?php echo $rec2['fullname'];?></i></p>
                        </td>
                        <td>
                            <p style="margin-bottom:0px"><b><?php echo $rec2['nama_area'];?></b></p>
                            <p style="margin-bottom:0px"><?php echo $alamat;?></p>
                        </td>
                        <td class="text-center"><?php echo $kirim;?></td>
                        <td class="text-center"><?php echo $dt1; ?></td>
                        <td class="text-right"><?php echo $ovr; ?></td>
                        <td class="text-right"><?php echo $dt3; ?></td>
                        <td class="text-right"><?php echo $dt4; ?></td>
                        <td class="text-right"><?php echo $dt5; ?></td>
                        <td class="text-right"><?php echo $ovt; ?></td>
                        <td><?php echo $spy; ?></td>
                    </tr>
                <?php } } ?>
                </tbody>
            </table>
        </div>

        <div class="form-group row">
            <div class="col-sm-6">
                <label>Catatan Finance</label>
                <div class="form-control" style="height:auto">
                    <?php echo $rec1['finance_summary']; ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rec1['finance_pic']." - ".date("d/m/Y H:i:s", strtotime($rec1['finance_tanggal']))." WIB"; ?></i></p>
                </div>
            </div>
            <div class="col-sm-6 col-sm-top">
                <label>Catatan BM</label>
                <div class="form-control" style="height:auto">
                    <?php echo $rec1['sm_summary']; ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rec1['sm_pic']." - ".date("d/m/Y H:i:s", strtotime($rec1['sm_tanggal']))." WIB"; ?></i></p>
                </div>
            </div>
		</div>
        <div class="form-group row">
            <div class="col-sm-6">
                <label>Catatan OM</label>
                <div class="form-control" style="height:auto">
                    <?php echo $rec1['om_summary']; ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rec1['om_pic']." - ".date("d/m/Y H:i:s", strtotime($rec1['om_tanggal']))." WIB"; ?></i></p>
                </div>
            </div>
    		<?php if($rec1['disposisi_ar'] == 3){ ?>
            <div class="col-sm-6 col-sm-top">
                <label>Catatan OM</label>
                <div class="form-control" style="height:auto">
                    <?php echo $rec1['mgr_summary']; ?>
                    <p style="margin:10px 0 0; font-size:12px;"><i><?php echo $rec1['mgr_pic']." - ".date("d/m/Y H:i:s", strtotime($rec1['mgr_tanggal']))." WIB"; ?></i></p>
                </div>
            </div>
            <?php } ?>
        </div>

	</div>
</div>
<?php } } ?>
