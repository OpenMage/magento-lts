<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Authorizenet
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DirectPost information block
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Authorizenet_Block_Directpost_Info extends Mage_Payment_Block_Info
{    
    const PAYMENT_METHOD_CODE = 'authorizenet_directpost';
    
    /**
     * Form block instance
     * 
     * @var Mage_Authorizenet_Block_Directpost_Form
     */
    protected $_formBlock;        
    
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Core/Block/Mage_Core_Block_Template#_toHtml()
     */
    protected function _toHtml()
    {
        if ($this->getForm()->getMethodCode() != self::PAYMENT_METHOD_CODE) {
            return;
        }            
        
        return parent::_toHtml();
    }
    
    
    /**
     * Set payment info
     * 
     * @return Mage_Authorizenet_Block_Directpost_Info
     */
    public function setMethodInfo()
    {        
        $payment = Mage::getSingleton('checkout/session')->getQuote()->getPayment();        
        $this->setInfo($payment);
        
        return $this;
    }
    
    /**
     * Get form instance
     * 
     * @return Mage_Authorizenet_Block_Directpost_Form
     */
    public function getForm()
    {
        if (!$this->_formBlock) {
            $this->_formBlock = Mage::getSingleton('core/layout')
                                ->createBlock($this->getMethod()->getFormBlockType());
            $this->_formBlock->setMethod($this->getMethod());
        }
        
        return $this->_formBlock;
    }    
}