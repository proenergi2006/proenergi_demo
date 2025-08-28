<?php

// Fungsi untuk melakukan permintaan GET menggunakan cURL
function curl_get($url)
{
    $datenow = date("d/m/Y H:i:s");
    $signature  = get_signature($datenow);
    // Inisialisasi sesi cURL
    $ch = curl_init();

    $bearerToken = 'aat.NTA.eyJ2IjoxLCJ1Ijo3NTM3MDEsImQiOjIwMDY3NzcsImFpIjo1MDAwMywiYWsiOiIxNzgyZjY0MS00ZjQ3LTQ0OTUtODk4Ny01OTdiMzMwNTlmZmIiLCJhbiI6IkFPTCAtU1lPUCIsImFwIjoiNjFkNDgwOGEtNGNmZS00MTFjLWI2ZjUtOTUyNDg3ZmVmMjk0IiwidCI6MTc1NTQ5ODE4NjI5NH0.TmgruJj7NKY685RR5aTbkKQVY2EwYBLuIuhLLov4AzctCPI2WLnZLWzd42588aIcb3Rd78Twk2C2ScBOken9TsJcAkNV+jcHT6PGdLq9tAiUvfcd4K+bModLapDpvV/3YVODgxDNnSOyxW//pfxuo57SjxDvkpCVqaIZ9CRnPIHtlU5qC6P+8BUL15QhX4jLT7O80g1nE+8=.Sx1+qzuyjW3YlxVzp+3z4OO26YjstDqshexfaLurDMg';

    // Setel opsi cURL
    curl_setopt($ch, CURLOPT_URL, $url); // URL API
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Api-Timestamp: ' . $datenow . '',
        'X-Api-Signature: ' . $signature . '',
        'Authorization: Bearer ' . $bearerToken,

    ]);

    // Eksekusi cURL dan simpan respons
    $response = curl_exec($ch);

    // Cek apakah terjadi error saat eksekusi
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    } else {
        // Tampilkan respons dari server
        $result = json_decode($response, true);
    }
    // Menutup sesi cURL
    curl_close($ch);

    // Mengembalikan respons
    return $result;
}

// Fungsi untuk melakukan permintaan POST menggunakan cURL
function curl_post($url, $data)
{
    // Inisialisasi sesi cURL
    $datenow = date("d/m/Y H:i:s");
    $signature = get_signature($datenow);
    $bearerToken = 'aat.NTA.eyJ2IjoxLCJ1Ijo3NTM3MDEsImQiOjIwMDY3NzcsImFpIjo1MDAwMywiYWsiOiIxNzgyZjY0MS00ZjQ3LTQ0OTUtODk4Ny01OTdiMzMwNTlmZmIiLCJhbiI6IkFPTCAtU1lPUCIsImFwIjoiNjFkNDgwOGEtNGNmZS00MTFjLWI2ZjUtOTUyNDg3ZmVmMjk0IiwidCI6MTc1NTQ5ODE4NjI5NH0.TmgruJj7NKY685RR5aTbkKQVY2EwYBLuIuhLLov4AzctCPI2WLnZLWzd42588aIcb3Rd78Twk2C2ScBOken9TsJcAkNV+jcHT6PGdLq9tAiUvfcd4K+bModLapDpvV/3YVODgxDNnSOyxW//pfxuo57SjxDvkpCVqaIZ9CRnPIHtlU5qC6P+8BUL15QhX4jLT7O80g1nE+8=.Sx1+qzuyjW3YlxVzp+3z4OO26YjstDqshexfaLurDMg';

    $ch = curl_init();

    // Menetapkan opsi cURL untuk permintaan POST
    curl_setopt($ch, CURLOPT_URL, $url); // URL yang ingin diakses
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Mengembalikan respons sebagai string
    curl_setopt($ch, CURLOPT_POST, true); // Menetapkan metode POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Data yang dikirimkan dalam bentuk query string
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Api-Timestamp: ' . $datenow . '',
        'X-Api-Signature: ' . $signature . '',
        'Authorization: Bearer ' . $bearerToken,
        // 'Content-Length: ' . strlen($jsonData)
    ]);

    // Eksekusi cURL dan mendapatkan respons
    $response = curl_exec($ch);

    // Memeriksa jika ada kesalahan
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
        curl_close($ch);
        return false;
    } else {
        // Tampilkan respons dari server
        $result = json_decode($response, true);
    }

    // Menutup sesi cURL
    curl_close($ch);

    return $result; // Mengembalikan respons
}

function curl_delete($url, $json)
{
    $datenow = date("d/m/Y H:i:s");
    $signature = get_signature($datenow);
    $bearerToken = 'aat.NTA.eyJ2IjoxLCJ1Ijo3NTM3MDEsImQiOjIwMDY3NzcsImFpIjo1MDAwMywiYWsiOiIxNzgyZjY0MS00ZjQ3LTQ0OTUtODk4Ny01OTdiMzMwNTlmZmIiLCJhbiI6IkFPTCAtU1lPUCIsImFwIjoiNjFkNDgwOGEtNGNmZS00MTFjLWI2ZjUtOTUyNDg3ZmVmMjk0IiwidCI6MTc1NTQ5ODE4NjI5NH0.TmgruJj7NKY685RR5aTbkKQVY2EwYBLuIuhLLov4AzctCPI2WLnZLWzd42588aIcb3Rd78Twk2C2ScBOken9TsJcAkNV+jcHT6PGdLq9tAiUvfcd4K+bModLapDpvV/3YVODgxDNnSOyxW//pfxuo57SjxDvkpCVqaIZ9CRnPIHtlU5qC6P+8BUL15QhX4jLT7O80g1nE+8=.Sx1+qzuyjW3YlxVzp+3z4OO26YjstDqshexfaLurDMg';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Api-Timestamp: ' . $datenow . '',
        'X-Api-Signature: ' . $signature . '',
        'Authorization: Bearer ' . $bearerToken
    ]);
    $response = curl_exec($ch);

    // Memeriksa jika ada kesalahan
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    } else {
        // Tampilkan respons dari server
        $result = json_decode($response, true);
    }

    curl_close($ch);

    return $result;
}
// Fungsi tambahan untuk menangani kode status HTTP
function get_http_status($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true); // Tidak mengambil konten, hanya untuk memeriksa status
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $http_code;
}

function get_signature($datenow)
{

    // $datenow = date("d/m/Y H:i:s");
    // Define the secret key and message
    $secret_key = 'da7rZM4C3ltFhEgQacIxFzwTuqg1av26JMIEE4pqUycw0T8KQNFAV0pE1k6kMpjt';  // Replace with your actual secret key

    // Choose the hashing algorithm (SHA-256 in this case)
    $algorithm = 'sha256';

    // Generate the raw HMAC using the secret key and message
    $hmac_raw = hash_hmac($algorithm, $datenow, $secret_key, true);  // true for raw output

    // Encode the raw HMAC to Base64
    $hmac_base64 = base64_encode($hmac_raw);

    return $hmac_base64;
}
