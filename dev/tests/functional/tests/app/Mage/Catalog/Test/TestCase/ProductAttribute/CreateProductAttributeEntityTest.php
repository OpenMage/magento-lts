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

namespace Mage\Catalog\Test\TestCase\ProductAttribute;

use Magento\Mtf\TestCase\Scenario;
use Mage\Catalog\Test\Fixture\CatalogProductAttribute;
use Mage\Catalog\Test\Fixture\CatalogAttributeSet;

/**
 * Precondition:
 * 1. Create new attribute set.
 *
 * Test Flow:
 * 1. Log in to backend.
 * 2. Navigate to Catalog > Attributes > Manage Attributes.
 * 3. Click 'Add New Attribute' button.
 * 4. Fill out fields data according to data set.
 * 5. Save Product Attribute.
 * 6. Perform appropriate assertions.
 *
 * @group Product_Attributes_(CS)
 * @ZephyrId MPERF-7278
 */
class CreateProductAttributeEntityTest extends Scenario
{
    /**
     * Run CreateProductAttributeEntity test.
     *
     * @param string $product
     * @return array
     */
    public function test($product)
    {
        $this->executeScenario();
        // Prepare data for asserts
        return $this->createProduct($product, $this->localArguments['productTemplate']);
    }

    /**
     * Create product.
     *
     * @param string $product
     * @param CatalogAttributeSet $attributeSet
     * @return array
     */
    protected function createProduct($product, CatalogAttributeSet $attributeSet) {
        $stepArguments = [
            'product' => $product,
            'data' => ['attribute_set_id' => ['attribute_set' => $attributeSet]]
        ];

        return $this->objectManager->create('Mage\Catalog\Test\TestStep\CreateProductStep', $stepArguments)->run();
    }
}
