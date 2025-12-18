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
 * @method Mage_Checkout_Model_Resource_Agreement            _getResource()
 * @method string                                            getCheckboxText()
 * @method Mage_Checkout_Model_Resource_Agreement_Collection getCollection()
 * @method string                                            getContent()
 * @method string                                            getContentHeight()
 * @method int                                               getIsActive()
 * @method int                                               getIsHtml()
 * @method string                                            getName()
 * @method Mage_Checkout_Model_Resource_Agreement            getResource()
 * @method Mage_Checkout_Model_Resource_Agreement_Collection getResourceCollection()
 * @method int                                               getStoreId()
 * @method $this                                             setCheckboxText(string $value)
 * @method $this                                             setContent(string $value)
 * @method $this                                             setContentHeight(string $value)
 * @method $this                                             setIsActive(int $value)
 * @method $this                                             setIsHtml(int $value)
 * @method $this                                             setName(string $value)
 */
class Mage_Checkout_Model_Agreement extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('checkout/agreement');
    }
}
