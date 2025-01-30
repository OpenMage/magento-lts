<?php

/**
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

if ($installer->getTable('cms_widget')) {
    $installer->run("
        ALTER TABLE `{$installer->getTable('cms_widget')}` COMMENT 'Preconfigured Widgets';
        ALTER TABLE `{$installer->getTable('cms_widget')}` RENAME TO `{$installer->getTable('widget/widget')}`;
    ");
}

$installer->endSetup();
