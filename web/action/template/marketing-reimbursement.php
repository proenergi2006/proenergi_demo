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
    <h3 style="text-align: center; font-weight: 500px; font-size: 25px; background-color: #ccc;">FORM REIMBURSEMENT</h3>
    <br/>
    <table class="no-border" width="100%">
        <tr>
            <td style="width: 15%;"><span style="font-weight: bold; font-size: 15px;">Tanggal</span></td>
            <td style="width: 85%;"><span style="font-size: 15px;">: <?php echo tgl_indo($marketing_reimbursement['marketing_reimbursement_date']); ?></span></td>
        </tr>
        <tr>
            <td style="width: 15%;"><span style="font-weight: bold; font-size: 15px;">No Polisi</span></td>
            <td style="width: 85%;"><span style="font-size: 15px;">: <?php echo $marketing_reimbursement['no_polisi']; ?></span></td>
        </tr>
        <tr>
            <td style="width: 15%;"><span style="font-weight: bold; font-size: 15px;">User</span></td>
            <td style="width: 85%;"><span style="font-size: 15px;">: <?php echo tgl_indo($marketing_reimbursement['user']); ?></span></td>
        </tr>
        <tr>
            <td style="width: 15%;"><span style="font-weight: bold; font-size: 15px;">KM Awal</span></td>
            <td style="width: 85%;"><span style="font-size: 15px;">: <?php echo $marketing_reimbursement['km_awal']; ?></span></td>
        </tr>
        <tr>
            <td style="width: 15%;"><span style="font-weight: bold; font-size: 15px;">KM Akhir</span></td>
            <td style="width: 85%;"><span style="font-size: 15px;">: <?php echo $marketing_reimbursement['km_akhir']; ?></span></td>
        </tr>
    </table>
    <br/>
    <table border="1" width="100%" style="border: 1px solid black; font-size: 13px;">
        <thead>
            <tr style="background-color: #ccc;">
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Item</th>
                <th style="width: 30%;">Keterangan</th>
                <th style="width: 20%;" colspan="2">Nilai</th>
                <th style="width: 20%;" colspan="2">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                if (count($marketing_reimbursement['item'])) {
                    foreach($marketing_reimbursement['item'] as $i => $row) {
            ?>
            <tr>
                <td style="border-bottom: none; border-top: none; padding: 10px 5px;"><?=($i+1)?></td>
                <td style="border-bottom: none; border-top: none; padding: 10px 5px;"><?=$row['item']?></td>
                <td style="border-bottom: none; border-top: none;">
                    <ul style="margin-left: -20px;">
                    <?php foreach ($row['keterangan'] as $k => $v) { ?>
                        <li><?=$v['keterangan']?></li>
                    <?php } ?>
                    </ul>
                </td>
                <td style="border-bottom: none; border-top: none; padding: 10px 5px; border-right: none;">
                    <?php foreach ($row['keterangan'] as $k => $v) { ?>
                        <div>Rp</div>
                    <?php } ?>
                </td>
                <td style="border-bottom: none; border-top: none; padding: 10px 5px; border-left: none; text-align: right;">
                    <?php foreach ($row['keterangan'] as $k => $v) { ?>
                        <div><?=number_format($v['nilai'])?></div>
                    <?php } ?>
                </td>
                <td style="border-bottom: none; border-top: none; padding: 10px 5px; border-right: none;">Rp</td>
                <td style="border-bottom: none; border-top: none; padding: 10px 5px; border-left: none; text-align: right;"><?=number_format($row['jumlah'])?></td>
            </tr>
            <?php 
                    }
                } 
            ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #ccc;">
                <th colspan="5" style="font-size: 14px; text-align: center;">Total</th>
                <th style="border-right: none; text-align: left;">Rp</th>
                <th style="border-left: none; text-align: right;"><?=number_format($marketing_reimbursement['total'])?></th>
            </tr>
        </tfoot>
    </table>
    <br/><br/>
    <table border="1" width="100%" style="border: 1px solid black">
        <thead>
            <tr style="background-color: #ccc;">
                <th style="width: 33%;">Dibuat</th>
                <th style="width: 33%;">Mengetahui</th>
                <th style="width: 33%;">Menyetujui</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="height: 80px;"></td>
                <td style="height: 80px;"></td>
                <td style="height: 80px;"></td>
            </tr>
        </tbody>
    </table>
    <br/><br/>
    <span style="font-size: 15px;">Bukti Transaksi</span>
    <table width="100%" style="border: 1px solid black">
        <tr>
            <td style="height: 300px;"></td>
        </tr>
    </table>
</div>