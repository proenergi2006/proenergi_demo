<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$param  = htmlspecialchars($_GET['param'], ENT_QUOTES);

	if(!$_SESSION["list_mobil"][$param]){
		$url 	= 'https://api.inovatrack.com/api/data/GetVehicles?memberCode=pro&password=Sf5cXNxkKOpYaKBA&vehicles='.$param;
		$curl 	= curl_init();
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
		$response 	= curl_exec($curl);
		$err 		= curl_error($curl);	
		$response 	= ($err) ? null : $response;
		curl_close($curl);
		$sessList = ($response) ? json_decode($response, true) : NULL;
		$_SESSION["list_mobil"][$param] = (is_array($sessList) && count($sessList) > 0) ? $sessList : NULL;
	}
	
	//unset($_SESSION["list_mobil"]);
	//var_dump($_SESSION["list_mobil"][$param]);

?>
<?php if($_SESSION["list_mobil"][$param]){ ?>

<style type="text/css">
    #map { height: 350px; }
</style>
<div id="map"></div>

<script>
    var dataMap = JSON.parse('<?php echo json_encode($_SESSION["list_mobil"][$param]); ?>');
    if (dataMap.length>0) { 
        var map = L.map('map').setView([dataMap[0].Latitude, dataMap[0].Longitude], 16);

        mapLink = '<a href="http://openstreetmap.org">OpenStreetMap</a>';
        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; ' + mapLink + ' Contributors',
        }).addTo(map);

        let i = 0;
        while (i < dataMap.length) {
            marker = new L.marker([dataMap[i].Latitude, dataMap[i].Longitude])
                .bindPopup('<?php echo $param; ?>')
                .openPopup()
                .addTo(map);
            i++;
        }
    }
</script>
<?php } else print_r($sessList); ?>
