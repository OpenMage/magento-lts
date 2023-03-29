<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog product composite helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Catalog_Product_Composite extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Adminhtml';

    /**
     * Init layout of product configuration update result
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     * @return $this
     */
    protected function _initUpdateResultLayout($controller)
    {
        $controller->getLayout()->getUpdate()
            ->addHandle('ADMINHTML_CATALOG_PRODUCT_COMPOSITE_UPDATE_RESULT');
        $controller->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        return $this;
    }

    /**
     * Prepares and render result of composite product configuration update for a case
     * when single configuration submitted
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     * @param Varien_Object $updateResult
     * @return $this
     */
    public function renderUpdateResult($controller, Varien_Object $updateResult)
    {
        Mage::register('composite_update_result', $updateResult);

        $this->_initUpdateResultLayout($controller);
        $controller->renderLayout();
        return $this;
    }

    /**
    * Init composite product configuration layout
    *
    * $isOk - true or false, whether action was completed nicely or with some error
    * If $isOk is FALSE (some error during configuration), so $productType must be null
    *
    * @param Mage_Adminhtml_Controller_Action $controller
    * @param bool $isOk
    * @param string $productType
    * @return $this
    */
    protected function _initConfigureResultLayout($controller, $isOk, $productType)
    {
        $update = $controller->getLayout()->getUpdate();
        if ($isOk) {
            $update->addHandle('ADMINHTML_CATALOG_PRODUCT_COMPOSITE_CONFIGURE')
                ->addHandle('PRODUCT_TYPE_' . $productType);
        } else {
            $update->addHandle('ADMINHTML_CATALOG_PRODUCT_COMPOSITE_CONFIGURE_ERROR');
        }
        $controller->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        return $this;
    }

    /**
     * Prepares and render result of composite product configuration request
     *
     * $configureResult holds either:
     *  - 'ok' = true, and 'product_id', 'buy_request', 'current_store_id', 'current_customer' or 'current_customer_id'
     *  - 'error' = true, and 'message' to show
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     * @param Varien_Object $configureResult
     * @return $this
     */
    public function renderConfigureResult($controller, Varien_Object $configureResult)
    {
        try {
            if (!$configureResult->getOk()) {
                Mage::throwException($configureResult->getMessage());
            }

            $currentStoreId = (int) $configureResult->getCurrentStoreId();
            if (!$currentStoreId) {
                $currentStoreId = Mage::app()->getStore()->getId();
            }

            $product = Mage::getModel('catalog/product')
                ->setStoreId($currentStoreId)
                ->load($configureResult->getProductId());
            if (!$product->getId()) {
                Mage::throwException($this->__('Product is not loaded.'));
            }
            Mage::register('current_product', $product);
            Mage::register('product', $product);

            // Register customer we're working with
            $currentCustomer = $configureResult->getCurrentCustomer();
            if (!$currentCustomer) {
                $currentCustomerId = (int) $configureResult->getCurrentCustomerId();
                if ($currentCustomerId) {
                    $currentCustomer = Mage::getModel('customer/customer')
                        ->load($currentCustomerId);
                }
            }
            if ($currentCustomer) {
                Mage::register('current_customer', $currentCustomer);
            }

            // Prepare buy request values
            $buyRequest = $configureResult->getBuyRequest();
            if ($buyRequest) {
                Mage::helper('catalog/product')->prepareProductOptions($product, $buyRequest);
            }

            $isOk = true;
            $productType = $product->getTypeId();
        } catch (Exception $e) {
            $isOk = false;
            $productType = null;
            Mage::register('composite_configure_result_error_message', $e->getMessage());
        }

        $this->_initConfigureResultLayout($controller, $isOk, $productType);
        $controller->renderLayout();
        return $this;
    }
}
