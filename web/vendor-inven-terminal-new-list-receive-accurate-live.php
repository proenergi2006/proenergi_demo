<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

if (isset($_POST['kode_vendor']) && !empty($_POST['kode_vendor'])) {
    $kode_vendor = $_POST['kode_vendor'];

    $datenow = date("d/m/Y H:i:s");

    $secret_key = 'ofLHHzlzMImOBwrvcIvABsH5TtTEtj9DUvAQ4uUvoFNljW6w276Lbv9FT2U1f16P';  // Replace with your actual secret key

    // Choose the hashing algorithm (SHA-256 in this case)
    $algorithm = 'sha256';

    // Generate the raw HMAC using the secret key and message
    $hmac_raw = hash_hmac($algorithm, $datenow, $secret_key, true);  // true for raw output

    // Encode the raw HMAC to Base64
    $hmac_base64 = base64_encode($hmac_raw);

    // Inisialisasi sesi cURL
    $ch = curl_init();

    $bearerToken = 'aat.NTA.eyJ2IjoxLCJ1Ijo4MTAyNDQsImQiOjE1OTgyMzIsImFpIjo1OTc1NCwiYWsiOiI4YTEzZjE5Zi00MDg3LTRjN2EtYTNlNi05MjA0ZjZlZDNkODMiLCJhbiI6IlNZT1AiLCJhcCI6ImRjNTExN2Y2LWFlZDMtNDQ5ZC05Mzg5LTIwMGE1MTRhYzEyNyIsInQiOjE3NTgyNzY3MTE1Njd9.ImlCdTQlscn76RItUg5PsUGw/5wTewnz46zwQqmLCpnCPNAa2OCqJLJCkRvCoFAdfevt0X5oD5q47tLKAp6POy28qq88+Wtzje0YIpATTjPes19ncoyoYLddn0bhI4aJ1TegMcXhIiXxvHZWvrro5d04dmT2zWB/5dPw6AAaXPX/PUtIujnKTLK8xFe583n/bijzkgEd77U=.D7ffeIJPqbofBNXfIXZ3O825qB7gVXAusVBx8eL8HaA';

    $query_item = http_build_query([
        'fields' => 'id,number,shipDate',
        'filter.vendorNo' => "$kode_vendor"
    ]);

    $url = 'https://zeus.accurate.id/accurate/api/receive-item/list.do?' . $query_item;
    // Setel opsi cURL
    curl_setopt($ch, CURLOPT_URL, $url); // URL API
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Api-Timestamp: ' . $datenow . '',
        'X-Api-Signature: ' . $hmac_base64 . '',
        'Authorization: Bearer ' . $bearerToken,

    ]);

    // Eksekusi cURL dan simpan respons
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(["status" => false, "message" => curl_error($ch)]);
    } else {
        // Convert response JSON menjadi array PHP
        $data = json_decode($response, true);

        // Cek apakah data dari API valid
        if ($data) {
            echo json_encode(["status" => true, "data" => $data['d']]);
        } else {
            echo json_encode(["status" => false, "message" => "Tidak ada item yang ditemukan."]);
        }
    }
    // Menutup sesi cURL
    curl_close($ch);
} else {
    echo json_encode(["status" => false, "message" => "Kode Vendor belum terisi."]);
}
