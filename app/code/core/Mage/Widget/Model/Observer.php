<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Widget
 */

/**
 * Widget Observer model
 *
 * @category   Mage
 * @package    Mage_Widget
 */
class Mage_Widget_Model_Observer
{
    /**
     * Add additional settings to wysiwyg config for Widgets Insertion Plugin
     *
     * @return $this
     */
    public function prepareWidgetsPluginConfig(Varien_Event_Observer $observer)
    {
        $config = $observer->getEvent()->getConfig();

        if ($config->getData('add_widgets')) {
            $settings = Mage::getModel('widget/widget_config')->getPluginSettings($config);
            $config->addData($settings);
        }
        return $this;
    }
}
