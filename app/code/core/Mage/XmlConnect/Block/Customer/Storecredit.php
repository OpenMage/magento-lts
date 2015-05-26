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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Store Credits xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Storecredit extends Mage_Core_Block_Abstract
{
    /**
     * Render customer store credits xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $xmlModel Mage_XmlConnect_Model_Simplexml_Element */
        $xmlModel = Mage::getModel('xmlconnect/simplexml_element', '<store_credits_info></store_credits_info>');

        $accountBalance = $this->getLayout()
            ->addBlock('enterprise_customerbalance/account_balance', 'account_balance');

        $xmlModel->addCustomChild('balance', null, array(
            'label' => $this->__('Your current balance is:'),
            'value' => $accountBalance->getBalance(),
            'formatted_value' => Mage::helper('core')->currency($accountBalance->getBalance(), true, false)
        ));

        $accountHistory = $this->getLayout()
            ->addBlock('enterprise_customerbalance/account_history', 'account_history');

        if ($accountHistory->canShow() && $accountHistory->getEvents() && count($accountHistory->getEvents())) {
            $balanceHistory = $xmlModel->addCustomChild('balance_history', null, array(
                'label' => $this->__('Balance History'),
                'action_label' => $this->__('Action'),
                'balance_change_label' => $this->__('Balance Change'),
                'balance_label' => $this->__('Balance'),
                'date_label' => $this->__('Date')
            ));

            foreach ($accountHistory->getEvents() as $event) {
                $item = $balanceHistory->addCustomChild('item');
                $item->addCustomChild('action', null, array(
                    'value' => $accountHistory->getActionLabel($event->getAction())
                ));

                $item->addCustomChild('balance_change', null, array(
                    'value' => Mage::helper('core')->currency($event->getBalanceDelta(), true, false)
                ));

                $item->addCustomChild('balance', null, array(
                    'value' => Mage::helper('core')->currency($event->getBalanceAmount(), true, false)
                ));

                $item->addCustomChild('date', null, array(
                    'value' => Mage::helper('core')->formatDate($event->getUpdatedAt(), 'short', true)
                ));
            }
        }

        return $xmlModel->asNiceXml();
    }
}
