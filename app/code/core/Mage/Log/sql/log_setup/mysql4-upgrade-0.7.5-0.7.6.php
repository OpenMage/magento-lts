<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Log
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();
$installer->getConnection()->modifyColumn($installer->getTable('log/visitor_online'), 'remote_addr', 'bigint(20) NOT NULL');
$installer->endSetup();
