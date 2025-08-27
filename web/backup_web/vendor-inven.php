<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
    $draw   = isset($_POST["element"])?htmlspecialchars($_POST["element"], ENT_QUOTES):0;
    $start  = isset($_POST["start"])?htmlspecialchars($_POST["start"], ENT_QUOTES):0;
    $length = isset($_POST['length'])?htmlspecialchars($_POST["length"], ENT_QUOTES):10;
	$enk  	= decode($_SERVER['REQUEST_URI']);
	$con 	= new Connection();
	$flash	= new FlashAlerts;
	$arrBln = array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
	$sesRole = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_role']);
	$sesGrup = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_group']);
	$sesCbng = paramDecrypt($_SESSION['sinori'.SESSIONID]['id_wilayah']);

	$q1 = (isset($_POST["q1"]))?htmlspecialchars($_POST["q1"], ENT_QUOTES):(isset($enk['q1'])?htmlspecialchars($enk['q1'], ENT_QUOTES):null);
	$q2 = (isset($_POST["q2"]))?htmlspecialchars($_POST["q2"], ENT_QUOTES):(isset($enk['q2'])?htmlspecialchars($enk['q2'], ENT_QUOTES):null);
	$q3 = (isset($_POST["q3"]))?htmlspecialchars($_POST["q3"], ENT_QUOTES):(isset($enk['q3'])?htmlspecialchars($enk['q3'], ENT_QUOTES):null);
	$q4 = (isset($_POST["q4"]))?htmlspecialchars($_POST["q4"], ENT_QUOTES):(isset($enk['q4'])?htmlspecialchars($enk['q4'], ENT_QUOTES):null);
	$q5 = (isset($_POST["q5"]))?htmlspecialchars($_POST["q5"], ENT_QUOTES):(isset($enk['q5'])?htmlspecialchars($enk['q5'], ENT_QUOTES):null);
	$q6 = (isset($_POST["q6"]))?htmlspecialchars($_POST["q6"], ENT_QUOTES):(isset($enk['q6'])?htmlspecialchars($enk['q6'], ENT_QUOTES):null);
	$tke = paramEncrypt('q1='.$q1.'&q2='.$q2.'&q3='.$q3.'&q4='.$q4.'&q5='.$q5.'&q6='.$q6);
	$lke = BASE_URL_CLIENT.'/vendor-inven-exp.php?'.$tke;

	$file  	= BASE_SELF;
	$limit 	= 31;
	$p		= new paging;

	$sql = "
        select a.*, b.nama_terminal, c.nama_vendor
        from pro_inventory_vendor a 
        join pro_master_terminal b on b.id_master = a.id_terminal
        join pro_master_vendor c on c.id_master = a.id_vendor
        where 1=1
    ";
	if($q1) $sql .= " and a.id_vendor = '".$q1."'";
	if($q2) $sql .= " and a.id_produk = '".$q2."'";
	if($q3) $sql .= " and a.id_area = '".$q3."'";
	if($q4) $sql .= " and a.id_terminal = '".$q4."'";
	if($q5 && $q6) $sql .= " and month(a.tanggal_inven) = '".$q5."' and year(a.tanggal_inven) = '".$q6."'";
    if(!$q5 && $q6) $sql .= " and year(a.tanggal_inven) = '".$q6."'";
	
	$tot_record = $con->num_rows($sql);
	$tot_page 	= ceil($tot_record/$limit);
    // $page       = 0;
	// $page		= ($enk[$page] > $tot_page)?$enk[$page]-1:$enk[$page]; 
    $page       = ($start > $tot_page)?$start-1:$start;
	$position 	= $p->findPosition($limit, $tot_record, $page);
	$param_ref	= "&q1=".$q1."&q2=".$q2."&q3=".$q3."&q4=".$q4."&q5=".$q5."&q6=".$q6;
	// if($q5 && $q6) $sql .= " order by a.tanggal_inven desc";
    if($q6) $sql .= " order by a.tanggal_inven desc";
	else $sql .= " order by a.tanggal_inven desc"; // limit ".$position.", ".$limit;
	$res = $con->getResult($sql);
    if (!$q5 && !$q6) {
        $resTemp = [];
        $j = 0;
        // for ($i=(count($res)-1); $i >= 0; $i--) {
        for ($i=0; $i < count($res); $i++) {
            $resTemp[$j] = $res[$i];
            $j ++;
        }
        $res = $resTemp;
    }
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
        		<h1>Inventory Vendor</h1>
        	</section>
			<section class="content">

            <?php $flash->display(); ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <div class="row">
                            	<div class="col-sm-6"><div style="font-size:18px; padding:4px 0px;"><b>PENCARIAN</b></div></div>
                            	<div class="col-sm-6">
                                    <?php if($sesRole == 5){ ?>
                                    <div class="text-right">
                                        <a href="<?php echo BASE_URL_CLIENT.'/vendor-inven-add.php'; ?>" class="btn btn-primary"><i class="fa fa-plus jarak-kanan"></i>Add Data</a>
									</div>
                                    <?php } ?>
                                </div>
							</div>
						</div>
                        <div class="box-body">
						<form name="sFrm" id="sFrm" method="post" class="form-validasi" action="<?php echo BASE_URL_CLIENT."/vendor-inven.php";?>">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <select id="q1" name="q1" class="form-control validate[required]">
                                        <option></option>
                                        <?php $con->fill_select("id_master","nama_vendor","pro_master_vendor",$q1,"where is_active=1","id_master",false); ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <select id="q2" name="q2" class="form-control validate[required]">
                                    <option></option>
                                    <?php $con->fill_select("id_master","concat(jenis_produk,' - ',merk_dagang)","pro_master_produk",$q2,"where is_active =1","id_master",false); ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <select id="q3" name="q3" class="form-control validate[required]">
                                        <option></option>
                                        <?php $con->fill_select("id_master","nama_area","pro_master_area",$q3,"where is_active=1","nama_area",false); ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <select id="q4" name="q4" class="form-control validate[required]">
                                        <option></option>
                                        <?php $con->fill_select("id_master","concat(nama_terminal,' ',tanki_terminal,', ',lokasi_terminal)","pro_master_terminal",$q4,"where is_active=1","id_master",false); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <select id="q5" name="q5" class="form-control">
                                        <option></option>
										<?php
                                            foreach($arrBln as $i3=>$t3){
                                                $selected = ($q5 == $i3 ?'selected' :'');
                                                echo '<option value="'.$i3.'" '.$selected.'>'.$t3.'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-sm-top">
                                    <select id="q6" name="q6" class="form-control">
                                        <option></option>
										<?php
                                            for($i=date('Y'); $i>2014; $i--){
                                                $selected = ($q6 == $i ?'selected' :'');
                                                echo '<option '.$selected.'>'.$i.'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-6 col-sm-top">
                                    <button type="submit" class="btn btn-info btn-sm" name="btnSearch" id="btnSearch"><i class="fa fa-search jarak-kanan"></i>Search</button>
									<?php if($q1 && $q2 && $q3 && $q4 && $q6){ ?>
                                    <a class="btn btn-success btn-sm jarak-kiri" target="_blank" href="<?php echo $lke;?>" id="linkexp">
                                    <i class="fa fa-random jarak-kanan"></i>Export</a>
                                    <?php } ?>
                                </div>
                            </div>
						</form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-info">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered col-sm-top">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="15%">TERMINAL/DEPOT</th>
                                        <th class="text-center" width="15%">VENDOR/SUPLIER</th>
                                        <th class="text-center" width="15%">TANGGAL</th>
                                        <th class="text-center" width="10%">BEGINNING</th>
                                        <th class="text-center" width="10%">INPUT</th>
                                        <th class="text-center" width="10%">OUTPUT</th>
                                        <th class="text-center" width="10%">ADJ INV</th>
                                        <th class="text-center" width="10%">ENDING</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if(!is_array($resq) or count($resq) == 0){
                                            echo '<tr><td colspan="8" class="text-center">Data tidak ditemukan...</td></tr>';
                                        } else {
                                            $noms = $position;
                                            $tot1 = 0;
                                            $tot2 = 0;
                                            $tot3 = 0;
                                            $tot4 = 0;
                                            $tot5 = 0;
                                            $totA = 0;
                                            $totTemp = 0;
                                            // foreach($resq as $i => $data){
                                            for ($i = count($resq)-1; $i >= 0; $i--) {
                                                $data = $resq[$i];
                                                $awal_inven = isset($data['awal_inven']) ? $data['awal_inven'] : 0;
                                                $out_inven = isset($data['out_inven']) ? $data['out_inven'] : 0;
                                                $in_inven = isset($data['in_inven']) ? $data['in_inven'] : 0;
                                                $adj_inven = isset($data['adj_inven']) ? $data['adj_inven'] : 0;
                                                $id_master = isset($data['id_master']) ? $data['id_master'] : null;
                                                $tanggal_inven = isset($data['tanggal_inven']) ? $data['tanggal_inven'] : null;

                                                $noms++;
                                                // $awal_inven = $totA;
                                                $awal_inven = $i==0?$awal_inven:$totA;
                                                $out_inven = str_replace('-', '', $out_inven);
                                                $totA = ($awal_inven + $in_inven + $adj_inven) - $out_inven;
                                                if ($i == 1) $totTemp = $totA;
                                                if ($i == 0) $awal_inven = $totTemp;
                                                /* Update new belum fix
                                                // if ($i == 0) {
                                                //     $awal_inven = $totTemp;
                                                //     $totA = ($awal_inven + $in_inven + $adj_inven) - $out_inven;
                                                // }
                                                */
                                                $tot1 	= $tot1 + $awal_inven;
                                                $tot2 	= $tot2 + $in_inven;
                                                $tot3 	= $tot3 + $out_inven;
                                                $tot4 	= $tot4 + $adj_inven;
                                                // $tot5 	= $tot5 + $totA;
                                                $tot5   = $tot2 - $tot3 - $tot4;
                                                $link2	= BASE_URL_CLIENT."/vendor-inven-add.php?".( $id_master ? paramEncrypt("idr=".$id_master) : '' );
												$class	= ($sesRole == 5?'clickable-row':'non-clickable-row');
                                    ?>
									<tr class="<?php echo $class;?>" data-href="<?php echo $link2;?>">
                                        <td class="text-left"><?php echo $data['nama_terminal'];?></td>
                                        <td class="text-left"><?php echo $data['nama_vendor'];?></td>
                                        <td class="text-center"><?php echo $tanggal_inven ? date("d/m/Y", strtotime($tanggal_inven)) : '-';?></td>
                                        <td class="text-right"><?php echo number_format($awal_inven);?></td>
                                        <td class="text-right"><?php echo number_format($in_inven);?></td>
                                        <td class="text-right"><?php echo number_format($out_inven);?></td>
                                        <td class="text-right"><?php echo number_format($adj_inven);?></td>
                                        <td class="text-right"><?php echo number_format($totA);?></td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-center bg-gray" colspan="3"><b>TOTAL</b></td>
                                        <td class="text-right bg-gray"><b><?php echo '-'; // number_format($tot1);?></b></td>
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
	$("select#q1").select2({placeholder:"Vendor/Suplier", allowClear:true });
	$("select#q2").select2({placeholder:"Produk", allowClear:true });
	$("select#q3").select2({placeholder:"Area", allowClear:true });
	$("select#q4").select2({placeholder:"Terminal/Depot", allowClear:true });
	$("select#q5").select2({placeholder:"Bulan", allowClear:true });
	$("select#q6").select2({placeholder:"Tahun", allowClear:true });
});
</script>
</body>
</html>      
