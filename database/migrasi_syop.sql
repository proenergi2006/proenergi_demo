/*
Supply Date => tgl pengiriman
Total Order => jumlah volume 
tambah 1 kolom total order = jumlah volume * hargaliter 

dr di admin belum muncul do akurat

GA
tambahin foto ruangan dan mobil di master datanya

purchasing
output ditampilkan pada rsspp jika tergantung dr data awal_inven
pada inventory dat di kolom output coba link ke dr
inventory depot hanya purchasing yg bisa add data 
list pembukuan bisa dilihat oleh user lain 


*/
delete from acl_user where id_role = 2;
delete from acl_role where id_role = 2;

alter table acl_role add column no_urut integer;

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(1, 'Role Super Admin', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 1, role_name= 'Role Super Admin';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(2, 'Role Administrator', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 2, role_name= 'Role Administrator';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(3, 'Role COO', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 4, role_name= 'Role COO';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(4, 'Role CFO', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 5, role_name= 'Role CFO';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(5, 'Role Purchasing', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 6, role_name= 'Role Purchasing';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(6, 'Role Operation Manager', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 7, role_name= 'Role Operation Manager';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(7, 'Role Branch Manager', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 8, role_name= 'Role Branch Manager';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(8, 'Role Legal', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 13, role_name= 'Role Legal';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(9, 'Role Logistik', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 17, role_name= 'Role Logistik';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(10, 'Role Admin Finance', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 12, role_name= 'Role Admin Finance';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(11, 'Role Marketing', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 16, role_name= 'Role Marketing';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(12, 'Role Transportir', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 19, role_name= 'Role Transportir';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(13, 'Role Terminal', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 18, role_name= 'Role Terminal';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(14, 'Role General Affair', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 11, role_name= 'Role General Affair';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(15, 'Role Manager Finance', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 9, role_name= 'Role Manager Finance';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(16, 'Role Manager Logistik', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 10, role_name= 'Role Manager Logistik';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(17, 'Role Key Account Executive', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 14, role_name= 'Role Key Account Executive';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(18, 'Role Customer Service', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 20, role_name= 'Role Customer Service';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(19, 'Role Customer', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 21, role_name= 'Role Customer';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by)
VALUES(20, 'Role SPV Marketing', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 15, role_name= 'Role SPV Marketing';

INSERT INTO acl_role(id_role, role_name, role_desc, is_active, created_time, created_ip, created_by) 
VALUES(21, 'Role CEO', '-', 1, '2022-11-26 20:47:49', '::1', 'Super Admin')
ON DUPLICATE KEY UPDATE no_urut = 3, role_name= 'Role CEO';

------------------------------------------------------------------------------------------------------------------------------------

update acl_user set id_role = 21 where username = 'vica.krisdianatha';
update acl_user set id_role = 21 where username = 'CEO';
INSERT INTO acl_user (
	username, password, fullname, mobile_user, email_user, id_role, 
	id_wilayah, id_group, id_transportir, id_terminal, id_customer, id_om, is_active, 
	created_time, created_ip, created_by, lastupdate_time, lastupdate_ip, lastupdate_by
) VALUES (
	'COO', '$2a$08$uD9mKbdk0o8RzEGnbEHRQO.GedC8uKglfaVF4TdnPOGJFhWHjIRC2', 'COO', '', '', 3, 
	1, 1, 0, 0, 0, 0, 1, NOW(), '::1', 'Super Admin', NULL, NULL, NULL
) ON DUPLICATE KEY UPDATE username = username;

alter table pro_master_harga_minyak add column harga_ceo integer default 0;
alter table pro_master_harga_minyak add column harga_coo integer default 0;


ALTER TABLE pro_customer ADD COLUMN induk_perusahaan varchar(225);
ALTER TABLE pro_customer ADD COLUMN kecamatan_customer varchar(225);
ALTER TABLE pro_customer ADD COLUMN kelurahan_customer varchar(225);

ALTER TABLE pro_customer_contact ADD COLUMN product_delivery_address varchar(1000);
ALTER TABLE pro_customer_contact ADD COLUMN invoice_delivery_addr_primary varchar(225);
ALTER TABLE pro_customer_contact ADD COLUMN invoice_delivery_addr_secondary varchar(225);
ALTER TABLE pro_customer_contact ADD COLUMN pic_fuelman_name varchar(50);
ALTER TABLE pro_customer_contact ADD COLUMN pic_fuelman_position varchar(50);
ALTER TABLE pro_customer_contact ADD COLUMN pic_fuelman_telp varchar(50);
ALTER TABLE pro_customer_contact ADD COLUMN pic_fuelman_mobile varchar(20);
ALTER TABLE pro_customer_contact ADD COLUMN pic_fuelman_email varchar(20);

ALTER TABLE pro_customer_payment ADD COLUMN kecamatan_billing varchar(225);
ALTER TABLE pro_customer_payment ADD COLUMN kelurahan_billing varchar(225);
ALTER TABLE pro_customer_payment ADD COLUMN calculate_method varchar(2);
ALTER TABLE pro_customer_payment ADD COLUMN bank_name varchar(225);
ALTER TABLE pro_customer_payment ADD COLUMN curency varchar(20);
ALTER TABLE pro_customer_payment ADD COLUMN bank_address varchar(225);
ALTER TABLE pro_customer_payment ADD COLUMN account_number varchar(20);
ALTER TABLE pro_customer_payment ADD COLUMN credit_facility int(2);
ALTER TABLE pro_customer_payment ADD COLUMN creditor varchar(225);

ALTER TABLE pro_customer_logistik ADD COLUMN supply_shceme int(2);
ALTER TABLE pro_customer_logistik ADD COLUMN specify_product int(2);
ALTER TABLE pro_customer_logistik ADD COLUMN volume_per_month int(11);
ALTER TABLE pro_customer_logistik ADD COLUMN operational_hour_from varchar(20);
ALTER TABLE pro_customer_logistik ADD COLUMN operational_hour_to varchar(20);
ALTER TABLE pro_customer_logistik ADD COLUMN nico int(2);

ALTER TABLE pro_customer_review ADD COLUMN jenis_asset varchar(500);
ALTER TABLE pro_customer_review ADD COLUMN kelengkapan_dok_tagihan varchar(500);
ALTER TABLE pro_customer_review ADD COLUMN alur_proses_periksaan varchar(500);
ALTER TABLE pro_customer_review ADD COLUMN jadwal_penerimaan varchar(500);
ALTER TABLE pro_customer_review ADD COLUMN background_bisnis varchar(500);
ALTER TABLE pro_customer_review ADD COLUMN lokasi_depo varchar(500);
ALTER TABLE pro_customer_review ADD COLUMN opportunity_bisnis varchar(500);

ALTER TABLE pro_penawaran ADD COLUMN kalkulasi_oa varchar(255);
ALTER TABLE pro_penawaran ADD COLUMN pembulatan tinyint default 1;
ALTER TABLE pro_penawaran ADD COLUMN coo_summary text default NULL;
ALTER TABLE pro_penawaran ADD COLUMN coo_pic varchar(80) default NULL;
ALTER TABLE pro_penawaran ADD COLUMN coo_tanggal datetime default NULL;
ALTER TABLE pro_penawaran ADD COLUMN coo_result tinyint default 0;

ALTER TABLE pro_penawaran MODIFY spv_mkt_summary text default NULL;
ALTER TABLE pro_penawaran MODIFY spv_mkt_pic varchar(80) default NULL;
ALTER TABLE pro_penawaran MODIFY spv_mkt_tanggal datetime default NULL;
ALTER TABLE pro_penawaran MODIFY spv_mkt_result tinyint default 0;
ALTER TABLE pro_penawaran MODIFY sm_mkt_summary text default NULL;
ALTER TABLE pro_penawaran MODIFY sm_mkt_pic varchar(80) default NULL;
ALTER TABLE pro_penawaran MODIFY sm_mkt_tanggal datetime default NULL;
ALTER TABLE pro_penawaran MODIFY sm_mkt_result tinyint default 0;
ALTER TABLE pro_penawaran MODIFY sm_wil_summary text default NULL;
ALTER TABLE pro_penawaran MODIFY sm_wil_pic varchar(80) default NULL;
ALTER TABLE pro_penawaran MODIFY sm_wil_tanggal datetime default NULL;
ALTER TABLE pro_penawaran MODIFY sm_wil_result tinyint default 0;
ALTER TABLE pro_penawaran MODIFY om_summary text default NULL;
ALTER TABLE pro_penawaran MODIFY om_pic varchar(80) default NULL;
ALTER TABLE pro_penawaran MODIFY om_tanggal datetime default NULL;
ALTER TABLE pro_penawaran MODIFY om_result tinyint default 0;
ALTER TABLE pro_penawaran MODIFY ceo_summary text default NULL;
ALTER TABLE pro_penawaran MODIFY ceo_pic varchar(80) default NULL;
ALTER TABLE pro_penawaran MODIFY ceo_tanggal datetime default NULL;
ALTER TABLE pro_penawaran MODIFY ceo_result tinyint default 0;
ALTER TABLE pro_penawaran MODIFY tgl_approval datetime default NULL;
ALTER TABLE pro_penawaran MODIFY pic_approval integer default NULL;


ALTER TABLE pro_inventory_vendor ADD COLUMN in_inven_po int;
ALTER TABLE pro_inventory_vendor ADD COLUMN is_diterima int DEFAULT 1;
ALTER TABLE pro_inventory_vendor ADD COLUMN in_inven_po_detail text;

alter table pro_pr_detail add column splitted_from_pr integer;
alter table pro_pr_detail add column vol_ori_pr integer;
alter table pro_pr_detail add column splitted_from integer;
alter table pro_pr_detail add column nomor_do_accurate varchar(80);

alter table pro_po add column catatan_selisih_mgrlog text;
alter table pro_po add column selisih_approved_mgrlog datetime default null;
alter table pro_po_detail add column oa_result_mgrlog tinyint default 0;
alter table pro_po_detail add column oa_pic_mgrlog varchar(50) default null;
alter table pro_po_detail add column oa_tanggal_mgrlog datetime default null;

ALTER TABLE pro_master_terminal ADD COLUMN id_cabang integer;
ALTER TABLE pro_master_terminal ADD COLUMN id_area integer;
DROP INDEX pro_master_terminal_idx1 ON pro_master_terminal;
CREATE INDEX pro_master_terminal_idx1 ON pro_master_terminal (id_cabang);
DROP INDEX pro_master_terminal_idx2 ON pro_master_terminal;
CREATE INDEX pro_master_terminal_idx2 ON pro_master_terminal (id_area);

ALTER TABLE pro_customer MODIFY prospect_customer_date date default NULL;
ALTER TABLE pro_customer MODIFY fix_customer_since date default NULL;
ALTER TABLE pro_customer MODIFY fix_customer_redate date default NULL;


update pro_customer set prospect_customer_date = NULL 
where id_customer in (
	select * from (
		select id_customer from pro_customer where prospect_customer_date < '0000-01-01'
	) a
);
update pro_customer set fix_customer_since = NULL 
where id_customer in (
	select * from (
		select id_customer from pro_customer where fix_customer_since < '0000-01-01'
	) a
);
update pro_customer set fix_customer_redate = NULL 
where id_customer in (
	select * from (
		select id_customer from pro_customer where fix_customer_redate < '0000-01-01'
	) a
);

ALTER TABLE pro_customer MODIFY nama_customer varchar(300);
ALTER TABLE pro_customer MODIFY alamat_customer varchar(300);
ALTER TABLE pro_customer MODIFY email_customer varchar(300);
ALTER TABLE pro_customer MODIFY tipe_bisnis_lain varchar(300);
ALTER TABLE pro_customer MODIFY ownership_lain varchar(300);
ALTER TABLE pro_customer MODIFY nomor_sertifikat varchar(300);
ALTER TABLE pro_customer MODIFY nomor_sertifikat_file varchar(300);
ALTER TABLE pro_customer MODIFY nomor_npwp varchar(300);
ALTER TABLE pro_customer MODIFY nomor_npwp_file varchar(300);
ALTER TABLE pro_customer MODIFY nomor_siup varchar(300);
ALTER TABLE pro_customer MODIFY nomor_siup_file varchar(300);
ALTER TABLE pro_customer MODIFY nomor_tdp varchar(300);
ALTER TABLE pro_customer MODIFY nomor_tdp_file varchar(300);
ALTER TABLE pro_customer MODIFY dokumen_lainnya varchar(300);
ALTER TABLE pro_customer MODIFY jenis_customer varchar(300);
ALTER TABLE pro_customer MODIFY induk_perusahaan varchar(300);
ALTER TABLE pro_customer MODIFY kecamatan_customer varchar(300);
ALTER TABLE pro_customer MODIFY kelurahan_customer varchar(300);

ALTER TABLE pro_customer_contact MODIFY pic_decision_name varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_decision_position varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_decision_email varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_ordering_name varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_ordering_position varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_ordering_email varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_billing_name varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_billing_position varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_billing_email varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_invoice_name varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_invoice_position varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_invoice_email varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_fuelman_name varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_fuelman_position varchar(300);
ALTER TABLE pro_customer_contact MODIFY pic_fuelman_email varchar(300);
ALTER TABLE pro_customer_contact MODIFY product_delivery_address text;
ALTER TABLE pro_customer_contact MODIFY invoice_delivery_addr_primary varchar(300);
ALTER TABLE pro_customer_contact MODIFY invoice_delivery_addr_secondary varchar(300);

ALTER TABLE pro_customer_payment MODIFY email_billing varchar(300);
ALTER TABLE pro_customer_payment MODIFY alamat_billing varchar(300);
ALTER TABLE pro_customer_payment MODIFY payment_schedule_other varchar(300);
ALTER TABLE pro_customer_payment MODIFY payment_method_other varchar(300);
ALTER TABLE pro_customer_payment MODIFY kecamatan_billing varchar(300);
ALTER TABLE pro_customer_payment MODIFY kelurahan_billing varchar(300);
ALTER TABLE pro_customer_payment MODIFY calculate_method varchar(300);
ALTER TABLE pro_customer_payment MODIFY bank_name varchar(300);
ALTER TABLE pro_customer_payment MODIFY curency varchar(300);
ALTER TABLE pro_customer_payment MODIFY bank_address varchar(300);
ALTER TABLE pro_customer_payment MODIFY account_number varchar(300);
ALTER TABLE pro_customer_payment MODIFY creditor varchar(300);

ALTER TABLE pro_customer_logistik MODIFY logistik_env_other varchar(300);
ALTER TABLE pro_customer_logistik MODIFY logistik_storage_other varchar(300);
ALTER TABLE pro_customer_logistik MODIFY logistik_hour_other varchar(300);
ALTER TABLE pro_customer_logistik MODIFY logistik_volume_other varchar(300);
ALTER TABLE pro_customer_logistik MODIFY logistik_quality_other varchar(300);
ALTER TABLE pro_customer_logistik MODIFY logistik_truck_other varchar(300);


------------------------------------------------------------------------------------------------------------------------------------

/*

01. Ubah label Role CEO menjadi Role COO
02. Tambahkan Role CEO
03. Tambahin No Urut biar liatnya enakan dikit
alter table acl_role add column no_urut integer;

*/
------------------------------------------------------------------------------------------------------------------------------------


CREATE TABLE pro_marketing_report_master (
	id_mkt_report int(11) NOT NULL AUTO_INCREMENT,
	id_customer int(11) DEFAULT NULL,
	pic_customer varchar(100) DEFAULT NULL,
	tanggal datetime DEFAULT NULL,
	kegiatan varchar(200) DEFAULT NULL,
	hasil_kegiatan text DEFAULT NULL,
	status int(11) DEFAULT NULL,
	create_date datetime DEFAULT NULL,
	create_by int(11) DEFAULT NULL,
	create_ip varchar(15) DEFAULT NULL,
	update_date datetime DEFAULT NULL,
	update_by int(11) DEFAULT NULL,
	update_ip varchar(15) DEFAULT NULL,
	PRIMARY KEY (id_mkt_report),
	KEY id_cust_mkt_report (id_customer),
	CONSTRAINT id_cust_mkt_report FOREIGN KEY (id_customer) REFERENCES pro_customer (id_customer)
) ENGINE=InnoDB;


CREATE TABLE pro_marketing_report_master_disposisi (
	id_disposisi int(11) NOT NULL,
	id_mkt_report int(11) NOT NULL,
	disposisi int(11) DEFAULT NULL,
	catatan text DEFAULT NULL,
	tanggal datetime DEFAULT NULL,
	result int(11) DEFAULT NULL,
	pic varchar(200) DEFAULT '',
	create_date datetime DEFAULT NULL,
	create_by int(11) DEFAULT NULL,
	create_ip varchar(15) DEFAULT NULL,
	PRIMARY KEY (id_disposisi,id_mkt_report)
) ENGINE=InnoDB;

CREATE TABLE pro_mapping_spv (
	no_urut int(11) NOT NULL,
	id_spv int(11) NOT NULL,
	id_mkt int(11) NOT NULL,
	PRIMARY KEY (no_urut, id_spv),
	KEY pro_mapping_spv_idx1 (id_spv,id_mkt)
) ENGINE=InnoDB;


CREATE TABLE pro_inventory_vendor_po (
	id_master int(11) NOT NULL AUTO_INCREMENT,
	id_vendor int(11) NOT NULL,
	id_produk int(11) NOT NULL,
	id_area int(11) NOT NULL,
	id_terminal int(11) NOT NULL,
	tanggal_inven date NOT NULL,
	nomor_po varchar(150) DEFAULT NULL,
	awal_inven int(11) NOT NULL DEFAULT 0,
	in_inven int(11) NOT NULL DEFAULT 0,
	out_inven int(11) NOT NULL DEFAULT 0,
	adj_inven int(11) NOT NULL DEFAULT 0,
	harga_tebus int(11) NOT NULL DEFAULT 0,
	created_time datetime NOT NULL,
	created_ip varchar(20) NOT NULL,
	created_by varchar(50) NOT NULL,
	lastupdate_time datetime DEFAULT NULL,
	lastupdate_ip varchar(20) DEFAULT NULL,
	lastupdate_by varchar(50) DEFAULT NULL,
	in_inven_po_detail text DEFAULT NULL,
	is_diterima int(11) DEFAULT 1,
	in_inven_po int(11) DEFAULT NULL,
	PRIMARY KEY (id_master),
	KEY inven_vendor_idx1 (id_vendor),
	KEY inven_vendor_idx2 (id_produk),
	KEY inven_vendor_idx3 (id_area),
	KEY inven_vendor_idx4 (id_terminal),
	KEY inven_vendor_unq1 (id_vendor,id_produk,id_area,id_terminal,tanggal_inven) USING BTREE,
	CONSTRAINT pro_inventory_vendor_po_ibfk_1 FOREIGN KEY (id_vendor) REFERENCES pro_master_vendor (id_master) ON UPDATE CASCADE,
	CONSTRAINT pro_inventory_vendor_po_ibfk_2 FOREIGN KEY (id_produk) REFERENCES pro_master_produk (id_master) ON UPDATE CASCADE,
	CONSTRAINT pro_inventory_vendor_po_ibfk_3 FOREIGN KEY (id_area) REFERENCES pro_master_area (id_master) ON UPDATE CASCADE,
	CONSTRAINT pro_inventory_vendor_po_ibfk_4 FOREIGN KEY (id_terminal) REFERENCES pro_master_terminal (id_master) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4942 DEFAULT CHARSET=latin1;

insert into pro_inventory_vendor_po (select * from pro_inventory_vendor)

CREATE TABLE pro_inventory_depot (
	id_master int(11) NOT NULL AUTO_INCREMENT,
	id_datanya varchar(255) NOT NULL,
	id_jenis int(11) NOT NULL,
	id_produk int(11) NOT NULL,
	id_terminal int(11) NOT NULL,
	id_vendor int(11),
	id_po_supplier int(11),
	id_inven_vendor int(11),
	tanggal_inven date NOT NULL,
	awal_inven int(11) NOT NULL DEFAULT '0',
	in_inven int(11) NOT NULL DEFAULT '0',
	out_inven int(11) NOT NULL DEFAULT '0',
	adj_inven int(11) NOT NULL DEFAULT '0',
	out_inven_virtual int(11) NOT NULL DEFAULT '0',
	keterangan text,
	created_time datetime NOT NULL,
	created_ip varchar(20) NOT NULL,
	created_by varchar(255) NOT NULL,
	lastupdate_time datetime DEFAULT NULL,
	lastupdate_ip varchar(20) DEFAULT NULL,
	lastupdate_by varchar(255) DEFAULT NULL,
	PRIMARY KEY (id_master),
	KEY pro_inventory_depot_idx1 (id_terminal),
	KEY pro_inventory_depot_idx2 (id_produk),
	KEY pro_inventory_depot_idx3 (id_vendor),
	CONSTRAINT pro_inventory_depot_fk1 FOREIGN KEY (id_terminal) REFERENCES pro_master_terminal (id_master) ON UPDATE CASCADE,
	CONSTRAINT pro_inventory_depot_fk2 FOREIGN KEY (id_produk) REFERENCES pro_master_produk (id_master) ON UPDATE CASCADE
) ENGINE=InnoDB;



------------------------------------------------------------------------------------------------------------------------------------

/* Benerin flag disposisi */
update pro_penawaran set flag_disposisi = 6 where flag_disposisi = 5;
update pro_penawaran set flag_disposisi = 6 where id_penawaran in (
	select * from (
		select id_penawaran from pro_penawaran 
		where flag_disposisi = 4 and ceo_result > 0
	) a
);

select distinct pic_approval from pro_penawaran 
where flag_disposisi = 4 and pic_approval != 0 
	and pic_approval not in (select id_user from acl_user where id_role = 6);

select id_role from acl_user where id_user in (
	94, 135, 151, 162, 167, 320, 321					
);

update pro_penawaran set flag_disposisi = 3 where id_penawaran in (
	select * from (
		select id_penawaran from pro_penawaran 
		where flag_disposisi = 4 and pic_approval != 0 
			and pic_approval in (94)
	) a
);


update pro_penawaran set flag_disposisi = 3 where id_penawaran in (
	select a.id_penawaran from (
		select c.id_user, c.username, c.fullname, c.id_role, d.role_name, 
		b.nama_customer, a.* 
		from pro_penawaran a 
		left join pro_customer b on a.id_customer = b.id_customer 
		left join acl_user c on b.id_marketing = c.id_user 
		left join acl_role d on c.id_role = d.id_role 
		where a.flag_disposisi = 2
	) a
);

update pro_penawaran set flag_disposisi = 3 where id_penawaran in (
	select a.id_penawaran from (
		select c.id_user, c.username, c.fullname, c.id_role, d.role_name, 
		b.nama_customer, a.* 
		from pro_penawaran a 
		left join pro_customer b on a.id_customer = b.id_customer 
		left join acl_user c on b.id_marketing = c.id_user 
		left join acl_role d on c.id_role = d.id_role 
		where a.flag_disposisi = 1 and flag_approval > 0
	) a
);
/* Benerin flag disposisi */

------------------------------------------------------------------------------------------------------------------------------------
ALTER TABLE pro_customer_review MODIFY review1 varchar(500);
ALTER TABLE pro_customer_review MODIFY review2 varchar(500);
ALTER TABLE pro_customer_review MODIFY review3 varchar(500);
ALTER TABLE pro_customer_review MODIFY review4 varchar(500);
ALTER TABLE pro_customer_review MODIFY review5 varchar(500);

ALTER TABLE pro_customer_review_attchment MODIFY review_attach varchar(500);
ALTER TABLE pro_customer_review_attchment MODIFY review_attach_ori varchar(500);

ALTER TABLE pro_customer_verification MODIFY legal_tgl_proses datetime default NULL;
ALTER TABLE pro_customer_verification MODIFY finance_tgl_proses datetime default NULL;
ALTER TABLE pro_customer_verification MODIFY logistik_tgl_proses datetime default NULL;
ALTER TABLE pro_customer_verification MODIFY sm_tgl_proses datetime default NULL;
ALTER TABLE pro_customer_verification MODIFY om_tgl_proses datetime default NULL;
ALTER TABLE pro_customer_verification MODIFY cfo_tgl_proses datetime default NULL;
ALTER TABLE pro_customer_verification MODIFY ceo_tgl_proses datetime default NULL;


ALTER TABLE pro_master_cabang ADD COLUMN urut_oslog integer default 0;
ALTER TABLE pro_po_detail ADD COLUMN nomor_oslog varchar(50);

ALTER TABLE acl_user ADD COLUMN last_login_time datetime default NULL;
ALTER TABLE pro_inventory_vendor_po ADD COLUMN is_selesai integer default 0;
ALTER TABLE pro_peminjaman_mobil ADD COLUMN last_km decimal(10,2) default 0;
ALTER TABLE pro_peminjaman_mobil ADD COLUMN bensin integer default 0;

------------------------------------------------------------------------------------------------------------------------------------

ALTER TABLE pro_customer_verification ADD COLUMN jenis_datanya integer default 0;
ALTER TABLE pro_customer_verification ADD COLUMN finance_data_kyc text default NULL; 

CREATE TABLE pro_customer_admin_arnya (
	id_arnya integer NOT NULL AUTO_INCREMENT, 
	id_customer integer NOT NULL, 
	not_yet numeric(22, 2) DEFAULT 0, 
	ov_up_07 numeric(22, 2) DEFAULT 0, 
	ov_under_30 numeric(22, 2) DEFAULT 0, 
	ov_under_60 numeric(22, 2) DEFAULT 0, 
	ov_under_90 numeric(22, 2) DEFAULT 0, 
	ov_up_90 numeric(22, 2) DEFAULT 0, 
	PRIMARY KEY (id_arnya),
	KEY pro_customer_admin_arnya_idx1 (id_customer),
	CONSTRAINT pro_customer_admin_arnya_fk1 FOREIGN KEY (id_customer) REFERENCES pro_customer (id_customer) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE pro_sales_confirmation ADD COLUMN credit_limit varchar(50);
ALTER TABLE pro_sales_confirmation ADD COLUMN ov_up_07 varchar(50);
ALTER TABLE pro_sales_confirmation ADD COLUMN lampiran_unblock varchar(250);
ALTER TABLE pro_sales_confirmation ADD COLUMN lampiran_unblock_ori text;

update pro_pr set disposisi_pr = 7 where id_pr in (
	select a.id_pr from (
		select id_pr 
		from pro_pr a where disposisi_pr = 6 
	) a
);

update pro_pr set disposisi_pr = 6 where id_pr in (
	select a.id_pr from (
		select id_pr 
		from pro_pr a where disposisi_pr = 5 
	) a
);

update pro_pr set disposisi_pr = 5 where id_pr in (
	select a.id_pr from (
		select id_pr 
		from pro_pr a where disposisi_pr = 4 and is_ceo = 1
	) a
);
ALTER TABLE pro_pr ADD COLUMN coo_result tinyint default 0;
ALTER TABLE pro_pr ADD COLUMN coo_pic varchar(80) default NULL;
ALTER TABLE pro_pr ADD COLUMN coo_summary text default NULL;
ALTER TABLE pro_pr ADD COLUMN coo_tanggal datetime default NULL;

CREATE TABLE pro_invoice_admin(
	id_invoice integer NOT NULL AUTO_INCREMENT, 
	id_customer integer NOT NULL, 
	no_invoice varchar(250), 
	tgl_invoice date NOT NULL, 
	tgl_kirim_awal date NOT NULL, 
	tgl_kirim_akhir date NOT NULL, 
	total_invoice numeric(22, 2) DEFAULT 0, 
	total_bayar numeric(22, 2) DEFAULT 0, 
	created_time datetime NOT NULL,
	created_ip varchar(20) NOT NULL,
	created_by varchar(250) NOT NULL,
	lastupdate_time datetime DEFAULT NULL,
	lastupdate_ip varchar(20) DEFAULT NULL,
	lastupdate_by varchar(250) DEFAULT NULL,
	PRIMARY KEY (id_invoice),
	KEY pro_invoice_admin_idx1 (id_customer),
	CONSTRAINT pro_invoice_admin_fk1 FOREIGN KEY (id_customer) REFERENCES pro_customer (id_customer) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE pro_invoice_admin_detail(
	id_invoice_detail integer NOT NULL, 
	id_invoice integer NOT NULL, 
	id_dsd integer NOT NULL, 
	tgl_delivered date NOT NULL, 
	vol_kirim numeric(22, 2) DEFAULT 0, 
	harga_kirim numeric(22, 2) DEFAULT 0, 
	jenisnya varchar(250), 
	PRIMARY KEY (id_invoice_detail, id_invoice),
	KEY pro_invoice_admin_detail_idx1 (id_invoice),
	CONSTRAINT pro_invoice_admin_detail_fk1 FOREIGN KEY (id_invoice) REFERENCES pro_invoice_admin (id_invoice) ON UPDATE CASCADE ON DELETE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE pro_invoice_admin_detail_bayar(
	id_invoice_bayar integer NOT NULL, 
	id_invoice integer NOT NULL, 
	tgl_bayar date DEFAULT NULL,
	jumlah_bayar numeric(22, 2) DEFAULT 0.00, 
	PRIMARY KEY (id_invoice_bayar, id_invoice),
	KEY pro_invoice_admin_detail_bayar_idx1 (id_invoice),
	CONSTRAINT pro_invoice_admin_detail_bayar_fk1 FOREIGN KEY (id_invoice) REFERENCES pro_invoice_admin (id_invoice) ON UPDATE CASCADE ON DELETE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE pro_invoice_admin ADD COLUMN status_ar varchar(20);
ALTER TABLE pro_penawaran ADD COLUMN gabung_oa integer default 0;

------------------------------------------------------------------------------------------------------------------------------------

ALTER TABLE pro_master_area MODIFY lampiran text default NULL;
ALTER TABLE pro_master_area MODIFY lampiran_ori text default NULL;

ALTER TABLE pro_master_ruangan ADD COLUMN attach_foto text;
ALTER TABLE pro_master_ruangan ADD COLUMN attach_foto_ori text;
ALTER TABLE pro_master_mobil ADD COLUMN attach_foto text;
ALTER TABLE pro_master_mobil ADD COLUMN attach_foto_ori text;


ALTER TABLE pro_po_customer_plan ADD COLUMN splitted_from_plan integer;
ALTER TABLE pro_po_customer_plan ADD COLUMN vol_ori_plan integer;

ALTER TABLE pro_customer_update ADD COLUMN kategori integer default 0;
ALTER TABLE pro_master_transportir ADD COLUMN owner_suplier integer default 0;

/*
delete from pro_pr where finance_result = 0 and tanggal_pr < '2023-01-01';
delete from pro_pr_ar where id_pr in (select id_pr from pro_pr where purchasing_result = 0 and tanggal_pr < '2023-01-01'); 
delete from pro_pr where purchasing_result = 0 and tanggal_pr < '2023-01-01';



*/

ALTER TABLE pro_manual_segel ADD COLUMN kategori integer default 0;
ALTER TABLE pro_po_detail ADD COLUMN ongkos_po_real integer default 0;
update pro_po_detail set ongkos_po_real = ongkos_po;

------------------------------------------------------------------------------------------------------------------------------------

CREATE TABLE pro_segel_detail(
	id_det_segel bigint NOT NULL AUTO_INCREMENT, 
	id_dsd integer NOT NULL, 
	jenis integer NOT NULL, 
	pre_segel varchar(15) NOT NULL,
	nomor_segel integer NOT NULL,
	is_used integer NOT NULL DEFAULT 1, 
	is_rusak integer NOT NULL DEFAULT 0, 
	PRIMARY KEY (id_det_segel), 
	KEY pro_segel_detail_idx1 (id_dsd, jenis, is_used, is_rusak) 
) ENGINE=InnoDB DEFAULT CHARSET=latin1;