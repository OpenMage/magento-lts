<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$installer->run("
UPDATE `{$this->getTable('core_store')}` SET `code` = 'admin', `name` = 'Admin' WHERE `code` LIKE 'default';
UPDATE `{$this->getTable('core_store')}` SET `code` = 'default', `name` = 'Default Store View' WHERE `code` LIKE 'base';
    ");

$installer->endSetup();
