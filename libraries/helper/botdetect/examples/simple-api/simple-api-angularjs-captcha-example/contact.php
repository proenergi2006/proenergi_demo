<?php

require("captcha-endpoint/simple-botdetect.php");
$form_page = "index.html";

// directly accessing this script is an error
if ($_SERVER['REQUEST_METHOD'] != "POST") {
  header("Location: ${form_page}");
  exit;
}

// get input params
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

$name = $input["name"];
$email = $input["email"];
$subject = $input["subject"];
$message = $input["message"];
$userInput = $input["captchaCode"];
$captchaId = $input["captchaId"];

// store error message
$error = array();

if (!isValidName($name)) {
  $error["name"] = "Name must be at least 3 characters.";
}

if (!isValidEmail($email)) {
  $error["email"] = "Email is invalid.";
}

if (!isValidSubject($subject)) {
  $error["subject"] = "Subject must be at least 10 characters.";
}

if (!isValidMessage($message)) {
  $error["message"] = "Message must be at least 10 characters.";
}

if (!isCaptchaCorrect($userInput, $captchaId)) {
  $error["captchaCode"] = "CAPTCHA validation failed.";
}

if (empty($error)) {
  // everything is ok
  // TODO: Insert form data into your database
}


$result = array('success' => empty($error), 'error' => $error);
echo json_encode($result, true);
exit;

// validate function

function isCaptchaCorrect($userInput, $captchaId) {
  // Captcha validation
  $FormCaptcha = new SimpleCaptcha("angularFormCaptcha");
  return $FormCaptcha->Validate($userInput, $captchaId);
}

function isValidName($name) {
  if($name == null) {
    return false;
  }
  return (strlen($name) >= 3);
}

function isValidEmail($email) {
  if($email == null) {
    return false;
  }

  return preg_match("/^[\\w-_\\.+]*[\\w-_\\.]\\@([\\w]+\\.)+[\\w]+[\\w]$/", $email, $matches);
}

function isValidSubject($subject) {
  if($subject == null) {
    return false;
  }

  return (strlen($subject) > 9) && (strlen($subject) < 255);
}

function isValidMessage($message) {
  if($message == null) {
    return false;
  }

  return (strlen($message) > 9) && (strlen($message) < 255);
}