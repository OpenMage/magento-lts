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
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Review\Test\TestCase;

use Magento\Mtf\TestCase\Scenario;

/**
 * Preconditions:
 * 1. Create simple product.
 * 2. Create custom rating type.
 *
 * Steps:
 * 1. Open frontend.
 * 2. Go to product page.
 * 3. Click "Be the first to review this product".
 * 4. Fill data according to dataset.
 * 5. Click "Submit review".
 * 6. Perform all assertions.
 *
 * @group Reviews_and_Ratings_(MX)
 * @ZephyrId MPERF-7382
 */
class CreateProductReviewFrontendEntityTest extends Scenario
{
    /**
     * Run create frontend product rating test.
     *
     * @return void
     */
    public function test() {
        $this->executeScenario();
    }

    /**
     * Delete all ratings after test.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->objectManager->create('Mage\Review\Test\TestStep\DeleteAllRatingsStep')->run();
    }
}
