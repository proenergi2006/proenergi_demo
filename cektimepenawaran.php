<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "htmlawed", "mailgen");

	$conDr1 = new Connection();
	$cek01 	= "select id_user, username from acl_user where id_role = 3 and username = 'COO'";
	$row01 	= $conDr1->getRecord($cek01);
	
	if($row01['id_user']){
		$sql01 	= "
			select a.*, time_to_sec(a.date_difference) as detiknya 
			from (
				select a.id_penawaran, a.id_customer, a.volume_tawar, a.catatan, a.detail_rincian, 
				a.om_tanggal, TIMEDIFF(NOW(), a.om_tanggal) AS date_difference 
				from pro_penawaran a  
				where 1=1 
					and flag_disposisi = 5 and flag_approval = 0 
					and om_result > 0 and year(masa_awal) >= 2023
			) a 
			where 1=1 and time_to_sec(date_difference) > 900 
		";
		$res01 = $conDr1->getResult($sql01);
	
		$ems1 = ""; $oke = true;
		if(count($res01) > 0){
			$ems1 = "select email_user from acl_user where id_role = 21";
			$sbjk = "Persetujuan Penawaran [".date('d/m/Y H:i:s')."]";
			$pesn = "Sistem SYOP meminta persetujuan untuk penawaran, dikarenakan dalam jangka waktu lebih dari 15 menit, COO belum memutuskan persetujuan untuk penawaran ini";
			
			foreach($res01 as $data){
				$id01 	= $data['id_customer'];
				$id02 	= $data['id_penawaran'];
				
				$rincian 		= json_decode($data['detail_rincian'], true);
				$cnt_rincian 	= 0;
				$ls_harga_dasar	= 0;
				$ls_oa_kirim	= 0;
				$ls_ppn			= 0;
				$ls_pbbkb		= 0;

				if(is_array($rincian)){
					foreach($rincian as $arr1){
						$cnt_rincian++;
						
						if($cnt_rincian == '1') {
							$ls_harga_dasar	= ($arr1['biaya'] ? $arr1['biaya'] : 0);
						} else if($cnt_rincian == '2'){
							$ls_oa_kirim = ($arr1['biaya'] ? $arr1['biaya'] : 0);
						} else if($cnt_rincian == '3'){
							$ls_ppn = ($arr1['biaya'] ? $arr1['biaya'] : 0);
						} else if($cnt_rincian == '4'){
							$ls_pbbkb = ($arr1['biaya'] ? $arr1['biaya'] : 0);
						}
					} 
				}

				$ls_harga_dasar	= ($ls_harga_dasar ? $ls_harga_dasar : 0);
				$ls_oa_kirim	= ($ls_oa_kirim ? $ls_oa_kirim : 0);
				$ls_ppn			= ($ls_ppn ? $ls_ppn : 0);
				$ls_pbbkb		= ($ls_pbbkb ? $ls_pbbkb : 0);
				$ls_volume		= ($data['volume_tawar'] ? $data['volume_tawar'] : 0);

				$sql02 	= "
					update pro_penawaran set coo_summary = 'Approved By System Automatically', coo_result = 1, coo_pic = 'Syop System', coo_tanggal = NOW(), 
					flag_disposisi = 6  
					where id_customer = '".$id01."' and id_penawaran = '".$id02."'
				";
				$conDr1->setQuery($sql02);
				$oke  = $oke && !$conDr1->hasError();
	
				$sql03 = "
					insert into pro_approval_hist (kd_approval, result, summary, id_user, tgl_approval, id_customer, id_penawaran, id_role, harga_dasar, oa_kirim, pbbkb, ppn, keterangan_pengajuan, volume)
					values ('P001', '1', 'Approved By System Automatically', '".$row01['id_user']."', NOW(), '".$id01."', '".$id02."', '3', 
					".$ls_harga_dasar.", ".$ls_oa_kirim.", ".$ls_pbbkb.", ".$ls_ppn.", '".$data['catatan']."', ".$ls_volume.");";
				$conDr1->setQuery($sql03);
				$oke  = $oke && !$conDr1->hasError();
			}
		}
	
		if($ems1){
			$rms1 = $conDr1->getResult($ems1);
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 465;
			$mail->SMTPSecure = 'ssl';
			$mail->SMTPAuth = true;
			$mail->SMTPKeepAlive = true;
			$mail->Username = USR_EMAIL_PROENERGI202389;
			$mail->Password = PWD_EMAIL_PROENERGI202389;
			
			$mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
			foreach($rms1 as $datms){
				$mail->addAddress($datms['email_user']);
			}
			$mail->Subject = $sbjk;
			$mail->msgHTML($pesn);
			$mail->send();
		}
	}
	$conDr1->close();
?>
