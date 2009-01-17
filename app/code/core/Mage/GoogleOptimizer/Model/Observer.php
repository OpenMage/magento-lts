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
 * @package    Mage_GoogleOptimizer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Optimizer Observer
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Model_Observer
{
    /**
     * Loading product scripts after load product
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function appendToProductGoogleOptimizerScripts($observer)
    {
        $product = $observer->getEvent()->getProduct();

        if (!Mage::helper('googleoptimizer')->isOptimizerActive()) {
            return $this;
        }

        $googleOptimizerModel = Mage::getModel('googleoptimizer/code_product')
            ->setEntity($product)
            ->loadScripts($product->getStoreId());
        if ($googleOptimizerModel->getId()) {
            $product->setGoogleOptimizerScripts($googleOptimizerModel);
        }
        return $this;
    }

    /**
     *  @deprecated please use prepareProductGoogleOptimizerScripts method instead
     *
     * @param Varien_Object $observer
     */
    public function prepareGoogleOptimizerScripts($observer)
    {
        $this->prepareProductGoogleOptimizerScripts($observer);
    }

    /**
     * Prepare product scripts for saving
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function prepareProductGoogleOptimizerScripts($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $request = $observer->getEvent()->getRequest();

        if ($googleOptimizer = $request->getPost('googleoptimizer')) {
            $product->setGoogleOptimizerScripts(new Varien_Object($googleOptimizer));
        }
        return $this;
    }

    /**
     * Save product scripts after saving product
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function saveProductGoogleOptimizerScripts($observer)
    {
        $product = $observer->getEvent()->getProduct();

        if ($product->getGoogleOptimizerScripts()) {
            $googleOptimizer = Mage::getModel('googleoptimizer/code_product')
                ->setEntity($product)
                ->saveScripts($product->getStoreId());
        }

        return $this;
    }

    /**
     * Delete Produt scripts after deleting product
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function deleteProductGoogleOptimizerScripts($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $googleOptimizer = Mage::getModel('googleoptimizer/code_product')
            ->setEntity($product)
            ->deleteScripts($product->getStoreId());
        return $this;
    }

    /**
     * Loading page scripts after load page
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function appendToPageGoogleOptimizerScripts($observer)
    {
        $cmsPage = $observer->getEvent()->getObject();
        $googleOptimizerModel = Mage::getModel('googleoptimizer/code_page')
            ->setEntity($cmsPage)
            ->loadScripts(0);

        if ($googleOptimizerModel->getId()) {
            $cmsPage->setGoogleOptimizerScripts($googleOptimizerModel);
        }
        return $this;
    }

    /**
     * Prepare page scripts for saving
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function preparePageGoogleOptimizerScripts($observer)
    {
        $cmsPage = $observer->getEvent()->getPage();
        $request = $observer->getEvent()->getRequest();

        if ($googleOptimizer = $request->getPost('googleoptimizer')) {
            $cmsPage->setGoogleOptimizerScripts(new Varien_Object($googleOptimizer));
        }
        return $this;
    }

    /**
     * Save page scripts after saving page
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function savePageGoogleOptimizerScripts($observer)
    {
        $cmsPage = $observer->getEvent()->getObject();

        if ($cmsPage->getGoogleOptimizerScripts()) {
            $googleOptimizer = Mage::getModel('googleoptimizer/code_page')
                ->setEntity($cmsPage)
                ->saveScripts(0);
        }

        return $this;
    }

    /**
     * Delete page scripts after deleting page
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function deletePageGoogleOptimizerScripts($observer)
    {
        $cmsPage = $observer->getEvent()->getObject();
        $googleOptimizer = Mage::getModel('googleoptimizer/code_page')
            ->setEntity($cmsPage)
            ->deleteScripts(0);
        return $this;
    }

    public function assignHandlers($observer)
    {
        $catalogHalper = $observer->getEvent()->getHelper();
        $helper = Mage::helper('googleoptimizer');
        $catalogHalper->addHandler('productAttribute', $helper)
            ->addHandler('categoryAttribute', $helper);
        return $this;
    }

    /**
     * Loading category scripts after load category
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function appendToCategoryGoogleOptimizerScripts($observer)
    {
        $category = $observer->getEvent()->getCategory();

        if (!Mage::helper('googleoptimizer')->isOptimizerActive()) {
            return $this;
        }

        $googleOptimizerModel = Mage::getModel('googleoptimizer/code_category')
            ->setEntity($category)
            ->loadScripts($category->getStoreId());
        if ($googleOptimizerModel->getId()) {
            $category->setGoogleOptimizerScripts($googleOptimizerModel);
        }
        return $this;
    }

    /**
     * Prepare category scripts for saving
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function prepareCategoryGoogleOptimizerScripts($observer)
    {
        $category = $observer->getEvent()->getCategory();
        $request = $observer->getEvent()->getRequest();

        if ($googleOptimizer = $request->getPost('googleoptimizer')) {
            $category->setGoogleOptimizerScripts(new Varien_Object($googleOptimizer));
        }
        return $this;
    }

    /**
     * Save category scripts after saving category
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function saveCategoryGoogleOptimizerScripts($observer)
    {
        $category = $observer->getEvent()->getCategory();

        if (!Mage::helper('googleoptimizer')->isOptimizerActive()) {
            return $this;
        }

        if ($category->getGoogleOptimizerScripts()) {
            $googleOptimizer = Mage::getModel('googleoptimizer/code_category')
                ->setEntity($category)
                ->saveScripts($category->getStoreId());
        }

        return $this;
    }

    /**
     * Delete Produt scripts after deleting product
     *
     * @param Varien_Object $observer
     * @return Mage_Googleoptimizer_Model_Observer
     */
    public function deleteCategoryGoogleOptimizerScripts($observer)
    {
        $category = $observer->getEvent()->getCategory();
        $googleOptimizer = Mage::getModel('googleoptimizer/code_category')
            ->setEntity($category)
            ->deleteScripts($category->getStoreId());
        return $this;
    }

}