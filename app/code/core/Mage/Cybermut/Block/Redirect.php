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
 * @package     Mage_Cybermut
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Redirect to Cybermut
 *
 * @category    Mage
 * @package     Mage_Cybermut
 * @name        Mage_Cybermut_Block_Standard_Redirect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cybermut_Block_Redirect extends Mage_Core_Block_Abstract
{

    protected function _toHtml()
    {
        $standard = Mage::getModel('cybermut/payment');
        $form = new Varien_Data_Form();
        $form->setAction($standard->getCybermutUrl())
            ->setId('cybermut_payment_checkout')
            ->setName('cybermut_payment_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($standard->setOrder($this->getOrder())->getStandardCheckoutFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }

        $formHTML = $form->toHtml();

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Cybermut in a few seconds.');
        $html.= $formHTML;
        $html.= '<script type="text/javascript">document.getElementById("cybermut_payment_checkout").submit();</script>';
        $html.= '</body></html>';

        if ($standard->getConfigData('debug_flag')) {
            Mage::getModel('cybermut/api_debug')
                ->setRequestBody($formHTML)
                ->save();
        }

        return $html;
    }
}
