<?php




$sql = "SELECT a.id_dsd, d.id_wilayah, c.link_gps FROM pro_po_ds_detail a
JOIN pro_po_detail b ON a.id_pod=b.id_pod
JOIN pro_master_transportir_mobil c ON c.id_master=b.mobil_po
JOIN pro_po d ON b.id_po=d.id_po
WHERE a.is_delivered = '0' AND a.is_cancel = '0' AND a.nomor_do IS NOT NULL AND
CASE
    WHEN d.id_wilayah = '2' AND c.link_gps = 'OSLOG' THEN a.tanggal_loading >= '2024-03-01'
    WHEN d.id_wilayah = '4' AND c.link_gps = 'OSLOG' THEN a.tanggal_loading >= '2024-04-01'
    WHEN d.id_wilayah = '6' AND c.link_gps = 'OSLOG' THEN a.tanggal_loading >= '2024-03-01'
    WHEN d.id_wilayah = '3' AND c.link_gps = 'OSLOG' THEN a.tanggal_loading >= '2024-03-01'
    ELSE NULL
    END
ORDER BY a.id_ds DESC";
$tot_record = $con->num_rows($sql);
$res = $con->getResult($sql);
