<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->modifyColumn(
    $this->getTable('core/flag'),
    'flag_id',
    'INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT',
);

$installer->endSetup();
