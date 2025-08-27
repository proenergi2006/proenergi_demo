<?php

require("captcha-endpoint/simple-botdetect.php");
$form_page = "index.html";

// directly accessing this script is an error
if ($_SERVER['REQUEST_METHOD'] != "POST") {
  header("Location: ${form_page}");
  exit;
}

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);
$userInput = $input["captchaCode"];
$captchaId = $input["captchaId"];

// Captcha validation
$FormCaptcha = new SimpleCaptcha("angularBasicCaptcha");
$isHuman = $FormCaptcha->Validate($userInput, $captchaId);

if ($isHuman) {
  // Captcha validation passed
  // TODO: do whatever you want here
}
$result = array('success' => $isHuman);
echo json_encode($result, true);
exit;
