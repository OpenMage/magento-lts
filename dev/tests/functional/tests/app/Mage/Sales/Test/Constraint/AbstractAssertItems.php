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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Sales\Test\Fixture\Order;
use Mage\Shipping\Test\Page\Adminhtml\SalesShipment;
use Mage\Shipping\Test\Page\Adminhtml\SalesShipmentView;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Adminhtml\Test\Block\Shipping\View\Items;
use Mage\Shipping\Test\Page\ShipmentView;
use Mage\Catalog\Test\Constraint\ProductHandler;

/**
 * Assert items represented in order's entity view page.
 */
abstract class AbstractAssertItems extends AbstractAssertSales
{
    /**
     * Sales type index page.
     *
     * @var SalesShipment|ShipmentView
     */
    protected $salesTypePage;

    /**
     * Sales type view page.
     *
     * @var SalesShipmentView
     */
    protected $salesTypeViewPage;

    /**
     * Special fields for verify.
     *
     * @var array
     */
    protected $specialFields = [];

    /**
     * Products array.
     *
     * @var array
     */
    protected $products;

    /**
     * Product handler class.
     *
     * @var ProductHandler
     */
    protected $productHandlerClass;

    /**
     * Product handler class path.
     *
     * @var string
     */
    protected $productHandlerPath = 'Mage\Catalog\Test\Constraint\ProductHandler';

    /**
     * Assert products are represented on view page.
     *
     * @param array $ids
     * @param Order|null $order
     * @param string|null $orderId
     * @param array|null $products
     * @param array|null $verifyData
     * @return void
     */
    public function processAssert(
        array $ids,
        Order $order = null,
        $orderId = null,
        array $products = null,
        array $verifyData = null
    ) {
        $this->setFields($order, $orderId, $products, $verifyData);
        $this->openPage();
        $this->assert($ids);
    }

    /**
     * Set fields for assert.
     *
     * @param Order|null $order
     * @param string|null $orderId
     * @param array|null $products
     * @param array|null $verifyData
     */
    protected function setFields(Order $order = null, $orderId = null, array $products = null, array $verifyData = null)
    {
        $this->orderId = ($orderId == null) ? $order->getId() : $orderId;
        $this->products = ($products == null) ? $order->getEntityId()['products'] : $products;
        $this->verifyData = $verifyData;
        $this->productHandlerClass = $this->getProductHandlerClass();
    }

    /**
     * Open verify page.
     *
     * @return void
     */
    protected function openPage()
    {
        $this->salesTypePage->open();
    }

    /**
     * Process assert.
     *
     * @param array $ids
     */
    protected function assert(array $ids)
    {
        $productsData = $this->prepareItemsData($this->products, $this->verifyData);
        foreach ($ids[$this->entityType . 'Ids'] as $entityId) {
            $itemsData = $this->getItemsBlock($entityId)->getData();
            $error = $this->verifyData($productsData, $itemsData);
            \PHPUnit_Framework_Assert::assertEmpty($error, $error);
        }
    }

    /**
     * Get items block.
     *
     * @param string|null $entityId
     * @return Items
     */
    protected function getItemsBlock($entityId)
    {
        $this->openItemEntity($entityId);
        return $this->salesTypeViewPage->getItemsBlock();
    }

    /**
     * Open item entity.
     *
     * @param string $entityId
     * @return void
     */
    protected function openItemEntity($entityId)
    {
        $this->salesTypePage->getGrid()->searchAndOpen(['order_id' => $this->orderId, 'id' => $entityId,]);
    }

    /**
     * Prepare items data.
     *
     * @param array $products
     * @param array|null $data
     * @return array
     */
    protected function prepareItemsData(array $products, array $data = null)
    {
        $productsData = [];
        foreach ($products as $key => $product) {
            $verifyData = isset($data['items_data'][$key]) ? $data['items_data'][$key] : null;
            $productsData[] = array_merge(
                $this->getDefaultItemData($product, $verifyData),
                $this->getSpecialItemData($verifyData)
            );
        }

        return $productsData;
    }

    /**
     * Prepare special item data.
     *
     * @param array|null $verifyData
     * @return array
     */
    protected function getSpecialItemData($verifyData = null)
    {
        $result = [];
        if ($verifyData == null) {
            return $result;
        }
        foreach ($this->specialFields as $field) {
            if (isset($verifyData[$field])) {
                $result[$field] = $verifyData[$field];
            }
        }

        return $result;
    }

    /**
     * Get default item data.
     *
     * @param InjectableFixture $product
     * @param array|null $verifyData
     * @return array
     */
    protected function getDefaultItemData(InjectableFixture $product, array $verifyData = null)
    {
        /** @var CatalogProductSimple $product */
        $checkoutData = $product->getCheckoutData();
        $defaultData['item_qty'] = (isset($verifyData['qty'])) ? $verifyData['qty'] : $checkoutData['qty'];
        $defaultData = array_merge($defaultData, $this->getProductData($product));

        return $defaultData;
    }

    /**
     * Get product data.
     *
     * @param InjectableFixture $product
     * @return array
     */
    protected function getProductData(InjectableFixture $product)
    {
        $productData['product'] = [
            'name' => $this->getProductName($product),
            'sku' => $this->productHandlerClass->getProductSku($product)
        ];
        $productOptions = $this->productHandlerClass->getProductOptions($product);
        if ($productOptions != null) {
            $productData['product']['options'] = $productOptions;
        }

        return $productData;
    }

    /**
     * Get product name.
     *
     * @param InjectableFixture $product
     * @return string
     */
    protected function getProductName(InjectableFixture $product)
    {
        return $product->getName();
    }

    /**
     * Get product handler class.
     *
     * @return ProductHandler
     */
    protected function getProductHandlerClass()
    {
        return $this->objectManager->create($this->productHandlerPath);
    }
}
