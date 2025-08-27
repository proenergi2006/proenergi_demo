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
	
	$enk['q1'] = (!$_POST["q1"])?$enk["q1"]:$_POST["q1"];
	$q1	= (!$enk['q1'])?date("d/m/Y"):htmlspecialchars($enk['q1'], ENT_QUOTES);
	$enk['q2'] = (!$_POST["q2"])?$enk["q2"]:$_POST["q2"];
	$q2	= (!$enk['q2'])?"":htmlspecialchars($enk['q2'], ENT_QUOTES);
	$seswil = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber", "jqueryUI"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>PO Customer Plan</h1>
        	</section>
			<section class="content">

				<?php $flash->display(); ?>
                <form name="searchForm" id="searchForm" role="form" class="form-horizontal" method="post" action="<?php echo BASE_URL_CLIENT.'/po-customer-admin.php'; ?>">
                <div class="form-group row">
                    <div class="col-sm-4 col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon">Tanggal Kirim</span>
                            <input type="text" name="q1" id="q1" class="form-control datepicker input-sm" value="<?php echo $q1;?>" />
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-3 col-sm-top">
                        <div class="input-group">
                            <span class="input-group-addon">Sampai dengan</span>
                            <input type="text" name="q2" id="q2" class="form-control datepicker input-sm" value="<?php echo $q2;?>" />
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-6 col-sm-top">
						<button type="submit" class="btn btn-sm btn-info" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i> Cari</button>
                    </div>
                </div>
                </form>
                <div class="row">                
                    <div class="col-sm-12">
                        <div class="box box-primary">
                            <div class="box-body">
                                <form action="<?php echo ACTION_CLIENT.'/po-customer-admin.php'; ?>" id="gform" name="gform" method="post" role="form">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="table-grid2">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="4%">No</th>
                                                <th class="text-center" width="15%">Customer</th>
                                                <th class="text-center" width="13%">Alamat Kirim</th>
                                                <th class="text-center" width="7%">TOP</th>
                                                <th class="text-center" width="8%">Kode Pelanggan</th>
                                                <th class="text-center" width="1%">Paste</th>
                                                <th class="text-center" width="10%">Actual TOP</th>
                                                <th class="text-center" width="10%">AR (Not Yet)</th>
                                                <th class="text-center" width="10%">AR (1 - 30)</th>
                                                <th class="text-center" width="10%">AR (> 30)</th>
                                                <th class="text-center" width="12%">Credit Limit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            $sql = "select a.*, e.id_customer, b.alamat_survey, c.nama_prov, d.nama_kab, f.credit_limit, 
													f.nama_customer, f.kode_pelanggan, f.jenis_payment, f.top_payment, g.fullname, h.nama_cabang, j.nama_area 
                                                    from pro_po_customer_plan a 
													join pro_customer_lcr b on a.id_lcr = b.id_lcr
                                                    join pro_master_provinsi c on b.prov_survey = c.id_prov 
													join pro_master_kabupaten d on b.kab_survey = d.id_kab
                                                    join pro_po_customer e on a.id_poc = e.id_poc 
													join pro_customer f on e.id_customer = f.id_customer 
													join acl_user g on f.id_marketing = g.id_user
													join pro_master_cabang h on f.id_wilayah = h.id_master  
													join pro_penawaran i on e.id_penawaran = i.id_penawaran  
													join pro_master_area j on i.id_area = j.id_master 
													where f.id_wilayah = '".$seswil."' and a.status_plan = 0";
											if($q1 && !$q2)
												$sql .= " and a.tanggal_kirim = '".tgl_db($q1)."'";
											else if($q1 && $q2)
												$sql .= " and a.tanggal_kirim between '".tgl_db($q1)."' and '".tgl_db($q2)."'";
											$sql .= " order by a.tanggal_kirim, a.id_plan";
                                            $res = $con->getResult($sql);

                                            if(count($res) == 0){
                                                echo '<tr><td colspan="11" style="text-align:center">Data tidak ditemukan </td></tr>';
                                            } else{
                                                $nom = 0;
                                                $arrId = array();
												foreach($res as $data){
                                                    $nom++;
                                                    $idp 	= $data['id_plan'];
													$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
													$alamat	= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
													$dt3 	= ($data['ar_notyet'])?number_format($data['ar_notyet']):'';
													$dt4 	= ($data['ar_satu'])?number_format($data['ar_satu']):'';
													$dt5 	= ($data['ar_dua'])?number_format($data['ar_dua']):'';
													$dt6 	= ($data['kredit_limit'])?number_format($data['kredit_limit']):number_format($data['credit_limit']);
													$dt7 	= ($data['actual_top_plan'])?$data['actual_top_plan']:'';
													$tmp3 	= ($data['pelanggan_plan'])?$data['pelanggan_plan']:$data['kode_pelanggan'];
													$tmp4 	= ($tmp3)?'readonly':'';

													$jns_payment = $data['jenis_payment'];
													$top_payment = $data['top_payment'];
													$arr_payment = array("CREDIT"=>"NET ".$top_payment, "COD"=>"COD","CDB"=>"CBD");
													$termPayment = $arr_payment[$jns_payment];
													$topCustomer = ($data['top_plan'])?$data['top_plan']:$termPayment;
                                        ?>
                                            <tr>
                                                <td class="text-center"><?php echo $nom; ?></td>
                                                <td>
                                                    <input type="hidden" name="<?php echo "cek[".$idp."]"; ?>" id="<?php echo "cek".$nom;?>" value="1" />
                                                    <input type="hidden" name="<?php echo "idr[".$idp."]"; ?>" value="<?php echo $data['id_customer'];?>" />
													<p style="margin-bottom:0px"><b><?php echo $data['nama_customer'];?></b></p>
													<p style="margin-bottom:0px"><?php echo tgl_indo($data['tanggal_kirim']);?></p>
													<p style="margin-bottom:0px"><?php echo number_format($data['volume_kirim']).' Liter';?></p>
													<p style="margin-bottom:0px"><i><?php echo $data['fullname'];?></i></p>
                                                </td>
                                                <td>
													<p style="margin-bottom:0px;"><b><?php echo $data['nama_area'];?></b></p>
													<p style="margin-bottom:0px;"><?php echo $alamat;?></p>
                                                </td>
                                                <td><input type="text" name="<?php echo "dt1[".$idp."]";?>" id="<?php echo "dt1".$nom;?>" class="form-control input-po" value="<?php echo $topCustomer;?>" readonly /></td>
                                                <td><input type="text" name="<?php echo "dt2[".$idp."]";?>" id="<?php echo "dt2".$nom;?>" class="form-control input-po" value="<?php echo $tmp3;?>" <?php echo $tmp4;?> /></td>
                                                <td><input type="text" name="cps[]" id="<?php echo "cps".$nom;?>" class="form-control input-po cps" /></td>
                                                <td><input type="text" name="<?php echo "dt7[".$idp."]";?>" id="<?php echo "dt7".$nom;?>" class="form-control input-po" value="<?php echo $dt7;?>" /></td>
                                                <td><input type="text" name="<?php echo "dt3[".$idp."]";?>" id="<?php echo "dt3".$nom;?>" class="form-control input-po hitung copas" value="<?php echo $dt3;?>" /></td>
                                                <td><input type="text" name="<?php echo "dt4[".$idp."]";?>" id="<?php echo "dt4".$nom;?>" class="form-control input-po hitung copas" value="<?php echo $dt4;?>" /></td>
                                                <td><input type="text" name="<?php echo "dt5[".$idp."]";?>" id="<?php echo "dt5".$nom;?>" class="form-control input-po hitung copas" value="<?php echo $dt5;?>" /></td>
                                                <td><input type="text" name="<?php echo "dt6[".$idp."]";?>" id="<?php echo "dt6".$nom;?>" class="form-control input-po hitung copas" value="<?php echo $dt6;?>" /></td>
                                            </tr>
                                        <?php } } ?>
                                        </tbody>
                                    </table>
                                </div>
								<?php if(count($res) > 0){ ?>
                                <hr style="margin:0 0 10px" />
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="pad bg-gray">
                                            <input type="hidden" name="idq1" value="<?php echo $q1; ?>" />
                                            <input type="hidden" name="idq2" value="<?php echo $q2; ?>" />
                                            <button type="submit" class="btn btn-primary" name="btnSbmt" id="btnSbmt"><i class="fa fa-floppy-o jarak-kanan"></i>Save</button>
										</div>
                                    </div>
                                </div>
                                <?php } ?>
                                </form>
                                
                            </div>
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
		#table-grid2 {margin-bottom: 15px;}
		#table-grid2 th{font-size: 11px; font-family:arial;}
		#table-grid2 td{font-size: 11px; font-family:arial;}
		.input-po {
			padding: 5px;
			height: auto;
			font-size: 11px;
			font-family:arial;
		}
    </style>
	<script>
		$(document).ready(function(){
			$(".hitung").number(true, 0, ".", ",");
			$(".cps").on("input", function(e){
				var data = $(this).val();
				var rows = data.split("\n");
				var elem = $(this).parent().next().find("input:text").first();
				for(var y in rows){
					if(rows[y] != ""){
						var cells = rows[y].split("\t");
						for(var x in cells){
							elem.val(cells[x]);
							elem = elem.parent().next().find("input:text").first();
						}
					}
				}
				$(this).val("");
			});
		});		
	</script>
</body>
</html>      
