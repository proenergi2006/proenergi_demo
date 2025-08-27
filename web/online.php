<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
    $con 	= new Connection();
    
    $query = '
        select 
            a.id_user, 
            b.username, 
            a.updated_at, 
            a.status
        from online a 
        join acl_user b 
        where 
            b.id_user = a.id_user
    ';

    $model = $con->setQuery($query);
    print_r($model);
    if (!$con->hasError()) {
        $con->close();

        $html = '';
        if (count($model))
            foreach ($model as $row)
                $html .= $row->username;

        return $html;
        exit();				
    } else {
        $con->clearError();
        $con->close();
        $flash->add("error", $msg, BASE_REFERER);
    }
?>