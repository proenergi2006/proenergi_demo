<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$idr 	= isset($enk["idr"])?htmlspecialchars($enk["idr"], ENT_QUOTES):'';
	$cek = "select a.id_pr, a.nomor_pr, a.tanggal_pr, a.disposisi_pr, b.nama_cabang, c.id_par, c.tanggal_buat 
			from pro_pr a 
			join pro_master_cabang b on a.id_wilayah = b.id_master 
			left join pro_pr_ar c on a.id_pr = c.id_pr and c.ar_approved = 1 
			where a.id_pr = '".$idr."'";
	$row = $con->getResult($cek);
	$arrMobil = array(1=>"Truck", "Kapal");
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("myGrid","formatNumber"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Purchase Request Detail</h1>
        	</section>
			<section class="content">

				<?php if($enk['idr'] !== '' && isset($enk['idr'])){ ?>
				<?php $flash->display(); ?>
                <div class="row">                
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-body">

                                <table border="0" cellpadding="0" cellspacing="0" id="table-detail">
                                    <tr>
                                        <td width="70">Kode PR</td>
                                        <td width="10">:</td>
                                        <td><?php echo $row[0]['nomor_pr']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal</td>
                                        <td>:</td>
                                        <td><?php echo tgl_indo($row[0]['tanggal_pr']); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Cabang</td>
                                        <td>:</td>
                                        <td><?php echo $row[0]['nama_cabang']; ?></td>
                                    </tr>
                                </table> 
                                <form action="<?php echo ACTION_CLIENT.'/perbaikan-data.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div style="overflow-x: scroll" id="table-long">
                                    <div style="width:1825px; height:auto;">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="table-grid3">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" rowspan="2" width="50">No</th>
                                                        <th class="text-center" rowspan="2" width="180">Customer/ Bidang Usaha</th>
                                                        <th class="text-center" rowspan="2" width="230">Area/ Alamat Kirim/ Wilayah OA</th>
                                                        <th class="text-center" rowspan="2" width="190">Data DR</th>
                                                        <th class="text-center" rowspan="2" width="130">Suplier</th>
                                                        <th class="text-center" rowspan="2" width="195">Depot</th>
                                                        <th class="text-center" rowspan="2" width="150">Harga Beli</th>
                                                        <th class="text-center" rowspan="2" width="70">TOP</th>
                                                        <th class="text-center" colspan="4">Outstanding</th>
                                                        <th class="text-center" rowspan="2" width="120">Kredit Limit</th>
                                                        <th class="text-center" rowspan="2" width="110">Aksi</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center" width="100">AR (Not Yet)</th>
                                                        <th class="text-center" width="100">AR (1 - 30)</th>
                                                        <th class="text-center" width="100">AR (> 30)</th>
                                                        <th class="text-center" width="100">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php 
                                                    $sql = "select a.*, c.tanggal_kirim, e.alamat_survey, e.id_wil_oa, e.jenis_usaha, f.nama_prov, g.nama_kab, d.nomor_poc, 
                                                            h.nama_customer, h.id_customer, h.kode_pelanggan, i.fullname, l.nama_area, o.wilayah_angkut, k.masa_awal, k.masa_akhir, 
                                                            ifnull(m.is_loaded, n.is_loaded) as is_loaded, p.nama_terminal, p.tanki_terminal, p.lokasi_terminal, q.nama_vendor, 
															ifnull(m.id_dsd, n.id_dsk) as is_ds, k.id_area, d.produk_poc 
                                                            from pro_pr_detail a 
                                                            join pro_pr b on a.id_pr = b.id_pr 
                                                            join pro_po_customer_plan c on a.id_plan = c.id_plan 
                                                            join pro_po_customer d on c.id_poc = d.id_poc 
                                                            join pro_customer_lcr e on c.id_lcr = e.id_lcr
                                                            join pro_master_provinsi f on e.prov_survey = f.id_prov 
                                                            join pro_master_kabupaten g on e.kab_survey = g.id_kab
                                                            join pro_customer h on d.id_customer = h.id_customer 
                                                            join acl_user i on h.id_marketing = i.id_user 
                                                            join pro_master_cabang j on h.id_wilayah = j.id_master 
                                                            join pro_penawaran k on d.id_penawaran = k.id_penawaran  
                                                            join pro_master_area l on k.id_area = l.id_master 
                                                            left join pro_po_ds_detail m on a.id_prd = m.id_prd 
                                                            left join pro_po_ds_kapal n on a.id_prd = n.id_prd 
                                                            left join pro_master_wilayah_angkut o on e.id_wil_oa = o.id_master and e.prov_survey = o.id_prov 
                                                                and e.kab_survey = o.id_kab 
															left join pro_master_terminal p on a.pr_terminal = p.id_master 
															left join pro_master_vendor q on a.pr_vendor = q.id_master 
                                                            where a.id_pr = '".$idr."' order by a.is_approved desc, c.tanggal_kirim, k.id_area, a.id_plan, a.id_prd";
                                                    $res = $con->getResult($sql);
                                                    if(count($res) == 0){
                                                        echo '<tr><td colspan="14" style="text-align:center">Data tidak ditemukan </td></tr>';
                                                    } else{
                                                        $nom = 0;
                                                        foreach($res as $data){
                                                            $nom++;
                                                            $idp 	= $data['id_prd'];
                                                            $tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
                                                            $alamat	= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
                                                            $kirim	= date("d/m/Y", strtotime($data['tanggal_kirim']));
                                                            $ar_not = $data['pr_ar_notyet'];
                                                            $ar_one = $data['pr_ar_satu'];
                                                            $ar_two = $data['pr_ar_dua'];
                                                            $ar_tot = $data['pr_ar_notyet'] + $data['pr_ar_satu'] + $data['pr_ar_dua'];
                                                            $ar_krl = $data['pr_kredit_limit'];		
                                                ?>
                                                    <tr>
                                                        <td class="text-center"><?php echo $nom; ?></td>
                                                        <td>
                                                            <p style="margin-bottom:0px"><b>
                                                                <?php echo ($data['kode_pelanggan'] ? $data['kode_pelanggan'].' - ':'').$data['nama_customer'];?></b>
                                                            </p>
                                                            <p style="margin-bottom:0px"><?php echo $data['jenis_usaha'];?></p>
                                                            <p style="margin-bottom:0px"><i><?php echo $data['fullname'];?></i></p>
                                                        </td>
                                                        <td>
                                                            <p style="margin-bottom:0px"><b><?php echo $data['nama_area'];?></b></p>
                                                            <p style="margin-bottom:0px"><?php echo $alamat;?></p>
                                                            <p style="margin-bottom:0px"><?php echo 'Wilayah OA : '.$data['wilayah_angkut'];?></p>
                                                        </td>
                                                        <td>
                                                            <p style="margin-bottom:0px"><b><?php echo $data['nomor_poc'];?></b></p>
                                                            <p style="margin-bottom:0px"><?php echo number_format($data['volume']).' Liter '.$data['produk'];?></p>
                                                            <p style="margin-bottom:0px"><?php echo 'Tgl Kirim '.tgl_indo($data['tanggal_kirim']);?></p>
                                                            <p style="margin-bottom:0px"><?php echo 'Angkutan Kirim '.$arrMobil[$data['pr_mobil']];?></p>
                                                        </td>
                                                        <td><?php 
                                                            if($data['is_ds'] || !$data['is_approved']){ 
                                                                echo '<input type="hidden" name="vendor['.$idp.']" id="vendor'.$nom.'" value="'.$data['pr_vendor'].'" />';
                                                                echo '<input type="hidden" name="area['.$idp.']" id="area'.$nom.'" value="'.$data['id_area'].'" />';
                                                                echo '<input type="hidden" name="produk['.$idp.']" id="produk'.$nom.'" value="'.$data['produk_poc'].'" />';
                                                                echo ($data['nama_vendor'])?$data['nama_vendor']:'&nbsp;';
                                                            } else {
                                                                $vendornya = $data['pr_vendor'];
                                                                echo '<select name="vendor['.$idp.']" id="vendor'.$nom.'" class="form-control select2 vendor"><option></option>';
																$con->fill_select("id_master","nama_vendor","pro_master_vendor",$vendornya,"where is_active=1","id_master",false);
                                                                echo '</select>';
                                                                echo '<input type="hidden" name="awal['.$idp.']" id="awal'.$nom.'" value="'.$data['masa_awal'].'" />';
                                                                echo '<input type="hidden" name="akhir['.$idp.']" id="akhir'.$nom.'" value="'.$data['masa_akhir'].'" />';
                                                                echo '<input type="hidden" name="area['.$idp.']" id="area'.$nom.'" value="'.$data['id_area'].'" />';
                                                                echo '<input type="hidden" name="produk['.$idp.']" id="produk'.$nom.'" value="'.$data['produk_poc'].'" />';
                                                            }
                                                        ?></td>
                                                        <td><?php 
                                                            $txtInv = 'Detail Inventory';
															if($data['is_ds'] || !$data['is_approved']){ 
                                                                $tmn1 = ($data['nama_terminal'])?$data['nama_terminal']:'';
                                                                $tmn2 = ($data['tanki_terminal'])?' - '.$data['tanki_terminal']:'';
                                                                $tmn3 = ($data['lokasi_terminal'])?', '.$data['lokasi_terminal']:'';
                                                                echo '<input type="hidden" name="depot['.$idp.']" id="depot'.$nom.'" value="'.$data['pr_terminal'].'" />';
                                                                echo $tmn1.$tmn2.$tmn3;
                                                            } else {
                                                                $tmpDp8 	= "concat(nama_terminal,'#',tanki_terminal,'#',lokasi_terminal)";
                                                                $depotnya 	= $data['pr_terminal'];
																echo '<select name="depot['.$idp.']" id="depot'.$nom.'" class="form-control depot"><option></option>';
                                                                $con->fill_select("id_master",$tmpDp8,"pro_master_terminal",$depotnya,"where is_active=1","id_master",false);
                                                                echo '</select>';
                                                            }
                                                            echo '<p style="margin:5px 0 0;"><a style="cursor:pointer" class="detInven" data-idnya="'.$nom.'">'.$txtInv.'</a></p>';
                                                        ?></td>
                                                        <td class="text-left">
                                                        <?php 
                                                            if($data['is_ds'] || !$data['is_approved']) echo ($data['pr_harga_beli'])?'<input type="hidden" name="harga_beli['.$idp.']" id="harga_beli'.$nom.'" value="'.$data['pr_harga_beli'].'" /><p style="margin-bottom:0px;" class="text-right">'.number_format($data['pr_harga_beli']).'</p>':'<input type="hidden" name="harga_beli['.$idp.']" id="harga_beli'.$nom.'" value="'.$data['pr_harga_beli'].'" />';
                                                            else echo '<input type="text" name="harga_beli['.$idp.']" id="harga_beli'.$nom.'" class="form-control input-po hitung harga_beli" value="'.$data['pr_harga_beli'].'"/>';
                                                            echo '<p style="margin:5px 0px 0px;"><b>Masa Berlaku</b></p>';
                                                            echo '<p style="margin-bottom:0px;">'.date("d/m/Y",strtotime($data['masa_awal'])).' - '.date("d/m/Y",strtotime($data['masa_akhir'])).'</p>';
                                                        ?></td>
                                                        <td class="text-center"><?php echo $data['pr_top']; ?></td>
                                                        <td class="text-right"><?php echo ($data['is_loaded'] || !$data['is_approved'])?number_format($ar_not):'<input type="text" name="dt1['.$idp.']" id="dt1'.$idp.'_'.$nom.'" class="form-control hitung input-po ar-not" value="'.$ar_not.'" />';?></td>
                                                        <td class="text-right"><?php echo ($data['is_loaded'] || !$data['is_approved'])?number_format($ar_one):'<input type="text" name="dt2['.$idp.']" id="dt2'.$idp.'_'.$nom.'" class="form-control hitung input-po ar-one" value="'.$ar_one.'" />';?></td>
                                                        <td class="text-right"><?php echo ($data['is_loaded'] || !$data['is_approved'])?number_format($ar_two):'<input type="text" name="dt3['.$idp.']" id="dt3'.$idp.'_'.$nom.'" class="form-control hitung input-po ar-two" value="'.$ar_two.'" />';?></td>
                                                        <td class="text-right"><?php echo ($data['is_loaded'] || !$data['is_approved'])?number_format($ar_tot):'<input type="text" name="dt4['.$idp.']" id="dt4'.$idp.'_'.$nom.'" class="form-control hitung input-po" value="'.$ar_tot.'" readonly />';?></td>
                                                        <td class="text-right"><?php echo ($data['is_loaded'] || !$data['is_approved'])?number_format($ar_krl):'<input type="text" name="dt5['.$idp.']" id="dt5'.$idp.'_'.$nom.'" class="form-control hitung input-po" value="'.$ar_krl.'" />';?></td>
                                                        <td><?php 
                                                            if($data['is_loaded'] || !$data['is_approved']){
                                                                echo '<div class="text-center">'.($data['is_approved']?'Approved':'Reschedule').'</div>';
                                                            } else{
                                                                echo '<select name="dt6['.$idp.']" id="dt6'.$idp.'_'.$nom.'" class="form-control input-po">
                                                                        <option value="0" '.(!$data['is_approved']?'selected':'').'>Reschedule</option>
                                                                        <option value="1" '.($data['is_approved']?'selected':'').'>Approved</option>
                                                                      </select>';
                                                            }
                                                        ?></td>
                                                    </tr>
                                                <?php } } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <?php if(count($res) > 0){ ?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="idr" value="<?php echo $idr; ?>" />
                                            <a class="btn btn-default jarak-kanan" href="<?php echo BASE_URL_CLIENT."/perbaikan-data.php";?>">Kembali</a> 
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt">Ubah Data</button>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>
            <div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Peringatan</h4>
                        </div>
                        <div class="modal-body">
                        	<div id="preview_alert" class="text-center"></div>
						</div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="inven_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Informasi</h4>
                        </div>
                        <div class="modal-body"></div>
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
			<?php $con->close(); ?>
			</section>
            <?php include_once($public_base_directory."/web/layout/footer.php"); ?>
		</aside>
	</div>

<style type="text/css">
	h3.form-title {
		 font-size: 18px;
		 margin: 0 0 10px;
		 font-weight: 700;
	}
	#table-long, #table-grid3, #table-detail {margin-bottom: 15px;}
	#table-grid3 td, #table-grid3 th {font-size: 11px; font-family:arial}
	#table-detail td { padding-bottom:3px; font-size: 12px;}
	.input-po {
		padding: 5px;
		height: auto;
		font-size: 11px;
		font-family:arial;
	}
	.select2-search--dropdown .select2-search__field{
		font-family: arial;
		font-size: 11px;
		padding: 4px 3px;
	}
	.select2-results__option{
		font-family: arial;
		font-size: 11px;
	}
</style>
<script>
	$(document).ready(function(){
		$(".hitung").number(true, 0, ".", ",");
		$("select.depot").select2({
			placeholder	: "Pilih salah satu",
			allowClear	: true,
			templateResult : function(repo){ 
				if(repo.loading) return repo.text;
				var text1 = repo.text.split("#");
				var $returnString = $('<span>'+text1[0]+(text1[1]?' - '+text1[1]:'')+(text1[2]?'<br />'+text1[2]:'')+'</span>');
				return $returnString;
			},
			templateSelection : function(repo){ 
				var text1 = repo.text.split("#");
				var $returnString = $('<span>'+text1[0]+(text1[1]?' - '+text1[1]:'')+(text1[2]?', '+text1[2]:'')+'</span>');
				return $returnString;
			},
		});

		$("#gform").on("click", "a.detInven", function(){
			var cRow = $(this).data('idnya');
			$.ajax({
				type	: 'POST',
				url		: "./__get_info_inventory.php",
				data	: {q1:$("#vendor"+cRow).val(), q2:$("#area"+cRow).val(), q3:$("#produk"+cRow).val(), q4:$("#depot"+cRow).val()},
				cache	: false,
				success : function(data){
					$("#inven_modal").find(".modal-body").html(data);
					$("#inven_modal").modal();
				}
			});
		});

		$("#gform").on("change", "select.vendor, select.depot", function(){
			var idnya = $(this).attr("id").replace(/(vendor|depot)/,"");
			getHargaBeli(idnya);
		});

		function getHargaBeli(newId){
			var vendor 	= $("#vendor"+newId).val();
			var awal 	= $("#awal"+newId).val();
			var akhir 	= $("#akhir"+newId).val();
			var area 	= $("#area"+newId).val();
			var produk 	= $("#produk"+newId).val();
			var depot 	= $("#depot"+newId).val();
			if(vendor != "" && awal != "" && akhir != "" && area != "" && produk != "" && depot != ""){
				$('#loading_modal').modal({backdrop:"static"});
				$.ajax({
					type	: 'POST',
					url		: "./__get_harga_tebus.php",
					data	: {q1:awal, q2:akhir, q3:produk, q4:area, q5:vendor, q6:depot},
					cache	: false,
					success : function(data){
						$("#harga_beli"+newId).val(data);
					}
				});
				$("#loading_modal").modal("hide");
			} else{
				$("#harga_beli"+newId).val("");
			}
		}

		$("form#gform").on("click", "#btnSbmt", function(){
			var oke = true;
			$(".vendor").each(function(){
				var newId 	= $(this).attr("id").replace(/(vendor|depot)/,"");
				var vendor 	= $("#vendor"+newId).val();
				var depot 	= $("#depot"+newId).val();
				var harga 	= $("#harga_beli"+newId).val();
				if(vendor == "" || depot == "" || harga == ""){
					$("#preview_modal").find("#preview_alert").html("Suplier, depot dan harga beli harus diisi");
					$("#preview_modal").modal();
					oke = false;
					return false;
				}
			});
			if(oke){
				if(confirm("Apakah anda yakin?")){
					$("#loading_modal").modal({backdrop:"static"});
					$("form#gform").submit();
				} else return false;
			} else return false;
		});

		$("#gform").on("keyup", ".ar-not, .ar-one, .ar-two", function(){
			var jumlah,
			idn = $(this).attr("id").substr(3),
			dt1 = $("#dt1"+idn).val().replace(/,/g, "") * 1,
			dt2 = $("#dt2"+idn).val().replace(/,/g, "") * 1,
			dt3 = $("#dt3"+idn).val().replace(/,/g, "") * 1;
			jumlah = dt1 + dt2 + dt3;
			$("#dt4"+idn).val(jumlah);
		});

		var x,y,top,left,down;
		$("#table-long").mousedown(function(e){
			if(e.target.nodeName != "INPUT" && e.target.nodeName != "SELECT"){
				down = true;
				x = e.pageX;
				y = e.pageY;
				top = $(this).scrollTop();
				left = $(this).scrollLeft();
			}
		});			
		$("body").mousemove(function(e){
			if(down){
				var newX = e.pageX;
				var newY = e.pageY;
				$("#table-long").scrollTop(top-newY+y);    
				$("#table-long").scrollLeft(left-newX+x);    
			}
		});
		$("body").mouseup(function(e){down=false;});
	});		
</script>
</body>
</html>      
