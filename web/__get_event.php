<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$flash	= new FlashAlerts;

	$sesuser 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
	$sesrole 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
    $seswil 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    $sesgroup 	= paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);

	$start 	= htmlspecialchars($_POST["start"], ENT_QUOTES);
	$end 	= htmlspecialchars($_POST["end"], ENT_QUOTES);
	$bulan 	= htmlspecialchars($_POST["bulan"], ENT_QUOTES) + 1;
	$tahun 	= htmlspecialchars($_POST["tahun"], ENT_QUOTES);
	$tipe 	= htmlspecialchars($_POST["tipe"], ENT_QUOTES);
	$ruang 	= htmlspecialchars($_POST["ruangan"], ENT_QUOTES);
	$tZone 	= "+07:00";

	$startOr = $tahun.'-'.$bulan.'-01';
	$endOr	 = $tahun.'-'.$bulan.'-'.cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
	$arrData = array();
	
	$sql = "
		select 
		a.id_reservasi, a.id_ruangan, b.id_cabang, d.nama_cabang, b.nama_ruangan, a.id_user, c.fullname, 
		a.tanggal_reservasi, a.jam_reservasi, 
		cast(substring(a.jam_reservasi, 1, 5) as time) as jam_mulai, 
		cast(substring(a.jam_reservasi, 7, 5) as time) as jam_selesai, 
		a.personel, a.keperluan  
		from pro_reservasi_ruangan a 
		join pro_master_ruangan b on a.id_ruangan = b.id_ruangan 
		join pro_master_cabang d on b.id_cabang = d.id_master 
		left join acl_user c on a.id_user = c.id_user 
		where 1=1 and a.id_ruangan = '".$ruang."' 
			and month(a.tanggal_reservasi) = '".$bulan."' and year(a.tanggal_reservasi) = '".$tahun."' 
		order by a.tanggal_reservasi, jam_mulai
	";
	$res = $conSub->getResult($sql);
	$conSub->close(); $conSub = NULL;

	if(count($res)){
		foreach($res as $data){
			$arr = array();
			$arr["title"] 		= $data['fullname']."\n".$data['keperluan'];
			$arr["fullname"] 	= $data['fullname'];
			$arr["ruangan"] 	= $data['nama_ruangan'];
			$arr["keperluan"] 	= $data['keperluan'];
			$arr["personel"] 	= $data['personel'];

			$start_time 		= ($data['tanggal_reservasi'] != "" && $data['jam_mulai'] != "00:00:00" && !$data['is_allday'])?"T".$data['jam_mulai'].$tZone:"";
			$arr["start"] 		= $data['tanggal_reservasi'].$start_time;
			$arr["startEvent"] 	= $arr["start"];

			$endTime 			= ($data['tanggal_reservasi'] != "" && $data['jam_selesai'] != "00:00:00" && !$data['is_allday'])?"T".$data['jam_selesai'].$tZone:"";
			$arr["end"] 		= $data['tanggal_reservasi'].$endTime;
			$arr["endEvent"] 	= $arr["end"];

			if($tipe == "non-order" && ($data['id_user'] == $sesuser || $sesrole == '1' || $sesrole == '14')){
				$arr["backgroundColor"] = "#00a65a";
				$arr["borderColor"] 	= "#000000";
				$arr["isOrder"]			= true;
				$arr["eventdata"] 		= paramEncrypt($data['id_reservasi']);
			}


			if($arr['start'] != "" && $arr['end'] != ""){
				$tglStart 	= substr($arr['start'],0,7);
				$tglEnd   	= substr($arr['end'],0,7);
				$tglDiff  	= $tglStart == $tglEnd;
				if(!$tglDiff){
					while(!$tglDiff){
						$arrTglBaru	= array();
						$tStartTgl	= date("d", strtotime($arr['start']));
						$tStartBln	= date("m", strtotime($arr['start']));
						$tStartThn	= date("Y", strtotime($arr['start']));
						$arrTglBaru['start'] = date("Y-m-d", strtotime($arr['start'])).$start_time;
						$arr['start'] 		 = date("Y-m-d", strtotime('first day of +1 month', mktime(0, 0, 0, $tStartBln, $tStartTgl, $tStartThn)));
						$arrTglBaru['end'] 	 = $arr['start'].$endTime;
						
						$tglStart 	= substr($arr['start'],0,7);
						$tglEnd   	= substr($arr['end'],0,7);
						$tglDiff  	= $tglStart == $tglEnd;
						array_push($arrData, array_merge($arr, $arrTglBaru)); 
					}
					array_push($arrData, array_merge($arr, array("start"=>$arr['start'].$start_time, "end"=>$arr['end'].$endTime))); 
				} else{
					array_push($arrData, $arr);
				}
			} else{
				array_push($arrData, $arr);
			}

		}
	}
	echo json_encode($arrData);
?>
