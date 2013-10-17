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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
            return;
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
        $clean  = array();
        $cacheTypes = array_keys(Mage::helper('core')->getCacheTypes());
        foreach ($cacheTypes as $type) {
            $flag = $allCache!='disable' && (!empty($e[$type]) || $allCache=='enable');
            $enable[$type] = $flag ? 1 : 0;
            if ($allCache=='' && !$flag) {
                $clean[] = $type;
            }
        }

        // beta cache enabler (?)
        $beta = $this->getRequest()->getPost('beta');
        $betaCache = array_keys(Mage::helper('core')->getCacheBetaTypes());
        foreach ($betaCache as $type) {
            if (empty($beta[$type])) {
                $clean[] = $type;
            } else {
                $enable[$type] = 1;
            }
        }

        // clean all requested system cache and update cache usage
        if (!empty($clean)) {
            Mage::app()->cleanCache($clean);
        }
        Mage::app()->saveUseCache($enable);

        // clean javascript/css cache
        if ($this->getRequest()->getPost('jscss_action')) {
            if (Mage::getDesign()->cleanMergedJsCss()) {
                $this->_getSession()->addSuccess(
                    Mage::helper('adminhtml')->__('The JavaScript/CSS cache has been cleared.')
                );
            } else {
                $this->_getSession()->addError(Mage::helper('adminhtml')->__('Failed to clear the JavaScript/CSS cache.'));
            }
        }

        /**
         * Run catalog actions
         */
        if ($catalogAction = $this->getRequest()->getPost('catalog_action')) {

            switch ($catalogAction) {
                case 'refresh_catalog_rewrites':
                    try {
                        Mage::getSingleton('catalog/url')->refreshRewrites();
                        $this->_getSession()->addSuccess(
                            Mage::helper('adminhtml')->__('The Catalog Rewrites were refreshed.')
                        );
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while refreshing the Catalog Rewrites.'));
                    }
                    break;

                case 'clear_images_cache':
                    try {
                        Mage::getModel('catalog/product_image')->clearCache();
                        $this->_getSession()->addSuccess(
                            Mage::helper('adminhtml')->__('The image cache was cleared.')
                        );
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while clearing the image cache.'));
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
                            Mage::helper('adminhtml')->__('Layered Navigation Indices were refreshed.')
                        );
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while refreshing the Layered Navigation indices.'));
                    }
                    break;

                case 'refresh_layered_navigation':
                    try {
                        $flag = Mage::getModel('catalogindex/catalog_index_flag')->loadSelf();
                        switch ($flag->getState()) {
                            case Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_QUEUED:
                                $flag->delete();
                                $this->_getSession()->addSuccess(
                                    Mage::helper('adminhtml')->__('The Layered Navigation indexing queue has been canceled.')
                                );
                                break;
                            case Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_RUNNING:
                                $kill = Mage::getModel('catalogindex/catalog_index_kill_flag')->loadSelf();
                                $kill->setFlagData($flag->getFlagData())->save();
                                $this->_getSession()->addSuccess(
                                    Mage::helper('adminhtml')->__('The Layered Navigation process has been queued to be killed.')
                                );
                                break;
                            default:
                                $flag->setState(Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_QUEUED)->save();
                                $this->_getSession()->addSuccess(
                                    Mage::helper('adminhtml')->__('The Layered Navigation indexing has been queued.')
                                );
                                break;
                        }
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while refreshing the Layered Navigation indices.'));
                    }
                    break;

                case 'rebuild_search_index':
                    try {
                        Mage::getSingleton('catalogsearch/fulltext')->rebuildIndex();
                        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('The search index has been rebuilt.'));
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while rebuilding the search index.'));
                    }
                    break;

                case 'rebuild_inventory_stock_status':
                    try {
                        Mage::getSingleton('cataloginventory/stock_status')->rebuild();
                        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('The CatalogInventory Stock Status has been rebuilt.'));
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while rebuilding the CatalogInventory Stock Status.'));
                    }
                    break;

                case 'rebuild_catalog_index':
                    try {
                        Mage::getSingleton('catalog/index')->rebuild();
                        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('The catalog index has been rebuilt.'));
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while rebuilding the catalog index.'));
                    }
                    break;

                case 'rebuild_flat_catalog_category':
                    try {
                        Mage::getResourceModel('catalog/category_flat')->rebuild();
                        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('The flat catalog category has been rebuilt.'));
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while rebuilding the flat catalog category.'));
                    }
                    break;

                case 'rebuild_flat_catalog_product':
                    try {
                        Mage::getResourceModel('catalog/product_flat_indexer')->rebuild();
                        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('The Flat Catalog Product was rebuilt'));
                    }
                    catch (Mage_Core_Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                    catch (Exception $e) {
                        $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while rebuilding the flat product catalog.'));
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
                Mage::helper('adminhtml')->__('The catalog rewrites have been refreshed.')
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while refreshing the catalog rewrites.'));
        }

        $this->_redirect('*/*');
    }

    public function clearImagesCacheAction()
    {
        try {
            Mage::getModel('catalog/product_image')->clearCache();
            $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('The image cache was cleared.')
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while clearing the image cache.'));
        }

        $this->_redirect('*/*');
    }

    public function refreshLayeredNavigationAction()
    {
        try {
            Mage::getSingleton('catalogindex/indexer')->plainReindex();
            $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('The Layered Navigation indices were refreshed.')
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while refreshing the layered navigation indices.'));
        }

        $this->_redirect('*/*');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/cache');
    }
}
