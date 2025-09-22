<?php

// Fungsi untuk melakukan permintaan GET menggunakan cURL
function curl_get($url)
{
    $datenow = date("d/m/Y H:i:s");
    $signature  = get_signature($datenow);
    // Inisialisasi sesi cURL
    $ch = curl_init();

    $bearerToken = 'aat.NTA.eyJ2IjoxLCJ1Ijo4MTAyNDQsImQiOjE1OTgyMzIsImFpIjo1OTc1NCwiYWsiOiI4YTEzZjE5Zi00MDg3LTRjN2EtYTNlNi05MjA0ZjZlZDNkODMiLCJhbiI6IlNZT1AiLCJhcCI6ImRjNTExN2Y2LWFlZDMtNDQ5ZC05Mzg5LTIwMGE1MTRhYzEyNyIsInQiOjE3NTgyNzY3MTE1Njd9.ImlCdTQlscn76RItUg5PsUGw/5wTewnz46zwQqmLCpnCPNAa2OCqJLJCkRvCoFAdfevt0X5oD5q47tLKAp6POy28qq88+Wtzje0YIpATTjPes19ncoyoYLddn0bhI4aJ1TegMcXhIiXxvHZWvrro5d04dmT2zWB/5dPw6AAaXPX/PUtIujnKTLK8xFe583n/bijzkgEd77U=.D7ffeIJPqbofBNXfIXZ3O825qB7gVXAusVBx8eL8HaA';

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
    $bearerToken = 'aat.NTA.eyJ2IjoxLCJ1Ijo4MTAyNDQsImQiOjE1OTgyMzIsImFpIjo1OTc1NCwiYWsiOiI4YTEzZjE5Zi00MDg3LTRjN2EtYTNlNi05MjA0ZjZlZDNkODMiLCJhbiI6IlNZT1AiLCJhcCI6ImRjNTExN2Y2LWFlZDMtNDQ5ZC05Mzg5LTIwMGE1MTRhYzEyNyIsInQiOjE3NTgyNzY3MTE1Njd9.ImlCdTQlscn76RItUg5PsUGw/5wTewnz46zwQqmLCpnCPNAa2OCqJLJCkRvCoFAdfevt0X5oD5q47tLKAp6POy28qq88+Wtzje0YIpATTjPes19ncoyoYLddn0bhI4aJ1TegMcXhIiXxvHZWvrro5d04dmT2zWB/5dPw6AAaXPX/PUtIujnKTLK8xFe583n/bijzkgEd77U=.D7ffeIJPqbofBNXfIXZ3O825qB7gVXAusVBx8eL8HaA';

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
    $bearerToken = 'aat.NTA.eyJ2IjoxLCJ1Ijo4MTAyNDQsImQiOjE1OTgyMzIsImFpIjo1OTc1NCwiYWsiOiI4YTEzZjE5Zi00MDg3LTRjN2EtYTNlNi05MjA0ZjZlZDNkODMiLCJhbiI6IlNZT1AiLCJhcCI6ImRjNTExN2Y2LWFlZDMtNDQ5ZC05Mzg5LTIwMGE1MTRhYzEyNyIsInQiOjE3NTgyNzY3MTE1Njd9.ImlCdTQlscn76RItUg5PsUGw/5wTewnz46zwQqmLCpnCPNAa2OCqJLJCkRvCoFAdfevt0X5oD5q47tLKAp6POy28qq88+Wtzje0YIpATTjPes19ncoyoYLddn0bhI4aJ1TegMcXhIiXxvHZWvrro5d04dmT2zWB/5dPw6AAaXPX/PUtIujnKTLK8xFe583n/bijzkgEd77U=.D7ffeIJPqbofBNXfIXZ3O825qB7gVXAusVBx8eL8HaA';

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
    $secret_key = 'ofLHHzlzMImOBwrvcIvABsH5TtTEtj9DUvAQ4uUvoFNljW6w276Lbv9FT2U1f16P';  // Replace with your actual secret key

    // Choose the hashing algorithm (SHA-256 in this case)
    $algorithm = 'sha256';

    // Generate the raw HMAC using the secret key and message
    $hmac_raw = hash_hmac($algorithm, $datenow, $secret_key, true);  // true for raw output

    // Encode the raw HMAC to Base64
    $hmac_base64 = base64_encode($hmac_raw);

    return $hmac_base64;
}
