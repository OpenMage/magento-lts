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

namespace Mage\Downloadable\Test\Constraint;

use Mage\Checkout\Test\Fixture\Cart;

/**
 * Assert that the correct links details are displayed on the "View Details" tool tip.
 */
class AssertDownloadableProductDetailsInWishlist extends \Mage\Wishlist\Test\Constraint\AssertProductDetailsInWishlist
{
    /**
     * Prepare downloadable product options.
     *
     * @param Cart $cart
     * @return array
     */
    protected function prepareOptions(Cart $cart)
    {
        $result = [];
        $data = parent::prepareOptions($cart);
        foreach ($data as $itemData) {
            $result[] = [
                'title' => 'LINKS',
                'value' => $itemData['value']
            ];
        }

        return  $this->sortDataByPath($result, '::title');
    }
}
