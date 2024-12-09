<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    'website_id'
);
