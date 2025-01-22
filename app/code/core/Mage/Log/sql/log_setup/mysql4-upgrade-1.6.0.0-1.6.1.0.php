<?php

/**
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropIndex($installer->getTable('log/url_table'), 'PRIMARY');
$installer->getConnection()->addIndex($installer->getTable('log/url_table'), 'url_id', ['url_id']);

$installer->endSetup();
