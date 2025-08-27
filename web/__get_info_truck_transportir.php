<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$q1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);
	$q2 	= htmlspecialchars($_POST["q2"], ENT_QUOTES);
	$q3 	= htmlspecialchars($_POST["q3"], ENT_QUOTES);
	$truk	= ($q2)?$q2:$q1;
	
	$sql = "select a.max_truk, a.min_vol_kirim, b.max_kap, b.komp_tanki, b.nomor_plat from pro_customer_lcr a, pro_master_transportir_mobil b 
			where a.id_lcr = '".$q3."' and b.id_master = '".$truk."'";
	$rgs = $conSub->getRecord($sql);
	$kap = ($rgs['max_kap'])?$rgs['max_kap'].' KL':'-';
	$max = ($rgs['max_truk'])?$rgs['max_truk'].' KL':'-';
	$min = ($rgs['min_vol_kirim'])?$rgs['min_vol_kirim'].' KL':'-';
	$kom = json_decode($rgs['komp_tanki'], true);
	if($rgs['nomor_plat']){
		echo '<h3 style="margin: 0px 0px 10px; font-weight: bold; font-size: 16px;">Truck '.$rgs['nomor_plat'].'</h3>';
		echo '<p style="margin-bottom:3px;"><b><u>TRUCK (ACTUAL)</u></b></p>';
		echo '<p style="margin-bottom:0px;">Kapasitas Max: '.$kap.'</p>';
		echo '<div class="row"><div class="col-sm-10"><div class="table-responsive">
			<table class="table table-bordered table-detail">
				<thead>
					<tr>
						<th class="text-center" width="50%">Kompartemen</th>
						<th class="text-center" width="50%">Tanki</th>
					</tr>
				</thead>
				<tbody>';
		if(count($kom) > 0){
			foreach($kom as $dx){
				echo '<tr><td>'.$dx['kompart'].'</td><td>'.$dx['tanki'].'</td></tr>';
			}
		} else{
			echo '<tr><td class="text-center" colspan="2">Data tidak ditemukan</td></tr>';
		}
		echo '</tbody></table></div></div></div>';
		echo '<p style="margin-bottom:3px;"><b><u>TRUCK (LCR)</u></b></p>';
		echo '<p style="margin-bottom:0px;">Min : '.$min.'</p>';
		echo '<p style="margin-bottom:0px;">Max : '.$max.'</p>';
		
	} else{
		echo '<p class="text-center" style="margin-bottom:0px">Truck tidak ditemukan</p>';
	}
	$conSub->close();
?>
