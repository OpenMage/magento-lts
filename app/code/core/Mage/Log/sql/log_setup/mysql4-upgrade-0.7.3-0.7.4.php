<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Log
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addKey($installer->getTable('log/customer'), 'IDX_VISITOR', 'visitor_id');
$installer->getConnection()->addKey($installer->getTable('log/url_table'), 'PRIMARY', 'url_id', 'primary');
$installer->getConnection()->addKey($installer->getTable('log/url_table'), 'IDX_VISITOR', 'visitor_id');

$installer->endSetup();
