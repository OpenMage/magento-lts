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

namespace Mage\Catalog\Test\TestCase\Product;

use Magento\Mtf\TestCase\Scenario;

/**
 * Preconditions:
 * 1. Two simple products are created.
 * 2. Configurable attribute with two options is created.
 * 3. Configurable attribute added to default template.
 * 4. Configurable product is created.
 *
 * Steps:
 * 1. Log in to backend.
 * 2. Open Catalog -> Manage Products.
 * 3. Search and open configurable product from preconditions.
 * 4. Fill in data according to dataset.
 * 5. Save product.
 * 6. Perform all assertions.
 *
 * @group Configurable_Product_(MX)
 * @ZephyrId MPERF-7439
 */
class UpdateConfigurableProductEntityTest extends Scenario
{
    /**
     * Update configurable product.
     *
     * @return array
     */
    public function test()
    {
        $this->executeScenario();
    }
}
