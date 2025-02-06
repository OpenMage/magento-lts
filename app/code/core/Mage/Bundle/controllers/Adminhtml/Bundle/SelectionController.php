<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Bundle
 */

/**
 * Adminhtml selection grid controller
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Adminhtml_Bundle_SelectionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'catalog/products';

    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Bundle');
    }

    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function searchAction()
    {
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('bundle/adminhtml_catalog_product_edit_tab_bundle_option_search')
                ->setIndex($this->getRequest()->getParam('index'))
                ->setFirstShow(true)
                ->toHtml(),
        );
    }

    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function gridAction()
    {
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock(
                    'bundle/adminhtml_catalog_product_edit_tab_bundle_option_search_grid',
                    'adminhtml.catalog.product.edit.tab.bundle.option.search.grid',
                )
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml(),
        );
    }
}
