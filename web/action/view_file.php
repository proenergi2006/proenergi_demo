<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

$auth	= new MyOtentikasi();
$con 	= new Connection();
$flash	= new FlashAlerts;
$url = paramDecrypt($_GET['url']); // Ensure this function works correctly
$tipe = $_GET['tipe'];
if ($tipe == "ktp") {
	$pathfile = $public_base_directory . '/files/uploaded_user/ktp_penerima_refund';
} elseif ($tipe == "npwp") {
	$pathfile = $public_base_directory . '/files/uploaded_user/npwp_penerima_refund';
} elseif ($tipe == "realisasi_bpuj") {
	$pathfile = $public_base_directory . '/files/uploaded_user/lampiran_realisasi_bpuj';
}

// echo $tipe;

// Ensure you are accessing the correct part of the exploded URL
if (isset($url)) {
	// Use glob to find the matching files
	$tmpPot = glob($pathfile . "/" . $url, GLOB_BRACE);

	// Check if any files were found
	if (count($tmpPot) > 0) {
		foreach ($tmpPot as $datj) {
			// Check if the file exists before trying to serve it
			if (file_exists($datj)) {
				$fileInfo = pathinfo($datj);
				$extension = strtolower($fileInfo['extension']);

				// Set the appropriate Content-Type
				switch ($extension) {
					case 'pdf':
						header('Content-Type: application/pdf');
						break;
					case 'png':
						header('Content-Type: image/png');
						break;
					case 'jpeg':
					case 'jpg':
						header('Content-Type: image/jpeg');
						break;
					default:
						// Handle unsupported file types
						header('HTTP/1.0 415 Unsupported Media Type');
						echo 'Unsupported file type.';
						exit;
				}

				// Set Content-Disposition header
				header('Content-Disposition: inline; filename="' . basename($datj) . '"');
				header('Content-Length: ' . filesize($datj));

				// Output the file
				readfile($datj);
				exit;
			}
		}
	} else {
		echo "No matching files found.";
	}
} else {
	echo "Invalid URL provided.";
}
