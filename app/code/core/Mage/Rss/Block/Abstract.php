<?php
class Mage_Rss_Block_Abstract extends Mage_Core_Block_Template
{
    protected function _getStoreId()
    {
        //store id is store view id
        $storeId =   (int) $this->getRequest()->getParam('sid');
        if($storeId == null) {
           $storeId = Mage::app()->getStore()->getId();
        }
        return $storeId;
    }

    protected function _getCustomerGroupId()
    {
        //customer group id
        $custGroupID =   (int) $this->getRequest()->getParam('cid');
        if($custGroupID == null) {
            $custGroupID = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
        return $custGroupID;
    }
}
