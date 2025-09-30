<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Class Mage_Customer_Block_Widget_Taxvat
 *
 * @package    Mage_Customer
 *
 * @method $this setFieldIdFormat(string $value)
 * @method $this setFieldNameFormat(string $value)
 * @method $this setTaxvat(string $value)
 */
class Mage_Customer_Block_Widget_Taxvat extends Mage_Customer_Block_Widget_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('customer/widget/taxvat.phtml');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->_getAttribute('taxvat')->getIsVisible();
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return (bool) $this->_getAttribute('taxvat')->getIsRequired();
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
}
