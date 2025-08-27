<?php
if ($q1 == '1' || $q1 == '2') {
	$sqlutama01 = "
		with tbl_cek_data_01 as (
			select distinct id_produk, id_terminal, tanggal_inven 
			from new_pro_inventory_depot  
			where id_jenis = 1 and id_produk = '" . $idproduk . "' and tanggal_inven <= '" . $q3 . "-" . $q2 . "-1'
		), tbl_data_awal_ori as (
			select a.id_jenis, 'Data Awal' as jenis_penambahan,
			a.id_produk, a.id_terminal, a.tanggal_inven,
			sum(in_inven) as beginningnya
			from new_pro_inventory_depot a
			join pro_master_terminal b on a.id_terminal = b.id_master
			join pro_master_produk c on a.id_produk = c.id_master
			where id_jenis = 1 and id_produk = '" . $idproduk . "' 
				and month(tanggal_inven) = '" . $q2 . "' and year(tanggal_inven) = '" . $q3 . "'
				" . $where01 . " 
			group by a.id_jenis, a.id_produk, a.id_terminal, a.tanggal_inven
		), tbl_data_awal_gabung as (
			select cast('" . $nextMonth . "' as date) as tanggal_inven, 
			a.id_produk, a.id_terminal, 
			(
				sum(a.awal_inven) + 
				sum(case when a.tanggal_inven < '" . $q3 . "-" . $q2 . "-01' then a.in_inven end) + 
				sum(case when a.tanggal_inven < '" . $q3 . "-" . $q2 . "-01' then a.adj_inven end)
			) - sum(case when a.tanggal_inven < '" . $q3 . "-" . $q2 . "-01' then a.out_inven end) as beginningnya
			from new_pro_inventory_depot a 
			join pro_master_terminal b on a.id_terminal = b.id_master 
			join pro_master_produk c on a.id_produk = c.id_master 
			join tbl_cek_data_01 d on a.id_terminal = d.id_terminal and a.tanggal_inven >= d.tanggal_inven
			where 1=1 and a.id_produk = '" . $idproduk . "' and a.tanggal_inven < '" . $nextMonth . "'
				 " . $where01 . "
			group by a.id_terminal, a.id_produk 
		), tbl_data_awal as (
			select
			coalesce(a.id_jenis, 1) as id_jenis,
			'Data Awal' as jenis_penambahan,
			coalesce(a.id_produk, b.id_produk) as id_produk,
			coalesce(a.id_terminal, b.id_terminal) as id_terminal,
			coalesce(a.tanggal_inven, b.tanggal_inven) as tanggal_inven,
			coalesce(a.beginningnya, b.beginningnya) as beginningnya 
			from tbl_data_awal_ori a 
			right join tbl_data_awal_gabung b on a.id_terminal = b.id_terminal and a.id_produk = b.id_produk 
		)
		select a.id_master as id_terminal, concat(a.nama_terminal, ' ', a.tanki_terminal) as ket_terminal,
		SUM(CASE WHEN day(b.tanggal_inven) = 1 then beginningnya end) as col01
		from pro_master_terminal a
		left join tbl_data_awal b on a.id_master = b.id_terminal
		where 1=1 " . $where02 . " 
		group by a.id_master  
	"; //echo nl2br($sqlutama01);
} else {
	$sqlutama01 = "
		with tbl_cek_data_01 as (
			select distinct id_produk, id_terminal, tanggal_inven 
			from new_pro_inventory_depot  
			where id_jenis = 1 and id_produk = '" . $idproduk . "' and tanggal_inven <= '" . $q3 . "-" . $q2 . "-1'
		), tbl_data_awal_ori as (
			select a.id_jenis, 'Data Awal' as jenis_penambahan,
			a.id_produk, a.id_terminal, a.tanggal_inven,
			sum(in_inven) as beginningnya
			from new_pro_inventory_depot a
			join pro_master_terminal b on a.id_terminal = b.id_master
			join pro_master_produk c on a.id_produk = c.id_master
			where id_jenis = 1 and id_produk = '" . $idproduk . "' 
				and month(tanggal_inven) = '" . $q2 . "' and year(tanggal_inven) = '" . $q3 . "'
				" . $where01 . " 
			group by a.id_jenis, a.id_produk, a.id_terminal, a.tanggal_inven
		), tbl_data_awal_gabung as (
			select cast('" . $nextMonth . "' as date) as tanggal_inven, 
			a.id_produk, a.id_terminal, 
			(
				sum(a.awal_inven) + 
				sum(case when a.tanggal_inven < '" . $q3 . "-" . $q2 . "-01' then a.in_inven end) + 
				sum(case when a.tanggal_inven < '" . $q3 . "-" . $q2 . "-01' then a.adj_inven end)
			) - sum(case when a.tanggal_inven < '" . $q3 . "-" . $q2 . "-01' then a.out_inven end) as beginningnya
			from new_pro_inventory_depot a 
			join pro_master_terminal b on a.id_terminal = b.id_master 
			join pro_master_produk c on a.id_produk = c.id_master 
			join tbl_cek_data_01 d on a.id_terminal = d.id_terminal and a.tanggal_inven >= d.tanggal_inven
			where 1=1 and a.id_produk = '" . $idproduk . "' and a.tanggal_inven < '" . $nextMonth . "'
				 " . $where01 . "
			group by a.id_terminal, a.id_produk 
		), tbl_data_awal as (
			select
			coalesce(a.id_jenis, 1) as id_jenis,
			'Data Awal' as jenis_penambahan,
			coalesce(a.id_produk, b.id_produk) as id_produk,
			coalesce(a.id_terminal, b.id_terminal) as id_terminal,
			coalesce(a.tanggal_inven, b.tanggal_inven) as tanggal_inven,
			coalesce(a.beginningnya, b.beginningnya) as beginningnya 
			from tbl_data_awal_ori a 
			right join tbl_data_awal_gabung b on a.id_terminal = b.id_terminal and a.id_produk = b.id_produk 
		), tbl_data_awal_nasional as (
			select a.id_master as id_terminal, a.id_cabang, concat(a.nama_terminal, ' ', a.tanki_terminal) as ket_terminal,
			SUM(CASE WHEN day(b.tanggal_inven) = 1 then beginningnya end) as col01
			from pro_master_terminal a
			left join tbl_data_awal b on a.id_master = b.id_terminal
			where 1=1 " . $where02 . " 
			group by a.id_master  
		)
		select a.id_master as id_terminal, a.nama_cabang as ket_terminal, SUM(col01) as col01 
		from pro_master_cabang a 
		left join tbl_data_awal_nasional b on a.id_master = b.id_cabang 
		where 1=1 and a.id_master <> 1 
		group by a.id_master
	"; //echo $sqlutama01;
}
