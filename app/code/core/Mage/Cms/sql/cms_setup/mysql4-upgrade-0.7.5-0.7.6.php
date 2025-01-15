<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$installer->getConnection()->dropKey($this->getTable('cms/page'), 'identifier');

$installer->run("ALTER TABLE `{$this->getTable('cms/page')}` ADD KEY `identifier` (`identifier`)");

$installer->getConnection()->dropColumn($this->getTable('cms/page'), 'store_id');

$installer->getConnection()->dropColumn($this->getTable('cms/block'), 'store_id');

$installer->endSetup();
