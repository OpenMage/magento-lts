<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogSearch
 */

/**
 * Catalog Search Controller
 *
 * @package    Mage_CatalogSearch
 * @module     Catalog
 */
class Mage_CatalogSearch_AdvancedController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalogsearch/session');
        $this->renderLayout();
    }

    public function resultAction()
    {
        $this->loadLayout();
        try {
            Mage::getSingleton('catalogsearch/advanced')->addFilters($this->getRequest()->getQuery());
        } catch (Mage_Core_Exception $mageCoreException) {
            Mage::getSingleton('catalogsearch/session')->addError($mageCoreException->getMessage());
            $this->_redirectError(
                Mage::getModel('core/url')
                    ->setQueryParams($this->getRequest()->getQuery())
                    ->getUrl('*/*/'),
            );
            return;
        }

        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
}
