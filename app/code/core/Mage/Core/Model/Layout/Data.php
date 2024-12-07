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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Layout _getResource()
 * @method Mage_Core_Model_Resource_Layout getResource()
 * @method string getHandle()
 * @method $this setHandle(string $value)
 * @method string getXml()
 * @method $this setXml(string $value)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
 */
class Mage_Core_Model_Layout_Data extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('core/layout');
    }
}
