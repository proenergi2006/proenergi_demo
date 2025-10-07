<?php
$urlnya = 'https://account.accurate.id/api/webhook-renew.do';

$result = curl_get($urlnya);

echo json_encode($result);
