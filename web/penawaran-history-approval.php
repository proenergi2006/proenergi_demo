<div class="box box-primary">
	<div class="box-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                	<tr>
                        <th class="text-center" width="60">No</th>
                        <th class="text-center" width="200">User Approval</th>
                        <th class="text-center" width="">Detil Penawaran</th>
                        <th class="text-center" width="100">Tgl Approval</th>
                        <th class="text-center" width="100">Status</th>
                        <th class="text-center" width="250">Catatan Disposisi</th>
                        <th class="text-center" width="250">Catatan Marketing / Key Account</th>
					</tr>
                </thead>
                <tbody>
				<?php
                    $sqlHist = "
                        select a.kd_approval,
                        case 
                            when a.result = '1' then 'Disetujui'
                            when a.result = '2' then 'Ditolak'
                            else ''
                        end as result, 
                        a.summary, a.id_user, DATE_FORMAT(a.tgl_approval, '%d-%m-%Y') tgl_approval, b.fullname, c.role_name,
                        harga_dasar, oa_kirim, ppn, pbbkb, keterangan_pengajuan, volume, a.tgl_approval as ordernya
                        from 
                        pro_approval_hist a 
                        join acl_user b on a.id_user = b.id_user 
                        join acl_role c on a.id_role = c.id_role 
                        where 1=1 and a.kd_approval = 'P001' and a.id_customer = '".$idr."' and a.id_penawaran = '".$idk."'
                        order by ordernya desc
                    ";
                    $resHist = $con->getResult($sqlHist);
					$nomor = 0;
                    if(count($resHist) > 0){
						foreach($resHist as $arr1){
							$nomor++;
							echo '
							<tr>
								<td class="text-right">'.$nomor.'</td>
								<td class="text-left"><p style="margin-bottom:5px;">'.$arr1['fullname'].'</p><i>'.$arr1['role_name'].'</i></td>
								<td class="text-left">
									<div style="display:table; width:100%;">
										<div style="display:table-row">
											<div style="display:table-cell; width:100px;">Volume (Liter)</div>
											<div style="display:table-cell; text-align:right;">
												<p style="margin-bottom:3px;">'.number_format($arr1['volume']).'</p>
											</div>
										</div>
										<div style="display:table-row">
											<div style="display:table-cell; width:100px;">Harga Dasar</div>
											<div style="display:table-cell; text-align:right;">
												<p style="margin-bottom:3px;">'.number_format($arr1['harga_dasar']).'</p>
											</div>
										</div>
										<div style="display:table-row">
											<div style="display:table-cell; width:100px;">Ongkos Angkut</div>
											<div style="display:table-cell; text-align:right;">
												<p style="margin-bottom:3px;">'.number_format($arr1['oa_kirim']).'</p>
											</div>
										</div>
										<div style="display:table-row">
											<div style="display:table-cell; width:100px;">PPN</div>
											<div style="display:table-cell; text-align:right;">
												<p style="margin-bottom:3px;">'.number_format($arr1['ppn']).'</p>
											</div>
										</div>
										<div style="display:table-row">
											<div style="display:table-cell; width:100px;">PBBKB</div>
											<div style="display:table-cell; text-align:right;">
												<p style="margin-bottom:3px;">'.number_format($arr1['pbbkb']).'</p>
											</div>
										</div>
									</div>
								</td>
								<td class="text-center">'.$arr1['tgl_approval'].'</td>
								<td class="text-left">'.$arr1['result'].'</td>
								<td class="text-left">'.nl2br($arr1['summary']).'</td>
								<td class="text-left">'.nl2br($arr1['keterangan_pengajuan']).'</td>
							</tr>';
						}
                    } else{
                        echo '<tr><td colspan="7">Histori approval penawaran belum ada</td></tr>';
                    }
                ?>
                </tbody>
            </table>
		</div>
    </div>
</div>

