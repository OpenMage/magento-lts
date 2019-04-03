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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Sales\Order\Create;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Adminhtml\Test\Block\Sales\Order\Create\Items\ItemProduct;
use Mage\Adminhtml\Test\Block\Template;

/**
 * Adminhtml sales order create items block.
 */
class Items extends Block
{
    /**
     * Item block selector.
     *
     * @var string
     */
    protected $itemProduct = '//tbody/tr[td[contains(.,"%s")]]';

    /**
     * 'Add Products' button.
     *
     * @var string
     */
    protected $addProducts = "//button[span='Add Products']";

    /**
     * 'Add Products By Sku' button.
     *
     * @var string
     */
    protected $addProductsBySku = "//button[span='Add Products By SKU']";

    /**
     * 'Update Item's and Qty' button selector.
     *
     * @var string
     */
    protected $updateProducts = '[onclick="order.itemsUpdate()"]';

    /**
     * Backend abstract block.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Product names.
     *
     * @var string
     */
    protected $productNames = 'tbody tr td.first span';

    /**
     * Empty block selector.
     *
     * @var string
     */
    protected $emptyBlock = '.empty-text';

    /**
     * Click 'Add Products' button.
     *
     * @return void
     */
    public function clickAddProducts()
    {
        $this->_rootElement->find($this->addProducts, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Update product data in sales.
     *
     * @param array $products
     * @return void
     */
    public function updateProductsData(array $products)
    {
        foreach ($products as $product) {
            $this->getItemProduct($product->getName())->fillProductOptions($product->getCheckoutData());
        }
        $this->_rootElement->find($this->updateProducts)->click();
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Get products data by fields from items ordered grid.
     *
     * @param array $fields
     * @return array
     */
    public function getProductsDataByFields($fields)
    {
        $this->getTemplateBlock()->waitLoader();
        $productsNames = $this->_rootElement->getElements($this->productNames);
        $pageData = [];
        foreach ($productsNames as $productName) {
            $pageData[] = $this->getItemProduct($productName->getText())->getCheckoutData($fields);
        }

        return $pageData;
    }

    /**
     * Get item product block.
     *
     * @param string $productName
     * @return ItemProduct
     */
    public function getItemProduct($productName)
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Create\Items\ItemProduct',
            ['element' => $this->_rootElement->find(sprintf($this->itemProduct, $productName), Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get backend abstract block.
     *
     * @return Template
     */
    public function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Check that empty block is visible.
     *
     * @return bool
     */
    public function isEmptyBlockVisible()
    {
        return $this->_rootElement->find($this->emptyBlock)->isVisible();
    }

    /**
     * Click 'Add Products By SKU' button.
     *
     * @return void
     */
    public function clickAddProductsBySku()
    {
        $this->_rootElement->find($this->addProductsBySku, Locator::SELECTOR_XPATH)->click();
    }
}
