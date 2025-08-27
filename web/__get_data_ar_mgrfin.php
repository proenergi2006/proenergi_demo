<?php /*<div class="table-responsive">
    <table class="table table-bordered" id="table-grid2">
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
					g.nama_customer, h.fullname, a.schedule_payment, i.nama_cabang 
					from pro_pr_ar j join pro_pr_ar_detail k on j.id_par = k.id_par join pro_pr_detail a on k.id_prd = a.id_prd 
					join pro_po_customer_plan b on a.id_plan = b.id_plan join pro_customer_lcr c on b.id_lcr = c.id_lcr
					join pro_master_provinsi d on c.prov_survey = d.id_prov join pro_master_kabupaten e on c.kab_survey = e.id_kab
					join pro_po_customer f on b.id_poc = f.id_poc 
					join pro_customer g on f.id_customer = g.id_customer join acl_user h on g.id_marketing = h.id_user 
					join pro_master_cabang i on c.id_wilayah = i.id_master 
					where j.id_par = '".$idr."'";
            $res = $con->getResult($sql);
            $fnr = $res[0]['om_result'];
			if(count($res) == 0){
                echo '<tr><td colspan="11" style="text-align:center">Data tidak ditemukan </td></tr>';
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
                    <p style="margin-bottom:0px"><b><?php echo $data['nama_cabang'];?></b></p>
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
</div> */ ?>

<div style="overflow-x: scroll" id="table-long">
    <div style="width:2030px; height:auto;">
        <div class="table-responsive-satu">
            <table class="table table-bordered" id="table-grid2">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2" width="50">No</th>
                        <th class="text-center" rowspan="2" width="200">Customer</th>
                        <th class="text-center" rowspan="2" width="200">Area/ Alamat Kirim/ Wilayah OA</th>
                        <th class="text-center" rowspan="2" width="100">Tgl dan Volume Kirim</th>
                        <th class="text-center" rowspan="2" width="75">Kode Pelanggan</th>
                        <th class="text-center" rowspan="2" width="55">TOP</th>
                        <th class="text-center" rowspan="2" width="80">Actual TOP</th>
                        <th class="text-center" colspan="7">Harga (Rp/Liter)</th>
                        <th class="text-center" colspan="5">AR</th>
                        <th class="text-center" rowspan="2" width="120">Credit Limit</th>
                        <th class="text-center" rowspan="2" width="180">Schedule Payment</th>
                    </tr>
                    <tr>
                        <th class="text-center" width="75">Harga Jual (Gross)</th>
                        <th class="text-center" width="60">Ongkos Angkut</th>
                        <th class="text-center" width="60">Refund</th>
                        <th class="text-center" width="60">Oil Dues</th>
                        <th class="text-center" width="60">PBBKB</th>
                        <th class="text-center" width="60">Other Cost</th>
                        <th class="text-center" width="75">Harga Jual (Nett)</th>
                        <th class="text-center" width="100">Overdue</th>
                        <th class="text-center" width="100">AR (Not Yet)</th>
                        <th class="text-center" width="100">AR (1 - 30)</th>
                        <th class="text-center" width="100">AR (> 30)</th>
                        <th class="text-center" width="120">Total</th>
                    </tr>
                </thead>
                <tbody>
				<?php 
                    $sql = "select o.*, a.id_prd, a.pr_top, a.pr_pelanggan, a.pr_ar_notyet, a.pr_ar_satu, a.pr_ar_dua, a.volume, a.transport, a.schedule_payment, c.tanggal_kirim, 
							e.alamat_survey, e.id_wil_oa, f.nama_prov, g.nama_kab, h.nama_customer, h.id_customer, i.fullname, l.nama_area, d.harga_poc, k.refund_tawar, 
							h.kode_pelanggan, a.pr_actual_top, a.pr_kredit_limit, k.other_cost, p.wilayah_angkut, q.nilai_pbbkb, d.lampiran_poc, d.lampiran_poc_ori, d.id_poc  
                            from pro_pr_detail a 
							join pro_pr b on a.id_pr = b.id_pr 
							join pro_po_customer_plan c on a.id_plan = c.id_plan 
							join pro_po_customer d on c.id_poc = d.id_poc 
							join pro_customer_lcr e on c.id_lcr = e.id_lcr
							join pro_master_provinsi f on e.prov_survey = f.id_prov 
							join pro_master_kabupaten g on e.kab_survey = g.id_kab
							join pro_customer h on d.id_customer = h.id_customer 
							join acl_user i on h.id_marketing = i.id_user 
							join pro_master_cabang j on h.id_wilayah = j.id_master 
							join pro_penawaran k on d.id_penawaran = k.id_penawaran  
							join pro_master_area l on k.id_area = l.id_master 
							join pro_master_produk m on d.produk_poc = m.id_master 
							join pro_pr_ar_detail n on a.id_prd = n.id_prd 
							join pro_pr_ar o on n.id_par = o.id_par 
							join pro_master_wilayah_angkut p on e.id_wil_oa = p.id_master and e.prov_survey = p.id_prov and e.kab_survey = p.id_kab 
							join pro_master_pbbkb q on k.pbbkb_tawar = q.id_master 
                            where n.id_par = '".$idr."' order by a.is_approved desc, c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd";
                    $res = $con->getResult($sql);
                    $fnr = $res[0]['mgr_result'];
                    if(count($res) == 0){
                        echo '<tr><td colspan="21" style="text-align:center">Data tidak ditemukan </td></tr>';
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
							$dt7 	= $data['pr_actual_top'];
							$spy 	= $data['schedule_payment'];
							$dt3 	= number_format($data['pr_ar_notyet']);
							$dt4 	= number_format($data['pr_ar_satu']);
							$dt5 	= number_format($data['pr_ar_dua']);
							$dt6 	= number_format($data['pr_kredit_limit']);
							$ovr	= number_format(($data['pr_ar_dua'] + $data['pr_ar_satu']));
							$ovt	= number_format(($data['pr_ar_dua'] + $data['pr_ar_satu'] + $data['pr_ar_notyet']));

							$pbbkbT = ($data['nilai_pbbkb']/100) + 1.11;
							$oildus = $data['harga_poc'] / $pbbkbT * 0.003;
							$pbbkbN = $data['harga_poc'] / $pbbkbT * ($data['nilai_pbbkb']/100);
							$tmphrg = $data['refund_tawar'] + $oildus + $data['transport'] + $pbbkbN + $data['other_cost'];
							$nethrg = $data['harga_poc'] - $tmphrg;

							$pathPt = $public_base_directory.'/files/uploaded_user/lampiran/'.$data['lampiran_poc'];
							$lampPt = $data['lampiran_poc_ori'];
							if($data['lampiran_poc'] && file_exists($pathPt)){
								$linkPt = ACTION_CLIENT."/download-file.php?".paramEncrypt("tipe=2&ktg=POC_".$data['id_poc']."_&file=".$lampPt);
								$attach = '<a href="'.$linkPt.'"><i class="fa fa-file-alt" title="'.$lampPt.'"></i> PO Customer</a>';
							} else {$attach = '';}
                ?>
                    <tr>
                        <td class="text-center"><?php echo $nom; ?></td>
                        <td>
                            <p style="margin-bottom:0px"><b><?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'].' - ':'').$data['nama_customer'];?></b></p>
                            <p style="margin-bottom:0px"><i><?php echo $data['fullname'];?></i></p>
                        </td>
                        <td>
                            <p style="margin-bottom:0px"><b><?php echo $data['nama_area'];?></b></p>
                            <p style="margin-bottom:0px"><?php echo $alamat;?></p>
                            <p style="margin-bottom:0px"><?php echo 'Wilayah OA : '.$data['wilayah_angkut'];?></p>
                        </td>
                        <td>
                            <p style="margin-bottom:0px"><?php echo $kirim;?></p>
                            <p style="margin-bottom:0px"><?php echo number_format($data['volume'])." Liter"; ?></p>
                            <p style="margin-bottom:0px"><?php echo $attach;?></p>
                        </td>
                        <td><?php echo $dt2;?></td>
                        <td><?php echo $dt1;?></td>
                        <td><?php echo $dt7;?></td>
                        <td class="text-right"><?php echo number_format($data['harga_poc']);?></td>
                        <td class="text-right"><?php echo number_format($data['transport']);?></td>
                        <td class="text-right"><?php echo number_format($data['refund_tawar']);?></td>
                        <td class="text-right"><?php echo number_format($oildus);?></td>
                        <td class="text-right"><?php echo number_format($pbbkbN);?></td>
                        <td class="text-right"><?php echo number_format($data['other_cost']);?></td>
                        <td class="text-right"><?php echo number_format($nethrg);?></td>
                        <td class="text-right"><?php echo $ovr;?></td>
                        <td class="text-right"><?php echo $dt3;?></td>
                        <td class="text-right"><?php echo $dt4;?></td>
                        <td class="text-right"><?php echo $dt5;?></td>
                        <td class="text-right"><?php echo $ovt;?></td>
                        <td class="text-right"><?php echo $dt6;?></td>
                        <td><?php echo $spy;?></td>
                    </tr>
				<?php } } ?>
                </tbody>
			</table>
		</div>
	</div>
</div>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Catatan Finance</label>
        <div class="form-control" style="height:auto">
			<?php echo ($res[0]['finance_summary']); ?>
			<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['finance_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['finance_tanggal']))." WIB"; ?></i></p>
		</div>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-6">
        <label>Catatan BM</label>
        <div class="form-control" style="height:auto">
			<?php echo ($res[0]['sm_summary']); ?>
			<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['sm_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['sm_tanggal']))." WIB"; ?></i></p>
		</div>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-6">
        <label>Catatan OM</label>
        <div class="form-control" style="height:auto">
			<?php echo ($res[0]['om_summary']); ?>
			<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['om_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['om_tanggal']))." WIB"; ?></i></p>
		</div>
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Catatan Manager Finance</label>
        <?php if(!$fnr){ ?>
		<textarea name="summary" id="summary" class="form-control"></textarea>
		<?php } else{ ?>
        <div class="form-control" style="height:auto">
			<?php echo ($res[0]['mgr_summary']); ?>
			<p style="margin:10px 0 0; font-size:12px;"><i><?php echo $res[0]['mgr_pic']." - ".date("d/m/Y H:i:s", strtotime($res[0]['mgr_tanggal']))." WIB"; ?></i></p>
		</div>
		<?php } ?>
    </div>
</div>


<?php if(count($res) > 0){ ?>
<hr style="margin:0 0 10px" />
<div class="form-group row">
    <div class="col-sm-12">
        <div class="pad bg-gray">
            <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/purchase-request-ar.php";?>">Kembali</a> 
            <?php if(!$fnr){ ?><button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Submit</button><?php } ?>
		</div>
    </div>
</div>
<?php } ?>