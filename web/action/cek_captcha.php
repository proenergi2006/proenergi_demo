<?php 
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	require($public_base_directory."/customer/botdetect.php");
	load_helper("autoload", "htmlawed");

	// validate the Captcha to check we're not dealing with a bot
	$ExampleCaptcha = new Captcha("ExampleCaptcha");
    $isHuman = $ExampleCaptcha->Validate();
    if (!$isHuman) {
      	// Captcha validation failed, show error message
      	// echo 0;
      	$ExampleCaptcha->UserInputID = "CaptchaCode";
	    $ExampleCaptcha->SoundEnabled = true; 
	    $ExampleCaptcha->ReloadEnabled = true;
	    $ExampleCaptcha->ImageWidth = 230;
	    $Captcha = $ExampleCaptcha->Html();
	    echo $Captcha;
    } else {
    	echo 1;
    }
?>