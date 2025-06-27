<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/** @var Mage_Paypal_Model_Resource_Setup $installer */
$installer = $this;

$installer->run("
CREATE TABLE `{$installer->getTable('paypal/cert')}` (
    `cert_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    `website_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
    `content` MEDIUMBLOB NOT NULL,
    `updated_at` datetime default NULL,
    PRIMARY KEY (`cert_id`),
    KEY `IDX_PAYPAL_CERT_WEBSITE` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'FK_PAYPAL_CERT_WEBSITE',
    $this->getTable('paypal/cert'),
    'website_id',
    $this->getTable('core/website'),
    'website_id',
);
