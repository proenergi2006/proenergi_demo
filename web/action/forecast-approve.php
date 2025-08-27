<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload", "htmlawed");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
    $enk  	= decode($_SERVER['REQUEST_URI']);
    $idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):null;

    $oke = true;
    $con->beginTransaction();
    $con->clearError();

    if ($idr) {
        $sql = "
            update forecast 
            set 
                status = 1, 
                updated_at = NOW() 
            where id = " . $idr;
        $con->setQuery($sql);

        $con->commit();
        $con->close();
        $flash->add("success", "Data has been processed", BASE_REFERER);
    
        // header("location: " . BASE_URL_CLIENT . "/forecast.php");
    }
?>