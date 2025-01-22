<?php

/**
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Variable observer
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Variable_Observer
{
    /**
     * Add variable wysiwyg plugin config
     *
     * @return $this
     */
    public function prepareWysiwygPluginConfig(Varien_Event_Observer $observer)
    {
        $config = $observer->getEvent()->getConfig();

        if ($config->getData('add_variables')) {
            $settings = Mage::getModel('core/variable_config')->getWysiwygPluginSettings($config);
            $config->addData($settings);
        }
        return $this;
    }
}
