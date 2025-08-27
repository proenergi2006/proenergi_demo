<?php
    session_start();
    $privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
    $public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
    require_once ($public_base_directory."/libraries/helper/load.php");
    load_helper("autoload", "mailgen", "htmlawed");

    $email 	= htmlspecialchars($_POST["to"], ENT_QUOTES);
    $pesan 	= $_POST["pesan"];
    $cc 	= htmlspecialchars($_POST["cc"], ENT_QUOTES);
    $subject = htmlspecialchars($_POST["judul"], ENT_QUOTES);
    $data['message'] = "error";

    if($email) 
    {
        try
        {
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->Username = USR_EMAIL_PROENERGI202389;
            $mail->Password = PWD_EMAIL_PROENERGI202389;
            $mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');

            if($cc)
                $mail->addCC($cc);
                                    
            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->msgHTML($pesan);
            $mail->send();
            $data['message'] = "Email Berhasil Dikirim";
        } catch (phpmailerException $e) {
            $data['message'] = $e->errorMessage(); 
        } catch (Exception $e) {
            $data['message'] = $e->getMessage(); 
        }
    }

    echo json_encode($data);
?>