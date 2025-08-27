<?php
	date_default_timezone_set('Asia/Jakarta');
	$BASE_HOST 			= getenv('APP_HOST'); // http://202.83.120.46/, https://s-operation.proenergi.com/, http://localhost/
	$BASE_SERVER		= $BASE_HOST.getenv('APP_NAME'); // proEnergi, proEnergi-demo

	define('BASE_HOST', $BASE_SERVER);
	define('BASE_SERVER', $BASE_SERVER);
	if (!empty($_SERVER['HTTP_REFERER']))
		$LINK_REFERER 	= explode("?",$_SERVER['HTTP_REFERER']);
	else
		$LINK_REFERER 	= explode("?",$_SERVER['HTTP_HOST']);
	
	define('BASE_PATH_CSS', $BASE_SERVER."/libraries/themes");
	define('BASE_PATH_JS', $BASE_SERVER."/libraries/js");
	define('BASE_IMAGE', $BASE_SERVER."/images");
	define('BASE_URL', $BASE_SERVER);
	define('BASE_URL_CLIENT', $BASE_SERVER."/web");
	define('ACTION_CLIENT', $BASE_SERVER."/web/action");
	define('BASE_SELF', $BASE_HOST.htmlspecialchars($_SERVER['SCRIPT_NAME']));
	define('BASE_SELF_URL', $BASE_HOST.htmlspecialchars($_SERVER['REQUEST_URI']));
	if (!empty($_SERVER['HTTP_REFERER']))
		define('BASE_REFERER', htmlspecialchars($_SERVER['HTTP_REFERER']));
	else
		define('BASE_REFERER', htmlspecialchars($_SERVER['HTTP_HOST']));
	define('REFERER', htmlspecialchars($LINK_REFERER[0]));
?>
