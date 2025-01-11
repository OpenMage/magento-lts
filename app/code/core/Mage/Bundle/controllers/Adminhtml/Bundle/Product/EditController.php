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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

/**
 * Adminhtml bundle product edit
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Adminhtml_Bundle_Product_EditController extends Mage_Adminhtml_Catalog_ProductController
{
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Bundle');
    }

    public function formAction()
    {
        $product = $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('bundle/adminhtml_catalog_product_edit_tab_bundle', 'admin.product.bundle.items')
                ->setProductId($product->getId())
                ->toHtml(),
        );
    }
}
