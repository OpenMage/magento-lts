<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * @package    Mage_Core
 */
class Mage_Core_Model_Language extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('core/language');
    }
}
