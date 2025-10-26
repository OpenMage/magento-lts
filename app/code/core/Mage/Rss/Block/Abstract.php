<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rss
 */

/**
 * Class Mage_Rss_Block_Abstract
 *
 * @package    Mage_Rss
 *
 * @method int getStoreId()
 */
class Mage_Rss_Block_Abstract extends Mage_Core_Block_Template
{
    /**
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getStoreId()
    {
        //store id is store view id
        $storeId =   (int) $this->getRequest()->getParam('store_id');
        if ($storeId == null) {
            $storeId = Mage::app()->getStore()->getId();
        }

        return $storeId;
    }

    /**
     * @return int
     * @throws Exception
     */
    protected function _getCustomerGroupId()
    {
        //customer group id
        $custGroupID =   (int) $this->getRequest()->getParam('cid');
        if ($custGroupID == null) {
            $custGroupID = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        return $custGroupID;
    }
}
