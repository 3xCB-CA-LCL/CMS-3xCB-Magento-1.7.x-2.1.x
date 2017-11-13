<?php

/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please contact us
 * via http://www.fia-net-group.com/formulaire.php so we can send you a copy immediately.
 *
 * @author     FIA-NET <support-boutique@fia-net.com>
 * @copyright  Copyright (c) 2017 FIA-NET
 * @version    Release: $Revision: 0.1.0 $
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

?>
<?php

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run(
    "
    CREATE TABLE IF NOT EXISTS  `{$this->getTable('fianetpayment3xcb_mapping_catalog_category')}` (
        `id` int(11) unsigned NOT NULL auto_increment,
        `category_id` int(11) unsigned NOT NULL,
        `fianet_product_type_id` int(5) unsigned NOT NULL,
        PRIMARY KEY  (`id`),
        UNIQUE KEY `category_id` (`category_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE IF NOT EXISTS `{$this->getTable('fianetpayment3xcb_mapping_shipping_method')}` (
        `id` int(11) unsigned NOT NULL auto_increment,
        `carrier_code` varchar(255) NOT NULL,
        `method_code` varchar(255) NOT NULL,
        `fianet_shipping_type_id` enum('1','2','3','4','5') NOT NULL default '4',
        `fianet_shipping_speed_id` enum('1','2') NOT NULL default '2',
        `fianet_shipping_name` varchar(255) NOT NULL,
        PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE IF NOT EXISTS `{$this->getTable('fianetpayment3xcb_log')}` (
        `id` int(11) NOT NULL auto_increment,
        `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP,
        `level` enum('DEBUG', 'NOTICE', 'WARNING', 'ERROR', 'UNKNOWN') NOT NULL default 'UNKNOWN',
        `message` text NOT NULL,
        `event` varchar(255) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `created_at` (`created_at`),
        KEY `level` (`level`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE IF NOT EXISTS `{$this->getTable('fianetpayment3xcb_store_information')}` (
        `information` varchar(255) NOT NULL,
        `value` varchar(255) NOT NULL,
        PRIMARY KEY  (`information`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    INSERT IGNORE INTO `{$this->getTable('fianetpayment3xcb_store_information')}` (`information`, `value`)
        VALUES ('store_address', '');
    INSERT IGNORE INTO `{$this->getTable('fianetpayment3xcb_store_information')}` (`information`, `value`)
        VALUES ('store_postal_code', '');
    INSERT IGNORE INTO `{$this->getTable('fianetpayment3xcb_store_information')}` (`information`, `value`)
        VALUES ('store_city', '');
    INSERT IGNORE INTO `{$this->getTable('fianetpayment3xcb_store_information')}` (`information`, `value`)
        VALUES ('store_country', '');

    -- FOREIGN KEY (`category_id`)
    -- REFERENCES `catalog_category_entity` (`entity_id`)
    -- ON DELETE CASCADE ON UPDATE CASCADE;

    INSERT IGNORE INTO `{$this->getTable('sales_order_status')}` (`status`, `label`)
    VALUES ('pending_fianet3xcb', 'Pending 3xCB');

    INSERT IGNORE INTO `{$this->getTable('sales_order_status_state')}` (`status`, `state`, `is_default`)
    VALUES ('pending_fianet3xcb', 'pending_payment', '0');

    CREATE TABLE IF NOT EXISTS `{$this->getTable('fianetpayment3xcb_mapping_transaction_state')}` (
        `transaction_state` VARCHAR(30) NOT NULL ,
        `order_status` VARCHAR(32) NOT NULL ,
        `notify_customer` BOOLEAN NOT NULL DEFAULT 0,
        PRIMARY KEY (`transaction_state`)
    ) ENGINE = InnoDB DEFAULT CHARSET=utf8;

    INSERT IGNORE INTO `{$this->getTable('fianetpayment3xcb_mapping_transaction_state')}`
    (`transaction_state`, `order_status`, `notify_customer`)
    VALUES
    ('CANCELLATION_ASKED',          'processing',         '0'),
    ('CONTRACT_ACCEPTED',           'processing',         '1'),
    ('CONTRACT_REFUSED',            'canceled',           '0'),
    ('CONTRACT_REVIEW_IN_PROGRESS', 'pending_fianet3xcb', '0'),
    ('CONTRACT_SENT',               'processing',         '0'),
    ('DEBIT_SENT',                  'processing',         '0'),
    ('PARTIAL_VALIDATION_ASKED',    'processing',         '0'),
    ('PAYMENT_ABORTED',             'canceled',           '0'),
    ('PAYMENT_CANCELLED',           'canceled',           '1'),
    ('PAYMENT_KO',                  'canceled',           '1'),
    ('PAYMENT_STORED',              'pending_fianet3xcb', '0'),
    ('PAYMENT_VALIDATED',           'processing',         '0'),
    ('REQUEST_KO',                  'canceled',           '0'),
    ('VALIDATION_ASKED',            'processing',         '0');

    CREATE TABLE IF NOT EXISTS `{$this->getTable('fianetpayment3xcb_transaction_history')}` (
        `id` int(10) unsigned NOT NULL auto_increment,
        `top3reference` varchar(50) NOT NULL,
        `order_increment_id` VARCHAR(50) NOT NULL,
        `created_at` DATETIME NOT NULL,
        `state` VARCHAR(30) NOT NULL,
        `mode` enum('test','production') NOT NULL default 'test',
        `amount` decimal(12,4) NOT NULL,
        `last_refresh` DATETIME NOT NULL,
        PRIMARY KEY (`id`),
        KEY `top3reference` (`top3reference`),
        KEY `order_increment_id` (`order_increment_id`),
        KEY `state` (`state`),
        KEY `mode` (`mode`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    "
);

$installer->addAttribute(
    'order',
    'fianet3xcb_state',
    array(
        'type' => 'varchar',
        'visible' => false,
        'required' => false,
    )
);

$installer->addAttribute(
    'order',
    'fianet3xcb_mode',
    array(
        'type' => 'varchar',
        'visible' => false,
        'required' => false,
        'option' => array('test' => 'test', 'production' => 'production'),
    )
);

$connection = $installer->getConnection();
$salesFlatOrderGridTable = $installer->getTable('sales_flat_order_grid');
$connection->addColumn($salesFlatOrderGridTable, 'fianet3xcb_state', 'varchar(30) default NULL');
$connection->addColumn($salesFlatOrderGridTable, 'fianet3xcb_mode', 'varchar(10) default NULL');

$installer->endSetup();
