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

namespace Mage\Wishlist\Test\TestCase;

use Mage\Customer\Test\Fixture\Customer;

/**
 * Preconditions:
 * 1. Create customer.
 * 2. Create composite products.
 * 3. Log in to frontend.
 * 4. Add products to the customer's wish list (unconfigured).
 *
 * Steps:
 * 1. Open Wish list.
 * 2. Click 'Edit' for the product.
 * 3. Fill data.
 * 4. Click 'Ok'.
 * 5. Perform assertions.
 *
 * @group Wishlist_(CS)
 * @ZephyrId MPERF-7630
 */
class ConfigureProductInCustomerWishlistOnFrontendTest extends AbstractWishlistTest
{
    /**
     * Configure customer wish list on frontend.
     *
     * @param Customer $customer
     * @param string $product
     * @return array
     */
    public function test(Customer $customer, $product)
    {
        // Preconditions
        $product = $this->createProducts($product)[0];
        $this->loginCustomer($customer);
        $this->addToWishlist($product);

        // Steps
        $this->cmsIndex->getTopLinksBlock()->openAccountLink('My Wishlist');
        $this->wishlistIndex->getItemsBlock()->getItemProductBlock($product)->clickEdit();
        $this->catalogProductView->getViewBlock()->updateWishlist($product);

        return ['product' => $product];
    }
}
