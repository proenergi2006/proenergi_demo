<?php
	session_start();
	$privat_base_directory = explode('/', dirname($_SERVER['PHP_SELF']))[1];
	$public_base_directory = $_SERVER['DOCUMENT_ROOT']."/".$privat_base_directory;
	require_once ($public_base_directory."/libraries/helper/load.php");
	load_helper("autoload");

	$auth	= new MyOtentikasi();
	$conSub = new Connection();
	$q1 	= htmlspecialchars($_POST["q1"], ENT_QUOTES);
?>
<div class="table-responsive">
    <table class="table table-bordered" id="table-grid3">
        <thead>
            <tr>
				<th class="text-center" width="5%">No</th>
				<th class="text-center" width="20%">Customer</th>
				<th class="text-center" width="20%">Alamat Kirim</th>
				<th class="text-center" width="8%">Produk</th>
				<th class="text-center" width="8%">Volume</th>
				<th class="text-center" width="8%">Tanggal Kirim</th>
				<th class="text-center" width="26%">Catatan</th>
				<th class="text-center" width="5%">Pilih</th>
            </tr>
        </thead>
        <tbody>
        <?php 
			$sql = "select a.*, c.tanggal_kirim, c.status_jadwal, e.alamat_survey, f.nama_prov, g.nama_kab, h.nama_customer, h.kode_pelanggan, i.fullname, l.nama_area
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
					join pro_master_produk m on d.produk_poc = m.id_master 
					where a.id_pr = '".$q1."' and a.is_approved = 1 and a.pr_mobil = 2 
					and a.id_prd not in (select b.id_prd from pro_po_ds_kapal a join pro_pr_detail b on a.id_prd = b.id_prd where a.id_pr = '".$q1."') 
					order by c.tanggal_kirim, k.id_cabang, k.id_area, a.id_plan, a.id_prd";
            $res = $conSub->getResult($sql);
            if(count($res) == 0){
                echo '<tr><td colspan="8" style="text-align:center">Data tidak ditemukan </td></tr>';
            } else{
                $nom = 0;
                foreach($res as $data){
                    $nom++;
                    $idp = $data['id_prd'];
					$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
					$alamat	= $data['alamat_survey']." ".ucwords($tempal)." ".$data['nama_prov'];
                    $kirim	= date("d/m/Y", strtotime($data['tanggal_kirim']));
        ?>
            <tr>
                <td class="text-center"><?php echo $nom; ?></td>
                <td>
                    <p style="margin-bottom:0px"><b><?php echo $data['kode_pelanggan'];?></b></p>
                    <p style="margin-bottom:0px"><?php echo $data['nama_customer'];?></p>
                    <p style="margin-bottom:0px"><i><?php echo $data['fullname'];?></i></p>
                </td>
                <td>
                    <p style="margin-bottom:0px"><b><?php echo $data['nama_area'];?></b></p>
                    <p style="margin-bottom:0px"><?php echo $alamat;?></p>
                </td>
                <td><?php echo $data['produk'];?></td>
                <td class="text-right"><?php echo number_format($data['volume']);?></td>
                <td class="text-center"><?php echo $kirim;?></td>
                <td><?php echo $data['status_jadwal'];?></td>
                <td class="text-center"><input type="radio" name="code_prd" id="code_prd" class="chkp validate[required]" value="<?php echo $idp;?>" /></td>
            </tr>
        <?php } } ?>
        </tbody>
    </table>
</div>
<script>
	$(document).ready(function(){
		$(".chkp, #cekAll").iCheck({checkboxClass:'icheckbox_square-blue', radioClass:'iradio_square-blue'});
	});
</script>

<?php $conSub->close(); ?>
