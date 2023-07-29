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

namespace Mage\Catalog\Test\TestCase\Product;

/**
 * Preconditions:
 * 1. Create products(simp1, simp2, config1).
 *
 * Steps:
 * 1. Open Backend.
 * 2. Go to Catalog -> Manage Products.
 * 3. Open simp1 product.
 * 4. For simp1 add as up-sells: config1, simp2.
 * 5. Save product.
 * 6. Open config1 product.
 * 7. For config1 add as up-sells: simp2.
 * 8. Save product.
 * 9. Perform all assertions.
 *
 * @group Up-sells_(MX)
 * @ZephyrId MPERF-7458
 */
class PromoteProductsAsUpSellsTest extends AbstractPromoteAppurtenantProductsEntityTest
{
    /**
     * Tab name.
     *
     * @var string
     */
    protected $tabName = 'up-sells';

    /**
     * Appurtenant type.
     *
     * @var array
     */
    protected $appurtenantType = [
        'arrayIndex' => 'upSellProducts',
        'formIndex' => 'up_sell_products'
    ];

    /**
     * Run test promote upSell products entity.
     *
     * @param string $upSellProducts
     * @param array $upSellProductsData
     * @return array
     */
    public function test($upSellProducts, array $upSellProductsData)
    {
        return parent::test($upSellProducts, $upSellProductsData);
    }
}
