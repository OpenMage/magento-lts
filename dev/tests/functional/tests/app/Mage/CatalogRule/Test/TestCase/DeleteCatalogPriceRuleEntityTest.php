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

namespace Mage\CatalogRule\Test\TestCase;

use Mage\CatalogRule\Test\Fixture\CatalogRule;
use Mage\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;
use Mage\CatalogRule\Test\Page\Adminhtml\CatalogRuleEdit;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. Catalog Price Rule is created.
 *
 * Steps:
 * 1. Log in as default admin user.
 * 2. Go to Promotions -> Catalog Price Rules.
 * 3. Select required catalog price rule from preconditions.
 * 4. Click on the "Delete" button.
 * 5. Perform all assertions.
 *
 * @group Catalog_Price_Rules_(MX)
 * @ZephyrId MPERF-7033
 */
class DeleteCatalogPriceRuleEntityTest extends Injectable
{
    /**
     * Catalog Rule index page.
     *
     * @var CatalogRuleIndex
     */
    protected $catalogRuleIndex;

    /**
     * Catalog Rule edit page.
     *
     * @var CatalogRuleEdit
     */
    protected $catalogRuleEdit;

    /**
     * Injection data.
     *
     * @param CatalogRuleIndex $catalogRuleIndex
     * @param CatalogRuleEdit $catalogRuleEdit
     * @return void
     */
    public function __inject(CatalogRuleIndex $catalogRuleIndex, CatalogRuleEdit $catalogRuleEdit)
    {
        $this->catalogRuleIndex = $catalogRuleIndex;
        $this->catalogRuleEdit = $catalogRuleEdit;
    }

    /**
     * Delete Catalog Price Rule test.
     *
     * @param CatalogRule $catalogPriceRule
     * @return void
     */
    public function test(CatalogRule $catalogPriceRule)
    {
        // Precondition
        $catalogPriceRule->persist();

        $filter = [
            'name' => $catalogPriceRule->getName(),
            'rule_id' => $catalogPriceRule->getId(),
        ];
        // Steps
        $this->catalogRuleIndex->open();
        $this->catalogRuleIndex->getCatalogRuleGrid()->searchAndOpen($filter);
        $this->catalogRuleEdit->getFormPageActions()->delete();
    }
}
