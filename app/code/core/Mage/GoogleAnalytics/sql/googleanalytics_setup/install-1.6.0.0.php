<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("INSERT INTO `{$this->getTable('core/config_data')}` (scope, scope_id, path, value)"
    . " SELECT scope, scope_id, 'google/analytics/type', 'analytics'"
    . " FROM `{$this->getTable('core/config_data')}`"
    . " WHERE path = 'google/analytics/active' AND value = '1'");

$installer->endSetup();
