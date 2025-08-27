<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$q1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);

	$sql = "
		select 
		    c.nomor_surat as kode_penawaran,
		    c.id_penawaran,
		    a.id, 
		    b.top_payment,
		    b.credit_limit,
		    a.not_yet,
		    a.ov_under_30,
		    a.ov_under_60,
		    a.ov_under_90,
		    a.ov_up_90,
		    a.reminding 
		from pro_sales_confirmation a
		join pro_customer b on a.id_customer = b.id_customer
		join pro_penawaran c on c.id_customer = a.id_customer
		where a.id_customer = " . $q1 . "
		and a.flag_approval = 1
		-- group by c.id_penawaran
		order by a.id desc, c.id_penawaran desc
	";

	$result = $conSub->getResult($sql);
	$answer	= array();
	
	if ($result != null) {
		$answer['top_payment'] = $result[0]['top_payment'];
		$answer['credit_limit'] = 'Rp '.($result[0]['credit_limit']?number_format($result[0]['credit_limit']):0);
		$answer['not_yet'] = 'Rp '.($result[0]['not_yet']?number_format($result[0]['not_yet']):0);
		$answer['ov_under_30'] = 'Rp '.($result[0]['ov_under_30']?number_format($result[0]['ov_under_30']):0);
		$answer['ov_under_60'] = 'Rp '.($result[0]['ov_under_60']?number_format($result[0]['ov_under_60']):0);
		$answer['ov_under_90'] = 'Rp '.($result[0]['ov_under_90']?number_format($result[0]['ov_under_90']):0);
		$answer['ov_up_90'] = 'Rp '.($result[0]['ov_up_90']?number_format($result[0]['ov_up_90']):0);
		$answer['reminding'] = 'Rp '.($result[0]['reminding']?number_format($result[0]['reminding']):0);
		$answer['items'][] = array('id' => '', 'text' => '');

		foreach ($result as $data) {
            $answer['items'][] = array(
				'id' => $data['id_penawaran'],
				'text' => $data['kode_penawaran']
			);
        }
    } else {
    	$sql1 = "
            select 
                b.id_penawaran, 
                nomor_surat as kode_penawaran, 
                if(a.jenis_payment = 'CREDIT', a.top_payment, a.jenis_payment) as top_customer,
                a.top_payment,
                a.credit_limit,
                '0' as not_yet,
				'0' as ov_under_30,
				'0' as ov_under_60,
				'0' as ov_under_90,
				'0' as ov_up_90,
				'0' as reminding
            from pro_customer a 
            left join pro_penawaran b on 
                a.id_customer = b.id_customer 
                and b.flag_approval = 1 
            where a.id_customer = " . $q1 . " 
            order by b.id_penawaran desc
        ";
        $row1 = $conSub->getResult($sql1);
        if ($row1 != null) {
			$answer['top_payment'] = $row1[0]['top_payment'];
			$answer['credit_limit'] = 'Rp '.($row1[0]['credit_limit']?number_format($row1[0]['credit_limit']):0);
			$answer['not_yet'] = 'Rp '.($row1[0]['not_yet']?number_format($row1[0]['not_yet']):0);
			$answer['ov_under_30'] = 'Rp '.($row1[0]['ov_under_30']?number_format($row1[0]['ov_under_30']):0);
			$answer['ov_under_60'] = 'Rp '.($row1[0]['ov_under_60']?number_format($row1[0]['ov_under_60']):0);
			$answer['ov_under_90'] = 'Rp '.($row1[0]['ov_under_90']?number_format($row1[0]['ov_under_90']):0);
			$answer['ov_up_90'] = 'Rp '.($row1[0]['ov_up_90']?number_format($row1[0]['ov_up_90']):0);
			$answer['reminding'] = 'Rp '.($row1[0]['reminding']?number_format($row1[0]['reminding']):0);
			$answer['items'][] = array('id' => '', 'text' => '');
	        foreach ($row1 as $data) {
	            $answer['items'][] = array(
					'id' => $data['id_penawaran'],
					'text' => $data['kode_penawaran']
				);
	        }
    	}
        // $answer["items"] = array();
    }
	$conSub->close();

    echo json_encode($answer);
?>
