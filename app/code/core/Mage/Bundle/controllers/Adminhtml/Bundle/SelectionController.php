<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
                ->toHtml()
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
                    'adminhtml.catalog.product.edit.tab.bundle.option.search.grid'
                )
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
        );
    }
}
