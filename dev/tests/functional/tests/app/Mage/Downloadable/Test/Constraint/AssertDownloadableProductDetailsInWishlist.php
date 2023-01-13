<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
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
