<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Review
 *
 * @method bool getAllowWriteReviewFlag()
 * @method $this setAllowWriteReviewFlag(bool $value)
 * @method $this setLoginLink(string $value)
 */
class Mage_Review_Block_Form extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $customerSession = Mage::getSingleton('customer/session');

        parent::__construct();

        $data =  Mage::getSingleton('review/session')->getFormData(true);
        $data = new Varien_Object($data);

        // add logged in customer name as nickname
        if (!$data->getNickname()) {
            $customer = $customerSession->getCustomer();
            if ($customer && $customer->getId()) {
                $data->setNickname($customer->getFirstname());
            }
        }

        $this->setAllowWriteReviewFlag(
            $customerSession->isLoggedIn() ||
            Mage::helper('review')->getIsGuestAllowToWrite()
        );

        if (!$this->getAllowWriteReviewFlag()) {
            $this->setLoginLink(
                Mage::getUrl('customer/account/login/', [
                    Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME => Mage::helper('core')->urlEncode(
                        Mage::getUrl('*/*/*', ['_current' => true]) .
                        '#review-form'
                    )
                ])
            );
        }

        $this->setTemplate('review/form.phtml')
            ->assign('data', $data)
            ->assign('messages', Mage::getSingleton('review/session')->getMessages(true));
    }

    /**
     * @return false|Mage_Catalog_Model_Product|Mage_Core_Model_Abstract
     * @throws Exception
     */
    public function getProductInfo()
    {
        $product = Mage::registry('current_product');
        if (is_object($product) && ($product->getId() == $this->getRequest()->getParam('id'))) {
            return $product;
        }

        $product = Mage::getModel('catalog/product');
        return $product->load($this->getRequest()->getParam('id'));
    }

    /**
     * @return string
     */
    public function getAction()
    {
        $productId = Mage::app()->getRequest()->getParam('id', false);
        return Mage::getUrl('review/product/post', ['id' => $productId, '_secure' => $this->_isSecure()]);
    }

    /**
     * @return Mage_Rating_Model_Resource_Rating_Collection
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getRatings()
    {
        return Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter('product')
            ->setPositionOrder()
            ->addRatingPerStoreName(Mage::app()->getStore()->getId())
            ->setStoreFilter(Mage::app()->getStore()->getId())
            ->load()
            ->addOptionToItems();
    }
}
