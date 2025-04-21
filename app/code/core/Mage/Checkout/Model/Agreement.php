<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * @package    Mage_Checkout
 *
 * @method Mage_Checkout_Model_Resource_Agreement _getResource()
 * @method Mage_Checkout_Model_Resource_Agreement getResource()
 * @method Mage_Checkout_Model_Resource_Agreement_Collection getCollection()
 *
 * @method string getName()
 * @method $this setName(string $value)
 * @method string getContent()
 * @method $this setContent(string $value)
 * @method string getContentHeight()
 * @method $this setContentHeight(string $value)
 * @method string getCheckboxText()
 * @method $this setCheckboxText(string $value)
 * @method int getIsActive()
 * @method $this setIsActive(int $value)
 * @method int getIsHtml()
 * @method $this setIsHtml(int $value)
 * @method int getStoreId()
 */
class Mage_Checkout_Model_Agreement extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('checkout/agreement');
    }
}
