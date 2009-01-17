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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer sharing config model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Config_Share extends Mage_Core_Model_Config_Data
{
    const XML_PATH_CUSTOMER_ACCOUNT_SHARE = 'customer/account_share/scope';
    const SHARE_GLOBAL  = 0;
    const SHARE_WEBSITE = 1;

    public function isGlobalScope()
    {
        return !$this->isWebsiteScope();
    }

    public function isWebsiteScope()
    {
        return Mage::getStoreConfig(self::XML_PATH_CUSTOMER_ACCOUNT_SHARE) == self::SHARE_WEBSITE;
    }

    public function toOptionArray()
    {
        return array(
            self::SHARE_GLOBAL  => Mage::helper('customer')->__('Global'),
            self::SHARE_WEBSITE => Mage::helper('customer')->__('Per Website'),
        );
    }

    public function _beforeSave()
    {
        $value = $this->getValue();
        if ($value == self::SHARE_GLOBAL) {
            $collection = Mage::getModel('customer/customer')->getCollection()
                ->groupByEmail();
            foreach ($collection as $customer) {
                if ($customer->getEmailCount()>1) {
                    Mage::throwException(
                        Mage::helper('customer')->__('Can\'t share customer accounts global. Because some customer accounts with same emails exist on multiple websites and cannot be merged.')
                    );
                }
            }
        }
        return $this;
    }
}
