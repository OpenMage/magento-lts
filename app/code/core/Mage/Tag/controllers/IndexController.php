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
 * @package     Mage_Tag
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

                    $tagModel = Mage::getModel('tag/tag');

                    // added tag relation statuses
                    $counter = array(
                        Mage_Tag_Model_Tag::ADD_STATUS_NEW => array(),
                        Mage_Tag_Model_Tag::ADD_STATUS_EXIST => array(),
                        Mage_Tag_Model_Tag::ADD_STATUS_SUCCESS => array(),
                        Mage_Tag_Model_Tag::ADD_STATUS_REJECTED => array()
                    );

                    $tagNamesArr = $this->_cleanTags($this->_extractTags($tagName));
                    foreach ($tagNamesArr as $tagName) {
                        // unset previously added tag data
                        $tagModel->unsetData()
                            ->loadByName($tagName);

                        if (!$tagModel->getId()) {
                            $tagModel->setName($tagName)
                                ->setFirstCustomerId($customerId)
                                ->setFirstStoreId($storeId)
                                ->setStatus($tagModel->getPendingStatus())
                                ->save();
                        }
                        $relationStatus = $tagModel->saveRelation($productId, $customerId, $storeId);
                        $counter[$relationStatus][] = $tagName;
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
     * Fill Message Box by success and notice messages about results of user actions.
     *
     * @param array $counter
     * @return void
     */
    protected function _fillMessageBox($counter)
    {
        $session = Mage::getSingleton('catalog/session');
        $helper = Mage::helper('core');

        if (count($counter[Mage_Tag_Model_Tag::ADD_STATUS_NEW])) {
            $session->addSuccess(
                $this->__('%s tag(s) have been accepted for moderation.', count($counter[Mage_Tag_Model_Tag::ADD_STATUS_NEW]))
            );
        }

        if (count($counter[Mage_Tag_Model_Tag::ADD_STATUS_EXIST])) {
            foreach ($counter[Mage_Tag_Model_Tag::ADD_STATUS_EXIST] as $tagName) {
                $session->addNotice(
                    $this->__('Tag "%s" has already been added to the product.' , $helper->escapeHtml($tagName))
                );
            }
        }

        if (count($counter[Mage_Tag_Model_Tag::ADD_STATUS_SUCCESS])) {
            foreach ($counter[Mage_Tag_Model_Tag::ADD_STATUS_SUCCESS] as $tagName) {
                $session->addSuccess(
                    $this->__('Tag "%s" has been added to the product.' ,$helper->escapeHtml($tagName))
                );
            }
        }

        if (count($counter[Mage_Tag_Model_Tag::ADD_STATUS_REJECTED])) {
            foreach ($counter[Mage_Tag_Model_Tag::ADD_STATUS_REJECTED] as $tagName) {
                $session->addNotice(
                    $this->__('Tag "%s" has been rejected by administrator.' ,$helper->escapeHtml($tagName))
                );
            }
        }
    }

}
