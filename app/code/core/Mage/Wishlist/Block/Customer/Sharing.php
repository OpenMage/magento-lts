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
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist customer sharing block
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Customer_Sharing extends Mage_Core_Block_Template
{

    protected  $_enteredData = null;

    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('Wishlist Sharing'));
        }
    }

    public function getSendUrl()
    {
        return $this->getUrl('*/*/send');
    }

    public function getEnteredData($key)
    {
        if(is_null($this->_enteredData)) {
            $this->_enteredData = Mage::getSingleton('wishlist/session')->getData('sharing_form', true);
        }

        if(!$this->_enteredData || !isset($this->_enteredData[$key])) {
            return null;
        } else {
            return $this->htmlEscape($this->_enteredData[$key]);
        }
    }

}
