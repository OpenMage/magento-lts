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

$installer->getConnection()->dropIndex($installer->getTable('log/url_table'), 'PRIMARY');
$installer->getConnection()->addIndex($installer->getTable('log/url_table'), 'url_id', ['url_id']);

$installer->endSetup();
