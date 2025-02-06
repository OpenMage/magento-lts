<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Bundle
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
