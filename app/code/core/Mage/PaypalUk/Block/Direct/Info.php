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
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_PaypalUk_Block_Direct_Info extends Mage_Payment_Block_Info_Cc
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paypaluk/direct/info.phtml');
    }

    protected function _getDirect()
    {
        return Mage::getSingleton('paypaluk/direct');
    }

     /**
     * Retrieve credit card type name
     *
     * @return string
     */
    public function getCcTypeName()
    {
        $types = $this->_getDirect()->getApi()->getCcTypes();
        if (isset($types[$this->getInfo()->getCcType()])) {
            return $types[$this->getInfo()->getCcType()];
        }
        return $this->getInfo()->getCcType();
    }

    /**
     * Retrieve CC start month for switch/solo card
     *
     * @return string
     */
    public function getCcStartMonth()
    {
        $month = $this->getInfo()->getCcSsStartMonth();
        if ($month<10) {
            $month = '0'.$month;
        }
        return $month;
    }
    
    public function toPdf()
    {
        $this->setTemplate('paypaluk/direct/pdf/info.phtml');
        return $this->toHtml();
    }
}