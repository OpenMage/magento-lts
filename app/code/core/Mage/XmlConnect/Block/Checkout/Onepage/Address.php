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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer saved addresses renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Onepage_Address extends Mage_Core_Block_Abstract
{
    /**
     * Save address action
     */
    const SAVE_ADDRESS_ACTION = 'xmlconnect/checkout/saveaddressinfo';

    /**
     * Render customer address form and saved addresses list
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $xmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $xmlObj = Mage::getModel('xmlconnect/simplexml_element', '<address_massaction></address_massaction>');

        // Address saved list render
        $listChild = $xmlObj->addCustomChild('address_list');
        $this->getChild('address_list')->setXmlObj($listChild)->toHtml();

        // Address form render
        $formChild = $xmlObj->addCustomChild('form_list', null, array(
            'id' => 'checkout_addresses', 'action' => self::SAVE_ADDRESS_ACTION
        ));

        $formBlock = $this->getChild('address_form');
        $isGuest = Mage::getSingleton('customer/session')->isLoggedIn() ? false : true;

        $billingFormXml = $formBlock->setType('billing')->setIsGuest($isGuest)->toHtml();
        $shippingFormXml = $formBlock->setType('shipping')->setIsGuest(false)->toHtml();
        $formChild->appendChild($billingFormXml)->appendChild($shippingFormXml);

        return $xmlObj->asNiceXml();
    }
}
