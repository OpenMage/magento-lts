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
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
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
    public function saveAction()
    {
        if(!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }
        $tagName    = (string) $this->getRequest()->getQuery('tagName');
        $productId  = (int)$this->getRequest()->getParam('product');

        if(strlen($tagName) && $productId) {
            $session = Mage::getSingleton('catalog/session');
            $product = Mage::getModel('catalog/product')
                ->load($productId);
            if(!$product->getId()){
                $session->addError($this->__('Unable to save tag(s)'));
            } else {
                try {
                    $customerId = Mage::getSingleton('customer/session')->getCustomerId();
                    $tagName = urldecode($tagName);
                    $tagNamesArr = explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $tagName));

                    foreach( $tagNamesArr as $key => $tagName ) {
                        $tagNamesArr[$key] = trim($tagNamesArr[$key], '\'');
                        $tagNamesArr[$key] = trim($tagNamesArr[$key]);
                        if( $tagNamesArr[$key] == '' ) {
                            unset($tagNamesArr[$key]);
                        }
                    }
                    $newCount = 0;
                    foreach( $tagNamesArr as $tagName ) {
                        if( $tagName ) {
                            $tagModel = Mage::getModel('tag/tag');
                            $tagModel->loadByName($tagName);
                            if ($tagModel->getId()) {
                                $status = $tagModel->getStatus();
                                $session->addNotice($this->__('Tag "%s" has already been added to the product' ,$tagName));
                            }
                            else {
                                $status = $tagModel->getPendingStatus();
                                $newCount++;
                            }

                            $tagModel->setName($tagName)
                                    ->setStoreId(Mage::app()->getStore()->getId())
                                    ->setStatus($status)
                                    ->save();

                            $tagRelationModel = Mage::getModel('tag/tag_relation');
                            $tagRelationModel->loadByTagCustomer($productId,
                                $tagModel->getId(),
                                $customerId,
                                Mage::app()->getStore()->getId()
                            );

                            if( $tagRelationModel->getCustomerId() == $customerId && $tagRelationModel->getActive()) {
                                continue;
                            }
                            $tagRelationModel->setTagId($tagModel->getId())
                                ->setCustomerId($customerId)
                                ->setProductId($productId)
                                ->setStoreId(Mage::app()->getStore()->getId())
                                ->setCreatedAt( now() )
                                ->setActive(1)
                                ->save();
                            $tagModel->aggregate();
                        } else {
                            continue;
                        }
                    }
                    if ($newCount > 0) {
                        $session->addSuccess($this->__('%s tag(s) have been accepted for moderation', $newCount));
                    }
                } catch (Exception $e) {
                    $session->addError($this->__('Unable to save tag(s)'));
                }
            }
        }
        $this->_redirectReferer();
    }
}
