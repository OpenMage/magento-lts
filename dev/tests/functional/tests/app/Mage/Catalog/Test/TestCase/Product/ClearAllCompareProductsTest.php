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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\TestCase\Product;

use Mage\Core\Test\Fixture\ConfigData;
use Mage\Customer\Test\Page\CustomerAccountIndex;

/**
 * Preconditions:
 * 1. All product types are created.
 * 2. Customer created.
 *
 * Steps:
 * 1. Login to frontend.
 * 2. Add to Compare Product $products.
 * 3. Navigate to My Account page.
 * 4. Click "Clear All" icon under the left menu tabs.
 * 5. Perform assertions.
 *
 * @group Compare_Products_(MX)
 * @ZephyrId MPERF-7480
 */
class ClearAllCompareProductsTest extends AbstractProductsCompareTest
{
    /**
     * Test creation for clear all compare products.
     *
     * @param string $products
     * @param ConfigData $config
     * @param CustomerAccountIndex $customerAccountIndex
     * @return void
     */
    public function test(CustomerAccountIndex $customerAccountIndex, $products, ConfigData $config)
    {
        // Preconditions
        $config->persist();
        $this->products = $this->createProducts($products);

        //Steps
        $this->cmsIndex->open();
        $this->loginCustomer($this->customer);
        foreach ($this->products as $itemProduct) {
            $this->addProduct($itemProduct);
            $this->assertProductCompareSuccessAddMessage->processAssert($this->catalogProductView, $itemProduct);
        }
        $this->cmsIndex->getTopLinksBlock()->openAccount();
        $this->cmsIndex->getLinksBlock()->openLink("My Account");
        $customerAccountIndex->getCompareBlock()->clickClearAll();
    }
}
