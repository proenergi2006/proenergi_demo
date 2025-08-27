<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$enk = decode($_SERVER['REQUEST_URI']);

$filetipe = htmlspecialchars($enk["tipe"], ENT_QUOTES);
$filename = htmlspecialchars($enk["file"], ENT_QUOTES);
$kategori = htmlspecialchars($enk["ktg"], ENT_QUOTES);
$ext = pathinfo($filename);

if ($filetipe == 1) {
	$filepath = $public_base_directory . '/files/uploaded_user/images';
	$filelink = $filepath . '/' . $kategori . $filename;
} else if ($filetipe == 2) {
	$filepath = $public_base_directory . '/files/uploaded_user/lampiran';
	$filelink = $filepath . '/' . $kategori . $filename;
} else if ($filetipe == 3) {
	$filelink = $kategori;
} else if ($filetipe == 'unblock') {
	$filepath = $public_base_directory . '/files/uploaded_user/lampiran/unblock';
	$filelink = $filepath . '/' . $kategori;
} else if ($filetipe == 6) {
	$filepath = $public_base_directory . '/files/uploaded_user/urgent';
	$filelink = $filepath . '/' . $kategori . $filename;
} else if ($filetipe == 7) {
	$filepath = $public_base_directory . '/files/uploaded_user/lampiran';
	$filelink = $filepath . '/' . $kategori . $filename;
} else if ($filetipe == 108900) {
	$filepath = $public_base_directory . '/files/uploaded_user/lampiran';
	$filelink = $filepath . '/' . $kategori;
} else {
	$filepath = $public_base_directory . '/files/uploaded_user/lampiran';
	$filelink = $filepath . '/' . $kategori . $filename;
}


if (file_exists($filelink)) {
	header('Content-Description: File Transfer');
	if ($ext['extension'] != 'pdf') {
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
	} else {
		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="' . $filename . '"');
	}
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($filelink));

	ob_clean();
	flush();
	readfile($filelink);
	exit;
} else {
	header("location: " . BASE_REFERER);
	exit;
}
