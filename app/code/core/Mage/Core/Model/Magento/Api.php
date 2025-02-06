<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Magento info API
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Magento_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Retrieve information about current Magento installation
     *
     * @return array
     */
    public function info()
    {
        $result = [];
        $result['magento_edition'] = Mage::getEdition();
        $result['magento_version'] = Mage::getVersion();
        $result['openmage_version'] = Mage::getOpenMageVersion();

        return $result;
    }
}
