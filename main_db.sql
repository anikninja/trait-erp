# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.5-10.4.13-MariaDB)
# Database: hasan_retail_erp
# Generation Time: 2020-08-15 12:41:31 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table rerp_addresses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_addresses`;

CREATE TABLE `rerp_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `line1` varchar(50) NOT NULL,
  `line2` varchar(50) DEFAULT NULL,
  `city` varchar(25) NOT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `state` varchar(25) NOT NULL,
  `country` varchar(50) NOT NULL,
  `area` int(11) DEFAULT NULL,
  `zone` int(11) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_adjustment_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_adjustment_items`;

CREATE TABLE `rerp_adjustment_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `adjustment_id` (`adjustment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_adjustments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_adjustments`;

CREATE TABLE `rerp_adjustments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reference_no` varchar(55) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `count_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_brands
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_brands`;

CREATE TABLE `rerp_brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `image` varchar(50) DEFAULT NULL,
  `slug` varchar(55) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_calendar
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_calendar`;

CREATE TABLE `rerp_calendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(55) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `color` varchar(7) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_captcha
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_captcha`;

CREATE TABLE `rerp_captcha` (
  `captcha_id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `captcha_time` int(10) unsigned NOT NULL,
  `ip_address` varchar(16) CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `word` varchar(20) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`captcha_id`),
  KEY `word` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_categories`;

CREATE TABLE `rerp_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(55) NOT NULL,
  `name` varchar(55) NOT NULL,
  `image` varchar(55) DEFAULT NULL,
  `parent_id` int(11) DEFAULT 0,
  `slug` varchar(55) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `featured` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `featured` (`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_categories` WRITE;
/*!40000 ALTER TABLE `rerp_categories` DISABLE KEYS */;

INSERT INTO `rerp_categories` (`id`, `code`, `name`, `image`, `parent_id`, `slug`, `description`, `featured`)
VALUES
	(1,'C1','Category 1',NULL,NULL,'category-1',NULL, 0);

/*!40000 ALTER TABLE `rerp_categories` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_combo_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_combo_items`;

CREATE TABLE `rerp_combo_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `item_code` varchar(20) NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `unit_price` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_companies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_companies`;

CREATE TABLE `rerp_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned DEFAULT NULL,
  `group_name` varchar(20) NOT NULL,
  `customer_group_id` int(11) DEFAULT NULL,
  `customer_group_name` varchar(100) DEFAULT NULL,
  `name` varchar(55) NOT NULL,
  `company` varchar(255) NOT NULL,
  `vat_no` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(55) DEFAULT NULL,
  `state` varchar(55) DEFAULT NULL,
  `postal_code` varchar(8) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `cf1` varchar(100) DEFAULT NULL,
  `cf2` varchar(100) DEFAULT NULL,
  `cf3` varchar(100) DEFAULT NULL,
  `cf4` varchar(100) DEFAULT NULL,
  `cf5` varchar(100) DEFAULT NULL,
  `cf6` varchar(100) DEFAULT NULL,
  `invoice_footer` text DEFAULT NULL,
  `payment_term` int(11) DEFAULT 0,
  `logo` varchar(255) DEFAULT 'logo.png',
  `award_points` int(11) DEFAULT 0,
  `deposit_amount` decimal(25,4) DEFAULT NULL,
  `price_group_id` int(11) DEFAULT NULL,
  `price_group_name` varchar(50) DEFAULT NULL,
  `gst_no` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `group_id_2` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_companies` WRITE;
/*!40000 ALTER TABLE `rerp_companies` DISABLE KEYS */;

INSERT INTO `rerp_companies` (`id`, `group_id`, `group_name`, `customer_group_id`, `customer_group_name`, `name`, `company`, `vat_no`, `address`, `city`, `state`, `postal_code`, `country`, `phone`, `email`, `cf1`, `cf2`, `cf3`, `cf4`, `cf5`, `cf6`, `invoice_footer`, `payment_term`, `logo`, `award_points`, `deposit_amount`, `price_group_id`, `price_group_name`, `gst_no`)
VALUES
	(1,3,'customer',1,'General','Walk-in Customer','Walk-in Customer','','Customer Address','Petaling Jaya','Selangor','46000','Malaysia','0123456789','customer@tecdiary.com','','','','','','',NULL,0,'logo.png',0,NULL,NULL,NULL,NULL),
	(2,4,'supplier',NULL,NULL,'Test Supplier','Supplier Company Name',NULL,'Supplier Address','Petaling Jaya','Selangor','46050','Malaysia','0123456789','supplier@tecdiary.com','-','-','-','-','-','-',NULL,0,'logo.png',0,NULL,NULL,NULL,NULL),
	(3,NULL,'biller',NULL,NULL,'Mian Saleem','Test Biller','5555','Biller adddress','City','','','Country','012345678','saleem@tecdiary.com','','','','','','',' Thank you for shopping with us. Please come again',0,'logo1.png',0,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `rerp_companies` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_costing
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_costing`;

CREATE TABLE `rerp_costing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `sale_item_id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `purchase_item_id` int(11) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `purchase_net_unit_cost` decimal(25,4) DEFAULT NULL,
  `purchase_unit_cost` decimal(25,4) DEFAULT NULL,
  `sale_net_unit_price` decimal(25,4) NOT NULL,
  `sale_unit_price` decimal(25,4) NOT NULL,
  `quantity_balance` decimal(15,4) DEFAULT NULL,
  `inventory` tinyint(1) DEFAULT 0,
  `overselling` tinyint(1) DEFAULT 0,
  `option_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_currencies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_currencies`;

CREATE TABLE `rerp_currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL,
  `name` varchar(55) NOT NULL,
  `rate` decimal(12,4) NOT NULL,
  `auto_update` tinyint(1) NOT NULL DEFAULT 0,
  `symbol` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_currencies` WRITE;
/*!40000 ALTER TABLE `rerp_currencies` DISABLE KEYS */;

INSERT INTO `rerp_currencies` (`id`, `code`, `name`, `rate`, `auto_update`, `symbol`)
VALUES
	(1,'USD','US Dollar',1.0000,0,NULL),
	(2,'EUR','EURO',0.7340,0,NULL);

/*!40000 ALTER TABLE `rerp_currencies` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_customer_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_customer_groups`;

CREATE TABLE `rerp_customer_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `percent` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_customer_groups` WRITE;
/*!40000 ALTER TABLE `rerp_customer_groups` DISABLE KEYS */;

INSERT INTO `rerp_customer_groups` (`id`, `name`, `percent`)
VALUES
	(1,'General',0),
	(2,'Reseller',-5),
	(3,'Distributor',-15),
	(4,'New Customer (+10)',10);

/*!40000 ALTER TABLE `rerp_customer_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_date_format
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_date_format`;

CREATE TABLE `rerp_date_format` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `js` varchar(20) NOT NULL,
  `php` varchar(20) NOT NULL,
  `sql` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_date_format` WRITE;
/*!40000 ALTER TABLE `rerp_date_format` DISABLE KEYS */;

INSERT INTO `rerp_date_format` (`id`, `js`, `php`, `sql`)
VALUES
	(1,'mm-dd-yyyy','m-d-Y','%m-%d-%Y'),
	(2,'mm/dd/yyyy','m/d/Y','%m/%d/%Y'),
	(3,'mm.dd.yyyy','m.d.Y','%m.%d.%Y'),
	(4,'dd-mm-yyyy','d-m-Y','%d-%m-%Y'),
	(5,'dd/mm/yyyy','d/m/Y','%d/%m/%Y'),
	(6,'dd.mm.yyyy','d.m.Y','%d.%m.%Y');

/*!40000 ALTER TABLE `rerp_date_format` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_deliveries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_deliveries`;

CREATE TABLE `rerp_deliveries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `sale_id` int(11) NOT NULL,
  `do_reference_no` varchar(50) NOT NULL,
  `sale_reference_no` varchar(50) NOT NULL,
  `customer` varchar(55) NOT NULL,
  `address` varchar(1000) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `status` varchar(15) DEFAULT NULL,
  `attachment` varchar(50) DEFAULT NULL,
  `delivered_by` varchar(50) DEFAULT NULL,
  `received_by` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_shipping_zones
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_shipping_zones`;

CREATE TABLE `rerp_shipping_zones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `continent` varchar(200) NOT NULL,
  `country` varchar(200) NOT NULL,
  `state` varchar(200) NOT NULL,
  `city` varchar(200) NOT NULL,
  `zip` varchar(200) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `order` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `country` (`country`),
  KEY `state` (`state`),
  KEY `city` (`city`),
  KEY `zip` (`zip`),
  UNIQUE KEY `zone_country_state_city_zip` (`country`,`state`,`city`,`zip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_shipping_zone_methods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_shipping_zone_methods`;

CREATE TABLE `rerp_shipping_zone_methods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `zone_id` int(11) NOT NULL,
  `method_id` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `cost` decimal(25,4) DEFAULT 0,
  `method_order` int(11) unsigned NOT NULL DEFAULT 0,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_shipping_zone_areas
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_shipping_zone_areas`;

CREATE TABLE `rerp_shipping_zone_areas` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `zone_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `cost_adjustment` decimal(25,4) DEFAULT 0,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `area_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_shipping_area_slots
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_shipping_area_slots`;

CREATE TABLE `rerp_shipping_area_slots` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `area_id` int(11) unsigned NOT NULL,
  `name` varchar(200) NOT NULL,
  `start_at` time NOT NULL,
  `end_at` time NOT NULL,
  `max_order` int(11) unsigned NOT NULL,
  `cost_adjustment` decimal(25,4) DEFAULT 0,
  `close_before` time NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `slot_start_end` (`start_at`,`end_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_delivery_schedules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_delivery_schedules`;

CREATE TABLE `rerp_delivery_schedules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `scheduled_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `start` timestamp NOT NULL,
  `end` timestamp NOT NULL,
  `slot_id` int(11) unsigned NOT NULL,
  `zone_id` int(11) unsigned NOT NULL,
  `area_id` int(11) unsigned NOT NULL,
  `sales_id` int(11) unsigned,
  `delivery_id` int(11) unsigned,
  PRIMARY KEY (`id`),
  KEY `slot_zone_area` (`slot_id`,`zone_id`,`area_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_deposits
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_deposits`;

CREATE TABLE `rerp_deposits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) NOT NULL,
  `amount` decimal(25,4) NOT NULL,
  `paid_by` varchar(50) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_expense_categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_expense_categories`;

CREATE TABLE `rerp_expense_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(55) NOT NULL,
  `name` varchar(55) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_expenses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_expenses`;

CREATE TABLE `rerp_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reference` varchar(50) NOT NULL,
  `amount` decimal(25,4) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `created_by` varchar(55) NOT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_gift_card_topups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_gift_card_topups`;

CREATE TABLE `rerp_gift_card_topups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `card_id` int(11) NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `card_id` (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_gift_cards
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_gift_cards`;

CREATE TABLE `rerp_gift_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `card_no` varchar(20) NOT NULL,
  `value` decimal(25,4) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer` varchar(255) DEFAULT NULL,
  `balance` decimal(25,4) NOT NULL,
  `expiry` date DEFAULT NULL,
  `created_by` varchar(55) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `card_no` (`card_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_groups`;

CREATE TABLE `rerp_groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_groups` WRITE;
/*!40000 ALTER TABLE `rerp_groups` DISABLE KEYS */;

INSERT INTO `rerp_groups` (`id`, `name`, `description`)
VALUES
	(1,'owner','Owner'),
	(2,'admin','Administrator'),
	(3,'customer','Customer'),
	(4,'supplier','Supplier'),
	(5,'sales','Sales Staff');

/*!40000 ALTER TABLE `rerp_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_login_attempts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_login_attempts`;

CREATE TABLE `rerp_login_attempts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varbinary(16) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_migrations`;

CREATE TABLE `rerp_migrations` (
  `version` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_migrations` WRITE;
/*!40000 ALTER TABLE `rerp_migrations` DISABLE KEYS */;

INSERT INTO `rerp_migrations` (`version`)
VALUES
	(315);

/*!40000 ALTER TABLE `rerp_migrations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_notifications
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_notifications`;

CREATE TABLE `rerp_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `from_date` datetime DEFAULT NULL,
  `till_date` datetime DEFAULT NULL,
  `scope` tinyint(1) NOT NULL DEFAULT 3,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_order_ref
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_order_ref`;

CREATE TABLE `rerp_order_ref` (
  `ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `so` int(11) NOT NULL DEFAULT 1,
  `qu` int(11) NOT NULL DEFAULT 1,
  `po` int(11) NOT NULL DEFAULT 1,
  `to` int(11) NOT NULL DEFAULT 1,
  `pos` int(11) NOT NULL DEFAULT 1,
  `do` int(11) NOT NULL DEFAULT 1,
  `pay` int(11) NOT NULL DEFAULT 1,
  `re` int(11) NOT NULL DEFAULT 1,
  `rep` int(11) NOT NULL DEFAULT 1,
  `ex` int(11) NOT NULL DEFAULT 1,
  `ppay` int(11) NOT NULL DEFAULT 1,
  `qa` int(11) DEFAULT 1,
  PRIMARY KEY (`ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_order_ref` WRITE;
/*!40000 ALTER TABLE `rerp_order_ref` DISABLE KEYS */;

INSERT INTO `rerp_order_ref` (`ref_id`, `date`, `so`, `qu`, `po`, `to`, `pos`, `do`, `pay`, `re`, `rep`, `ex`, `ppay`, `qa`)
VALUES
	(1,'2015-03-01',1,1,1,1,1,1,1,1,1,1,1,1);

/*!40000 ALTER TABLE `rerp_order_ref` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_payments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_payments`;

CREATE TABLE `rerp_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NULL DEFAULT current_timestamp(),
  `sale_id` int(11) DEFAULT NULL,
  `return_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `reference_no` varchar(50) NOT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `paid_by` varchar(20) NOT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `cc_no` varchar(20) DEFAULT NULL,
  `cc_holder` varchar(25) DEFAULT NULL,
  `cc_month` varchar(2) DEFAULT NULL,
  `cc_year` varchar(4) DEFAULT NULL,
  `cc_type` varchar(20) DEFAULT NULL,
  `amount` decimal(25,4) NOT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `pos_paid` decimal(25,4) DEFAULT 0.0000,
  `pos_balance` decimal(25,4) DEFAULT 0.0000,
  `approval_code` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_paypal
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_paypal`;

CREATE TABLE `rerp_paypal` (
  `id` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `account_email` varchar(255) NOT NULL,
  `paypal_currency` varchar(3) NOT NULL DEFAULT 'USD',
  `fixed_charges` decimal(25,4) NOT NULL DEFAULT 2.0000,
  `extra_charges_my` decimal(25,4) NOT NULL DEFAULT 3.9000,
  `extra_charges_other` decimal(25,4) NOT NULL DEFAULT 4.4000,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_paypal` WRITE;
/*!40000 ALTER TABLE `rerp_paypal` DISABLE KEYS */;

INSERT INTO `rerp_paypal` (`id`, `active`, `account_email`, `paypal_currency`, `fixed_charges`, `extra_charges_my`, `extra_charges_other`)
VALUES
	(1,1,'mypaypal@paypal.com','USD',0.0000,0.0000,0.0000);

/*!40000 ALTER TABLE `rerp_paypal` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_sslcommerz
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_sslcommerz`;

CREATE TABLE `rerp_sslcommerz` (
  `id` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `store_id` varchar( 255 ) NOT NULL,
  `store_password` varchar( 255 ) NOT NULL,
  `merchant_id` varchar( 255 ) NOT NULL,
  `account_email` varchar(255) NOT NULL,
  `sslcommerz_currency` varchar(3) NOT NULL DEFAULT 'BDT',
  `fixed_charges` decimal(25,4) NOT NULL DEFAULT 0.0000,
  `extra_charges_my` decimal(25,4) NOT NULL DEFAULT 0.0000,
  `extra_charges_other` decimal(25,4) NOT NULL DEFAULT 0.0000,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_sslcommerz` WRITE;
/*!40000 ALTER TABLE `rerp_sslcommerz` DISABLE KEYS */;

INSERT INTO `rerp_sslcommerz` (`id`, `active`, `store_id`, `store_password`, `merchant_id`, `account_email`, `sslcommerz_currency`, `fixed_charges`, `extra_charges_my`, `extra_charges_other`)
VALUES
	(1,1,'testbox', 'qwerty@ssl', 'test3f1acf587accc', 'support@sslcommerz.com','BDT',0.0000,0.0000,0.0000);

/*!40000 ALTER TABLE `rerp_sslcommerz` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_permissions`;

CREATE TABLE `rerp_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `products-index` tinyint(1) DEFAULT 0,
  `products-add` tinyint(1) DEFAULT 0,
  `products-edit` tinyint(1) DEFAULT 0,
  `products-delete` tinyint(1) DEFAULT 0,
  `products-cost` tinyint(1) DEFAULT 0,
  `products-price` tinyint(1) DEFAULT 0,
  `quotes-index` tinyint(1) DEFAULT 0,
  `quotes-add` tinyint(1) DEFAULT 0,
  `quotes-edit` tinyint(1) DEFAULT 0,
  `quotes-pdf` tinyint(1) DEFAULT 0,
  `quotes-email` tinyint(1) DEFAULT 0,
  `quotes-delete` tinyint(1) DEFAULT 0,
  `sales-index` tinyint(1) DEFAULT 0,
  `sales-add` tinyint(1) DEFAULT 0,
  `sales-edit` tinyint(1) DEFAULT 0,
  `sales-pdf` tinyint(1) DEFAULT 0,
  `sales-email` tinyint(1) DEFAULT 0,
  `sales-delete` tinyint(1) DEFAULT 0,
  `purchases-index` tinyint(1) DEFAULT 0,
  `purchases-add` tinyint(1) DEFAULT 0,
  `purchases-edit` tinyint(1) DEFAULT 0,
  `purchases-pdf` tinyint(1) DEFAULT 0,
  `purchases-email` tinyint(1) DEFAULT 0,
  `purchases-delete` tinyint(1) DEFAULT 0,
  `transfers-index` tinyint(1) DEFAULT 0,
  `transfers-add` tinyint(1) DEFAULT 0,
  `transfers-edit` tinyint(1) DEFAULT 0,
  `transfers-pdf` tinyint(1) DEFAULT 0,
  `transfers-email` tinyint(1) DEFAULT 0,
  `transfers-delete` tinyint(1) DEFAULT 0,
  `customers-index` tinyint(1) DEFAULT 0,
  `customers-add` tinyint(1) DEFAULT 0,
  `customers-edit` tinyint(1) DEFAULT 0,
  `customers-delete` tinyint(1) DEFAULT 0,
  `suppliers-index` tinyint(1) DEFAULT 0,
  `suppliers-add` tinyint(1) DEFAULT 0,
  `suppliers-edit` tinyint(1) DEFAULT 0,
  `suppliers-delete` tinyint(1) DEFAULT 0,
  `sales-deliveries` tinyint(1) DEFAULT 0,
  `sales-add_delivery` tinyint(1) DEFAULT 0,
  `sales-edit_delivery` tinyint(1) DEFAULT 0,
  `sales-delete_delivery` tinyint(1) DEFAULT 0,
  `sales-email_delivery` tinyint(1) DEFAULT 0,
  `sales-pdf_delivery` tinyint(1) DEFAULT 0,
  `sales-gift_cards` tinyint(1) DEFAULT 0,
  `sales-add_gift_card` tinyint(1) DEFAULT 0,
  `sales-edit_gift_card` tinyint(1) DEFAULT 0,
  `sales-delete_gift_card` tinyint(1) DEFAULT 0,
  `pos-index` tinyint(1) DEFAULT 0,
  `sales-return_sales` tinyint(1) DEFAULT 0,
  `reports-index` tinyint(1) DEFAULT 0,
  `reports-warehouse_stock` tinyint(1) DEFAULT 0,
  `reports-quantity_alerts` tinyint(1) DEFAULT 0,
  `reports-expiry_alerts` tinyint(1) DEFAULT 0,
  `reports-products` tinyint(1) DEFAULT 0,
  `reports-daily_sales` tinyint(1) DEFAULT 0,
  `reports-monthly_sales` tinyint(1) DEFAULT 0,
  `reports-sales` tinyint(1) DEFAULT 0,
  `reports-payments` tinyint(1) DEFAULT 0,
  `reports-purchases` tinyint(1) DEFAULT 0,
  `reports-profit_loss` tinyint(1) DEFAULT 0,
  `reports-customers` tinyint(1) DEFAULT 0,
  `reports-suppliers` tinyint(1) DEFAULT 0,
  `reports-staff` tinyint(1) DEFAULT 0,
  `reports-register` tinyint(1) DEFAULT 0,
  `sales-payments` tinyint(1) DEFAULT 0,
  `purchases-payments` tinyint(1) DEFAULT 0,
  `purchases-expenses` tinyint(1) DEFAULT 0,
  `products-adjustments` tinyint(1) NOT NULL DEFAULT 0,
  `bulk_actions` tinyint(1) NOT NULL DEFAULT 0,
  `customers-deposits` tinyint(1) NOT NULL DEFAULT 0,
  `customers-delete_deposit` tinyint(1) NOT NULL DEFAULT 0,
  `products-barcode` tinyint(1) NOT NULL DEFAULT 0,
  `purchases-return_purchases` tinyint(1) NOT NULL DEFAULT 0,
  `reports-expenses` tinyint(1) NOT NULL DEFAULT 0,
  `reports-daily_purchases` tinyint(1) DEFAULT 0,
  `reports-monthly_purchases` tinyint(1) DEFAULT 0,
  `products-stock_count` tinyint(1) DEFAULT 0,
  `edit_price` tinyint(1) DEFAULT 0,
  `returns-index` tinyint(1) DEFAULT 0,
  `returns-add` tinyint(1) DEFAULT 0,
  `returns-edit` tinyint(1) DEFAULT 0,
  `returns-delete` tinyint(1) DEFAULT 0,
  `returns-email` tinyint(1) DEFAULT 0,
  `returns-pdf` tinyint(1) DEFAULT 0,
  `reports-tax` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_permissions` WRITE;
/*!40000 ALTER TABLE `rerp_permissions` DISABLE KEYS */;

INSERT INTO `rerp_permissions` (`id`, `group_id`, `products-index`, `products-add`, `products-edit`, `products-delete`, `products-cost`, `products-price`, `quotes-index`, `quotes-add`, `quotes-edit`, `quotes-pdf`, `quotes-email`, `quotes-delete`, `sales-index`, `sales-add`, `sales-edit`, `sales-pdf`, `sales-email`, `sales-delete`, `purchases-index`, `purchases-add`, `purchases-edit`, `purchases-pdf`, `purchases-email`, `purchases-delete`, `transfers-index`, `transfers-add`, `transfers-edit`, `transfers-pdf`, `transfers-email`, `transfers-delete`, `customers-index`, `customers-add`, `customers-edit`, `customers-delete`, `suppliers-index`, `suppliers-add`, `suppliers-edit`, `suppliers-delete`, `sales-deliveries`, `sales-add_delivery`, `sales-edit_delivery`, `sales-delete_delivery`, `sales-email_delivery`, `sales-pdf_delivery`, `sales-gift_cards`, `sales-add_gift_card`, `sales-edit_gift_card`, `sales-delete_gift_card`, `pos-index`, `sales-return_sales`, `reports-index`, `reports-warehouse_stock`, `reports-quantity_alerts`, `reports-expiry_alerts`, `reports-products`, `reports-daily_sales`, `reports-monthly_sales`, `reports-sales`, `reports-payments`, `reports-purchases`, `reports-profit_loss`, `reports-customers`, `reports-suppliers`, `reports-staff`, `reports-register`, `sales-payments`, `purchases-payments`, `purchases-expenses`, `products-adjustments`, `bulk_actions`, `customers-deposits`, `customers-delete_deposit`, `products-barcode`, `purchases-return_purchases`, `reports-expenses`, `reports-daily_purchases`, `reports-monthly_purchases`, `products-stock_count`, `edit_price`, `returns-index`, `returns-add`, `returns-edit`, `returns-delete`, `returns-email`, `returns-pdf`, `reports-tax`)
VALUES
	(1,5,1,0,0,0,0,0,1,1,1,1,1,0,1,1,0,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,0,0,0,0,0,1,1,1,0,0,1,1,1,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

/*!40000 ALTER TABLE `rerp_permissions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_pos_register
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_pos_register`;

CREATE TABLE `rerp_pos_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `cash_in_hand` decimal(25,4) NOT NULL,
  `status` varchar(10) NOT NULL,
  `total_cash` decimal(25,4) DEFAULT NULL,
  `total_cheques` int(11) DEFAULT NULL,
  `total_cc_slips` int(11) DEFAULT NULL,
  `total_cash_submitted` decimal(25,4) DEFAULT NULL,
  `total_cheques_submitted` int(11) DEFAULT NULL,
  `total_cc_slips_submitted` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `transfer_opened_bills` varchar(50) DEFAULT NULL,
  `closed_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_pos_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_pos_settings`;

CREATE TABLE `rerp_pos_settings` (
  `pos_id` int(1) NOT NULL,
  `cat_limit` int(11) NOT NULL,
  `pro_limit` int(11) NOT NULL,
  `default_category` int(11) NOT NULL,
  `default_customer` int(11) NOT NULL,
  `default_biller` int(11) NOT NULL,
  `display_time` varchar(3) NOT NULL DEFAULT 'yes',
  `cf_title1` varchar(255) DEFAULT NULL,
  `cf_title2` varchar(255) DEFAULT NULL,
  `cf_value1` varchar(255) DEFAULT NULL,
  `cf_value2` varchar(255) DEFAULT NULL,
  `receipt_printer` varchar(55) DEFAULT NULL,
  `cash_drawer_codes` varchar(55) DEFAULT NULL,
  `focus_add_item` varchar(55) DEFAULT NULL,
  `add_manual_product` varchar(55) DEFAULT NULL,
  `customer_selection` varchar(55) DEFAULT NULL,
  `add_customer` varchar(55) DEFAULT NULL,
  `toggle_category_slider` varchar(55) DEFAULT NULL,
  `toggle_subcategory_slider` varchar(55) DEFAULT NULL,
  `cancel_sale` varchar(55) DEFAULT NULL,
  `suspend_sale` varchar(55) DEFAULT NULL,
  `print_items_list` varchar(55) DEFAULT NULL,
  `finalize_sale` varchar(55) DEFAULT NULL,
  `today_sale` varchar(55) DEFAULT NULL,
  `open_hold_bills` varchar(55) DEFAULT NULL,
  `close_register` varchar(55) DEFAULT NULL,
  `keyboard` tinyint(1) NOT NULL,
  `pos_printers` varchar(255) DEFAULT NULL,
  `java_applet` tinyint(1) NOT NULL,
  `product_button_color` varchar(20) NOT NULL DEFAULT 'default',
  `tooltips` tinyint(1) DEFAULT 1,
  `paypal_pro` tinyint(1) DEFAULT 0,
  `stripe` tinyint(1) DEFAULT 0,
  `rounding` tinyint(1) DEFAULT 0,
  `char_per_line` tinyint(4) DEFAULT 42,
  `pin_code` varchar(20) DEFAULT NULL,
  `purchase_code` varchar(100) DEFAULT 'purchase_code',
  `envato_username` varchar(50) DEFAULT 'envato_username',
  `version` varchar(10) DEFAULT '3.4.37',
  `after_sale_page` tinyint(1) DEFAULT 0,
  `item_order` tinyint(1) DEFAULT 0,
  `authorize` tinyint(1) DEFAULT 0,
  `toggle_brands_slider` varchar(55) DEFAULT NULL,
  `remote_printing` tinyint(1) DEFAULT 1,
  `printer` int(11) DEFAULT NULL,
  `order_printers` varchar(55) DEFAULT NULL,
  `auto_print` tinyint(1) DEFAULT 0,
  `customer_details` tinyint(1) DEFAULT NULL,
  `local_printers` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`pos_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_pos_settings` WRITE;
/*!40000 ALTER TABLE `rerp_pos_settings` DISABLE KEYS */;

INSERT INTO `rerp_pos_settings` (`pos_id`, `cat_limit`, `pro_limit`, `default_category`, `default_customer`, `default_biller`, `display_time`, `cf_title1`, `cf_title2`, `cf_value1`, `cf_value2`, `receipt_printer`, `cash_drawer_codes`, `focus_add_item`, `add_manual_product`, `customer_selection`, `add_customer`, `toggle_category_slider`, `toggle_subcategory_slider`, `cancel_sale`, `suspend_sale`, `print_items_list`, `finalize_sale`, `today_sale`, `open_hold_bills`, `close_register`, `keyboard`, `pos_printers`, `java_applet`, `product_button_color`, `tooltips`, `paypal_pro`, `stripe`, `rounding`, `char_per_line`, `pin_code`, `purchase_code`, `envato_username`, `version`, `after_sale_page`, `item_order`, `authorize`, `toggle_brands_slider`, `remote_printing`, `printer`, `order_printers`, `auto_print`, `customer_details`, `local_printers`)
VALUES
	(1,22,20,1,1,3,'1','GST Reg','VAT Reg','123456789','987654321','BIXOLON SRP-350II','x1C','Ctrl+F3','Ctrl+Shift+M','Ctrl+Shift+C','Ctrl+Shift+A','Ctrl+F11','Ctrl+F12','F4','F7','F9','F8','Ctrl+F1','Ctrl+F2','Ctrl+F10',1,'BIXOLON SRP-350II, BIXOLON SRP-350II',0,'default',1,0,0,0,42,NULL,'purchase_code','envato_username','3.4.37',0,0,0,NULL,1,NULL,NULL,0,NULL,NULL);

/*!40000 ALTER TABLE `rerp_pos_settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_price_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_price_groups`;

CREATE TABLE `rerp_price_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_price_groups` WRITE;
/*!40000 ALTER TABLE `rerp_price_groups` DISABLE KEYS */;

INSERT INTO `rerp_price_groups` (`id`, `name`)
VALUES
	(1,'Default');

/*!40000 ALTER TABLE `rerp_price_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_printers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_printers`;

CREATE TABLE `rerp_printers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(55) NOT NULL,
  `type` varchar(25) NOT NULL,
  `profile` varchar(25) NOT NULL,
  `char_per_line` tinyint(3) unsigned DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `ip_address` varbinary(45) DEFAULT NULL,
  `port` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_product_photos
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_product_photos`;

CREATE TABLE `rerp_product_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `photo` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_product_prices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_product_prices`;

CREATE TABLE `rerp_product_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `price_group_id` int(11) NOT NULL,
  `price` decimal(25,4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `price_group_id` (`price_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_product_variants
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_product_variants`;

CREATE TABLE `rerp_product_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `name` varchar(55) NOT NULL,
  `cost` decimal(25,4) DEFAULT NULL,
  `price` decimal(25,4) DEFAULT NULL,
  `quantity` decimal(15,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_id_name` (`product_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_products`;

CREATE TABLE `rerp_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `unit` int(11) DEFAULT NULL,
  `cost` decimal(25,4) DEFAULT NULL,
  `price` decimal(25,4) NOT NULL,
  `alert_quantity` decimal(15,4) DEFAULT 20.0000,
  `image` varchar(255) DEFAULT '',
  `category_id` int(11) NOT NULL,
  `subcategory_id` int(11) DEFAULT NULL,
  `cf1` varchar(255) DEFAULT NULL,
  `cf2` varchar(255) DEFAULT NULL,
  `cf3` varchar(255) DEFAULT NULL,
  `cf4` varchar(255) DEFAULT NULL,
  `cf5` varchar(255) DEFAULT NULL,
  `cf6` varchar(255) DEFAULT NULL,
  `quantity` decimal(15,4) DEFAULT 0.0000,
  `tax_rate` int(11) DEFAULT NULL,
  `track_quantity` tinyint(1) DEFAULT 1,
  `details` varchar(1000) DEFAULT NULL,
  `warehouse` int(11) DEFAULT NULL,
  `barcode_symbology` varchar(55) NOT NULL DEFAULT 'code128',
  `file` varchar(100) DEFAULT NULL,
  `product_details` text DEFAULT NULL,
  `tax_method` tinyint(1) DEFAULT 0,
  `type` varchar(55) NOT NULL DEFAULT 'standard',
  `supplier1` int(11) DEFAULT NULL,
  `supplier1price` decimal(25,4) DEFAULT NULL,
  `supplier2` int(11) DEFAULT NULL,
  `supplier2price` decimal(25,4) DEFAULT NULL,
  `supplier3` int(11) DEFAULT NULL,
  `supplier3price` decimal(25,4) DEFAULT NULL,
  `supplier4` int(11) DEFAULT NULL,
  `supplier4price` decimal(25,4) DEFAULT NULL,
  `supplier5` int(11) DEFAULT NULL,
  `supplier5price` decimal(25,4) DEFAULT NULL,
  `promotion` tinyint(1) DEFAULT 0,
  `promo_price` decimal(25,4) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `supplier1_part_no` varchar(50) DEFAULT NULL,
  `supplier2_part_no` varchar(50) DEFAULT NULL,
  `supplier3_part_no` varchar(50) DEFAULT NULL,
  `supplier4_part_no` varchar(50) DEFAULT NULL,
  `supplier5_part_no` varchar(50) DEFAULT NULL,
  `sale_unit` int(11) DEFAULT NULL,
  `purchase_unit` int(11) DEFAULT NULL,
  `brand` int(11) DEFAULT NULL,
  `slug` varchar(55) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT NULL,
  `weight` decimal(10,4) DEFAULT NULL,
  `hsn_code` int(11) DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `hide` tinyint(1) NOT NULL DEFAULT 0,
  `second_name` varchar(255) DEFAULT NULL,
  `hide_pos` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `category_id` (`category_id`),
  KEY `id` (`id`),
  KEY `id_2` (`id`),
  KEY `category_id_2` (`category_id`),
  KEY `unit` (`unit`),
  KEY `brand` (`brand`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_promos
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_promos`;

CREATE TABLE `rerp_promos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `product2buy` int(11) NOT NULL,
  `product2get` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_purchase_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_purchase_items`;

CREATE TABLE `rerp_purchase_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) DEFAULT NULL,
  `transfer_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `net_unit_cost` decimal(25,4) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `item_tax` decimal(25,4) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(20) DEFAULT NULL,
  `discount` varchar(20) DEFAULT NULL,
  `item_discount` decimal(25,4) DEFAULT NULL,
  `expiry` date DEFAULT NULL,
  `subtotal` decimal(25,4) NOT NULL,
  `quantity_balance` decimal(15,4) DEFAULT 0.0000,
  `date` date NOT NULL,
  `status` varchar(50) NOT NULL,
  `unit_cost` decimal(25,4) DEFAULT NULL,
  `real_unit_cost` decimal(25,4) DEFAULT NULL,
  `quantity_received` decimal(15,4) DEFAULT NULL,
  `supplier_part_no` varchar(50) DEFAULT NULL,
  `purchase_item_id` int(11) DEFAULT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `product_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,4) NOT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_purchases
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_purchases`;

CREATE TABLE `rerp_purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(55) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `supplier_id` int(11) NOT NULL,
  `supplier` varchar(55) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `note` varchar(1000) NOT NULL,
  `total` decimal(25,4) DEFAULT NULL,
  `product_discount` decimal(25,4) DEFAULT NULL,
  `order_discount_id` varchar(20) DEFAULT NULL,
  `order_discount` decimal(25,4) DEFAULT NULL,
  `total_discount` decimal(25,4) DEFAULT NULL,
  `product_tax` decimal(25,4) DEFAULT NULL,
  `order_tax_id` int(11) DEFAULT NULL,
  `order_tax` decimal(25,4) DEFAULT NULL,
  `total_tax` decimal(25,4) DEFAULT 0.0000,
  `shipping` decimal(25,4) DEFAULT 0.0000,
  `grand_total` decimal(25,4) NOT NULL,
  `paid` decimal(25,4) NOT NULL DEFAULT 0.0000,
  `status` varchar(55) DEFAULT '',
  `payment_status` varchar(20) DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `payment_term` tinyint(4) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `return_id` int(11) DEFAULT NULL,
  `surcharge` decimal(25,4) NOT NULL DEFAULT 0.0000,
  `return_purchase_ref` varchar(55) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `return_purchase_total` decimal(25,4) NOT NULL DEFAULT 0.0000,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_quote_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_quote_items`;

CREATE TABLE `rerp_quote_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_code` varchar(55) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_type` varchar(20) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `net_unit_price` decimal(25,4) NOT NULL,
  `unit_price` decimal(25,4) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `item_tax` decimal(25,4) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `discount` varchar(55) DEFAULT NULL,
  `item_discount` decimal(25,4) DEFAULT NULL,
  `subtotal` decimal(25,4) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `real_unit_price` decimal(25,4) DEFAULT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `product_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,4) NOT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quote_id` (`quote_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_quotes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_quotes`;

CREATE TABLE `rerp_quotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reference_no` varchar(55) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer` varchar(55) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `biller_id` int(11) NOT NULL,
  `biller` varchar(55) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `internal_note` varchar(1000) DEFAULT NULL,
  `total` decimal(25,4) NOT NULL,
  `product_discount` decimal(25,4) DEFAULT 0.0000,
  `order_discount` decimal(25,4) DEFAULT NULL,
  `order_discount_id` varchar(20) DEFAULT NULL,
  `total_discount` decimal(25,4) DEFAULT 0.0000,
  `product_tax` decimal(25,4) DEFAULT 0.0000,
  `order_tax_id` int(11) DEFAULT NULL,
  `order_tax` decimal(25,4) DEFAULT NULL,
  `total_tax` decimal(25,4) DEFAULT NULL,
  `shipping` decimal(25,4) DEFAULT 0.0000,
  `grand_total` decimal(25,4) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `supplier` varchar(55) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_return_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_return_items`;

CREATE TABLE `rerp_return_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_code` varchar(55) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_type` varchar(20) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `net_unit_price` decimal(25,4) NOT NULL,
  `unit_price` decimal(25,4) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `item_tax` decimal(25,4) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `discount` varchar(55) DEFAULT NULL,
  `item_discount` decimal(25,4) DEFAULT NULL,
  `subtotal` decimal(25,4) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `real_unit_price` decimal(25,4) DEFAULT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `product_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,4) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `return_id` (`return_id`),
  KEY `product_id` (`product_id`),
  KEY `product_id_2` (`product_id`,`return_id`),
  KEY `return_id_2` (`return_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_returns
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_returns`;

CREATE TABLE `rerp_returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reference_no` varchar(55) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer` varchar(55) NOT NULL,
  `biller_id` int(11) NOT NULL,
  `biller` varchar(55) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `staff_note` varchar(1000) DEFAULT NULL,
  `total` decimal(25,4) NOT NULL,
  `product_discount` decimal(25,4) DEFAULT 0.0000,
  `order_discount_id` varchar(20) DEFAULT NULL,
  `total_discount` decimal(25,4) DEFAULT 0.0000,
  `order_discount` decimal(25,4) DEFAULT 0.0000,
  `product_tax` decimal(25,4) DEFAULT 0.0000,
  `order_tax_id` int(11) DEFAULT NULL,
  `order_tax` decimal(25,4) DEFAULT 0.0000,
  `total_tax` decimal(25,4) DEFAULT 0.0000,
  `grand_total` decimal(25,4) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_items` smallint(6) DEFAULT NULL,
  `paid` decimal(25,4) DEFAULT 0.0000,
  `surcharge` decimal(25,4) NOT NULL DEFAULT 0.0000,
  `attachment` varchar(55) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  `shipping` decimal(25,4) DEFAULT 0.0000,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_sale_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_sale_items`;

CREATE TABLE `rerp_sale_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_code` varchar(55) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_type` varchar(20) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `net_unit_price` decimal(25,4) NOT NULL,
  `unit_price` decimal(25,4) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `item_tax` decimal(25,4) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `discount` varchar(55) DEFAULT NULL,
  `item_discount` decimal(25,4) DEFAULT NULL,
  `subtotal` decimal(25,4) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `real_unit_price` decimal(25,4) DEFAULT NULL,
  `sale_item_id` int(11) DEFAULT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `product_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,4) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  KEY `product_id` (`product_id`),
  KEY `product_id_2` (`product_id`,`sale_id`),
  KEY `sale_id_2` (`sale_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_sales
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_sales`;

CREATE TABLE `rerp_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reference_no` varchar(55) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer` varchar(55) NOT NULL,
  `biller_id` int(11) NOT NULL,
  `biller` varchar(55) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `staff_note` varchar(1000) DEFAULT NULL,
  `total` decimal(25,4) NOT NULL,
  `product_discount` decimal(25,4) DEFAULT 0.0000,
  `order_discount_id` varchar(20) DEFAULT NULL,
  `total_discount` decimal(25,4) DEFAULT 0.0000,
  `order_discount` decimal(25,4) DEFAULT 0.0000,
  `product_tax` decimal(25,4) DEFAULT 0.0000,
  `order_tax_id` int(11) DEFAULT NULL,
  `order_tax` decimal(25,4) DEFAULT 0.0000,
  `total_tax` decimal(25,4) DEFAULT 0.0000,
  `shipping` decimal(25,4) DEFAULT 0.0000,
  `shipping_method_id` int(11) DEFAULT null,
  `grand_total` decimal(25,4) NOT NULL,
  `sale_status` varchar(20) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `payment_term` tinyint(4) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_items` smallint(6) DEFAULT NULL,
  `pos` tinyint(1) NOT NULL DEFAULT 0,
  `paid` decimal(25,4) DEFAULT 0.0000,
  `return_id` int(11) DEFAULT NULL,
  `surcharge` decimal(25,4) NOT NULL DEFAULT 0.0000,
  `attachment` varchar(55) DEFAULT NULL,
  `return_sale_ref` varchar(55) DEFAULT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `return_sale_total` decimal(25,4) NOT NULL DEFAULT 0.0000,
  `rounding` decimal(10,4) DEFAULT NULL,
  `suspend_note` varchar(255) DEFAULT NULL,
  `api` tinyint(1) DEFAULT 0,
  `shop` tinyint(1) DEFAULT 0,
  `address_id` int(11) DEFAULT NULL,
  `reserve_id` int(11) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `manual_payment` varchar(55) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  `payment_method` varchar(55) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_sessions`;

CREATE TABLE `rerp_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT 0,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_sessions` WRITE;
/*!40000 ALTER TABLE `rerp_sessions` DISABLE KEYS */;

INSERT INTO `rerp_sessions` (`id`, `ip_address`, `timestamp`, `data`)
VALUES
	('4gociu2cofkgkek1qi9h32ufv255j6v5','127.0.0.1',1597492673,X'5F5F63695F6C6173745F726567656E65726174657C693A313539373439323637333B7265717565737465645F706167657C733A31333A2273686F702F70726F6475637473223B6964656E746974797C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365726E616D657C733A353A226F776E6572223B656D61696C7C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365725F69647C733A313A2231223B6F6C645F6C6173745F6C6F67696E7C733A31303A2231353937343738373630223B6C6173745F69707C733A393A223132372E302E302E31223B6176617461727C4E3B67656E6465727C733A343A226D616C65223B67726F75705F69647C733A313A2231223B77617265686F7573655F69647C4E3B766965775F72696768747C733A313A2230223B656469745F72696768747C733A313A2230223B616C6C6F775F646973636F756E747C733A313A2230223B62696C6C65725F69647C4E3B636F6D70616E795F69647C4E3B73686F775F636F73747C733A313A2230223B73686F775F70726963657C733A313A2230223B6572726F727C733A33303A226572726F7220343034206D65737361676573686F702F70726F6475637473223B5F5F63695F766172737C613A313A7B733A353A226572726F72223B733A333A226F6C64223B7D'),
	('79iugv60j6688nh8deekoin3ufobfuv6','127.0.0.1',1597486375,X'5F5F63695F6C6173745F726567656E65726174657C693A313539373438363337353B7265717565737465645F706167657C733A353A2261646D696E223B6964656E746974797C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365726E616D657C733A353A226F776E6572223B656D61696C7C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365725F69647C733A313A2231223B6F6C645F6C6173745F6C6F67696E7C733A31303A2231353937343738373630223B6C6173745F69707C733A393A223132372E302E302E31223B6176617461727C4E3B67656E6465727C733A343A226D616C65223B67726F75705F69647C733A313A2231223B77617265686F7573655F69647C4E3B766965775F72696768747C733A313A2230223B656469745F72696768747C733A313A2230223B616C6C6F775F646973636F756E747C733A313A2230223B62696C6C65725F69647C4E3B636F6D70616E795F69647C4E3B73686F775F636F73747C733A313A2230223B73686F775F70726963657C733A313A2230223B'),
	('8r1m0hqlo18t18q4ul1gblp2f45iub08','127.0.0.1',1597493394,X'5F5F63695F6C6173745F726567656E65726174657C693A313539373439333339343B7265717565737465645F706167657C733A31333A2273686F702F70726F6475637473223B6964656E746974797C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365726E616D657C733A353A226F776E6572223B656D61696C7C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365725F69647C733A313A2231223B6F6C645F6C6173745F6C6F67696E7C733A31303A2231353937343738373630223B6C6173745F69707C733A393A223132372E302E302E31223B6176617461727C4E3B67656E6465727C733A343A226D616C65223B67726F75705F69647C733A313A2231223B77617265686F7573655F69647C4E3B766965775F72696768747C733A313A2230223B656469745F72696768747C733A313A2230223B616C6C6F775F646973636F756E747C733A313A2230223B62696C6C65725F69647C4E3B636F6D70616E795F69647C4E3B73686F775F636F73747C733A313A2230223B73686F775F70726963657C733A313A2230223B'),
	('b15ro92c7hsrh5h80t38go03g6coh9d0','127.0.0.1',1597486074,X'5F5F63695F6C6173745F726567656E65726174657C693A313539373438363037343B'),
	('dr65u1f8f72rijfs1pfd8mdnflhttajj','127.0.0.1',1597491645,X'5F5F63695F6C6173745F726567656E65726174657C693A313539373439313634353B7265717565737465645F706167657C733A31333A2273686F702F70726F6475637473223B6964656E746974797C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365726E616D657C733A353A226F776E6572223B656D61696C7C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365725F69647C733A313A2231223B6F6C645F6C6173745F6C6F67696E7C733A31303A2231353937343738373630223B6C6173745F69707C733A393A223132372E302E302E31223B6176617461727C4E3B67656E6465727C733A343A226D616C65223B67726F75705F69647C733A313A2231223B77617265686F7573655F69647C4E3B766965775F72696768747C733A313A2230223B656469745F72696768747C733A313A2230223B616C6C6F775F646973636F756E747C733A313A2230223B62696C6C65725F69647C4E3B636F6D70616E795F69647C4E3B73686F775F636F73747C733A313A2230223B73686F775F70726963657C733A313A2230223B6572726F727C733A33303A226572726F7220343034206D65737361676573686F702F70726F6475637473223B5F5F63695F766172737C613A313A7B733A353A226572726F72223B733A333A226F6C64223B7D'),
	('dvggkls3th3uio4pmnpq81jla5s63980','127.0.0.1',1597491973,X'5F5F63695F6C6173745F726567656E65726174657C693A313539373439313937333B7265717565737465645F706167657C733A31333A2273686F702F70726F6475637473223B6964656E746974797C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365726E616D657C733A353A226F776E6572223B656D61696C7C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365725F69647C733A313A2231223B6F6C645F6C6173745F6C6F67696E7C733A31303A2231353937343738373630223B6C6173745F69707C733A393A223132372E302E302E31223B6176617461727C4E3B67656E6465727C733A343A226D616C65223B67726F75705F69647C733A313A2231223B77617265686F7573655F69647C4E3B766965775F72696768747C733A313A2230223B656469745F72696768747C733A313A2230223B616C6C6F775F646973636F756E747C733A313A2230223B62696C6C65725F69647C4E3B636F6D70616E795F69647C4E3B73686F775F636F73747C733A313A2230223B73686F775F70726963657C733A313A2230223B'),
	('f6u1jroghji5hk85bt343lodgl2cium1','127.0.0.1',1597491337,X'5F5F63695F6C6173745F726567656E65726174657C693A313539373439313333373B7265717565737465645F706167657C733A31333A2273686F702F70726F6475637473223B6964656E746974797C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365726E616D657C733A353A226F776E6572223B656D61696C7C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365725F69647C733A313A2231223B6F6C645F6C6173745F6C6F67696E7C733A31303A2231353937343738373630223B6C6173745F69707C733A393A223132372E302E302E31223B6176617461727C4E3B67656E6465727C733A343A226D616C65223B67726F75705F69647C733A313A2231223B77617265686F7573655F69647C4E3B766965775F72696768747C733A313A2230223B656469745F72696768747C733A313A2230223B616C6C6F775F646973636F756E747C733A313A2230223B62696C6C65725F69647C4E3B636F6D70616E795F69647C4E3B73686F775F636F73747C733A313A2230223B73686F775F70726963657C733A313A2230223B6572726F727C733A33303A226572726F7220343034206D65737361676573686F702F70726F6475637473223B5F5F63695F766172737C613A313A7B733A353A226572726F72223B733A333A226F6C64223B7D'),
	('hampkhcao0dmpj7cu0vhvjekrc171o71','127.0.0.1',1597493469,X'5F5F63695F6C6173745F726567656E65726174657C693A313539373439333339343B7265717565737465645F706167657C733A31333A2273686F702F70726F6475637473223B6964656E746974797C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365726E616D657C733A353A226F776E6572223B656D61696C7C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365725F69647C733A313A2231223B6F6C645F6C6173745F6C6F67696E7C733A31303A2231353937343738373630223B6C6173745F69707C733A393A223132372E302E302E31223B6176617461727C4E3B67656E6465727C733A343A226D616C65223B67726F75705F69647C733A313A2231223B77617265686F7573655F69647C4E3B766965775F72696768747C733A313A2230223B656469745F72696768747C733A313A2230223B616C6C6F775F646973636F756E747C733A313A2230223B62696C6C65725F69647C4E3B636F6D70616E795F69647C4E3B73686F775F636F73747C733A313A2230223B73686F775F70726963657C733A313A2230223B'),
	('kg68f2ealndpug404fr2gu3fh3dl0p2e','127.0.0.1',1597489158,X'5F5F63695F6C6173745F726567656E65726174657C693A313539373438393135383B7265717565737465645F706167657C733A31333A2273686F702F70726F6475637473223B6964656E746974797C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365726E616D657C733A353A226F776E6572223B656D61696C7C733A31393A226E69616D756C31313140676D61696C2E636F6D223B757365725F69647C733A313A2231223B6F6C645F6C6173745F6C6F67696E7C733A31303A2231353937343738373630223B6C6173745F69707C733A393A223132372E302E302E31223B6176617461727C4E3B67656E6465727C733A343A226D616C65223B67726F75705F69647C733A313A2231223B77617265686F7573655F69647C4E3B766965775F72696768747C733A313A2230223B656469745F72696768747C733A313A2230223B616C6C6F775F646973636F756E747C733A313A2230223B62696C6C65725F69647C4E3B636F6D70616E795F69647C4E3B73686F775F636F73747C733A313A2230223B73686F775F70726963657C733A313A2230223B');

/*!40000 ALTER TABLE `rerp_sessions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_settings`;

CREATE TABLE `rerp_settings` (
  `setting_id` int(1) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `logo2` varchar(255) NOT NULL,
  `site_name` varchar(55) NOT NULL,
  `language` varchar(20) NOT NULL,
  `default_warehouse` int(2) NOT NULL,
  `accounting_method` tinyint(4) NOT NULL DEFAULT 0,
  `default_currency` varchar(3) NOT NULL,
  `default_tax_rate` int(2) NOT NULL,
  `rows_per_page` int(2) NOT NULL,
  `version` varchar(10) NOT NULL DEFAULT '1.0',
  `default_tax_rate2` int(11) NOT NULL DEFAULT 0,
  `dateformat` int(11) NOT NULL,
  `sales_prefix` varchar(20) DEFAULT NULL,
  `quote_prefix` varchar(20) DEFAULT NULL,
  `purchase_prefix` varchar(20) DEFAULT NULL,
  `transfer_prefix` varchar(20) DEFAULT NULL,
  `delivery_prefix` varchar(20) DEFAULT NULL,
  `payment_prefix` varchar(20) DEFAULT NULL,
  `return_prefix` varchar(20) DEFAULT NULL,
  `returnp_prefix` varchar(20) DEFAULT NULL,
  `expense_prefix` varchar(20) DEFAULT NULL,
  `item_addition` tinyint(1) NOT NULL DEFAULT 0,
  `theme` varchar(20) NOT NULL,
  `product_serial` tinyint(4) NOT NULL,
  `default_discount` int(11) NOT NULL,
  `product_discount` tinyint(1) NOT NULL DEFAULT 0,
  `discount_method` tinyint(4) NOT NULL,
  `tax1` tinyint(4) NOT NULL,
  `tax2` tinyint(4) NOT NULL,
  `overselling` tinyint(1) NOT NULL DEFAULT 0,
  `restrict_user` tinyint(4) NOT NULL DEFAULT 0,
  `restrict_calendar` tinyint(4) NOT NULL DEFAULT 0,
  `timezone` varchar(100) DEFAULT NULL,
  `iwidth` int(11) NOT NULL DEFAULT 0,
  `iheight` int(11) NOT NULL,
  `twidth` int(11) NOT NULL,
  `theight` int(11) NOT NULL,
  `watermark` tinyint(1) DEFAULT NULL,
  `reg_ver` tinyint(1) DEFAULT NULL,
  `allow_reg` tinyint(1) DEFAULT NULL,
  `reg_notification` tinyint(1) DEFAULT NULL,
  `auto_reg` tinyint(1) DEFAULT NULL,
  `protocol` varchar(20) NOT NULL DEFAULT 'mail',
  `mailpath` varchar(55) DEFAULT '/usr/sbin/sendmail',
  `smtp_host` varchar(100) DEFAULT NULL,
  `smtp_user` varchar(100) DEFAULT NULL,
  `smtp_pass` varchar(255) DEFAULT NULL,
  `smtp_port` varchar(10) DEFAULT '25',
  `smtp_crypto` varchar(10) DEFAULT NULL,
  `corn` datetime DEFAULT NULL,
  `customer_group` int(11) NOT NULL,
  `default_email` varchar(100) NOT NULL,
  `mmode` tinyint(1) NOT NULL,
  `bc_fix` tinyint(4) NOT NULL DEFAULT 0,
  `auto_detect_barcode` tinyint(1) NOT NULL DEFAULT 0,
  `captcha` tinyint(1) NOT NULL DEFAULT 1,
  `reference_format` tinyint(1) NOT NULL DEFAULT 1,
  `racks` tinyint(1) DEFAULT 0,
  `attributes` tinyint(1) NOT NULL DEFAULT 0,
  `product_expiry` tinyint(1) NOT NULL DEFAULT 0,
  `decimals` tinyint(2) NOT NULL DEFAULT 2,
  `qty_decimals` tinyint(2) NOT NULL DEFAULT 2,
  `decimals_sep` varchar(2) NOT NULL DEFAULT '.',
  `thousands_sep` varchar(2) NOT NULL DEFAULT ',',
  `invoice_view` tinyint(1) DEFAULT 0,
  `default_biller` int(11) DEFAULT NULL,
  `envato_username` varchar(50) DEFAULT NULL,
  `purchase_code` varchar(100) DEFAULT NULL,
  `rtl` tinyint(1) DEFAULT 0,
  `each_spent` decimal(15,4) DEFAULT NULL,
  `ca_point` tinyint(4) DEFAULT NULL,
  `each_sale` decimal(15,4) DEFAULT NULL,
  `sa_point` tinyint(4) DEFAULT NULL,
  `update` tinyint(1) DEFAULT 0,
  `sac` tinyint(1) DEFAULT 0,
  `display_all_products` tinyint(1) DEFAULT 0,
  `display_symbol` tinyint(1) DEFAULT NULL,
  `symbol` varchar(50) DEFAULT NULL,
  `remove_expired` tinyint(1) DEFAULT 0,
  `barcode_separator` varchar(2) NOT NULL DEFAULT '-',
  `set_focus` tinyint(1) NOT NULL DEFAULT 0,
  `price_group` int(11) DEFAULT NULL,
  `barcode_img` tinyint(1) NOT NULL DEFAULT 1,
  `ppayment_prefix` varchar(20) DEFAULT 'POP',
  `disable_editing` smallint(6) DEFAULT 90,
  `qa_prefix` varchar(55) DEFAULT NULL,
  `update_cost` tinyint(1) DEFAULT NULL,
  `apis` tinyint(1) NOT NULL DEFAULT 0,
  `state` varchar(100) DEFAULT NULL,
  `pdf_lib` varchar(20) DEFAULT 'dompdf',
  `use_code_for_slug` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_settings` WRITE;
/*!40000 ALTER TABLE `rerp_settings` DISABLE KEYS */;

INSERT INTO `rerp_settings` (`setting_id`, `logo`, `logo2`, `site_name`, `language`, `default_warehouse`, `accounting_method`, `default_currency`, `default_tax_rate`, `rows_per_page`, `version`, `default_tax_rate2`, `dateformat`, `sales_prefix`, `quote_prefix`, `purchase_prefix`, `transfer_prefix`, `delivery_prefix`, `payment_prefix`, `return_prefix`, `returnp_prefix`, `expense_prefix`, `item_addition`, `theme`, `product_serial`, `default_discount`, `product_discount`, `discount_method`, `tax1`, `tax2`, `overselling`, `restrict_user`, `restrict_calendar`, `timezone`, `iwidth`, `iheight`, `twidth`, `theight`, `watermark`, `reg_ver`, `allow_reg`, `reg_notification`, `auto_reg`, `protocol`, `mailpath`, `smtp_host`, `smtp_user`, `smtp_pass`, `smtp_port`, `smtp_crypto`, `corn`, `customer_group`, `default_email`, `mmode`, `bc_fix`, `auto_detect_barcode`, `captcha`, `reference_format`, `racks`, `attributes`, `product_expiry`, `decimals`, `qty_decimals`, `decimals_sep`, `thousands_sep`, `invoice_view`, `default_biller`, `envato_username`, `purchase_code`, `rtl`, `each_spent`, `ca_point`, `each_sale`, `sa_point`, `update`, `sac`, `display_all_products`, `display_symbol`, `symbol`, `remove_expired`, `barcode_separator`, `set_focus`, `price_group`, `barcode_img`, `ppayment_prefix`, `disable_editing`, `qa_prefix`, `update_cost`, `apis`, `state`, `pdf_lib`, `use_code_for_slug`)
VALUES
	(1,'logo2.png','logo3.png','RetailERP','english',1,0,'USD',1,10,'1.0.0',1,5,'SALE','QUOTE','PO','TR','DO','IPAY','SR','PR','',0,'default',1,1,1,1,1,1,0,1,0,'Asia/Dhaka',800,600,180,150,0,0,0,0,NULL,'mail','/usr/sbin/sendmail','pop.gmail.com','niamul111@gmail.com','jEFTM4T63AiQ9dsidxhPKt9CIg4HQjCN58n/RW9vmdC/UDXCzRLR469ziZ0jjpFlbOg43LyoSmpJLBkcAHh0Yw==','25',NULL,NULL,1,'niamul111@gmail.com',0,4,1,0,2,1,1,0,2,2,'.',',',0,3,'ThemeRox','e03c6f08-d3fb-4c23-9186-9774cfd67943',0,NULL,NULL,NULL,NULL,0,0,0,0,'',0,'-',0,1,1,'POP',90,'',0,1,'AN','dompdf',0);

/*!40000 ALTER TABLE `rerp_settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_skrill
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_skrill`;

CREATE TABLE `rerp_skrill` (
  `id` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `account_email` varchar(255) NOT NULL DEFAULT 'testaccount2@moneybookers.com',
  `secret_word` varchar(20) NOT NULL DEFAULT 'mbtest',
  `skrill_currency` varchar(3) NOT NULL DEFAULT 'USD',
  `fixed_charges` decimal(25,4) NOT NULL DEFAULT 0.0000,
  `extra_charges_my` decimal(25,4) NOT NULL DEFAULT 0.0000,
  `extra_charges_other` decimal(25,4) NOT NULL DEFAULT 0.0000,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_skrill` WRITE;
/*!40000 ALTER TABLE `rerp_skrill` DISABLE KEYS */;

INSERT INTO `rerp_skrill` (`id`, `active`, `account_email`, `secret_word`, `skrill_currency`, `fixed_charges`, `extra_charges_my`, `extra_charges_other`)
VALUES
	(1,1,'testaccount2@moneybookers.com','mbtest','USD',0.0000,0.0000,0.0000);

/*!40000 ALTER TABLE `rerp_skrill` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_stock_count_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_stock_count_items`;

CREATE TABLE `rerp_stock_count_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_count_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_variant` varchar(55) DEFAULT NULL,
  `product_variant_id` int(11) DEFAULT NULL,
  `expected` decimal(15,4) NOT NULL,
  `counted` decimal(15,4) NOT NULL,
  `cost` decimal(25,4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_count_id` (`stock_count_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_stock_counts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_stock_counts`;

CREATE TABLE `rerp_stock_counts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reference_no` varchar(55) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `initial_file` varchar(50) NOT NULL,
  `final_file` varchar(50) DEFAULT NULL,
  `brands` varchar(50) DEFAULT NULL,
  `brand_names` varchar(100) DEFAULT NULL,
  `categories` varchar(50) DEFAULT NULL,
  `category_names` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `products` int(11) DEFAULT NULL,
  `rows` int(11) DEFAULT NULL,
  `differences` int(11) DEFAULT NULL,
  `matches` int(11) DEFAULT NULL,
  `missing` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `finalized` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_suspended_bills
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_suspended_bills`;

CREATE TABLE `rerp_suspended_bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_id` int(11) NOT NULL,
  `customer` varchar(55) DEFAULT NULL,
  `count` int(11) NOT NULL,
  `order_discount_id` varchar(20) DEFAULT NULL,
  `order_tax_id` int(11) DEFAULT NULL,
  `total` decimal(25,4) NOT NULL,
  `biller_id` int(11) DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `suspend_note` varchar(255) DEFAULT NULL,
  `shipping` decimal(15,4) DEFAULT 0.0000,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_suspended_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_suspended_items`;

CREATE TABLE `rerp_suspended_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `suspend_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_code` varchar(55) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `net_unit_price` decimal(25,4) NOT NULL,
  `unit_price` decimal(25,4) NOT NULL,
  `quantity` decimal(15,4) DEFAULT 0.0000,
  `warehouse_id` int(11) DEFAULT NULL,
  `item_tax` decimal(25,4) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `discount` varchar(55) DEFAULT NULL,
  `item_discount` decimal(25,4) DEFAULT NULL,
  `subtotal` decimal(25,4) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `product_type` varchar(20) DEFAULT NULL,
  `real_unit_price` decimal(25,4) DEFAULT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `product_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,4) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_tax_rates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_tax_rates`;

CREATE TABLE `rerp_tax_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `rate` decimal(12,4) NOT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_tax_rates` WRITE;
/*!40000 ALTER TABLE `rerp_tax_rates` DISABLE KEYS */;

INSERT INTO `rerp_tax_rates` (`id`, `name`, `code`, `rate`, `type`)
VALUES
	(1,'No Tax','NT',0.0000,'2'),
	(2,'VAT @10%','VAT10',10.0000,'1'),
	(3,'GST @6%','GST',6.0000,'1'),
	(4,'VAT @20%','VT20',20.0000,'1');

/*!40000 ALTER TABLE `rerp_tax_rates` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_transfer_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_transfer_items`;

CREATE TABLE `rerp_transfer_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_code` varchar(55) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `expiry` date DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `item_tax` decimal(25,4) DEFAULT NULL,
  `net_unit_cost` decimal(25,4) DEFAULT NULL,
  `subtotal` decimal(25,4) DEFAULT NULL,
  `quantity_balance` decimal(15,4) NOT NULL,
  `unit_cost` decimal(25,4) DEFAULT NULL,
  `real_unit_cost` decimal(25,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `product_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,4) NOT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfer_id` (`transfer_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_transfers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_transfers`;

CREATE TABLE `rerp_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_no` varchar(55) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `from_warehouse_id` int(11) NOT NULL,
  `from_warehouse_code` varchar(55) NOT NULL,
  `from_warehouse_name` varchar(55) NOT NULL,
  `to_warehouse_id` int(11) NOT NULL,
  `to_warehouse_code` varchar(55) NOT NULL,
  `to_warehouse_name` varchar(55) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `total` decimal(25,4) DEFAULT NULL,
  `total_tax` decimal(25,4) DEFAULT NULL,
  `grand_total` decimal(25,4) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `status` varchar(55) NOT NULL DEFAULT 'pending',
  `shipping` decimal(25,4) NOT NULL DEFAULT 0.0000,
  `attachment` varchar(55) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_units
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_units`;

CREATE TABLE `rerp_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `name` varchar(55) NOT NULL,
  `base_unit` int(11) DEFAULT NULL,
  `operator` varchar(1) DEFAULT NULL,
  `unit_value` varchar(55) DEFAULT NULL,
  `operation_value` varchar(55) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `base_unit` (`base_unit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_user_logins
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_user_logins`;

CREATE TABLE `rerp_user_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `ip_address` varbinary(16) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_user_logins` WRITE;
/*!40000 ALTER TABLE `rerp_user_logins` DISABLE KEYS */;

INSERT INTO `rerp_user_logins` (`id`, `user_id`, `company_id`, `ip_address`, `login`, `time`)
VALUES
	(1,1,NULL,X'3132372E302E302E31','niamul111@gmail.com','2020-08-15 14:06:00'),
	(2,1,NULL,X'3132372E302E302E31','niamul111@gmail.com','2020-08-15 16:12:24');

/*!40000 ALTER TABLE `rerp_user_logins` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_users`;

CREATE TABLE `rerp_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `last_ip_address` varbinary(45) DEFAULT NULL,
  `ip_address` varbinary(45) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(40) NOT NULL,
  `salt` varchar(40) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(55) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `warehouse_id` int(10) unsigned DEFAULT NULL,
  `biller_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `show_cost` tinyint(1) DEFAULT 0,
  `show_price` tinyint(1) DEFAULT 0,
  `award_points` int(11) DEFAULT 0,
  `view_right` tinyint(1) NOT NULL DEFAULT 0,
  `edit_right` tinyint(1) NOT NULL DEFAULT 0,
  `allow_discount` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`,`warehouse_id`,`biller_id`),
  KEY `group_id_2` (`group_id`,`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_users` WRITE;
/*!40000 ALTER TABLE `rerp_users` DISABLE KEYS */;

INSERT INTO `rerp_users` (`id`, `last_ip_address`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`, `avatar`, `gender`, `group_id`, `warehouse_id`, `biller_id`, `company_id`, `show_cost`, `show_price`, `award_points`, `view_right`, `edit_right`, `allow_discount`)
VALUES
	(1,X'3132372E302E302E31',X'0000','owner','2c8ab736b2ccab4f50e72d5fd7d21020cbb77ae7',NULL,'niamul111@gmail.com',NULL,NULL,NULL,NULL,1351661704,1597486344,1,'Owner','Owner','Stock Manager','012345678',NULL,'male',1,NULL,NULL,NULL,0,0,0,0,0,0);

/*!40000 ALTER TABLE `rerp_users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_variants
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_variants`;

CREATE TABLE `rerp_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_warehouses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_warehouses`;

CREATE TABLE `rerp_warehouses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `map` varchar(255) DEFAULT NULL,
  `phone` varchar(55) DEFAULT NULL,
  `email` varchar(55) DEFAULT NULL,
  `price_group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_warehouses` WRITE;
/*!40000 ALTER TABLE `rerp_warehouses` DISABLE KEYS */;

INSERT INTO `rerp_warehouses` (`id`, `code`, `name`, `address`, `map`, `phone`, `email`, `price_group_id`)
VALUES
	(1,'WHI','Warehouse 1','<p>Address, City</p>',NULL,'012345678','whi@tecdiary.com',NULL),
	(2,'WHII','Warehouse 2','<p>Warehouse 2, Jalan Sultan Ismail, 54000, Kuala Lumpur</p>',NULL,'0105292122','whii@tecdiary.com',NULL);

/*!40000 ALTER TABLE `rerp_warehouses` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_warehouses_products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_warehouses_products`;

CREATE TABLE `rerp_warehouses_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `rack` varchar(55) DEFAULT NULL,
  `avg_cost` decimal(25,4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_warehouses_products_variants
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_warehouses_products_variants`;

CREATE TABLE `rerp_warehouses_products_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `option_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `rack` varchar(55) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `option_id` (`option_id`),
  KEY `product_id` (`product_id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_api_keys
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_api_keys`;

CREATE TABLE `rerp_api_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reference` varchar(40) NOT NULL,
  `key` varchar(40) NOT NULL,
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT 0,
  `is_private_key` tinyint(1) NOT NULL DEFAULT 0,
  `ip_addresses` text DEFAULT NULL,
  `date_created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_api_limits
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_api_limits`;

CREATE TABLE `rerp_api_limits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) NOT NULL,
  `count` int(10) NOT NULL,
  `hour_started` int(11) NOT NULL,
  `api_key` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_api_logs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_api_logs`;

CREATE TABLE `rerp_api_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) NOT NULL,
  `method` varchar(6) NOT NULL,
  `params` text DEFAULT NULL,
  `api_key` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `time` int(11) NOT NULL,
  `rtime` float DEFAULT NULL,
  `authorized` varchar(1) NOT NULL,
  `response_code` smallint(3) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_cart
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_cart`;

CREATE TABLE `rerp_cart` (
  `id` varchar(40) NOT NULL,
  `time` varchar(30) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `data` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_pages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_pages`;

CREATE TABLE `rerp_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `title` varchar(60) NOT NULL,
  `description` varchar(180) NOT NULL,
  `slug` varchar(55) DEFAULT NULL,
  `body` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `order_no` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rerp_shop_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_shop_settings`;

CREATE TABLE `rerp_shop_settings` (
  `shop_id` int(11) NOT NULL,
  `shop_name` varchar(55) NOT NULL,
  `description` varchar(160) NOT NULL,
  `warehouse` int(11) NOT NULL,
  `biller` int(11) NOT NULL,
  `about_link` varchar(55) NOT NULL,
  `terms_link` varchar(55) NOT NULL,
  `privacy_link` varchar(55) NOT NULL,
  `contact_link` varchar(55) NOT NULL,
  `payment_text` varchar(100) NOT NULL,
  `follow_text` varchar(100) NOT NULL,
  `facebook` varchar(55) NOT NULL,
  `twitter` varchar(55) DEFAULT NULL,
  `google_plus` varchar(55) DEFAULT NULL,
  `instagram` varchar(55) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(55) DEFAULT NULL,
  `cookie_message` varchar(180) DEFAULT NULL,
  `cookie_link` varchar(55) DEFAULT NULL,
  `slider` text DEFAULT NULL,
  `shipping` int(11) DEFAULT NULL,
  `purchase_code` varchar(100) DEFAULT 'purchase_code',
  `envato_username` varchar(50) DEFAULT 'envato_username',
  `version` varchar(10) DEFAULT '3.4.37',
  `logo` varchar(55) DEFAULT NULL,
  `bank_details` varchar(255) DEFAULT NULL,
  `products_page` tinyint(1) DEFAULT NULL,
  `hide0` tinyint(1) DEFAULT 0,
  `products_description` varchar(255) DEFAULT NULL,
  `private` tinyint(1) DEFAULT 0,
  `hide_price` tinyint(1) DEFAULT 0,
  `stripe` tinyint(1) DEFAULT 0,
  `mc_api` VARCHAR(200) DEFAULT NULL,
  `free_shipping` decimal(25,4) NOT NULL,
  `minimum_order` decimal(25,4) NOT NULL,
  PRIMARY KEY (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_shop_settings` WRITE;
/*!40000 ALTER TABLE `rerp_shop_settings` DISABLE KEYS */;

INSERT INTO `rerp_shop_settings` (`shop_id`, `shop_name`, `description`, `warehouse`, `biller`, `about_link`, `terms_link`, `privacy_link`, `contact_link`, `payment_text`, `follow_text`, `facebook`, `twitter`, `google_plus`, `instagram`, `phone`, `email`, `cookie_message`, `cookie_link`, `slider`, `shipping`, `purchase_code`, `envato_username`, `version`, `logo`, `bank_details`, `products_page`, `hide0`, `products_description`, `private`, `hide_price`, `stripe`)
VALUES
	(1,'Retail Shop','Demo Ecommerce Shop that would help you to sell your products from your site.',1,3,'','','','','We accept PayPal or you can pay with your credit/debit cards.','Please click the link below to follow us on social media.','http://facebook.com/tecdiary','http://twitter.com/tecdiary','','','010 1234 567','info@tecdiary.com','We use cookies to improve your experience on our website. By browsing this website, you agree to our use of cookies.','','[{\"link\":\"\",\"caption\":\"\"},{\"link\":\"\",\"caption\":\"\"},{\"link\":\"\",\"caption\":\"\"},{\"link\":\"\",\"caption\":\"\"},{\"link\":\"\",\"caption\":\"\"}]',0,'b75dd66f-e0fd-4619-a289-fbc84860b7b4','ThemeRox','3.4.37','sma-shop.png','',0,0,'Demo',0,0,0);

/*!40000 ALTER TABLE `rerp_shop_settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_sms_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_sms_settings`;

CREATE TABLE `rerp_sms_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auto_send` tinyint(1) DEFAULT NULL,
  `config` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `rerp_sms_settings` WRITE;
/*!40000 ALTER TABLE `rerp_sms_settings` DISABLE KEYS */;

INSERT INTO `rerp_sms_settings` (`id`, `auto_send`, `config`)
VALUES
	(1,NULL,'{\"gateway\":\"Log\",\"Log\":{}}');

/*!40000 ALTER TABLE `rerp_sms_settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table rerp_wishlist
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_wishlist`;

CREATE TABLE `rerp_wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table rerp_options
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_options`;

CREATE TABLE `rerp_options` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) NOT NULL DEFAULT '',
  `option_value` longtext NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`),
  UNIQUE KEY `option_name` (`option_name`),
  KEY `autoload` (`autoload`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table rerp_menus
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_menus`;

CREATE TABLE `rerp_menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent` bigint(20) NOT NULL DEFAULT 0,
  `label` varchar(191) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL DEFAULT '',
  `target` varchar(10) NOT NULL DEFAULT '',
  `class` varchar(10) NOT NULL DEFAULT '',
  `tip` varchar(10) NOT NULL DEFAULT '',
  `order` int(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `label` (`label`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table rerp_menus
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rerp_product_meta`;

CREATE TABLE `rerp_product_meta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
