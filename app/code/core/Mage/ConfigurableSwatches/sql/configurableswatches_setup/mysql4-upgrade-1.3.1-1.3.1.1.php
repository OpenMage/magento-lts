<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Widget
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$swatchAttributes = Mage::getModel('core/config_data')->getCollection()
    ->addFieldToFilter('path', Mage_ConfigurableSwatches_Helper_Data::CONFIG_PATH_SWATCH_ATTRIBUTES);

foreach ($swatchAttributes as $swatchAttribute)  {
    $config = Mage::getModel('core/config_data');
    $config->setData($swatchAttribute->getData())
        ->setPath(Mage_ConfigurableSwatches_Helper_Data::CONFIG_PATH_SWATCH_ATTRIBUTES_COLORPICKER)
        ->setScope($swatchAttribute->getScope())
        ->setScopeId($swatchAttribute->getScopeId())
        ->save();
}

$installer->endSetup();