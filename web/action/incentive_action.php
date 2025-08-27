
<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload", "htmlawed");

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$enk      = decode($_SERVER['REQUEST_URI']);
$fullname = paramDecrypt($_SESSION['sinori' . SESSIONID]['fullname']);

$oke = true;
$con->beginTransaction();
$con->clearError();

foreach ($_POST as $key => $value) {
    if (strpos($key, 'point_') === 0) {
        // Extract the point ID from the input field name
        $point_id = str_replace('point_', '', $key);
        $new_point_value = $value;

        // Get the current point value from the database
        $sql_select = "SELECT point FROM pro_point_incentive WHERE id = '$point_id'";
        $current_point_value = $con->getRecord($sql_select);

        // Check if the point value has changed
        if ($current_point_value['point'] != $new_point_value) {
            // Prepare the SQL update query to change the point value if it has changed
            $sql_update = "UPDATE pro_point_incentive SET point = $new_point_value, updated_at = NOW(), updated_by = '" . $fullname . "' WHERE id = '$point_id'";
            $con->setQuery($sql_update);
            $oke = $oke && !$con->hasError();
        }
    }
}

if ($oke) {
    $con->commit();
    $con->close();
    $data = [
        "pesan" => "Point berhasil di update",
        "status" => true
    ];
    echo json_encode($data);
} else {
    $con->rollBack();
    $con->clearError();
    $con->close();
    $data = [
        "pesan" => "Point gagal di update",
        "status" => false
    ];
    echo json_encode($data);
}
