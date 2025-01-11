<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $installer->getTable('rating/rating_option_vote'),
    'remote_ip_long',
    'remote_ip_long',
    'varbinary(16)',
);

$installer->getConnection()->changeColumn(
    $installer->getTable('rating/rating_option_vote'),
    'remote_ip',
    'remote_ip',
    'varchar(50)',
);

$installer->getConnection()->update(
    $installer->getTable('rating/rating_option_vote'),
    [
        'remote_ip_long' => new Zend_Db_Expr('UNHEX(HEX(CAST(remote_ip_long as UNSIGNED INT)))'),
    ],
);

$installer->endSetup();
