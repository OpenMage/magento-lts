<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
