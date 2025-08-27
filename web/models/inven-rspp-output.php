<?php
if ($q1 == '1' || $q1 == '2') {
	$sqlutama04 = "
		with tbl_cek_data_01 as (
			select distinct id_produk, id_terminal, tanggal_inven
			from new_pro_inventory_depot
			where id_jenis = 1 and id_produk = '" . $idproduk . "' and tanggal_inven <= '" . $q3 . "-" . $q2 . "-1'
		), tbl_data_input as (
			select 31 as id_jenis, 'Output' as jenis_penambahan, 
			a.id_produk, a.id_terminal, a.tanggal_inven, 
			a.out_inven as inputnya 
			from new_pro_inventory_depot a
			join pro_master_terminal b on a.id_terminal = b.id_master
			join pro_master_produk c on a.id_produk = c.id_master
			join tbl_cek_data_01 d on a.id_terminal = d.id_terminal and a.tanggal_inven >= d.tanggal_inven
			where id_jenis in (2, 7, 10) and a.id_produk = '" . $idproduk . "' 
				and month(a.tanggal_inven) = '" . $q2 . "' and year(a.tanggal_inven) = '" . $q3 . "'
				" . $where01Output . " 
		)
		select a.id_master as id_terminal, concat(a.nama_terminal, ' ', a.tanki_terminal) as ket_terminal,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-1' then inputnya end) as col01,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-2' then inputnya end) as col02,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-3' then inputnya end) as col03,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-4' then inputnya end) as col04,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-5' then inputnya end) as col05,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-6' then inputnya end) as col06,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-7' then inputnya end) as col07,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-8' then inputnya end) as col08,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-9' then inputnya end) as col09,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-10' then inputnya end) as col10,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-11' then inputnya end) as col11,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-12' then inputnya end) as col12,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-13' then inputnya end) as col13,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-14' then inputnya end) as col14,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-15' then inputnya end) as col15,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-16' then inputnya end) as col16,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-17' then inputnya end) as col17,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-18' then inputnya end) as col18,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-19' then inputnya end) as col19,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-20' then inputnya end) as col20,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-21' then inputnya end) as col21,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-22' then inputnya end) as col22,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-23' then inputnya end) as col23,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-24' then inputnya end) as col24,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-25' then inputnya end) as col25,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-26' then inputnya end) as col26,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-27' then inputnya end) as col27,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-28' then inputnya end) as col28,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-29' then inputnya end) as col29,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-30' then inputnya end) as col30,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-31' then inputnya end) as col31
		from pro_master_terminal a
		left join tbl_data_input b on a.id_master = b.id_terminal
		where 1=1 " . $where02 . " 
		group by a.id_master 
	"; //echo nl2br($sqlutama04);
} else {
	$sqlutama04 = "
		with tbl_cek_data_01 as (
			select distinct id_produk, id_terminal, tanggal_inven 
			from new_pro_inventory_depot 
			where id_jenis = 1 and id_produk = '" . $idproduk . "' and tanggal_inven <= '" . $q3 . "-" . $q2 . "-1'
		), tbl_data_input as (
			select 31 as id_jenis, 'Output' as jenis_penambahan, 
			a.id_produk, b.id_cabang, a.tanggal_inven, 
			a.out_inven as inputnya 
			from new_pro_inventory_depot a
			join pro_master_terminal b on a.id_terminal = b.id_master
			join pro_master_produk c on a.id_produk = c.id_master
			join tbl_cek_data_01 d on a.id_terminal = d.id_terminal 
			where id_jenis in (2, 7, 10) and a.id_produk = '" . $idproduk . "' 
				and month(a.tanggal_inven) = '" . $q2 . "' and year(a.tanggal_inven) = '" . $q3 . "'
		)
		select a.id_master as id_terminal, a.nama_cabang as ket_terminal,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-1' then inputnya end) as col01,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-2' then inputnya end) as col02,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-3' then inputnya end) as col03,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-4' then inputnya end) as col04,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-5' then inputnya end) as col05,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-6' then inputnya end) as col06,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-7' then inputnya end) as col07,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-8' then inputnya end) as col08,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-9' then inputnya end) as col09,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-10' then inputnya end) as col10,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-11' then inputnya end) as col11,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-12' then inputnya end) as col12,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-13' then inputnya end) as col13,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-14' then inputnya end) as col14,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-15' then inputnya end) as col15,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-16' then inputnya end) as col16,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-17' then inputnya end) as col17,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-18' then inputnya end) as col18,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-19' then inputnya end) as col19,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-20' then inputnya end) as col20,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-21' then inputnya end) as col21,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-22' then inputnya end) as col22,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-23' then inputnya end) as col23,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-24' then inputnya end) as col24,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-25' then inputnya end) as col25,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-26' then inputnya end) as col26,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-27' then inputnya end) as col27,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-28' then inputnya end) as col28,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-29' then inputnya end) as col29,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-30' then inputnya end) as col30,
		SUM(CASE WHEN b.tanggal_inven = '" . $q3 . "-" . $q2 . "-31' then inputnya end) as col31
		from pro_master_cabang a
		left join tbl_data_input b on a.id_master = b.id_cabang
		where 1=1 and a.id_master <> 1  
		group by a.id_master 
	";
}
