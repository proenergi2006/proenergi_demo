<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$idk 	= htmlspecialchars($enk["idk"], ENT_QUOTES);


	$sql1 = "
		select a.id_customer, a.nama_customer, a.kode_pelanggan, a.credit_limit, a.top_payment  
		from pro_customer a 
		order by 1 
	"; 
    $rsms = $con->getResult($sql1);
	
	echo '
	<table width="100%" cellpadding="0" cellspacing="0" border="1" style="border-collapse:collapse;">
		<tr>
			<th>No</th>
			<th>ID Customer</th>
			<th>Kode Customer</th>
			<th>Nama Customer</th>
			<th>TOP</th>
			<th>Credit Limit</th>
			<th>Not Yet</th>
			<th>Overdue 1-30 Days</th>
			<th>Overdue 31-60 Days</th>
			<th>Overdue 61-90 Days</th>
			<th>Overdue > 90 Days</th>
			<th>Reminding</th>
		</tr>
	';
	$nomor = 0;
	foreach($rsms as $data){
		$nomor++;
		$sql2 = "
			select id_customer, not_yet, ov_under_30, ov_under_60, ov_under_90, ov_up_90 
			from pro_sales_confirmation 
			where id_customer = '".$data['id_customer']."' 
			order by id desc 
		";
    	$rsms2 = $con->getRecord($sql2);
		$not_yet 		= ($rsms2['not_yet'] ? $rsms2['not_yet'] : 0);
		$ov_under_30 	= ($rsms2['ov_under_30'] ? $rsms2['ov_under_30'] : 0);
		$ov_under_60 	= ($rsms2['ov_under_60'] ? $rsms2['ov_under_60'] : 0);
		$ov_under_90 	= ($rsms2['ov_under_90'] ? $rsms2['ov_under_90'] : 0);
		$ov_up_90 		= ($rsms2['ov_up_90'] ? $rsms2['ov_up_90'] : 0);
		$reminding 		= ($data['credit_limit'] ? $data['credit_limit'] - ($not_yet + $ov_under_30 + $ov_under_60 + $ov_under_90 + $ov_up_90) : 0);
		echo '
			<tr>
				<td>'.$nomor.'</td>
				<td>'.$data['id_customer'].'</td>
				<td>'.$data['kode_pelanggan'].'</td>
				<td>'.$data['nama_customer'].'</td>
				<td>'.$data['top_payment'].'</td>
				<td>'.$data['credit_limit'].'</td>
				<td>'.$not_yet.'</td>
				<td>'.$ov_under_30.'</td>
				<td>'.$ov_under_60.'</td>
				<td>'.$ov_under_90.'</td>
				<td>'.$ov_up_90.'</td>
				<td>'.$reminding.'</td>
			</tr>
		';
	}
	echo '</table>';
