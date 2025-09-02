<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

if (isset($_POST['kode_vendor']) && !empty($_POST['kode_vendor'])) {
    $kode_vendor = $_POST['kode_vendor'];

    $datenow = date("d/m/Y H:i:s");

    $secret_key = 'da7rZM4C3ltFhEgQacIxFzwTuqg1av26JMIEE4pqUycw0T8KQNFAV0pE1k6kMpjt';  // Replace with your actual secret key

    // Choose the hashing algorithm (SHA-256 in this case)
    $algorithm = 'sha256';

    // Generate the raw HMAC using the secret key and message
    $hmac_raw = hash_hmac($algorithm, $datenow, $secret_key, true);  // true for raw output

    // Encode the raw HMAC to Base64
    $hmac_base64 = base64_encode($hmac_raw);

    // Inisialisasi sesi cURL
    $ch = curl_init();

    $bearerToken = 'aat.NTA.eyJ2IjoxLCJ1Ijo3NTM3MDEsImQiOjIwMDY3NzcsImFpIjo1MDAwMywiYWsiOiIxNzgyZjY0MS00ZjQ3LTQ0OTUtODk4Ny01OTdiMzMwNTlmZmIiLCJhbiI6IkFPTCAtU1lPUCIsImFwIjoiNjFkNDgwOGEtNGNmZS00MTFjLWI2ZjUtOTUyNDg3ZmVmMjk0IiwidCI6MTc1NTQ5ODE4NjI5NH0.TmgruJj7NKY685RR5aTbkKQVY2EwYBLuIuhLLov4AzctCPI2WLnZLWzd42588aIcb3Rd78Twk2C2ScBOken9TsJcAkNV+jcHT6PGdLq9tAiUvfcd4K+bModLapDpvV/3YVODgxDNnSOyxW//pfxuo57SjxDvkpCVqaIZ9CRnPIHtlU5qC6P+8BUL15QhX4jLT7O80g1nE+8=.Sx1+qzuyjW3YlxVzp+3z4OO26YjstDqshexfaLurDMg';

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
