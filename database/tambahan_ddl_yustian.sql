ALTER TABLE pro_inventory_vendor ADD COLUMN in_inven_po int;
ALTER TABLE pro_inventory_vendor ADD COLUMN is_diterima int DEFAULT 1;

alter table pro_pr_detail add column splitted_from_pr integer;
alter table pro_pr_detail add column vol_ori_pr integer;

ALTER TABLE pro_inventory_vendor ADD COLUMN in_inven_po_detail text;

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

ALTER TABLE pro_penawaran ADD COLUMN spv_summary text;
ALTER TABLE pro_penawaran ADD COLUMN spv_pic varchar(50);
ALTER TABLE pro_penawaran ADD COLUMN spv_tanggal datetime;
ALTER TABLE pro_penawaran ADD COLUMN spv_result tinyint;
ALTER TABLE pro_penawaran ADD COLUMN spv_flag_disposisi tinyint;


CREATE TABLE `pro_marketing_report_master` (
  `id_mkt_report` int(11) NOT NULL AUTO_INCREMENT,
  `id_customer` int(11) DEFAULT NULL,
  `pic_customer` varchar(100) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `kegiatan` varchar(200) DEFAULT NULL,
  `hasil_kegiatan` text DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `create_by` int(11) DEFAULT NULL,
  `create_ip` varchar(15) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `update_by` int(11) DEFAULT NULL,
  `update_ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id_mkt_report`),
  KEY `id_cust_mkt_report` (`id_customer`),
  CONSTRAINT `id_cust_mkt_report` FOREIGN KEY (`id_customer`) REFERENCES `pro_customer` (`id_customer`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `pro_marketing_report_master_disposisi` (
  `id_disposisi` int(11) NOT NULL,
  `id_mkt_report` int(11) NOT NULL,
  `disposisi` int(11) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `result` int(11) DEFAULT NULL,
  `pic` varchar(200) DEFAULT '',
  `create_date` datetime DEFAULT NULL,
  `create_by` int(11) DEFAULT NULL,
  `create_ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id_disposisi`,`id_mkt_report`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;







