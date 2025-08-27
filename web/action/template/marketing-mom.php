<style>
.container, table {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 13px;
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

</style>
<htmlpagefooter name="myHTMLFooter1">
	<p style="font-size:6pt; text-align:right;">Printed by <?php echo $printe;?></p>
</htmlpagefooter>
<sethtmlpagefooter name="myHTMLFooter1" page="ALL" value="on" show-this-page="1" />

<div class="container">
    <h3 style="text-align: center; font-weight: 500px; font-size: 25px; background-color: #ccc;">Minutes Of Meeting</h3>
    <br/>
    <table class="no-border" width="100%">
        <tr>
            <td style="width: 15%;"><span style="font-weight: bold; font-size: 15px;">Tanggal</span></td>
            <td style="width: 85%;"><span style="font-size: 15px;">: <?php echo tgl_indo($marketing_mom['date']); ?></span></td>
        </tr>
        <tr>
            <td style="width: 15%;"><span style="font-weight: bold; font-size: 15px;">Place</span></td>
            <td style="width: 85%;"><span style="font-size: 15px;">: <?php echo $marketing_mom['place']; ?></span></td>
        </tr>
        <tr>
            <td style="width: 15%;"><span style="font-weight: bold; font-size: 15px;">Customer</span></td>
            <td style="width: 85%;"><span style="font-size: 15px;">: <?php echo $marketing_mom['customer']; ?></span></td>
        </tr>
    </table>
    <br/>
    <h4 style="text-align: center; font-weight: 500px; font-size: 22px; background-color: #eee; margin-bottom: 5px;">Kehadiran</h4>
    <table border="1" width="100%" style="border: 1px solid black; font-size: 13px;">
        <thead>
            <tr style="background-color: #ccc;">
                <th style="width: 10%;">No</th>
                <th style="width: 45%;">Nama</th>
                <th style="width: 45%;">Jabatan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if (count($marketing_mom_participant)) {
                    foreach($marketing_mom_participant as $i => $row) {
            ?>
            <tr>
                <td style="border-bottom: none; border-top: none; padding: 10px 5px;"><?=($i+1)?></td>
                <td style="border-bottom: none; border-top: none; padding: 10px 5px;"><?=$row['name']?></td>
                <td style="border-bottom: none; border-top: none; padding: 10px 5px;"><?=$row['position']?></td>
            </tr>
            <?php 
                    }
                } 
            ?>
        </tbody>
    </table>
    <br/>
    <h4 style="text-align: center; font-weight: 500px; font-size: 22px; background-color: #eee; margin-bottom: 5px;">Fuel</h4>
    <table border="1" width="100%" style="border: 1px solid black; font-size: 13px;">
        <thead>
            <tr style="background-color: #ccc;">
                <th class="text-center" width="10%" rowspan="2">No</th>
                <th class="text-center" width="10%" rowspan="2">Nama Customer</th>
                <th class="text-center" width="20%" colspan="2">Potensi</th>
                <th class="text-center" width="30%" colspan="3">Tersuplai</th>
                <th class="text-center" width="10%" rowspan="2">Sisa Potensi</th>
                <th class="text-center" width="10%" rowspan="2">Kompetitor</th>
                <th class="text-center" width="10%" rowspan="2">Harga Kompetitor</th>
                <th class="text-center" width="10%" rowspan="2">PIC</th>
                <th class="text-center" width="10%" rowspan="2">TOP</th>
                <th class="text-center" width="20%" colspan="2">Kontak</th>
                <th class="text-center" width="20%" rowspan="2">Catatan</th>
            </tr>
            <tr style="background-color: #ccc;">
                <th class="text-center" width="10%" style="background: white;">Volume</th>
                <th class="text-center" width="10%" style="background: white;">Waktu</th>
                <th class="text-center" width="10%" style="background: white;">Jumlah Pengiriman</th>
                <th class="text-center" width="10%" style="background: white;">Waktu</th>
                <th class="text-center" width="10%" style="background: white;">Volume</th>
                <th class="text-center" width="10%" style="background: white;">Email</th>
                <th class="text-center" width="10%" style="background: white;">HP/Tlpn</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if (count($database_fuel)) {
                    foreach($database_fuel as $i => $row) {
            ?>
            <tr>
                <td><?=($i+1)?></td>
                <td><?=$row['nama_customer']?></td>
                <td><?=$row['potensi_volume']?number_format($row['potensi_volume']):''?></td>
                <td><?=$row['potensi_waktu']?></td>
                <td><?=$row['tersuplai_jumlah_pengiriman']?></td>
                <td><?=$row['tersuplai_waktu']?></td>
                <td><?=$row['potensi_volume']?number_format($row['tersuplai_volume']):''?></td>
                <td><?=$row['sisa_potensi']?></td>
                <td><?=$row['kompetitor']?></td>
                <td><?=$row['potensi_volume']?number_format($row['harga_kompetitor']):''?></td>
                <td><?=$row['top']?></td>
                <td><?=$row['pic']?></td>
                <td><?=$row['kontak_email']?></td>
                <td><?=$row['kontak_phone']?></td>
                <td><?=$row['catatan']?></td>
            </tr>
            <?php 
                    }
                } else {
            ?>
            <tr>
                <td colspan="15">&nbsp;</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <br/>
    <h4 style="text-align: center; font-weight: 500px; font-size: 22px; background-color: #eee; margin-bottom: 5px;">Lubricant</h4>
    <table border="1" width="100%" style="border: 1px solid black; font-size: 13px;">
        <thead>
            <tr style="background-color: #ccc;">
                <th class="text-center" width="10%" rowspan="2">No</th>
                <th class="text-center" width="10%" rowspan="2">Nama Customer</th>
                <th class="text-center" width="10%" rowspan="2">Jenis Oil</th>
                <th class="text-center" width="10%" rowspan="2">Spesifikasi</th>
                <th class="text-center" width="20%" colspan="2">Konsumsi</th>
                <th class="text-center" width="10%" rowspan="2">Kompetitor</th>
                <th class="text-center" width="10%" rowspan="2">PIC</th>
                <th class="text-center" width="10%" rowspan="2">TOP</th>
                <th class="text-center" width="10%" rowspan="2">Harga Kompetitor</th>
                <th class="text-center" width="20%" colspan="2">Kontak</th>
                <th class="text-center" width="20%" rowspan="2">Keterangan</th>
            </tr>
            <tr style="background-color: #ccc;">
                <th class="text-center" width="10%" style="background: white;">Volume</th>
                <th class="text-center" width="10%" style="background: white;">Unit</th>
                <th class="text-center" width="10%" style="background: white;">Email</th>
                <th class="text-center" width="10%" style="background: white;">HP/Tlpn</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if (count($database_lubricant_oil)) {
                    foreach($database_lubricant_oil as $i => $row) {
            ?>
            <tr>
                <td><?=($i+1)?></td>
                <td><?=$row['nama_customer']?></td>
                <td><?=$row['jenis_oil']?></td>
                <td><?=$row['spesifikasi']?></td>
                <td><?=$row['konsumsi_volume']?number_format($row['konsumsi_volume']):''?></td>
                <td><?=$row['konsumsi_unit']?></td>
                <td><?=$row['kompetitor']?></td>
                <td><?=$row['konsumsi_volume']?number_format($row['harga_kompetitor']):''?></td>
                <td><?=$row['top']?></td>
                <td><?=$row['pic']?></td>
                <td><?=$row['kontak_email']?></td>
                <td><?=$row['kontak_phone']?></td>
                <td><?=$row['keterangan']?></td>
            </tr>
            <?php 
                    }
                } else {
            ?>
            <tr>
                <td colspan="13">&nbsp;</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <br/>
    <h4 style="text-align: center; font-weight: 500px; font-size: 22px; background-color: #eee; margin-bottom: 5px;">Dokumentasi</h4>
    <table border="1" width="100%" style="border: 1px solid black">
        <thead>
            <tr style="background-color: #ccc;">
                <th style="text-align: center;">Odometer Pergi</th>
                <th style="text-align: center;">Odometer Pulang</th>
                <th style="text-align: center;">Meeting Customer</th>
                <th style="text-align: center;">Tambahan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center; width: 25%">
                    <?php if ($path_odometer_pergi) { ?>
                    <img src="<?=$path_odometer_pergi?>" style="width: 175px;">
                    <?php } ?>
                </td>
                <td style="text-align: center; width: 25%">
                    <?php if ($path_odometer_pulang) { ?>
                    <img src="<?=$path_odometer_pulang?>" style="width: 175px;">
                    <?php } ?>
                </td>
                <td style="text-align: center; width: 25%">
                    <?php if ($path_meeting_customer) { ?>
                    <img src="<?=$path_meeting_customer?>" style="width: 175px;">
                    <?php } ?>
                </td>
                <td style="text-align: center; width: 25%">
                    <?php if ($path_tambahan) { ?>
                    <img src="<?=$path_tambahan?>" style="width: 175px;">
                    <?php } ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>