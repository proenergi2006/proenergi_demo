<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$file 	= htmlspecialchars($_POST["file"], ENT_QUOTES);
	$aksi 	= htmlspecialchars(paramDecrypt($_POST["aksi"]), ENT_QUOTES);
	// $data['id_dsd']."|#|1|#|".$data['nomor_poc']."[]".$data['nama_customer']."|#|".$data['mobil_po']."|#|".$data['volume_po']
	$temp	= explode("|#|", $aksi);
	$idnya 	= $temp[0];
	$tipe 	= $temp[1];
	$judul 	= $temp[2];
	$mobil 	= $temp[3];
	$volume = $temp[4];
	$answer	= array();

	if($file == "logistik"){
		$arrSql = array(1=>array("table"=>"pro_po_ds_detail", "key"=>"id_dsd"), array("table"=>"pro_po_ds_kapal", "key"=>"id_dsk"));
		$cek1 = "select status_pengiriman, is_delivered, is_cancel, realisasi_volume, terima_jalan, catatan 
				 from ".$arrSql[$tipe]["table"]." where ".$arrSql[$tipe]["key"]." = '".$idnya."'";
		$row1 = $conSub->getRecord($cek1);
		$arrS 	= ($row1['status_pengiriman'] ? json_decode($row1['status_pengiriman'], true) : array());
		$tmp_nm = explode("[]", $judul);
		$title	= '
			<table style="width:100%">
				<tr style="height:25px;">
					<td width="110"><b>Customer</b></td>
					<td width="20" class="text-center"><b>:</b></td>
					<td><b>'.$tmp_nm[1].'</b></td>
				</tr>
				<tr>
					<td><b>'.($tipe == 1?'No. DN':'No. DN Kapal').'</b></td>
					<td class="text-center"><b>:</b></td>
					<td><b>'.$tmp_nm[0].'</b></td>
				</tr>
			</table>';

		$tmids	= paramEncrypt($idnya."|#|");
		$isiTab = "";
		if(count($arrS) > 0){
			$nom = 0;
			foreach($arrS as $idxnya=>$data){
				$nom++;
				$tanggal = tgl_indo(substr($data['tanggal'],0,10), 'long', 'ndb').' '.substr($data['tanggal'],11); 
				$opt_tgl = "<option></option>";
				for($i=0;$i<24;$i++){
					$select = (substr($data['tanggal'],11,2) == $i)?' selected':'';
					$opt_tgl .= '<option'.$select.'>'.str_pad($i,2,'0',STR_PAD_LEFT).'</option>';
				}

				$opt_jam = "<option></option>";
				for($i=0;$i<60;$i++){
					$select = (substr($data['tanggal'],14,2) == $i)?' selected':'';
					$opt_jam .= '<option'.$select.'>'.str_pad($i,2,'0',STR_PAD_LEFT).'</option>';
				}

				$isiTab .= '<tr>
					<td class="text-center">'.$nom.'</td>
					<td class="text-center">
						<p style="margin-bottom:0px;" class="histori-text'.$nom.'">'.$tanggal.'</p>
						<p style="margin-bottom:0px;" class="histori-form'.$nom.' hide">
							<input type="text" name="edit1[]" id="edit1_'.$nom.'" class="input-date" value="'.substr($data['tanggal'],0,10).'" />
							<select name="edit2[]" id="edit2_'.$nom.'" style="height:28px; width:40px;">'.$opt_tgl.'</select> : 
							<select name="edit3[]" id="edit3_'.$nom.'" style="height:28px; width:40px;">'.$opt_jam.'</select>
						</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px;" class="histori-text'.$nom.'">'.$data['status'].'</p>
						<div style="margin-bottom:0px;" class="histori-form'.$nom.' hide">
							<div class="input-group">
								<input type="text" name="edit4[]" id="edit4_'.$nom.'" class="input-list" value="'.$data['status'].'" />
								<div class="input-group-btn">
									<a data-idx="'.$nom.'" data-ids="'.$tmids.'" data-jns="'.$tipe.'" class="fa-simpan-sts btn btn-primary"><i class="fa fa-floppy-o"></i></a>
								</div>
							</div>
						</div>
					</td>
					<td class="text-center">
						'.$row1['catatan'].'
					</td>
					<td class="text-center">
						'.(!$row1['is_delivered'] && !$row1['is_cancel'] && $nom > 1
						?'<a data-idx="'.$nom.'" class="fa-ubah-sts btn btn-info histori-text'.$nom.'"><i class="fa fa-edit"></i></a>':'&nbsp;').'
					</td>
				</tr>';
			}
		} else{
			$isiTab .= '<tr><td colspan="4" class="text-center">Histori pengiriman belum ada</td></tr>';
		}

		$tambahan = "";
		if($row1['is_delivered'] && !$row1['is_cancel']){
			$tambahan .= '
			<div class="row"><div class="col-sm-6 col-md-4"><div style="border:1px solid #ddd; margin-bottom:10px;">
				<div style="background-color:#f4f4f4; padding:8px 5px; font-size:11px; font-family:arial; font-weight:bold; border-bottom:1px solid #ddd;">Keterangan Lain</div>
				<div class="table-responsive">
					<table class="table no-border" style="margin-bottom: 0px;">
						<tbody>
							<tr>
								<td style="padding:8px 5px 2px;" width="100">Volume</td>
								<td style="padding:8px 5px 2px;" width="10" class="text-center">:</td>
								<td style="padding:8px 5px 2px;">'.($volume?number_format($volume).' Liter':'&nbsp;').'</td>
							</tr>
							<tr>
								<td style="padding:8px 5px 2px;" width="100">Realisasi Volume</td>
								<td style="padding:8px 5px 2px;" width="10" class="text-center">:</td>
								<td style="padding:8px 5px 2px;">'.($row1['realisasi_volume']?number_format($row1['realisasi_volume']).' Liter':'&nbsp;').'</td>
							</tr>
							<tr>
								<td style="padding:2px 5px 8px;">Terima Surat Jalan</td>
								<td style="padding:2px 5px 8px;" class="text-center">:</td>
								<td style="padding:2px 5px 8px;">'.$row1['terima_jalan'].'</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div></div></div>';
		} else if(!$row1['is_delivered'] && !$row1['is_cancel']){
			$cek2 = "select nomor_plat, link_gps, user_gps, pass_gps, membercode_gps from pro_master_transportir_mobil where id_master = '".$mobil."'";
			$row2 = $conSub->getRecord($cek2);
			if($tipe == 1){
				$tambahan .= '
				<div class="row"><div class="col-sm-8 col-md-6"><div style="border:1px solid #ddd; margin-bottom:10px;">
					<div style="background-color:#f4f4f4; padding:8px 5px; font-size:11px; font-family:arial; font-weight:bold; border-bottom:1px solid #ddd;">
					GPS TRUCK '.$row2['nomor_plat'].'</div>
					<div class="table-responsive">
						<table class="table no-border" style="margin-bottom: 0px;">
							<tbody>
								<tr>
									<td style="padding:8px 5px 2px;" width="85">Member Code</td>
									<td style="padding:8px 5px 2px;" width="10" class="text-center">:</td>
									<td style="padding:8px 5px 2px;">'.$row2['membercode_gps'].'</td>
								</tr>
								<tr>
									<td style="padding:2px 5px;">Username</td>
									<td style="padding:2px 5px;" class="text-center">:</td>
									<td style="padding:2px 5px;">'.$row2['user_gps'].'</td>
								</tr>
								<tr>
									<td style="padding:2px 5px;">Password</td>
									<td style="padding:2px 5px;" class="text-center">:</td>
									<td style="padding:2px 5px;">'.$row2['pass_gps'].'</td>
								</tr>
								<tr>
									<td style="padding:2px 5px 8px;" colspan="3">
										'.($row2['link_gps']?'<a href="'.$row2['link_gps'].'" target="_blank">'.$row2['link_gps'].'</a>':'').'
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div></div></div>';
			}
		}
	}
	
	else if($file == "transportir"){
		$arrSql = array(1=>array("table"=>"pro_po_ds_detail", "key"=>"id_dsd"), array("table"=>"pro_po_ds_kapal", "key"=>"id_dsk"));
		$cek1 	= "select status_pengiriman, is_delivered, is_cancel, realisasi_volume, terima_jalan from ".$arrSql[$tipe]["table"]." 
				   where ".$arrSql[$tipe]["key"]." = '".$idnya."'";
		$row1 	= $conSub->getRecord($cek1);
		$arrS 	= json_decode($row1['status_pengiriman'], true);
		$title	= '<b><u>Nomor SPJ : '.$judul.'</u></b>';
		$tmids	= paramEncrypt($idnya."|#|");
		$isiTab = "";
		if(count($arrS) > 0){
			$nom = 0;
			foreach($arrS as $idxnya=>$data){
				$nom++;
				$tanggal = tgl_indo(substr($data['tanggal'],0,10), 'long', 'ndb').' '.substr($data['tanggal'],11); 
				$opt_tgl = "<option></option>";
				for($i=0;$i<24;$i++){
					$select = (substr($data['tanggal'],11,2) == $i)?' selected':'';
					$opt_tgl .= '<option'.$select.'>'.str_pad($i,2,'0',STR_PAD_LEFT).'</option>';
				}

				$opt_jam = "<option></option>";
				for($i=0;$i<60;$i++){
					$select = (substr($data['tanggal'],14,2) == $i)?' selected':'';
					$opt_jam .= '<option'.$select.'>'.str_pad($i,2,'0',STR_PAD_LEFT).'</option>';
				}

				$isiTab .= '<tr>
					<td class="text-center">'.$nom.'</td>
					<td class="text-center">
						<p style="margin-bottom:0px;" class="histori-text'.$nom.'">'.$tanggal.'</p>
						<p style="margin-bottom:0px;" class="histori-form'.$nom.' hide">
							<input type="text" name="edit1[]" id="edit1_'.$nom.'" class="input-date" value="'.substr($data['tanggal'],0,10).'" />
							<select name="edit2[]" id="edit2_'.$nom.'" style="height:28px; width:40px;">'.$opt_tgl.'</select> : 
							<select name="edit3[]" id="edit3_'.$nom.'" style="height:28px; width:40px;">'.$opt_jam.'</select>
						</p>
					</td>
					<td class="text-left">
						<p style="margin-bottom:0px;" class="histori-text'.$nom.'">'.$data['status'].'</p>
						<div style="margin-bottom:0px;" class="histori-form'.$nom.' hide">
							<div class="input-group">
								<input type="text" name="edit4[]" id="edit4_'.$nom.'" class="input-list" value="'.$data['status'].'" />
								<div class="input-group-btn">
									<a data-idx="'.$nom.'" data-ids="'.$tmids.'" data-jns="1" class="fa-simpan-sts btn btn-primary"><i class="fa fa-floppy-o"></i></a>
								</div>
							</div>
						</div>
					</td>
					<td class="text-center">
						'.(!$row1['is_delivered'] && !$row1['is_cancel'] && $nom > 1
						?'<a data-idx="'.$nom.'" class="fa-ubah-sts btn btn-info histori-text'.$nom.'"><i class="fa fa-edit"></i></a>':'&nbsp;').'
					</td>
				</tr>';
			}
		} else{
			$isiTab .= '<tr><td colspan="4" class="text-center">Histori pengiriman belum ada</td></tr>';
		}
		$tambahan = "";
		if($row1['is_delivered'] && !$row1['is_cancel']){
			$tambahan .= '
			<div class="row"><div class="col-sm-6 col-md-4"><div style="border:1px solid #ddd; margin-bottom:10px;">
				<div style="background-color:#f4f4f4; padding:8px 5px; font-size:11px; font-family:arial; font-weight:bold; border-bottom:1px solid #ddd;">Keterangan Lain</div>
				<div class="table-responsive">
					<table class="table no-border" style="margin-bottom: 0px;">
						<tbody>
							<tr>
								<td style="padding:8px 5px 2px;" width="100">Volume</td>
								<td style="padding:8px 5px 2px;" width="10" class="text-center">:</td>
								<td style="padding:8px 5px 2px;">'.($volume?number_format($volume).' Liter':'&nbsp;').'</td>
							</tr>
							<tr>
								<td style="padding:8px 5px 2px;" width="100">Realisasi Volume</td>
								<td style="padding:8px 5px 2px;" width="10" class="text-center">:</td>
								<td style="padding:8px 5px 2px;">'.($row1['realisasi_volume']?number_format($row1['realisasi_volume']).' Liter':'&nbsp;').'</td>
							</tr>
							<tr>
								<td style="padding:2px 5px 8px;">Terima Surat Jalan</td>
								<td style="padding:2px 5px 8px;" class="text-center">:</td>
								<td style="padding:2px 5px 8px;">'.$row1['terima_jalan'].'</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div></div></div>';
		} else if(!$row1['is_delivered'] && !$row1['is_cancel']){
			$cek2 = "select nomor_plat, link_gps, user_gps, pass_gps, membercode_gps from pro_master_transportir_mobil where id_master = '".$mobil."'";
			$row2 = $conSub->getRecord($cek2);
			$tambahan .= '
			<div class="row"><div class="col-sm-8 col-md-6"><div style="border:1px solid #ddd; margin-bottom:10px;">
				<div style="background-color:#f4f4f4; padding:8px 5px; font-size:11px; font-family:arial; font-weight:bold; border-bottom:1px solid #ddd;">
				GPS TRUCK '.$row2['nomor_plat'].'</div>
				<div class="table-responsive">
					<table class="table no-border" style="margin-bottom: 0px;">
						<tbody>
							<tr>
								<td style="padding:8px 5px 2px;" width="85">Member Code</td>
								<td style="padding:8px 5px 2px;" width="10" class="text-center">:</td>
								<td style="padding:8px 5px 2px;">'.$row2['membercode_gps'].'</td>
							</tr>
							<tr>
								<td style="padding:2px 5px;">Username</td>
								<td style="padding:2px 5px;" class="text-center">:</td>
								<td style="padding:2px 5px;">'.$row2['user_gps'].'</td>
							</tr>
							<tr>
								<td style="padding:2px 5px;">Password</td>
								<td style="padding:2px 5px;" class="text-center">:</td>
								<td style="padding:2px 5px;">'.$row2['pass_gps'].'</td>
							</tr>
							<tr>
								<td style="padding:2px 5px 8px;" colspan="3">
									'.($row2['link_gps']?'<a href="'.$row2['link_gps'].'" target="_blank">'.$row2['link_gps'].'</a>':'').'
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div></div></div>';
		}
	}

	else if($file == "marketing"){
		$arrSql = array(1=>array("table"=>"pro_po_ds_detail", "key"=>"id_dsd"), array("table"=>"pro_po_ds_kapal", "key"=>"id_dsk"));
		$cek1 	= "select status_pengiriman, is_delivered, is_cancel, realisasi_volume, terima_jalan from ".$arrSql[$tipe]["table"]." 
				   where ".$arrSql[$tipe]["key"]." = '".$idnya."'";
		$row1 	= $conSub->getRecord($cek1);
		$arrS 	= ($row1['status_pengiriman'] ? json_decode($row1['status_pengiriman'], true) : array());
		$tmp_nm = explode("[]", $judul);
		$title	= '
			<table style="width:100%">
				<tr style="height:25px;">
					<td width="110"><b>Customer</b></td>
					<td width="20" class="text-center"><b>:</b></td>
					<td><b>'.$tmp_nm[1].'</b></td>
				</tr>
				<tr>
					<td><b>No. PO Customer</b></td>
					<td class="text-center"><b>:</b></td>
					<td><b>'.$tmp_nm[0].'</b></td>
				</tr>
			</table>';
					
		$isiTab = "";
		if(count($arrS) > 0){
			$nom = 0;
			foreach($arrS as $idxnya=>$data){
				$nom++;
				$tanggal = tgl_indo(substr($data['tanggal'],0,10), 'long', 'ndb').' '.substr($data['tanggal'],11); 
				$isiTab .= '<tr>
					<td class="text-center">'.$nom.'</td>
					<td class="text-center">'.$tanggal.'</td>
					<td class="text-left">'.$data['status'].'</td>
				</tr>';
			}
		} else{
			$isiTab .= '<tr><td colspan="3" class="text-center">Histori pengiriman belum ada</td></tr>';
		}
		$tambahan = "";
		if($row1['is_delivered'] && !$row1['is_cancel']){
			if(paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']) != 14){
				$tambahan .= '
				<div class="row"><div class="col-sm-6 col-md-4"><div style="border:1px solid #ddd; margin-bottom:10px;">
					<div style="background-color:#f4f4f4; padding:8px 5px; font-size:11px; font-family:arial; font-weight:bold; border-bottom:1px solid #ddd;">Keterangan Lain</div>
					<div class="table-responsive">
						<table class="table no-border" style="margin-bottom: 0px;">
							<tbody>
								<tr>
									<td style="padding:8px 5px 2px;" width="100">Volume</td>
									<td style="padding:8px 5px 2px;" width="10" class="text-center">:</td>
									<td style="padding:8px 5px 2px;">'.($volume?number_format($volume).' Liter':'&nbsp;').'</td>
								</tr>
								<tr>
									<td style="padding:8px 5px 2px;" width="100">Realisasi Volume</td>
									<td style="padding:8px 5px 2px;" width="10" class="text-center">:</td>
									<td style="padding:8px 5px 2px;">'.($row1['realisasi_volume']?number_format($row1['realisasi_volume']).' Liter':'&nbsp;').'</td>
								</tr>
								<tr>
									<td style="padding:2px 5px 8px;">Terima Surat Jalan</td>
									<td style="padding:2px 5px 8px;" class="text-center">:</td>
									<td style="padding:2px 5px 8px;">'.$row1['terima_jalan'].'</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div></div></div>';
			}
		} else if(!$row1['is_delivered'] && !$row1['is_cancel']){
			$cek2 = "select nomor_plat, link_gps, user_gps, pass_gps, membercode_gps from pro_master_transportir_mobil where id_master = '".$mobil."'";
			$row2 = $conSub->getRecord($cek2);
			if($tipe == 1){
				$tambahan .= '
				<div class="row"><div class="col-sm-8 col-md-6"><div style="border:1px solid #ddd; margin-bottom:10px;">
					<div style="background-color:#f4f4f4; padding:8px 5px; font-size:11px; font-family:arial; font-weight:bold; border-bottom:1px solid #ddd;">
					GPS TRUCK '.$row2['nomor_plat'].'</div>
					<div class="table-responsive">
						<table class="table no-border" style="margin-bottom: 0px;">
							<tbody>
								<tr>
									<td style="padding:8px 5px 2px;" width="85">Member Code</td>
									<td style="padding:8px 5px 2px;" width="10" class="text-center">:</td>
									<td style="padding:8px 5px 2px;">'.$row2['membercode_gps'].'</td>
								</tr>
								<tr>
									<td style="padding:2px 5px;">Username</td>
									<td style="padding:2px 5px;" class="text-center">:</td>
									<td style="padding:2px 5px;">'.$row2['user_gps'].'</td>
								</tr>
								<tr>
									<td style="padding:2px 5px;">Password</td>
									<td style="padding:2px 5px;" class="text-center">:</td>
									<td style="padding:2px 5px;">'.$row2['pass_gps'].'</td>
								</tr>
								<tr>
									<td style="padding:2px 5px 8px;" colspan="3">
										'.($row2['link_gps']?'<a href="'.$row2['link_gps'].'" target="_blank">'.$row2['link_gps'].'</a>':'').'
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div></div></div>';
			}
		}
	}

	else if($file == "customer"){
		$arrSql = array(1=>array("table"=>"pro_po_ds_detail", "key"=>"id_dsd"), array("table"=>"pro_po_ds_kapal", "key"=>"id_dsk"));
		$cek1 = "select status_pengiriman, rating, komentar, is_delivered, is_cancel, realisasi_volume, terima_jalan 
				 from ".$arrSql[$tipe]["table"]." where ".$arrSql[$tipe]["key"]." = '".$idnya."'";
		$row1 = $conSub->getRecord($cek1);
		$arrS 	= json_decode($row1['status_pengiriman'], true);
		$tmp_nm = explode("[]", $judul);
		$title	= '
			<table style="width:100%">
				<tr style="height:25px;">
					<td width="110"><b>Customer</b></td>
					<td width="20" class="text-center"><b>:</b></td>
					<td><b>'.$tmp_nm[1].'</b></td>
				</tr>
				<tr>
					<td><b>No. PO Customer</b></td>
					<td class="text-center"><b>:</b></td>
					<td><b>'.$tmp_nm[0].'</b></td>
				</tr>
			</table>';

		$isiTab = "";
		if(count($arrS) > 0){
			$nom = 0;
			foreach($arrS as $idxnya=>$data){
				$nom++;
				$tanggal = tgl_indo(substr($data['tanggal'],0,10), 'long', 'ndb').' '.substr($data['tanggal'],11); 
				$isiTab .= '<tr>
					<td class="text-center">'.$nom.'</td>
					<td class="text-center">'.$tanggal.'</td>
					<td class="text-left">'.$data['status'].'</td>
				</tr>';
			}
		} else{
			$isiTab .= '<tr><td colspan="3" class="text-center">Histori pengiriman belum ada</td></tr>';
		}

		$ext_rate = '
			<div style="margin-top:3px;">
				<select id="modal-histori-rate" name="modal-histori-rate">
				  <option value="1"'.($row1['rating'] == 1?' selected':'').'>1</option>
				  <option value="2"'.($row1['rating'] == 2?' selected':'').'>2</option>
				  <option value="3"'.($row1['rating'] == 3?' selected':'').'>3</option>
				  <option value="4"'.($row1['rating'] == 4?' selected':'').'>4</option>
				  <option value="5"'.($row1['rating'] == 5?' selected':'').'>5</option>
				</select>
			</div>';
		$tambahan = '
			<div class="row"><div class="col-sm-6 col-md-4"><div style="border:1px solid #ddd; margin-bottom:10px;">
				<div style="background-color:#f4f4f4; padding:8px 5px; font-size:11px; font-family:arial; border-bottom:1px solid #ddd;"><b>Komentar &amp; Rating</b></div>
				<div style="padding:5px; font-size:11px; font-family:arial;">'.$row1['komentar'].'</div>
				<div style="padding:5px; font-size:11px; font-family:arial;">Rating : '.($row1['rating']?$ext_rate:'<i>Belum dirating</i>').'</div>
			</div></div></div>';
	}

	$answer = array("judul"=>$title, "items"=>$isiTab, "extras"=>$tambahan);
	$conSub->close();
    echo json_encode($answer);
	
?>
