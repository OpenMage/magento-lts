<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Rating
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
