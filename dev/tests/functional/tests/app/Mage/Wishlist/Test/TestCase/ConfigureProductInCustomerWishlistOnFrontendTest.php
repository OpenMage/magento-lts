<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
