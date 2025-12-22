<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Layout _getResource()
 * @method string                          getHandle()
 * @method Mage_Core_Model_Resource_Layout getResource()
 * @method int                             getSortOrder()
 * @method string                          getXml()
 * @method $this                           setHandle(string $value)
 * @method $this                           setSortOrder(int $value)
 * @method $this                           setXml(string $value)
 */
class Mage_Core_Model_Layout_Data extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('core/layout');
    }
}
