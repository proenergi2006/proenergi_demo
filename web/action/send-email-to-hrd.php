<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	require_once ($public_base_directory."/libraries/helper/passwordHash.php");
	load_helper("autoload", "mailgen", "htmlawed");

	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$id_customer = isset($enk['cust_id']) ? $enk['cust_id'] : 0;
	$pnwrn_sql = "select a.*, b.nama_customer, b.top_payment, b.status_customer, c.fullname, d.nama_cabang, e.jenis_produk, e.merk_dagang, f.harga_normal, f.harga_sm, f.harga_om, g.nama_area from pro_penawaran a join pro_customer b on a.id_customer = b.id_customer join acl_user c on b.id_marketing = c.id_user join pro_master_cabang d on a.id_cabang = d.id_master join pro_master_produk e on a.produk_tawar = e.id_master join pro_master_area g on a.id_area = g.id_master left join pro_master_harga_minyak f on a.masa_awal = f.periode_awal and a.masa_akhir = f.periode_akhir and a.id_area = f.id_area and a.pbbkb_tawar = f.pajak and f.is_approved = 1 where a.id_customer = ".$id_customer." AND flag_disposisi = 4 ORDER BY a.id_penawaran DESC LIMIT 0, 1";
    $rpnwrn = $con->getRecord($pnwrn_sql);
    
    $sqlCek = "select a.nama_customer, a.credit_limit, a.alamat_customer, a.telp_customer, a.fax_customer, a.email_customer, a.need_update, b.nama_kab, c.nama_prov from pro_customer a 
			   join pro_master_kabupaten b on a.kab_customer = b.id_kab join pro_master_provinsi c on a.prov_customer = c.id_prov where a.id_customer = '".$id_customer."'";
    $resCek = $con->getRecord($sqlCek);

	if($id_customer) {
        $rincian = json_decode($rpnwrn['detail_rincian'], true);
        $nom = 0;
        $dtrinci = '';
        foreach($rincian as $arr1) {
            $nom++;
            $cetak = 1;// $arr1['rinci'];
            $nilai = $arr1['nilai'];
            $biaya = ($arr1['biaya'])?number_format($arr1['biaya']):'';
            $jenis = $arr1['rincian'];
            if($cetak) {
                $dtrinci .='<tr>
                    <td class="text-center">'.$nom.'</td>
                    <td class="text-center">'.$jenis.'</td>
                    <td class="text-center">'.($nilai ? $nilai." %" : "").'</td>
                    <td class="text-center">'.$biaya.'</td>
                </tr>';
            }
        }
        $pesan = '<h3>Dear HRD ProEnergi</h3>
                
        <p>Berikut data penwaran dari <strong>'. $rpnwrn['nama_customer'] .' :</strong></p><br />
        <table>
            <tr>
                <td width="170">Volume</td>
                <td width="20">:</td>
                <td>'.number_format($rpnwrn['volume_tawar']).' Liter</td>
            </tr>
            <tr>
                <td>Refund</td>
                <td>:</td>
                <td>'.(($rpnwrn['refund_tawar'])?number_format($rpnwrn['refund_tawar']):'-').'</td>
            </tr>
            <tr>
                <td>Ongkos Angkut</td>
                <td>:</td>
                <td>'.number_format($rpnwrn['oa_kirim']).'</td>
            </tr>
            <tr>
                <td>Other Cost</td>
                <td>:</td>
                <td>'.number_format($rpnwrn['other_cost']).'</td>
            </tr>
            <tr>
                <td>Harga Penawaran</td>
                <td>:</td>
                <td>'.number_format($rpnwrn['harga_dasar']).'</td>
            </tr>
        </table>
        <p style="margin-bottom:0px;">Dengan rincian sebagai berikut: </p>
        <table border="1px" style="width: 100%; max-width: 100%; margin-bottom: 20px; border: 1px solid #ddd;" class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center" width="10%">NO</th>
                    <th class="text-center" width="40%">RINCIAN</th>
                    <th class="text-center" width="10%">NILAI</th>
                    <th class="text-center" width="40%">HARGA</th>
                </tr>
            </thead>
            <tbody>
                '.$dtrinci.'
            </tbody>
        </table>
        <p style="margin-bottom:0px;">Pricelist : '.number_format($rsm['harga_normal']).'</p>
        <p><hr></p>
        <!--<p style="margin-bottom:0px;">Catatan Marketing/Key Account: '.($rpnwrn['catatan']?$rpnwrn['catatan']:'&nbsp;').'</p>-->
        <!--<p><hr></p>-->';

        $pesan .= '<p>Credit Limit yang telah disetujui dengan nominal Rp.'.number_format($resCek['credit_limit']).'</p><br/>';

        if($rpnwrn['sm_wil_summary']){
            $pesan .='<p style="margin-bottom:0px;">Catatan Branch Manager Cabang: '.($rpnwrn['sm_wil_summary'] ? $rpnwrn['sm_wil_summary'] : ' - ').' <i>('.$rpnwrn['sm_wil_pic'].' - '.date("d/m/Y H:i:s", strtotime($rpnwrn['sm_wil_tanggal'])).' WIB)</i>.</p>
            <p><hr></p>';
        }
        if($rpnwrn['om_summary']){
            $pesan .='<p style="margin-bottom:0px;">Catatan Operation Manager: '.($rpnwrn['om_summary'] ? $rpnwrn['om_summary'] : ' - ').'  <i>('.$rpnwrn['om_pic'].' - '.date("d/m/Y H:i:s", strtotime($rpnwrn['om_tanggal'])).' WIB)</i>.</p>
            <p><hr></p>';
        }
        if($rpnwrn['ceo_summary']){
            $pesan .='<p style="margin-bottom:0px;">Catatan COO: '.($rpnwrn['ceo_summary'] ? $rpnwrn['ceo_summary'] : ' - ').' <i>('.$rpnwrn['ceo_pic'].' - '.date("d/m/Y H:i:s", strtotime($rpnwrn['ceo_tanggal'])).' WIB)</i></p>';
        }

        $email 	= htmlspecialchars($_POST["to"], ENT_QUOTES);
        $cc 	= htmlspecialchars($_POST["cc"], ENT_QUOTES);
        $subject = htmlspecialchars($_POST["subject"], ENT_QUOTES);

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
        $data['message']="success";
        echo json_encode($data);
	}
?>
