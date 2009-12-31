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
 * @package     Mage_AmazonPayments
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Amazon Payments Checkout by Amazon Redirect Block
 *
 * @category    Mage
 * @package     Mage_AmazonPayments
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Block_Cba_Redirect extends Mage_Core_Block_Abstract
{
    /**
     * Shopping cart form to CBA in case XML-based cart
     *
     * @return string
     */
    protected function _toHtml()
    {
        $cba = Mage::getModel('amazonpayments/payment_cba');
        /* @var $cba Mage_AmazonPayments_Model_Payment_Cba */

        $form = new Varien_Data_Form();
        $form->setAction($cba->getAmazonRedirectUrl())
            ->setId('amazonpayments_cba')
            ->setName('amazonpayments_cba')
            ->setMethod('POST')
            ->setUseContainer(true);

        $formFields = $cba->getCheckoutXmlFormFields();
        $values = '';
        $i = 1;
        foreach ($formFields as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
            $values .= ($i++ > 1) ? '&' : '';
            $values .= "{$field}={$value}";
        }
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Checkout by Amazon in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("amazonpayments_cba").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}
