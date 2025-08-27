<?php
	date_default_timezone_set('Asia/Jakarta');
	$arr = array();
	$arr['tanggal'] = date('d/m/Y');
	$arr['jam'] 	= date('H');
	$arr['menit'] 	= date('i');
	echo json_encode($arr);
?>
