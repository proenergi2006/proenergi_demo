<?php
    session_start();
    $privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
    $public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
    require_once ($public_base_directory."/libraries/helper/load.php");
    load_helper("autoload", "mailgen");

    $auth   = new MyOtentikasi();
    $con    = new Connection();
    $flash  = new FlashAlerts;
    $enk    = decode($_SERVER['REQUEST_URI']);
    $id     = paramDecrypt(htmlspecialchars($_POST["id"], ENT_QUOTES));
    $idc    = paramDecrypt(htmlspecialchars($_POST["idc"], ENT_QUOTES));
    $note   = str_replace(array("\r\n", "\r", "\n"), "<br />", htmlspecialchars($_POST["summary"], ENT_QUOTES));

    $role   = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
    $pic    = paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname']);
    $id_wil = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
    
    $cl = str_replace(",","",htmlspecialchars($_POST["cl"], ENT_QUOTES));
    $approval = htmlspecialchars($_POST["result"], ENT_QUOTES);
    $ov_30 = str_replace(",","",htmlspecialchars($_POST["ov_under_30"], ENT_QUOTES));
    $ov_60 = str_replace(",","",htmlspecialchars($_POST["ov_under_60"], ENT_QUOTES));
    $ov_90 = str_replace(",","",htmlspecialchars($_POST["ov_under_90"], ENT_QUOTES));
    $up_90 = str_replace(",","",htmlspecialchars($_POST["ov_up_90"], ENT_QUOTES));
    // $top = str_replace(",","",htmlspecialchars($_POST["top"], ENT_QUOTES));
    $not_yet = str_replace(",","",htmlspecialchars($_POST["not_yet"], ENT_QUOTES));
    $reminding = str_replace(",","",htmlspecialchars($_POST["reminding"], ENT_QUOTES));
    $type_customer = htmlspecialchars($_POST["type_customer"], ENT_QUOTES);
    $customer_amount = str_replace(",","",htmlspecialchars($_POST["customer_amount"], ENT_QUOTES));
    // $item = htmlspecialchars($_POST["item"], ENT_QUOTES);
    $status_po = htmlspecialchars($_POST["status_po"], ENT_QUOTES);
    $volume_po = str_replace(",","",htmlspecialchars($_POST["volume_po"], ENT_QUOTES));
    $amount_po = str_replace(",","",htmlspecialchars($_POST["amount_po"], ENT_QUOTES));
    $proposed  = htmlspecialchars($_POST["proposed"], ENT_QUOTES);
    $add_top   = str_replace(",","",htmlspecialchars($_POST["add_top"], ENT_QUOTES));
    $add_cl    = str_replace(",","",htmlspecialchars($_POST["add_cl"], ENT_QUOTES));
    $kode_pelanggan = isset($_POST["kode_pelanggan"])?htmlspecialchars($_POST["kode_pelanggan"], ENT_QUOTES):null;
    
    if(isset($_POST["customer_date"])) {
        $customer_date = date("Y-m-d", strtotime(str_replace('/', '-', $_POST["customer_date"])));
    } else
        $customer_date = NULL;

    $oke = true;
    $con->beginTransaction();
    $con->clearError();

    $ems2 = null;
    $terima = null;

    $angka_aman1 = 250000000;
    $angka_aman2 = 500000000;
    if ($add_cl=='') $add_cl = 0;
    $total_cl = $cl + $add_cl;
    $is_disposisi = false;
    if ($total_cl > 0 && $total_cl < $angka_aman1) {
        // approved by OM & BM
        $is_disposisi = true;
    } else if ($total_cl > $angka_aman1 && $total_cl < $angka_aman2) {
        // approved by FM
        $is_disposisi = true;
    } else if ($total_cl > $angka_aman2) {
        // approved by Commisioner
        $is_disposisi = true;
    }

    if ($add_top > 30) {
        // approved by FM
        $is_disposisi = true;
    }
	// $disposisi = array(1=>"Adm Finance",2=>"BM",3=>"OM",4=>"MGR Finance",5=>"CFO");

    if ($role == 10) {
        $sql = "update pro_sales_confirmation set ov_under_30 = '".$ov_30."', ov_under_60 = '".$ov_60."', ov_under_90 = '".$ov_90."', ov_up_90 = '".$up_90."', reminding = '".$reminding."', not_yet = '".$not_yet."', po_status = '".$status_po."', po_volume = '".$volume_po."', po_amount = '".$amount_po."', type_customer = '".$type_customer."', proposed_status = '".$proposed."', add_top = '".$add_top."', add_cl = '".$add_cl."', disposisi = 2, flag_approval = 0, role_approved = NULL, tgl_approved = NULL";

        if ($type_customer == '2') {
            $amount_coll = $_POST['customer_amount_coll'];
            $data_coll = array();
            
            foreach($amount_coll as $i => $val)
            {
                $str_amount = str_replace(",","", $val);
                
                if($val > 0)
                {
                    $date_custom = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['customer_date_coll'][$i])));
                    $data_coll[] = "(".$id.", '".$date_custom."', ".$str_amount.", '".$_POST['item_coll'][$i]."' )";
                }
            }

            $sql_delete = 'DELETE FROM pro_sales_colleteral WHERE sales_id = '.$id;

            $con->setQuery($sql_delete);
            $oke  = $oke && !$con->hasError();

            if($data_coll)
            {
                $sql_coll = "INSERT INTO pro_sales_colleteral(sales_id, date, amount, item) VALUES ".implode(", ", $data_coll);
                $con->setQuery($sql_coll);
                $oke  = $oke && !$con->hasError();
            }
        }
        else 
        {
            $sql .= ", customer_amount = '".$customer_amount."' ";

            if($customer_date)
                $sql .= ", customer_date = '".$customer_date."' ";
        }

        $sql .= " where id = ".$id;
        $con->setQuery($sql);
        $oke  = $oke && !$con->hasError();
        
        $sql2 = "update pro_sales_confirmation_approval set adm_result = '".$approval."', adm_summary = '".$note."', adm_result_date = NOW(), adm_pic = '".$pic."'"; 

        if(isset($_POST['revisi']))
        {
            $sql2 .= ", bm_result = 0, bm_summary = NULL, bm_result_date = NULL, bm_pic = NULL "; 
            $sql2 .= ", om_result = 0, om_summary = NULL, om_result_date = NULL, om_pic = NULL "; 
            $sql2 .= ", mgr_result = 0, mgr_summary = NULL, mgr_result_date = NULL, mgr_pic = NULL "; 
            $sql2 .= ", cfo_result = 0, cfo_summary = NULL, cfo_result_date = NULL, cfo_pic = NULL "; 
        }
        
        $sql2 .= " where id_sales = ".$id;
        
        $con->setQuery($sql2);
        $oke  = $oke && !$con->hasError();
        
        if($kode_pelanggan)
        {
            $sql3 = "update pro_customer set kode_pelanggan = '".$kode_pelanggan."' where id_customer = ".$idc;

            $con->setQuery($sql3);
            $oke  = $oke && !$con->hasError();
        }

        if($approval == 1)
            $ems1 = "select email_user from acl_user where id_role in(7) and id_wilayah = '".$id_wil."'";
    }

    if($role == 7)
    {
        /* Not Used
        $sql = "update pro_sales_confirmation_approval set bm_summary = '".$note."', bm_result_date = NOW(), bm_result = ".$approval.", bm_pic ='".$pic."' where id_sales = ".$id;
        if($is_disposisi === true && $_POST['proposed'] == 1)
        {
            $ems1 = "select email_user from acl_user where id_role in(6)";
            $con->setQuery("update pro_sales_confirmation set disposisi = 2 where id = ".$id);
            $oke  = $oke && !$con->hasError();
        }
        else
        {
            $terima = $approval;
            $con->setQuery("update pro_sales_confirmation set flag_approval = ".$approval.", role_approved = 7, tgl_approved = NOW() where id = ".$id);
            $oke  = $oke && !$con->hasError();
        }
        */
        $proposed = $_POST["proposed"]; // 1: agree, 0: not agree
        $supply = $_POST["result"]; // 1: agree, 2: not agree
        // proposed & supply: approve
        // proposed & not supply: tidak
        // not proposed & supply: approve
        // not propose & not supply: tidak
        if (($proposed=='1' and $supply=='1') or ($proposed=='0' and $supply=='1')) {
            $sql = "update pro_sales_confirmation_approval set bm_summary = '".$note."', bm_result_date = NOW(), bm_result = 1, bm_pic ='".$pic."' where id_sales = ".$id;
            $terima = $approval;
            $con->setQuery("update pro_sales_confirmation set flag_approval = ".$supply.", role_approved = 7, tgl_approved = NOW() where id = ".$id);
            $oke  = $oke && !$con->hasError();
        }
        else if (($proposed=='1' and $supply=='2') or ($proposed=='0' and $supply=='2')) {
            $sql = "update pro_sales_confirmation_approval set bm_summary = '".$note."', bm_result_date = NOW(), bm_result = 0, bm_pic ='".$pic."' where id_sales = ".$id;
            $ems1 = "select email_user from acl_user where id_role in(6)";
            $con->setQuery("update pro_sales_confirmation set flag_approval = ".$supply.", role_approved = 7, tgl_approved = NOW(), disposisi = 1 where id = ".$id);
            $oke  = $oke && !$con->hasError();
        }

        $oke = $oke && history($id, $con);

        $con->setQuery($sql);
        $oke  = $oke && !$con->hasError();
    }

    if($role == 6)
    {
        $sql = "update pro_sales_confirmation_approval set om_summary = '".$note."', om_result_date = NOW(), om_result = ".$approval.", om_pic ='".$pic."' where id_sales = ".$id;
        $con->setQuery($sql);
        $oke  = $oke && !$con->hasError();

        $ems1 = "select email_user from acl_user where id_role in(15)";
        $con->setQuery("update pro_sales_confirmation set disposisi = 3 where id = ".$id);
        $oke  = $oke && !$con->hasError();
    }

    if($role == 15)
    {
        $sql = "update pro_sales_confirmation_approval set mgr_summary = '".$note."', mgr_result_date = NOW(), mgr_result = ".$approval.", mgr_pic ='".$pic."' where id_sales = ".$id;
        
        if($is_disposisi === true && $_POST['proposed'] == 1)
        {
            $ems1 = "select email_user from acl_user where id_role in(4)";
            $con->setQuery("update pro_sales_confirmation set disposisi = 4 where id = ".$id);
            $oke  = $oke && !$con->hasError();
        }
        else
        {
            $terima = $approval;

            $con->setQuery("update pro_sales_confirmation set flag_approval = ".$approval." , role_approved = 15, tgl_approved = NOW() where id = ".$id);
            $oke  = $oke && !$con->hasError();
        }

        $oke = $oke && history($id, $con);

        $con->setQuery($sql);
        $oke  = $oke && !$con->hasError();
    }

    if($role == 4)
    {
        $sql = "update pro_sales_confirmation_approval set cfo_summary = '".$note."', cfo_result_date = NOW(), cfo_result = ".$approval.", cfo_pic ='".$pic."' where id_sales = ".$id;
        $con->setQuery($sql);
        $oke  = $oke && !$con->hasError();    

        $terima = $approval;
        $oke = $oke && history($id, $con);

        $con->setQuery("update pro_sales_confirmation set flag_approval = ".$approval.", role_approved = 4, tgl_approved = NOW() where id = ".$id);
        $oke  = $oke && !$con->hasError();
    }

    if($terima)
    {
        $row = $con->getRecord('select * from pro_sales_confirmation sc join pro_sales_confirmation_approval sca on sca.id_sales = sc.id where sc.id = '.$id);
        $notif = 0;

        if($approval == 1)
            $notif = 1;

        $sql = "update pro_po_customer set poc_approved = '".$approval."', tgl_approved = NOW(), sm_summary = '".$note."', sm_result = 1, sm_tanggal = NOW(), sm_pic = '".$pic."', po_notif = ".$notif." 
                where id_poc = '".$row['id_poc']."' and id_customer = '".$idc."'";
                
        $con->setQuery($sql);
        $oke  = $oke && !$con->hasError();
        $ems2 = "select email_user from acl_user where id_role in(11,17) and id_user = (select id_marketing from pro_customer where id_customer = '".$idr."')";
    }

    if ($oke)
    {
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->SMTPKeepAlive = true;
        $mail->Username = USR_EMAIL_PROENERGI202389;
        $mail->Password = PWD_EMAIL_PROENERGI202389;
            
        if($ems1)
        {
            $rms1 = $con->getResult($ems1);
            
            $mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
            foreach($rms1 as $datms){
                $mail->addAddress($datms['email_user']);
            }
            $mail->Subject = "Persetujuan Sales Confirmation [".date('d/m/Y H:i:s')."]";
            $mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." Meminta Persetujuan anda <p>".BASE_SERVER."</p>");
            $mail->send();
        }
        
        if($ems2)
        {
            $rms1 = $con->getResult($ems2);
            
            $mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
            foreach($rms1 as $datms){
                $mail->addAddress($datms['email_user']);
            }
            
            $mail->Subject = "Persetujuan PO[".date('d/m/Y H:i:s')."]";
            $mail->msgHTML(paramDecrypt($_SESSION['sinori'.SESSIONID]['fullname'])." Menyetujui PO Customer anda <p>".BASE_SERVER."</p>");

            $mail->send();
        }

        $con->commit();
        $con->close();
        header("location: ".BASE_URL_CLIENT."/pro_sales_confirmation.php"); 
        exit();             
    } else{
        $con->rollBack();
        $con->clearError();
        $con->close();
        $flash->add("error", "GAGAL_MASUK", BASE_REFERER);
    }

    function history($id, $con){

        $row = $con->getRecord('select * from pro_sales_confirmation sc join pro_sales_confirmation_approval sca on sca.id_sales = sc.id where sc.id = '.$id);

        $con->setQuery('DELETE FROM pro_sales_confirmation_log WHERE id_sales = '.$row['id_sales']);
        
        $sql = "
            INSERT INTO pro_sales_confirmation_log (
                id_sales,
                id_customer,
                id_poc,
                id_wilayah,
                not_yet,
                ov_under_30,
                ov_under_60,
                ov_under_90,
                ov_up_90,
                reminding,
                type_customer,
                customer_items,
                customer_date,
                customer_amount,
                po_status,
                po_volume,
                po_amount,
                proposed_status,
                add_top,
                add_cl,
                supply_date,
                period_date,
                adm_result,
                adm_pic,
                adm_summary,
                adm_result_date,
                bm_result,
                bm_pic,
                bm_summary,
                bm_result_date,
                om_result,
                om_pic,
                om_summary,
                om_result_date,
                mgr_result,
                mgr_pic,
                mgr_summary,
                mgr_result_date,
                cfo_result,
                cfo_pic,
                cfo_summary,
                cfo_result_date
            ) VALUES "; 
        $sql .= '('.
            $row['id_sales'].', 
            '.$row['id_customer'].', 
            '.$row['id_poc'].', 
            '.$row['id_wilayah'].', 
            "'.$row['not_yet'].'", 
            "'.$row['ov_under_30'].'", 
            "'.$row['ov_under_60'].'", 
            "'.$row['ov_under_90'].'", 
            "'.$row['ov_up_90'].'", 
            "'.$row['reminding'].'", 
            "'.$row['type_customer'].'", 
            "'.$row['customer_items'].'", 
            '. ($row['customer_date']==''?'NULL':'"'.$row['customer_date'].'"') .', 
            "'.$row['customer_amount'].'", 
            "'.$row['po_status'].'",
            "'.$row['po_volume'].'", 
            "'.$row['po_amount'].'", 
            "'.$row['proposed_status'].'", 
            "'.$row['add_top'].'", 
            "'.$row['add_cl'].'", 
            '. ($row['supply_date']==''?'NULL':'"'.$row['supply_date'].'"') .', 
            '. ($row['period_date']==''?'NULL':'"'.$row['period_date'].'"') .',
            "'.$row['adm_result'].'", 
            "'.$row['adm_pic'].'", 
            "'.$row['adm_summary'].'",  
            '. ($row['adm_result_date']==''?'NULL':'"'.$row['adm_result_date'].'"') .',
            "'.$row['bm_result'].'", 
            "'.$row['bm_pic'].'", 
            "'.$row['bm_summary'].'", 
            '. ($row['bm_result_date']==''?'NULL':'"'.$row['bm_result_date'].'"') .',
            "'.$row['om_result'].'", 
            "'.$row['om_pic'].'", 
            "'.$row['om_summary'].'", 
            '. ($row['om_result_date']==''?'NULL':'"'.$row['om_result_date'].'"') .',
            "'.$row['mgr_result'].'", 
            "'.$row['mgr_pic'].'", 
            "'.$row['mgr_summary'].'", 
            '. ($row['mgr_result_date']==''?'NULL':'"'.$row['mgr_result_date'].'"') .',
            "'.$row['cfo_result'].'", 
            "'.$row['cfo_pic'].'", 
            "'.$row['cfo_summary'].'", 
            '. ($row['cfo_result_date']==''?'NULL':'"'.$row['cfo_result_date'].'"') .'
        )';
        $con->setQuery($sql);
        
        return !$con->hasError();
    }
    
?>
