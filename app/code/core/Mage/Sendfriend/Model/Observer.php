<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sendfriend
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sendfriend Observer
 *
 * @category   Mage
 * @package    Mage_Sendfriend
 */
class Mage_Sendfriend_Model_Observer
{
    /**
     * Register Sendfriend Model in global registry
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function register(Varien_Event_Observer $observer)
    {
        Mage::getModel('sendfriend/sendfriend')->register();
        return $this;
    }
}
