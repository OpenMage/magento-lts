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

namespace Mage\Catalog\Test\TestCase\Product;

use Mage\Catalog\Test\Page\Product\CatalogProductCompare;
use Mage\Catalog\Test\Constraint\AssertProductCompareSuccessAddMessage;
use Mage\Customer\Test\Fixture\Customer;

/**
 * Preconditions:
 * 1. All product types are created.
 * 2. Customer created.
 *
 * Steps:
 * 1. Navigate to front-end.
 * 2. Login as customer according to dataset.
 * 3. Open product page of test product(s) and click "Add to Compare" button.
 * 4. Assert success message is present on page.
 * 5. Navigate to compare page.
 * 6. Perform all asserts.
 *
 * @group Compare_Products_(MX)
 * @ZephyrId MPERF-7190
 */
class AddProductsToCompareTest extends AbstractProductsCompareTest
{
    /**
     * Catalog product compare page.
     *
     * @var CatalogProductCompare
     */
    protected $catalogProductCompare;

    /**
     * Assert Product Compare success add message.
     *
     * @var AssertProductCompareSuccessAddMessage
     */
    protected $assertProductCompareSuccessAddMessage;

    /**
     * Test creation for adding compare products.
     *
     * @param CatalogProductCompare $catalogProductCompare
     * @param string $products
     * @param string $isCustomerLoggedIn
     * @return array
     */
    public function test(CatalogProductCompare $catalogProductCompare, $products, $isCustomerLoggedIn)
    {
        //Steps
        $this->catalogProductCompare = $catalogProductCompare;
        $this->cmsIndex->open();
        if ($isCustomerLoggedIn === 'Yes') {
            $this->loginCustomer($this->customer);
        }
        $this->products = $this->createProducts($products);
        foreach ($this->products as $itemProduct) {
            $this->addProduct($itemProduct);
            $this->assertProductCompareSuccessAddMessage->processAssert($this->catalogProductView, $itemProduct);
        }

        return ['products' => $this->products];
    }

    /**
     * Clear compare list after test variation.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getCompareBlock()->clickClearAll();
    }
}
