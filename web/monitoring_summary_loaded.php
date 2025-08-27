<?php
// monitoring_summary.php
// 1) Load koneksi & helper
$privat = explode('/', dirname($_SERVER['PHP_SELF']))[1];
$pub    = $_SERVER['DOCUMENT_ROOT'] . "/" . $privat;
require_once("$pub/libraries/helper/load.php");
load_helper("autoload");
$con    = new Connection();

// 2) Terima bulan (YYYY-MM)
$month = $_POST['month'] ?? date('Y-m');
$q5  = $_POST['q5'] ?? '';
$start = "$month-01";
$end   = date('Y-m-t', strtotime($start));
$maxDays = (int)date('t', strtotime($start));

$termWhere = $q5
    ? "AND d.id_master = '" . addslashes($q5) . "'"
    : "";

// 3) Query summary
$sql = "
  SELECT
    d.nama_terminal,
    DAY(a.tanggal_loaded) AS day,
    SUM(c.volume) AS total_volume
  FROM pro_po_ds_detail a
  JOIN pro_pr b ON a.id_pr = b.id_pr
  JOIN pro_pr_detail c ON a.id_pr = c.id_pr AND c.id_prd = a.id_prd
  JOIN pro_master_terminal d ON c.pr_terminal = d.id_master
  WHERE
    a.tanggal_loaded BETWEEN '$start' AND '$end'
    AND a.is_loaded = 1 AND a.is_cancel != 1
    $termWhere
  GROUP BY d.nama_terminal, DAY(a.tanggal_loaded)
  ORDER BY d.nama_terminal, DAY(a.tanggal_loaded)
";
$result = $con->getResult($sql);

// 4) Pivot data
$data = [];
foreach ($result as $r) {
    $data[$r['nama_terminal']][(int)$r['day']] = (float)$r['total_volume'];
}

$totals = [];
foreach ($data as $days) {
    foreach ($days as $day => $vol) {
        $totals[$day] = ($totals[$day] ?? 0) + $vol;
    }
}

// 5) Cetak HTML tabel (thead + tbody)
?>
<div class="table-responsive no-padding">
    <table class="table table-bordered table-condensed text-center">
        <thead>
            <tr>
                <th>Terminal</th>
                <?php for ($d = 1; $d <= $maxDays; $d++):
                    $ymd   = sprintf('%s-%02d', $month, $d);
                    $label = date('j M', strtotime($ymd));
                ?>
                    <th><?= $label ?></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $cabang => $days): ?>
                <tr>
                    <td class="text-left"><strong><?= htmlspecialchars($cabang) ?></strong></td>
                    <?php for ($d = 1; $d <= $maxDays; $d++): ?>
                        <td>
                            <?php if (isset($days[$d])): ?>
                                <button
                                    type="button"
                                    class="btn btn-xs btn-link loaded-btn"
                                    data-cabang="<?= htmlspecialchars($cabang) ?>"
                                    data-day="<?= $d ?>"
                                    data-volume="<?= $days[$d] ?>">
                                    <?= number_format($days[$d], 0, ',', '.') ?>
                                </button>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>

            <!-- Baris TOTAL per tanggal -->
            <tr>
                <td class="text-left"><strong>Total</strong></td>
                <?php for ($d = 1; $d <= $maxDays; $d++): ?>
                    <td>
                        <?php
                        if (isset($totals[$d])) {
                            echo '<strong>' . number_format($totals[$d], 0, ',', '.') . '</strong>';
                        } else {
                            echo '<span class="text-muted">—</span>';
                        }
                        ?>
                    </td>
                <?php endfor; ?>
            </tr>
        </tbody>
    </table>
</div>