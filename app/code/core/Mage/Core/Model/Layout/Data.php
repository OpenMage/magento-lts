<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
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
