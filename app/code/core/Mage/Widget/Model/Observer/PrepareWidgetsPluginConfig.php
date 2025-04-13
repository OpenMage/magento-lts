<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Widget
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Widget Observer model
 *
 * @category   Mage
 * @package    Mage_Widget
 */
class Mage_Widget_Model_Observer_PrepareWidgetsPluginConfig implements Mage_Core_Observer_Interface
{
    /**
     * Add additional settings to wysiwyg config for Widgets Insertion Plugin
     */
    public function execute(Varien_Event_Observer $observer): void
    {
        /** @var Varien_Object $config */
        $config = $observer->getEvent()->getDataByKey('config');
        if ($config->getDataByKey('add_widgets')) {
            $settings = Mage::getModel('widget/widget_config')->getPluginSettings($config);
            $config->addData($settings);
        }
    }
}
