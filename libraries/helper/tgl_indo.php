<?php
function is_array_empty($arr)
{
	$empty = true;
	if (is_array($arr)) {
		foreach ($arr as $idx => $val) {
			$empty = $empty && is_array_empty($val);
		}
	} else {
		$empty = empty($arr);
	}
	return $empty;
}
function is_array_no_empty($arr)
{
	$empty = true;
	if (is_array($arr)) {
		foreach ($arr as $idx => $val) {
			$empty = $empty && is_array_no_empty($val);
		}
	} else {
		$empty = !empty($arr);
	}
	return $empty;
}
function terbilang($x)
{
	$satuan = array(1 => "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
	if ($x < 12) {
		if (isset($satuan[$x]))
			return $satuan[$x];
	} else if ($x < 20)
		return terbilang($x - 10) . " Belas";
	else if ($x < 100)
		return terbilang($x / 10) . " Puluh " . terbilang($x % 10);
	else if ($x < 200)
		return " Seratus " . terbilang($x - 100);
	else if ($x < 1000)
		return terbilang($x / 100) . " Ratus " . terbilang($x % 100);
	else if ($x < 2000)
		return " Seribu " . terbilang($x - 1000);
	else if ($x < 1000000)
		return terbilang($x / 1000) . " Ribu " . terbilang($x % 1000);
	else if ($x < 1000000000)
		return terbilang($x / 1000000) . " Juta " . terbilang($x % 1000000);
}

function terbilang_inggris($x)
{
	$units = array(1 => "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve");
	$tens = array(2 => "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety");

	if ($x < 0) {
		return "Negative " . terbilang_inggris(abs($x));
	} elseif ($x < 13) {
		return $units[$x];
	} elseif ($x < 20) {
		return terbilang_inggris($x - 10) . " Teen";
	} elseif ($x < 100) {
		return $tens[$x / 10] . " " . terbilang_inggris($x % 10);
	} elseif ($x < 1000) {
		return terbilang_inggris($x / 100) . " Hundred " . terbilang_inggris($x % 100);
	} elseif ($x < 1000000) {
		return terbilang_inggris($x / 1000) . " Thousand " . terbilang_inggris($x % 1000);
	} elseif ($x < 1000000000) {
		return terbilang_inggris($x / 1000000) . " Million " . terbilang_inggris($x % 1000000);
	} elseif ($x < 1000000000000) {
		return terbilang_inggris($x / 1000000000) . " Billion " . terbilang_inggris($x % 1000000000);
	}
}


function timeHarian($x)
{
	if ($x == 0)
		return "";
	else if ($x < 3600)
		return floor($x / 60) . " Menit";
	else if ($x < 86400)
		return floor($x / 3600) . " Jam " . timeHarian($x % 3600);
	else
		return floor($x / 86400) . " Hari " . timeHarian($x % 86400);
}

function timeManHours($x)
{
	if ($x == 0)
		return "";
	else if ($x < 3600)
		return floor($x / 60) . " Menit";
	else
		return floor($x / 3600) . " Jam " . timeHarian($x % 3600);
}

function tgl_db($tgl, $sep = "/")
{
	if ($tgl != "") {
		$tanggal = substr($tgl, 0, 2);
		$bulan 	 = substr($tgl, 3, 2);
		$tahun 	 = substr($tgl, 6, 4);
		return $tahun . $sep . $bulan . $sep . $tanggal;
	} else
		return $tgl;
}
function tgl_indo($tgl, $format = 'long', $from = 'db', $sep = ' ')
{
	if ($tgl != "" && $tgl != "0000-00-00" && $tgl != "0000-00-00 00:00:00") {
		if ($from == "ndb") {
			$tanggal = substr($tgl, 0, 2);
			if ($format == "long") {
				$bulan = getBulan(substr($tgl, 3, 2));
			} else if ($format == "short") {
				$bulan = getBulanShort(substr($tgl, 3, 2));
			} else if ($format == "normal") {
				$bulan = substr($tgl, 3, 2);
			}
			$tahun 	 = substr($tgl, 6, 4);
		} else if ($from == "db") {
			$tanggal = substr($tgl, 8, 2);
			if ($format == "long") {
				$bulan = getBulan(substr($tgl, 5, 2));
			} else if ($format == "short") {
				$bulan = getBulanShort(substr($tgl, 5, 2));
			} else if ($format == "normal") {
				$bulan = substr($tgl, 5, 2);
			}
			$tahun 	 = substr($tgl, 0, 4);
		}
		$frmt	 = $tanggal . $sep . $bulan . $sep . $tahun;
		return $frmt;
	} else if ($tgl == "0000-00-00")
		return $tgl = "";
}
function tgl_eng($tgl, $format = 'long', $from = 'db', $sep = ' ')
{
	if ($tgl != "" && $tgl != "0000-00-00" && $tgl != "0000-00-00 00:00:00") {
		if ($from == "ndb") {
			$tanggal = substr($tgl, 0, 2);
			if ($format == "long") {
				$bulan = getBulanEng(substr($tgl, 3, 2));
			} else if ($format == "short") {
				$bulan = getBulanShort(substr($tgl, 3, 2));
			} else if ($format == "normal") {
				$bulan = substr($tgl, 3, 2);
			}
			$tahun 	 = substr($tgl, 6, 4);
		} else if ($from == "db") {
			$tanggal = substr($tgl, 8, 2);
			if ($format == "long") {
				$bulan = getBulanEng(substr($tgl, 5, 2));
			} else if ($format == "short") {
				$bulan = getBulanShort(substr($tgl, 5, 2));
			} else if ($format == "normal") {
				$bulan = substr($tgl, 5, 2);
			}
			$tahun 	 = substr($tgl, 0, 4);
		}
		$frmt	 = $tanggal . $sep . $bulan . $sep . $tahun;
		return $frmt;
	} else if ($tgl == "0000-00-00")
		return $tgl = "";
}
function getBulan($bln)
{
	switch (intval($bln)) {
		case 1:
			return "Januari";
			break;
		case 2:
			return "Februari";
			break;
		case 3:
			return "Maret";
			break;
		case 4:
			return "April";
			break;
		case 5:
			return "Mei";
			break;
		case 6:
			return "Juni";
			break;
		case 7:
			return "Juli";
			break;
		case 8:
			return "Agustus";
			break;
		case 9:
			return "September";
			break;
		case 10:
			return "Oktober";
			break;
		case 11:
			return "November";
			break;
		case 12:
			return "Desember";
			break;
	}
}
function getBulanEng($bln)
{
	switch (intval($bln)) {
		case 1:
			return "January";
			break;
		case 2:
			return "February";
			break;
		case 3:
			return "March";
			break;
		case 4:
			return "April";
			break;
		case 5:
			return "May";
			break;
		case 6:
			return "June";
			break;
		case 7:
			return "Juli";
			break;
		case 8:
			return "Augusts";
			break;
		case 9:
			return "September";
			break;
		case 10:
			return "October";
			break;
		case 11:
			return "November";
			break;
		case 12:
			return "December";
			break;
	}
}
function getBulanShort($bln)
{
	switch (intval($bln)) {
		case 1:
			return "Jan";
			break;
		case 2:
			return "Feb";
			break;
		case 3:
			return "Mar";
			break;
		case 4:
			return "Apr";
			break;
		case 5:
			return "Mei";
			break;
		case 6:
			return "Jun";
			break;
		case 7:
			return "Jul";
			break;
		case 8:
			return "Agu";
			break;
		case 9:
			return "Sep";
			break;
		case 10:
			return "Okt";
			break;
		case 11:
			return "Nov";
			break;
		case 12:
			return "Des";
			break;
	}
}

function create_thumbnail($source, $destination, $image_type, $file_temp)
{
	$dimension 	= getimagesize($file_temp);
	$ratio 		= $dimension[0] / $dimension[1];
	if ($ratio > 1) {
		$thumbWidth  = 200;
		$thumbHeight = 200 / $ratio;
	} else {
		$thumbWidth  = 200 * $ratio;
		$thumbHeight = 200;
	}
	$thumb 	= imagecreatetruecolor($thumbWidth, $thumbHeight);
	imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $dimension[0], $dimension[1]);
	save_image($thumb, $destination, $image_type);
	imagedestroy($thumb);
}

function save_image($source, $destination, $image_type, $quality = 60)
{
	switch (strtolower($image_type)) {
		case 'image/png':
			return imagepng($source, $destination);
			break;
		case 'image/gif':
			return imagegif($source, $destination);
			break;
		case 'image/jpeg':
		case 'image/pjpeg':
			return imagejpeg($source, $destination, $quality);
			break;
		default:
			break;
	}
}

function sanitize_filename($string)
{
	$strip = array(
		"&amp;",
		"&",
		"/",
		"\\",
		"?",
		"%",
		"*",
		":",
		"|",
		"&quot;",
		"\"",
		"&#039;",
		"'",
		"<",
		"&lt;",
		">",
		"&gt;",
		",",
		"~",
		"`",
		"!",
		"@",
		"#",
		"$",
		"^",
		"(",
		")",
		"=",
		"+",
		"[",
		"]",
		"{",
		"}",
		";",
		"&#8216;",
		"&#8217;",
		"&#8220;",
		"&#8221;",
		"—",
		"–"
	);
	$clean = trim(str_replace($strip, "", strip_tags($string)));
	$clean = preg_replace('/\s+/', "_", $clean);
	return strtolower($clean);
}

function random_password($length = 8)
{
	$chars 		= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%?";
	$password 	= substr(str_shuffle($chars), 0, $length);
	return $password;
}

function weekOfMonth($date)
{
	$firstOfMonth = (date("Y-m-01", strtotime($date)));
	return intval(date("W", strtotime($date))) - intval(date("W", strtotime($firstOfMonth)));
}
