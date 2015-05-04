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
 * Customer order totals xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Totals extends Mage_Sales_Block_Order_Totals
{
    /**
     * Add order totals rendered to XML object
     * (get from template: sales/order/totals.phtml)
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderXmlObj
     * @return null
     */
    public function addTotalsToXmlObject(Mage_XmlConnect_Model_Simplexml_Element $orderXmlObj)
    {
        // all Enterprise renderers from layout update into array an realize checking of their using
        $enterpriseBlocks = array(
            'reward.sales.order.total'  => array(
                'module'    => 'Enterprise_Reward',
                'block'     => 'enterprise_reward/sales_order_total'
            ),
            'customerbalance'  => array(
                'module'    => 'Enterprise_CustomerBalance',
                'block'     => 'xmlconnect/customer_order_totals_customerbalance',
                'template'  => 'customerbalance/order/customerbalance.phtml'
            ),
            'customerbalance_total_refunded'  => array(
                'module'    => 'Enterprise_CustomerBalance',
                'block'     => 'xmlconnect/customer_order_totals_customerbalance_refunded',
                'template'  => 'customerbalance/order/customerbalance_refunded.phtml',
                'after'     => '-',
                'action'    => array(
                    'method'    => 'setAfterTotal',
                    'value'     => 'grand_total'
            )),
            'giftwrapping'  => array(
                'module'    => 'Enterprise_GiftWrapping',
                'block'     => 'enterprise_giftwrapping/sales_totals'
            ),
            'giftcards'  => array(
                'module'    => 'Enterprise_GiftCardAccount',
                'block'     => 'xmlconnect/customer_order_totals_giftcards',
                'template'  => 'giftcardaccount/order/giftcards.phtml'
        ));

        foreach ($enterpriseBlocks as $name => $block) {
            // create blocks only for Enterprise/Pro modules which are in system
            if (is_object(Mage::getConfig()->getNode('modules/' . $block['module']))) {
                $blockInstance = $this->getLayout()->createBlock($block['block'], $name);
                $this->setChild($name, $blockInstance);
                if (isset($block['action']['method']) && isset($block['action']['value'])) {
                    $method = $block['action']['method'];
                    $blockInstance->$method($block['action']['value']);
                }
            }
        }

        $this->_beforeToHtml();
        $totalsXml = $orderXmlObj->addCustomChild('totals');

        if ($this->getNewApi()) {
            $this->_prepareTotalsApi23($totalsXml);
            return;
        }

        foreach ($this->getTotals() as $total) {
            if ($total->getValue()) {
                $total->setValue($totalsXml->escapeXml($total->getValue()));
            }
            if ($total->getBlockName()) {
                $block = $this->getLayout()->getBlock($total->getBlockName());
                if (is_object($block)) {
                    if (method_exists($block, 'addToXmlObject')) {
                        $block->setTotal($total)->addToXmlObject($totalsXml);
                    } else {
                        $this->_addTotalToXml($total, $totalsXml);
                    }
                }
            } else {
                $this->_addTotalToXml($total, $totalsXml);
            }
        }
    }

    /**
     * Prepare totals using for response api version 23
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $totalsXml
     * @return null
     */
    protected function _prepareTotalsApi23(Mage_XmlConnect_Model_Simplexml_Element $totalsXml)
    {
        $totalsXml = $totalsXml->addCustomChild('total', null, array('id' => 'totals_full_list'));
        foreach ($this->getTotals() as $total) {
            if ($total->getValue()) {
                $total->setValue($totalsXml->escapeXml($total->getValue()));
            }
            if ($total->getBlockName()) {
                $block = $this->getLayout()->getBlock($total->getBlockName());
                if (is_object($block)) {
                    if (method_exists($block, 'addToXmlObject')) {
                        $block->setTotal($total)->setNewApi($this->getNewApi())->addToXmlObject($totalsXml);
                    } else {
                        $this->_addTotalToXmlApi23($total, $totalsXml);
                    }
                }
            } else {
                $this->_addTotalToXmlApi23($total, $totalsXml);
            }
        }
    }

    /**
     * Add total to totals XML
     *
     * @param Varien_Object $total
     * @param Mage_XmlConnect_Model_Simplexml_Element $totalsXml
     * @return null
     */
    protected function _addTotalToXml($total, Mage_XmlConnect_Model_Simplexml_Element $totalsXml)
    {
        if ($total instanceof Varien_Object && $total->getCode() && $total->getLabel() && $total->hasData('value')) {
            $totalsXml->addCustomChild(
                preg_replace('@[\W]+@', '_', trim($total->getCode())),
                $this->_formatPrice($total),
                array('label' => $totalsXml->escapeXml($total->getLabel()))
            );
        }
    }

    /**
     * Add total to totals XML. Api version 23.
     *
     * @param Varien_Object $total
     * @param Mage_XmlConnect_Model_Simplexml_Element $totalsXml
     * @return null
     */
    protected function _addTotalToXmlApi23($total, Mage_XmlConnect_Model_Simplexml_Element $totalsXml)
    {
        if ($total instanceof Varien_Object && $total->getCode() && $total->getLabel() && $total->hasData('value')) {
            $totalsXml->addCustomChild('item', $this->_formatPrice($total), array(
                 'id' => preg_replace('@[\W]+@', '_', trim($total->getCode())),
                 'label' => $totalsXml->escapeXml($total->getLabel()),
            ));
        }
    }

    /**
     * Format price using order currency
     *
     * @param   Varien_Object $total
     * @return  string
     */
    protected function _formatPrice($total)
    {
        if (!$total->getIsFormated()) {
            return Mage::helper('xmlconnect/customer_order')->formatPrice($this, $total->getValue());
        }
        return $total->getValue();
    }
}
