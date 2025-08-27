<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$id_sender = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_user']);
	$id_receiver = $_POST['chat_to'];
	$response	= '';

	// Update Chat
	$sql = "
	UPDATE pro_chat
	SET is_notif=0, is_read=0
	WHERE id_receiver = '$id_sender'
	";
	$set = $conSub->setQuery($sql);
	// $oke  = $oke && !$con->hasError();
	// Get Chat
	$sql = "
	SELECT * 
	FROM pro_chat 
	WHERE 
		1=1
		AND deleted_time IS NULL
		AND (
        	(id_sender = '$id_sender' AND id_receiver = '$id_receiver') 
        	OR 
        	(id_sender = '$id_receiver' AND id_receiver = '$id_sender')
        )
    ORDER BY id_chat ASC
    ";
	$rsm = $conSub->getResult($sql);
	if(count($rsm) > 0){
		foreach ($rsm as $key => $value) {
			$message = $value['message'];
			$created_time = date('d M Y, H:i:s', strtotime($value['created_time']));
			if ($id_receiver != $value['id_receiver']){
				$response .= '
					<div class="incoming_msg">
	                    <div class="incoming_msg_img"> 
	                    	<img src="'.BASE_URL.'/images/no_profile_image.jpg" style="width: 40px; height: 40px; border-radius: 120%;" alt="image"> 
	                    </div>
	                    <div class="received_msg">
	                        <div class="received_withd_msg">
	                          <p>'.$message.'</p>
	                          <span class="time_date">'.$created_time.'</span></div>
	                    </div>
	                </div>
	            ';
	        } else {
	        	$response .= '
		        	<div class="outgoing_msg">
		                <div class="sent_msg">
		                	<p>'.$message.'</p>
		                	<span class="time_date">'.$created_time.'</span>
		                </div>
	              	</div>
              	';
	        }
		}
	}
    echo $response;
	$conSub->close();
?>
