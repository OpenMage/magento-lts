<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
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
