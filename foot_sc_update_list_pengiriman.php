<?php
if ($tot_record > 0) {
    foreach ($res as $i => $key) {
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJJZCI6MTE1OSwiTmFtZSI6InByb2VuZXJnaSIsIlJvbGUiOiJhZG1fcHJvZW5lcmdpIiwiQ29tcGFueSI6NjA2LCJVc2VyUG9kSWQiOjAsImlzcyI6Ik9TTE9HIDUgQVBJIn0.H-ljfy7I0zVzpvXsar3FddpUT2RHChNaEP8uw50kmV8'

        );
        curl_setopt($ch, CURLOPT_URL, "https://oslog.id/javaz-api/shipment-order/share-data?column=shipment_id_internal&logic_operator==&value=" . $key['id_dsd'] . "&operator=AND");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 1200);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            $result = json_decode($response, true);
            // echo $response;
        }
        curl_close($ch);

        if ($result['status'] == true) {
            // array_push($arrData, $key['id_dsd']);
            $sqlds = "SELECT a.*, i.nama_customer, e.alamat_survey, f.nama_prov, g.nama_kab, k.nomor_plat,
                l.nama_sopir, p.id_area, c.pr_vendor, c.id_po_supplier, c.id_po_receive, r.nama_terminal, r.tanki_terminal, r.lokasi_terminal, d.volume_kirim, b.volume_po, m.id_wilayah as id_wilayah_po, h.produk_poc, j.id_user as pic_marketing, o.id_terminal 
                from pro_po_ds_detail a 
                join pro_po_ds o on a.id_ds = o.id_ds 
                join pro_po_detail b on a.id_pod = b.id_pod 
                join pro_po m on a.id_po = m.id_po 
                join pro_pr_detail c on a.id_prd = c.id_prd 
                join pro_po_customer_plan d on a.id_plan = d.id_plan 
                join pro_po_customer h on a.id_poc = h.id_poc 
                join pro_customer_lcr e on d.id_lcr = e.id_lcr
                join pro_customer i on h.id_customer = i.id_customer 
                join acl_user j on i.id_marketing = j.id_user 
                join pro_master_provinsi f on e.prov_survey = f.id_prov 
                join pro_master_kabupaten g on e.kab_survey = g.id_kab
                join pro_penawaran p on h.id_penawaran = p.id_penawaran
                join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
                join pro_master_transportir_sopir l on b.sopir_po = l.id_master
                join pro_master_terminal r on o.id_terminal = r.id_master
                where a.id_dsd = '" . $key['id_dsd'] . "'";
            $hasil = $con->getRecord($sqlds);

            $nama_cust = $hasil['nama_customer'];
            $alamat_survey = $hasil['alamat_survey'] . ", " . $hasil['nama_prov'] . ", " . $hasil['nama_kab'];
            $no_plat = $hasil['nomor_plat'];
            $nama_sopir = $hasil['nama_sopir'];
            $id_wilayah_po = $hasil['id_wilayah_po'];
            $pic_marketing = $hasil['pic_marketing'];
            $nama_terminal = $hasil['nama_terminal'] . " " . $hasil['tanki_terminal'] . " " . $hasil['lokasi_terminal'];
            $id_terminal = $hasil['id_terminal'];
            $id_produk = $hasil['produk_poc'];
            $volume_kirim = $hasil['volume_po'];
            $vendor = $hasil['pr_vendor'];
            $area = $hasil['id_area'];
            $id_po_supplier = $hasil['id_po_supplier'] != NULL ? $hasil['id_po_supplier'] : 0;
            $id_po_receive = $hasil['id_po_receive'] != NULL ? $hasil['id_po_receive'] : 0;

            if ($result['data']['tanggal_loading'] != NULL) {
                $date_loaded_email = date("d/m/Y H:i:s", strtotime($result['data']['tanggal_loading']));
                $date_loaded = date("Y-m-d", strtotime($result['data']['tanggal_loading']));
                $jam_loaded = date("H:i", strtotime($result['data']['tanggal_loading']));
            } else {
                $date_loaded_email = "";
                $date_loaded = NULL;
                $jam_loaded = NULL;
            }

            if ($result['data']['drop_point_start'] == NULL) {
                $drop_point_start = '0000-00-00 00:00:00';
            } else {
                $drop_point_start = $result['data']['drop_point_start'];
            }

            if ($result['data']['drop_point_end'] == NULL) {
                $drop_point_end = '0000-00-00 00:00:00';
            } else {
                $drop_point_end = $result['data']['drop_point_end'];
            }

            if ($result['data']['is_delivered'] == 0) {

                if ($result['data']['is_loaded'] == 1 && $result['data']['is_cancel'] == 0) {

                    $cek3 = "select * from new_pro_inventory_depot where id_jenis = '7' and id_dsd = '" . $key['id_dsd'] . "'";
                    $ada3 = $con->getRecord($cek3);


                    if ($ada3 == "" || $ada3 == NULL) {
                        $sqlupdate = "
                            update pro_po_ds_detail set is_loaded = '1', tanggal_loaded = '" . $date_loaded . "', jam_loaded = '" . $jam_loaded . "', drop_point_start = '" . $drop_point_start . "', drop_point_end = '" . $drop_point_end . "' where id_dsd = '" . $key['id_dsd'] . "'";
                        $con->setQuery($sqlupdate);
                        $oke  = $oke && !$con->hasError();

                        if ($oke) {
                            $sql4 = "insert into new_pro_inventory_depot (id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, out_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_dsd, id_pr, id_prd)
                                VALUES
                                ('generate Loading', '7', '" . $id_produk . "', '" . $id_terminal . "', '" . $vendor . "', '" . $id_po_supplier . "' , '" . $id_po_receive . "', '" . $date_loaded . "', '" . $volume_kirim . "', '-" . $volume_kirim . "', 'Loaded', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', 'OSLOG','" . $key['id_dsd'] . "', '" . $hasil['id_pr'] . "', '" . $hasil['id_prd'] . "')";
                            $con->setQuery($sql4);
                            $oke  = $oke && !$con->hasError();

                            $ems1 = "select distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wilayah_po . "' AND (id_user='" . $pic_marketing . "' OR id_role='18' OR id_role='10') AND is_active = 1";

                            if ($ems1) {
                                $rms1 = $con->getResult($ems1);
                                $mail = new PHPMailer;
                                $mail->isSMTP();
                                $mail->Host = 'smtp.gmail.com';
                                $mail->Port = 465;
                                $mail->SMTPSecure = 'ssl';
                                $mail->SMTPAuth = true;
                                $mail->SMTPKeepAlive = true;
                                $mail->Username = USR_EMAIL_PROENERGI202389;
                                $mail->Password = PWD_EMAIL_PROENERGI202389;

                                $mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
                                foreach ($rms1 as $datms) {
                                    $mail->addAddress($datms['email_user']);
                                }
                                $mail->Subject = "Loaded " . $nama_cust . ', ' . $date_loaded_email . "";
                                $mail->msgHTML("Pengiriman telah loading di " . $nama_terminal . " " . $no_plat . " " . $nama_sopir . "");
                                $mail->send();
                            }
                        }
                    }

                    // if ($ada3 == "" || $ada3 == NULL) {
                    //     $cek_loaded = "select is_loaded from pro_po_ds_detail where id_dsd = '" . $key['id_dsd'] . "'";
                    //     $ada_loaded = $con->getRecord($cek_loaded);

                    //     if ($ada_loaded['is_loaded'] == '0') {
                    //         $sqlupdate = "
                    //         update pro_po_ds_detail set is_loaded = '1', tanggal_loaded = '" . $date_loaded . "', jam_loaded = '" . $jam_loaded . "', drop_point_start = '" . $drop_point_start . "', drop_point_end = '" . $drop_point_end . "' where id_dsd = '" . $key['id_dsd'] . "'";
                    //         $con->setQuery($sqlupdate);
                    //         $oke  = $oke && !$con->hasError();

                    //         $ems1 = "select distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wilayah_po . "' AND (id_user='" . $pic_marketing . "' OR id_role='18' OR id_role='10') AND is_active = 1";

                    //         // if ($ems1) {
                    //         //     $rms1 = $con->getResult($ems1);
                    //         //     $mail = new PHPMailer;
                    //         //     $mail->isSMTP();
                    //         //     $mail->Host = 'smtp.gmail.com';
                    //         //     $mail->Port = 465;
                    //         //     $mail->SMTPSecure = 'ssl';
                    //         //     $mail->SMTPAuth = true;
                    //         //     $mail->SMTPKeepAlive = true;
                    //         //     $mail->Username = USR_EMAIL_PROENERGI202389;
                    //         //     $mail->Password = PWD_EMAIL_PROENERGI202389;

                    //         //     $mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
                    //         //     foreach ($rms1 as $datms) {
                    //         //         $mail->addAddress($datms['email_user']);
                    //         //     }
                    //         //     $mail->Subject = "Loaded " . $nama_cust . ', ' . $date_loaded_email . "";
                    //         //     $mail->msgHTML("Pengiriman telah loading di " . $nama_terminal . " " . $no_plat . " " . $nama_sopir . "");
                    //         //     $mail->send();
                    //         // }
                    //     } else {
                    //         $sqlupdate = "
                    //         update pro_po_ds_detail set is_loaded = '1', tanggal_loaded = '" . $date_loaded . "', jam_loaded = '" . $jam_loaded . "', drop_point_start = '" . $drop_point_start . "', drop_point_end = '" . $drop_point_end . "' where id_dsd = '" . $key['id_dsd'] . "'";
                    //         $con->setQuery($sqlupdate);
                    //         $oke  = $oke && !$con->hasError();
                    //     }

                    //     if ($oke) {
                    //         $sql4 = "insert into new_pro_inventory_depot (id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, out_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_dsd, id_pr, id_prd)
                    //             VALUES
                    //             ('generate Loading', '7', '" . $id_produk . "', '" . $id_terminal . "', '" . $vendor . "', '" . $id_po_supplier . "' , '" . $id_po_receive . "', '" . $date_loaded . "', '" . $volume_kirim . "', '-" . $volume_kirim . "', 'Loaded', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', 'OSLOG','" . $key['id_dsd'] . "', '" . $hasil['id_pr'] . "', '" . $hasil['id_prd'] . "')";
                    //         $con->setQuery($sql4);
                    //         $oke  = $oke && !$con->hasError();
                    //     }
                    // }
                } elseif ($result['data']['is_loaded'] == 1 && $result['data']['is_cancel'] == 1) {
                    $sqlupdate = "
                        update pro_po_ds_detail set is_loaded = '1', tanggal_loaded = '" . $date_loaded . "', jam_loaded = '" . $jam_loaded . "', is_cancel = '1', tanggal_cancel = '" . $result['data']['tanggal_cancel'] . "', drop_point_start = '" . $drop_point_start . "', drop_point_end = '" . $drop_point_end . "' where id_dsd = '" . $key['id_dsd'] . "'";
                    $con->setQuery($sqlupdate);
                    $oke  = $oke && !$con->hasError();

                    if ($oke) {
                        $date_cancel_email = date("d/m/Y H:s", strtotime($result['data']['tanggal_cancel']));

                        $ems1 = "select distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wilayah_po . "' AND (id_user='" . $pic_marketing . "' OR id_role='18' OR id_role='10') AND is_active = 1";

                        if ($ems1) {
                            $rms1 = $con->getResult($ems1);
                            $mail = new PHPMailer;
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->Port = 465;
                            $mail->SMTPSecure = 'ssl';
                            $mail->SMTPAuth = true;
                            $mail->SMTPKeepAlive = true;
                            $mail->Username = USR_EMAIL_PROENERGI202389;
                            $mail->Password = PWD_EMAIL_PROENERGI202389;

                            $mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
                            foreach ($rms1 as $datms) {
                                $mail->addAddress($datms['email_user']);
                            }
                            $mail->Subject = "Cancelled  [" . $nama_cust . ', ' . $date_cancel_email . "]";
                            $mail->msgHTML("Pengiriman [" . $alamat_survey . "] [" . $no_plat . "] [" . $nama_sopir . "] telah di Cancel");
                            $mail->send();
                        }
                    }
                } elseif ($result['data']['is_loaded'] == 0 && $result['data']['is_cancel'] == 1) {

                    $sqlupdate_cancel = "
                        update pro_po_ds_detail set is_cancel = '1', tanggal_cancel = '" . $result['data']['tanggal_cancel'] . "', drop_point_start = '" . $drop_point_start . "', drop_point_end = '" . $drop_point_end . "' where id_dsd = '" . $key['id_dsd'] . "'";
                    $con->setQuery($sqlupdate_cancel);
                    $oke  = $oke && !$con->hasError();

                    if ($oke) {
                        $date_cancel_email = date("d/m/Y H:s", strtotime($result['data']['tanggal_cancel']));

                        $ems1 = "select distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wilayah_po . "' AND (id_user='" . $pic_marketing . "' OR id_role='18' OR id_role='10') AND is_active = 1";

                        if ($ems1) {
                            $rms1 = $con->getResult($ems1);
                            $mail = new PHPMailer;
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->Port = 465;
                            $mail->SMTPSecure = 'ssl';
                            $mail->SMTPAuth = true;
                            $mail->SMTPKeepAlive = true;
                            $mail->Username = USR_EMAIL_PROENERGI202389;
                            $mail->Password = PWD_EMAIL_PROENERGI202389;

                            $mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
                            foreach ($rms1 as $datms) {
                                $mail->addAddress($datms['email_user']);
                            }
                            $mail->Subject = "Cancelled  [" . $nama_cust . ', ' . $date_cancel_email . "]";
                            $mail->msgHTML("Pengiriman [" . $alamat_survey . "] [" . $no_plat . "] [" . $nama_sopir . "] telah di Cancel");
                            $mail->send();
                        }
                    }
                }
            } else {
                $sqlupdate = "
            	    update pro_po_ds_detail set is_loaded = '1', tanggal_loaded = '" . $date_loaded . "', jam_loaded = '" . $jam_loaded . "', is_delivered = '1', tanggal_delivered = '" . $result['data']['tanggal_delivered'] . "', drop_point_start = '" . $drop_point_start . "', drop_point_end = '" . $drop_point_end . "' where id_dsd = '" . $key['id_dsd'] . "'";
                $con->setQuery($sqlupdate);
                $oke  = $oke && !$con->hasError();

                $cek1 = "select a.id_plan, d.volume_kirim from pro_po_ds_detail a join pro_po_customer b on a.id_poc = b.id_poc join pro_customer c on b.id_customer = c.id_customer join pro_po_customer_plan d on a.id_plan=d.id_plan where a.id_dsd = '" . $key['id_dsd'] . "'";
                $row1 = $con->getRecord($cek1);

                $sql2 = "update pro_po_customer_plan set realisasi_kirim = " . $row1['volume_kirim'] . " where id_plan = '" . $row1['id_plan'] . "'";
                $con->setQuery($sql2);
                $oke  = $oke && !$con->hasError();

                if ($oke) {
                    $cek3 = "select * from new_pro_inventory_depot where id_jenis = '7' and id_dsd = '" . $key['id_dsd'] . "'";
                    $ada3 = $con->getRecord($cek3);

                    if ($ada3 == "" || $ada3 == NULL) {
                        $sql4 = "insert into new_pro_inventory_depot (id_datanya, id_jenis, id_produk, id_terminal, id_vendor, id_po_supplier, id_po_receive, tanggal_inven, out_inven, out_inven_virtual, keterangan, created_time, created_ip, created_by, id_dsd, id_pr, id_prd)
                        VALUES
                       ('generate Loading', '7', '" . $id_produk . "', '" . $id_terminal . "', '" . $vendor . "', '" . $id_po_supplier . "' , '" . $id_po_receive . "', '" . $date_loaded . "', '" . $volume_kirim . "', '-" . $volume_kirim . "', 'Loaded', NOW(), '" . $_SERVER['REMOTE_ADDR'] . "', 'OSLOG','" . $key['id_dsd'] . "','" . $hasil['id_pr'] . "', '" . $hasil['id_prd'] . "')";
                        $con->setQuery($sql4);
                        $oke  = $oke && !$con->hasError();
                    }

                    $date_delivered_email = date("d/m/Y H:s", strtotime($result['data']['tanggal_delivered']));

                    $ems1 = "SELECT distinct email_user FROM acl_user WHERE id_wilayah ='" . $id_wilayah_po . "' AND (id_user='" . $pic_marketing . "' OR id_role='18' OR id_role='10') AND is_active=1";

                    if ($ems1) {
                        $rms1 = $con->getResult($ems1);
                        $mail = new PHPMailer;
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->Port = 465;
                        $mail->SMTPSecure = 'ssl';
                        $mail->SMTPAuth = true;
                        $mail->SMTPKeepAlive = true;
                        $mail->Username = USR_EMAIL_PROENERGI202389;
                        $mail->Password = PWD_EMAIL_PROENERGI202389;

                        $mail->setFrom(USR_EMAIL_PROENERGI202389, 'Pro-Energi');
                        foreach ($rms1 as $datms) {
                            $mail->addAddress($datms['email_user']);
                        }
                        $mail->Subject = "Delivered  [" . $nama_cust . ', ' . $date_delivered_email . "]";
                        $mail->msgHTML("Pengiriman telah terkirim di [" . $alamat_survey . "] [" . $no_plat . "] [" . $nama_sopir . "]");
                        $mail->send();
                    }
                }
            }
        }
    }
    if ($oke) {
        $con->commit();
        $con->close();
        echo "Berhasil terupdate";
    } else {
        $con->rollBack();
        $con->clearError();
        $con->close();
        echo "Gagal di update";
    }
} else {
    echo "Tidak ada data terupdate";
}
