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

namespace Mage\Catalog\Test\TestCase\Product;

/**
 * Preconditions:
 * 1. Create products(simp1, simp2, config1).
 *
 * Steps:
 * 1. Open Backend.
 * 2. Go to Catalog -> Manage Products.
 * 3. Open simp1 product.
 * 4. For simp1 add as related: config1, simp2.
 * 5. Save product.
 * 6. Open config1 product.
 * 7. For config1 add as related: simp2.
 * 8. Save product.
 * 9. Perform all assertions.
 *
 * @group Up-sells_(MX)
 * @ZephyrId MPERF-7469
 */
class PromoteProductsAsRelatedTest extends AbstractPromoteAppurtenantProductsEntityTest
{
    /**
     * Tab name.
     *
     * @var string
     */
    protected $tabName = 'related-products';

    /**
     * Appurtenant type.
     *
     * @var array
     */
    protected $appurtenantType = [
        'arrayIndex' => 'relatedProducts',
        'formIndex' => 'related_products'
    ];

    /**
     * Run test promote related products entity.
     *
     * @param string $relatedProducts
     * @param array $relatedProductsData
     * @return array
     */
    public function test($relatedProducts, array $relatedProductsData)
    {
        return parent::test($relatedProducts, $relatedProductsData);
    }
}
