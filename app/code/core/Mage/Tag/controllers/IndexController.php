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
 * @package     Mage_Tag
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag Frontend controller
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Saving tag and relation between tag, customer, product and store
     */
    public function saveAction()
    {
        $customerSession = Mage::getSingleton('customer/session');
        if(!$customerSession->authenticate($this)) {
            return;
        }
        $tagName    = (string) $this->getRequest()->getQuery('productTagName');
        $productId  = (int)$this->getRequest()->getParam('product');

        if(strlen($tagName) && $productId) {
            $session = Mage::getSingleton('catalog/session');
            $product = Mage::getModel('catalog/product')
                ->load($productId);
            if(!$product->getId()){
                $session->addError($this->__('Unable to save tag(s).'));
            } else {
                try {
                    $customerId = $customerSession->getCustomerId();
                    $storeId = Mage::app()->getStore()->getId();

                    $tagNamesArr = $this->_cleanTags($this->_extractTags($tagName));

                    $counter = new Varien_Object(array("new" => 0,
                                                       "exist" => array(),
                                                       "success" => array(),
                                                       "recurrence" => array()));

                    $tagModel = Mage::getModel('tag/tag');
                    $tagRelationModel = Mage::getModel('tag/tag_relation');

                    foreach ($tagNamesArr as $tagName) {
                        $tagModel->unsetData()
                            ->loadByName($tagName)
                            ->setStoreId($storeId)
                            ->setName($tagName);

                        $tagRelationModel->unsetData()
                            ->setStoreId($storeId)
                            ->setProductId($productId)
                            ->setCustomerId($customerId)
                            ->setActive(1)
                            ->setCreatedAt( $tagRelationModel->getResource()->formatDate(time()) );

                        if (!$tagModel->getId()) {
                            $tagModel->setFirstCustomerId($customerId)
                                ->setFirstStoreId($storeId)
                                ->setStatus($tagModel->getPendingStatus())
                                ->save();

                            $tagRelationModel->setTagId($tagModel->getId())->save();

                            $counter->setNew($counter->getNew() + 1);
                        } else {
                            $tagStatus = $tagModel->getStatus();
                            $tagRelationModel->setTagId($tagModel->getId());

                            switch($tagStatus) {
                                case $tagModel->getApprovedStatus():
                                    if($this->_checkLinkBetweenTagProduct($tagRelationModel)) {
                                        if(!$this->_checkLinkBetweenTagCustomerProduct($tagRelationModel, $tagModel)) {
                                            $tagRelationModel->save();
                                        }
                                        $counter->setExist(array_merge($counter->getExist(), array($tagName)));
                                    } else {
                                        $tagRelationModel->save();
                                        $counter->setSuccess(array_merge($counter->getSuccess(), array($tagName)));
                                    }
                                    break;
                                case $tagModel->getPendingStatus():
                                    if(!$this->_checkLinkBetweenTagCustomerProduct($tagRelationModel, $tagModel)) {
                                        $tagRelationModel->save();
                                    }
                                    $counter->setNew($counter->getNew() + 1);
                                    break;
                                case $tagModel->getDisabledStatus():
                                    if($this->_checkLinkBetweenTagCustomerProduct($tagRelationModel, $tagModel)) {
                                        $counter->setRecurrence(array_merge($counter->getRecurrence(), array($tagName)));
                                    } else {
                                        $tagModel->setStatus($tagModel->getPendingStatus())->save();
                                        $tagRelationModel->save();
                                        $counter->setNew($counter->getNew() + 1);
                                    }
                                    break;
                            }
                        }
                    }

                    $this->_fillMessageBox($counter);
                    
                } catch (Exception $e) {
                    Mage::logException($e);
                    $session->addError($this->__('Unable to save tag(s).'));
                }
            }
        }
        $this->_redirectReferer();
    }

    /**
     * Checks inputed tags on the correctness of symbols and split string to array of tags
     * 
     * @param string $tagNamesInString
     * @return array
     */
    protected function _extractTags($tagNamesInString)
    {
        return explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $tagNamesInString));
    }

    /**
     * Clears the tag from the separating characters.
     * 
     * @param array $tagNamesArr
     * @return array
     */
    protected function _cleanTags(array $tagNamesArr)
    {
        foreach( $tagNamesArr as $key => $tagName ) {
            $tagNamesArr[$key] = trim($tagNamesArr[$key], '\'');
            $tagNamesArr[$key] = trim($tagNamesArr[$key]);
            if( $tagNamesArr[$key] == '' ) {
                unset($tagNamesArr[$key]);
            }
        }
        return $tagNamesArr;
    }

    /**
     * Checks whether the already marked this product in this store by this tag.
     * 
     * @param Mage_Tag_Model_Tag_Relation $tagRelationModel
     * @return boolean
     */
    protected function _checkLinkBetweenTagProduct($tagRelationModel)
    {
        $customerId = $tagRelationModel->getCustomerId();
        $tagRelationModel->setCustomerId(null);
        $res = in_array($tagRelationModel->getProductId(), $tagRelationModel->getProductIds());
        $tagRelationModel->setCustomerId($customerId);
        return $res;
    }

    /**
     * Checks whether the already marked this product in this store by this tag and by this customer.
     * 
     * @param Mage_Tag_Model_Tag_Relation $tagRelationModel
     * @param Mage_Tag_Model_Tag $tagModel
     * @return boolean
     */
    protected function _checkLinkBetweenTagCustomerProduct($tagRelationModel, $tagModel)
    {
        return (count(Mage::getModel('tag/tag_relation')->loadByTagCustomer(
                            $tagRelationModel->getProductId(),
                            $tagModel->getId(),
                            $tagRelationModel->getCustomerId(),
                            $tagRelationModel->getStoreId())
                        ->getProductIds()) > 0);
    }

    /**
     * Fill Message Box by success and notice messages about results of user actions.
     * 
     * @param Varien_Object $counter
     * @return void
     */
    protected function _fillMessageBox($counter)
    {
        $session = Mage::getSingleton('catalog/session');

        if ($counter->getNew()) {
            $session->addSuccess($this->__('%s tag(s) have been accepted for moderation.', $counter->getNew()));
        }

        if (count($counter->getExist())) {
            foreach ($counter->getExist() as $tagName) {
                $session->addNotice($this->__('Tag "%s" has already been added to the product.' ,$tagName));
            }
        }

        if (count($counter->getSuccess())) {
            foreach ($counter->getSuccess() as $tagName) {
                $session->addSuccess($this->__('Tag "%s" has been added to the product.' ,$tagName));
            }
        }

        if (count($counter->getRecurrence())) {
            foreach ($counter->getRecurrence() as $tagName) {
                $session->addSuccess($this->__('Tag "%s" has been rejected by administrator.' ,$tagName));
            }
        }
    }

}
