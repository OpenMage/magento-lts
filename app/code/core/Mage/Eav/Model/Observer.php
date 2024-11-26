<?php

/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Eav_Model_Observer
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Observer
{
    /**
     * @param Varien_Event_Observer $event
     * @return void
     * @throws Mage_Core_Model_Store_Exception
     */
    public function onControllerActionPredispatch($event)
    {
        /** @var Mage_Core_Controller_Varien_Action $controllerAction */
        $controllerAction = $event->getData('controller_action');

        // initialize cached store_id for frontend controllers only to avoid issues with cron jobs and admin controllers which sometimes change store view
        if ($controllerAction instanceof Mage_Core_Controller_Front_Action) {
            Mage::getSingleton('eav/config')->setCurrentStoreId(Mage::app()->getStore()->getId());
        }
    }
}
