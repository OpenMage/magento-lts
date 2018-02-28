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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\Constraint;

use Mage\Adminhtml\Test\Block\Sales\Order\Create\Items;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderCreateIndex;
use Magento\Mtf\Constraint\AbstractAssertForm;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert product was added to Items Ordered grid in customer account on Order creation page backend.
 */
class AssertProductInItemsOrderedGrid extends AbstractAssertForm
{
    /**
     * Fields for assert.
     *
     * @var array
     */
    protected $fields = ['name' => '', 'price' => '', 'checkout_data' => ['qty' => '']];

    /**
     * Check configured products.
     *
     * @var bool
     */
    protected $productsIsConfigured;

    /**
     * Assert product was added to Items Ordered grid in customer account on Order creation page backend.
     *
     * @param SalesOrderCreateIndex $orderCreatePage
     * @param array $products
     * @param bool $productsIsConfigured
     * @return void
     */
    public function processAssert(SalesOrderCreateIndex $orderCreatePage, array $products, $productsIsConfigured = true)
    {
        $this->productsIsConfigured = $productsIsConfigured;
        $data = $this->prepareData($products, $orderCreatePage->getCreateBlock()->getItemsBlock());

        \PHPUnit_Framework_Assert::assertEquals(
            $data['fixtureData'],
            $data['pageData'],
            'Product data on order create page not equals to passed from fixture.'
        );
    }

    /**
     * Prepare data.
     *
     * @param array $data
     * @param Items $itemsBlock
     * @return array
     */
    protected function prepareData(array $data, Items $itemsBlock)
    {
        $fixtureData = [];
        foreach ($data as $product) {
            $checkoutData = $product->getCheckoutData();
            $fixtureData[] = [
                'name' => $product->getName(),
                'price' => number_format($this->getProductPrice($product), 2),
                'checkout_data' => [
                    'qty' => $this->productsIsConfigured && isset($checkoutData['qty']) ? $checkoutData['qty'] : 1,
                ],
            ];
        }
        $pageData = $itemsBlock->getProductsDataByFields($this->fields);
        $preparePageData = $this->arraySort($fixtureData, $pageData);

        return ['fixtureData' => $fixtureData, 'pageData' => $preparePageData];
    }

    /**
     * Sort of array.
     *
     * @param array $fixtureData
     * @param array $pageData
     * @return array
     */
    protected function arraySort(array $fixtureData, array $pageData)
    {
        $result = [];
        foreach ($fixtureData as $key => $value) {
            foreach ($pageData as $pageDataKey => $pageDataValue) {
                if ($value['name'] == $pageDataValue['name']) {
                    $result[$key] = $pageDataValue;
                    unset($pageData[$pageDataKey]);
                    break;
                }
            }
        }

        return array_merge($result, $pageData);
    }

    /**
     * Get product price.
     *
     * @param InjectableFixture $product
     * @return int
     */
    protected function getProductPrice(InjectableFixture $product)
    {
        return isset($product->getCheckoutData()['cartItem']['price'])
            ? $product->getCheckoutData()['cartItem']['price']
            : $product->getPrice();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is added to Items Ordered grid from "Last Ordered Items" section on Order creation page.';
    }
}
