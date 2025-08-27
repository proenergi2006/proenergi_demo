<?php
	$q2 = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$q3 = isset($_POST["q3"])?htmlspecialchars($_POST["q3"], ENT_QUOTES):date("d/m/Y");
	$q4 = isset($_POST["q4"])?htmlspecialchars($_POST["q4"], ENT_QUOTES):'';
?>
<div style="margin-bottom:20px; border-bottom:4px double #ccc;">
<h3 style="margin:0px 0px 10px; font-size:20px; font-weight:bold;">PENCARIAN</h3>
<form name="searchForm" id="searchForm" role="form" class="form-horizontal form-validasi" method="post" action="<?php echo BASE_SELF;?>">
    <div class="form-group row">
        <div class="col-sm-3 col-sm-top">
            <div class="input-group">
                <span class="input-group-addon">Tgl Kirim</span>
                <input type="text" name="q3" id="q3" class="form-control input-sm datepicker validate[required]" value="<?php echo $q3;?>" autocomplete="off" />
            </div>
        </div>
        <div class="col-sm-3 col-sm-top">
            <div class="input-group">
                <span class="input-group-addon">S/D</span>
                <input type="text" name="q4" id="q4" class="form-control input-sm datepicker" value="<?php echo $q4;?>" autocomplete="off" />
            </div>
        </div>
        <div class="col-sm-6 col-sm-top">
            <button type="submit" class="btn btn-info btn-sm" name="btnSearch1" id="btnSearch1" style="width:80px;">Cari</button>
        </div>
        <script type="text/javascript">
            $('#btnSearch1').on('click', function(){
                let q3 = $('#q3').val()
                let q3d = q3.substr(0, 2)
                let q3m = q3.substr(3, 2)
                let q3y = q3.substr(6, 4)
                    q3 = q3y+q3m+q3d
                let q4 = $('#q4').val()
                if (!q4) {
                    alert('Anda harus mengisi tanggal dengan lengkap')
                    $('#q4').focus()
                    return false
                }
                let q4d = q4.substr(0, 2)
                let q4m = q4.substr(3, 2)
                let q4y = q4.substr(6, 4)
                    q4 = q4y+q4m+q4d
                if (q4<q3) {
                    alert('Tanggal pertama tidak boleh lebih dari tanggal kedua')
                    $('#q4').focus()
                    return false
                }
                return true
            })
        </script>
    </div>
</form>
</div>

<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#data-truck" aria-controls="data-truck" role="tab" data-toggle="tab">Mobil Tanki</a></li>
    <li role="presentation" class=""><a href="#data-kapal" aria-controls="data-kapal" role="tab" data-toggle="tab">Kapal</a></li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="data-truck">
        <?php 
			$sql1 = "select a.*, c.pr_pelanggan, i.nama_customer, i.kode_pelanggan, e.alamat_survey, f.nama_prov, g.nama_kab, j.fullname, h.nomor_poc, 
					n.nama_transportir, n.nama_suplier, n.lokasi_suplier, b.no_spj, b.tgl_eta_po, b.jam_eta_po, k.nomor_plat, l.nama_sopir, b.volume_po, 
					k.photo as poto_mobil, k.photo_ori as poto_mobil_ori, l.photo as poto_sopir, l.photo_ori as poto_sopir_ori, d.tanggal_kirim, b.mobil_po  
					from pro_po_ds_detail a join pro_po_detail b on a.id_pod = b.id_pod join pro_pr_detail c on a.id_prd = c.id_prd 
					join pro_po_customer_plan d on a.id_plan = d.id_plan join pro_customer_lcr e on d.id_lcr = e.id_lcr
					join pro_master_provinsi f on e.prov_survey = f.id_prov join pro_master_kabupaten g on e.kab_survey = g.id_kab
					join pro_po_customer h on d.id_poc = h.id_poc join pro_customer i on h.id_customer = i.id_customer join acl_user j on i.id_marketing = j.id_user 
					join pro_master_transportir_mobil k on b.mobil_po = k.id_master join pro_master_transportir_sopir l on b.sopir_po = l.id_master
					join pro_po m on a.id_po = m.id_po join pro_master_transportir n on m.id_transportir = n.id_master join pro_po_ds o on a.id_ds = o.id_ds
					where a.is_loaded = 1";
			if($q2 == 14)
					$sql .= '';
					//$sql1 .= " and i.id_customer = '".paramDecrypt($_SESSION["sinori".SESSIONID]["customer"])."'";
			else if($q2 == 11 || $q2 == 17)
				$sql1 .= " and i.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'";
			
			if($q3 != "" && $q4 == "")
				$sql1 .= " and d.tanggal_kirim = '".tgl_db($q3)."'";
			else if($q3 != "" && $q4 != "")
				$sql1 .= " and d.tanggal_kirim between '".tgl_db($q3)."' and '".tgl_db($q4)."'";
			
			$sql1 .= " order by a.tanggal_loading desc, a.jam_loading, a.nomor_urut_ds, a.id_dsd";
			$res1 = $con->getResult($sql1);
			if(count($res1) == 0){
				echo '<p class="text-center">Tidak ada pengiriman</p>';
			} else{
				$nom=0;
				foreach($res1 as $data1){
					$pathMt = $public_base_directory.'/files/uploaded_user/lampiran/'.$data1['poto_mobil'];
					$pathSt = $public_base_directory.'/files/uploaded_user/lampiran/'.$data1['poto_sopir'];
					$listLn = paramEncrypt($data1['id_dsd']."|#|1|#|".$data1['nomor_poc']."[]".$data1['nama_customer']."|#|".$data1['mobil_po']."|#|".$data1['volume_po']);
					$eta_po	= date("d/m/Y", strtotime($data1['tgl_eta_po'])).($data1['jam_eta_po']?' '.date("H:i", strtotime($data1['jam_eta_po'])):'');
					if($data1['is_delivered']){
						$status = '<p style="margin-bottom:0px;"><b>Delivered</b><br/>'.date("d/m/Y H:i", strtotime($data1['tanggal_delivered'])).'</p>';
					} else if($data1['is_cancel']){
						$status = '<p style="margin-bottom:0px;" class="text-red"><b>Canceled</b><br/>'.date("d/m/Y H:i", strtotime($data1['tanggal_cancel'])).'</p>';
					} else{
						if($data1['status_pengiriman']){
							$bmp = json_decode($data1['status_pengiriman'], true);
							$idb = count($bmp)-1;
							$statKirim = "[".$bmp[$idb]['tanggal'].'] '.$bmp[$idb]['status'];
						} else{
							$statKirim = '<i>Belum ada status pengiriman</i>';
						}
						$status = '<p style="margin-bottom:0px; padding-right:30px;">'.$statKirim.'</p>';
					}

		?>
        <div class="wrap-list">
            <div class="table-responsive">
                <table class="table table-utama no-border" style="margin-bottom:0px;">
                    <tr>
                        <td width="150" rowspan="6" class="text-center">
                            <?php 
                                echo '<div style="border:1px solid #ddd; height:auto; width:auto;"><p class="titelnya"><b>Driver</b></p>';
                                if($data1['poto_sopir'] && file_exists($pathSt)){
                                    $urliSt = BASE_URL.'/files/uploaded_user/lampiran/'.$data1['poto_sopir'];
                                    echo '<img src="'.$urliSt.'" title="'.$data1['poto_sopir_ori'].'" width="75" height="100" />';
                                } else{
                                    echo '<img src="'.BASE_IMAGE.'/no_profile_image.jpg" width="75" height="100" />';
                                }
                                echo '<p>'.$data1['nama_sopir'].'</p></div>';
                            ?>
                        </td>
                        <td width="160" rowspan="6" class="text-center">
                            <?php 
                                echo '<div style="border:1px solid #ddd; height:auto; width:auto;"><p class="titelnya"><b>Mobil Tanki</b></p>';
                                if($data1['poto_mobil'] && file_exists($pathMt)){
                                    $urliMt = BASE_URL.'/files/uploaded_user/lampiran/'.$data1['poto_mobil'];
                                    echo '<img src="'.$urliMt.'" title="'.$data1['poto_mobil_ori'].'" width="133" height="100" />';
                                } else{
                                    echo '<img src="'.BASE_IMAGE.'/img_not_available.jpg" width="133" height="100" />';
                                }
                                echo '<p><a class="getlokasimobil" data-mobil="'.$data1['nomor_plat'].'">'.$data1['nomor_plat'].'</a></p></div>';
                            ?>
                        </td>
                        <td width="10%">Nomor PO</td>
                        <td width="2%" class="text-center">:</td>
                        <td><?php echo $data1['nomor_poc'];?></td>
                    </tr>
                    <tr>
                        <td>Volume</td>
                        <td class="text-center">:</td>
                        <td><?php echo number_format($data1['volume_po']).' Liter';?></td>
                    </tr>
                    <tr>
                        <td>Tanggal ETA</td>
                        <td class="text-center">:</td>
                        <td><?php echo $eta_po;?></td>
                    </tr>
                    <tr>
                        <td>Transportir</td>
                        <td class="text-center">:</td>
                        <td><?php echo $data1['nama_suplier'].', '.$data1['lokasi_suplier'];?></td>
                    </tr>
                    <tr>
                        <td>Posisi</td>
                        <td class="text-center">:</td>
                        <td><?php echo $status;?></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <a class="btn btn-info btn-sm listStsT jarak-kanan" data-param="<?php echo $listLn;?>">Histori Perjalanan</a>
                            <?php if($q2 == 14){?><a class="btn btn-info btn-sm editStsT" data-param="<?php echo $listLn;?>">Komentar &amp; Rating</a><?php } ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
		<?php } } ?>
	</div>

    <div role="tabpanel" class="tab-pane" id="data-kapal">
        <div class="row">
		<?php 
			$sql2 = "select a.*, c.tanggal_kirim, d.produk_poc, d.nomor_poc, e.nama_customer, f.nama_suplier, f.lokasi_suplier 
					from pro_po_ds_kapal a join pro_pr_detail b on a.id_prd = b.id_prd 
					join pro_po_customer_plan c on b.id_plan = c.id_plan join pro_po_customer d on c.id_poc = d.id_poc 
					join pro_customer e on d.id_customer = e.id_customer join pro_master_transportir f on a.transportir = f.id_master 			
					where a.is_loaded = 1";

			if($q2 == 14)
				$sql2 .= " and e.id_customer = '".paramDecrypt($_SESSION["sinori".SESSIONID]["customer"])."'";
			else if($q2 == 11 || $q2 == 17)
				$sql2 .= " and e.id_marketing = '".paramDecrypt($_SESSION["sinori".SESSIONID]["id_user"])."'";
			
			if($q3 != "" && $q4 == "")
				$sql2 .= " and c.tanggal_kirim = '".tgl_db($q3)."'";
			else if($q3 != "" && $q4 != "")
				$sql2 .= " and c.tanggal_kirim between '".tgl_db($q3)."' and '".tgl_db($q4)."'";
			
			$sql2 .= " order by a.tanggal_loading desc, a.jam_loading, c.tanggal_kirim";
			$res2 = $con->getResult($sql2);
			if(count($res2) == 0){
				echo '<p class="text-center">Tidak ada pengiriman</p>';
			} else{
				$nom=0;
				foreach($res2 as $data2){
					$listLk = paramEncrypt($data2['id_dsk']."|#|2|#|".$data2['nomor_poc']."[]".$data2['nama_customer']."|#|".$data2['mobil_po']."|#|".$data2['bl_lo_jumlah']);
					if($data2['is_delivered']){
						$posisi = '<p style="margin-bottom:0px;"><b>Delivered</b><br/>'.date("d/m/Y H:i", strtotime($data2['tanggal_delivered'])).'</p>';
					} else if($data2['is_cancel']){
						$posisi = '<p style="margin-bottom:0px;" class="text-red"><b>Canceled</b><br/>'.date("d/m/Y H:i", strtotime($data2['tanggal_cancel'])).'</p>';
					} else{
						if($data2['status_pengiriman']){
							$bmp = json_decode($data2['status_pengiriman'], true);
							$idb = count($bmp)-1;
							$stKirim = "[".$bmp[$idb]['tanggal'].'] '.$bmp[$idb]['status'];
						} else{
							$stKirim = '<i>Belum ada status pengiriman</i>';
						}
						$posisi = '<p style="margin-bottom:0px; padding-right:30px;">'.$stKirim.'</p>';
					}

		?>
        <div class="col-sm-6">
        	<div class="wrap-list">
                <div class="table-responsive">
                    <table class="table table-utama no-border" style="margin-bottom:0px;">
                        <tr>
                            <td width="85">Nomor PO</td>
                            <td width="15" class="text-center">:</td>
                            <td><?php echo $data2['nomor_poc'];?></td>
                        </tr>
                        <tr>
                            <td>Volume</td>
                            <td class="text-center">:</td>
                            <td><?php echo number_format($data2['bl_lo_jumlah']).' Liter';?></td>
                        </tr>
                        <tr>
                            <td>Tanggal ETA</td>
                            <td class="text-center">:</td>
                            <td><?php echo date("d/m/Y", strtotime($data2['tanggal_kirim']));?></td>
                        </tr>
                        <tr>
                            <td>Transportir</td>
                            <td class="text-center">:</td>
                            <td><?php echo $data2['nama_suplier'].', '.$data2['lokasi_suplier'];?></td>
                        </tr>
                        <tr>
                            <td>Nama Kapal</td>
                            <td class="text-center">:</td>
                            <td><?php echo $data2['vessel_name'];?></td>
                        </tr>
                        <tr>
                            <td>Kapten</td>
                            <td class="text-center">:</td>
                            <td><?php echo $data2['kapten_name'];?></td>
                        </tr>
                        <tr>
                            <td>Posisi</td>
                            <td class="text-center">:</td>
                            <td><?php echo $posisi;?></td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <a class="btn btn-info btn-sm listStsT jarak-kanan" data-param="<?php echo $listLk;?>">Histori Perjalanan</a>
                                <?php if($q2 == 14){?><a class="btn btn-info btn-sm editStsT" data-param="<?php echo $listLk;?>">Komentar &amp; Rating</a><?php } ?>
                            </td>
                        </tr>
                    </table>
                </div>
        	</div>
		</div>
		<?php } } ?>
    </div></div>

</div>
			
<div class="modal fade" id="status_history_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-histori">
        <div class="modal-content">
            <div class="modal-header bg-blue">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Histori Status Pengiriman</h4>
            </div>
            <div class="modal-body">
                <p id="jdlKirim"></p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="listHistoriLP">
                        <thead>
                            <tr>
                                <th class="text-center" width="6%">No</th>
                                <th class="text-center" width="16%">Tanggal</th>
                                <th class="text-center" width="78%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div id="detilHistoriLp"></div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="status_kirim_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-blue">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Komentar &amp; Rating</h4>
            </div>
            <div class="modal-body"><div id="komentarLp"></div></div>
        </div>
    </div>
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

<div class="modal fade" id="show_maptracking_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width:1000px;">
        <div class="modal-content">
            <div class="modal-header bg-blue">
                <button type="button" class="close btnBatal_show_maptracking_modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Tracking</h4>
            </div>
            <div class="modal-body">
                <div class="text-left infonya"></div>	
                <div id="map_track_view" style="border: 4px double #ddd;"></div>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
	.table{
		border:1px solid #ddd;
		margin-bottom:15px;
		border-collapse:collapse;
		border-spacing:0px;
	}
	.table > thead > tr > th, 
	.table > tbody > tr > td{
		border:1px solid #ddd;
		padding: 5px;
		font-size:11px;
		font-family:arial;
		vertical-align:top;
	}
	.table-utama > thead > tr > th, 
	.table-utama > tbody > tr > td{
		border:1px solid #ddd;
		padding: 3px 5px;
		font-size:12px;
		font-family:arial;
		vertical-align:top;
	}
	.table > thead > tr > th{
		background-color: #f4f4f4;
		vertical-align: middle;
		padding: 8px 5px;
	}

	.titelnya{
		background-color:#eee;
		padding:3px 0px;
		border-bottom:1px solid #ddd;
	}
	.wrap-list{
		 padding:10px;
		 margin-bottom:15px;
		 background-color: #fff;
		 border:1px solid #ddd;
		 border-top:3px solid #00c0ef;
		-webkit-border-radius: 3px;
				border-radius: 3px;
		-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
				box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
	}
	@media screen and (min-width: 992px) {
		.modal-dialog-histori{
			width: 70%;
		}
	}

	.getlokasimobil{
		cursor: pointer;
	}
	#map_track_view { min-height: 420px; }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" crossorigin=""></script>

<script>
$(document).ready(function(){
	var map_track_view;

	$('.wrap-list').on('click', '.listStsT', function(e){
		var param = $(this).data("param");
		$("#status_kirim_modal").modal("hide");
		$("#status_history_modal").find("#listHistoriLP > tbody, #jdlKirim").html("");
		$("#loading_modal").modal({backdrop:"static"});
		$.ajax({
			type	: 'POST',
			url		: "./__get_pengiriman_list.php",
			data	: {"file":"marketing", "aksi":param},
			cache	: false,
			dataType: "json",
			success : function(data){
				$("#loading_modal").modal("hide");
				$("#status_history_modal").find("#listHistoriLP > tbody").html(data.items);
				$("#status_history_modal").find("#jdlKirim").html(data.judul);
				$("#status_history_modal").find("#detilHistoriLp").html(data.extras);
			}
		});
		$("#status_history_modal").modal();
	});

	$('.wrap-list').on('click', '.editStsT', function(e){
		var param = $(this).data("param");
		$("#status_history_modal").modal("hide");
		$("#loading_modal").modal({backdrop:"static"});
		$.ajax({
			type	: 'POST',
			url		: "./__get_pengiriman_komentar.php",
			data	: {"aksi":param},
			cache	: false,
			success : function(data){
				$("#loading_modal").modal("hide");
				$("#status_kirim_modal").find("#komentarLp").html(data);
				$("#rating").barrating({
					theme: "fontawesome-stars",
					allowEmpty: true,
				});
			}
		});
		$("#status_kirim_modal").modal();
	});

	$("#status_kirim_modal").on("click", "#btnLP1", function(){
		if(confirm("Apakah anda yakin?")){
			var tipe = $("#tipeLP").val(), idnya = $("#idLP").val(), komentar = $("#komentar").val(), rating = $("#rating").val();
			$("#loading_modal").modal({backdrop:"static"});
			$("#status_kirim_modal").modal("hide");
			$.ajax({
				type	: 'POST',
				url		: "./action/pengiriman-list-komentar.php",
				data	: {"komentar":komentar, "rating":rating, "idnya":idnya, "tipe":tipe},
				cache	: false,
				success : function(data){
					$("#loading_modal").modal("hide");
				}
			});
		}
	});

	$('#data-truck').on('click', '.getlokasimobil', function(e){
		let nilai = $(this).data("mobil");
		if(nilai){
			$("#loading_modal").modal({backdrop:"static"});
			$.ajax({
				type	: 'POST',
				url		: "./tracking-view.php",
				data	: {"id1": nilai},
				cache	: false,
				dataType: "json",
				success : function(data){
					if(data.hasil){
						const dataMap = data.items;
						if (dataMap.length>0) { 
							if(map_track_view){
								map_track_view.off(); 
								map_track_view.remove();
							}

							map_track_view = L.map('map_track_view').setView([dataMap[0].data['lat'], dataMap[0].data['lon']], 8);
					
							mapLink = '<a href="http://openstreetmap.org">OpenStreetMap</a>';
							L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
								attribution: '&copy; ' + mapLink + ' Contributors',
								maxZoom: 18,
							}).addTo(map_track_view);
					
							let i = 0;
							while (i < dataMap.length) {
								marker = new L.marker([dataMap[i].data['lat'], dataMap[i].data['lon']])
									.bindPopup(dataMap[i].data['vehicle_name'])
									.openPopup()
									.addTo(map_track_view);
								i++;
							}
						}
						$("#show_maptracking_modal").find(".infonya").addClass("hide").html("");
						$("#show_maptracking_modal").find("#map_track_view").removeClass("hide");
						$("#show_maptracking_modal").modal({backdrop:"static"});
					} else{
						$("#show_maptracking_modal").find(".infonya").removeClass("hide").html(data.items);
						$("#show_maptracking_modal").find("#map_track_view").addClass("hide");
						$("#show_maptracking_modal").modal({backdrop:"static"});
					}
				}
			});
		}
	});

	$("#show_maptracking_modal").on("show.bs.modal", function(){
		$("#loading_modal").modal("hide");
	}).on("shown.bs.modal", function(){
		setTimeout(function() {
			map_track_view.invalidateSize();
		}, 10);
	}).on("hidden.bs.modal", function(){
		$("body").css("padding-right","0px");
	}).on("click", ".btnBatal_show_maptracking_modal", function(){
		$("#show_maptracking_modal").modal("hide");
	});

});
</script>
