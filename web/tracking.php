<?php
session_start();
$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$public_base_directory = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat_base_directory;
require_once($public_base_directory . "/libraries/helper/load.php");
load_helper("autoload");

ini_set('memory_limit', '-1');
ini_set('max_execution_time', '1200');

$auth    = new MyOtentikasi();
$con     = new Connection();
$flash    = new FlashAlerts;
$sesrol = paramDecrypt($_SESSION['sinori' . SESSIONID]['id_role']);

$query = '
        select 
            a.*, 
            c.pr_pelanggan, 
            i.nama_customer, 
            e.alamat_survey, 
            f.nama_prov, 
            g.nama_kab, 
            j.fullname, 
            n.nama_transportir, 
            n.nama_suplier, 
            b.no_spj, 
            k.nomor_plat, 
            l.nama_sopir, 
            b.volume_po, 
            h.produk_poc, 
            p.id_area, 
            c.pr_vendor, 
            r.nama_terminal, 
            r.tanki_terminal, 
            r.lokasi_terminal, 
            s.wilayah_angkut, 
            m.nomor_po, 
            m.tanggal_po, 
            c.produk, 
            b.tgl_kirim_po, 
            h.nomor_poc, 
            h.tanggal_poc, 
            b.tgl_eta_po, 
            b.jam_eta_po, 
            b.mobil_po 
        from pro_po_ds_detail a 
        join pro_po_ds o on a.id_ds = o.id_ds 
        join pro_po_detail b on a.id_pod = b.id_pod 
        join pro_po m on a.id_po = m.id_po 
        join pro_pr_detail c on a.id_prd = c.id_prd 
        join pro_po_customer_plan d on a.id_plan = d.id_plan 
        join pro_po_customer h on d.id_poc = h.id_poc 
        join pro_customer_lcr e on d.id_lcr = e.id_lcr
        join pro_customer i on h.id_customer = i.id_customer 
        join acl_user j on i.id_marketing = j.id_user 
        join pro_master_provinsi f on e.prov_survey = f.id_prov 
        join pro_master_kabupaten g on e.kab_survey = g.id_kab
        join pro_penawaran p on h.id_penawaran = p.id_penawaran  
        join pro_master_area q on p.id_area = q.id_master 
        join pro_master_transportir_mobil k on b.mobil_po = k.id_master 
        join pro_master_transportir_sopir l on b.sopir_po = l.id_master
        join pro_master_transportir n on m.id_transportir = n.id_master 
        join pro_master_terminal r on o.id_terminal = r.id_master 
        join pro_master_wilayah_angkut s on e.id_wil_oa = s.id_master and 
            e.prov_survey = s.id_prov and 
            e.kab_survey = s.id_kab
        where a.is_loaded = 1';

if ($sesrol == 14) {
    $query .= " and i.id_customer = '" . paramDecrypt($_SESSION["sinori" . SESSIONID]["id_user"]) . "'";
}

$response         = null;
$arrResponse     = array();
// $nomor_platnya     = htmlspecialchars($_GET["nomor_plat"], ENT_QUOTES);

$sql01 = "select distinct nomor_plat from pro_master_transportir_mobil where 1=1 and link_gps = 'OSLOG'";
$res01 = $con->getResult($sql01);

// if ($nomor_platnya) {
//     $url01 = "https://oslog.id/apiv5/open-api/current-vehicle-status?apiKey=3549af8b-2607-4415-8d0d-09130e2e4c29&licensePlate=";
//     $tmp01 = file_get_contents($url01 . urlencode($nomor_platnya));
//     if ($tmp01) {
//         array_push($arrResponse, json_decode($tmp01, true));
//     }
//     /*if(count($res01) > 0){
// 			foreach($res01 as $data01){
// 				$tmp01 = file_get_contents($url01.urlencode($data01['nomor_plat']));
// 				if($tmp01){
// 					array_push($arrResponse, json_decode($tmp01, true));
// 				}
// 			}
// 		}*/
// }

/*$url02 = "https://api.inovatrack.com/api/VehicleSummary/GetAll?memberCode=pro&password=Sf5cXNxkKOpYaKBA";
	$resT2 = file_get_contents($url02);
	$res02 = json_decode($resT2, true);
	if($res02 && count($res02) > 0){
		foreach($res02 as $data02){
			array_push($arrResponse, array("data"=>array("lat"=>$data02['lat'], "lon"=>$data02['lon'], "vehicle_name"=>$data02['vehicle_name'])));
		}
	}*/

// if (count($arrResponse) > 0) {
//     $response = json_encode($arrResponse);
// }


/*$response = null;
    $tot_record = $con->num_rows($query);
    if ($tot_record > 0) {
        $result = $con->getResult($query);
        if ($sesrol == 14) {
            $url = 'https://api.inovatrack.com/v1/api/data/GetVehicles?memberCode=pro&password=Sf5cXNxkKOpYaKBA&vehicles=';
            $plat = '';

            foreach ($result as $data) {
                $plat .= str_replace(' ', '', $data['nomor_plat']) . '|';
            }
            rtrim($plat, '|');

            $url .= $plat;
        } else
            $url = 'https://oslog.id/apiv5/open-api/current-vehicle-status?apiKey=3549af8b-2607-4415-8d0d-09130e2e4c29&licensePlate=B+9106+SYN';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
            
		$response 		= curl_exec($curl);
        $err 			= curl_error($curl);
        curl_close($curl);

        if ($err){
            $response = null;
		} else{
			$arrResponse 	= array();
			$arrTmp01 		= json_decode($response, true);
			array_push($arrResponse, $arrTmp01);
			$response = json_encode($arrResponse);
			//echo '<pre>'; print_r($arrTmp01); exit;
		}
        
    }*/
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js" => array("formatNumber", "jqueryUI", "myGrid"), "css" => array("jqueryUI"))); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="" />

<body class="skin-blue fixed">
    <?php include_once($public_base_directory . "/web/layout/header.php"); ?>
    <div class="wrapper row-offcanvas row-offcanvas-left">
        <?php include_once($public_base_directory . "/web/layout/sidebar.php"); ?>
        <aside class="right-side">
            <section class="content-header">
                <h1>Tracking</h1>
            </section>
            <section class="content">
                <?php $flash->display(); ?>
                <div class="alert alert-danger alert-dismissible" style="display:none">
                    <div class="box-tools">
                        <button data-alert="remove" class="btn btn-box-tool close" type="button"><i class="fa fa-times"></i></button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group form-group-sm">
                            <label class="control-label col-md-4">Nomor Plat *</label>
                            <div class="col-md-8">
                                <span id="warning" style="color: red; display:none;"><b>Silahkan pilih plat nomor</b></span>
                                <select name="nomor_plat" id="nomor_plat" class="form-control select2">
                                    <option></option>
                                    <?php
                                    if (count($res01) > 0) {
                                        foreach ($res01 as $data01) {
                                            $selected = ($nomor_platnya == $data01['nomor_plat']) ? 'selected' : '';
                                            echo '<option value="' . $data01['nomor_plat'] . '" ' . $selected . '>' . $data01['nomor_plat'] . '</option>';
                                        }
                                    }

                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group form-group-sm">
                            <button type="button" class="btn btn-primary btn-sm" name="btnSbmt" id="btnSbmt" style="min-width:90px;">
                                <i class="fa fa-search jarak-kanan"></i> Cari</button>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div id="map" style="border: 4px double #ddd;">
                            <iframe id="mapsFrame" width="100%" height="500vh" src="" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
                <?php $con->close(); ?>
            </section>
            <?php include_once($public_base_directory . "/web/layout/footer.php"); ?>
        </aside>
    </div>

    <div class="modal fade" id="loading_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue">
                    <h4 class="modal-title">Loading Data ...</h4>
                </div>
                <div class="modal-body text-center modal-loading"></div>
            </div>
        </div>
    </div>

    <style type="text/css">
        #map {
            min-height: 420px;
        }
    </style>

    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <!-- <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script> -->

    <script>
        $(document).ready(function() {
            var formValidasiCfg = {
                submitHandler: function(form) {
                    $("#loading_modal").modal({
                        keyboard: false,
                        backdrop: 'static'
                    });

                    if ($("#cekkolnup").is(":checked") && $("#nup_fee").val() == "") {
                        $("#loading_modal").modal("hide");
                        $.validator.showErrorField('nup_fee', "Kolom ini belum diisi atau dipilih");
                        setErrorFocus($("#nup_fee"), $("form#gform"), false);
                    } else {
                        form.submit();
                    }
                }
            };
            // $("form#gform").validate($.extend(true, {}, config.validation, formValidasiCfg));

            $("#mapsFrame").css("min-height", ($("section.content").outerHeight()) + "px");

            $("#btnSbmt").click(function() {
                var plat_nomor = $("#nomor_plat").val();

                if (plat_nomor == "") {
                    $("#warning").css("display", "inline")
                } else {
                    $("#warning").css("display", "none")
                    var iframe = $("#mapsFrame");
                    iframe.attr("src", "https://oslog.id/embedd-monitoring-vehicle/index.html?apikey_jyoti=3549af8b-2607-4415-8d0d-09130e2e4c29&licensePlate=" + plat_nomor);
                }

            })
        });
    </script>
</body>

</html>