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

/**
 * Preconditions:
 * 1. Create products(simp1, simp2, config1).
 *
 * Steps:
 * 1. Open Backend.
 * 2. Go to Catalog -> Manage Products.
 * 3. Open config1 product.
 * 4. For config1 add as cross-sells: simp1, simp2.
 * 5. Save product.
 * 6. Open simp1 product.
 * 7. For simp1 add as cross-sells: config1, simp2.
 * 8. Save product.
 * 9. Perform all assertions.
 *
 * @group Cross-sells_(MX)
 * @ZephyrId MPERF-7447
 */
class PromoteProductsAsCrossSellsTest extends AbstractPromoteAppurtenantProductsEntityTest
{
    /**
     * Tab name.
     *
     * @var string
     */
    protected $tabName = 'crosssells';

    /**
     * Appurtenant type.
     *
     * @var array
     */
    protected $appurtenantType = [
        'arrayIndex' => 'crossSellProducts',
        'formIndex' => 'cross_sell_products'
    ];

    /**
     * Run test promote cross sell products entity.
     *
     * @param string $crossSellProducts
     * @param array $crossSellProductsData
     * @return array
     */
    public function test($crossSellProducts, array $crossSellProductsData)
    {
        return parent::test($crossSellProducts, $crossSellProductsData);
    }
}
