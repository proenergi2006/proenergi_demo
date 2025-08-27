<style>
.container, table {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
}
table {
    border-collapse: collapse;
}
table th, table td {
    border: 1px solid black;
    padding: 3px;
}
table.no-border td {
    border: none;
}
.tabel_header td{
	padding: 1px 3px;
	font-size: 9pt;
	height: 18px;
}
.tabel_rincian td{
	padding:2px;
}
.td-ket,
.td-subisi{
	padding:1px 0px 2px;
	vertical-align:top;
}
.td-subisi{font-size:5pt;}
.td-ket{
	padding:1px 0px;
	font-size:8pt;
}
p{
	margin: 0 0 10px;
	text-align: justify;
}
.b1{border-top: 1px solid #000;}
.b2{border-right: 1px solid #000;}
.b3{border-bottom: 1px solid #000;}
.b4{border-left: 1px solid #000;}

.b1d{border-top: 2px solid #000;}
.b2d{border-right: 2px solid #000;}
.b3d{border-bottom: 2px solid #000;}
.b4d{border-left: 2px solid #000;}

</style>
<htmlpagefooter name="myHTMLFooter1">
	<p style="font-size:6pt; text-align:right;">Printed by <?php echo $printe;?></p>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />
<?php
    if (count($res) > 0) {
		$nom = 0;
		foreach ($res as $data) {
			$nom++;
			$volume_po = $data['volume_po'];
			$tempal = strtolower(str_replace(array("KABUPATEN ","KOTA "), array("",""), $data['nama_kab']));
			$alamat	= ucwords($tempal)." ".$data['nama_prov'];
			$bar	= $data['kode_barcode']."06".str_pad($data['id_dsd'],6,'0',STR_PAD_LEFT);
			$seg_aw = ($data['nomor_segel_awal'])?str_pad($data['nomor_segel_awal'],4,'0',STR_PAD_LEFT):'';
			$seg_ak = ($data['nomor_segel_akhir'])?str_pad($data['nomor_segel_akhir'],4,'0',STR_PAD_LEFT):'';
			if($data['jumlah_segel'] == 1)
				$nomor_segel = $data['pre_segel']."-".$seg_aw;
			else if($data['jumlah_segel'] == 2)
				$nomor_segel = $data['pre_segel']."-".$seg_aw." &amp; ".$data['pre_segel']."-".$seg_ak;
			else if($data['jumlah_segel'] > 2)
				$nomor_segel = $data['pre_segel']."-".$seg_aw." s/d ".$data['pre_segel']."-".$seg_ak;
			else $nomor_segel = '';
?>
<div class="container">
    <h3 style="text-decoration: underline">BERITA ACARA SERAH TERIMA BAHAN BAKAR MINYAK</h3>
    <div style="float: left; width: 70%">
        <table class="no-border">
            <tr>
                <td>Nama Perusahaan</td>
                <td><?php echo $data['nama_customer']; ?></td>
            </tr>
            <tr>
                <td>No. Surat Jalan</td>
                <td><?php echo $data['no_spj']; ?></td>
            </tr>
            <tr>
                <td>Tanggal & Jam</td>
                <td><?php echo tgl_indo($data['tanggal_loading']); ?></td>
            </tr>
            <tr>
                <td>No. Truck</td>
                <td><?php echo $data['nomor_plat']; ?></td>
            </tr>
        </table>
    </div>
    <div style="float: right; width: 25%; text-align: right">
        <img src="<?php echo BASE_IMAGE ?>/proenergi.jpg" width="50%">
    </div>
    <div style="clear:both"></div>

    <table border="0" cellpadding="10" cellspacing="1" width="100%" class="table_rincian" style="border: 1px double black">
        <tr>
            <td>
                <h4 style="font-weight: bold; text-decoration: underline">BAGIAN 1: Pelanggan memastikan tangki penerima cukup</h4>
                <table border="1" cellpadding="5" cellspacing="0" width="100%" style="margin-top: 10px">
                    <tr>
                        <td colspan="2">No. Tangki</td>
                        <td style="width: 13%">1</td>
                        <td style="width: 13%">2</td>
                        <td style="width: 13%">3</td>
                        <td style="width: 13%">4</td>
                    </tr>
                    <tr>
                        <td colspan="2">Tipe Produk:</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Ukuran Tangki (liter)</td>
                        <td>A</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Isi tangki sekarang (liter)</td>
                        <td>B</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Muatan tangki (liter)</td>
                        <td>A - B = C</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Jumlah untuk diisi (liter)</td>
                        <td style="background-color: #ddd">kurang dari C</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                <small>Tarik garis penuntun dari kompartemen lori ke UGT</small>
            </td>
        </tr>
        <tr>
            <td style="height: 130px; background: url(<?php echo BASE_IMAGE ?>/oil-tanker-1.png) no-repeat center center; border-bottom: 1px solid black;">
                <table class="no-border" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>&nbsp;</td>
                        <td style="border-right: 2px solid #000; padding-right: 80px">1</td>
                        <td style="padding-left: 80px">2</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 2px dashed #000; padding-top: 20px; padding-bottom: 5px; padding-right: 250px">Nama Produk</td>
                        <td style="border-bottom: 2px dashed #000; border-right: 2px solid #000"></td>
                        <td style="border-bottom: 2px dashed #000; padding-right: 120px"></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 5px; padding-bottom: 50px">Volume (liter)</td>
                        <td style="padding-left: 50px; padding-bottom: 50px; border-right: 2px solid #000">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid black">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="width: 70%">
                            <h4 style="font-weight: bold; text-decoration: underline">BAGIAN 2: Proses bongkar muatan</h4><br>
                            = Sebelum Discharge =
                            <ol>
                                <li>Mengenakan APD dan Seragam dengan benar</li>
                                <li>Pasang ganjal, Safety cone, dan tidak ada sumber api / percikan api / orang merokok</li>
                                <li>Memeriksa Segel tidak rusak dan no. seri sama dengan surat jalan</li>
                                <li>Mengisi ketinggian produk sesuai dengan batas TERA dan pengukuran dengan deepstick ................ mm</li>
                                <li>Letakkan APAR, wadah penampung dibawah bottom loading, Alkom, dan Outlet pipa pelanggan</li>
                                <li>Selang dan koneksi terpasang baik, lakukan prosedur pengecekan</li>
                                <li>Pasang selang ke inlet pipa pelanggan sesuai perintah dan Staff pelanggan</li>
                            </ol>
                            = Selama Discharge =
                            <ol>
                                <li>Pastikan tidak ada tumpahan atau tetesan semasa proses discharga, lakukan pemeriksaan</li>
                            </ol>
                            = Sesudah Discharge =
                            <ol>
                                <li>Semua kompartemen sudah kosong (cek bersama pelanggan)</li>
                                <li>Semua tirisan telah diserahkan kepada pelanggan, pastikan semua valve telah tertutup</li>
                                <li>Dokumen pengiriman telah dilengkapi, seperti tanda tangan dari Stempel</li>
                                <li>Tidak terjadi tumpahan, kebocoran dan ketidaknormalan selama proses pembongkaran</li>
                            </ol>
                        </td>
                        <td style="width: 30%">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid black">
                                <tr>
                                    <td style="background-color: #ccc">Pengemudi</td>
                                    <td>Pelanggan</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #ccc">
                                        Bila Siap
                                        <i class="fa fa-check"></i>
                                    </td>
                                    <td>
                                        Bila Siap
                                        <i class="fa fa-check"></i>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #ccc">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #ccc">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #ccc">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #ccc">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #ccc">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #ccc">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #ccc">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="border: none">&nbsp;</td>
                                    <td style="border: none">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #ccc">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="border: none">&nbsp;</td>
                                    <td style="border: none">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #ccc">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #ccc">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #ccc">&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <p>Pemeriksaan kualitas dan volume kenaikan BBM telah dilakukan. Apabila BBM telah dibongkar/diterima ke tangki milik pelanggan, maka selanjutnya menjadi tanggung jawab pelanggan sepenuhnya</p>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="no-border">
                    <tr>
                        <td style="border-top: 1px solid black">Tanda tangan pelanggan/perwakilan:</td>
                        <td style="border-top: 1px solid black">Tanda tangan pengemudi:</td>
                    </tr>
                    <tr>
                        <td style="height: 100px">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px solid black">Nama:</td>
                        <td style="border-top: 1px solid black">Nama:</td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px solid black">Tanggal & Jam:</td>
                        <td style="border-top: 1px solid black">Tanggal & Jam:</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <small>
        1. PT TDS/Transporter&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        2. PT TDS/HO (Hijau)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        3. PT Pro Energi/Supplier (merah)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        4. PT Customer (Kuning)
    </small>
    <?php if($nom < count($res)) echo '<pagebreak sheet-size="A4" margin-left="10mm" margin-right="10mm" margin-top="20mm" margin-bottom="10mm" />'; } } ?>
</div>