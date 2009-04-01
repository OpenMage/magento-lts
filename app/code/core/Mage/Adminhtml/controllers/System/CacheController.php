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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * config controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_System_CacheController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Retrieve session model
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Display cache management form
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/cache')
            ->_addContent($this->getLayout()->createBlock('adminhtml/system_cache_edit')->initForm())
            ->renderLayout();
    }

    /**
     * Seva cache settings
     */
    public function saveAction()
    {
        /**
         * Protect empty post data
         */
        $postData = $this->getRequest()->getPost();
        if (empty($postData)) {
            $this->_redirect('*/*');
            return $this;
        }

        /**
         * Process cache settings
         */
        $allCache = $this->getRequest()->getPost('all_cache');
        if ($allCache=='disable' || $allCache=='refresh') {
            Mage::app()->cleanCache();
        }

        $e = $this->getRequest()->getPost('enable');
        $enable = array();
        $clean = array();
        foreach (Mage::helper('core')->getCacheTypes() as $type=>$label) {
            $flag = $allCache!='disable' && (!empty($e[$type]) || $allCache=='enable');
            $enable[$type] = $flag ? 1 : 0;
            if ($allCache=='' && !$flag) {
                $clean[] = $type;
            }
        }

        $beta = $this->getRequest()->getPost('beta');
        foreach (Mage::helper('core')->getCacheBetaTypes() as $type=>$label) {
            if (empty($beta[$type])) {
                $clean[] = $type;
            } else {
                $enable[$type] = 1;
            }
        }

        if (!empty($clean)) {
            Mage::app()->cleanCache($clean);
        }
        Mage::app()->saveUseCache($enable);

        /**
         * Run catalog actions
         */
        if ($catalogAction = $this->getRequest()->getPost('catalog_action')) {
            switch ($catalogAction) {
                case 'refresh_catalog_rewrites':
                    try {
                        Mage::getSingleton('catalog/url')->refreshRewrites();
                        $this->_getSession()->addSuccess(
                            Mage::helper('adminhtml')->__('Catalog Rewrites were refreshed successfully')
                        );
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('Error while refreshed Catalog Rewrites. Please try again later'));
                    }
                    break;

                case 'clear_images_cache':
                    try {
                        Mage::getModel('catalog/product_image')->clearCache();
                        $this->_getSession()->addSuccess(
                            Mage::helper('adminhtml')->__('Image cache was cleared successfully')
                        );
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('Error while cleared Image cache. Please try again later'));
                    }
                    break;

                case 'refresh_layered_navigation_now':
                    try {
                        $flag = Mage::getModel('catalogindex/catalog_index_flag')->loadSelf();
                        if ($flag->getState() == Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_RUNNING) {
                            $kill = Mage::getModel('catalogindex/catalog_index_kill_flag')->loadSelf();
                            $kill->setFlagData($flag->getFlagData())->save();
                        }

                        $flag->setState(Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_QUEUED)->save();
                        Mage::getSingleton('catalogindex/indexer')->plainReindex();
                        $this->_getSession()->addSuccess(
                            Mage::helper('adminhtml')->__('Layered Navigation Indices were refreshed successfully')
                        );
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('Error while refreshed Layered Navigation Indices. Please try again later'));
                    }
                    break;

                case 'refresh_layered_navigation':
                    try {
                        $flag = Mage::getModel('catalogindex/catalog_index_flag')->loadSelf();
                        switch ($flag->getState()) {
                            case Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_QUEUED:
                                $flag->delete();
                                $this->_getSession()->addSuccess(
                                    Mage::helper('adminhtml')->__('Layered Navigation indexing queue cancelled')
                                );
                                break;
                            case Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_RUNNING:
                                $kill = Mage::getModel('catalogindex/catalog_index_kill_flag')->loadSelf();
                                $kill->setFlagData($flag->getFlagData())->save();
                                $this->_getSession()->addSuccess(
                                    Mage::helper('adminhtml')->__('Layered Navigation process queued to be killed')
                                );
                                break;
                            default:
                                $flag->setState(Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_QUEUED)->save();
                                $this->_getSession()->addSuccess(
                                    Mage::helper('adminhtml')->__('Layered Navigation indexing queued')
                                );
                                break;
                        }
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('Error while refreshed Layered Navigation Indices. Please try again later'));
                    }
                    break;

                case 'rebuild_search_index':
                    try {
                        Mage::getSingleton('catalogsearch/fulltext')->rebuildIndex();
                        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('Search Index was rebuilt successfully'));
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('Search Index rebuild error. Please try again later'));
                    }
                    break;

                case 'rebuild_inventory_stock_status':
                    try {
                        Mage::getSingleton('cataloginventory/stock_status')->rebuild();
                        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('CatalogInventory Stock Status was rebuilt successfully'));
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('CatalogInventory Stock Status rebuild error. Please try again later'));
                    }
                    break;

                case 'rebuild_flat_catalog_category':
                    try {
                        Mage::getResourceModel('catalog/category_flat')->rebuild();
                        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('Flat Catalog Category was rebuilt successfully'));
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('Flat Catalog Category rebuild error'));
                    }
                    break;

                case 'rebuild_flat_catalog_product':
                    try {
                        Mage::getResourceModel('catalog/product_flat_indexer')->rebuild();
                        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('Flat Catalog Product was rebuilt successfully'));
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        echo $e;
                        die();
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('Flat Catalog Product rebuild error. Please try again later'));
                    }
                    break;

                default:
                    break;
            }
        }

        $this->_redirect('*/*');
    }

    public function refreshCatalogRewritesAction()
    {
        try {
            Mage::getSingleton('catalog/url')->refreshRewrites();
            $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('Catalog Rewrites was refreshed successfully')
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('Error while refreshed Catalog Rewrites. Please try again later'));
        }

        $this->_redirect('*/*');
    }

    public function clearImagesCacheAction()
    {
        try {
            Mage::getModel('catalog/product_image')->clearCache();
            $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('Image cache was cleared successfully')
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('Error while cleared Image cache. Please try again later'));
        }

        $this->_redirect('*/*');
    }

    public function refreshLayeredNavigationAction()
    {
        try {
            Mage::getSingleton('catalogindex/indexer')->plainReindex();
            $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('Layered Navigation Indices was refreshed successfully')
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('Error while refreshed Layered Navigation Indices. Please try again later'));
        }

        $this->_redirect('*/*');
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('system/cache');
    }
}
