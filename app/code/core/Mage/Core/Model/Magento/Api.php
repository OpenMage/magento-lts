<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Magento info API
 *
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
        return [
            'magento_edition' => Mage::getEdition(),
            'magento_version' => Mage::getVersion(),
            'openmage_version' => Mage::getOpenMageVersion(),
        ];
    }
}
