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
			$sql = "select j.*, k.id_prd, a.pr_top, a.pr_pelanggan, a.pr_ar_notyet, a.pr_ar_satu, a.pr_ar_dua, b.tanggal_kirim, c.alamat_survey, d.nama_prov, e.nama_kab, 
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
					where j.id_par = '".$dRow['id_par']."'";
            $res = $con->getResult($sql);
			if(count($res) == 0){
                echo '<tr><td colspan="11" style="text-align:center">Data tidak ditemukan</td></tr>';
            } else{
                $nom = 0;
                foreach($res as $data){
                    $nom++;
                    $idk 	= $data['id_prd'];
					$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
					$alamat	= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
					$kirim	= date("d/m/Y", strtotime($data['tanggal_kirim']));
					$dt1 	= $data['pr_top'];
					$dt2 	= $data['pr_pelanggan'];
					$spy 	= $data['schedule_payment'];
					$dt3 	= number_format($data['pr_ar_notyet'],0);
					$dt4 	= number_format($data['pr_ar_satu'],0);
					$dt5 	= number_format($data['pr_ar_dua'],0);
					$ovr	= number_format(($data['pr_ar_dua'] + $data['pr_ar_satu']),0);
					$ovt	= number_format(($data['pr_ar_dua'] + $data['pr_ar_satu'] + $data['pr_ar_notyet']),0);
        ?>
            <tr>
                <td class="text-center"><?php echo $nom; ?></td>
                <td>
                    <p style="margin-bottom:0px"><?php echo ($dt2?'<b>'.$dt2.'</b>':'');?></p>
                    <p style="margin-bottom:0px"><b><?php echo $data['nama_customer'];?></b></p>
                    <p style="margin-bottom:0px"><i><?php echo $data['fullname'];?></i></p>
                </td>
                <td>
                    <p style="margin-bottom:0px"><b><?php echo $data['nama_area'];?></b></p>
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
			<?php echo $res[0]['finance_summary']; ?>
			<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['finance_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['finance_tanggal']))." WIB"; ?></i></p>
		</div>
    </div>
    <div class="col-sm-6 col-sm-top">
        <label>Catatan BM</label>
        <div class="form-control" style="height:auto">
			<?php echo $res[0]['sm_summary']; ?>
			<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['sm_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['sm_tanggal']))." WIB"; ?></i></p>
		</div>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-6">
        <label>Catatan OM</label>
        <div class="form-control" style="height:auto">
			<?php echo $res[0]['om_summary']; ?>
			<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['om_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['om_tanggal']))." WIB"; ?></i></p>
		</div>
    </div>
    <?php if($res[0]['disposisi_ar'] == 3){ ?>
    <div class="col-sm-6 col-sm-top">
        <label>Catatan Manager Finance</label>
        <div class="form-control" style="height:auto">
			<?php echo $res[0]['mgr_summary']; ?>
			<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['mgr_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['mgr_tanggal']))." WIB"; ?></i></p>
		</div>
    </div>
    <?php } ?>
</div>
