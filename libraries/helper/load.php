<?php
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
$env = $public_base_directory . '/env.php';
if (!file_exists($env)) {
	echo 'Please put env file.';
	exit;
}
require_once($env);

if (getenv('APP_ENV') == 'production') {
	error_reporting(0);
	ini_set('display_errors', 0);
} else {
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set('display_errors', 1);
}

define("SESSIONID", getenv('APP_ENV') . getenv('APP_NAME'));
define("USR_EMAIL_PROENERGI202389", getenv('USR_EMAIL_PROENERGI'));
define("PWD_EMAIL_PROENERGI202389", getenv('PWD_EMAIL_PROENERGI'));

// Check Session Expired
if (isset($_SESSION["sinori" . SESSIONID])) {
	$timeout = time() - $_SESSION["sinori" . SESSIONID]["timeout"];
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		if (!$_SESSION["sinori" . SESSIONID]["timeout"] or (int) $timeout > getenv('SESSION_EXPIRED')) {
			session_unset();
			session_destroy();
			header('location: ' . getenv('APP_HOST') . getenv('APP_NAME'));
			exit;
		}
	}
}

function load_helper()
{
	$args 	 		= func_get_args();
	$numargs 		= count($args);
	$document_root 	= $_SERVER['DOCUMENT_ROOT'] . '/' . getenv('APP_NAME');
	if ($numargs == 0) {
		require_once($document_root . "/libraries/helper/url.php");
		require_once($document_root . "/config/connection.php");
		require_once($document_root . "/config/enkripsi.php");
		require_once($document_root . "/libraries/helper/class.messages.php");
	} else {
		if (in_array("autoload", $args)) {
			require_once($document_root . "/libraries/helper/url.php");
			require_once($document_root . "/config/connection.php");
			require_once($document_root . "/config/enkripsi.php");
			require_once($document_root . "/libraries/helper/class.messages.php");
			require_once($document_root . "/libraries/helper/message.php");
			require_once($document_root . "/libraries/helper/tgl_indo.php");
			require_once($document_root . "/libraries/helper/paging.php");
			require_once($document_root . "/libraries/helper/pagination.php");
			require_once($document_root . "/libraries/helper/class.authenticate.php");
			require_once($document_root . "/libraries/helper/curl.php");
		}
		if (in_array("htmlawed", $args)) {
			require_once($document_root . "/libraries/helper/htmLawed.php");
		}
		if (in_array("pdfgen", $args)) {
			if (PHP_VERSION >= 5.6) {
				require_once($document_root . "/libraries/helper/mpdf-development/vendor/autoload.php");
			} else
				require_once($document_root . "/libraries/helper/pdfgen/mpdf.php");
		}
		if (in_array("mailgen", $args)) {
			require_once($document_root . "/libraries/helper/mailgen/PHPMailerAutoload.php");
		}
		if (in_array("captcha", $args)) {
			require_once($document_root . "/libraries/helper/recaptchalib.php");
		}
		if (in_array("newcaptcha", $args)) {
			require_once($document_root . "/libraries/helper/recaptchalib.new.php");
		}
		if (in_array("fileupload", $args)) {
			require_once($document_root . "/libraries/helper/class.upload.handler.php");
		}
	}
}
function load_headHtml($linkCss, $linkJs, $param = array())
{
	$titleHtml 	= (!isset($param["title"])) ? "Pro-Energi" : $param["title"];
	$extraJs	= (!isset($param["js"])) ? array() : $param["js"];
	$extraCss	= (!isset($param["css"])) ? array() : $param["css"];
	$extraVar01	= (isset($_SESSION['sinori' . SESSIONID]) ? paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']) : 0);

	$head = "";
	$head .= "<head>\n";
	$head .= "\t<meta charset=\"UTF-8\">\n";
	$head .= "\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n";
	$head .= "\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
	$head .= "\t<title>" . $titleHtml . "</title>\n";
	$head .= "\t<link rel=\"shortcut icon\" type=\"image/png\" href=\"" . BASE_IMAGE . "/proenergi.jpg\">\n";

	if (in_array("login", $extraCss)) {
		/*$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"".$linkCss."/lte-bootstrap/css/bootstrap.css\" />\n";*/
		/*$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"".$linkCss."/lte.admin.css\" />\n";*/
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . BASE_SERVER . "/libraries/thirdparty/bootstrap/css/bootstrap.min.css\" />\n";
	} else {
		//$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"".$linkCss."/bootstrap/css/bootstrap.min.css\" />\n";
		//$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"".$linkCss."/lte-bootstrap/css/bootstrap.css\" />\n";
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . BASE_SERVER . "/libraries/thirdparty/bootstrap/css/bootstrap.min.css\" />\n";
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/lte.admin.css\" />\n";
	}
	$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . BASE_SERVER . "/libraries/thirdparty/fonts/font-awesome/css/fontawesome.min.css\" />\n";
	$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . BASE_SERVER . "/libraries/thirdparty/fonts/google.font.face.css\" />\n";
	$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/style.bootstrap.css\" />\n";
	$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/style.select2.css\" />\n";
	$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/style.flash.alert.css\" />\n";
	$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/style.jquery.validationEngine.css\" />\n";
	$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/sweetalert2.min.css\" />\n";

	if (in_array("login", $extraCss)) {
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/style.login.css\" />\n";
	} else {
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/style.menu.css\" />\n";
	}

	if (in_array("jqueryUI", $extraCss)) {
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/style.jquery.ui.css\" />\n";
	}
	if (in_array("calendar", $extraCss)) {
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/calendar/style.fullcalendar.css\" />\n";
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/calendar/style.fullcalendar.ext.css\" />\n";
	}
	if (in_array("calendar02", $extraCss)) {
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/calendar02/style.fullcalendar.css\" />\n";
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/calendar02/style.fullcalendar.ext.css\" />\n";
	}
	if (in_array("timepicker", $extraCss)) {
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/style.jquery.timepicker.css\" />\n";
	}
	if (in_array("rating", $extraCss)) {
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/style.rating.css\" />\n";
	}
	if (in_array("fileupload", $extraCss)) {
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/file-upload/jquery.blueimp-gallery.min.css\" />\n";
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/file-upload/jquery.fileupload.css\" />\n";
	}
	if (in_array("scrolltab", $extraCss)) {
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . BASE_SERVER . "/libraries/thirdparty/jquery-tabs/css/scrolltabs.css\" />\n";
	}
	if (in_array("formhelper", $extraJs)) {
		$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/calendar02/style.form.helper.css\" />\n";
	}

	/*$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"".$linkCss."/web/".CaptchaUrls::LayoutStylesheetUrl()."\" />\n";*/

	$head .= "\t\t<script language=\"javascript\" type=\"text/javascript\">var base_url = '" . getenv('APP_HOST') . getenv('APP_NAME') . "';</script>\n";
	$head .= "\t\t<script language=\"javascript\" type=\"text/javascript\">var role_id = '" . $extraVar01 . "';</script>\n";
	$head .= "\t<!--[if lt IE 9]>\n";
	$head .= "\t\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/bootstrap/html5shiv.js\"></script>\n";
	$head .= "\t\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/bootstrap/respond.min.js\"></script>\n";
	$head .= "\t<![endif]-->\n";

	/*$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"".$linkJs."/jquery.1.11.0.min.js\"></script>\n";*/
	/*$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"".$linkJs."/bootstrap/bootstrap.min.js\"></script>\n";*/
	/*$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"".$linkCss."/lte-bootstrap/js/bootstrap.js\"></script>\n";*/
	/*$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"".$linkJs."/jquery.1.11.0.min.js\"></script>\n";*/
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . BASE_SERVER . "/libraries/thirdparty/bootstrap/js/jquery.min.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . BASE_SERVER . "/libraries/thirdparty/bootstrap/js/bootstrap.min.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkCss . "/lte-bootstrap/js/app.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/plugins/jquery.plugin.sidebar.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/plugins/jquery.plugin.pace.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/plugins/jquery.plugin.slimscroll.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/plugins/jquery.plugin.iCheck.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/plugins/jquery.plugin.select2.min.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/plugins/jquery.plugin.select2.id.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/jquery.flash.alert.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/validation/jquery.validationEngine.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/validation/jquery.validationEngine-id.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/validation/jquery.validationEngine.cfg.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/validation/validate/jquery.validate.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/validation/validate/jquery.validate.additional.methods.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/validation/validate/jquery.validate.messages.id.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/jquery.mask.min.js\"></script>\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/sweetalert2.min.js\"></script>\n";

	if (in_array("myGrid", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/datatable/jquery.myDataGrid.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/datatable/jquery.myDataGridNew.js\"></script>\n";
	}
	if (in_array("ckeditor", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/ckeditor/ckeditor.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/ckeditor/adapters/jquery.js\"></script>\n";
	}
	if (in_array("ckeditor419", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/ckeditor_419/ckeditor.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/ckeditor_419/adapters/jquery.js\"></script>\n";
	}
	if (in_array("formatUang", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/money-format/jquery.formatuang.js\"></script>\n";
	}
	if (in_array("formatNumber", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/money-format/jquery.number.min.js\"></script>\n";
	}
	if (in_array("jqueryUI", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/jquery-ui/jquery.ui.min.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/jquery-ui/jquery.timepicker.ui.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/jquery-ui/jquery.datepicker.cfg.js\"></script>\n";
	}
	if (in_array("calendar", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/calendar/jquery.moment.min.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/calendar/jquery.fullcalendar.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/calendar/jquery.fullcalendar.id.js\"></script>\n";
	}
	if (in_array("calendar02", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/calendar02/jquery.moment.min.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/calendar02/jquery.fullcalendar.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/calendar02/jquery.fullcalendar.id.js\"></script>\n";
	}
	if (in_array("timepicker", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/jquery-ui/jquery.timepicker.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/jquery-ui/jquery.sliderAccess.js\"></script>\n";
	}
	if (in_array("formhelper", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/calendar02/jquery.form.helper.js\"></script>\n";
	}
	if (in_array("gmaps", $extraJs)) {
		$head .= "\t<script async defer src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyBOgDx9BfF4VH2DA1FtP5URf2SwZgcMbi0\"></script>\n";
		/*$head .= "\t<script async defer src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyBOgDx9BfF4VH2DA1FtP5URf2SwZgcMbi0&callback=Function.prototype\"></script>\n";
			$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=false\"></script>\n";*/
	}
	if (in_array("rating", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/jquery.bar.rating.js\"></script>\n";
	}
	if (in_array("fileupload", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/file-upload/jquery.blueimp.tmpl.min.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/file-upload/jquery.blueimp.load.image.all.min.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/file-upload/jquery.blueimp.canvas.to.blob.min.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/file-upload/jquery.blueimp-gallery.min.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/file-upload/jquery.iframe-transport.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/file-upload/jquery.fileupload.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/file-upload/jquery.fileupload-process.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/file-upload/jquery.fileupload-image.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/file-upload/jquery.fileupload-validate.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/file-upload/jquery.fileupload-ui.js\"></script>\n";
	}
	if (in_array("form-update", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/jquery.form.update.js\"></script>\n";
	}

	if (in_array("scrolltab", $extraJs)) {
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . BASE_SERVER . "/libraries/thirdparty/jquery-tabs/js/jquery.scrolltabs.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . BASE_SERVER . "/libraries/thirdparty/jquery-tabs/js/jquery.mousewheel.js\"></script>\n";
		$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . BASE_SERVER . "/libraries/thirdparty/jquery-tabs/js/jquery.floatThead.min.js\"></script>\n";
	}

	// added from oman
	if (isset($_SESSION['sinori' . SESSIONID])) {
		if (in_array(paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']), array(9))) {
			$head .= "\t<link href=\"" . $linkCss . "/calendar/fullcalendar.min.css\" rel=\"stylesheet\" />\n";
			$head .= "\t<link href=\"" . $linkCss . "/calendar/fullcalendar.print.min.css\" rel=\"stylesheet\" media=\"print\" />\n";
			$head .= "\t<script src=\"" . $linkJs . "/calendar/lib/moment.min.js\"></script>\n";
			$head .= "\t<script src=\"" . $linkJs . "/calendar/fullcalendar.min.js\"></script>\n";
		}
	}
	/*$head .= "\t<script src=\"https://cdn.jsdelivr.net/npm/apexcharts\"></script>\n";*/

	$head .= "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $linkCss . "/style.table.custom.css\" />\n";
	$head .= "\t<script language=\"javascript\" type=\"text/javascript\" src=\"" . $linkJs . "/jquery.generate.js\"></script>\n";

	$head .= "</head>";

	echo $head;
}
