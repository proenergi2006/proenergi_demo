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
	$arrBln = array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
	$q1 = (isset($_POST["q1"]))?htmlspecialchars($_POST["q1"], ENT_QUOTES):(isset($enk['q1'])?htmlspecialchars($enk['q1'], ENT_QUOTES):null);
	$q2 = (isset($_POST["q2"]))?htmlspecialchars($_POST["q2"], ENT_QUOTES):(isset($enk['q2'])?htmlspecialchars($enk['q2'], ENT_QUOTES):null);
	$q3 = (isset($_POST["q3"]))?htmlspecialchars($_POST["q3"], ENT_QUOTES):(isset($enk['q3'])?htmlspecialchars($enk['q3'], ENT_QUOTES):null);

	$sesRole = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$sesGrup = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
	$sesCbng = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);

	$cek = "
        select *
        from pro_master_produk
        where is_active = 1 
        order by id_master
    ";
	$row = $con->getResult($cek);
	$prd = (isset($enk['prd']))?htmlspecialchars($enk["prd"], ENT_QUOTES):$row[0]['id_master'];
	$tke = paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$prd);
	$lke = BASE_URL_CLIENT.'/vendor-inven-terminal-exp.php?'.$tke;

	$sql = "
        select b.id_master, b.nama_terminal, c.nama_area, d.nama_vendor, sum(a.awal_inven) as awal_inven, sum(a.in_inven) as in_inven, sum(a.out_inven) as out_inven, sum(a.adj_inven) as adj_inven
		from pro_inventory_vendor a 
        join pro_master_terminal b on b.id_master = a.id_terminal
        join pro_master_area c on c.id_master = a.id_area
        join pro_master_vendor d on d.id_master = a.id_vendor
        where a.id_produk = '".$prd."'
    ";
    if ($q1) $sql .= " and a.id_terminal = '".$q1."' ";
    if ($q2) $sql .= " and month(a.tanggal_inven) = '".$q2."' ";
    if ($q3) $sql .= " and year(a.tanggal_inven) = '".$q3."' ";
    $sql .= " group by b.id_master, c.nama_area, d.nama_vendor ";
    // $sql .= " order by b.id_master ";
    // echo $sql; die();
	$res = $con->getResult($sql);
    $resq = $res;
?>
<!DOCTYPE html>
<html lang="en">
<?php load_headHtml(BASE_PATH_CSS, BASE_PATH_JS, array("js"=>array("formatNumber", "jqueryUI", "myGrid"), "css"=>array("jqueryUI"))); ?>

<body class="skin-blue fixed">
	<?php include_once($public_base_directory."/web/layout/header.php"); ?>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<?php include_once($public_base_directory."/web/layout/sidebar.php"); ?>
        <aside class="right-side">
        	<section class="content-header">
        		<h1>Inventory Vendor By Depot</h1>
        	</section>
			<section class="content">

            <?php $flash->display(); ?>
            <div class="box box-info">
                <div class="box-header with-border">
                    <p style="font-size:18px; margin-bottom:0px;"><b>PENCARIAN</b></p>
                </div>
                <div class="box-body">
                <form name="sFrm" id="sFrm" method="post" class="form-validasi" action="<?php echo BASE_URL_CLIENT."/vendor-inven-terminal.php";?>">
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <select id="q1" name="q1" class="form-control validate[required]">
                                <option></option>
                                <?php $con->fill_select("id_master","concat(nama_terminal,' ',tanki_terminal,', ',lokasi_terminal)","pro_master_terminal",$q1,"where is_active=1","id_master",false); ?>
                            </select>
                        </div>
                        <div class="col-sm-2 col-sm-top" hidden>
                            <select id="q2" name="q2" class="form-control validate[required]">
                            <option></option>
                                <?php
                                    foreach($arrBln as $i3=>$t3){
                                        $selected = ($q2 == $i3 ?'selected' :'');
                                        echo '<option value="'.$i3.'" '.$selected.'>'.$t3.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-2 col-sm-top" hidden>
                            <select id="q3" name="q3" class="form-control validate[required]">
                                <option></option>
                                <?php
                                    for($i=date('Y'); $i>2014; $i--){
                                        $selected = ($q3 == $i ?'selected' :'');
                                        echo '<option '.$selected.'>'.$i.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-4 col-sm-top">
                            <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>

			<?php if($sesRole == 5){ ?>
            <div class="text-left" style="margin-bottom:20px;">
                <a href="<?php echo BASE_URL_CLIENT.'/vendor-inven-terminal-add.php'; ?>" class="btn btn-primary"><i class="fa fa-plus jarak-kanan"></i>Add Data</a>
            </div>
            <?php } ?>

            <ul class="nav nav-tabs" role="tablist">
                <?php 
					foreach($row as $nil){ 
						$lknya = BASE_URL_CLIENT."/vendor-inven-terminal.php?".paramEncrypt("prd=".$nil['id_master']."&q1=".$q1."&q2=".$q2."&q3=".$q3);
						echo '<li role="presentation" class="'.($nil['id_master'] == $prd?'active':'').'">
								<a href="'.$lknya.'" role="tab" data-toggle="tablink">'.$nil['jenis_produk'].' - '.$nil['merk_dagang'].'</a>
							  </li>';
					}
				?>
            </ul>

            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                 			<?php 
								// if($q1 && $q2 && $q3 && $prd) echo '<a class="btn btn-success btn-sm" target="_blank" href="'.$lke.'" style="width:80px;">Export</a>';
								// else echo '<a class="btn btn-success btn-sm disabled" style="width:80px;">Export</a>'; 
							?>
                            <a class="btn btn-success btn-sm" target="_blank" href="<?=$lke?>" style="width:80px;">Export</a>
						</div>
                        <div class="box-body table-responsive">
                            <table class="table table-bordered col-sm-top">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="15%">TERMINAL/DEPOT</th>
                                        <th class="text-center" width="15%">VENDOR/SUPLIER</th>
                                        <th class="text-center" width="15%">NAMA AREA</th>
                                        <th class="text-center" width="10%">BEGINNING</th>
                                        <th class="text-center" width="10%">INPUT</th>
                                        <th class="text-center" width="10%">OUTPUT</th>
                                        <th class="text-center" width="10%">ADJ INV</th>
                                        <th class="text-center" width="10%">ENDING</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        if(count($resq) == 0){
                                            echo '<tr><td colspan="6" class="text-center">Data tidak ditemukan...</td></tr>';
                                        } else{
                                            // $noms = $position;
                                            $noms = 0;
                                            $tot1 = 0;
                                            $tot2 = 0;
                                            $tot3 = 0;
                                            $tot4 = 0;
                                            $tot5 = 0;
                                            foreach($resq as $data){
                                                $awal_inven = isset($data['awal_inven']) ? $data['awal_inven'] : 0;
                                                $in_inven = isset($data['in_inven']) ? $data['in_inven'] : 0;
                                                $adj_inven = isset($data['adj_inven']) ? $data['adj_inven'] : 0;
                                                $out_inven = isset($data['out_inven']) ? $data['out_inven'] : 0;
                                                $tanggal_inven = isset($data['tanggal_inven']) ? $data['tanggal_inven'] : null;

                                                $noms++;
                                                $totA 	= ($awal_inven + $in_inven + $adj_inven) - $out_inven;
                                                $tot1 	= $tot1 + $awal_inven;
                                                $tot2 	= $tot2 + $in_inven;
                                                $tot3 	= $tot3 + $out_inven;
                                                $tot4 	= $tot4 + $adj_inven;
                                                $tot5 	= $tot5 + $totA;
                                    ?>
                                    <tr>
                                        <td class="text-left"><?php echo $data['nama_terminal'];?></td>
                                        <td class="text-left"><?php echo $data['nama_vendor'];?></td>
                                        <td class="text-left"><?php echo $data['nama_area'];?></td>
                                        <td class="text-right"><?php echo number_format($awal_inven);?></td>
                                        <td class="text-right"><?php echo number_format($in_inven);?></td>
                                        <td class="text-right"><?php echo number_format($out_inven);?></td>
                                        <td class="text-right"><?php echo number_format($adj_inven);?></td>
                                        <td class="text-right"><?php echo number_format($totA);?></td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-center bg-gray" colspan="3"><b>TOTAL</b></td>
                                        <td class="text-right bg-gray"><b><?php echo number_format($tot1);?></b></td>
                                        <td class="text-right bg-gray"><b><?php echo number_format($tot2);?></b></td>
                                        <td class="text-right bg-gray"><b><?php echo number_format($tot3);?></b></td>
                                        <td class="text-right bg-gray"><b><?php echo number_format($tot4);?></b></td>
                                        <td class="text-right bg-gray"><b><?php echo number_format($tot5);?></b></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
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
</style>
<script>
$(document).ready(function(){
	$("select#q1").select2({placeholder:"Terminal/Depot", allowClear:true });
	$("select#q2").select2({placeholder:"Bulan", allowClear:true });
	$("select#q3").select2({placeholder:"Tahun", allowClear:true });

	$("[data-toggle='tablink']").click(function(){
		var $this = $(this);
		var idnya = $this.attr('href');
		if($this.parent().hasClass("active") == false){
			window.location.href = idnya;
		}
		return false;	
	});
});
</script>
</body>
</html>      
