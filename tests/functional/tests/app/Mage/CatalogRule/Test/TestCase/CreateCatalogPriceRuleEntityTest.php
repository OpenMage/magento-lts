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

namespace Mage\CatalogRule\Test\TestCase;

use Mage\CatalogRule\Test\Fixture\CatalogRule;

/**
 * Steps:
 *
 * 1. Log in to backend.
 * 2. Go to Promotions -> Catalog Price Rules.
 * 3. Click "Add New Rule" button.
 * 4. Fill in data according to dataset.
 * 5. Save catalog price rule.
 * 6. Perform all assertions.
 *
 * @group Catalog_Price_Rules_(MX)
 * @ZephyrId MPERF-6774
 */
class CreateCatalogPriceRuleEntityTest extends AbstractCatalogRuleEntityTest
{
    /**
     * Create Catalog Price Rule test.
     *
     * @param CatalogRule $catalogPriceRule
     * @return void
     */
    public function test(CatalogRule $catalogPriceRule)
    {
        // Steps:
        $this->catalogRuleIndex->open();
        $this->catalogRuleIndex->getGridPageActions()->addNew();
        $this->catalogRuleEdit->getEditForm()->fill($catalogPriceRule);
        $this->catalogRuleEdit->getFormPageActions()->save();
    }
}
