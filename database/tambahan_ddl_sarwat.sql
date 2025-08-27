/*

01. Ubah label Role CEO menjadi Role COO
02. Tambahkan Role CEO
03. Tambahin No Urut biar liatnya enakan dikit
alter table acl_role add column no_urut integer;
alter table pro_master_harga_minyak add column harga_ceo integer default 0;
alter table pro_master_harga_minyak add column harga_coo integer default 0;




*/

alter table pro_pr_detail add column splitted_from integer;

alter table pro_po add column catatan_selisih_mgrlog text;
alter table pro_po add column selisih_approved_mgrlog datetime default null;
alter table pro_po_detail add column oa_result_mgrlog tinyint default 0;
alter table pro_po_detail add column oa_pic_mgrlog varchar(50) default null;
alter table pro_po_detail add column oa_tanggal_mgrlog datetime default null;

alter table pro_pr_detail add column nomor_do_accurate varchar(80);

ALTER TABLE pro_inventory_vendor ADD COLUMN in_inven_po int;
ALTER TABLE pro_inventory_vendor ADD COLUMN is_diterima int DEFAULT 1;

alter table pro_pr_detail add column splitted_from_pr integer;
alter table pro_pr_detail add column vol_ori_pr integer;

ALTER TABLE pro_inventory_vendor ADD COLUMN in_inven_po_detail text;

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
) ENGINE=InnoDB AUTO_INCREMENT=3732 DEFAULT CHARSET=latin1;

insert into pro_inventory_vendor_po (select * from pro_inventory_vendor)

ALTER TABLE pro_penawaran ADD COLUMN kalkulasi_oa varchar(255);
ALTER TABLE pro_penawaran ADD COLUMN pembulatan tinyint default 1;

-------------------------------------------------------------------------------------------------------------------

ALTER TABLE pro_master_terminal ADD COLUMN id_cabang integer;
ALTER TABLE pro_master_terminal ADD COLUMN id_area integer;
DROP INDEX pro_master_terminal_idx1 ON pro_master_terminal;
CREATE INDEX pro_master_terminal_idx1 ON pro_master_terminal (id_cabang);
DROP INDEX pro_master_terminal_idx2 ON pro_master_terminal;
CREATE INDEX pro_master_terminal_idx2 ON pro_master_terminal (id_area);

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


CREATE TABLE pro_mapping_spv (
	no_urut int(11) NOT NULL,
	id_spv int(11) NOT NULL,
	id_mkt int(11) NOT NULL,
	PRIMARY KEY (no_urut, id_spv),
	KEY pro_mapping_spv_idx1 (id_spv,id_mkt)
) ENGINE=InnoDB;


ALTER TABLE pro_penawaran ADD COLUMN spv_mkt_summary text default NULL;
ALTER TABLE pro_penawaran ADD COLUMN spv_mkt_pic varchar(80) default NULL;
ALTER TABLE pro_penawaran ADD COLUMN spv_mkt_tanggal datetime default NULL;
ALTER TABLE pro_penawaran ADD COLUMN spv_mkt_result tinyint default 0;

ALTER TABLE pro_penawaran ADD COLUMN coo_summary text default NULL;
ALTER TABLE pro_penawaran ADD COLUMN coo_pic varchar(80) default NULL;
ALTER TABLE pro_penawaran ADD COLUMN coo_tanggal datetime default NULL;
ALTER TABLE pro_penawaran ADD COLUMN coo_result tinyint default 0;

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


update pro_penawaran set flag_disposisi = 6 where flag_disposisi = 4;
update pro_penawaran set flag_disposisi = 4 where flag_disposisi = 3;
update pro_penawaran set flag_disposisi = 3 where flag_disposisi = 2;
update pro_penawaran set flag_disposisi = 2 where flag_disposisi = 1;



